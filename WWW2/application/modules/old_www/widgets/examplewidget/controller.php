<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package Cosmic 2
	Global Controller: plugins/testing/controller.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
class Examplewidget
{
	private $ui;
	private $out = NULL;
	 function __construct() {
		 $this->ui = $_SESSION['UI'];
		 
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
										'script' => 'ui.discard("dialog");',
										'dialog' => $this->load->widget_view($this,'widget_dialog',$this->ui,TRUE)
										);
					break;
			case 'interface' :
					$this->out = array(
										'script' => 'ui.discard("interface");',
										'interface' => $this->load->widget_view($this,'widget_interface',$this->ui,TRUE)
										);
					break;
			case 'element' : 
					$this->out = array(
										'script' => 'ui.discard("ajax");',
										'ajax' => $this->load->widget_view($this,'widget',$this->ui,TRUE)
										);
					break;
			case 'assets' : 
					$this->load->widget_model($this,$this->ui['module'],'examplewidget_model');
					$this->ui['model'] = $this->examplewidget_model->example();
					$script = $this->load->widget_view($this,'js_widget_assets',$this->ui,TRUE);
					$view = $this->load->widget_view($this,'widget_assets',$this->ui,TRUE);
					return array('script' => $script, 'ajax' => $view);
				break;
			default:
				$this->out = array('script' => 'alert("No tests selected")');break;
		}
		return $this->out;
	}
}
