<?PHP
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$v=$_REQUEST['v'];

if (!$v) {
	template("<div class='log'>升级确认 | 升級確認</div><form action='updateto210.php?v=1' method='post'><div class='mes'><div>本程序可将 2.0.3 sp1 / 2.1.0 alpha版本的数据格式升级到 2.1.0 beta/正式版的最新数据格式。 建议您在升级前备份您的数据。这步操作是不可逆的！<br/><br/>如果当前您的blog正处于关闭状态，请将其打开后继续。<br/><hr/><br/>本程式可將 2.0.3 sp1 / 2.1.0 alpha版本的資料格式升級到 2.1.0 beta/正式版的最新資料格式。 建議您在升級前備份您的資料。這步操作是不可逆的！<br/><br/>如果當前您的blog正處於關閉狀態，請將其開啟後繼續。</div><br/><div align='center'><input type='submit' value='现在升级 | 現在升級' class='inbut'></div></form></div>");
}

if ($v=='1') {
	include ("function.php");
	$queries=array();
	if ($db_410==1) {
		$sqlcharset="  CHARSET=utf8";
	}

	$try=$blog->getbyquery("SELECT * FROM `{$db_prefix}blogs` LIMIT 1");
	if (!array_key_exists('comefrom', $try)) {
		$queries[]="ALTER TABLE `{$db_prefix}blogs` ADD `entrysummary` TEXT NULL , ADD `comefrom` VARCHAR( 255 ) NULL , ADD `originsrc` VARCHAR( 255 ) NULL , ADD `blogalias` VARCHAR( 100 ) NULL";
	}

	$try=$blog->getbyquery("SELECT * FROM `{$db_prefix}mods` WHERE `name`='columnbreak' LIMIT 1");
	if ($try['name']!='columnbreak') {
		$queries[]="INSERT INTO `{$db_prefix}mods` VALUES ('sidebar', 'columnbreak', '侧边栏一与侧边栏二的分割线', '1', '1', 'system')";
	}

	$try=$blog->getbyquery("SELECT * FROM `{$db_prefix}user` LIMIT 1");
	if (array_key_exists('empty2', $try)) {
		$queries[]="ALTER TABLE `{$db_prefix}user` DROP `empty2` ,DROP `empty3` ,DROP `empty4` ,DROP `empty5` ,DROP `empty6` ,DROP `empty7` ,DROP `empty8`";
	}
	if (array_key_exists('from', $try)) {
		$queries[]="ALTER TABLE `{$db_prefix}user` CHANGE `from` `fromplace` TEXT NULL DEFAULT NULL";
	}


	$try=$blog->getbyquery("SELECT * FROM `{$db_prefix}mods` LIMIT 1");
	if (array_key_exists('order', $try)) {
		$queries[]="ALTER TABLE `{$db_prefix}mods` CHANGE `order` `modorder` INT( 5 ) NOT NULL";
	}

	$try=$blog->getbyquery("SELECT * FROM `{$db_prefix}categories` LIMIT 1");
	if (array_key_exists('empty1', $try)) {
		$queries[]="ALTER TABLE `{$db_prefix}categories` CHANGE `empty1` `cateurlname` VARCHAR( 100 ) NULL";
	}

	$queries[]="CREATE TABLE IF NOT EXISTS `{$db_prefix}pages` (
`pageid` INT( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`pagetitle` VARCHAR( 255 ) NULL ,
`pagecontent` TEXT NULL ,
`pageauthor` INT( 8 ) NOT NULL DEFAULT '0',
`pagetime` INT( 11 ) NOT NULL DEFAULT '0',
`pageedittime` INT( 11 ) NOT NULL DEFAULT '0',
`closesidebar` TINYINT( 1 ) NOT NULL DEFAULT '0',
`htmlstat` TINYINT( 1 ) NOT NULL DEFAULT '0',
`ubbstat` TINYINT( 1 ) NOT NULL DEFAULT '0',
`emotstat` TINYINT( 1 ) NOT NULL DEFAULT '0',
`pagealias` VARCHAR( 255 ) NULL,
INDEX ( `pageauthor` )
) ENGINE = MYISAM{$sqlcharset}";
	
	$queries[]="CREATE TABLE IF NOT EXISTS `{$db_prefix}upload` (
  `fid` int(6) NOT NULL auto_increment,
  `filepath` varchar(255) default NULL,
`originalname` VARCHAR( 255 ) NULL,
`dltime` int(8) NOT NULL default '0',
  `uploadtime` int(11) default NULL,
  `uploaduser` int(6) NOT NULL default '0',
  PRIMARY KEY  (`fid`)
) ENGINE=MyISAM";

	foreach ($queries as $singlequery) {
		$blog->query($singlequery);
	}

	writetofile("data/cache_adminskinlist.php", "<?PHP\n\$adminskin[]='default';\n\$currentadminskin='default';");

	template("<div class='log'>升级完成 | 升級完成</div><div class='mes'>已将 2.0.3 sp1 / 2.1.0 alpha 版本的数据格式升级到 2.1.0 beta/正式版的数据格式。<br/><br/>请到后台的“参数设置”中，设置tag分页数、每页表情数、防盗链等新选项，到“用户组权限”设置处赋予管理员创建自定义页面的权限，同时刷新所有缓存，否则blog显示可能不正常。<br/><br/>请立即从服务器上删除这个文件。<br/><hr/><br/>已將 2.0.3 sp1 / 2.1.0 alpha 版本的資料格式升級到 2.1.0 beta/正式版的資料格式。<br/><br/>請到後台的“參數設定”中，設定tag分頁數、每頁表情數、防盜鏈等新選項，到“使用者組權限”設定處賦予管理員創建自訂頁面的權限，同時重新整理所有快取，否則blog顯示可能不正常。<br/><br/>請立即從伺服器上刪除這個檔案。</div><br/></div>");
}


function template ($body) {
	$bbb=<<<eot
<html xmlns="http://www.w3.org/1999/xhtml" lang="UTF-8">
<head>
<style><!--
body {
	margin: 15px;
	background-color: #EEE;
	font-family: Tahoma;
	text-align: center;
}
#tips {
	margin-left: auto;
	margin-right: auto;
	width: 600px;
	height: auto;
	background-color: #fff;
	font-size: 9pt;
	border: 1px solid #ccc;
	text-align: left;
	padding-bottom: 5px;
}
#tips	a {
	color: #000;
	text-decoration: none;
}
#tips	a:hover {
	color: #000;
	text-decoration: underline;
}

#titles {
	margin-left: auto;
	margin-right: auto;
	margin-top: 50px;
	width: 600px;
	height: 30px;
	font-size: 14px;
	color: #3F68A6;
	font-weight: bold;
	text-align: left;
}

div, textarea, option, input {
	font-size: 9pt;
	font-family: Tahoma;
}

.log {
	display: block;
	background-color: #4971AD;
	color: #fff;
	height: 20px;
	padding-top: 5px;
	padding-bottom: 5px;
	padding-left: 20px;
}

.mes {
	display: block;
	padding-left: 20px;
	padding-top: 5px;
	min-height: 100px;
}

.inbut {
	border-color: #EEE;
	background-color: #fff;
}
--></style>
<title>Bo-Blog Update</title>
</head>
<body>
<div id="titles">
Bo-Blog Update
</div>

<div id="tips">
$body
</div>
</body>
</html>
eot;
	@header("Content-Type: text/html; charset=utf-8");
	print($bbb);
	exit();
}

?>