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
$finishok=$lna[1095];
$backtoaddnew="{$lna[1056]}|admin.php?go=page_pagewrite";
$backtomanage="{$lna[1057]}|admin.php?go=entry_pagemanage";

if (!$job) $job='pagewrite';
$id=$itemid;

include_once ("data/cache_emot.php");


checkpermission('PageWrite');
if ($job=='pagemanage') checkpermission('CP');
confirmpsw(); //Re-check password


if ($job=='editpage' || $job=='restore') {
	if ($id=="") $cancel=$lna[127];
	else {
		$records=$blog->getbyquery("SELECT * FROM `{$db_prefix}pages` WHERE `pageid`='{$id}' LIMIT 1");
		if ($records['pageid']=='') {
			$cancel=$lna[127];
		}
		$records['pagecontent']=safe_invert($records['pagecontent'], $records['htmlstat']);
		$records['pagecontent']=preg_replace("/\[php\](.+?)\[\/php\]/ise", "phpcode4('\\1')", $records['pagecontent']);
	}
}

catcherror ($cancel);

if ($job=='pagewrite' || $job=='editpage') { //Initialize public items
	$currentjob=basename($_SERVER['QUERY_STRING']);
	@list($currentjob, $unuse)=@explode('&useeditor=', $currentjob);

	$arrayoption_sidebar=array($lna[534], $lna[535]);
	$arrayvalue_sidebar=array(0, 1);
	$arrayoption_editors=array('QuickTags', $lna[568], $lna[1017], $lna[711]);
	$arrayvalue_editors=array('quicktags', 'ubb', 'tinymce', 'custom');
}

if ($job=='editpage') { //Initialize Edit only items
	$selectedid_sidebar=array_search($records['closesidebar'], $arrayvalue_sidebar);
	$records['pagecontent']=stripslashes($records['pagecontent']);
	$hiddenareas="<input type='hidden' name='go' id='go' value='page_restore_{$records['pageid']}'/>";
	$hiddenareas.="<input type='hidden' name='idforsave' id='idforsave' value='{$records['pageid']}'/>";
	$hiddenareas.="<input type='hidden' name='oldgo' id='oldgo' value='{$currentjob}'/>";
}

if ($job=='pagewrite') { //Initialize Add only items
	if ($permission['Html']==1) $records['htmlstat']=1;
	if ($permission['Ubb']==1) $records['ubbstat']=1;
	if ($permission['Emot']==1) $records['emotstat']=1;
	$hiddenareas="<input type='hidden' name='go' id='go' value='page_store'/>";
	$hiddenareas.="<input type='hidden' name='idforsave' id='idforsave' value=''/>";
	$hiddenareas.="<input type='hidden' name='oldgo' id='oldgo' value='{$currentjob}'/>";

	$createlinks="<tr bgcolor=\"#ffffff\" align=left class=\"visibleitem\" valign=top><td width=100 align=center>{$lna[1125]}</td><td><input type=checkbox name=addshortcut value=1>{$lna[1124]}<br>{$lna[97]} <input type='radio' name='shortcuttarget' value='' checked>{$lna[98]} <input type='radio' name='shortcuttarget' value='_blank'>{$lna[99]}<br>{$lna[95]} <input type=text name='shortcutname' value='' size='50'> {$lna[102]}<br>{$lna[1126]}</td></tr>\n";
}

if ($job=='pagewrite' || $job=='editpage') { //Initialize public items
	if ($permission['Html']==1) $disablehtmlstatus=0;
	else $disablehtmlstatus=1;
	if ($permission['Ubb']==1) $disableubbstatus=0;
	else $disableubbstatus=1;
	if ($permission['Emot']==1) $disableemotstatus=0;
	else $disableemotstatus=1;
	if ($permission['PinEntry']==1) $disabled_sticky=0;
	else $disabled_sticky=1;
	$puttingsidebar=autoradio('checkbox', 'closesidebar', array($lna[1127]), array(1), array($records['closesidebar']), array(''));

	$selectedid_editors=array_search($useeditor, $arrayvalue_editors);
	$puttingeditors=autoselect('useeditor', $arrayoption_editors, $arrayvalue_editors, $selectedid_editors);


	$puttinghtml=autoradio('checkbox', 'html', array($lna[280]), array(1), array($records['htmlstat']), array($disablehtmlstatus));
	$puttingubb=autoradio('checkbox', 'ubb', array($lna[281]), array(1), array($records['ubbstat']), array($disableubbstatus));
	$puttingemot=autoradio('checkbox', 'emot', array($lna[282]), array(1), array($records['emotstat']), array($disableemotstatus));

	$editorbody=str_replace("{content}", $records['pagecontent'], $editorbody);



//Now Begins the main part
	$display_overall.=highlightadminitems('pagewrite', 'entry');
$display_overall.= <<<eot
<script type='text/javascript'>
function chktitle() {
	if (document.getElementById('title').value=='' || document.getElementById('title').value==null) {
		alert("{$lna[877]}");
	}
	else document.getElementById('realsubmit').click();
}
</script>
<form name='editentry' id='editentry' action='admin.php' method='post' enctype='multipart/form-data' 	{$submitjs}>{$hiddenareas}
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[1056]}
</td>
<td class="sectend">{$lna[1058]}</td>
</tr>
<tr>
<td colspan=2  class="sect">

<table width=100% cellpadding=4 cellspacing=1 align=center>
<tr bgcolor="#ffffff" align=left class="visibleitem">
<td width=100 align=center>{$lna[567]}</td><td>{$puttingeditors} <input type=button value="{$lna[64]}" onclick="changeeditor();"></td>
</tr>

<tr bgcolor="#ffffff" align=left class="hiddenitem">
<td width=100 align=center>{$lna[284]}</td><td><input type='text' name='pagetitle' id='title' value="{$records['pagetitle']}" size='50'  class='formtext'></td></tr>

<tr bgcolor="#ffffff" align=left class="visibleitem">
<td width=100 valign=top align=center>{$lna[287]}<br><div align=left>{$puttinghtml}<br>{$puttingubb}<br>{$puttingemot}<br>{$puttingsidebar}</div>
</td>
<td>
$editorbody
</td>
</tr>

<tr bgcolor="#ffffff" align=left valign=top class="hiddenitem">
<td width=100 align=center>{$lna[1117]}</td><td><input type=text name='pagealias' value="{$records['pagealias']}" size="50"> {$lna[102]}<br>
 {$lna[1118]}
</td>
</tr>

$createlinks

</table>

</td>
</tr>
<tr>
<td colspan=4 align=center class="sectbar">
<input type=button value="{$lna[64]}" onclick="chktitle();" class="formbutton"> <input type=reset value="{$lna[65]}" class="formbutton">
</td></tr>
</table>
<div style='visibility: hidden'><input type=submit value="{$lna[64]}" id='realsubmit' class='formbutton'></div>
</form>
eot;
}

if ($job=='store' || $job=='restore') {
	acceptrequest('pagetitle,closesidebar,html,ubb,emot,useeditor,pagealias,addshortcut,shortcuttarget,shortcutname', 0, 'post');

	//Get content
	$content=$_POST['content'];
	//If magic quotes is on, strip the slashes automatically added
	if ($mqgpc_status==1) $content=stripslashes($content);

	if ($pagetitle=='' || $content=='')  {
		$cancel=$lna[307];
	}

	catcherror ($cancel);

	$closesidebar=@floor($closesidebar);
	$htmlstat=@floor($html);
	$ubbstat=@floor($ubb);
	$emotstat=@floor($emot);
	$pageid=@floor($id);

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

	$pagetitle=safe_convert(stripslashes($pagetitle));
	$shortcutname=(!$shortcutname) ? $pagetitle : safe_convert(stripslashes($shortcutname));

	$pagealias=blogalias_convert($pagealias);
	if ($pagealias=='') {
		$deletealias=true;
	} else {
		if ($job=='restore') $findalias_plus="AND `pageid`<>'{$records['pageid']}'";
		$findalias=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}pages` WHERE `pagealias`='{$pagealias}' {$findalias_plus} LIMIT 1");
		if ($findalias[0]['pagealias']==$pagealias) $pagealias.='_'.rand(1000,9999);
		$deletealias=false;
	}
	
	$currentuserid=$userdetail['userid'];

	$finaltime=time();

	if ($job=='store') {
		$query="INSERT INTO `{$db_prefix}pages` (pageid,pagetitle,pagecontent,pageauthor,pagetime,pageedittime,closesidebar,htmlstat,ubbstat,emotstat,pagealias) VALUES (null, '{$pagetitle}','{$content}','{$currentuserid}','{$finaltime}', 0, '{$closesidebar}','{$htmlstat}', '{$ubbstat}', '{$emotstat}', '{$pagealias}')";
		$blog->query($query);

		$currentid=db_insert_id();
		//Add shortcuts on the top ,shortcuttext,shortcuttarget
		if ($addshortcut==1) {

			$shortcuttext=$shortcutname;
			$shortcuttarget=($shortcuttarget=='') ? '' : ", 'target'=>'".admin_convert($shortcuttarget)."'";

			$infotoadd="\$blogitem['pageshortcut{$currentid}']=array('type'=>'link', 'name'=>'pageshortcut{$currentid}', 'url'=>'".getlink_pages($currentid, $pagealias)."', 'text'=>'{$shortcuttext}' {$shortcuttarget});";
			$maxmodid=$blog->countbyquery("SELECT MAX(`modorder`) FROM `{$db_prefix}mods`");
			$maxmodid+=1;
			$intro="{$lna[1128]}{$shortcuttext}";
			$blog->query("INSERT INTO `{$db_prefix}mods` VALUES ('header', 'pageshortcut{$currentid}', '{$intro}', '1', '$maxmodid', 'custom')");
			recache_mods ();
			mod_append ($infotoadd);
		}
	} else {
		$currentid=$itemid;
		$query="UPDATE `{$db_prefix}pages` SET pagetitle='{$pagetitle}', pageedittime='{$finaltime}', closesidebar='{$closesidebar}', htmlstat='{$htmlstat}', ubbstat='{$ubbstat}', emotstat='{$emotstat}', pagecontent='{$content}', pagealias='{$pagealias}' WHERE `pageid`='{$id}'";
		$blog->query($query);
	}

	catchsuccess ($finishok, array($backtoaddnew, $backtomanage));
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
		$formcontent.="<label><input type='{$type}' name='{$name}' value='{$arrayvalue[$i]}' {$addcheck} class='formradiobox' {$disabled}/>{$arraylabel[$i]}</label> ";
	}
	return $formcontent;
}


?>