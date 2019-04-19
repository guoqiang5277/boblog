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
acceptrequest('blogid,ajax');
checkpermission('CP');

if ($ajax=='on') $in_ajax_mode=1;

checkpermission ('AddEntry');
if ($ajax=='on' && $cancel!='') die ($cancel);


$blogid=floor($blogid);
$blog->query("UPDATE `{$db_prefix}blogs` SET `starred`=`starred`+1 WHERE `blogid`='{$blogid}'");

if ($ajax!='on') {
	$urlreturn=($_SERVER['HTTP_REFERER']=='') ? "index.php" : $_SERVER['HTTP_REFERER'];
	header ("Location: $urlreturn");
} else {
	die('ok');
}

?>