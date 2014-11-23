<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package Cosmic 2
	Global Model: core\MY_Loader.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
/**
 * Extended loader
 */
class MY_Loader extends CI_Loader {
	
	var $ui = NULL;
	var $device = NULL;
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
	 function __construct() {
		parent::__construct();
		$this->_ci_is_php5 = (floor(phpversion()) >= 5) ? TRUE : FALSE;
		$this->_plugin_ci_view_path = APPPATH.'plugins/';
		$this->_module_ci_view_path = APPPATH.'modules/';
		$this->_widget_ci_view_path = MODULESPATH;
		
		
		
		
		$this->_dashboard_ci_view_path = MODULESPATH.'dashboard/';
		$this->_dashboard_plugin_ci_view_path = MODULESPATH.'dashboard/plugins/';
		$this->_gearbox_ci_view_path = MODULESPATH.'gearbox/';
		$this->_gearbox_plugin_ci_view_path = MODULESPATH.'gearbox/plugins/';
		$this->_website_ci_view_path = MODULESPATH.'website/';
		$this->_website_plugin_ci_view_path = MODULESPATH.'website/plugins/';
		(!empty($_SESSION['UI'])) ? $this->ui = $_SESSION['UI'] : $this->ui = NULL;
		(!empty($this->ui['device']['ext'])) ? $this->device = $this->ui['device']['ext'] : $this->device = '';
		if($this->ui['selected_device'] !== false) {
			 $this->device = $this->ui['selected_device'];
		}
		$this->template = $this->ui['path']['template_system_path'];
	}
	
	/* MODEL LOADERS */
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
	function plugin_model( $obj, $model, $name = '', $db_conn = FALSE){ 
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
		if ( ! file_exists(APPPATH.'plugins/'.$current_plugin.'/models/'.$path.$model.EXT)) {
			show_error('Unable to locate the model you have specified: '.$model.'  '.APPPATH.'plugins/'.$current_plugin.'/models/'.$path.$model.EXT);
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
		require_once(APPPATH.'plugins/'.$current_plugin.'/models/'.$path.$model.EXT);
		$model = ucfirst($model);
		$obj->$name = new $model();
		$obj->$name->_assign_libraries();
		$this->_ci_models[] = $name;	
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
	function module_model( $obj, $module='SITE', $model, $name = '', $db_conn = FALSE){ 
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
		if ( ! file_exists(MODULESPATH.strtolower($module).'/models/'.$path.$model.EXT)) {
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
		require_once(MODULESPATH.strtolower($module).'/models/'.$path.$model.EXT);
		$model = ucfirst($model);
		$obj->$name = new $model();
		$obj->$name->_assign_libraries();
		$this->_ci_models[] = $name;	
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
	function widget_model( $obj, $module='SITE', $model, $name = '', $db_conn = FALSE){ 
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
		if ( ! file_exists(MODULESPATH.strtolower($module).'/widgets/'.$current_widget.'/models/'.$path.$model.EXT)) {
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
		require_once(MODULESPATH.strtolower($module).'/plugins/'.$current_widget.'/models/'.$path.$model.EXT);
		$model = ucfirst($model);
		$obj->$name = new $model();
		$obj->$name->_assign_libraries();
		$this->_ci_models[] = $name;	
	}
	/* VIEW LOADERS */
	/* END MODEL LOADERS */
	/**
	 * Loads plugin view
	 * 
	 * @param Object calling object
	 * @param String view name
	 * @param Array data
	 * @param Bool return output
	 */
	function plugin_view($obj, $view, $vars = array(), $return = FALSE) {
		return $this->_plugin_ci_load($obj,array('_ci_view' => $this->template.$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}
	/**
	 * Loads plugin 
	 * 
	 * @return void
	 */
	function _plugin_ci_load($obj,$_ci_data) {
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
	 * Loads dashboard view
	 * 
	 * @param Object calling object
	 * @param String view name
	 * @param Array data
	 * @param Bool return output
	 */
	function module_view($ui, $view, $vars = array(), $return = FALSE) {
		return $this->_module_ci_load($ui, array('_ci_view' => $ui['path']['template_system_path'].$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}
	/**
	 * Loads dashboard 
	 * 
	 * @return void
	 */
	function _module_ci_load($ui, $_ci_data) {
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
	 * Loads plugin view
	 * 
	 * @param Object calling object
	 * @param String view name
	 * @param Array data
	 * @param Bool return output
	 */
	function widget_view($obj, $ui, $view, $vars = array(), $return = FALSE) {
		return $this->_widget_ci_load($obj, $ui, array('_ci_view' => $ui['path']['template_system_path'].$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}
	/**
	 * Loads plugin 
	 * 
	 * @return void
	 */
	function _widget_ci_load($obj, $ui, $_ci_data) {
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
			$_ci_path = $this->_widget_ci_view_path.strtolower($ui['module']).'/'.$current_widget.'/views/'.$_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = $this->_widget_ci_view_path.strtolower(strtolower($ui['module'])).'/'.$current_widget.'/views/'.$_ci_d_file;
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
	 * Loads dashboard model
	 * 
	 * @param Object calling object
	 * @param String model name
	 * @param String optional model name
	 * @param Bool db connection
	 * 
	 * @return void
	 */
	function dashboard_model( $obj, $model, $name = '', $db_conn = FALSE){ 
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
		if ( ! file_exists(MODULESPATH.'dashboard/models/'.$path.$model.EXT)) {
			show_error('Unable to locate the model you have specified: '.$model.'  '.MODULESPATH.'dashboard/models/'.$path.$model.EXT);
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
		require_once(MODULESPATH.'dashboard/models/'.$path.$model.EXT);
		$model = ucfirst($model);
		$obj->$name = new $model();
		$obj->$name->_assign_libraries();
		$this->_ci_models[] = $name;	
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
	function dashboard_plugin_model( $obj, $model, $name = '', $db_conn = FALSE){ 
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
		if ( ! file_exists(MODULESPATH.'dashboard/plugins/'.$current_plugin.'/models/'.$path.$model.EXT)) {
			show_error('Unable to locate the model you have specified: '.$model.'  '.MODULESPATH.'dashboard/plugins/'.$current_plugin.'/models/'.$path.$model.EXT);
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
		require_once(MODULESPATH.'dashboard/plugins/'.$current_plugin.'/models/'.$path.$model.EXT);
		$model = ucfirst($model);
		$obj->$name = new $model();
		$obj->$name->_assign_libraries();
		$this->_ci_models[] = $name;	
	}
	function gearbox_model( $obj, $model, $name = '', $db_conn = FALSE){ 
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
		if ( ! file_exists(MODULESPATH.'gearbox/models/'.$path.$model.EXT)) {
			show_error('Unable to locate the model you have specified: '.$model.'  '.MODULESPATH.'gearbox/models/'.$path.$model.EXT);
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
		require_once(MODULESPATH.'gearbox/models/'.$path.$model.EXT);
		$model = ucfirst($model);
		$obj->$name = new $model();
		$obj->$name->_assign_libraries();
		$this->_ci_models[] = $name;	
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
	function gearbox_plugin_model( $obj, $model, $name = '', $db_conn = FALSE){ 
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
		if ( ! file_exists(MODULESPATH.'gearbox/plugins/'.$current_plugin.'/models/'.$path.$model.EXT)) {
			show_error('Unable to locate the model you have specified: '.$model.'  '.MODULESPATH.'gearbox/plugins/'.$current_plugin.'/models/'.$path.$model.EXT);
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
		require_once(MODULESPATH.'gearbox/plugins/'.$current_plugin.'/models/'.$path.$model.EXT);
		$model = ucfirst($model);
		$obj->$name = new $model();
		$obj->$name->_assign_libraries();
		$this->_ci_models[] = $name;	
	}
	function website_model( $obj, $model, $name = '', $db_conn = FALSE){ 
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
		if ( ! file_exists(MODULESPATH.'website/models/'.$path.$model.EXT)) {
			show_error('Unable to locate the model you have specified: '.$model.'  '.MODULESPATH.'website/models/'.$path.$model.EXT);
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
		require_once(MODULESPATH.'website/models/'.$path.$model.EXT);
		$model = ucfirst($model);
		$obj->$name = new $model();
		$obj->$name->_assign_libraries();
		$this->_ci_models[] = $name;	
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
	function website_plugin_model( $obj, $model, $name = '', $db_conn = FALSE){ 
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
		if ( ! file_exists(MODULESPATH.'website/plugins/'.$current_plugin.'/models/'.$path.$model.EXT)) {
			show_error('Unable to locate the model you have specified: '.$model.'  '.MODULESPATH.'website/plugins/'.$current_plugin.'/models/'.$path.$model.EXT);
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
		require_once(MODULESPATH.'website/plugins/'.$current_plugin.'/models/'.$path.$model.EXT);
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
	function dashboard_view($obj, $view, $vars = array(), $return = FALSE) {
		return $this->_dashboard_ci_load($obj,array('_ci_view' => $this->template.$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}
	/**
	 * Loads dashboard 
	 * 
	 * @return void
	 */
	function _dashboard_ci_load($obj,$_ci_data) {
		// Set the default data variables
		foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val) {
			$$_ci_val = ( ! isset($_ci_data[$_ci_val])) ? FALSE : $_ci_data[$_ci_val];
		}
		// Set the path to the requested file
		if ($_ci_path == '') {
			$_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
			$_ci_file = ($_ci_ext == '') ? $_ci_view.EXT : $_ci_view;
			$_ci_path = $this->_dashboard_ci_view_path.'views/'.$_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = $this->_dashboard_ci_view_path.'views/'.$_ci_d_file;
		
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
	 * Loads plugin view
	 * 
	 * @param Object calling object
	 * @param String view name
	 * @param Array data
	 * @param Bool return output
	 */
	function dashboard_plugin_view($obj, $view, $vars = array(), $return = FALSE) {
		return $this->_dashboard_plugin_ci_load($obj,array('_ci_view' => $this->template.$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}
	/**
	 * Loads plugin 
	 * 
	 * @return void
	 */
	function _dashboard_plugin_ci_load($obj,$_ci_data) {
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
			$_ci_path = $this->_dashboard_plugin_ci_view_path.$current_plugin.'views/'.$_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = $this->_dashboard_plugin_ci_view_path.$current_plugin.'views/'.$_ci_d_file;
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
	 * Loads gearbox view
	 * 
	 * @param Object calling object
	 * @param String view name
	 * @param Array data
	 * @param Bool return output
	 */
	function gearbox_view($obj, $view, $vars = array(), $return = FALSE) {
		return $this->_gearbox_ci_load($obj,array('_ci_view' => $this->template.$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}
	/**
	 * Loads gearbox 
	 * 
	 * @return void
	 */
	function _gearbox_ci_load($obj,$_ci_data) {
		// Set the default data variables
		foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val) {
			$$_ci_val = ( ! isset($_ci_data[$_ci_val])) ? FALSE : $_ci_data[$_ci_val];
		}
		// Set the path to the requested file
		if ($_ci_path == '') {
			$_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
			$_ci_file = ($_ci_ext == '') ? $_ci_view.EXT : $_ci_view;
			$_ci_path = $this->_gearbox_ci_view_path.'views/'.$_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = $this->_gearbox_ci_view_path.'views/'.$_ci_d_file;
		
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
	 * Loads plugin view
	 * 
	 * @param Object calling object
	 * @param String view name
	 * @param Array data
	 * @param Bool return output
	 */
	function gearbox_plugin_view($obj, $view, $vars = array(), $return = FALSE) {
		return $this->_gearbox_plugin_ci_load($obj,array('_ci_view' => $this->template.$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}
	/**
	 * Loads gearbox plugin 
	 * 
	 * @return void
	 */
	function _gearbox_plugin_ci_load($obj,$_ci_data) {
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
			$_ci_path = $this->_gearbox_plugin_ci_view_path.$current_plugin.'/views/'.$_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = $this->_gearbox_plugin_ci_view_path.$current_plugin.'/views/'.$_ci_d_file;
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
	 * Loads website view
	 * 
	 * @param Object calling object
	 * @param String view name
	 * @param Array data
	 * @param Bool return output
	 */
	function website_view($obj, $view, $vars = array(), $return = FALSE) {
		return $this->_website_ci_load($obj,array('_ci_view' => $this->template.$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}
	/**
	 * Loads dashboard 
	 * 
	 * @return void
	 */
	function _website_ci_load($obj,$_ci_data) {
		// Set the default data variables
		foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val) {
			$$_ci_val = ( ! isset($_ci_data[$_ci_val])) ? FALSE : $_ci_data[$_ci_val];
		}
		// Set the path to the requested file
		if ($_ci_path == '') {
			$_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
			$_ci_file = ($_ci_ext == '') ? $_ci_view.EXT : $_ci_view;
			$_ci_path = $this->_website_ci_view_path.'/views/'.$_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = $this->_website_ci_view_path.'/views/'.$_ci_d_file;
		
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
	 * Loads plugin view
	 * 
	 * @param Object calling object
	 * @param String view name
	 * @param Array data
	 * @param Bool return output
	 */
	function website_plugin_view($obj, $view, $vars = array(), $return = FALSE) {
		return $this->_website_plugin_ci_load($obj,array('_ci_view' => $this->template.$view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}
	/**
	 * Loads plugin 
	 * 
	 * @return void
	 */
	function _website_plugin_ci_load($obj,$_ci_data) {
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
			$_ci_path = $this->_website_plugin_ci_view_path.$current_plugin.'/views/'.$_ci_file;
		}
		else {
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		$_ci_d_file = ($_ci_ext == '') ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = $this->_website_plugin_ci_view_path.$current_plugin.'/views/'.$_ci_d_file;
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
	/* END VIEW LOADERS */
}
