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
	var $default_function = 'index';
	var $_plugin_ci_view_path = '';
	var $_module_ci_view_path = '';
	var $_widget_ci_view_path = '';
	var $_dashboard_ci_view_path = '';
	var $_dashboard_plugin_ci_view_path = '';
	var $_gearbox_ci_view_path = '';
	var $_gearbox_plugin_ci_view_path = '';
	var $_website_ci_view_path = '';
	var $_website_plugin_ci_view_path = '';
	var $_template = 'default/';
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->_ci_is_php5 = (floor(phpversion()) >= 5) ? TRUE : FALSE;
		$this->_plugin_ci_view_path = REL_APPLICATION.'plugins/';
		$this->_module_ci_view_path = REL_APPLICATION.'modules/';
		if (isset($_SESSION['UI']['module'])) {
			$this->_widget_ci_view_path = REL_APPLICATION.'modules/'.$_SESSION['UI']['module'].'/widgets/';
		}
		else {
			$this->_widget_ci_view_path =  REL_APPLICATION.'modules/'.$this->default_module.'/widgets/';
		}
		!empty($_SESSION['UI']) ? $this->ui = $_SESSION['UI'] : $this->ui = NULL;
		!empty($this->ui['device']['ext']) ? $this->device = $this->ui['device']['ext'] : $this->device = '';
		if ($this->ui['selected_device'] !== false) {
			 $this->device = $this->ui['selected_device'];
		}
		!empty($this->ui['module']) ? define('MODULE', $this->ui['module']) : define('MODULE', $this->default_module);
		define('SELECTED_DEVICE', $this->device);
		$this->template = $this->ui['template']['system_name'].'/';
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
			$_ci_path = REL_MODULE.'views/'.$_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = REL_MODULE.'views/'.$_ci_d_file;
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
			$_ci_path = $this->_plugin_ci_view_path.$current_plugin.'/views/'.$_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = $this->_plugin_ci_view_path.$current_plugin.'/views/'.$_ci_d_file;
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
			$_ci_path = $this->_widget_ci_view_path.$current_widget.'/views/'.$_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = $this->_widget_ci_view_path.$current_widget.'/views/'.$_ci_d_file;
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
		$ui['module_id'] = $this->get_module_id($ui['module']);
		$ui['session'] = crc32($_SERVER['REMOTE_ADDR'].time()); // unique session
		$ui['session_start'] = time(); // unique session
		$ui['device'] =  $ci->mobile->get_device();
		$ui['selected_device'] = false;
		$groups = $this->user_groups();
		foreach ($groups as $group) {
			define('GROUP_'.strtoupper($group['system_name']), $group['group_id']);
		}
		$ui['group_id'] = GROUP_GUEST;
		$ui['modules'] = $this->user_modules($ui);
		$ui['plugins'] = $this->user_plugins($ui);
		$ui['module_controllers'] = $this->module_controllers($ui['module']);
		$ui['widgets'] = $this->user_widgets($ui);
		/* Set language data */
		$ui['language'] = $ci->language_model->get_language($ui);
		/* Set template data */
		$ui['template'] = $ci->template_model->get_template($ui);
		/* Set theme data */
		$ui['theme'] = $ci->theme_model->get_theme($ui['template']['template_id']);
		/* Set template paths */
		$this->paths($ui);
		/* set url to links collection*/
		$this->links($ui);
		//$constants = get_defined_constants(true);
		//qe($constants['user']);
		$this->get_request();
		$this->language_files($ui);//loads plugin definitions
		$_SESSION['UI'] = $ui;
	}
	
	function module_controllers($module)
	{
		$module_dir = REL_APPLICATION.'modules/'.$module.'/';
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
	*/
	function functions(&$ui)
	{
		$ci =& get_instance();
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
		if($action['module_id']) {
			/*
				Load action through module
			*/
			if (in_array($action['action'],$ui['module_controllers'])) {
				/* 
					Load module controller
				*/
				$out = $this->module($action['action'], $function, $params);
			}
			else {
				if(in_array($action['action'],$ui['widgets'])) {
					/* 
					Load widget
					*/
					$out = $this->widget($action['action'], $function, $params,true);
				}
			}
		}
		else {
			/*
				Load plugin
			*/
			if (in_array($action['action'],$ui['plugins'])) {
				$out = $this->plugin($action['action'], $function, $params,true);
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
	function language_files($ui = false)
	{
		if (empty($ui['language']['language'])) {
			return false;
		}
		/*
			Load default global language file
		*/
		require_once(REL_LANGUAGE . 'default.php');
		
		/*
			Load plugin language files ~ plugins are global and shareable across modules
		*/
		foreach($ui["plugins"] as $plugin) {
			if(file_exists(REL_PLUGINS_LANGUAGE . $plugin.'.php')) {
				require_once(REL_PLUGINS_LANGUAGE . $plugin.'.php');
			}
		}
		
		/*
			Load current module language file
		*/
		$controller_files = scandir(REL_MODULE_LANGUAGE);
		$language_files=array();
		foreach ($controller_files as $c_file) {
			if (strpos($c_file,'.php')) {
				$language_files[] = $c_file;
			}
		}
		foreach ($language_files as $l_file) {
			require_once(REL_MODULE_LANGUAGE.$l_file);
		}
		/*
			Load widget language files
		*/
		foreach($ui["widgets"] as $widget) {
			if(file_exists(REL_WIDGETS_LANGUAGE. $widget.'.php')) {
				require_once(REL_WIDGETS_LANGUAGE .$widget.'.php');
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
				$links[$link->link] = array(
												'module_id' => $link->module_id,
												'action' => $link->action,
												'functionality' => $link->functionality
											);
			}
		}
		
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
	function load_module($ui, $all_params = NULL)
	{
		$ci =& get_instance(); 
		static $instances = array();
		$parent = 0;
		/*
			make sure user has access to this module
		*/
		if(in_array($ui['module'],$ui['modules'])) {
			$module = $ui['module'];
			$module_id = $ui['module_id'];
			$params = array();
			$class = false;
			$function = false;
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
					include_once(REL_MODULE.$class.'.php');
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
				include_once($this->_plugin_ci_view_path . $plugin . '/controller.php');
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
				include_once($this->_widget_ci_view_path . $widget . '/controller.php');
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
	function module($module,  $function = NULL, $args = NULL) {
		if (!$module = $this->load_module($module)) {
			return false;
		}
		$function = $function ? $function : 'index';
		return $module->$function($args);
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
		return $this->_module_ci_load($this->ui, array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}

	/**
	 * function returns plugins data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	
	function modules()
	{
		$ci =& get_instance();
		$ci->db->from('modules');
		$query = $ci->db->get();
		return $query->result_array();
	}
 */	
	/**
	 * function defines default global paths,
	 * plugins paths and 
	 * current module, language, template and theme paths
	 *
	 * @Param	array
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
		define('PATH_MODULES_ASSETS',PATH_ASSETS.'modules/');
		define('REL_MODULES_ASSETS', REL_ASSETS.'modules/');
		define('PATH_MODULE_ASSETS',PATH_MODULES_ASSETS.$module.'/');
		define('REL_MODULE_ASSETS', REL_MODULES_ASSETS.$module.'/');
	//LANGUAGE
		define('PATH_MODULES_LANGUAGE',PATH_LANGUAGE.'modules/');
		define('REL_MODULES_LANGUAGE', REL_LANGUAGE.'modules/');
		define('PATH_MODULE_LANGUAGE',PATH_MODULES_LANGUAGE.$module.'/');
		define('REL_MODULE_LANGUAGE', REL_MODULES_LANGUAGE.$module.'/');
	//IMAGES
		define('PATH_MODULE_IMAGE', PATH_MODULE_ASSETS.'images/');
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
		define('PATH_MODULE_SCRIPT', PATH_MODULE_ASSETS.'script/');		//module level script
		define('REL_MODULE_SCRIPT', REL_MODULE_ASSETS.'script/');
		define('PATH_MODULE_TEMPLATESCRIPT',PATH_MODULE_SCRIPT.$template);		//template level script
		define('REL_MODULE_TEMPLATESCRIPT',REL_MODULE_SCRIPT.$template);
		define('PATH_MODULE_THEMESCRIPT',PATH_MODULE_TEMPLATESCRIPT.$theme);	//theme level script
		define('REL_MODULE_THEMESCRIPT',REL_MODULE_TEMPLATESCRIPT.$theme);
	//CSS
		define('PATH_MODULE_CSS',PATH_MODULE_ASSETS.'css/');
		define('REL_MODULE_CSS',REL_MODULE_ASSETS.'css/');
		define('PATH_MODULE_TEMPLATECSS',PATH_MODULE_CSS.$template);
		define('REL_MODULE_TEMPLATECSS',REL_MODULE_CSS.$template);
		define('PATH_MODULE_THEMECSS',PATH_MODULE_TEMPLATECSS.$theme);
		define('REL_MODULE_THEMECSS',REL_MODULE_TEMPLATECSS.$theme);
/* WIDGETS 
Widgets have templates and languages
=================================================================*/
		define('PATH_WIDGETS', PATH_MODULE.'widgets/');
		define('REL_WIDGETS', REL_MODULE.'widgets/');
		define('PATH_WIDGETS_ASSETS', PATH_MODULE_ASSETS.'widgets/');
		define('REL_WIDGETS_ASSETS', REL_MODULE_ASSETS.'widgets/');
	//LANGUAGES
		define('PATH_WIDGETS_LANGUAGE',PATH_MODULE_LANGUAGE.'widgets/');
		define('REL_WIDGETS_LANGUAGE', REL_MODULE_LANGUAGE.'widgets/');
	//IMAGES
		define('PATH_WIDGETS_IMAGE', PATH_WIDGETS_ASSETS.'images/');
		define('REL_WIDGETS_IMAGE', REL_WIDGETS_ASSETS.'images/');
		define('PATH_WIDGETS_TEMPLATEIMAGE',PATH_WIDGETS_IMAGE.$template);
		define('REL_WIDGETS_TEMPLATEIMAGE',REL_WIDGETS_IMAGE.$template);
		define('PATH_WIDGETS_THEMEIMAGE', PATH_WIDGETS_TEMPLATEIMAGE.$theme);
		define('REL_WIDGETS_THEMEIMAGE', REL_WIDGETS_TEMPLATEIMAGE.$theme);
		
		define('PATH_WIDGETS_LANGUAGE_IMAGE', PATH_WIDGETS_IMAGE.$language.'/');
		define('REL_WIDGETS_LANGUAGE_IMAGE', REL_WIDGETS_IMAGE.$language.'/');
		define('PATH_WIDGETS_LANGUAGE_TEMPLATEIMAGE', PATH_WIDGETS_LANGUAGE_IMAGE.$template);
		define('REL_WIDGETS_LANGUAGE_TEMPLATEIMAGE',REL_WIDGETS_LANGUAGE_IMAGE.$template);
		define('PATH_WIDGETS_LANGUAGE_THEMEIMAGE', PATH_WIDGETS_LANGUAGE_TEMPLATEIMAGE.$theme);
		define('REL_WIDGETS_LANGUAGE_THEMEIMAGE',REL_WIDGETS_LANGUAGE_TEMPLATEIMAGE.$theme);
		//SCRIPT
		define('PATH_WIDGETS_SCRIPT',PATH_WIDGETS_ASSETS.'script/');
		define('REL_WIDGETS_SCRIPT',REL_WIDGETS_ASSETS.'script/');
		define('PATH_WIDGETS_TEMPLATESCRIPT',PATH_WIDGETS_SCRIPT.$template);
		define('REL_WIDGETS_TEMPLATESCRIPT',REL_WIDGETS_SCRIPT.$template);
		define('PATH_WIDGETS_THEMESCRIPT',PATH_WIDGETS_TEMPLATESCRIPT.$theme);
		define('REL_WIDGETS_THEMESCRIPT',REL_WIDGETS_TEMPLATESCRIPT.$theme);
		//CSS
		define('PATH_WIDGETS_CSS',PATH_WIDGETS_ASSETS.'css/');
		define('REL_WIDGETS_CSS',REL_WIDGETS_ASSETS.'css/');
		define('PATH_WIDGETS_TEMPLATECSS',PATH_WIDGETS_CSS.$template);
		define('REL_WIDGETS_TEMPLATECSS',REL_WIDGETS_CSS.$template);
		define('PATH_WIDGETS_THEMECSS',PATH_WIDGETS_TEMPLATECSS.$theme);
		define('REL_WIDGETS_THEMECSS',REL_WIDGETS_TEMPLATECSS.$theme);
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
		if ( ! file_exists($this->_plugin_ci_view_path.$current_plugin.'/models/'.$path.$model.EXT)) {
			show_error('Unable to locate the model you have specified: '.$model.'  '.$this->_plugin_ci_view_path.$current_plugin.'/models/'.$path.$model.EXT);
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
		require_once($this->_plugin_ci_view_path.$current_plugin.'/models/'.$path.$model.EXT);
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
		return $this->_plugin_ci_load($obj,array('_ci_view' => $this->template.$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}
	

	/**
	 * function returns plugins data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	 
	function plugins()
	{
		$ci =& get_instance();
		$ci->db->from('plugins as c');
		$ci->db->join('plugin_structure as s', 's.plugin_id = c.plugin_id','left');
		$query = $ci->db->get();
		return $query->result_array();
	}
*/	
	/**
	 * function reloads session
	 *
	 * @param array;
	 *
	 * @return void
	 */
	function reloader(&$ui) {
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
			Loads components definitions
		*/
		$this->language_files($ui);
		//$this->language($ui);
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
		
		/*
			Check if we continue within same module, or did module changed
			if you go to panel.xflo.com from www.xflo.com
		*/
		
		if ($ci->uri->segment(2)) {
				/*
					this segment will only cary a function if it is in global scope
					otherwise it is always plugin or module controller or widget
					and group is known as components
				*/
				$_SESSION['component'] = $ui['component'] = $ci->uri->segment(2);
			}
		
		
		if(empty($_SESSION['UI'])) {/* fresh load / new session */
			
			/*
				loads global functions
				i.e. 
				defining paths 
				and 
				GET requests to argunebts passed to ajaxed functions referred as args[]
			*/
			$this->client_session($ui);
			
			/*
				creates session with all variables for current user/group
			*/
			
		}
		else {
			if($_SESSION['UI']['module'] == $module) { /* just reloading */
				/*
					loads global functions
					i.e. 
					defining paths 
					and 
					GET requests to argunebts passed to ajaxed functions referred as args[]
				*/
				$this->reloader($ui);
			}
			else {/* Module has changed */
				/*
					preload session for new module use
				*/
				$this->module_session($ui,$host);
			}
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
		$this->language_files($ui);//loads plugin definitions
		$_SESSION['UI'] = $ui;
		
		return $ui;
	}
	
	
	/**
	 * function returns plugins data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	 */
	function user_plugins($ui)
	{
		$ci =& get_instance();
		$ci->db->from('plugins as p');
		$ci->db->join('plugin_structure as s', 's.plugin_id = p.plugin_id','left');
		$ci->db->join('user_plugins as u', 'p.plugin_id = u.plugin_id','left');
		
		if ($this->ui['group_id'] == GROUP_CUSTOM) {
			$ci->db->where('u.user_id',$ui['id']);
		}
		else {
			$ci->db->where('u.user_id',$ui['group_id']);
		}
		$ci->db->from('user_plugins');
		$query = $ci->db->get();
		$plugins = $query->result_array();
		$user_plugins = array();
		foreach($plugins as $plugin) {
			$user_plugins[$plugin['plugin_id']] = $plugin['system_name'];
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
	function user_modules($ui)
	{
		$ci =& get_instance();
		$ci->db->from('modules as m');
		$ci->db->join('user_modules as u', 'm.module_id = u.module_id','left');
		if ($this->ui['group_id'] == GROUP_CUSTOM) {
			$ci->db->where('u.user_id',$ui['id']);
		}
		else {
			$ci->db->where('u.user_id',$ui['group_id']);
		}
		$query = $ci->db->get();
		$modules = $query->result_array();
		$user_modules = array();
		foreach ($modules as $module) {
			$user_modules[$module['module_id']] = $module['system_name'];
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
	function user_widgets($ui)
	{
		$ci =& get_instance();
		$module_id = $ui['module_id'];
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
		$widgets = $query->result_array();
		$user_widgets = array();
		foreach($widgets as $widget) {
			$user_widgets[$widget['widget_id']] = $widget['system_name'];
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
		return $this->_widget_ci_load($obj, array('_ci_view' => $this->template.$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}

	/**
	 * function returns widgets data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	 
	function widgets($module=false)
	{
		$ci =& get_instance();
		$ci->db->from('widgets as c');
		$ci->db->join('widget_structure as s', 's.widget_id = c.widget_id','left');
		$query = $ci->db->get();
		return $query->result_array();
	}*/
}