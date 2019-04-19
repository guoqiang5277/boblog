<?PHP
/* -----------------------------------------------------
Bo-Blog 2 : The Blog Reloaded.
<<A Bluview Technology Product>>
禁止使用Windows记事本修改文件，由此造成的一切使用不正常恕不解答！
PHP+MySQL blog system.
Code: Bob Shen
Offical site: http://www.bo-blog.com
Copyright (c) Bob Shen
In memory of my university life
本文件用于基于PHP的URL优化
------------------------------------------------------- */

@error_reporting(E_ERROR | E_WARNING | E_PARSE);
include_once("./data/config.php");
if ($config['urlrewritemethod']!='1') {
	die("ACCESS DENIED.");
}

$q_url=$_SERVER["REQUEST_URI"];
@list($relativePath, $rawURL)=@explode('/go.php/', $q_url);
$rewritedURL=false;
$includeFile='';


$RewriteRules=$RedirectTo=array();

$RewriteRules[]="/page\/([0-9]+)\/([0-9]+)\/?/e";
$RewriteRules[]="/starred\/([0-9]+)\/?([0-9]+)?\/?/e";
$RewriteRules[]="/category\/([a-z|A-Z|0-9|_|-]+)\/?([0-9]+)?\/?([0-9]+)?\/?/e";
$RewriteRules[]="/archiver\/([0-9]+)\/([0-9]+)\/?([0-9]+)?\/?([0-9]+)?\/?/e";
$RewriteRules[]="/date\/([0-9]+)\/([0-9]+)\/([0-9]+)\/?([0-9]+)?\/?([0-9]+)?\/?/e";
$RewriteRules[]="/user\/([0-9]+)\/?/e";
$RewriteRules[]="/component\/id\/([0-9]+)\/?/e";
$RewriteRules[]="/component\/([a-z|A-Z|0-9|_|-]+)\/?/e";
$RewriteRules[]="/tags\/([a-z|A-Z|0-9|_|-|%]+)\/?([0-9]+)?\/?([0-9]+)?\/?/e";

$RedirectTo[]="loadURL('index.php', array('mode'=>'\\1', 'page'=>'\\2'));";
$RedirectTo[]="loadURL('star.php', array('mode'=>'\\1', 'page'=>'\\2'));";
$RedirectTo[]="loadURL('index.php', array('go'=>'category_\\1', 'mode'=>'\\2', 'page'=>'\\3'));";
$RedirectTo[]="loadURL('index.php', array('go'=>'archive', 'cm'=>'\\1', 'cy'=>'\\2', 'mode'=>'\\3', 'page'=>'\\4'));";
$RedirectTo[]="loadURL('index.php', array('go'=>'showday_\\1-\\2-\\3', 'mode'=>'\\4', 'page'=>'\\5'));";
$RedirectTo[]="loadURL('view.php', array('go'=>'user_\\1'));";
$RedirectTo[]="loadURL('page.php', array('pageid'=>'\\1'));";
$RedirectTo[]="loadURL('page.php', array('pagealias'=>'\\1'));";
$RedirectTo[]="loadURL('tag.php', array('tag'=>'\\1', 'mode'=>'\\2', 'page'=>'\\3'));";

function loadURL($url, $pref) {
	global $includeFile;
	if (!is_array($pref)) {
		return false;
	}
	$includeFile=basename($url);
	foreach ($pref as $p=>$v) {
		global $$p;
		$$p=$v;
	}
	return true;
}

$i=0;
foreach ($RewriteRules as $rule) {
	if (preg_match($rule, $rawURL)) {
		$rewritedURL=preg_replace($rule, $RedirectTo[$i], $rawURL, 1);
		break;
	}
	$i+=1;
}

if (!$rewritedURL || !$includeFile) {
	@header ("HTTP/1.1 404 Not Found");
	if ($config['customized404']) {
		@header ("Location: {$config['customized404']}");
		exit();
	}
	else {
		die("<html><head><title>Not Found</title></head><body><h1>HTTP/1.1 404 Not Found</h1></body></html>");
	}
}

include($includeFile);

