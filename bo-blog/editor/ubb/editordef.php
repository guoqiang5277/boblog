<?PHP
if (!defined('VALIDADMIN')) die ('Access Denied.');
$plugin_ubbeditor_buttons=$plugin_ubbeditor_functions='';
plugin_runphp('ubbeditor');

$srcHTML="data/cache_emsel.php";

@include ($srcHTML);
$emots=str_replace("<br/>", ' ', $emots);
$emots=str_replace("</a>", '</a> ', $emots);


if ($act=='edit') {
	$editoreditmodeonly=<<<eot
<a href="JavaScript: void(0); "><IMG border=0 onclick="AddText('[separator]')" title="{$lna[701]}" src="editor/ubb/images/separator.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick="AddText('[newpage]')" title="{$lna[702]}" src="editor/ubb/images/newpage.gif" ></a>
eot;
	$editoreditmodeonly2=<<<eot
<br><span id="timemsg">{$lna[1179]}</span>&nbsp; &nbsp;<span id="timemsg2"></span>
 <script type='text/javascript' src='editor/ubb/autosaver.js'>
</script>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[<a href="javascript: stopautosaver();">{$lna[1176]}</a>] | [<a href="javascript: restartautosaver();">{$lna[1175]}</a>] | [<a href="javascript: stopforever();">{$lna[1177]}</a>] | [<a href="javascript: switchtodraft();">{$lna[1173]}</a>] | [<a href="javascript: savedraft();">{$lna[1178]}</a>] | [<a href="javascript: cleardraft();">{$lna[1180]}</a>]
eot;
}

$editorjs=<<<eot
<script type="text/javascript" src="editor/ubb/ubbeditor.js"></script>
<script type="text/javascript">
function insertemot (emotcode) {
	var emot="[emot]"+emotcode+"[/emot]";
	AddText(emot);
	document.getElementById('emotid').style.display='none';
}

function showemot () {
	if (document.getElementById('emotid').style.display=='block') document.getElementById('emotid').style.display='none';
	else document.getElementById('emotid').style.display='block';
}
$plugin_ubbeditor_functions
</script>
eot;
$onloadjs=" onload=\"init_ubb('content');\"";

$editorbody=<<<eot
<a href="JavaScript: void(0); "><IMG border=0 onclick=bold() title="{$lna[681]}" src="editor/ubb/images/bold.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick=italicize() title="{$lna[682]}" src="editor/ubb/images/italic.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick=underline() title="{$lna[683]}" src="editor/ubb/images/underline.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick=strike() title="{$lna[684]}" src="editor/ubb/images/strikethrough.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick=subsup('sup') title="{$lna[685]}" src="editor/ubb/images/superscript.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick=subsup('sub') title="{$lna[686]}" src="editor/ubb/images/subscript.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick=center() title="{$lna[687]}" src="editor/ubb/images/center.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick=hyperlink() title="{$lna[688]}" src="editor/ubb/images/url.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick=email() title="{$lna[689]}" src="editor/ubb/images/email.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick=image() title="{$lna[690]}" src="editor/ubb/images/insertimage.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick="addmedia('swf');" title="{$lna[691]}" src="editor/ubb/images/swf.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick="addmedia('wmp');" title="{$lna[692]}" src="editor/ubb/images/wmp.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick="addmedia('real');" title="{$lna[693]}" src="editor/ubb/images/real.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick="addmedia('flv');" title="{$lna[1014]}" src="editor/ubb/images/flv.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick=showcode() title="{$lna[694]}" src="editor/ubb/images/code.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick=quoteme() title="{$lna[695]}" src="editor/ubb/images/quote.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick="addacronym();" title="{$lna[696]}" src="editor/ubb/images/acronym.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick="AddText('[hr]')" title="{$lna[697]}" src="editor/ubb/images/line.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick="addfile();" title="{$lna[698]}" src="editor/ubb/images/file.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick="addsfile();" title="{$lna[699]}" src="editor/ubb/images/sfile.gif" ></a>
<a href="JavaScript: void(0); "><IMG border=0 onclick="showemot()" title="{$lna[700]}" src="editor/ubb/images/insertsmile.gif" ></a>
$editoreditmodeonly
<script type="text/javascript">
if (is_firefox) {
	document.write("<a href='JavaScript: void(0); '><IMG border=0 onclick='undo_fx();' title='{$lna[703]}' src='editor/ubb/images/undo.gif' ></a>");
}

</script>{$plugin_ubbeditor_buttons}
<br>
<div id='emotid' style="display: none;">{$emots}</div>
 {$lna[712]} 
<select onChange="showfont(this.options[this.selectedIndex].value);this.options[0].selected='selected';" name=font>
<option value="#define#" selected>{$lna[704]}</option>
<option value="{$lna[705]}">{$lna[705]}</option>
<option value="{$lna[706]}">{$lna[706]}</option>
<option value="{$lna[707]}">{$lna[707]}</option>
<option value="{$lna[708]}">{$lna[708]}</option>
<option value="{$lna[709]}">{$lna[709]}</option>
<option value="{$lna[710]}">{$lna[710]}</option>
<option value=Arial>Arial</option>
<option value=Tahoma>Tahoma</option>
<option value=Verdana>Verdana</option>
<option value="Times New Roman">Times New Roman</option>
<option value="Bookman Old Style">Bookman Old Style</option>
<option value="Century Gothic">Century Gothic</option>
<option value="Comic Sans MS">Comic Sans MS</option>
<option value="Courier New">Courier New</option>
<option value="Wingdings">Wingdings</option>
<option value="#define#">{$lna[711]}</option>

</select>
&nbsp;&nbsp;{$lna[713]}
<select onChange="showsize(this.options[this.selectedIndex].value);this.options[0].selected='selected';" name=size>
<option value="#define#" selected>{$lna[714]}</option>
<option value=1>1</option>
<option value=2>2</option>
<option value=3>3</option>
<option value=4>4</option>
<option value=5>5</option>
<option value=6>6</option>
</select>
&nbsp;&nbsp;{$lna[715]} 
<select onChange="showcolor(this.options[this.selectedIndex].value);this.options[0].selected='selected';" name=color>
<option value="#define#" selected>{$lna[716]}</option>
<option value="#87CEEB" style="color:#87CEEB">{$lna[717]}</option>
<option value="#4169E1" style="color:#4169E1">{$lna[718]}</option>
<option value="#0000FF" style="color:#0000FF">{$lna[719]}</option>
<option value="#00008B" style="color:#00008B">{$lna[720]}</option>
<option value="#FFA500" style="color:#FFA500">{$lna[721]}</option>
<option value="#FF4500" style="color:#FF4500">{$lna[722]}</option>
<option value="#DC143C" style="color:#DC143C">{$lna[723]}</option>
<option value="#FF0000" style="color:#FF0000">{$lna[724]}</option>
<option value="#B22222" style="color:#B22222">{$lna[725]}</option>
<option value="#8B0000" style="color:#8B0000">{$lna[726]}</option>
<option value="#008000" style="color:#008000">{$lna[727]}</option>
<option value="#32CD32" style="color:#32CD32">{$lna[728]}</option>
<option value="#2E8B57" style="color:#2E8B57">{$lna[729]}</option>
<option value="#FF1493" style="color:#FF1493">{$lna[730]}</option>
<option value="#FF6347" style="color:#FF6347">{$lna[731]}</option>
<option value="#FF7F50" style="color:#FF7F50">{$lna[732]}</option>
<option value="#800080" style="color:#800080">{$lna[733]}</option>
<option value="#4B0082" style="color:#4B0082">{$lna[734]}</option>
<option value="#DEB887" style="color:#DEB887">{$lna[735]}</option>
<option value="#F4A460" style="color:#F4A460">{$lna[736]}</option>
<option value="#A0522D" style="color:#A0522D">{$lna[737]}</option>
<option value="#D2691E" style="color:#D2691E">{$lna[738]}</option>
<option value="#008080" style="color:#008080">{$lna[739]}</option>
<option value="#C0C0C0" style="color:#C0C0C0">{$lna[740]}</option>
<option value="#define#">{$lna[711]}</option>
</select>
[<a href="javascript: showhidediv('FrameUpload');" title="{$lna[741]}" class="thickbox">{$lna[741]}</a>]

<div id="FrameUpload" style="display: none;"><iframe width=90% frameborder=0 height=200 frameborder=0 src='admin.php?act=upload&useeditor={$useeditor}'></iframe></div>
<textarea name='content' id='content' rows='30' cols='140' class='formtextarea'>{content}</textarea>
$editoreditmodeonly2

<input type=hidden id='content_old' value=''>
<!-- <br><ul>
<script type="text/javascript">
if (is_firefox) {
	document.write("{$lna[742]}");
}
</script>
<li>{$lna[743]}</li>
<li>{$lna[744]}</li></ul> -->
eot;

$initialjs="<script type='text/javascript' src=\"editor/ubb/uploader.js\"></script>";

$autobr=1;
