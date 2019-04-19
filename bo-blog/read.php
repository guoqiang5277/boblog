<?PHP
@error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once ("data/config.php");

//Auto detect mirror site
$tmp_host=($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_ENV['HTTP_HOST'];
$config['blogurl']=str_replace('{host}', $tmp_host, $config['blogurl']);

$use_blogalias=$itemid=false;

if (isset($entryid)) {
	if (is_numeric($entryid)) {
		$itemid=(floor($entryid)==$entryid) ? $entryid : false;
	}
}
elseif (isset($_REQUEST['entryid'])) {
	if (is_numeric($_REQUEST['entryid'])) {
		$itemid=(floor($_REQUEST['entryid'])==$_REQUEST['entryid']) ? $_REQUEST['entryid'] : false;
	}
}

if ($itemid===false) {
	if (isset($_REQUEST['blogalias'])) $blogalias=$_REQUEST['blogalias'];
	if ($blogalias) {
		$blogaliasp=addslashes($blogalias);
		$use_blogalias=true;
		$itemid='';
	}
	else {
		$nav=($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : $_ENV["SCRIPT_NAME"];
		if ($nav && strstr($nav, '.htm')) {
			$nav=str_replace($_SERVER["SCRIPT_NAME"], '', $nav);
			$tmp_nav=@explode('/', $nav);
			$nav=($tmp_nav[1]);
			$itemid=str_replace('.htm', '', $nav);
			if ($itemid=='test') {
				@include_once ("data/cache_latest.php");
				$itemid=$cache_latest_all[0]['blogid'];
			}
			elseif (!is_numeric($itemid)) $itemid=false;
		} else {
			$itemid=basename($_SERVER['QUERY_STRING']);
			if (!is_numeric($itemid) && strstr($itemid, 'save_')===false && strstr($itemid, 'preview_')===false) $itemid=false;
		}
	}
}

if ($itemid===false && !$use_blogalias) {
	@header ("HTTP/1.1 404 Not Found");
	if ($config['customized404']) {
		@header ("Location: {$config['customized404']}");
	}
	else {
		die("<html><head><title>Not Found</title></head><body><h1>HTTP/1.1 404 Not Found</h1></body></html>");
	}
}

//Very Simple Re-direct
$act='read';
require ("index.php");
?>