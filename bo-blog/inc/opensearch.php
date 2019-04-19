<?php
error_reporting(0);
define ("allowCache", 1);
include("../data/config.php");
$config['blogurl']=str_replace('{host}', $_SERVER['HTTP_HOST'], $config['blogurl']);
@header("Content-type:application/xml");
echo <<<eot
<?xml version="1.0" encoding="UTF-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
<ShortName>{$config['blogname']}</ShortName>
<Description>{$config['blogname']}</Description>
<InputEncoding>UTF-8</InputEncoding>
<Url type="text/html" template="{$config['blogurl']}/visit.php?job=search&amp;searchmethod=2&amp;keyword={searchTerms}"/>
</OpenSearchDescription>
eot;
exit();
