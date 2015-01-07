<p>
	<i>Module :<b>Site</b></i><br />
    <i>Widget :<b>Examplewidget</b></i><br />
	<i>Page : <b>Ajaxed Element</b></i><br />
	<i>Device : <b>Mobile</b></i><br />
	<i>File : <b>modules/site/widgets/examplewidget/views/<?php echo $template['name'];?>/widget_mobi.php</b></i><br />
	<i>Template : <b><?php echo $template['name'];?></b></i>
</p>
<hr />
<span class="annotation">Output :</span>
<div id="code">
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => 'ui.discard("ajax");'<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'ajax' => $this->load->widget_view('widget_mobi',$data,true),<br />
	&nbsp;&nbsp;)
</div>
<span class="annotation">Result:</span>
<div id="result">
	<ul>
		<li>Discards previous element "ajax"</li>
		<li>Updates element "ajax" within facebox</li>
	</ul>
</div>
<span class="annotation">Notes:</span>
<div id="notes">
	<ul>
		<li>function "widget_view" has built in device and template path</li>
	</ul>
</div>