<p>
	<i>Module :<b>Panel</b></i><br />
	<i>Page : <b>Ajaxed Element</b></i><br />
	<i>Device : <b>Mobile</b></i><br />
	<i>File : <b>modules/panel/views/<?php echo $template['name'];?>/website_mobi.php</b></i><br />
	<i>Template : <b><?php echo $template['name'];?></b></i>
</p>
<hr />
<span class="annotation">Output :</span>
<div id="code">
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => 'ui.discard("ajax");'<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'ajax' => $this->load->module_view('website_mobi',$data,true),<br />
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
		<li>function "module_view" has built in device and template path</li>
	</ul>
</div>