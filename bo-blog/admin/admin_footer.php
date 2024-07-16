<?PHP
if (!defined('VALIDADMIN')) die ('Access Denied.');
$display_overall.="</div></div>
<div id=\"adminfooter\">
    <div id=\"copyright\" style='display: flex; flex-direction: column'>
        <div>
            <span style='color: aliceblue;background: #4AB4EC;border-radius: 3px;padding: 1px 2px 2px 8px;'>ver: {$new_blogversion}  </span><span> [<a href=\"index.php\" target='_blank'>{$lna[41]}</a>] [<a href=\"admin.php\">{$lna[39]}</a>] [<a href=\"login.php?job=logout\">{$lna[40]}</a>]</span></div> 
        <div style='margin:10px 0 30px 0;'>{$lna[1198]} <a href=\"http://www.bo-blog.com\">{$blogversion}</a> ({$codeversion})</div>
    </div>
</div>
</body></html>";
@header("Content-Type: text/html; charset=utf-8");
echo ($display_overall);