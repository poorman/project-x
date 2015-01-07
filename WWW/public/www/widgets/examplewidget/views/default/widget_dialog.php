<div id="dialog_example">
<p>
	<i>Module :<b>Site</b></i><br />
	<i>Widget :<b>Examplewidget</b></i><br />
	<i>Page : <b>Dialog</b></i><br />
	<i>Device : <b>Default</b></i><br />
	<i>File : <b>modules/site/widgets/examplewigdget/views/<?php echo $template['name'];?>/widget_dialog.php</b></i><br />
	<i>Template : <b><?php echo $template['name'];?></b></i>
</p>
<hr />
<span class="annotation">Output :</span>
<div id="code">
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => 'ui.discard("dialog");'<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'dialog' => $this->load->widget_view('widget_dialog',$data,true),<br />
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
		<li>function "widget_view" has built in device and template path</li>
	</ul>
</div>
</div>