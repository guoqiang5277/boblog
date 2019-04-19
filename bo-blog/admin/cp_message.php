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
$finishok=$lna[342];
$finishok2=$lna[343];
$backtodefault="{$lna[7]}|admin.php?go=message_default";
$backtoindex="{$lna[41]}|index.php";
$backtoprevious="{$lna[344]}|<";
$backtocensor="{$lna[26]}|admin.php?go=message_censor";

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
	$blog->query("UPDATE `{$db_prefix}messages` SET `adminrepcontent`='{$adminreplycontent}' , {$queryplus} WHERE `repid`='{$repid}'");
	if ($ajax!='on') catchsuccess ($finishok2, array($backtoprevious, $backtoindex, $backtodefault));
	else {// For ajax
		$thiscommentwithreply=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}messages` WHERE `repid`='{$repid}'");
		include("data/cache_emot.php");
		$mbcon['images']=$template['images'];
		define("ADMIN_LOGIN", 1);
		$m_b=new getblogs;
		$ajaxresult=$m_b->single_message($thiscommentwithreply[0]);
		catchsuccess ($ajaxresult);
	}
} else {
	checkpermission('CP');
	confirmpsw(); //Re-check password
}

if ($job=='deladminreply') {
	$blog->query("UPDATE `{$db_prefix}messages` SET `adminrepcontent`='', `adminreplier`='', `adminrepid`='0',`adminreptime`='0',  `adminrepeditorid`='0', `adminrepeditor`='', `adminrepedittime`='0' WHERE `repid`='{$repid}'");
	catchsuccess ($finishok2, array($backtoprevious, $backtoindex, $backtodefault));
}

if ($job=='delreply') {
	acceptrequest('returnurl');
	if (!$returnurl) $returnurl="admin.php?go=message_default";
	if (!is_array($repid)) {
		$tmp_array[0]=$repid;
		$repid=$tmp_array;
	}
	for ($i=0; $i<count($repid); $i++) {
		$delrange[]="`repid`='{$repid[$i]}'";
	}
	$querydel=@implode(' OR ', $delrange);
	if (count($repid)>0) {
		$blog->query("DELETE FROM `{$db_prefix}messages` WHERE {$querydel}");
		$countreps=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}messages` WHERE `reproperty`<>2");
		$blog->query("UPDATE `{$db_prefix}counter` SET `messages`='{$countreps}'");
	}
	if ($ajax=='on') {
		catchsuccessandfetch($finishok, $returnurl);
	}
	catchsuccess ($finishok, array($backtoprevious, $backtoindex, $backtodefault));
}

if ($job=='censor' || $job=='default') {
	$start_id=($page-1)*$adminitemperpage;
	if ($job=='censor') {
		$detail_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}messages` WHERE `reproperty`=2 OR `reproperty`=3  ORDER BY `reptime` DESC");
		$address="pass";
		$titles=$lna[259];
		$picture="yes";
		$titlem=$lna[26];
		$titler=$lna[347];
		$param2=5;
		$totalvolume=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}messages` WHERE `reproperty`=2 OR `reproperty`=3");
		$censorclearall="<br><br>[<a href=\"javascript: redirectcomfirm('admin.php?go=message_messageclearall');\">{$lna[1021]}</a>]";
	}
	else 	{
		$detail_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}messages` WHERE `reproperty`<>2 AND `reproperty`<>3 ORDER BY `reptime` DESC  LIMIT $start_id, $adminitemperpage");
		$address="block";
		$titles=$lna[348];
		$picture="block";
		$titlem=$lna[7];
		$param2=3;
		$titler=$lna[349];
		$totalvolume=$statistics['messages'];
	}
	for ($i=0; $i<count($detail_array); $i++) {
		$tmp_tm=gmdate('Y/m/d H:i', $detail_array[$i]['reptime']+3600*$config['timezone']);
		$detail_array[$i]['repcontent']=msubstr($detail_array[$i]['repcontent'], 0, 120);
		$tablebody.="<tr class='visibleitem'><td align='center'><input type='checkbox' name='selid[]' id='selid[]' value='{$detail_array[$i]['repid']}'></td><td>{$detail_array[$i]['replier']}</td><td>{$tmp_tm}</td><td align='left' width=50%>{$detail_array[$i]['repcontent']}</td><td align='center'><a href='javascript: ensuredel(\"{$detail_array[$i]['repid']}\", \"{$param2}\");'><img src='admin/theme/{$themename}/del.gif' alt='{$lna[78]}' title='{$lna[78]}' border='0'></a></td><td align='center'><a href=\"javascript: simulateFormSubmit('admin.php?go=message_{$address}_{$detail_array[$i]['repid']}')\"><img src='admin/theme/{$themename}/{$picture}.gif' alt='$titles' title='$titles' border='0'></a></td></tr>";
	}
	$pagebar=gen_page ($page, 5, "admin.php?go=message_{$job}", $totalvolume, $adminitemperpage);
	$display_overall.=highlightadminitems($job, 'message');
$display_overall_plus= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
$titlem
</td>
<td class="sectend">{$titler}</td>
</tr>
</table>

<form action="admin.php?act=message" method="post" id='f_s' name='f_s'>
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr align=center  class="admintitle"><td width=35>{$lna[245]}</td><td width=100>{$lna[350]}</td><td width=120>{$lna[288]}</td><td >{$lna[287]}</td><td width=35>{$lna[78]}</td><td width=35>$titles</td></tr>
{$tablebody}
<tr><td colspan=3><a href="#unexist" onclick="checkallbox('f_s', 'checked');">{$lna[247]}</a> | <a href="#unexist" onclick="checkallbox('f_s', '');">{$lna[248]}</a></td><td colspan=3 align=right>{$pagebar}</td></tr>
<tr><td colspan=6 height=20></td></tr>
<tr class="adminoption"><td colspan=7>{$lna[249]}<input type=radio name='job' value='delreply'>{$lna[78]} <input type=radio name='job' value='{$address}'>{$titles}  <input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax('f_s');">{$censorclearall}
</td></tr>
</form>
</table>
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
		$passrange[]="`repid`='{$repid[$i]}'";
	}
	$querypass=@implode(' OR ', $passrange);
	if (count($repid)>0) {
		if ($job=='pass') {
			$blog->query("UPDATE `{$db_prefix}messages` SET `reproperty`=`reproperty`-2  WHERE {$querypass}");
			$countreps=db_affected_rows();
			$blog->query("UPDATE `{$db_prefix}counter` SET `messages`=`messages`+$countreps");
			$fetchURL='admin.php?go=message_censor';
		} else {
			$blog->query("UPDATE `{$db_prefix}messages` SET `reproperty`=`reproperty`+2  WHERE {$querypass}");
			$countreps=db_affected_rows();
			$blog->query("UPDATE `{$db_prefix}counter` SET `messages`=`messages`-$countreps");
			$fetchURL='admin.php?go=message_default';
		}
	}
	if ($ajax=='on') {
		catchsuccessandfetch($finishok, $fetchURL);
	}
	else catchsuccess ($finishok, array($backtocensor, $backtoindex, $backtodefault));
}

if ($job=='messageclearall') {
	$blog->query("DELETE FROM `{$db_prefix}messages` WHERE `reproperty`=2 OR `reproperty`=3");
	if ($ajax=='on') {
		$fetchURL='admin.php?go=message_censor';
		catchsuccessandfetch($finishok, $fetchURL);
	}
	else catchsuccess ($finishok, $backtodefault);
}
