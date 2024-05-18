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
$linkgp=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}linkgroup` ORDER BY `linkgporder`");
for ($i=0; $i<sizeof($linkgp); $i++) {
	$tmp_id=$linkgp[$i]['linkgpid'];
	if ($linkgp[$i]['linkgppt']==0) $addclass='hiddenitem';
	else $addclass='visibleitem';
	$puttinggp.="<option class='{$addclass}' value='{$tmp_id}'>{$linkgp[$i]['linkgpname']}</option>";
	$linkgps[$tmp_id]=array('linkgpid'=>$tmp_id, 'linkgpname'=>$linkgp[$i]['linkgpname'], 'linkgppt'=>$linkgp[$i]['linkgppt'], 'linkgporder'=>$linkgp[$i]['linkgporder']);
}

//Define some senteces
$finishok=$lna[214];
$backtodefault="{$lna[18]}|admin.php?go=link_default";
$backtodetail="{$lna[19]}|admin.php?go=link_detail";
$backtoaddnew="{$lna[20]}|admin.php?go=link_add";
$backtopending="{$lna[21]}|admin.php?go=link_pending";


if ($job=='' || $job=="default") {
	$urlnew="admin.php?go=link_ordergp_";
	$puttinggp2=$puttinggp."<option value='removeall'>{$lna[215]}</option>";
	$display_overall.=highlightadminitems('default', 'link');
$display_overall_plus=<<<eot
<form action="admin.php?go=link_newgp" method="post" id="ajaxForm1">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[216]}
</td>
<td class="sectend">{$lna[217]}</td>
</tr>
<tr>
<td colspan=2  class="sect">
<table class='tablewidth' align=center>
<tr>
<td width=50>{$lna[182]}</td>
<td><input type="text" name="newlinkgpname" value="" size="30"></td>
<td>{$lna[218]} </td>
<td><select name="newlinkgppt"><option value="1">{$lna[219]}</option><option value="0">{$lna[220]}</option></select></td></tr>
<tr>
<td colspan=4 align=center>
	{$lna[221]}
</td></tr></td></tr>
</table></td></tr>
<tr>
<td colspan=4 align=center class="sectbar">
<input type=button value="{$lna[64]}" class='formbutton' onclick="clearAllRadios();adminSubmitAjax(1);"> <input type=reset value="{$lna[65]}" class='formbutton'>
</td></tr>
</table>
</form>
<br>
<br>

<form action="" method="post" >
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[222]}
</td>
<td class="sectend">{$lna[223]}</td>
</tr>
<tr>
<td colspan=2  class="sect">
<select multiple size=8 style="width: 50%;" name="list2" style="width: 120px">
$puttinggp
</select><br>
<input type="button" value="{$lna[141]}" onclick="Moveup(this.form.list2)" name="B3" class='formbutton'>
<input type="button" value="{$lna[142]}" onclick="Movedown(this.form.list2)" name="B4" class='formbutton'>
<input type=button onclick="GetOptions(this.form.list2, '$urlnew')"  class='formbutton' value="{$lna[64]}">
{$lna[143]}
</td>
</tr>
</table>
</form>
<br>
<br>
<form action="admin.php" method="post" id="ajaxForm2">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[224]}
</td>
<td class="sectend">{$lna[225]}</td>
</tr>
<tr>
<td colspan=2  class="sect">
<table class='tablewidth' align=center>
<tr>
<td width=150>{$lna[226]}
<select name="sourcelinkgp">
$puttinggp
</select><input type=hidden name=comm value='0'>
</td>
<td width=50%>{$lna[200]}<input type=radio name='go' value='link_editgp' onclick="swapdiv(0);">{$lna[77]} <input type=radio name='go' value='link_order' onclick="swapdiv(0);">{$lna[227]} <input type=radio  name='go' value='link_delgp' onclick="swapdiv(1);">{$lna[78]} <input type=radio  name='go' value='link_combinegp' onclick="swapdiv(2);">{$lna[201]}</td></tr>
<tr>
<td width=50%>&nbsp;</td>
<td width=50%>
<div id="targetdiv3" style="display:none"><input type=submit value="{$lna[64]}" class='formbutton'></div>
<div id="targetdiv" style="display:none">{$lna[228]}<select name="targetlinkgp2">
$puttinggp2
</select>
<input type=button value="{$lna[64]}" onclick='if(confirm("{$lna[179]}")){adminSubmitAjax(2);}' class='formbutton'>
</div>
<div id="targetdiv2" style="display:none">{$lna[204]}
<select name="targetlinkgp">
$puttinggp
</select>
<input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax(2);">
</div>
</td></tr></td></tr>
</table>
<tr>
<td colspan=4 align=center class="sectbar">
</td></tr>
</table>
</form>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}


if ($job=='ordergp') {
	if ($itemid=='') {
		$cancel="{$lna[205]}";
	}
	catcherror ($cancel);
	$array_linkgp=@explode(':', $itemid);
	$lastcount=count($array_linkgp);
	for ($i=0; $i<($lastcount-1); $i++) {
		$blog->query("UPDATE `{$db_prefix}linkgroup` SET `linkgporder`='{$i}' WHERE `linkgpid`='{$array_linkgp[$i]}'");
	}
	recache_links();
	catchsuccess($finishok, $backtodefault);
}

if ($job=='editgp' ) {
	acceptrequest('sourcelinkgp');
	if ($linkgps[$sourcelinkgp]['linkgppt']==1) {
		$sel1='selected';
	} else {
		$sel2='selected';
	}
	$display_overall.=highlightadminitems('default', 'link');
$display_overall.= <<<eot
<form action="admin.php" method="post" id='ajaxForm1'>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[224]}
</td>
<td class="sectend">{$lna[225]}</td>
</tr>
<tr>
<td colspan=2  class="sect">

<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160>{$lna[182]}</td>
<td><input type="text" name="newlinkgpname" value="{$linkgps[$sourcelinkgp]['linkgpname']}"></td>
</tr>
<tr>
<td width=160>{$lna[218]}</td>
<td><select name="newlinkgppt"><option value="1" {$sel1}>{$lna[219]}</option><option value="0" 	{$sel2}>{$lna[220]}</option></select></td>
</tr>
</table>

</td></tr>
<tr>
<td colspan=4 align=center class="sectbar">
<input type=hidden name=go value="link_savegp_{$sourcelinkgp}">
<input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax(1);"> <input type=reset value="{$lna[65]}" class='formbutton'>
</td></tr>
</table>
</form>
eot;
}


if ($job=='newgp' || $job=='savegp') {
	acceptrequest('newlinkgpname,newlinkgppt');
	if ($newlinkgpname=='') {
		$cancel="No Empty item allowed.";
	}
	catcherror ($cancel);
	$newlinkgpname=safe_convert(stripslashes($newlinkgpname));
	if ($job=='newgp') {
		$new_linkgp_id=$maxrecord['maxlinkgpid']+1;
		$result=$blog->query("INSERT INTO `{$db_prefix}linkgroup` VALUES ('{$new_linkgp_id}', '{$newlinkgpname}', '{$newlinkgppt}', '{$new_linkgp_id}',  '', '')");
		$result=$blog->query("UPDATE `{$db_prefix}maxrec` SET maxlinkgpid='{$new_linkgp_id}'");
	} else {
		$result=$blog->query("UPDATE `{$db_prefix}linkgroup` SET `linkgpname`='{$newlinkgpname}', `linkgppt`='{$newlinkgppt}' WHERE `linkgpid`='{$itemid}'");
	}
	recache_links ();
	if ($ajax=='on') {
		$fetchURL='admin.php?go=link_default';
		catchsuccessandfetch($finishok, $fetchURL);
	}
	else catchsuccess($finishok, $backtodefault);
}


if ($job=='delgp' || $job=='combinegp') {
	acceptrequest('sourcelinkgp,targetlinkgp,targetlinkgp2');
	if ($job=='delgp') $targetlinkgp=$targetlinkgp2;
	if ($sourcelinkgp=='' || $targetlinkgp=='') {
		$cancel=$lna[205];
	}
	if ($sourcelinkgp==$targetlinkgp) {
		$cancel=$lna[229];
	}
	catcherror ($cancel);
	if ($targetlinkgp!='removeall') {
		$blog->query("UPDATE `{$db_prefix}links` SET `linkgptoid`='{$targetlinkgp}' WHERE `linkgptoid`='{$sourcelinkgp}'");
	} else {
		$blog->query("DELETE FROM `{$db_prefix}links` WHERE `linkgptoid`='{$sourcelinkgp}'");
	}
	if ($job=='delgp') {
		$blog->query("DELETE FROM `{$db_prefix}linkgroup` WHERE `linkgpid`='{$sourcelinkgp}'");
	}
	recache_links ();
	if ($ajax=='on') {
		$fetchURL='admin.php?go=link_default';
		catchsuccessandfetch($finishok, $fetchURL);
	}
	else catchsuccess($finishok, $backtodefault);
}

if ($job=='add' || $job=='modify' || $job=='approve') {
	if ($job=='modify') {
		if ($itemid=='') $cancel=$lna[230];
		else {
			$linkvalue=$blog->getbyquery("SELECT * FROM `{$db_prefix}links` WHERE `linkid`='{$itemid}'");
			if (!is_array($linkvalue) || !($linkvalue)) $cancel=$lna[230];
		}
		catcherror ($cancel);
		$tmp_gp=$linkvalue['linkgptoid'];
		$puttinggp3=$puttinggp."<option class='currentitem' value='{$tmp_gp}' selected>{$lna[231]}{$linkgps[$tmp_gp]['linkgpname']}";
		$sel1=($linkvalue['isdisplay']==1) ? 'selected' : '';
		$sel2=($linkvalue['isdisplay']==1) ? '' : 'selected';
		$addhidden="<input type='hidden' name='tomodify' value='1'/><input type='hidden' name='linkid' value='{$itemid}'/>";
	} else {
		$puttinggp3=$puttinggp;
	}
	if ($job=='approve') {
		$filename="data/cache_applylinks.php";
		$wlink=@file($filename);
		for ($i=0; $i<count($wlink); $i++) {
			$aatmp=@explode('<|>', $wlink[$i]);
			if ($aatmp[1]==$itemid) {
				@list($unuse, $unuse, $linkvalue['linkname'], $linkvalue['linkurl'], $linkvalue['linklogo'], $linkvalue['linkdesc'])=$aatmp;
				break;
			}
		}
		$addhidden="<input type='hidden' name='alsodel' value='{$itemid}'/>";
	}
	$display_overall.=highlightadminitems('add', 'link');
$display_overall.= <<<eot
<form action="admin.php" method="post" id="ajaxForm1">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[19]}
</td>
<td class="sectend">{$lna[232]}</td>
</tr>
<tr>
<td colspan=2  class="sect">

<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160>{$lna[233]}</td>
<td><input type="text" name="newlinkname" value="{$linkvalue['linkname']}"></td>
</tr>
<tr>
<td width=160>{$lna[234]}</td>
<td><input type="text" name="newlinkurl" value="{$linkvalue['linkurl']}"></td>
</tr>
<tr>
<td width=160>{$lna[235]}</td>
<td><input type="text" name="newlinklogo" value="{$linkvalue['linklogo']}"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$lna[236]}</td>
</tr>
<tr>
<td width=160>{$lna[183]}</td>
<td><input type="text" name="newlinkdesc" value="{$linkvalue['linkdesc']}"></td>
</tr>
<tr>
<td width=160>{$lna[237]}</td>
<td><select name="newlinkgptoid">	{$puttinggp3}</select></td>
</tr>
<tr>
<td width=160>{$lna[238]}</td>
<td><select name="newisdisplay">
	<option value='1' $sel1>{$lna[239]}</option> <option value='0' $sel2>{$lna[240]}</option> 
	</select>
<input type=hidden name=go value="link_save">{$addhidden}</td>
</tr>
</table>

</td>
</tr>
<tr><td colspan=4 align=center class="sectbar">
<input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax(1);"> <input type=reset value="{$lna[65]}" class='formbutton'>
</td>
</tr></table>
</form>
eot;
}

if ($job=='save') {
	acceptrequest('newlinkname,newlinkurl,newlinklogo,newlinkdesc,newlinkgptoid,newisdisplay,linkid,tomodify,alsodel');
	if ($newlinkname=='' || $newlinkurl=='' || $newlinkgptoid=='') $cancel=$lna[241];
	if ($tomodify=='1' && $linkid=='') $cancel=$lna[241];
	catcherror($cancel);
	$newlinkname=safe_convert(stripslashes($newlinkname));
	$newlinkurl=safe_convert(stripslashes($newlinkurl));
	$newlinklogo=safe_convert(stripslashes($newlinklogo));
	$newlinkdesc=safe_convert(stripslashes($newlinkdesc));
	if ($mbcon['anticorrupturl']==1) {
		$newlinkurl=urlconvert($newlinkurl);
		$newlinklogo=urlconvert($newlinklogo);
	}
	if ($tomodify) {
		$blog->query("UPDATE `{$db_prefix}links` SET `linkname`='{$newlinkname}', `linkurl`='{$newlinkurl}', `linklogo`='{$newlinklogo}', `linkdesc`='{$newlinkdesc}', `linkgptoid`='{$newlinkgptoid}', `isdisplay`='{$newisdisplay}' WHERE `linkid`='{$linkid}'");
		$return_display='detail';
		$fetchURL='admin.php?go=link_detail';
	} else {
		$new_link_id=$maxrecord['maxlinkid']+1;
		$blog->query("INSERT INTO `{$db_prefix}links` VALUES ($new_link_id, '{$newlinkname}', '{$newlinkurl}', '{$newlinklogo}', '{$newlinkdesc}', '{$newlinkgptoid}', $new_link_id, '{$newisdisplay}', '', '')");
		$blog->query("UPDATE `{$db_prefix}maxrec` SET `maxlinkid`='{$new_link_id}'");
		$return_display='add';
		$fetchURL='admin.php?go=link_order&sourcelinkgp={$new_link_id}';
	}
	if (!empty($alsodel)) {
		$filename="data/cache_applylinks.php";
		if (file_exists($filename)) {
			$wlink=@file($filename);
			for ($i=0; $i<count($wlink); $i++) {
				$aatmp=@explode('<|>', $wlink[$i]);
				if ($aatmp[1]==$alsodel) {
					$wlink[$i]='';
					break;
				}
			}
			$allnow=@implode('', $wlink);
			if ($allnow=='') @unlink($filename);
			else writetofile ($filename, $allnow);
		}
		$fetchURL='admin.php?go=link_pending';
	}
	recache_links ();
	if ($ajax=='on') {
		catchsuccessandfetch($finishok, $fetchURL);
	}
	else {
		if (!empty($alsodel)) {
			catchsuccess ($finishok, $backtopending);
		}
		else {
			catchsuccess ($finishok, array($backtodetail , $backtoaddnew));
		}
	}
}

if ($job=='detail') {
	acceptrequest('linkgptoid');
	$queryplus=($linkgptoid=="") ? '' : "WHERE `linkgptoid`='{$linkgptoid}'";
	$start_id=($page-1)*$adminitemperpage;

	$detail_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}links` {$queryplus}  ORDER BY `linkorder` LIMIT $start_id, $adminitemperpage");
	for ($i=0; $i<count($detail_array); $i++) {
		$imgsign=(!empty($detail_array[$i]['linklogo'])) ? "<img src='{$detail_array[$i]['linklogo']}' alt='' title='Logo'>" : '&nbsp;';
		$tmp_gp=$detail_array[$i]['linkgptoid'];
		if ($linkgps[$tmp_gp]['linkgppt']==0 || $detail_array[$i]['isdisplay']==0) $addclass='hiddenitem';
		else $addclass='visibleitem';
		$hiddensign=($detail_array[$i]['isdisplay']==1) ? "<img src='admin/theme/{$themename}/yes.gif' alt='' title='Yes'>" : "<img src='admin/theme/{$themename}/no.gif' alt='' title='No'>";
		$tablebody.="<tr class='$addclass'><td align='center'><input type='checkbox' name='selid[]' id='selid[]' value='{$detail_array[$i]['linkid']}'></td><td align='center'>{$imgsign}</td><td>{$detail_array[$i]['linkname']}</td><td><a href='{$detail_array[$i]['linkurl']}' target='_blank'>{$detail_array[$i]['linkurl']}</a></td><td align='center'>{$linkgps[$tmp_gp]['linkgpname']}</td><td align='center'>{$hiddensign}</td><td align='center'><a href='admin.php?go=link_modify_{$detail_array[$i]['linkid']}'><img src='admin/theme/{$themename}/edit.gif' alt='{$lna[77]}' title='{$lna[77]}' border='0'></a></td></tr>";
	}
	$numenries=$blog->countbyquery("SELECT COUNT(*) FROM `{$db_prefix}links` {$queryplus}  ORDER BY `linkorder`");
	$pagebar=gen_page ($page, 5, "admin.php?go=link_detail&linkgptoid={$linkgptoid}", $numenries, $adminitemperpage);
	$display_overall.=highlightadminitems('detail', 'link');
$display_overall_plus= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[5]}
</td>
<td class="sectend">{$lna[242]}</td>
</tr>
</table>

<form action="admin.php?go=link_detail" method="post">
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr><td colspan=7>
<select name="linkgptoid"><option value=''>{$lna[243]}</option>$puttinggp</select> <input type=submit value="{$lna[244]}" class='formbutton'></td></tr>
</table>
</form>

<form action="admin.php?go=link_batch" method="post" id='f_s' name='f_s'>
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr><td colspan=7 height=10></td></tr>
<tr align=center class="admintitle"><td width=35>{$lna[245]}</td><td width=95>Logo</td><td width=220>{$lna[233]}</td><td width=250>{$lna[234]}</td><td width=80>{$lna[237]}</td><td width=35>{$lna[246]}</td><td width=35>{$lna[77]}</td></tr>
{$tablebody}
<tr><td colspan=7><a href="#unexist" onclick="checkallbox('f_s', 'checked');">{$lna[247]}</a> | <a href="#unexist" onclick="checkallbox('f_s', '');">{$lna[248]}</a></td></tr>
<tr><td colspan=7 height=20></td></tr>
<tr class="adminoption"><td colspan=7>{$lna[249]} <input type=radio name=opt value='del'>{$lna[78]} <input type=radio name=opt value='move'>{$lna[250]}<select name="newlinkgptoid">$puttinggp</select>  <input type=radio name=opt value='ppt'>{$lna[238]}<select name="newproperty"><option value=1>{$lna[239]}</option><option value=0>{$lna[240]}</option></select>  <input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax('f_s');"><br><br>
$pagebar
</td></tr>
</table>
</form>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='batch') {
	acceptrequest('opt,newlinkgptoid,newproperty,selid');
	if (!$opt) $cancel=$lna[251];
	if (!$selid) $cancel=$lna[205];
	catcherror($cancel);
	if ($opt=='del') $queryact="DELETE FROM `{$db_prefix}links` WHERE `linkid`='::'";
	elseif ($opt=='move') $queryact="UPDATE `{$db_prefix}links` SET `linkgptoid`='{$newlinkgptoid}' WHERE `linkid`='::'";
	else $queryact="UPDATE `{$db_prefix}links` SET `isdisplay`='{$newproperty}' WHERE `linkid`='::'";
	for ($i=0; $i<count($selid); $i++) {
		$queryactnow=str_replace('::', $selid[$i], $queryact);
		$blog->query($queryactnow);
	}
	recache_links ();
	if ($ajax=='on') {
		$fetchURL='admin.php?go=link_detail';
		catchsuccessandfetch($finishok, $fetchURL);
	}
	else catchsuccess($finishok, $backtodefault);
}

if ($job=='groupsorting') {
	$display_overall.=highlightadminitems('groupsorting', 'link');
$display_overall.=<<<eot
<form action="admin.php?go=link_order" method="post" >
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[252]}
</td>
<td class="sectend">{$lna[253]}</td>
</tr>
<tr>
<td colspan=2  class="sect">

<table class='tablewidth' align=center>
<tr>
<td>{$lna[226]}
<select name="sourcelinkgp">
$puttinggp
</select>
</td>
<tr>
<td align=center>
<input type=submit value="{$lna[64]}" class='formbutton'>
</td></tr></td></tr>
</table>
</table>
</form>
eot;
}


if ($job=='order') {
	acceptrequest('sourcelinkgp');
	if ($sourcelinkgp=='') $cancel=$lna[205];
	$linkgptoid=$sourcelinkgp;
	catcherror($cancel);
	$detail_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}links` WHERE `linkgptoid`='{$linkgptoid}' ORDER BY `linkorder`");
	for ($i=0; $i<sizeof($detail_array); $i++) {
		$tmp_id=$detail_array[$i]['linkid'];
		$puttingdetail.="<option value='{$tmp_id}'>{$detail_array[$i]['linkname']}</option>";
	}
	$urlnew="admin.php?go=link_saveorder_";
	$display_overall.=highlightadminitems('groupsorting', 'link');
$display_overall_plus=<<<eot
<form action="" method="post" >
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[252]}
</td>
<td class="sectend">{$lna[253]}</td>
</tr>

<tr><td colspan=2 class="sect">
{$lna[231]} {$linkgps[$linkgptoid]['linkgpname']} <br>
<select multiple size=8 name="list2" style="width: 50%;">
$puttingdetail
</select><br>
<input type="button" value="{$lna[141]}" onclick="Moveup(this.form.list2)" name="B3" class='formbutton'>
<input type="button" value="{$lna[142]}" onclick="Movedown(this.form.list2)" name="B4" class='formbutton'>
<input type=button onclick="GetOptions(this.form.list2, '$urlnew')" class='formbutton' value="{$lna[64]}">
{$lna[143]}
</td>
</tr>
</table>
</form>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='saveorder') {
	if ($itemid=='') {
		$cancel="{$lna[205]}";
	}
	catcherror ($cancel);
	$array_links=@explode(':', $itemid);
	$lastcount=count($array_links);
	for ($i=0; $i<($lastcount-1); $i++) {
		$blog->query("UPDATE `{$db_prefix}links` SET `linkorder`='{$i}' WHERE `linkid`='{$array_links[$i]}'");
	}
	recache_links ();
	catchsuccess ($finishok, $backtodetail);
}


if ($job=="pending") {
	$filename="data/cache_applylinks.php";
	if (!file_exists($filename)) $tablebody="<tr><td colspan=10 align=center><br><br>{$lna[254]}<br><br></td></tr>";
	else {
		$wlinks=@file($filename);
		if (trim($wlinks[0])=='') $tablebody="<tr><td colspan=10 align=center><br><br>{$lna[254]}<br><br></td></tr>";
		else {
			$totoshow=1;
			foreach ($wlinks as $link) {
				@list($unuse, $siteid, $sitename, $siteurl, $sitelogo, $siteintro)=@explode('<|>', trim($link));
				$sitelogo=($sitelogo) ? "<a href='$sitelogo' target='_blank' title='{$lna[255]}'><img src='admin/theme/{$themename}/view.gif' border='0'></a>" : "&nbsp;";
				$tablebody.="<tr class='visibleitem'><td width=35 align=center><input type=checkbox name='selid[]' value='{$siteid}'></td><td width=150>{$sitename}</td><td width=250><a href='{$siteurl}' target=_blank>{$siteurl}</a></td><td width=35 align=center>{$sitelogo}</td><td>{$siteintro}</td><td width=30 align=center><a href=\"admin.php?go=link_approve_{$siteid}\" title='{$lna[256]}'><img src='admin/theme/{$themename}/yes.gif' border='0'></a></td><td width=30 align=center><a href=\"javascript: simulateFormSubmit('admin.php?go=link_disapprove_{$siteid}')\" title='{$lna[257]}'><img src='admin/theme/{$themename}/del.gif' border='0'></a></td></tr>\n";
			}
		}
	}
	$display_overall.=highlightadminitems('pending', 'link');
$display_overall_plus=<<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[21]}
</td>
<td class="sectend">{$lna[258]}</td>
</tr>
</table>

<form action="admin.php?go=link_batchpending" method="post" id='f_s' name='f_s'>
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr class="admintitle"><td width=35 align=center></td><td width=150 align=center>{$lna[233]}</td><td width=250 align=center>{$lna[234]}</td><td width=35 align=center>Logo</td><td align=center>{$lna[134]}</td><td width=30 align=center>{$lna[259]}</td><td width=30 align=center>{$lna[260]}</td></tr>
$tablebody
eot;

	if ($totoshow==1) $display_overall_plus.=<<<eot
<tr><td colspan=7><a href="#unexist" onclick="checkallbox('f_s', 'checked');">{$lna[247]}</a> | <a href="#unexist" onclick="checkallbox('f_s', '');">{$lna[248]}</a></td></tr>
<tr><td colspan=7 height=20></td></tr>
<tr class="adminoption"><td colspan=7>
	{$lna[249]}<br>
	<input type=radio name=opt value='del'>{$lna[78]}<br>
	<input type=radio name=opt value='accept'>{$lna[261]}<select name="newlinkgptoid">$puttinggp</select><br>
	<input type=radio name=opt value='textonly'>{$lna[262]}<select name="newlinkgptoid2">$puttinggp</select><br>
	<div align=center> <input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax('f_s');"> </div>
</td></tr>
eot;
	$display_overall_plus.="</table></form>";
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=="disapprove") {
	$filename="data/cache_applylinks.php";
	if (file_exists($filename) && $itemid!=='') {
		$wlink=@file($filename);
		for ($i=0; $i<count($wlink); $i++) {
			if (strstr($wlink[$i], "<|>{$itemid}<|>")) {
				$wlink[$i]='';
				break;
			}
		}
		$allnow=@implode('', $wlink);
		if ($allnow=='') @unlink($filename);
		else writetofile ($filename, $allnow);
	}
	if ($ajax=='on') {
		$fetchURL='admin.php?go=link_pending';
		catchsuccessandfetch($finishok, $fetchURL);
	}
	else catchsuccess ($finishok, $backtopending);
}

if ($job=="batchpending") {
	acceptrequest('selid,opt,newlinkgptoid,newlinkgptoid2');
	if ($opt=='textonly') $newlinkgptoid=$newlinkgptoid2;
	if (!is_array($selid)) {
		$cancel=$lna[263];
	}
	catcherror ($cancel);
	$filename="data/cache_applylinks.php";
	$wlink=@file($filename);
	if ($opt=='del') {
		for ($i=0; $i<count($wlink); $i++) {
			$link=@explode('<|>', $wlink[$i]);
			if (@in_array($link[1], $selid)) {
				$wlink[$i]='';
			}
			unset ($link);
		}
		$allnow=@implode('', $wlink);
		if ($allnow=='') @unlink($filename);
		else writetofile ($filename, $allnow);
	} elseif ($opt=='accept' || $opt=='textonly') {
		if ($newlinkgptoid==='') catcherror($lna[264]);
		else $newlinkgptoid=floor($newlinkgptoid);
		$linkid=$maxrecord['maxlinkid'];
		for ($i=0; $i<count($wlink); $i++) {
			$link=@explode('<|>', $wlink[$i]);
			if (@in_array($link[1], $selid)) {
				$linklogo=($opt=='textonly') ? '' : $link[4];
				$linkid+=1;
				$plinkout[]="('{$linkid}', '{$link[2]}', '{$link[3]}', '{$linklogo}', '{$link[5]}', '{$newlinkgptoid}', '{$linkid}', '1', '', '')";
				$wlink[$i]='';
			}
			unset ($link);
		}
		$link_query=@implode(',', $plinkout);
		$blog->query("INSERT INTO `{$db_prefix}links` VALUES {$link_query}");
		$blog->query("UPDATE `{$db_prefix}maxrec` SET `maxlinkid`='{$linkid}'");
		$allnow=@implode('', $wlink);
		if ($allnow=='') @unlink($filename);
		else writetofile ($filename, $allnow);	
		recache_links ();
	}
	if ($ajax=='on') {
		$fetchURL='admin.php?go=link_pending';
		catchsuccessandfetch($finishok, $fetchURL);
	}
	else catchsuccess ($finishok, $backtopending);
}


?>