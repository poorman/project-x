<?php  if( !defined( 'BASEPATH' )) exit( 'No direct script access allowed' );
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
	var $supercontroller = 'ui';
	var $default_module = 'www';
	private $default_content = DEFAULT_CONTENT;
	private $default_function = DEFAULT_FUNCTION;
	var $_template = 'default/';
	var $modules_ci_path = '';
	var $plugins_ci_path = '';
	private $shell_view = DEFAULT_SHELL_VIEW;
	private $out = NULL; //output array
	protected $ajaxload = false;
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->_ci_is_php5 = ( floor( phpversion() ) >= 5 ) ? TRUE : FALSE;
		!empty( $_SESSION['UI'] ) ? $this->ui = $_SESSION['UI'] : $this->ui = NULL;
		!empty( $this->ui['device']['ext'] ) ? $this->device = $this->ui['device']['ext'] : $this->device = '';
		if ( $this->ui['selected_device'] !== false ) {
			 $this->device = $this->ui['selected_device'];
		}
		!empty( $this->ui['module'] ) ? define( 'MODULE', $this->ui['module'] ) : define( 'MODULE', $this->default_module );
		define( 'SELECTED_DEVICE', $this->device );
		$this->_template = ( $this->ui['path']['template_system_path'] ) ? $this->ui['path']['template_system_path'] : $this->_template;
		$this->modules_ci_path = REL_APPLICATION.'modules/';
		$this->components_ci_path = REL_APPLICATION.'components/';
	}

	/**
	 * superloader
	 *
	 * @param array
	 *
	 * @return array || string
	 *
	 * global echo call should bypass all function calls in this function(this note is for non-ajax calls only)
	 * if no segments set then it is  a call to function within this controller, this call is echo only
	 * segment 1 will either be name of function in this controller<br />
	 * or it will become collection of names for
	 * controller and function within one of the components
	 * call maybe ajax request or echo
	 */
	function loader( &$ui )
	{
		$ci =& get_instance();
		/*
			If active_view not set, we need to reload all elements down to shell
		*/
		$active_view = isset($_SESSION['active_view']) ? $_SESSION['active_view'] : false;
//qe($active_view);
		if(!$active_view) {
			$_SESSION['active_view'] = 1;
		}
		/*
			check for existing session
		*/
		if ( !empty( $_SESSION['UI'] ) ) {
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
		extract( $this->host = $this->host() );
		$ui['module'] = $module;
		$ui['scheme'] = $scheme;
		$ui['domain'] = $domain;
		$ui['extension'] = $extension;
		$ui['url'] = $url;
		$ui['base_url'] = $base_url;
		
		/*
			setting current view mode
		*/
		if ( !empty( $_SESSION['module'] ) && $_SESSION['module'] == $ci->uri->segment( 2 ) ) {
			$_SESSION['widget'] = $ci->uri->segment( 3 );
			if( isset( $_SESSION['component'] ) ) {
				unset( $_SESSION['component'] );
			}
		}
		else {
			if ( $ci->uri->segment( 2 ) ) {
				$_SESSION['component'] = $ci->uri->segment( 2 );
			}
			if ( isset( $_SESSION['widget'] ) ) {
				unset( $_SESSION['widget'] );
			}
		}
		
		
		/* 
			Make sure session exists beyond this point 
		*/
		if ( !empty( $_SESSION['UI'] ) ) {
			$ui = $_SESSION['UI'];
			
			/*
				loads global functions
				i.e. 
				defining paths 
				and 
				GET requests to argunebts passed to ajaxed functions referred as args[]
			*/
			$this->paths( $ui );
			$this->get_request( );
			
			/*
				Loads component definitions
			*/
			$this->language( $ui );
		}
		else {
			
			/*
				creates session with all variables for current user/group
			*/
			$this->client_session( $ui );
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/*
			All following segments convert to parameter($args)
		*/
		$ui['uri'] = array();
		$ui['uri']['params'] = NULL;
		if ( $ci->uri->segment( 3 )  !== FALSE ) {
			$ui['uri']['params'] = array();
			$param = 3;
			$has_params = TRUE;
			while( $has_params ) {
				$ui['uri']['params'][] = $ci->uri->segment( $param );
				$param++;
				if ( $ci->uri->segment( $param ) === FALSE ) {
					$has_params = FALSE;
				}
			}
		}
		$this->default_function = ( $ci->uri->segment( 2 ) ) ? $ci->uri->segment( 2 ) : $this->default_function;
		$controller = false;
		$function = false;

		/* 
			if curl request return as ajax
			$this->out will get set
		*/
		$this->ajaxload = ( $ci->input->post( 'curl ') == SECRET ) ? $this->ajaxload = true : $this->ajaxload;

		/*
			if ajax request return as array
			$this->out will get set
		*/
		$this->ajaxload = ( $ci->input->get( 'ajax' ) || $ci->input->post( 'ajax' ) ) ? $this->ajaxload = true : $this->ajaxload;

		/*
			this is either this constructors own function
			or
			this is plugin name
		*/
		$controller = $ci->uri->segment( 1 ) ? $ci->uri->segment( 1 ) : false;

		/*
			is call global?
			global when $function exists within this class
			or when segment 1 does not exist
		*/
		$global = ( $controller ) ? in_array( $controller, $ui['global_methods'] ) : true;
		if( $ui['module_seo'] && $controller ) {
			/*
				Trying SEO load
			*/
			if( !empty( $ui['links'][$controller] ) ) {
				$controller = $ui['links'][$controller]['action'];
				$global = ( $controller ) ? in_array( $controller, $ui['global_methods'] ) : true;
				if( $ci->uri->segment( 2 ) ) {
					$function = ( !empty( $this->ui['functions'][$ci->uri->segment( 2 ) ]['action'] ) ) ? $ui['functions'][$ci->uri->segment( 2 )]['action'] : $ci->uri->segment( 2 );
				}
				else {
					$function = ( !empty ($ui['links'][$controller]['functionality'] ) ) ? $ui['links'][$controller]['functionality'] : $this->default_function;
				}
			}
			$function = ( $function ) ? $function : $this->default_function;
		}
		else {
			/*
				Non SEO Load
			*/
			$function = ( $ci->uri->segment( 2 ) ) ? $ci->uri->segment( 2 ) : $this->default_function;
		}
		$ui['uri']['controller'] = $controller;
		$ui['uri']['function'] = $function; 
		
		if ( $this->ajaxload ) {
			/*
				this is ajax request
			*/
		if ( $global ) {
				/*
					Global ajax call
				*/
				$this->out = $controller;
			}
		else {
					/*
					component ajax call
				*/
				$this->out = $this->instance($ui, $controller, $function, $ui['uri']['params'], true );
			}
		}
		else {
			if ( !$global ) {
				/*
					echo component
				*/
				if ( $active_view ) {
qe(22);					if ( isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] ) {
						/*
							refresh hits here
							reload shell as well
						*/
						$this->out = $this->load_shell( $ui, $this->instance( $ui, $controller, $function, $ui['uri']['params'], true ) );
					}
					else {
						$this->out = $this->instance( $ui, $controller, $function, $ui['uri']['params'], true );
					}
				}
				else {
					$this->out = $this->load_shell( $ui, $this->instance( $ui, $controller, $function, $ui['uri']['params'], true ) );
				}
			}
		}
		return ( isset($this->out) ) ? $this->out : NULL;
	 }
	 
	 function load_shell($ui, $contents = array()) {
		$output = '';
		$ui['interface'] = NULL;
		$output = $shell = $this->module_view( $this->shell_view, $ui, true );
		
		if( is_array( $contents ) ) {
			foreach ($contents as $tag => $content ) {
				$tag = '<!--{'.$tag.'}-->';
				$output = str_replace( $tag, $content, $output );
				
			}
		}
		else {
			return str_replace( '{interface}', $content, $shell );
	 	}
		return $output;
	 }
	/**
	 *This function is responsible for loading contents by direct url
	 *
	 */
	function load_contents( &$ui=array() ) {
		
		$output = '';
		$shell = '';
		if( empty($ui['uri']['controller'] ) ) {
			/*
				if no controller set, then load shell + default content
			*/
			$ui['interface'] = NULL;
			$output = $this->module_view( $this->shell_view, $ui, true );
			$contents = $this->module_view( $this->default_content, $ui, true );
			foreach ($contents as $tag => $content ) {
				$tag = '{' . $tag . '}';
				$output = str_replace( $tag, $content, $output );
			}
			echo $output;
			exit(0);
		}
		else {
			/*
				controller is set 
			*/
			if ( in_array( $ui['uri']['controller'], $ui['global_methods'] ) ) {
				/* 
					Since this is a global controller there is no processing needed here.
					Global controllers do not load by ajax or need any component/module/widget loads either
					Sending false back to global will simply ignore any other processing, and call that funtion
				*/
				return false;
			}
			
			/*
			
			
			
			
			
			
			
				/*
					controller is set to sub class
					Lets find correct action to take
				*/
				if ( !empty($this->ui['links'][$ui['uri']['controller']] ) ) {
					/*
						Action found in database as collection
					*/
					$action = $this->ui['links'][$ui['uri']['controller']];
				}
				else {
					/* 
						Action was not found in database
					*/
					if( in_array( $this->ui['uri']['controller'],$this->ui['module_controllers'] ) ) {
						/*
							Action found in modules collection
						*/
						$contents = $this->load->load_module( $this->ui, $this->ui['uri']['controller'], $this->ui['uri']['function'], $this->ui['uri']['params']);
						//$module->module();
					}
					else {
						if( in_array( $this->ui['uri']['controller'], $this->ui['component_controllers'] ) ) {
							//load component controller
							$this->content = $this->load->load_component( $this->ui, $this->ui['uri']['controller'], $this->ui['uri']['function'], $this->ui['uri']['params']);
						}
					}
				}
			
			if( !empty( $this->action ) ) {
				$action = $this->action['action'];
				if ( $this->ui['uri']['function'] != $this->default_function )
					$method = $this->ui['uri']['function']; 
				else {
					$method = (!empty ( $this->action['functionality'] ) ) ? $this->action['functionality'] : $this->default_function;
				}
				if ( $this->action['module_id'] ) {
					/* this is module call */
					
				}
				else {
					/* this is component call */
					if ( in_array( $action, $this->ui['component_controllers'] ) ) {
						$component = $this->load->load_component($action,$this->ui['uri']['params']);
						if ( !empty( $method ) ) {
							$this->content = $component->$method($this->ui['uri']['params']);
						}
						else {
							$this->content = $component->$this->default_function($this->ui['uri']['params']);
						}
					}
				}
				if( is_array($this->content)) {
						$script = $content = '';
						foreach( $this->content as $element=>$contents ) {
							if( $element == 'script' ) {
								$script = '<script>' .$contents . '</script>';
							}
							if($element == 'interface') {
								$content = $contents;
							}
						}
						$this->ui['content'] = $script.$content;
					}
			}
			else {
				if ( !empty( $this->content ) ) {
					if( is_array($this->content)) {
						$script = $content = '';
						foreach( $this->content as $element=>$contents ) {
							if( $element == 'script' ) {
								$script = '<script>' .$contents . '</script>';
							}
							if($element == 'interface') {
								$content = $contents;
							}
						}
						$this->ui['content'] = $script.$content;
					}
				}
				else {
					$this->ui['content'] = $this->load->module_view( $this->default_content );
				}
			}
		}
	}



	/*
	* once a component is loaded, assigns the $ci object's properties to the new component object
	*
	* @param object
	* @param bool
	*
	* @return void
	*/
	function assign_libraries( $instance, $use_reference = TRUE )
	{
		$ci =& get_instance(); 
		foreach ( array_keys( get_object_vars( $ci ) ) as $key ) {
			if ( !isset( $instance->$key ) AND $key != $instance->parent_name ) {
				// In some cases using references can cause
				// problems so we'll conditionally use them
				if ( $use_reference == TRUE ) {
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
	function change_language( &$ui,$language )
	{
		$mode = array( 'language' => $language );
		return $this->re_session( $ui, $mode );
	}

	/**
	 * function currently not in use
	 *
	 * @param void;
	 *
	 * @return array
	 */
	function change_template( &$ui, $template )
	{
		$mode = array( 'template' => $template );
		return $this->re_session( $ui, $mode );
	}
	
	/**
	 * function currently not in use
	 *
	 * @param void;
	 *
	 * @return array
	 */
	 
	function change_theme( &$ui, $heme )
	{
		$mode = array( 'theme' => $theme );
		return $this->re_session( $ui, $mode );
	}

	/**
	 * set all client session data
	 *
	 * @param void;
	 *
	 * @return array
	 */
	function client_session( &$ui, $mode = false )
	{
		
		$ci =& get_instance(); 
		$ui['module'] = !empty( $_SESSION['app']['module'] ) ? $_SESSION['app']['module'] : $this->default_module;
		$ui['app'] = !empty( $_SESSION['app'] ) ? $_SESSION['app'] : $this->host();
		if ( !empty( $_SESSION['app'] ) ) {
			unset( $_SESSION['app'] );
		}
		$ui['module_id'] = $this->get_module_id( $ui['module'] );
		$ui['module_seo'] = $seo_enabled = $this->seo_enabled( $ui['module'] );
		$ui['session'] = crc32( $_SERVER['REMOTE_ADDR'].time() ); // unique session
		$ui['session_start'] = time(); // unique session
		$ui['device'] =  $ci->mobile->get_device();
		$ui['selected_device'] = false;
		$ui['groups'] = $this->user_groups();
		foreach ( $ui['groups'] as $group ) {
			define( 'GROUP_' . strtoupper( $group['system_name'] ), $group['group_id'] );
		}
		$ui['group_id'] = GROUP_GUEST;
		$ui['path'] = array();
		$ui['modules'] = $this->modules();
		$ui['components'] = $this->components();
		
		$ui['widgets'] = $this->widgets();
		$ui['user_modules'] = $this->user_modules( $ui );
		$ui['user_components'] = $this->user_components( $ui );
		$ui['user_widgets'] = $this->user_widgets( $ui );
		
		$ui['module_controllers'] = $this->module_controllers( $ui['module'] );
		$ui['component_controllers'] = $this->component_controllers( $ui['user_components'] );
		$ui['widget_controllers'] = $this->widget_controllers( $ui['user_widgets'] );
		$ui['module_seo'] = $seo_enabled = $this->seo_enabled( $ui['module_id'], $ui['modules'] );
	/* Set language data */
		if ($ci->config->item( 'language' )) {
			$ui['language'] = $ci->language_model->get_language( $ci->config->item( 'language' ) );
		}
		else {
			$ui['language'] = $ci->language_model->get_language( $ui );
		}
		$ui['languages'] = $ci->language_model->fetch_indexed_array_of_languages();
		/* Set template data */
		$ui['template'] = $ci->template_model->get_template();
		$ui['templates'] = $ci->template_model->fetch_indexed_array_of_templates( $ui['module_id'] );
		if ( isset( $ui['template'] )&& count( $ui['template'] ) ) {
			/* Set theme data */
			$ui['theme'] = $ci->theme_model->get_theme( $ui['template']['template_id'] );
			$ui['themes'] = $ci->theme_model->fetch_indexed_array_of_themes( $ui['template']['template_id'] );
			/* Set template paths */
			if ( $ui['template']['system_name'] != '' ) {
				$ui['path']['template_system_path'] =  $ui['template']['system_name']. '/';
				$ui['template_system_name'] =  $ui['template']['system_name'];
			}
			else {
				$ui['path']['template_system_path'] = $ui['template_system_name'] = '';
			}
			/* Set theme paths */
			if ( isset( $ui['theme'] )&& count( $ui['theme'] ) ) {
				if ( $ui['theme']['system_name'] != '' ) {
					$ui['path']['theme_system_path'] = $ui['theme']['system_name']. '/';
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
		
		$this->paths( $ui );
		/* set url to links collection*/
		//$this->links($ui);
		( $seo_enabled ) ? $this->links( $ui ) : $ui['links'] = false;
		$this->get_request();
		$this->ui = $ui;
		$this->ui['components'] = $ui['components'];
		$this->ui['widgets'] = $ui['widgets'];
		$this->ui['user_components'] = $ui['user_components'];
		$this->ui['user_widgets'] = $ui['user_widgets'];
		$this->language( $ui );//loads component definitions
		$_SESSION['UI'] = $ui;
		
	}
	
	/**
	 * call component load then check loaded data
	 *
	 * @Param void
	 *
	 *@Return void
	 */
	function component( $component = NULL, $function = NULL, $args = NULL )
	{
		if ( !$component = $this->load_component( $component ) ) {
			return false;
		}
		$function = $function ? $function : 'index';
		return $component->$function( $args );
	}
	
	/**
	 * Loads component 
	 * 
	 * @return void
	 */
	function component_ci_load( $obj,$_ci_data )
	{
		// get info about calling class so that we can get the name and therefore the directory to build paths
		$current_component = strtolower( $obj->parent_name );
		// Set the default data variables
		foreach ( array( '_ci_view', '_ci_vars', '_ci_path', '_ci_return' ) as $_ci_val ) {
			$$_ci_val = ( !isset( $_ci_data[$_ci_val] ) ) ? FALSE : $_ci_data[$_ci_val];
		}
		// Set the path to the requested file
		
		if ( $_ci_path == '' ) {
			$_ci_ext = pathinfo ( $_ci_view, PATHINFO_EXTENSION );
			$_ci_file = ( $_ci_ext == '' ) ? $_ci_view.EXT : $_ci_view;
			$_ci_path = REL_PUBLIC_COMPONENTS . $current_component . COMPONENT_VIEWS . $_ci_file;
		}
		else {
			$_ci_x = explode( '/', $_ci_path );
			$_ci_file = end( $_ci_x );
		}
		$_ci_d_file = ( $_ci_ext == '' ) ? $_ci_view . $this->device.EXT : $_ci_view;
		$_ci_d_path = REL_PUBLIC_COMPONENTS . $current_component . COMPONENT_VIEWS . $_ci_d_file;
		if ( $this->ui['selected_device'] !== false ) {
			if ( file_exists( $_ci_d_path ) ) {
				$_ci_path = $_ci_d_path;
			}
			else {
				show_error( 'Unable to load the requested file: ' . $_ci_d_file . ' ' . $_ci_d_path );
			}
		}
		else {
			if ( file_exists( $_ci_d_path ) ) {
				$_ci_path = $_ci_d_path;
			}
			else {
				if ( !file_exists( $_ci_path ) ) {
					show_error( 'Unable to load the requested file: ' . $_ci_file . ' ' . $_ci_path );
				}
			}
		}
		// This allows anything loaded using $this->load(views, files, etc.)
		// to become accessible from within the Controller and Model functions.
		// Only needed when running PHP 5

		if ( $this->_ci_is_instance() ) {
			$_ci_CI =& get_instance();
			foreach ( get_object_vars( $_ci_CI ) as $_ci_key => $_ci_var) {
				if ( !isset( $this->$_ci_key ) ) {
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
		if ( is_array( $_ci_vars ) ) {
			$this->_ci_cached_vars = array_merge( $this->_ci_cached_vars, $_ci_vars );
		}
		extract( $this->_ci_cached_vars );
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
		if ( ( bool ) @ini_get( 'short_open_tag' ) === FALSE AND config_item ( 'rewrite_short_tags' ) == TRUE) {
			echo eval( '?>' . preg_replace( "/;*\s*\?>/", "; ?>", str_replace( '<?=', '<?php echo ', file_get_contents( $_ci_path ) ) ) );
		}
		else {
			include( $_ci_path ); // include() vs include_once() allows for multiple views with the same name 
		}
		// Return the file data if requested
		if ( $_ci_return === TRUE ) {
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
		if ( ob_get_level() > $this->_ci_ob_level + 1 ) {
			ob_end_flush();
		}
		else {
			// PHP 4 requires that we use a global
			global $OUT;
			$OUT->append_output( ob_get_contents() );
			@ob_end_clean();
		}
	}
	
	/*
	* creating array of component controllers with associated key of id
	*
	* @param array
	*
	* @return array
	*/
	function component_controllers( $components ) {
		$component_controllers = array();
		foreach( $components as $component ) {
			$component_controllers[$component['component_id']] = $component['system_name'];
		}
		return $component_controllers;
	}
	/**
	 * Loads component model
	 * 
	 * @param Object calling object
	 * @param String model name
	 * @param String optional model name
	 * @param Bool db connection
	 * 
	 * @return void
	 */
	function component_model( $obj, $model, $name = '', $db_conn = FALSE )
	{
	// get info about calling class so that we can get the name and therefore the directory to build paths
		$current_component = strtolower( $obj->parent_name );
		if  (is_array( $model ) ) {
			foreach ( $model as $babe ) {
				$this->model( $babe );	
			}
			return;
		}
		if ( $model == '' ) {
			return;
		}
		// Is the model in a sub-folder? If so, parse out the filename and path.
		if ( strpos( $model, '/' ) === FALSE ) {
			$path = '';
		}
		else {
			$x = explode( '/', $model );
			$model = end( $x );
			unset( $x[count( $x ) -1] );
			$path = implode( '/', $x ). '/';
		}

		if ( $name == '' ) {
			$name = $model;
		}
		if ( in_array( $name, $this->_ci_models, TRUE ) ) {
			return;
		}
		if ( isset( $obj->$name ) ) {
			show_error( 'The model name you are loading is the name of a resource that is already being used: ' . $name );
		}
		$model = strtolower( $model );
		if ( !file_exists( REL_COMPONENTS . $current_component . '/models/' . $path.$model.EXT ) ) {
			show_error( 'Unable to locate the model you have specified: ' .$model . '  ' . REL_COMPONENTS . $current_component . '/models/' . $path . $model . EXT);
		}
		if ( $db_conn !== FALSE AND !class_exists( 'CI_DB' ) ) {
			if ( $db_conn === TRUE ) {
				$db_conn = '';
			}
			$obj->load->database( $db_conn, FALSE, TRUE );
		}
		if ( !class_exists( 'Model' ) ) {
			load_class( 'Model', FALSE );
		}		
		require_once( REL_COMPONENTS . $current_component . '/models/' . $path . $model . EXT );
		$model = ucfirst( $model );
		$obj->$name = new $model();
		$obj->$name->_assign_libraries();
		$this->_ci_models[] = $name;
	}
	
	/**
	 * Loads component view
	 * 
	 * @param Object calling object
	 * @param String view name
	 * @param Array data
	 * @param Bool return output
	 */
	function component_view( $obj, $view, $vars = array(), $return = FALSE )
	{
		return $this->component_ci_load( $obj,array( '_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array( $vars ), '_ci_return' => $return ) );
	}
	

	/**
	 * function returns components data array
	 *
	 * @Param int(language id)
	 *
	 * @return array
	 */
	function components()
	{
		$ci =& get_instance();
		$ci->db->from( 'components as c' );
		$ci->db->join( 'component_structure as s', 's.component_id = c.component_id','left' );
		$query = $ci->db->get();
		return $query->result_array();
	}
	
	
	/**
	 * gets module by id
	 *
	 * @param string
	 *
	 * @return array
	 */
	function get_module_by_id( $module_id )
	{
		$ci =& get_instance();
		$ci->db->where( 'module_id', $module_id );
		$ci->db->limit( 1 );
		$query = $ci->db->get( 'modules' );
		return $query->row_array();
	}

	/**
	 * gets module_id by name
	 *
	 * @param string
	 *
	 * @return int
	 */
	function get_module_id( $module )
	{
		$ci =& get_instance();
		$ci->db->where( 'system_name', strtolower( $module ) );
		$ci->db->limit( 1 );
		$query = $ci->db->get( 'modules' );
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
		if ( strpos( $_SERVER['REQUEST_URI'], '?' ) ) {
			$new_get_data = array();
			$arr = $_SERVER['REQUEST_URI'];
			$arr = explode( '?' , $arr );
			$get = $arr[1];
			$get_data = explode( '&' , $get );
			foreach ( $get_data as $data_item ) {
				if ( $data_item ) {
					$temp = explode( '=' , $data_item );
					$key = $temp[0];
					$value = $temp[1];
					$_GET[$key] = $ci->security->xss_clean( $value );
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
		$module = array_shift( ( explode( "." , $_SERVER['SERVER_NAME'] ) ) );
		$extension = pathinfo ( $url, PATHINFO_EXTENSION );
		$parsed_url = parse_url( $url );
		if ( !empty( $parsed_url['host'] ) ) {
			$parts = explode( '.', $parsed_url['host'] );
		}
		else {
			$parts = explode( '.', $url );
		}
		
		$domain = $parts[1];
		if ( empty( $parsed_url['scheme'] ) ) {
			$base_url = 'http://' . ltrim( $url, '/' ) . '/';
			$scheme = 'http://';
		}
		else {
			$scheme = $parsed_url['scheme'];
		}
		return array( 'scheme' => $scheme, 'module' => $module, 'domain' => $domain, 'extension' => $extension, 'url' => $url, 'base_url' => $base_url );
	}

	/** loads instance
	 *
	 * @param array
	 *
	 * @return void
	 */
	function instance( &$ui, $action, $function, $params, $return=false )
	{
		$function = ( $function ) ? $function : $this->default_function;
		
		
		if ( in_array( $action,$ui['component_controllers'] ) ) {
			
			$out = $this->component( $action, $function, $params,true );
		}
		else {
			/*
				Load action through module
			*/
			if ( in_array( $action,$ui['module_controllers'] ) ) {
				/* 
					Load module controller
				*/
				$out = $this->module( $ui, $action, $function, $params );
			}
			else {
				if ( in_array( $action,$ui['widget_controllers'] ) ) {
					/* 
					Load widget
					*/
					$out = $this->widget( $action, $function, $params,true );
				}
			}
		}
		return $out;
	}
	/** loads language definition files for globals, components,  modules, widgets 
	 *
	 * @param array
	 *
	 * @return void
	 */
	function language( $ui = false )
	{
		if ( empty( $ui['language']['language'] ) ) {
			return false;
		}
		/* load default global language file */
		require_once( REL_APPLICATION . 'language/' . $ui['language']['language'] . '/default.php' );
		/* load default module language file */
		require_once( REL_APPLICATION . 'language/' . $ui['language']['language'] . '/modules/' . $ui['module'] . '/default.php' );
		/* load language files for all user  components*/
		if ( !empty( $ui['user_components'] ) && count( $ui['user_components'] ) ) {
			foreach ( $ui['user_components'] as $component ) {
				if ( file_exists( REL_APPLICATION . 'language/' . $ui['language']['language'] . '/' . $component['system_name'] . '.php' ) ) {
					require_once( REL_APPLICATION . 'language/' . $ui['language']['language'] . '/' . $component['system_name'] . '.php' );
				}
			}
		}
		/* load language files for all user widgets */
		if ( !empty( $ui['user_widgets']) && count( $ui['user_widgets'] ) ) {
			foreach ( $ui['user_widgets'] as $widget ) {
				if ( file_exists( REL_APPLICATION . 'language/' . $ui['language']['language'] . '/modules/'.$ui['module'] . '/' . $widget['system_name'] . '.php' ) ) {
					require_once( REL_APPLICATION . 'language/' . $ui['language']['language'] . '/modules/' . $ui['module'] . '/' . $widget['system_name'] . '.php' );
				}
			}
		}
	}	

	/**
		Creates a collection 
	*/
	function links( &$ui )
	{
		$ci =& get_instance();
		$links = array();
		$ci->db->where( 'flag_active', 1 );
		$query = $ci->db->get( 'links' );
		if ( $query->num_rows() ) {
			$result = $query->result();
			foreach ($result as $link) {
				if ( $link->is_method ) {
					$functions[$link->link] = array(
													'module_id' => $link->module_id,
													'action' => $link->action
												);
				}
				else {
					$links[$link->link] = array(
													'parent_id' => $link->parent_id,
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
	function load_module( $ui, $class=false, $function=false, $all_params = NULL )
	{
		$ci =& get_instance(); 
		static $instances = array();
		$parent = 0;
		/*
			make sure user has access to this module
		*/
		if( in_array( $ui['module'],$ui['module_controllers'] ) ) {
			$module = $ui['module'];
			$module_id = $ui['module_id'];
			$params = array();
			if ( $all_params ) {
				foreach ( $all_params as $param ) {
					if ( !$class ) {
						$class = $param;
					}
					else {
						if ( !$function ) { //function is not used
							$function = $param;
						}
						else {
							$params[] = $param;
						}
					}
				}
			}
			else {
				if ( ! $class ) {
					$class = $module;
				}
			}
			$instance_name = $module . $module_id . $class;
			// see if there already is an instance of the component
			
			if ( !array_key_exists( $instance_name, $instances ) ) {
				// instance does not exist, so create it
				if ( !class_exists( $class ) ) {
					include_once( REL_MODULE_CONTROLLERS . $class . '.php' );
				}
				$instance = new $class( $params );
				$instance->parent_name = ucfirst( get_class( $instance ) ); 
				$instance->id = $module_id . $class;
				$this->assign_libraries( $instance, TRUE );
				$instances[$instance_name] =& $instance;
			}
			return  $instances[$instance_name];
		}
		return false;
	}
	
	/**
	 * load component by checking if component registered, if user allowed, if files exist
	 *
	 *@param string
	 *@param array
	 *
	 *@return void
	 */
	function load_component( $component = NULL, $params = NULL )
	{
		$ci =& get_instance(); 
		static $instances = array();
		$parent = 0;
		// make sure this component is registered
		$ci->db->where( 'name', $component );
		$ci->db->limit( 1 );
		$query = $ci->db->get( 'components' );
		if ( !$row = $query->row() ) {
			return FALSE;
		}
		$instance_id = $row->component_id;
		$instance_name = $component . $instance_id;
		// see if there already is an instance of the component
		if ( !array_key_exists( $instance_name, $instances ) ) {
			if ( !class_exists( $component ) ) {
				// instance does not exist, so create it
				include_once( REL_COMPONENTS . $component . '/controller.php' );
			}
			$instance = new $component( $params );
			$instance->parent_name = ucfirst( get_class( $instance ) ); 
			$instance->id = $instance_id;
			$this->assign_libraries( $instance, TRUE );
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
	function load_widget( $widget = NULL, $params = NULL )
	{
		$ci =& get_instance(); 
		static $instances = array();
		$parent = 0;
		isset( $params['module'] ) ? $module = $params['module'] : $module = $this->ui['module'];
		// make sure this component is registered
		$ci->db->where( 'name', $widget );
		$ci->db->limit( 1 );
		$query = $ci->db->get( 'widgets' );
		if ( !$row = $query->row() ) {
			return FALSE;
		}
		$instance_id = $row->widget_id;
		$instance_name = $widget . $instance_id;
		// see if there already is an instance of the component
		if ( !array_key_exists( $instance_name, $instances ) ) {
			// instance does not exist, so create it
			if( !class_exists( $widget ) ) {
				include_once( REL_APPLICATION . 'modules/' . $module . '/widgets/' . $widget . '/controller.php' );
			}
			$instance = new $widget( $params );
			$instance->parent_name = ucfirst( get_class( $instance ) ); 
			$instance->id = $instance_id;
			$this->assign_libraries( $instance, TRUE );
			$instances[$instance_name] =& $instance;
		}
		return  $instances[$instance_name];
	}
	 
	/**
	 * check and load component
	 *
	 *@Param void
	 *
	 *@Return void
	 */
	function module( $ui, $module=false,  $function = NULL, $params = NULL ) {
		if ( !$module = $this->load_module( $ui, $module, $function, $params ) ) {
			return false;
		}
		$function = $function ? $function : 'index';
		return $module->$function( $params );
	}
	
	/**
	 * Loads module 
	 * 
	 * @return void
	 */
	function module_ci_load( $ui, $_ci_data )
	{
		// Set the default data variables
		foreach ( array( '_ci_view', '_ci_vars', '_ci_path', '_ci_return' ) as $_ci_val ) {
			$$_ci_val = ( !isset( $_ci_data[$_ci_val] ) ) ? FALSE : $_ci_data[$_ci_val];
		}
		// Set the path to the requested file
		if ( $_ci_path == '' ) {
			$_ci_ext = pathinfo ( $_ci_view, PATHINFO_EXTENSION );
			$_ci_file = ( $_ci_ext == '' ) ? $_ci_view.EXT : $_ci_view;
			$_ci_path = REL_MODULE_VIEWS .$_ci_file;
		}
		else {
			$_ci_x = explode( '/', $_ci_path );
			$_ci_file = end( $_ci_x );
		}
		
		$_ci_d_file = ( $_ci_ext == '' ) ? $_ci_view.$this->device.EXT : $_ci_view;
		$_ci_d_path = REL_MODULE_VIEWS .$_ci_d_file;
		if ( $this->ui['selected_device'] !== false ) {
			if ( file_exists( $_ci_d_path ) ) {
				$_ci_path = $_ci_d_path;
			}
			else {
				show_error( 'Unable to load the requested file: '.$_ci_d_file. ' '.$_ci_d_path );
			}
		}
		else {
			if ( file_exists( $_ci_d_path ) ) {
				$_ci_path = $_ci_d_path;
			}
			else {
				if ( !file_exists( $_ci_path ) ) {
					show_error( 'Unable to load the requested file: '.$_ci_file. ' '.$_ci_path );
				}
			}
		}
		// This allows anything loaded using $this->load(views, files, etc.)
		// to become accessible from within the Controller and Model functions.
		// Only needed when running PHP 5
		if ( $this->_ci_is_instance() ) {
			$_ci_CI =& get_instance();
			foreach ( get_object_vars( $_ci_CI ) as $_ci_key => $_ci_var ) {
				if ( !isset( $this->$_ci_key ) ) {
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
		if ( is_array( $_ci_vars ) ) {
			$this->_ci_cached_vars = array_merge( $this->_ci_cached_vars, $_ci_vars );
		}
		extract( $this->_ci_cached_vars );
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
		if ( ( bool ) @ini_get( 'short_open_tag' ) === FALSE AND config_item ( 'rewrite_short_tags' ) == TRUE ) {
			echo eval( '?>'.preg_replace( "/;*\s*\?>/", "; ?>", str_replace( '<?=', '<?php echo ', file_get_contents( $_ci_path ) ) ) );
		}
		else {
			include( $_ci_path ); // include() vs include_once() allows for multiple views with the same name 
		}
		// Return the file data if requested
		if ( $_ci_return === TRUE ) {
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
		if ( ob_get_level() > $this->_ci_ob_level + 1 ) {
			ob_end_flush();
		}
		else {
			// PHP 4 requires that we use a global
			global $OUT;
			$OUT->append_output( ob_get_contents() );
			@ob_end_clean();
		}
	}
	
	/*
	* Creates array of available controllers by scanning the directory
	* Every php file in scanned location is treated as controller
	*
	* @param string
	*
	* @return array
	*/
	function module_controllers( $module )
	{
		$module_dir = REL_APPLICATION . 'modules/' . $module . '/controllers/';
		$controller_files = scandir( $module_dir );
		$controllers = array();
		foreach ( $controller_files as $c_file ) {
			if ( strpos( $c_file,'.php' ) ) {
				$controllers[] = str_replace( '.php', '', $c_file );
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
	function module_model( $obj, $module = 'WWW', $model, $name = '', $db_conn = FALSE)
	{
		// get info about calling class so that we can get the name and therefore the directory to build paths
		if ( is_array( $model ) ) {
			foreach ( $model as $babe ) {
				$this->model( $babe );	
			}
			return;
		}
		if ( $model == '' ) {
			return;
		}
		// Is the model in a sub-folder? If so, parse out the filename and path.
		if ( strpos( $model, '/' ) === FALSE ) {
			$path = '';
		}
		else {
			$x = explode( '/' , $model );
			$model = end( $x );
			unset( $x[ count( $x ) -1] );
			$path = implode( '/', $x ) . '/';
		}

		if ( $name == '' ) {
			$name = $model;
		}
		if ( in_array($name, $this->_ci_models, TRUE ) ) {
			return;
		}
		if ( isset( $obj->$name ) ) {
			show_error( 'The model name you are loading is the name of a resource that is already being used: '.$name);
		}
		$model = strtolower( $model );
		if ( !file_exists( $this->modules_ci_path . strtolower( $module ) . '/models/' . $path.$model.EXT ) ) {
			show_error( 'Unable to locate the model you have specified: ' . $model . '  ' . MODULESPATH . strtolower($module) . '/models/' . $path . $model . EXT );
		}
		if ( $db_conn !== FALSE AND !class_exists( 'CI_DB' ) ) {
			if ( $db_conn === TRUE ) {
				$db_conn = '';
			}
			$obj->load->database( $db_conn, FALSE, TRUE );
		}
		if ( !class_exists( 'Model' ) ) {
			load_class( 'Model', FALSE );
		}
		require_once( $this->modules_ci_path . strtolower( $module ) . '/models/' . $path . $model . EXT );
		$model = ucfirst( $model );
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
	function module_view( $view, $vars = array(), $return = FALSE )
	{
		return $this->module_ci_load( $this->ui, array( '_ci_view' => $this->_template . $view, '_ci_vars' => $this->_ci_object_to_array( $vars ), '_ci_return' => $return ) );
	}

	/**
	 * function returns components data array
	 *
	 * @Param int(language id)
	 *
	 * @return array
	 */
	function modules()
	{
		$ci =& get_instance();
		$ci->db->from ( 'modules as m' );
		$ci->db->join( 'module_structure as s', 's.module_id = m.module_id', 'left' );
		$query = $ci->db->get();
		$modules_result = $query->result_array();
		$modules = array();
		foreach ( $modules_result as $module ) {
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
	function paths( $ui = false )
	{
		$ci =& get_instance();
		!empty( $ui['language']['language'] ) ? $language = $ui['language']['language'] : $language = $ci->config->item ( 'language' );
		!empty( $ui['template']['system_name'] ) ? $template = $ui['template']['system_name'] : $template = 'default';
		!empty( $ui['theme']['system_name'] ) ? $theme = $ui['theme']['system_name'] : $theme = 'default';
		
		define( 'APPTEMPLATE', $template );
		define( 'APPTHEME', $theme);
		
		$template .= '/';
		$theme .= '/';
		$scheme = $ui['scheme'];
		$module = $ui['module'];
		$domain = $ui['domain'];
		$extension = $ui['extension'];
		$language = $ui['language']['system_name'];
		
		define( 'HTTP_MODE', str_replace( '://', '', $scheme ) );
		define( 'PRE','#' . $this->controler );
		define( 'JS_PRE', $this->controler );
		define( 'JS_BASE_URL', $scheme . $module . '.' . $domain . '.' . $extension . '/index.php/' );
/* GLOBAL 
Global has no templates, it has languages
=================================================================*/
		define( 'PATH_APPLICATION', base_url() . REL_APPLICATION );
		define( 'PATH_ASSETS', base_url() . 'assets/' );
		define( 'REL_ASSETS', 'assets/' );
		define( 'PATH_PUBLIC', base_url() . 'public/' );
		define( 'REL_PUBLIC', 'public/' );
	//LANGUAGE
		define( 'PATH_LANGUAGES', PATH_APPLICATION . 'language/' );
		define( 'REL_LANGUAGES', REL_APPLICATION . 'language/' );
		define( 'PATH_LANGUAGE', PATH_LANGUAGES . $language. '/' );
		define( 'REL_LANGUAGE', REL_LANGUAGES . $language. '/' );
	//IMAGES
		define( 'PATH_IMAGE', PATH_ASSETS . 'images/' );
		define( 'REL_IMAGE', REL_ASSETS . 'images/' );
		define( 'PATH_LANGUAGE_IMAGE', PATH_ASSETS . 'images/' . $language . '/' );
		define( 'REL_LANGUAGE_IMAGE', REL_ASSETS . 'images/' . $language . '/' );
	//SCRIPT
		define( 'PATH_SCRIPT', PATH_ASSETS . 'script/' );
		define( 'REL_SCRIPT', REL_ASSETS . 'script/' );
	//CSS
		define( 'PATH_CSS',PATH_ASSETS . 'css/' );
		define( 'REL_CSS',REL_ASSETS . 'css/' );
/* COMPONENTS 
Plugins have no templates, they do have languages
=================================================================*/
		define( 'PATH_COMPONENTS', PATH_APPLICATION . 'components/' );
		define( 'REL_COMPONENTS', REL_APPLICATION . 'components/' );
		define( 'PATH_COMPONENTS_ASSETS', PATH_ASSETS . 'components/' );
		define( 'REL_COMPONENTS_ASSETS', REL_ASSETS . 'components/' );
	//LANGUAGE
		define( 'PATH_COMPONENTS_LANGUAGE',PATH_LANGUAGE . 'components/' );
		define( 'REL_COMPONENTS_LANGUAGE', REL_LANGUAGE . 'components/' );
		
		define( 'PATH_PUBLIC_COMPONENTS', PATH_PUBLIC . 'components/' );
		define( 'REL_PUBLIC_COMPONENTS', REL_PUBLIC . 'components/' );
		define( 'COMPONENT_VIEWS', '/views/' );
		define( 'COMPONENT_ASSETS', '/assets/' );
		define( 'COMPONENT_IMAGES', COMPONENT_ASSETS . 'images/' );
		define( 'COMPONENT_CSS', COMPONENT_ASSETS . 'css/' );
		define( 'COMPONENT_SCRIPT', COMPONENT_ASSETS . 'script/' );
		
	//IMAGES
		define( 'PATH_COMPONENTS_IMAGE', PATH_COMPONENTS_ASSETS . 'images/' );
		define( 'REL_COMPONENTS_IMAGE', REL_COMPONENTS_ASSETS . 'images/' );
		define( 'PATH_COMPONENTS_LANGUAGE_IMAGE', PATH_COMPONENTS_ASSETS . 'images/'.$language. '/' );
		define( 'REL_COMPONENTS_LANGUAGE_IMAGE', REL_COMPONENTS_ASSETS . 'images/'.$language. '/' );
	//SCRIPT
		define( 'PATH_COMPONENTS_SCRIPT', PATH_COMPONENTS_ASSETS . 'script/' );
		define( 'REL_COMPONENTS_SCRIPT', REL_COMPONENTS_ASSETS . 'script/' );
		define( 'PATH_COMPONENTS_TEMPLATESCRIPT', PATH_COMPONENTS_SCRIPT . $template );
		define( 'REL_COMPONENTS_TEMPLATESCRIPT', REL_COMPONENTS_SCRIPT . $template );
		define( 'PATH_COMPONENTS_THEMESCRIPT', PATH_COMPONENTS_TEMPLATESCRIPT . $theme );
		define( 'REL_COMPONENTS_THEMESCRIPT', REL_COMPONENTS_TEMPLATESCRIPT . $theme );
	//CSS
		define( 'PATH_COMPONENTS_CSS', PATH_COMPONENTS_ASSETS . 'css/' );
		define( 'REL_COMPONENTS_CSS', REL_COMPONENTS_ASSETS . 'css/' );
		define( 'PATH_COMPONENTS_TEMPLATECSS', PATH_COMPONENTS_CSS . $template );
		define( 'REL_COMPONENTS_TEMPLATECSS', REL_COMPONENTS_CSS . $template );
		define( 'PATH_COMPONENTS_THEMECSS', PATH_COMPONENTS_TEMPLATECSS . $theme );
		define( 'REL_COMPONENTS_THEMECSS', REL_COMPONENTS_TEMPLATECSS . $theme );
/* MODULES 
Modules have templates and languages
=================================================================*/
		define( 'PATH_MODULES', PATH_APPLICATION . 'modules/' );
		define( 'REL_MODULES', REL_APPLICATION . 'modules/' );
		define( 'PATH_MODULE', PATH_APPLICATION . 'modules/'. $module . '/' );
		define( 'REL_MODULE', REL_APPLICATION . 'modules/'. $module . '/' );
		define( 'PATH_MODULE_CONTROLLERS', PATH_MODULE. 'controllers/' );
		define( 'REL_MODULE_CONTROLLERS', REL_MODULE. 'controllers/' );
		define( 'PATH_MODULE_MODELS', PATH_MODULE. 'models/' );
		define( 'REL_MODULE_MODELS', REL_MODULE. 'models/' );
		define( 'PATH_MODULE_VIEWS', PATH_PUBLIC. $module . '/views/' );
		define( 'REL_MODULE_VIEWS', REL_PUBLIC. $module . '/views/' );
		define( 'PATH_MODULE_ASSETS', PATH_PUBLIC. $module . '/assets/' );
		define( 'REL_MODULE_ASSETS', REL_PUBLIC. $module . '/assets/' );
	//LANGUAGE
		define( 'PATH_MODULE_LANGUAGE',PATH_LANGUAGE. 'modules/'. $module . '/' );
		define( 'REL_MODULE_LANGUAGE', REL_LANGUAGE. 'modules/'. $module . '/' );
	//IMAGES
		define( 'PATH_MODULE_IMAGE',PATH_MODULE_ASSETS . 'images/' );
		define( 'REL_MODULE_IMAGE', REL_MODULE_ASSETS . 'images/' );
		define( 'PATH_MODULE_TEMPLATEIMAGE', PATH_MODULE_IMAGE. $template );
		define( 'REL_MODULE_TEMPLATEIMAGE', REL_MODULE_IMAGE. $template );
		define( 'PATH_MODULE_THEMEIMAGE', PATH_MODULE_TEMPLATEIMAGE. $theme );
		define( 'REL_MODULE_THEMEIMAGE', REL_MODULE_TEMPLATEIMAGE. $theme );

		define( 'PATH_MODULE_LANGUAGE_IMAGE', PATH_MODULE_IMAGE . $language. '/' );
		define( 'REL_MODULE_LANGUAGE_IMAGE', REL_MODULE_IMAGE . $language. '/' );
		define( 'PATH_MODULE_LANGUAGE_TEMPLATEIMAGE', PATH_MODULE_LANGUAGE_IMAGE . $template );
		define( 'REL_MODULE_LANGUAGE_TEMPLATEIMAGE',REL_MODULE_LANGUAGE_IMAGE . $template );
		define( 'PATH_MODULE_LANGUAGE_THEMEIMAGE', PATH_MODULE_LANGUAGE_TEMPLATEIMAGE . $theme );
		define( 'REL_MODULE_LANGUAGE_THEMEIMAGE',REL_MODULE_LANGUAGE_TEMPLATEIMAGE . $theme );
	//SCRIPT
		define( 'PATH_MODULE_SCRIPT',PATH_MODULE_ASSETS . 'script/' );
		define( 'REL_MODULE_SCRIPT', REL_MODULE_ASSETS . 'script/' );
		define( 'PATH_MODULE_TEMPLATESCRIPT', PATH_MODULE_SCRIPT . $template );//template level script
		define( 'REL_MODULE_TEMPLATESCRIPT', REL_MODULE_SCRIPT . $template );
		define( 'PATH_MODULE_THEMESCRIPT', PATH_MODULE_TEMPLATESCRIPT . $theme );//theme level script
		define( 'REL_MODULE_THEMESCRIPT', REL_MODULE_TEMPLATESCRIPT . $theme );
	//CSS
		define( 'PATH_MODULE_CSS', PATH_MODULE_ASSETS . 'css/' );
		define( 'REL_MODULE_CSS', REL_MODULE_ASSETS . 'css/' );
		define( 'PATH_MODULE_TEMPLATECSS', PATH_MODULE_CSS . $template );
		define( 'REL_MODULE_TEMPLATECSS', REL_MODULE_CSS . $template );
		define( 'PATH_MODULE_THEMECSS', PATH_MODULE_TEMPLATECSS . $theme );
		define( 'REL_MODULE_THEMECSS', REL_MODULE_TEMPLATECSS . $theme );
		

/* WIDGETS 
Widgets have templates and languages
=================================================================*/
		define( 'PATH_WIDGETS', PATH_MODULE. 'widgets/' );
		define( 'REL_WIDGETS', REL_MODULE. 'widgets/' );
		define( 'PATH_PUBLIC_WIDGETS', PATH_PUBLIC. $module . '/widgets/' );
		define( 'REL_PUBLIC_WIDGETS', REL_PUBLIC. $module . '/widgets/' );
	//LANGUAGE
		define( 'PATH_WIDGETS_LANGUAGE', PATH_LANGUAGE. 'modules/'. $module . '/widgets/' );
		define( 'REL_WIDGETS_LANGUAGE', REL_LANGUAGE. 'modules/'. $module . '/widgets/' );


		define( 'WIDGET_VIEWS', '/views/' );
		define( 'WIDGET_ASSETS', '/assets/' );
		
		define( 'WIDGET_IMAGE', WIDGET_ASSETS . '/images/' );
		define( 'WIDGET_TEMPLATEIMAGE', WIDGET_IMAGE. $template );
		define( 'WIDGET_THEMEIMAGE', WIDGET_TEMPLATEIMAGE. $theme );
		define( 'WIDGET_LANGUAGE_IMAGE', WIDGET_IMAGE.$language. '/' );
		define( 'WIDGET_LANGUAGE_TEMPLATEIMAGE', WIDGET_LANGUAGE_IMAGE. $theme );
		define( 'WIDGET_LANGUAGE_THEMEIMAGE', WIDGET_LANGUAGE_TEMPLATEIMAGE. $theme );
		
		define( 'WIDGET_CSS', WIDGET_ASSETS . 'css/' );
		define( 'WIDGET_TEMPLATECSS', WIDGET_CSS . $template );
		define( 'WIDGET_THEMECSS', WIDGET_TEMPLATECSS . $theme );
		
		define( 'WIDGET_SCRIPT', WIDGET_ASSETS . 'script/' );
		define( 'WIDGET_TEMPLATESCRIPT', WIDGET_SCRIPT . $template );
		define( 'WIDGET_THEMESCRIPT', WIDGET_TEMPLATESCRIPT . $theme );
/* ADDONS
Addons don't have languages but it does have templates
==================================================================*/
		define( 'PATH_PLUGINS', PATH_ASSETS . 'plugins/' );
		define( 'REL_PLUGINS', REL_ASSETS . 'plugins/' );
	//IMAGES
		define( 'PLUGIN_IMAGE', 'images/' );
		define( 'PLUGIN_TEMPLATEIMAGE', PLUGIN_IMAGE. $template );
		define( 'PLUGIN_THEMEIMAGE', PLUGIN_TEMPLATEIMAGE. $theme );
	//SCRIPT
		define( 'PLUGIN_SCRIPT', 'script/' );
		define( 'PLUGIN_TEMPLATESCRIPT', 'script/'. $template );
		define( 'PLUGIN_THEMESCRIPT', PLUGIN_TEMPLATESCRIPT . $theme );
	//CSS
		define( 'PLUGIN_CSS', 'css/' );
		define( 'PLUGIN_TEMPLATECSS', PLUGIN_CSS . $template );
		define( 'PLUGIN_THEMECSS', PLUGIN_TEMPLATECSS . $theme );
	}
	
	/**
	 * Prototype function not in production
	 * reset some conditional client session data
	 *
	 * @param void;
	 *
	 * @return array
	 */
	function re_session( &$ui, $mode = false )
	{
		if(!$mode) {
			return false;
		}
		$ui = $_SESSION['UI'];
		unset( $_SESSION['UI'] );
		$ci =& get_instance(); 
		if(array_key_exists( 'language', $mode ) == true ) {
			/* Set language data */
			$ui['language'] = $ci->language_model->get_language( $mode['language'] );
			$ui['languages'] = $ci->language_model->fetch_indexed_array_of_languages();
		}
		if(array_key_exists( 'template', $mode ) == true) {
			/* Set language data */
			//$ui['language'] = $ci->language_model->get_language($mode['language']);
			//$ui['languages'] = $ci->language_model->fetch_indexed_array_of_languages();
		}
		if(array_key_exists( 'theme', $mode ) == true) {
			/* Set language data */
			//$ui['language'] = $ci->language_model->get_language($mode['language']);
			//$ui['languages'] = $ci->language_model->fetch_indexed_array_of_languages();
		}
		$this->paths( $ui );
		$this->get_request();
		$this->ui = $ui;
		$this->language( $ui );//loads component definitions
		$_SESSION['UI'] = $ui;
		return $ui;
	}
	function seo_enabled( $module_id, $modules=false ) {
		if ( $modules ) {
			foreach ( $modules as $module ) {
				if ( $module['module_id'] == $module_id ) {
					return $module['seo_enabled'];
				}
			}
		}
		else {
			$module = $this->get_module_by_id( $module_id );
			if ( !empty( $module['seo_enabled'] ) ) {
				return $module['seo_enabled'];
			}
		}
		return false;
	}
	/**
	 * function returns components data array
	 *
	 * @Param int(language id)
	 *
	 * @return array
	 */
	function user_components( $ui = false )
	{
		$ci =& get_instance();
		if ( !$ui ) {
			return false;
		}
		$user_components = array();
		if ( $this->ui['group_id'] == GROUP_CUSTOM ) {
			$ci->db->where( 'user_id', $ui['id'] );
		}
		else {
			$ci->db->where( 'user_id', $ui['group_id'] );
		}
		$ci->db->from ( 'user_components' );
		$query = $ci->db->get();
		$results = $query->result_array();
		foreach ( $results as $user_component ) {
			foreach ( $ui['components'] as $component ) {
				if ( $component['component_id'] == $user_component['component_id'] ) {
					$user_components[$user_component['component_id']] = $component;
					$user_components[$user_component['component_id']]['crud'] = $user_component['crud'];
				}
			}
		}
		return $user_components;
	}

	/**
	 * function returns components data array
	 *
	 * @Param int(language id)
	 *
	 * @return array
	 */
	function user_modules( $ui = false )
	{
		$ci =& get_instance();
		if ( !$ui ) {
			return false;
		}
		$ci->db->from ( 'modules as m' );
		$ci->db->join( 'user_modules as u', 'm.module_id = u.module_id','left' );
		if ( $this->ui['group_id'] == GROUP_CUSTOM ) {
			$ci->db->where( 'user_id',$ui['id'] );
		}
		else {
			$ci->db->where( 'user_id',$ui['group_id'] );
		}
		$query = $ci->db->get();
		$results = $query->result_array();
		$user_modules = array();
		foreach ( $results as $module ) {	// make array key a module_id
			$user_modules[$module['module_id']] = $module;
		}
		return $user_modules;
	}
	

	/**
	 * function returns components data array
	 *
	 * @Param int(language id)
	 *
	 * @return array
	 */
	function user_widgets( $ui=false )
	{
		$ci =& get_instance();
		if ( !$ui ) {
			return false;
		}
		$module = $ui['module'];
		$module_id = $ui['module_id'];
		$user_widgets = array();
		$ci->db->from ( 'widgets as w' );
		$ci->db->join( 'user_widgets as u', 'w.widget_id = u.widget_id','left' );
		$ci->db->join( 'widget_structure as s', 'w.widget_id = s.widget_id','left' );
		$ci->db->where( 's.module_id', $module_id );
		if ( $ui['group_id'] == GROUP_CUSTOM ) {
			$ci->db->where( 'u.user_id', $ui['id'] );
		}
		else {
			$ci->db->where( 'u.user_id', $ui['group_id'] );
		}
		$query = $ci->db->get();
		$results = $query->result_array();
		$user_widgets = array();
		foreach ( $results as $widget ) {
			$user_widgets[$widget['widget_id']] = $widget;
		}
		return $user_widgets;
	}
	function user_groups() {
		$ci =& get_instance();
		$query = $ci->db->get( 'user_groups' );
		$groups =  $query->result_array();
		$user_groups = array();
		foreach ( $groups as $group ) {
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
	function widget( $widget = NULL, $function = NULL, $args = NULL )
	{
		$msg = str_replace( '<WIDGET>', '<b>' . $widget . '</b>', '<div class="msg-error">' . NO_WIDGET . '</div>' );
		if ( !$widget = $this->load_widget( $widget ) ) {
			return false;
		}
		$function = $function ? $function : 'index';
		return $widget->$function($args);
	}
	
	/**
	 * Loads widget 
	 * 
	 * @return void
	 */
	function widget_ci_load( $obj, $_ci_data )
	{
		// get info about calling class so that we can get the name and therefore the directory to build paths
		$current_widget = strtolower( $obj->parent_name );
		// Set the default data variables
		foreach ( array( '_ci_view', '_ci_vars', '_ci_path', '_ci_return' ) as $_ci_val ) {
			$$_ci_val = ( !isset( $_ci_data[$_ci_val] ) ) ? FALSE : $_ci_data[$_ci_val];
		}
		// Set the path to the requested file
		if ( $_ci_path == '' ) {
			$_ci_ext = pathinfo ( $_ci_view, PATHINFO_EXTENSION );
			$_ci_file = ( $_ci_ext == '' ) ? $_ci_view.EXT : $_ci_view;
			$_ci_path = REL_PUBLIC_WIDGETS . $current_widget . WIDGET_VIEWS . $_ci_file;
		}
		else {
			$_ci_x = explode( '/', $_ci_path );
			$_ci_file = end( $_ci_x );
		}
		$_ci_d_file = ( $_ci_ext == '' ) ? $_ci_view . $this->device.EXT : $_ci_view;
		$_ci_d_path = REL_PUBLIC_WIDGETS . $current_widget . WIDGET_VIEWS . $_ci_file;
		if ( $this->ui['selected_device'] !== false ) {
			if ( file_exists( $_ci_d_path ) ) {
				$_ci_path = $_ci_d_path;
			}
			else {
				show_error( 'Unable to load the requested file: ' . $_ci_d_file . ' ' . $_ci_d_path );
			}
		}
		else {
			if ( file_exists( $_ci_d_path ) ) {
				$_ci_path = $_ci_d_path;
			}
			else {
				if ( !file_exists($_ci_path ) ) {
					show_error( 'Unable to load the requested file: ' . $_ci_file . ' ' . $_ci_path );
				}
			}
		}
		// This allows anything loaded using $this->load(views, files, etc.)
		// to become accessible from within the Controller and Model functions.
		// Only needed when running PHP 5

		if ( $this->_ci_is_instance() ) {
			$_ci_CI =& get_instance();
			foreach ( get_object_vars( $_ci_CI ) as $_ci_key => $_ci_var) {
				if ( !isset( $this->$_ci_key ) ) {
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
		if ( is_array( $_ci_vars ) ) {
			$this->_ci_cached_vars = array_merge( $this->_ci_cached_vars, $_ci_vars );
		}
		extract( $this->_ci_cached_vars );
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
		if ( ( bool ) @ini_get( 'short_open_tag' ) === FALSE AND config_item ( 'rewrite_short_tags' ) == TRUE ) {
			echo eval( '?>' . preg_replace( "/;*\s*\?>/", "; ?>", str_replace( '<?=', '<?php echo ', file_get_contents( $_ci_path ) ) ) );
		}
		else {
			include( $_ci_path ); // include() vs include_once() allows for multiple views with the same name 
		}
		// Return the file data if requested
		if ( $_ci_return === TRUE ) {
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
		if ( ob_get_level() > $this->_ci_ob_level + 1 ) {
			ob_end_flush();
		}
		else {
			// PHP 4 requires that we use a global
			global $OUT;
			$OUT->append_output( ob_get_contents() );
			@ob_end_clean();
		}
	}
	
	/*
	* creating array of widget controllers with associated key of id
	*
	* @param array
	*
	* @return array
	*/
	function widget_controllers( $widgets ) {
		$widget_controllers = array();
		foreach( $widgets as $widget ) {
			$widget_controllers[$widget['widget_id']] = $widget['system_name'];
		}
		return $widget_controllers;
	}
	
	/**
	 * Loads dashboard component model
	 * 
	 * @param Object calling object
	 * @param String model name
	 * @param String optional model name
	 * @param Bool db connection
	 * 
	 * @return void
	 */
	function widget_model( $obj, $module='www', $model, $name = '', $db_conn = FALSE )
	{ 
	// get info about calling class so that we can get the name and therefore the directory to build paths
		$current_widget = strtolower( $obj->parent_name );
		if ( is_array( $model ) ) {
			foreach ( $model as $babe ) {
				$this->model( $babe );	
			}
			return;
		}
		if ( $model == '' ) {
			return;
		}
		// Is the model in a sub-folder? If so, parse out the filename and path.
		if ( strpos($model, '/' ) === FALSE ) {
			$path = '';
		}
		else {
			$x = explode( '/', $model );
			$model = end( $x );
			unset( $x[count( $x )-1]);
			$path = implode( '/', $x ). '/';
		}

		if ( $name == '' ) {
			$name = $model;
		}
		if ( in_array( $name, $this->_ci_models, TRUE ) ) {
			return;
		}
		if ( isset( $obj->$name ) ) {
			show_error( 'The model name you are loading is the name of a resource that is already being used: ' . $name );
		}
		$model = strtolower( $model );
		if ( !file_exists( $this->modules_ci_path.strtolower( $module ) . '/widgets/' . $current_widget . '/models/' . $path . $model  .EXT ) ) {
			show_error( 'Unable to locate the model you have specified: ' . $model . '  ' . MODULESPATH . strtolower( $module ) . '/widgets/' . $current_component . '/models/' . $path . $model . EXT );
		}
		if ( $db_conn !== FALSE AND !class_exists( 'CI_DB' ) ) {
			if ( $db_conn === TRUE ) {
				$db_conn = '';
			}
			$obj->load->database( $db_conn, FALSE, TRUE );
		}
		if ( !class_exists( 'Model' ) ) {
			load_class( 'Model', FALSE );
		}
		require_once( $this->modules_ci_path.strtolower( $module ) . '/widgets/' . $current_widget . '/models/' . $path . $model . EXT );
		$model = ucfirst( $model );
		$obj->$name = new $model();
		$obj->$name->_assign_libraries();
		$this->_ci_models[] = $name;	
	}
	
	/**
	 * Loads component view
	 * 
	 * @param Object calling object
	 * @param String view name
	 * @param Array data
	 * @param Bool return output
	 */
	function widget_view( $obj, $view, $vars = array(), $return = FALSE )
	{
		return $this->widget_ci_load( $obj, array( '_ci_view' => $this->_template . $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return ) );
	}

	/**
	 * function returns widgets data array
	 *
	 * @Param int(language id)
	 *
	 * @return array
	 */
	function widgets( $module = false )
	{
		$ci =& get_instance();
		$ci->db->from( 'widgets as c' );
		$ci->db->join( 'widget_structure as s', 's.widget_id = c.widget_id','left' );
		$query = $ci->db->get();
		return $query->result_array();
	}
}