<?PHP
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_magic_quotes_runtime (0);

$v=$_REQUEST['v'];

if ($v!=2 && $v!=3) {
	@header("Content-Type: text/html; charset=utf-8");
	print<<<eot

Bo-Blog 2.0.1 应急恢复程序<br>
<B><font color='red'>警告：本程序相当危险，请只在应急状态下上传使用，使用完毕立刻删除！</font></B><br>
<br><br><form action='emergency.php' method='post'><input type='hidden' value='3' name='v'>
<b>用户组：重置管理员组和游客组的一切权限为默认状态</b><br>
<input type='submit' value='Start'>
</form>
<hr>
<form action='emergency.php' method='post'><input type='hidden' value='2' name='v'>
<b>常规：请在需要执行的操作前打勾并填写必要的选项</b><br>
<input type='checkbox' name='act[]' value='psw'> 恢复管理员<input type='text' name='oldadmin'>的密码为<input type='text' name='newpsw'><br>
<input type='checkbox' name='act[]' value='nologinval'> 取消登陆时需要验证码<br>
<input type='checkbox' name='act[]' value='changeuser'> 重置以下用户的身份为管理员 用户名：<input type='text' name='newadmin'> 大小写需正确<br>
<input type='checkbox' name='act[]' value='open'> 恢复blog状态为打开<br>
<input type='submit' value='OK'>
</form>
eot;

	exit();
}

elseif ($v!=3) {
	require_once ("data/config.php");
	require_once ("global.php");
	require_once ("inc/db.php");
	require_once ("inc/boblog_class_run.php");
}

if ($v==2) {
	$act=$_POST['act'];
	$newpsw=$_REQUEST['newpsw'];
	$oldadmin=$_REQUEST['oldadmin'];
	$newadmin=$_REQUEST['newadmin'];
	if (!is_array($act)) {
		header ("Location: emergency.php");
		exit();
	}

	if (in_array('psw', $act)) {
		$newpsw=md5($newpsw);
		$blog->query("UPDATE `{$db_prefix}user` SET `userpsw`='{$newpsw}' WHERE `username`='{$oldadmin}'");
	}
	if (in_array('changeuser', $act)) {
		$blog->query("UPDATE `{$db_prefix}user` SET `usergroup`='2' WHERE `username`='{$newadmin}'");
	}

	$content=readfromfile("data/config.php");
	if (in_array('nologinval', $act)) {
		$content.="\$config['loginvalidation']='0';\n";
	}
	if (in_array('open', $act)) {
		$content.="\$config['blogopen']='1';\n";
	}

	writetofile("data/config.php", $content);
	@header("Content-Type: text/html; charset=utf-8");
	die ("Bo-Blog 2.0.1 应急恢复程序完成了请求的动作。<br><B><font color='red'>警告：请立即删除本文件！！");
}

if ($v==3) {
$usorigin0=<<<eot
<?PHP
\$permission['gpname']='游客';
\$permission['visit']='1';
\$permission['ViewPHPError']='0';
\$permission['SeeSecretCategory']='0';
\$permission['SeeHiddenEntry']='0';
\$permission['SeeHiddenReply']='0';
\$permission['SeeIP']='0';
\$permission['ViewUserList']='1';
\$permission['ViewUserDetail']='0';
\$permission['ApplyLink']='1';
\$permission['AddEntry']='0';
\$permission['EditEntry']='0';
\$permission['EditSafeMode']='0';
\$permission['AddTag']='0';
\$permission['Reply']='1';
\$permission['ReplyReply']='0';
\$permission['LeaveMessage']='1';
\$permission['MaxPostLength']='5000';
\$permission['MinPostInterval']='5';
\$permission['NoSpam']='0';
\$permission['Html']='0';
\$permission['Ubb']='1';
\$permission['Emot']='1';
\$permission['PinEntry']='0';
\$permission['CP']='0';
\$permission['XMLRPC']='0';
\$permission['AllowSearch']='1';
\$permission['FulltextSearch']='0';
\$permission['SearchInterval']='15';
\$permission['Upload']='0';
\$permission['MaxSize']='0';
\$permission['AllowedTypes']='';
eot;
$usorigin2=<<<eot
<?PHP
\$permission['gpname']='管理员';
\$permission['visit']='1';
\$permission['ViewPHPError']='1';
\$permission['SeeSecretCategory']='1';
\$permission['SeeHiddenEntry']='1';
\$permission['SeeHiddenReply']='1';
\$permission['SeeIP']='1';
\$permission['ViewUserList']='1';
\$permission['ViewUserDetail']='1';
\$permission['ApplyLink']='1';
\$permission['AddEntry']='1';
\$permission['EditEntry']='1';
\$permission['EditSafeMode']='1';
\$permission['AddTag']='1';
\$permission['Reply']='1';
\$permission['ReplyReply']='1';
\$permission['LeaveMessage']='1';
\$permission['MaxPostLength']='99999999';
\$permission['MinPostInterval']='-1';
\$permission['NoSpam']='1';
\$permission['Html']='1';
\$permission['Ubb']='1';
\$permission['Emot']='1';
\$permission['PinEntry']='1';
\$permission['CP']='1';
\$permission['XMLRPC']='1';
\$permission['AllowSearch']='1';
\$permission['FulltextSearch']='1';
\$permission['SearchInterval']='-1';
\$permission['Upload']='1';
\$permission['MaxSize']='2048';
\$permission['AllowedTypes']='zip rar gz bz2 jpg jpeg gif bmp png swf mp3 wma rm htm html txt doc xml css wmv';
eot;
	writetofile2("data/usergroup0.php", $usorigin0);
	writetofile2("data/usergroup2.php", $usorigin2);
	@header("Content-Type: text/html; charset=utf-8");
	die ("Bo-Blog 2.0.1 应急恢复程序完成了请求的动作。<br><B><font color='red'>警告：请立即删除本文件！！");
}

function writetofile2 ($filename, $data) {
	$filenum=@fopen($filename,"w");
	if (!$filenum) {
		return false;
	}
	flock($filenum,LOCK_EX);
	$file_data=fwrite($filenum,$data);
	fclose($filenum);
	return true;
}


?>