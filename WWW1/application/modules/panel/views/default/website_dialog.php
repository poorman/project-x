<div id="dialog_example">
<p>
	<i>Module :<b>Panel</b></i><br />
	<i>Page : <b>Dialog</b></i><br />
	<i>Device : <b>Default</b></i><br />
	<i>File : <b>modules/panel/views/<?php echo $template['name'];?>/website_dialog.php</b></i><br />
	<i>Template : <b><?php echo $template['name'];?></b></i>
</p>
<hr />
<span class="annotation">Output :</span>
<div id="code">
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => 'ui.discard("dialog");'<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'dialog' => $this->load->module_view('website_dialog',$data,true),<br />
	&nbsp;&nbsp;)
</div>
<span class="annotation">Result:</span>
<div id="result">
	<ul>
		<li>Discards previous element "dialog"</li>
		<li>Opens facebox</li>
		<li>Updates element "dialog" within facebox</li>
	</ul>
</div>
<span class="annotation">Notes:</span>
<div id="notes">
	<ul>
		<li>function "module_view" has built in device and template path</li>
	</ul>
</div>
</div>