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
$finishok1=$lna[431];
$finishok2=$lna[432];
$backtodefault="{$lna[433]}|admin.php?go=user_usergroup";
$backtouseradmin="{$lna[434]}|admin.php?go=user_users";
$backtoaddnew="{$lna[435]}|admin.php?go=user_add";
$prefseccounter=0;

if ($job=='' || $job=="usergroup") {
	if (is_array($usergp)) {
		foreach ($usergp as $key=>$value) {
			$addclass=($i%2==0) ? 'visibleitem' : 'hiddenitem';
			$delitornot=($key==0 || $key==1 || $key==2) ? "<a href='javascript: alert(\"{$lna[436]}\");'><img src='admin/theme/{$themename}/del.gif' alt='' title='{$lna[436]}' border='0'></a>" : "<a href='admin.php?go=user_delgp_{$key}'><img src='admin/theme/{$themename}/del.gif' alt='{$lna[78]}' title='{$lna[78]}' border='0'></a>";
			$tablebody.="<tr class='$addclass'><td align='center'>{$key}</td><td>{$value}</td><td align='center'>{$delitornot}</td><td align='center'><a href='admin.php?go=user_editgp_{$key}'><img src='admin/theme/{$themename}/edit.gif' alt='{$lna[77]}' title='{$lna[77]}' border='0'></a></td></tr>";
			$i+=1;
		}
	}
	$display_overall.=highlightadminitems('usergroup', 'user');
$display_overall_plus= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[437]}
</td>
<td class="sectend">{$lna[438]}</td>
</tr>
</table>
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr align=center class="admintitle"><td width=35 align=center>{$lna[439]}</td><td align=center>{$lna[440]}</td><td width=35 align=center>{$lna[78]}</td><td width=35 align=center>{$lna[77]}</td></tr>
{$tablebody}
</table>
<br><br>

<form action="admin.php?go=user_newgp" method="post">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[441]}
</td>
<td class="sectend">{$lna[442]}</td>
</tr>
<tr align=center class="sect"><td align=center colspan=2>{$lna[443]} <input type=text name='gpname' value=''> <input type='submit' value="{$lna[64]}" class='formbutton'> <input type='reset' value="{$lna[65]}" class='formbutton'></td></tr></table>
<br><br></form>

<table class='tablewidth' align=center cellpadding=6 cellspacing=0>
<tr class="prefright"><td>
<ul><li>{$lna[444]}</li><li>{$lna[445]}</li><li>{$lna[446]}</li><li>{$lna[447]}</li></ul>
</td></tr>
</table>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='newgp' || $job=='editgp') {
	acceptrequest('gpname');
	if ($job=='newgp' && !$gpname) catcherror($lna[448]);
	if ($job=='editgp' && $itemid==='') catcherror($lna[449]);
	$hiddenaction=($job=='newgp') ? 'addnewgp' : 'savegp';
	if ($job=='editgp') {
		if (file_exists("data/usergroup{$itemid}.php")) {
			unset($permission);
			require ("data/usergroup{$itemid}.php"); 	//$Permission now has been changed
		}
		$newpermission=$permission;
	} else {
		require ("data/usergroup0.php");
		$newpermission=$permission;
		$newpermission['gpname']=safe_convert(trim($gpname)); //For a new group, use visitor's permission as default
	}
	$pref_leftchar="180";
	$pref_variable="newpermission";
	include ("admin/pref_usergroup.php");
	$pref_result_show=@implode('', $pref_result);
	$cau_icon="<img src='admin/theme/{$themename}/caution.gif' alt='!' title='{$lna[450]}' align='absmiddle' border='0'>";
	$pref_result_show=str_replace("^caution^", $cau_icon, $pref_result_show);
	$display_overall.=highlightadminitems('usergroup', 'user');
$display_overall.= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[451]}
</td>
<td class="sectend">{$lna[452]}</td>
</tr>
</table>
<form action="admin.php?go=user_{$hiddenaction}" method="post" id="ajaxForm1">
<table class='tablewidth' cellpadding=4 cellspacing=1 align=center>
<tr><td class="prefsection" align="center" colspan='2'><a name="top"></a>{$lna[47]}</td></tr>
<tr><td class="prefright" align="center" colspan='2'><table width=100%>{$pref_quicksel}</table></td></tr>
$pref_result_show
<tr><td colspan=2 class="sect"><ul><li> {$cau_icon} {$lna[453]}</li></ul>
</table>
<input type=hidden name='gpnum' value='{$itemid}'>
<br>
<div align=center><a name="bottom"></a><input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax(1);"> <input type=reset value="{$lna[65]}" class='formbutton'></div>
</form>
eot;
}

if ($job=='addnewgp' || $job=='savegp') {
	acceptrequest('gpnum');
	$newpermission=$_POST['prefconfig'];
	if ($job=='addnewgp') {
		$gpnum=$maxrecord['maxgpid']+1;
		$blog->query("UPDATE `{$db_prefix}maxrec` SET `maxgpid`=maxgpid+1");
	}
	$newgpname=safe_convert(trim($newpermission['gpname']));
	$usergp[$gpnum]=$newgpname;
	$savetext.="<?PHP\n";
	foreach ($usergp as $key=>$value) {
		$savetext.="\$usergp[{$key}]='".safe_convert($value)."';\n";
	}
	if (!writetofile ("data/cache_usergroup.php", $savetext)) {
		catcherror ($lna[66]."data/cache_usergroup.php");
	}

	unset ($savetext);
	$savetext.="<?PHP\n";
	foreach ($newpermission as $key=>$value) {
		$savetext.="\$permission['{$key}']='".safe_convert($value)."';\n";
	}

	if (!writetofile ("data/usergroup{$gpnum}.php", $savetext)) {
		catcherror ($lna[66]."data/usergroup{$gpnum}.php");
	} 
	recache_adminlist ();
	if ($ajax=='on') {
		$fetchURL='admin.php?go=user_usergroup';
		catchsuccessandfetch($finishok1, $fetchURL);
	}
	else catchsuccess ($finishok1, $backtodefault);
}

if ($job=='delgp') {
	if ($itemid==='') catcherror($lna[449]);
	foreach ($usergp as $key=>$value) {
		if ($key!=$itemid) $gplist.="<option value='{$key}'>{$value}</option>\n";
	}
	@header("Content-Type: text/html; charset=utf-8");
	$t=new template;
	$t->showtips($lna[454], "<br><form action='admin.php?go=user_dodelgp_{$itemid}' method='post'>{$lna[455]}<br><br><select name='togp'>{$gplist}</select> <input type=button value='{$lna[138]}' onclick='window.location=\"admin.php?go=user_usergroup\"' class='formbutton'> <input type=submit value='{$lna[64]}' class='formbutton'></form>");
	exit();
}

if ($job=='dodelgp') {
	if ($itemid==='') catcherror($lna[449]);
	if ($togp==='' || $togp==$itemid) catcherror($lna[449]);
	$blog->query("UPDATE `{$db_prefix}user` SET `usergroup`='$togp' WHERE `usergroup`='$itemid'");
	if (file_exists("data/usergroup{$itemid}.php")) @unlink ("data/usergroup{$itemid}.php"); 
	$savetext.="<?PHP\n";
	foreach ($usergp as $key=>$value) {
		if ($key!=$itemid) $savetext.="\$usergp[{$key}]='".safe_convert($value)."';\n";
	}
	if (!writetofile ("data/cache_usergroup.php", $savetext)) {
		catcherror ($lna[66]."data/cache_usergroup.php");
	}
	recache_adminlist ();
	catchsuccess ($finishok1, $backtodefault);
}

if ($job=='users') {
	acceptrequest('usergroup,ordered');
	$queryplus=($usergroup==="") ? '' : "WHERE `usergroup`='{$usergroup}'";
	if ($ordered!=='') {
		$allorder=array('`username` ASC', '`username` ASC', '`username` DESC', '`regtime` DESC', '`regtime` ASC');
		$ordernow=$allorder[$ordered];
	} else $ordernow='`username` ASC';
	$start_id=($page-1)*$adminitemperpage;
	$detail_array=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}user` {$queryplus}  ORDER BY {$ordernow} LIMIT $start_id, $adminitemperpage");
	for ($i=0; $i<count($detail_array); $i++) {
		$tmp_gp=$detail_array[$i]['usergroup'];
		$tmp_sgp=$usergp[$tmp_gp];
		$tmp_tm=gmdate('Y/m/d H:i', $detail_array[$i]['regtime']+3600*$config['timezone']);
		if ($i%2==0) $addclass='hiddenitem';
		else $addclass='visibleitem';
		$tablebody.="<tr class='$addclass'><td align='center'><input type='checkbox' name='selid[]' id='selid[]' value='{$detail_array[$i]['userid']}'></td><td>{$tmp_sgp}</td><td align='center'>{$detail_array[$i]['userid']}</td><td width=50%>{$detail_array[$i]['username']}</td><td align='center'>{$tmp_tm}</td><td align='center'><a href='javascript: redirectcomfirm (\"admin.php?go=user_deluser_{$detail_array[$i]['userid']}\");'><img src='admin/theme/{$themename}/del.gif' alt='{$lna[78]}' title='{$lna[78]}' border='0'></a></td><td align='center'><a href='admin.php?go=user_edituser_{$detail_array[$i]['userid']}'><img src='admin/theme/{$themename}/edit.gif' alt='{$lna[77]}' title='{$lna[77]}' border='0'></a></td></tr>";
	}
	unset($i);
	foreach ($usergp as $i=>$value) {
		if ($i==0) continue;
		$selected=($i==$usergroup) ? ' selected' : '';
		$puttingcates.="<option value='{$i}'{$selected}>{$value}</option>";
	}
	$pagebar=gen_page ($page, 5, "admin.php?go=user_users&usergroup={$usergroup}&ordered={$ordered}", $statistics['users'], $adminitemperpage);
	$display_overall.=highlightadminitems('users', 'user');
$display_overall_plus= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[8]}
</td>
<td class="sectend">{$lna[456]}</td>
</tr>
</table>

<form action="admin.php?go=user_users&ordered={$ordered}" method="post">
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr><td colspan=7>
<select name="usergroup"><option value=''>{$lna[457]}</option>$puttingcates</select> <input type=submit value="{$lna[244]}" class='formbutton'> {$lna[458]} <a href="admin.php?go=user_users&usergroup={$usergroup}&ordered=1">{$lna[459]}</a> | <a href="admin.php?go=user_users&usergroup={$usergroup}&ordered=2">{$lna[460]}</a> | <a href="admin.php?go=user_users&usergroup={$usergroup}&ordered=3">{$lna[461]}</a> | <a href="admin.php?go=user_users&usergroup={$usergroup}&ordered=4">{$lna[462]}</a></td></tr>
</table>
</form>
<br>

<form action="admin.php?go=user_batchusers" method="post" id='f_s' name='f_s'>
<table cellpadding=3 cellspacing=1 align=center class='tablewidth'>
<tr align=center class="admintitle"><td width=35 align=center>{$lna[245]}</td><td width=80>{$lna[463]}</td><td width=40>{$lna[471]}</td><td align=center>{$lna[464]}</td><td width=190 align=center>{$lna[465]}</td><td width=35 align=center>{$lna[78]}</td><td width=35 align=center>{$lna[77]}</td></tr>
{$tablebody}
<tr><td colspan=3><a href="#unexist" onclick="checkallbox('f_s', 'checked');">{$lna[247]}</a> | <a href="#unexist" onclick="checkallbox('f_s', '');">{$lna[248]}</a></td><td colspan=4 align=right>$pagebar</td></tr>
<tr><td colspan=6 height=10></td></tr>
<tr class="adminoption"><td colspan=8>{$lna[249]}<input type=radio name=opt value='del'>{$lna[78]} <input type=radio name=opt value='newusergroup'>{$lna[466]}<select name="tousergroup">$puttingcates</select> <input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax('f_s');">
</td></tr>
</table>
</form>

<br>
<br>
<form action="admin.php?go=user_finduser" method="post">
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
{$lna[467]}
</td>
<td class="sectend">{$lna[468]}</td>
</tr>
<tr><td colspan=2 class='sect'>

<table class='tablewidth' align=center>
<tr><td width=25%>{$lna[469]}</td><td><input type='text' name='detailuser' size='20'></td></tr>
<tr><td width=25%>{$lna[470]}</td><td><input type='radio' checked=checked name='detailtype' value='username'>{$lna[464]} &nbsp; <input type='radio' name='detailtype' value='userid'>{$lna[471]}</td></tr>
<tr><td width=25%>{$lna[472]}</td><td><input type='radio' checked=checked name='detailact' value='edituser'>{$lna[77]} &nbsp; <input type='radio' name='detailact' value='deluser'>{$lna[78]}[<b>{$lna[473]}</b>]</td></tr>
<tr><td colspan=2 align=center><input type=submit value="{$lna[64]}" class='formbutton'> <input type=reset value="{$lna[65]}" class='formbutton'>
</td></tr></table>

</td></tr></table>
</form>
eot;
	if ($ajax=='on') die($display_overall_plus);
	else $display_overall.=$display_overall_plus;
}

if ($job=='finduser') {
	acceptrequest ('detailuser,detailtype,detailact');
	if ($detailtype=='username') {
		$try=$blog->getbyquery("SELECT * FROM `{$db_prefix}user` WHERE `username`='$detailuser'");
		if (!$try) catcherror ($lna[474]);
		else $detailuser=$try['userid'];
	}
	$newurl="admin.php?go=user_{$detailact}_{$detailuser}";
	header ("Location: $newurl");
}


if ($job=='add' || $job=='edituser') {
	if ($job=='edituser') {
		if ($itemid==='') catcherror ($lna[474]);
		$edituser=$blog->getbyquery("SELECT * FROM `{$db_prefix}user` WHERE `userid`='$itemid'");
		if (!$edituser) catcherror ($lna[474]);
		if ($edituser['usergroup']==2) $pluswarn="<font color=red><b>{$lna[475]}</b></color><br/>";
		$jobs="saveuser";
		$t=new template;
		$formbody.=$t->set('form_eachline', array('text'=>$lna[464], 'formelement'=>$edituser['username']."<input type='hidden'  name='p[userid]' value='$itemid'/>"));
		$formbody.=$t->set('form_eachline', array('text'=>$lna[476], 'formelement'=>$lna[477]));
		$formbody.=$t->set('form_eachline', array('text'=>$lna[478], 'formelement'=>"<input type='password'  class='text' size='16' name='p[newpsw]' /> {$lna[479]}"));
		$formbody.=$t->set('form_eachline', array('text'=>$lna[480], 'formelement'=>"<input type='password'  class='text' size='16' name='p[confirmpsw]' /> {$lna[479]}"));
		$light='users';
		$showword=$lna[481];
	} else {
		$jobs="savenewuser";
		$t=new template;
		$formbody.=$t->set('form_eachline', array('text'=>$lna[464], 'formelement'=>"<input type='text' class='text' size='16' name='p[username]' />"));
		$formbody.=$t->set('form_eachline', array('text'=>$lna[482], 'formelement'=>"<input type='password'  class='text' size='16' name='p[password]' />"));
		$formbody.=$t->set('form_eachline', array('text'=>$lna[483], 'formelement'=>"<input type='password' class='text' size='16' name='p[confirmpsw]' />"));
		$light='add';
		$showword=$lna[484];
	}
	foreach ($usergp as $i=>$value) {
		if ($i==0) continue;
		$selected=($i==$edituser['usergroup']) ? ' selected' : '';
		$puttingcates.="<option value='{$i}'{$selected}>{$value}</option>";
	}
	$formbody.=$t->set('form_eachline', array('text'=>$lna[463], 'formelement'=>"<select name='p[usergroup]'>{$puttingcates}</select>"));
	$formbody.=$t->set('form_eachline', array('text'=>$lna[485], 'formelement'=>"<input type='text' class='text' size='16' name='p[email]' value='".stripslashes($edituser['email'])."'/>"));
	$formbody.=$t->set('form_eachline', array('text'=>$lna[486], 'formelement'=>"<input type='text' class='text' size='16' name='p[homepage]' value='".stripslashes($edituser['homepage'])."'/>"));
	$sex_sel=array('0'=>$lna[487], '1'=>$lna[488], '2'=>$lna[489]);
	$sex_choice=array('0'=>'', '1'=>'', '2'=>'');
	$tmp_gender=$edituser['gender'];
	$sex_choice[$tmp_gender]="checked=checked";
	$formbody.=$t->set('form_eachline', array('text'=>$lna[491], 'formelement'=>"<input type='radio' name='p[gender]' value='0' {$sex_choice[0]}/>{$lna[487]} <input type='radio' name='p[gender]' value='1' {$sex_choice[1]}/>{$lna[488]} <input type='radio' name='gender' value='2' {$sex_choice[2]}/>{$lna[489]}"));
	$formbody.=$t->set('form_eachline', array('text'=>$lna[490], 'formelement'=>"<input type='text' class='text' size='16' name='p[qq]' value='".stripslashes($edituser['qq'])."'/>"));
	$formbody.=$t->set('form_eachline', array('text'=>'MSN', 'formelement'=>"<input type='text' class='text' size='16' name='p[msn]' value='".stripslashes($edituser['msn'])."'/>"));
	$formbody.=$t->set('form_eachline', array('text'=>'Skype', 'formelement'=>"<input type='text' class='text' size='16' name='p[skype]' value='".stripslashes($edituser['skype'])."'/>"));
	$formbody.=$t->set('form_eachline', array('text'=>$lna[492], 'formelement'=>"<input type='text' class='text' size='16' name='p[from]' value='".stripslashes($edituser['fromplace'])."'/>"));
	$formbody.=$t->set('form_eachline', array('text'=>$lna[493], 'formelement'=>"<textarea cols='30' rows='3' name='p[intro]'>".stripslashes($edituser['intro'])."</textarea>"));
	$display_overall.=highlightadminitems($light, 'user');
$display_overall.= <<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
$showword
</td>
<td class="sectend">{$lna[494]}</td>
</tr>
</table>

<table cellpadding=3 cellspacing=0 align=center class='tablewidth'>
<tr>
<td class='sect'>$pluswarn

<form action="admin.php?go=user_{$jobs}" method="post" id="ajaxForm1">
<table cellpadding=4 cellspacing=1 align=center class='tablewidth'>
$formbody
<tr class="adminoption"><td colspan=2 align=center><input type=button value="{$lna[64]}" class='formbutton' onclick="adminSubmitAjax(1);"> <input type=reset value="{$lna[65]}" class='formbutton'>
</td></tr></table>
</form>

</td></tr></table>
</td></tr></table>
eot;
}

if ($job=='savenewuser' || $job=='saveuser') {
	acceptrequest('p');
	if ($job=='savenewuser') {
		$username=trimplus(safe_convert($p['username']));
		if ($username==='') catcherror ($lna[495]);
		if ($p['password']==='' || $p['password']!=$p['confirmpsw']) catcherror ($lna[496]);
		else $password=md5($p['password']);
		$usercheck=mystrtolower($username);
		$try=$blog->getbyquery("SELECT userid FROM `{$db_prefix}user` WHERE LOWER(username)='{$usercheck}'");
		if (is_array($try)) catcherror ($lna[497]);
	} else {
		if ($p['newpsw']!=='') {
			if ($p['newpsw']!=$p['confirmpsw']) catcherror ($lna[496]);
			$password="`userpsw`='".md5($p['newpsw'])."', ";
		} else $password="";
	}
	$email=strtolower(trimplus(safe_convert($p['email'])));
	$homepage=trimplus(safe_convert($p['homepage']));
	$gender=floor($p['gender']);
	$qq=floor($p['qq']);
	$msn=trimplus(safe_convert($p['msn']));
	$skype=trimplus(safe_convert($p['skype']));
	$from=trimplus(safe_convert($p['from']));
	$intro=trimplus(safe_convert($p['intro']));
	$musergroup=floor($p['usergroup']);
	if ($job=='savenewuser') {
		$currentuserid=$maxrecord['maxuserid']+1;
		$imajikan=time();
		$blog->query("INSERT INTO `{$db_prefix}user` VALUES ('{$currentuserid}', '{$username}', '{$password}', '{$imajikan}', '{$musergroup}', '{$email}', '{$homepage}', '{$qq}', '{$msn}', '{$intro}', '{$gender}', '{$skype}', '{$from}', '0', '{$userdetail['ip']}', '')");
		$blog->query("UPDATE `{$db_prefix}maxrec` SET `maxuserid`=`maxuserid`+1");
		$blog->query("UPDATE `{$db_prefix}counter` SET `users`=`users`+1");
	} else {
		$blog->query("UPDATE `{$db_prefix}user` SET {$password} `usergroup`='{$musergroup}', `email`='{$email}', homepage='{$homepage}',  qq='{$qq}', msn='{$msn}', intro='{$intro}', gender='{$gender}', skype='{$skype}', `fromplace`='{$from}' WHERE `userid`='{$p['userid']}'");
		recache_adminlist();
	}
	if ($ajax=='on') {
		$fetchURL='admin.php?go=user_users';
		catchsuccessandfetch($finishok2, $fetchURL);
	}
	else catchsuccess ($finishok2, array($backtouseradmin,$backtoaddnew));
}

if ($job=='deluser') {
	if ($itemid==='') catcherror ($lna[474]);
	$itemid=floor($itemid);
	if ($itemid==$userdetail['userid']) catcherror ($lna[1193]);
	$try=$blog->getbyquery("SELECT userid FROM `{$db_prefix}user` WHERE`userid`='{$itemid}'");
	if (!$try) catcherror ($lna[474]);
	$blog->query("DELETE FROM `{$db_prefix}user` WHERE `userid`='{$itemid}'");
	$blog->query("UPDATE `{$db_prefix}counter` SET `users`=`users`-1");
	if ($ajax=='on') {
		$fetchURL='admin.php?go=user_users';
		catchsuccessandfetch($finishok2, $fetchURL);
	}
	else catchsuccess ($finishok2, array($backtouseradmin,$backtoaddnew));
}

if ($job=='batchusers') {
	acceptrequest('opt,selid,tousergroup');
	if (!is_array($selid)) $cancel=$lna[498];
	if (!$opt) $cancel=$lna[499];
	catcherror ($cancel);
	$dels=@implode(',', $selid);
	if ($opt=='del') {
		$blog->query("DELETE 	FROM `{$db_prefix}user` WHERE `userid` IN ({$dels})");
		$delednum=db_affected_rows();
		$blog->query("UPDATE `{$db_prefix}counter` SET `users`=`users`-{$delednum}");
	} elseif ($opt=='newusergroup') {
		$blog->query("UPDATE `{$db_prefix}user` SET `usergroup`='{$tousergroup}'  WHERE `userid` IN ({$dels})");
	}
	recache_adminlist();
	if ($ajax=='on') {
		$fetchURL='admin.php?go=user_users';
		catchsuccessandfetch($finishok2, $fetchURL);
	}
	else catchsuccess ($finishok2, array($backtouseradmin,$backtoaddnew));
}

?>