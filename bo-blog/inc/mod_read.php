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

if (!defined('VALIDREQUEST')) die ('Access Denied.');

$is_save=$is_preview=false;
if (strstr($itemid, 'save_')) {
	@list ($is_save, $itemid)=@explode('_', $itemid);
}
if (strstr($itemid, 'preview_')) {
	$is_preview=true;
}

$itemid=floor($itemid);

$m_b=new getblogs;

if ($is_preview) {
	checkpermission('AddEntry');
	acceptrequest('title,property,category,tags,sticky,html,ubb,emot,sweather,permitgp,pinged,changemytime,resend,autoping,starred,blogpsw,useeditor,summaryway,blogalias,originsrc,comefrom,forcedraft,clearautosaver', 0, 'post');

	$blogid=-1000;
	$category=@floor($category);
	$sticky=@floor($sticky);
	$htmlstat=@floor($html);
	$ubbstat=@floor($ubb);
	$emotstat=@floor($emot);
	$starred=@floor($starred);
	$summaryway=@floor($summaryway);
	$property=$frontpage=0;
	$entrysummary=$pinged='';
	$title=safe_convert(stripslashes($title));
	//Get content
	$content=$_POST['content'];
	//If magic quotes is on, strip the slashes automatically added
	if ($mqgpc_status==1) $content=stripslashes($content);
	$content=preg_replace("/\[php\](.+?)\[\/php\]/ise", "phpcode3('\\1')", $content);
	if ($htmlstat!=1 || $permission['Html']!=1) {
		$content=preg_replace("/\[code\](.+?)\[\/code\]/ise", "phpcode2('\\1')", $content);
		$content=safe_convert($content, 0, 1);
	} else {
		$content=preg_replace("/\[code\](.+?)\[\/code\]/ise", "phpcode('\\1')", $content);
		$content=safe_convert($content, 1, 1);
	}
	if ($comefrom && $originsrc) {
		$comefrom=safe_convert($comefrom);
		$originsrc=safe_convert($originsrc);
	} else {
		$comefrom=$originsrc='';
	}
	if ($tags) {
		$tags_array=@explode(' ', mystrtolower(trim($tags)));
		$tags_array_all=array_unique($tags_array);
		$tags=@implode(' ', $tags_array_all);
		$tags=safe_convert($tags);
		$tags=str_replace('&nbsp;', '', $tags);
		$tags_array=@explode(' ', $tags);
		$tags='>'.str_replace(' ', '>', $tags).'>';
	} else $tags='';

	$currentuserid=$userdetail['userid'];
	if ($changemytime==1) {
		acceptrequest('newyear,newmonth,newday,newhour,newmin,newsec');
		$finaltime=gmmktime($newhour,$newmin,$newsec,$newmonth,$newday,$newyear)-$config['timezone']*3600;
	}
	else $finaltime=time();


	$records=array();
	$records[0]=array('blogid'=>$blogid, 'title'=>$title, 'pubtime'=>$finaltime, 'authorid'=>$currentuserid, 'replies'=>0, 'tbs'=>0, 'views'=>0, 'property'=>$property, 'category'=>$category, 'tags'=>$tags, 'sticky'=>$sticky, 'htmlstat'=>$htmlstat, 'ubbstat'=>$ubbstat, 'emotstat'=>$emotstat, 'content'=>$content, 'editorid'=>0, 'edittime'=>0, 'weather'=>$sweather, 'mobile'=>0, 'pinged'=>$pinged, 'permitgp'=>'', 'starred'=>$starred, 'blogpsw'=>$blogpsw, 'frontpage'=>$frontpage, 'entrysummary'=>$entrysummary, 'comefrom'=>$comefrom, 'originsrc'=>$originsrc, 'blogalias'=>$blogalias);


}
else {
	$order=($mbcon['replyorder']=='0') ? "DESC" : "ASC";
	$start_id=($page-1)*$mbcon['replyperpage'];
	$querycondition=($use_blogalias) ? "`blogalias`='{$blogaliasp}'" : "`blogid`='{$itemid}'";
	if ($permission['SeeHiddenEntry']!=1) {
		$partialquery="SELECT * FROM `{$db_prefix}blogs` WHERE {$querycondition} AND `property`<'2' LIMIT 0, 1";
		$partialquery2="WHERE `property`<'2'";
	} else {
		$partialquery="SELECT * FROM `{$db_prefix}blogs` WHERE {$querycondition} AND `property`<'3' LIMIT 0, 1";
		$partialquery2=" WHERE property<'3'";
	}
	$records=$m_b->single_record($partialquery, $partialquery2);
}


if (is_array($records)) {
	$itemid=$records[0]['blogid'];
	if ($is_save && $mbcon['txtdown']=='1') $m_b->save_a_text($records[0]);
	else $section_body_main=$m_b->make_viewentry($records[0]);
} else {
	getHttp404($lnc[186]);
}

//Load plugins
$section_body_main[0]=plugin_get('entrybegin').$section_body_main[0];
$section_body_main[]=plugin_get('commentbegin');


if ($records[0]['replies']!=0 || $records[0]['tbs']!=0)  {
	if ($mbcon['avatar']==1 || $mbcon['usergravatar']==1 || $mbcon['visitorgravatar']==1) {
		$replyarray=$m_b->getgroupbyquery("SELECT t1.*, t2.userid, t2.avatar FROM `{$db_prefix}replies` t1 LEFT JOIN `{$db_prefix}user` t2 ON t1.replierid=t2.userid WHERE t1.blogid='{$itemid}' AND (t1.reproperty<2 OR t1.reproperty=4) ORDER BY t1.reptime {$order}  LIMIT {$start_id}, {$mbcon['replyperpage']}");
	} else {
		$replyarray=$m_b->getgroupbyquery("SELECT * FROM `{$db_prefix}replies` WHERE blogid='{$itemid}' AND (reproperty<2 OR reproperty=4) ORDER BY reptime {$order}  LIMIT {$start_id}, {$mbcon['replyperpage']}");

	}
	if ($replyarray[0]['repid']!='') {
		$section_body_main[]=$m_b->make_replies($replyarray);
		$outurl=getlink_entry($itemid, $records[0]['blogalias'], '%s', $part);
		$pageway=1;
		$innerpages=$m_b->make_pagebar ($page, $mbcon['pagebaritems'], $outurl, $records[0]['replies']+$records[0]['tbs'], $mbcon['replyperpage'], $pageway);
	} else {
		checkPageValidity($page, 1);
	}
} else {
	checkPageValidity($page, 1);
}
if ($records[0]['property']!='1' && $permission['Reply']==1) {
	$form_reply=$m_b->make_visit_form($lnc[187], $records[0]['blogid'], "visit.php?job=addreply");
}

$section_body_main[]=$t->set('endviewentry', array('form_reply'=>$form_reply, 'innerpages'=>$innerpages));

if (!$is_preview) {
	//Read counter
	$allreads=$_COOKIE['readentry'];
	if (!strstr($allreads, "{$itemid},")) {
		$m_b->query("UPDATE LOW_PRIORITY `{$db_prefix}blogs` SET `views`=`views`+1 WHERE `blogid`='{$itemid}'");
		@setcookie('readentry', $allreads."{$itemid},", time()+7200);
	}
}


//announcebar();
$ifannouncement="none";
$bodymenu=$t->set('mainpage', array('pagebar'=>'', 'iftoppage'=>'none', 'ifbottompage'=>'none', 'ifannouncement'=>$ifannouncement, 'topannounce'=>$topannounce, 'mainpart'=>@implode('', $section_body_main), 'currentpage'=>'', 'previouspageurl'=>'', 'nextpageurl'=>'', 'turningpages'=>'', 'totalpages'=>'', 'previouspageexists'=>'', 'nextpageexists'=>''));
$pagetitle="{$records[0]['title']} - ";