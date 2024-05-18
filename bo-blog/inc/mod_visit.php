<?PHP
/* -----------------------------------------------------
Bo-Blog 2 : The Blog Reloaded.
<<A Bluview Technology Product>>
禁止使用Windows记事本修改文件，由此造成的一切使用不正常恕不解答！
PHP+MySQL blog system.
Code: Bob Shen
Offical site: http://www.bo-blog.com
Copyright (c) Bob Shen 中国－上海
In memory of my university life
------------------------------------------------------- */
//die (print_r($_COOKIE));
if (!defined('VALIDREQUEST')) die ('Access Denied.');

extract_forbidden();
acceptrequest('job');

if (!$job) {
	@header("Location: index.php");
	exit();
}


if ($job=='openidsubmit') {
	if ($mbcon['enableopenid']!='1') catcherror($lnc[315].$lnc[319]);
	$openidresult=completeOpenID();
	$v_replier=$openidresult['openidurl'];
	if (@$openidresult['sreg']['email']) {
		$v_repemail=$openidresult['sreg']['email'];
	}
	acceptrequest('todojob');
	$job=$todojob;
	setcookie ('openid_url_id', $openidresult['openidurl']);
}

if ($job=='addreply' || $job=='addmessage' || $job=='editreply' || $job=='editmessage') {
	if (check_ip ($userdetail['ip'], $forbidden['banip'])) catcherror($lnc[209]);
	define('REPLYSPECIAL', 1);
	
	if ($job=='addreply' || $job=='addmessage') {
		//Check post interval
		$lastpost=$_COOKIE['lastpost'];
		if (($nowtime['timestamp']-$lastpost)<$permission['MinPostInterval']) catcherror($lnc[210]);
		$findintable=($job=='addreply') ? 'replies' : 'messages';
		$findreplies=$blog->getbyquery("SELECT * FROM `{$db_prefix}{$findintable}` WHERE `repip`='{$userdetail['ip']}' ORDER BY `reptime` DESC LIMIT 1");
		if ($findreplies['repip']==$userdetail['ip']) {
			if (($nowtime['timestamp']-$findreplies['reptime'])<$permission['MinPostInterval']) catcherror($lnc[210]); 
		}
	}
	acceptrequest('v_replier,v_password,stat_html,stat_ubb,stat_emot,stat_property,v_repurl,v_repemail,v_content,v_id,v_security,onetimecounter,v_reppsw,v_contentc');

	if ($v_contentc) $v_content=$v_contentc;
	$v_id=intval(trimplus($v_id));
	if ($job=='addreply') {
		checkpermission('Reply');
		if ($v_id==='') $cancel=$lnc[211];
		else {
			if ($permission['SeeHiddenEntry']!=1) $limitmore="AND `property`<>2";
			$originblog=$blog->getbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE `blogid`='{$v_id}' AND `property`<>1  AND `property`<>3 {$limitmore}");
			if ($originblog['blogid']!=$v_id) $cancel=$lnc[211];
			else {
				$allowedgp=@explode('|', $originblog['permitgp']);
				if ($originblog['permitgp']!='' && !@in_array ($userdetail['usergroup'], $allowedgp)) $cancel=$lnc[211];
			}
		}
	} elseif ($job=='addmessage' || $job=='editmessage') {
		checkpermission('LeaveMessage');
	} else {
		checkpermission('Reply');
	}
	catcherror ($cancel);


	if ($job=='addreply' || $job=='addmessage') {
		if ($stat_html==1 && $permission['Html']==1) $html=1;
		else $html=0;
		if ($stat_ubb==1 && $permission['Ubb']==1) $ubb=1;
		else $ubb=0;
		if ($stat_emot==1 && $permission['Emot']==1) $emot=1;
		else $emot=0;

		$v_replier=safe_convert(trimplus($v_replier));
		if ($v_replier=='') $cancel=$lnc[212];
		if (preg_search($v_replier, $forbidden['banword']) || preg_search($v_replier, $forbidden['keep'])) $cancel=$lnc[158];
		if (strlen($v_replier)>45) $cancel=$lnc[213];
		if ($config['validation']==1) {
			if ($db_defaultsessdir!=1) session_save_path("./{$db_tmpdir}");
			session_cache_limiter("private, must-revalidate");
			session_start();
			if ($v_security=='' || strtolower($v_security)!=strtolower($_SESSION['code'])) $cancel=$lnc[165];
			else { //Delete current session
				session_destroy();
			}
		}
		$v_repurl=safe_convert(trimplus($v_repurl));
		$v_repemail=safe_convert(trimplus($v_repemail));
		$v_reppsw=trimplus($v_reppsw);
		if ($v_reppsw!='') $v_reppsw=md5($v_reppsw);
		if ($mbcon['anticorrupturl']==1) $v_repurl=urlconvert($v_repurl);

		if ($stat_property==1 || $originblog['property']==2) $reproperty=1;
		else $reproperty=0;

		if ($logstat==1) {
			$replier=$userdetail['username'];
			$replierid=$userdetail['userid'];
		} else {
			$v_password=md5($v_password);
			$v_replier_checker=mystrtolower($v_replier);
			$userchecker=$blog->getbyquery("SELECT * FROM `{$db_prefix}user` WHERE LOWER(username)='{$v_replier_checker}'");
			if (mystrtolower($userchecker['username'])==$v_replier_checker && $v_password==$userchecker['userpsw']) {
				$replier=$userchecker['username'];
				$replierid=$userchecker['userid'];
				@setcookie ('userid', $userchecker['userid']);
				@setcookie ('userpsw', $v_password);
			} else {
				if ($userchecker['username']) $cancel=$lnc[308];
				if (@in_iarray ($v_replier, $adminlist)) $cancel=$lnc[212];
				$replier=$v_replier;
				$replierid=-1;
			}
		}
	}
	$v_content=safe_convert(trimplus($v_content), $html);
	if ($v_content=='') $cancel=$lnc[214];
	if (strlen($v_content)>$permission['MaxPostLength']) $cancel=$lnc[214];
	if (preg_search($v_content, $forbidden['banword'])) $cancel=$lnc[214];
	catcherror ($cancel);

	if ($mbcon['censorall']=='1') $suspectspam=1;
	elseif ($mbcon['antispam']=='1' && $permission['NoSpam']!=1) {
		//If the post contains more than X links, it may be a spam
		if (substr_count($v_content, 'http://')>=$mbcon['susurlnum']) $suspectspam=1;
		if (substr_count($v_content, '[url')>=$mbcon['susurlnum']) $suspectspam=1;
		if (substr_count($v_content, 'www.')>=$mbcon['susurlnum']) $suspectspam=1;
		//If the post contains spam common words, it may be a spam
		if (preg_search($v_content, $forbidden['suspect'])) $suspectspam=1;
		//If the post is too short, it may be a spam
		if (strlen($v_content)<=$mbcon['susminchar']) $suspectspam=1;
	} else $suspectspam=0;

	$maxrecord=$blog->getsinglevalue("{$db_prefix}maxrec");
	if ($job=='addreply') { //Is a reply
		$plusquery_c="'{$v_id}', ";
		$targettable="{$db_prefix}replies";
		$currentmaxid=$maxrecord['maxrepid']+1;
		$alter_column='maxrepid';
		$replies_now=$originblog['replies']+1;
		if ($suspectspam!=1) { //Not a spam
			$blog->query("UPDATE `{$db_prefix}blogs` SET `replies`='{$replies_now}' WHERE `blogid`='{$v_id}'");
			$count_new=$statistics['replies']+1;
			$blog->query("UPDATE `{$db_prefix}counter` SET `replies`='{$count_new}'");
		}
	} elseif ($job=='addmessage') { // Is a message
		$plusquery_c='';
		$targettable="{$db_prefix}messages";
		$currentmaxid=$maxrecord['maxmessagepid']+1;
		$alter_column='maxmessagepid';
		if ($suspectspam!=1) { //Not a spam
			$blog->query("UPDATE `{$db_prefix}counter` SET `messages`=`messages`+1");
		}
	} else { // Edit reply or message
		acceptrequest('repid');
		$repid=floor($repid);
		$valuedtable=($job=='editreply') ? 'replies' : 'messages';
		$try=$blog->getbyquery("SELECT * FROM `{$db_prefix}{$valuedtable}` WHERE `repid`='{$repid}' AND `repip`='{$userdetail['ip']}'");
		if ($try['repid']!=$repid) catcherror($lnc[303]);
		if (!empty($try['adminreptime'])) catcherror($lnc[303]);
		if (time()-$try['reptime']>$mbcon['editcomment']) catcherror($lnc[303]);
	}

	$reptime=time();

	if ($suspectspam==1) {
		$reproperty+=2;
	}

	//Start storage
	if ($job=='addreply' || $job=='addmessage') {
		setcookie ('lastpost', time(), time()+$permission['MinPostInterval']);
		plugin_runphp('visitorsubmit');
		$blog->query("INSERT INTO  `{$targettable}` VALUES ('{$currentmaxid}', '{$reproperty}', {$plusquery_c} '{$reptime}', '{$replierid}', '{$replier}', '{$v_repemail}', '{$v_repurl}', '{$userdetail['ip']}', '{$v_content}', '{$html}', '{$ubb}', '{$emot}', '0', '', '0', '', '0', '', '0', '{$v_reppsw}', '', '', '', '', '', '', '')");
		$blog->query("UPDATE `{$db_prefix}maxrec` SET `{$alter_column}`='{$currentmaxid}'");
	} else {
		$blog->query("UPDATE `{$db_prefix}{$valuedtable}` SET `reptime`='{$reptime}', `repcontent`='{$v_content}' WHERE `repid`='{$repid}' LIMIT 1");
		$try['reptime']=$reptime;
		$try['repcontent']=$v_content;
	}
	if ($suspectspam==1) {
		$tipsplus=($mbcon['censorall']=='1') ? "<br/>{$lnc[270]}" : "<br/>{$lnc[215]}";
		if ($ajax=='on') catcherror (strip_tags($tipsplus));
	}
	if (($job=='addreply'|| $job=='editreply') && $suspectspam!=1) {
		include("admin/cache_func.php");
		recache_latestreplies();
	}
	$returnurl=($job=='addmessage') ? "{$lnc[91]}|guestbook.php" : "{$lnc[5]}|".getlink_entry($v_id, $originblog['blogalias']).'#topreply';
	if ($ajax!='on') catchsuccess("{$lnc[216]}{$tipsplus}", array($returnurl, "{$lnc[163]}|index.php"));
	else { //Deal with ajax
		$m_b=new getblogs;
		if ($job=='addreply' || $job=='addmessage') {
			$eachreply=array('repid'=>$currentmaxid, 'reproperty'=>$reproperty, 'blogid'=>$v_id, 'reptime'=>$reptime, 'replierid'=>$replierid, 'replier'=>$replier, 'repemail'=>$v_repemail, 'repurl'=>$v_repurl, 'repip'=>$userdetail['ip'], 'repcontent'=>$v_content, 'html'=>$html, 'ubb'=>$ubb, 'emot'=>$emot, 'avatar'=>$userdetail['avatar'], 'reppsw'=>$v_reppsw);
		} else $eachreply=$try;
		if ($job=='addreply' || $job=='editreply') $output_single=$m_b->single_reply($eachreply, floor($onetimecounter));
		else $output_single=$m_b->single_message($eachreply, floor($onetimecounter));
		catchsuccess($output_single);
	}
}

if ($job=='search') {
	if ($mbcon['searchon']==0) catcherror ($lnc[217]);
	checkpermission('AllowSearch');

	//Check post interval
	$lastsearch=$_COOKIE['lastsearch'];
	if ((time()-$lastsearch)<$permission['SearchInterval']) catcherror($lnc[218].$permission['SearchInterval']. $lnc[219]);

	acceptrequest('keyword,searchmethod');
	if (strlen($keyword)<$mbcon['keymin'] || strlen($keyword)>$mbcon['keymax'] || !$searchmethod) {
		catcherror ($lnc[220]);
	}
	if (preg_search($keyword, $forbidden['nosearch'])) catcherror ($lnc[221]);
	$keyword=safe_convert($keyword);
	$keywordgroup=@explode(' ', $keyword);
	$keywordgroup=array_values(array_unique($keywordgroup));
	if ($searchmethod!=1 && $searchmethod!=5 && $permission['FulltextSearch']!=1) catcherror ($lnc[222]);
	$sqlconditions=array();
	switch ($searchmethod) {
		case 1:
			$target_table="{$db_prefix}blogs";
			$target_id="blogid";
			$extralimit=($permission['SeeHiddenEntry']==1) ? " AND `property`<>'3'" : " AND `property`<=1"; 
			foreach ($keywordgroup as $keyword) {
				$sqlconditions[]="`title` LIKE '%{$keyword}%'";
			}
			$sqlcondition=@implode(' AND ', $sqlconditions).$extralimit;
			break;
		case 2:
			$target_table="{$db_prefix}blogs";
			$target_id="blogid";
			$extralimit=($permission['SeeHiddenEntry']==1) ? " AND `property`<>'3'" : " AND `property`<=1"; 
			foreach ($keywordgroup as $keyword) {
				$sqlconditions[]="(`content` LIKE '%{$keyword}%'  OR `title` LIKE '%{$keyword}%')";
			}
			$sqlcondition=@implode(' AND ', $sqlconditions).$extralimit;
			break;
		case 3:
			$target_table="{$db_prefix}replies";
			$target_id="repid";
			$extralimit=($permission['SeeHiddenReply']==1) ? " AND `reproperty`<=1" : " AND `reproperty`='0'"; 
			foreach ($keywordgroup as $keyword) {
				$sqlconditions[]="`repcontent` LIKE '%{$keyword}%'";
			}
			$sqlcondition=@implode(' AND ', $sqlconditions).$extralimit;
			break;
		case 4:
			$target_table="{$db_prefix}messages";
			$target_id="repid";
			$extralimit=($permission['SeeHiddenReply']==1) ? " AND `reproperty`<=1" : " AND `reproperty`='0'"; 
			foreach ($keywordgroup as $keyword) {
				$sqlconditions[]="`repcontent` LIKE '%{$keyword}%'";
			}
			$sqlcondition=@implode(' AND ', $sqlconditions).$extralimit;
			break;
		case 5:
			$target_table="{$db_prefix}tags";
			$target_id="tagname";
			foreach ($keywordgroup as $keyword) {
				$sqlconditions[]="`tagname` LIKE '%{$keyword}%'";
			}
			$sqlcondition=@implode(' AND ', $sqlconditions).$extralimit;
			break;
		default:
			$target_table="{$db_prefix}blogs";
			$target_id="blogid";
			$extralimit=($permission['SeeHiddenEntry']==1) ? " AND `property`<>'3'" : " AND `property`<=1"; 
			foreach ($keywordgroup as $keyword) {
				$sqlconditions[]="`title` LIKE '%{$keyword}%'";
			}
			$sqlcondition=@implode(' AND ', $sqlconditions).$extralimit;
	}
	$results=$blog->getarraybyquery("SELECT `{$target_id}` FROM `{$target_table}` WHERE {$sqlcondition} LIMIT 0,{$mbcon['maxresults']}");
	$result=$results[$target_id];
	$count_result=(is_array($result)) ? (count($result)) : 0;
	setcookie ('lastsearch', time(), time()+$permission['SearchInterval']);
	if ($count_result==0) {
		$showresult="<br/><div align='center'><span style='font-size: 14px;'>{$lnc[223]}</span></div><br/>";
		$mainpart=$t->set('contentpage', array('title'=>"{$lnc[224]} {$keyword}", 'contentbody'=>$showresult));
		announcebar();
		$bodymenu=$t->set('mainpage', array('pagebar'=>'', 'iftoppage'=>'none', 'ifbottompage'=>'none', 'ifannouncement'=>$ifannouncement, 'topannounce'=>$topannounce, 'mainpart'=>$mainpart, 'currentpage'=>'', 'previouspageurl'=>'', 'nextpageurl'=>'', 'turningpages'=>'', 'totalpages'=>'', 'previouspageexists'=>'', 'nextpageexists'=>''));
		$pagetitle="{$lnc[225]} - ";
	} else {
		$pinch=array();
		foreach ($result as $item) {
			$pinch[]="{$item}";
		}
		$pinchall="<?PHP exit;?>\n{$keyword}\n{$searchmethod}\n".@implode(',', $pinch);
		$sid=md5(time().$userdetail['ip']);
		$keyword=urlencode($keyword);
		if (!writetofile ("{$db_tmpdir}/{$sid}.php", $pinchall)) catcherror ($lnc[226]);
		@header("Content-Type: text/html; charset=utf-8");
		$t=new template;
		$t->showtips($lnc[227], $lnc[228], "{$lnc[229]}|visit.php?job=viewresult&amp;sid={$sid}", true);
	}
}

if ($job=='viewresult') {
	acceptrequest('sid');
	$sid=basename($sid);
	if (!file_exists("{$db_tmpdir}/{$sid}.php")) catcherror ($lnc[226]);
	$results=file("{$db_tmpdir}/{$sid}.php");
	$searchmethod=trim($results[2]);
	$keyword=trim($results[1]);
	$tmp_results3=@explode(',', trim($results[3]));
	for ($i=0; $i<count($tmp_results3); $i++) {
		$tmp_results3[$i]="'".$tmp_results3[$i]."'";
	}
	$result=@implode(',', $tmp_results3);
	$start_id=($page-1)*$mbcon['listitemperpage'];
	$counter_now=substr_count($result, ',')+1;
	$m_b=new getblogs;
	$urlref="visit.php?job=viewresult&amp;sid={$sid}";
	if ($searchmethod==1 || $searchmethod==2) {
		$records=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE `blogid` in ({$result}) ORDER BY `sticky` DESC LIMIT {$start_id},{$mbcon['listitemperpage']}");
		$pagebar=$m_b->make_pagebar($page, $mbcon['pagebaritems'], $urlref, $counter_now, $mbcon['listitemperpage']);
		$listbody=$m_b->make_excerption($records, 'list');
		$section_body_main[]=$m_b->make_list(@implode('', $listbody));
	}

	if ($searchmethod==3) {
		$records=$blog->getgroupbyquery("SELECT t1.*, t2.title, t2.blogalias FROM `{$db_prefix}replies` t1 INNER JOIN `{$db_prefix}blogs` t2 ON t2.blogid=t1.blogid WHERE t1.repid in  ({$result}) ORDER BY t1.reptime DESC LIMIT {$start_id}, {$mbcon['listitemperpage']}");
		for ($i=0; $i<count($records); $i++) {
			$records[$i]['repcontent']="<strong>{$lnc[71]}</strong><a href=\"".getlink_entry($records[$i]['blogid'], $records[$i]['blogalias'])."\">{$records[$i]['title']}</a><br/><strong>{$lnc[76]}</strong>".$records[$i]['repcontent'];
		}
		$section_body_main[]=$m_b->make_replies($records);
		$pagebar=$m_b->make_pagebar ($page, $mbcon['pagebaritems'], $urlref, $counter_now, $mbcon['listitemperpage']);
	}

	if ($searchmethod==4) {
		$records=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}messages` WHERE `repid` in ({$result}) ORDER BY `reptime` DESC LIMIT {$start_id}, {$mbcon['listitemperpage']}");
		$section_body_main[]=$m_b->make_messages($records);
		$pagebar=$m_b->make_pagebar ($page, $mbcon['pagebaritems'], $urlref, $counter_now, $mbcon['listitemperpage']);
	}

	if ($searchmethod==5) {
		$alltags=$blog->getarraybyquery("SELECT * FROM `{$db_prefix}tags` WHERE `tagname` in ({$result})");
		for ($i=0; $i<count($alltags['tagname']); $i++) {	
			$eachtag_encoded=urlencode(urlencode($alltags['tagname'][$i]));
			$urlref=getlink_tags($eachtag_encoded);
			if ($mbcon['tagunderlinetospace']==1) 	$alltags['tagname'][$i]=str_replace('_', ' ', $alltags['tagname'][$i]);
			$tag_show[]="<a href=\"{$urlref}\" title=\"{$lnc[188]}{$alltags['tagcounter'][$i]}\" rel=\"tag\">{$alltags['tagname'][$i]}</a>";
		}
		if (is_array($tag_show)) {
			$tagshow=@implode(" &nbsp; ", $tag_show);
		}
		else $tagshow=$lnc[189];
		$t=new template;
		$section_body_main[]=$t->set('taglist', array('tagcategory'=>$lnc[230], 'tagcontent'=>$tagshow, 'tagextra'=>''));
	}

	$iftoppage=($mbcon['pagebarposition']=='down') ? 'none' : 'block';
	$ifbottompage=($mbcon['pagebarposition']=='up') ? 'none' : 'block';
	$pagetitle="{$lnc[225]} - ";
	$mainpart=$t->set('contentpage', array('title'=>"{$lnc[224]} {$keyword}", 'contentbody'=>@implode('', $section_body_main)));
	announcebar();
	$bodymenu=$t->set('mainpage', array('pagebar'=>$pagebar, 'iftoppage'=>$iftoppage, 'ifbottompage'=>$ifbottompage, 'ifannouncement'=>$ifannouncement, 'topannounce'=>$topannounce, 'mainpart'=>$mainpart, 'currentpage'=>$pageitems['currentpage'], 'previouspageurl'=>$pageitems['previouspageurl'], 'nextpageurl'=>$pageitems['nextpageurl'], 'turningpages'=>$pageitems['turningpages'], 'totalpages'=>$pageitems['totalpages'], 'previouspageexists'=>$pageitems['previouspageexists'], 'nextpageexists'=>$pageitems['nextpageexists']));
}

if ($job=='getcontentonly') {
	acceptrequest('blogid,blogpsw,way');
	$blogid=floor($blogid);
	if ($permission['SeeHiddenEntry']!=1) {
		$partialquery="SELECT * FROM `{$db_prefix}blogs` WHERE `blogid`='{$blogid}' AND `property`<'2' LIMIT 1";
	} else {
		$partialquery="SELECT * FROM `{$db_prefix}blogs` WHERE `blogid`='{$blogid}' AND `property`<'3' LIMIT 1";
	}
	$m_b=new getblogs;
	$records=$m_b->getbyquery($partialquery);
	if (!is_array($records) || $records['blogid']!=$blogid) catcherror($lnc[211]);
	if ($blogpsw!=$records['blogpsw']) catcherror($lnc[297]);
	$return_main=$m_b->make_viewentry($records, $way, true);
	setcookie("entrypassword{$blogid}", $blogpsw);
	catchsuccess($return_main);
}

if ($job=='getreplyonly') {
	acceptrequest('repid,reppsw,way,onetimecounter');
	$repid=floor($repid);
	$reppsw=md5($reppsw);
	$tablename=($way=='reply') ? 'replies' : 'messages';
	$partialquery="SELECT * FROM `{$db_prefix}{$tablename}` WHERE `repid`='{$repid}' LIMIT 1";
	$m_b=new getblogs;
	$records=$m_b->getbyquery($partialquery);
	if (!is_array($records) || $records['repid']!=$repid) catcherror($lnc[305]);
	if ($reppsw!=$records['reppsw']) catcherror($lnc[297]);
	$records['reppsw']='';
	$records['reproperty']='0';
	if ($way=='reply') $output_single=$m_b->single_reply($records, floor($onetimecounter));
	else $output_single=$m_b->single_message($records, floor($onetimecounter));
	catchsuccess($output_single);
}

//OpenID support
if ($job=='openidaddreply' || $job=='openidaddmessage') {
	if ($mbcon['enableopenid']!='1') catcherror($lnc[315].$lnc[319]);
	$lastpost=$_COOKIE['lastpost'];
	if (($nowtime['timestamp']-$lastpost)<$permission['MinPostInterval']) catcherror($lnc[210]);
	$findintable=($job=='openidaddreply') ? 'replies' : 'messages';
	$findreplies=$blog->getbyquery("SELECT * FROM `{$db_prefix}{$findintable}` WHERE `repip`='{$userdetail['ip']}' ORDER BY `reptime` DESC LIMIT 1");
	if ($findreplies['repip']==$userdetail['ip']) {
		if (($nowtime['timestamp']-$findreplies['reptime'])<$permission['MinPostInterval']) catcherror($lnc[210]); 
	}

	acceptrequest('openid_url,stat_html,stat_ubb,stat_emot,stat_property,v_content,v_id,v_security,onetimecounter');

	if (!$openid_url) catcherror($lnc[212]);
	$v_id=intval(trimplus($v_id));
	if ($job=='openidaddreply') {
		checkpermission('Reply');
		if ($permission['SeeHiddenEntry']!=1) $limitmore="AND `property`<>2";
		$originblog=$blog->getbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE `blogid`='{$v_id}' AND `property`<>1  AND `property`<>3 {$limitmore}");
		if ($originblog['blogid']!=$v_id) $cancel=$lnc[211];
		else {
			$allowedgp=@explode('|', $originblog['permitgp']);
			if ($originblog['permitgp']!='' && !@in_array ($userdetail['usergroup'], $allowedgp)) $cancel=$lnc[211];
		}
	} else checkpermission('LeaveMessage');
	catcherror ($cancel);

	if (preg_search($openid_url, $forbidden['banword']) || preg_search($openid_url, $forbidden['keep'])) $cancel=$lnc[158];
	if ($config['validation']==1) {
		if ($db_defaultsessdir!=1) session_save_path("./{$db_tmpdir}");
		session_cache_limiter("private, must-revalidate");
		session_start();
		if ($v_security=='' || strtolower($v_security)!=strtolower($_SESSION['code'])) $cancel=$lnc[165];
	}
	catcherror ($cancel);
	$v_content=urlencode($v_content);
	$v_replier=urlencode($openid_url);
	$openidtojob=str_replace('openid', '', $job);


	$openid = $openid_url;
	$process_url = "{$config['blogurl']}/visit.php?job=openidsubmit&todojob={$openidtojob}&ajax=off&v_replier={$v_replier}&stat_html={$stat_html}&stat_ubb={$stat_ubb}&stat_emot={$stat_emot}&stat_property={$stat_property}&v_id={$v_id}&onetimecounter={$onetimecounter}&v_security={$v_security}&v_content={$v_content}";
	prepareOpenID($openid, $process_url);
}

