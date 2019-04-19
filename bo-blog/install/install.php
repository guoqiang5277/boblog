<?PHP
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_magic_quotes_runtime (0);
include ('db.php');
$mqgpc_status=get_magic_quotes_gpc();
if ($mqgpc_status==0) $_POST=addsd($_POST);
@extract($_POST, EXTR_SKIP); 
@extract($_GET, EXTR_SKIP); 
if (isset($password)) $password=md5($password);
$ts=time();
$ip=$_SERVER['REMOTE_ADDR'];

if (!$v) {
	template("<div class='log'>Select Language</div><form action='install.php?v=setlang' method='post'><div class='mes'><div align='center'><select style=\"width: 50%;\" name='slang'><option value='en'>English</option><option value='zh-cn' selected='selected'>Simplified Chinese (简体中文)</option><option value='zh-tw'>Traditional Chinese (正體中文)</option><option value='vn'>Vietnamese (Tiếng Việt)</option></select></div><br/><div align='center'><input type='submit' value='OK' class='inbut'></div></form></div>");
}

if ($v=='setlang') {
	setcookie('setuplang', $slang, time()+3600);
	$language=$slang;
	if (!$language) $language='zh-cn';
	include("lang_{$language}.php");
	template("<div class='log'>{$lang[72]}</div><form action='install.php?v=start' method='post'><div class='mes'><div align='left'><input type='radio' name='newinstall' value='1' checked='checked'/> {$lang[73]}<br/><input type='radio' name='newinstall' value='0'/> {$lang[74]}<br/></div><br/><div align='center'><input type='submit' value='{$lang[21]}' class='inbut'></div></form></div>");
}

$language=$_COOKIE['setuplang'];
if (!$language) $language='zh-cn';
include("lang_{$language}.php");

if ($v=='start') {
	//Checking PHP version
	if (PHP_VERSION<"4.1.0") {
		template("<div class='log'>{$lang[1]}</div><div class='mes'>{$lang[2]}</div>");
	}

	//Is <dir>data editable?
	writetofile('../data/test.php', '');
	if (!file_exists('../data/test.php'))  {
		template("<div class='log'>{$lang[1]}</div><div class='mes'>{$lang[70]}</div>");
	} else {
		@unlink('../data/test.php');
	}

	$copytxt=readfromfile("licence.txt");
	template("<div class='log'>{$lang[3]}</div><div class='mes'><div align='center'><form action=\"install.php?v=1&newinstall={$newinstall}\" method='post'><textarea style=\"width: 90%; height: 200px;\">{$copytxt}</textarea></div><br/><div align='center'><input type='submit' value='{$lang[4]}' class='inbut'> <input type='button' value='{$lang[5]}' onclick='window.location=\"install.php?v=cancel\";' class='inbut'></form></div></div>");
}

if ($v=='cancel') {
	template("<div class='log'>{$lang[6]}</div><div class='mes'>{$lang[7]}</div>");
}

if ($v=='1') {
	$linkfrom=@parse_url($_SERVER['HTTP_REFERER']);
	$port=($linkfrom['port']) ? ":{$linkfrom['port']}" : '';
	$blogurlpath=$linkfrom['host'].$port.str_replace('/install/install.php', '', $linkfrom['path']);
	if ($newinstall==1) { // not repair
		$overwritesel="<b>{$lang[65]}</b><br><input type='radio' value='1' name='db_overwrite' onclick=\"alert('{$lang[68]}');\">{$lang[66]} <input type='radio' value='0' name='db_overwrite' checked>{$lang[67]}<br><br>";
	}
	template("<div class='log'>{$lang[8]}</div><div class='mes'><form action='install.php?v=2' method='post'><b>{$lang[9]}</b><br><input type='text' size='20' value='localhost' name='db_server'><br><br><b>{$lang[10]}</b><br><input type='text' size='20' value='' name='db_username'><br><br><b>{$lang[11]}</b><br><input type='password' size='20' value='' name='db_password'><br><br><b>{$lang[12]}</b><br><input type='text' size='20' value='' name='db_name'><br> {$lang[13]}<br><br><b>{$lang[14]}</b><br><input type='text' size='20' value='boblog_' name='db_prefix'><br> {$lang[15]}<br><br><b>{$lang[82]}</b><br>{$linkfrom['scheme']}://<input type='text' size='35' value='{$blogurlpath}' name='blogurlpath'>/index.php<br> {$lang[83]}<br><br>{$overwritesel}<b>{$lang[16]}</b> <a href='javascript:showlayer(\"what1\");'>[{$lang[17]}]</a><br><input type='radio' value='0' name='db_410'>{$lang[18]} <input type='radio' value='1' name='db_410'>{$lang[19]} <div id='what1' style='display: none;'><br>{$lang[20]}</div><div align='center'><br><input type='hidden' name='blogurlpathscheme' value='{$linkfrom['scheme']}'><input type='hidden' name='newinstall' value='{$newinstall}'><input type='submit' value='{$lang[21]}' class='inbut'> <input type='reset' value='{$lang[22]}'  class='inbut'></div></form></div>");
}

if ($v=='2') {
	$pp=db_connect($db_server, $db_username, $db_password, $db_name);
	if (!$pp) {
		template("<div class='log'>{$lang[1]}</div><div class='mes'>{$lang[23]}<br><br>{$errmsg}</div>");
	}
	$mysqlver=@mysql_get_server_info();
	if ($mysqlver<'3') {
		template("<div class='log'>{$lang[1]}</div><div class='mes'>{$lang[24]}</div>");
	}

	if ($newinstall==1) { // not repair
		$overwritesel="{$lang[26]}<br><input type='text' size='20' value='' name='username'><br><br>{$lang[27]}<br><input type='password' size='20' value='' name='password' id='password'><br><br>{$lang[80]}<br><input type='password' size='20' value='' name='confirmpassword' id='confirmpassword'><br><br>";
	}
	$blogurlpath=$blogurlpathscheme.'://'.$blogurlpath;
	template("<div class='log'>{$lang[25]}</div><div class='mes'><form action='install.php?v=3' method='post' id='frm1'>{$overwritesel} {$lang[28]}<br><input type='text' size='40' name='blogname' value='Bo-Blog'><br><br>{$lang[29]}<br><input type='text' size='40' name='blogdesc' value='Bo-Blog'><input type='hidden' name='db_server' value='{$db_server}'><input type='hidden' name='db_username' value='{$db_username}'><input type='hidden' name='db_password' value='{$db_password}'><input type='hidden' name='db_name' value='{$db_name}'><input type='hidden' value='{$db_410}' name='db_410'><input type='hidden' name='db_prefix' value='{$db_prefix}'><input type='hidden' name='blogurlpath' value='{$blogurlpath}'><input type='hidden' name='db_overwrite' value='{$db_overwrite}'><br><br><div align='center'><input type='hidden' name='newinstall' value='{$newinstall}'><input type='button' id='btn1' value='{$lang[21]}' onclick='checkandsubmit();' class='inbut'> <input type='reset' value='{$lang[22]}'  class='inbut'></div></form></div>");
}

if ($v=='3') {
	//Connect and Select Database
	$pp=db_connect($db_server, $db_username, $db_password, $db_name);
	if (!$pp) {
		template("<div class='log'>{$lang[1]}</div><div class='mes'>{$lang[23]}<br><br>{$errmsg}</div>");
	}
	$mysqlver=@mysql_get_server_info();
	if ($mysqlver<'4') {
		template("<div class='log'>{$lang[1]}</div><div class='mes'>{$lang[24]}</div>");
	}

	//If the server is running MySQL with a version higher than 4.1, set the character as UTF-8
	if ($db_410==1) {
		db_query("SET NAMES 'utf8'");
		$sqlcharset=" CHARSET=utf8";
	}

	if ($newinstall!=1) { // Repair tables now
		db_query("REPAIR TABLE IF EXISTS `{$db_prefix}blogs`, `{$db_prefix}calendar`, `{$db_prefix}categories`, `{$db_prefix}counter`, `{$db_prefix}forbidden`, `{$db_prefix}history`, `{$db_prefix}linkgroup`, `{$db_prefix}links`, `{$db_prefix}maxrec`, `{$db_prefix}messages`, `{$db_prefix}replies`, `{$db_prefix}user`, `{$db_prefix}mods`, `{$db_prefix}tags`, `{$db_prefix}plugins`");
		template("<div class='log'>{$lang[76]}</div><div class='mes'><form action='install.php?v=4' method='post' id='frm1'>{$lang[77]}<br>{$lang[58]}<br><input type='hidden' value='{$db_server}' name='db_server'><input type='hidden' value='{$db_username}' name='db_username'><input type='hidden' value='{$db_password}' name='db_password'><input type='hidden' value='{$db_name}' name='db_name'><input type='hidden' value='{$db_prefix}' name='db_prefix'><input type='hidden' name='blogurlpath' value='{$blogurlpath}'><input type='hidden' value='{$db_410}' name='db_410'><input type='hidden' value='{$username}' name='username'><input type='hidden' value='{$password}' name='password'><input type='hidden' value='{$blogname}' name='blogname'><input type='hidden' value='".addslashes($blogdesc)."' name='blogdesc'><br><br><div align='center'><input type='hidden' name='newinstall' value='{$newinstall}'><input type='button' id='btn1' value='{$lang[21]}' onclick='submitit();' class='inbut'> <input type='reset' value='{$lang[22]}'  class='inbut'></div></form></div>");
	}

	if ($db_overwrite==1) {
		db_query("DROP TABLE IF EXISTS `{$db_prefix}blogs`, `{$db_prefix}calendar`, `{$db_prefix}categories`, `{$db_prefix}counter`, `{$db_prefix}forbidden`, `{$db_prefix}history`, `{$db_prefix}linkgroup`, `{$db_prefix}links`, `{$db_prefix}maxrec`, `{$db_prefix}messages`, `{$db_prefix}replies`, `{$db_prefix}user`, `{$db_prefix}pages`, `{$db_prefix}mods`, `{$db_prefix}tags`, `{$db_prefix}plugins`, `{$db_prefix}upload`");
	}

	//Creating Tables
	$setup_query="
	CREATE TABLE `{$db_prefix}blogs` (
	  `blogid` int(10) NOT NULL default '0',
	  `title` text NULL,
	  `pubtime` int(11) NOT NULL default '0',
	  `authorid` int(8) NOT NULL default '0',
	  `replies` int(8) NOT NULL default '0',
	  `tbs` int(8) NOT NULL default '0',
	  `views` int(8) NOT NULL default '0',
	  `property` int(1) NOT NULL default '0',
	  `category` int(3) NOT NULL default '0',
	  `tags` text NULL,
	  `sticky` int(1) NOT NULL default '0',
	  `htmlstat` int(1) NOT NULL default '0',
	  `ubbstat` int(1) NOT NULL default '1',
	  `emotstat` int(1) NOT NULL default '1',
	  `content` MEDIUMTEXT NULL,
	  `editorid` int(8) NOT NULL default '0',
	  `edittime` int(11) NOT NULL default '0',
	  `weather` TINYTEXT NULL,
	  `mobile` int(1) NOT NULL default '0',
	  `pinged` text NULL,
	  `permitgp` text NULL,
	  `starred` INT( 5 ) NOT NULL DEFAULT '0',
	  `blogpsw` TINYTEXT NULL,
	  `frontpage` TINYINT( 1 ) NOT NULL DEFAULT '0',
	 `entrysummary` TEXT NULL ,
	 `comefrom` VARCHAR( 255 ) NULL ,
	 `originsrc` VARCHAR( 255 ) NULL ,
	 `blogalias` VARCHAR( 100 ) NULL,
	  KEY `blogid` (`blogid`),
	  KEY `pubtime` (`pubtime`),
	  KEY `views` (`views`),
	  KEY `sticky` (`sticky`)
	) TYPE=MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="INSERT INTO `{$db_prefix}blogs` VALUES ('0', '{$lang[30]}', '{$ts}', '1', '0', '0', '0', '0', '0', '', '0', '0', '1', '1', '{$lang[31]}', '0', '0', 'sunny', '0', '', '', '0', '', '0', '', '', '', '')";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}calendar` (
	`cyearmonth` TINYTEXT NULL ,
	`cday` INT( 2 ) DEFAULT '1' NOT NULL ,
	`cid` INT( 8 ) DEFAULT '0' NOT NULL ,
	`cevent` TEXT NULL
	) TYPE = MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}categories` (
	  `cateid` int(3) NOT NULL default '0',
	  `catename` text NULL,
	  `catedesc` text NULL,
	  `cateproperty` int(1) NOT NULL default '0',
	  `cateorder` int(20) NOT NULL default '0',
	  `catemode` int(1) NOT NULL default '0',
	  `cateicon` tinytext NULL,
	  `cateurl` text NULL,
	  `cateurlname` VARCHAR(100) NULL,
	  `empty2` text NULL,
	  `empty3` text NULL,
	  KEY `cateorder` (`cateorder`)
	) TYPE=MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");
	$setup_query="INSERT INTO `{$db_prefix}categories` VALUES (0, '{$lang[32]}', '{$lang[33]}', 0, 0, 0, '', '', '', '', '')";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}counter` (
	  `total` int(20) NOT NULL default '0',
	  `max` int(20) NOT NULL default '0',
	  `today` int(20) NOT NULL default '0',
	  `entries` int(20) NOT NULL default '0',
	  `replies` int(20) NOT NULL default '0',
	  `tb` int(20) NOT NULL default '0',
	  `messages` int(20) NOT NULL default '0',
	  `users` int(20) NOT NULL default '0',
	  `empty1` int(20) NOT NULL default '0',
	  `empty2` int(20) NOT NULL default '0',
	  `empty3` int(20) NOT NULL default '0'
	) TYPE=MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");
	$setup_query="INSERT INTO `{$db_prefix}counter` VALUES (0, 0, 0, 1, 0, 0, 0, 0, {$ts}, 0, 0)";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}forbidden` (
	  `banword` text NULL,
	  `nosearch` text NULL,
	  `keep` text NULL,
	  `suspect` text NULL,
	  `banip` text NULL,
	  `empty1` text NULL,
	  `empty2` text NULL,
	  `empty3` text NULL
	) TYPE=MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");
	$setup_query="INSERT INTO `{$db_prefix}forbidden` VALUES ('', '', '', '', '', '', '', '')";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}history` (
	  `hisday` int(8) NOT NULL default '0',
	  `visit` int(8) NOT NULL default '0'
	) TYPE=MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}linkgroup` (
	  `linkgpid` int(3) NOT NULL default '0',
	  `linkgpname` text NULL,
	  `linkgppt` int(1) NOT NULL default '1',
	  `linkgporder` int(3) NOT NULL default '0',
	  `empty1` text NULL,
	  `empty2` text NULL,
	  KEY `linkgporder` (`linkgporder`)
	) TYPE=MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");
	$setup_query="INSERT INTO `{$db_prefix}linkgroup` VALUES (0, '{$lang[34]}', 1, 0, '', '')";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}links` (
	  `linkid` int(4) NOT NULL default '0',
	  `linkname` text NULL,
	  `linkurl` text NULL,
	  `linklogo` text NULL,
	  `linkdesc` text NULL,
	  `linkgptoid` int(3) NOT NULL default '0',
	  `linkorder` int(4) NOT NULL default '0',
	  `isdisplay` int(1) NOT NULL default '1',
	  `empty1` text NULL,
	  `empty2` text NULL,
	  KEY `linkorder` (`linkorder`)
	) TYPE=MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");
	$setup_query="INSERT INTO `{$db_prefix}links` VALUES (0, 'Bo-Blog', 'http://www.bo-blog.com', '', 'Bo-Blog Official Site', 0, 0, 1, '', ''), (1, 'BMForum', 'http://www.bmforum.com', '', 'BMForum - {$lang[71]}', 0, 0, 1, '', '')";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}maxrec` (
	  `maxblogid` int(8) NOT NULL default '0',
	  `maxuserid` int(8) NOT NULL default '0',
	  `maxcateid` int(8) NOT NULL default '0',
	  `maxgpid` int(8) NOT NULL default '0',
	  `maxrepid` int(8) NOT NULL default '0',
	  `maxmessagepid` int(8) NOT NULL default '0',
	  `maxtagid` int(8) NOT NULL default '0',
	  `maxlinkgpid` int(8) NOT NULL default '0',
	  `maxlinkid` int(8) NOT NULL default '0',
	  `empty1` int(8) NOT NULL default '0',
	  `empty2` int(8) NOT NULL default '0',
	  `empty3` int(8) NOT NULL default '0'
	) TYPE=MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");
	$setup_query="INSERT INTO `{$db_prefix}maxrec` VALUES (0, 1, 0, 3, 0, 0, 0, 0, 1, 0, 0, 0)";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}messages` (
	  `repid` int(10) NOT NULL default '0',
	  `reproperty` int(1) NOT NULL default '0',
	  `reptime` int(11) NOT NULL default '0',
	  `replierid` int(8) NOT NULL default '0',
	  `replier` text NULL,
	  `repemail` text NULL,
	  `repurl` text NULL,
	  `repip` text NULL,
	  `repcontent` text NULL,
	  `html` int(1) NOT NULL default '0',
	  `ubb` int(1) NOT NULL default '1',
	  `emot` int(1) NOT NULL default '1',
	  `adminrepid` int(8) NOT NULL default '0',
	  `adminreplier` text NULL,
	  `adminreptime` int(11) NOT NULL default '0',
	  `adminrepcontent` text NULL,
	  `adminrepeditorid` int(8) NOT NULL default '0',
	  `adminrepeditor` text NULL,
	  `adminrepedittime` int(11) NOT NULL default '0',
	  `reppsw` tinytext NULL,
	  `empty2` text NULL,
	  `empty3` text NULL,
	  `empty4` text NULL,
	  `empty5` text NULL,
	  `empty6` text NULL,
	  `empty7` text NULL,
	  `empty8` text NULL,
	  KEY `repid` (`repid`),
	  KEY `reptime` (`reptime`)
	) TYPE=MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}replies` (
	  `repid` int(10) NOT NULL default '0',
	  `reproperty` int(1) NOT NULL default '0',
	  `blogid` int(10) NOT NULL default '0',
	  `reptime` int(11) NOT NULL default '0',
	  `replierid` int(8) NOT NULL default '-1',
	  `replier` text NULL,
	  `repemail` text NULL,
	  `repurl` text NULL,
	  `repip` text NULL,
	  `repcontent` text NULL,
	  `html` int(1) NOT NULL default '0',
	  `ubb` int(1) NOT NULL default '1',
	  `emot` int(1) NOT NULL default '1',
	  `adminrepid` int(8) NOT NULL default '0',
	  `adminreplier` text NULL,
	  `adminreptime` int(11) NOT NULL default '0',
	  `adminrepcontent` text NULL,
	  `adminrepeditorid` int(8) NOT NULL default '0',
	  `adminrepeditor` text NULL,
	  `adminrepedittime` int(11) NOT NULL default '0',
	  `reppsw` tinytext NULL,
	  `empty2` text NULL,
	  `empty3` text NULL,
	  `empty4` text NULL,
	  `empty5` text NULL,
	  `empty6` text NULL,
	  `empty7` text NULL,
	  `empty8` text NULL,
	  KEY `repid` (`repid`),
	  KEY `reptime` (`reptime`),
	  KEY `blogid` (`blogid`)
	) TYPE=MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}tags` (
	  `tagid` int(10) NOT NULL default '0',
	  `tagname` text NULL,
	  `tagcounter` int(8) NOT NULL default '0',
	  `tagentry` text NULL,
	  `tagrelate` text NULL
	) TYPE=MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");


	$setup_query="
	CREATE TABLE `{$db_prefix}user` (
	  `userid` int(8) NOT NULL default '0',
	  `username` text NULL,
	  `userpsw` text NULL,
	  `regtime` int(11) NOT NULL default '0',
	  `usergroup` int(2) NOT NULL default '0',
	  `email` text NULL,
	  `homepage` text NULL,
	  `qq` int(20) NOT NULL default '0',
	  `msn` text NULL,
	  `intro` text NULL,
	  `gender` int(1) NOT NULL default '0',
	  `skype` text NULL,
	  `fromplace` text NULL,
	  `birthday` int(11) NOT NULL default '0',
	  `regip` text NULL,
	  `avatar` text NULL,
	  KEY `userid` (`userid`),
	  KEY `usergroup` (`usergroup`)
	) TYPE=MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");
	$setup_query="INSERT INTO `{$db_prefix}user` VALUES (1, '{$username}', '{$password}', {$ts}, 2, 'admin@yourname.com', 'http://www.yourname.com', 0, '', '', 0, '', '', 0, '{$ip}', '')";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}pages` (
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
	) TYPE=MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}mods` (
	`position` TEXT NULL ,
	`name` TEXT NULL ,
	`desc` TEXT NULL ,
	`active` INT( 1 ) DEFAULT '1' NOT NULL ,
	`modorder` INT( 5 ) NOT NULL ,
	`func` TEXT NULL
	) TYPE = MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");
	$setup_query="INSERT INTO `{$db_prefix}mods` VALUES ('header', 'index', '{$lang[35]}', '1', '1', 'system'), ('header', 'customrss', '{$lang[36]}', '0', '2', 'system'), ('header', 'login', '{$lang[37]}', '0', '3', 'system'), ('header', 'modpro', '{$lang[38]}', '0', '4', 'system'), ('header', 'alltags', '{$lang[39]}', '1', '5', 'system'), ('header', 'guestbook', '{$lang[40]}', '1', '6', 'system'), ('header', 'togglesidebar', '{$lang[41]}', '1', '7', 'system'), ('header', 'starred', '{$lang[42]}', '1', '20', 'system')";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");
	$setup_query="INSERT INTO `{$db_prefix}mods` VALUES ('sidebar', 'category', '{$lang[43]}', '1', '8', 'system'), ('sidebar', 'calendar', '{$lang[44]}', '1', '9', 'system'), ('sidebar', 'statistics', '{$lang[45]}', '1', '10', 'system'), ('sidebar', 'entries', '{$lang[46]}', '1', '11', 'system'), ('sidebar', 'replies', '{$lang[47]}', '1', '12', 'system'),  ('sidebar', 'columnbreak', '{$lang[85]}', '1', '12', 'system'), ('sidebar', 'link', '{$lang[48]}', '1', '13', 'system'), ('sidebar', 'archive', '{$lang[49]}', '1', '14', 'system'), ('sidebar', 'misc', '{$lang[50]}', '1', '15', 'system'), ('footer', 'copyright', '{$lang[51]}', '1', '16', 'system'), ('footer', 'mii', '{$lang[52]}', '0', '17', 'system'), ('sidebar', 'announcement', '{$lang[53]}', '0', '0', 'custom'), ('header', 'viewlinks', '{$lang[54]}', '1', '7', 'system'), ('sidebar', 'search', '{$lang[55]}', '1', '10', 'system'), ('header', 'archivelink', '{$lang[84]}', '1', '11', 'custom')";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}plugins` (
	`plid` TINYINT( 3 ) NOT NULL ,
	`plname` TINYTEXT NOT NULL ,
	`plauthor` TINYTEXT,
	`plintro` TINYTEXT,
	`plversion` TINYTEXT,
	`plauthorurl` TINYTEXT,
	`plblogversion` TINYTEXT NOT NULL,
	`active` TINYINT( 1 ),
	`pladmin` TINYINT( 1 ),
	`plregister` TINYTEXT
	) TYPE = MyISAM{$sqlcharset}";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");
	$setup_query="INSERT INTO `{$db_prefix}plugins` VALUES (1, 'viewstat', 'Bob', '{$lang[69]}', '1.0', 'http://www.bo-blog.com', '2.0.1', 1, 1, '')"; 
	$result=db_query($setup_query);
	$setup_query="INSERT INTO `{$db_prefix}plugins` VALUES (2, 'ccunion', 'Bob', 'CC Video Union plugin for Bo-Blog.', '1.1', 'http://www.bo-blog.com', '2.0.2', 0, 1, 'ubbeditor,ubbanalyseadvance,page')"; 
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");

	$setup_query="
	CREATE TABLE `{$db_prefix}upload` (
	  `fid` int(6) NOT NULL auto_increment,
	  `filepath` varchar(255) default NULL,
	  `originalname` VARCHAR( 255 ) NULL,
	  `dltime` int(8) NOT NULL default '0',
	  `uploadtime` int(11) default NULL,
	  `uploaduser` int(6) NOT NULL default '0',
	  PRIMARY KEY  (`fid`)
	) TYPE=MyISAM";
	$result=db_query($setup_query);
	if (!$result) template("<div class='log'>{$lang[1]}</div><div class='mes'>{$errmsg}</div>");
	
	template("<div class='log'>{$lang[56]}</div><div class='mes'><form action='install.php?v=4' method='post' id='frm1'>{$lang[57]}<br>{$lang[58]}<br><input type='hidden' value='{$db_server}' name='db_server'><input type='hidden' value='{$db_username}' name='db_username'><input type='hidden' value='{$db_password}' name='db_password'><input type='hidden' value='{$db_name}' name='db_name'><input type='hidden' value='{$db_prefix}' name='db_prefix'><input type='hidden' name='blogurlpath' value='{$blogurlpath}'><input type='hidden' value='{$db_410}' name='db_410'><input type='hidden' value='{$username}' name='username'><input type='hidden' value='{$password}' name='password'><input type='hidden' value='{$blogname}' name='blogname'><input type='hidden' value='".addslashes($blogdesc)."' name='blogdesc'><br><br><div align='center'><input type='hidden' value='{$newinstall}' name='newinstall'><input type='button' id='btn1' value='{$lang[21]}' onclick='submitit();' class='inbut'> <input type='reset' value='{$lang[22]}'  class='inbut'></div></form></div>");
}

if ($v=='4') {
	$config_data="<?PHP
\$db_server='$db_server';
\$db_username='$db_username';
\$db_password='$db_password';
\$db_name='$db_name';
\$db_prefix='$db_prefix';
\$db_410='$db_410';
\$db_tmpdir='temp';
\$db_defaultsessdir='0';
\$config['blogname']='$blogname';
\$config['blogdesc']='$blogdesc';
\$config['blogkeywords']='bo-blog';
\$config['blogcreatetime']='$ts';
\$config['blogurl']='$blogurlpath';
\$config['bloglogo']='http://';
\$config['blogopen']='1';
\$config['message_off']='Sorry, the blog is temporarily closed for maintenance.';
\$config['registeron']='1';
\$config['registeroffmess']='Sorry, registration has been disabled.';
\$config['onlinetime']='1800';
\$config['timezone']='8';
\$config['validation']='0';
\$config['loginvalidation']='0';
\$config['registervalidation']='0';
\$config['applylinkvalidation']='0';
\$config['closeajax']='0';
\$config['closeadminajax']='0';
\$config['noadminsession']='1';
\$config['gzip']='0';
\$config['urlrewritemethod']='0';
\$config['updatesrc']='http://www.bo-blog.com';
";
	writetofile('../data/config.php', $config_data);

	if ($newinstall!=1) 	{ //Quit	
		if (file_exists("copy/mod_config.php")) { //Repair mod_config.php
			$orgin=readfromfile("copy/mod_config.php");
			writetofile("../data/mod_config.php", $orgin);
		}	
		@rename ("install.php", "install.bak"); //Try to rename install.php
		template("<div class='log'>{$lang[61]}</div><div class='mes'><form action='' method='post' id='frm1'>{$lang[78]}<br>{$lang[79]}<br><br><div align=center><input type='button' value='{$lang[64]}' onclick='window.location=\"../index.php\";' class='inbut'></div>");
	}


	writetofile('../data/cache_adminlist.php', "<?PHP\n\$adminlist=array('1'=>'{$username}');");

	writetofile('../data/modules.php', "<?PHP\n/*--PREPENDAREA--*/\n/*--APPENDAREA--*/\n\$blogitem['announcement']=array('type'=>'block',	'name'=>'announcement', 'title'=>'{$lang[59]}', 'content'=>'{$lang[60]}', 'extend'=>1);\n\$blogitem['archivelink']=array('type'=>'link', 'url'=>'archive.php', 'text'=>'{$lang[49]}');\n");

	writetofile('../data/cache_categories.php', "<?PHP exit;?><|>0<|>{$lang[32]}<|>{$lang[33]}<|>0<|>0<|>0<|><|><|>1<|>-1<|>\n");
	
	writetofile('../data/online.php', "");

	writetofile('../data/cache_tags.php', "");

	writetofile('../data/language.php', "<?PHP\n\$langfront=\"{$language}\";\n\$langback=\"{$language}\";\n@include_once (\"lang/{$language}/common.php\");");

	writetofile('../data/cache_currentmonth.php', "");

	writetofile('../data/cache_latest.php', "<?PHP\n\$cache_latest_limit[]=array(\"blogid\"=>0, \"title\"=>\"{$lang[30]}\", \"category\"=>0, \"fulltitle\"=>\"{$lang[30]}\");\n\$cache_latest_all[]=array(\"blogid\"=>0, \"title\"=>\"{$lang[30]}\", \"category\"=>0, \"fulltitle\"=>\"{$lang[30]}\");?>");

	writetofile('../data/cache_replies.php', "");

	writetofile('../data/plugin_enabled.php', "<?PHP\n");

	$copylangorigin=array('{langcopy0}','{langcopy1}','{langcopy2}','{langcopy3}','{langcopy4}','{langcopy5}','{langcopy6}','{langcopy7}','{langcopy8}','{langcopy9}','{langcopy10}','{langcopy11}','{langcopy12}','{langcopy13}','{langcopy14}','{langcopy15}','{langcopy16}');
	$linkfrom=@parse_url($blogurlpath);
	$langcopy[16]=$linkfrom['host'];
	$file_list=@file('filelist.txt');
	for ($i=0; $i<count($file_list); $i++) {
		$file_s=trim($file_list[$i]);
		if (file_exists("copy/{$file_s}") && !is_dir("copy/{$file_s}")) {
			$orgin=readfromfile("copy/{$file_s}");
			$orgin=str_replace($copylangorigin, $langcopy, $orgin);
			writetofile("../data/{$file_s}", $orgin);
			unset ($orgin);
		}
	}

	//Try to rename install.php
	@rename ("install.php", "install.bak");
	template("<div class='log'>{$lang[61]}</div><div class='mes'><form action='' method='post' id='frm1'>{$lang[62]}<br>{$lang[63]}<br><br><div align=center><input type='button' value='{$lang[64]}' onclick='window.location=\"../index.php\";' class='inbut'></div>");
}


function template ($body) {
	global $newinstall, $lang;
	$bbb=<<<eot
<html xmlns="http://www.w3.org/1999/xhtml" lang="UTF-8">
<head>
<link rel="stylesheet" rev="stylesheet" href="install.css" type="text/css" media="all" />
<title>Bo-Blog Installation</title>
<script type="text/javascript">
function submitit(){
	document.getElementById('btn1').value='{$lang[75]}';
	document.getElementById('btn1').disabled='disabled';
	document.getElementById('frm1').submit();

}
function checkandsubmit(){
	if (document.getElementById('password') && document.getElementById('confirmpassword')) {
		if (document.getElementById('password').value != document.getElementById('confirmpassword').value || document.getElementById('password').value=='' || document.getElementById('confirmpassword').value=='') { 
			alert("{$lang[81]}");
			document.getElementById('password').value='';
			document.getElementById('confirmpassword').value='';
		}
		else submitit();
	} else submitit();
}

function showlayer(id){
  try{
    var panel=document.getElementById(id);
    if(panel){
      if(panel.style.display=='none'){
        panel.style.display='block';
      }else{
        panel.style.display='none';
      }
    }
  }catch(e){}
}
</script>
</head>
<body>
<div id="titles">
	{$lang[0]}
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

function readfromfile($file_name) { //File Reading
	if (file_exists($file_name)) {
		$filenum=fopen($file_name,"r");
		$sizeofit=filesize($file_name);
		if ($sizeofit<=0) return '';
		@flock($filenum,LOCK_EX);
		$file_data=fread($filenum, $sizeofit);
		fclose($filenum);
		return $file_data;
	} else return '';
}

function writetofile ($filename, $data) { //File Writing
	$filenum=@fopen($filename,"w");
	if (!$filenum) {
		return false;
	}
	flock($filenum,LOCK_EX);
	$file_data=fwrite($filenum,$data);
	fclose($filenum);
	return true;
}

function addsd($array) { //Auto Adding Slashes
	global $mqgpc_status;
	if ($mqgpc_status!=0) return $array;
	if (is_array($array)) {
		foreach($array as $key=>$value){
			if(!is_array($value)){
				$array[$key]=addslashes($value);
			}else{
				addsd($value);
			}
		}
	} else $array=addslashes($array);
	return $array;
}

?>