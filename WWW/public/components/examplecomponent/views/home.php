<?php  if ( ! defined('BASEPATH')) exit('Access denied.'); ?>
<!--Device: <?php echo var_dump($device);?>-->
<div id="example">
<div id="l-margin">
<!--
=======================================================================================================================================
-->
<p><?php echo LOADED_LANGUAGE; ?></p>
<p><?php echo LOADED_COMPONENT_LANGUAGE; ?></p>
<p><?php echo LOADED_MODULE_LANGUAGE; ?></p>
<p><?php echo LOADED_WIDGET_LANGUAGE; ?></p>
<h2><a href="/kill">KILL</a></h2>

<?php echo $select_input_language; ?><br /><?php echo $select_input_template; ?><br /><?php echo $select_input_theme; ?>
<div id="links"><?php echo $menu; ?></div>
<!--
=======================================================================================================================================
-->
</div>
<div id="content">
	<div style="margin-top:-50px; float:right;"><h2>Application code resources</h2></div>
	<div id="ajax">
		<div class="global_note global_border">
			<h2>Global</h2>
			<p>
			<i>Controller:</i> <b>`ui`</b><br />
			<i>Function:</i> <b>`example`</b></p>
		</div>


		<hr />
		

		<div class="component_note">
			<h2>Component</h2>
			<p>
				When process does not find a function in global controller, here referred to as `examplecomponent`,<br />
				then process begins its search within components and uses `examplecomponent` name as component directory name to search for instead.<br />
				When found, next uri segment here used as `example` is used as function name to search for within found directory controller.
			</p>
			<p>
				<i>Global Controller:</i> <b>`ui`</b><br />
				<i>Component Controller:</i> <b>`examplecomponent`</b><br />
				<i>Component folder:</i> <b>`components/examplecomponent/`</b><br />
				<i>Function:</i> <b>`example`</b></p>
		</div>


		<hr />

		<div class="module_note">
			<h2>Module</h2>
			<p>
				When process does not find a function in global controller, here referred to as `site`,<br />
				and it also does not find a component with name `site`,<br />
				then process begins its search within modules and uses `site` name as directory name to search for within modules directory.<br />
				When found, next uri segment here used as `example` is used as function name to search for within found directory controller,
			</p>
			<p>
				<i>Global Controller:</i> <b>`ui`</b><br />
				<i>Module Controller:</i> <b>`site`</b><br />
				<i>Module folder:</i> <b>`modules/site/`</b><br />
				<i>Function:</i> <b>`example`</b></p>
		</div>


		<hr />

		<div class="widget_note">
			<h2>Widget</h2>
			<p>
				When process does not find a function in global controller, here referred to as `site_examplewidget`,<br />
				and it then does not find a component with name `site_examplewidget`, also no match is found as module with name `site_examplewidget`<br />
				then process begins its search within modules widgets and uses `site_examplewidget` name as directory name to search for within modules widget directory.<br />
				When found, next uri segment here used as `example` is used as function name to search for within found directory controller,
			</p>
			<p>
				<i>Global Controller:</i> <b>`ui`</b><br />
				<i>Module Controller:</i> always currently set as active but in this example it is <b>`site`</b><br />
				<i>Module folder:</i> <b>`modules/site/`</b><br />
				<i>Function:</i> <b>`site_examplewidget`</b></p>
		</div>


<hr />


		<div class="arguments_note">
			<h2>Examples</h2>
			<i>Arguments:</i><br />
			<ul>
				<li><b>`dialog`</b> - <i>demonstrates loading new content within dialog window.</i></li>
				<li><b>`interface`</b> - <i>demonstrates loading new content in global parent element... Every view output is contained within `interface` element.</i></li>
				<li><b>`element`</b> - <i>demonstrates loading new content in specific element.</i></li>
				<li><b>`assets`</b> - <i>demonstrates loading new content in specific element with use of global assets like styles scripts and model functions</i></li>
			</ul>
		</div>

	</div>
</div>
</div>