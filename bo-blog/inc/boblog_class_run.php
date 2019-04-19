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

class boblog {
//Bo-Blog 2.x Database Control Class (c) Bob Shen
	function boblog() {
		global $db_connected;
		if (empty($db_connected)) $this->connectdb();
	}

	function connectdb() {
		global $db_connected, $db_server, $db_username, $db_password, $db_name;
		db_connect($db_server, $db_username, $db_password, $db_name);
		$db_connected=1;
	}

	function getsinglevalue($tablename) {
		$result=db_query("SELECT * FROM `$tablename` LIMIT 0,1");
		$fetchresult=db_fetch_array($result);
		return $fetchresult;
	}

	function getbyquery($query) {
		$result=db_query($query);
		$fetchresult=db_fetch_array($result);
		return $fetchresult;
	}

	function getgroupbyquery($query) {
		$result=db_query($query);
		$i=0;
		while ($row=db_fetch_array($result)) {
			while (@list($key, $val)=@each($row)) {
				$fetchresult[$i][$key]=$val;
			}
			$i+=1;
		}
		return $fetchresult;
	}

	function getarraybyquery($query) {
		$result=db_query($query);
		$i=0;
		while ($row=db_fetch_array($result)) {
			while (@list($key, $val)=@each($row)) {
				$fetchresult[$key][$i]=$val;
			}
			$i+=1;
		}
		return $fetchresult;
	}

	function query($myquery) {
		$result=db_query($myquery);
		return $result;
	}

	function countbyquery($myquery) {
		$result=db_query($myquery);
		$row=db_fetch_row($result);
		return $row[0];
	}
}

class template {
	function template() {
		global $elements, $template;
		if (empty($elements)) {
			global $lnc, $tptvalue;
			if (!defined('elementfile') || !file_exists(elementfile)) define('elementfile', 'template/default/elements.php');
			if (!file_exists(elementfile)) die ("Cannot find template. You may need to reinstall the program.");
			include_once(elementfile);
			if (!$template['moreimages']) $template['moreimages']="images";
			if (!$template['sysver']) {
				$template['sysver']='5.1';
			}
			else {
				$template['sysver']=basename($template['sysver']);
			}
			if (is_file("inc/tpltune/{$template['sysver']}.php")) include_once("inc/tpltune/{$template['sysver']}.php");
			if (is_file("inc/tpltune/{$template['sysver']}.css")) {
				global $csslocation;
				$csslocation.="<link rel=\"stylesheet\" rev=\"stylesheet\" href=\"inc/tpltune/{$template['sysver']}.css\" type=\"text/css\" media=\"all\" />\n";
			}
			$tptvalue=array();
		}
	}

	function set($elementname, $array, $inherit=0) {
		global $elements, $tptvalue;
		if ($inherit==1) global $content;
		$content[$elementname]=$elements[$elementname];

		$a=preg_match("/<!-- php --><!--(.+?)--><!-- \/php -->/is", $content[$elementname], $phpcode_array);
		if ($a!=0) {
			$phpcode=$phpcode_array[1];
			eval($phpcode);
			$content[$elementname]=preg_replace("/<!-- php --><!--(.+?)--><!-- \/php -->/is", '', $content[$elementname]);
		}

		while (@list($parser, $value) = @each ($array)) {
			$tptvalue[$parser]=$value;
			$content[$elementname]=str_replace("{".$parser."}", $value, $content[$elementname]);
		}
		if ($inherit==0) return (@implode('', $content));
	}

	function publish ($contentstr, $screen=false) {
		global $tptvalue;
		$globalvar=array();
		preg_replace("/<!--global:{(.+?)}-->/ie", "\$globalvar['\\1']=\$tptvalue['\\1']", $contentstr);
		while (@list($parser, $value) = @each ($globalvar)) {
			$contentstr=str_replace("<!--global:{".$parser."}-->", $value, $contentstr);
		}
		if ($screen) echo ($contentstr);
		else return $contentstr;
	}

	function publish_all($screen=false) { //Only works while inherit=1
		global $content;
		if (is_array($content)) {
			$content=@implode('', $content);
		}
		if ($screen) echo ($content);
		else return $content;
	}

	function showtips($title, $tips, $links='', $enableautojump=false) {
		global $config, $permission, $template, $lnc, $baseurl, $logstat, $langname;
		$previouspage=($_SERVER['HTTP_REFERER']=='') ? "javascript: history.back(1);" : $_SERVER['HTTP_REFERER'];
		if ($permission['CP']==1 && !defined('isLogout')) {
			$admin_plus=" | <a href=\"admin.php\">{$lnc[15]}</a>";
		}
		if ($logstat!=1 && !defined('isLogin')) {
			$admin_plus=" | <a href=\"login.php\">{$lnc[89]}</a>";
		}
		if ($links) {
			if ($enableautojump) $tips.="<br/>{$lnc[310]}<ul>";
			else $tips.="<ul>";
			if (is_array($links)) {
				$isfirstlink=true;
				foreach ($links as $onelink) {
					$current_link=@explode('|', $onelink);
					if ($current_link[1]=='<') $current_link[1]=$previouspage;
					$tips.="<li><a href=\"{$current_link[1]}\">{$current_link[0]}</a></li>";
					if ($isfirstlink) {
						$autojump="<meta http-equiv=\"refresh\" content=\"3; url={$current_link[1]}\" />";
						$isfirstlink=false;
					}
				}
			} else {
				$current_link=@explode('|', $links);
				if ($current_link[1]=='<') $current_link[1]=$previouspage;
				$tips.="<li><a href=\"{$current_link[1]}\">{$current_link[0]}</a></li>";
				$autojump="<meta http-equiv=\"refresh\" content=\"3; url={$current_link[1]}\" />";
			}
			$tips.="</ul>";
		}
		$csslocation.=$baseurl;
		if ($enableautojump) $csslocation.=$autojump;
		for ($i=0; $i<count($template['css']); $i++) {
			$csslocation.="<link rel=\"stylesheet\" rev=\"stylesheet\" href=\"{$template['css'][$i]}\" type=\"text/css\" media=\"all\" />\n";
		}
		$tipsin=$this->set('tips', array('tips'=>$tips, 'title'=>$title, 'csslocation'=>$csslocation, 'blogname'=>$config['blogname'], 'blogdesc'=>$config['blogdesc'], 'admin_plus'=>$admin_plus, 'language'=>$langname['languagename']));
		echo $tipsin;
		exit();
	}
}

class getblogs extends boblog {
	var $total_rows;
	var $total_pages;
	function new_record_array ($partialquery, $perpagevolume, $currentpage) {
		global $db_prefix;
		$start_id=($currentpage-1)*$perpagevolume;
		$result=db_query("SELECT * FROM `{$db_prefix}blogs` {$partialquery} LIMIT $start_id, $perpagevolume");
		$this->total_rows=db_num_rows($result);
		$i=0;
		while ($row=db_fetch_array($result)) {
			while (list($key, $val)=each($row)) {
				$fetchresult[$i][$key]=$val;
			}
			$i+=1;
		}
		return $fetchresult;
	}

	function single_record ($partialquery, $partialquery2='') {
		global $db_prefix, $mbcon;
		$result=$this->getgroupbyquery($partialquery);
		if (!$result) return false;
		if ($partialquery2) {
			if ($mbcon['prevnextshowsamecate']=='1') {
				$setcateplus=" AND `category`='".floor($result[0]['category'])."'";
			} else $setcateplus='';
			$previousresult=$this->getbyquery("SELECT `blogid`,`title`,`blogalias` FROM `{$db_prefix}blogs` {$partialquery2} AND `pubtime`<'{$result[0]['pubtime']}' {$setcateplus} ORDER BY `pubtime` DESC LIMIT 1");
			$nextresult=$this->getbyquery("SELECT `blogid`,`title`,`blogalias` FROM `{$db_prefix}blogs` {$partialquery2} AND `pubtime`>'{$result[0]['pubtime']}' {$setcateplus} ORDER BY `pubtime` ASC LIMIT 1");
			$result[0]['previoustitle']=($mbcon['linklength']==0)? $previousresult['title'] : msubstr($previousresult['title'], 0, $mbcon['linklength']);
			$result[0]['previousid']=$previousresult['blogid'];
			$result[0]['nexttitle']=($mbcon['linklength']==0)? $nextresult['title'] : msubstr($nextresult['title'], 0, $mbcon['linklength']);
			$result[0]['nextid']=$nextresult['blogid'];
			$result[0]['previousblogalias']=$previousresult['blogalias'];
			$result[0]['nextblogalias']=$nextresult['blogalias'];
		}
		return $result;
	}

	function reply_record_array ($perpagevolume, $currentpage) {
		global $db_prefix, $mbcon;
		$start_id=($currentpage-1)*$perpagevolume;
		$order="DESC";
		if ($flset['avatar']!=1 && ($mbcon['avatar']==1 || $mbcon['usergravatar']==1 || $mbcon['visitorgravatar']==1)) {
			$patialquery="SELECT t1.*, t2.userid, t2.avatar FROM `{$db_prefix}messages` t1 LEFT JOIN `{$db_prefix}user` t2 ON t1.replierid=t2.userid WHERE t1.reproperty<2 ORDER BY t1.reptime {$order}  LIMIT {$start_id}, {$perpagevolume}";
		} else {
			$patialquery="SELECT * FROM `{$db_prefix}messages` WHERE `reproperty`<>'2' AND `reproperty`<>'3' ORDER BY `reptime` {$order} LIMIT $start_id, $perpagevolume";
		}
		$fetchresult=$this->getgroupbyquery($patialquery);
		return $fetchresult;
	}

	function make_excerption ($arrayrecords, $way='excerpt') {
		global $mbcon, $section_body, $permission, $adminlist, $userdetail, $config, $categories, $weather, $t, $section_bodys, $lnc;
		if (!@is_a($t, 'template')) {
			$t=new template;
		}
		if (is_array($arrayrecords)) {
			foreach ($arrayrecords as $entry) {
				if ($entry['permitgp']!=='') {
					$allowedgp=@explode('|', $entry['permitgp']);
					if (!@in_array($userdetail['usergroup'], $allowedgp)) {
						$entry['content']="<div align=\"center\">{$lnc[16]}</div>";
					}
				}
				$this->output($entry, $way);
			}
		}
		return $section_bodys;
	}

	function make_viewentry ($entry, $way='viewentry', $contentonly=false) {
		global $mbcon, $section_body, $permission, $adminlist, $userdetail, $config, $categories, $weather, $t, $section_bodys, $lnc;
		if (!@is_a($t, 'template')) {
			$t=new template;
		}
		if ($entry['permitgp']) {
			$allowedgp=@explode('|', $entry['permitgp']);
			if (!@in_array($userdetail['usergroup'], $allowedgp)) {
				catcherror($lnc[16]);
				return;
			}
		}
		if ($contentonly) {
			$return_str=$this->output($entry, $way, $contentonly);
			return $return_str;
		}
		else {
			$this->output($entry, $way, $contentonly);
			return $section_bodys;
		}
	}

	function make_replies ($arrayrecords) {
		global $mbcon, $section_body, $permission, $adminlist, $userdetail, $config, $categories, $weather, $t, $section_bodys;
		for ($i=0; $i<count($arrayrecords); $i++) {
			$eachreply=$arrayrecords[$i];
			$output_rep[]=$this->single_reply ($eachreply, $i);
		}
		$output_rep_all=@implode('', $output_rep);
		return $output_rep_all;
	}

	function single_reply ($eachreply, $i=0) {
		global $mbcon, $section_body, $permission, $adminlist, $userdetail, $config, $categories, $weather, $t, $section_bodys, $lnc, $nowtime, $flset;
		if (!@is_a($t, 'template')) {
			$t=new template;
		}
		if ($eachreply['reproperty']==0 || $eachreply['reproperty']==1) { // A Normal Reply
			unset ($replier, $replierip, $replytime, $addadminreply, $deladminreply, $editadminreply, $replycontent, $adminreplycontent, $ifadminreplied, $adminrepliershow, $adminreplycontent, $adminreplybody, $replieremail, $replierhomepage, $avataraddress, $avatardetail); //UNSET
			if ($eachreply['replierid']==-1) {
				$replier=$eachreply['replier'];
				if ($flset['avatar']!=1 && $mbcon['visitorgravatar']=='1' && !empty($eachreply['repemail'])) { //Avatars for nonusers
					$avataraddress=get_gravatar($eachreply['repemail']);		
				}
			}
			else {
				$replier="<a href=\"".getlink_user($eachreply['replierid'])."\" target=\"_blank\" title=\"{$lnc[17]}\">{$eachreply['replier']}</a>";
				//Avatars for users
				if ($flset['avatar']!=1) {
					$avatardetail=@explode('|', $eachreply['avatar']);
					if ($avatardetail[0]=='1' && $mbcon['usergravatar']=='1') {
						$avataraddress=get_gravatar($eachreply['repemail']);
					}	elseif ($mbcon['avatar']=='1' && !empty($avatardetail[1])) {
						$avataraddress="images/avatars/{$avatardetail[1]}";
					}
				}
			}
			if ($permission['SeeIP']==1) $replierip="<a href=\"{$mbcon['ipsearch']}{$eachreply['repip']}\" target=\"_blank\"><img src=\"{$mbcon['images']}/ip.gif\" border=\"0\" alt=\"IP\" title=\"IP: {$eachreply['repip']}\" /></a>";
			if ($eachreply['repemail']) $replieremail="<a href=\"mailto:{$eachreply['repemail']}\"><img src=\"{$mbcon['images']}/email.gif\" border=\"0\" alt=\"Email\" title=\"{$lnc[18]}\" /></a>";
			if ($eachreply['repurl']) $replierhomepage="<a href=\"{$eachreply['repurl']}\" target=\"_blank\"><img src=\"{$mbcon['images']}/homepage.gif\" border=\"0\" alt=\"Homepage\" title=\"{$lnc[19]}\" /></a>";
			$replytime=zhgmdate("{$mbcon['timeformat']} H:i", ($eachreply['reptime']+3600*$config['timezone'])); 
			if ($permission['ReplyReply']==1) {
				$addadminreply="<a href=\"javascript: showadminreply('com_{$eachreply['repid']}');\">[{$lnc[20]}]</a>";
				$deladminreply="<a href=\"javascript: showdeladminreply('{$eachreply['repid']}');\">[{$lnc[21]}]</a>";
				$delreply="<a href=\"javascript: showdelreply('{$eachreply['repid']}', '{$eachreply['blogid']}');\">[{$lnc[22]}]</a>";
				$blockreply="<a href=\"javascript: showblockreply('{$eachreply['repid']}', '{$eachreply['blogid']}');\">[{$lnc[290]}]</a>";
			}
			if ($eachreply['reproperty']==1 && $permission['SeeHiddenReply']!=1) { //No permission to view
				if ($eachreply['reppsw']!='') { //You can provide a Password to view the rep/mes
					$replycontent=$lnc[304];
					$replycontent.="<form action=\"javascript: getprotectedreply({$eachreply['repid']}, 'reply', {$i});\" method=\"post\"><input type=\"password\" name=\"reppsw\" id=\"reppsw{$eachreply['repid']}\" maxlength='12' size='8' class='text' /> <input type=\"submit\" value=\"{$lnc[25]}\" class='button' /></form>";
				} else $replycontent=$lnc[23];
			} else {
				$replycontent=$this->getcontent($eachreply['repcontent'],  $eachreply['html'], $eachreply['ubb'], $eachreply['emot']);
			}
			if ($userdetail['ip']==$eachreply['repip'] && ($nowtime['timestamp']-$eachreply['reptime']<$mbcon['editcomment']) && $eachreply['reppsw']=='' && empty($eachreply['adminreptime'])) { 			//Allow edit
				$rawreplycontent=safe_invert($eachreply['repcontent']);
				$expirereplytime=zhgmdate("{$mbcon['timeformat']} H:i", ($eachreply['reptime']+3600*$config['timezone']+$mbcon['editcomment'])); 
				$replycontent.="<br/><div class=\"commentbox-label\">{$lnc[300]} {$expirereplytime} {$lnc[301]} "."[<a href=\"javascript: showhidediv('editcomment{$eachreply['repid']}');\">{$lnc[302]}</a>]</div>";
				$replycontent.="<div id=\"editcomment{$eachreply['repid']}\" style='display: none;'><form action=\"javascript: ajax_editcomment({$eachreply['repid']}, 'reply', {$i});\" method=\"post\" id=\"formeditcomment{$eachreply['repid']}\"><textarea cols='55' rows='4'  id=\"editcomcontent{$eachreply['repid']}\" name='v_contentc'>{$rawreplycontent}</textarea><br/><input type='button' value='{$lnc[25]}' onclick=\"ajax_editcomment({$eachreply['repid']}, 'reply', {$i});\" class='button' /> <input type='reset' value='{$lnc[26]}' class='button' /></form></div>";
			}
			if (!empty($avataraddress)) { //Make avatar
				$avatarposition=($mbcon['leftavatar']=='1') ? "left" : "right";
				$avatarposition2=($mbcon['leftavatar']=='1') ? "right" : "left";
				$replycontent="<img src=\"{$avataraddress}\" alt=\"\" style=\"float: {$avatarposition}; padding-{$avatarposition2}: 5px; width: {$mbcon['avatarwidth']}px; height: {$mbcon['avatarheight']}px; \"/><div>{$replycontent}</div><div style=\"clear:both;\"></div>";
			}
			if ($eachreply['adminreptime'] && ($eachreply['reproperty']!=1 || $permission['SeeHiddenReply']==1 || $eachreply['reppsw']=='')) {
				$ifadminreplied="block";
				$adminreplier="<a href=\"".getlink_user($eachreply['adminrepid'])."\" target=\"_blank\" title=\"{$lnc[17]}\">{$eachreply['adminreplier']}</a>";
				$adminreptime=zhgmdate("{$mbcon['timeformat']} H:i", ($eachreply['adminreptime']+3600*$config['timezone']));
				$adminreplybody="<form action='admin.php?go=reply_editadminreply_{$eachreply['repid']}' id='formadminreply{$eachreply['repid']}' method='post'>";
				$adminreplybody.="{$lnc[24]} <br/><textarea cols='66' rows='3' name='adminreplycontent' id='adminreplycontent{$eachreply['repid']}'>".safe_invert($eachreply['adminrepcontent'])."</textarea><br/>";
				$adminreplybody.="<input type='button' value='{$lnc[25]}' onclick=\"ajax_adminreply_edit('{$eachreply['repid']}', 'reply'); return false;\" class='button'/> <input type='reset' value='{$lnc[26]}'  class='button'/> <input type='button' value='{$lnc[27]}' onclick=\"showhidediv('com_{$eachreply['repid']}');\" class='button'/></form>";
				if ($permission['ReplyReply']==1) $addadminreply="<a href=\"javascript: showhidediv('com_{$eachreply['repid']}');\">[{$lnc[20]}]</a>";
				$adminrepliershow="$adminreplier {$lnc[28]} $adminreptime";
				$adminreplycontent=$this->getcontent($eachreply['adminrepcontent']);
				if ($eachreply['adminrepedittime']) {
					$adminrepedittime=zhgmdate("{$mbcon['timeformat']} H:i", ($eachreply['adminrepedittime']+3600*$config['timezone']));
					$adminreplycontent.="<br/><span class='lastmodified'>{$lnc[29]} {$eachreply['adminrepeditor']} {$lnc[30]} {$adminrepedittime}</span>";
				}
				if ($permission['ReplyReply']!=1) $adminreplybody='';
			} else $ifadminreplied="none";
			$oddorcouplecss=($i%2==0) ? 'couple' : 'odd'; //added on 2006-11-14
			//Starting Template
			$output_single=$t->set('comment', array('replier'=>$replier, 'replierip'=>$replierip, 'replytime'=>$replytime,'addadminreply'=>$addadminreply,'deladminreply'=>$deladminreply,'editadminreply'=>$editadminreply,'delreply'=>$delreply,'blockreply'=>$blockreply,'replycontent'=>$replycontent,'ifadminreplied'=>$ifadminreplied,'adminrepliershow'=>$adminrepliershow,'adminreplycontent'=>$adminreplycontent,'commentid'=>"com_{$eachreply['repid']}", 'adminreplybody'=>$adminreplybody, 'replieremail'=>$replieremail, 'replierhomepage'=>$replierhomepage, 'oddorcouplecss'=>$oddorcouplecss));
			$output_single=plugin_get('eachcommentbegin').$output_single.plugin_get('eachcommentend'); //Added on 2008/10/2
			$output_single="<div id=\"blogcomment{$eachreply['repid']}\">".$output_single."</div>";
		} elseif ($eachreply['reproperty']==4) {//A Trackback
			unset ($tbtitle,$tbtime,$tburl,$tbblogname,$tbcontent,$delreply);
			$tbtitle=$eachreply['repemail'];
			$tbtime=zhgmdate("{$mbcon['timeformat']} H:i", ($eachreply['reptime']+3600*$config['timezone']));
			$tburl=$eachreply['repurl'];
			$tbblogname=$eachreply['replier'];
			$tbcontent=$eachreply['repcontent'];
			if ($permission['ReplyReply']==1) {
				$delreply="<a href=\"javascript: comfirmurl('admin.php?go=reply_deltb_{$eachreply['repid']}-{$eachreply['blogid']}');\">[{$lnc[31]}]</a>";
			}
			$output_single=$t->set('trackback', array('tbtitle'=>$tbtitle, 'tbtime'=>$tbtime, 'tburl'=>$tburl,'tbblogname'=>$tbblogname,'tbcontent'=>$tbcontent, 'delreply'=>$delreply, 'oddorcouplecss'=>$oddorcouplecss));
			$output_single=plugin_get('eachtbbegin').$output_single.plugin_get('eachtbend'); //Added on 2008/10/2
		}
		return $output_single;
	}

	function make_messages ($arrayrecords) {
		global $mbcon, $section_body, $permission, $adminlist, $userdetail, $config, $categories, $weather, $t, $section_bodys;
		$output_rep[]="<a name='topreply'></a>";
		for ($i=0; $i<count($arrayrecords); $i++) {
			$eachreply=$arrayrecords[$i];
			$output_rep[]=$this->single_message ($eachreply, $i);
		}
		$output_rep_all=@implode('', $output_rep);
		return $output_rep_all;
	}

	function single_message ($eachreply, $i=0) {
		global $mbcon, $section_body, $permission, $adminlist, $userdetail, $config, $categories, $weather, $t, $section_bodys, $lnc, $nowtime, $flset;
		if (!@is_a($t, 'template')) {
			$t=new template;
		}
		unset ($replier, $replierip, $replytime, $addadminreply, $deladminreply, $editadminreply, $replycontent, $adminreplycontent, $ifadminreplied, $adminrepliershow, $adminreplycontent, $adminreplybody, $avataraddress, $avatardetail); //UNSET
		if ($eachreply['replierid']==-1) {
			$replier=$eachreply['replier'];
			if ($flset['avatar']!=1 && $mbcon['visitorgravatar']=='1' && !empty($eachreply['repemail'])) { //Avatars for nonusers
				$avataraddress=get_gravatar($eachreply['repemail']);		
			}
		}
		else {
			$replier="<a href=\"".getlink_user($eachreply['replierid'])."\" target=\"_blank\" title=\"{$lnc[17]}\">{$eachreply['replier']}</a>";
			//Avatars for users
			if ($flset['avatar']!=1) {
				$avatardetail=@explode('|', $eachreply['avatar']);
				if ($avatardetail[0]=='1' && $mbcon['usergravatar']=='1') {
					$avataraddress=get_gravatar($eachreply['repemail']);
				}	elseif ($mbcon['avatar']=='1' && !empty($avatardetail[1])) {
					$avataraddress="images/avatars/{$avatardetail[1]}";
				}
			}
		}
		if ($permission['SeeIP']==1) $replierip="<a href=\"{$mbcon['ipsearch']}{$eachreply['repip']}\" target=\"_blank\"><img src=\"{$mbcon['images']}/ip.gif\" border=\"0\" alt=\"IP\" title=\"IP: {$eachreply['repip']}\" /></a>";
		$replytime=zhgmdate("{$mbcon['timeformat']} H:i", ($eachreply['reptime']+3600*$config['timezone'])); //Need Further Change
		if ($eachreply['repemail']) $replieremail="<a href=\"mailto:{$eachreply['repemail']}\"><img src=\"{$mbcon['images']}/email.gif\" border=\"0\" alt=\"Email\" title=\"{$lnc[18]}\" /></a>";
		if ($eachreply['repurl']) $replierhomepage="<a href=\"{$eachreply['repurl']}\" target=\"_blank\"><img src=\"{$mbcon['images']}/homepage.gif\" border=\"0\" alt=\"Homepage\" title=\"{$lnc[19]}\" /></a>";
		if ($permission['ReplyReply']==1) {
			$addadminreply="<a href=\"javascript: showadminreplyformessage('com_{$eachreply['repid']}');\">[{$lnc[20]}]</a>";
			$deladminreply="<a href=\"javascript: showdeladminreplyformessage('{$eachreply['repid']}');\">[{$lnc[21]}]</a>";
			$delreply="<a href=\"javascript: showdelreplyformessage('{$eachreply['repid']}');\">[{$lnc[32]}]</a>";
			$blockreply="<a href=\"javascript: showblockmessage('{$eachreply['repid']}', '{$eachreply['blogid']}');\">[{$lnc[291]}]</a>";
		}
		if ($eachreply['reproperty']==1 && $permission['SeeHiddenReply']!=1) { //No permission to view
			if ($eachreply['reppsw']!='') { //You can provide a Password to view the rep/mes
				$replycontent=$lnc[304];
				$replycontent.="<form action=\"javascript: getprotectedreply({$eachreply['repid']}, 'message', {$i});\" method=\"post\"><input type=\"password\" name=\"reppsw\" id=\"reppsw{$eachreply['repid']}\" maxlength='12' size='8' class='text' /> <input type=\"submit\" value=\"{$lnc[25]}\" class='button' /></form>";
			} else $replycontent=$lnc[23];
		} else {
			$replycontent=$this->getcontent($eachreply['repcontent'],  $eachreply['html'], $eachreply['ubb'], $eachreply['emot']);
		}
		if ($userdetail['ip']==$eachreply['repip'] && ($nowtime['timestamp']-$eachreply['reptime']<$mbcon['editcomment']) && $eachreply['reppsw']=='' && empty($eachreply['adminreptime'])) { //Allow edit
			$rawreplycontent=safe_invert($eachreply['repcontent']);
			$expirereplytime=zhgmdate("{$mbcon['timeformat']} H:i", ($eachreply['reptime']+3600*$config['timezone']+$mbcon['editcomment'])); 
			$replycontent.="<br/><div class=\"commentbox-label\">{$lnc[300]} {$expirereplytime} {$lnc[301]} "."[<a href=\"javascript: showhidediv('editcomment{$eachreply['repid']}');\">{$lnc[302]}</a>]</div>";
			$replycontent.="<div id=\"editcomment{$eachreply['repid']}\" style='display: none;'><form action=\"javascript: ajax_editcomment({$eachreply['repid']}, 'message', {$i});\" method=\"post\" id=\"formeditcomment{$eachreply['repid']}\"><textarea cols='55' rows='4'  id=\"editcomcontent{$eachreply['repid']}\" name='v_contentc'>{$rawreplycontent}</textarea><br/><input type='button' value='{$lnc[25]}' onclick=\"ajax_editcomment({$eachreply['repid']}, 'message', {$i});\"  class='button' /> <input type='reset' value='{$lnc[26]}'  class='button' /></form></div>";
		}
		if (!empty($avataraddress)) { //Make avatar
			$avatarposition=($mbcon['leftavatar']=='1') ? "left" : "right";
			$avatarposition2=($mbcon['leftavatar']=='1') ? "right" : "left";
			$replycontent="<img src=\"{$avataraddress}\" alt=\"\" style=\"float: {$avatarposition}; padding-{$avatarposition2}: 5px; width: {$mbcon['avatarwidth']}px; height: {$mbcon['avatarheight']}px; \"/><div>{$replycontent}</div><div style=\"clear:both;\"></div>";
		}
		if ($eachreply['adminreptime'] && ($eachreply['reproperty']!=1 || $permission['SeeHiddenReply']==1 || $eachreply['reppsw']=='')) {
			$ifadminreplied="block";
			$adminreplier="<a href=\"".getlink_user($eachreply['adminrepid'])."\" target=\"_blank\" title=\"{$lnc[17]}\">{$eachreply['adminreplier']}</a>";
			$adminreptime=zhgmdate("{$mbcon['timeformat']} H:i", ($eachreply['adminreptime']+3600*$config['timezone']));
			$adminreplybody="<form action='admin.php?go=message_editadminreply_{$eachreply['repid']}' method='post'>";
			$adminreplybody.="{$lnc[24]} <br/><textarea cols='66' rows='3' name='adminreplycontent' id='adminreplycontent{$eachreply['repid']}'>".safe_invert($eachreply['adminrepcontent'])."</textarea><br/>";
			$adminreplybody.="<input type='button' value='{$lnc[25]}' onclick=\"ajax_adminreply_edit('{$eachreply['repid']}', 'message'); return false;\" class='button'/> <input type='reset' value='{$lnc[26]}'  class='button'/> <input type='button' value='{$lnc[27]}' onclick=\"showhidediv('com_{$eachreply['repid']}');\" class='button'/></form>";
			if ($permission['ReplyReply']==1) $addadminreply="<a href=\"javascript: showhidediv('com_{$eachreply['repid']}');\">[{$lnc[20]}]</a>";
			$adminrepliershow="$adminreplier {$lnc[28]} $adminreptime";
			$adminreplycontent=$this->getcontent($eachreply['adminrepcontent']);
			if ($eachreply['adminrepedittime']) {
				$adminrepedittime=zhgmdate("{$mbcon['timeformat']} H:i", ($eachreply['adminrepedittime']+3600*$config['timezone']));
				$adminreplycontent.="<br/><span class='lastmodified'>{$lnc[29]} {$eachreply['adminrepeditor']} {$lnc[30]} {$adminrepedittime}</span>";
			}
			if ($permission['ReplyReply']!=1) $adminreplybody='';
		} else $ifadminreplied="none";
		$oddorcouplecss=($i%2==0) ? 'couple' : 'odd'; //added on 2006-11-14
		//Starting Template
		$output_single=$t->set('comment', array('replier'=>$replier, 'replierip'=>$replierip, 'replytime'=>$replytime,'addadminreply'=>$addadminreply,'deladminreply'=>$deladminreply,'editadminreply'=>$editadminreply,'delreply'=>$delreply,'blockreply'=>$blockreply,'replycontent'=>$replycontent,'ifadminreplied'=>$ifadminreplied, 'adminrepliershow'=>$adminrepliershow,'adminreplycontent'=>$adminreplycontent,'commentid'=>"com_{$eachreply['repid']}", 'adminreplybody'=>$adminreplybody, 'replieremail'=>$replieremail, 'replierhomepage'=>$replierhomepage, 'oddorcouplecss'=>$oddorcouplecss));
		$output_single=plugin_get('eachcommentbegin').$output_single.plugin_get('eachcommentend'); //Added on 2008/10/2
		$output_single="<div id=\"blogcomment{$eachreply['repid']}\">".$output_single."</div>";
		return $output_single;
	}

	function output ($entry, $way='excerpt', $contentonly=false) {
		global $mbcon, $section_body, $permission, $adminlist, $userdetail, $config, $categories, $weather, $t, $section_bodys, $part, $page, $template, $lnc, $tptvalue, $flset;
		if (!@is_a($t, 'template')) {
			$t=new template;
		}
		$entrytitle="<a href=\"".getlink_entry($entry['blogid'], $entry['blogalias'])."\">{$entry['title']}</a>";
		if ($entry['sticky']==1 || $entry['sticky']==2) $entrytitle="[{$lnc[33]}] ".$entrytitle;
		$entrydate=zhgmdate("{$mbcon['timeformat']}", ($entry['pubtime']+3600*$config['timezone'])); 
		$entrytime=gmdate('H:i', ($entry['pubtime']+3600*$config['timezone'])); 
		list($entrydatey, $entrydatem, $entrydated)=explode('/', gmdate('Y/n/j', ($entry['pubtime']+3600*$config['timezone'])));
		$entrydatemnamefull=gmdate('F', ($entry['pubtime']+3600*$config['timezone']));
		$entrydatemnameshort=gmdate('M', ($entry['pubtime']+3600*$config['timezone']));
		$tmp=$entry['authorid'];
		$entryauthor=$adminlist[$tmp];
		$entryauthor="<a href=\"".getlink_user($tmp)."\" target=\"_blank\">{$entryauthor}</a>";
		if ($flset['tags']!=1 && $entry['tags'] && $entry['tags']!='>') {
			$entry['tags']=trim($entry['tags'],'>');
			$taginfo=@explode('>', $entry['tags']);
			foreach ($taginfo as $eachtag) {
				$eachtag_encoded=urlencode(urlencode($eachtag));
				if ($mbcon['tagunderlinetospace']==1) 	$eachtag=str_replace('_', ' ', $eachtag);
				$urlref=getlink_tags($eachtag_encoded);
				$taginfos[]="<a href=\"{$urlref}\" title=\"Tags:  {$eachtag}\" rel=\"tag\">{$eachtag}</a>";
			}
			$alltags=@implode(' , ', $taginfos);
			if ($way=='viewentry') $tptvalue['additionalkeywords']=strip_tags(@implode(',', $taginfos)).',';
			$iftags="block";
			$tags="Tags: ";
		} else $iftags="none";
		$tmp=$entry['category'];
		if ($mbcon['extendcategory']==1 && $categories[$tmp]['parentcate']!=-1) {
			$tmp_parent=$categories[$tmp]['parentcate'];
			$outurl=getlink_category($tmp_parent);
			$entrycate="<a href=\"{$outurl}\" title=\"{$lnc[34]} {$categories[$tmp_parent]['catename']}\">{$categories[$tmp_parent]['catename']}</a> &raquo; ";
		} else {
			$tmp_parent='';
			$entrycate='';
		}
		$outurl=getlink_category($entry['category']);
		$entrycate.="<a href=\"{$outurl}\" title=\"{$lnc[34]} {$categories[$tmp]['catename']}\">{$categories[$tmp]['catename']}</a>";
		$entrycateicon=($categories[$tmp]['cateicon']) ? "<img src=\"{$categories[$tmp]['cateicon']}\" alt=\"\" style=\"margin:3px 1px -4px 0px;\"/> " : '';
		$entryviews="<a href=\"".getlink_entry($entry['blogid'], $entry['blogalias'])."\">{$lnc[35]}({$entry['views']})</a>";
		if ($entry['property']==1) {//Locked
			$entrycomment="{$lnc[36]}({$entry['replies']})";
		} else $entrycomment="<a href=\"".getlink_entry($entry['blogid'], $entry['blogalias'])."#reply\" title=\"{$lnc[37]}\">{$lnc[38]}({$entry['replies']})</a>";
		if ($flset['weather']!=1 && $entry['weather']) {
			$tmp=$entry['weather'];
			$entryicon="<img src=\"{$weather[$tmp]['image']}\" alt=\"{$weather[$tmp]['text']}\" title=\"{$weather[$tmp]['text']}\"/>";
		} else $entryicon='';
		
		if ($flset['star']!=1) {
			$entrystar="<span id=\"starid{$entry['blogid']}\">";
			$entrystar.=($permission['AddEntry']==1) ? (($entry['starred']%2==1) ? "<a href=\"javascript: dostar('{$entry['blogid']}');\"><img src=\"{$template['moreimages']}/others/starred.gif\" alt=\"\" title=\"{$lnc[39]}\" border=\"0\"/></a>" : "<a href=\"javascript: dostar('{$entry['blogid']}');\"><img src=\"{$template['moreimages']}/others/unstarred.gif\" alt=\"\" title=\"{$lnc[40]}\" border=\"0\"/></a>") : (($entry['starred']%2==1) ? "<img src=\"{$template['moreimages']}/others/starred.gif\" alt=\"\" title=\"{$lnc[39]}\" border=\"0\"/>" : "<img src=\"{$template['moreimages']}/others/unstarred.gif\" alt=\"\" title=\"{$lnc[40]}\" border=\"0\"/>");
			$entrystar.="</span>";
		}

		if ($permission['EditEntry']!=1) $ifadmin='';
		else {
			$adminbar="<div id=\"admin{$entry['blogid']}\" style=\"display: none;\" class=\"textbox-adminbar\"><br/><strong>{$lnc[41]}</strong><a href=\"admin.php?go=edit_edit_{$entry['blogid']}\">{$lnc[42]}</a> | <a href=\"javascript: showdelblog('{$entry['blogid']}');\">{$lnc[43]}</a> | <a href=\"javascript: comfirmurl('admin.php?go=entry_ae_noreply&amp;tid={$entry['blogid']}');\">{$lnc[44]}</a> | <a href=\"javascript: comfirmurl('admin.php?go=entry_ae_notb&amp;tid={$entry['blogid']}');\">{$lnc[45]}</a><br/><strong>{$lnc[46]}</strong><a href=\"admin.php?go=entry_ae_lock&amp;tid={$entry['blogid']}\">{$lnc[47]}</a> | <a href=\"admin.php?go=entry_ae_unlock&amp;tid={$entry['blogid']}\">{$lnc[48]}</a> | <a href=\"admin.php?go=entry_ae_sticky1&amp;tid={$entry['blogid']}\">{$lnc[49]}</a> | <a href=\"admin.php?go=entry_ae_sticky2&amp;tid={$entry['blogid']}\">{$lnc[50]}</a> | <a href=\"admin.php?go=entry_ae_sticky0&amp;tid={$entry['blogid']}\">{$lnc[51]}</a><br/><strong>{$lnc[52]}</strong><a href=\"javascript: comfirmurl('admin.php?go=entry_ae_noread&amp;tid={$entry['blogid']}');\">{$lnc[53]}</a> | <a href=\"admin.php?go=entry_ae_recountrep&amp;tid={$entry['blogid']}\">{$lnc[54]}</a> | <a href=\"admin.php?go=entry_ae_recounttb&amp;tid={$entry['blogid']}\">{$lnc[55]}</a></div>";
			$ifadmin=" | <a href='javascript: showhidediv(\"admin{$entry['blogid']}\");'>{$lnc[56]}</a>";
			$adminlink="<a href='javascript: showhidediv(\"admin{$entry['blogid']}\");'>{$lnc[56]}</a>";
		}
		$tbauthentic=tbcertificate($entry['blogid'], $entry['pubtime']);
		if ($mbcon['allowtrackback']==1) {
			$entrytburl="{$config['blogurl']}/tb.php?t={$entry['blogid']}&amp;extra={$tbauthentic}";
			$entrytburl2=($mbcon['tburljs']==1) ? "<span id='tbb{$entry['blogid']}'></span>" : $entrytburl;
			$tbb=($entry['property']==0) ? "<strong>{$lnc[57]}</strong>  {$entrytburl2}" : "<strong>{$lnc[58]}</strong>";
			if ($mbcon['tburlexpire']==1) $tbb.="<br/>{$lnc[289]}";
			$tbbar="<div id=\"tb{$entry['blogid']}\" style=\"display: none;\" class=\"textbox-tburl\">{$tbb}</div>";
			$tbonclick=($mbcon['tburljs']==1) ? "showhidediv(\"tb{$entry['blogid']}\"); if (document.getElementById(\"tbb{$entry['blogid']}\")) document.getElementById(\"tbb{$entry['blogid']}\").innerHTML=decodetburl(\"".encodetburl($entrytburl)."\", {$mbcon['tburlmath']}, {$entry['blogid']});" : "showhidediv(\"tb{$entry['blogid']}\");";
		} else {
			$tbbar="<div id=\"tb{$entry['blogid']}\" style=\"display: none;\" class=\"textbox-tburl\"><strong>{$lnc[282]}</strong></div>";
			$entrytburl=$lnc[282];
			$tbonclick="showhidediv(\"tb{$entry['blogid']}\");";
		}
		$entrytb="<a href='javascript: void(0);' title=\"{$lnc[59]}\" onclick='{$tbonclick}'>{$lnc[60]}({$entry['tbs']})</a>";
		$entrytbnumwithlink="<a href='javascript: void(0);' title=\"{$lnc[59]}\" onclick='{$tbonclick}'>{$entry['tbs']}</a>";
		if ($way=='excerpt' || $way=='viewentry') {
			if ($entry['blogpsw'] && $permission['SeeAllProtectedEntry']!=1 && $userdetail['userid']!=$entry['authorid'] && !$contentonly &&  $_COOKIE["entrypassword{$entry['blogid']}"]!=$entry['blogpsw']) { //Password protected entry
				$entry['content']="<div id='protectedentry{$entry['blogid']}'><div class=\"quote\"><div class=\"quote-title\">{$lnc[294]}</div><div class=\"quote-content\"><form action=\"javascript: getprotectedblog({$entry['blogid']}, '{$way}');\" method='post'>{$lnc[296]}<br/>{$lnc[133]} <input type='password' name='entrypsw' id='entrypsw{$entry['blogid']}'  class='text' /> <input type='submit' value='{$lnc[25]}'  class='button'/></form></div></div></div>";
				$aprotectedone=1;
			} else {
				if ($way=='excerpt') {
					if ($entry['entrysummary']) {
						$entry['content']=$entry['entrysummary'];
						$notfinish=1;
					}
					else {
						$entry['content']=str_replace('[newpage]', '[separator]', $entry['content']);
						if (strstr($entry['content'], '[separator]'))  {
							@list($entry['content'])=@explode('[separator]', $entry['content']);
							$notfinish=1;
						}
					}
				}
				else {
					$entry['content']=preg_replace("/\[separator\]/", "<a name=\"entrymore\"></a>", $entry['content'], 1);
					$entry['content']=@str_replace('[separator]', '', $entry['content']);
				}
				if ($way=='viewentry') {
					if (strstr($entry['content'], '[newpage]')) {
						$entrycontent_tmp=@explode('[newpage]', $entry['content']);
						$entry['content']=$entrycontent_tmp[$part-1];
						$totalvolume=count($entrycontent_tmp);

						$outurl=getlink_entry($entry['blogid'], $entry['blogalias'], $page, '%s');
						$pageway=1;
						$innerpage=$this->make_innerpagebar ($part, $outurl, $totalvolume, $pageway);
						$entry['content'].=$t->set('entryadditional', array('readmore'=>$innerpage));
					} else {
						checkPageValidity($part, 1);
					}
				}
			}

			if ($way=='viewentry' && $mbcon['showtoolbar']=='1') { //Toolbar
				$toolbarplus=($mbcon['txtdown']=='1') ? " <a href=\"read.php?save_{$entry['blogid']}\"><img src=\"{$mbcon['images']}/toolbar_save.gif\" alt='' title=\"{$lnc[66]}\" border='0'/></a>" : '';
				$toolbar="<img src=\"{$mbcon['images']}/toolbar_fontsize.gif\" alt='' title=\"{$lnc[61]}\" border='0'/> <a href=\"javascript: doZoom(16);\">{$lnc[62]}</a> | <a href=\"javascript: doZoom(14);\">{$lnc[63]}</a> | <a href=\"javascript: doZoom(12);\">{$lnc[64]}</a> <a href=\"feed.php?go=entry_{$entry['blogid']}\"><img src=\"{$mbcon['images']}/toolbar_rss.gif\" alt='' title=\"{$lnc[65]}\" border='0'/></a>{$toolbarplus}";
			}
			$entrycontent=$this->getcontent($entry['content'],  $entry['htmlstat'], $entry['ubbstat'], $entry['emotstat'], 1);
			$entrycontent=$this->keep_htmlcode_matches($entrycontent);
			if ($notfinish==1) {
				$entrycontent.=$t->set('entryadditional', array('readmore'=>"<a href=\"".get_entry_url($entry['blogid'], $entry['blogalias'])."#entrymore\" title=\"{$lnc[67]}\">{$lnc[68]}</a>"));
			}
			if ($way=='viewentry') { //Load plugin: entryend
				$entrycontent.=plugin_get('entrycontentend');
				$adminbar.=plugin_get('entryend');
			}

			if ($contentonly) { //Content Only Mode
				return $entrycontent;
			}

			if ($entry['previousid']!='') {
				$previousentryexist='inline';
				$previousentrytitle=$entry['previoustitle'];
				$previousentryurl=getlink_entry($entry['previousid'], $entry['previousblogalias']);
				$previous="<a href=\"{$previousentryurl}\" title=\"{$lnc[69]} {$entry['previoustitle']}\"><img src=\"{$mbcon['images']}/toolbar_previous.gif\" alt='' border='0'/>{$entry['previoustitle']}</a>";
			} else $previousentryexist='none';
			if ($entry['nextid']!='') {
				$nextentryexist='inline';
				$nextentrytitle=$entry['nextid'];
				$nextentryurl=getlink_entry($entry['nextid'], $entry['nextblogalias']);
				$next="<a href=\"{$nextentryurl}\" title=\"{$lnc[70]} {$entry['nexttitle']}\"><img src=\"{$mbcon['images']}/toolbar_next.gif\" alt='' border='0'/>{$entry['nexttitle']}</a>";
			} else $nextentryexist='none';
			if ($entry['editorid'] && $mbcon['showeditor']=='1') {
				$tmp=$entry['editorid'];
				$editby="<br/>{$lnc[29]} {$adminlist[$tmp]} {$lnc[30]}";
				$editby.=zhgmdate("{$mbcon['timeformat']} H:i", $entry['edittime']+3600*$config['timezone']);
			}
		}
		if ($entry['sticky']==2 && $way=='excerpt') {
			$way='excerptontop';
			$topid="top_{$entry['blogid']}";
		}

		//Source
		if ($entry['comefrom'] && $entry['originsrc']) {
			$entrysourcewithlink="<a href=\"{$entry['originsrc']}\" target=\"_blank\">{$entry['comefrom']}</a>";
		}
		else {
			$entrysourcewithlink=$entrysource=$lnc[307];
			$entrysourcelink='#';
		}

		//Start Template
		$section_bodys[]=$t->set($way, array('entryid'=>$entry['blogid'], 'entryicon'=>$entryicon, 'entrytitle'=>$entrytitle, 'entrydate'=>$entrydate,  'entrytime'=>$entrytime, 'entryauthor'=>$entryauthor, 'entrycontent'=>$entrycontent, 'iftags'=>$iftags, 'tags'=>$tags, 'alltags'=>$alltags, 'entrycate'=>$entrycate, 'entrycateicon'=>$entrycateicon, 'entrycomment'=>$entrycomment, 'entrytb'=>$entrytb, 'entryviews'=>$entryviews, 'ifadmin'=>$ifadmin, 'adminbar'=>$adminbar, 'tbbar'=>$tbbar, 'previous'=>$previous, 'next'=>$next, 'ifedited'=>$editby, 'toolbar'=>$toolbar, 'topid'=>$topid, 'entrystar'=>$entrystar, 'entrytitletext'=>$entry['title'], 'entryrelurl'=>get_entry_url($entry['blogid'], $entry['blogalias']), 'entryabsurl'=>"{$config['blogurl']}/".get_entry_url($entry['blogid'], $entry['blogalias']), 'entrydatey'=>$entrydatey, 'entrydatem'=>$entrydatem, 'entrydated'=>$entrydated, 'entrycommentnum'=>$entry['replies'], 'entrytbnum'=>$entry['tbs'], 'entryviewsnum'=>$entry['views'], 'entrytbnumwithlink'=>$entrytbnumwithlink, 'entrytburl'=>$entrytburl, 'previousentryexist'=>$previousentryexist, 'previousentrytitle'=>$previousentrytitle, 'previousentryurl'=>$previousentryurl, 'nextentryexist'=>$nextentryexist, 'nextentrytitle'=>$nextentrytitle, 'nextentryurl'=>$nextentryurl, 'entrydatemnamefull'=>$entrydatemnamefull, 'entrydatemnameshort'=>$entrydatemnameshort, 'entrysourcewithlink'=>$entrysourcewithlink, 'entrysource'=>$entry['comefrom'], 'entrysourcelink'=>$entry['originsrc'], 'adminlink'=>$adminlink));
	}

	function save_a_text ($entry) {
		global $mbcon, $adminlist, $blogversion, $config, $lnc, $permission, $userdetail, $logstat;
		if ($entry['property']>=2 && $permission['SeeHiddenEntry']!=1) catcherror($lnc[272]);
		if ($entry['blogpsw'] && $permission['SeeAllProtectedEntry']!=1 && $userdetail['userid']!=$entry['authorid']) catcherror($lnc[272]);
		$entrytitle=$entry['title'];
		$entrytime=gmdate('r', ($entry['pubtime']+3600*$config['timezone'])); //Need Further Change
		$tmp=$entry['authorid'];
		$entryauthor=$adminlist[$tmp];
		$entry['content']=@str_replace('[separator]', '', $entry['content']);
		$entry['content']=@str_replace('[newpage]', '', $entry['content']);
		if ($logstat!=1) {
			$entry['content']=preg_replace("/\[hide\](.+?)\[\/hide\]/is", "<br/>{$lnc[312]}  {$lnc[79]} {$lnc[235]} {$lnc[89]}<br/>", $entry['content']);
		} else {
			$entry['content']=str_replace(array('[hide]','[/hide]'), '', $entry['content']);
		}
		$entrycontent=$this->getrsscontent($entry['content'],  0, $entry['ubbstat'], 0);
		$entrycontent=str_replace(array('<br/>', '</p>', '</div>', '&#123;', '&#125;', '&nbsp;'), array("\r\n", "\r\n", "\r\n", '{', '}', ' '), $entrycontent);
		$entrycontent=strip_tags($entrycontent);
		$entrycontent=html_entity_decode($entrycontent, ENT_QUOTES);
		$entryurl="{$config['blogurl']}/".getlink_entry($entry['blogid'], $entry['blogalias']);
		@header('Content-type: text/plain');
		@header('Content-Disposition: attachment; filename="'.date('Ymd-His').'.txt"');
		$UTF8BOM=chr(239).chr(187).chr(191);
		echo "{$UTF8BOM}{$lnc[71]}{$entrytitle}\r\n{$lnc[72]}{$config['blogname']}\r\n{$lnc[73]}{$entrytime}\r\n{$lnc[74]}{$entryauthor}\r\n{$lnc[75]}{$entryurl}\r\n\r\n{$lnc[76]}\r\n{$entrycontent}\r\n\r\n\r\nGenerated by Bo-blog {$blogversion}";
		exit();
	}

	function rss_entry ($entry) {
		global $mbcon, $adminlist, $userdetail, $config, $categories, $t, $admin_email, $lnc;
		if (!@is_a($t, 'template')) {
			$t=new template;
		}
		$entrytitle=$entry['title'];
		$entrytitle=preg_replace("/&(.+?);/is", "", $entrytitle);
		$entrytime=gmdate('r', $entry['pubtime']);
		$entrytime=str_replace('  ', ' ', $entrytime);  //PHP outputs two spaces between weekday and time
		$tmp=$entry['authorid'];
		$entryauthor=$adminlist[$tmp];
		$entryemail=$admin_email[$tmp];
		$tmp=$entry['category'];
		$entrycate=$categories[$tmp]['catename'];
		if ($entry['blogpsw']) $entrycontent=$lnc[295];
		else {
			if ($entry['entrysummary']) {
				$entry['content']=$entry['entrysummary'];
				$notfinish=1;
			} else {
				$entry['content']=@str_replace('[newpage]', '[separator]', $entry['content']);
				if ($mbcon['wholerss']!=1 && strstr($entry['content'], '[separator]')) {
					@list($entry['content'])=@explode('[separator]', $entry['content']);
					$notfinish=1;
				}
				else $entry['content']=@str_replace('[separator]', '', $entry['content']);
			}
			$entrycontent=$this->getrsscontent($entry['content'],  1, $entry['ubbstat'], $entry['emotstat']);
			if ($notfinish==1) $entrycontent.="<br/>............<br/>";
		}
		if ($entry['tags'] && $entry['tags']!='>') {
			$entry['tags']=trim($entry['tags'],'>');
			$taginfo=@explode('>', $entry['tags']);
			foreach ($taginfo as $eachtag) {
				$eachtag_encoded=urlencode(urlencode($eachtag));
				if ($mbcon['tagunderlinetospace']==1) 	$eachtag=str_replace('_', ' ', $eachtag);
				$urlref=getlink_tags($eachtag_encoded);
				$taginfos[]="<a href=\"{$config['blogurl']}/{$urlref}\" rel=\"tag\">{$eachtag}</a>";
			}
			$alltags=@implode(' , ', $taginfos);
			$tags="<br/>Tags - ".$alltags;
		} else $tags='';
		$entrycontent.=$tags;
		$entryurl="{$config['blogurl']}/".getlink_entry($entry['blogid'], $entry['blogalias']);
		//Start Template
		$section=$t->set('rss', array('entrytitle'=>$entrytitle, 'entrytime'=>$entrytime, 'entryauthor'=>$entryauthor, 'entrycontent'=>$entrycontent, 'entryurl'=>$entryurl, 'entrycate'=>$entrycate, 'entryid'=>$entry['blogid'], 'entryemail'=>$entryemail));
		return $section;
	}

	function rss_replies ($entry) {
		global $mbcon, $adminlist, $userdetail, $config, $categories, $t, $lnc;
		if (!@is_a($t, 'template')) {
			$t=new template;
		}
		$entrytitle='['.$lnc[38].'] '.$entry['title'];
		$entrytitle=preg_replace("/&(.+?);/is", "", $entrytitle);
		$entrytime=gmdate('r', $entry['reptime']); 
		$entrytime=str_replace('  ', ' ', $entrytime);  //PHP outputs two spaces between weekday and time
		$entryauthor=$entry['replier'];
		$entryemail=(strstr($entry['repemail'], '@')) ? $entry['repemail'] : "user@domain.com";
		$entrycate=$lnc[38];
		$entrycontent=$this->getrsscontent($entry['repcontent'],  $entry['html'], $entry['ubb'], $entry['emot']);

		$entryurl="{$config['blogurl']}/".getlink_entry($entry['blogid'], $entry['blogalias'])."#blogcomment{$entry['repid']}";
		//Start Template
		$section=$t->set('rss', array('entrytitle'=>$entrytitle, 'entrytime'=>$entrytime, 'entryauthor'=>$entryauthor, 'entrycontent'=>$entrycontent, 'entryurl'=>$entryurl, 'entrycate'=>$entrycate, 'entryid'=>'reply'.$entry['repid'], 'entryemail'=>$entryemail));
		return $section;
	}

	function rss_xml ($rssbody) {
		global $mbcon, $adminlist, $userdetail, $config, $categories, $pagetitle, $t, $langname;
		if (!@is_a($t, 'template')) {
			$t=new template;
		}
		$section=$t->set('rssbody', array('blogname'=>$pagetitle.$config['blogname'], 'blogurl'=>$config['blogurl'], 'blogdesc'=>$config['blogdesc'], 'rssbody'=>$rssbody, 'bloglanguage'=>$langname['languagename']));
		return $section;
	}

	function make_list ($listbody) {
		global $t;
		if (!@is_a($t, 'template')) {
			$t=new template;
		}
		return $t->set('listbody', array('listbody'=>$listbody));
	}
	
	function make_visit_form($formtitle, $id, $actionurl) {
		global $mbcon, $logstat, $openidloginstat, $permission, $userdetail, $config, $emots, $t, $lnc;
		if (!@is_a($t, 'template')) {
			$t=new template;
		}
		$disable_html=($permission['Html']==1) ? "" : "disabled='disabled'";
		$disable_ubb=($permission['Ubb']==1) ? "checked='checked'" : "disabled='disabled'";
		$disable_emot=($permission['Emot']==1) ? "checked='checked'" : "disabled='disabled'";
		if ($logstat==1) {
			$replier=$userdetail['username'];
			$disable_replier="disabled='disabled'";
			$password='confidential';
			$if_neednopsw_begin='<!--';
			$if_neednopsw_end="-->{$lnc[77]} <strong>{$userdetail['username']}</strong> <a href=\"login.php?job=logout\" title=\"{$lnc[78]}\">[{$lnc[78]}]</a>";
			$if_neednopsw_rawend="-->";
			$disable_password="disabled='disabled'";
			$additional="";
			$repurl=$userdetail['homepage'];
			$repemail=$userdetail['email'];
			$repopenurl='';
			$hidden_areas.="<input type=\"hidden\" name=\"v_replier\" id=\"v_replier\" value=\"{$replier}\" /><input type=\"hidden\" name=\"v_password\" id=\"v_password\" value=\"{$password}\" /><input type=\"hidden\" name=\"v_repemail\" id=\"v_repemail\" value=\"{$repemail}\" /><input type=\"hidden\" name=\"v_repurl\" id=\"v_repurl\" value=\"{$repurl}\" />";
		} elseif ($openidloginstat==1) {
			$repopenurl=$_COOKIE['openid_url_id'];
			$disable_replier="disabled='disabled'";
			$if_neednopsw_begin='<!--';
			$if_neednopsw_end="-->{$lnc[77]} <strong>{$_COOKIE['openid_url_id']}</strong> <a href=\"login.php?job=logout\" title=\"{$lnc[78]}\">[{$lnc[78]}]</a>";
			$if_neednopsw_rawend="-->";
			$disable_password=$disable_openurl="disabled='disabled'";
			$additional="";
			$hidden_areas.="<input type=\"hidden\" name=\"v_replier\" id=\"v_replier\" value=\"\" /><input type=\"hidden\" name=\"v_password\" id=\"v_password\" value=\"\" /><input type=\"hidden\" name=\"v_repemail\" id=\"v_repemail\" value=\"\" /><input type=\"hidden\" name=\"v_repurl\" id=\"v_repurl\" value=\"\" /><input type=\"hidden\" name=\"openid_url\" id=\"openid_url\" value=\"{$_COOKIE['openid_url_id']}\" />";
		} else {
			$replier=$_COOKIE['rem_v_replier'];
			$repurl=$_COOKIE['rem_v_repurl'];
			$repemail=$_COOKIE['rem_v_repemail'];
			$repopenurl='';
			$checked_rememberme=(floor($_COOKIE['rem_v_rememberme'])==1) ? "checked='checked'" : '';
			$additional="<a href=\"login.php?job=register\" title=\"{$lnc[79]}\">[{$lnc[79]}]</a>";
		}
		$hidden_areas.="<input type=\"hidden\" name=\"v_id\" id=\"v_id\" value=\"{$id}\" /><input type=\"hidden\" name=\"v_reppsw\" id=\"v_reppsw\" value=\"\" />";
		if ($config['validation']==0) {
			$if_securitycode_begin='<!--';
			$if_securitycode_end='-->';
		}
		if ($mbcon['enableopenid']!='1' || $openidloginstat==1 || $logstat==1) {
			$if_openid_begin='<!--';
			$if_openid_end='-->';
		}
		if ($id==='') $jobnow='addmessage';
		else $jobnow='addreply';
		$ubbcode="<script type=\"text/javascript\" src=\"editor/ubb/ubbeditor_tiny.js\"></script><div style=\"margin: 4px 0px 4px 0px;\"><img src=\"editor/ubb/images/bar.gif\" alt=''/> &nbsp;<a href=\"javascript: bold();\"><img border='0' title=\"{$lnc[80]}\" src=\"editor/ubb/images/bold.gif\" alt=''/></a> &nbsp;<a href=\"javascript: italicize();\"><img border='0' title=\"{$lnc[81]}\" src=\"editor/ubb/images/italic.gif\" alt=''/></a> &nbsp;<a href=\"javascript: underline();\"><img border='0' title=\"{$lnc[82]}\" src=\"editor/ubb/images/underline.gif\"  alt=''/></a> &nbsp;<img src=\"editor/ubb/images/bar.gif\" alt=''/> &nbsp;<a href=\"javascript: image();\"><img border='0' title=\"{$lnc[83]}\" src=\"editor/ubb/images/insertimage.gif\" alt=''/></a> &nbsp;<a href=\"javascript: hyperlink();\"><img border='0' title=\"{$lnc[84]}\" src=\"editor/ubb/images/url.gif\" alt=''/></a> &nbsp;<a href=\"javascript: email();\"><img border='0' title=\"{$lnc[85]}\" src=\"editor/ubb/images/email.gif\"  alt=''/></a> &nbsp;<a href=\"javascript: quoteme();\"><img border='0' title=\"{$lnc[86]}\" src=\"editor/ubb/images/quote.gif\" alt=''/></a></div>";
		$emots=generate_emots_panel($emots);
		$out=$t->set("form_reply", array('actionurl'=>$actionurl, 'formtitle'=>$formtitle, 'emots'=>$emots, 'disable_html'=>$disable_html, 'disable_ubb'=>$disable_ubb, 'disable_emot'=>$disable_emot, 'replier'=>$replier, 'disable_replier'=>$disable_replier, 'password'=>$password, 'disable_password'=>$disable_password, 'additional'=>$additional, 'repurl'=>$repurl, 'repemail'=>$repemail, 'hidden_areas'=>$hidden_areas, 'if_securitycode_begin'=>$if_securitycode_begin, 'if_securitycode_end'=>$if_securitycode_end, 'jobnow'=>$jobnow, 'if_neednopsw_begin'=>$if_neednopsw_begin, 'if_neednopsw_end'=>$if_neednopsw_end, 'if_neednopsw_rawend'=>$if_neednopsw_rawend, 'checked_rememberme'=>$checked_rememberme, 'ubbcode'=>$ubbcode, 'repopenurl'=>$repopenurl, 'if_openid_begin'=>$if_openid_begin, 'if_openid_end'=>$if_openid_end, 'rand'=>rand(1000,9999)));
		return $out;
	}

	function getcontent($content, $html=0, $ubb=1, $emot=1, $advanced=0) {
		$content=str_replace('[separator]', '', $content);
		$content=str_replace('[newpage]', '', $content);
		if ($ubb==1) {
			include_once ("inc/ubb.php");
			$content =convert_ubb($content, $advanced);
		}
		if ($emot==1) {
			$content =preg_replace_callback("/\[emot\]([^ ]+?)\[\/emot\]/is", 'getemot', $content);
		}
		return $content;
	}

	function getrsscontent($content, $advanced=0, $ubb=1, $emot=1) {
		$content=str_replace('[separator]', '', $content);
		$content=str_replace('[newpage]', '', $content);
		if ($emot==1) {
			$content =preg_replace("/\[emot\]([^ ]+?)\[\/emot\]/is", '', $content);
		}
		if ($ubb==1) {
			include_once ("inc/ubb.php");
			$content =convert_ubb($content, $advanced, 1);
		}
		return $content;
	}

	function make_pagebar ($page, $numperline, $returnurl, $totalvolume='auto', $perpagevolume, $pageway=0) {
		global $lnc, $pageitems, $template;
		$conxer=(strstr($returnurl, '?'))? '&amp;' : '?';
		$totalvolume=($totalvolume=='auto') ? ($this->total_rows) : $totalvolume;
		$this->total_pages=floor(($totalvolume-1)/$perpagevolume)+1;
		checkPageValidity ($page, $this->total_pages); //2008/5/25 Block abnormal pages
		if (empty($this->total_pages)) return '';
		$firstpagededuct=floor($numperline/2);
		$firstindexpage=min(($page-$firstpagededuct), $page);
		if ($firstindexpage<1) $firstindexpage=1;
		$lastindexpage=min(($firstindexpage+$numperline-1), $this->total_pages);
		$pagebar.=" {$lnc[8]} {$page}/{$this->total_pages} ";
		$urlpattern=($pageway==0) ? str_replace('%', '%%', $returnurl)."{$conxer}page=%s" : $returnurl;
		$pagebar.=" <a href=\"".sprintf($urlpattern, 1)."\"><img src=\"{$template['moreimages']}/arrows/doubleleft.gif\" alt=\"{$lnc[9]}\" title=\"{$lnc[9]}\" border=\"0\"/></a> ";
		if ($page!=1) {
			$previouspageurl=sprintf($urlpattern, ($page-1));
			$pagebar.=" <a href=\"{$previouspageurl}\"><img src=\"{$template['moreimages']}/arrows/singleleft.gif\" alt=\"{$lnc[10]}\" title=\"{$lnc[10]}\" border=\"0\"/></a> ";
			$previouspageexists='inline';
		} else $previouspageexists='none';
		$turningpages='';
		for ($i=$firstindexpage; $i<=$lastindexpage; $i++) {
			if ($i!=$page) $turningpages.=" <a href=\"".sprintf($urlpattern, $i)."\">{$i}</a> ";
			else $turningpages.=" <span class=\"pagelink-current\">{$i}</span> ";
		}
		$pagebar.="<span class=\"pagebar-selections\">".$turningpages."</span>";
		if ($page!=$this->total_pages) {
			$nextpageurl=sprintf($urlpattern, ($page+1));
			$pagebar.=" <a href=\"{$nextpageurl}\"><img src=\"{$template['moreimages']}/arrows/singleright.gif\" alt=\"{$lnc[11]}\" title=\"{$lnc[11]}\" border=\"0\"/></a> ";
			$nextpageexists='inline';
		} else $nextpageexists='none';
		$pagebar.=" <a href=\"".sprintf($urlpattern, ($this->total_pages))."\"><img src=\"{$template['moreimages']}/arrows/doubleright.gif\" alt=\"{$lnc[12]}\" title=\"{$lnc[12]}\" border=\"0\"/></a> ";
		$pageitems=array('currentpage'=>$page, 'previouspageurl'=>$previouspageurl, 'nextpageurl'=>$nextpageurl, 'turningpages'=>$turningpages, 'totalpages'=>($this->total_pages), 'previouspageexists'=>$previouspageexists, 'nextpageexists'=>$nextpageexists);
		$pagebar="<span class=\"pagebar-mainbody\">{$pagebar}</span>";
		$pagebar.=plugin_get('afterpagebar');
		return $pagebar;
	}

	function make_innerpagebar ($page, $returnurl, $totalvolume, $pageway=0) {
		global $lnc;
		$conxer=(strstr($returnurl, '?'))? '&amp;' : '?';
		$pagebar.="{$lnc[87]} ";
		checkPageValidity ($page, $totalvolume); //2008/5/25 Block abnormal pages
		for ($i=1; $i<=$totalvolume; $i++) {
			if ($i!=$page) $pagebar.=($pageway==0) ? " <a href=\"{$returnurl}{$conxer}part={$i}\">[{$i}]</a> " : " <a href=\"".str_replace('%s', $i, $returnurl)."\">[{$i}]</a> ";
			else $pagebar.=" [{$i}] ";
		}
		return $pagebar;
	}

	function make_login () {
		global $t, $config;
		if (!@is_a($t, 'template')) {
			$t=new template;
		}
		if ($config['loginvalidation']!=1) {
			$lvstart="<!-- ";
			$lvend=" -->";
		} else $rand=rand(0,500);
		return $t->set('login', array('lvstart'=>$lvstart, 'lvend'=>$lvend, 'rand'=>$rand));
	}


	function output_page ($entry) {
		global $mbcon, $section_body, $permission, $adminlist, $userdetail, $config, $categories, $weather, $t, $section_bodys, $part, $template, $lnc;
		if (!@is_a($t, 'template')) {
			$t=new template;
		}
		$entrytitle=$entry['pagetitle'];
		$entrydate=zhgmdate("{$mbcon['timeformat']}", ($entry['pagetime']+3600*$config['timezone'])); 
		$entrytime=gmdate('H:i', ($entry['pagetime']+3600*$config['timezone'])); 
		list($entrydatey, $entrydatem, $entrydated)=explode('/', gmdate('Y/n/j', ($entry['pagetime']+3600*$config['timezone'])));
		$entrydatemnamefull=gmdate('F', ($entry['pagetime']+3600*$config['timezone']));
		$entrydatemnameshort=gmdate('M', ($entry['pagetime']+3600*$config['timezone']));
		$tmp=$entry['pageauthor'];
		$entryauthor=$adminlist[$tmp];
		$entryauthor="<a href=\"".getlink_user($tmp)."\" target=\"_blank\">{$entryauthor}</a>";
		$iftags="none";
		$entry['pagecontent']=@str_replace('[separator]', '', $entry['pagecontent']);
		$entrycontent=$this->getcontent($entry['pagecontent'],  $entry['htmlstat'], $entry['ubbstat'], $entry['emotstat'], 1);
		$entrycontent=$this->keep_htmlcode_matches($entrycontent);

		//Start Template
		$section_bodys[]=$t->set('viewpage', array('entrytitle'=>$entrytitle, 'entrydate'=>$entrydate,  'entrytime'=>$entrytime, 'entryauthor'=>$entryauthor, 'entrycontent'=>$entrycontent, 'entrytitletext'=>$entry['pagetitle'],'entrydatey'=>$entrydatey, 'entrydatem'=>$entrydatem, 'entrydated'=>$entrydated));
		return $section_bodys;
	}


	function keep_htmlcode_matches($str) {
		/* HTML code tidy 
			by Bob Shen 2007-2-21
		*/
		global $mbcon;
		if ($mbcon['tidyhtml']!='1') return $str;
		$outhtml='';
		$htmltagstart=array('li'=>0, 'ul'=>0, 'ol'=>0, 'dd'=>0, 'dt'=>0, 'dl'=>0, 'td'=>0, 'tr'=>0, 'tbody'=>0, 'table'=>0);
		$htmltagend=array();
		$allowsingle=array('br','hr','img','param');
		$str = preg_split("/(<[^>]+?>)/si",$str, -1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		foreach ($str as $singlestr) {
			if ($singlestr=='' || strstr($singlestr, '<!--')) $outhtml.=$singlestr;
			elseif (strstr($singlestr, '<')) {
				//if (substr_count($singlestr, '<')!=substr_count($singlestr, '>') || substr_count($singlestr, '"')%2==1 || substr_count($singlestr, "'")%2==1) continue;
				$tmp=str_replace(array('/>', '<', '>'), array(' />', '', ''), mystrtolower($singlestr));
				@list($currenttag)=@explode(' ', $tmp);
				if (!in_array($currenttag, $allowsingle)) {
					if ($currenttag{0}=='/') {
						$currenttag=str_replace('/', '', $currenttag);
						$htmltagend[$currenttag]+=1;
					} else {
						$htmltagstart[$currenttag]+=1;
					}
				}
				$outhtml.=$singlestr;
			} else {
				$outhtml.=$singlestr;
			}
		}
		foreach ($htmltagstart as $tag=>$counter) {
			$counter=floor($counter);
			$htmltagend[$tag]=floor($htmltagend[$tag]);
			$difference=$counter-$htmltagend[$tag];
			if ($difference==0) continue;
			elseif ($difference<0) {
				$outhtml=@implode('', array_fill(0, abs($difference), "<{$tag}>")).$outhtml;
			}
			else {
				$outhtml.=@implode('', array_fill(0, abs($difference), "</{$tag}>"));
			}
		}
		return $outhtml;
	}
}

