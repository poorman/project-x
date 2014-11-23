<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package Cosmic 2
	Global Controller: plugins/testing/controller.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
class Exampleplugin
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
			case 'dialog' : $this->out = array('dialog' => $this->load->plugin_view($this,'plugin_dialog',NULL,TRUE));break;
			case 'interface' :$this->out = array('interface' => $this->load->plugin_view($this,'plugin_interface',NULL,TRUE));break;
			case 'ajax' : return array('ajax' => $this->load->plugin_view($this,'plugin_element',NULL,TRUE));break;
			case 'asset' : 
					$this->load->plugin_model($this,'exampleplugin_model');
					$data = array('model' => $this->exampleplugin_model->example());
					$script = $this->load->plugin_view($this,'js_plugin_assets',$data,TRUE);
					$view = $this->load->plugin_view($this,'plugin_assets',$data,TRUE);
					return array('ajax' => $view, 'script' => $script);
				break;
			default:
				$this->out = array('script' => 'alert("No tests selected")');break;
		}
		return $this->out;
	}
}
