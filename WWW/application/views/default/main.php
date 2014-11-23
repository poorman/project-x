<?php  if ( ! defined('BASEPATH')) exit('Access denied.');
if (!isset($interface)) { //if not refreshing
	include('processor/preprocessor.php');
	echo '<body id="top"><div id="interface">';
}
/* CONTENT */
?>
<? $this->load->view($path['template_system_path'].$content);?>
<?
/* END CONTENT */
if(!isset($interface)) {
?>
</div>
<!-- END V2 -->
<div id="refresh_data" style="display:none;"></div>
<div id="status-data" style="display:none;"></div>
<div id="script" style="display:none;"></div> 
<!-- ajax post script -->
<!-- Preloaders -->
<div class="processing galaxy">
		<ul class="loader">
			<li></li>
			<li></li>
			<li></li>
			<li></li>
		</ul>
	<div id="clear_content" style="display:none;"></div> 
</div>
<div class="processing pendulum">
	<div class="bar">
		<i class="sphere"></i>
	</div>
</div>
<div class="processing spinhead">
	<div class="spinner"></div>
</div>
<!-- End Preloaders -->
<? 
	include('processor/postprocessor.php');
	echo '</body></html>';
	/**/
	$c = get_defined_constants(true);
}