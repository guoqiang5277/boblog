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
$finishok=$lna[265];
$partbacktoart=$lna[266];
$backtoaddnew="{$lna[267]}|admin.php?go=edit_add";
$backtodraft="{$lna[325]}|admin.php?go=entry_draft";

if (!$job) $job='add';
$id=$itemid;

include_once ("data/cache_emot.php");
if ($flset['weather']!=1) {
	include_once ("data/weather.php");
}

//All Tags
if ($flset['tags']!=1) {
	$existtagall=trim(readfromfile("data/cache_tags.php"));
	$exist_tags_all=@explode(' ',$existtagall);
}

if ($job=='add' || $job=='store') { //Permission check
	checkpermission('AddEntry');
	confirmpsw(); //Re-check password
} elseif ($job=='edit' || $job=='restore') {
	checkpermission('EditEntry');
	confirmpsw(); //Re-check password
} 

if ($job!='add' && $job!='store' && $job!='sendtb') {
	if ($id=="") $cancel=$lna[268];
	else {
		if ($permission['SeeHiddenEntry']!=1) {
			$partialquery="SELECT * FROM `{$db_prefix}blogs` WHERE `blogid`='{$id}'  AND `property`<>'2'";
		} else {
			$partialquery="SELECT * FROM `{$db_prefix}blogs` WHERE `blogid`='{$id}'";
		}
		$recordsa=$blog->getgroupbyquery($partialquery);
		$records=$recordsa[0];
		if ($records['blogid']=='' && $ajax!='on') {
			$cancel=$lna[268];
		}
		$records['entrysummary']=safe_invert($records['entrysummary'], $records['htmlstat']);
		$records['entrysummary']=preg_replace("/\[php\](.+?)\[\/php\]/ise", "phpcode4('\\1')", $records['entrysummary']);
		$displaysummary=($records['entrysummary']) ? 'block' : 'none';
	}
}

if ($job=='edit' && $records['authorid']!=$userdetail['userid'] && $permission['EditSafeMode']!=1) {
	$cancel=$lna[268];
}

catcherror ($cancel);

if ($job=='add' || $job=='edit') { //Initialize public items
	acceptrequest('ignore');
	$findautosaver=$blog->getbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE `blogid`=-1");
	if ($findautosaver['blogid']=='-1' && $ignore!=1) {
		@header("Content-Type: text/html; charset=utf-8");
		$t=new template;
		$t->showtips($lna[926],"<font color=red>{$lna[927]}</font><br>",array("{$lna[928]}|admin.php?go=edit_edit_-1&ignore=1",  "{$lna[929]}|admin.php?".$_SERVER['QUERY_STRING']."&ignore=1"));
		exit();
	}

	$currentjob=basename($_SERVER['QUERY_STRING']);
	@list($currentjob, $unuse)=@explode('&useeditor=', $currentjob);

	if($flset['weather']!=1 && is_array($weather)) { //Get Weather List
		while (@list($wkey, $wvalue)=@each($weather)) {
			$arrayoption_weather[]=$wvalue['text'];
			$arrayvalue_weather[]=$wkey;
		}
	}
	$arrayoption_property=array($lna[269], $lna[270], $lna[271], $lna[272], $lna[1111]);
	$arrayvalue_property=array(0, 1, 2, 3, 4);
	$arrayoption_sticky=array($lna[273], $lna[274], $lna[275]);
	$arrayvalue_sticky=array(0, 1, 2);
	$usergp_1=array_values($usergp);
	$usergp_2=array_keys($usergp);
	$arrayoption_editors=array('QuickTags', $lna[568], "FCKeditor {$lna[1017]}", "TinyMCE {$lna[1017]}", $lna[711]);
	$arrayvalue_editors=array('quicktags', 'ubb', 'fckeditor', 'tinymce', 'custom');

	$ismoreon='none';
	if ($flset['tags']!=1 && $permission['AddTag']==1) {
		$exist_tags_tmp=$blog->getarraybyquery("SELECT * FROM `{$db_prefix}tags` ORDER BY `tagcounter` DESC LIMIT 50");
		$exist_tags=$exist_tags_tmp['tagname'];
		for ($i=0; $i<count($exist_tags); $i++) {
			$exist_tags[$i]="'".str_replace("'", '&#39;', $exist_tags[$i])."'";
		}
		$tag_js="<script type='text/javascript'>\nvar custom_array = new Array();\ncustom_array=[".@implode(',', $exist_tags)."];\n</script>\n<script type='text/javascript' src='images/js/autosuggestion.js'></script>";
		$taglist='';
	} else {
		$taglist=$lna[277];
		$tagdisable='disabled';
	}

	$quickbutton_bottom="<input type=button value=\"{$lna[1123]}\" class=\"formbutton\" onclick=\"savetodraftnow();\";>";

}

if ($job=='edit') { //Initialize Edit only items
	if ($flset['weather']!=1) $selectedid_weather=array_search($records['weather'], $arrayvalue_weather); //selected weather
	$selectedid_category=array_search($records['category'], $arrayvalue_categories); //selected category
	$selectedid_sticky=array_search($records['sticky'], $arrayvalue_sticky); //if pinned
	$records['tags']=str_replace('>', ' ', trim($records['tags'],'>'));
	if ($permission['AddTag']!=1) $taglist.="<input type='hidden' name='tags' value='{$records['tags']}'>";
	if ($records['permitgp']!=='') {
		$allowedgp=@explode('|', $records['permitgp']);
		foreach ($usergp as $gpid=>$gpname) {
			if (@!in_array($gpid, $allowedgp)) $arraychecked_permitgp[]=1;
			else $arraychecked_permitgp[]=0;
		}
	}
	$editwarntime=$lna[278];
	$hiddenareas=($records['blogid']==-1) ? "<input type='hidden' name='go' id='go' value='edit_store'/>" : "<input type='hidden' name='go' id='go' value='edit_restore_{$records['blogid']}'/>";
	$hiddenareas.="<input type='hidden' name='idforsave' id='idforsave' value='{$records['blogid']}'/>";
	$hiddenareas.="<input type='hidden' name='oldgo' id='oldgo' value='{$currentjob}'/>";
	$resendping=($records['blogid']==-1) ? '' : "<input type=checkbox name='resend' value=1>{$lna[279]}<br>";
	if ($records['blogid']==-1) {
		$records['property']=0;
		$hiddenareas.="<input type='hidden' name='clearautosaver' id='clearautosaver' value='1'/>";
	}
	$records['pub_tmp']=gmdate('Y-n-j-H-i-s', $records['pubtime']+$config['timezone']*3600);

	if ($records['entrysummary']) $entrysummaryplus1=" selected";
	elseif ($records['frontpage']==1) $entrysummaryplus2=" selected";
	else $entrysummaryplus0=" selected";

	$quickbutton_bottom=($records['property']>=3) ? "<input type=button value=\"{$lna[340]}\" class=\"formbutton\" onclick=\"publishdraftnow();\";>" : "<input type=button value=\"{$lna[1123]}\" class=\"formbutton\" onclick=\"savetodraftnow();\";>";

}

if ($job=='add') { //Initialize Add only items
	if ($permission['Html']==1) $records['htmlstat']=1;
	if ($permission['Ubb']==1) $records['ubbstat']=1;
	if ($permission['Emot']==1) $records['emotstat']=1;
	$hiddenareas="<input type='hidden' name='go' id='go' value='edit_store'/>";
	$hiddenareas.="<input type='hidden' name='idforsave' id='idforsave' value=''/>";
	$hiddenareas.="<input type='hidden' name='oldgo' id='oldgo' value='{$currentjob}'/>";
	$records['pub_tmp']=gmdate('Y-n-j-H-i-s', time()+$config['timezone']*3600);
	$displaysummary='none';
}

if ($job=='add' || $job=='edit') { //Initialize public items
	@list($records['pub_year'], $records['pub_month'], $records['pub_day'], $records['pub_hour'], $records['pub_min'], $records['pub_sec'])=explode('-', $records['pub_tmp']);
	if ($permission['Html']==1) $disablehtmlstatus=0;
	else $disablehtmlstatus=1;
	if ($permission['Ubb']==1) $disableubbstatus=0;
	else $disableubbstatus=1;
	if ($permission['Emot']==1) $disableemotstatus=0;
	else $disableemotstatus=1;
	if ($permission['PinEntry']==1) $disabled_sticky=0;
	else $disabled_sticky=1;
	$puttingproperty=autoselect('property', $arrayoption_property, $arrayvalue_property, $records['property']);

	$selectedid_editors=array_search($useeditor, $arrayvalue_editors);
	$puttingeditors=autoselect('useeditor', $arrayoption_editors, $arrayvalue_editors, $selectedid_editors);

	$puttingcates=autoselect('category', $arrayoption_categories, $arrayvalue_categories, $selectedid_category);
	$puttingcates=str_replace('</select>', "<option value='new'>[+]{$lna[180]}</option></select>", $puttingcates);
	$puttingcates=str_replace('<select', "<select onchange=\"if (this.options[this.selectedIndex].value=='new') {document.getElementById('addnewcate').style.display='block';} else {document.getElementById('addnewcate').style.display='none';}\"  ", $puttingcates);
	for ($i=0; $i<sizeof($arrayvalue_categories); $i++) {
		$puttingcates_after.="<option value='{$arrayvalue_categories[$i]}'>{$lna[1025]} {$arrayoption_categories[$i]}</option>";
	}

	if ($flset['weather']!=1) $puttingweather=autoselect('sweather', $arrayoption_weather, $arrayvalue_weather, $selectedid_weather);
	else {
		$lna[301]=$puttingweather='';
	}
	$puttingsticky=autoselect('sticky', $arrayoption_sticky, $arrayvalue_sticky, $selectedid_sticky);
	$puttinghtml=autoradio('checkbox', 'html', array($lna[280]), array(1), array($records['htmlstat']), array($disablehtmlstatus));
	$puttingubb=autoradio('checkbox', 'ubb', array($lna[281]), array(1), array($records['ubbstat']), array($disableubbstatus));
	$puttingemot=autoradio('checkbox', 'emot', array($lna[282]), array(1), array($records['emotstat']), array($disableemotstatus));
	$puttingpermitgp=autoradio ('checkbox', 'permitgp[]', $usergp_1, $usergp_2, $arraychecked_permitgp);
	if ($flset['star']!=1) $puttingstarred=autoradio ('checkbox', 'starred', array($lna[1020]), array(1), array($records['starred']%2));
	else $puttingstarred='';

	$hiddenareas.="<input type='hidden' name='forcedraft' id='forcedraft' value='0'/>";
	if ($disableinvert!=1) $records['content']=safe_invert($records['content'], $records['htmlstat']);
	$records['content']=preg_replace("/\[php\](.+?)\[\/php\]/ise", "phpcode4('\\1')", $records['content']);
	$records['content']=stripslashes($records['content']);
	if ($editorbody!='PHP_INCLUDE') $editorbody=str_replace("{content}", $records['content'], $editorbody);

//Now Begins the main part
	$display_overall.=highlightadminitems('write', 'entry');
$display_overall.= <<<eot
<script type='text/javascript'>
function chktitle() {
	if (document.getElementById('title').value=='' || document.getElementById('title').value==null) {
		alert("{$lna[877]}");
	} else if (document.getElementById('category').options[document.getElementById('category').selectedIndex].value=='new') {
		alert("{$lna[1026]}");
	}
	else document.getElementById('realsubmit').click();
}

function savetodraftnow() {
	if (document.getElementById('forcedraft')) document.getElementById('forcedraft').value=1;
	chktitle();
}

function publishdraftnow() {
	if (document.getElementById('forcedraft')) document.getElementById('forcedraft').value=2;
	chktitle();
}

function previewcontent() {
	var govaluetmp=document.getElementById('go').value;
	document.getElementById('go').value='';
	document.getElementById('editentry').target='_blank';
	document.getElementById('editentry').action='read.php?preview_';
	document.getElementById('editentry').submit();
	document.getElementById('editentry').target='_self';
	document.getElementById('editentry').action='admin.php';
	document.getElementById('go').value=govaluetmp;
}

</script>
$tag_js
<form name='editentry' id='editentry' action='admin.php' method='post' enctype='multipart/form-data' 	{$submitjs}>{$hiddenareas}
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[22]} <a href="#" onclick="dohs()"><img align="absmiddle" src="admin/theme/{$themename}/more.gif" alt="More" border="0"/></a>
</td>
<td class="sectend">{$lna[283]}</td>
</tr>
<tr>
<td colspan=2  class="sect">

<table width=100% cellpadding=4 cellspacing=1 align=center>
<tr bgcolor="#ffffff" align=left class="hiddenitem" id="extraoption1">
<td width=100 align=center>{$lna[567]}</td><td>{$puttingeditors} <input type=button value="{$lna[64]}" onclick="changeeditor();"></td>
</tr>
<tr bgcolor="#ffffff" align=left class="visibleitem">
<td width=100 align=center>{$lna[284]}</td><td><input type='text' name='title' id='title' value="{$records['title']}" size='50'  class='formtext'></td>
</tr>
<tr bgcolor="#ffffff" align=left class="hiddenitem">
<td width=100 align=center valign=top>{$lna[285]}</td><td>
<div id='cateselarea'>{$puttingcates} {$lna[286]}

<div id='addnewcate' style='display: none;'>
<table width='95%' align=left>
<tr><td width=100>{$lna[182]}</td>
<td><input type="text" name="newcatename" id="newcatename" value="" size="30"></td></tr>
<tr><td width=100 valign=top>{$lna[183]}</td>
<td><textarea cols=30 rows=4 name="newcatedesc" id="newcatedesc"></textarea></td></tr>
<tr><td width=100>{$lna[187]}</td>
<td><select name="newcatemode" id="newcatemode"><option value="0">{$lna[188]}</option><option value="1">{$lna[189]}</option></select>
</td></tr>
<tr><td width=100>{$lna[184]}</td>
<td><select name="newcateproperty" id="newcateproperty"><option value="0">{$lna[185]}</option><option value="1">{$lna[186]}</option></select></td></tr>
<tr><td width=100>{$lna[1022]}</td><td><select name="targetcate" id="targetcate">
<option value='-1'>{$lna[1023]}</option><option value='-2' selected>{$lna[1024]}</option>
$puttingcates_after
</select></td></tr>
<tr><td colspan=2><input type=button value="{$lna[64]}" onclick="ajax_addcategory();"> <input type=button value="{$lna[138]}" onclick="showhidediv('addnewcate');">
</td></tr></table>
</div>
	
</div>
</td></tr>
<tr bgcolor="#ffffff" align=left class="visibleitem">
<td width=100 valign=top align=center>{$lna[287]}<br><div align=left>{$puttinghtml}<br>{$puttingubb}<br>{$puttingemot}<br>{$puttingstarred}</div>
</td>
<td>
eot;

if ($editorbody=='PHP_INCLUDE') {
	include_once("editor/{$useeditor}/editorinclude.php");
} else $display_overall.=$editorbody;

$display_overall.= <<<eot
</td>
</tr>

<tr bgcolor="#ffffff" align=left class="hiddenitem" id="extraoption2">
<td width=100 valign=top align=center>{$lna[1112]}</td>
<td><select name='summaryway' id='summaryway' onchange="if (this.options[this.selectedIndex].value=='1') {document.getElementById('entrysummary').style.display='block';} else {document.getElementById('entrysummary').style.display='none';}"><option value="0"{$entrysummaryplus0}>{$lna[1113]}</option><option value="2"{$entrysummaryplus2}>{$lna[1114]}</option><option value="1"{$entrysummaryplus1}>{$lna[1115]}</option></select>
<div id='entrysummary' style='display: {$displaysummary};'>
<textarea style="width: 95%; height: 100px" name="entrysummary" id="entrysummary">{$records['entrysummary']}</textarea>
<br>{$lna[1116]}
</div>
</td>
</tr>

<tr bgcolor="#ffffff" align=left valign=top class="visibleitem">
<td width=100 align=center>{$lna[1117]}</td><td><input type=text name='blogalias' id='blogalias' value="{$records['blogalias']}" size="50"> {$lna[102]}<br>
{$lna[1118]}
</td>
</tr>


<tr bgcolor="#ffffff" align=left valign=top class="hiddenitem" id="extraoption3">
<td width=100 align=center>{$lna[1119]}</td><td>{$lna[1120]}: <input type=text name='originsrc' id='originsrc' value="{$records['originsrc']}" size="50">{$lna[1121]} &nbsp;&nbsp; {$lna[1122]}: <input name='comefrom' id='comefrom' type=text value="{$records['comefrom']}" size="20"></td>
</tr>

<tr bgcolor="#ffffff" align=left class="visibleitem" id="extraoption4">
<td width=100 valign=top align=center>{$lna[288]}</td>
<td><input type=checkbox id='changemytime' name='changemytime' value=1 onclick="timechanger();">{$lna[289]} $editwarntime
<div style="clear:both; display: none;" id="changetime">{$lna[290]} <input type='text' name='newyear' size='4' value="{$records['pub_year']}" maxlength='4'>{$lna[291]} - <input type='text' name='newmonth' size='2' value="{$records['pub_month']}" maxlength='2'>{$lna[292]} - <input type='text' name='newday' size='2' value="{$records['pub_day']}" maxlength='2'>{$lna[293]} -  <input type='text' name='newhour' size='2' value="{$records['pub_hour']}" maxlength='2'>{$lna[294]} -  <input type='text' name='newmin' size='2' value="{$records['pub_min']}" maxlength='2'>{$lna[295]}  -  <input type='text' name='newsec' size='2' value="{$records['pub_sec']}" maxlength='2'>{$lna[296]}
</div>
</td>
</tr>

<tr bgcolor="#ffffff" align=left class="hiddenitem" id="extraoption5">
<td width=100 valign=top align=center>{$lna[297]}</td>
<td>
{$lna[298]} {$puttingproperty} ({$lna[299]})<br>
{$lna[300]} {$puttingsticky} <br>
{$lna[301]} {$puttingweather}
</td>
</tr>
<tr bgcolor="#ffffff" align=left class="visibleitem" id="extraoption6">
<td width=100 valign=top align=center>{$lna[302]}</td>
<td>{$lna[303]}<br>
{$puttingpermitgp}
</td>
</tr>
eot;

if ($flset['tags']!=1) {
	$display_overall.= <<<eot
<tr bgcolor="#ffffff" align=left class="hiddenitem">
<td width=100 valign=top align=center>Tags</td>
<td>{$lna[304]}<br><input name='tags' autocomplete="off" id='tags' size='100' class='formtextarea'  value="{$records['tags']}" onfocus="simple_ac_init('tags', 'taghint')" {$tagdisable} />
<div id="taghint" style="">{$taglist}</div>
</td>
</tr>
eot;
}

$display_overall.= <<<eot
<tr bgcolor="#ffffff" align=left class="visibleitem" id="extraoption7">
<td width=100 valign=top align=center>{$lna[305]}</td>
<td>{$resendping}<textarea name='pinged' id='pinged' rows='2' cols='100' class='formtextarea'>{$records['pinged']}</textarea><br>
{$lna[306]}
</td>
</tr>
<tr bgcolor="#ffffff" align=left class="hiddenitem" id="extraoption8">
<td width=100 align=center valign=top>{$lna[1080]}</td><td><input type='text' name='blogpsw' id='blogpsw' value="{$records['blogpsw']}" size='15' maxlength='18' class='formtext'> {$lna[1081]}</td></tr>

</table>

</td>
</tr>
<tr>
<td colspan=4 align=center class="sectbar">
<input type=button value="{$lna[64]}" onclick="chktitle();" class="formbutton"> <input type=reset value="{$lna[65]}" class="formbutton"> 
$quickbutton_bottom
<input type=button value="{$lna[1197]}" onclick="previewcontent();" class="formbutton">
</td></tr>
</table>
<div style='visibility: hidden'><input type=submit value="{$lna[64]}" id='realsubmit' class='formbutton'></div>
</form>

<script type="text/javascript">
var totalextras=8;
var extrasdisplay=0;
function hs_extras(status) {
	var cihs='extraoption';
	for (var ihs=1; ihs<totalextras+1; ihs++) {
		document.getElementById(cihs+ihs).style.display=status;
	}
}
function dohs() {
	if (extrasdisplay==1) {
		hs_extras('none');
		extrasdisplay=0;
	} else {
		hs_extras('');
		extrasdisplay=1;
	}
}
hs_extras('none');
</script>
eot;
}

if ($job=='store' || $job=='restore') {
	acceptrequest('title,property,category,tags,sticky,html,ubb,emot,sweather,permitgp,pinged,changemytime,resend,autoping,starred,blogpsw,useeditor,summaryway,blogalias,originsrc,comefrom,forcedraft,clearautosaver', 0, 'post');

	if ($ajax=='on') {
		$itemid=-1;
		$job='restore';
	}

	//Get content
	$content=$_POST['content'];
	//If magic quotes is on, strip the slashes automatically added
	if ($mqgpc_status==1) $content=stripslashes($content);

	if ($ajax!='on' && ($title=='' || $content==''))  {
		$cancel=$lna[307];
	} else if($title=='') {
		$title=$lna[1172];
	}
	if ($job=='restore' && $records['authorid']!=$userdetail['userid'] && $permission['EditSafeMode']!=1) {
		$cancel=$lna[308];
	}
	if ($permission['PinEntry']!=1 && $sticky!=0) {
		$cancel=$lna[309];
	}

	catcherror ($cancel);

	$property=@floor($property);
	$category=@floor($category);
	$sticky=@floor($sticky);
	$htmlstat=@floor($html);
	$ubbstat=@floor($ubb);
	$emotstat=@floor($emot);
	$blogid=@floor($blogid);
	$starred=@floor($starred);
	$summaryway=@floor($summaryway);

	if ($categories[$category]['cateproperty']==1) $property=2;
	if (($forcedraft==1 && $property!=4) || $ajax=='on') $property=3;
	if ($forcedraft==2 && $ajax!='on') $property=0;

	if ($autobr==0) {
		$content=str_replace("\r",'',$content);  //Disable auto linebreak in WYSIWYG editors
	}
	if ($callaftersubmit) {
		$content=call_user_func ($callaftersubmit, $content);
	}

	$content=preg_replace("/\[php\](.+?)\[\/php\]/ise", "phpcode3('\\1')", $content);
	if ($htmlstat!=1 || $permission['Html']!=1) {
		$content=preg_replace("/\[code\](.+?)\[\/code\]/ise", "phpcode2('\\1')", $content);
		$content=safe_convert($content, 0, 1);
	} else {
		$content=preg_replace("/\[code\](.+?)\[\/code\]/ise", "phpcode('\\1')", $content);
		$content=safe_convert($content, 1, 1);
	}

	if ($summaryway==1) {
		//Get content-summary
		$entrysummary=$_POST['entrysummary'];
		//If magic quotes is on, strip the slashes automatically added
		if ($mqgpc_status==1) $entrysummary=stripslashes($entrysummary);
		if ($autobr==0) {
			$entrysummary=str_replace("\r",'',$entrysummary);  //Disable auto linebreak in WYSIWYG editors
		}
		if ($callaftersubmit) {
			$entrysummary=call_user_func ($callaftersubmit, $entrysummary);
		}
		$entrysummary=preg_replace("/\[php\](.+?)\[\/php\]/ise", "phpcode3('\\1')", $entrysummary);
		if ($htmlstat!=1 || $permission['Html']!=1) {
			$entrysummary=preg_replace("/\[code\](.+?)\[\/code\]/ise", "phpcode2('\\1')", $entrysummary);
			$entrysummary=safe_convert($entrysummary, 0, 1);
		} else {
			$entrysummary=preg_replace("/\[code\](.+?)\[\/code\]/ise", "phpcode('\\1')", $entrysummary);
			$entrysummary=safe_convert($entrysummary, 1, 1);
		}
	} else $entrysummary='';

	$frontpage=($summaryway==2) ? 1 : 0;

	$title=safe_convert(stripslashes($title));

	if ($comefrom && $originsrc) {
		$comefrom=safe_convert($comefrom);
		$originsrc=safe_convert($originsrc);
	} else {
		$comefrom=$originsrc='';
	}

	if ($ajax!='on') {
		$blogalias=blogalias_convert($blogalias);
		if ($blogalias=='') {
			$deletealias=true;
		} else {
			if ($job=='restore') $findalias_plus="AND `blogid`<>'{$records['blogid']}'";
			$findalias=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE `blogalias`='{$blogalias}' {$findalias_plus} LIMIT 1");
			if ($findalias[0]['blogalias']==$blogalias) $blogalias.='_'.rand(1000,9999);
			$deletealias=false;
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
	}

	if ($pinged) $pinged=safe_convert($pinged);
	if (is_array($permitgp)) {
		$permitgp=array_diff(array_keys($usergp), $permitgp);
		$permitgp=array_values($permitgp);
		$permitgp=@implode('|', $permitgp);
	}

	$currenttime=time();
	$currentuserid=$userdetail['userid'];

	if ($changemytime==1) {
		acceptrequest('newyear,newmonth,newday,newhour,newmin,newsec');
		$finaltime=gmmktime($newhour,$newmin,$newsec,$newmonth,$newday,$newyear)-$config['timezone']*3600;
	} elseif ($job=='store') $finaltime=$currenttime;
	elseif ($ajax!='on') $finaltime=$records['pubtime'];
	else $finaltime=$currenttime;
	if ($finaltime<=$currenttime && $property==4) $property=0; //Already should be published

	$content=plugin_walk('storecontent', $content); //load plugin

	$blog->query("DELETE FROM `{$db_prefix}blogs` WHERE `blogid`=-1");

	if ($job=='store') {
		$currentid=$maxrecord['maxblogid']+1;
		$query="INSERT INTO `{$db_prefix}blogs` VALUES ('{$currentid}', '{$title}','{$finaltime}','{$currentuserid}', 0, 0, 0, '{$property}','{$category}','{$tags}','{$sticky}','{$htmlstat}', '{$ubbstat}', '{$emotstat}', '{$content}', '0', '0', '{$sweather}', '0', '{$pinged}', '{$permitgp}', '{$starred}', '{$blogpsw}', '{$frontpage}', '{$entrysummary}', '{$comefrom}', '{$originsrc}', '{$blogalias}')";
	} else {
		$currentid=$itemid;
		if ($tags || $records['tags']!='' && $ajax!='on') {
			$oldtags=@explode('>', trim($records['tags'],'>'));
			$oldtags_query="'".@implode("', '", $oldtags)."'";
			if ($oldtags_query!="''") $blog->query("UPDATE `{$db_prefix}tags` SET tagentry=replace(tagentry, ',{$currentid},', ','), tagcounter=tagcounter-1 WHERE tagname in({$oldtags_query})"); //Remove all records containing this entry
		}
		if ($currentid==-1) {
			$query="INSERT INTO `{$db_prefix}blogs` VALUES ('{$currentid}', '{$title}','{$finaltime}','{$currentuserid}', 0, 0, 0, '{$property}','{$category}','{$tags}','{$sticky}','{$htmlstat}', '{$ubbstat}', '{$emotstat}', '{$content}', '0', '0', '{$sweather}', '0', '{$pinged}', '{$permitgp}', '{$starred}', '{$blogpsw}', '{$frontpage}', '{$entrysummary}', '{$comefrom}', '{$originsrc}', '{$blogalias}')";
		}
		else {
			$query="UPDATE `{$db_prefix}blogs` SET title='{$title}', pubtime='{$finaltime}', property='{$property}', category='{$category}', tags='{$tags}', sticky='{$sticky}', htmlstat='{$htmlstat}', ubbstat='{$ubbstat}', emotstat='{$emotstat}', content='{$content}',  editorid='{$currentuserid}', edittime='{$currenttime}', weather='{$sweather}', pinged='{$pinged}', permitgp='{$permitgp}', starred='{$starred}', blogpsw='{$blogpsw}', frontpage='{$frontpage}', entrysummary='{$entrysummary}', comefrom='{$comefrom}', originsrc='{$originsrc}', blogalias='{$blogalias}' WHERE `blogid`='{$id}'";
		}
	}
	$blog->query($query);

	if ($ajax!='on') {
		//blog alias & advanced rewrite
		if ($job=='store') {
			if (!$deletealias && $config['urlrewritemethod']==1) {
				$redirect_new="<?php\nchdir('../');\n\$entryid={$currentid};\ninclude('read.php');";
				writetofile("post/{$blogalias}.php", $redirect_new);
			}
		} else {
			if ($deletealias && $records['blogalias']!='' && $config['urlrewritemethod']==1) {
				@unlink("post/{$records['blogalias']}.php");
			}
			elseif (!$deletealias && $blogalias!=$records['blogalias'] && $config['urlrewritemethod']==1) {
				if ($records['blogalias']!='') @unlink("post/{$records['blogalias']}.php");
				$redirect_new="<?php\nchdir('../');\n\$entryid={$id};\ninclude('read.php');";
				writetofile("post/{$blogalias}.php", $redirect_new);
			}
		}
	}

	if ($tags || $records['tags']!='') {
		$newtags=@array_diff($tags_array_all, $exist_tags_all);
		$newtags=array_values($newtags); //Kill all keys
		$modifytags=@array_diff($tags_array_all, $newtags);
		$modifytags_query="'".@implode("', '", $modifytags)."'";
		$blog->query("UPDATE `{$db_prefix}tags` SET  tagentry=replace(tagentry, '<end>', '{$currentid},<end>'), tagcounter=tagcounter+1 WHERE tagname in({$modifytags_query})");
		for ($m=0; $m<count($newtags); $m++) {
			if ($newtags[$m]!=='') $blog->query("INSERT INTO `{$db_prefix}tags` VALUES ('{$currenttime}', '{$newtags[$m]}', 1, '<tag>,{$currentid},<end>', '')");
		}
	}
	@writetofile("data/cache_tags.php", trim($existtagall.' '.@implode(' ',$newtags))); //Update all tags cache

	if ($job=='store') {
		$blog->query("UPDATE `{$db_prefix}maxrec` SET maxblogid={$currentid}");
		$blog->query("UPDATE `{$db_prefix}counter` SET entries=entries+1");
		$newcym=gmdate("Ym", $finaltime+$config['timezone']*3600);
		$newcd=gmdate("d", $finaltime+$config['timezone']*3600);
		$blog->query("INSERT INTO `{$db_prefix}calendar` VALUES ('{$newcym}', '{$newcd}', '{$currentid}', '')");
		recache_currentmonthentries();
	}
	if ($job=='restore' && $changemytime==1) {
		$newcym=gmdate("Ym", $finaltime+$config['timezone']*3600);
		$newcd=gmdate("d", $finaltime+$config['timezone']*3600);
		$blog->query("UPDATE `{$db_prefix}calendar` SET cyearmonth='{$newcym}', cday='{$newcd}' WHERE `cid`='{$id}'");
		recache_currentmonthentries();
	}
	recache_latestentries (); //Update Latest Entry Cache
	recache_categories(); //Update Category counter
	
	if ($job=='restore' && $property!=$records['property']) recache_latestreplies();

	if ($clearautosaver=='1') $blog->query("DELETE FROM `{$db_prefix}blogs` WHERE `blogid`=-1");

	$backtowhere=($property==3) ? $backtodraft : $partbacktoart;
	if ($ajax=='on') {
		catchsuccess('');
	}
	if (($job=='store' && !$pinged) || ($job=='restore' && $resend!=1) || ($job=='restore' && !$pinged)) {
		catchsuccess ($finishok, array("{$backtowhere}|".get_entry_url($currentid, $blogalias), $backtoaddnew));
	}
	else {
		if ($htmlstat==1) $excerpt=tb_convert($content);
		else $excerpt=tb_no_quote($content);
		$ping_show=@explode(' ', $pinged);
		for ($i=0; $i<count($ping_show); $i++) {
			$ping_urls.="<input type='hidden' name='pingurl[]' value='{$ping_show[$i]}'>";
		}
		$ping_url_show=@implode('<br>', $ping_show);
		$form="<div align=center><form action='admin.php?go=edit_sendtb' method='post'><input type='hidden' name='title' value=\"{$title}\"><input type='hidden' name='excerpt' value=\"{$excerpt}\"><input type='hidden' name='blog_name' value=\"{$config['blogname']}\"><input type='hidden' name='url' value='{$config['blogurl']}/".get_entry_url($currentid, $blogalias)."'>{$ping_urls}<input type='submit' value='{$lna[310]}' class='formbutton'> <input type='button' value='{$lna[311]}' onclick='window.location=(\"".get_entry_url($currentid, $blogalias)."\");' class='formbutton'></form></div>";
		$t=new template;
		$t->showtips($lna[312],$lna[313].$ping_url_show."<br><br>{$lna[314]}<br><br>".$form, "{$backtowhere}|".get_entry_url($currentid, $blogalias));
	}
}


if ($job=='sendtb') {
	checkpermission('EditEntry');
	acceptrequest('title,excerpt,url,blog_name,pingurl');
	if (!is_array($pingurl)) catcherror($lna[315]);
	plugin_runphp('trackbacksending');
	@header("Content-Type: text/html; charset=utf-8");
	$url=str_replace('{host}', $_SERVER['HTTP_HOST'], $url);
	foreach ($pingurl as $durl) {
		$result=sendping ($durl, $title, $excerpt, $url, $blog_name);
		if (!$result) $showp.="<b>{$lna[316]}</b>{$durl} ; <b>{$lna[317]}</b>{$lna[318]}";
		elseif ($result=='ok') $showp.="<b>{$lna[316]}</b>{$durl} ; <b>{$lna[317]}</b>{$lna[319]}<br>";
		elseif ($result=='unknown')  $showp.="<b>{$lna[316]}</b>{$durl} ; <b>{$lna[317]}</b>{$lna[949]}<br>";
		else  $showp.="<b>{$lna[316]}</b>{$durl} ; <br><b>{$lna[317]}</b>{$lna[950]}{$result}<br>";
	}
	plugin_runphp('trackbacksent');
	$t=new template;
	$t->showtips("{$lna[320]}","{$lna[321]}<br><br>".$showp."<br><br>{$lna[322]}","{$partbacktoart}|{$url}");
}

function autoselect ($name, $arrayoption, $arrayvalue, $selectedid=0, $disabled=0) {
	if (empty($selectedid)) $selectedid=0;
	if ($disabled==1) $wdisabled=" disabled='disabled' ";
	$formcontent.="<select name='{$name}' id='{$name}' class='formselect' {$wdisabled}>";
	for ($i=0; $i<count($arrayoption); $i++) {
		if ($selectedid==$i) $wselected="selected='selected'";
		else $wselected='';
		$formcontent.="<option value='{$arrayvalue[$i]}' {$wselected}>{$arrayoption[$i]}</option>";
	}
	$formcontent.="</select>";
	return $formcontent;
}

function autoradio ($type, $name, $arraylabel, $arrayvalue, $arraychecked=array(), $arraydisabled=array()) {
	if ($type!='checkbox' && $type!='radio') return;
	for ($i=0; $i<count($arraylabel); $i++) {
		if ($arraychecked[$i]==1) $addcheck="checked='checked'";
		else $addcheck='';
		if ($arraydisabled[$i]==1) $disabled="disabled='disabled'";
		else $disabled='';
		if ($type=='checkbox') $disabled.=" id='{$name}' ";
		$formcontent.="<label><input type='{$type}' name='{$name}' value='{$arrayvalue[$i]}' {$addcheck} class='formradiobox' {$disabled}/>{$arraylabel[$i]}</label> ";
	}
	return $formcontent;
}

function sendping ($url, $title, $excerpt, $blog_url, $blog_name) {
	$blog_url=str_replace('{host}', $_SERVER['HTTP_HOST'], $blog_url);
	$trackback_url=parse_url($url);
	$out="POST {$trackback_url['path']}".($trackback_url['query'] ? '?'.$trackback_url['query'] : '')." HTTP/1.0\r\n";
	$out.="Host: {$trackback_url['host']}\r\n";
	$out.="Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";
	$query_string="nouse=nouse&title=".urlencode($title)."&url=".urlencode($blog_url)."&blog_name=".urlencode($blog_name)."&excerpt=".urlencode($excerpt);
	$out.='Content-Length: '.strlen($query_string)."\r\n";
	$out.="User-Agent: Bo-Blog\r\n\r\n";
	$out.=$query_string;
	if ($trackback_url['port']=='') $trackback_url['port']=80;
	$fs=fsockopen($trackback_url['host'], $trackback_url['port'], $errno, $errstr, 10);
	if (!$fs) return false;
	fputs($fs, $out);
	$http_response = '';
	while(!feof($fs)) {
		$http_response .= fgets($fs, 128);
	}
	@fclose($fs);
	@list($http_headers, $http_content) = @explode("\r\n\r\n", $http_response);
	if (strstr($http_content, "<error>0</error>")) return ("ok");
	elseif (preg_match("/<message>(.+?)<\/message>/is", $http_content, $messages)==1) {
		return (htmlspecialchars($messages[1]));
	}
	//writetofile("data/trackbacklog.txt", $http_content);
	else return (htmlspecialchars($http_content));
}

/*function technorati () {
	global $config;
	$query_string="<?xml version=\"1.0\"?>";
	$query_string.=<<<eot
<methodCall>
  <methodName>weblogUpdates.ping</methodName>
  <params>
    <param>
      <value>{$config['blogname']}</value>
    </param>
    <param>
      <value>{$config['blogurl']}/</value>
    </param>
  </params>
</methodCall>
eot;
	$out.='POST /rpc/ping HTTP/1.0'."\r\n";
	$out.="User-Agent: Bo-Blog\r\n\r\n";
	$out.="Host: rpc.technorati.com\r\n";
	$out.="Content-Type: text/xml\r\n";
	$out.="Content-length: ".strlen($query_string)."\r\n\r\n";
	$out.=$query_string;
	$fs=fsockopen("rpc.technorati.com/rpc/ping", 80, $errno, $errstr, 10);
	if (!$fs) return false;
	fputs($fs, $out);
	@fclose($fs);
	return true;
}
*/
function tb_convert ($str) {
	$str=tb_no_quote(strip_tags($str));
	$str=preg_replace("/&(.+?);/is", "", $str);
	$str=preg_replace("/\[(.+?)\]/is", "", $str);
	return $str;
}

function tb_no_quote($str) {
	$str=str_replace("'", '', $str);
	$str=str_replace("\"", '', $str);
	$str=str_replace("\\", '', $str);
	return $str;
}

?>