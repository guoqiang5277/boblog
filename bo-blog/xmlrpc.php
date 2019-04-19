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

require_once ("data/config.php");
include_once ("data/mod_config.php");
require_once ("global.php");
require_once ("inc/db.php");
require_once ("inc/boblog_class_run.php");
define('VALIDADMIN', 1);
require_once ("admin/cache_func.php");

//$validuploadfiletype=array("");

$rawdata=get_http_raw_post_data();
//writetofile ("text_".rand(0,500).".php", $rawdata); //For debug use
//$rawdata=file_get_contents('text2.php'); //For debug use

$stringType_o="i4|int|boolean|struct|string|double|base64|dateTime\.iso8601";
$stringType="(".$stringType_o.")";

$rawdata=str_replace("\r", '', $rawdata);
$rawdata=str_replace("\n", '', $rawdata);
$rawdata=str_replace("\t", '', $rawdata);
$rawdata=str_replace("<![CDATA[", '', $rawdata);
$rawdata=str_replace("]]>", '', $rawdata); //Stupid CDATA, I don't want it
$rawdata=preg_replace("/<([^>]+?) \/>/is", '<\\1></\\1>', $rawdata); //Self-closed tags

$rawdata=preg_replace_callback("/<struct>(.+?)<\/struct>/is", 'filter_struct', $rawdata); //Struct can be a trouble, use this to avoid values and names being parsed
if (!$rawdata) die ($lnc[271]);


$nameType=array (
	'blogger.newPost' => array ('appkey', 'blogid', 'username', 'password', 'content', 'publish'),
	'blogger.editPost' => array ('appkey', 'postid', 'username', 'password', 'content', 'publish'),
	'blogger.getUsersBlogs' => array ('appkey', 'username', 'password'),
	'blogger.getUserInfo' => array ('appkey', 'username', 'password'),
	'blogger.getTemplate' => array ('appkey', 'blogid', 'username', 'password', 'templateType'),
	'blogger.setTemplate' => array ('appkey', 'blogid', 'username', 'password', 'template', 'templateType'),
 	'metaWeblog.newPost' => array ('blogid', 'username', 'password', 'struct', 'publish'),
 	'metaWeblog.editPost' => array ('postid', 'username', 'password', 'struct', 'publish'),
 	'metaWeblog.getPost' => array ('postid', 'username', 'password'),
 	'metaWeblog.newMediaObject' => array ('blogid', 'username', 'password', 'struct'),
 	'metaWeblog.getCategories' => array ('blogid', 'username', 'password'),
 	'metaWeblog.getRecentPosts' => array ('blogid', 'username', 'password', 'numberOfPosts')
);
$methodFamily=array('blogger.newPost', 'blogger.editPost', 'blogger.getUsersBlogs', 'blogger.getUserInfo', 	'blogger.getTemplate', 	'blogger.setTemplate', 	'metaWeblog.newPost', 'metaWeblog.editPost', 'metaWeblog.getPost', 'metaWeblog.newMediaObject', 'metaWeblog.getCategories', 'metaWeblog.getRecentPosts'); 


function parse_get ($whole_line, $parser, $single=false) { //Parse specific value(s)
	$reg= "/<".$parser.">(.+?)<\/".$parser.">/is";
	preg_match_all ($reg, $whole_line, $array_matches);
	if ($single) return $array_matches[1][0];
	else return $array_matches[1];
}


function parse_walk_array ($array, $names) { //Turn all values into readable forms
	global $stringType, $nameType;
	if (!is_array($nameType[$names])) return;
	$reg= "/<".$stringType.">(.+?)<\/".$stringType.">/is";
	$i=0;
	foreach ($array as $whole_line) {
		$name=$nameType[$names][$i];
		if (is_array($whole_line)) $return[$name]=$whole_line;
		else {
			$try=preg_match($reg, $whole_line, $matches);
			if ($try=0) $return[$name]='';
			else {
				@list($whole, $type, $value)=$matches;
				if ($type!='struct') $return[$name]=$value;
				else $return[$name]=parse_struct($value);
			}
		}
		$i+=1;
		unset ($try, $name, $whole, $type, $value);
	}
	return $return;
}

function filter_struct ($matches) {
	global $stringType;
	$structcontent=$matches[0];
	$structcontent=preg_replace("/<".$stringType.">/is", "<struct-\\1>", $structcontent);
	$structcontent=preg_replace("/<\/".$stringType.">/is", "</struct-\\1>", $structcontent);
	$structcontent=str_replace("<value>", "<struct-value>", $structcontent);
	$structcontent=str_replace("</value>", "</struct-value>", $structcontent);
	$structcontent=str_replace("<struct-struct>", "<struct>", $structcontent);
	$structcontent=str_replace("</struct-struct>", "</struct>", $structcontent);
	//$structcontent=preg_replace("/<struct>(.+?)<\/struct>/is", '', $structcontent);
	//die ($structcontent);
	return $structcontent;
}

function parse_struct ($struct) { //Now let's deal with struct
	global $stringType;
	$reg= "/<struct-".$stringType.">(.+?)<\/struct-".$stringType.">/is";
	$all_names=parse_get($struct, 'name');
	$all_values=parse_get($struct, 'struct-value');
	foreach ($all_values as $single_value) {
		$try=preg_match($reg, $single_value, $matches);
		@list($whole, $type, $value)=$matches;
		$result_values[]=$value; //I don't care any types
		unset ($whole, $type, $value);
	}
	$all_values=$result_values;
	for ($i=0; $i<count($all_names); $i++) {
		$key=$all_names[$i];
		$value=$all_values[$i];
		$result[$key]=$value;
	}
	return $result;
}

function xml_error ($error) { //Output an error
	$xml=<<<eot
<methodResponse>
  <fault>
    <value>
      <struct>
        <member>
          <name>faultCode</name>
          <value><int>500</int></value>
        </member>
        <member>
          <name>faultString</name>
          <value><string>{$error}</string></value>
        </member>
      </struct>
    </value>
  </fault>
</methodResponse> 
eot;
	send_response ($xml);
}
	
function xml_generate ($body_xml) { //Generate an XML cluster with certain format
	$xml=<<<eot
<methodResponse>
	<params>
		<param>
			<value>
				{$body_xml}
			</value>
		</param>
	</params>
</methodResponse>
eot;
	return $xml;
}

function make_xml_piece ($type, $values) { //Compose a piece of XML
	switch ($type) {
		case "array":
			$xml="
					<array>
						<data>";
			foreach ($values as $singlevalue) {
				$xml.="
							<value>
								{$singlevalue}
							</value>";
			}
			$xml.="
						</data>
					</array>";
			break;
		case "struct":
			$xml="
					<struct>";
			while (@list($key, $singlevalue)=@each($values)) {
				if ($key=='dateCreated') $stype="<dateTime.iso8601>{$singlevalue}</dateTime.iso8601>";
				elseif ($key=='categories') $stype=$singlevalue;
				else $stype="<string>{$singlevalue}</string>";
				$xml.="
						<member>
							<name>{$key}</name>
							<value>
							{$stype}
							</value>
						</member>";
			}
			$xml.="
					</struct>";
			break;
		default:
			$xml="<{$type}>{$values}</{$type}>";
		break;
	}
	return $xml;
}

function send_response ($xml) { //Send out the response
	$date_p=date('r', time());
	$xml="<?xml version=\"1.0\" ?>\n".$xml;
	$lens=strlen($xml);
	header("HTTP/1.1 200 OK");
	header("Connection: close");
	header("Content-Length: {$lens}");
	header("Content-Type: text/xml");
	header("Date: {$date_p}");
	header("Server: Bo-Blog V2");
	echo ($xml);
	exit();
}
$methodName=parse_get($rawdata, 'methodName', true);
if (!@in_array($methodName, $methodFamily)) xml_error ("Method ({$methodName}) is not availble.");
$values=parse_get($rawdata, 'value');

$values=parse_walk_array($values, $methodName);
//print_r($values); //For debug only
//exit();
//Get default category, for those editors which don't support Categories
$defualtcategoryid=$arrayvalue_categories[0];

$methodName=str_replace('.', '_', $methodName);
call_user_func ($methodName, $values);

function checkuser($username, $password) {
	global $db_prefix;
	$blog=new boblog;
	$password=md5($password);
	$username=mystrtolower($username);
	$userdetail=$blog->getbyquery("SELECT * FROM `{$db_prefix}user` WHERE LOWER(username)='{$username}' AND `userpsw`='{$password}'");
	if (!$userdetail) {
		return false;	
	}
	else {
		if (file_exists("data/usergroup{$userdetail['usergroup']}.php")) include ("data/usergroup{$userdetail['usergroup']}.php");
		else include("data/usergroup0.php");
		if ($permission['XMLRPC']!=1) return false; //Check 'Browse' permission
		else return $userdetail;
	}
}

function check_user ($username, $password) {
	$username=safe_convert(addslashes($username)); //2007-1-20 Security Fix
	$password=safe_convert(addslashes($password)); //2007-1-20 Security Fix
	$userdetail=checkuser($username, $password);
	if (!$userdetail) xml_error("Authentification failed by the conbination of provided username ({$username}) and password.");
	else return $userdetail;
}

/*function nochaoscode($encode, $str) {
	$str = iconv($encode, "UTF-16BE", $str);
		for ($i = 0; $i < strlen($str); $i++,$i++) {
			$code = ord($str{$i}) * 256 + ord($str{$i + 1});
				if ($code < 128) {
					$output .= chr($code);
				} else if ($code != 65279) {
			$output .= "&#".$code.";";
		}
	}
	return $output;
}*/

function reduce_entities($str) { //Convert the submitted content back to HTML
	$str=stripslashes($str);
	$str=html_entity_decode($str, ENT_QUOTES);
	$str=safe_convert($str, 1);
	return $str;
}



//functions of MetawebblogAPI
//We no longer provide the methods that resembles the same function as in bloggerAPI, eg metaWeblog.newPost is supported, but blogger.newPost is not
function blogger_getUsersBlogs ($values) {
	global $config;
	$userdetail=check_user ($values['username'], $values['password']);
	$value_body=array('url'=>$config['blogurl'], 'blogid'=>$values['appkey'], 'blogName'=>$config['blogname']);
	$array_body[0]=make_xml_piece ("struct", $value_body);
	$xml_content=make_xml_piece("array", $array_body);
	$body_xml=xml_generate($xml_content);
	send_response ($body_xml);
}

function metaWeblog_newPost ($values) {
	global $config, $defualtcategoryid, $db_prefix, $mbconfig;
	$struct=$values['struct'];
	$userdetail=check_user ($values['username'], $values['password']);
	if (!$struct['title']) $title="Untitled MetaWeblogAPI Entry";
	else $title=safe_convert($struct['title']);
	if (!$struct['description']) xml_error("You MUST provide a decription element in your post.");
	else $content=reduce_entities($struct['description']);
	if ($struct['pubDate']) $struct['dateCreated']=$struct['pubDate'];
	if ($struct['dateCreated']) $time=get_time_unix($struct['dateCreated']);
	else $time=time();

	$blog=new boblog;
	//writetofile ('text5.php', $struct['categories']); //For debug only
	if ($struct['categories']!='') {
		$c_tmp=$blog->getgroupbyquery("SELECT cateid FROM `{$db_prefix}categories` WHERE `catename`='{$struct['categories']}'");
		$category=$c_tmp[0]['cateid'];
		if ($category=='') $category=$defualtcategoryid;
	}
	else $category=$defualtcategoryid;
	$html=1;
	if ($struct['flNotOnHomePage']==1) $property=3;
	else $property=0;

	$maxrecord=$blog->getsinglevalue("{$db_prefix}maxrec");
	$currentid=$maxrecord['maxblogid']+1;
	$query="INSERT INTO `{$db_prefix}blogs` VALUES ('{$currentid}', '{$title}','{$time}','{$userdetail['userid']}', 0, 0, 0, '{$property}','{$category}','','0','{$html}', '1', '1', '{$content}', '0', '0', 'blank', '0', '', '', '0', '', '0', '', '', '', '')";
	$blog->query($query);
	$newcym=gmdate("Ym", $time+$config['timezone']*3600);
	$newcd=gmdate("d", $time+$config['timezone']*3600);
	$blog->query("INSERT INTO `{$db_prefix}calendar` VALUES ('{$newcym}', '{$newcd}', '{$currentid}', '')");
	recache_latestentries ();
	recache_currentmonthentries();
	recache_categories(); //Update Category counter
	$blog->query("UPDATE `{$db_prefix}maxrec` SET maxblogid={$currentid}");
	$blog->query("UPDATE `{$db_prefix}counter` SET entries=entries+1");
	plugin_runphp('metaweblogadd');
	$xml_content=make_xml_piece("string", $currentid);
	$body_xml=xml_generate($xml_content);
	send_response ($body_xml);
}

function metaWeblog_editPost ($values) {
	global $config, $defualtcategoryid, $db_prefix, $mbconfig;
	$struct=$values['struct'];
	$userdetail=check_user ($values['username'], $values['password']);

	$blog=new boblog;
	$values['postid']=floor($values['postid']);
	$records=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE `blogid`='{$values['postid']}'");
	if ($records[0]['blogid']=='') xml_error ("Entry does not exist.");

	if (!$struct['title']) $title="Untitled MetaWeblogAPI Entry";
	else $title=safe_convert($struct['title']);
	if (!$struct['description']) xml_error("You MUST provide a decription element in your post.");
	else $content=reduce_entities($struct['description']);
	$nowtime=time();
	if ($struct['pubDate']) $struct['dateCreated']=$struct['pubDate'];
	if ($struct['dateCreated']) $time=get_time_unix($struct['dateCreated'])+3600*$config['timezone'];
	else $time=$records[0]['pubtime'];
	$newcym=date("Ym", $time);
	$newcd=date("d", $time);
	$blog->query("UPDATE `{$db_prefix}calendar` SET cyearmonth='{$newcym}', cday='{$newcd}' WHERE `cid`='{$values['postid']}'");
	//writetofile ('text5.php', $struct['categories']); //For debug only

	if ($struct['categories']!='') {
		$c_tmp=$blog->getgroupbyquery("SELECT cateid FROM `{$db_prefix}categories` WHERE `catename`='{$struct['categories']}'");
		$category=$c_tmp[0]['cateid'];
		if ($category=='') $category=$defualtcategoryid;
	}
	else $category=$records[0]['category'];
	if ($values['publish']==1 && $struct['flNotOnHomePage']!=1) $property=0;
	else $property=3;

	$query="UPDATE `{$db_prefix}blogs` SET `title`='{$title}', `pubtime`='{$time}', `property`='{$property}', `category`='{$category}', `content`='{$content}', `editorid`='{$userdetail['userid']}', `edittime`='{$nowtime}' WHERE `blogid`='{$values['postid']}'";
	recache_latestentries ();
	recache_currentmonthentries();
	recache_categories(); //Update Category counter
	$blog->query($query);
	plugin_runphp('metaweblogedit');
	$xml_content=make_xml_piece("boolean", '1');
	$body_xml=xml_generate($xml_content);
	send_response ($body_xml);
}

function metaWeblog_getPost ($values) {
	global $config, $db_prefix;
	$userdetail=check_user ($values['username'], $values['password']);
	$blog=new boblog;
	$values['postid']=floor($values['postid']);
	$records=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}blogs` WHERE `blogid`='{$values['postid']}'");
	if ($records[0]['blogid']=='') xml_error ("Entry does not exist.");
	else {
		$record=$records[0];
		$time=get_time_unix($record['pubtime'], 'iso');
		$value_body=array('dateCreated'=>$time, 'userid'=>$userdetail['userid'], 'postid'=>$record['blogid'], 'description'=>htmlspecialchars($record['content']), 'title'=>htmlspecialchars($record['title']), 'link'=>"{$config['blogurl']}/read.php?{$record['blogid']}", 'categories'=>make_xml_piece('array', array("Category {$record['category']}")));
		$body=make_xml_piece ("struct", $value_body);
		$body_xml=xml_generate($body);
		send_response ($body_xml);
	}
}

function metaWeblog_getRecentPosts ($values) {
	global $config, $db_prefix;
	$userdetail=check_user ($values['username'], $values['password']);
	$blog=new boblog;
	$values['numberOfPosts']=floor($values['numberOfPosts']);
	$records=$blog->getgroupbyquery("SELECT * FROM `{$db_prefix}blogs` ORDER BY `pubtime` DESC LIMIT 0, {$values['numberOfPosts']}");
	if ($records[0]['blogid']=='') xml_error ("Entry does not exist.");
	else {
		for ($i=0; $i<count($records); $i++) {
			$record=$records[$i];
			$time=get_time_unix($record['pubtime'], 'iso');
			$value_body=array('dateCreated'=>$time, 'userid'=>$userdetail['userid'], 'postid'=>$record['blogid'], 'description'=>htmlspecialchars($record['content']), 'title'=>htmlspecialchars($record['title']), 'link'=>"{$config['blogurl']}/read.php?{$record['blogid']}", 'categories'=>make_xml_piece('array', array("Category {$record['category']}")));
			$value_bodys[]=make_xml_piece ("struct", $value_body);
		}
		$body=make_xml_piece ("array", $value_bodys);
		$body_xml=xml_generate($body);
		send_response ($body_xml);
	}
}

function metaWeblog_getCategories ($values) {
	global $config, $db_prefix;
	$userdetail=check_user ($values['username'], $values['password']);
	//Get Categories
	$result=db_query("SELECT * FROM `{$db_prefix}categories` ORDER BY `cateorder`");
	while ($row=db_fetch_array($result)) {
		$struct_body[]=make_xml_piece ("struct", array('description'=>"{$row['catename']}", 'htmlUrl'=>"{$config['blogurl']}/index.php?go=category_{$row['cateid']}", 'rssUrl'=>"{$config['blogurl']}/feed.php?go=category_{$row['cateid']}"));
	}
	$xml_content.=make_xml_piece ("array", $struct_body);
	$body_xml=xml_generate($xml_content);
	send_response ($body_xml);
}

function blogger_getUserInfo ($values) {
	global $config, $db_prefix;
	$userdetail=check_user ($values['username'], $values['password']);
	$xml_content=make_xml_piece ("struct", array('nickname'=>$values['username'], 'userid'=>$userdetail['userid'], 'url'=>$config['blogurl'], 'email'=>$userdetail['email']));
	$body_xml=xml_generate($xml_content);
	send_response ($body_xml);
}

function metaWeblog_newMediaObject ($values) { //2006-12-2 add support for uploading files
	global $config, $defualtcategoryid, $db_prefix, $mbcon, $nowtime;
	$userdetail=check_user ($values['username'], $values['password']);
	$struct=$values['struct'];
	//writetofile ('text1.php', $struct['bits']); //debug only
	if ($struct['bits'] && $struct['name']) {
		$writefilecontent=base64_decode($struct['bits']);
		$ext=strtolower(strrchr($struct['name'],'.'));
		$ext=str_replace(".", '', $ext);
		$upload_filename=time().'_'.rand(1000, 9999).substr(md5($struct['name']), 0, 4).'.'.$ext;

		if ($mbcon['uploadfolders']=='1') {
			$targetfolder_ym=date("Ym").'/';
			$targetfolder="attachment/{$targetfolder_ym}";
			if (!is_dir ($targetfolder)) {
				$mktargetfolder=@mkdir($targetfolder, 0777);
				if (!$mktargetfolder) xml_error ("Sorry, uploading file ({$struct['name']}) failed because PHP was unable to create a new directory.");
			}
		} else {
			$targetfolder_ym='';
			$targetfolder='attachment';
		}

		$filenum=@fopen("{$targetfolder}/{$upload_filename}","wb");
		if (!$filenum) {
			xml_error ("Sorry, uploading file ({$struct['name']}) failed.");
		}
		flock($filenum,LOCK_EX);
		fwrite($filenum,$writefilecontent);
		fclose($filenum);

		//DB updating, new function in 2.1.0
		$blog=new boblog;
		$blog->query("INSERT INTO `{$db_prefix}upload` (fid,filepath,originalname,uploadtime,uploaduser) VALUES (null, \"attachment/{$targetfolder_ym}{$upload_filename}\", \"{$struct['name']}\", {$nowtime['timestamp']}, {$userdetail['userid']})");
		$currentid=db_insert_id();

		if ($mbcon['wmenable']=='1') {	//Add watermark
			$imgext_watermark=array('jpg', 'gif', 'png');
			if (in_array($ext, $imgext_watermark)) {
				create_watermark("attachment/{$targetfolder_ym}{$upload_filename}");
			}
		}
	}
	$xml_content=make_xml_piece ("struct", array('url'=>"{$config['blogurl']}/attachment.php?fid={$currentid}"));
	$body_xml=xml_generate($xml_content);
	send_response ($body_xml);
}


//Give an error code for bloggerAPI aliases
function blogger_newPost ($values) {
	xml_error ("Sorry, this method is no longer supported. Please use metaWeblog.newPost instead.");
}


function blogger_editPost ($values) {
	xml_error ("Sorry, this method is no longer supported. Please use metaWeblog.editPost instead.");
}

//Give an error code for unsupported methods, like template
function blogger_getTemplate ($values) {
	xml_error ("Sorry, this method is not supported yet.");
}

function blogger_setTemplate ($values) {
	xml_error ("Sorry, this method is not supported yet.");
}

?>