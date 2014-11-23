<?php  if ( ! defined('BASEPATH')) exit('Access denied.'); ?>
<!--Device: <?php echo var_dump($device);?>-->
<div id="example">
<div id="l-margin">
<!--
=======================================================================================================================================
-->
<H1><?php echo LOADED_LANGUAGE; ?></H1>
<H1><?php echo LOADED_PLUGIN_LANGUAGE; ?></H1>
<H1><?php echo LOADED_MODULE_LANGUAGE; ?></H1>
<H1><?php echo LOADED_WIDGET_LANGUAGE; ?></H1>
<h2><a href="/kill">KILL</a></h2>

<?php echo $select_input_language; ?><br /><?php echo $select_input_template; ?><br /><?php echo $select_input_theme; ?>
	<fieldset class="example_fieldset global_border">
		<legend class="global_font bold" >Global</legend>
<!--
global controller used: `ui`
function in global controller used: `example`
arguments used:
`dialog` - demonstrates loading new content within dialog window.
`interface` - demonstrates loading new content in global parent element... Every view output is contained within `interface` element.
`element` - demonstrates loading new content in specific element.
`assets` -  demonstrates loading new content in specific element with use of global assets like styles scripts and model functions
-->
		<ul class="global_font">
			<li><a href="<?php echo PRE?>/example/dialog" onClick="ui.dialog(this);return false;">Dialog</a>&nbsp;&nbsp;&nbsp;(js only)</li>
			<li><a href="<?php echo PRE?>/example/interface">Interface</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo PRE?>/example/interface" onClick="ui.element(this);return false;">via JS</a></li>
			<li><a href="<?php echo PRE?>/example/element" >Element</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo PRE?>/example/element" onClick="ui.element(this);return false;">via JS</a></li>
			<li><a href="<?php echo PRE?>/example/assets">Assets</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo PRE?>/example/asset" onClick="ui.element(this);return false;">via JS</a></li>
		</ul><br />
<!--
=======================================================================================================================================
-->
		<fieldset class="example_fieldset plugin_border">
			<legend class="plugin_font bold" >Plugin</legend>
<!--
When process does not find a function in global controller, here referred to as `exampleplugin`,
then process begins its search within plugins and uses `exampleplugin` name as plugin directory name to search for instead.
When found, next uri segment here used as `example` is used as function name to search for within found directory controller.,
plugin used: exampleplugin
plugin folder: plugins/exampleplugin/
plugin controller used: `controller.php`
function in plugin controller used: `example`
arguments used:
`dialog` - demonstrates loading new content within dialog window.
`interface` - demonstrates loading new content in global parent element... Every view output is contained within `interface` element.
`element` - demonstrates loading new content in specific element.
`assets` -  demonstrates loading new content in specific element with use of global assets like styles scripts and model functions
-->
			<ul class="plugin_font">
				<li><a href="<?php echo PRE?>/exampleplugin/example/dialog" onClick="ui.dialog(this);return false;">Dialog</a>&nbsp;&nbsp;&nbsp;(js only)</li>
				<li><a href="<?php echo PRE?>/exampleplugin/example/interface">Interface</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo PRE?>/exampleplugin/example/interface" onClick="ui.element(this);return false;">via JS</a></li>
				<li><a href="<?php echo PRE?>/exampleplugin/example/element">Element</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo PRE?>/exampleplugin/example/element" onClick="ui.element(this);return false;">via JS</a></li>
				<li><a href="<?php echo PRE?>/exampleplugin/example/assets">Assets</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo PRE?>/exampleplugin/example/asset" onClick="ui.element(this);return false;">via JS</a></li>
			</ul>
		</fieldset><br /><br />
<!--
=======================================================================================================================================
-->
		<fieldset class="example_fieldset module_border">
			<legend class="module_font bold"><?php echo $module;?> Module</legend>
<!--
When process does not find a function in global controller, here referred to as `site`, and it also does not find a plugin with name `site`,
then process begins its search within modules and uses `site` name as directory name to search for within modules directory.
When found, next uri segment here used as `example` is used as function name to search for within found directory controller,
module used: `site`
module folder: modules/site/
function in module controller used: `example`
arguments used:
`dialog` - demonstrates loading new content within dialog window.
`interface` - demonstrates loading new content in global parent element... Every view output is contained within `interface` element.
`element` - demonstrates loading new content in specific element.
`assets` -  demonstrates loading new content in specific element with use of global assets like styles scripts and model functions
-->
			<ul class="module_font">
				<li><a href="<?php echo PRE.'/'.$module?>/example/dialog" onClick="ui.dialog(this);return false;">Dialog</a>&nbsp;&nbsp;&nbsp;(js only)</li>
				<li><a href="<?php echo PRE.'/'.$module?>/example/interface">Interface</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo PRE.'/'.$module?>/example/interface" onClick="ui.element(this);return false;">via JS</a></li>
				<li><a href="<?php echo PRE.'/'.$module?>/example/element">Element</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo PRE.'/'.$module?>/example/element" onClick="ui.element(this);return false;">via JS</a></li></li>
				<li><a href="<?php echo PRE.'/'.$module?>/example/assets">Assets</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo PRE.'/'.$module?>/example/asset" onClick="ui.element(this);return false;">via JS</a></li></li>
			</ul><br />
			<fieldset class="example_fieldset widget_border">
				<legend class="widget_font">Widget</legend>
<!--
When process does not find a function in global controller, here referred to as `site_examplewidget`,<br />
and it then does not find a plugin with name `site_examplewidget`, also no match is found as module with name `site_examplewidget`
then process begins its search within modules widgets and uses `site_examplewidget` name as directory name to search for within modules widget directory.
When found, next uri segment here used as `example` is used as function name to search for within found directory controller,

module used: always currently set as active but in this example it is `site`
widget folder: modules/site/
widget used: '`site_examplewidget`
function in widget controller used: `example`
arguments used:
`dialog` - demonstrates loading new content within dialog window.
`interface` - demonstrates loading new content in global parent element... Every view output is contained within `interface` element.
`element` - demonstrates loading new content in specific element.
`assets` -  demonstrates loading new content in specific element with use of global assets like styles scripts and model functions
-->
				<ul class="widget_font">	
					<li><a href="<?php echo PRE?>/examplewidget/example/dialog" onClick="ui.dialog(this);return false;">Dialog</a>&nbsp;&nbsp;&nbsp;(js only)</li>
					<li><a href="<?php echo PRE?>/examplewidget/example/interface" onClick="ui.element(this);return false;">Interface</a></li>
					<li><a href="<?php echo PRE?>/examplewidget/example/element" onClick="ui.element(this);return false;">Element</a></li>
					<li><a href="<?php echo PRE?>/examplewidget/example/assets" onClick="ui.element(this);return false;">Assets</a></li>
				</ul>
			</fieldset>
		</fieldset>
	</fieldset>
	<img src="<?php echo PATH_THEMEIMAGE;?>examples/flow_1.png" style="max-width:250px;" />
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
		

		<div class="plugin_note">
			<h2>Plugin</h2>
			<p>
				When process does not find a function in global controller, here referred to as `exampleplugin`,<br />
				then process begins its search within plugins and uses `exampleplugin` name as plugin directory name to search for instead.<br />
				When found, next uri segment here used as `example` is used as function name to search for within found directory controller.
			</p>
			<p>
				<i>Global Controller:</i> <b>`ui`</b><br />
				<i>Plugin Controller:</i> <b>`exampleplugin`</b><br />
				<i>Plugin folder:</i> <b>`plugins/exampleplugin/`</b><br />
				<i>Function:</i> <b>`example`</b></p>
		</div>


		<hr />

		<div class="module_note">
			<h2>Module</h2>
			<p>
				When process does not find a function in global controller, here referred to as `site`,<br />
				and it also does not find a plugin with name `site`,<br />
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
				and it then does not find a plugin with name `site_examplewidget`, also no match is found as module with name `site_examplewidget`<br />
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