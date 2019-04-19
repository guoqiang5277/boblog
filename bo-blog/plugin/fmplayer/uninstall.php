<?php
if (!defined('VALIDADMIN')) die ('Access Denied.');
remove_module ('fmplayer');
include("plugin/fmplayer/include.php");

unlink($file['dlist']);
unlink($file['dcfg']);