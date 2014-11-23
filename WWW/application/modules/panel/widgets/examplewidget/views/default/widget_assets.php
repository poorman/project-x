<p>
	<i>Module :<b>Panel</b></i><br />
	<i>Widget : <b>Examplewidget</b></i><br />
	<i>Page : <b>Assets</b></i><br />
	<i>Device : <b>Default</b></i><br />
	<i>File : <b>modules/panel/widget/examplewidget/views/<?php echo $template['name'];?>/webpanel_assets.php</b></i><br />
	<i>Template : <b><?php echo $template['name'];?></b></i>
</p>
<hr />
<span class="annotation">Output :</span>
<div id="code">
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => 'ui.discard("dialog");'<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'ajax' => $this->load->widget_view('webpanel',$data,true),<br />
	&nbsp;&nbsp;)
</div>
<span class="annotation">Result:</span>
<div id="result">
	<ul>
		<li>Discards previous element "ajax"</li>
		<li>Updates element "ajax"</li>
	</ul>
</div>
<span class="annotation">Notes:</span>
<div id="notes">
	<ul>
		<li>To load global widget model across any module<br />$this->load->widget_model($this,'<b>examplewidget_model</b>');</li>
		<li>To use loaded model functions<br />$this->examplewidget_model-><b>example()</b></li>
		<li>function "widget_view" has built in device and template path</li>
	</ul>
</div>
<span class="annotation">Model:</span>
<?php echo $model;?>