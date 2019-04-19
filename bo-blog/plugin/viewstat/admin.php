<?PHP

if (!defined('VALIDADMIN')) die ('Access Denied.');
checkpermission('CP');

if ($langback=='zh-tw') include_once("plugin/viewstat/lang_zh-tw.php");
else include_once("plugin/viewstat/lang_zh-cn.php");

acceptrequest('cleardata');
if ($cleardata==1) {
	acceptrequest('clearyear,clearmonth,clearday');
	$delstr=floor($clearyear)*10000+floor($clearmonth)*100+floor($clearday);
	if (strlen((string)$delstr)!=8) catcherror($langstat[19]);
	else {
		$blog->query("DELETE FROM `{$db_prefix}history` WHERE `hisday`<'{$delstr}'");
		catchsuccess($langstat[20]);
	}
}

$crtime=gmdate('Y/m/d H:i', ($config['blogcreatetime']+3600*$config['timezone']));
$the_current_time=time();
$days=floor(($the_current_time-$config['blogcreatetime'])/(24*60*60));
if ($days==0) $days=1;

$av_art=floor($statistics['entries']/$days);
if ($av_art==0) $av_art="&lt;1";

$av_rep=floor($statistics['replies']/$days);
if ($av_rep==0) $av_rep="&lt;1";

$av_reg=floor($statistics['users']/$days);
if ($av_reg==0) $av_reg="&lt;1";

$av_vis=floor($statistics['total']/$days);
if ($av_vis==0) $av_vis="&lt;1";

$av_mes=floor($statistics['messages']/$days);
if ($av_mes==0) $av_mes="&lt;1";

$stathistory_all=$blog->getgroupbyquery("SELECT hisday, SUM(visit) FROM `{$db_prefix}history` GROUP BY `hisday` ORDER BY `hisday` DESC LIMIT 0,20");
//print_r($stathistory_all);
//exit();


$thesedays=sizeof($stathistory_all);

$ruler=0;
for ($i=0; $i<$thesedays; $i++) {
	$day_his[$i]=substr($stathistory_all[$i]['hisday'], 0, 4).'-'.substr($stathistory_all[$i]['hisday'], 4, 2).'-'.substr($stathistory_all[$i]['hisday'], 6, 2);
	$vis_his[$i]=$stathistory_all[$i]['SUM(visit)'];
	$ruler=max($vis_his[$i],$ruler);
}
for ($i=0; $i<$thesedays; $i++) {
	$putgraph.="<tr><td width=\"120\">".$day_his[$i]."</td><td>".statgraphic($vis_his[$i], $ruler)."</td></tr>";
}

$afilename="data/online.php"; 
$onlineusers=$nowonline=array();
$online_all=@file($afilename);
$nowonline=$online_all;
$nowonline=array_slice($nowonline,0,50);
$theseonline=min(sizeof($nowonline), 50);
for ($i=0; $i<$theseonline; $i++) {
	$tmpdata=explode("|", $nowonline[$i]);
	$putonline.="<tr><td>".$tmpdata[1]." <a href=\"{$mbcon['ipsearch']}{$tmpdata[1]}\" target=\"_blank\"><img src=\"{$template['images']}/ip.gif\" border=\"0\" alt=\"IP\" title=\"IP: {$tmpdata[1]}\" /></a></td><td>".gmdate('Y/m/d H:i', ($tmpdata[2]+3600*$config['timezone']))."</td></tr>";
}

function statgraphic($visitnum, $ruler) {
	if ($ruler==0) $ruler=1;
	$width=floor(($visitnum/$ruler)*90)."%";
	if ($width=="0%") $width="1%";
	$out="<table width=\"90%\" cellpadding=\"0\" cellspacing=\"0\" height=\"10\"><tr><td width=\"$width\" background=\"plugin/viewstat/bar.gif\" style=\"background-repeat: repeat-x;\">&nbsp;</td><td align=\"right\">$visitnum</td></tr></table>";
	return $out;
}

$plugin_return=<<<eot
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
$langstat[21]
</td>
<td class="sectend">$langstat[22]</td>
</tr>
</table>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr><td align="center" class="sect">
<table width=90% align=center><tr><td>
<tr><td width="120">$langstat[1]</td><td colspan="3">{$config['blogname']}</td></tr>
<tr><td width="120">$langstat[3]</td><td>$crtime</td><td width="120">$langstat[4]</td><td>$days $langstat[5]</td></tr>
<tr><td width="120">$langstat[6]</td><td>{$statistics['entries']}</td><td width="120">$langstat[7]</td><td>$av_art</td></tr>
<tr><td width="120">$langstat[8]</td><td>{$statistics['replies']}</td><td width="120">$langstat[7]</td><td>$av_rep</td></tr>
<tr><td width="120">$langstat[9]</td><td>{$statistics['users']}</td><td width="120">$langstat[7]</td><td>$av_reg</td></tr>
<tr><td width="120">$langstat[2]</td><td>{$statistics['messages']}</td><td width="120">$langstat[7]</td><td>$av_mes</td></tr>
</table>
</td></tr>
</table>
<br/><br/>

<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
$langstat[13]
</td>
<td class="sectend">$langstat[14]</td>
</tr>
</table>
<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr><td align="center" class="sect">
<table width=90% align=center><tr><td>
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td width="120">$langstat[11]</td><td>{$statistics['total']} ({$langstat[7]} $av_vis)</td></tr>
<tr><td width="120">$langstat[12]</td><td>{$statistics['today']}</td></tr>
</table><br/>
<form action="admin.php?act=viewstat" method=post>
<table width="100%" cellpadding="0" cellspacing="0">
$putgraph
</table>
<div align=center>$langstat[18] <input type=text size=4 maxlength=4 value="{$nowtime['year']}" name='clearyear'>$langstat[23] <input type=text size=2 maxlength=2 value="{$nowtime['month']}" name='clearmonth'>$langstat[24] <input type=text size=2 maxlength=2 value="{$nowtime['day']}" name='clearday'>$langstat[25] $langstat[26] <input type=hidden value="1" name='cleardata'><input type=submit value='$langstat[27]'>
</form>
</td></tr></table>
</td></tr></table>

<br/><br/>

<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr>
<td width=160 class="sectstart">
$langstat[28]
</td>
<td class="sectend">$langstat[29]</td>
</tr>
</table>

<table class='tablewidth' align=center cellpadding=4 cellspacing=0>
<tr><td align="center" class="sect">
<table width="90%" cellpadding="3" cellspacing="0" align="center">
<tr><td>$langstat[15]</td><td>$langstat[16]</td></tr>
$putonline
</table>
</td></tr></table>
</td></tr></table>
eot;
?>