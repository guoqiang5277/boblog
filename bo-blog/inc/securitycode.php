<?PHP
/* -----------------------------------------------------
Bo-Blog 2 : The Blog Reloaded.
<<A Bluview Technology Product>>
��ֹʹ��Windows���±��޸��ļ����ɴ���ɵ�һ��ʹ�ò�����ˡ�����
PHP+MySQL blog system.
Code: Bob Shen
Offical site: http://www.bo-blog.com
Copyright (c) Bob Shen �й����Ϻ�
In memory of my university life
------------------------------------------------------- */

//Security Code Image Generation
//This part needs GD Library to be installed
//Original Code: С�������   http://guan8.net
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once ("../data/config.php");

if ($db_defaultsessdir!=1) session_save_path("../{$db_tmpdir}");
session_cache_limiter("private, must-revalidate");
session_start();
session_register('code');
$_SESSION['code'] = "";
$width = "40";//ͼƬ��
$height = "15";//ͼƬ��
$len = "4";//���ɼ�λ��֤��
$bgcolor = "#ffffff";//����ɫ
$noise = true;//�����ӵ�
$noisenum = 10;//�ӵ�����
$border = false;//�߿�
$bordercolor = "#000000";
$image = imageCreate($width, $height);
$back = getcolor($bgcolor);
imageFilledRectangle($image, 0, 0, $width, $height, $back);
$size = $width/$len;
if($size>$height) $size=$height;
$left = ($width-$len*($size+$size/10))/$size;

$textall=range('A','Z');
for ($i=0; $i<$len; $i++) {
    $tmptext=rand(0, 25);
	$randtext = $textall[$tmptext];
    $code .= $randtext;
}
$textColor = imageColorAllocate($image, 0, 0, 0);
imagestring($image, $size, 0, 0, $code, $textColor); 

if($noise == true) setnoise();
$_SESSION['code'] = $code;
$bordercolor = getcolor($bordercolor); 
if($border==true) imageRectangle($image, 0, 0, $width-1, $height-1, $bordercolor);
header("Content-type: image/png");
imagePng($image);
imagedestroy($image);
function getcolor($color)
{
     global $image;
     $color = eregi_replace ("^#","",$color);
     $r = $color[0].$color[1];
     $r = hexdec ($r);
     $b = $color[2].$color[3];
     $b = hexdec ($b);
     $g = $color[4].$color[5];
     $g = hexdec ($g);
     $color = imagecolorallocate ($image, $r, $b, $g); 
     return $color;
}
function setnoise()
{
	global $image, $width, $height, $back, $noisenum;
	for ($i=0; $i<$noisenum; $i++){
		$randColor = imageColorAllocate($image, rand(0, 255), rand(0, 255), rand(0, 255));  
		imageSetPixel($image, rand(0, $width), rand(0, $height), $randColor);
	} 
}
?> 