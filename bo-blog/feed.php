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
require_once ("global.php");
include_once ("data/mod_config.php");
include_once("data/cache_adminlist.php");
include_once("data/plugin_enabled.php");

acceptrequest('go');
if ($config['blogopen']!=1 && $act!='login') {
	exit();
}

if ($go) @list($job, $itemid)=@explode('_', basename($go));
if (!$job) $job='main';
else $job=basename($job);

$itemid=floor($itemid);
$seed=0;

//Begin get email address
$admin_ids=@implode(',', array_keys($adminlist));
$email_admin=$blog->getgroupbyquery("SELECT `email`, `username`, `userid` FROM `{$db_prefix}user` WHERE `userid` IN ({$admin_ids})");
foreach ($email_admin as $single_email) {
	$tmp1=$single_email['email'];
	$tmp2=$single_email['userid'];
	$admin_email[$tmp2]=$tmp1;
	unset($tmp1, $tmp2);
}


switch ($job) {
	case 'category':
		$query="SELECT * FROM `{$db_prefix}blogs` WHERE `property`<'2' AND `permitgp`='' AND `category`='{$itemid}' ORDER BY `pubtime` DESC LIMIT 0, {$mbcon['maxrssitem']}";
		break;
	case 'entry':
		$query="SELECT t1.*, t2.* FROM `{$db_prefix}blogs` t1 LEFT JOIN `{$db_prefix}replies` t2 ON t1.blogid=t2.blogid WHERE t1.blogid={$itemid} AND t1.property<>'2' AND t1.property<>'3' ORDER BY t2.reptime {$order}  LIMIT 0, {$mbcon['maxrssitem']}";
		break;
	case 'comment':
		$query="SELECT t1.*, t2.* FROM `{$db_prefix}replies` t1 LEFT JOIN `{$db_prefix}blogs` t2 ON t1.blogid=t2.blogid WHERE t1.reproperty='0' ORDER BY t1.reptime DESC LIMIT 0, {$mbcon['maxrssitem']}";
		break;
	default:
		$query="SELECT * FROM `{$db_prefix}blogs` WHERE `property`<'2' AND `permitgp`='' ORDER BY `pubtime` DESC LIMIT 0, {$mbcon['maxrssitem']}";
		break;
}

include ("inc/rsstemplate.php");
$m_b=new getblogs;
$records=$m_b->getgroupbyquery($query);
if (!is_array($records)) exit();
if ($job=='category' || $job=='main') {
	foreach ($records as $entry) {
		$rssbody.=$m_b->rss_entry($entry);
	}
} elseif ($job=='entry') {
	$rssbody.=$m_b->rss_entry($records[0]);
	foreach ($records as $entry) {
		$rssbody.=$m_b->rss_replies($entry);
		$seed+=1;
	}
} elseif ($job=='comment') {
	foreach ($records as $entry) {
		$rssbody.=$m_b->rss_replies($entry);
		$seed+=1;
	}
}


$rss_xml=$m_b->rss_xml($rssbody);
@header("Content-type:application/xml");
die($rss_xml);