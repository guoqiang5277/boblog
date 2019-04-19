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

if ($flset['tags']==1) getHttp404($lnc[313]);

if (!$job) $job='default';
else $job=basename($job);
$itemid=safe_convert($itemid);

acceptrequest('tag');
if ($tag!=='') $job='show';
$tag=addslashes(urldecode($tag));
if ($job=='default') {
	$sequence=($mbcon['tagorder']=='1') ? 'tagcounter' : 'tagid';
	$tagperpage=floor($mbcon['tagperpage']);
	$start_id=($page-1)*$tagperpage;
	$alltags=$blog->getarraybyquery("SELECT tagid,tagname,tagcounter FROM `{$db_prefix}tags` ORDER BY {$sequence} DESC LIMIT {$start_id}, {$tagperpage}");
	$maxtagcounter=$blog->countbyquery("SELECT MAX(tagcounter) FROM `{$db_prefix}tags`");
	$alltagcounter=$blog->countbyquery("SELECT COUNT(tagcounter) FROM `{$db_prefix}tags`");
	for ($i=0; $i<count($alltags['tagid']); $i++) {
		$bit_tag_size=get_tag_size($alltags['tagcounter'][$i], $maxtagcounter);
		if ($mbcon['tagunderlinetospace']==1) $eachtag=str_replace('_', ' ', $alltags['tagname'][$i]);
		else $eachtag=$alltags['tagname'][$i];
		$urlref=getlink_tags(urlencode(urlencode($alltags['tagname'][$i])));
		if ($alltags['tagcounter'][$i]<0) $alltags['tagcounter'][$i]='?';
		$tag_show[]="<a href=\"{$urlref}\" title=\"{$lnc[188]}{$alltags['tagcounter'][$i]}\"><span style=\"font-size: {$bit_tag_size}px;\">{$eachtag}</span></a>";
	}
	if (is_array($tag_show)) {
		if ($mbcon['tagorder']=='0') shuffle($tag_show);
		$tagshow=@implode(" &nbsp; ", $tag_show);
	}
	else $tagshow="{$lnc[189]}";

	$m_b=new getblogs;
	$returnurl="tag.php?page=%s";
	$pagebar=$m_b->make_pagebar($page, $mbcon['pagebaritems'], $returnurl, $alltagcounter, $tagperpage, 1);

	$t=new template;
	$section_tag=$t->set('taglist', array('tagcategory'=>$lnc[190], 'tagcontent'=>$tagshow, 'tagextra'=>"<div align='right'>{$lnc[191]}</div>"));
	$section_body_main=$t->set('contentpage', array('title'=>'Tags', 'contentbody'=>$section_tag));
	announcebar();
	$iftoppage=($mbcon['pagebarposition']=='down') ? 'none' : 'block';
	$ifbottompage=($mbcon['pagebarposition']=='up') ? 'none' : 'block';

	$bodymenu=$t->set('mainpage', array('pagebar'=>$pagebar, 'iftoppage'=>$iftoppage, 'ifbottompage'=>$ifbottompage, 'ifannouncement'=>$ifannouncement, 'topannounce'=>$topannounce, 'mainpart'=>$section_body_main, 'currentpage'=>$pageitems['currentpage'], 'previouspageurl'=>$pageitems['previouspageurl'], 'nextpageurl'=>$pageitems['nextpageurl'], 'turningpages'=>$pageitems['turningpages'], 'totalpages'=>$pageitems['totalpages'], 'previouspageexists'=>$pageitems['previouspageexists'], 'nextpageexists'=>$pageitems['nextpageexists']));
 	$pagetitle="Tags - ";
}

if ($job=='show') {
	acceptrequest('mode');
	if ($mode==1 || $mode==2) $mbcon['tag_list']=$mode-1;
	else $mode=$mbcon['tag_list']+1;

	$m_b=new getblogs;
	if ($tag==='') catcherror($lnc[192]);
	$tag=str_replace('&#039;', "\\'", $tag);

	$allentries=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}tags` WHERE `tagname`='{$tag}' LIMIT 0,1");
	if (!is_array($allentries[0]) || $allentries[0]['tagentry']=='<end>' || $allentries[0]['tagcounter']==0) {
		getHttp404($lnc[186]);
	} else {
		$taginfo=$allentries[0];
		$entries_query=str_replace(',<end>', '', $taginfo['tagentry']);
		$entries_query=str_replace('<tag>,', '', $entries_query);
		$partialquery="WHERE `blogid` IN ({$entries_query}) AND `property`<'2' ORDER BY  `sticky` DESC, `pubtime` DESC";
		if ($mbcon['tag_list']==1) {
			$records=$m_b->new_record_array($partialquery, $mbcon['listitemperpage'], $page);
			$listbody=$m_b->make_excerption($records, 'list');
			$section_body_main[]=$m_b->make_list(@implode('', $listbody));
			$perpagevalue=$mbcon['listitemperpage'];
		} else {
			$records=$m_b->new_record_array($partialquery, $mbcon['exceptperpage'], $page);
			$section_body_main=$m_b->make_excerption($records);
			$perpagevalue=$mbcon['exceptperpage'];
		}
		$counter_now=$blog->countbyquery("SELECT COUNT(blogid) FROM `{$db_prefix}blogs` WHERE `blogid` IN ({$entries_query}) AND `property`<'2'");
		$urlref=getlink_tags(str_replace('%', '%%', urlencode(urlencode($tag))), $mode, '%s');
		$pagebar=$m_b->make_pagebar($page, $mbcon['pagebaritems'], $urlref, $counter_now, $perpagevalue, '1');
		if ($flset['modeselectable']!=1) $pagebar.=" [ {$lnc[181]} <a href=\"".getlink_tags(urlencode(urlencode($tag)), '1')."\" title=\"{$lnc[182]}\">{$lnc[183]}</a> | <a href=\"".getlink_tags(urlencode(urlencode($tag)), '2')."\" title=\"{$lnc[184]}\">{$lnc[185]}</a> ]";
	}
	$iftoppage=($mbcon['pagebarposition']=='down') ? 'none' : 'block';
	$ifbottompage=($mbcon['pagebarposition']=='up') ? 'none' : 'block';

	if ($mbcon['tagunderlinetospace']==1) 	$allentries[0]['tagname']=str_replace('_', ' ', $allentries[0]['tagname']);
	if ($mbcon['tag_list']==1) $mainbody=$t->set('contentpage', array('title'=>"Tags：{$allentries[0]['tagname']}",  'contentbody'=>@implode('', $section_body_main)));
	else $mainbody=@implode('', $section_body_main);
 	$pagetitle="Tags：{$allentries[0]['tagname']} - ";
	announcebar();
	$bodymenu=$t->set('mainpage', array('pagebar'=>$pagebar, 'iftoppage'=>$iftoppage, 'ifbottompage'=>$ifbottompage, 'ifannouncement'=>$ifannouncement, 'topannounce'=>$topannounce, 'mainpart'=>$mainbody, 'currentpage'=>$pageitems['currentpage'], 'previouspageurl'=>$pageitems['previouspageurl'], 'nextpageurl'=>$pageitems['nextpageurl'], 'turningpages'=>$pageitems['turningpages'], 'totalpages'=>$pageitems['totalpages'], 'previouspageexists'=>'', 'nextpageexists'=>''));
}
