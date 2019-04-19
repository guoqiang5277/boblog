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

include_once("data/cache_adminskinlist.php");
$themename=$currentadminskin;
$csslocation="admin/theme/{$themename}/common.css";
if ($permission['Upload']==0) print_upload ($lna[413]);
confirmpsw(); //Re-check password
acceptrequest('useeditor');
$useeditor=basename($useeditor);
if (!$useeditor) $useeditor=$mbcon['editortype'];

if (!$job || $job=="default") {
	$message="<form enctype='multipart/form-data' action=\"admin.php?go=upload_doattach&useeditor={$useeditor}\" method=\"post\">";
	$display_allowed="<select><option>".@str_replace(' ', "</option><option>", $permission['AllowedTypes'])."</option></select>";
	$message.="<table width=86% align=center class=\"adminoption\" ><tr><td width=25% valign=top align=center>{$lna[408]}</td><td width=60%><input type=\"file\" name='newupfile[]'><br><input type=\"file\" name='newupfile[]'><br><input type=\"file\" name='newupfile[]'><br><input type=\"file\" name='newupfile[]'><br><input type=\"file\" name='newupfile[]'><br>{$lna[677]} {$display_allowed}</td></tr><tr><td colspan=10 align=center><input type='submit' value='{$lna[414]}' class='formbutton'> <input type='reset' value='{$lna[65]}' class='formbutton'></td></tr></table></form>";
	print_upload ($message);
}

if ($job=="doattach") {
	acceptrequest ('filerename');
	unset ($upload_filename_list, $upload_parts);
	$imgext_watermark=array('jpg', 'gif', 'png');
	$lang_wm=explode('|', $lna[999]);

	$newupfiles=$_FILES['newupfile'];
	if (!$newupfiles) print_upload ($lna[418]);

	if ($mbcon['uploadfolders']=='1') {
		$targetfolder_ym=date("Ym").'/';
		$targetfolder="attachment/{$targetfolder_ym}";
		if (!is_dir ($targetfolder)) {
			$mktargetfolder=@mkdir($targetfolder, 0777);
			if (!$mktargetfolder) print_upload ($lna[974]);
		}
	} else {
		$targetfolder_ym='';
		$targetfolder='attachment';
	}

	$newupfiles=reArrayFiles($newupfiles);

	$permission['AllowedTypes']=@explode(' ', $permission['AllowedTypes']);

	for ($i=0; $i<count($newupfiles); $i++) {
		if (!$newupfiles[$i]['tmp_name']) continue;
		$newupfile=$newupfiles[$i];
		$upload_file=$newupfile['tmp_name'];
		$upload_file_size=$newupfile['size'];

		$ext=strtolower(strrchr($newupfile['name'],'.'));
		$ext=str_replace(".", '', $ext);

		$upload_filename=urlencode(str_replace('+', ' ', $newupfile['name']));

		//Change name
		$original_uploadname=$upload_filename;
		$upload_filename=time().'_'.rand(1000, 9999).substr(md5($original_uploadname), 0, 4).'.'.$ext;

		if (@!in_array($ext, $permission['AllowedTypes'])) {
			print_upload ("{$lna[420]} .{$ext} ");
		}
		if ($upload_file_size>$permission['MaxSize']*1024) print_upload ("{$lna[421]} ( {$permission['MaxSize']} KB)");

		$upload_filename=strtolower($upload_filename);
		
		if (!move_uploaded_file ($upload_file,"{$targetfolder}/{$upload_filename}")) print_upload ($lna[130]."{$targetfolder}/");

		@chmod("{$targetfolder}/{$upload_filename}", 0755);

		//Add watermark
		if ($mbcon['wmenable']=='1') {
			if (in_array($ext, $imgext_watermark)) {
				unset($watermark_result);
				$watermark_result=create_watermark("{$targetfolder}/{$upload_filename}");
				if (!$watermark_result) $watermark_result="<br>({$lang_wm[0]}: {$lang_wm[8]}{$watermark_err})";
				else $watermark_result="<br>({$lang_wm[0]}: {$watermark_result})";
			} else $watermark_result='';
		} else $watermark_result='';

		//DB updating, new function in 2.1.0
		$blog->query("INSERT INTO `{$db_prefix}upload` (fid,filepath,originalname,uploadtime,uploaduser) VALUES (null, \"attachment/{$targetfolder_ym}{$upload_filename}\", \"{$original_uploadname}\", {$nowtime['timestamp']}, {$userdetail['userid']})");
		$currentid=db_insert_id();

		$upload_filename_list[]="{$targetfolder_ym}".str_replace('.', '*', $original_uploadname);
		$upload_filename_list_insert[]="[attach]{$currentid}[/attach]";
		$upload_filename_watermark[]=$watermark_result;
	}

	for ($i=0; $i<count($upload_filename_list); $i++) {
		$upload_parts.=str_replace('*', '.', $upload_filename_list[$i])." <input type='button' value='{$lna[424]}' onclick=\"generateUpload('{$upload_filename_list_insert[$i]}', '{$upload_filename_list[$i]}');\">{$upload_filename_watermark[$i]}<br>";
	}

	print_upload ("<div align=left>{$lna[422]}<br>{$upload_parts}<br><div align=center><input type='button' value='{$lna[423]}' onclick='window.location=\"admin.php?go=upload&useeditor={$useeditor}\";'><span style='display: none;'><input type='checkbox' id='ifautoaddubb' checked='checked'></span></div></div>");
}

if ($job=="filedir") {
	$start_id=($page-1)*51;
	acceptrequest('uploadmonth,uploadyear');
	$queryplus=$showysel=$showmsel='';
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

	$inserttext=array();

	for ($i=0; $i<count($detail_array); $i++) {
		$file=$detail_array[$i]['originalname'];
		$file2=str_replace('.', '*', $file);
		$inserttext[]="<li><a href=\"javascript: generateUpload('[attach]{$detail_array[$i]['fid']}[/attach]', '{$file2}');\">".urldecode($file)."</a></li>\n";
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

	$pagebar=gen_page ($page, 5, "admin.php?go=upload_filedir&useeditor={$useeditor}&uploadyear={$uploadyear}&uploadmonth={$uploadmonth}", $numenries, 51);

	$message="<form action='admin.php?go=upload_filedir&useeditor={$useeditor}' method=post><div align=left style=\"margin-left: 15px;\">{$showysel} / {$showmsel} <input type=submit value='{$lna[244]}'> &nbsp; &nbsp; {$pagebar}</div></form><div align=left style=\"margin-left: 15px;\"><b>{$lna[425]}</b> <input type='checkbox' id='ifautoaddubb' checked='checked'>{$lna[426]}</div><div id='uploadrow'><ul>".@implode("\n", $inserttext)."</ul></div>";
	print_upload ($message, "normal", "highlight", "normal");
}

if ($job=="gallery") {
	$all_images=array('.gif','.jpg','.png','.bmp','.jpeg');
	$constr=makeaquery ($all_images, "`originalname` LIKE '%%s%'", 'OR');
	$start_id=($page-1)*51;
	acceptrequest('uploadmonth,uploadyear');
	$queryplus=$showysel=$showmsel='';
	if (!empty($uploadyear) && empty($uploadmonth)) {
		$starttimestamp=mktime(0, 0, 0, 1, 1, $uploadyear);
		$finishtimestamp=mktime(23, 59, 59, 12, 31, $uploadyear);
		$queryplus="AND `uploadtime`>={$starttimestamp} AND `uploadtime`<={$finishtimestamp} ";
	}
	if (!empty($uploadmonth) && !empty($uploadyear)) {
		$starttimestamp=mktime(0, 0, 0, $uploadmonth, 1, $uploadyear);
		$finishtimestamp=mktime(23, 59, 59, $uploadmonth+1, 0, $uploadyear);
		$queryplus="AND `uploadtime`>={$starttimestamp} AND `uploadtime`<={$finishtimestamp} ";
	}
	
	$detail_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}upload` WHERE {$constr} {$queryplus} ORDER BY `uploadtime` DESC LIMIT {$start_id}, 51");
	$numenries=$blog->countbyquery("SELECT COUNT(*) FROM `{$db_prefix}upload` WHERE {$constr} {$queryplus}");

	$inserttext=array();

	for ($i=0; $i<count($detail_array); $i++) {
		$file=$detail_array[$i]['originalname'];
		$file2=str_replace('.', '*', $file);
		$inserttext[]="<li><a href=\"javascript: generateUpload('[attach]{$detail_array[$i]['fid']}[/attach]', '{$file2}');\"  onmouseover=\"picPreview('{$detail_array[$i]['filepath']}', '[attach]{$detail_array[$i]['fid']}[/attach]');\">".urldecode($file)."</a></li>\n";
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

	$pagebar=gen_page ($page, 3, "admin.php?go=upload_gallery&useeditor={$useeditor}&uploadyear={$uploadyear}&uploadmonth={$uploadmonth}", $numenries, 51);

	$message="<form action='admin.php?go=upload_gallery&useeditor={$useeditor}' method=post><div align=left style=\"margin-left: 15px;\">{$showysel} / {$showmsel} <input type=submit value='{$lna[244]}'> &nbsp; &nbsp; {$pagebar} &nbsp; &nbsp; <input type='checkbox' id='ifautoaddubb' checked='checked'>{$lna[426]}</div></form><div><div id='uploadrow'  style=\"width: 210px; float: left; overflow-y: auto; height: 165px;\"><ul>".@implode("\n", $inserttext)."</ul></div><div id='picp' style='margin-top: 0px;  width: 400px !important; width: 320px; height: 165px;'>{$lna[427]}</div></div>";
	print_upload ($message, "normal", "normal", "highlight");
}

function print_upload ($message, $classup="highlight", $classdown="normal", $classpre="normal") {
	global $csslocation, $mbcon, $lna, $langback, $useeditor;
$display_overall.=<<<eot
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="UTF-8">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="UTF-8" />
<link rel="stylesheet" rev="stylesheet" href="{$csslocation}" type="text/css" media="all" />
<script type='text/javascript' src="lang/{$langback}/jslang.js"></script>
<script type='text/javascript' src="editor/{$useeditor}/uploader.js"></script>
</head>
<body style="margin: 0px; padding: 0px; overflow-y: auto; overflow-x: hidden; height: 200px;">
<div id="adminrow" style="width: 100%">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>

<table width="100%" cellpadding="0" cellspacing="0" style="border-left: 1px solid #000; border-right: 1px solid #000;">
<tr>
<td class="$classup" height="15" width="33%"><a href="admin.php?go=upload&useeditor={$useeditor}">{$lna[428]}</a></td>
<td class="$classdown" height="15" width="33%"><a href="admin.php?go=upload_filedir&useeditor={$useeditor}">{$lna[429]}</a></td>
<td class="$classpre" height="15" width="34%"><a href="admin.php?go=upload_gallery&useeditor={$useeditor}">{$lna[430]}</a></td>
</tr>
</table>

</td></tr><tr>
<td>

<table width="100%" cellpadding="1" cellspacing="0" height="180" style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">
<tr>
<td class="sect" align=center valign=top>$message</td>
</tr>
</table>

</td>
</tr>
</table>
</div>
</body></html>
eot;
@header("Content-Type: text/html; charset=utf-8");
die ($display_overall);
}