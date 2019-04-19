<?PHP
if (!defined('VALIDADMIN')) die ('Access Denied.');

/*$srcHTML="data/cache_emsel.php";

@include ($srcHTML);
$emots=str_replace("<br/>", ' ', $emots);
$emots=str_replace("</a>", '</a> ', $emots);*/

switch ($langback) {
	case 'en' : $langeditor='en'; break;
	case 'zh-cn' : $langeditor='zh_cn_utf8'; break;
	case 'zh-tw' : $langeditor='zh_tw_utf8'; break;
	default : $langeditor='en';
}
$editorjs=<<<eot
<script language="javascript" type="text/javascript" src="editor/tinymce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	theme : "advanced",
	mode : "textareas",
	language : "{$langeditor}",
	editor_selector : "mceEditor",
	height : "420",
	plugins : "emotions,insertdatetime,preview,searchreplace,table,multimedia,quoteubb,addmore,uploader,filedownload",
	force_br_newlines : true,
	force_p_newlines : false,
	convert_fonts_to_spans : true,
	theme_advanced_toolbar_location : "top",
	theme_advanced_statusbar_location : "none",
	theme_advanced_toolbar_align : "left",
	auto_focus : "mce_editor_0",
	theme_advanced_disable : "formatselect,styleselect,help",
	theme_advanced_buttons1_add : "forecolor,backcolor,fontselect,fontsizeselect",
	theme_advanced_buttons2_add : "separator,table,quoteubb,emotions,multimedia,insertdate,inserttime,filedownload,insertseparator,insertnewpage",
	theme_advanced_buttons3_add : "search,replace,preview,uploader"
});
</script>
eot;
$editorbody=<<<eot
<textarea name='content' id='content' class='mceEditor' style="width:96%; height:300px; padding: 0px; ">{content}</textarea>
eot;
$onloadjs="";
$submitjs="";
$autobr=0;

$callaftersubmit='';
