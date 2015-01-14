<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package X
	Global Controller: ui.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
class Ui extends CI_Controller
{
	protected $ui = array(); //entire current session data
	private $out = NULL; //output array
	protected $ajaxload = false;
	protected $params = false;
	protected $default_function = 'home';

	/**
	 * constructor
	 *
	 * @return json
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->request( $this->ui );
		$this->load();
		
	}
	
	/**
	 * curl and ajax request processor
	 *
	 * @param void
	 *
	 * @return array
	 *
	 * global echo call should bypass all function calls in this function(this note is for non-ajax calls only)
	 * if no segments set then it is  a call to function within this controller, this call is echo only
	 * segment 1 will either be name of function in this controller<br />
	 * or it will become collection of names for
	 * controller and function within one of the components
	 * call maybe ajax request or echo
	 */
	 function load()
	 {
		 $this->default_function = ( $this->uri->segment( 3 ) ) ? $this->uri->segment( 3 ) : $this->default_function;
		 $controller = false;
		 $function = false;
		 
		/* 
			if curl request return as ajax
			$this->out will get set
		*/
		$this->ajaxload = ( $this->input->post( 'curl ') == SECRET ) ? $this->ajaxload = true : $this->ajaxload;
		/*
			if ajax request return as array
			$this->out will get set
		*/
		$this->ajaxload = ( $this->input->get( 'ajax' ) || $this->input->post( 'ajax' ) ) ? $this->ajaxload = true : $this->ajaxload;
		/*
			this is either this constructors own function
			or
			this is plugin name
		*/
		$controller = $this->uri->segment( 2 ) ? $this->uri->segment( 2 ) : false;
		/*
			is call global?
			global when $function exists within this class
			or when segment 1 does not exist
		*/
		$global = ( $controller ) ? method_exists( $this, $controller ) : true;
		if( $this->ui['module_seo'] && $controller ) {
			/*
				Trying SEO load
			*/
			if( !empty( $this->ui['links'][$controller] ) ) {
				$controller = $this->ui['links'][$controller]['action'];
				$global = ( $controller ) ? method_exists( $this, $controller ) : true;
				if( $this->uri->segment( 3 ) ) {
					$function = ( !empty( $this->ui['functions'][$this->uri->segment( 3 ) ]['action'] ) ) ? $this->ui['functions'][$this->uri->segment( 3 )]['action'] : $this->uri->segment( 3 );
				}
				else {
					$function = $this->ui['links'][$controller]['functionality'];
				}
			}
			$function = ( $function ) ? $function : $this->default_function;
		}
		else {
			/*
				Non SEO Load
			*/
			$function = ( $this->uri->segment( 3 ) ) ? $this->uri->segment( 3 ) : $this->default_function;
		}
		/*
			All following segments convert to parameter($args)
		*/
		$params = NULL;
		if ( $this->uri->segment( 4 )  !== FALSE ) {
			$params = array();
			$param = 4;
			$has_params = TRUE;
			while( $has_params ) {
				$params[] = $this->uri->segment( $param );
				$param++;
				if ( $this->uri->segment( $param ) === FALSE ) {
					$has_params = FALSE;
				}
			}
		}
		/*
			this is ajax request
		*/
		if ( $this->ajaxload ) {
			if ( $global ) {
				/*
					Global ajax call
				*/
				$this->out = $this->$controller();
			}
			else {
				/*
					component ajax call
				*/
				$this->out = $this->load->instance($this->ui, $controller, $function, $params, true );
			}
		}
		else {
			if ( !$global ) {
				/*
					echo component
				*/
				$this->load->instance( $this->ui, $controller, $function, $params, true );
			}
		}
		/*
			if there is ajax data to output
			output as json
		*/
		if ( $this->out ) {//log_msg(json_encode($this->out));
			echo json_encode( $this->out );
			exit( 0 );
		}
	 }
	/**
	 * home when refreshed
	 *
	 * @return array
	 */
	function home()
	{
		$this->ui['interface'] = true;
		$this->ui['content'] = 'content';
		$view = $this->ui['content'];
		$this->out['interface'] = $this->load->view( $view,$this->ui, TRUE );
		return $this->out;
	}
	
	/**
	 * default index
	 *
	 * @return array
	 */
	function index()
	{
		//$module = $this->load->load_module($this->ui['module']);
		$module = $this->load->load_module( $this->ui );
		$module->module();
	}
	
	/**
	 * Function is a php starting point for internationalization language switch
	 * 
	 */
	function language( $args = array() ) {//args should be /language/change/english';
		$action = $this->uri->segment( 3 );
		$language = $this->uri->segment( 4 );
		$function = $action . '_language';
		$this->load->$function( $this->ui, $language );
		return;
	}
	
	/**
	 * Function is a php starting point for internationalization language switch
	 * 
	 */
	function template( $args = array() ) {//args should be /template/change/sometemplate';
		$action = $this->uri->segment(3);
		$template = $this->uri->segment(4);
		$function = $action.'_template';
		$this->load->$function( $this->ui, $template);
		return;
	}
	
	/**
	 * Function is a php starting point for internationalization language switch
	 * 
	 */
	function theme( $args = array() ) {//args should be /template/change/sometemplate';
		$action = $this->uri->segment(3);
		$template = $this->uri->segment(4);
		$function = $action . '_template';
		$this->load->$function( $this->ui, $template );
		return;
	}
	
	
	
	/**
	 * test function
	 *
	 * @return array
	 */
	function example()
	{
		
		switch( $this->uri->segment(3) ) {
			case 'dialog' : 
				return array(
							'script' => 'ui.discard( "dialog" );',
							'dialog' => $this->load->view( 'examples/dialog', $this->ui, TRUE )
							);log_msg(2);
				break;
			case 'interface' :
				return array(
							'script' => 'ui.discard( "interface" );',
							'interface' => $this->load->view( 'examples/interface', NULL, TRUE )
							);
				break;
			case 'element' :
				if( $this->uri->segment( 4 ) ) {
					return array(
								'script' => 'ui.discard( "ajax" );',
								'ajax' => $this->load->view( 'examples/element', $this->ui, TRUE ) . $this->uri->segment( 4 )
								);
				}
				else {
					return array(
								'script' => 'ui.discard( "ajax" );',
								'ajax' => $this->load->view( 'examples/element', $this->ui, TRUE )
								); 
				} 
				break;
			case 'assets' : 
							$this->load->model( 'example_model' );
							$this->ui['model'] = $this->example_model->example();
							if( $this->uri->segment( 4 ) ) { 
								$out = array( 'script' => 'ui.discard( "ajax" );alert( "Linked via javaScript" );', 'ajax' => $this->load->view( 'examples/element', $this->ui, TRUE) . $this->uri->segment( 4 ) );
							}
							else {
								$out = array( 'script' => 'ui.discard( "ajax" );alert( "Linked via javaScript" ) ;', 'ajax' => $this->load->view( 'examples/assets', $this->ui, TRUE ) ); 
							} 
							return $out; break;
			default:
				return array(
							'script' => 'alert( "No tests selected" ) '
							);
				break;
		}
	}
}
