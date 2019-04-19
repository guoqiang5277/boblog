<?php

if (!defined('OpenIDFileStorePath')) die('Access Denied.');

require_once ("./inc/openid/Auth/OpenID/Consumer.php");
require_once ("./inc/openid/Auth/OpenID/FileStore.php");
$store_path = OpenIDFileStorePath;
$store = new Auth_OpenID_FileStore($store_path);
$consumer = new Auth_OpenID_Consumer($store);

?>