<p>
<i>Global Component Controller : <b>ASSETS</b></i><br />
<i>Device : <b>Mobile</b></i>
</p>
<hr />
<span class="annotation">Output :</span>
<div id="code">
	$this->load->component_model($this,'examplecomponent_model');<br />
	$data = array('model' => $this->examplecomponent_model->example());<br />
	$script = 'ui.discard("ajax");'$this->load->component_view($this,'js_component_assets',$data,TRUE);<br />
	$view = $this->load->component_view($this,'component_assets',$data,TRUE);<br />
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
		<li>Loads component model</li>
		<li>Utilizes model function to output</li>
		<li>Tests global component JavaScript</li>
		<li>Discards previous element "ajax"</li>
		<li>Updates element "ajax"</li>
	</ul>
</div>
<span class="annotation">Notes:</span>
<div id="notes">
	<ul>
		<li>To load global component model across any module<br />$this->load->component_model($this,'<b>examplecomponent_model</b>');</li>
		<li>To use loaded model functions<br />$this->examplecomponent_model-><b>example()</b></li>
		<li>function "component_view" has built in device and template path</li>
	</ul>
</div>
<span class="annotation">Model:</span>
<?php echo $model;?>