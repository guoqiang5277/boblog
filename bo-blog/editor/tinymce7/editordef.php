<?php
if (!defined('VALIDADMIN')) die ('Access Denied.');

switch ($langback) {
    case 'en' : $langeditor='en'; break;
    case 'zh-cn' : $langeditor='zh_CN'; break;
    case 'zh-tw' : $langeditor='zh_TW'; break;
    default : $langeditor='en';
}
$editorjs=<<<eot
<script language="javascript" type="text/javascript" src="editor/tinymce7/tinymce.min.js"></script>
<script language="javascript" type="text/javascript">

tinymce.init({
	selector: '#content',
	language: '{$langeditor}',
    min_Width: 620,
    min_height: 400,
    width: 805,
    height: 800,
	
	menubar: true,
	autosave_interval: '10s',//自动保存间隔
	autosave_ask_before_unload: true,//离开页面前询问是否保存
	image_advtab: true,//高级选项卡
	automatic_uploads: true,
	insertdatetime_formats: ["%Y年%m月%d日 %H点%M分%S秒", "%H点%M分%S秒", "%Y年%m月%d日", "%Y-%m-%d %H:%M:%S", "%Y-%m-%d", "%H:%M:%S"],
	plugins: ' lists,advlist anchor autolink autosave directionality fullscreen help preview save' +
            ' code charmap codesample emoticons link media insertdatetime table searchreplace' +
            ' image visualblocks visualchars wordcount uploader',
	toolbar: [
            'bold italic underline strikethrough  blockquote alignleft aligncenter alignright alignnone   ltr rtl visualblocks visualchars  fullscreen restoredraft save cancel',
            'bullist numlist indent outdent undo redo link unlink anchor image code codesample emoticons media',
            'styles fontfamily fontsize fontsizeinput  insertdatetime  wordcount',
            'forecolor backcolor hr removeformat table subscript superscript charmap searchreplace preview uploader'
        ],
    promotion:false,//是否显示右上角付费的提示,去掉右上角的upgrade按钮
    branding:false,//是否显示右下角的官方链接
    skin: 'bobo',
	codesample_languages: [
            //生成一个比较全的代码语言列表
            {text: 'HTML/XML', value: 'markup'},
            {text: 'JavaScript', value: 'javascript'},
            {text: 'CSS', value: 'css'},
            {text: 'PHP', value: 'php'},
            // {text: 'Ruby', value: 'ruby'},
            {text: 'Python', value: 'python'},
            {text: 'Java', value: 'java'},
            {text: 'C', value: 'c'},
            {text: 'C#', value: 'csharp'},
            {text: 'C++', value: 'cpp'},
            // {text: 'Dart', value: 'dart'},
            // {text: 'Go', value: 'go'},
            // {text: 'Kotlin', value: 'kotlin'},
            // {text: 'Objective-C', value: 'objectivec'},
            // {text: 'Perl', value: 'perl'},
            {text: 'SQL', value: 'sql'},
            {text: 'Swift', value: 'swift'},
            // {text: 'TypeScript', value: 'typescript'},
            // {text: 'VBScript', value: 'vbscript'},
            // {text: 'VB.NET', value: 'vbnet'},
            // {text: 'Vue', value: 'vue'},
            {text: 'XML', value: 'xml'},
            // {text: 'YAML', value: 'yaml'},
            // {text: 'Less', value: 'less'},
            // {text: 'Sass', value: 'sass'},
            // {text: 'SCSS', value: 'scss'},
            // {text: 'Stylus', value: 'stylus'},
            {text: 'Markdown', value: 'markdown'},
            {text: 'Plain text', value: 'text'},
        ],
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