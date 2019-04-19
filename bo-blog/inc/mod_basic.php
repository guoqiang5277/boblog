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

if (!defined('VALIDREQUEST')) die ('Access Denied.');

$systemitems=array('index', 'customrss', 'login', 'alltags', 'guestbook', 'togglesidebar', 'starred', 'viewlinks', 'category', 'link', 'statistics', 'archive', 'misc', 'entries', 'replies', 'calendar', 'search', 'copyright', 'mii', 'columnbreak');
$defineditems=array_keys($blogitem);
$undefineditems=array_diff($systemitems, $defineditems);
if (count($undefineditems)>0) {
	foreach ($undefineditems as $undefineditem) {
		$blogitem[$undefineditem]=array();
	}
}

$blogitem['index']+=array('type'=>'link', 'url'=>'index.php', 'text'=>$lnc[88]);

$blogitem['customrss']+=array('type'=>'link', 'url'=>'feed.php', 'text'=>'RSS', 'target'=>'_blank');

$blogitem['login']+=array('type'=>'link');
if ($logstat==1 || $openidloginstat==1) {
	$blogitem['login']['text']=$lnc[78];
	$blogitem['login']['url']='login.php?job=logout';
} else {
	$blogitem['login']['text']=$lnc[89];
	$blogitem['login']['url']='login.php';
}

$blogitem['modpro']['type']='link';
if ($logstat==1) {
	$blogitem['modpro']['text']=$lnc[90];
	$blogitem['modpro']['url']='login.php?job=modpro';
} 
elseif ($openidloginstat==1) {
	$blogitem['modpro']['text']=$lnc[318];
	$blogitem['modpro']['url']='http://'.$_COOKIE['openid_url_id'];
}
else {
	$blogitem['modpro']['text']=$lnc[79];
	$blogitem['modpro']['url']='login.php?job=register';
}


if ($flset['tags']!=1) $blogitem['alltags']+=array('type'=>'link', 'url'=>'tag.php', 'text'=>$lnc[288]);

if ($flset['guestbook']!=1) $blogitem['guestbook']+=array('type'=>'link', 'url'=>'guestbook.php', 'text'=>$lnc[91]);

if ($plugin_closesidebar!=1) $blogitem['togglesidebar']+=array('type'=>'link', 'url'=>'javascript:showHideSidebar();', 'text'=>$lnc[92]);

if ($flset['star']!=1) $blogitem['starred']+=array('type'=>'link', 'url'=>'star.php', 'text'=>$lnc[93]);

$blogitem['viewlinks']+=array('type'=>'link', 'url'=>'links.php', 'text'=>$lnc[94]);

//[Start]category
if (in_array('category', $allopenmods)) {
	if ($job=='category') {
		$categories[$itemid]['catename']="<strong>{$categories[$itemid]['catename']}</strong>";
	}
	$categoryshow=$categoryplainshow="<ul>";
	if ($categories && is_array($categories)) {
		foreach ($categories as $catearray) {
			$catearray['catedesc']=strip_tags($catearray['catedesc']);
			if ($catearray['cateurl']!='') {
				$cateurl=$catearray['cateurl'];
				$rssurl='';
				$target=' target="_blank"';
				$catecount='';
			} else{
				$cateurl=getlink_category($catearray['cateid']);
				$rssurl=" <a href=\"feed.php?go=category_{$catearray['cateid']}\"><img src=\"{$mbcon['images']}/rss.png\" border=\"0\" alt=\"RSS\" title=\"{$lnc[95]}\" /></a>";
				$target='';
				if ($mbcon['parentcatenum']=='2' && $catearray['subcates']!='') $catecount='';
				else $catecount=($mbcon['showcateartnum']==1) ? " [".floor($catearray['catecount'])."]" : '';
				
			}
			$cateicon=($catearray['cateicon']) ? "<img src=\"{$catearray['cateicon']}\" alt=\"\" style=\"margin:3px 1px -4px 0px;\"/> " : '';
			if ($catearray['catemode']==0) {
				$categoryshow.="<li>{$cateicon}<a href=\"{$cateurl}\" title=\"{$catearray['catedesc']}\"{$target}>{$catearray['catename']}</a>{$catecount}{$rssurl}</li>";
				$categoryplainshow.="<li><a href=\"{$cateurl}\" title=\"{$catearray['catedesc']}\"{$target}>{$catearray['catename']}</a></li>";
			}
			else {
				$categoryshow.="<li class=\"indent\">{$cateicon}<a href=\"{$cateurl}\" title=\"{$catearray['catedesc']}\"{$target}>{$catearray['catename']}</a>{$catecount}{$rssurl}</li>";
				$categoryplainshow.="<li class=\"indent\"><a href=\"{$cateurl}\" title=\"{$catearray['catedesc']}\"{$target}>{$catearray['catename']}</a></li>";
			}
		}
		$categoryshow.="</ul>";
		$categoryplainshow.="</ul>";
		if ($job=='category') {
			$categories[$itemid]['catename']=strip_tags($categories[$itemid]['catename']);
		}
	} else {
		$categoryshow.="None</ul>";
	}
	plugin_runphp('sidebarcategory');
	$blogitem['category']+=array(
		'type'=>'block',
		'name'=>'category',
		'title'=>$lnc[96],
		'content'=>$categoryshow
	);
	if ($mbcon['extend_category']==1) $blogitem['category']['extend']=1;
}
//[End]category

//[Start]link
if (in_array('link', $allopenmods)) {
	if (file_exists("data/cache_links.php")) include_once ("data/cache_links.php");
	$blogitem['link']+=array(
		'type'=>'block',
		'name'=>'link',
		'title'=>$lnc[94],
		'content'=>$linksshow
	);
	if ($mbcon['extend_link']==1) $blogitem['link']['extend']=1;
}
//[End]link

//[Start]statistics
if (in_array('statistics', $allopenmods)) {
	if ($mbcon['stattotal']=='1') $statshow.="{$lnc[97]} {$statistics['total']}<br/>";
	if ($mbcon['stattoday']=='1') $statshow.="{$lnc[98]} {$statistics['today']}<br/>";
	if ($mbcon['statentries']=='1') $statshow.="{$lnc[99]} {$statistics['entries']}<br/>";
	if ($mbcon['statreplies']=='1') $statshow.="<a href=\"view.php?go=comment\">{$lnc[100]} {$statistics['replies']}</a><br/>";
	if ($mbcon['stattb']=='1') $statshow.="<a href=\"view.php?go=tb\">{$lnc[101]} {$statistics['tb']}</a><br/>";
	if ($flset['guestbook']!=1 && $mbcon['statmessages']=='1') $statshow.="<a href=\"guestbook.php\">{$lnc[102]} {$statistics['messages']}</a><br/>";
	if ($mbcon['statusers']=='1') $statshow.="<a href=\"view.php?go=userlist\">{$lnc[103]} {$statistics['users']}</a><br/>";
	if ($mbcon['statonline']=='1' && !defined('noCounter')) $statshow.="{$lnc[104]} {$statistics['nowusers']}<br/>";
	plugin_runphp('sidebarstatistics');
	$blogitem['statistics']+=array(
		'type'=>'block',
		'name'=>'statistics',
		'title'=>$lnc[105],
		'content'=>$statshow
	);
	if ($mbcon['extend_statistics']==1) $blogitem['statistics']['extend']=1;
}
//[End]statistics

//[Start]archive
if (in_array('archive', $allopenmods)) {
	if (!empty($mbcon['archivemonths'])) {
		$basemonth=$nowtime['month'];
		$baseyear=$nowtime['year'];
		for ($i=0; $i<$mbcon['archivemonths']; $i++) {
			if ($basemonth==0) {
				$basemonth=12;
				$baseyear=$baseyear-1;
			}
			$archiveformat=($mbcon['archiveformat']=='custom') ? $mbcon['customarchiveformat'] : $mbcon['archiveformat'];
			$ymshow=zhgmdate($archiveformat, gmmktime(0, 0, 0, $basemonth, 1, $baseyear));
			$outurl=getlink_archive($basemonth, $baseyear);
			$archive1[]="<li><a href=\"{$outurl}\" rel=\"noindex,nofollow\">{$ymshow}</a></li>";
			$basemonth-=1;
		}
		$archiveshow='<ul>'.implode("\n", $archive1).'</ul>';
	}
	plugin_runphp('sidebararchive');
	$blogitem['archive']+=array(
		'type'=>'block',
		'name'=>'archive',
		'title'=>$lnc[106],
		'content'=>$archiveshow
	);
	if ($mbcon['extend_archive']==1) $blogitem['archive']['extend']=1;
}
//[End]archive

//[Start]Misc
if (in_array('misc', $allopenmods)) {
	if ($logstat==1) {
		$misccontent="<a href='login.php?job=logout'>{$lnc[78]}</a><br/>";
		$misccontent.="<a href='login.php?job=modpro'>{$lnc[90]}</a><br/>";
	}
	elseif ($openidloginstat==1) {
		$misccontent="<a href='login.php?job=logout'>{$lnc[78]}</a><br/>";
	}
	else {
		$misccontent="<a href='login.php'>{$lnc[89]}</a><br/>";
		$misccontent.="<a href='login.php?job=register'>{$lnc[79]}</a><br/>";
	}
	$misccontent.=($permission['CP']==1) ? "<a href='admin.php'>{$lnc[107]}</a><br/>" : '';
	$misccontent.=($permission['AddEntry']==1) ? "<a href='admin.php?act=edit'>{$lnc[108]}</a><br/>" : '';
	$misccontent.=($permission['ApplyLink']==1) ? "<a href='login.php?job=applylink'>{$lnc[109]}</a><br/>" : '';
	$misccontent.="{$lnc[285]} <a href='feed.php'>{$lnc[286]}</a> | <a href='feed.php?go=comment'>{$lnc[287]}</a><br/>{$lnc[111]}UTF-8<br/>";
	$misccontent.="<a href=\"http://validator.w3.org/check?uri=referer\" target=\"_blank\">XHTML 1.0</a>";
	plugin_runphp('sidebarmisc');
	$blogitem['misc']+=array(
		'type'=>'block',
		'name'=>'misc',
		'title'=>$lnc[112],
		'content'=>$misccontent,
		'extend'=>1
	);
}
//[End]Misc

//[Start]entries
if (in_array('entries', $allopenmods)) {
	if (file_exists("data/cache_latest.php")) include ("data/cache_latest.php");
	$listentriesitem=($permission['SeeHiddenEntry']==1) ? $cache_latest_all : $cache_latest_limit;
	$entries_list="<ul>";
	for ($i=0; $i<count($listentriesitem); $i++) {
		$addintionalcssclass=($i%2==0) ? 'rowcouple' : 'rowodd';
		$listentriesitems=$listentriesitem[$i];
		$entries_list.="<li class='{$addintionalcssclass}'><a href=\"".getlink_entry($listentriesitems['blogid'], $listentriesitems['blogalias'])."\" title=\"{$listentriesitems['fulltitle']}\">{$listentriesitems['title']}</a></li>";
	}
	$entries_list.="</ul>";
	plugin_runphp('sidebarentries');
	$blogitem['entries']+=array(
		'type'=>'block',
		'name'=>'entries',
		'title'=>$lnc[113],
		'content'=>$entries_list,
		'extend'=>1
	);
}
//[End]entries

//[Start]replies
if (in_array('replies', $allopenmods)) {
	if (file_exists("data/cache_replies.php")) $tmpreplies=@explode('<||>', readfromfile("data/cache_replies.php"));
	$tmpreplyarray=$cache_replies_all=$cache_replies_limit=array();
	if ($tmpreplies[0]) {
		foreach ($tmpreplies as $tmpsinglereply) {
			$tmpsinglereplyarray=@explode('<|>', $tmpsinglereply);
			if (!$tmpsinglereplyarray[1]) break;
			$replyarrayasigned=array('blogid'=>$tmpsinglereplyarray[2], 'repcontent'=>stripslashes($tmpsinglereplyarray[3]), 'replier'=>$tmpsinglereplyarray[4], 'repid'=>$tmpsinglereplyarray[5], 'title'=>$tmpsinglereplyarray[6], 'blogalias'=>$tmpsinglereplyarray[7]);
			if ($tmpsinglereplyarray[1]=='limit') $cache_replies_limit[]=$replyarrayasigned;
			elseif ($tmpsinglereplyarray[1]=='all') $cache_replies_all[]=$replyarrayasigned;
		}
	}
	
	$listrepliesitem=($permission['SeeHiddenReply']==1) ? $cache_replies_all : $cache_replies_limit;
	if ($listrepliesitem) {
		$replies_list="<ul>";
		for ($i=0; $i<count($listrepliesitem); $i++) {
			$addintionalcssclass=($i%2==0) ? 'rowcouple' : 'rowodd';
			$listrepliesitems=$listrepliesitem[$i];
			$replies_list.="<li class='{$addintionalcssclass}'><a href=\"".getlink_entry($listrepliesitems['blogid'], $listrepliesitems['blogalias'])."#blogcomment{$listrepliesitems['repid']}\" title=\"[{$listrepliesitems['replier']}] - {$listrepliesitems['title']}\">{$listrepliesitems['repcontent']}</a></li>";
		}
		$replies_list.="</ul>";
	}
	plugin_runphp('sidebarreplies');
	$blogitem['replies']+=array(
		'type'=>'block',
		'name'=>'replies',
		'title'=>$lnc[114],
		'content'=>$replies_list,
		'extend'=>1
	);
}
//[End]replies

//[Start]calendar
if (in_array('calendar', $allopenmods)) {
	if (file_exists("data/cache_currentmonthentries.php")) include ("data/cache_currentmonthentries.php");
	acceptrequest('cm,cy');
	$cm=floor($cm);
	$cy=floor($cy);
	$cm=($cm<=0 || $cm>12) ? ($nowtime['month']) : $cm;
	$cy=($cy<=1970 || $cy>2100) ? ($nowtime['year']) : $cy;
	$month_calendar=array();
	if ($cy==$nowtime['year'] && $cm==$nowtime['month']) {
		$cal_body=@readfromfile("data/cache_currentmonth.php");
		if (!strstr($cal_body, "<span class=\"calendar-month\">{$cm}</span>")) { //Cache auto refresh once a month
			define ('REPLYSPECIAL', 1);
			include_once ("admin/cache_func.php");
			recache_currentmonthentries();
			$cal_body=@readfromfile("data/cache_currentmonth.php");
		}
		$currentdate=gmdate('j', $nowtime['timestamp']+3600*$config['timezone']);
		$cal_search=array ("<td id=\"cal{$currentdate}\" class=\"calendar-sunday\">", "<td id=\"cal{$currentdate}\" class=\"calendar-saturday\">", "<td id=\"cal{$currentdate}\" class=\"calendar-day\">");
		$cal_replace=array ("<td id=\"cal{$currentdate}\" class=\"calendar-today\">", "<td id=\"cal{$currentdate}\" class=\"calendar-today\">", "<td id=\"cal{$currentdate}\" class=\"calendar-today\">");
		$cal_body=str_replace($cal_search, $cal_replace, $cal_body);
	} else {
		$cm_s=($cm<10) ? ('0'.$cm) : $cm;
		$month_calendars=$blog->getarraybyquery("SELECT cday FROM `{$db_prefix}calendar` WHERE `cyearmonth`='{$cy}{$cm_s}'");
		$month_calendar=(is_array($month_calendars['cday'])) ? array_unique($month_calendars['cday']) : array();
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
		$cal_body=<<<eot
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
	}
	$blogitem['calendar']+=array(
		'type'=>'block',
		'name'=>'calendar',
		'title'=>$lnc[122],
		'content'=>$cal_body,
		'extend'=>1
	);
}
//[End]calendar

//[Start]Search
if ($mbcon['searchon']==1) {
	$addtagsearch=($flset['tags']==0) ? '' : "<option value=\"5\">{$lnc[127]}</option>";
	$searchbox=<<<eot
	<form method="post" action="visit.php">
	<input name="job" type="hidden" value="search"/>
	<input name="keyword" class="search-field" type="text"/>
	<select name="searchmethod"><option value="1">{$lnc[123]}</option><option value="2">{$lnc[124]}</option><option value="3">{$lnc[125]}</option><option value="4">{$lnc[126]}</option>{$$addtagsearch}</select>
	<input value="{$lnc[128]}" class="button" type="submit"/>
	</form>
eot;
	$blogitem['search']+=array(
		'type'=>'block',
		'name'=>'search',
		'title'=>$lnc[128],
		'content'=>$searchbox,
		'extend'=>1
	);
}
//[End]Search

$blogitem['copyright']+=array('type'=>'html', 'code'=>" Powered by <a href=\"http://www.bo-blog.com\" target=\"_blank\">Bo-Blog {$blogversion}</a><span id=\"footer-security\"><a href=\"http://www.cnbct.org\" target=\"_blank\" title=\"Code detection by Bug.Center.Team\"><img src=\"images/others/detect.gif\" alt=\"Code detection by Bug.Center.Team\" border=\"0\" /></a></span>");
$blogitem['mii']+=array('type'=>'link', 'url'=>'http://www.miibeian.gov.cn', 'text'=>'<br/>'.$mbcon['miinum'], 'target'=>'_blank');

$blogitem['columnbreak']+=array('type'=>'block', 'name'=>'columnbreak', 'title'=>'1', 'content'=>'1', 'extend'=>1);
