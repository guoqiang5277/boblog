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
checkpermission('CP');
confirmpsw(); //Re-check password
if (!$job) $job='recache';

//Define some senteces
$finishok=$lna[753];
$finishok3=$lna[754];
$finishok4=$lna[893];
$finishok5=$lna[1108];
$delok=$lna[755];
$optimizeok=$lna[756];
$replaceok=$lna[1186];
$backtocache="{$lna[757]}|admin.php?go=carecenter_recache";
$backtoupload="{$lna[758]}|admin.php?go=carecenter_adminattach";
$backtomysql="{$lna[759]}|admin.php?go=carecenter_mysql";

if ($job=='cleartmpandreturn') {
	$job='cleartmp';
	$backtocache="{$lna[344]}|admin.php";
	$fetchURL="admin.php?go=main_default";
}

if ($job=='recache') {
		$display_overall.=highlightadminitems('recache', 'carecenter');
$display_overall.= <<<eot
<form action="admin.php?go=carecenter_refreshcache" method="post"  id='f_s' name='f_s'>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[35]}
</td>
<td class="sectend">{$lna[760]}</td>
</tr>
<tr class='sect'><td colspan=2>
<ul><li>{$lna[761]}</li><li>{$lna[762]}</li></ul>
<br>
<table width=80% align=center cellpadding=4>
<tr>
<td width=25%><input type=checkbox name='selid[]' value='links'>{$lna[763]}</td>
<td width=25%><input type=checkbox name='selid[]' value='emotselection'>{$lna[764]}</td>
<td width=25%><input type=checkbox name='selid[]' value='mods'>{$lna[765]}</td>
<td width=25%><input type=checkbox name='selid[]' value='adminlist'>{$lna[766]}</td>
</tr>
<tr>
<td width=25%><input type=checkbox name='selid[]' value='categories'>{$lna[767]}</td>
<td width=25%><input type=checkbox name='selid[]' value='latestentries'>{$lna[768]}</td>
<td width=25%><input type=checkbox name='selid[]' value='latestreplies'>{$lna[769]}</td>
<td width=25%><input type=checkbox name='selid[]' value='currentmonthentries'>{$lna[770]}</td>
</tr>
<tr>
<td width=25%><input type=checkbox name='selid[]' value='taglist'>{$lna[771]}</td>
<td width=25%><input type=checkbox name='selid[]' value='plugins'>{$lna[954]}</td>
<td width=50% colspan=2></td>
</tr>
<tr>
<td colspan=4><a href="#unexist" onclick="checkallbox('f_s', 'checked');">{$lna[247]}</a> | <a href="#unexist" onclick="checkallbox('f_s', '');">{$lna[248]}</a></td>
</tr>
</table>

</td><tr>
<tr><td colspan=2 class="sectbar" align=center><input type='button' value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax('f_s');"> <input type=reset value="{$lna[65]}" class='formbutton'>
</td></tr>
</table>
</form>
<br>
<br>
<form action="admin.php?go=carecenter_refreshcounter" method="post" id='ajaxForm1' name='ajaxForm1'>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[773]}
</td>
<td class="sectend">{$lna[774]}</td>
</tr>
<tr class='sect'><td colspan=2>
<ul><li>{$lna[775]}</li></ul>
<br>
<table width=80% align=center cellpadding=4>
<tr>
<td width=25%><input type=checkbox name='selid2[]' value='entries'>{$lna[776]}</td>
<td width=25%><input type=checkbox name='selid2[]' value='tb'>{$lna[777]}</td>
<td width=25%><input type=checkbox name='selid2[]' value='messages'>{$lna[778]}</td>
<td width=25%><input type=checkbox name='selid2[]' value='users'>{$lna[779]}</td>
</tr>
<tr>
<td width=25%><input type=checkbox name='selid2[]' value='replies'>{$lna[780]}</td>
<td width=75% colspan=3><input type=checkbox name='selid2[]' value='max'>{$lna[781]}</td>
</tr>
<tr>
<td colspan=4><a href="#unexist" onclick="checkallbox('ajaxForm1', 'checked');">{$lna[247]}</a> | <a href="#unexist" onclick="checkallbox('ajaxForm1', '');">{$lna[248]}</a></td>
</tr>
</table>
</td><tr>
<tr><td colspan=2 class="sectbar" align=center><input type='button' value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax(1);"> <input type=reset value="{$lna[65]}" class='formbutton'>
</td></tr>
</table>
</form>
<br>
<br>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[894]}
</td>
<td class="sectend">{$lna[895]}</td>
</tr>
<tr class='sect'><td colspan=2>
<ul>
<li><b>{$lna[772]}</b> <a href="javascript: simulateFormSubmit('admin.php?go=carecenter_cleartmp')">[{$lna[896]}]</a><br>{$lna[897]}<br><br></li>
<li><b>{$lna[898]}</b> <a href="javascript: simulateFormSubmit('admin.php?go=carecenter_rebuildcalendar')">[{$lna[896]}]</a><br>{$lna[899]}<br><br></li>
<li><b>{$lna[1109]}</b> <a href="javascript: simulateFormSubmit('admin.php?go=carecenter_rebuildrewritefiles')">[{$lna[896]}]</a><br>{$lna[1110]}<br></li>
</ul>
</td></tr>
</table>
eot;
}

if ($job=='refreshcache') {
	acceptrequest('selid');
	if (!is_array($selid)) catcherror($lna[205]);
	for ($i=0; $i<count($selid); $i++) {
		$func="recache_{$selid[$i]}";
		call_user_func($func);
	}
	catchsuccess ($finishok, $backtocache);
}

if ($job=='refreshcounter') {
	acceptrequest('selid2');
	if (!is_array($selid2)) catcherror($lna[205]);
	$countsql=array(
		'entries'=>"SELECT COUNT(blogid) FROM `{$db_prefix}blogs`", 
		'replies'=>"SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`<=1", 
		'tb'=>"SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`=4", 
		'messages'=>"SELECT COUNT(repid) FROM `{$db_prefix}messages` WHERE `reproperty`<=1", 
		'users'=>"SELECT COUNT(userid) FROM `{$db_prefix}user`"
	);
	for ($i=0; $i<count($selid2); $i++) {
		if ($selid2[$i]=='max') continue;
		$idsql=$selid2[$i];
		$result_num=$blog->countbyquery($countsql[$idsql]);
		if ($idsql=='users') $result_num-=1;
		$blog->query("UPDATE `{$db_prefix}counter` SET `{$idsql}`={$result_num}");
	}

	if (in_array('max', $selid2)) {
		$maxsql=array(
			"SELECT MAX(blogid) FROM `{$db_prefix}blogs`",
			"SELECT MAX(userid) FROM `{$db_prefix}user`", 
			"SELECT MAX(cateid) FROM `{$db_prefix}categories`",
			"SELECT MAX(repid) FROM `{$db_prefix}replies`", 
			"SELECT MAX(repid) FROM `{$db_prefix}messages`", 
			"SELECT MAX(linkgpid) FROM `{$db_prefix}linkgroup`", 
			"SELECT MAX(linkid) FROM `{$db_prefix}links`"
		);
		$max2ar=array('maxblogid','maxuserid','maxcateid','maxrepid','maxmessagepid','maxlinkgpid','maxlinkid');
		for ($i=0; $i<count($maxsql); $i++) {
			$resultmax=$blog->countbyquery($maxsql[$i]);
			if (empty($resultmax)) $resultmax=0;
			$query_sql[]="`{$max2ar[$i]}`={$resultmax}";
		}
		$all_sql="UPDATE `{$db_prefix}maxrec` SET ".@implode(',', $query_sql);
		$blog->query($all_sql);	
	}
	catchsuccess ($finishok3, $backtocache);
}

if ($job=='cleartmp') {
	recache_cleartemp ();
	$blog->query("UPDATE `{$db_prefix}counter` SET `empty1`={$nowtime['timestamp']}");
	if ($ajax=='on' && $fetchURL) {
		catchsuccessandfetch($finishok4, $fetchURL);
	}
	else catchsuccess ($finishok4, $backtocache);
}

if ($job=='rebuildcalendar') {
	//Del all existed values
	$blog->query("TRUNCATE TABLE `{$db_prefix}calendar`");

	//Re-Build Calendar
	$all=$blog->getgroupbyquery("SELECT `blogid`,`pubtime` FROM `{$db_prefix}blogs` WHERE `property`<>2 AND `property`<>3");
	if (is_array($all)) foreach ($all as $item) {
		$time=$item['pubtime'];
		$t_Ym=gmdate('Ym', $time+3600*$config['timezone']);
		$t_day=gmdate('j', $time+3600*$config['timezone']);
		$values[]="('{$t_Ym}', '{$t_day}', '{$item['blogid']}', '')";
	}
	$all_value=@implode(',', $values);
	if ($all_value) $blog->query("INSERT INTO `{$db_prefix}calendar` VALUES {$all_value}");
	recache_currentmonthentries ();
	catchsuccess ($finishok4, $backtocache);
}

if ($job=='rebuildrewritefiles') {
	$all=$blog->getgroupbyquery("SELECT `blogid`,`blogalias` FROM `{$db_prefix}blogs` WHERE `blogalias`<>''");

	if (is_array($all)) foreach ($all as $item) {
		$blogalias=blogalias_convert($item['blogalias']);
		if ($blogalias) {
			$redirect_new="<?php\nchdir('../');\n\$entryid={$item['blogid']};\ninclude('read.php');";
			writetofile("post/{$blogalias}.php", $redirect_new);
		}
	}
	catchsuccess ($finishok5, $backtocache);
}



if ($job=='adminattach') {

	$start_id=($page-1)*$adminitemperpage;
	acceptrequest('uploadmonth,uploadyear');

	if (!empty($uploadyear) && empty($uploadmonth)) {
		$starttimestamp=mktime(0, 0, 0, 1, 1, $uploadyear);
		$finishtimestamp=mktime(23, 59, 59, 12, 31, $uploadyear);
		$queryplus="WHERE `uploadtime`>={$starttimestamp} AND `uploadtime`<={$finishtimestamp} ";
	}
	if (!empty($uploadmonth) && !empty($uploadyear)) {
		$starttimestamp=mktime(0, 0, 0, $uploadmonth, 1, $uploadyear);
		$finishtimestamp=mktime(23, 59, 59, $uploadmonth+1, 0, $uploadyear);
		$queryplus="WHERE `uploadtime`>={$starttimestamp} AND `uploadtime`<={$finishtimestamp} ";
	}
	
	$detail_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}upload` {$queryplus} ORDER BY `uploadtime` DESC LIMIT {$start_id}, 51");
	$numenries=$blog->countbyquery("SELECT COUNT(*) FROM `{$db_prefix}upload` {$queryplus}");

	if (count($detail_array)!=0) {
		foreach ($detail_array as $dafile) {
			$time_t=date('Y-n-j', $dafile['uploadtime']);
			$dataoriginalname=urldecode($dafile['originalname']);
			$tablebody.="<tr class='visibleitem'><td width=35 align=center><input type=checkbox name='selid[]' value='{$dafile['fid']}'></td><td align=center width=50%>{$dataoriginalname}</td><td align=center>{$time_t}</td><td width=35 align=center><a href=\"javascript: redirectcomfirm('admin.php?go=carecenter_delupload&filename={$dafile['fid']}');\"><img src='admin/theme/{$themename}/del.gif' alt='{$lna[78]}' title='{$lna[78]}' border='0'></a></td><td width=35 align=center><a href=\"{$dafile['filepath']}\" target='_blank'><img src='admin/theme/{$themename}/view.gif' alt='{$lna[782]}' title='{$lna[782]}' border='0'></a></td><td width=35 align=center><a href=\"admin.php?go=carecenter_updateattachment&fileid={$dafile['fid']}\"><img src='admin/theme/{$themename}/edit.gif' alt='{$lna[782]}' title='{$lna[782]}' border='0'></a></td></tr>";
		}
	}

	$foryears=range(2001,2050);
	$formonths=range(1,12);
	$showysel="<select name=uploadyear><option value=0 selected>{$lna[291]}</option><option value={$nowtime['year']}>{$nowtime['year']}</option>";
	$showmsel="<select name=uploadmonth><option value=0>{$lna[292]}</option>";

	foreach ($foryears as $y) {
		$showysel.="<option value=$y>$y</option>\n";
	}
	foreach ($formonths as $m) {
		$showmsel.="<option value=$m>$m</option>\n";
	}
	$showysel.="</select>\n";
	$showmsel.="</select>\n";

	$pagebar=gen_page ($page, 5, "admin.php?go=carecenter_adminattach&uploadyear={$uploadyear}&uploadmonth={$uploadmonth}", $numenries, $adminitemperpage);


	$display_overall.=highlightadminitems('adminattach', 'carecenter');
$display_overall_plus= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[36]}
</td>
<td class="sectend">{$lna[783]}</td>
</tr>
</table>
<form action="admin.php?go=carecenter_adminattach" method="post">
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr><td colspan=7>
<!--
{$lna[784]} <select name="specext2"><option value=''>{$lna[785]}</option><option value='gif jpg png bmp jpeg'>{$lna[786]}</option><option value='rar zip bz2 gz tar ace 7z'>{$lna[787]}</option><option value='txt doc htm html wps xsl ppt'>{$lna[788]}</option><option value='mp3 wma wmv rm ra rmvb wav asf swf'>{$lna[789]}</option></select> {$lna[790]} <input type=text name=specext size=9> {$lna[791]} <input type=submit value="{$lna[64]}" class='formbutton'>
-->
{$showysel} / {$showmsel} <input type=submit value='{$lna[244]}' class='formbutton'>
</td></tr>
<tr><td colspan=7 height=10></td></tr>
</table>
</form>

<form action="admin.php?go=carecenter_delupload" method="post" id='f_s' name='f_s'>
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr align=center class="admintitle"><td width=35 align=center>{$lna[245]}</td><td align=center>{$lna[792]}</td><td align=center width=260>{$lna[793]}</td><td align=center>{$lna[78]}</td><td align=center>{$lna[794]}</td><td align=center>{$lna[875]}</td></tr>
{$tablebody}
<tr><td colspan=7><a href="#unexist" onclick="checkallbox('f_s', 'checked');">{$lna[247]}</a> | <a href="#unexist" onclick="checkallbox('f_s', '');">{$lna[248]}</a><br>{$pagebar}</td></tr>
<tr><td colspan=7 height=20></td></tr>
<tr class="adminoption"><td colspan=7 align=center><input type=checkbox name=opt value='del'>{$lna[795]}<input type='button' value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax('f_s');">
</td></tr>
</table>
</form>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='delupload') {
	acceptrequest('selid,filename,opt');
	if ($filename!=='') {
		$filename=floor($filename);
		$detail_array=$blog->getbyquery("SELECT * FROM `{$db_prefix}upload` WHERE `fid`='{$filename}' LIMIT 1");

		if (file_exists($detail_array['filepath'])) $result=@unlink ($detail_array['filepath']);
		else $result=true;
		if ($result) 	{
			$blog->query("DELETE FROM `{$db_prefix}upload` WHERE `fid`='{$filename}'");
			if ($ajax=='on') {
				$fetchURL='admin.php?go=carecenter_adminattach';
				catchsuccessandfetch($delok, $fetchURL);
			} else catchsuccess ($delok, "{$lna[758]}|admin.php?go=carecenter_adminattach&dir={$dir}");
		}
		else catcherror ($lna[796]);
	}
	if ($opt!="del") {
		catcherror($lna[499]);
	}
	if (!is_array($selid)) catcherror ($lna[797]);
	else {
		$files=array();
		foreach ($selid as $filename) {
			$files[]=floor($filename);
		}
	}
	if (count($files)>0) {
		$delfiles=@implode(',', $files);
		$detail_array=$blog->getarraybyquery("SELECT * FROM `{$db_prefix}upload` WHERE `fid` in ({$delfiles}) ");
		foreach ($detail_array['filepath'] as $filesingle) {
			@unlink($filesingle);
		}
		$blog->query("DELETE FROM `{$db_prefix}upload` WHERE `fid` in ({$delfiles}) ");
	}
	if ($ajax=='on') {
		$fetchURL='admin.php?go=carecenter_adminattach';
		catchsuccessandfetch($delok, $fetchURL);
	} else catchsuccess ($delok, "{$lna[758]}|admin.php?go=carecenter_adminattach");
}


if ($job=='updateattachment') {
	acceptrequest('fileid');
	$fileid=floor($fileid);
	$detail_array=$blog->getbyquery("SELECT * FROM `{$db_prefix}upload` WHERE `fid`='{$fileid}' LIMIT 1");
	if ($detail_array['fid']!=$fileid) catcherror($lna[127]);
	$dataoriginalname=urldecode($detail_array['originalname']);
	$message.="<table width=70% class=\"adminoption\"  align=center><tr><td width=15% valign=top><b>{$lna[792]}</b></td><td width=60%>{$dataoriginalname} &nbsp; [<a href=\"{$detail_array['filepath']}\" target=\"_blank\">{$lna[782]}</a>]</td></tr><tr><td width=15% valign=top><b>{$lna[408]}</b></td><td width=60%><input type=\"file\" name='newupfile' size=35></td></tr><tr><td colspan=2 align=center><input type='submit' value='{$lna[64]}' class='formbutton'> <input type='reset' value='{$lna[65]}' class='formbutton'></td></tr></table>";

	$display_overall.=highlightadminitems('adminattach', 'carecenter');
$display_overall.= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[36]}
</td>
<td class="sectend">{$lna[783]}</td>
</tr>
</table>
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<form action="admin.php?go=carecenter_replaceattachment" method="post" enctype='multipart/form-data'>
<tr><td height=110>
$message
<input type="hidden" name="fileid" value="{$fileid}" />
</td></tr>
</form>
</table>
eot;
}


if ($job=='replaceattachment') {
	acceptrequest('fileid');
	$fileid=floor($fileid);
	$detail_array=$blog->getbyquery("SELECT * FROM `{$db_prefix}upload` WHERE `fid`='{$fileid}' LIMIT 1");
	if ($detail_array['fid']!=$fileid) catcherror($lna[127]);

	unset ($upload_filename_list, $upload_parts);
	$imgext_watermark=array('jpg', 'gif', 'png');
	$lang_wm=explode('|', $lna[999]);

	$newupfile=$_FILES['newupfile'];
	if (!$newupfile['name']) catcherror ($lna[418]);

	//die(print_r($newupfile));
	
	$targetfolder=dirname($detail_array['filepath']);
	$targetname=basename($detail_array['filepath']);


	$upload_file=$newupfile['tmp_name'];
	$upload_file_size=$newupfile['size'];
	if ($upload_file_size>$permission['MaxSize']*1024) catacherror ("{$lna[421]} ( {$permission['MaxSize']} KB)");

	$ext=strtolower(strrchr($newupfile['name'],'.'));
	$ext=str_replace(".", '', $ext);
	$permission['AllowedTypes']=@explode(' ', $permission['AllowedTypes']);

	if (@!in_array($ext, $permission['AllowedTypes'])) {
		catcherror ("{$lna[420]} .{$ext} ");
	}

	@unlink($detail_array['filepath']);
	if (file_exists($detail_array['filepath'])) catcherror($lna[1185]);

	if (!move_uploaded_file ($upload_file,"{$targetfolder}/{$targetname}")) catcherror ($lna[130]."{$targetfolder}/");

	//Add watermark
	if ($mbcon['wmenable']=='1') {
		if (in_array($ext, $imgext_watermark)) {
			unset($watermark_result);
			$watermark_result=create_watermark("{$targetfolder}/{$targetname}");
			if (!$watermark_result) $watermark_result="<br>({$lang_wm[0]}: {$lang_wm[8]}{$watermark_err})";
			else $watermark_result="<br>({$lang_wm[0]}: {$watermark_result})";
		} else $watermark_result='';
	} else $watermark_result='';

	catchsuccess($replaceok.' '.$watermark_result, "{$lna[758]}|admin.php?go=carecenter_adminattach");
}


if ($job=='mysql') {
	$all_tables=array("{$db_prefix}blogs", "{$db_prefix}calendar", "{$db_prefix}categories", "{$db_prefix}counter", "{$db_prefix}forbidden", "{$db_prefix}history", "{$db_prefix}linkgroup", "{$db_prefix}links", "{$db_prefix}maxrec", "{$db_prefix}messages", "{$db_prefix}mods", "{$db_prefix}replies", "{$db_prefix}tags", "{$db_prefix}user", "{$db_prefix}plugins", "{$db_prefix}pages", "{$db_prefix}upload");
	$tablebody.="<tr>";
	for ($i=0; $i<count($all_tables); $i++) {
		$tablebody.="<td><input type=checkbox name='selid[]' value='{$all_tables[$i]}' checked> {$all_tables[$i]}</td>";
		if ($i%3==2) $tablebody.="</tr><tr>";
	}
	$tablebody.="</tr>";
	$display_overall.=highlightadminitems('mysql', 'carecenter');
$display_overall.= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
MySQL
</td>
<td class="sectend">{$lna[798]}</td>
</tr>
<form action="admin.php?go=carecenter_mysqlquery" method='post'>
<tr class='sect'>
<td colspan=2>
{$lna[799]} {$db_prefix} {$lna[800]}<br>
<font color=red>{$lna[801]}</font><br>
<textarea rows="10" cols="80" name=sqlinput></textarea>
<br>{$lna[802]}<br>
<input type='submit' value='{$lna[64]}' class='formbutton'> <input type='reset' value='{$lna[65]}' class='formbutton'>
<br><br>
</td></tr>
</form>
</table>
<br><br>
<form action="admin.php?go=carecenter_optimize" method='post' id="ajaxForm1">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[803]}
</td>
<td class="sectend">{$lna[804]}</td>
</tr>
<tr class='sect'>
<td colspan=2>
{$lna[805]}
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
{$tablebody}
</table>
<br>
<div align=center><input type='button' value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax(1);"> <input type='reset' value='{$lna[65]}' class='formbutton'></div>
</td></tr>
</table>
</form>
</table>
eot;
}

if ($job=='mysqlquery') {
	acceptrequest('sqlinput');
	$sqlinput=stripslashes($sqlinput);
	$sqlinput=str_replace('&#96;', '`', $sqlinput);
	if (!$sqlinput) catcherror ($lna[806]);
	$sqlinput=str_replace('[db]', $db_prefix, $sqlinput);
	$result=db_query($sqlinput);
	if (@db_num_rows($result)>0) {
		$table_infos.="<table width=\"100%\"><tr><td><b>SQL: {$qinput[$i]}</b></td></tr></table><table width=\"100%\">";            	
		$columns=mysql_num_fields($result);
		$table_infos.="<tr class='admintitle'><td>";
		for ($s = 0; $s < $columns; $s++) {
			$table_infos .= mysql_field_name($result, $s) . "</td><td>\n";
		} 
		$table_infos .= "</td></tr>";
		while (false !== ($tmpline = db_fetch_array($result))) {
			$table_infos .= "<tr><td class='visibleitem'>". implode("</td><td class='visibleitem'>", $tmpline) ."</td></tr>";
		}
		$table_infos .= "</table>";
	}
	$display_overall.=highlightadminitems('mysql', 'carecenter');
$display_overall.= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
MySQL
</td>
<td class="sectend">{$lna[798]}</td>
</tr>
</table>
<table class='tablewidth' align=center cellpadding=0 cellspacing=0>
<tr><td>
<center><b>{$lna[1151]}</b></center>
<div class='sect' style="overflow-x: auto; height: auto; width: 90%;">
$table_infos
</div>
</td></tr>
</table>
<br><br>
<div align=center><input type=button onclick="window.location='admin.php?go=carecenter_mysql';" value="{$lna[344]}"></div>
eot;
}

if ($job=='optimize') {
	acceptrequest('selid');
	if (!is_array($selid)) {
		header("Location: admin.php?go=carecenter_mysql");
		exit();
	}
	$tables_query=@implode(',', $selid);
	$blog->query("OPTIMIZE TABLE {$tables_query}");
	catchsuccess ($optimizeok, $backtomysql);
}

if ($job=='export') {
	$all_tables=array("blogs"=>$lna[807], "categories"=>$lna[809], "forbidden"=>$lna[810], "history"=>$lna[811], "linkgroup"=>$lna[812], "links"=>$lna[763], "messages"=>$lna[813], "mods"=>$lna[814], "replies"=>$lna[815], "tags"=>'Tags', "user"=>$lna[816], "plugins"=>$lna[954], "pages"=>$lna[1164], "upload"=>$lna[1165], "textfile"=>$lna[1027]);
	$tablebody.="<tr>";
	$i=0;
	foreach ($all_tables as $key=>$val) {
		$tablebody.="<td><input type=checkbox name='selid[]' value='{$key}' checked> {$val}</td>";
		if ($i%3==2) $tablebody.="</tr><tr>";
		$i++;
	}
	$tablebody.="</tr>";
	$display_overall.=highlightadminitems('export', 'carecenter');
$display_overall.= <<<eot
<script type="text/javascript">
function swapdiv(opt) {
	if (opt==1) {
		document.getElementById("showrange1").style.display="block";
		document.getElementById("showrange2").style.display="none";
	}  else {
		document.getElementById("showrange1").style.display="none";
		document.getElementById("showrange2").style.display="block";
	} 
}
setCookie ('exrange','',null,null, null, false);
setCookie ('iszip','',null,null, null, false);
setCookie ('expause','',null,null, null, false);
setCookie ('exall','',null,null, null, false);
setCookie ('endnumber','',null,null, null, false);
setCookie ('currenttable','',null,null, null, false);
setCookie ('batchid','',null,null, null, false);
</script>

<form action="admin.php?go=carecenter_doexport" method="post" id='f_s' name='f_s'>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[817]}
</td>
<td class="sectend">{$lna[818]}</td>
</tr>
</table>
<table class='tablewidth' align=center cellpadding=8 cellspacing=0>
<tr><td class="hiddenitem">
<b>{$lna[819]}</b>
</td></tr>
<tr><td class="visibleitem">

<table class='tablewidth' align=center cellpadding=0 cellspacing=0>
<tr>
<td><input type="radio" name="extype" value="xml" checked onclick="swapdiv(1);"><b>{$lna[820]}</b></td><td>{$lna[821]}</td>
</tr>
<tr>
<td><input type="radio" name="extype" value="rss" onclick="swapdiv(2);"><b>RSS 2.0</b> </td><td>{$lna[822]}</td>
</tr>
</table>

</td></tr>
<tr><td class="hiddenitem">
<b>{$lna[823]}</b>
</td></tr>
<tr><td class="visibleitem">

<div id="showrange1" style="display:block;">
<table class='tablewidth' align=center cellpadding=0 cellspacing=0>
$tablebody
</table>
<br>
<table class='tablewidth' align=center cellpadding=0 cellspacing=0>
<tr><td>
<a href="#unexist" onclick="checkallbox('f_s', 'checked');">{$lna[247]}</a> | <a href="#unexist" onclick="checkallbox('f_s', '');">{$lna[248]}</a>
</td></tr>
</table>
</div>

<div id="showrange2" style="display:none;">
<table class='tablewidth' align=center cellpadding=0 cellspacing=0>
<tr><td>
<input type=checkbox checked disabled>{$lna[807]}
</td></tr>
</table>
</div>

</td></tr>
<tr><td class="hiddenitem">
<b>{$lna[824]}</b>
</td></tr>
<tr><td class="visibleitem">
{$lna[825]} <input type=text name='exrange' value='50' size='4'> {$lna[539]}<br>
{$lna[826]} <input type=text name='expause' value='2' size='2'> {$lna[296]}<br>
{$lna[827]} <input type=radio name='iszip' value=1 checked>{$lna[828]}<input type=radio  name='iszip' value=0>{$lna[829]}<br>

</td></tr>
<tr><td class="hiddenitem">
<b>{$lna[830]}</b>
</td></tr>
<tr><td class="visibleitem">
<ul><li>{$lna[831]}</li><li>{$lna[832]}</li><li>{$lna[833]}</li></ul>
</td></tr>

</table>
<br><br>
<div align=center><input type=submit value="{$lna[64]}" class='formbutton'> <input type=reset value="{$lna[65]}" class='formbutton'></div>
eot;
}

if ($job=='doexport') {
	acceptrequest('extype,exrange,expause,iszip,selid', 1, 'post');
	if ($extype=='xml') {
		if (!is_array($selid)) catcherror ($lna[834]);
		if (count($selid)==0)  catcherror ($lna[834]);
		else {
			$exall=implode("//", $selid);
		}
		$gotype="xmlbackup";
	} else $gotype="rssbackup";
	$expause=floor($expause);
	if ($iszip==1) { //Check if gzip is supported
		if (!function_exists("gzopen")) catcherror($lna[835]);
	}

	$display_overall.=highlightadminitems('export', 'carecenter');
$display_overall.= <<<eot
<script type="text/javascript">
var dateObjexp= new Date();
dateObjexp.setSeconds(7200);
setCookie ('exrange', '$exrange', dateObjexp, null, null, false);
setCookie ('iszip', '$iszip', dateObjexp, null, null, false);
setCookie ('expause', '$expause', dateObjexp, null, null, false);
setCookie ('exall', '$exall', dateObjexp, null, null, false);
setCookie ('endnumber', '0', dateObjexp, null, null, false);
setCookie ('currenttable', '0', dateObjexp, null, null, false);
setCookie ('batchid', "{$nowtime['timestamp']}", dateObjexp, null, null, false);
</script>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[817]}
</td>
<td class="sectend">{$lna[818]}</td>
</tr>
<tr>
<td colspan=2 class="sect"><br>{$lna[836]}<br>{$lna[837]}<br>
<div id="backupshowarea">
<div align=center><a href="admin.php?ajax=on&go=carecenter_{$gotype}">{$lna[838]}</a></div>
</div>
</td>
</tr>
</table>
eot;
}

if ($job=='xmlbackup') {
	acceptcookie('exrange,iszip,expause,exall,endnumber,currenttable,batchid');
	$start_id=$endnumber;
	$end_id=$start_id+$exrange-1;
	$filename="bak{$batchid}_{$currenttable}_{$start_id}.bak";
	$all_tables=@explode('//', $exall);
	if ($currenttable>=count($all_tables)) {
		$display=finishbackup($batchid);
		$display_overall.=highlightadminitems('export', 'carecenter');
$display_overall.= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[817]}
</td>
<td class="sectend">{$lna[818]}</td>
</tr>
<tr>
<td colspan=2 class="sect"><br>{$lna[839]}<br><br>
$display
<br>{$lna[840]}<br></td>
</tr>
</table>
eot;
	return;
	}
	$current_table=$all_tables[$currenttable];
	if ($current_table=='textfile') {
		$importantfiles=array('downloadcounter.php', 'mod_config.php', 'modules.php', 'cache_emot.php', 'cache_usergroup.php');
		for ($i=0; $i<=$maxrecord['maxgpid']; $i++) {
			$importantfiles[]="usergroup{$i}.php";
		}
		$out_importantfile="<?php\n";
		$i=0;
		foreach ($importantfiles as $important_file) {
			if (file_exists("data/{$important_file}")) {
				$tmpread=base64_encode(readfromfile("data/{$important_file}"));
				$out_importantfile.="\$bfname[{$i}]='data/{$important_file}';\n";
				$out_importantfile.="\$bfcontent[{$i}]=\"{$tmpread}\";\n";
				$i+=1;
			}
		}
	
		$out_importantfile.="\$additional['counter']='{$statistics['total']}';\n";

		writetofile("bak/textfile_".$filename, $out_importantfile);
		addbakfile('textfile_'.$filename, $batchid);

		$lasttime=1;
		$currenttable+=1;
		$endnumber=0;
		$jssetcookie="setCookie ('endnumber', '$endnumber', dateObjexp, null, null, false);\n";
		$jssetcookie.="setCookie ('currenttable', '$currenttable', dateObjexp, null, null, false);";
	} else {
		$count_ct=$blog->countbyquery("SELECT COUNT(*) FROM `{$db_prefix}{$current_table}`");
		if ($end_id>=$count_ct-1) {
			$lasttime=1;
			$currenttable+=1;
			$jssetcookie="setCookie ('currenttable', '$currenttable', dateObjexp, null, null, false);\n";
			$endnumber=0;
		}
		else {
			$lasttime=0;
			$endnumber=$end_id+1;
		}
		$jssetcookie.="setCookie ('endnumber', '$endnumber', dateObjexp, null, null, false);";
		$data_back=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}{$current_table}` LIMIT {$start_id}, {$exrange}");
		if (is_array($data_back)) {
			foreach ($data_back as $bakdata) {
				$out_bak.=backup_xmlbody($bakdata, $current_table);
			}
			$content_out=trim($out_bak);
			if ($iszip==0) writetofile("bak/".$filename, $content_out);
			else {
				$filename.='.gz';
				gzwritetofile("bak/".$filename, $content_out);
			}
			addbakfile($filename, $batchid);
		}
	}
	$display_overall=str_replace("</head>", "\r\n<meta http-equiv=\"refresh\" content=\"{$expause};URL=admin.php?go=carecenter_xmlbackup\"></head>", $display_overall);
	$display_overall.=highlightadminitems('export', 'carecenter');
$display_overall.= <<<eot
<script type="text/javascript">
var dateObjexp= new Date();
dateObjexp.setSeconds(7200);
$jssetcookie
</script>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[817]}
</td>
<td class="sectend">{$lna[818]}</td>
</tr>
<tr>
<td colspan=2 class="sect"><br>{$lna[841]}<br><br>
{$lna[842]} $filename
<br><br>{$lna[843]}<br><br></td>
</tr>
</table>
eot;
}

if ($job=='rssbackup') {
	include_once("data/cache_adminlist.php");
	acceptcookie('exrange,iszip,expause,endnumber,currenttable,batchid');
	$current_table="{$db_prefix}blogs";
	$start_id=$endnumber;
	$end_id=$start_id+$exrange-1;
	$filename="bak{$batchid}_rss_{$start_id}.xml";
	$count_ct=$blog->countbyquery("SELECT COUNT(*) FROM `{$current_table}`");
	if ($start_id>=$count_ct) {
		$display=finishbackup($batchid);
		$display_overall.=highlightadminitems('export', 'carecenter');
$display_overall.= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[817]}
</td>
<td class="sectend">{$lna[818]}</td>
</tr>
<tr>
<td colspan=2 class="sect"><br>{$lna[839]}<br><br>
$display
<br>{$lna[840]}<br></td>
</tr>
</table>
eot;
	return;
	}
	$endnumber=$end_id+1;
	$jssetcookie.="setCookie ('endnumber', '$endnumber', dateObjexp, null, null, false);";
	$data_back=$blog->getgroupbyquery("SELECT * FROM `{$current_table}` LIMIT {$start_id}, {$exrange}");
	if (is_array($data_back)) {
		foreach ($data_back as $bakdata) {
			$out_bak.=backup_rssbody($bakdata);
		}
		$content_out=backup_rsswhole($out_bak);
		if ($iszip==0) writetofile("bak/".$filename, $content_out);
		else {
			$filename.='.gz';
			gzwritetofile("bak/".$filename, $content_out);
		}
		addbakfile($filename, $batchid);
	}
	$display_overall=str_replace("</head>", "\r\n<meta http-equiv=\"refresh\" content=\"{$expause};URL=admin.php?go=carecenter_rssbackup\"></head>", $display_overall);
	$display_overall.=highlightadminitems('export', 'carecenter');
$display_overall.= <<<eot
<script type="text/javascript">
var dateObjexp= new Date();
dateObjexp.setSeconds(7200);
$jssetcookie
</script>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[817]}
</td>
<td class="sectend">{$lna[818]}</td>
</tr>
<tr>
<td colspan=2 class="sect"><br>{$lna[841]}<br><br>
{$lna[842]} $filename
<br><br>{$lna[843]}<br><br></td>
</tr>
</table>
eot;
}

if ($job=='import') {
	for ($i=0; $i<sizeof($arrayvalue_categories); $i++) {
		$puttingcates.="<option value='{$arrayvalue_categories[$i]}'>{$arrayoption_categories[$i]}</option>";
	}
	$display_overall.=highlightadminitems('import', 'carecenter');
$display_overall.= <<<eot
<script type="text/javascript">
function swapdiv(opt) {
	if (opt==1) {
		document.getElementById("showrange1").style.display="block";
		document.getElementById("showrange2").style.display="none";
	}  else {
		document.getElementById("showrange1").style.display="none";
		document.getElementById("showrange2").style.display="block";
	} 
}
setCookie ('endnumber','',null,null, null, false);
setCookie ('impause','',null,null, null, false);
setCookie ('srcindex','',null,null, null, false);
setCookie ('nonstop','',null,null, null, false);
</script>

<form action="admin.php?go=carecenter_doimport" method="post" enctype='multipart/form-data'>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[844]}
</td>
<td class="sectend">{$lna[845]}</td>
</tr>
</table>
<table class='tablewidth' align=center cellpadding=8 cellspacing=0>
<tr><td class="hiddenitem">
<b>{$lna[846]}</b>
</td></tr>
<tr><td class="visibleitem">

<table class='tablewidth' align=center cellpadding=0 cellspacing=0>
<tr>
<td><input type="radio" name="imtype" value="xml" checked onclick="swapdiv(1);"><b>{$lna[820]}</b></td><td>{$lna[847]}</td>
</tr>
<tr>
<td><input type="radio" name="imtype" value="rss" onclick="swapdiv(2);"><b>RSS 2.0</b> </td><td>{$lna[848]}</td>
</tr>
</table>

</td></tr>
<tr><td class="hiddenitem">
<b>{$lna[849]}</b>
</td></tr>
<tr><td class="visibleitem">

<div id="showrange1" style="display:block;">
<table class='tablewidth' align=center cellpadding=0 cellspacing=0>
<tr><td>
{$lna[850]}<br>
{$lna[851]} bak/<input type='text' name='srcindex'> <br>
{$lna[826]} <input type=text name='impause' value='2' size='2'> {$lna[296]}<br>
{$lna[953]} <input type=radio name='nonstop' value='1'>{$lna[239]} <input type=radio name='nonstop' value='0' checked>{$lna[240]}<br>
</table>
</div>

<div id="showrange2" style="display:none;">
<table class='tablewidth' align=center cellpadding=0 cellspacing=0>
<tr><td>
{$lna[852]}<input type='file' name='rsssrc'><br>
{$lna[853]} xml/txt/gz<br>
{$lna[854]} <select name='targetcate'>{$puttingcates}</select><br>
{$lna[855]}<br>
</td></tr>
</table>
</div>

</td></tr>
<tr><td class="hiddenitem">
<b>{$lna[830]}</b>
</td></tr>
<tr><td class="visibleitem">
<ul><li><font color=red>{$lna[892]}</font></li><li>{$lna[831]}</li><li>{$lna[833]}</li></ul>
</td></tr>

</table>
<br><br>
<div align=center><input type=submit value="{$lna[64]}" class='formbutton'> <input type=reset value="{$lna[65]}" class='formbutton'></div>
eot;
}

if ($job=='doimport') {
	acceptrequest('imtype,impause,srcindex,targetcate,nonstop', 1, 'post');
	if ($imtype=='xml') {
		if (!file_exists("bak/{$srcindex}")) catcherror($lna[856]);
		else {
			$tmp=readfromfile("bak/{$srcindex}");
			if (strstr($tmp, ".gz") && !function_exists('gzopen'))  catcherror($lna[857]);
		}
		$display_overall.=highlightadminitems('import', 'carecenter');
$display_overall.= <<<eot
<script type="text/javascript">
var dateObjexp= new Date();
dateObjexp.setSeconds(7200);
setCookie ('endnumber', '0', dateObjexp, null, null, false);
setCookie ('impause', '$impause', dateObjexp, null, null, false);
setCookie ('srcindex', '$srcindex', dateObjexp, null, null, false);
setCookie ('dbnonstop', '$nonstop', dateObjexp, null, null, false);
setCookie ('changevisittotal','',null,null, null, false);
</script>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[844]}
</td>
<td class="sectend">{$lna[845]}</td>
</tr>
<tr>
<td colspan=2 class="sect"><br>{$lna[836]}<br>{$lna[837]}<br>
<div align=center><a href="admin.php?go=carecenter_rollback">{$lna[858]}</a></div>
</td>
</tr>
</table>
eot;
	} else {
		if ($targetcate==='') catcherror($lna[859]);
		$rsssrc=$_FILES['rsssrc'];
		if (!$rsssrc) catcherror($lna[860]);
		$upload_file=$rsssrc['tmp_name'];
		$upload_filename=urlencode($rsssrc['name']);
		$ext=strtolower(strrchr($upload_filename,'.'));
		if ($ext!='.gz' && $ext!='.xml' && $ext!='.txt') catcherror($lna[861]);
		if ($ext=='.gz' && !function_exists('gzopen'))  catcherror($lna[857]);
		if (!move_uploaded_file ($upload_file,"bak/{$upload_filename}")) catcherror ($lna[130].'bak/');
		$display_overall.=highlightadminitems('import', 'carecenter');
$display_overall.= <<<eot
<script type="text/javascript">
var dateObjexp= new Date();
dateObjexp.setSeconds(7200);
setCookie ('srcindex', '$upload_filename', dateObjexp, null, null, false);
setCookie ('endnumber', '$targetcate', dateObjexp, null, null, false);
</script>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[844]}
</td>
<td class="sectend">{$lna[845]}</td>
</tr>
<tr>
<td colspan=2 class="sect"><br>{$lna[836]}<br>{$lna[862]}{$categories[$targetcate]['catename']}<br>{$lna[837]}<br>
<div align=center><a href="admin.php?go=carecenter_rssrollback">{$lna[858]}</a> | <a href="admin.php?go=carecenter_import">{$lna[863]}</a></div>
</td>
</tr>
</table>
eot;
	}
}

if ($job=='rollback') {
	acceptcookie('endnumber,impause,srcindex,dbnonstop');
	if ($dbnonstop==1) $ignore_db_errors=1;
	$tmp=readfromfile("bak/{$srcindex}");
	$all_files=@explode('//', $tmp);
	if ($endnumber>=count($all_files)) {

		//Auto refresh caches now
		$selid=array('links','emotselection', 'mods','adminlist','categories','latestentries', 'taglist', 'plugins');
		for ($i=0; $i<count($selid); $i++) {
			$func="recache_{$selid[$i]}";
			call_user_func($func);
		}

		$countsql=array(
			'entries'=>"SELECT COUNT(blogid) FROM `{$db_prefix}blogs`", 
			'replies'=>"SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`<=1", 
			'tb'=>"SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`=4", 
			'messages'=>"SELECT COUNT(repid) FROM `{$db_prefix}messages` WHERE `reproperty`<=1", 
			'users'=>"SELECT COUNT(userid) FROM `{$db_prefix}user`"
		);
		foreach ($countsql as $key=>$value) {
			$result_num=$blog->countbyquery($value);
			if ($key=='users') $result_num-=1;
			$blog->query("UPDATE `{$db_prefix}counter` SET `{$key}`={$result_num}");
		}
		if ($_COOKIE['changevisittotal']) {
			$result_num=floor($_COOKIE['changevisittotal']);
			$blog->query("UPDATE `{$db_prefix}counter` SET `total`=`total`+{$result_num}");
		}

		$maxsql=array(
			"SELECT MAX(blogid) FROM `{$db_prefix}blogs`",
			"SELECT MAX(userid) FROM `{$db_prefix}user`", 
			"SELECT MAX(cateid) FROM `{$db_prefix}categories`",
			"SELECT MAX(repid) FROM `{$db_prefix}replies`", 
			"SELECT MAX(repid) FROM `{$db_prefix}messages`", 
			"SELECT MAX(linkgpid) FROM `{$db_prefix}linkgroup`", 
			"SELECT MAX(linkid) FROM `{$db_prefix}links`"
		);
		$max2ar=array('maxblogid','maxuserid','maxcateid','maxrepid','maxmessagepid','maxlinkgpid','maxlinkid');
		for ($i=0; $i<count($maxsql); $i++) {
			$resultmax=$blog->countbyquery($maxsql[$i]);
			if (empty($resultmax)) $resultmax=0;
			$query_sql[]="`{$max2ar[$i]}`={$resultmax}";
		}
		$all_sql="UPDATE `{$db_prefix}maxrec` SET ".@implode(',', $query_sql);
		$blog->query($all_sql);

		$blog->query("TRUNCATE TABLE `{$db_prefix}calendar`");
		$all=$blog->getgroupbyquery("SELECT `blogid`,`pubtime` FROM `{$db_prefix}blogs` WHERE `property`<>2 AND `property`<>3");
		if (is_array($all)) {
			foreach ($all as $item) {
				$time=$item['pubtime'];
				$t_Ym=gmdate('Ym', $time+3600*$config['timezone']);
				$t_day=gmdate('j', $time+3600*$config['timezone']);
				$values[]="('{$t_Ym}', '{$t_day}', '{$item['blogid']}', '')";
			}
		}
		$all_value=@implode(',', $values);
		if ($all_value) $blog->query("INSERT INTO `{$db_prefix}calendar` VALUES {$all_value}");
		recache_currentmonthentries ();
		//Auto refresh caches end


		$display_overall.=highlightadminitems('import', 'carecenter');
$display_overall.= <<<eot
<script type='text/javascript'>
setCookie ('endnumber','',null,null, null, false);
setCookie ('impause','',null,null, null, false);
setCookie ('srcindex','',null,null, null, false);
setCookie ('nonstop','',null,null, null, false);
setCookie ('changevisittotal','',null,null, null, false);
</script>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[844]}
</td>
<td class="sectend">{$lna[845]}</td>
</tr>
<tr>
<td colspan=2 class="sect"><br>{$lna[864]}
</td>
</tr>
</table>
eot;
		return;
	}
	$current_file=$all_files[$endnumber];
	if (file_exists("bak/{$current_file}")) {
		if (!strstr($current_file, 'textfile_')) {
			if (strstr($current_file, '.gz')) {
				$raw_data_whole=gzreadfromfile("bak/{$current_file}");
				$raw_data=@explode("\n", $raw_data_whole);
			}
			else $raw_data=file("bak/{$current_file}");
			rollback_xml ($raw_data);
		} else {
			unset($bfname, $bfcontent);
			include ("bak/{$current_file}");
			for ($i=0; $i<count($bfname); $i++) {
				writetofile($bfname[$i], base64_decode($bfcontent[$i]));
			}
			if ($additional['counter']) $jssetcookie="setCookie ('changevisittotal', '{$additional['counter']}', dateObjexp, null, null, false);\n";
		}
	}
	$jssetcookie.="setCookie ('endnumber', '".($endnumber+1)."', dateObjexp, null, null, false);";
	$display_overall=str_replace("</head>", "\r\n<meta http-equiv=\"refresh\" content=\"{$impause};URL=admin.php?go=carecenter_rollback\"></head>", $display_overall);
	$display_overall.=highlightadminitems('import', 'carecenter');
$display_overall.= <<<eot
<script type="text/javascript">
var dateObjexp= new Date();
dateObjexp.setSeconds(7200);
$jssetcookie
</script>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[844]}
</td>
<td class="sectend">{$lna[845]}</td>
</tr>
<tr>
<td colspan=2 class="sect"><br>{$lna[867]}<br><br>
{$lna[868]} $current_file
<br><br>{$lna[843]}<br><br></td>
</tr>
</table>
eot;
}

if ($job=='rssrollback') {
	acceptcookie('srcindex,endnumber');
	$srcindex=basename($srcindex);
	if (!file_exists("bak/{$srcindex}")) catcherror($lna[869]);
	if (strstr($srcindex, '.gz')) $rsscontent=@gzreadfromfile("bak/{$srcindex}");
	else $rsscontent=readfromfile("bak/{$srcindex}");
	$array_insert=rssrollback($rsscontent);
	if (is_array($array_insert)) {
		$i=$maxrecord['maxblogid'];
		foreach ($array_insert as $singlevalue) {
			$allvalues[]="('{$i}', '{$singlevalue['title']}', '{$singlevalue['time']}', '{$singlevalue['content']}', '{$endnumber}', '{$userdetail['userid']}', 1, '')";
			$i+=1;
		}
		$all_values=@implode(',', $allvalues);
		$sql_query="INSERT INTO {$db_prefix}blogs (`blogid`, `title`, `pubtime`, `content`, `category`, `authorid`, `htmlstat`, `permitgp`) VALUES {$all_values}";
		$blog->query($sql_query);
	}
	$display_overall.=highlightadminitems('import', 'carecenter');
$display_overall.= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[844]}
</td>
<td class="sectend">{$lna[845]}</td>
</tr>
<tr>
<td colspan=2 class="sect"><br>{$lna[864]}<br>{$lna[865]}<br>
<div align=center><a href="admin.php?go=carecenter_recache">{$lna[866]}</a></div>
</td>
</tr>
</table>
eot;
}

function backup_xmlbody($data_array, $table_name) {
	global $db_prefix;
	$out.="tablename:{$table_name}>>>";
	foreach ($data_array as $datakey=>$dataval) {
		$dataval=base64_encode($dataval);
		$out.="{$datakey}:{$dataval}>>>";
	}
	$out.="\n";
	return $out;
}

function backup_rssbody($entry) {
	global $mbcon, $adminlist, $config, $categories;
	$entrytitle=$entry['title'];
	$entrytime=gmdate('r', ($entry['pubtime']+3600*$config['timezone']));
	$tmp=$entry['authorid'];
	$entryauthor=$adminlist[$tmp];
	$entryemail="user@domain.com";
	$tmp=$entry['category'];
	$entrycate=$categories[$tmp]['catename'];
	$entrycontent=$entry['content'];
	$entryurl="{$config['blogurl']}/read.php?{$entry['blogid']}";
	$rss=<<<eot
<item>
<link>{$entryurl}</link>
<title>{$entrytitle}</title> 
<author>{$entryauthor} &lt;{$entryemail}&gt;</author>
<category>{$entrycate}</category>
<pubDate>{$entrytime}</pubDate> 
<guid>{$entryid}</guid> 
<description>
<![CDATA[ 
	{$entrycontent}
]]> 
</description>
</item>
eot;
	return $rss;
}

function backup_rsswhole($rssbody) {
	global $mbcon, $config, $categories, $blogversion;
	$out="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n";
	$out.=<<<eot
<rss version="2.0">
<channel>
<title>{$config['blogname']}</title> 
<link>{$config['blogurl']}/index.php</link> 
<description>{$config['blogdesc']}</description> 
<language>zh-cn</language> 
<copyright>Powered by Bo-blog {$blogversion}</copyright>
{$rssbody}
</channel>
</rss>
eot;
	return $out;
}

function rollback_xml($data_array) {
	global $db_prefix, $maxrecord, $blog;
	$tmp=@explode('>>>', $data_array[0]);
	for ($i=1; $i<count($tmp)-1; $i++) {
		@list($key, $unuse)=@explode(':', $tmp[$i]);
		$allkeys[]="`".$key."`";
	}
	$finalallkeys=@implode(',', $allkeys);
	$table_name=str_replace('tablename:', '', $tmp[0]);
	switch ($table_name) {
		case 'blogs':
			$uniquekey="blogid";
			break;
		case 'categories':
			$uniquekey="cateid";
			break;
		case 'linkgroup':
			$uniquekey="linkgpid";
			break;
		case 'links':
			$uniquekey="linkid";
			break;
		case 'messages':
			$uniquekey="repid";
			break;
		case 'replies':
			$uniquekey="repid";
			break;
		case 'user':
			$uniquekey="userid";
			break;
		case 'mods':
			$uniquekey="name";
			break;
		case 'tags':
			$uniquekey="tagname";
			break;
		case 'plugins':
			$uniquekey="plid";
			break;
		case 'pages':
			$uniquekey="pageid";
			break;
		case 'upload':
			$uniquekey="fid";
			break;
		default:
			$uniquekey='';
	}
	foreach ($data_array as $rawdata) {
		$raws=@explode('>>>', $rawdata);
		for ($i=1; $i<count($raws)-1; $i++) {
			@list($key, $value)=@explode(':', $raws[$i]);
			if ($key==$uniquekey) {
				$deletenow[]="'".base64_decode($value)."'";
			}
			$alleachvalues[]="'".addslashes(base64_decode($value))."'";
		}
		$allsinglevalues[]="(".@implode(',', $alleachvalues).")";
		unset ($alleachvalues);
	}
	if (is_array($deletenow) && $uniquekey!='') {
		$sql_query="DELETE FROM `{$db_prefix}{$table_name}` WHERE `{$uniquekey}` IN (".@implode(',', $deletenow).")";
		$blog->query($sql_query);
	}
	$finalallvalues=@implode(',', $allsinglevalues);
	if ($finalallvalues!='') {
		$sql_query="INSERT INTO `{$db_prefix}{$table_name}` ({$finalallkeys}) VALUES {$finalallvalues}";
		$blog->query($sql_query);
	}
}

function rssrollback ($rawdata) {
	$rawdata=str_replace("\r", '', $rawdata);
	$rawdata=str_replace("\n", '', $rawdata);
	$rawdata=str_replace("<![CDATA[", '', $rawdata);
	$rawdata=str_replace("]]>", '', $rawdata);
	preg_match_all("/<item>(.+?)<\/item>/is", $rawdata, $array_match);
	$xmlall=$array_match[1];
	if (!is_array($xmlall)) $array_insert[]=parserss($xmlall);
	else {
		foreach ($xmlall as $xml) {
			$array_insert[]=parserss($xml);
		}
	}
	return $array_insert;
}

function parserss ($xml) {
	$count_items=preg_match("/<title>(.+?)<\/title>(.+?)<pubDate>(.+?)<\/pubDate>(.+?)<description>(.+?)<\/description>/is", $xml, $array_match);
	if ($count_items!=0) {
		$title=addslashes($array_match[1]);
		$time=strtotime($array_match[3]);
		if (preg_match("/<content:encoded>(.+?)<\/content:encoded>/is", $xml, $array_match_possible)!=0) $content=addslashes($array_match_possible[1]);
		else $content=addslashes($array_match[5]);
	}
	return array('title'=>$title, 'time'=>$time, 'content'=>$content);
}

function addbakfile($filename, $batchid) {
	if (file_exists("bak/bak{$batchid}_filelist.txt")) {
		$ir=readfromfile("bak/bak{$batchid}_filelist.txt");
		$is=@explode('//', $ir);
	}
	$is[]=$filename;
	$ir=@implode('//', $is);
	writetofile ("bak/bak{$batchid}_filelist.txt", $ir);
}

function finishbackup($batchid) {
	global $lna;
	$ir=trim(readfromfile("bak/bak{$batchid}_filelist.txt"));
	if (!$ir) return ($lna[870]);
	$is=@explode('//', $ir);
	$display="<b>{$lna[871]}</b>{$lna[872]}<br>";
	foreach ($is as $item) {
		$display.="<a href=\"bak/{$item}\">bak/{$item}</a><br>";
		if (strstr($item, 'textfile_')) $txtfilebacked=1;
	}
	$display.="<a href=\"bak/bak{$batchid}_filelist.txt\">bak/bak{$batchid}_filelist.txt</a><br>";
	if ($txtfilebacked!=1) $display.="<br>{$lna[900]}<br>";
	return $display;
}

function gzwritetofile ($gzfilename, $gzcontent) {
	$gzfp=gzopen($gzfilename, 'wb9');
	gzwrite($gzfp, $gzcontent);
	gzclose($gzfp);
}

function gzreadfromfile ($gzfilename) {
   $file = @gzopen($gzfilename, 'rb');
   if ($file) {
       $data = '';
       while (!gzeof($file)) {
           $data .= gzread($file, 1024);
       }
       gzclose($file);
   }
   return $data;
}

?>
