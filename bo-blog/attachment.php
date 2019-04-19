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
define ("noCounter", 1);
define ("allowCache", 1);

require_once ("global.php");
$fid=floor($_REQUEST['fid']);


$attachfind=$blog->getbyquery("SELECT * FROM `{$db_prefix}upload` WHERE `fid`='{$fid}' LIMIT 1");
if ($attachfind['fid']!=$fid) die ("File does not exist.");

$check_ok=($mbcon['antileech']=='0') ? true : false;
if ($mbcon['antileech']!='0') {
	if (empty($_SERVER['HTTP_REFERER'])) $check_ok=true;
	else {
		$allowedlinkdomain=@explode(' ', $mbcon['alloweddomain']);
		$linkfrom=@parse_url($_SERVER['HTTP_REFERER']);
		$linkdomain=$linkfrom['host'];
		if (@in_array($linkdomain, $allowedlinkdomain)) $check_ok=true;
	}
}

if (!$check_ok) {
	generate_leech_error();
}
else {
	$exitcount=0;
	$dl=$_COOKIE['filedownloaded'];
	if ($dl) {
		$all_dl=@explode(',', $dl);
		if (@in_array($fid, $all_dl)) $exitcount=1;
	}
	if ($exitcount!=1) {
		$blog->query("UPDATE `{$db_prefix}upload` SET `dltime`=`dltime`+1 WHERE `fid`='{$fid}'");
		setcookie('filedownloaded', $dl.','.$fid, time()+7200);
	}
}


if ($mbcon['antileech']=='0' || $mbcon['antileech']=='1') {
	@header("Location: {$attachfind['filepath']}");
	exit();
}
else {
	$sfilename=basename($attachfind['originalname']);
	$browser=browserdetection();
	if (in_array($browser, array('Firefox', 'Mozilla', 'Opera'))) $sfilename=urldecode($sfilename);
	$sfileext=strtolower(strrchr($sfilename,'.'));
	$sfileext=str_replace(".", '', $sfileext);


	if (in_array($sfileext, array('gif', 'jpg', 'png', 'bmp', 'jpeg'))) {
		$headerposition='inline';
	}
	else {
		$headerposition='attachment';
	}

	include_once ("inc/mimetype.php");
	$contenttype=(array_key_exists($sfileext, $MIMETypes)) ? $MIMETypes[$sfileext] : 'application/octet-stream';
	$sfilesize=filesize($attachfind['filepath']);

	@header("Content-Disposition: {$headerposition}; filename=\"{$sfilename}\"");
    @header("Content-Type: {$contenttype}");
	@header('Content-Length: '.$sfilesize);
    echo readfromfile($attachfind['filepath']);
	exit();
}

function generate_leech_error(){
	@header("Content-Disposition: inline; filename=\"no_leech.gif\"");
    @header("Content-Type: image/gif");
	$sfilesize=filesize('images/others/no_leech.gif');
    echo readfromfile('images/others/no_leech.gif');
	exit();
}

function browserdetection() {
	if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko')!==false )
	{
	   if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape') )
	   {
		 $browser = 'Netscape';
	   }
	   else if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')!==false )
	   {
		 $browser = 'Firefox';
	   }
	   else
	   {
		 $browser = 'Mozilla';
	   }
	}
	else if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')!==false )
	{
	   if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')!==false )
	   {
		 $browser = 'Opera'; //Opera 8.0
	   }
	   else
	   {
		 $browser = 'IE';
	   }
	}
	else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')!==false)
	{
	   $browser = 'Opera';  //Opera 9.0
	}
	else
	{
	   $browser = 'Other';
	}

	return $browser;
}

