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
//Define some senteces
$finishok=$lna[42];
$finishok2=$lna[43];
$backtoconfig="{$lna[13]}|admin.php?go=main_config";
$backtombcon="{$lna[14]}|admin.php?go=main_mbcon";
$backtomodule="{$lna[15]}|admin.php?go=main_module";
$backtorefreshcache="{$lna[866]}|admin.php?go=carecenter";
$prefseccounter=0;

if (!$job) $job='default';

if ($job=='default') {
	$phpver=PHP_VERSION;
	$phpos=PHP_OS.' '.$_SERVER['SERVER_SOFTWARE'];
	$mysqlver=@mysql_get_server_info();
	if (function_exists("gd_info")) {
		$tmp_gd_info=gd_info();
		$gdver=$tmp_gd_info["GD Version"];
	} else $gdver=$lna[44];

	$last_cleartmp_time=$statistics['empty1'];
	if (($nowtime['timestamp']-$last_cleartmp_time)>864000) {
		$last_cleartmp_days=floor(($nowtime['timestamp']-$last_cleartmp_time)/86400);
		$promptcleartmp=sprintf($lna[1166], $last_cleartmp_days);
	} else $promptcleartmp='';

	//Begin check unapproved comments and messages
	$pending_replies=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`=2 OR `reproperty`=3");
	if ($flset['guestbook']!=1) $pending_messages=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}messages` WHERE `reproperty`=2 OR `reproperty`=3");
	$pending_tbs=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`=5");
	if (file_exists("data/cache_applylinks.php")) {
		$tmps=@file("data/cache_applylinks.php");
		if ($tmps[0]=='') $pending_links=0;
		else $pending_links=count($tmps);
		unset ($tmps);
	} else $pending_links=0;
	$pending_replies_show=(empty($pending_replies)) ? " (0 {$lna[45]})" : " (<b><font color=red>{$pending_replies}</font> {$lna[45]}</b>)";
	if ($flset['guestbook']!=1) {
		$pending_messages_show=(empty($pending_messages)) ? " (0 {$lna[45]})" : " (<b><font color=red>{$pending_messages}</font> {$lna[45]}</b>)";
		$pending_messages_shows="<li><a href=\"admin.php?go=message_censor\">{$lna[26]}</a> $pending_messages_show</li>";
	}
	$pending_tbs_show=(empty($pending_tbs)) ? " (0 {$lna[45]})" : " (<b><font color=red>{$pending_tbs}</font> {$lna[45]}</b>)";
	$pending_links_show=(empty($pending_links)) ? " (0 {$lna[45]})" : " (<b><font color=red>{$pending_links}</font> {$lna[45]}</b>)";

	foreach ($adminskin as $adskitem) {
		$selected=($adskitem==$currentadminskin) ? 'selected' : '';
		$admskinsel.="<option name='$adskitem' {$selected}>{$adskitem}</option>";
	}

	if (file_exists("lang/{$langback}/licence")) $copytxt=nl2br(readfromfile("lang/{$langback}/licence"));
	else $copytxt=nl2br(readfromfile("admin/licence"));
	$display_overall.=highlightadminitems('default', 'main');
$display_overall_plus= <<<eot
<form action='admin.php?go=main_selectadminskin' method='post'>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[47]}
</td>
<td class="sectend">{$lna[48]}</td>
</tr>
<tr>
<td colspan=2  class="sect">
$promptcleartmp
<ul>
	<li><a href="admin.php?go=edit_add">{$lna[22]}</a></li>
	<li><a href="admin.php?go=reply_censor">{$lna[24]}</a> $pending_replies_show</li>
	$pending_messages_shows
	<li><a href="admin.php?go=reply_tbcensor">{$lna[947]}</a> $pending_tbs_show</li>
	<li><a href="admin.php?go=link_pending">{$lna[21]}</a> $pending_links_show</li>
	<li><a href="admin.php?go=addon_skin">{$lna[27]}</a></li>
	<li><a href="admin.php?go=addon_plugin">{$lna[28]}</a></li>
	<li><a href="admin.php?go=main_update">{$lna[16]}</a></li>
	<li>{$lna[874]} <select name=targetskin>{$admskinsel}</select> <input type='submit' value='$lna[64]' class='formbutton'> <input type='button' value='{$lna[875]}' onclick='window.location="admin.php?go=main_refreshadminskinlist";' class='formbutton'></li>
</td>
</tr>
</table>
</form><br><br>

<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[49]}
</td>
<td class="sectend">{$lna[50]}</td>
</tr>
<tr>
<td colspan=2  class="sect">

<b>{$lna[51]}</b> {$phpver} &nbsp;&nbsp;[<a href="admin.php?go=main_phpinfo" target="_blank">{$lna[55]}</a>]<br> 
<b>{$lna[52]}</b> {$phpos} <br>
<b>{$lna[53]}</b> {$mysqlver} <br>
<b>{$lna[54]}</b> {$gdver} <br>
<!-- <div align=center><a href="admin.php?go=main_phpinfo" target="_blank">{$lna[55]}</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?go=main_dbstat">{$lna[56]}</a></div> -->

</td>
</tr>
</table>
<br><br>

<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td class="sectstart">
{$lna[146]}
</td>
</tr>
<tr>
<td colspan=2  class="sect">
<b>Version {$blogversion}</b> ({$codeversion})<br>
<br>
<div style="height: 100px; width: 500px; overflow:auto; border: 1px solid #CCC">
<b>Core Team</b><br>
<b>Chief Developer:</b> Bob<br>
<b>Plugins:</b> Mesak Chuan<br>
<b>UI Designers:</b> guihuo<br>
<b>Supports:</b> Totti, Lang Man Hai Er, yeyo, Mahayu<br>
<br>
<b>Localization</b><br>
<b>English:</b> Bob<br>
<b>Traditional Chinese:</b> Mesak Chuan<br>
<b>Vietnamese:</b> Meng Ling, kusanagi<br>
<br>
<b>Security</b><br>
<b>Code Detection:</b> Bug.Center.Team<br>
<br>
<b>Special Thanks to</b><br>
msxcms, Nicky, Loveshell, 4ngel, and many more...
</div>
</td>
</tr>
</table>


<br><br>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[46]}
</td>
<td class="sectend">&nbsp;</td>
</tr>
<tr>
<td colspan=2  class="sect">
<div style="height: 100px; width: 500px; overflow:auto; border: 1px solid #CCC">
{$copytxt}
</div>

</td>
</tr>
</table>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='update') {
	$config['blogurl']=str_replace('{host}', $_SERVER['HTTP_HOST'], $config['blogurl']);
	$hostinfomation=base64_encode("{$config['blogurl']}/");
	$display_overall.=highlightadminitems('update', 'main');
$display_overall.= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[16]}
</td>
<td class="sectend">{$lna[57]}</td>
</tr>
<tr>
<td colspan=2  class="sect">
<div id='newversionshow'></div>
</td>
</tr>
</table>
<script type="text/javascript" src="{$config['updatesrc']}/updatejs.php?v={$codeversion}&lang={$langback}&h={$hostinfomation}"></script>
eot;
}

if ($job=='phpinfo') {
	phpinfo();
	exit();
}

if ($job=='dbstat') {
	$results_t=$blog->query("SHOW TABLE STATUS");
	$table_infos="<table width=\"100%\"><tr class='admintitle'><td><b>{$lna[58]}</b></td><td><b>{$lna[59]}</b></td><td><b> 	{$lna[60]}</b></td><td><b>{$lna[61]}</b></td></tr>";
	while (false!==($line_t=db_fetch_array($results_t)) && strstr($line_t['Name'], $db_prefix)) {
		$table_infos .="<tr><td>{$line_t['Name']}</td><td>{$line_t['Rows']}</td>	<td>".round($line_t['Data_length']/1024, 2)." KB</td><td>".round($line_t['Index_length']/1024, 2)." KB</td></tr>";
		$datalengthall+=$line_t['Data_length'];
		$indexlengthall+=$line_t['Index_length'];
	} 
	$table_infos.="<tr class='sect'><td><b>{$lna[62]}</b></td><td>&nbsp;</td><td>".round($datalengthall/1024, 2)." KB</td><td>".round($indexlengthall/1024, 2)." KB</td></tr>";
	$table_infos.="</table>";
	$display_overall.=highlightadminitems('default', 'main');
	$display_overall.=$table_infos;
}

if ($job=='config') {
	$pref_leftchar="180";
	$pref_variable="config";
	include ("admin/pref_config.php");
	$pref_result_show=@implode('', $pref_result);
	$display_overall.=highlightadminitems('config', 'main');
$display_overall.= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[13]}
</td>
<td class="sectend">{$lna[63]}</td>
</tr>
</table>
<table class='tablewidth' cellpadding=4 cellspacing=1 align=center>
<form action="admin.php?go=main_configsave" method="post" id="ajaxForm1">
<tr><td class="prefsection" align="center" colspan='2'><a name="top"></a>{$lna[47]}</td></tr>
<tr><td class="prefright" align="center" colspan='2'><table width=100%>{$pref_quicksel}</table></td></tr>
$pref_result_show
<tr><td class="prefleft" valign="top" width="180">{$lna[527]}</td><td class="prefright"><input type="button" value="{$lna[1083]}" onclick="window.location='admin.php?go=misc_urlrewrite';" class="formbutton"></td></tr>
</table>
<br><input type='hidden' name='prefconfig[urlrewritemethod]' value="{$config['urlrewritemethod']}">
<div align=center><a name="bottom"></a><input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax(1);"> <input type=reset value="{$lna[65]}" class='formbutton'></div>
</form>
eot;
}

if ($job=='configsave') {
	$savetext="<?PHP\n\$db_server='{$db_server}';\n\$db_username='{$db_username}';\n\$db_password='{$db_password}';\n\$db_name='{$db_name}';\n\$db_prefix='{$db_prefix}';\n\$db_410='{$db_410}';\n\$db_tmpdir='{$db_tmpdir}';\n\$db_defaultsessdir='{$db_defaultsessdir}';\n";
	$save_config=$_POST['prefconfig'];
	//catcherror (print_r($save_config, true));
	if (count($save_config)<=1) catcherror ($lna[1013]);
	$save_config['blogcreatetime']=strtotime($save_config['blogcreatetime']);
	while (@list ($key, $val) = @each ($save_config)) {
		$savetext.="\$config['{$key}']='".admin_convert(stripslashes($val))."';\n";
	}
	if ($savetext=='') catcherror ($lna[1013]);
	if (!writetofile ("data/configbakup.php", $savetext)) catcherror ($lna[66]."data/configbakup.php");
	if (writetofile ("data/config.php", $savetext)) {
		//Check now
		$tmp=readfromfile ("data/config.php");
		if (strlen($tmp)<=1) {
			@unlink("data/config.php");
			rename ("data/configbakup.php", "data/config.php");
		}
		catchsuccess ($finishok, array($backtoconfig, $backtombcon, $backtorefreshcache));
	} else {
		catcherror ($lna[66]."data/config.php");
	}
}

if ($job=='mbcon') {
	$pref_leftchar="200";
	$pref_variable="mbcon";
	include ("admin/pref_mbcon.php");
	$pref_result_show=@implode('', $pref_result);
	$cau_icon="<img src='admin/theme/{$themename}/global.gif' alt='.' align='absmiddle' border='0'>";
	$pref_result_show=str_replace("^global^", $cau_icon, $pref_result_show);
	$display_overall.=highlightadminitems('mbcon', 'main');
$display_overall.= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[14]}
</td>
<td class="sectend">{$lna[67]}</td>
</tr>
</table>
<table class='tablewidth' cellpadding=4 cellspacing=1 align=center>
<form action="admin.php?go=main_mbconsave" method="post" id="ajaxForm1">
<tr><td class="prefsection" align="center" colspan='2'><a name="top"></a>{$lna[47]}</td></tr>
<tr><td class="prefright" align="center" colspan='2'><table width=100%>{$pref_quicksel}</table></td></tr>
$pref_result_show
<tr><td colspan=2 class="sect"><ul><li>{$lna[746]}</li><li>{$cau_icon} {$lna[68]}</li><li>{$lna[69]}</li><li>{$lna[70]}</li></ul>
</table>
<br>
<div align=center><a name="bottom"></a><input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax(1);"> <input type=reset value="{$lna[65]}" class='formbutton'></div>
</form>
eot;
}

if ($job=='mbconsave') {
	$savetext="<?PHP\n";
	$save_config=$_POST['prefconfig'];
	if (count($save_config)<=1) catcherror ($lna[1013]);
	while (@list ($key, $val) = @each ($save_config)) {
		$savetext.="\$mbcon['{$key}']='".admin_convert($val)."';\n";
	}
	if ($savetext=='') catcherror ($lna[1013]);
	if (!writetofile ("data/mod_configbakup.php", $savetext)) catcherror ($lna[66]."data/mod_configbakup.php");
	if (writetofile ("data/mod_config.php", $savetext)) {
		//Check now
		$tmp=readfromfile ("data/mod_config.php");
		if (strlen($tmp)<=1) {
			@unlink("data/mod_config.php");
			rename ("data/mod_configbakup.php", "data/mod_config.php");
		}
		catchsuccess ($finishok, array($backtoconfig, $backtombcon, $backtorefreshcache));
	} else {
		catcherror ("{$lna[66]}"."data/mod_config.php");
	}
}

if ($job=='module') {
	acceptrequest('section');
	if (!$section) $section='header';
	$class_head=($section=='header') ? 'sect' : 'prefleft';
	$class_side=($section=='sidebar') ? 'sect' : 'prefleft';
	$class_footer=($section=='footer') ? 'sect' : 'prefleft';
	$class_prebody=($section=='prebody') ? 'sect' : 'prefleft';
	$class_new=($section=='new' || $section=='new2') ? 'sect' : 'prefleft';
	$realname=array('header'=>$lna[71], 'sidebar'=>$lna[72], 'footer'=>$lna[73], 'prebody'=>$lna[1055], 'new'=>$lna[74]);
	if ($section=='header' || $section=='sidebar' || $section=='footer' || $section=='prebody') {
		$formbody="<form action=\"admin.php?go=main_modulesave\" method=\"post\" id=\"ajaxForm1\"><table width=95% align=right cellpadding=4 cellspacing=1><tr class='admintitle' ><td width=30 align=center>{$lna[75]}</td><td align=center>{$lna[76]}</td><td align=center>{$lna[77]}</td><td align=center>{$lna[78]}</td></tr>\n";
		$mod_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}mods` WHERE `position`='{$section}' ORDER BY `modorder`");
		for ($i=0; $i<count($mod_array); $i++) {
			if ($mod_array[$i]['active']==1) {
				$chex=" checked";
				$addwords=$lna[79];
			}
			else {
				$chex="";
				$addwords="<font color=red>{$lna[80]}</font>";
			}
			$linkdel=($mod_array[$i]['func']=='system') ? "javascript: alert(\"{$lna[81]}\");" : "javascript: redirectcomfirm (\"admin.php?go=main_moduledel_".base64_encode($mod_array[$i]['name'])."\");";
			$linkedit="admin.php?go=main_module&section=edit&itemname=".base64_encode($mod_array[$i]['name']);
			$class_distinct=($i%2==0) ? 'visibleitem' : 'hiddenitem';
			$formbody.="<tr class='$class_distinct'><td width=30 align=center><input type=checkbox name='selid[]' value='{$mod_array[$i]['name']}'{$chex}></td><td width='80%'><b>{$mod_array[$i]['name']}</b>$addwords<br>{$mod_array[$i]['desc']}</td><td align=center width=30><a href='$linkedit'><img src='admin/theme/{$themename}/edit.gif' alt='{$lna[77]}' title='{$lna[77]}' border='0'></a></td><td align=center width=30><a href='$linkdel'><img src='admin/theme/{$themename}/del.gif' alt='{$lna[78]}' title='{$lna[78]}' border='0'></a></td></tr>\n";
		}
		$formbody.="<tr class='sect' align=center><td colspan=4><input type=hidden name=section value='$section'><input type=button value=\"{$lna[82]}\" class='formbutton' onclick=\"adminSubmitAjax(1);\"> <input type=reset value=\"{$lna[65]}\" class='formbutton'> <input type=button value=\"{$lna[83]}\" onclick=\"window.location='admin.php?go=main_module&section=new2&newitemposition={$section}'\" class='formbutton'> <input type=button value=\"{$lna[84]}\" onclick=\"window.location='admin.php?go=main_ordermodule&section={$section}'\" class='formbutton'></td></tr>\n";
		$formbody.="<tr class='sect'><td colspan=4><ul><li>{$lna[85]}</li><li>{$lna[86]}</li><li>{$lna[87]}</li><li>{$lna[88]}</li></ul></td></tr>\n";
	}
	if ($section=='new') {
		$formbody.="<form action=\"admin.php?go=main_module&section=new2\" method=\"post\" id=\"ajaxForm1\"><table width=95% align=right cellpadding=4 cellspacing=1><tr class='admintitle'><td align='center' colspan=2>1. {$lna[89]}</td></tr>\n";
		$formbody.="<tr><td align='center' class='prefleft' width=20% valign=top>{$lna[90]}</td><td class='hiddenitem' valign=top><select name='newitemposition'><option value='header' selected>{$lna[71]}</option><option value='sidebar'>{$lna[72]}</option><option value='footer'>{$lna[73]}</option><option value='prebody'>{$lna[1055]}</option></select></td></tr>\n";
		$formbody.="<tr class='admintitle'><td align='center' colspan=2><input type=submit value='{$lna[64]}' class='formbutton'> <input type=reset value='{$lna[65]}' class='formbutton'></td></tr></form>\n";
		$formbody.="<tr><td colspan=2 height=30></td></tr><form enctype='multipart/form-data' action=\"admin.php?go=main_automod\" method=\"post\"><tr class='admintitle'><td align='center' colspan=2>2. {$lna[91]}</td></tr>\n";
		$formbody.="<tr><td align='center' class='prefleft' width=20% valign=top>{$lna[92]}</td><td class='hiddenitem' valign=top><input type='file' name='newmodfile'><br>{$lna[93]}</td></tr>\n";
		$formbody.="<tr><td align='center' class='prefleft' width=20% valign=top>{$lna[482]}</td><td class='hiddenitem' valign=top><input type='password' name='modpassword'><br>{$lna[964]}</td></tr>\n";
		$formbody.="<tr class='admintitle'><td align='center' colspan=2><input type=submit value='{$lna[64]}' class='formbutton'> <input type=reset value='{$lna[65]}' class='formbutton'></td></tr></form>\n";
	}
	if ($section=='new2' || $section=='edit') {
		if ($section=='new2') {
			acceptrequest('newitemposition');
			$actionform='modulenew';
			$permitgpshow='';
			foreach ($usergp as $gpid=>$gpname) {
				$permitgpshow.="<input type='checkbox' name='permitgp[]' value='{$gpid}'>{$gpname} ";
			}
		}
		else {
			acceptrequest('itemname');
			$itemname=base64_decode($itemname);
			$actionform='moduledoedit';
			$detail=$blog->getbyquery("SELECT * FROM `{$db_prefix}mods` WHERE `name`='$itemname'");
			$newitemposition=$detail['position'];
			$newitemname=stripslashes($detail['name']);
			$newitemdesc=stripslashes($detail['desc']);
			$newitemactive=$detail['active'];
			include ("data/modules.php");
			$currentv=$blogitem[$newitemname];
			$func_text=stripslashes($currentv['text']);
			$func_url=stripslashes($currentv['url']);
			$func_target=stripslashes($currentv['target']);
			$func_title=stripslashes($currentv['title']);
			$func_extend=stripslashes($currentv['extend']);
			$func_content=str_replace('\n', "\r\n", $currentv['content']);
			$func_code=str_replace('\n', "\r\n", $currentv['code']);
			$func_content=stripslashes($currentv['content']);
			$func_code=stripslashes($currentv['code']);
			$indexonly=floor($currentv['indexonly']);
			$permitgpshow='';
			if ($currentv['permitgp']!='') {
				$allowedgp=@explode('|', $currentv['permitgp']);
				foreach ($usergp as $gpid=>$gpname) {
					$checked_permitgp=(!in_array($gpid, $allowedgp)) ? ' checked' : '';
					$permitgpshow.="<input type='checkbox' name='permitgp[]' value='{$gpid}'{$checked_permitgp}>{$gpname} ";
				}
			} else {
				foreach ($usergp as $gpid=>$gpname) {
					$permitgpshow.="<input type='checkbox' name='permitgp[]' value='{$gpid}'>{$gpname} ";
				}
			}
		}
		if ($detail['func']=='system') $ddshow=$lna[81];
		else {
			switch ($newitemposition) {
				case 'header':
					if ($func_target) $selected["x{$func_target}"]='selected';
					else $selected["x_self"]='selected';
					$ddshow="{$lna[71]}{$lna[94]}<br><br><table width=95% class='sect'><tr><td width=25%>{$lna[95]}</td><td><input type='text' name='func_text' size='30' value='{$func_text}'></td></tr><tr><td width=25%>{$lna[96]}</td><td><input type='text' name='func_url' size='30' value='{$func_url}'></td></tr><tr><td width=25%>{$lna[97]}</td><td><select name='func_target'><option value='_self' 			{$selected['x_self']}>{$lna[98]}</option><option value='_blank' {$selected['x_blank']}>{$lna[99]}</option><option value='_parent' {$selected['x_parent']}>{$lna[100]}</option></select></td></tr><tr><td width=25%>{$lna[101]}</td><td><input type='text' name='func_title' size='30' value='{$func_title}'> {$lna[102]}</td></tr></table>";
					break;
				case 'sidebar':
					$box_1=($func_extend==1) ? 'checked' : '';
					$box_0=($func_extend==0) ? 'checked' : '';
					$ddshow="{$lna[103]}<br><br><table width=95% class='sect'><tr><td width=25%>{$lna[104]}</td><td><input type='text' name='func_title' size='30' value='{$func_title}'><br>{$lna[105]}</td></tr><tr><td width=25%>{$lna[106]}</td><td><input type='radio' name='func_extend' value='1' {$box_1}>{$lna[107]} <input type='radio' name='func_extend' value='0' {$box_0}>{$lna[108]}</td></tr><tr><td width=25%>{$lna[109]}</td><td><textarea name='func_content' cols=40 rows=8>{$func_content}</textarea><br>{$lna[110]}</td></tr></table>";
					break;
				case 'footer':
					$ddshow="{$lna[111]}<br><br><table width=95% class='sect'><tr><td width=25%>{$lna[109]}</td><td><textarea name='func_code' cols=40 rows=8>{$func_code}</textarea><br>{$lna[110]}</td></tr></table>";
					break;
				case 'prebody':
					$ddshow="{$lna[111]}<br><br><table width=95% class='sect'><tr><td width=25%>{$lna[109]}</td><td><textarea name='func_code' cols=40 rows=8>{$func_code}</textarea><br>{$lna[110]}</td></tr></table>";
					break;
				default:
					$ddshow="{$lna[112]}";
			}
		}
		$formbody.="<form action=\"admin.php?go=main_{$actionform}\" method=\"post\" id=\"ajaxForm1\"><table width=95% align=right cellpadding=4 cellspacing=1><tr class='admintitle'><td align='center' colspan=2>{$lna[113]}</td></tr>\n";
		//$formbody.="<tr><td align='center' class='prefleft' width=20% valign=top>{$lna[482]}</td><td class='hiddenitem' valign=top><input name='modpassword' size=20 type='password'><br>{$lna[964]}</td></tr>\n";
		$formbody.="<tr><td align='center' class='prefleft' width=20% valign=top>{$lna[90]}</td><td class='hiddenitem' valign=top>{$realname[$newitemposition]}<input type=hidden name=newitemposition value={$newitemposition}></td></tr>\n";
		if ($section=='new2') {
			$formbody.="<tr><td align='center' class='prefleft' width=20% valign=top>{$lna[114]}</td><td class='hiddenitem' valign=top><input name='newitemname' size=20 type='text' value='{$newitemname}'><br>{$lna[115]}</td></tr>\n";
		} else {
			$formbody.="<tr><td align='center' class='prefleft' width=20% valign=top>{$lna[114]}</td><td class='hiddenitem' valign=top><input name='newitemname' type='hidden' value='{$newitemname}'>{$newitemname}</td></tr>\n";
		}
		$formbody.="<tr><td align='center' class='prefleft' width=20% valign=top>{$lna[116]}</td><td class='hiddenitem' valign=top><input name='newitemdesc' size=40 type='text' value='{$newitemdesc}'><br>{$lna[117]}</td></tr>\n";
		$box_3=($newitemactive==1) ? 'checked' : '';
		$box_4=($registeronly==1) ? 'checked' : '';
		$box_5=($indexonly==1) ? 'selected' : '';
		$box_6=($indexonly==2) ? 'selected' : '';
		$formbody.="<tr><td align='center' class='prefleft' width=20% valign=top>{$lna[118]}</td><td class='hiddenitem' valign=top><input name='newitemactive' type='checkbox' value='1' {$box_3}>{$lna[119]}</td></tr>\n";
		$formbody.="<tr><td align='center' class='prefleft' width=20% valign=top>{$lna[75]}</td><td class='hiddenitem' valign=top><select name='indexonly'> <option value=''>{$lna[1182]}</option><option value='1' {$box_5}>{$lna[1152]}</option><option value='2' {$box_6}>{$lna[1181]}</option></td></tr>\n";
		$formbody.="<tr><td align='center' class='prefleft' width=20% valign=top>{$lna[186]}</td><td class='hiddenitem' valign=top>{$lna[930]}<br>{$permitgpshow}</td></tr>\n";
		$formbody.="<tr><td align='center' class='prefleft' width=20% valign=top>{$lna[120]}</td><td class='hiddenitem' valign=top>{$ddshow}</td></tr>\n";
		$formbody.="<tr class='admintitle'><td align='center' colspan=2><input type=button value='{$lna[64]}' class='formbutton' onclick=\"adminSubmitAjax(1);\"> <input type=reset value='{$lna[65]}' class='formbutton'></td></tr>\n";
		$formbody.="<tr class='sect'><td colspan=2><ul><li>{$lna[121]}</li><li>{$lna[122]}</li></ul></td></tr>\n";
	}
	$display_overall.=highlightadminitems('module', 'main');
$display_overall_plus= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[15]}
</td>
<td class="sectend">{$lna[123]}</td>
</tr>
</table>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr><td width=150 class="sect" valign=top>

<table width=100% cellpadding=4 cellspacing=1>
<tr><td class="{$class_head}" align=center height=40><a href="admin.php?go=main_module&section=header">{$lna[71]}</a></td></tr>
<tr><td class="{$class_side}" align=center height=40><a href="admin.php?go=main_module&section=sidebar">{$lna[72]}</a></td></tr>
<tr><td class="{$class_footer}" align=center height=40><a href="admin.php?go=main_module&section=footer">{$lna[73]}</a></td></tr>
<tr><td class="{$class_prebody}" align=center height=40><a href="admin.php?go=main_module&section=prebody">{$lna[1055]}</a></td></tr>
<tr><td class="{$class_new}" align=center height=40><a href="admin.php?go=main_module&section=new">{$lna[113]}</a></td></tr>
</table>

</td>
<td class="sect" valign=top>


$formbody
</table>
</form>
</td></tr></table>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='modulesave') {
	acceptrequest('section,selid');
	if (!$section) @header("Location: admin.php?go=main_module");
	//if ($section=='footer' && !@in_array('copyright', $selid)) catcherror("{$lna[146]}"); //2006-11-22 not important, really
	$count_selid=(is_array($selid)) ? (count($selid)) : 0;
	if ($count_selid==0) $blog->query("UPDATE `{$db_prefix}mods` SET `active`=0 WHERE `position`='{$section}'");
	else {
		for ($i=0; $i<$count_selid; $i++) {
			$selid_array[$i]="'{$selid[$i]}'";
		}
		$in_selid=@implode(',', $selid_array);
		$blog->query("UPDATE `{$db_prefix}mods` SET `active`=1 WHERE `position`='{$section}' AND name in({$in_selid})");
		$blog->query("UPDATE `{$db_prefix}mods` SET `active`=0 WHERE `position`='{$section}' AND name not in({$in_selid})");
	}

	//Rebuild cache
	recache_mods ();
	$backtomodule.="&section={$section}";
	catchsuccess ($finishok2, $backtomodule);
}

if ($job=='modulenew' || $job=='moduledoedit') {
	acceptrequest('newitemname,newitemposition,newitemdesc,newitemactive,modpassword,permitgp,indexonly');

	$newitemactive=floor($newitemactive);
	$indexonly=floor($indexonly);
	
	acceptrequest('func_text,func_url,func_target,func_title,func_extend,func_content,func_code');
	if (!$newitemname || !$newitemposition || !$newitemdesc) catcherror($lna[124]);
	$try=$blog->getbyquery("SELECT * FROM `{$db_prefix}mods` WHERE `name`='$newitemname'");
	if ($job=='modulenew') {
		if ($try) catcherror($lna[125]);
	}
	$newitemdesc=safe_convert($newitemdesc);
	if (is_array($permitgp)) {
		$permitgp=array_diff(array_keys($usergp), $permitgp);
		$permitgp=array_values($permitgp);
		$permitgp=@implode('|', $permitgp);
		$value="'permitgp'=>'{$permitgp}', ";
	} else $value='';
	if ($indexonly) $value.="'indexonly'=>{$indexonly}, ";

	if ($try['func']!='system') {
		switch ($newitemposition) {
			case 'header':
				if (!$func_text || !$func_url || !$func_target) catcherror($lna[124]);
				$value.="'type'=>'link', 'url'=>'".safe_convert($func_url)."', 'text'=>'".safe_convert($func_text)."'";
				if ($func_target!='_self') $value.=", 'target'=>'{$func_target}'";
				if ($func_title) $value.=", 'title'=>'".safe_convert($func_title)."'";
				break;
			case 'sidebar':
				if (!$func_title || !$func_content) catcherror($lna[124]);
				$func_content=stripslashes($func_content);
				$func_content=str_replace("\"", "\\\"",$func_content);
				$func_content=str_replace("\r", '',$func_content);
				$func_content=str_replace("\n", '\n',$func_content);
				$value.="'type'=>'block', 'name'=>'".safe_convert($newitemname)."', 'title'=>'".safe_convert($func_title)."', 'extend'=>'{$func_extend}', 'content'=>\"{$func_content}\"";
				break;
			case 'footer':
				if (!$func_code) catcherror($lna[124]);
				$func_code=stripslashes($func_code);
				$func_code=str_replace("\"", "\\\"",$func_code);
				$func_code=str_replace("\r", '',$func_code);
				$func_code=str_replace("\n", '\n',$func_code);
				$value.="'type'=>'html', 'code'=>\"{$func_code}\"";
				break;
			case 'prebody':
				if (!$func_code) catcherror($lna[124]);
				$func_code=stripslashes($func_code);
				$func_code=str_replace("\"", "\\\"",$func_code);
				$func_code=str_replace("\r", '',$func_code);
				$func_code=str_replace("\n", '\n',$func_code);
				$value.="'type'=>'extraheader', 'code'=>\"{$func_code}\"";
				break;
		}
	}
	$value=trim($value);
	if (substr($value, -1, 1)==',') $value=substr($value, 0, strlen($value)-1); //Remove last ','
	$workout="\$blogitem['".safe_convert($newitemname)."']=array({$value});\n";

	if ($job=='modulenew') {
		$newitemname=safe_convert($newitemname);
		$newitemname=str_replace('_', '', $newitemname);
		$newitemnameforcheck=urlencode($newitemname);
		if (strstr($newitemnameforcheck, '%')) catcherror($lna[931]);
		$maxmodid=$blog->countbyquery("SELECT MAX(`modorder`) FROM `{$db_prefix}mods`");
		$maxmodid+=1;
		$blog->query("INSERT INTO `{$db_prefix}mods` VALUES ('$newitemposition', '$newitemname', '$newitemdesc', '$newitemactive', '$maxmodid', 'custom')");
		if ($newitemactive==1) {
			recache_mods ();
		}
		mod_append ($workout);
	} else {
		$blog->query("UPDATE `{$db_prefix}mods` SET `desc`='$newitemdesc', `active`='$newitemactive' WHERE `name`='$newitemname'");
		mod_replace ($newitemname ,$workout, true);
		recache_mods ();
	}
	if ($ajax=='on' && $job=='modulenew') {
		$fetchURL="admin.php?go=main_module&section=edit&itemname=".base64_encode($newitemname);
		catchsuccessandfetch($finishok2, $fetchURL);
	}
	else catchsuccess ($finishok2, $backtomodule);
}

if ($job=='moduledel') {
	if ($itemid==='') catcherror ($lna[126]);
	$itemid=base64_decode($itemid);
	$try=$blog->getbyquery("SELECT * FROM `{$db_prefix}mods` WHERE `name`='$itemid'");
	if (!$try) catcherror($lna[127]);
	$blog->query("DELETE FROM `{$db_prefix}mods` WHERE `name`='$itemid'");
	mod_replace ($itemid , '');
	if ($try['active']==1) recache_mods();
	if ($ajax=='on') {
		$fetchURL="admin.php?go=main_module&section={$try['position']}";
		catchsuccessandfetch($finishok2, $fetchURL);
	}
	else catchsuccess ($finishok2, $backtomodule);
}

if ($job=='automod') {
	acceptrequest('modpassword');
	$checkmodp=md5($modpassword);
	$checkmodp_try=array();
	$checkmodp_try=$blog->getbyquery("SELECT * FROM `{$db_prefix}user` WHERE `userid`='{$userdetail['userid']}' AND `userpsw`='{$checkmodp}'");
	if ($checkmodp_try['userid']!=$userdetail['userid']) catcherror($lna[965]);
	if ($checkmodp_try['usergroup']!=2) catcherror($lna[965]);
	
	$newmodfile=$_FILES['newmodfile'];
	if (!$newmodfile) catcherror($lna[128]);
	$upload_file=$newmodfile['tmp_name'];
	$upload_filename=urlencode($newmodfile['name']);
	$ext=strtolower(strrchr($upload_filename,'.'));
	if ($ext!='.blog' && $ext!='.txt') catcherror($lna[129]);
	if (!move_uploaded_file ($upload_file,"{$db_tmpdir}/{$upload_filename}")) catcherror ($lna[130].'temp/'); 
	$filecontent=readfromfile("{$db_tmpdir}/{$upload_filename}");
	$security_check=checksafe($filecontent);
	if ($security_check) $warn="<font color=red>{$lna[932]}</font>";
	else $warn=$lna[933];
	$display_overall.=highlightadminitems('module', 'main');
$display_overall.= <<<eot
<form action="admin.php?go=main_automod2" method="post">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[136]}
</td>
<td class="sectend">{$lna[137]}</td>
</tr>
</table>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr><td class="sect">
<b>{$lna[934]}<br><br></b>
$warn
<br><br>
<b>{$lna[923]}</b>
<br><br>
{$lna[135]}
<input type='hidden' name='upload_filename' value='{$upload_filename}'>
</td>
<tr class='admintitle'><td align='center'><input type=submit value='{$lna[64]}' class='formbutton'> <input type=button onclick='window.location="admin.php";' value='{$lna[138]}' class='formbutton'></td></tr>
</form>
</td></tr></table>
eot;
}

if ($job=='automod2') {
	$upload_filename=basename($_POST['upload_filename']);
	$ext=strtolower(strrchr($upload_filename,'.'));
	if ($ext!='.blog' && $ext!='.txt') catcherror($lna[129]);
	$filecontent=readfromfile("{$db_tmpdir}/".$upload_filename);
	eval ($filecontent);
	$warn.="<table width=95% align=center><tr><td width=20%>{$lna[131]}</td><td>{$info['name']}</td></tr><tr><td width=20%>{$lna[132]}</td><td>{$info['author']}</td></tr><tr><td width=20%>{$lna[133]}</td><td>{$info['time']}</td></tr><tr><td width=20%>{$lna[134]}</td><td>{$info['intro']}</td></tr></table><br><div align=center><b>{$lna[135]}</div>";
	$display_overall.=highlightadminitems('module', 'main');
$display_overall.= <<<eot
<form action="admin.php?go=main_autoaddmodule" method="post">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[136]}
</td>
<td class="sectend">{$lna[137]}</td>
</tr>
</table>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr><td class="sect">
$warn
<input type='hidden' name='newmodfile' value='{$upload_filename}'>
</td>
<tr class='admintitle'><td align='center'><input type=submit value='{$lna[64]}' class='formbutton'> <input type=button onclick='window.location="admin.php";' value='{$lna[138]}' class='formbutton'></td></tr>
</form>
</td></tr></table>
eot;
}

if ($job=='autoaddmodule') {
	acceptrequest('newmodfile');
	$newmodfile=basename($newmodfile);
	$ext=strtolower(strrchr($newmodfile,'.'));
	if ($ext!='.blog' && $ext!='.txt') catcherror($lna[129]);
	$filecontent=readfromfile("{$db_tmpdir}/".$newmodfile);
	eval ($filecontent);
	$maxmodid=$blog->countbyquery("SELECT MAX(`modorder`) FROM `{$db_prefix}mods`");
	$maxmodid+=1;
	$blog->query("INSERT INTO `{$db_prefix}mods` VALUES ('{$info['newitemposition']}', '{$info['name']}', '{$info['intro']}', '{$info['newitemactive']}', '$maxmodid', 'custom')");
	if ($info['newitemactive']==1) {
		recache_mods ();
	}
	mod_append ($info['content']);
	@unlink("{$db_tmpdir}/{$newmodfile}");
	catchsuccess ($finishok2, $backtomodule);
}

if ($job=='ordermodule') {
	acceptrequest('section');
	if (!$section) catcherror ($lna[126]);
	$mod_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}mods` WHERE `position`='{$section}' AND `active`=1 ORDER BY `modorder`");
	for ($i=0; $i<count($mod_array); $i++) {
		$mod_array[$i]['desc']=msubstr($mod_array[$i]['desc'], 0, 15);
		$puttingcates.="<option value='".urlencode($mod_array[$i]['name'])."'>{$mod_array[$i]['name']} ({$mod_array[$i]['desc']})</option>";
	}
	$realname=array('header'=>$lna[71], 'sidebar'=>$lna[72], 'footer'=>$lna[73], 'prebody'=>$lna[1055]);
	$display_overall.=highlightadminitems('module', 'main');
	$urlnew="admin.php?section={$section}&go=main_modorder_";
$display_overall.= <<<eot
<form action="admin.php?go=main_doordermodule" method="post">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[139]}
</td>
<td class="sectend">{$lna[140]}</td>
</tr>
</table>
<table class='tablewidth' align=center cellpadding=14 cellspacing=0>
<tr><td class="sect">
{$lna[90]} <b>{$realname[$section]}</b><br><br>
<select multiple size=8 style="width: 50%;" name="list2" style="width: 120px">
$puttingcates
</select><br>
<input type="button" value="{$lna[141]}" onclick="Moveup(this.form.list2)" name="B3">
<input type="button" value="{$lna[142]}" onclick="Movedown(this.form.list2)" name="B4"><br>
<ul><li>{$lna[143]}</li>
<li>{$lna[145]}</li></ul>
<br>
<div align=center><input type=button onclick="GetOptions(this.form.list2, '$urlnew')" value="{$lna[64]}"> <input type=reset value="{$lna[65]}"></div>
<input type='hidden' name='section' value='{$section}'>
</td>
</tr></table>
</form>
eot;
}

if ($job=='modorder') {
	acceptrequest('section');
	if ($itemid==='' || !$section) catcherror ("{$lna[126]}");
	$array_cates=@explode(':', $itemid);
	$lastcount=count($array_cates);
	for ($i=0; $i<($lastcount-1); $i++) {
		$blog->query("UPDATE `{$db_prefix}mods` SET `modorder`='{$i}' WHERE `name`='{$array_cates[$i]}' AND `position`='{$section}'");
	}
	recache_mods();
	catchsuccess ($finishok2, $backtomodule);
}

if ($job=='langset') {
	$display_overall.=highlightadminitems('langset', 'main');
	$lnc_tmp=$lnc;
	$langname_tmp=$langname;
	$alllanglist=array();

	$handle=opendir("lang/");
	while (false !== ($file=readdir($handle))) {
		if (is_dir("lang/{$file}")) {
			if ($file==$langfront) $alllanglist[]=array('ldir'=>$file, 'lname'=>$langname_tmp['front']);
			elseif (is_file("lang/{$file}/common.php")) {
				@include_once("lang/{$file}/common.php");
				$alllanglist[]=array('ldir'=>$file, 'lname'=>$langname['front']);
			}
		}
	}

	$lnc=$lnc_tmp;
	$langname=$langname_tmp;

	foreach ($alllanglist as $eachlang) {
		$selectbody.="<option value=\"{$eachlang['ldir']}\">{$eachlang['lname']}</option>\n";
	}

	$display_overall.= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
Language/语言/語言
</td>
<td class="sectend">Set blog language</td>
</tr>
</table>
<table align=center class='tablewidth'>
<form action="admin.php?go=main_dolangset" method="post">
<tr>
<td class='sect'>
<table align=center class='tablewidth'>
<tr>
<td class='sect'>
Current Language Pack/当前语言包/當前語言包<br><br>
<b>Front-End/前台/前臺</b> {$langname['front']}<br>
<b>Back-End/后台/後臺</b> {$langname['back']}

<br><br><hr><br>
Set Language Pack Location/更改语言包为/更改語言包為<br><br>
<b>Front-End/前台/前臺</b>  
<br><select name='newlangf'>
$selectbody
</select>
<br><br>
<b>Back-End/后台/後臺</b>  
<br><select name='newlangb'>
$selectbody
</select>
<br>
<br>
<input type='submit' value='OK' class='formbutton'> <input type='reset' value='Cancel' class='formbutton'><br>
</td></tr>
</table>
</td></tr>
</form>
</table>
eot;
}

if ($job=='dolangset') {
	acceptrequest('newlangf,newlangb');
	if (!file_exists("lang/{$newlangf}/common.php")) catcherror("Front-end langauge pack does not exist. 前台语言包不存在。 前臺語言包不存在。");
	if (!file_exists("lang/{$newlangb}/backend.php")) catcherror("Back-end langauge pack does not exist. 后台语言包不存在。 後臺語言包不存在。");
	$newcontent="<?PHP\n\$langfront=\"{$newlangf}\";\n\$langback=\"{$newlangb}\";\n@include_once (\"lang/{$newlangf}/common.php\");";
	writetofile ("data/language.php", $newcontent);
	catchsuccess("Language set has been changed. 语言包设置完成。 語言包設置完成。");
}

if ($job=='refreshadminskinlist' || $job=='selectadminskin') {
	$handle=opendir("admin/theme/");
	if (!$handle) catcherror ("{$lna[155]} admin/theme/ {$lna[156]}<ul><li>{$lna[157]}</li><li>{$lna[158]}</li><li>{$lna[159]}</li></ul>");
	while (false !== ($file=readdir($handle))) {
		if ($file!="." && $file!=".." && is_dir("admin/theme/{$file}")) {
			$out.="\$adminskin[]='{$file}';\n";
		}
	}
	if ($job=='selectadminskin') {
		acceptrequest('targetskin');
		$targetskin=basename($targetskin);
		if ($targetskin) {
			$currentadminskin=$targetskin;
		}
	}
	$sleout="<?PHP\n".$out."\$currentadminskin='{$currentadminskin}';";
	writetofile("data/cache_adminskinlist.php", $sleout);
	header ("Location: admin.php");
}

if ($job=='funclock') {
	if (sizeof($flset)<1) {
		$flset=array('tags'=>0, 'weather'=>0, 'avatar'=>0, 'star'=>0, 'guestbook'=>0, 'modeselectable'=>0);
	}

	$uidesc=array('tags'=>$lnc[288], 'weather'=>$lna[301], 'avatar'=>$lna[881], 'star'=>$lnc[93], 'guestbook'=>$lnc[91], 'modeselectable'=>"{$lnc[183]}/{$lnc[185]}");
	
	$pref_leftchar="200";
	$pref_variable="flset";
	foreach ($flset as $flkey => $flval) {
		addpref("r", "{$flkey}|{$uidesc[$flkey]}|{$lna[512]}|{$lna[511]}");
	}
	$pref_result_show=@implode('', $pref_result);
	$display_overall.=highlightadminitems('funclock', 'main');
$display_overall.= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[1194]}
</td>
<td class="sectend">{$lna[1195]}</td>
</tr>
</table>
<table class='tablewidth' cellpadding=4 cellspacing=1 align=center>
<form action="admin.php?go=main_funclocksave" method="post" id="ajaxForm1">
$pref_result_show
<tr><td colspan=2 class="sect">{$lna[1196]}</td></tr>
</table>
<br>
<div align=center><a name="bottom"></a><input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax(1);"> <input type=reset value="{$lna[65]}" class='formbutton'></div>
</form>
eot;
}


if ($job=='funclocksave') {
	$savetext="<?PHP\nif (!defined('VALIDREQUEST')) die ('Access Denied.');\n";
	$save_config=$_POST['prefconfig'];
	if (count($save_config)<=1) catcherror ($lna[1013]);

	$relatedmods=array('tags'=>'alltags', 'star'=>'starred', 'guestbook'=>'guestbook');
	$tosetinvisiblearray=$tosetvisiblearray=array();

	while (@list ($key, $val) = @each ($save_config)) {
		$savetext.="\$flset['{$key}']='".admin_convert($val)."';\n";
		if ($relatedmods[$key]) {
			if ($val==1) $tosetinvisiblearray[]=$relatedmods[$key];
			if ($val==0) $tosetvisiblearray[]=$relatedmods[$key];
		}
	}
	if ($savetext=='') catcherror ($lna[1013]);
	if (writetofile ("data/functionlock.php", $savetext)) {
		if (sizeof($tosetinvisiblearray)>=1) {
			$allinvisibles=makeaquery($tosetinvisiblearray, "`name`='%s'", 'OR');
			$blog->query("UPDATE `{$db_prefix}mods` SET `active`=0 WHERE {$allinvisibles}");
		}
		if (sizeof($tosetvisiblearray)>=1) {
			$allvisibles=makeaquery($tosetvisiblearray, "`name`='%s'", 'OR');
			$blog->query("UPDATE `{$db_prefix}mods` SET `active`=1 WHERE {$allvisibles}");
		}
		recache_mods();
		catchsuccess ($finishok, "{$lna[1194]}|admin.php?go=main_funclock");
	} else {
		catcherror ("{$lna[66]}"."data/functionlock.php");
	}
}