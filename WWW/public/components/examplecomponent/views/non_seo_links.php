<?php  if ( ! defined('BASEPATH')) exit('Access denied.'); ?>
<fieldset class="example_fieldset global_border">
		<li><a href="<?php echo PRE?>/examplecomponent/seo_enabled" onClick="ui.element(this);return false;">SEO Enabled links</a></li>
    </fieldset>
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
		<fieldset class="example_fieldset component_border">
			<legend class="component_font bold" >Component
<!--
When process does not find a function in global controller, here referred to as `examplecomponent`,
then process begins its search within components and uses `examplecomponent` name as component directory name to search for instead.
When found, next uri segment here used as `example` is used as function name to search for within found directory controller.,
component used: examplecomponent
component folder: components/examplecomponent/
component controller used: `controller.php`
function in component controller used: `example`
arguments used:
`dialog` - demonstrates loading new content within dialog window.
`interface` - demonstrates loading new content in global parent element... Every view output is contained within `interface` element.
`element` - demonstrates loading new content in specific element.
`assets` -  demonstrates loading new content in specific element with use of global assets like styles scripts and model functions
-->
			</legend><ul class="component_font">
				<li><a href="<?php echo PRE?>/examplecomponent/example/dialog" onClick="ui.dialog(this);return false;">Dialog</a>&nbsp;&nbsp;&nbsp;(js only)</li>
				<li><a href="<?php echo PRE?>/examplecomponent/example/interface">Interface</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo PRE?>/examplecomponent/example/interface" onClick="ui.element(this);return false;">via JS</a></li>
				<li><a href="<?php echo PRE?>/examplecomponent/example/element">Element</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo PRE?>/examplecomponent/example/element" onClick="ui.element(this);return false;">via JS</a></li>
				<li><a href="<?php echo PRE?>/examplecomponent/example/assets">Assets</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo PRE?>/examplecomponent/example/asset" onClick="ui.element(this);return false;">via JS</a></li>
			</ul>
		</fieldset><br /><br />
<!--
=======================================================================================================================================
-->
		<fieldset class="example_fieldset module_border">
			<legend class="module_font bold"><?php echo $module;?> Module</legend>
<!--
When process does not find a function in global controller, here referred to as `site`, and it also does not find a component with name `site`,
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
and it then does not find a component with name `site_examplewidget`, also no match is found as module with name `site_examplewidget`
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