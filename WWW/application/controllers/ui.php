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
		
		/*
			Save all available global methods
		*/
		$this->ui['global_methods'] = get_class_methods($this);
		
		/*
			Process request to loader
		*/
		$this->out = $this->load->loader($this->ui);
		
		/*
			if there is ajax data to output
			output as json
		*/
		if ( isset($this->out) && $this->out ) {//log_msg(json_encode($this->out));
			if( !is_array( $this->out ) ) { 
				if( is_string($this->out) && method_exists($this, $this->out) ) {
					$this->out = $this->$this->out;
				}
			}
			else {
				echo json_encode( $this->out );
				exit( 0 );
			}
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
		if ( isset( $this->out ) ) {
			echo $this->out;
		}
		else {
			$module = $this->load->load_module( $this->ui);
			$module->module();
		}
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
