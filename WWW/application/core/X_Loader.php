<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 11/12/2014
	framework Codeigniter 2
	Package X
	Global Model: core\X_Loader.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
/**
 * Extended loader
 */
class X_Loader extends CI_Loader
{
	var $ui = NULL;
	var $device = NULL;
	var $host = NULL;
	var $controler = 'ui';
	var $default_module = 'www';
	var $default_function = 'home';
	var $_template = 'default/';
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->_ci_is_php5 = (floor(phpversion()) >= 5) ? TRUE : FALSE;
		!empty($_SESSION['UI']) ? $this->ui = $_SESSION['UI'] : $this->ui = NULL;
		!empty($this->ui['device']['ext']) ? $this->device = $this->ui['device']['ext'] : $this->device = '';
		if ($this->ui['selected_device'] !== false) {
			 $this->device = $this->ui['selected_device'];
		}
		!empty($this->ui['module']) ? define('MODULE', $this->ui['module']) : define('MODULE', $this->default_module);
		define('SELECTED_DEVICE', $this->device);
		$this->_template = ($this->ui['path']['template_system_path']) ? $this->ui['path']['template_system_path'] : $this->_template;
	}

	/**
	 * Loads module 
	 * 
	 * @return void
	 */
	function _module_ci_load($ui, $_ci_data)
	{
		// Set the default data variables
		foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val) {
			$$_ci_val = ( ! isset($_ci_data[$_ci_val])) ? FALSE : $_ci_data[$_ci_val];
		}
		// Set the path to the requested file
		if ($_ci_path == '') {
			$_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
			$_ci_file = ($_ci_ext == '') ? $_ci_view.EXT : $_ci_view;
			$_ci_path = REL_MODULE_VIEWS.$_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = REL_MODULE_VIEWS.$_ci_d_file;
		if($this->ui['selected_device'] !== false) {
			if ( file_exists($_ci_d_path)) {
				$_ci_path = $_ci_d_path;
			}
			else {
				show_error('Unable to load the requested file: '.$_ci_d_file.' '.$_ci_d_path);
			}
		}
		else {
			if ( file_exists($_ci_d_path)) {
				$_ci_path = $_ci_d_path;
			}
			else {
				if ( ! file_exists($_ci_path)) {
					show_error('Unable to load the requested file: '.$_ci_file.' '.$_ci_path);
				}
			}
		}
		// This allows anything loaded using $this->load (views, files, etc.)
		// to become accessible from within the Controller and Model functions.
		// Only needed when running PHP 5
		if ($this->_ci_is_instance()) {
			$_ci_CI =& get_instance();
			foreach (get_object_vars($_ci_CI) as $_ci_key => $_ci_var) {
				if ( ! isset($this->$_ci_key)) {
					$this->$_ci_key =& $_ci_CI->$_ci_key;
				}
			}
		}
		/*
		 * Extract and cache variables
		 *
		 * You can either set variables using the dedicated $this->load_vars()
		 * function or via the second parameter of this function. We'll merge
		 * the two types and cache them so that views that are embedded within
		 * other views can have access to these variables.
		 */	
		if (is_array($_ci_vars)) {
			$this->_ci_cached_vars = array_merge($this->_ci_cached_vars, $_ci_vars);
		}
		extract($this->_ci_cached_vars);
		/*
		 * Buffer the output
		 *
		 * We buffer the output for two reasons:
		 * 1. Speed. You get a significant speed boost.
		 * 2. So that the final rendered template can be
		 * post-processed by the output class.  Why do we
		 * need post processing?  For one thing, in order to
		 * show the elapsed page load time.  Unless we
		 * can intercept the content right before it's sent to
		 * the browser and then stop the timer it won't be accurate.
		 */
		ob_start();
		// If the PHP installation does not support short tags we'll
		// do a little string replacement, changing the short tags
		// to standard PHP echo statements.		
		if ((bool) @ini_get('short_open_tag') === FALSE AND config_item('rewrite_short_tags') == TRUE) {
			echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
		}
		else {
			include($_ci_path); // include() vs include_once() allows for multiple views with the same name 
		}
		// Return the file data if requested
		if ($_ci_return === TRUE) {
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}
		/*
		 * Flush the buffer... or buff the flusher?
		 *
		 * In order to permit views to be nested within
		 * other views, we need to flush the content back out whenever
		 * we are beyond the first level of output buffering so that
		 * it can be seen and included properly by the first included
		 * template and any subsequent ones. Oy!
		 *
		 */	
		if (ob_get_level() > $this->_ci_ob_level + 1) {
			ob_end_flush();
		}
		else {
			// PHP 4 requires that we use a global
			global $OUT;
			$OUT->append_output(ob_get_contents());
			@ob_end_clean();
		}
	}

	/**
	 * Loads plugin 
	 * 
	 * @return void
	 */
	function _plugin_ci_load($obj,$_ci_data)
	{
		// get info about calling class so that we can get the name and therefore the directory to build paths
		$current_plugin = strtolower($obj->parent_name);
		// Set the default data variables
		foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val) {
			$$_ci_val = ( ! isset($_ci_data[$_ci_val])) ? FALSE : $_ci_data[$_ci_val];
		}
		// Set the path to the requested file
		
		if ($_ci_path == '') {
			$_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
			$_ci_file = ($_ci_ext == '') ? $_ci_view.EXT : $_ci_view;
			$_ci_path = REL_PUBLIC_PLUGINS.$current_plugin.PLUGIN_VIEWS.$_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = REL_PUBLIC_PLUGINS.$current_plugin.PLUGIN_VIEWS.$_ci_d_file;
		if($this->ui['selected_device'] !== false) {
			if ( file_exists($_ci_d_path)) {
				$_ci_path = $_ci_d_path;
			}
			else {
				show_error('Unable to load the requested file: '.$_ci_d_file.' '.$_ci_d_path);
			}
		}
		else {
			if ( file_exists($_ci_d_path)) {
				$_ci_path = $_ci_d_path;
			}
			else {
				if ( ! file_exists($_ci_path)) {
					show_error('Unable to load the requested file: '.$_ci_file.' '.$_ci_path);
				}
			}
		}
		// This allows anything loaded using $this->load (views, files, etc.)
		// to become accessible from within the Controller and Model functions.
		// Only needed when running PHP 5

		if ($this->_ci_is_instance()) {
			$_ci_CI =& get_instance();
			foreach (get_object_vars($_ci_CI) as $_ci_key => $_ci_var) {
				if ( ! isset($this->$_ci_key)) {
					$this->$_ci_key =& $_ci_CI->$_ci_key;
				}
			}
		}
		/*
		 * Extract and cache variables
		 *
		 * You can either set variables using the dedicated $this->load_vars()
		 * function or via the second parameter of this function. We'll merge
		 * the two types and cache them so that views that are embedded within
		 * other views can have access to these variables.
		 */	
		if (is_array($_ci_vars)) {
			$this->_ci_cached_vars = array_merge($this->_ci_cached_vars, $_ci_vars);
		}
		extract($this->_ci_cached_vars);
		/*
		 * Buffer the output
		 *
		 * We buffer the output for two reasons:
		 * 1. Speed. You get a significant speed boost.
		 * 2. So that the final rendered template can be
		 * post-processed by the output class.  Why do we
		 * need post processing?  For one thing, in order to
		 * show the elapsed page load time.  Unless we
		 * can intercept the content right before it's sent to
		 * the browser and then stop the timer it won't be accurate.
		 */
		ob_start();
		// If the PHP installation does not support short tags we'll
		// do a little string replacement, changing the short tags
		// to standard PHP echo statements.
		if ((bool) @ini_get('short_open_tag') === FALSE AND config_item('rewrite_short_tags') == TRUE) {
			echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
		}
		else {
			include($_ci_path); // include() vs include_once() allows for multiple views with the same name 
		}
		// Return the file data if requested
		if ($_ci_return === TRUE) {
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}
		/*
		 * Flush the buffer... or buff the flusher?
		 *
		 * In order to permit views to be nested within
		 * other views, we need to flush the content back out whenever
		 * we are beyond the first level of output buffering so that
		 * it can be seen and included properly by the first included
		 * template and any subsequent ones. Oy!
		 *
		 */	
		if (ob_get_level() > $this->_ci_ob_level + 1) {
			ob_end_flush();
		}
		else {
			// PHP 4 requires that we use a global
			global $OUT;
			$OUT->append_output(ob_get_contents());
			@ob_end_clean();
		}
	}

	/**
	 * Loads widget 
	 * 
	 * @return void
	 */
	function _widget_ci_load($obj, $_ci_data)
	{
		// get info about calling class so that we can get the name and therefore the directory to build paths
		$current_widget = strtolower($obj->parent_name);
		// Set the default data variables
		foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val) {
			$$_ci_val = ( ! isset($_ci_data[$_ci_val])) ? FALSE : $_ci_data[$_ci_val];
		}
		// Set the path to the requested file
		if ($_ci_path == '') {
			$_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
			$_ci_file = ($_ci_ext == '') ? $_ci_view.EXT : $_ci_view;
			//$_ci_path = $this->_widget_ci_view_path.$current_widget.'/views/'.$_ci_file;
			$_ci_path = REL_PUBLIC_WIDGETS . $current_widget . WIDGET_VIEWS . $_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		//$_ci_d_path = $this->_widget_ci_view_path.$current_widget.'/views/'.$_ci_d_file;
		$_ci_d_path = REL_PUBLIC_WIDGETS . $current_widget . WIDGET_VIEWS . $_ci_file;
		if($this->ui['selected_device'] !== false) {
			if ( file_exists($_ci_d_path)) {
				$_ci_path = $_ci_d_path;
			}
			else {
				show_error('Unable to load the requested file: '.$_ci_d_file.' '.$_ci_d_path);
			}
		}
		else {
			if ( file_exists($_ci_d_path)) {
				$_ci_path = $_ci_d_path;
			}
			else {
				if ( ! file_exists($_ci_path)) {
					show_error('Unable to load the requested file: '.$_ci_file.' '.$_ci_path);
				}
			}
		}
		// This allows anything loaded using $this->load (views, files, etc.)
		// to become accessible from within the Controller and Model functions.
		// Only needed when running PHP 5

		if ($this->_ci_is_instance()) {
			$_ci_CI =& get_instance();
			foreach (get_object_vars($_ci_CI) as $_ci_key => $_ci_var) {
				if ( ! isset($this->$_ci_key)) {
					$this->$_ci_key =& $_ci_CI->$_ci_key;
				}
			}
		}
		/*
		 * Extract and cache variables
		 *
		 * You can either set variables using the dedicated $this->load_vars()
		 * function or via the second parameter of this function. We'll merge
		 * the two types and cache them so that views that are embedded within
		 * other views can have access to these variables.
		 */	
		if (is_array($_ci_vars)) {
			$this->_ci_cached_vars = array_merge($this->_ci_cached_vars, $_ci_vars);
		}
		extract($this->_ci_cached_vars);
		/*
		 * Buffer the output
		 *
		 * We buffer the output for two reasons:
		 * 1. Speed. You get a significant speed boost.
		 * 2. So that the final rendered template can be
		 * post-processed by the output class.  Why do we
		 * need post processing?  For one thing, in order to
		 * show the elapsed page load time.  Unless we
		 * can intercept the content right before it's sent to
		 * the browser and then stop the timer it won't be accurate.
		 */
		ob_start();
		// If the PHP installation does not support short tags we'll
		// do a little string replacement, changing the short tags
		// to standard PHP echo statements.
		if ((bool) @ini_get('short_open_tag') === FALSE AND config_item('rewrite_short_tags') == TRUE) {
			echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
		}
		else {
			include($_ci_path); // include() vs include_once() allows for multiple views with the same name 
		}
		// Return the file data if requested
		if ($_ci_return === TRUE) {
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}
		/*
		 * Flush the buffer... or buff the flusher?
		 *
		 * In order to permit views to be nested within
		 * other views, we need to flush the content back out whenever
		 * we are beyond the first level of output buffering so that
		 * it can be seen and included properly by the first included
		 * template and any subsequent ones. Oy!
		 *
		 */	
		if (ob_get_level() > $this->_ci_ob_level + 1) {
			ob_end_flush();
		}
		else {
			// PHP 4 requires that we use a global
			global $OUT;
			$OUT->append_output(ob_get_contents());
			@ob_end_clean();
		}
	}
	

	/*
	* once a plugin is loaded, assigns the $ci object's properties to the new plugin object
	*
	* @param object
	* @param bool
	*
	* @return void
	*/
	function assign_libraries($instance, $use_reference = TRUE)
	{
		$ci =& get_instance(); 
		foreach (array_keys(get_object_vars($ci)) as $key) {
			if (!isset($instance->$key) AND $key != $instance->parent_name) {
				// In some cases using references can cause
				// problems so we'll conditionally use them
				if ($use_reference == TRUE) {
					$instance->$key = NULL; // Needed to prevent reference errors with some configurations
					$instance->$key =& $ci->$key;
				}
				else {
					$instance->$key = $ci->$key;
				}
			}
		}
	}

	/**
	 * function currently not in use
	 *
	 * @param void;
	 *
	 * @return array
	 */
	function change_language(&$ui,$language)
	{
		$mode = array('language' => $language);
		return $this->re_session($ui, $mode);
	}

	/**
	 * set all client session data
	 *
	 * @param void;
	 *
	 * @return array
	 */
	function client_session(&$ui, $mode = false)
	{
		$ci =& get_instance(); 
		$ui['module'] = !empty($_SESSION['app']['module']) ? $_SESSION['app']['module'] : $this->default_module;
		
		$ui['app'] = !empty($_SESSION['app']) ? $_SESSION['app'] : $this->setup();
		if (!empty($_SESSION['app'])) {
			unset($_SESSION['app']);
		}
		$ui['module_id'] = $this->get_module_id($ui['module']);
		//$ui['module_seo'] = $seo_enabled = $this->seo_enabled($ui['module']);
		$ui['session'] = crc32($_SERVER['REMOTE_ADDR'].time()); // unique session
		$ui['session_start'] = time(); // unique session
		$ui['device'] =  $ci->mobile->get_device();
		$ui['selected_device'] = false;
		$ui['groups'] = $this->user_groups();
		foreach ($ui['groups'] as $group) {
			define('GROUP_'.strtoupper($group['system_name']),	$group['group_id']);
		}
		$ui['group_id'] = GROUP_GUEST;
		$ui['path'] = array();
		$ui['modules'] = $this->modules();
		$ui['plugins'] = $this->plugins();
		
		$ui['widgets'] = $this->widgets();
		$ui['user_modules'] = $this->user_modules($ui);
		$ui['user_plugins'] = $this->user_plugins($ui);
		$ui['user_widgets'] = $this->user_widgets($ui);
		
		$ui['module_controllers'] = $this->module_controllers($ui['module']);
		$ui['plugin_controllers'] = $this->plugin_controllers($ui['user_plugins']);
		$ui['widget_controllers'] = $this->widget_controllers($ui['user_widgets']);
		$ui['module_seo'] = $seo_enabled = $this->seo_enabled($ui['module_id'],$ui['modules']);
	/* Set language data */
		if ($ci->config->item('language')) {
			$ui['language'] = $ci->language_model->get_language($ci->config->item('language'));
		}
		else {
			$ui['language'] = $ci->language_model->get_language($ui);
		}
		$ui['languages'] = $ci->language_model->fetch_indexed_array_of_languages();
		/* Set template data */
		$ui['template'] = $ci->template_model->get_template();
		$ui['templates'] = $ci->template_model->fetch_indexed_array_of_templates($ui['module_id']);
		if (isset($ui['template'])&& count($ui['template'])) {
			/* Set theme data */
			$ui['theme'] = $ci->theme_model->get_theme($ui['template']['template_id']);
			$ui['themes'] = $ci->theme_model->fetch_indexed_array_of_themes($ui['template']['template_id']);
			/* Set template paths */
			if ($ui['template']['system_name'] != '') {
				$ui['path']['template_system_path'] =  $ui['template']['system_name'].'/';
				$ui['template_system_name'] =  $ui['template']['system_name'];
			}
			else {
				$ui['path']['template_system_path'] = $ui['template_system_name'] = '';
			}
			/* Set theme paths */
			if (isset($ui['theme'])&& count($ui['theme'])) {
				if ($ui['theme']['system_name'] != '') {
					$ui['path']['theme_system_path'] = $ui['theme']['system_name'].'/';
					$ui['theme_system_name'] = $ui['theme']['system_name'];
				}
				else {
					$ui['path']['theme_system_path'] = $ui['theme_system_name'] = $ui['theme']['system_name'];
				}
			}
			else {
				$ui['path']['theme_system_path'] = $ui['theme_system_name'] = '';
			}
		}
		else {
			$ui['theme'] = false;
			$ui['themes'] = array();
			$ui['path']['template_system_path'] = $ui['template_system_name'] = '';
			$ui['path']['theme_system_path'] = $ui['theme_system_name'] =  '';
		}
		$ui['path']['resources_path'] = $ui['path']['template_system_path'].$ui['path']['theme_system_path'];
		
		$this->paths($ui);
		/* set url to links collection*/
		//$this->links($ui);
		($seo_enabled) ? $this->links($ui) : $ui['links'] = false;
		$this->get_request();
		$this->ui = $ui;
		$this->ui['plugins'] = $ui['plugins'];
		$this->ui['widgets'] = $ui['widgets'];
		$this->ui['user_plugins'] = $ui['user_plugins'];
		$this->ui['user_widgets'] = $ui['user_widgets'];
		$this->language($ui);//loads plugin definitions
		$_SESSION['UI'] = $ui;
	}
	
	/**
	 * function currently not in use
	 *
	 * @param void;
	 *
	 * @return array
	 */
	function change_template(&$ui,$template)
	{
		$mode = array('template' => $template);
		return $this->re_session($ui, $mode);
	}
	
	/**
	 * function currently not in use
	 *
	 * @param void;
	 *
	 * @return array
	 */
	 
	function change_theme(&$ui,$heme)
	{
		$mode = array('theme' => $yheme);
		return $this->re_session($ui, $mode);
	}
	
	/**
	 * gets module by id
	 *
	 * @param string
	 *
	 * @return array
	 */
	function get_module_by_id($module_id)
	{
		$ci =& get_instance();
		$ci->db->where('module_id',$module_id);
		$ci->db->limit(1);
		$query = $ci->db->get('modules');
		return $query->row_array();
	}
	/**
	 * gets module_id by name
	 *
	 * @param string
	 *
	 * @return int
	 */
	function get_module_id($module)
	{
		$ci =& get_instance();
		$ci->db->where('system_name',strtolower($module));
		$ci->db->limit(1);
		$query = $ci->db->get('modules');
		$row = $query->row();
		return $row->module_id;
	}

	/**
	 * function parses GET data  to array args
	 *
	 *@param void
	 *
	 *@return void
	 */
	function get_request()
	{ 
		$ci =& get_instance(); 
		if (strpos($_SERVER['REQUEST_URI'],'?')) {
			$new_get_data = array();
			$arr = $_SERVER['REQUEST_URI'];
			$arr = explode('?', $arr);
			$get = $arr[1];
			$get_data = explode('&', $get);
			foreach ($get_data as $data_item) {
				if ($data_item) {
					$temp = explode('=', $data_item);
					$key = $temp[0];
					$value = $temp[1];
					$_GET[$key] = $ci->security->xss_clean($value);
				}
			}
			return true;
		}
		return false;
	}
	/**
	 * gets host info
	 *
	 * @return array
	 */
	function host()
	{
		$url = $_SERVER['SERVER_NAME'];
		$module = array_shift((explode(".",$_SERVER['SERVER_NAME'])));
		$extension = pathinfo($url, PATHINFO_EXTENSION);
		$parsed_url = parse_url($url);
		if(!empty($parsed_url['host'])) {
			$parts = explode('.',$parsed_url['host']);
		}
		else {
			$parts = explode('.',$url);
		}
		
		$domain = $parts[1];
		if (empty($parsed_url['scheme'])) {
			$base_url = 'http://' . ltrim($url, '/').'/';
			$scheme = 'http://';
		}
		else {
			$scheme = $parsed_url['scheme'];
		}
		return array('scheme' => $scheme, 'module' => $module, 'domain' => $domain, 'extension' => $extension, 'url' => $url, 'base_url' => $base_url);
	}
	/** loads instance
	 *
	 * @param array
	 *
	 * @return void
	 */
	function instance(&$ui, $action, $function, $params, $return=false)
	{
		$function = ($function) ? $function : $this->default_function;
		
		
		if (in_array($action,$ui['plugin_controllers'])) {
			$out = $this->plugin($action, $function, $params,true);
		}
		else {
			/*
				Load action through module
			*/
			if (in_array($action,$ui['module_controllers'])) {
				/* 
					Load module controller
				*/
				$out = $this->module($ui, $action, $function, $params);
			}
			else {
				if(in_array($action,$ui['widget_controllers'])) {
					/* 
					Load widget
					*/
					$out = $this->widget($action, $function, $params,true);
				}
			}
		}
		return $out;
	}
	/** loads language definition files for globals, plugins,  modules, widgets 
	 *
	 * @param array
	 *
	 * @return void
	 */
	function language($ui = false)
	{
		if (empty($ui['language']['language'])) {
			return false;
		}
		/* load default global language file */
		require_once(REL_APPLICATION.'language/' . $ui['language']['language'] . '/default.php');
		/* load default module language file */
		require_once(REL_APPLICATION.'language/' . $ui['language']['language'] . '/modules/'.$ui['module'].'/default.php');
		/* load language files for all user  plugins*/
		if (!empty($ui['user_plugins']) && count($ui['user_plugins'])) {
			foreach ($ui['user_plugins'] as $plugin) {
				if (file_exists(REL_APPLICATION.'language/' . $ui['language']['language'] . '/' . $plugin['system_name'] . '.php')) {
					require_once(REL_APPLICATION.'language/' . $ui['language']['language'] . '/' . $plugin['system_name'] . '.php');
				}
			}
		}
		/* load language files for all user widgets */
		if (!empty($ui['user_widgets']) && count($ui['user_widgets'])) {
			foreach ($ui['user_widgets'] as $widget) {
				if (file_exists(REL_APPLICATION.'language/' . $ui['language']['language'] . '/modules/'.$ui['module'].'/' . $widget['system_name'] . '.php')) {
					require_once(REL_APPLICATION.'language/' . $ui['language']['language'] . '/modules/'.$ui['module'].'/' . $widget['system_name'] . '.php');
				}
			}
		}
	}	

	/**
		Creates a collection 
	*/
	function links(&$ui)
	{
		$ci =& get_instance();
		$links = array();
		$ci->db->where('flag_active',1);
		$query = $ci->db->get('links');
		if ($query->num_rows()) {
			$result = $query->result();
			foreach ($result as $link) {
				if($link->is_method) {
					$functions[$link->link] = array(
													'module_id' => $link->module_id,
													'action' => $link->action
												);
				}
				else {
					$links[$link->link] = array(
													'module_id' => $link->module_id,
													'action' => $link->action,
													'functionality' => $link->functionality
												);
				}
			}
		}
		$ui['functions'] = $functions;
		$ui['links'] = $links;
	}
	/**
	 * load module by checking if module registered, if user allowed, if files exist
	 *
	 *@param string
	 *@param array
	 *
	 *@Return void
	 */
	//function load_module($module = NULL, $params = NULL)
	function load_module($ui, $class=false, $function=false, $all_params = NULL)
	{
		$ci =& get_instance(); 
		static $instances = array();
		$parent = 0;
		/*
			make sure user has access to this module
		*/
		if(in_array($ui['module'],$ui['module_controllers'])) {
			$module = $ui['module'];
			$module_id = $ui['module_id'];
			$params = array();
			if($all_params) {
				foreach($all_params as $param) {
					if(!$class) {
						$class = $param;
					}
					else {
						if(!$function) { //function is not used
							$function = $param;
						}
						else {
							$params[] = $param;
						}
					}
				}
			}
			else {
				$class = $module;
			}
			$instance_name = $module . $module_id . $class;
			// see if there already is an instance of the plugin
			
			if (!array_key_exists($instance_name, $instances)) {
				// instance does not exist, so create it
				if(! class_exists($class)) {
					include_once(REL_MODULE_CONTROLLERS.$class.'.php');
				}
				$instance = new $class($params);
				$instance->parent_name = ucfirst(get_class($instance)); 
				$instance->id = $module_id . $class;
				$this->assign_libraries($instance, TRUE);
				$instances[$instance_name] =& $instance;
			}
			return  $instances[$instance_name];
		}
		return false;
	}
	
	/**
	 * load plugin by checking if plugin registered, if user allowed, if files exist
	 *
	 *@param string
	 *@param array
	 *
	 *@return void
	 */
	function load_plugin($plugin = NULL, $params = NULL)
	{
		$ci =& get_instance(); 
		static $instances = array();
		$parent = 0;
		// make sure this plugin is registered
		$ci->db->where('name', $plugin);
		$ci->db->limit(1);
		$query = $ci->db->get('plugins');
		if (!$row = $query->row()) {
			return FALSE;
		}
		$instance_id = $row->plugin_id;
		$instance_name = $plugin . $instance_id;
		// see if there already is an instance of the plugin
		if (!array_key_exists($instance_name, $instances)) {
			if(! class_exists($plugin)) {
				// instance does not exist, so create it
				include_once(REL_PLUGINS . $plugin . '/controller.php');
			}
			$instance = new $plugin($params);
			$instance->parent_name = ucfirst(get_class($instance)); 
			$instance->id = $instance_id;
			$this->assign_libraries($instance, TRUE);
			$instances[$instance_name] =& $instance;
		}
		return  $instances[$instance_name];
	}
	
	/**
	 * load widgeet by checking if widget registered, if user allowed, if files exist
	 *
	 *@param string
	 *@param array
	 *
	*@Return void
	 */
	function load_widget($widget = NULL, $params = NULL)
	{
		$ci =& get_instance(); 
		static $instances = array();
		$parent = 0;
		isset($params['module']) ? $module = $params['module'] : $module = $this->ui['module'];
		// make sure this plugin is registered
		$ci->db->where('name', $widget);
		$ci->db->limit(1);
		$query = $ci->db->get('widgets');
		if (!$row = $query->row()) {
			return FALSE;
		}
		$instance_id = $row->widget_id;
		$instance_name = $widget . $instance_id;
		// see if there already is an instance of the plugin
		if (!array_key_exists($instance_name, $instances)) {
			// instance does not exist, so create it
			if(! class_exists($widget)) {
				include_once(REL_APPLICATION.'modules/'.$module.'/widgets/' . $widget . '/controller.php');
			}
			$instance = new $widget($params);
			$instance->parent_name = ucfirst(get_class($instance)); 
			$instance->id = $instance_id;
			$this->assign_libraries($instance, TRUE);
			$instances[$instance_name] =& $instance;
		}
		return  $instances[$instance_name];
	}
	
	/**
	 * check and load plugin
	 *
	 *@Param void
	 *
	 *@Return void
	 */
	function module($ui, $module=false,  $function = NULL, $params = NULL) {
		if (!$module = $this->load_module($ui, $module, $function, $params)) {
			return false;
		}
		$function = $function ? $function : 'index';
		return $module->$function($params);
	}
function module_controllers($module)
	{
		$module_dir = REL_APPLICATION.'modules/'.$module.'/controllers/';
		$controller_files = scandir($module_dir);
		$controllers = array();
		foreach ($controller_files as $c_file) {
			if (strpos($c_file,'.php')) {
				$controllers[] = str_replace('.php','',$c_file);
			}
		}
		return $controllers;
	}
	/**
	 * Loads dashboard model
	 * 
	 * @param Object calling object
	 * @param String model name
	 * @param String optional model name
	 * @param Bool db connection
	 * 
	 * @return void
	 */
	function module_model( $obj, $module='WWW', $model, $name = '', $db_conn = FALSE)
	{
	// get info about calling class so that we can get the name and therefore the directory to build paths
		if (is_array($model)) {
			foreach ($model as $babe) {
				$this->model($babe);	
			}
			return;
		}
		if ($model == '') {
			return;
		}
		// Is the model in a sub-folder? If so, parse out the filename and path.
		if (strpos($model, '/') === FALSE) {
			$path = '';
		}
		else {
			$x = explode('/', $model);
			$model = end($x);			
			unset($x[count($x)-1]);
			$path = implode('/', $x).'/';
		}

		if ($name == '') {
			$name = $model;
		}
		if (in_array($name, $this->_ci_models, TRUE)) {
			return;
		}
		if (isset($obj->$name)) {
			show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
		}
		$model = strtolower($model);
		if ( ! file_exists($this->_module_ci_view_path.strtolower($module).'/models/'.$path.$model.EXT)) {
			show_error('Unable to locate the model you have specified: '.$model.'  '.MODULESPATH.strtolower($module).'/models/'.$path.$model.EXT);
		}
		if ($db_conn !== FALSE AND ! class_exists('CI_DB')) {
			if ($db_conn === TRUE) {
				$db_conn = '';
			}
			$obj->load->database($db_conn, FALSE, TRUE);
		}
		if ( ! class_exists('Model')) {
			load_class('Model', FALSE);
		}
		require_once($this->_module_ci_view_path.strtolower($module).'/models/'.$path.$model.EXT);
		$model = ucfirst($model);
		$obj->$name = new $model();
		$obj->$name->_assign_libraries();
		$this->_ci_models[] = $name;	
	}
	
	/**
	 * Loads dashboard view
	 * 
	 * @param Object calling object
	 * @param String view name
	 * @param Array data
	 * @param Bool return output
	 */
	function module_view($view, $vars = array(), $return = FALSE)
	{
		return $this->_module_ci_load($this->ui, array('_ci_view' => $this->_template.$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}

	/**
	 * function returns plugins data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	 */
	function modules()
	{
		$ci =& get_instance();
		$ci->db->from('modules as m');
		$ci->db->join('module_structure as s', 's.module_id = m.module_id', 'left');
		$query = $ci->db->get();
		$modules_result = $query->result_array();
		$modules = array();
		foreach($modules_result as $module) {
			$modules[$module['module_id']] = $module;
		}
		return $modules;
	}
	
	/**
	 * function defines default  paths
	 *
	 * @Param	string
	 * @Param	string
	 * @Param	string
	 * @return	void
	 */
	function paths($ui = false)
	{
		$ci =& get_instance();
		!empty($ui['language']['language']) ? $language = $ui['language']['language'] : $language = $ci->config->item('language');
		!empty($ui['template']['system_name']) ? $template = $ui['template']['system_name'] : $template = 'default';
		!empty($ui['theme']['system_name']) ? $theme = $ui['theme']['system_name'] : $theme = 'default';
		
		define('APPTEMPLATE',$template);
		define('APPTHEME',$theme);
		
		$template .= '/';
		$theme .= '/';
		$scheme = $ui['scheme'];
		$module = $ui['module'];
		$domain = $ui['domain'];
		$extension = $ui['extension'];
		$language = $ui['language']['system_name'];
		
		define('HTTP_MODE',str_replace('://','',$scheme));
		define('PRE','#'.$this->controler);
		define('JS_PRE',$this->controler);
		define('JS_BASE_URL',$scheme.$module.'.'.$domain.'.'.$extension.'/index.php/');
/* GLOBAL 
Global has no templates, it has languages
=================================================================*/
		define('PATH_APPLICATION', base_url().REL_APPLICATION);
		define('PATH_ASSETS', base_url().'assets/');
		define('REL_ASSETS', 'assets/');
		define('PATH_PUBLIC', base_url().'public/');
		define('REL_PUBLIC', 'public/');
		/*
		define('PATH_',);
		define('REL_',);
		define('PATH_',);
		define('REL_',);*/
		
	//LANGUAGE
		define('PATH_LANGUAGES',PATH_APPLICATION.'language/');
		define('REL_LANGUAGES',REL_APPLICATION.'language/');
		define('PATH_LANGUAGE',PATH_LANGUAGES.$language.'/');
		define('REL_LANGUAGE',REL_LANGUAGES.$language.'/');
	//IMAGES
		define('PATH_IMAGE', PATH_ASSETS.'images/');
		define('REL_IMAGE', REL_ASSETS.'images/');
		define('PATH_LANGUAGE_IMAGE', PATH_ASSETS.'images/'.$language.'/');
		define('REL_LANGUAGE_IMAGE', REL_ASSETS.'images/'.$language.'/');
	//SCRIPT
		define('PATH_SCRIPT',PATH_ASSETS.'script/');
		define('REL_SCRIPT',REL_ASSETS.'script/');
	//CSS
		define('PATH_CSS',PATH_ASSETS.'css/');
		define('REL_CSS',REL_ASSETS.'css/');
/* PLUGINS 
Plugins have no templates, they do have languages
=================================================================*/
		define('PATH_PLUGINS', PATH_APPLICATION.'plugins/');
		define('REL_PLUGINS', REL_APPLICATION.'plugins/');
		define('PATH_PLUGINS_ASSETS',PATH_ASSETS.'plugins/');
		define('REL_PLUGINS_ASSETS', REL_ASSETS.'plugins/');
	//LANGUAGE
		define('PATH_PLUGINS_LANGUAGE',PATH_LANGUAGE.'plugins/');
		define('REL_PLUGINS_LANGUAGE', REL_LANGUAGE.'plugins/');
		
		define('PATH_PUBLIC_PLUGINS', PATH_PUBLIC.'plugins/');
		define('REL_PUBLIC_PLUGINS', REL_PUBLIC.'plugins/');
		define('PLUGIN_VIEWS', '/views/');
		define('PLUGIN_ASSETS', '/assets/');
		define('PLUGIN_IMAGES', PLUGIN_ASSETS.'images/');
		define('PLUGIN_CSS', PLUGIN_ASSETS.'css/');
		define('PLUGIN_SCRIPT', PLUGIN_ASSETS.'script/');
		
	//IMAGES
		define('PATH_PLUGINS_IMAGE', PATH_PLUGINS_ASSETS.'images/');
		define('REL_PLUGINS_IMAGE', REL_PLUGINS_ASSETS.'images/');
		define('PATH_PLUGINS_LANGUAGE_IMAGE', PATH_PLUGINS_ASSETS.'images/'.$language.'/');
		define('REL_PLUGINS_LANGUAGE_IMAGE', REL_PLUGINS_ASSETS.'images/'.$language.'/');
	//SCRIPT
		define('PATH_PLUGINS_SCRIPT', PATH_PLUGINS_ASSETS.'script/');
		define('REL_PLUGINS_SCRIPT', REL_PLUGINS_ASSETS.'script/');
		define('PATH_PLUGINS_TEMPLATESCRIPT', PATH_PLUGINS_SCRIPT.$template);
		define('REL_PLUGINS_TEMPLATESCRIPT', REL_PLUGINS_SCRIPT.$template);
		define('PATH_PLUGINS_THEMESCRIPT', PATH_PLUGINS_TEMPLATESCRIPT.$theme);
		define('REL_PLUGINS_THEMESCRIPT', REL_PLUGINS_TEMPLATESCRIPT.$theme);
	//CSS
		define('PATH_PLUGINS_CSS',PATH_PLUGINS_ASSETS.'css/');
		define('REL_PLUGINS_CSS',REL_PLUGINS_ASSETS.'css/');
		define('PATH_PLUGINS_TEMPLATECSS',PATH_PLUGINS_CSS.$template);
		define('REL_PLUGINS_TEMPLATECSS',REL_PLUGINS_CSS.$template);
		define('PATH_PLUGINS_THEMECSS',PATH_PLUGINS_TEMPLATECSS.$theme);
		define('REL_PLUGINS_THEMECSS',REL_PLUGINS_TEMPLATECSS.$theme);
/* MODULES 
Modules have templates and languages
=================================================================*/
		define('PATH_MODULES', PATH_APPLICATION.'modules/');
		define('REL_MODULES', REL_APPLICATION.'modules/');
		define('PATH_MODULE', PATH_APPLICATION.'modules/'.$module.'/');
		define('REL_MODULE', REL_APPLICATION.'modules/'.$module.'/');
		define('PATH_MODULE_CONTROLLERS', PATH_MODULE.'controllers/');
		define('REL_MODULE_CONTROLLERS', REL_MODULE.'controllers/');
		define('PATH_MODULE_MODELS', PATH_MODULE.'models/');
		define('REL_MODULE_MODELS', REL_MODULE.'models/');
		define('PATH_MODULE_VIEWS', PATH_PUBLIC.$module.'/views/');
		define('REL_MODULE_VIEWS', REL_PUBLIC.$module.'/views/');
		define('PATH_MODULE_ASSETS',PATH_PUBLIC.$module.'/assets/');
		define('REL_MODULE_ASSETS', REL_PUBLIC.$module.'/assets/');
	//LANGUAGE
		define('PATH_MODULE_LANGUAGE',PATH_LANGUAGE.'modules/'.$module.'/');
		define('REL_MODULE_LANGUAGE', REL_LANGUAGE.'modules/'.$module.'/');
	//IMAGES
		define('PATH_MODULE_IMAGE',PATH_MODULE_ASSETS.'images/');
		define('REL_MODULE_IMAGE', REL_MODULE_ASSETS.'images/');
		define('PATH_MODULE_TEMPLATEIMAGE', PATH_MODULE_IMAGE.$template);
		define('REL_MODULE_TEMPLATEIMAGE',REL_MODULE_IMAGE.$template);
		define('PATH_MODULE_THEMEIMAGE', PATH_MODULE_TEMPLATEIMAGE.$theme);
		define('REL_MODULE_THEMEIMAGE',REL_MODULE_TEMPLATEIMAGE.$theme);

		define('PATH_MODULE_LANGUAGE_IMAGE', PATH_MODULE_IMAGE.$language.'/');
		define('REL_MODULE_LANGUAGE_IMAGE', REL_MODULE_IMAGE.$language.'/');
		define('PATH_MODULE_LANGUAGE_TEMPLATEIMAGE', PATH_MODULE_LANGUAGE_IMAGE.$template);
		define('REL_MODULE_LANGUAGE_TEMPLATEIMAGE',REL_MODULE_LANGUAGE_IMAGE.$template);
		define('PATH_MODULE_LANGUAGE_THEMEIMAGE', PATH_MODULE_LANGUAGE_TEMPLATEIMAGE.$theme);
		define('REL_MODULE_LANGUAGE_THEMEIMAGE',REL_MODULE_LANGUAGE_TEMPLATEIMAGE.$theme);
	//SCRIPT
		define('PATH_MODULE_SCRIPT',PATH_MODULE_ASSETS.'script/');
		define('REL_MODULE_SCRIPT', REL_MODULE_ASSETS.'script/');
		define('PATH_MODULE_TEMPLATESCRIPT',PATH_MODULE_SCRIPT.$template);		//template level script
		define('REL_MODULE_TEMPLATESCRIPT',REL_MODULE_SCRIPT.$template);
		define('PATH_MODULE_THEMESCRIPT',PATH_MODULE_TEMPLATESCRIPT.$theme);	//theme level script
		define('REL_MODULE_THEMESCRIPT',REL_MODULE_TEMPLATESCRIPT.$theme);
	//CSS
		define('PATH_MODULE_CSS',PATH_MODULE_ASSETS.'css/');
		define('REL_MODULE_CSS', REL_MODULE_ASSETS.'css/');
		define('PATH_MODULE_TEMPLATECSS',PATH_MODULE_CSS.$template);
		define('REL_MODULE_TEMPLATECSS', REL_MODULE_CSS.$template);
		define('PATH_MODULE_THEMECSS',PATH_MODULE_TEMPLATECSS.$theme);
		define('REL_MODULE_THEMECSS', REL_MODULE_TEMPLATECSS.$theme);
		

/* WIDGETS 
Widgets have templates and languages
=================================================================*/
		define('PATH_WIDGETS', PATH_MODULE.'widgets/');
		define('REL_WIDGETS', REL_MODULE.'widgets/');
		define('PATH_PUBLIC_WIDGETS',PATH_PUBLIC.$module.'/widgets/');
		define('REL_PUBLIC_WIDGETS', REL_PUBLIC.$module.'/widgets/');
	//LANGUAGE
		define('PATH_WIDGETS_LANGUAGE',PATH_LANGUAGE.'modules/'.$module.'/widgets/');
		define('REL_WIDGETS_LANGUAGE', REL_LANGUAGE.'modules/'.$module.'/widgets/');


		define('WIDGET_VIEWS', '/views/');
		define('WIDGET_ASSETS', '/assets/');
		
		define('WIDGET_IMAGE', WIDGET_ASSETS.'/images/');
		define('WIDGET_TEMPLATEIMAGE', WIDGET_IMAGE.$template);
		define('WIDGET_THEMEIMAGE', WIDGET_TEMPLATEIMAGE.$theme);
		define('WIDGET_LANGUAGE_IMAGE', WIDGET_IMAGE.$language.'/');
		define('WIDGET_LANGUAGE_TEMPLATEIMAGE', WIDGET_LANGUAGE_IMAGE.$theme);
		define('WIDGET_LANGUAGE_THEMEIMAGE', WIDGET_LANGUAGE_TEMPLATEIMAGE.$theme);
		
		define('WIDGET_CSS', WIDGET_ASSETS.'css/');
		define('WIDGET_TEMPLATECSS', WIDGET_CSS.$template);
		define('WIDGET_THEMECSS', WIDGET_TEMPLATECSS.$theme);
		
		define('WIDGET_SCRIPT', WIDGET_ASSETS.'script/');
		define('WIDGET_TEMPLATESCRIPT', WIDGET_SCRIPT.$template);
		define('WIDGET_THEMESCRIPT', WIDGET_TEMPLATESCRIPT.$theme);
/* ADDONS
Addons don't have languages but it does have templates
==================================================================*/
		define('PATH_ADDONS',PATH_ASSETS.'addons/'.$template);
		define('REL_ADDONS',REL_ASSETS.'addons/'.$template);
	//IMAGES
		define('PATH_ADDONS_TEMPLATEIMAGE',PATH_ADDONS.'images/');
		define('REL_ADDONS_TEMPLATEIMAGE',REL_ADDONS.'images/');
		define('PATH_ADDONS_THEMEIMAGE',PATH_ADDONS_TEMPLATEIMAGE.$theme);
		define('REL_ADDONS_THEMEIMAGE',REL_ADDONS_TEMPLATEIMAGE.$theme);
	//SCRIPT
		define('PATH_ADDONS_SCRIPT',PATH_ADDONS.'script/');
		define('REL_ADDONS_SCRIPT',REL_ADDONS.'script/');
	//CSS
		define('PATH_ADDONS_CSS',PATH_ADDONS.'css/');
		define('REL_ADDONS_CSS',REL_ADDONS.'css/');
		define('PATH_ADDONS_THEMECSS',PATH_ADDONS_CSS.$theme);
		define('REL_ADDONSTHEMECSS',REL_ADDONS_CSS.$theme);
	}
	/**
	 * call plugin load then check loaded data
	 *
	 * @Param void
	 *
	 *@Return void
	 */
	function plugin($plugin = NULL, $function = NULL, $args = NULL)
	{
		if (!$plugin = $this->load_plugin($plugin)) {
			return false;
		}
		$function = $function ? $function : 'index';
		return $plugin->$function($args);
	}
	function plugin_controllers($plugins) {
		$plugin_controllers = array();
		foreach($plugins as $plugin) {
			$plugin_controllers[$plugin['plugin_id']] = $plugin['system_name'];
		}
		return $plugin_controllers;
	}
	/**
	 * Loads plugin model
	 * 
	 * @param Object calling object
	 * @param String model name
	 * @param String optional model name
	 * @param Bool db connection
	 * 
	 * @return void
	 */
	function plugin_model( $obj, $model, $name = '', $db_conn = FALSE)
	{
	// get info about calling class so that we can get the name and therefore the directory to build paths
		$current_plugin = strtolower($obj->parent_name);
		if (is_array($model)) {
			foreach ($model as $babe) {
				$this->model($babe);	
			}
			return;
		}
		if ($model == '') {
			return;
		}
		// Is the model in a sub-folder? If so, parse out the filename and path.
		if (strpos($model, '/') === FALSE) {
			$path = '';
		}
		else {
			$x = explode('/', $model);
			$model = end($x);			
			unset($x[count($x)-1]);
			$path = implode('/', $x).'/';
		}

		if ($name == '') {
			$name = $model;
		}
		if (in_array($name, $this->_ci_models, TRUE)) {
			return;
		}
		if (isset($obj->$name)) {
			show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
		}
		$model = strtolower($model);
		if ( ! file_exists(REL_PLUGINS.$current_plugin.'/models/'.$path.$model.EXT)) {
			show_error('Unable to locate the model you have specified: '.$model.'  '.REL_PLUGINS.$current_plugin.'/models/'.$path.$model.EXT);
		}
		if ($db_conn !== FALSE AND ! class_exists('CI_DB')) {
			if ($db_conn === TRUE) {
				$db_conn = '';
			}
			$obj->load->database($db_conn, FALSE, TRUE);
		}
		if ( ! class_exists('Model')) {
			load_class('Model', FALSE);
		}		
		require_once(REL_PLUGINS.$current_plugin.'/models/'.$path.$model.EXT);
		$model = ucfirst($model);
		$obj->$name = new $model();
		$obj->$name->_assign_libraries();
		$this->_ci_models[] = $name;	
	}
	
	/**
	 * Loads plugin view
	 * 
	 * @param Object calling object
	 * @param String view name
	 * @param Array data
	 * @param Bool return output
	 */
	function plugin_view($obj, $view, $vars = array(), $return = FALSE)
	{
		return $this->_plugin_ci_load($obj,array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}
	

	/**
	 * function returns plugins data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	 */
	function plugins()
	{
		$ci =& get_instance();
		$ci->db->from('plugins as c');
		$ci->db->join('plugin_structure as s', 's.plugin_id = c.plugin_id','left');
		$query = $ci->db->get();
		return $query->result_array();
	}
	
	/**
	 * set all client session data
	 *
	 * @param void;
	 *
	 * @return array
	 */
	function request(&$ui)
	{
		$ci =& get_instance(); 
		/*
			check for existing session
		*/
		if (!empty($_SESSION['UI'])) {
			$ui = $_SESSION['UI'];
		}
		/*
			This gives variables
		
			[scheme] => http://
			[module] => www
			[domain] => xflo
			[extension] => info
			[url] => www.xflo.info
			[base_url] => http://www.xflo.info/
		*/
		extract($this->host = $this->host());
		$ui['module'] = $module;
		$ui['scheme'] = $scheme;
		$ui['domain'] = $domain;
		$ui['extension'] = $extension;
		$ui['url'] = $url;
		$ui['base_url'] = $base_url;
		
		if(!empty($_SESSION['module']) && $_SESSION['module'] == $ci->uri->segment(2)) {
			$_SESSION['widget'] = $ci->uri->segment(3);
			if(isset($_SESSION['plugin'])) {
				unset($_SESSION['plugin']);
			}
		}
		else {
			if($ci->uri->segment(2)) {
				$_SESSION['plugin'] = $ci->uri->segment(2);
			}
			if(isset($_SESSION['widget'])) {
				unset($_SESSION['widget']);
			}
		}
		
		//check if segment 2 is current module
		//then set $_SESSION['widget'] = $ci->uri->segment(3);
		/* 
			Make sure session exists beyond this point 
		*/
		if (!empty($_SESSION['UI'])) {
			$ui = $_SESSION['UI'];
			/*
				loads global functions
				i.e. 
				defining paths 
				and 
				GET requests to argunebts passed to ajaxed functions referred as args[]
			*/
			$this->paths($ui);
			$this->get_request();
			
			/*
				Loads plugin definitions
			*/
			$this->language($ui);
		}
		else {
			/*
				creates session with all variables for current user/group
			*/
			$this->client_session($ui);
		}
	}

	/**
	 * Prototype function not in production
	 * reset some conditional client session data
	 *
	 * @param void;
	 *
	 * @return array
	 */
	function re_session(&$ui, $mode = false)
	{
		if(!$mode) {
			return false;
		}
		$ui = $_SESSION['UI'];
		unset($_SESSION['UI']);
		

		$ci =& get_instance(); 
		if(array_key_exists ('language', $mode) == true) {
			/* Set language data */
			$ui['language'] = $ci->language_model->get_language($mode['language']);
			$ui['languages'] = $ci->language_model->fetch_indexed_array_of_languages();
		}
		if(array_key_exists ('template', $mode) == true) {
			/* Set language data */
			//$ui['language'] = $ci->language_model->get_language($mode['language']);
			//$ui['languages'] = $ci->language_model->fetch_indexed_array_of_languages();
		}
		if(array_key_exists ('theme', $mode) == true) {
			/* Set language data */
			//$ui['language'] = $ci->language_model->get_language($mode['language']);
			//$ui['languages'] = $ci->language_model->fetch_indexed_array_of_languages();
		}
		$this->paths($ui);
		$this->get_request();
		$this->ui = $ui;
		$this->language($ui);//loads plugin definitions
		$_SESSION['UI'] = $ui;
		
		return $ui;
	}
	function seo_enabled($module_id,$modules=false) {
		if($modules) {
			foreach ($modules as $module) {
				if ($module['module_id'] == $module_id) {
					return $module['seo_enabled'];
				}
			}
		}
		else {
			$module = $this->get_module_by_id($module_id);
			if(!empty($module['seo_enabled'])) {
				return $module['seo_enabled'];
			}
		}
		return false;
	}
	/**
	 * functon sets up collection 'app' if session failed to set
	 *
	 * @param void;
	 *
	 * @return array
	 */
	function setup()
	{
		$url = $_SERVER['SERVER_NAME'];
		$module = array_shift((explode(".",$_SERVER['SERVER_NAME'])));
		$extension = pathinfo($url, PATHINFO_EXTENSION);
		$parsed_url = parse_url($url);
		if (!empty($parsed_url['host'])) {
			$parts = explode('.',$parsed_url['host']);
		}
		else {
			$parts = explode('.',$url);;
		}
		$domain = $parts[1];
		if (empty($parsed_url['scheme'])) {
			$base_url = 'http://' . ltrim($url, '/').'/';
			$scheme = 'http://';
		}
		else {
			$scheme = $parsed_url['scheme'];
		}
		$app = $_SESSION['app'] = array('scheme' => $scheme, 'module' => $module, 'domain' => $domain, 'extension' => $extension, 'url' => $url, 'base_url' => $base_url);
		return $app;
	}
	/**
	 * function returns plugins data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	 */
	function user_plugins($ui = false)
	{
		$ci =& get_instance();
		if (!$ui) {
			return false;
		}
		$user_plugins = array();
		if ($this->ui['group_id'] == GROUP_CUSTOM) {
			$ci->db->where('user_id',$ui['id']);
		}
		else {
			$ci->db->where('user_id',$ui['group_id']);
		}
		$ci->db->from('user_plugins');
		$query = $ci->db->get();
		$results = $query->result_array();
		foreach ($results as $user_plugin) {
			foreach ($ui['plugins'] as $plugin) {
				if ($plugin['plugin_id'] == $user_plugin['plugin_id']) {
					$user_plugins[$user_plugin['plugin_id']] = $plugin;
					$user_plugins[$user_plugin['plugin_id']]['crud'] = $user_plugin['crud'];
				}
			}
		}
		return $user_plugins;
	}

	/**
	 * function returns plugins data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	 */
	function user_modules($ui = false)
	{
		$ci =& get_instance();
		if (!$ui) {
			return false;
		}
		$ci->db->from('modules as m');
		$ci->db->join('user_modules as u', 'm.module_id = u.module_id','left');
		if ($this->ui['group_id'] == GROUP_CUSTOM) {
			$ci->db->where('user_id',$ui['id']);
		}
		else {
			$ci->db->where('user_id',$ui['group_id']);
		}
		$query = $ci->db->get();
		$results = $query->result_array();
		$user_modules = array();
		foreach($results as $module) {	// make array key a module_id
			$user_modules[$module['module_id']] = $module;
		}
		return $user_modules;
	}
	

	/**
	 * function returns plugins data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	 */
	function user_widgets($ui=false)
	{
		$ci =& get_instance();
		if (!$ui) {
			return false;
		}
		$module = $ui['module'];
		$module_id = $ui['module_id'];
		$user_widgets = array();
		$ci->db->from('widgets as w');
		$ci->db->join('user_widgets as u', 'w.widget_id = u.widget_id','left');
		$ci->db->join('widget_structure as s', 'w.widget_id = s.widget_id','left');
		$ci->db->where('s.module_id',$module_id);
		if ($ui['group_id'] == GROUP_CUSTOM) {
			$ci->db->where('u.user_id',$ui['id']);
		}
		else {
			$ci->db->where('u.user_id',$ui['group_id']);
		}
		$query = $ci->db->get();
		$results = $query->result_array();
		$user_widgets = array();
		foreach ($results as $widget) {
			$user_widgets[$widget['widget_id']] = $widget;
		}
		return $user_widgets;
	}
	function user_groups() {
		$ci =& get_instance();
		$query = $ci->db->get('user_groups');
		$groups =  $query->result_array();
		$user_groups = array();
		foreach($groups as $group) {
			$user_groups[$group['group_id']] = $group;
		}
		return $user_groups;
	}

	/**
	 * call widget load then check loaded data
	 *
	 * @Param void
	 *
	 *@Return void
	 */
	function widget($widget = NULL, $function = NULL, $args = NULL)
	{
		$msg = str_replace('<WIDGET>', '<b>' . $widget . '</b>', '<div class="msg-error">'.NO_WIDGET.'</div>');
		if (!$widget = $this->load_widget($widget)) {
			return false;
		}
		$function = $function ? $function : 'index';
		return $widget->$function($args);
	}
	function widget_controllers($widgets) {
		$widget_controllers = array();
		foreach($widgets as $widget) {
			$widget_controllers[$widget['widget_id']] = $widget['system_name'];
		}
		return $widget_controllers;
	}
	/**
	 * Loads dashboard plugin model
	 * 
	 * @param Object calling object
	 * @param String model name
	 * @param String optional model name
	 * @param Bool db connection
	 * 
	 * @return void
	 */
	function widget_model( $obj, $module='SITE', $model, $name = '', $db_conn = FALSE)
	{ 
	// get info about calling class so that we can get the name and therefore the directory to build paths
		$current_widget = strtolower($obj->parent_name);
		if (is_array($model)) {
			foreach ($model as $babe) {
				$this->model($babe);	
			}
			return;
		}
		if ($model == '') {
			return;
		}
		// Is the model in a sub-folder? If so, parse out the filename and path.
		if (strpos($model, '/') === FALSE) {
			$path = '';
		}
		else {
			$x = explode('/', $model);
			$model = end($x);			
			unset($x[count($x)-1]);
			$path = implode('/', $x).'/';
		}

		if ($name == '') {
			$name = $model;
		}
		if (in_array($name, $this->_ci_models, TRUE)) {
			return;
		}
		if (isset($obj->$name)) {
			show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
		}
		$model = strtolower($model);
		if ( ! file_exists($this->_module_ci_view_path.strtolower($module).'/widgets/'.$current_widget.'/models/'.$path.$model.EXT)) {
			show_error('Unable to locate the model you have specified: '.$model.'  '.MODULESPATH.strtolower($module).'/widgets/'.$current_plugin.'/models/'.$path.$model.EXT);
		}
		if ($db_conn !== FALSE AND ! class_exists('CI_DB')) {
			if ($db_conn === TRUE) {
				$db_conn = '';
			}
			$obj->load->database($db_conn, FALSE, TRUE);
		}
		if ( ! class_exists('Model')) {
			load_class('Model', FALSE);
		}		
		require_once($this->_module_ci_view_path.strtolower($module).'/widgets/'.$current_widget.'/models/'.$path.$model.EXT);
		$model = ucfirst($model);
		$obj->$name = new $model();
		$obj->$name->_assign_libraries();
		$this->_ci_models[] = $name;	
	}
	
	/**
	 * Loads plugin view
	 * 
	 * @param Object calling object
	 * @param String view name
	 * @param Array data
	 * @param Bool return output
	 */
	function widget_view($obj, $view, $vars = array(), $return = FALSE)
	{
		return $this->_widget_ci_load($obj, array('_ci_view' => $this->_template.$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}

	/**
	 * function returns widgets data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	 */
	function widgets($module=false)
	{
		$ci =& get_instance();
		$ci->db->from('widgets as c');
		$ci->db->join('widget_structure as s', 's.widget_id = c.widget_id','left');
		$query = $ci->db->get();
		return $query->result_array();
	}
}