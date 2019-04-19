<?php
error_reporting(0);
define ("allowCache", 1);
include("../data/config.php");
$config['blogurl']=str_replace('{host}', $_SERVER['HTTP_HOST'], $config['blogurl']);
@header("Content-type:application/xml");
echo <<<eot
<?xml version="1.0" encoding="UTF-8"?>
<rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd" >
    <service>
        <engineName>Bo-Blog</engineName> 
        <engineLink>http://www.bo-blog.com</engineLink>
        <homePageLink>{$config['blogurl']}/</homePageLink>
        <apis>
                <api name="MetaWeblog" preferred="true" apiLink="{$config['blogurl']}/xmlrpc.php" blogID="" />
        </apis>
    </service>
</rsd>
eot;
exit();