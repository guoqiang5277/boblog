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

//If you want to use persistant connection, please set the following variety as 1
$persistant_connect=0;

//If you don't want to discontinue the program when there is any database query error, set the following variety as  1
$ignore_db_errors=0;


if (!defined('VALIDREQUEST')) die ('Access Denied.');

if (!function_exists("mysqli_connect")) {
	die ("Your server does not seem to support MySQL, so Bo-Blog 2.x can not run at your server.");
}

function db_connect($dbhost, $dbuser, $dbpw, $dbname='') {
	global $db_410;

    $con = mysqli_connect($dbhost, $dbuser, $dbpw);
		if(!$con) {
			db_halt($con,'Can not connect to MySQL server');
		}

	if (!empty($dbname)) {
		$a_result=mysqli_select_db($con,$dbname);
		if ($a_result) {
			if ($db_410=='1')  mysqli_query($con,"SET NAMES 'utf8'");
		}
		return $con;
	}
}

function db_select_db($con,$dbname) {
	$a_result=mysqli_select_db($con,$dbname);
	if ($a_result) {
		if (mysqli_get_server_info($con)>='4.1.0') mysqli_query($con,"SET NAMES 'utf8'");
	}
	return $a_result;
}

//mysqli_fetch_array该函数不需要加链接
function db_fetch_array($query, $result_type = MYSQLI_ASSOC) {
	return mysqli_fetch_array($query, $result_type);
}

function db_query($con, $sql, $silence = 0) {
	global $querynum, $allqueries, $ignore_db_errors;
	$query = mysqli_query($con, $sql);
	if(!$query && !$silence && !$ignore_db_errors) {
		db_halt($con, 'MySQL Query Error', $sql);
	}
	$querynum++;
	//$allqueries[]=$sql; //For Debug Use Only
	return $query;
}

function db_unbuffered_query($con,$sql, $silence = 0) {
	global $querynum;
	$func_unbuffered_query = @function_exists('mysql_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysql_query';
	$query = $func_unbuffered_query($sql);
	if(!$query && !$silence) {
		db_halt($con,'MySQL Query Error', $sql);
	}
	$querynum++;
	return $query;
}

function db_affected_rows($con) {
	return mysqli_affected_rows($con);
}

function db_error($con) {
	return mysqli_error($con);
}

function db_errno($con) {
	return mysqli_errno();
}

//function result($query, $row) {
//	$query = @mysql_result($query, $row);
//	return $query;
//}

//mysqli_num_rows不需要转化
function db_num_rows($query) {
	$query = mysqli_num_rows($query);
	return $query;
}
//mysqli_num_fields 不需要转化
function db_num_fields($query) {
	return mysqli_num_fields($query);
}
//mysqli_free_result 不需要转化
function db_free_result($query) {
	return mysqli_free_result($query);
}

function db_insert_id($con) {
	$id = mysqli_insert_id($con);
	return $id;
}
//mysqli_fetch_row 不需要转化
function db_fetch_row($query) {
	$query = mysqli_fetch_row($query);
	return $query;
}

function db_close($con) {
	mysqli_close($con);
}

function db_halt($con,$message = '', $sql = '') {
global $db_prefix;
$timestamp = time();
$errmsg = '';

$dberror = db_error($con);
$dberrno = db_errno($con);
$dberror=str_replace($db_prefix, '***', $dberror);
$sql=str_replace($db_prefix, '***', $sql);


$errmsg = "<b>Bo-Blog Database System Tips</b>: $message\n\n";
$errmsg .= "<b>Time</b>: ".gmdate("Y-n-j g:ia", $timestamp + ($GLOBALS["timeoffset"] * 3600))."\n";
$errmsg .= "<b>Script</b>: ".$GLOBALS['PHP_SELF']."\n\n";
if($sql) {
	$errmsg .= "<b>SQL</b>: ".htmlspecialchars($sql)."\n";
}
$errmsg .= "<b>Error</b>:  $dberror\n";
$errmsg .= "<b>Errno.</b>:  $dberrno";

@header("Content-Type: text/html; charset=utf-8");
echo "</table></table></table></table></table>\n";
echo "<p style=\"font-family: Verdana, Tahoma; font-size: 11px; background: #FFFFFF;\">";
echo nl2br($errmsg);
	
echo '</p>';
exit;
}


?>