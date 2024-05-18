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

$skinperpage=20;

//Define some senteces
$finishok=$lna[160];
$finishok2=$lna[751];
$finishok3=$lna[903];
$finishok4=$lna[1098];
$backtoskin="{$lna[27]}|admin.php?go=addon_skin";
$backtoplugin="{$lna[28]}|admin.php?go=addon_plugin";
$backtolangspec="{$lna[1099]}|admin.php?go=addon_langspec";

if (!$job) $job='skin';

if ($job=='skin') {
	$start_id=($page-1)*$skinperpage;
	$end_id=$start_id+$skinperpage-1;
	include_once ("data/cache_skinlist.php");
	include_once ("data/mod_template.php");
	$tablebody="<tr class='sect'><td width=412 align=center class='sect'><img src='template/{$template['dirname']}/{$template['thumbnail']}' alt='' border=0></a></td><td class='visibleitem'><font color=red><b>{$lna[161]}</b> {$template['name']}</font><br><b>{$lna[162]}</b> {$template['author']}<br><b>{$lna[163]}</b> {$template['intro']}<br><br></td></tr>\n";
	$count=-1;
	if (is_array($skinset)) {
		foreach ($skinset as $skin) {
			$count+=1;
			if ($count<$start_id) continue;
			if ($count>$end_id) break;
			@list($skid, $skname, $skauthor, $skintro, $skdir, $skthumbnail)=@explode('|', $skin);
			if ($skid==$template['id']) continue;
			$tablebody.="<tr class='sect'><td width=412 align=center class='sect'><a href=\"javascript: simulateFormSubmit('admin.php?go=addon_setskin_{$skdir}')\" title='{$lna[164]}' class='visibleitem'><img src='template/{$skdir}/{$skthumbnail}' title='{$lna[164]}' alt='' border=0></a></td><td class='visibleitem'><b>{$lna[165]}</b> {$skname}<br><b>{$lna[162]}</b> {$skauthor}<br><b>{$lna[163]}</b> {$skintro}<br><br><a href=\"javascript: simulateFormSubmit('admin.php?go=addon_setskin_{$skdir}')\" title='{$lna[164]}'>{$lna[166]}</a> | <a href=\"javascript: redirectcomfirm('admin.php?go=addon_removeskin_{$skid}');\" title='{$lna[167]}'>{$lna[168]}</a></td></tr>\n";
		}
	}
	$pagebar=gen_page ($page, 5, "admin.php?go=addon_skin", count($skinset), $skinperpage);
	$display_overall.=highlightadminitems('skin', 'addon');
$display_overall_plus= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[169]}
</td>
<td class="sectend">{$lna[170]}</td>
</tr>
</table>
<table align=center class='tablewidth'>
{$tablebody}
<tr><td colspan=2 align=right>{$pagebar}</td></tr>
</table>
<br><br>

<form action="admin.php?go=addon_addskin" method="post" id="ajaxForm1">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[171]}
</td>
<td class="sectend">{$lna[172]}
</td>
</tr>
<tr class='sect'><td colspan=2 align=center>
<br>{$lna[173]} template/<input type=text name=newskindir size=20>/ <input type='button' value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax(1);"><br>
</td></tr>
</table>
</form>
<br><br>
<form action="admin.php?go=addon_scanskin" method="post" id="ajaxForm2">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[748]}
</td>
<td class="sectend">{$lna[749]}
</td>
</tr>
<tr class='sect'><td colspan=2 align=center>
<input type='button' value="{$lna[750]}" class='formbutton' onclick="adminSubmitAjax(2);">
</td></tr>
</table>
</form>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='scanskin') {
	$addline="<?PHP\n";
	$handle=opendir("template/");
	if (!$handle) catcherror ("{$lna[155]} template/ {$lna[156]}<ul><li>{$lna[157]}</li><li>{$lna[158]}</li><li>{$lna[159]}</li></ul>");
	while (false !== ($file=readdir($handle))) {
		if (!empty($file) && $file!="." && $file!=".." && file_exists("template/{$file}/info.php") ) {
			include ("template/{$file}/info.php");
			$addline.="\$skinset[\"{$template['id']}\"]=\"{$template['id']}|".skin_convert($template['name'])."|".skin_convert($template['author'])."|".skin_convert($template['intro'])."|".skin_convert($template['dirname'])."|".skin_convert($template['thumbnail'])."|\";\n";
		}
	}
	include("data/mod_template.php");
	if (!writetofile("data/cache_skinlist.php", $addline)) catcherror($lna[66]."data/cache_skinlist.php");
	else {
		if ($ajax=='on') {
			$fetchURL='admin.php?go=addon_skin';
			catchsuccessandfetch($finishok2, $fetchURL);
		} else catchsuccess ($finishok2, $backtoskin);
	}
}

if ($job=='addskin') {
	include_once ("data/cache_skinlist.php");
	acceptrequest('newskindir');
	$newskindir=trim(basename($newskindir));
	if (!$newskindir || !is_dir("template/{$newskindir}")) catcherror($lna[174]);
	if (!file_exists("template/{$newskindir}/info.php"))  catcherror($lna[174]);
	else include_once("template/{$newskindir}/info.php");
	$tmp=$template['id'];
	if (!$tmp) catcherror($lna[174]);
	if ($skinset[$tmp]) catcherror($lna[175]);
	$addline="\$skinset[\"{$template['id']}\"]=\"{$template['id']}|".skin_convert($template['name'])."|".skin_convert($template['author'])."|".skin_convert($template['intro'])."|".skin_convert($template['dirname'])."|".skin_convert($template['thumbnail'])."|\";\n";
	$oldcontent=readfromfile("data/cache_skinlist.php");
	if (!writetofile("data/cache_skinlist.php", $oldcontent.$addline)) catcherror($lna[66]."data/cache_skinlist.php");
	else {
		if ($ajax=='on') {
			$fetchURL='admin.php?go=addon_skin';
			catchsuccessandfetch($finishok, $fetchURL);
		} else catchsuccess ($finishok, $backtoskin);
	}
}

if ($job=='setskin') {
	if (!$itemid || !file_exists("template/".basename($itemid)."/info.php")) catcherror($lna[176]); 
	$content=readfromfile ("template/".basename($itemid)."/info.php");
	if (!writetofile("data/mod_template.php", $content)) catcherror($lna[66]."data/mod_template.php");
	else {
		if ($ajax=='on') {
			$fetchURL='admin.php?go=addon_skin';
			catchsuccessandfetch($finishok, $fetchURL);
		} else catchsuccess ($finishok, $backtoskin);
	}
}

if ($job=='removeskin') {
	include_once ("data/cache_skinlist.php");
	$itemid=trim(basename($itemid));
	if (!$itemid || !$skinset[$itemid]) catcherror($lna[176]);
	$tmps=@file("data/cache_skinlist.php");
	for ($i=0; $i<count($tmps); $i++) {
		if (strstr($tmps[$i], "=\"{$itemid}|")) {
			$tmps[$i]='';
			break;
		}
	}
	$content=@implode('', $tmps);
	if (!writetofile("data/cache_skinlist.php", $content)) catcherror($lna[66]."data/cache_skinlist.php");
	else {
		if ($ajax=='on') {
			$fetchURL='admin.php?go=addon_skin';
			catchsuccessandfetch($finishok, $fetchURL);
		} else catchsuccess ($finishok, $backtoskin);
	}
}

if ($job=='plugin') {
	$formbody="<tr class='admintitle' ><td width=30 align=center>{$lna[904]}</td><td align=center width='80%'>{$lna[905]}</td><td align=center>{$lna[0]}</td><td align=center>{$lna[78]}</td></tr>\n";
	$mod_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}plugins` ORDER BY `plid` ASC");
	for ($i=0; $i<count($mod_array); $i++) {
		if ($mod_array[$i]['active']==1) {
			$chex=" checked";
			$addwords=" {$lna[906]}";
		}
		else {
			$chex="";
			$addwords=" <font color=red>{$lna[907]}</font>";
		}
		$linkdel="javascript: redirectcomfirm (\"admin.php?go=addon_plugindel_{$mod_array[$i]['plid']}\");";
		$linkadmin=($mod_array[$i]['pladmin']=='0') ? "javascript: alert(\"{$lna[908]}\");" : "admin.php?act={$mod_array[$i]['plname']}";
		$class_distinct=($i%2==0) ? 'visibleitem' : 'hiddenitem';
		$formbody.="<tr class='$class_distinct'><td width=30 align=center><input type=checkbox name='selid[]' value='{$mod_array[$i]['plid']}'{$chex}></td><td width='80%'><b>{$mod_array[$i]['plname']}</b>$addwords<br>{$mod_array[$i]['plintro']}</td><td align=center width=30><a href='$linkadmin'><img src='admin/theme/{$themename}/edit.gif' alt='{$lna[0]}' title='{$lna[0]}' border='0'></a></td><td align=center width=30><a href='$linkdel'><img src='admin/theme/{$themename}/del.gif' alt='{$lna[78]}' title='{$lna[78]}' border='0'></a></td></tr>\n";
	}
	if (count($mod_array)==0) $formbody.="<tr class='sect' align=center><td colspan=4> <br> <br>{$lna[909]}<br><br></td></tr>";
	else {
		$formbody.="<tr class='sect' align=center><td colspan=4><input type=hidden name=section value='$section'><input type=button value=\"{$lna[82]}\" class='formbutton' onclick=\"adminSubmitAjax(1);\"> <input type=reset value=\"{$lna[65]}\" class='formbutton'> </td></tr>\n";
		$formbody.="<tr class='sect'><td colspan=4>{$lna[910]}</td></tr>\n";
	}

	for ($i=0; $i<sizeof($mod_array); $i++) {
		$tmp_id=$mod_array[$i]['plname'];
		$puttingdetail.="<option value='{$tmp_id}'>{$mod_array[$i]['plname']}</option>";
	}

	$urlnew="admin.php?go=addon_pluginsort_";
	$display_overall.=highlightadminitems('plugin', 'addon');
$display_overall_plus= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[28]}
</td>
<td class="sectend">{$lna[911]}</td>
</tr>
</table>
<table align=center class='tablewidth' cellpadding=3 cellspacing=1>
<tr><td class='sect'>

<form action="admin.php?go=addon_pluginsave" method="post" id="ajaxForm1">
<table align=center width="100%" cellpadding=3 cellspacing=1>
$formbody
</table>
</form>

</td>
</tr></table>
<br><br>
<form action="admin.php?go=addon_addplugin" method="post">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[912]}
</td>
<td class="sectend">{$lna[913]}
</td>
</tr>
<tr class='sect'><td colspan=2 align=center>
<div align=left><b><font color=red>{$lna[923]}</font></b></div>
<br>{$lna[914]} plugin/<input type=text name=newplugindir size=20>/ <input type='submit' value='{$lna[64]}' class='formbutton'><br><br>
</td></tr>
</table>
</form>
<br><br>
<form action="admin.php?go=addon_pluginsort" method="post">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[1033]}
</td>
<td class="sectend">{$lna[1034]}
</td>
</tr>
<tr class='sect'><td colspan=2 align=center>
<select multiple size=15 name="list2" style="width: 50%;">
$puttingdetail
</select><br>
<input type="button" value="{$lna[141]}" onclick="Moveup(this.form.list2)" name="B3" class='formbutton'>
<input type="button" value="{$lna[142]}" onclick="Movedown(this.form.list2)" name="B4" class='formbutton'>
<input type=button onclick="GetOptions(this.form.list2, '$urlnew')" value="{$lna[64]}" class='formbutton'>
{$lna[143]}
</td></tr>
</table>
</form>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='addplugin') {
	acceptrequest('newplugindir');
	$newplugindir=basename($newplugindir);
	$newplugindir=trim(basename($newplugindir));
	if (!$newplugindir || !is_file("plugin/{$newplugindir}/setup.php")) catcherror($lna[915]);
	else include_once("plugin/{$newplugindir}/setup.php");
	$try=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}plugins` WHERE `plname`='{$info['name']}'");
	if (is_array($try)) catcherror($lna[916]);
	if ($info['blogversion']>$blogversion)  catcherror($lna[917]);
	$warn.="<b>{$lna[918]}</b><br><table width=95% align=center><tr><td width=20%>{$lna[919]}</td><td>{$info['name']}</td></tr><tr><td width=20%>{$lna[132]}</td><td>{$info['author']}  &nbsp; [<a href=\"{$info['authorurl']}\" target=_blank>{$lna[920]}</a>]</td></tr><tr><td width=20%>{$lna[921]}</td><td>{$info['version']}</td></tr><tr><td width=20%>{$lna[134]}</td><td>{$info['intro']}</td></tr></table><br><input type='checkbox' name='activate' value='1' checked>{$lna[922]}";
	$display_overall.=highlightadminitems('plugin', 'addon');
$display_overall.= <<<eot
<form action="admin.php?go=addon_autoaddplugin" method="post">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[912]}
</td>
<td class="sectend">{$lna[913]}</td>
</tr>
</table>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr><td class="sect">
$warn
<br>
<input type='hidden' name='newplugindir' value='{$newplugindir}'>
</td>
<tr class='admintitle'><td align='center'><input type=submit value='{$lna[64]}' class='formbutton'> <input type=button onclick='window.location="admin.php?go=addon_plugin";' value='{$lna[138]}' class='formbutton'></td></tr>
</form>
</td></tr></table>
eot;
}

if ($job=='autoaddplugin') {
	acceptrequest('newplugindir,activate');
	$newplugindir=basename($newplugindir);
	$activate=floor($activate);
	$newplugindir=trim(basename($newplugindir));
	if (!$newplugindir || !is_file("plugin/{$newplugindir}/setup.php")) catcherror($lna[915]);
	else include_once("plugin/{$newplugindir}/setup.php");
	if (is_file("plugin/{$newplugindir}/admin.php")) $pladmin=1;
	else $pladmin=0;
	$maxplid=$blog->countbyquery("SELECT MAX(plid) FROM `{$db_prefix}plugins`");
	$plid=floor($maxplid)+1;
	$blog->query("INSERT INTO `{$db_prefix}plugins` VALUES ({$plid}, '{$info['name']}', '{$info['author']}', '{$info['intro']}', '{$info['version']}', '{$info['authorurl']}', '{$info['blogversion']}', {$activate}, {$pladmin}, '{$info['register']}')");

	if ($activate==1 && $info['register']) recache_plugins();
	if (is_file("plugin/{$newplugindir}/install.php")) include_once("plugin/{$newplugindir}/install.php");
	catchsuccess ($finishok3, $backtoplugin);
}

if ($job=='pluginsave') {
	acceptrequest('selid');
	$count_selid=(is_array($selid)) ? (count($selid)) : 0;
	if ($count_selid==0) $blog->query("UPDATE `{$db_prefix}plugins` SET `active`=0");
	else {
		for ($i=0; $i<$count_selid; $i++) {
			$selid_array[$i]="'{$selid[$i]}'";
		}
		$in_selid=@implode(',', $selid_array);
		$blog->query("UPDATE `{$db_prefix}plugins` SET `active`=1 WHERE `plid` in({$in_selid})");
		$blog->query("UPDATE `{$db_prefix}plugins` SET `active`=0 WHERE `plid` not in({$in_selid})");
	}
	recache_plugins ();
	if ($ajax=='on') {
		$fetchURL='admin.php?go=addon_plugin';
		catchsuccessandfetch($finishok3, $fetchURL);
	}
	else catchsuccess ($finishok3, $backtoplugin);
}

if ($job=='plugindel') {
	if ($itemid==='') catcherror ($lna[241]);
	$try=$blog->getbyquery("SELECT * FROM `{$db_prefix}plugins` WHERE `plid`='$itemid'");
	if (is_file("plugin/{$try['plname']}/uninstall.php")) include_once("plugin/{$try['plname']}/uninstall.php");
	$blog->query("DELETE FROM `{$db_prefix}plugins` WHERE `plid`=$itemid");
	recache_plugins ();
	if ($ajax=='on') {
		$fetchURL='admin.php?go=addon_plugin';
		catchsuccessandfetch($finishok3, $fetchURL);
	}
	else catchsuccess ($finishok3, $backtoplugin);
}

if ($job=='pluginsort') {
	if ($itemid=='') 	catcherror ($lna[205]);
	$array_plugins=@explode(':', $itemid);
	$lastcount=count($array_plugins);
	for ($i=0; $i<($lastcount-1); $i++) {
		$blog->query("UPDATE `{$db_prefix}plugins` SET `plid`='{$i}' WHERE `plname`='{$array_plugins[$i]}'");
	}
	recache_plugins();
	catchsuccess ($finishok3, $backtodetail);
}


if ($job=='langspec') {
	$langstext=$langexist='';
	$langmodulejs="var jslnc = new Array ();\n";
	foreach ($lnc as $key => $val) {
		$langmodulejs.="jslnc[{$key}]='".str_replace("'", "\\'", $val)."';\n";
		$langstext.="{$key} - ".htmlspecialchars($val)."<br>\n";
	}
	$lni=0;
	if (is_file("data/langspecoverwrite.php")) {
		include_once("data/langspecoverwrite.php");
		if (is_array($lncoverwrite)) {
			foreach ($lncoverwrite as $key=>$val) {
				$val=htmlspecialchars($val);
				$langexist.="<div id='editline{$lni}'><table width=100% cellpadding=0 cellspacing=0><tr class=\"visibleitem\"><td width=15% align=center><input type=text size=4 maxlength=4 name='newlnum[]' id='newlnum{$lni}' onblur='changeldarea({$lni});' value='{$key}'></td><td width=40%><span id='ldarea{$lni}'>{$lnc[$key]}</span></td><td width=40%><input type=text size=52 name='newldesc[]' id='newldesc{$lni}' value=\"{$val}\"></td><td width=5% align=center><input type=button onclick='deleteeditline({$lni});' value='{$lna[1149]}' class='formbutton'></td></tr></table></div>";
				$lni+=1;
			}
		}
	}
	$lnni=$lni+1;
	$display_overall.=highlightadminitems('langspec', 'addon');
$display_overall.= <<<eot
<script type="text/javascript">
$langmodulejs
var newlcount={$lnni};
var getnewlnum;
var getnewldesc;
var getldarea;
var skiplcount = new Array ();
function addneweditline() {
	var addnewarea=document.getElementById('addnew');
	var addnewareato='';
	for (var i={$lnni}; i<=newlcount; i++) {
		if (skiplcount[i]!=1) {
			getnewlnum=(document.getElementById('newlnum'+i)) ? document.getElementById('newlnum'+i).value : '';
			getnewldesc=(document.getElementById('newldesc'+i)) ? document.getElementById('newldesc'+i).value : '';
			getldarea=(document.getElementById('ldarea'+i)) ? document.getElementById('ldarea'+i).innerHTML : '&nbsp;';
			addnewareato=addnewareato+"<div id='editline"+i+"'><table width=100% cellpadding=0 cellspacing=0><tr class=visibleitem><td width=15% align=center><input type=text size=4 maxlength=4 name='newlnum[]' id='newlnum"+i+"' onblur='changeldarea("+i+");' value='"+getnewlnum+"'></td><td width=40%><span id='ldarea"+i+"'>"+getldarea+"</span></td><td width=40%><input type=text size=52 name='newldesc[]'  id='newldesc"+i+"' value='"+getnewldesc+"'></td><td width=5% align=center><input type=button onclick='deleteeditline("+i+");' value='{$lna[1149]}' class='formbutton'></td></tr></table></div>";
		}
	}
	addnewarea.innerHTML=addnewareato;
	newlcount=newlcount+1;
}

function deleteeditline(lid) {
	var newarea=document.getElementById('editline'+lid);
	if (newarea) {
		newarea.innerHTML='';
		newarea.style.display='none';
	}
	skiplcount[lid]=1;
}

function changeldarea(lid) {
	var newarea=document.getElementById('ldarea'+lid);
	if (newarea) {
		var areaval=document.getElementById('newlnum'+lid).value;
		if (areaval!=null && areaval!='' && jslnc[areaval])
		newarea.innerHTML=jslnc[areaval];
	}
}

function saveldata() {
	var getnewlnums=new Array ();
	var getnewldescs=new Array ();
	for (var i=0; i<newlcount; i++) {
		if (skiplcount[i]!=1) {
			getnewlnums[i]=(document.getElementById('newlnum'+i)) ? document.getElementById('newlnum'+i).value.replace(/,/g, '&#44;') : '';
			getnewldescs[i]=(document.getElementById('newldesc'+i)) ? document.getElementById('newldesc'+i).value.replace(/,/g, '&#44;') : '';
		}
	}
	document.getElementById('newlnums').value=getnewlnums;
	document.getElementById('newldescs').value=getnewldescs;
	document.getElementById('ldata').submit();
}

function sresetldata() {
	if(confirm("{$lna[1100]}")){
		window.location=location;
	}
	else {
		return;
	}
}

</script>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<form action="admin.php?go=addon_savelangspec" method="post" id="ldata">
<td width=160 class="sectstart">
{$lna[1101]}
</td>
<td class="sectend">{$lna[1102]}</td>
</tr>
</td>
</tr>
</table>

<table width=100% cellpadding=0 cellspacing=0><tr class="admintitle"><td width=15% align=center>{$lna[1103]}</td><td width=40%>{$lna[1104]}</td><td width=40%>{$lna[1105]}</td><td width=5% align=center>&nbsp;</td></tr></table>
$langexist
<div id='editline{$lni}'><table width=100% cellpadding=0 cellspacing=0><tr class="visibleitem"><td width=15% align=center><input type=text size=4 maxlength=4 name='newlnum[]' id='newlnum{$lni}' onblur='changeldarea({$lni});'></td><td width=40%><span id='ldarea{$lni}'>&nbsp;</span></td><td width=40%><input type=text size=52 name='newldesc[]' id='newldesc{$lni}'></td><td width=5% align=center><input type=button onclick='deleteeditline({$lni});' value='{$lna[1149]}' class='formbutton'></td></tr></table></div>
<div id="addnew"></div>
<input type=hidden name=newlnums id=newlnums>
<input type=hidden name=newldescs id=newldescs>
<div align=center><br><input type=button value="{$lna[64]}" class='formbutton' onclick="saveldata();"> <input type=button onclick='addneweditline();' value="{$lna[1150]}" class='formbutton'> <input type=button onclick='sresetldata();' value="{$lna[65]}" class='formbutton'></div>
</form>

<br><br><br>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr><td>
<b>{$lna[1106]}</b>
<div style="width:100%; border: 1px solid #ccc; height: 210px; overflow: auto;">{$langstext}</div>
<br><br>
{$lna[1107]}
</td></tr></table>
eot;
}

if ($job=='savelangspec') {
	acceptrequest('newlnums,newldescs');
	if ($newlnums=='' || $newldescs=='') catcherror($lna[241]);
	$savelnum=@explode(',', $newlnums);
	$saveldesc=@explode(',', $newldescs);
	$savedata=$savedata2="<?php\n";
	for ($i=0; $i<count($savelnum); $i++) {
		if ($savelnum[$i]=='') continue;
		$savedata.="\$lnc[{$savelnum[$i]}]='".admin_convert($saveldesc[$i])."';\n";
		$savedata2.="\$lncoverwrite[{$savelnum[$i]}]='".admin_convert($saveldesc[$i])."';\n";
	}
	if (!writetofile ("data/langspec.php", $savedata)) {
		catcherror ($lna[66]."data/langspec.php");
	}
	if (!writetofile ("data/langspecoverwrite.php", $savedata2)) {
		catcherror ($lna[66]."data/langspecoverwrite.php");
	}
	catchsuccess ($finishok4, $backtolangspec);
}

function skin_convert($str) {
	$str=str_replace("\r", '', $str);
	$str=str_replace("\n", '', $str);
	$str=addslashes($str);
	$str=str_replace('|', '', $str);
	return $str;
}

function add_module ($filename) {
	global $activate, $newplugindir, $blog, $db_prefix;
	if (is_file("plugin/{$newplugindir}/{$filename}")) {
		$filecontent=readfromfile("plugin/{$newplugindir}/{$filename}");
		eval ($filecontent);
		$maxmodid=$blog->countbyquery("SELECT MAX(`modorder`) FROM `{$db_prefix}mods`");
		$maxmodid+=1;
		$blog->query("INSERT INTO `{$db_prefix}mods` VALUES ('{$info['newitemposition']}', '{$info['name']}', '{$info['intro']}', '{$info['newitemactive']}', '$maxmodid', 'custom')");
		if ($activate==1) {
			recache_mods ();
		}
		mod_append ($info['content']);
	}
}

function remove_module ($modname) {
	global $blog, $db_prefix;
	$blog->query("DELETE FROM `{$db_prefix}mods` WHERE `name`='$modname'");
	mod_replace ($modname , '');
	recache_mods();
}