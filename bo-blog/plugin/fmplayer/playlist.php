<?PHP
error_reporting(E_ALL & ~E_NOTICE);
include("include.php");
include($file['list']);
$id=$_GET['id'];
foreach($fmp_list as $fp_id => $fp_song){
	$addto.="\t\t<track>\n";
		foreach ($fp_song as $key => $value) {
			if(!empty($id) & $id==$fp_id)$addtoid.= "\t\t\t<$key>{$value}</$key>\n";
		   	$addto.= "\t\t\t<$key>{$value}</$key>\n";
		}
	$addto.="\t\t</track>\n";
}
if(!empty($addtoid)){
	$addto="\n\t\t<track>\n";
	$addto.= $addtoid;
	$addto.="\t\t</track>\n";
}
$addto=<<<eot
<playlist version="1" xmlns="http://xspf.org/ns/0/">
    <trackList>
$addto
	 </trackList>
</playlist>
eot;
echo $addto;
?>