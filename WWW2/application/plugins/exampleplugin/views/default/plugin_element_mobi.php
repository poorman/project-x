<p>
<i>Global Plugin Controller : <b>AJAX</b></i><br />
<i>Device : <b>Mobile</b></i>
</p>
<hr />
<span class="annotation">Output :</span>
<div id="code">
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => 'ui.discard("ajax");<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'ajax' => $this->load->plugin_view($this,'plugin_element',NULL,TRUE)<br />
	&nbsp;&nbsp;);<br />
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
		<li>function "plugin_view" has built in device and template path</li>
	</ul>
</div>
