<?php
function plugin_fmplayer_run($str) { 
  $str=preg_replace("/\s*\[fmp(,id=.+?)?(,url=.+?)?\](.*?)\[\/fmp\]\s*/ise", "makeFMPlayer('\\1', '\\2', '\\3')", $str);
  return $str;
}
function makeFMPlayer($tid, $url, $str) {
	$RunID = 0;
	if (!empty($tid)) {$RunID+=2;}
	if (!empty($url)) {$RunID+=4;}
	if (!empty($str)) {$RunID++;}
	$str=str_replace("<br/>", "\n" , stripslashes($str));
	if($RunID<=5){
		switch ($RunID) {
			case 5:
			case 4:$url=str_replace(',url=', '', strtolower($url));break;
			case 3:
			case 2:$url="plugin/fmplayer/playlist.php?id=".str_replace(',id=', '', strtolower($tid));break;
			case 1:	$url=$str;$str="";break;
		}
	$tid=rand(999, 999999);
	$fmp_put="<div class=\"quote fmp_q\"><div class=\"quote-title\">{$str}</div><div class=\"quote-content\"><div id=\"fmp_{$tid}\"><a href=\"http://www.macromedia.com/go/getflashplayer\">Get the Flash Player</a> to see this player.</div><script type=\"text/javascript\">var so_{$tid} = new SWFObject(\"images/others/mediaplayer.swf\",\"JW_Media_Player_{$tid}\",\"80%\",\"20\",\"7\");so_{$tid}.addVariable(\"file\",\"{$url}\");so_{$tid}.addVariable(\"displayheight\",\"0\");so_{$tid}.addVariable(\"base\",\".\");so_{$tid}.write(\"fmp_{$tid}\");</script></div></div>";
	}else{
		$fmp_put ="FMP Code Error";
	}
	return $fmp_put;
}
?>