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
$finishok=$lna[177];
$backtodefault="{$lna[4]}|admin.php?go=category_default";
$finishok2=$lna[178];
$backtotag="{$lna[17]}|admin.php?go=category_tags";

if ($job=='' || $job=="default") {
	$urlnew="admin.php?go=category_order_";
	for ($i=0; $i<sizeof($arrayvalue_categories); $i++) {
		$puttingcates.="<option value='{$arrayvalue_categories[$i]}'>{$arrayoption_categories[$i]}</option>";
		$puttingcates_after.="<option value='{$arrayvalue_categories[$i]}'>{$lna[1025]} {$arrayoption_categories[$i]}</option>";
	}
	$display_overall.=highlightadminitems('default', 'category');
$display_overall_plus=<<<eot
<form action="admin.php?go=category_new" method="post" id="ajaxForm1">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[180]}
</td>
<td class="sectend">{$lna[181]}</td>
</tr>
<tr>
<td colspan=2  class="sect">

<table class='tablewidth' align=center>
<tr><td width=160>{$lna[182]}</td>
<td><input type="text" name="newcatename" value="" size="30"></td></tr>
<tr><td width=160 valign='top'>{$lna[1117]} <a href="#" title="{$lna[1118]}" onclick="alert('{$lna[1118]}')">[?]</a></td>
<td><input type="text" name="newcateurlname" value="" size="30" maxlength="70"> {$lna[102]}</td></tr>
<tr><td width=160 valign=top>{$lna[183]}</td>
<td><textarea cols=100 rows=4 name="newcatedesc"></textarea></td></tr>
<tr><td width=160>{$lna[187]}</td>
<td><select name="newcatemode"><option value="0">{$lna[188]}</option><option value="1">{$lna[189]}</option></select> {$lna[190]}
</td></tr>
<tr><td width=160>{$lna[184]}</td>
<td><select name="newcateproperty"><option value="0">{$lna[185]}</option><option value="1">{$lna[186]}</option></select> {$lna[958]}</td></tr>
<tr><td width=160 valign=top>{$lna[191]}</td>
<td><input type="text" name="newcateurl" value="" size="30"> {$lna[192]}</td></tr>
<tr><td width=160>{$lna[193]}</td>
<td><input type="text" name="newcateicon" value="" size="30"> {$lna[194]}</td></tr>
<tr><td width=160>{$lna[1022]}</td><td><select name="targetcate">
<option value='-1'>{$lna[1023]}</option><option value='-2' selected>{$lna[1024]}</option>
$puttingcates_after
</select></td></tr>

</table>

</td></tr>
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
{$lna[195]}
</td>
<td class="sectend">{$lna[196]}</td>
</tr>
<tr>
<td colspan=2  class="sect">
<select multiple size=8 style="width: 50%;" name="list2" style="width: 120px">
$puttingcates
</select><br>
<input type="button" value="{$lna[141]}" onclick="Moveup(this.form.list2)" name="B3" class='formbutton'>
<input type="button" value="{$lna[142]}" onclick="Movedown(this.form.list2)" name="B4" class='formbutton'>
<input type=button onclick="GetOptions(this.form.list2, '$urlnew')" class='formbutton' value="{$lna[64]}">
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
{$lna[197]}
</td>
<td class="sectend">{$lna[198]}</td>
</tr>
<tr>
<td colspan=2  class="sect">
<table class='tablewidth' align=center>
<tr>
<td width=150>{$lna[199]}
<select name="sourcecate">
$puttingcates
</select><input type=hidden name=comm value='0'>
</td>
<td width=50%>{$lna[200]}<input type=radio name='go' value='category_edit' onclick="swapdiv(0);">{$lna[77]} <input type=radio  name='go' value='category_del' onclick="swapdiv(1);">{$lna[78]} <input type=radio  name='go' value='category_combine' onclick="swapdiv(2);">{$lna[201]}</td></tr>
<tr>
<td width=50%>&nbsp;</td>
<td width=50%>
<div id="targetdiv3" style="display:none"><input type=submit value="{$lna[64]}" class='formbutton'></div>
<div id="targetdiv" style="display:none"><b>{$lna[202]}<a href='admin.php?act=entry'>{$lna[203]}</a><br>
<input type=button value="{$lna[64]}" onclick='if(confirm("{$lna[179]}")){adminSubmitAjax(2);}' class='formbutton'>
</div>
<div id="targetdiv2" style="display:none">{$lna[204]}
<select name="targetcate">
$puttingcates
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

if ($job=='order') {
	if ($itemid=='') {
		$cancel=$lna[205];
	}
	catcherror ($cancel);
	$array_cates=@explode(':', $itemid);
	$lastcount=count($array_cates);
	
	for ($i=0; $i<($lastcount-1); $i++) {
		$blog->query("UPDATE `{$db_prefix}categories` SET `cateorder`='{$i}' WHERE `cateid`='{$array_cates[$i]}'");
	}
	recache_categories();
	catchsuccess($finishok, $backtodefault);
}

if ($job=='edit' ) {
	acceptrequest('sourcecate');
	if ($categories[$sourcecate]['cateproperty']==0) {
		$sel1='selected';
		$alsochange="{$lna[924]}<br><input type=radio name=alsochange value=1 checked>{$lna[239]} <input type=radio name=alsochange value=0>{$lna[240]} ";
	} else {
		$sel2='selected';
		$alsochange="{$lna[925]}<br><input type=radio name=alsochange value=2 checked>{$lna[239]} <input type=radio name=alsochange value=0>{$lna[240]} ";
	}
	if ($categories[$sourcecate]['catemode']==0) {
		$sel3='selected';
	} else {
		$sel4='selected';
	}
	$display_overall.=highlightadminitems('default', 'category');
$display_overall.= <<<eot
<script type="text/javascript">
function catchusefuldata() {
	if (document.getElementById('alsoch').style.display=='none') {
		document.getElementById('ignorepropertychange').value='1';
	}
	adminSubmitAjax('f_s');
}
</script>

<form action="admin.php" method="post" id='f_s'>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[197]}
</td>
<td class="sectend">{$lna[198]}</td>
</tr>
<tr>
<td colspan=2  class="sect">

<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160>{$lna[182]}</td>
<td><input type="text" name="newcatename" value="{$categories[$sourcecate]['catename']}"></td>
</tr>
<tr><td width=160 valign='top'>{$lna[1117]} <a href="#" title="{$lna[1118]}" onclick="alert('{$lna[1118]}')">[?]</a></td>
<td><input type="text" name="newcateurlname" value="{$categories[$sourcecate]['cateurlname']}" size="30" maxlength="70"> {$lna[102]}</td></tr>
<tr>
<td width=160 valign=top>{$lna[183]}</td>
<td><textarea cols=100 rows=4 name="newcatedesc">{$categories[$sourcecate]['catedesc']}</textarea></td>
</tr>
<tr><td width=160>{$lna[187]}</td>
<td><select name="newcatemode"><option value="0" {$sel3}>{$lna[188]}</option><option value="1" {$sel4}>{$lna[189]}</option></select> {$lna[190]}
</td></tr>
<tr>
<td width=160 valign=top>{$lna[184]}</td>
<td><select name="newcateproperty" onchange="document.getElementById('alsoch').style.display=(document.getElementById('alsoch').style.display=='none')? 'block': 'none';"><option value="0" {$sel1}>{$lna[185]}</option><option value="1" 	{$sel2}>{$lna[186]}</option></select>  {$lna[958]}<div id='alsoch' style="display: none;">{$alsochange}</div></td>
</tr>
<tr><td width=50 valign=top>{$lna[191]}</td>
<td><input type="text" name="newcateurl" value="{$categories[$sourcecate]['cateurl']}" size="30"> {$lna[192]}</td> </tr>
<tr><td width=50>{$lna[193]}</td>
<td><input type="text" name="newcateicon" value="{$categories[$sourcecate]['cateicon']}" size="30"> {$lna[194]}</td></tr>
</table>

</td></tr>
<tr>
<td colspan=4 align=center class="sectbar">
<input type=hidden name=go value="category_save_{$sourcecate}">
<input type=hidden name='ignorepropertychange' id='ignorepropertychange' value=''>
<input type=button value="{$lna[64]}" class='formbutton' onclick="catchusefuldata();"> <input type=reset class='formbutton' value="{$lna[65]}">
</td></tr>
</table>
</form>
eot;
}


if ($job=='new' || $job=='save' || $job=='newinedit') {
	acceptrequest('newcatename,newcateurlname,newcatedesc,newcateproperty,newcatemode,newcateurl,newcateicon,alsochange,ignorepropertychange,targetcate');
	if ($newcatename=='' || $newcatedesc=='') {
		catcherror ($lna[206]);
	}
	$newcatename=addslashes(safe_convert(stripslashes($newcatename)));
	
	$newcateurlname=safe_convert(stripslashes($newcateurlname));
	if ($newcateurlname) {
		if (is_numeric($newcateurlname)) catcherror($lna[1168]);
		$newurlcatename=urlencode($newcateurlname);
		if (strstr($newcateurlname, '%')) catcherror($lna[1168]);
		$newcateurlname=addslashes($newcateurlname);
		if ($job=='save') $queryplus=" AND `cateid`<>'{$itemid}'";
		$tmpresult=$blog->getbyquery("SELECT * FROM `{$db_prefix}categories` WHERE `cateurlname`='{$newcateurlname}' {$queryplus} LIMIT 1");
		if ($tmpresult['catename']) catcherror($lna[1169]);
	}
	

	$newcatedesc=addslashes(safe_convert(stripslashes($newcatedesc), 1));
	$newcatedesc=str_replace('<|>', '&lt;|&gt;', $newcatedesc);
	
	if ($job=='new' || $job=='newinedit') {
		$new_cate_id=$maxrecord['maxcateid']+1;
		$targetcate=floor($targetcate);
		if ($targetcate!=-2) {
			if ($targetcate==-1) {
				$tmpresult=$blog->getbyquery("SELECT * FROM `{$db_prefix}categories` ORDER BY `cateorder` ASC LIMIT 1");
			}
			else {
				$tmpresult=$blog->getbyquery("SELECT * FROM `{$db_prefix}categories` WHERE `cateid`='{$targetcate}' LIMIT 1");
				$insertcateorder=$tmpresult['cateorder'];
				$tmpresult=$blog->getbyquery("SELECT * FROM `{$db_prefix}categories` WHERE `cateorder`>'{$insertcateorder}' ORDER BY `cateorder` ASC LIMIT 1");
			}
			if (trim($tmpresult['cateorder'])!='') {
				$insertcateorder=$tmpresult['cateorder'];
				$result=$blog->query("UPDATE `{$db_prefix}categories` SET `cateorder`=`cateorder`+1 WHERE `cateorder`>={$insertcateorder}");
			}
		} else $insertcateorder=$new_cate_id;
		$result=$blog->query("INSERT INTO `{$db_prefix}categories` VALUES ('{$new_cate_id}', '{$newcatename}', '{$newcatedesc}', '{$newcateproperty}', '{$insertcateorder}', '{$newcatemode}', '{$newcateicon}', '{$newcateurl}', '{$newcateurlname}', '', '')");
		$result=$blog->query("UPDATE `{$db_prefix}maxrec` SET `maxcateid`='{$new_cate_id}'");
	} else {
		$result=$blog->query("UPDATE `{$db_prefix}categories` SET `catename`='{$newcatename}', `catedesc`='{$newcatedesc}', `cateproperty`='{$newcateproperty}', `catemode`='{$newcatemode}', `cateurl`='{$newcateurl}', `cateicon`='{$newcateicon}', `cateurlname`='{$newcateurlname}' WHERE `cateid`='{$itemid}'");
		if (($alsochange==1 || $alsochange==2) && $ignorepropertychange!=1) {
			$newblogproperty=($alsochange==1) ? 2 : 0;
			$newreproperty=($alsochange==1) ? 1 : 0;
			$result=$blog->query("UPDATE `{$db_prefix}blogs` SET `property`='{$newblogproperty}' WHERE `category`='{$itemid}'");
			$all_raw_afflects=$blog->getarraybyquery("SELECT blogid FROM `{$db_prefix}blogs` WHERE `category`='{$itemid}'");
			$all_affected=$all_raw_afflects['blogid'];
			if (is_array($all_affected) && count($all_affected)!=0) {
				$all_affected_q=@implode(',', $all_affected);
				$querystr="UPDATE `{$db_prefix}replies` SET `reproperty`='{$newreproperty}' WHERE blogid in ($all_affected_q)";
				$blog->query($querystr);
				recache_latestentries();
				recache_latestreplies();
			}
		}
	}
	recache_categories();

	if ($job=='newinedit' && $ajax=='on') {
		$tmpresult=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}categories` ORDER BY `cateorder` ASC");
		$puttingcates='<select name="category" id="category" class="formselect">';
		foreach ($tmpresult as $tmp) {
			$tmp['catename']=($tmp['catemode']==1) ? ('|- '.$tmp['catename']) : $tmp['catename'];
			$tmp['catename']=($tmp['cateurl']) ? ($tmp['catename'].$lnc[0]) : $tmp['catename'];
			$issel=($tmp['cateid']==$new_cate_id) ? ' selected' : '';
			$puttingcates.="<option value='{$tmp['cateid']}'{$issel}>{$tmp['catename']}</option>";
		}
		$puttingcates.='</select>';
		catchsuccess($puttingcates);
	}

	if ($ajax=='on') {
		$fetchURL='admin.php?go=category_default';
		catchsuccessandfetch($finishok, $fetchURL);
	}
	else catchsuccess($finishok, $backtodefault);
}

if ($job=='del') {
	acceptrequest('sourcecate');
	if ($sourcecate==='') {
		catcherror ($lna[207]);
	}
	$entrynum=$blog->countbyquery("SELECT COUNT(blogid) FROM `{$db_prefix}blogs` WHERE `category`='{$sourcecate}'");
	if ($entrynum>=1) $cancel=$lna[208];
	catcherror ($cancel);
	$blog->query("DELETE FROM `{$db_prefix}categories` WHERE `cateid`='{$sourcecate}'");
	recache_categories();
	if ($ajax=='on') {
		$fetchURL='admin.php?go=category_default';
		catchsuccessandfetch($finishok, $fetchURL);
	}
	else catchsuccess($finishok, $backtodefault);
}
	

if ($job=='combine') {
	acceptrequest('sourcecate,targetcate');
	if ($sourcecate=='' || $targetcate=='') {
		$cancel=$lna[207];
	}
	if ($sourcecate==$targetcate) {
		$cancel=$lna[209];
	}
	catcherror ($cancel);
	$blog->query("UPDATE `{$db_prefix}blogs` SET `category`='{$targetcate}' WHERE `category`='{$sourcecate}'");
	recache_categories();
	catchsuccess($finishok, $backtodefault);
}

if ($job=='tags') {
	$all_tags=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}tags`");
	$tablebody.="<tr>";
	for ($i=0; $i<count($all_tags); $i++) {
		$tablebody.="<td><input type=checkbox name='selid[]' value='{$all_tags[$i]['tagname']}'>{$all_tags[$i]['tagname']}</td>";
		if ($i%5==4) $tablebody.="</tr><tr>";
	}
	$tablebody.="</tr>";
	$display_overall.=highlightadminitems('tags', 'category');
$display_overall_plus= <<<eot
<form action="admin.php?go=category_batchtags" method='post' id="ajaxForm1">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[17]}
</td>
<td class="sectend">{$lna[210]}</td>
</tr>
<tr class='sect'>
<td colspan=2>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
$tablebody
</table>
<br>
</td></tr>
<tr class='sect'>
<td colspan=2 align=center class="sectbar">
{$lna[211]} <!--<input type=radio name='opt' value='combine'>{$lna[212]}<input type=text size=6 name='newtagname'> &nbsp;&nbsp;--> <input type=radio name='opt' value='del'>{$lna[78]} &nbsp;&nbsp;  <input type=radio name='opt' value='counttags'><acronym title="{$lna[1184]}">{$lna[1183]}</acronym></a> &nbsp;&nbsp; <input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax(1);">
</td></tr>
</table>
</form>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='batchtags') {
	acceptrequest('selid,opt');
	if (!is_array($selid)) catcherror ($lna[213]);
	if ($opt=='del') {
		for ($i=0; $i<count($selid); $i++) {
			$blog->query("UPDATE `{$db_prefix}blogs` SET tags=replace(tags, '>{$selid[$i]}>', '>')");
			$blog->query("DELETE FROM `{$db_prefix}tags` WHERE `tagname`='{$selid[$i]}'");
		}
	}
	if ($opt=='counttags') {
		$all_tagentries=$blog->getarraybyquery("SELECT `tags` FROM `{$db_prefix}blogs` WHERE tags<>'' AND tags<>'>'");
		$all_tag_lists=@implode('', $all_tagentries['tags']);
		$all_tag_lists=@explode('>', $all_tag_lists);
		$counted_list=array_count_values($all_tag_lists);
		for ($i=0; $i<count($selid); $i++) {
			$to_update_tag=$selid[$i];
			$to_update_value=floor($counted_list[$to_update_tag]);
			$blog->query("UPDATE `{$db_prefix}tags` SET tagcounter='{$to_update_value}' WHERE `tagname`='{$to_update_tag}'");
		}
	}
	recache_taglist();
	if ($ajax=='on') {
		$fetchURL='admin.php?go=category_tags';
		catchsuccessandfetch($finishok2, $fetchURL);
	}
	else catchsuccess($finishok2, $backtotag);
}

?>