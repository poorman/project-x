<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package Cosmic 2
	Global Controller: plugins/testing/controller.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
class Gearbox
{
	private $ui;
	private $out = NULL;
	
	/**
	 * constructor
	 *
	 */
	function __construct()
	{
		 $this->ui = $_SESSION['UI'];
	}

	/**
	 * Gearbox initialize function
	 * @Params
	 *
	 */
	function module()
	{
		$this->ui['content'] = 'content';
		$view = APPTEMPLATE.'/main';
		$this->load->view($view,$this->ui);
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
		$this->out['interface'] = $this->load->plugin_view($this,$this->ui['content'],$this->ui, TRUE);
		$this->out['script'] = $this->load->plugin_view($this,$this->ui['js_content'],$this->ui, TRUE);
		return $this->out;
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
									'dialog' => $this->load->module_view('website_dialog',$this->ui,TRUE),
									'script' => 'ui.discard("dialog");'
								);
				break;
			case 'interface' :
				$this->out = array(
									'interface' => $this->load->module_view('website_interface',$this->ui,TRUE),
									'script' => 'ui.discard("interface");'
								);
				break;
			case 'element' :
				return array(
							'ajax' => $this->load->module_view('website',$this->ui,TRUE),
							'script' => 'ui.discard("ajax");'
							);
				break;
			case 'assets' : 
					$this->load->module_model($this,$this->ui['module'],'examplemodule_model');
					$this->ui['model'] = $this->examplemodule_model->example();
					$script = $this->load->module_view('js_website_assets',$this->ui,TRUE);
					$view = $this->load->module_view('website_assets',$this->ui,TRUE);
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
