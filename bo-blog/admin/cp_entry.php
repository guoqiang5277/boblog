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
//Section: Blog Posting
if ($job=='write') {
	header ("Location: admin.php?go=edit");
	exit();
}

checkpermission('CP');
confirmpsw(); //Re-check password
//Define some senteces
$finishok=$lna[323];
$backtodefault="{$lna[324]}|admin.php?go=entry_default";
$backtodraft="{$lna[325]}|admin.php?go=entry_draft";
$backtoaddnew="{$lna[326]}|admin.php?go=entry_write";

include_once("data/cache_adminlist.php");

if ($job=='' || $job=="default") {
	acceptrequest('category,property,keyword,timeperiod');
	$timeperiod=floor($timeperiod);
	$keyword=safe_convert($keyword);
	if ($keyword==$lna[1129]) $keyword='';

	$adminselection="<select name='chnadm'>";
	foreach ($adminlist as $adk=>$adn) {
		$adminselection.="<option value='{$adk}'>{$adn}</option>";
	}
	$adminselection.="</select>";

	$propertysel=$timeperiodsel=array();

	$propertysel[$property]="selected";
	$adminselection2="<select name=\"property\"><option value='10' {$propertysel[10]}>{$lna[1130]}</option><option value=0 {$propertysel[0]}>{$lna[269]}</option><option value=1 {$propertysel[1]}>{$lna[270]}</option><option value=2 {$propertysel[2]}>{$lna[271]}</option></select>";

	$timeperiodsel[$timeperiod]="selected";
	$adminselection4="<select name=\"timeperiod\"><option value='0' {$timeperiodsel[0]}>{$lna[1131]}</option><option value=7 {$timeperiodsel[7]}>{$lna[1132]}</option><option value=30 {$timeperiodsel[30]}>{$lna[1133]}</option><option value=90 {$timeperiodsel[90]}>{$lna[1134]}</option><option value=180 {$timeperiodsel[180]}>{$lna[1135]}</option><option value=365 {$timeperiodsel[365]}>{$lna[1136]}</option></select>";

	$queryplus=($property==10 || $property==="") ? "`property`<3" : "`property`=".floor($property);
	$queryplus.=($category==="") ? '' : " AND `category`='".floor($category)."'";
	$queryplus.=($timeperiod==0) ? '' : " AND `pubtime`>='".(time()-$timeperiod*3600*24)."'";
	$queryplus.=($keyword) ? " AND `title` LIKE '%{$keyword}%'" : '';

	if ($keyword=='') $adminselection3="<input type=\"text\" name=\"keyword\" size=\"29\" value=\"{$lna[1129]}\" style=\"font-style: italic;\" onclick=\"if (this.value=='{$lna[1129]}') {this.value=''; this.style.fontStyle='normal';}\" onblur=\"if (this.value=='') {this.value='{$lna[1129]}'; this.style.fontStyle='italic';}\" />";
	else $adminselection3="<input type=\"text\" name=\"keyword\" size=\"29\" value=\"{$keyword}\" style=\"font-style: normal;\" onclick=\"if (this.value=='{$lna[1129]}') {this.value=''; this.style.fontStyle='normal';}\" onblur=\"if (this.value=='') {this.value='{$lna[1129]}'; this.style.fontStyle='italic';}\" />";


	$start_id=($page-1)*$adminitemperpage;
	$detail_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE {$queryplus}  ORDER BY `pubtime` DESC LIMIT $start_id, $adminitemperpage");
	for ($i=0; $i<count($detail_array); $i++) {
		$tmp_gp=$detail_array[$i]['category'];
		$tmp_st=$detail_array[$i]['property'];
		$tmp_tm=gmdate('Y/m/d H:i', $detail_array[$i]['pubtime']+3600*$config['timezone']);
		if ($tmp_st || $tmp_st==3) $addclass='hiddenitem';
		else $addclass='visibleitem';
		$hiddensign_s=array(0=>"openblog.gif", 1=>"lockblog.gif", 2=>"secretblog.gif", 3=>"draft.gif");
		$hiddensign_p=array(0=>$lna[269], 1=>$lna[270], 2=>$lna[271], 3=>$lna[272]);
		$hiddensign="<img src='admin/theme/{$themename}/{$hiddensign_s[$tmp_st]}' alt='' title='{$hiddensign_p[$tmp_st]}'>";
		$tablebody.="<tr class='$addclass'><td align='center'><input type='checkbox' name='selid[]' id='selid[]' value='{$detail_array[$i]['blogid']}'></td><td align='center'>{$hiddensign}</td><td>{$detail_array[$i]['title']}</td><td>{$tmp_tm}</td><td align='center'>{$categories[$tmp_gp]['catename']}</td><td align='center'><a href='javascript: ensuredel(\"{$detail_array[$i]['blogid']}\", \"1\");'><img src='admin/theme/{$themename}/del.gif' alt='{$lna[78]}' title='{$lna[78]}' border='0'></a></td><td align='center'><a href='admin.php?go=edit_edit_{$detail_array[$i]['blogid']}'><img src='admin/theme/{$themename}/edit.gif' alt='{$lna[77]}' title='{$lna[77]}' border='0'></a></td></tr>";
	}
	for ($i=0; $i<sizeof($arrayvalue_categories); $i++) {
		$selected=($arrayvalue_categories[$i]===$category) ? ' selected' : '';
		$puttingcates.="<option value='{$arrayvalue_categories[$i]}'{$selected}>{$arrayoption_categories[$i]}</option>";
	}
	$numenries=$blog->countbyquery("SELECT COUNT(*) FROM `{$db_prefix}blogs` WHERE {$queryplus}");
	$pagebar=gen_page ($page, 5, "admin.php?go=entry_default&category={$category}&property={$property}&timeperiod={$timeperiod}&keyword=".urlencode($keyword), $numenries, $adminitemperpage);
	$display_overall.=highlightadminitems('default', 'entry');
$display_overall_plus= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[3]}
</td>
<td class="sectend">{$lna[327]}</td>
</tr>
</table>

<form action="admin.php?go=entry_default" method="post">
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr><td colspan=7>
<select name="category"><option value=''>{$lna[328]}</option>$puttingcates</select>  $adminselection2 $adminselection4 $adminselection3 <input type=submit value="{$lna[244]}" class='formbutton'></td></tr>
<tr><td colspan=7 height=10></td></tr>
</table>
</form>

<form action="admin.php?go=entry_batch" method="post" id='f_s' name='f_s'>
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr align=center class="admintitle">
<td width=35 align=center>{$lna[245]}</td><td width=35>{$lna[297]}</td><td align=center>{$lna[284]}</td><td width=200 align=center>{$lna[288]}</td><td width=80 align=center>{$lna[285]}</td><td width=35 align=center>{$lna[78]}</td><td width=35 align=center>{$lna[77]}</td></tr>
{$tablebody}
<tr><td colspan=3><a href="#unexist" onclick="checkallbox('f_s', 'checked');">{$lna[247]}</a> | <a href="#unexist" onclick="checkallbox('f_s', '');">{$lna[248]}</a></td><td colspan=4 align=right>$pagebar</td></tr>
<tr><td colspan=7 height=20></td></tr>
<tr class="adminoption"><td colspan=7>{$lna[249]}<br><input type=radio name=opt value='del' onclick="document.getElementById('f_s').submit();">{$lna[78]} <input type=radio name=opt value='noreply'onclick="document.getElementById('f_s').submit();">{$lna[329]} <input type=radio name=opt value='sticky'>{$lna[330]} <input type=radio name=opt value='unsticky'>{$lna[331]}  <br><input type=radio name=opt value='changeauthor'>{$lna[873]}{$adminselection} <input type=radio name=opt value='move'>{$lna[250]}<select name="newcategory">$puttingcates</select>  <input type=radio name=opt value='newppt'>{$lna[332]}<select name="newproperty"><option value=0>{$lna[269]}</option><option value=1>{$lna[270]}</option><option value=2>{$lna[271]}</option></select><br> <input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax('f_s');">
</td></tr>
</table>
</form>

<br><br><div align=center width=70%>{$lna[333]}<img src='admin/theme/{$themename}/openblog.gif'>{$lna[269]} <img src='admin/theme/{$themename}/lockblog.gif'>{$lna[270]} <img src='admin/theme/{$themename}/secretblog.gif'>{$lna[271]} </div>
<br>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='batch') {
	acceptrequest('opt,newcategory,newproperty,selid,confirm,chnadm');
	if (!$opt) $cancel="{$lna[251]}";
	if (!$selid) $cancel="{$lna[205]}";
	catcherror($cancel);
	if (($opt=='del' || $opt=='noreply')&&$confirm!=1) {
		$convey.="<form action='admin.php?go=entry_batch' method='post'>{$lna[335]}";
		$convey.="<input type='hidden' name='opt' value='{$opt}'>";
		$convey.="<input type='hidden' name='confirm' value='1'>";
		for ($i=0; $i<count($selid); $i++) {
			$convey.="<input type='hidden' name='selid[]' value='{$selid[$i]}'>";
		}
		$convey.="<br/><br/><div align=center><input type=submit value='{$lna[64]}' class='formbutton'> <input type=button onclick='window.location=(\"admin.php?act=entry\");' value='{$lna[138]}' class='formbutton'></div>";
		$convery.="</form>";
		$m_b=new template;
		$m_b->showtips($lna[336], $convey);
	}

	$selid=array_values(array_unique($selid));
	$batch_id=makeaquery($selid, "`blogid`='%s'", 'OR');
	if ($opt=='del' || $opt=='deldraft') {
		$tags_affected_raw=$blog->getarraybyquery("SELECT blogid, tags FROM `{$db_prefix}blogs` WHERE ({$batch_id}) AND tags<>'' AND tags<>'>'");
		for ($i=0; $i<count($tags_affected_raw['blogid']); $i++) {
			$tmp_tag=@explode('>', trim($tags_affected_raw['tags'][$i],'>'));
			$tmp_tag_q=makeaquery($tmp_tag, "`tagname`='%s'", 'OR');
			$tmp_query="UPDATE `{$db_prefix}tags` SET tagentry=replace(tagentry, ',{$tags_affected_raw['blogid'][$i]},', ','), tagcounter=tagcounter-1 WHERE {$tmp_tag_q}";
			$blog->query($tmp_query);
			unset ($tmp_query, $tmp_tag_q, $tmp_tag);
		}
		$queryact="DELETE FROM `{$db_prefix}blogs` WHERE {$batch_id}";
	}
	elseif ($opt=='move') $queryact="UPDATE `{$db_prefix}blogs` SET `category`='{$newcategory}' WHERE {$batch_id}";
	elseif ($opt=='sticky') $queryact="UPDATE `{$db_prefix}blogs` SET `sticky`='1' WHERE {$batch_id}";
	elseif ($opt=='unsticky') $queryact="UPDATE `{$db_prefix}blogs` SET `sticky`='0' WHERE {$batch_id}";
	elseif ($opt=='newppt') $queryact="UPDATE `{$db_prefix}blogs` SET `property`='{$newproperty}' WHERE {$batch_id}";
	elseif ($opt=='noreply') $queryact="DELETE FROM `{$db_prefix}replies` WHERE {$batch_id}";
	elseif ($opt=='publish') $queryact="UPDATE `{$db_prefix}blogs` SET `property`='0' WHERE {$batch_id}";
	elseif ($opt=='changeauthor') $queryact="UPDATE `{$db_prefix}blogs` SET `authorid`='{$chnadm}' WHERE {$batch_id}";
	$blog->query($queryact);
	if ($opt=='del' || $opt=='deldraft') {
		$delednum=db_affected_rows();
		$statistics['entries']=$statistics['entries']-$delednum;
		$blog->query("UPDATE `{$db_prefix}counter` SET `entries`={$statistics['entries']}");
		if ($opt=='del') {
			$queryact="DELETE FROM `{$db_prefix}replies` WHERE {$batch_id}";
			$blog->query($queryact);
			$countreps=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`<>2 AND `reproperty`<>3 AND `reproperty`<>4");
			$blog->query("UPDATE `{$db_prefix}counter` SET `replies`='{$countreps}'");
			recache_latestentries();
			if ($opt=='del') {
				$batch_id=str_replace('blogid', 'cid', $batch_id);
				$blog->query("DELETE FROM `{$db_prefix}calendar` WHERE {$batch_id}");
				recache_currentmonthentries();
			}
		}
		//catchsuccess($finishok, $backtodefault);
		//exit();
	}
	if ($opt=='noreply') {
		$countreps=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`<=1");
		$blog->query("UPDATE `{$db_prefix}counter` SET `replies`='{$countreps}'");
		recache_latestreplies();
	}

	if ($opt=='del' || $opt=='deldraft' || $opt=='move') recache_categories(); //Update Category counter

	if ($opt=='publish') {
		recache_latestentries();
	}
	
	if ($ajax=='on' && $opt!='del' && $opt!='deldraft') {
		$fetchURL=($opt=='publish') ? 'admin.php?go=entry_draft' : 'admin.php?go=entry_default';
		catchsuccessandfetch($finishok, $fetchURL);
	}
	else catchsuccess($finishok, $backtodefault);
}

if ($job=='deleteblog' || $job=='deletedraft') {
	acceptrequest('returnurl');
	if ($returnurl) $backtodefault="{$lna[344]}|{$returnurl}";
	if ($itemid=='') catcherror($lna[337]);
	else {
		$detail=$blog->getbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE `blogid`='{$itemid}'");
		if ($detail['blogid']!=$itemid) {
			if ($itemid==-1) {
				catchsuccess();
			} else {
				catcherror($lna[337]);
			}
		}
		if ($job=='deleteblog') {
			$blog->query("DELETE FROM `{$db_prefix}replies` WHERE `blogid`='{$itemid}'");
			$countreps=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`<=1");
			$blog->query("UPDATE `{$db_prefix}counter` SET `replies`='{$countreps}'");
		}
		if ($detail['tags']) {
			$oldtags=@explode('>', trim($detail['tags'], '>'));
			$oldtags_query="'".@implode("', '", $oldtags)."'";
			$blog->query("UPDATE `{$db_prefix}tags` SET tagentry=replace(tagentry, ',{$currentid},', ','), tagcounter=tagcounter-1 WHERE tagname in({$oldtags_query})"); //Remove 
		}
		$blog->query("DELETE FROM `{$db_prefix}blogs` WHERE `blogid`='{$itemid}'");
		$blog->query("DELETE FROM `{$db_prefix}calendar` WHERE `cid`='{$itemid}'");
		recache_currentmonthentries();
		recache_categories();
		$blog->query("UPDATE `{$db_prefix}counter` SET `entries`=`entries`-1");
		if ($job=='deleteblog') {
			recache_latestentries();
			recache_latestreplies();
		}
		if ($ajax=='on') {
			catchsuccessandfetch($finishok, $returnurl);
		}
		else catchsuccess($finishok, array($backtodefault, $backtodraft));
	}
	catcherror ($cancel);
}

if ($job=='draft') {
	$detail_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE `property`>=3 AND `blogid`>-1 ORDER BY `pubtime` DESC");
	if (count($detail_array)==0) $tablebody.="<tr class='visibleitem'><td colspan='9' align='center' height='100'>{$lna[338]}</td></tr>";
	else {
	for ($i=0; $i<count($detail_array); $i++) {
		$addclass=($i%2==1) ? 'hiddenitem' : 'visibleitem';
		$tmp_gp=$detail_array[$i]['category'];
		$tmp_tm=gmdate('Y/m/d H:i', $detail_array[$i]['pubtime']+3600*$config['timezone']);
		$hiddensign=($detail_array[$i]['property']==3) ? "<img src='admin/theme/{$themename}/draft.gif' alt='' title='{$lna[339]}'>" : "<img src='admin/theme/{$themename}/openblog.gif' alt='' title='{$lna[1111]}'>";
		if ($detail_array[$i]['property']==4) $detail_array[$i]['title'].=" [<b>{$lna[1174]}</b>]";
		$tablebody.="<tr class='$addclass'><td align='center'><input type='checkbox' name='selid[]' id='selid[]' value='{$detail_array[$i]['blogid']}'></td><td align='center'>{$hiddensign}</td><td>{$detail_array[$i]['title']}</td><td>{$tmp_tm}</td><td align='center'>{$categories[$tmp_gp]['catename']}</td><td align='center'><a href='javascript: ensuredel(\"{$detail_array[$i]['blogid']}\", \"0\");'><img src='admin/theme/{$themename}/del.gif' alt='{$lna[78]}' title='{$lna[78]}' border='0'></a></td><td align='center'><a href='admin.php?go=edit_edit_{$detail_array[$i]['blogid']}'><img src='admin/theme/{$themename}/edit.gif' alt='{$lna[77]}' title='{$lna[77]}' border='0'></a></td><td align='center'><a href='admin.php?go=entry_publish_{$detail_array[$i]['blogid']}' onclick=\"if(shutajax==0) {window.location='admin.php?go=entry_publish_{$detail_array[$i]['blogid']}&ajax=on';return false;}\"><img src='admin/theme/{$themename}/openblog.gif' alt='{$lna[340]}' title='{$lna[340]}' border='0'></a></td></tr>";
	}
	}
	$display_overall.=highlightadminitems('draft', 'entry');
$display_overall_plus= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[23]}
</td>
<td class="sectend">{$lna[341]}</td>
</tr>
</table>

<form action="admin.php?go=entry_batch" method="post" id='f_s' name='f_s'>
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr align=center class="admintitle"><td width=35 align=center>{$lna[245]}</td><td width=35 align=center>{$lna[297]}</td><td align=center>{$lna[284]}</td><td width=200 align=center>{$lna[288]}</td><td width=80 align=center>{$lna[285]}</td><td width=35 align=center>{$lna[78]}</td><td width=35 align=center>{$lna[77]}</td><td width=35 align=center>{$lna[340]}</td></tr>
{$tablebody}
eot;

if (count($detail_array)>0) $display_overall_plus.= <<<eot
<tr><td colspan=7><a href="#unexist" onclick="checkallbox('f_s', 'checked');">{$lna[247]}</a> | <a href="#unexist" onclick="checkallbox('f_s', '');">{$lna[248]}</a></td></tr>
<tr><td colspan=7 height=20></td></tr>
<tr class="adminoption"><td colspan=7>{$lna[249]}{$lna[249]}<input type=radio name=opt value='del'>{$lna[78]} <input type=radio name=opt value='publish'>{$lna[340]} <input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax('f_s');">
</td></tr>
eot;

	$display_overall_plus.="</table></form>";
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='publish') {
	$blog->query("UPDATE `{$db_prefix}blogs` SET `property`=0 WHERE `blogid`='{$itemid}'");
	recache_latestentries();
	if ($ajax=='on') {
		@header('Location: admin.php?go=entry_draft');
	}
	else catchsuccess($finishok, $backtodraft);
}


if ($job=='ae') {
	if (!$itemid) header ("Location: index.php");
	else acceptrequest('tid');
	switch ($itemid) {
		case 'noreply':
			$queryact="DELETE FROM `{$db_prefix}replies` WHERE `reproperty`<>4 AND `blogid`='{$tid}'";
			break;
		case 'notb':
			$queryact="DELETE FROM `{$db_prefix}replies` WHERE `reproperty`=4 AND `blogid`='{$tid}'";
			break;
		case 'lock':
			$queryact="UPDATE `{$db_prefix}blogs` SET `property`=1 WHERE `blogid`='{$tid}'";
			break;
		case 'unlock':
			$queryact="UPDATE `{$db_prefix}blogs` SET `property`=0 WHERE `blogid`='{$tid}'";
			break;
		case 'sticky1':
			$queryact="UPDATE `{$db_prefix}blogs` SET `sticky`=1 WHERE `blogid`='{$tid}'";
			break;
		case 'sticky2':
			$queryact="UPDATE `{$db_prefix}blogs` SET `sticky`=2 WHERE `blogid`='{$tid}'";
			break;
		case 'sticky0':
			$queryact="UPDATE `{$db_prefix}blogs` SET `sticky`=0 WHERE `blogid`='{$tid}'";
			break;
		case 'noread':
			$queryact="UPDATE `{$db_prefix}blogs` SET `views`=0 WHERE `blogid`='{$tid}'";
			break;
		case 'recountrep':
			$countreps=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`<=1 AND `blogid`='{$tid}'");
			$queryact="UPDATE `{$db_prefix}blogs` SET `replies`='{$countreps}' WHERE `blogid`='{$tid}'";
			break;
		case 'recounttb':
			$countreps=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`=4 AND `blogid`='{$tid}'");
			$queryact="UPDATE `{$db_prefix}blogs` SET `tbs`='{$countreps}' WHERE `blogid`='{$tid}'";
			break;
	}
	$blog->query($queryact);
	if ($itemid=='noreply') {
		$countreps=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`<=1");
		$blog->query("UPDATE `{$db_prefix}counter` SET `replies`='{$countreps}'");
		$blog->query("UPDATE `{$db_prefix}blogs` SET `replies`=0 WHERE `blogid`='{$tid}'");
		recache_latestreplies();
	}
	if ($itemid=='notb') {
		$countreps=$blog->countbyquery("SELECT COUNT(repid) FROM `{$db_prefix}replies` WHERE `reproperty`=4");
		$blog->query("UPDATE `{$db_prefix}counter` SET `tb`='{$countreps}'");
		$blog->query("UPDATE `{$db_prefix}blogs` SET `tbs`=0 WHERE `blogid`='{$tid}'");
		recache_latestreplies();
	}
	$previouspage=($_SERVER['HTTP_REFERER']=='' || strstr($_SERVER['HTTP_REFERER'], 'login.php')) ? "index.php" : $_SERVER['HTTP_REFERER'];
	header ("Location: {$previouspage}");
}


if ($job=='pagewrite') {
	header ("Location: admin.php?go=page");
}

if ($job=="pagemanage") {
	$start_id=($page-1)*$adminitemperpage;
	$detail_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}pages` ORDER BY `pagetime` DESC LIMIT $start_id, $adminitemperpage");
	for ($i=0; $i<count($detail_array); $i++) {
		$tmp_tm=gmdate('Y/m/d H:i', $detail_array[$i]['pagetime']+3600*$config['timezone']);
		if ($i%2==0) $addclass='hiddenitem';
		else $addclass='visibleitem';
		$hiddensign="<img src='admin/theme/{$themename}/openblog.gif' alt=''>";
		$tablebody.="<tr class='$addclass'><td align='center'><input type='checkbox' name='selid[]' id='selid[]' value='{$detail_array[$i]['pageid']}'></td><td align='center'>{$hiddensign}</td><td><a href=\"".getlink_pages($detail_array[$i]['pageid'], $detail_array[$i]['pagealias'])."\">{$detail_array[$i]['pagetitle']}</a></td><td>{$tmp_tm}</td><td align='center'><a href='javascript: redirectcomfirm (\"admin.php?go=entry_deletepage_{$detail_array[$i]['pageid']}&opt=d\");'><img src='admin/theme/{$themename}/del.gif' alt='{$lna[78]}' title='{$lna[78]}' border='0'></a></td><td align='center'><a href='admin.php?go=page_editpage_{$detail_array[$i]['pageid']}'><img src='admin/theme/{$themename}/edit.gif' alt='{$lna[77]}' title='{$lna[77]}' border='0'></a></td></tr>";
	}
	$numenries=$blog->countbyquery("SELECT COUNT(*) FROM `{$db_prefix}pages`");
	$pagebar=gen_page ($page, 5, "admin.php?go=entry_pagemanage", $numenries, $adminitemperpage);
	$display_overall.=highlightadminitems('pagemanage', 'entry');
$display_overall_plus= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[1057]}
</td>
<td class="sectend">{$lna[1058]}</td>
</tr>
</table>

<form action="admin.php?go=entry_deletepage" method="post" id='f_s' name='f_s'>
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr align=center class="admintitle">
<td width=35 align=center>{$lna[245]}</td><td width=35>{$lna[297]}</td><td align=center>{$lna[284]}</td><td width=200 align=center>{$lna[288]}</td><td width=35 align=center>{$lna[78]}</td><td width=35 align=center>{$lna[77]}</td></tr>
{$tablebody}
<tr><td colspan=3><a href="#unexist" onclick="checkallbox('f_s', 'checked');">{$lna[247]}</a> | <a href="#unexist" onclick="checkallbox('f_s', '');">{$lna[248]}</a></td><td colspan=4 align=right>$pagebar</td></tr>
<tr><td colspan=7 height=20></td></tr>
<tr class="adminoption"><td colspan=7>{$lna[249]} <input type=radio name=opt value='del'>{$lna[78]}  <input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax('f_s');">
</td></tr>
</table>
</form>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=="deletepage") {
	acceptrequest('opt,selid');
	if ($opt=='d') {
		if ($itemid=='') catcherror($lna[337]);
		$itemid=floor($itemid);
		$blog->query("DELETE FROM `{$db_prefix}pages` WHERE `pageid`='{$itemid}'");
		$blog->query("DELETE FROM `{$db_prefix}mods` WHERE `name`='pageshortcut{$itemid}' AND `position`='header'");
		mod_replace ('pageshortcut{$itemid}', '');
		recache_mods();
		if ($ajax=='on') {
			catchsuccessandfetch($lna[1095], 'admin.php?go=entry_pagemanage');
		}
		else catchsuccess ($lna[1095], "{$lna[1057]}|admin.php?go=entry_pagemanage");
	}
	if ($opt=='del') {
		if ($selid=='') catcherror($lna[337]);
		$batch_id=makeaquery($selid, "`pageid`='%s'", 'OR');
		$batch_id2=makeaquery($selid, "`name`='pageshortcut%s'", 'OR');
		$blog->query("DELETE FROM `{$db_prefix}pages` WHERE {$batch_id}");
		$blog->query("DELETE FROM `{$db_prefix}mods` WHERE {$batch_id2}");
		foreach ($selid as $singleid) {
			mod_replace ('pageshortcut{$singleid}', '');
		}
		recache_mods();
		if ($ajax=='on') {
			catchsuccessandfetch($lna[1095], 'admin.php?go=entry_pagemanage');
		}
		else catchsuccess ($lna[1095], "{$lna[1057]}|admin.php?go=entry_pagemanage");
	}
	catcherror($lna[965]);
}

?>