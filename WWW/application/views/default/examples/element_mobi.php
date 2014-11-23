<p>
	<i>Module :<b>Global</b></i><br />
	<i>Page : <b>Ajaxed Element</b></i><br />
	<i>Device : <b>Mobile</b></i><br />
	<i>File : <b>views/<?php echo $template['name'];?>/element_mobi.php</b></i><br />
	<i>Template : <b><?php echo $template['name'];?></b></i>
</p>
<hr />
<span class="annotation">Output :</span>
<div id="code">
	if($this->uri->segment(4)) {//sample test : if exists, append 4th segment to view<br />
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => 'ui.discard("ajax");<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'ajax' => $this->load->view($this->url.'examples/element',NULL, TRUE).$this->uri->segment(4)<br />
	&nbsp;&nbsp;);<br />
	}<br />
	else {<br />
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => 'ui.discard("ajax");<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'ajax' => $this->load->view($this->url.'examples/element',NULL, TRUE)<br />
	&nbsp;&nbsp;);<br />
	}
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
		<li>function "view" does not have built in template path<br />
			variable "$this->url" is template path</li>
	</ul>
</div>