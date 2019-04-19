<?PHP
/* Sitemap generator for Bo-Blog 2.0.x
Written by Bob
Updated on 2008-7-12  */

//How many items are included?
$entrynumber=1500;

//No need to change anything below
error_reporting(0);
define ("noCounter", 1);
include_once ("function.php");
$smentries=GetNewPosts ($entrynumber, 'blogid,pubtime,edittime,blogalias');

$outputxml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
$outputxml.="<url>\n<loc>{$config['blogurl']}/index.php</loc>\n<lastmod>".gmdate("Y-m-d\TH:i:s+00:00")."</lastmod>\n<changefreq>always</changefreq>\n<priority>1.0</priority>\n</url>\n";
if (is_array($smentries)) {
	foreach ($smentries as $entry) {
		$entryurl="{$config['blogurl']}/".getlink_entry($entry['blogid'], $entry['blogalias']);
		$entrytime=($entry['edittime']) ? $entry['edittime'] : $entry['pubtime'];
		$entrytime=gmdate("Y-m-d\TH:i:s+00:00", $entrytime);
		$outputxml.="<url>\n<loc>{$entryurl}</loc>\n<lastmod>{$entrytime}</lastmod>\n<changefreq>daily</changefreq>\n<priority>0.9</priority>\n</url>\n";
	}
}
$outputxml.="</urlset>";

@header("Content-Type: application/xml; charset=utf-8");
die ($outputxml);
?>