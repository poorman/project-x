<?php  if ( ! defined('BASEPATH')) exit('Access denied.');?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="CACHE-CONTROL" content="NO-CACHE">
<title>Content Management System</title>
<!------------------------------------------------------------------------------------------------------------------->
<!-- HEADERS -------------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<!-- CSS STYLES ----------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<!-- MODULE --------------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<link rel="stylesheet" href="<?php echo PATH_MODULE_THEMECSS;?>facebox/facebox.css" media="screen" type="text/css"/>
<link rel="stylesheet" href="<?php echo PATH_MODULE_THEMECSS;?>preloaders/preloaders.css" media="screen" type="text/css"/>
<?php
if (file_exists(REL_MODULE_CSS.'style.css')) {
	echo '<link rel="stylesheet" href="' . PATH_MODULE_CSS.'style.css' . '" type="text/css" />'.PHP_EOL;
}
if (file_exists( REL_MODULE_CSS.'autostyle.css')) {
	echo '<link rel="stylesheet" href="'.PATH_MODULE_CSS.'autostyle.css" type="text/css" media="screen" charset="utf-8" />'.PHP_EOL;
}
if (file_exists( REL_MODULE_TEMPLATECSS.'template.css')) {
	echo '<link rel="stylesheet" href="'.PATH_MODULE_TEMPLATECSS.'template.css" type="text/css" media="screen" charset="utf-8" />'.PHP_EOL;
}
if (file_exists( REL_MODULE_TEMPLATECSS.'autotemplate.css')) {
	echo '<link rel="stylesheet" href="'.PATH_MODULE_TEMPLATECSS.'autotemplate.css" type="text/css" media="screen" charset="utf-8" />'.PHP_EOL;
}
if (file_exists(REL_MODULE_TEMPLATECSS.'template.php')) {
	include(PATH_MODULE_TEMPLATECSS.'template.php?language='.$language['system_name'].'&path='.$template['system_name']);
}
if (file_exists( REL_MODULE_THEMECSS.'theme.css')) {
	echo '<link rel="stylesheet" href="'.PATH_MODULE_THEMECSS.'theme.css" type="text/css" media="screen" charset="utf-8" />'.PHP_EOL;
}
if (file_exists( REL_MODULE_THEMECSS.'autotheme.css')) {
	echo '<link rel="stylesheet" href="'.PATH_MODULE_THEMECSS.'autotheme.css" type="text/css" media="screen" charset="utf-8" />'.PHP_EOL;
}
if (file_exists( REL_MODULE_THEMECSS.'theme.php')) {
	include(PATH_MODULE_THEMECSS.'theme.php?language='.$language['system_name'].'&path='.$template['system_name']);
}
?>
<!------------------------------------------------------------------------------------------------------------------->
<!-- JAVASCRIPT ----------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<!-- MODULE --------------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<script src="<?php echo PATH_MODULE_SCRIPT;?>jquery/jquery-1.8.2.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo PATH_MODULE_THEMESCRIPT;?>facebox/facebox.js" type="text/javascript"></script>
<!------------------------------------------------------------------------------------------------------------------->
<!-- COMPONENT HEADERS -------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<?php
if ($component_dirs = opendir(REL_COMPONENTS)) {
	while (FALSE !== ($file = readdir($component_dirs))) {
		if ($file == '..' || $file == '.') {
			continue;
		}
		if (file_exists(REL_PUBLIC_COMPONENTS. $file .COMPONENT_CSS.'style.css')) {
			echo '<link rel="stylesheet" href="' . PATH_PUBLIC_COMPONENTS. $file .COMPONENT_CSS.'style.css' . '" type="text/css" />'.PHP_EOL;
		}
		if (file_exists(REL_PUBLIC_COMPONENTS. $file .COMPONENT_SCRIPT.'script.js')) {
			echo '<script type="text/javascript" src="' . PATH_PUBLIC_COMPONENTS. $file .COMPONENT_SCRIPT.'script.js"></script>'.PHP_EOL;
		}
	}
	closedir($component_dirs);
}
?>

<!------------------------------------------------------------------------------------------------------------------->
<!-- WIDGET HEADERS -------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<?php
if ($widget_dirs = opendir(REL_WIDGETS)) {
	while (FALSE !== ($file = readdir($widget_dirs))) {
		if ($file == '..' || $file == '.') {
			continue;
		}
		if (file_exists(REL_PUBLIC_WIDGETS. $file . WIDGET_CSS.'style.css')) {
			echo '<link rel="stylesheet" href="' . PATH_PUBLIC_WIDGETS. $file . WIDGET_CSS.'style.css' . '" type="text/css" />'.PHP_EOL;
		}
		if (file_exists(REL_PUBLIC_WIDGETS. $file . WIDGET_TEMPLATECSS.'template.css')) {
			echo '<link rel="stylesheet" href="' . PATH_PUBLIC_WIDGETS. $file . WIDGET_TEMPLATECSS.'template.css' . '" type="text/css" />'.PHP_EOL;
		}
		if (file_exists(REL_PUBLIC_WIDGETS. $file . WIDGET_THEMECSS.'theme.css')) {
			echo '<link rel="stylesheet" href="' . PATH_PUBLIC_WIDGETS. $file . WIDGET_THEMECSS.'theme.css' . '" type="text/css" />'.PHP_EOL;
		}

		if (file_exists(REL_PUBLIC_WIDGETS. $file . WIDGET_SCRIPT.'script.js')) {
			echo '<script type="text/javascript" src="' .PATH_PUBLIC_WIDGETS. $file . WIDGET_SCRIPT .'script.js"></script>'.PHP_EOL;
		}
		if (file_exists(REL_PUBLIC_WIDGETS. $file . WIDGET_TEMPLATESCRIPT.'template.js')) {
			echo '<script type="text/javascript" src="' . PATH_PUBLIC_WIDGETS. $file . WIDGET_TEMPLATESCRIPT.'template.js"></script>'.PHP_EOL;
		}
		if(file_exists(REL_PUBLIC_WIDGETS. $file . WIDGET_THEMESCRIPT.'theme.js')) {
			echo '<script type="text/javascript" src="' . PATH_PUBLIC_WIDGETS. $file . WIDGET_THEMESCRIPT.'theme.js"></script>'.PHP_EOL;
		}
	}
	closedir($widget_dirs);
}
?>
<!------------------------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
</head>