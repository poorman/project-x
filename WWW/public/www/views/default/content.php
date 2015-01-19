<?php  if ( ! defined('BASEPATH')) exit('Access denied.'); ?>
<div>
<a href="http://www.xflo.info/kill" >KILL</a>
</div>

<!--Device: <?php //echo var_dump($device);?>-->
<div><a href="<?php echo PRE ?>/examplecomponent/home" onclick="ui.element(this);return false;">Examples via javaScript</a></div>
<div><a href="<?php echo PRE ?>/examplecomponent/home">Examples</a></div>


<div>
	Testing <a href="examplecomponent/home?atest=1975" class="_import">Examples test</a>
</div>
<?php
echo EXAMPLE_COMPONENT_TEST.'<br>';
echo MODULE_TEST.'<br>';
echo EXAMPLE_WIDGET_TEST;
 ?>
<span id="logo">
	<!--<img src="<?php  //echo REL_TEMPLATEIMAGE?>logo.png" />-->
</span>