<?PHP
if (!defined('VALIDADMIN')) die ('Access Denied.');
$editorjs=<<<eot
<script type="text/javascript" src="editor/quicktags/js_quicktags.js"></script>
eot;
$onloadjs="";
$editorbody=<<<eot
<script type="text/javascript">edToolbar();</script>
<textarea name='content' id='content' rows='20' cols='100' class='formtextarea'>{content}</textarea>
<script type="text/javascript">var edCanvas = document.getElementById('content');</script>
<br>
{$lna[745]}
<br>
</div>
eot;
