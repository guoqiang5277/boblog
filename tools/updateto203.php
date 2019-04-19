<?PHP
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$v=$_REQUEST['v'];

if (!$v) {
	template("<div class='log'>升级确认</div><form action='updateto203.php?v=1' method='post'><div class='mes'><div>本程序可将 2.0.2 sp2 / 2.0.3 alpha版本的数据格式升级到 2.0.3 beta/正式版的数据格式。 建议您在升级前备份您的数据。这步操作是不可逆的！<br/><br/>如果当前您的blog正处于关闭状态，请将其打开后继续。</div><br/><div align='center'><input type='submit' value='现在升级' class='inbut'></div></form></div>");
}

if ($v=='1') {
	include ("function.php");
	$queries=array();
	if ($codeversion<'2.0.3.1203.2') {
		$queries[]="ALTER TABLE `{$db_prefix}blogs`  DROP `empty2`,  DROP `empty3`,  DROP `empty4`,  DROP `empty5`,  DROP `empty6`,  DROP `empty7`,  DROP `empty8`,  DROP `empty9`,  DROP `empty10`";
		$queries[]="ALTER TABLE `{$db_prefix}blogs` ADD `blogpsw` TINYTEXT, ADD `frontpage` TINYINT( 1 ) DEFAULT '0' NOT NULL";
	}
	if ($codeversion<'2.0.3.1209.0') {
		$queries[]="ALTER TABLE `{$db_prefix}replies` CHANGE `empty1` `reppsw` TINYTEXT";
		$queries[]="ALTER TABLE `{$db_prefix}messages` CHANGE `empty1` `reppsw` TINYTEXT";
	}
	if ($codeversion>='2.0.3.1209.0') {
		template("<div class='log'>升级终止</div><div class='mes'>程序检测到您的服务器上的文件已经升级到了 2.0.3。<br><br>您是否在未执行本更新程序的情况下先上传了主程序文件，替换、覆盖了原先旧版的程序文件？<br><br>如果是这样，请换回旧版的 global.php 文件，然后重新执行升级程序。<br><br>如果不是，则您已经是2.0.3的数据格式了，无需升级，请退出。<br/><br/>请立即从服务器上删除这个文件。</div><br/></div>");
	}
	for ($i=0; $i<count($queries); $i++) {
		$blog->query($queries[$i]);
	}
	template("<div class='log'>升级完成</div><div class='mes'>已将 2.0.2 sp2 / 2.0.3 alpha版本的数据格式升级到 2.0.3 beta/正式版的数据格式。<br/><br/>请到后台参数设置中，设置自定义日期格式等选项，否则blog显示可能不正常。<br/><br/>请立即从服务器上删除这个文件。</div><br/></div>");
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