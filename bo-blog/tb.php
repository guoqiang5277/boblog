<?PHP
/* -----------------------------------------------------
Bo-Blog 2 : The Blog Reloaded.
<<A Bluview Technology Product>>
禁止使用Windows记事本修改文件，由此造成的一切使用不正常恕不解答！
PHP+MySQL blog system.
Code: Bob Shen
Offical site: http://www.bo-blog.com
Copyright (c) Bob Shen 中国－上海
In memory of my university life
------------------------------------------------------- */

define ("noCounter", 1);
require_once ("global.php");
include_once ("data/mod_config.php");

if ($mbcon['allowtrackback']!=1) tb_xml_error('Trackback is disabled.');

acceptrequest('t,extra');
$t=(integer)$t;
if ($t<0) tb_xml_error ("Invalid ID.");
$v_id=$t;
unset($t);
//$rawdata=get_http_raw_post_data();
//die ($rawdata);

//Detect Charset
$charset_convert=0;
$charset=strtolower($_SERVER['HTTP_ACCEPT_CHARSET']);
if ($charset && !strstr($charset, 'utf-8')) {
	if (strstr($charset, 'gb') || strstr($charset, 'big5')) {
		 tb_xml_error ("Your trackback uses a charset other than UTF-8.");
	}
}

$originblog=$blog->getbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE `blogid`='{$v_id}' AND `property`=0 LIMIT 0,1");
if ($originblog['blogid']!=$v_id) tb_xml_error ("Invalid ID or the ID refers to a locked entry.");

//Anti-spam
$tbauthentic=tbcertificate($originblog['blogid'], $originblog['pubtime']);
if ($tbauthentic!=$extra) tb_xml_error ("Verifying failed.");

acceptrequest ('title,excerpt,url,blog_name');
$sourceforcheck=array('title'=>$title, 'excerpt'=>$excerpt, 'url'=>$url,'blog_name'=>$blog_name);
if ($url=='') tb_xml_error ("Invalid URL.");
else $url=safe_convert($url);
if ($excerpt=='') tb_xml_error ("We require all Trackbacks to provide an excerption.");
else $excerpt=tb_convert($excerpt);
$title=msubstr($title, 0, $mbcon['maxtblen']);
$blog_name=msubstr($blog_name, 0, $mbcon['maxtblen']);
$excerpt=msubstr($excerpt, 0, $mbcon['maxtblen']);
$title=($title) ? tb_convert($title) : $lnc[13];
$blog_name=($blog_name) ? tb_convert($blog_name) : $lnc[14];

//Check unacceptable words
$setspam=0;
extract_forbidden();
if (check_ip ($userdetail['ip'], $forbidden['banip'])) tb_xml_error ("Your IP address is banned from sending trackbacks.");
if (preg_search($excerpt, $forbidden['banword']) || preg_search($title, $forbidden['banword']) || preg_search($blog_name, $forbidden['banword'])) tb_xml_error ("The trackback content contains some words that are not welcomed on our site. You may edit your post and send it again. Sorry for the inconvenience.");
if ($mbcon['antispam']=='1') {
	if (preg_search($excerpt, $forbidden['suspect'])) $setspam=1;
}


//Trackback评分式的防御机制
//此部分的思路是与安全天使（www.4ngel.net）讨论的结果。原始代码由4ngel原创。
//Under test
$point=0;
if ($mbcon['tbfilter']==3) { //如果人工审核
    $setspam = 1;
} elseif ($mbcon['tbfilter']!=0) { //如果不是不打开

    if ($mbcon['tbfilter']==2) {
        // 防范强:检查来路
		$source_content = '';
		if (!empty($sourceforcheck['url'])) {
		 $source_content = @fopen_url($sourceforcheck['url'], true);
		}
		if (empty($source_content)) {
			//没有获得原代码就-1分
		 $point -= 1;
		} else {
			if (strpos(strtolower($source_content), strtolower($this_server)) !== FALSE) {
            //对比链接，如果原代码中包含本站的hostname就+1分，这个未必成立
				$point += 1;
			}
			if (strpos(strtolower($source_content), strtolower($sourceforcheck['title'])) !== FALSE) {
            //对比标题，如果原代码中包含发送来的title就+1分，这个基本可以成立
				$point += 1;
			}
			if (strpos(strtolower($source_content), strtolower($sourceforcheck['excerpt'])) !== FALSE) {
				//对比内容，如果原代码中包含发送来的excerpt就+1分，这个由于标签或者其他原因，未必成立
				$point += 1;
			}
		}
    }
    $tbinterval = ($mbcon['tbfilter']==1) ? '30' : '60';
    //根据防范强度设置时间间隔，强的话在30内发现有同一IP发送。弱的话就是60秒内发现有同一IP发送.
    $trytb=$blog->countbyquery("SELECT COUNT(*) FROM `{$db_prefix}replies` WHERE `repip`='{$userdetail['ip']}' AND `reproperty`>=4 AND `reptime`+{$tbinterval}>='".time()."'");
    //在单位时间内发送的次数
    if ($trytb > 0) {
        //如果发现在单位时间内同一IP发送次数大于0就扣一分，人工有这么快发送trackback的吗？
        $point -= 1;
    } 

    if ($mbcon['tbfilter']==2) {
        // 防范强:最终分数少于1分就CUT！
        $setspam = (($point < 1) ? 1 : 0);
    } else {
        // 防范弱:最终分数少于0分才CUT！
        $setspam = (($point < 0) ? 1 : 0);
    }
}

//Final result
$tbproperty=($setspam==1) ? 5 : 4;

//Input
define('REPLYSPECIAL', 1);
include("admin/cache_func.php");
$maxrecord=$blog->getsinglevalue("{$db_prefix}maxrec");
$currentmaxid=$maxrecord['maxrepid']+1;
$reptime=time();
$blog->query("INSERT INTO  `{$db_prefix}replies` VALUES ('{$currentmaxid}', '{$tbproperty}', '{$v_id}', '{$reptime}', '-1', '{$blog_name}', '{$title}', '{$url}', '{$userdetail['ip']}', '{$excerpt}', '0', '0', '0', '0', '', '0', '', '0', '', '0', '', '', '', '', '', '', '', '')");
$blog->query("UPDATE `{$db_prefix}maxrec` SET `maxrepid`='{$currentmaxid}'");
if ($setspam==0) {
	$blog->query("UPDATE `{$db_prefix}counter` SET `tb`=`tb`+1");
	$blog->query("UPDATE `{$db_prefix}blogs` SET `tbs`=`tbs`+1 WHERE `blogid`='{$v_id}'");
	recache_latestreplies();
}
tb_xml_success();



function tb_xml_error($error) {
	header("Content-type:application/xml");
	echo "<?xml version=\"1.0\" ?>";
	print <<<eot
<response>
<error>1</error>
<message>{$error}</message>
</response>
eot;
	exit;
}

function tb_xml_success() {
	header("Content-type:application/xml");
	echo "<?xml version=\"1.0\" ?>";
	print <<<eot
<response>
<error>0</error>
</response>
eot;
	exit;
}

function tb_convert ($str) {
	$str=safe_convert($str);
	$str=preg_replace("/&(.+?);/is", "", $str);
	$str=preg_replace("/\[(.+?)\]/is", "", $str);
	$str=str_replace("\\", "", $str);
	return $str;
}

function fopen_url($url, $convert_case = false) {
	$file_content = '';

	$surl=parse_url($url);
	if ($surl['port']=='') $surl['port']=80;
	$fp = fsockopen($surl['host'], $surl['port'], $errno, $errstr, 8);
	if ($fp) {
		$out = "GET {$surl['path']}".($surl['query'] ? '?'.$surl['query'] : '')." HTTP/1.1\r\n";
		$out .= "Host: {$surl['host']}\r\n";
		$out .= "Connection: Close\r\n\r\n";
		fwrite($fp, $out);
		while (!feof($fp)) {
			$file_content .= fgets($fp, 128);
		}
		fclose($fp);
	}

//Alternatives using fopen instead of fsockopen, not activated by default
/*	if($file = @fopen($url, 'r')){
		$i = 0;
		while (!feof($file) && $i++ < 1000) {
			if ($convert_case) {
				$file_content .= strtolower(fread($file, 4096));
			} else {
				$file_content .= fread($file, 4096);
			}
		}
		fclose($file);
	} elseif (function_exists('file_get_contents')) {		
		$file_content = @file_get_contents($url);
	} 
	elseif (function_exists('curl_init')) {
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT,2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl_handle, CURLOPT_FAILONERROR,1);
  		curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Trackback Spam Check');
		$file_content = curl_exec($curl_handle);		
		curl_close($curl_handle);
	} 
	else {
		$file_content = '';
	}
*/	
	return $file_content;
}