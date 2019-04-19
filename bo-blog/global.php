<?PHP
/* -----------------------------------------------------
Bo-Blog 2 : The Blog Reloaded.
<<A Bluview Technology Product>>
PHP+MySQL blog system.
Offical site: http://www.bo-blog.com
Copyright (c) Bob Shen 中国－上海
In memory of my university life
------------------------------------------------------- */

/* Version and Copyright Declaration
    You are not allowed to change anything in this part. */
$blogversion="2.1.1 Release";
$codeversion="2.1.1.3626.3";
$codename="pilot";
//You can change anything below as you wish. Good Luck!

if (file_exists('install/install.php')) {
	@header("Content-Type: text/html; charset=utf-8");
	die ("警告：安装文件install/install.php仍然在您的服务器上，请立刻将其改名或删除！<br>警告：安裝程式install/install.php仍然在您的伺服器上，請立刻將其改名或刪除！<br>NOTICE: Installation file: install/install.php is still on your server. Please DELETE or RENAME it immediately.");
}

error_reporting(E_ERROR | E_WARNING | E_PARSE);
unregister_GLOBALS(); //When register_globals=On
@set_magic_quotes_runtime (0);
$mqgpc_status=get_magic_quotes_gpc();
define("VALIDREQUEST",1);
if (!defined('allowCache')) {
	@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	@header("Cache-Control: no-store, no-cache, must-revalidate");
	@header("Pragma: no-cache");
}
if (stristr($_SERVER['SCRIPT_FILENAME'], 'global.php')) die ("Access Denied.");

$ajax=$_REQUEST['ajax']; //If the page is in Ajax request mode

require_once ("data/mod_config.php");
require_once ("data/config.php");
if (file_exists("data/functionlock.php")) {
	require_once ("data/functionlock.php");
}
require_once ("inc/url.php");
require_once ("inc/db.php");
require_once ("inc/boblog_class_run.php");

//Load language
if (defined('isIndex')) {
	if ($_REQUEST['lang']) {
		$customlang=basename($_REQUEST['lang']);
		setcookie('bloglanguage', $customlang);
	}
	else $customlang=basename($_COOKIE['bloglanguage']);
	if (!empty($customlang) && file_exists("lang/{$customlang}/common.php")) {
		include_once ("lang/{$customlang}/common.php");
		$langfront=$customlang;
	}
	else {
		require_once ("data/language.php"); 
	}
	if (is_file("data/langspec.php")) require_once ("data/langspec.php"); //Load customized language data
} else {
	require_once ("data/language.php"); 
}


//Auto detect mirror site
if (!defined('VALIDADMIN')) {
	//Set Base URL
	if (!$config['blogurl'] || $config['blogurl']=='http://') { // $config['blogurl'] not set
		@header("Content-Type: text/html; charset=utf-8");
		die ($lnc[292]);
	} else {
		$config['blogurl']=str_replace('{host}', $_SERVER['HTTP_HOST'], $config['blogurl']);
	}
	$baseurl="<base href=\"{$config['blogurl']}/\" />";
}

/* data/config.php may be corrupted in some servers */
if (!$db_server) {
	@header("Content-Type: text/html; charset=utf-8");
	die ($lnc[293]);
}
/* data/config.php check finished */

/* data/mod_config.php may be corrupted in some servers */
if (!$mbcon['entrynum'] && !defined('VALIDADMIN') && !defined('isLogin')) {
	@header("Content-Type: text/html; charset=utf-8");
	die ($lnc[292]);
}
/* data/mod_config.php check finished */


//Load template
if ($_REQUEST['tem']) {
	$customtemplate=basename($_REQUEST['tem']);
	setcookie('blogtemplate', $customtemplate);
}
else $customtemplate=basename($_COOKIE['blogtemplate']);
if (!empty($customtemplate) && file_exists("template/{$customtemplate}/info.php")) {
	require ("template/{$customtemplate}/info.php");
}
else {
	require ("data/mod_template.php");
}
define ('elementfile', $template['structure']); //2006-7-2 Seurity Fix, 2006-7-5 modified

acceptcookie("userid,userpsw");
$userid=safe_convert($userid);
$userpsw=safe_convert($userpsw);

$blog=new boblog;

//Initialize Time Info
$nowtime['timestamp']=time();
$nowtime+=array('year'=>gmdate('Y', $nowtime['timestamp']+3600*$config['timezone']), 'month'=>gmdate('n', $nowtime['timestamp']+3600*$config['timezone']), 'day'=>gmdate('j', $nowtime['timestamp']+3600*$config['timezone']), 'Ymd'=>gmdate('Ymd', $nowtime['timestamp']+3600*$config['timezone']), 'Ym'=>gmdate('Ym', $nowtime['timestamp']+3600*$config['timezone']));

//Sessions and Cookies
$userdetail=array();
if (empty($userid) || empty($userpsw)) {
	$userdetail['usergroup']=0;
	$userdetail['userid']=-1;
	$logstat=0;
}
else {
	$userdetail=$blog->getbyquery("SELECT * FROM `{$db_prefix}user` WHERE `userid`='{$userid}' AND `userpsw`='{$userpsw}'");
	if (!$userdetail) {
		$userdetail['usergroup']=0;
		$userdetail['userid']=-1;
		$logstat=0;
	}
	else $logstat=1;
}
if ($mbcon['enableopenid']=='1') { 
	$openidloginstat=($logstat==0 && $_COOKIE['openid_url_id']) ? 1 : 0;
} else $openidloginstat=0;

//Load User Group Permission Cache
$permission=array();
if (file_exists("data/usergroup{$userdetail['usergroup']}.php")) @include_once("data/usergroup{$userdetail['usergroup']}.php");
else include_once("data/usergroup0.php");
if (!defined('isLogin')) checkpermission('visit'); //Check 'Browse' permission

if ($permission['ViewPHPError']==0) error_reporting(0);
if ($permission['CloseSecurityCode']==1 && !defined('VALIDADMIN')) { //Disable security code for some usergroups
	$config['validation']='0';
	$config['loginvalidation']='0';
	$config['applylinkvalidation']='0';
}

//Get IP
$ip_tmp=$_SERVER['REMOTE_ADDR'];
$ip_tmp1 = $_SERVER['HTTP_X_FORWARDED_FOR'];
if ($ip_tmp1!= "" && $ip_tmp1!= "unknown") $userdetail['ip']=$ip_tmp1;
else $userdetail['ip']=$ip_tmp;
$userdetail['ip']=addslashes($userdetail['ip']);

//Get Statistics
$statistics=$blog->getsinglevalue("{$db_prefix}counter");


//Who's online
if (!defined('noCounter')) { //trackback, rss, sitemap are not regarded as normal visits
	$afilename="data/online.php"; 
	$onlineusers=$nowonline=array(); //2006-11-22 Security fix, 2006-11-25 modified
	$online_all=@file($afilename);
	for($i=0;$i<count($online_all);$i++){ 
		$oldip=explode("|",$online_all[$i]);
		if (trim($oldip[2])=='') continue;
		if (gmdate("Ymd", $oldip[2]+$config['timezone']*3600+86400)==$nowtime['Ymd']) {
			savehistory(gmdate("Ymd", $oldip[2]+$config['timezone']*3600), $statistics['today']);
			$statistics['today']=0;
			break; //This will clear all visitors since yesterday, but will save visitors from today
		}
		$onlinetime=$nowtime['timestamp']-$oldip[2]; 
		if ($oldip[1]!=$userdetail['ip'] && $onlinetime<=$config['onlinetime']) {
			$nowonline[]=$online_all[$i];
			$onlineusers[]=array('userid'=>$oldip[3], 'username'=>$oldip[4], 'activetime'=>$oldip[2], 'ip'=>$oldip[1]);
		} elseif ($oldip[1]==$userdetail['ip']) {
			$nowonline[]="<?PHP exit;?>|{$userdetail['ip']}|{$nowtime['timestamp']}|{$userdetail['userid']}|{$userdetail['username']}|\n";
			$onlineusers[]=array('userid'=>$oldip[3], 'username'=>$oldip[4], 'activetime'=>$nowtime['timestamp'], 'ip'=>$oldip[1]);
			$tmp_checked_current=1;
		}
	}
	if ($tmp_checked_current!=1) {
		$nowonline[]="<?PHP exit;?>|{$userdetail['ip']}|{$nowtime['timestamp']}|{$userdetail['userid']}|{$userdetail['username']}|\n";
		$onlineusers[]=array('userid'=>$userdetail['userid'], 'username'=>$userdetail['username'], 'activetime'=>$nowtime['timestamp'], 'ip'=>$userdetail['ip']);
		$blog->query("UPDATE `{$db_prefix}counter` SET `today`={$statistics['today']}+1");
		$statistics['today']+=1;
	}
	writetofile($afilename, @implode("", $nowonline));
	$statistics['nowusers']=count($nowonline);
	$statistics['total']+=$statistics['today'];
}

//Get Categories
if (file_exists('data/cache_categories.php')) {
	$categories=$categorynames=array();
	$cates_lines=@file('data/cache_categories.php');
	for ($i=0; $i<count($cates_lines); $i++) {
		@list($unuse, $tmp_result['cateid'], $tmp_result['catename'], $tmp_result['catedesc'], $tmp_result['cateproperty'], $tmp_result['cateorder'], $tmp_result['catemode'], $tmp_result['cateurl'], $tmp_result['cateicon'], $tmp_result['catecount'], $tmp_result['parentcate'], $tmp_result['cateurlname'])=@explode('<|>', $cates_lines[$i]);
		if ($permission['SeeSecretCategory']==1 || $tmp_result['cateproperty']!='1') $result[$i]=$tmp_result;
		if ($tmp_result['cateurlname']) $categorynames[$tmp_result['cateurlname']]=$tmp_result['cateid'];
	}
	foreach ((array)$result as $row) {
		$catid=$row['cateid'];
		$categories[$catid]=array("catename"=>stripslashes($row['catename']), "catedesc"=>stripslashes($row['catedesc']), "cateproperty"=>$row['cateproperty'], "cateorder"=>$row['cateorder'], "catemode"=>$row['catemode'], "cateid"=>$row['cateid'], "cateurl"=>$row['cateurl'], "cateicon"=>$row['cateicon'], "catecount"=>$row['catecount'], "parentcate"=>$row['parentcate'], "cateurlname"=>$row['cateurlname']);
		if ($row['catemode']==1) { //Sub-category
			$row['catename']='|- '.$row['catename'];
			$categories[$row['parentcate']]['subcates'][]=$catid; //Parent category get its sub-categories
			if ($mbcon['parentcatenum']=='1') $categories[$row['parentcate']]['catecount']+=$row['catecount']; //Add blog volumes into its parent category if necessary
		}
		if ($row['cateurl']) $row['catename'].=$lnc[0];
		if ($row['cateproperty']==1) $row['catename'].=$lnc[1];
		unset ($catid);
		$arrayvalue_categories[]=$row['cateid']; //For edit use only
		$arrayoption_categories[]=$row['catename']; //For edit use only
	}
	unset ($result, $plusquery);
}


/*Start Func Lib*/
function acceptcookie($valuedesc, $overwrite=0) {
	global $mqgpc_status;
	$values=@explode(',', $valuedesc);
	if (!is_array($values)) return;
	foreach ($values as $valuename) {
		global $$valuename;
		if ($overwrite==0 && isset($$valuename)) continue;
		if ($mqgpc_status==0) $$valuename=addsd($_COOKIE[$valuename]);
		else $$valuename=$_COOKIE[$valuename];
		$$valuename=str_replace('`', '&#96;', $$valuename);
	}
}

function acceptrequest($valuedesc, $overwrite=0, $type="both") {
	global $mqgpc_status;
	$values=@explode(',', $valuedesc);
	if (!is_array($values)) return;
	foreach ($values as $valuename) {
		global $$valuename;
		if ($overwrite==0 && isset($$valuename)) continue;
		if ($mqgpc_status==0) {
			if ($type=="both") $$valuename=addsd($_REQUEST[$valuename]);
			elseif ($type=="post") $$valuename=addsd($_POST[$valuename]);
			else $$valuename=addsd($_GET[$valuename]);
		}
		else {
			if ($type=="both") $$valuename=$_REQUEST[$valuename];
			elseif ($type=="post") $$valuename=$_POST[$valuename];
			else $$valuename=$_GET[$valuename];
		}
		$$valuename=str_replace('`', '&#96;', $$valuename);
	}
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

function readfromfile($file_name) { //File Reading
	if (file_exists($file_name)) {
		if (PHP_VERSION >= "4.3.0") return file_get_contents($file_name);
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

function checkpermission($permission_name) {
	global $permission, $in_ajax_mode, $lnc;
	if ($permission[$permission_name]!=1) {
		if ($in_ajax_mode!=1) {
			catcherror("{$lnc[2]} <br/>{$lnc[3]}  {$permission['gpname']}");
		} else {
			die($lnc[2]);
		}
	}
}

function savehistory($targetdate, $targetnum) { //Save history of visitors
	global $blog, $db_prefix;
	$targetnum=floor($targetnum);
	$tmp=$blog->countbyquery("SELECT (visit) FROM `{$db_prefix}history` WHERE `hisday`='{$targetdate}'");
	if ($tmp) {
		$tmp=floor($tmp)+$targetnum;
		$blog->query("UPDATE `{$db_prefix}history` SET `visit`='{$tmp}' WHERE `hisday`='{$targetdate}'");
	}
	else $blog->query("INSERT INTO `{$db_prefix}history` VALUES ({$targetdate}, {$targetnum})");
	$blog->query("UPDATE `{$db_prefix}counter` SET  `total`=`total`+{$targetnum}");
}


function addbar ($barname, $actions) { //Generate a module
	$addto="section_{$barname}";
	global $$addto, $blogitem, $addbarbehavior, $userdetail;
	if ($addbarbehavior=='array') {
		return;
	}
	foreach ($actions as $eachitem) {
		if ($blogitem[$eachitem]['permitgp']!='') {
			$allowedgp=@explode('|', $blogitem[$eachitem]['permitgp']);
			if (!in_array($userdetail['usergroup'], $allowedgp)) continue;
		}
		if (($blogitem[$eachitem]['indexonly']==1 && !strstr($_SERVER['SCRIPT_FILENAME'], 'index.php')) || ($blogitem[$eachitem]['indexonly']==2 && strstr($_SERVER['SCRIPT_FILENAME'], 'index.php'))) continue;
		if ($blogitem[$eachitem]['type']=='link') {
			$plus='';
			if ($blogitem[$eachitem]['target']) $plus.=" target=\"".$blogitem[$eachitem]['target']."\"";
			if ($blogitem[$eachitem]['title']) $plus.=" title=\"".$blogitem[$eachitem]['title']."\"";
			if ($blogitem[$eachitem]['onclick']) $plus.=" onclick=\"".$blogitem[$eachitem]['onclick']."\"";
			$spanid=str_replace('%', '_', urlencode(str_replace('.php', '', $blogitem[$eachitem]['url'])));
			${$addto}[]="<span id=\"nav_{$spanid}\"><a href=\"{$blogitem[$eachitem]['url']}\" {$plus}><span id=\"navitem_{$spanid}\">{$blogitem[$eachitem]['text']}</span></a></span>";
		} elseif  ($blogitem[$eachitem]['type']=='function') {
			eval("\${$addto}[]={$blogitem[$eachitem]['userfunction']}();");
		} elseif  ($blogitem[$eachitem]['type']=='html') {
			$tmp_code=evalmycode($blogitem[$eachitem]['code']);
			${$addto}[]=$tmp_code;
			unset ($tmp_code);
		} elseif  ($blogitem[$eachitem]['type']=='block') {
			${$addto}[]=$blogitem[$eachitem];
		} elseif  ($blogitem[$eachitem]['type']=='extraheader') {
			${$addto}[]=evalmycode($blogitem[$eachitem]['code']);
		} elseif  ($blogitem[$eachitem]['type']=='plugin') {
			$eachitem=basename($eachitem);
			if (file_exists("plugin/{$eachitem}/source.php")) include ("plugin/{$eachitem}/source.php");
		}
	}
}

function safe_convert($string, $html=0, $filterslash=0) { //Words Filter
	if ($html==0) {
		$string=htmlspecialchars($string, ENT_QUOTES);
		$string=str_replace("<","&lt;",$string);
		$string=str_replace(">","&gt;",$string);
		if ($filterslash==1) $string=str_replace("\\", '&#92;', $string);
	} else {
		$string=addslashes($string);
		if ($filterslash==1) $string=str_replace("\\\\", '&#92;', $string);
	}
	$string=str_replace("\r","<br/>",$string);
	$string=str_replace("\n","",$string);
	$string=str_replace("\t","&nbsp;&nbsp;",$string);
	$string=str_replace("  ","&nbsp;&nbsp;",$string);
	$string=str_replace('|', '&#124;', $string);
	$string=str_replace("&amp;#96;","&#96;",$string);
	$string=str_replace("&amp;#92;","&#92;",$string);
	$string=str_replace("&amp;#91;","&#91;",$string);
	$string=str_replace("&amp;#93;","&#93;",$string);
	return $string;
}

function safe_invert($string, $html=0) { //Transfer the converted words into editable characters
	if ($html==0) {
		$string = str_replace("<br/>","\r",$string);
	} else {
		$string = str_replace("<br/>","\r",$string);
		$string = str_replace("&nbsp;"," ",$string);
		$string = str_replace("&","&amp;",$string);
		$string=preg_replace("/\[code\](.+?)\[\/code\]/ise", "'[code]'.str_replace('&amp;', '&', '\\1').'[/code]'", $string);
	}
	$string = str_replace("&nbsp;"," ",$string);
	return $string;
}

function msubstr($str,$start,$end,$len=0) { //UTF-8 Cutting
	preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $info);
	$lens=sizeof($info[0]);
	if ($len==1) return array(join("",array_slice($info[0],$start,$end)), $lens);
	else return join("",array_slice($info[0],$start,$end));
}


function catchsuccess ($tip, $returnurl=false) {
	global $ajax, $lnc;
	@header("Content-Type: text/html; charset=utf-8");
	if ($ajax=='on') die ("<boblog_ajax::success>".$tip);
	$t=new template;
	$t->showtips($lnc[6], $tip, $returnurl, true);
}

function catcherror ($error, $enableautojump=true) {
	global $ajax, $lnc;
	if (!empty($error)) {
		@header("Content-Type: text/html; charset=utf-8");
		if ($ajax=='on') die ("<boblog_ajax::error>".strip_tags($error));
		$t=new template;
		$t->showtips($lnc[4], $error, "{$lnc[5]}|<", $enableautojump);
	}
}

function catchsuccessandfetch ($tip, $url) {
	global $ajax, $lnc;
	@header("Content-Type: text/html; charset=utf-8");
	if ($ajax!='on') { //This function should be used in ajax mode only
		return;
	}
	die ("<boblog_ajax::success>".$tip."<boblog_ajax::fetch>".$url);
}

function urlconvert($url, $defaultprefix="http://") { //Turn url without http:// to a valid Internet link
	if (trim($url)=='') return $url; //Do not convert an empty value
	$validurlprefix=array('http', 'https', 'ftp', 'tencent', 'news', 'ed2k');
	$url_parts=@explode('://', $url);
	if (!@in_array($url_parts[0], $validurlprefix)) $url=$defaultprefix.$url;
	return $url;
}

function trimplus ($str) { //Trim all invisible words
	$str=str_replace('　', '  ', $str);
	$str=trim($str);
	$str=trim($str,"\x00..\x1F");
	return $str;
}

function makeaquery ($array, $each, $connection='OR') { //To build a query containing a group of validators
	foreach ($array as $item) {
		$build_element[]=str_replace('%s', $item, $each);
	}
	$output=@implode(" {$connection} ", $build_element);
	return $output;
}

function get_tag_size($hits, $max) { //To decide how big the tag name has to be displayed
	global $mbcon;
	if ($max==0) $max=1;
	$fsize=floor(($hits/$max)*$mbcon['tagmaxsize']);
	if ($fsize<$mbcon['tagminsize']) $fsize=$mbcon['tagminsize'];
	return $fsize;
}

function getemot ($matches) {//Emot
	global $myemots;
	$currentemot=$matches[1];
	$emotimage=$myemots[$currentemot]['image'];
	return "<img src=\"images/emot/{$emotimage}\" border=\"0\" alt=\"$currentemot\" />";
}

function get_time_unix ($date, $destination="stamp") { //Convert an iso8601 date into unix time format, or vice versa
	if ($destination=="stamp") {
		global $config;
		$year=substr($date, 0, 4);
		$month=substr($date, 4, 2);
		$day=substr($date, 6, 2);
		$hour=substr($date, 9, 2);
		$minute=substr($date, 12, 2);
		$second=substr($date, 15, 2);
		$timestamp=gmmktime((integer)$hour, (integer)$minute, (integer)$second, (integer)$month, (integer)$day,  (integer)$year)-$config['timezone']*3600;
	} else {
		$timestamp=gmdate("Ymd\TH:i:s\Z", $date);
	}
	return $timestamp;
}

function strip_ubbs ($str) {
	$str=preg_replace("/\[(.+?)\]/is", "", $str);
	$str=preg_replace("/&(.+?);/is", "", $str);
	return $str;
}

//Calendar functions * 2
function monthly ($month,$year) {
	$firstdate=mktime(0, 0, 0, $month, 1, $year);
	$first_day=date("w", $firstdate);
	$lastdate=date("t", $firstdate);
	$last_day=date("w", mktime (0,0,0,$month,$lastdate,$year));
	$end_blank=6-$last_day; 
	$padstart=$first_day+$lastdate;
	$padend=$padstart+$end_blank;
	$all_date=range(1, $lastdate);
	$all_date=array_pad ($all_date, -$padstart, '');
	$all_date=array_pad ($all_date, $padend, '');
	return $all_date;
}

function makecalendar ($month, $year, $month_calendar, $lunarstream='') {
	global $nowtime, $mbcon, $config;
	$all_date=monthly($month, $year);
	$weekline=count($all_date)/7;
	for ($i=0; $i<$weekline; $i++) {
		$chart.="<tr class=\"calendar-weekdays\">";
		for ($j=0; $j<7; $j++) {
			$currentdate=$all_date[$i*7+$j];
			if ($j==0) $class="calendar-sunday"; //Sunday
			elseif ($j==6) $class="calendar-saturday"; //Saturday
			else $class="calendar-day"; //workdays
			$outurl=getlink_date($year, $month, $currentdate);
			if (@in_array($currentdate, $month_calendar)) $ca_sh="<a href=\"{$outurl}\" rel=\"noindex,nofollow\">{$currentdate}</a>";
			else $ca_sh=$currentdate;
			if (is_array($lunarstream)) {
				if ($mbcon['lunarcalendar']==2) $ca_sh="<span title='{$lunarstream[$currentdate]}'>{$ca_sh}</span>";
				else $ca_sh.='<br/>'.$lunarstream[$currentdate];
			}
			if ($currentdate!=='') $chart.="<td id=\"cal{$currentdate}\" class=\"$class\">{$ca_sh}</td>";
			else $chart.="<td class=\"$class\">{$ca_sh}</td>";
		}
		$chart.="</tr>";
	}
	return $chart;
}

function lunarcalendar ($month, $year) {
	global $lnlunarcalendar;
	/*Lunar calendar 博大精深的农历
	原始数据和算法思路来自 S&S Lab http://www.focus-2000.com 可惜网站好像关了
	*/
	//农历每月的天数。每个元素为一年。每个元素中的数据为：[0]是闰月在哪个月，0为无闰月；[1]到[13]是每年12或13个月的每月天数；[14]是当年的天干次序，[15]是当年的地支次序
	$everymonth=array( 0=>array(8,0,0,0,0,0,0,0,0,0,0,0,29,30,7,1), 1=>array(0,29,30,29,29,30,29,30,29,30,30,30,29,0,8,2), 2=>array(0,30,29,30,29,29,30,29,30,29,30,30,30,0,9,3), 3=>array(5,29,30,29,30,29,29,30,29,29,30,30,29,30,10,4), 4=>array(0,30,30,29,30,29,29,30,29,29,30,30,29,0,1,5), 5=>array(0,30,30,29,30,30,29,29,30,29,30,29,30,0,2,6), 6=>array(4,29,30,30,29,30,29,30,29,30,29,30,29,30,3,7), 7=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,4,8), 8=>array(0,30,29,29,30,30,29,30,29,30,30,29,30,0,5,9), 9=>array(2,29,30,29,29,30,29,30,29,30,30,30,29,30,6,10), 10=>array(0,29,30,29,29,30,29,30,29,30,30,30,29,0,7,11), 11=>array(6,30,29,30,29,29,30,29,29,30,30,29,30,30,8,12), 12=>array(0,30,29,30,29,29,30,29,29,30,30,29,30,0,9,1), 13=>array(0,30,30,29,30,29,29,30,29,29,30,29,30,0,10,2), 14=>array(5,30,30,29,30,29,30,29,30,29,30,29,29,30,1,3), 15=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0,2,4), 16=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,3,5), 17=>array(2,30,29,29,30,29,30,30,29,30,30,29,30,29,4,6), 18=>array(0,30,29,29,30,29,30,29,30,30,29,30,30,0,5,7), 19=>array(7,29,30,29,29,30,29,29,30,30,29,30,30,30,6,8), 20=>array(0,29,30,29,29,30,29,29,30,30,29,30,30,0,7,9), 21=>array(0,30,29,30,29,29,30,29,29,30,29,30,30,0,8,10), 22=>array(5,30,29,30,30,29,29,30,29,29,30,29,30,30,9,11), 23=>array(0,29,30,30,29,30,29,30,29,29,30,29,30,0,10,12), 24=>array(0,29,30,30,29,30,30,29,30,29,30,29,29,0,1,1), 25=>array(4,30,29,30,29,30,30,29,30,30,29,30,29,30,2,2), 26=>array(0,29,29,30,29,30,29,30,30,29,30,30,29,0,3,3), 27=>array(0,30,29,29,30,29,30,29,30,29,30,30,30,0,4,4), 28=>array(2,29,30,29,29,30,29,29,30,29,30,30,30,30,5,5), 29=>array(0,29,30,29,29,30,29,29,30,29,30,30,30,0,6,6), 30=>array(6,29,30,30,29,29,30,29,29,30,29,30,30,29,7,7), 31=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0,8,8), 32=>array(0,30,30,30,29,30,29,30,29,29,30,29,30,0,9,9), 33=>array(5,29,30,30,29,30,30,29,30,29,30,29,29,30,10,10), 34=>array(0,29,30,29,30,30,29,30,29,30,30,29,30,0,1,11), 35=>array(0,29,29,30,29,30,29,30,30,29,30,30,29,0,2,12), 36=>array(3,30,29,29,30,29,29,30,30,29,30,30,30,29,3,1), 37=>array(0,30,29,29,30,29,29,30,29,30,30,30,29,0,4,2), 38=>array(7,30,30,29,29,30,29,29,30,29,30,30,29,30,5,3), 39=>array(0,30,30,29,29,30,29,29,30,29,30,29,30,0,6,4), 40=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0,7,5), 41=>array(6,30,30,29,30,30,29,30,29,29,30,29,30,29,8,6), 42=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0,9,7), 43=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,10,8), 44=>array(4,30,29,30,29,30,29,30,29,30,30,29,30,30,1,9), 45=>array(0,29,29,30,29,29,30,29,30,30,30,29,30,0,2,10), 46=>array(0,30,29,29,30,29,29,30,29,30,30,29,30,0,3,11), 47=>array(2,30,30,29,29,30,29,29,30,29,30,29,30,30,4,12), 48=>array(0,30,29,30,29,30,29,29,30,29,30,29,30,0,5,1), 49=>array(7,30,29,30,30,29,30,29,29,30,29,30,29,30,6,2), 50=>array(0,29,30,30,29,30,30,29,29,30,29,30,29,0,7,3), 51=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0,8,4), 52=>array(5,29,30,29,30,29,30,29,30,30,29,30,29,30,9,5), 53=>array(0,29,30,29,29,30,30,29,30,30,29,30,29,0,10,6), 54=>array(0,30,29,30,29,29,30,29,30,30,29,30,30,0,1,7), 55=>array(3,29,30,29,30,29,29,30,29,30,29,30,30,30,2,8), 56=>array(0,29,30,29,30,29,29,30,29,30,29,30,30,0,3,9), 57=>array(8,30,29,30,29,30,29,29,30,29,30,29,30,29,4,10), 58=>array(0,30,30,30,29,30,29,29,30,29,30,29,30,0,5,11), 59=>array(0,29,30,30,29,30,29,30,29,30,29,30,29,0,6,12), 60=>array(6,30,29,30,29,30,30,29,30,29,30,29,30,29,7,1), 61=>array(0,30,29,30,29,30,29,30,30,29,30,29,30,0,8,2), 62=>array(0,29,30,29,29,30,29,30,30,29,30,30,29,0,9,3), 63=>array(4,30,29,30,29,29,30,29,30,29,30,30,30,29,10,4), 64=>array(0,30,29,30,29,29,30,29,30,29,30,30,30,0,1,5), 65=>array(0,29,30,29,30,29,29,30,29,29,30,30,29,0,2,6), 66=>array(3,30,30,30,29,30,29,29,30,29,29,30,30,29,3,7), 67=>array(0,30,30,29,30,30,29,29,30,29,30,29,30,0,4,8), 68=>array(7,29,30,29,30,30,29,30,29,30,29,30,29,30,5,9), 69=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,6,10), 70=>array(0,30,29,29,30,29,30,30,29,30,30,29,30,0,7,11), 71=>array(5,29,30,29,29,30,29,30,29,30,30,30,29,30,8,12), 72=>array(0,29,30,29,29,30,29,30,29,30,30,29,30,0,9,1), 73=>array(0,30,29,30,29,29,30,29,29,30,30,29,30,0,10,2), 74=>array(4,30,30,29,30,29,29,30,29,29,30,30,29,30,1,3), 75=>array(0,30,30,29,30,29,29,30,29,29,30,29,30,0,2,4), 76=>array(8,30,30,29,30,29,30,29,30,29,29,30,29,30,3,5), 77=>array(0,30,29,30,30,29,30,29,30,29,30,29,29,0,4,6), 78=>array(0,30,29,30,30,29,30,30,29,30,29,30,29,0,5,7), 79=>array(6,30,29,29,30,29,30,30,29,30,30,29,30,29,6,8), 80=>array(0,30,29,29,30,29,30,29,30,30,29,30,30,0,7,9), 81=>array(0,29,30,29,29,30,29,29,30,30,29,30,30,0,8,10), 82=>array(4,30,29,30,29,29,30,29,29,30,29,30,30,30,9,11), 83=>array(0,30,29,30,29,29,30,29,29,30,29,30,30,0,10,12), 84=>array(10,30,29,30,30,29,29,30,29,29,30,29,30,30,1,1), 85=>array(0,29,30,30,29,30,29,30,29,29,30,29,30,0,2,2), 86=>array(0,29,30,30,29,30,30,29,30,29,30,29,29,0,3,3), 87=>array(6,30,29,30,29,30,30,29,30,30,29,30,29,29,4,4), 88=>array(0,30,29,30,29,30,29,30,30,29,30,30,29,0,5,5), 89=>array(0,30,29,29,30,29,29,30,30,29,30,30,30,0,6,6), 90=>array(5,29,30,29,29,30,29,29,30,29,30,30,30,30,7,7), 91=>array(0,29,30,29,29,30,29,29,30,29,30,30,30,0,8,8), 92=>array(0,29,30,30,29,29,30,29,29,30,29,30,30,0,9,9), 93=>array(3,29,30,30,29,30,29,30,29,29,30,29,30,29,10,10), 94=>array(0,30,30,30,29,30,29,30,29,29,30,29,30,0,1,11), 95=>array(8,29,30,30,29,30,29,30,30,29,29,30,29,30,2,12), 96=>array(0,29,30,29,30,30,29,30,29,30,30,29,29,0,3,1), 97=>array(0,30,29,30,29,30,29,30,30,29,30,30,29,0,4,2), 98=>array(5,30,29,29,30,29,29,30,30,29,30,30,29,30,5,3), 99=>array(0,30,29,29,30,29,29,30,29,30,30,30,29,0,6,4), 100=>array(0,30,30,29,29,30,29,29,30,29,30,30,29,0,7,5), 101=>array(4,30,30,29,30,29,30,29,29,30,29,30,29,30,8,6), 102=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0,9,7), 103=>array(0,30,30,29,30,30,29,30,29,29,30,29,30,0,10,8), 104=>array(2,29,30,29,30,30,29,30,29,30,29,30,29,30,1,9), 105=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,2,10), 106=>array(7,30,29,30,29,30,29,30,29,30,30,29,30,30,3,11), 107=>array(0,29,29,30,29,29,30,29,30,30,30,29,30,0,4,12), 108=>array(0,30,29,29,30,29,29,30,29,30,30,29,30,0,5,1), 109=>array(5,30,30,29,29,30,29,29,30,29,30,29,30,30,6,2), 110=>array(0,30,29,30,29,30,29,29,30,29,30,29,30,0,7,3), 111=>array(0,30,29,30,30,29,30,29,29,30,29,30,29,0,8,4), 112=>array(4,30,29,30,30,29,30,29,30,29,30,29,30,29,9,5), 113=>array(0,30,29,30,29,30,30,29,30,29,30,29,30,0,10,6), 114=>array(9,29,30,29,30,29,30,29,30,30,29,30,29,30,1,7), 115=>array(0,29,30,29,29,30,29,30,30,30,29,30,29,0,2,8), 116=>array(0,30,29,30,29,29,30,29,30,30,29,30,30,0,3,9), 117=>array(6,29,30,29,30,29,29,30,29,30,29,30,30,30,4,10), 118=>array(0,29,30,29,30,29,29,30,29,30,29,30,30,0,5,11), 119=>array(0,30,29,30,29,30,29,29,30,29,29,30,30,0,6,12), 120=>array(4,29,30,30,30,29,30,29,29,30,29,30,29,30,7,1) );
	//农历天干
	$mten=$lnlunarcalendar['tiangan'];
	//农历地支
	$mtwelve=$lnlunarcalendar['dizhi'];
	//农历月份
	$mmonth=$lnlunarcalendar['month'];
	//农历日
	$mday=$lnlunarcalendar['day'];
	//阳历总天数 至1900年12月21日
	$total=69*365+17+11; //1970年1月1日前的就不算了
	if ($year=="" || $month=="" || ($year<1970 or $year>2020)) return ''; //超出这个范围不计算
	//计算到所求日期阳历的总天数-自1900年12月21日始
	//先算年的和
	for ($y=1970; $y<$year;$y++){
		$total+=365;
		if ($y%4==0) $total ++;
	}
	//再加当年的几个月
	$total+=gmdate("z",gmmktime(0,0,0,$month,1,$year));
	//用农历的天数累加来判断是否超过阳历的天数
	$flag1=0; //判断跳出循环的条件
	$lcj=0; 
	while ($lcj<=120){
		$lci=1;
		while ($lci<=13){
			$mtotal+=$everymonth[$lcj][$lci];
			if ($mtotal>=$total){
				$flag1=1;
				break;
			}
			$lci++;
		}
		if ($flag1==1) break;
		$lcj++;
	}
	//由上，得到的 $lci 为当前农历月， $lcj 为当前农历年
	//计算所求月份1号的农历日期
	$fisrtdaylunar=$everymonth[$lcj][$lci]-($mtotal-$total);
	$results['year']=$mten[$everymonth[$lcj][14]].$mtwelve[$everymonth[$lcj][15]]; //当前是什么年
	$daysthismonth=gmdate("t",gmmktime(0,0,0,$month,1,$year)); //当前月共几天
	$op=1;
	for ($i=1; $i<=$daysthismonth; $i++) {
		$possiblelunarday=$fisrtdaylunar+$op-1; //理论上叠加后的农历日
		if ($possiblelunarday<=$everymonth[$lcj][$lci]) { //在本月的天数范畴内
			$results[$i]=$mday[$possiblelunarday];
			$op+=1;
		}
		else { //不在本月的天数范畴内
			$results[$i]=$mday[1]; //退回到1日
			$fisrtdaylunar=1;
			$op=2;
			$curmonthnum=($everymonth[$lcj][0]!=0) ? 13 : 12; //当年有几个月
			if ($lci+1>$curmonthnum) { //第13/14个月了，转到下一年
				$lci=1;
				$lcj=$lcj+1;
				//换年头了，把新一年的天干地支也写上
				$results['year'].='/'.$mten[$everymonth[$lcj][14]].$mtwelve[$everymonth[$lcj][15]];
			} else { //还在这年里
				$lci=$lci+1;
				$lcj=$lcj;
			}
		}
		if ($results[$i]==$mday[1]) { //每月的初一应该显示当月是什么月
			if ($everymonth[$lcj][0]!=0) { //有闰月的年
				$monthss=($lci>$everymonth[$lcj][0]) ? ($lci-1) : $lci; //闰月后的月数-1
				if ($lci==$everymonth[$lcj][0]+1) { //这个月正好是闰月
					$monthssshow=$mmonth[0].$mmonth[$monthss]; //前面加个闰字
					$runyue=1;
				} else {
					$monthssshow=$mmonth[$monthss];
				}
			} else {
				$monthss=$lci;
				$monthssshow=$mmonth[$monthss];
			}
			if ($monthss<=10 && $runyue!=1) $monthssshow.=$mmonth[13]; //只有1个字的月加上‘月’字
			$results[$i]=$monthssshow;
		}
	}
	return $results;
}


function preg_search ($str, $array_searches) { //Serach for sensitive strings
	$str_searches=@implode("|", $array_searches);
	if ($str_searches=='') return false;
	$str_searches=preg_quote($str_searches);
	$str_searches=str_replace('\|', '|', $str_searches);
	$str_searches=str_replace('/', '\/', $str_searches);
	$str_searches="/".$str_searches."/is";
	if (preg_match($str_searches, $str)==1) return true;
	else return false;
}

function extract_forbidden () {
	global $blog, $forbidden, $db_prefix;
	$forbidden1=$blog->getsinglevalue("{$db_prefix}forbidden");
	$forbidden['banword']=@explode(',', trim($forbidden1['banword']));
	$forbidden['suspect']=@explode(',', trim($forbidden1['suspect']));
	$forbidden['keep']=@explode(',', trim($forbidden1['keep']));
	$forbidden['nosearch']=@explode(',', trim($forbidden1['nosearch']));
	$forbidden['banip']=@explode(',', trim($forbidden1['banip']));
}

function announcebar () {
	global $mbcon,$ifannouncement,$topannounce;
	if ($mbcon['displayannounce']==1) {
		$ifannouncement="block";
		$topannounce=$mbcon['announce'];
	} else {
		$ifannouncement="none";
		$topannounce='';
	}
}

function get_http_raw_post_data () { //Get http_raw_post_data
	global $HTTP_RAW_POST_DATA;
	if (isset($HTTP_RAW_POST_DATA)) { //Good, the server supports $HTTP_RAW_POST_DATA, then return it directly
		return trim($HTTP_RAW_POST_DATA);
	}
	elseif (PHP_OS>="4.3.0") { //PHP 4.3.0 and higher version supports another way to get it
		return file_get_contents( 'php://input' );
	}
	else return false; //Sorry, no way out, or $raw data is not set at all
}

function check_ip ($ip, $iparray) {
	if (!is_array($iparray)) return false;
	for ($i=0; $i<count($iparray); $i++) {
		$ips=@explode(".", $ip);
		$ipc=@explode(".", $iparray[$i]);
		if (($ipc[0]=='*' || $ips[0]==$ipc[0]) && ($ipc[1]=='*' || $ips[1]==$ipc[1]) && ($ipc[2]=='*' || $ips[2]==$ipc[2]) && ($ipc[3]=='*' || $ips[3]==$ipc[3])) return true;
	}
	return false;
}

function mystrtolower ($str) { //strtolower function that works fine with Chinese characters
	$str=preg_replace("/([A-Z]+)/e", "strtolower('\\1')", $str);
	return $str;
}

function evalmycode($code) {
	$a=preg_match("/<php>(.+?)<\/php>/is", $code, $phpcode_array);
	if ($a!=0) {
		$rawphp=$phpcode_array[1];
		$phpcode=base64_decode($rawphp);
		eval($phpcode);
		$code=preg_replace("/<php>(.+?)<\/php>/is", $phpreturn, $code);
		unset($phpreturn);
	}
	return $code;
}

function get_gravatar ($email) { //Get gravatar address
	global $mbcon;
	$address=$mbcon['gravatarurl'].md5(trim($email));
	return $address;
}

function plugin_get ($pluginpart) { //Load plugins and return results
	global $blogplugin;
	if ($blogplugin[$pluginpart]) {
		$valid_plugins=@explode(',', $blogplugin[$pluginpart]);
		foreach ($valid_plugins as $loadplugin) {
			$loadplugin=basename($loadplugin);
			if (is_file("plugin/{$loadplugin}/{$pluginpart}.php")) {
				include_once("plugin/{$loadplugin}/{$pluginpart}.php");
				$result.=$plugin_return;
				unset($plugin_return);
			}
		}
	}
	return $result;
}

function plugin_walk ($pluginpart, $str) { //Load plugins, compute and return results
	global $blogplugin;
	if ($blogplugin[$pluginpart]) {
		$valid_plugins=@explode(',', $blogplugin[$pluginpart]);
		foreach ($valid_plugins as $loadplugin) {
			$loadplugin=basename($loadplugin);
			if (is_file("plugin/{$loadplugin}/{$pluginpart}.php")) {
				include_once("plugin/{$loadplugin}/{$pluginpart}.php");
				if (function_exists("plugin_{$loadplugin}_{$pluginpart}")) $str=call_user_func("plugin_{$loadplugin}_{$pluginpart}", $str);
				elseif (function_exists("plugin_{$loadplugin}_run")) $str=call_user_func("plugin_{$loadplugin}_run", $str);
			}
		}
	}
	return $str;
}

function plugin_runphp ($pluginpart) { //Load plugins, execute the php code
	global $blogplugin;
	if ($blogplugin[$pluginpart]) {
		$valid_plugins=@explode(',', $blogplugin[$pluginpart]);
		foreach ($valid_plugins as $loadplugin) {
			$loadplugin=basename($loadplugin);
			if (is_file("plugin/{$loadplugin}/{$pluginpart}.php")) {
				include_once("plugin/{$loadplugin}/{$pluginpart}.php");
			}
		}
	}
	return $str;
}

function tbcertificate ($blogid, $pubtime) { //Prevent Trackback spam
	global $mbcon;
	if ($mbcon['tburlexpire']==1) {
		global $nowtime;
		$blogid.='+'.$nowtime['Ymd'];
	}
	$str=substr(md5($blogid.$pubtime), 0, 5);
	return $str;
}

function zhgmdate ($timeformat, $timestamp) {
	if (strstr($timeformat, 'custom')) {
		global $mbcon;
		$timeformat=str_replace('custom', $mbcon['customtimeformat'], $timeformat);
	}
	$zh_numbers=array('零','一','二','三','四','五','六','七','八','九','十');
	$zh_sybols=array('年','月','日');
	$zh_weeks=array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
	$ja_weeks=array('日曜日','月曜日','火曜日','水曜日','木曜日','金曜日 ','土曜日');
	$result=gmdate ($timeformat, $timestamp);
	$fact=getdate($timestamp);
	$factyear=(string)$fact['year'];
	$factmonth=(string)$fact['mon'];
	$factday=(string)$fact['mday'];
	$factweek=$fact['wday'];
	$sfact[0]=$zh_numbers[$factyear{0}].$zh_numbers[$factyear{1}].$zh_numbers[$factyear{2}].$zh_numbers[$factyear{3}].$zh_sybols[0];
	$sfact[1]=($factmonth>=10) ? ($zh_numbers[10].$zh_numbers[$factmonth{1}].$zh_sybols[1]) : ($zh_numbers[$factmonth].$zh_sybols[1]);
	$sfact[1]=str_replace($zh_numbers[10].$zh_numbers[0], $zh_numbers[10], $sfact[1]);
	$sfact[2]=($factday>=10) ? ($zh_numbers[$factday{0}].$zh_numbers[10].$zh_numbers[$factday{1}].$zh_sybols[2]) : ($zh_numbers[$factday].$zh_sybols[2]);
	$sfact[2]=str_replace($zh_numbers[1].$zh_numbers[10], $zh_numbers[10], $sfact[2]);
	$sfact[2]=str_replace($zh_numbers[10].$zh_numbers[0], $zh_numbers[10], $sfact[2]);
	$sfact[3]=$zh_weeks[$factweek];
	$sfact[4]=$ja_weeks[$factweek];
	$rfact=array('@','^','%','!','~');
	$result=str_replace($rfact, $sfact, $result);
	return $result;
}

function encodetburl ($str, $outputjs=1) {
	$rand=rand(0,9);
	$newstr[0]=$rand;
	for ($i=0; $i<strlen($str); $i++) {
		$newstr[]=ord($str{$i})+$rand;
	}
	$putstr=@implode('%', $newstr);
	return $putstr;
}

function create_watermark($uploadfile) { //Watermark
	global $mbcon, $watermark_err, $lang_wm;
	$waterimg = "images/others/watermark.png";
	if (!is_file($waterimg)) {
		$watermark_err=$lang_wm[1];
		return false;
	}
	if (!function_exists('getimagesize')) {
		$watermark_err=$lang_wm[2];
		return false;
	}
	if (PHP_VERSION<4.3) {
		$watermark_err=$lang_wm[3];
		return false;
	}
	$upload_info=@getimagesize($uploadfile);
	if (!$upload_info[0] || !$upload_info[1]) {
		return false;
	}
	$watermark_size=explode('x', strtolower($mbcon['wmsize']));
	if ($upload_info[0]<$watermark_size[0] || $upload_info[1]<$watermark_size[1]) return $lang_wm[4];
	switch ($upload_info['mime']) {
		case 'image/jpeg':
			$tmp=@imagecreatefromjpeg($uploadfile);
			break;
		case 'image/gif':
			if (!function_exists('imagecreatefromgif')) {
				$watermark_err=$lang_wm[5];
				return false;				
			} else $tmp=@imagecreatefromgif($uploadfile);
			break;
		case 'image/png':
			$tmp=@imagecreatefrompng($uploadfile);
			break;
		default:
			$watermark_err=$lang_wm[6];
			return false;				
	}
	$marksize=@getimagesize($waterimg);
	$width=$marksize[0];
	$height=$marksize[1];
	$pos_padding=($mbcon['wmpadding'] && $mbcon['wmpadding']>0) ? $mbcon['wmpadding'] : 5; 	//Padding
	switch ($mbcon['wmposition']) {
		// right-bottom
		case '0':
			$pos_x=$upload_info[0]-$width-$pos_padding;
			$pos_y=$upload_info[1]-$height-$pos_padding;
			break;
		// left-top
		case '1': 
			$pos_x=$pos_padding;
			$pos_y=$pos_padding;
			break;
		// left-bottom
		case '2':
			$pos_x=$pos_padding;
			$pos_y=$upload_info[1]-$height-$pos_padding;
			break;
		// right-top
		case '3':
			$pos_x=$upload_info[0]-$width-$pos_padding;
			$pos_y=$pos_padding;
			break;
		// mid
		case '4':
			$pos_x=($upload_info[0]-$width)/2;
			$pos_y=($upload_info[1]-$height)/2;
			break;
		// random
		default:
			$pos_x=rand(0,($upload_info[0]-$width));
			$pos_y=rand(0,($upload_info[1]-$height));
			break;
	}
	if($imgmark=@imagecreatefrompng($waterimg)) {
		imageAlphaBlending($imgmark, false);
		if ($mbcon['wmtrans']) {
			@imagecopymerge($tmp, $imgmark, $pos_x, $pos_y, 0, 0, $width, $height, $mbcon['wmtrans']);
		} else {
			@imagecopy($tmp, $imgmark, $pos_x, $pos_y, 0, 0, $width, $height);
		}
	}
	switch ($upload_info['mime']) {
		case 'image/jpeg':
			@imagejpeg($tmp,$uploadfile,80);
			@imagedestroy($tmp);
			break;
		case 'image/gif':
			@imagegif($tmp,$uploadfile);
			@imagedestroy($tmp);
			break;
		case 'image/png':
			@imagepng($tmp,$uploadfile);
			@imagedestroy($tmp);
			break;
		default :
			return;
	}
	return $lang_wm[7];
}

function unregister_GLOBALS() { //When register_globals = 'on'
	if (!ini_get('register_globals')) { //Already off
		return;
	}
	// Variables that shouldn't be unset
	$noUnset = array('_GET', '_POST',  '_COOKIE',  '_REQUEST', '_SERVER',  '_ENV',  '_FILES');
	$input = array_merge($_GET,  $_POST,	$_COOKIE, $_SERVER, $_ENV,  $_FILES,	isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
	foreach ($input as $k => $v) {
		if ($k=='GLOBALS') {
			global $kgr;
			$kgr=0;
			kill_GLOBALS($input[$k]); //GLOBALS is recursive -,-
		}
		elseif (!in_array($k, $noUnset) && isset($GLOBALS[$k])) {
			$GLOBALS[$k]=NULL;
		}
	}
}

function kill_GLOBALS($input) { //Unregister $_REQUEST['GLOBALS'] like array recursively
	global $kgr;
	$kgr+=1;
	if ($kgr>10) die('Access Denied!');
	if (is_array($input)) {
		foreach ($input as $k => $v) {
			if ($k=='GLOBALS') kill_GLOBALS($input[$k]);
			$GLOBALS[$k]=NULL;
		}
	}
}

function get_entry_url($id, $blogalias) { //To be discard
	return getlink_entry($id, $blogalias);
}


function scheduledpublish() {
	global $blog, $db_prefix, $nowtime;
	$blog->query("UPDATE `{$db_prefix}blogs` SET `property`=0 WHERE `property`=4 AND `pubtime`<={$nowtime['timestamp']}");
	$affn=db_affected_rows();
	if ($affn>0) {
		if (!defined('REPLYSPECIAL')) define('REPLYSPECIAL', 1);
		include_once('admin/cache_func.php');
		recache_latestentries ();
		plugin_runphp('plannedpublish');
	}
}

function in_iarray ($search, &$array) {
  $search = strtolower($search);
  foreach ($array as $item)
   if (strtolower($item) == $search)
     return true;
  return false;
} 

function generate_emots_panel ($emots) {
	if (!strstr($emots, '<!-- EmotPage -->')) return $emots;
	else {
		$emg=explode('<!-- EmotPage -->', $emots);
		$howmanyemg=count($emg);
		$emgout="<div id=\"smileygroup\">{$emg[0]}</div>\n";
		$emgout.="<script type=\"text/javascript\">\n//<![CDATA[\n";
		$emgout.="var emotgroup = new Array ();\n";
		$emtslb="<div id=\"smileybuttons\">";
		for ($i=0; $i<$howmanyemg; $i++) {
			$emgout.="emotgroup[{$i}]='".str_replace("'", "\\'", $emg[$i])."';\n";
			$emtslb.="<span class=\"smileybut\"><a href=\"javascript: turnsmileygroup({$i});\">".($i+1)."</a></span>";
		}
		$emtslb.="</div>";
		$emgout.="//]]>\n</script>\n";
		$emgout.=$emtslb;
		return $emgout;
	}
}

function checkPageValidity($page, $total) {
	$total=max(1, $total);
	if ($page>$total) {
		global $lnc;
		getHttp404($lnc[313]);
	}
}

function getHttp404($errormsg) {
	global $config;
	@header ("HTTP/1.1 404 Not Found");
	if ($config['customized404']) {
		@header ("Location: {$config['customized404']}");
		exit();
	}
	else {
		catcherror($errormsg);
	}
}

function prepareOpenID($openid, $process_url) {
	global $db_defaultsessdir, $db_tmpdir, $config, $lnc;
	define('OpenIDFileStorePath', $db_tmpdir.'/openid');
	require_once ('openid.php');
	if ($db_defaultsessdir!=1) session_save_path("./{$db_tmpdir}");
	session_start();
	$trust_root = $config['blogurl'];

	// Begin the OpenID authentication process.
	$auth_request = $consumer->begin($openid);

	// Handle failure status return values.
	if (!$auth_request) {
		catcherror($lnc[315]."Authentication error.");
	}

	$auth_request->addExtensionArg('sreg', 'optional', 'email');

	// Redirect the user to the OpenID server for authentication.  Store
	// the token for this authentication so we can verify the response.
	$redirect_url = $auth_request->redirectURL($trust_root, $process_url);
	header("Location: ".$redirect_url);
}

function completeOpenID () {
	global $db_defaultsessdir, $db_tmpdir, $config, $lnc;
	define('OpenIDFileStorePath', $db_tmpdir.'/openid');
	require_once ('openid.php');
	if ($db_defaultsessdir!=1) session_save_path("./{$db_tmpdir}");
	session_start();

	// Complete the authentication process using the server's response.
	$response = $consumer->complete($_GET);

	if ($response->status == Auth_OpenID_CANCEL) {
		// This means the authentication was cancelled.
		catcherror($lnc[316], false);
	} else if ($response->status == Auth_OpenID_FAILURE) {
		$msg = "OpenID authentication failed: " . $response->message;
		catcherror($lnc[315].$msg, false);
	} else if ($response->status == Auth_OpenID_SUCCESS) {
		// This means the authentication succeeded.
		$openid = $response->identity_url;
		$sreg = $response->extensionResponse('sreg');

		//Format openid
		$openid=str_replace(array('http://', 'https://'), array('', ''), $openid);
		if (substr($openid, -1, 1)=='/') $openid=substr($openid, 0, strlen($openid)-1);

		return array('openidurl'=>$openid, 'sreg'=>$sreg);
	} else {
		catcherror($lnc[315].'OpenID Unknown Error.', false);
	}
}