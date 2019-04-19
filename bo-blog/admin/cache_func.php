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


if (!defined('VALIDADMIN') && !defined('REPLYSPECIAL')) die ('Access Denied');

function recache_links () {
	global $blog, $db_prefix, $lna;
	$linkgp=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}linkgroup` WHERE `linkgppt`<>'0' ORDER BY `linkgporder`");
	if ($linkgp && is_array($linkgp)) {
		foreach ($linkgp as $linkgpeachitem) {
			unset ($tmp_gp);
			$tmp_gp=$linkgpeachitem['linkgpid'];
			$displaygp[$tmp_gp]="<li><strong>{$linkgpeachitem['linkgpname']}</strong></li>";
			$visiblegp[]=$tmp_gp;
		}
	} else {
		$displaygp[]=$lna[147];
	}
	$links=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}links` WHERE `isdisplay`<>'0' ORDER BY `linkorder`");
	if ($links && is_array($links)) {
		foreach ($links as $linkeachitem) {
			unset ($tmp_gp, $tmp_displayitem);
			$tmp_gp=$linkeachitem['linkgptoid'];
			if (!@in_array($tmp_gp, $visiblegp)) continue;
			if ($linkeachitem['linklogo']) $tmp_displayitem="<img src=\"{$linkeachitem['linklogo']}\" alt=\"{$linkeachitem['linkname']}\" border=\"0\" />";
			else $tmp_displayitem=$linkeachitem['linkname'];
			$displaygp[$tmp_gp].="<li class=\"indent\"><a href=\"{$linkeachitem['linkurl']}\" target=\"_blank\" title=\"{$linkeachitem['linkname']} - {$linkeachitem['linkdesc']}\">{$tmp_displayitem}</a></li>";
		}
	}
	$linksshow='<ul>'.@implode('', $displaygp).'</ul>';
	$write="<?PHP\n\$linksshow=<<<eot\n{$linksshow}\neot;\n?>";
	writetofile ("data/cache_links.php", $write);
}

function recache_emotselection () {
	global $mbcon;
	include("data/cache_emot.php");
	$perline=floor($mbcon['emotperline']);
	$perpage=floor($mbcon['emotperpage']);
	if ($perline<=0) $perline=5;
	if ($perpage<=0) $perpage=100;
	if (is_array($myemots)) {
		$i=0;
		while (@list($emotcode, $emott)=@each($myemots)) {
			$emotthumb=$emott['thumb'];
			$selbody.="<a href=\"javascript: insertemot('$emotcode');\"><img src=\"images/emot/{$emotthumb}\" alt='emot' border='0'/></a>";
			$i+=1;
			if ($i%$perline==0) $selbody.="<br/>";
			if ($i%$perpage==0) $selbody.="<!-- EmotPage -->";
			unset ($emotcode, $emotthumb);
		}
	$write="<?PHP\n\$emots=<<<eot\n{$selbody}\neot;\n?>";
	writetofile ("data/cache_emsel.php", $write);
	}
}

function recache_mods () {
	global $blog, $db_prefix, $lna;
	$allmods_active=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}mods` WHERE `active`=1 ORDER BY  `modorder`");
	if (is_array($allmods_active)) {
		for ($i=0; $i<count($allmods_active); $i++) {
			$isection=$allmods_active[$i]['position'];
			$section_out[$isection][]="'{$allmods_active[$i]['name']}'";
			$section_all[]="'{$allmods_active[$i]['name']}'";
		}
	}

	if (is_array($section_out)) {
		foreach($section_out as $key=>$valuearray) {
			$body_sec=@implode(', ', $valuearray);
			$writeout.="addbar('{$key}', array({$body_sec}));\n";
		}
	}
	if (!writetofile ("data/mods.php", "<?PHP\nif (!defined('VALIDREQUEST')) die ('Access Denied.');\n{$writeout}")) {
		catcherror ($lna[66]."data/mods.php");
	}

	unset ($writeout);
	$writeout="<?PHP\n\$allopenmods=array(".@implode(', ', $section_all).");\n?>";
	if (!writetofile ("data/allmods.php", $writeout)) {
		catcherror ($lna[66]."data/allmods.php");
	}
}

function recache_adminlist () {
	global $blog, $db_prefix, $lna;
	include("data/cache_usergroup.php");
	if (is_array($usergp)) {
		foreach ($usergp as $key=>$value) {
			if (file_exists("data/usergroup{$key}.php")) {
				$tmp=readfromfile("data/usergroup{$key}.php");
				if (strstr($tmp, "['AddEntry']='1'")) $admin_s[]=$key;
			}
		}
		if (is_array($admin_s)) {
			$query_string=@implode(',', $admin_s);
			$all_admins=$blog->getarraybyquery("SELECT * FROM `{$db_prefix}user` WHERE usergroup in ($query_string)");
			for ($i=0; $i<count($all_admins['userid']); $i++) {
				$out[]="'{$all_admins['userid'][$i]}'=>'{$all_admins['username'][$i]}'";
			}
			$outout=@implode(',', $out);
			$writeout="<?PHP\n\$adminlist=array({$outout});\n";
			if (!writetofile ("data/cache_adminlist.php", $writeout)) {
				catcherror ($lna[66]."data/cache_adminlist.php");
			}
		}
	}
}

function recache_categories () {
	global $db_prefix, $lna;
	$result=db_query("SELECT * FROM `{$db_prefix}categories` {$plusquery} ORDER BY `cateorder`");
	$result2=db_query("SELECT category,COUNT(*) FROM `{$db_prefix}blogs` GROUP BY `category`");
	while ($row=db_fetch_array($result2)) {
		$result3[$row['category']]=$row['COUNT(*)'];
 	}
	$previousid=-1;
	while ($row=db_fetch_array($result)) {
		$i=$row['cateid'];
		$row['parentcate']=($row['catemode']==1) ? $previousid : -1;
		$writeout.="<?PHP exit;?><|>{$row['cateid']}<|>".stripslashes($row['catename'])."<|>".stripslashes($row['catedesc'])."<|>{$row['cateproperty']}<|>{$row['cateorder']}<|>{$row['catemode']}<|>{$row['cateurl']}<|>{$row['cateicon']}<|>{$result3[$i]}<|>{$row['parentcate']}<|>{$row['cateurlname']}<|>\n";
		if ($row['catemode']==0) $previousid=$row['cateid']; //Change parent id now
	}
	if (!writetofile ("data/cache_categories.php", $writeout)) catcherror ($lna[66]."data/cache_categories.php");
}

function recache_latestentries () {
	global $blog, $db_prefix, $mbcon, $lna;
	$mbcon['entrylength']=($mbcon['entrylength']==0) ? 9999 : $mbcon['entrylength'];
	$result_limit=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE `property`<2 ORDER BY `pubtime` DESC LIMIT 0, {$mbcon['entrynum']}");
	$result_all=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE `property`<3 ORDER BY `pubtime` DESC LIMIT 0, {$mbcon['entrynum']}");
	if (is_array($result_limit)) {
		foreach ($result_limit as $result_limit_detail) {
			$title=msubstr($result_limit_detail['title'], 0, $mbcon['entrylength']);
			if ($title!=$result_limit_detail['title']) $title.='...';
			$outcache_limit.="\$cache_latest_limit[]=array(\"blogid\"=>{$result_limit_detail['blogid']}, \"title\"=>'{$title}', \"category\"=>{$result_limit_detail['category']}, \"fulltitle\"=>'{$result_limit_detail['title']}', \"blogalias\"=>'{$result_limit_detail['blogalias']}');\n";
		}
	}
	if (is_array($result_all)) {
		foreach ($result_all as $result_all_detail) {
			$title=msubstr($result_all_detail['title'], 0, $mbcon['entrylength']);
			if ($title!=$result_all_detail['title']) $title.='...';
			$outcache_limit.="\$cache_latest_all[]=array(\"blogid\"=>{$result_all_detail['blogid']}, \"title\"=>'{$title}', \"category\"=>{$result_all_detail['category']}, \"fulltitle\"=>'{$result_all_detail['title']}', \"blogalias\"=>'{$result_all_detail['blogalias']}');\n";
		}
	}
	if (!writetofile ("data/cache_latest.php", "<?PHP\n".$outcache_limit."?>")) catcherror ($lna[66]."data/cache_latest.php");
}

function recache_latestreplies () {
	global $blog, $db_prefix, $mbcon, $lna;
	$mbcon['replylength']=($mbcon['replylength']==0) ? 9999 : $mbcon['replylength'];
	$result_limit=$blog->getgroupbyquery("SELECT t1.*, t2.title, t2.blogalias FROM `{$db_prefix}replies` t1 INNER JOIN `{$db_prefix}blogs` t2 ON t2.blogid=t1.blogid WHERE (t1.reproperty='0' OR t1.reproperty='4') AND t2.property<2 ORDER BY t1.reptime DESC LIMIT 0, {$mbcon['replynum']}");
	$result_all=$blog->getgroupbyquery("SELECT t1.*, t2.title, t2.blogalias FROM `{$db_prefix}replies` t1 INNER JOIN `{$db_prefix}blogs` t2 ON t2.blogid=t1.blogid WHERE t1.reproperty<>'2'  AND t1.reproperty<>'3' AND t1.reproperty<>'5' AND t2.property<3 ORDER BY t1.reptime DESC LIMIT 0, {$mbcon['replynum']}");
	if (is_array($result_limit)) {
		foreach ($result_limit as $result_limit_detail) {
			$result_limit_detail['repcontent']=strip_tags($result_limit_detail['repcontent']);
			$result_limit_detail['repcontent']=strip_ubbs($result_limit_detail['repcontent']);
			$title=msubstr($result_limit_detail['repcontent'], 0, $mbcon['replylength']);
			if ($title!=$result_limit_detail['repcontent']) $title.='...';
			$outcache_limit.="<?php die();?><|>limit<|>{$result_limit_detail['blogid']}<|>{$title}<|>{$result_limit_detail['replier']}<|>{$result_limit_detail['repid']}<|>{$result_limit_detail['title']}<|>{$result_limit_detail['blogalias']}<||>";
			//$outcache_limit.="\$cache_replies_limit[]=array(\"blogid\"=>{$result_limit_detail['blogid']}, \"repcontent\"=>\"{$title}\", \"replier\"=>\"{$result_limit_detail['replier']}\",  \"repid\"=>'{$result_limit_detail['repid']}', \"title\"=>\"{$result_limit_detail['title']}\", \"blogalias\"=>'{$result_limit_detail['blogalias']}');\n";
		}
	}
	if (is_array($result_all)) {
		foreach ($result_all as $result_all_detail) {
			$result_all_detail['repcontent']=strip_tags($result_all_detail['repcontent']);
			$result_all_detail['repcontent']=strip_ubbs($result_all_detail['repcontent']);
			$result_all_detail['repcontent']=str_replace("\\", "\\\\", $result_all_detail['repcontent']);
			$result_all_detail['repcontent']=str_replace('$', "\\\$", $result_all_detail['repcontent']);
			$title=msubstr($result_all_detail['repcontent'], 0, $mbcon['replylength']);
			if ($title!=$result_all_detail['repcontent']) $title.='...';
			$outcache_limit.="<?php die();?><|>all<|>{$result_all_detail['blogid']}<|>{$title}<|>{$result_all_detail['replier']}<|>{$result_all_detail['repid']}<|>{$result_all_detail['title']}<|>{$result_all_detail['blogalias']}<||>";
			//$outcache_limit.="\$cache_replies_all[]=array(\"blogid\"=>{$result_all_detail['blogid']}, \"repcontent\"=>\"{$title}\", \"replier\"=>\"{$result_all_detail['replier']}\",  \"repid\"=>'{$result_all_detail['repid']}', \"title\"=>\"{$result_all_detail['title']}\", \"blogalias\"=>'{$result_all_detail['blogalias']}');\n";
		}
	}
	if (!writetofile ("data/cache_replies.php", $outcache_limit)) catcherror ($lna[66]."data/cache_replies.php");
}

function recache_currentmonthentries () {
	global $blog, $db_prefix, $nowtime, $lna, $lnc, $mbcon, $config;
	$cm=$nowtime['month'];
	$cy=$nowtime['year'];
	$result=$blog->getarraybyquery("SELECT (cday) FROM `{$db_prefix}calendar` WHERE `cyearmonth`='{$nowtime['Ym']}'");
	$month_calendar=(is_array($result['cday'])) ? array_unique($result['cday']) : array();
	if ($mbcon['lunarcalendar']!=0) {
		$lunarstream=lunarcalendar($cm, $cy);
		$lunarym="<br/>{$lunarstream['year']}";
	}
	else $lunarstream='';
	$calendarbody=makecalendar ($cm, $cy, $month_calendar, $lunarstream);
	$nextmonth=($cm==12) ? 1 : $cm+1;
	$lastmonth=($cm==1) ? 12 : $cm-1;
	$yearofnextmonth=($cm==12) ? $cy+1 : $cy;
	$yearoflastmonth=($cm==1) ? $cy-1 : $cy;
	$nextyear=$cy+1;
	$lastyear=$cy-1;

	$nextmonthurl=getlink_archive($nextmonth, $yearofnextmonth);
	$lastmonthurl=getlink_archive($lastmonth, $yearoflastmonth);
	$nextyearurl=getlink_archive($cm, $nextyear);
	$lastyearurl=getlink_archive($cm, $lastyear);
	$thismonthurl=getlink_archive($cm, $cy);
	$thisyearurl="archive.php";
	$out=<<<eot
<table id="calendar" cellspacing="1" width="100%">
<tbody><tr><td colspan="7" class="calendar-top">
<a href="{$lastyearurl}" rel="noindex,nofollow">&lt;</a>
<a href="{$thisyearurl}" rel="noindex,nofollow"><span class="calendar-year">{$cy}</span></a>
<a href="{$nextyearurl}" rel="noindex,nofollow">&gt;</a>
	&nbsp;&nbsp;
<a href="{$lastmonthurl}" rel="noindex,nofollow">&lt;</a>
<a href="{$thismonthurl}" rel="noindex,nofollow"><span class="calendar-month">{$cm}</span></a>
<a href="{$nextmonthurl}" rel="noindex,nofollow">&gt;</a>{$lunarym}
</td></tr>
<tr class="calendar-weekdays">
	<td class="calendar-weekday-cell">{$lnc[115]}</td>
	<td class="calendar-weekday-cell">{$lnc[116]}</td>
	<td class="calendar-weekday-cell">{$lnc[117]}</td>
	<td class="calendar-weekday-cell">{$lnc[118]}</td>
	<td class="calendar-weekday-cell">{$lnc[119]}</td>
	<td class="calendar-weekday-cell">{$lnc[120]}</td>
	<td class="calendar-weekday-cell">{$lnc[121]}</td>
</tr>
{$calendarbody}
</tbody></table>
eot;
	//$out="<?PHP\n\$month_calendar=array(".@implode(',', $result_all).");";
	if (!writetofile ("data/cache_currentmonth.php", $out)) catcherror ($lna[66]."data/cache_currentmonth.php");
}

function recache_taglist () {
	global $blog, $db_prefix, $lna;
	$alltags=$blog->getarraybyquery("SELECT `tagname` FROM `{$db_prefix}tags`");
	$out=@implode(' ', $alltags['tagname']);
	if (!writetofile ("data/cache_tags.php", $out)) catcherror ($lna[66]."data/cache_tags.php");
}

function recache_cleartemp () {
	global $blog, $db_prefix, $lna, $db_tmpdir;
	$handle=opendir("{$db_tmpdir}/");
	if (!$handle) catcherror ("{$lna[155]} {$db_tmpdir}/ {$lna[156]}<ul><li>{$lna[157]}</li><li>{$lna[158]}</li><li>{$lna[159]}</li></ul>");
	while (false !== ($file=readdir($handle))) {
		if ($file!="." && $file!=".."  && !is_dir("{$db_tmpdir}/{$file}")) {
			@unlink ("{$db_tmpdir}/{$file}");
		}
	}
}

function recache_plugins () {
	global $blog, $db_prefix, $lna;
	$all_pl=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}plugins` WHERE `active`=1 ORDER BY `plid` ASC");
	if (is_array($all_pl)) {
		foreach ($all_pl as $plugin) {
			if ($plugin['plregister']) {
				$register=@explode(',', str_replace(' ','',$plugin['plregister']));
				foreach ($register as $singlereg) {
					$blogplugin[$singlereg][]=$plugin['plname'];
				}
			}
		}
	}
	if (is_array($blogplugin)) {
		foreach ($blogplugin as $typename=>$plugins) {
			$pluginline='"'.@implode(',', $plugins).'"';
			$plugwrt[]="\$blogplugin['{$typename}']={$pluginline};\r\n";
		}
		$out="<?PHP\r\n".@implode('', $plugwrt);
	}
	if (!writetofile ("data/plugin_enabled.php", $out)) catcherror ($lna[66]."data/plugin_enabled.php");
}