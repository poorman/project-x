<div id="dialog_example">
<p>
	<i>Global Plugin Controller : <b>DIALOG</b></i><br />
	<i>Device : <b>Default</b></i>
</p>
<hr />
<span class="annotation">Output :</span>
<div id="code">
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => 'ui.discard("dialog");'<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'dialog' => $this->load->plugin_view($this,'plugin_dialog',NULL,TRUE),<br />
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
		<li>function "plugin_view" has built in template path</li>
	</ul>
</div>
</div>