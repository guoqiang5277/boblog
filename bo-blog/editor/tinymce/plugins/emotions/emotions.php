<?php
error_reporting(0);
include ("../../../../data/cache_emot.php");

print<<<eot
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Emots</title>
	<script language="javascript" type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="jscripts/functions.js"></script>
	<base target="_self" />
</head>
<body>
<div align="center">
		<table border="0" cellspacing="0" cellpadding="4" width="100%">
eot;

$perline=15;
$selbody='';
if (is_array($myemots)) {
	$i=0;
	while (@list($emotcode, $emott)=@each($myemots)) {
		$emotthumb=$emott['thumb'];
		$emotorigin=$emott['image'];
		$selbody.="<td><a href=\"javascript:insertEmotion('{$emotorigin}','{$emotcode}');\"><img src=\"../../../../images/emot/{$emotthumb}\" border=\"0\" alt=\"{$emotcode}\" /></a></td>";
		$i+=1;
		if ($i%$perline==0) $selbody.="</tr><tr>";
		unset ($emotcode, $emotthumb);
	}
}

print<<<eot
<tr>
$selbody
</tr>
		</table>
	</div>
</body>
</html>
eot;

?>