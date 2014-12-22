<p>
<i>Global Plugin Controller : <b>ASSETS</b></i><br />
<i>Device : <b>Default</b></i>
</p>
<hr />
<span class="annotation">Output :</span>
<div id="code">
	$this->load->plugin_model($this,'exampleplugin_model');<br />
	$data = array('model' => $this->exampleplugin_model->example());<br />
	$script = 'ui.discard("ajax");'$this->load->plugin_view($this,'js_plugin_assets',$data,TRUE);<br />
	$view = $this->load->plugin_view($this,'plugin_assets',$data,TRUE);<br />
</div>
<span class="annotation">Output :</span>
<div id="code">
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => ui.discard("ajax").$script;<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'ajax' => $view<br />
	&nbsp;&nbsp;);<br />
</div>
<span class="annotation">Result:</span>
<div id="result">
	<ul>
		<li>Loads plugin model</li>
		<li>Utilizes model function to output</li>
		<li>Tests global plugin JavaScript</li>
		<li>Discards previous element "ajax"</li>
		<li>Updates element "ajax"</li>
	</ul>
</div>
<span class="annotation">Notes:</span>
<div id="notes">
	<ul>
		<li>To load global plugin model across any module<br />$this->load->plugin_model($this,'<b>exampleplugin_model</b>');</li>
		<li>To use loaded model functions<br />$this->exampleplugin_model-><b>example()</b></li>
		<li>function "plugin_view" has built in device and template path</li>
	</ul>
</div>
<span class="annotation">Model:</span>
<?php echo $model;?>