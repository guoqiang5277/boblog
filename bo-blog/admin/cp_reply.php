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

if (!defined('VALIDADMIN')) die ('Access Denied.');

//Define some senteces
$finishok=$lna[351];
$finishok2=$lna[352];
$finishok3=$lna[353];
$backtodefault="{$lna[6]}|admin.php?go=reply_default";
$backtotb="{$lna[25]}|admin.php?go=reply_tb";
$backtoindex="{$lna[41]}|index.php";
$backtoprevious="{$lna[344]}|<";
$backtocensor="{$lna[24]}|admin.php?go=reply_censor";

acceptrequest('job,selid');
if ($selid) $repid=$selid;
else $repid=$itemid;

if (empty($job)) {
	if ($ajax=='on') {
		catcherror ($lna[499]);
	}
	else {
		$job='default';
	}
}

if ($job=='addadminreply' || $job=='editadminreply') {
	if ($permission['ReplyReply']!=1) $cancel=$lna[345];
	catcherror ($cancel);
	acceptrequest('adminreplycontent');
	$adminreplycontent=trimplus($adminreplycontent);
	if ($adminreplycontent=='') {
		catcherror ($lna[346]);
	}
	$adminreplycontent=safe_convert($adminreplycontent);
	$currenttime=time();
	if ($logstat==0) $userdetail['username']=$lna[901];
	if ($job=='editadminreply') $queryplus="`adminrepeditorid`='{$userdetail['userid']}', `adminrepeditor`='{$userdetail['username']}', `adminrepedittime`='{$currenttime}'";
	else $queryplus="`adminreplier`='{$userdetail['username']}', `adminrepid`='{$userdetail['userid']}',`adminreptime`='{$currenttime}'";
	$blog->query("UPDATE `{$db_prefix}replies` SET `adminrepcontent`='{$adminreplycontent}' , {$queryplus} WHERE `repid`='{$repid}'");
	if ($ajax!='on') catchsuccess ($finishok2, array($backtoprevious, $backtoindex, $backtodefault));
	else {// For ajax
		if ($mbcon['avatar']==1 || $mbcon['usergravatar']==1 || $mbcon['visitorgravatar']==1) {
			$thiscommentwithreply=$blog->getgroupbyquery("SELECT t1.*, t2.userid, t2.avatar FROM `{$db_prefix}replies` t1 LEFT JOIN `{$db_prefix}user` t2 ON t1.replierid=t2.userid WHERE t1.repid='{$repid}'");
		}
		else  $thiscommentwithreply=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}replies` WHERE `repid`='{$repid}'");
		include("data/cache_emot.php");
		$mbcon['images']=$template['images'];
		define("ADMIN_LOGIN", 1);
		$m_b=new getblogs;
		$ajaxresult=$m_b->single_reply($thiscommentwithreply[0]);
		catchsuccess ($ajaxresult);
	}
} else {
	checkpermission('CP');
	confirmpsw(); //Re-check password
}


if ($job=='deladminreply') {
	$blog->query("UPDATE `{$db_prefix}replies` SET `adminrepcontent`='', `adminreplier`='', `adminrepid`='0',`adminreptime`='0',  `adminrepeditorid`='0', `adminrepeditor`='', `adminrepedittime`='0' WHERE `repid`='{$repid}'");
	catchsuccess ($finishok2, array($backtoprevious, $backtoindex, $backtodefault));
}

if ($job=='delreply') {
	acceptrequest('returnurl');
	if (!$returnurl) $returnurl="admin.php?go=reply_default";
	if (!is_array($repid)) {
		$tmp_array[0]=$repid;
		$repid=$tmp_array;
	}
	for ($i=0; $i<count($repid); $i++) {
		@list($tmp_repid, $tmp_entryid)=@explode('-', $repid[$i]);
		$delrange[]="`repid`='{$tmp_repid}'";
		$countblogid[]=$tmp_entryid;
	}
	$querydel=@implode(' OR ', $delrange);
	if (count($repid)>0) {
		$blog->query("DELETE FROM `{$db_prefix}replies` WHERE {$querydel}");
		$countreps=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`<=1");
		$blog->query("UPDATE `{$db_prefix}counter` SET `replies`='{$countreps}'");
	}
	$countblogid=array_values(array_unique($countblogid));
	for ($i=0; $i<count($countblogid); $i++) {
		$countreps=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`<=1 AND `blogid`='{$countblogid[$i]}'");
		$blog->query("UPDATE `{$db_prefix}blogs` SET `replies`='{$countreps}' WHERE `blogid`='{$countblogid[$i]}'");
	}
	recache_latestreplies();
	if ($ajax=='on') {
		catchsuccessandfetch($finishok, $returnurl);
	}
	else catchsuccess ($finishok, array($backtoprevious, $backtoindex, $backtodefault));
}

if ($job=='deltb' || $job=='tbnopass') {
	if (!is_array($repid)) {
		$tmp_array[0]=$repid;
		$repid=$tmp_array;
	}
	for ($i=0; $i<count($repid); $i++) {
		@list($tmp_repid, $tmp_entryid)=@explode('-', $repid[$i]);
		$delrange[]="`repid`='{$tmp_repid}'";
		$countblogid[]=$tmp_entryid;
	}
	$querydel=@implode(' OR ', $delrange);
	if (count($repid)>0) {
		$blog->query("DELETE FROM `{$db_prefix}replies` WHERE {$querydel}");
		if ($job=='deltb') {
			$delednum=db_affected_rows();
			$blog->query("UPDATE `{$db_prefix}counter` SET `tb`=`tb`-$delednum");
		}
	}
	if ($job=='deltb') {
		$countblogid=array_values(array_unique($countblogid));
		for ($i=0; $i<count($countblogid); $i++) {
			$countreps=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`=4  AND `blogid`='{$countblogid[$i]}'");
			$blog->query("UPDATE `{$db_prefix}blogs` SET `tbs`='{$countreps}' WHERE `blogid`='{$countblogid[$i]}'");
		}
		recache_latestreplies();
		$returnurl="admin.php?go=reply_tb";
	}
	else {
		$returnurl="admin.php?go=reply_tbcensor";
	}
	if ($ajax=='on') {
		catchsuccessandfetch($finishok3, $returnurl);
	}
	else catchsuccess ($finishok3, array($backtoprevious, $backtoindex, $backtotb));
}

if ($job=='censor' || $job=='default') {
	$start_id=($page-1)*$adminitemperpage;
	if ($job=='censor') {
		$detail_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}replies` WHERE `reproperty`=2 OR `reproperty`=3  ORDER BY `reptime` DESC LIMIT $start_id, $adminitemperpage");
		$address="pass";
		$titles=$lna[259];
		$picture="yes";
		$titlem=$lna[24];
		$titler=$lna[354];
		$param2=4;
		$totalvolume=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`=2 OR `reproperty`=3");
		$censorclearall="<br><br>[<a href=\"javascript: redirectcomfirm('admin.php?go=reply_repliesclearall');\">{$lna[1021]}</a>]";
	}
	elseif ($job=='default') 	{
		$detail_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}replies` WHERE `reproperty`<=1 ORDER BY `reptime` DESC  LIMIT $start_id, $adminitemperpage");
		$address="block";
		$titles=$lna[348];
		$picture="block";
		$titlem=$lna[6];
		$titler=$lna[355];
		$param2=2;
		$totalvolume=$statistics['replies'];
	} 
	for ($i=0; $i<count($detail_array); $i++) {
		$tmp_tm=gmdate('Y/m/d H:i', $detail_array[$i]['reptime']+3600*$config['timezone']);
		$detail_array[$i]['repcontent']=msubstr($detail_array[$i]['repcontent'], 0, 120);
		$tablebody.="<tr class='visibleitem'><td align='center'><input type='checkbox' name='selid[]' id='selid[]' value='{$detail_array[$i]['repid']}-{$detail_array[$i]['blogid']}'></td><td>{$detail_array[$i]['replier']}</td><td>{$tmp_tm}</td><td align='left' width=50%><a href='".getlink_entry($detail_array[$i]['blogid'], '')."' target='_blank' title='{$lna[356]}'>{$detail_array[$i]['repcontent']}</a></td><td align='center'><a href='javascript: ensuredel(\"{$detail_array[$i]['repid']}-{$detail_array[$i]['blogid']}\", \"{$param2}\");'><img src='admin/theme/{$themename}/del.gif' alt='{$lna[78]}' title='{$lna[78]}' border='0'></a></td><td align='center'><a href=\"javascript: simulateFormSubmit('admin.php?go=reply_{$address}_{$detail_array[$i]['repid']}-{$detail_array[$i]['blogid']}')\"><img src='admin/theme/{$themename}/{$picture}.gif' alt='$titles' title='$titles' border='0'></a></td></tr>";
	}
	$pagebar=gen_page ($page, 5, "admin.php?go=reply_{$job}", $totalvolume, $adminitemperpage);
	$display_overall.=highlightadminitems($job, 'reply');
$display_overall_plus= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
$titlem
</td>
<td class="sectend">{$titler}</td>
</tr>
</table>

<form action="admin.php?act=reply" method="post" id='f_s' name='f_s'>
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr align=center  class="admintitle"><td width=35>{$lna[245]}</td><td width=100>{$lna[357]}</td><td width=120>{$lna[288]}</td><td >{$lna[287]}</td><td width=35>{$lna[78]}</td><td width=35>$titles</td></tr>
{$tablebody}
<tr><td colspan=3><a href="#unexist" onclick="checkallbox('f_s', 'checked');">{$lna[247]}</a> | <a href="#unexist" onclick="checkallbox('f_s', '');">{$lna[248]}</a></td><td colspan=3 align=right>{$pagebar}</td></tr>
<tr><td colspan=6 height=20></td></tr>
<tr class="adminoption"><td colspan=7>{$lna[249]}<input type=radio name='job' value='delreply'>{$lna[78]} <input type=radio name='job' value='{$address}'>{$titles}  <input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax('f_s');">{$censorclearall}
</td></tr>
</table>
</form>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='tb' || $job=='tbcensor') {
	$start_id=($page-1)*$adminitemperpage;
	$tbproperty=($job=='tb' ) ? 4 : 5;
	$tbactdel=($job=='tb' ) ? 'deltb' : 'tbnopass';
	$detail_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}replies` WHERE `reproperty`={$tbproperty} ORDER BY `reptime` DESC  LIMIT $start_id, $adminitemperpage");
	for ($i=0; $i<count($detail_array); $i++) {
		$tmp_tm=gmdate('Y/m/d H:i', $detail_array[$i]['reptime']+3600*$config['timezone']);
		$detail_array[$i]['repcontent']=msubstr($detail_array[$i]['repcontent'], 0, 120);
		$tablebody.="<tr class='visibleitem'><td align='center'><input type='checkbox' name='selid[]' id='selid[]' value='{$detail_array[$i]['repid']}-{$detail_array[$i]['blogid']}'></td><td><a href='{$detail_array[$i]['repurl']}' target='_blank' title='{$lna[358]}'>{$detail_array[$i]['replier']}</a><br>{$detail_array[$i]['repip']}</td><td>{$tmp_tm}</td><td align='left' width=50%><a href='".getlink_entry($detail_array[$i]['blogid'], '')."' target='_blank' title='{$lna[356]}'>{$detail_array[$i]['repcontent']}</a></td><td align='center'><a href='javascript: redirectcomfirm(\"admin.php?go=reply_{$tbactdel}_{$detail_array[$i]['repid']}-{$detail_array[$i]['blogid']}\");'><img src='admin/theme/{$themename}/del.gif' alt='{$lna[78]}' title='{$lna[78]}' border='0'></a></td>";
		if ($job=='tbcensor') $tablebody.="<td align='center'><a href=\"javascript: simulateFormSubmit('admin.php?go=reply_tbpass_{$detail_array[$i]['repid']}-{$detail_array[$i]['blogid']}')\"><img src='admin/theme/{$themename}/yes.gif' alt='{$lna[259]}' title='{$lna[259]}' border='0'></a></td>";
		$tablebody.="</tr>";
	}

	if ($job=='tbcensor') {
		$censorplus1="<td width=35>{$lna[259]}</td>";
		$censorplus2="&nbsp; &nbsp; <input type=radio name='job' value='tbpass'>{$lna[259]}";
		$censorclearall="<br><br>[<a href=\"javascript: redirectcomfirm('admin.php?go=reply_tbclearall');\">{$lna[1021]}</a>]";
		$titlem=$lna[947];
		$titler=$lna[948];
		$actionurl="admin.php?go=reply_tbcensor";
		$countnum=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`=5");
	}
	else {
		$censorplus1=$censorplus2='';
		$censorclearall='';
		$titlem=$lna[25];
		$titler=$lna[359];
		$actionurl="admin.php?go=reply_tb";
		$countnum=$blog->$statistics['tb'];
	}
	$pagebar=gen_page ($page, 5, $actionurl, $countnum, $adminitemperpage);
	$display_overall.=highlightadminitems($job, 'reply');
$display_overall_plus= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$titlem}
</td>
<td class="sectend">{$titler}</td>
</tr>
</table>

<form action="admin.php?act=reply" method="post" id='f_s' name='f_s'>
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr align=center  class="admintitle"><td width=35>{$lna[245]}</td><td width=100>{$lna[357]}</td><td width=120>{$lna[288]}</td><td >{$lna[287]}</td><td width=35>{$lna[78]}</td>
{$censorplus1}
</tr>
{$tablebody}
<tr><td colspan=3><a href="#unexist" onclick="checkallbox('f_s', 'checked');">{$lna[247]}</a> | <a href="#unexist" onclick="checkallbox('f_s', '');">{$lna[248]}</a></td><td colspan=3 align=right>{$pagebar}</td></tr>
<tr><td colspan=6 height=20></td></tr>
<tr class="adminoption"><td colspan=7>{$lna[249]}<input type=radio name='job' value='{$tbactdel}'>{$lna[78]} {$censorplus2}  <input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax('f_s');">{$censorclearall}
</td></tr>
</table>
</form>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='pass' || $job=='block') {
	if (!is_array($repid)) {
		$tmp_array[0]=$repid;
		$repid=$tmp_array;
	}
	for ($i=0; $i<count($repid); $i++) {
		@list($tmp_repid, $tmp_entryid)=@explode('-', $repid[$i]);
		$passrange[]="`repid`='{$tmp_repid}'";
		$countblogid[]=$tmp_entryid;
	}
	$querypass=@implode(' OR ', $passrange);
	if (count($repid)>0) {
		if ($job=='pass') {
			$blog->query("UPDATE `{$db_prefix}replies` SET `reproperty`=`reproperty`-2  WHERE {$querypass}");
			$countreps=db_affected_rows();
			$blog->query("UPDATE `{$db_prefix}counter` SET `replies`=`replies`+$countreps");
			$fetchURL='admin.php?go=reply_censor';
		} else {
			$blog->query("UPDATE `{$db_prefix}replies` SET `reproperty`=`reproperty`+2  WHERE {$querypass}");
			$countreps=db_affected_rows();
			$blog->query("UPDATE `{$db_prefix}counter` SET `replies`=`replies`-$countreps");
			$fetchURL='admin.php?go=reply_default';
		}
	}
	$countblogid=array_values(array_unique($countblogid));
	for ($i=0; $i<count($countblogid); $i++) {
		$countreps=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`<=1 AND `blogid`='{$countblogid[$i]}'");
		$blog->query("UPDATE `{$db_prefix}blogs` SET `replies`='{$countreps}' WHERE `blogid`='{$countblogid[$i]}'");
	}
	recache_latestreplies();
	if ($ajax=='on') {
		catchsuccessandfetch($finishok, $fetchURL);
	}
	else catchsuccess ($finishok, array($backtocensor, $backtoindex, $backtodefault));
}

if ($job=='tbpass') {
	if (!is_array($repid)) {
		$tmp_array[0]=$repid;
		$repid=$tmp_array;
	}
	for ($i=0; $i<count($repid); $i++) {
		@list($tmp_repid, $tmp_entryid)=@explode('-', $repid[$i]);
		$passrange[]="`repid`='{$tmp_repid}'";
		$countblogid[]=$tmp_entryid;
	}
	$querypass=@implode(' OR ', $passrange);
	if (count($repid)>0) {
		$blog->query("UPDATE `{$db_prefix}replies` SET `reproperty`=4  WHERE {$querypass}");
		$countreps=db_affected_rows();
		$blog->query("UPDATE `{$db_prefix}counter` SET `tb`=`tb`+$countreps");
	}
	$countblogid=array_values(array_unique($countblogid));
	for ($i=0; $i<count($countblogid); $i++) {
		$countreps=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`=4 AND `blogid`='{$countblogid[$i]}'");
		$blog->query("UPDATE `{$db_prefix}blogs` SET `tbs`='{$countreps}' WHERE `blogid`='{$countblogid[$i]}'");
	}
	recache_latestreplies();
	if ($ajax=='on') {
		$fetchURL='admin.php?go=reply_tbcensor';
		catchsuccessandfetch($finishok3, $fetchURL);
	}
	else catchsuccess ($finishok3, $backtoprevious);
}

if ($job=='repliesclearall') {
	$blog->query("DELETE FROM `{$db_prefix}replies` WHERE `reproperty`=2 OR `reproperty`=3");
	if ($ajax=='on') {
		$fetchURL='admin.php?go=reply_censor';
		catchsuccessandfetch($finishok, $fetchURL);
	}
	else catchsuccess ($finishok, $backtodefault);
}

if ($job=='tbclearall') {
	$blog->query("DELETE FROM `{$db_prefix}replies` WHERE `reproperty`=5");
	if ($ajax=='on') {
		$fetchURL='admin.php?go=reply_tbcensor';
		catchsuccessandfetch($finishok3, $fetchURL);
	}
	else catchsuccess ($finishok3, $backtoprevious);
}