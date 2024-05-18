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

if (!$job) $job='default';
else $job=basename($job);
$ifannouncement="none";

acceptrequest('mode');
if ($mode==1 || $mode==2) {
	$mbcon['main_list']=$mode-1;
	$mbcon['cate_list']=$mode-1;
	$mbcon['archive_list']=$mode-1;
	$mbcon['showday_list']=$mode-1;
	$mbcon['starred_list']=$mode-1;
} elseif (!empty($mode) && (!is_numeric($mode) || $mode>2)) {
	getHttp404($lnc[313]);
}

$pageway=1;

$m_b=new getblogs;
if ($permission['SeeHiddenEntry']!=1) {
	$limitation1="WHERE `property`<2 AND `frontpage`=0";
	$limitation2="WHERE `property`<2 AND `frontpage`=0 AND ";
} else {
	$limitation1="WHERE `property`<3 AND `frontpage`=0";
	$limitation2="WHERE `property`<3 AND `frontpage`=0 AND ";
}
switch ($job) {
	case 'default':
		$counter_now=($permission['SeeHiddenEntry']==1) ? ($blog->countbyquery("SELECT COUNT(blogid) FROM `{$db_prefix}blogs` WHERE `property`<'3'")) : ($blog->countbyquery("SELECT COUNT(blogid) FROM `{$db_prefix}blogs` WHERE `property`<'2'"));
		$statistics['entries']=$counter_now; //Modify the entry counter to a correct value
		$urlpattern=getlink_index ('%s', 1);
		if ($flset['modeselectable']!=1) $pagebaritem=" [ {$lnc[181]} <a href=\"".sprintf($urlpattern, 1)."\" title=\"{$lnc[182]}\">{$lnc[183]}</a> | <a href=\"".sprintf($urlpattern, 2)."\" title=\"{$lnc[184]}\">{$lnc[185]}</a> ]";
		if ($mbcon['main_list']==1) {
			$partialquery="{$limitation1} ORDER BY `sticky`DESC, `pubtime` DESC";
			$perpagevalue=$mbcon['listitemperpage'];
			$actionforexc='list';
			$urlref=getlink_index ('2', '%s');
		} else {
			$partialquery="{$limitation1} ORDER BY `sticky`DESC, `pubtime` DESC";
			$perpagevalue=$mbcon['exceptperpage'];
			$actionforexc='excerpt';
			$urlref=getlink_index ('1', '%s');
		}
		if ($page==1) {
			announcebar();
		}
		break;
	case 'starred':
		if ($flset['star']==1) getHttp404($lnc[313]);
		$counter_now=($permission['SeeHiddenEntry']==1) ? ($blog->countbyquery("SELECT COUNT(blogid) FROM `{$db_prefix}blogs` WHERE `starred`%2 = 1 AND `property`<'3'")) : ($blog->countbyquery("SELECT COUNT(blogid) FROM `{$db_prefix}blogs` WHERE `starred`%2 = 1 AND `property`<'2'"));
		$urlpattern=getlink_star ('%s', 1);
		if ($flset['modeselectable']!=1) $pagebaritem=" [ {$lnc[181]} <a href=\"".sprintf($urlpattern, 1)."\" title=\"{$lnc[182]}\">{$lnc[183]}</a> | <a href=\"".sprintf($urlpattern, 2)."\" title=\"{$lnc[184]}\">{$lnc[185]}</a> ]";
		$pagetitle="{$lnc[93]} - ";
		if ($mbcon['starred_list']==1) {
			$partialquery="{$limitation1} AND `starred`%2 = 1 ORDER BY `sticky`DESC, `pubtime` DESC";
			$perpagevalue=$mbcon['listitemperpage'];
			$actionforexc='list';
			$urlref=getlink_star ('2', '%s');
		} else {
			$partialquery="{$limitation1} AND `starred`%2 = 1 ORDER BY `sticky`DESC, `pubtime` DESC";
			$perpagevalue=$mbcon['exceptperpage'];
			$actionforexc='excerpt';
			$urlref=getlink_star ('1', '%s');
		}
		break;
	case 'category':
		if ($permission['SeeHiddenEntry']!=1) {
			$limitation1="WHERE `property`<2 ";
			$limitation2="WHERE `property`<2 AND ";
		} else {
			$limitation1="WHERE `property`<3 ";
			$limitation2="WHERE `property`<3 AND ";
		}
		if (is_numeric($itemid)) $itemid=floor($itemid);
		elseif (isset($categorynames[$itemid])) $itemid=floor($categorynames[$itemid]);
		else {
			getHttp404($lnc[186]);
		}
		if (is_array($categories[$itemid]['subcates'])) {
			$categories[$itemid]['subcates'][]=$itemid;
			$all_needed_cates=@implode(',', $categories[$itemid]['subcates']);
		} else {
			$all_needed_cates=$itemid;
		}
		$counter_now=$blog->countbyquery("SELECT COUNT(blogid) FROM `{$db_prefix}blogs` {$limitation2}  `category` in ({$all_needed_cates})");
		$urlpattern=getlink_category($itemid, '%s', '1');
		if ($flset['modeselectable']!=1) $pagebaritem=" [ {$lnc[181]} <a href=\"".sprintf($urlpattern, 1)."\" title=\"{$lnc[182]}\">{$lnc[183]}</a> | <a href=\"".sprintf($urlpattern, 2)."\" title=\"{$lnc[184]}\">{$lnc[185]}</a> ]";
		$pagetitle="{$categories[$itemid]['catename']} - ";
		if ($mbcon['cate_list']==1) {
			$partialquery="{$limitation2} `category` in ({$all_needed_cates}) ORDER BY `sticky`DESC, `pubtime` DESC";
			$perpagevalue=$mbcon['listitemperpage'];
			$actionforexc='list';
			$urlref=getlink_category($itemid, '2', '%s');
		} else {
			$partialquery="{$limitation2} `category` in ({$all_needed_cates}) ORDER BY `sticky`DESC, `pubtime` DESC";
			$perpagevalue=$mbcon['exceptperpage'];
			$actionforexc='excerpt';
			$urlref=getlink_category($itemid, '1', '%s');
		}
		if ($page==1) {
			announcebar();
			$topannounce=$categories[$itemid]['catedesc'];
		}
		break;
	case 'archive':
		acceptrequest('cm,cy');
		$cm=floor(abs($cm));
		$cy=floor(abs($cy));

		if ($cm>12 || $cy<1990 || $cy>2099) {
			getHttp404($lnc[313]);
		}
		$month=$cm;
		$year=$cy;

		$cyearmonth=($cm<10) ? "{$year}0{$month}" : "{$year}{$month}";
		$all_datas=$blog->getarraybyquery("SELECT `cid` FROM `{$db_prefix}calendar` WHERE `cyearmonth`='{$cyearmonth}'");
		$counter_now=count($all_datas['cid']);
		$jointstr=@implode(',', $all_datas['cid']);
		if ($jointstr=='') $jointstr='null';
		$partialquery="{$limitation2} blogid in($jointstr) ORDER BY `pubtime` DESC";

		$urlpattern=getlink_archive($month, $year, '%s', '1');
		if ($flset['modeselectable']!=1) $pagebaritem=" [ {$lnc[181]} <a href=\"".sprintf($urlpattern, 1)."\" title=\"{$lnc[182]}\">{$lnc[183]}</a> | <a href=\"".sprintf($urlpattern, 2)."\" title=\"{$lnc[184]}\">{$lnc[185]}</a> ]";

		$timeperiod_start=gmmktime(0, 0, 0, $month, 1, $year);
		$archiveformat=($mbcon['archiveformat']=='custom') ? $mbcon['customarchiveformat'] : $mbcon['archiveformat'];
		$pagetitle=zhgmdate($archiveformat, $timeperiod_start)." {$lnc[106]} - ";

		if ($mbcon['archive_list']==1) {
			$perpagevalue=$mbcon['listitemperpage'];
			$actionforexc='list';
			$urlref=getlink_archive($month, $year, '2', '%s');
		} else {
			$perpagevalue=$mbcon['exceptperpage'];
			$actionforexc='excerpt';
			$urlref=getlink_archive($month, $year, '1', '%s');
		}
		break;
	case 'showday':
		if (!$itemid) @header ("Location: index.php");
		else (@list($year, $month, $day)=@explode('-', $itemid));
		$year=intval($year);
		$month=intval($month);
		$day=intval($day);
		$timeperiod_start=gmmktime(0, 0, 0, $month, $day, $year)-$config['timezone']*3600;
		$timeperiod_end=gmmktime(0, 0, 0, $month, $day+1, $year)-$config['timezone']*3600;
		$counter_now=$blog->countbyquery("SELECT COUNT(blogid) FROM `{$db_prefix}blogs` {$limitation2}  `pubtime`>'{$timeperiod_start}' AND `pubtime`<'{$timeperiod_end}' ORDER BY `pubtime` DESC");
		$urlpattern=getlink_date($year, $month, $day, '%s', '1');
		if ($flset['modeselectable']!=1) $pagebaritem=" [ {$lnc[181]} <a href=\"".sprintf($urlpattern, 1)."\" title=\"{$lnc[182]}\">{$lnc[183]}</a> | <a href=\"".sprintf($urlpattern, 2)."\" title=\"{$lnc[184]}\">{$lnc[185]}</a> ]";
		$pagetitle="{$year}/{$month}/{$day} {$lnc[106]} - ";
		$partialquery="{$limitation2} `pubtime`>'{$timeperiod_start}' AND `pubtime`<'{$timeperiod_end}' ORDER BY `pubtime` DESC";
		if ($mbcon['showday_list']==1) {
			$perpagevalue=$mbcon['listitemperpage'];
			$actionforexc='list';
			$urlref=getlink_date($year, $month, $day, '2', '%s');
		} else {
			$perpagevalue=$mbcon['exceptperpage'];
			$actionforexc='excerpt';
			$urlref=getlink_date($year, $month, $day, '1', '%s');
		}
		break;
	default:
		@header ("Location: index.php");
		break;
}

$records=$m_b->new_record_array($partialquery, $perpagevalue, $page);
$pagebar=$m_b->make_pagebar($page, $mbcon['pagebaritems'], $urlref, $counter_now, $perpagevalue, $pageway);
if ($pagebar) $pagebar.=$pagebaritem;
if (!empty($m_b->total_rows)) {
	if ($actionforexc=='excerpt') $section_body_main=$m_b->make_excerption($records);
	else {
		$listbody=$m_b->make_excerption($records, 'list');
		$section_body_main[]=$m_b->make_list(@implode('', $listbody));
	}
}
else $section_body_main[]="<br/><div align='center'><span style='font-size: 14px;'>{$lnc[186]}</span></div><br/>";

$iftoppage=($mbcon['pagebarposition']=='down') ? 'none' : 'block';
$ifbottompage=($mbcon['pagebarposition']=='up') ? 'none' : 'block';

$bodymenu=$t->set('mainpage', array('pagebar'=>$pagebar, 'iftoppage'=>$iftoppage, 'ifbottompage'=>$ifbottompage, 'ifannouncement'=>$ifannouncement, 'topannounce'=>$topannounce, 'mainpart'=>@implode('', $section_body_main), 'currentpage'=>$pageitems['currentpage'], 'previouspageurl'=>$pageitems['previouspageurl'], 'nextpageurl'=>$pageitems['nextpageurl'], 'turningpages'=>$pageitems['turningpages'], 'totalpages'=>$pageitems['totalpages'], 'previouspageexists'=>$pageitems['previouspageexists'], 'nextpageexists'=>$pageitems['nextpageexists']));