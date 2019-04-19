<?PHP
/* This file provides a few functions for developers 
Usage: include ("function.php"); */

//Include necessary files
require_once ("global.php");
include_once ("data/mod_config.php");
include_once ("data/weather.php");
include_once ("data/cache_emot.php");
include_once("data/cache_adminlist.php");
$blog=new getblogs;

/* Function: GetNewPosts 
This function returns the latest blog entries of a certain volume in array.
Usage:
array GetNewPosts (int number [, string columns [, int startid [, int category]]])
Explanations:
[number] specifies how many new posts should be returned.
[columns] specifies which column(s) should be in the returned array.
The [columns] is a string which list the names of certain columns, and you can separate every two columns with a comma.
[startid] specifies which post to start with, which can be useful when the function is called in multipages.
[category] specifies the category of the posts you want. Currently it can only be an INTEGER, i.e., only one category at a time.
Example:
$news=GetNewPosts (10 , "title, blogid"); 
This should return the latest 10 posts, and the array contains only two keys: blogid and title. 
Note:
This function does not return drafts. Hidden entrys will be filtered if the visitor has no right of viewing hidden posts.*/
function GetNewPosts ($number, $columns='', $startid=0, $category='') {
	global $blog, $db_prefix, $permission;
	if (!empty($columns)) {
		$columns=str_replace(' ', '', $columns);
		$all_columns=@explode(',', $columns);
		foreach ($all_columns as $each_column) {
			$need_columns[]='`'.$each_column.'`';
		}
		$all_needed=@implode(',', $need_columns);
	} else {
		$all_needed='*';
	}
	$permissionlimit=($permission['SeeHiddenEntry']!=1) ? 2 : 3;
	$categoryplus=($category==='') ? '' : "AND `category`={$category}";
	$return=$blog->getgroupbyquery("SELECT {$all_needed} FROM `{$db_prefix}blogs` WHERE `property`<{$permissionlimit} {$categoryplus} ORDER BY `pubtime`DESC LIMIT {$startid}, {$number}");
	return $return;
}

/* Function: GetPostContent
This function returns the content of a certain blog entry. If the blog is not found or the visitor has no right to see it, a (bool) false will be returned, otherwise an array containing every column of that post will be returned.
Usage:
array GetPostContent (int blogid [, int conversion ])
Explanations:
[blogid] specifies which post should be returned.
[conversion] determines how the UBB codes, HTML tags and Emots will be handled when returning the content.
0 - All UBB, HTML, Emots will be filtered (default)
1 - UBB will be converted, while others will be filtered
2 - UBB and Emots will be converted, while HTML tags will be filtered
3 - All will be converted or reserved
Note:
The setting here will override the original setting which the writer specifies when posting.
Example:
$content=GetPostContent (1 , 0); 
This should return the content of the blog with blogid=1, and all UBB, HTML, Emots will be filtered.  */
function GetPostContent ($blogid, $conversion=0) {
	global $blog, $db_prefix, $permission;
	$permissionlimit=($permission['SeeHiddenEntry']!=1) ? 2 : 3;
	$content=$blog->getbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE `blogid`='{$blogid}' AND `property`<{$permissionlimit}  LIMIT 0, 1");
	if (!$content) return false;
	$ubb=($conversion==0) ? 0 : 1;
	$emot=($conversion<=1) ? 0 : 1;
	$html=($conversion<=2) ? 0 : 1;
	$content['content']=$blog->getcontent($content['content'], $html, $ubb, $emot);
	return $content;
}

/* Function: GetReplies
This function returns replies of certain amount. If no replies are found according to the criterion, a (bool) false will be returned, otherwise an array containing every column of the qualified replies will be returned.
Usage:
array GetReplies (int number [, int range [, int startid]])
Explanations:
[number] specifies how many replies should be returned.
[range] determines which ones to be returned. It can be one of the following values:
any integer larger than -1 : This will return the replies of a certain blog entry. The integer itself will be used as the specified id of the post.
-1 (default) : This will return replies of all posts.
[startid] specifies which reply to start with, which can be useful when the function is called in multipages. (default: 0)
Example:
$content=GetReplies (5 , 30 , 5);
This should return the content of 5 replies of the post with a blogid of 30, starting from the fifth reply.  */
function GetReplies ($number, $range=-1, $startid=0) {
	global $blog, $db_prefix, $permission;
	$permissionlimit=($permission['SeeHiddenEntry']!=1) ? 2 : 3;
	if ($range!=-1) $rangeplus=" AND t1.blogid='{$range}' ";
	$content=$blog->getgroupbyquery("SELECT t1.*, t2.title, t2.blogalias FROM `{$db_prefix}replies` t1 INNER JOIN `{$db_prefix}blogs` t2 ON t2.blogid=t1.blogid WHERE t1.reproperty<=1 {$rangeplus} AND t2.property<{$permissionlimit} ORDER BY t1.reptime DESC LIMIT {$startid}, {$number}");
	if (!$content) return false;
	return $content;
}

/* Function: GetGuestbook
This function returns messages on your guestbook of certain amount. If no messages are found according to the criterion, a (bool) false will be returned, otherwise an array containing every column of the qualified messages will be returned.
Usage:
array GetGuestbook (int number [, int startid])
Explanations:
[number] specifies how many messages should be returned.
[startid] specifies which message to start with, which can be useful when the function is called in multipages. (default: 0)
Example:
$content=GetGuestbook (5 , 5);
This should return the content of 5 messages, starting from the fifth message.  */
function GetGuestbook ($number, $startid=0) {
	global $blog, $db_prefix, $permission;

	$content=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}messages` WHERE `reproperty`<=1 ORDER BY `reptime` DESC LIMIT {$startid}, {$number}");
	if (!$content) return false;
	return $content;
}