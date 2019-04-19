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

if (!function_exists("mysql_connect")) {
	die ("Your server does not seem to support MySQL, so Bo-Blog 2.x can not run at your server.");
}

function db_connect($dbhost, $dbuser, $dbpw, $dbname='') {
	if(!@mysql_connect($dbhost, $dbuser, $dbpw)) {
		db_halt('Can not connect to MySQL server');
	}
	if (!empty($dbname)) {
		$a_result=mysql_select_db($dbname);
		/*if ($a_result) {
			if (mysql_get_server_info()>='4.1.0') mysql_query("SET NAMES 'utf8'");
		}*/
		return $a_result;
	}
}

function db_select_db($dbname) {
	$a_result=mysql_select_db($dbname);
	if ($a_result) {
		if (mysql_get_server_info()>='4.1.0') mysql_query("SET NAMES 'utf8'");
	}
	return $a_result;
}

function db_fetch_array($query, $result_type = MYSQL_ASSOC) {
	return mysql_fetch_array($query, $result_type);
}

function db_query($sql, $silence = 0) {
	global $querynum;
	$query = mysql_query($sql);
	if(!$query && !$silence) {
		db_halt('MySQL Query Error', $sql);
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

function db_affected_rows() {
	return mysql_affected_rows();
}

function db_error() {
	return mysql_error();
}

function db_errno() {
	return mysql_errno();
}

function result($query, $row) {
	$query = @mysql_result($query, $row);
	return $query;
}

function db_num_rows($query) {
	$query = mysql_num_rows($query);
	return $query;
}

function db_num_fields($query) {
	return mysql_num_fields($query);
}

function db_free_result($query) {
	return mysql_free_result($query);
}

function db_insert_id() {
	$id = mysql_insert_id();
	return $id;
}

function db_fetch_row($query) {
	$query = mysql_fetch_row($query);
	return $query;
}

function db_close() {
	mysql_close();
}

function db_halt($message = '', $sql = '') {
	global $errmsg;
	$dberror = db_error();
	$dberrno = db_errno();
	if($sql) $errmsg .= "<b>SQL</b>: ".htmlspecialchars($sql)."<br>";
	$errmsg .= "<b>Error</b>:  $dberror<br>";
	$errmsg .= "<b>Errno. </b>:  $dberrno<br>";
	return false;
}


?>