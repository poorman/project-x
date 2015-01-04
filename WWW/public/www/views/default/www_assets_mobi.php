<p>
	<i>Module :<b>www</b></i><br />
	<i>Page : <b>Assets</b></i><br />
	<i>Device : <b>Mobile</b></i><br />
	<i>File : <b>public/www/views/<?php echo $template['name'];?>/www_assets_mobi.php</b></i><br />
	<i>Template : <b><?php echo $template['name'];?></b></i>
</p>
<hr />
<span class="annotation">Output :</span>
<div id="code">
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => 'ui.discard("dialog");'<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'ajax' => $this->load->module_view('website',$data,true),<br />
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
		<li>To load global website model across any module<br />$this->load->website_model($this,'<b>examplewebsite_model</b>');</li>
		<li>To use loaded model functions<br />$this->examplemodule_model-><b>example()</b></li>
		<li>function "module_view" has built in device and template path</li>
	</ul>
</div>
<span class="annotation">Model:</span>
<?php echo $model;?>