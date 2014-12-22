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
	var $controler = 'ui';
	var $default_module = 'www';
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
		$this->template = $this->ui['path']['template_system_path'];
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
			$_ci_path = $this->_module_ci_view_path.strtolower($ui['module']).'/views/'.$_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = $this->_module_ci_view_path.strtolower($ui['module']).'/views/'.$_ci_d_file;
		
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
		$ui['module'] = !empty($_SESSION['app']['module']) ? $_SESSION['app']['module'] : $this->default_module;
		$ui['app'] = !empty($_SESSION['app']) ? $_SESSION['app'] : $this->setup();
		if (!empty($_SESSION['app'])) {
			unset($_SESSION['app']);
		}
		$ui['module_id'] = $this->get_module_id($ui['module']);
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
	
	/** loads instance
	 *
	 * @param array
	 *
	 * @return void
	 */
	function instance($action, $function, $params)
	{
		/*
		Call to plugin controller
		*/
		if (!$out = $this->plugin($action, $function, $params)) {
			/*
			Call to module controller
			*/
			if (!$out = $this->module($action, $function, $params)) {
				/*
				Call to module widget controller
				*/
				if (!$out = $this->widget($action, $function, $params)) {
					$out['error'] = TRUE;
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
	 * load module by checking if module registered, if user allowed, if files exist
	 *
	 *@param string
	 *@param array
	 *
	 *@Return void
	 */
	function load_module($module = NULL, $params = NULL)
	{
		$ci =& get_instance(); 
		static $instances = array();
		$parent = 0;
		// make sure this module is registered
		$ci->db->where('name', strtolower($module));
		$ci->db->limit(1);
		$query = $ci->db->get('modules');
		if(!$row = $query->row()) return FALSE;
		$instance_id = $row->module_id;
		$instance_name = $module . $instance_id;
		// see if there already is an instance of the plugin
		if (!array_key_exists($instance_name, $instances)) {
			// instance does not exist, so create it
			if(! class_exists($module)) {
				include_once($this->_module_ci_view_path . strtolower($module) . '/controller.php');
			}
			$instance = new $module($params);
			$instance->parent_name = ucfirst(get_class($instance)); 
			$instance->id = $instance_id;
			$this->assign_libraries($instance, TRUE);
			$instances[$instance_name] =& $instance;
		}
		return  $instances[$instance_name];
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
		$msg = str_replace('<MODULE>', '<b>' . $module . '</b>', '<div class="msg-error">'.NO_MODULE.'</div>');
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
		return $this->_module_ci_load($this->ui, array('_ci_view' => $this->template.$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
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
		$ci->db->from('modules');
		$query = $ci->db->get();
		return $query->result_array();
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
		define('HTTP_MODE','http');
		define('PRE','#'.$this->controler);
		define('JS_PRE',$this->controler);
		define('JS_BASE_URL','http://'.$ui['app']['domain'].'/index.php/');
		
		define('PATH_APPLICATION', base_url().REL_APPLICATION);
		define('PATH_PLUGINS', PATH_APPLICATION.'plugins/');
		define('REL_PLUGINS', REL_APPLICATION.'plugins/');
		define('PATH_MODULES', PATH_APPLICATION.'modules/');
		define('REL_MODULES', REL_APPLICATION.'modules/');
		isset($_SESSION['UI']['module']) ? define('PATH_WIDGETS', PATH_MODULES.$_SESSION['UI']['module'].'/widgets/') : define('PATH_WIDGETS', PATH_MODULES.$this->default_module.'/widgets/');
		define('PATH_ASSETS', base_url().'assets/');
		define('REL_ASSETS', 'assets/');
		!empty($ui['language']['language']) ? $language = $ui['language']['language'] : $language = $this->config->item('language');
		!empty($ui['template']['system_name']) ? $template = $ui['template']['system_name'] : $template = '_default';
		!empty($ui['theme']['system_name']) ? $theme = $ui['theme']['system_name'] : $theme = '_default';
		define('APPTEMPLATE',$template);
		define('APPTHEME',$theme);
		$template .= '/';
		$theme .= '/';
/* GLOBAL */
		define('PATH_THIRDPARTY',PATH_ASSETS.'third_party/'.$template.$theme);
		define('REL_THIRDPARTYPATH',REL_ASSETS.'third_party/'.$template.$theme);
		define('PATH_LANGUAGES',PATH_APPLICATION.'language/');
		define('REL_LANGUAGES',REL_APPLICATION.'language/');
		define('PATH_PLUGIN',PATH_APPLICATION.'plugins/');
	//IMAGES
		define('PATH_IMAGE', PATH_ASSETS.'images/');
		define('REL_IMAGE', REL_ASSETS.'images/');
		define('PATH_TEMPLATEIMAGE', PATH_IMAGE.$template);
		define('REL_TEMPLATEIMAGE',REL_IMAGE.$template);
		define('PATH_THEMEIMAGE', PATH_TEMPLATEIMAGE.$theme);
		define('REL_THEMEIMAGEPATH_REL', REL_TEMPLATEIMAGE.$theme);
		
		define('PATH_LANGUAGEIMAGES', PATH_LANGUAGES.$language.'/images/');
		define('REL_LANGUAGEIMAGES', REL_LANGUAGES.$language.'/images/');
		define('PATH_TEMPLATELANGUAGEIMAGE', PATH_LANGUAGEIMAGES.$template);
		define('REL_TEMPLATELANGUAGEIMAGE', REL_LANGUAGEIMAGES.$template);
		define('PATH_THEMELANGUAGEIMAGE', PATH_TEMPLATELANGUAGEIMAGE.$theme);
		define('REL_THEMELANGUAGEIMAGEPATH_REL', REL_TEMPLATELANGUAGEIMAGE.$theme);
	//SCRIPT
		define('PATH_SCRIPT',PATH_ASSETS.'script/');
		define('REL_SCRIPT',REL_ASSETS.'script/');
		define('PATH_TEMPLATESCRIPT',PATH_SCRIPT.$template);
		define('REL_TEMPLATESCRIPT',REL_SCRIPT.$template);
		define('PATH_THEMESCRIPT',PATH_TEMPLATESCRIPT.$theme);
		define('REL_THEMESCRIPT',REL_TEMPLATESCRIPT.$theme);
	//CSS
		define('PATH_CSS',PATH_ASSETS.'css/');
		define('REL_CSS',REL_ASSETS.'css/');
		define('PATH_TEMPLATECSS',PATH_CSS.$template);
		define('REL_TEMPLATECSS',REL_CSS.$template);
		define('PATH_THEMECSS',PATH_TEMPLATECSS.$theme);
		define('REL_THEMECSS',REL_TEMPLATECSS.$theme);
/* PLUGINS */
	//IMAGES	
		define('PATH_PLUGIN_TEMPLATEIMAGE', PATH_IMAGE.'plugin/'.$template);
		define('PATH_PLUGIN_THEMEIMAGE', PATH_PLUGIN_TEMPLATEIMAGE.$theme);
		define('PATH_PLUGIN_LANGUAGEIMAGE', PATH_LANGUAGEIMAGES.'plugin/');
		define('PATH_PLUGIN_TEMPLATELANGUAGEIMAGE', PATH_PLUGIN_LANGUAGEIMAGE.$template);
		define('PATH_PLUGIN_THEMELANGUAGEIMAGE', PATH_PLUGIN_TEMPLATELANGUAGEIMAGE.$theme);
	//SCRIPT
		define('PATH_PLUGIN_SCRIPT','assets/script/');
		define('PATH_PLUGIN_TEMPLATESCRIPT',PATH_PLUGIN_SCRIPT.$template);
		define('PATH_PLUGIN_THEMESCRIPT',PATH_PLUGIN_TEMPLATESCRIPT.$theme);
	//CSS
		define('PATH_PLUGIN_CSS','assets/css/');
		define('PATH_PLUGIN_TEMPLATECSS',PATH_PLUGIN_CSS.$template);
		define('PATH_PLUGIN_THEMECSS',PATH_PLUGIN_TEMPLATECSS.$theme);

/* MODULES */
		foreach($ui['user_modules'] as $module) {
			define('PATH_MODULE_'.strtoupper($module['system_name']), PATH_MODULES.$module['system_name'].'/');
			define('REL_MODULE_'.strtoupper($module['system_name']), REL_MODULES.$module['system_name'].'/');
			if($module['system_name'] == $ui['module']) {
				$current_path_moduleapppath = 'PATH_MODULE_'.strtoupper($module['system_name']);
				$current_rel_moduleapppath = 'REL_MODULE_'.strtoupper($module['system_name']);
			}
		}
		define('PATH_MODULE', base_url().constant($current_path_moduleapppath));
		define('REL_MODULE', constant($current_rel_moduleapppath));
		
		define('PATH_MODULE_ASSETS', PATH_ASSETS.MODULE.'/');
		define('REL_MODULE_ASSETS', REL_ASSETS.MODULE.'/');

		define('MODULE_THIRDPARTYPATH',PATH_MODULE_ASSETS.'third_party/'.$template.$theme);
		define('MODULE_THIRDPARTYPATH_REL',REL_MODULE_ASSETS.'third_party/'.$template.$theme);
	//IMAGES
		define('PATH_MODULE_IMAGE', PATH_MODULE_ASSETS.'images/');
		define('REL_MODULE_IMAGE', REL_MODULE_ASSETS.'images/');
		
		define('PATH_MODULE_TEMPLATEIMAGE', PATH_MODULE_IMAGE.$template);
		define('REL_MODULE_TEMPLATEIMAGE', REL_MODULE_IMAGE.$template);
		
		define('PATH_MODULE_THEMEIMAGE', PATH_MODULE_TEMPLATEIMAGE.$theme);
		define('REL_MODULE_THEMEIMAGE', REL_MODULE_TEMPLATEIMAGE.$theme);
		
		define('PATH_MODULE_LANGUAGEIMAGE', PATH_LANGUAGES.'images/modules/');
		define('REL_MODULE_LANGUAGEIMAGE', REL_LANGUAGES.'images/modules/');
		define('PATH_MODULE_TEMPLATELANGUAGEIMAGE', PATH_MODULE_LANGUAGEIMAGE.$template);
		define('REL_MODULE_TEMPLATELANGUAGEIMAGE', REL_MODULE_LANGUAGEIMAGE.$template);
		
		define('PATH_MODULE_THEMELANGUAGEIMAGE', PATH_MODULE_TEMPLATELANGUAGEIMAGE.$theme);
		define('MODULE_THEMELANGUAGEIMAGE', REL_MODULE_TEMPLATELANGUAGEIMAGE.$theme);
	//SCRIPT
		define('PATH_MODULE_SCRIPT',PATH_MODULE_ASSETS.'script/');
		define('REL_MODULE_SCRIPT',REL_MODULE_ASSETS.'script/');
		define('PATH_MODULE_TEMPLATESCRIPT',PATH_MODULE_SCRIPT.$template);
		define('REL_MODULE_TEMPLATESCRIPT',REL_MODULE_SCRIPT.$template);
		define('PATH_MODULE_THEMESCRIPT',PATH_MODULE_TEMPLATESCRIPT.$theme);
		define('REL_MODULE_THEMESCRIPT',REL_MODULE_TEMPLATESCRIPT.$theme);
	//CSS
		define('PATH_MODULE_CSS',PATH_MODULE_ASSETS.'css/');
		define('REL_MODULE_CSS',REL_MODULE_ASSETS.'css/');
		define('PATH_MODULE_TEMPLATECSS',PATH_MODULE_CSS.$template);
		define('REL_MODULE_TEMPLATECSS',REL_MODULE_CSS.$template);
		define('PATH_MODULE_THEMECSS',PATH_MODULE_TEMPLATECSS.$theme);
		define('REL_MODULE_THEMECSS',REL_MODULE_TEMPLATECSS.$theme);
/* WIDGETS */
		define('PATH_WIDGET',PATH_MODULE.'widgets/');
		define('REL_WIDGET',REL_MODULE.'widgets/');
	//IMAGES		
		define('PATH_WIDGET_IMAGE', PATH_MODULE_IMAGE.'widgets/');
		define('PATH_WIDGET_TEMPLATEIMAGE',PATH_WIDGET_IMAGE.$template);
		define('PATH_WIDGET_THEMEIMAGE', PATH_WIDGET_TEMPLATEIMAGE.$theme);

		define('PATH_WIDGET_LANGUAGEIMAGE', PATH_MODULE_LANGUAGEIMAGE.MODULE.'/widgets/');
		define('PATH_WIDGET_TEMPLATELANGUAGEIMAGE', PATH_WIDGET_LANGUAGEIMAGE.$template);
		define('PATH_WIDGET_THEMELANGUAGEIMAGE', PATH_WIDGET_TEMPLATELANGUAGEIMAGE.$theme);
	//SCRIPT		
		define('PATH_WIDGET_SCRIPT','assets/script/');
		define('PATH_WIDGET_TEMPLATESCRIPT',PATH_WIDGET_SCRIPT.$template);
		define('PATH_WIDGET_THEMESCRIPT',PATH_WIDGET_TEMPLATESCRIPT.$theme);
		
	//CSS		
		define('PATH_WIDGET_CSS','assets/css/');
		define('PATH_WIDGET_TEMPLATECSS',PATH_WIDGET_CSS.$template);
		define('PATH_WIDGET_THEMECSS',PATH_WIDGET_TEMPLATECSS.$theme);
	// AUTO DEFINITIONS	
		foreach ($ui['user_modules'] as $module) {
			if ($module['module_id'] != 1) {//exclude gloabal module
				define('PATH_'.strtoupper($module['system_name']).'_SCRIPT',PATH_MODULE_SCRIPT);
				define('REL_'.strtoupper($module['system_name']).'_SCRIPT',REL_MODULE_SCRIPT);
				define('PATH_'.strtoupper($module['system_name']).'_TEMPLATESCRIPT',constant('PATH_'.strtoupper($module['system_name']).'_SCRIPT').$template);
				define('REL_'.strtoupper($module['system_name']).'_TEMPLATESCRIPT',constant('REL_'.strtoupper($module['system_name']).'_SCRIPT').$template);
				define('PATH_'.strtoupper($module['system_name']).'_THEMESCRIPTPATH',constant('PATH_'.strtoupper($module['system_name']).'_TEMPLATESCRIPT').$theme);
				define('REL_'.strtoupper($module['system_name']).'_THEMESCRIPTPATH_REL',constant('REL_'.strtoupper($module['system_name']).'_TEMPLATESCRIPT').$theme);
				define('PATH_'.strtoupper($module['system_name']).'_CSS',PATH_MODULE_CSS.$module['system_name'].'/');
				define('REL_'.strtoupper($module['system_name']).'_CSS',REL_MODULE_CSS.'script/');
				define('PATH_'.strtoupper($module['system_name']).'_TEMPLATECSS',constant('PATH_'.strtoupper($module['system_name']).'_CSS').$template);
				define('REL_'.strtoupper($module['system_name']).'_TEMPLATECSS',constant('REL_'.strtoupper($module['system_name']).'_CSS').$template);
				define('PATH_'.strtoupper($module['system_name']).'_THEMECSS',constant('PATH_'.strtoupper($module['system_name']).'_TEMPLATECSS').$theme);
				define('REL_'.strtoupper($module['system_name']).'_THEMECSS',constant('REL_'.strtoupper($module['system_name']).'_TEMPLATECSS').$theme);
			}
		}
		define('STATUS_0',	'declined');
		define('STATUS_1',	'active');
		define('STATUS_2',	'new');
		define('STATUS_3',	'pre-approved');
/* == */	
		define('CI_CURRENT_LANGUAGE', 'english');
		define('NOT_APPLICABLE',	'N/A');
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
		$msg = str_replace('<PLUGIN>', '<b>' . $plugin . '</b>', '<div class="msg-error">'.NO_PLUGIN.'</div>');
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