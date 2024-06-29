<?PHP
/* -----------------------------------------------------
Bo-Blog 2 : The Blog Reloaded.
<<A Bluview Technology Product>>
PHP+MySQL blog system under GNU Licence.
Code: Bob Shen <bob.shen@gmail.com>
Offical site: http://www.bo-blog.com
Copyright (c) Bob Shen й?
In memory of my university life
------------------------------------------------------- */

if (!function_exists("mysqli_connect")) {
	die ("Your server does not seem to support MySQL, so Bo-Blog 2.x can not run at your server.");
}

function db_connect($dbhost, $dbport, $dbuser, $dbpw, $dbname='') {
    $con = mysqli_connect($dbhost, $dbuser, $dbpw, $dbname, $dbport);
    if(!$con) {
        db_halt($con,'Can not connect to MySQL server');
    }
    return $con;
}

function db_select_db($con,$dbname) {
	$a_result=mysqli_select_db($con,$dbname);
	if ($a_result) {
		if (mysql_geti_server_info($con)>='4.1.0') mysqli_query($con,"SET NAMES 'utf8'");
	}
	return $a_result;
}

//mysqli_fetch_array该函数不需要加链接
function db_fetch_array($query, $result_type = MYSQLI_ASSOC) {
	return mysqli_fetch_array($query, $result_type);
}

function db_query($con,$sql, $silence = 0) {
	global $querynum;
	$query = mysqli_query($con,$sql);
	if(!$query && !$silence) {
		db_halt($con,'MySQL Query Error', $sql);
	}
	$querynum++;
	return $query;
}

function db_unbuffered_query($sql, $silence = 0) {
	global $querynum;
	$func_unbuffered_query = @function_exists('mysql_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysql_query';
	$query = $func_unbuffered_query($sql);
	if(!$query && !$silence) {
		db_halt('MySQL Query Error', $sql);
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
	return mysqli_errno($con);
}

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
	global $errmsg;
	$dberror = db_error($con);
	$dberrno = db_errno($con);
	if($sql) $errmsg .= "<b>SQL</b>: ".htmlspecialchars($sql)."<br>";
	$errmsg .= "<b>Error</b>:  $dberror<br>";
	$errmsg .= "<b>Errno. </b>:  $dberrno<br>";
	return false;
}


?>