<p>
	<i>Module :<b>Global</b></i><br />
	<i>Page : <b>Ajaxed Element with JavaScript</b></i><br />
	<i>Device : <b>Mobile</b></i><br />
	<i>File : <b>views/<?php echo $template['name'];?>/assets_mobi.php</b></i><br />
	<i>Template : <b><?php echo $template['name'];?></b></i>
</p>
<hr />
<span class="annotation">Output :</span>
<div id="code">
	if($this->uri->segment(4)) {//sample test : if exists, append 4th segment to view<br />
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => 'ui.discard("ajax");alert("Linked via javaScript");<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'ajax' => $this->load->view($this->url.'examples/element',NULL, TRUE).$this->uri->segment(4)<br />
	&nbsp;&nbsp;);<br />
	}<br />
	else {<br />
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => 'ui.discard("ajax");alert("Linked via javaScript");<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'ajax' => $this->load->view($this->url.'examples/element',NULL, TRUE)<br />
	&nbsp;&nbsp;);<br />
	}
</div>
<span class="annotation">Result:</span>
<div id="result">
	<ul>
	<li>Loads plugin model</li>
		<li>Utilizes model function to output</li>
		<li>Discards previous element "ajax"</li>
		<li>Triggers Alert Java Script</li>
		<li>Updates element "ajax"</li>
	</ul>
</div>
<span class="annotation">Notes:</span>
<div id="notes">
	<ul>
		<li>To load global model across any module<br />$this->load->model('<b>example_model</b>');</li>
		<li>To use loaded model functions<br />$this->example_model-><b>example()</b></li>
		<li>function "view" does not have built in template path<br />
			variable "$this->url" is template path</li>
	</ul>
</div>
<span class="annotation">Model:</span>
<?php echo $model;?>