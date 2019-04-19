<?php
if (!defined('VALIDREQUEST')) die ('Access Denied.');

$elements['viewpage']=<<<eot
<div class="textbox">
	<div class="textbox-title">
		<h4>
		{entrytitle}
		</h4>
	</div>
	<div class="textbox-content" id="zoomtext">
		{entrycontent}
	</div>
</div>
eot;


?>