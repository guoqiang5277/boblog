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

if ($configjob=='save_list') {
	$savetext="<?PHP\n";
	$save_list=$_POST['fmp'];
	//if (count($save_list)<=0) catcherror ($lna[1013]);
	while (@list ($key, $val) = @each ($save_list)) {
		while (@list ($val_key, $val_value) = @each ($val)) {
			if($val_key=='title' && empty($val_value))break;
			if(!empty($val_value))$savetext.="\$fmp_list['{$key}']['{$val_key}']='".admin_convert($val_value)."';\n";
		}
	}
	//if ($savetext=='') catcherror ($lna[1013]);
	if (!writetofile($file['dlist'], $savetext)) catcherror("{$lna[66]}".$file['dlist']);
	else catchsuccess ($finishok, array($backtofmp_list, $backtofmp_cfg,$backtoplugin));
}
include($file['dlist']);

foreach($fmp_list as $fp_id => $fp_song){
	//$fmp_list_show .= "add_fpRow('{$fp_id}','{$fmp_list[$fp_id][title]}','{$fmp_list[$fp_id][creator]}','{$fmp_list[$fp_id][location]}','{$fmp_list[$fp_id][info]}','{$fmp_list[$fp_id][image]}','{$fmp_list[$fp_id][album]}','{$fmp_list[$fp_id][meta]}')\n";
	//$fmp_list_show .= "add_fpRow('{$fp_id}','".htmlspecialchars($fmp_list[$fp_id][title]). "','" .htmlspecialchars($fmp_list[$fp_id][creator]). "','" .htmlspecialchars($fmp_list[$fp_id][location]). "','" .htmlspecialchars($fmp_list[$fp_id][info]). "','" .htmlspecialchars($fmp_list[$fp_id][image]). "','" .htmlspecialchars($fmp_list[$fp_id][album]). "','" .htmlspecialchars($fmp_list[$fp_id][meta])."')\n";
	$fmp_list_show .= "\n add_fpRow('-1','{$fp_id}'";
	$list_arr = array('title','creator','location','info','image','album','meta');
	foreach ($list_arr as $value) {
	    if(!empty($fmp_list[$fp_id][$value])) $fmp_list_show .= ",'". htmlspecialchars($fmp_list[$fp_id][$value], ENT_QUOTES) ."'";
	    else $fmp_list_show .= ",''";
	}
	$fmp_list_show .= ");"; 
}


$plugin_header=<<<eot
<style type="text/css">
.pd3 {
	padding:2px;
}
</style>
<script type="text/javascript">
var dyn_t,divi;
var jslanfp = Array('{$page['list']}','{$lna[64]}','{$lna[65]}','{$lanfp[10]}','{$lanfp[14]}','{$lanfp[11]}','{$lanfp[12]}','{$lanfp[15]}','{$lanfp[16]}','{$lanfp[3]}','{$lanfp[4]}','{$lanfp[5]}','{$lanfp[6]}','{$lanfp[7]}','{$lanfp[8]}','{$lanfp[9]}');
</script>
<script type="text/javascript" src="{$plugin_address}/dyn_table.js"></script>
eot;

$plugin_return=<<<eot
<table class="tablewidth" align="center" cellpadding="4" cellspacing="0">
	<tr>
		<td width="280" class="sectstart">{$lanfp[0]} {$lanfp[13]}</td>
		<td class="sectend">
			<a href="admin.php?act={$page['home']}"><span class="sectstart pd3">{$lanfp[96]}{$lanfp[1]}<span class="sectend pd3"></span></a>
			<a href="admin.php?act={$page['list']}"><span class="sectstart pd3">{$lanfp[13]}{$lanfp[1]}</span><span class="sectend pd3"></span></a>
			&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp{$lanfp[13]}{$lanfp[1]}
		</td>
	</tr>
</table>

<div align="right" class="prefsection">{$lanfp[75]}&nbsp&nbsp&nbsp&nbsp{$lanfp[77]}<input type="button" value="{$lanfp[2]}" onclick="add_fpRow('0','','','','','','','')" /></div>
<div id="form_div">
	<div id="input_div"></div>
</div>
<div align="right" class="prefsection">{$lanfp[78]}<input type="button" value="{$lanfp[2]}" onclick="add_fpRow('-1','','','','','','','','')" /></div>
<div align="right" class="prefsection">{$lanfp[79]}<input id="inum" type="text" value="2" size="5"/>{$lanfp[80]}<input type="button" value="{$lanfp[2]}" onclick="add_fpRow('inum','','','','','','','','')" /></div>
<script type="text/javascript">
creatFrom();	
creatSubmit();
dyn_t = document.getElementById("tbl_setList");
{$fmp_list_show}
</script>
<div id="copyright" align="right">$lanfp[76]</div>
eot;
?>