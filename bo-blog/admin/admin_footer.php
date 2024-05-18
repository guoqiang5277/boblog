<?PHP
if (!defined('VALIDADMIN')) die ('Access Denied.');
$display_overall.="</div></div>
<div id=\"adminfooter\"><div id=\"copyright\">Ver <a href=\"http://www.bo-blog.com\">{$blogversion}</a> ({$codeversion}) [<a href=\"index.php\">{$lna[41]}</a>] [<a href=\"admin.php\">{$lna[39]}</a>] [<a href=\"login.php?job=logout\">{$lna[40]}</a>]</div></div>
</body></html>";
@header("Content-Type: text/html; charset=utf-8");
echo ($display_overall);