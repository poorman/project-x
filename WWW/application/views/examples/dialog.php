<div id="dialog_example">
<p>
	<i>Module :<b>Global</b></i><br />
	<i>Page : <b>Dialog</b></i><br />
	<i>Device : <b>Default</b></i><br />
	<i>File : <b>views/<?php echo $template['name'];?>/dialog.php</b></i><br />
	<i>Template : <b><?php echo $template['name'];?></b></i>
</p>
<hr />
<span class="annotation">Output :</span>
<div id="code">
	&nbsp;&nbsp;array(<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'script' => 'ui.discard("dialog");'<br />
	&nbsp;&nbsp;&nbsp;&nbsp;'dialog' => $this->load->view($this->url.'examples/dialog',NULL, TRUE),<br />
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
		<li>function "view" does not have built in template path<br />
			variable "$this->url" is template path</li>
	</ul>
</div>
</div>