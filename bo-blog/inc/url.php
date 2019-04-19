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


/*本文件用于URL Rewrite
$config['urlrewritemethod']的意义
0：关闭，ugly url
1：PHP URL Optimization
2：Apache URL Rewrite
*/

if (!defined('VALIDREQUEST')) die ('Access Denied.');
define ('URLRewrite', floor($config['urlrewritemethod']));


function getlink_entry ($id, $alias, $page=null, $part=null) {
	if (URLRewrite==0) $outurl="read.php?{$id}";
	elseif (URLRewrite==1) $outurl=($alias) ? "post/{$alias}.php" : "read.php/{$id}.htm";
	else  $outurl=($alias) ? "{$alias}/" : "post/{$id}/";
	if (!is_null($page)) {
		if (URLRewrite==0) $outurl="read.php?entryid={$id}&amp;page={$page}";
		elseif (URLRewrite==1) $outurl=($alias) ? "{$outurl}?page={$page}" : "read.php?entryid={$id}&amp;page={$page}";
		else $outurl.=$page.'/';
	}
	if (!is_null($part)) {
		if (URLRewrite==0) $outurl.="&amp;part={$part}";
		elseif (URLRewrite==1) $outurl.="&amp;part={$part}";
		else $outurl.=$part.'/';
	}
	return $outurl;
}

function getlink_category ($id, $mode=null, $page=null) {
	global $categories;
	$alias=($categories[$id]['cateurlname']) ? $categories[$id]['cateurlname'] : false;
	if (URLRewrite==0) $outurl="index.php?go=category_{$id}";
	elseif (URLRewrite==1) $outurl=($alias) ? "go.php/category/{$alias}/" : "go.php/category/{$id}/";
	else  $outurl=($alias) ? "category/{$alias}/" : "category/{$id}/";
	if (!is_null($mode)) {
		$outurl.=(URLRewrite>=1) ? "{$mode}/" : "&amp;mode={$mode}";
	}
	if (!is_null($page)) {
		$outurl.=(URLRewrite>=1) ? "{$page}/" : "&amp;page={$page}";
	}
	return $outurl;
}

function getlink_date ($y, $m, $d, $mode=null, $page=null) {
	if (URLRewrite==1) $outurl="go.php/date/{$y}/{$m}/{$d}/";
	elseif (URLRewrite==2) $outurl="date/{$y}/{$m}/{$d}/";
	else $outurl="index.php?go=showday_{$y}-{$m}-{$d}";
	if (!is_null($mode)) {
		$outurl.=(URLRewrite>=1) ? "{$mode}/" : "&amp;mode={$mode}";
	}
	if (!is_null($page)) {
		$outurl.=(URLRewrite>=1) ? "{$page}/" : "&amp;page={$page}";
	}
	return $outurl;
}

function getlink_archive ($m, $y, $mode=null, $page=null) {
	if (URLRewrite==1) $outurl="go.php/archiver/{$m}/{$y}/";
	elseif (URLRewrite==2) $outurl="archiver/{$m}/{$y}/";
	else $outurl="index.php?go=archive&amp;cm={$m}&amp;cy={$y}";
	if (!is_null($mode)) {
		$outurl.=(URLRewrite>=1) ? "{$mode}/" : "&amp;mode={$mode}";
	}
	if (!is_null($page)) {
		$outurl.=(URLRewrite>=1) ? "{$page}/" : "&amp;page={$page}";
	}
	return $outurl;
}

function getlink_index ($mode, $page) {
	if (URLRewrite==1) $outurl="go.php/page/{$mode}/{$page}/";
	elseif (URLRewrite==2) $outurl="page/{$mode}/{$page}/";
	else $outurl="index.php?mode={$mode}&amp;page={$page}";
	return $outurl;
}

function getlink_star ($mode, $page) {
	if (URLRewrite==1) $outurl="go.php/starred/{$mode}/{$page}/";
	elseif (URLRewrite==2) $outurl="starred/{$mode}/{$page}/";
	else $outurl="star.php?mode={$mode}&amp;page={$page}";
	return $outurl;
}

function getlink_user ($uid) {
	if (URLRewrite==1) $outurl="go.php/user/{$uid}/";
	elseif (URLRewrite==2) $outurl="user/{$uid}/";
	else $outurl="view.php?go=user_{$uid}";
	return $outurl;
}

function getlink_tags ($tagname, $mode=null, $page=null) {
	if (URLRewrite==1) $outurl="go.php/tags/{$tagname}/";
	elseif (URLRewrite==2) $outurl="tags/{$tagname}/";
	else $outurl="tag.php?tag={$tagname}";
	if (!is_null($mode)) {
		$outurl.=(URLRewrite>=1) ? "{$mode}/" : "&amp;mode={$mode}";
	}
	if (!is_null($page)) {
		$outurl.=(URLRewrite>=1) ? "{$page}/" : "&amp;page={$page}";
	}
	return $outurl;
}

function getlink_pages ($id, $alias) {
	if (URLRewrite==0) $outurl="page.php?pageid={$id}";
	elseif (URLRewrite==2) $outurl=($alias) ? "component/{$alias}/" : "component/id/{$id}/";
	else $outurl=($alias) ? "go.php/component/{$alias}" : "go.php/component/id/{$id}/";
	return $outurl;
}

?>