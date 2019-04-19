<?PHP
if (!defined('VALIDADMIN')) die ('Access Denied.');
checkpermission('CP');

$plugin_address="plugin/fmplayer";
if (!empty($langback)) include("{$plugin_address}/lang_{$langback}.php");
else include_once("{$plugin_address}/lang_en.php");

include("{$plugin_address}/include.php");
if(empty($lanfp[76])){exit;}
$backtoplugin="{$lna[28]}|admin.php?go=addon_plugin";
$backtofmp_cfg="{$lanfp[0]}{$lanfp[1]}|admin.php?act={$page['act']}";
$backtofmp_list="{$lanfp[0]}{$lanfp[13]}{$lanfp[1]}|admin.php?act={$page['list']}";


acceptrequest('configjob');

if ($configjob=='save_config') {
	$savetext="<?PHP\n";
	$save_config=$_POST['prefconfig'];
	if (count($save_config)<=1) catcherror ($lna[1013]);
		while (@list ($key, $val) = @each ($save_config)) {
			$savetext.="\$fmp_cfg['{$key}']='".admin_convert($val)."';\n";
		}
	if ($savetext=='') catcherror ($lna[1013]);
	if (!writetofile ($file['dcfg'], $savetext)) catcherror ("{$lna[66]}".$file['dcfg']);
	else catchsuccess ($finishok, array($backtofmp_cfg,$backtofmp_list,$backtoplugin));
}


$plugin_header=<<<eot
<style type="text/css">
.pd3 {
	padding:2px;
}
</style>
eot;

	$pref_leftchar="200";
	$pref_variable="fmp_cfg";
	include($file['dcfg']);
	$ow = array("?", "=", "&");
	$cw = array("%3F", "%3D", "%26");
	$fmp_cfg['file'] = str_replace($ow,$cw,$fmp_cfg['file']);
	//$fmp_cfg['file'] = urlencode($fmp_cfg['file']);
	include ("{$plugin_address}/fmp_pref.php");
	$pref_result_show=@implode('', $pref_result);
	
$display_overall.=<<<eot
eot;

$plugin_return=<<<eot
<table class="tablewidth" align="center" cellpadding="4" cellspacing="0">
	<tr>
		<td width="280" class="sectstart">{$lanfp[0]}</td>
		<td class="sectend">
			<a href="admin.php?act={$page['home']}"><span class="sectstart pd3">{$lanfp[96]}{$lanfp[1]}<span class="sectend pd3"></span></a>
			<a href="admin.php?act={$page['list']}"><span class="sectstart pd3">{$lanfp[13]}{$lanfp[1]}</span><span class="sectend pd3"></span></a>
			&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp{$lanfp[96]}{$lanfp[1]}
		</td>
	</tr>
</table>
<table class="tablewidth" cellpadding="4" cellspacing="1" align="center">
	<form action="admin.php?act={$page['home']}" method="post">
	<tr><td class="prefsection" align="center" colspan="2"><a name="top"></a>{$lna[500]}</td></tr>
$pref_result_show
	<tr><td align="center" colspan="2"><input type="hidden" value="save_config" name="configjob" /><input type="submit" value="{$lna[64]}" class="formbutton"/> <input type="reset" value="{$lna[65]}" class="formbutton" /></form></td></tr>
</table>


<div id="copyright" align="right">{$lanfp[99]}<br/>{$lanfp[76]}</div>
eot;
?>