<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package Cosmic 2
	Global Controller: plugins/testing/controller.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
class Www
{
	private $ui;
	private $action = NULL;
	private $content = NULL;
	private $default_content = DEFAULT_CONTENT;
	private $default_function = DEFAULT_FUNCTION;
	private $out = NULL;
	function __construct()
	{
		$this->ui = $_SESSION['UI'];
	}

	/**
	* WWW initialize function
	* @Params
	*
	*/
	function module()
	{
		$view = 'main';
		$this->ui['interface'] = NULL;
		$content = $this->load->module_view($view,$this->ui,true);
		$component = $this->load->load_component('examplecomponent', $this->ui);
		echo $this->load->load_shell( $this->ui, $component->home() );
	}
	
	/**
	* Example Home
	*
	* @return array
	 */

	function home() {
		$this->out = array();
		$this->ui = $_SESSION['UI'];
		$this->ui['interface']=true;
		$this->ui['content'] = 'home';
		$this->ui['js_content'] = 'js_home';
		$this->out['interface'] = $this->load->component_view($this,$this->ui['content'],$this->ui, TRUE);
		$this->out['script'] = $this->load->component_view($this,$this->ui['js_content'],$this->ui, TRUE);
		//return $this->out;
	}
	
/**
 * test function
 *
 * @return array
 */
	function example($args=array()) {
		!empty($args[0]) ? $test = $args[0] : $test = $this->uri->segment(4);
		switch ($test) {
			case 'dialog' : 
				$this->out = array(
									'dialog' => $this->load->module_view('www_dialog',$this->ui,TRUE),
									'script' => 'ui.discard("dialog");'
								);
				break;
			case 'interface' :
				$this->out = array(
									'interface' => $this->load->module_view('www_interface',$this->ui,TRUE),
									'script' => 'ui.discard("interface");'
								);
				break;
			case 'element' :
				return array(
							'ajax' => $this->load->module_view('www',$this->ui,TRUE),
							'script' => 'ui.discard("ajax");'
							);
				break;
			case 'assets' :
					$this->load->module_model($this,$this->ui['module'],'examplemodule_model');
					$this->ui['model'] = $this->examplemodule_model->example();
					$script = $this->load->module_view('js_www_assets',$this->ui,TRUE);
					$view = $this->load->module_view('www_assets',$this->ui,TRUE);
					return array(
								'ajax' => $view,
								'script' => 'ui.discard("ajax");'.$script
								);
				break;
			default:
				$this->out = array(
									'script' => 'alert("No tests selected")'
								);
				break;
		}
		return $this->out;
	}
}
