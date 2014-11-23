<?php  if ( ! defined('BASEPATH')) exit('Access denied.');?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="CACHE-CONTROL" content="NO-CACHE">
<title>Content Management System</title>
<!------------------------------------------------------------------------------------------------------------------->
<!-- CSS STYLES HEADER ---------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<link rel="stylesheet" href="<?php echo PATH_THEMECSS;?>facebox/facebox.css" media="screen" type="text/css"/>
<link rel="stylesheet" href="<?php echo PATH_THEMECSS;?>preloaders/preloaders.css" media="screen" type="text/css"/>
<?php /* AUTO STYLES */
if (file_exists( REL_CSS.'autostyle.css')) {
	echo '<link rel="stylesheet" href="'.PATH_CSS.'autostyle.css" type="text/css" media="screen" charset="utf-8" />'.PHP_EOL;
}
if (file_exists( REL_TEMPLATECSS.'autotemplate.css')) {
	echo '<link rel="stylesheet" href="'.REL_TEMPLATECSS.'autotemplate.css" type="text/css" media="screen" charset="utf-8" />'.PHP_EOL;
}
if (file_exists( REL_THEMECSS.'autotheme.css')) {
	echo '<link rel="stylesheet" href="'.PATH_THEMECSS.'autotheme.css" type="text/css" media="screen" charset="utf-8" />'.PHP_EOL;
}/* END AUTO STYLES */
if (file_exists( REL_THEMECSS.'theme.css')) {
	echo '<link rel="stylesheet" href="'.PATH_THEMECSS.'theme.css" type="text/css" media="screen" charset="utf-8" />'.PHP_EOL;
}
if (file_exists( REL_THEMECSS.'theme.php')) {
	include(PATH_THEMECSS.'theme.php?language='.$language.'&path='.$template_path);
}
if (file_exists( REL_TEMPLATECSS.'template.css')) {
	echo '<link rel="stylesheet" href="'.PATH_TEMPLATECSS.'template.css" type="text/css" media="screen" charset="utf-8"'.PHP_EOL;
}
if (file_exists(REL_TEMPLATECSS.'template.php')) {
	include(PATH_TEMPLATECSS.'template.php?language='.$language['language'].'&path='.$path['template_system_path']);
}
?>
<!------------------------------------------------------------------------------------------------------------------->
<!-- JAVASCRIPT HEADER ---------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<script src="<?php echo PATH_SCRIPT;?>jquery/jquery-1.8.2.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo PATH_THEMESCRIPT;?>facebox/facebox.js" type="text/javascript"></script>

<script>
	window.pre = '<?php echo JS_PRE?>';
	window.base_url = '<?php echo JS_BASE_URL?>';
	window.action = false;
	if(!window.location.hash) {
		window.location.hash = pre;
	}
</script>
<!------------------------------------------------------------------------------------------------------------------->
<!-- PLUGIN HEADER -------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<?php
if ($plugin_dirs = opendir(REL_PLUGINS)) {
	while (FALSE !== ($file = readdir($plugin_dirs))) {
		if ($file == '..' || $file == '.') {
			continue;
		}
		if (file_exists(REL_PLUGINS . $file . '/'.PATH_PLUGIN_CSS.'style.css')) {
			echo '<link rel="stylesheet" href="' . REL_PLUGINS . $file . '/'.PATH_PLUGIN_CSS.'style.css' . '" type="text/css" />'.PHP_EOL;
		}
		if (file_exists(REL_PLUGINS . $file . '/'.PATH_PLUGIN_TEMPLATECSS.'template.css')) {
			echo '<link rel="stylesheet" href="' . REL_PLUGINS . $file . '/'.PATH_PLUGIN_TEMPLATECSS.'template.css' . '" type="text/css" />'.PHP_EOL;
		}
		if (file_exists(REL_PLUGINS . $file . '/'.PATH_PLUGIN_THEMECSS.'theme.css')) {
			echo '<link rel="stylesheet" href="' . REL_PLUGINS . $file .'/'.PATH_PLUGIN_THEMECSS.'theme.css' . '" type="text/css" />'.PHP_EOL;
		}

		if (file_exists(REL_PLUGINS . $file . '/'.PATH_PLUGIN_SCRIPT.'script.js')) {
			echo '<script type="text/javascript" src="' . REL_PLUGINS . $file . '/'.PATH_PLUGIN_SCRIPT.'script.js"></script>'.PHP_EOL;
		}
		if (file_exists(REL_PLUGINS . $file . '/'. PATH_PLUGIN_TEMPLATESCRIPT.'template.js')) {
			echo '<script type="text/javascript" src="' . REL_PLUGINS . $file . '/'.PATH_PLUGIN_TEMPLATESCRIPT.'template.js"></script>'.PHP_EOL;
		}
		if(file_exists(REL_PLUGINS . $file . '/'. PATH_PLUGIN_THEMESCRIPT.'theme.js')) {
			echo '<script type="text/javascript" src="' . REL_PLUGINS . $file . '/'.PATH_PLUGIN_THEMESCRIPT.'theme.js"></script>'.PHP_EOL;
		}
	}
	closedir($plugin_dirs);
}
?>
<!------------------------------------------------------------------------------------------------------------------->
<!-- MODULE HEADER -------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<?php
if (file_exists(REL_MODULE_CSS.'style.css')) {
	echo '<link rel="stylesheet" href="' . REL_MODULE_CSS.'style.css' . '" type="text/css" />'.PHP_EOL;
}
if (file_exists(REL_MODULE_TEMPLATECSS.'template.css')) {
	echo '<link rel="stylesheet" href="' . REL_MODULE_TEMPLATECSS.'template.css' . '" type="text/css" />'.PHP_EOL;
}
if (file_exists(REL_MODULE_THEMECSS.'theme.css')) {
	echo '<link rel="stylesheet" href="' . REL_MODULE_THEMECSS.'theme.css' . '" type="text/css" />'.PHP_EOL;
}
if (file_exists(REL_MODULE_SCRIPT.'script.js')) {
	echo '<script type="text/javascript" src="' . REL_MODULE_SCRIPT.'script.js"></script>'.PHP_EOL;
}
if (file_exists(REL_MODULE_TEMPLATESCRIPT.'template.js')) {
	echo '<script type="text/javascript" src="' . REL_MODULE_TEMPLATESCRIPT.'template.js"></script>'.PHP_EOL;
}
if(file_exists(REL_MODULE_THEMESCRIPT.'theme.js')) {
	echo '<script type="text/javascript" src="' . REL_MODULE_THEMESCRIPT.'theme.js"></script>'.PHP_EOL;
}
?>
<!------------------------------------------------------------------------------------------------------------------->
<!-- WIDGET HEADER -------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<?php
if ($widget_dirs = opendir(REL_WIDGET)) {
	while (FALSE !== ($file = readdir($widget_dirs))) {
		if ($file == '..' || $file == '.') {
			continue;
		}
		if (file_exists(REL_WIDGET . $file . '/'.PATH_WIDGET_CSS.'style.css')) {
			echo '<link rel="stylesheet" href="' . REL_WIDGET . $file . '/'.PATH_WIDGET_CSS.'style.css' . '" type="text/css" />'.PHP_EOL;
		}
		if (file_exists(REL_WIDGET . $file . '/'.PATH_WIDGET_TEMPLATECSS.'template.css')) {
			echo '<link rel="stylesheet" href="' . REL_WIDGET . $file . '/'.PATH_WIDGET_TEMPLATECSS.'template.css' . '" type="text/css" />'.PHP_EOL;
		}
		if (file_exists(REL_WIDGET . $file . '/'.PATH_WIDGET_THEMECSS.'theme.css')) {
			echo '<link rel="stylesheet" href="' . REL_WIDGET . $file .'/'.PATH_WIDGET_THEMECSS.'theme.css' . '" type="text/css" />'.PHP_EOL;
		}

		if (file_exists(REL_WIDGET . $file . '/'.PATH_WIDGET_SCRIPT.'script.js')) {
			echo '<script type="text/javascript" src="' .REL_WIDGET . $file . '/'.PATH_WIDGET_SCRIPT.'script.js"></script>'.PHP_EOL;
		}
		if (file_exists(REL_WIDGET . $file . '/'. PATH_WIDGET_TEMPLATESCRIPT.'template.js')) {
			echo '<script type="text/javascript" src="' . REL_WIDGET . $file . '/'.PATH_WIDGET_TEMPLATESCRIPT.'template.js"></script>'.PHP_EOL;
		}
		if(file_exists(REL_WIDGET . $file . '/'. PATH_WIDGET_THEMESCRIPT.'theme.js')) {
			echo '<script type="text/javascript" src="' . REL_WIDGET . $file . '/'.PATH_WIDGET_THEMESCRIPT.'theme.js"></script>'.PHP_EOL;
		}
	}
	closedir($widget_dirs);
}
?>
<!------------------------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------->
</head>