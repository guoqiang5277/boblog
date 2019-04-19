<?php
if (!defined('VALIDADMIN')) die ('Access Denied.');
remove_module ('fmplayer');
add_module ('fmplayer.txt');
include("plugin/fmplayer/include.php");

if (!file_exists($filename)) {
	copy($file['olist'], $file['dlist']);
}
copy($file['ocfg'], $file['dcfg']);
