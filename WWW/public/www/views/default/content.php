<?php  if ( ! defined('BASEPATH')) exit('Access denied.'); ?>
<!--Device: <?php //echo var_dump($device);?>-->
<div><a href="<?php echo PRE ?>/examplecomponent/home" onclick="ui.element(this);return false;">Examples via javaScript</a></div>
<div><a href="<?php echo PRE ?>/examplecomponent/home">Examples</a></div>
<?php
echo EXAMPLE_COMPONENT_TEST.'<br>';
echo MODULE_TEST.'<br>';
echo EXAMPLE_WIDGET_TEST;
 ?>
<span id="logo">
	<!--<img src="<?php  //echo REL_TEMPLATEIMAGE?>logo.png" />-->
</span>