<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package Cosmic 2
	Global Controller: components/testing/controller.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
class Examplecomponent
{
	private $ui;
	private $out = NULL;
	 function __construct() {
		 $this->ui = $_SESSION['UI'];
		 
	 }
 
 /**
 * Example Home
 *
 * @return array
 */

	function home() {
		$this->out = array();
		$this->ui['interface']=true;
		$this->ui['content'] = 'home';
		$this->ui['js_content'] = 'js_home';
		$language = $_SESSION['UI']['language']['language_id'];
		$language_select_properties = array(
											'name' => 'language',
											'id' => 'language',
											//'class' => '',
											'values' => $this->ui['languages'],
											'selected' => $language,
											'script' => array('onChange' => 'option.ChangeLanguage(this);return false'),
											'label' => 'Select language'
											);
		$template_select_properties = array(
											'name' => 'template',
											'id' => 'template',
											//'class' => '',
											'values' => $this->ui['templates'],
											'selected' => $this->ui['template']['template_id'],
											'script' => array('onChange' => 'option.ChangeTemplate(this);return false'),
											'label' => 'Select template'
											);
		$theme_select_properties = array(
											'name' => 'theme',
											'id' => 'theme',
											//'class' => '',
											'values' => $this->ui['themes'],
											'selected' => $this->ui['theme']['theme_id'],
											'script' => array('onChange' => 'option.ChangeTheme(this;return false)'),
											'label' => 'Select theme'
											);
		$this->ui['select_input_language'] = $this->forms_model->select_input($language_select_properties);
		$this->ui['select_input_template'] = $this->forms_model->select_input($template_select_properties);
		$this->ui['select_input_theme'] = $this->forms_model->select_input($theme_select_properties);
		$this->out['script'] = 'ui.discard("interface");'.$this->load->component_view($this,$this->ui['js_content'],$this->ui, TRUE);
		$this->out['interface'] = $this->load->component_view($this,$this->ui['content'],$this->ui, TRUE);
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
									'dialog' => $this->load->component_view($this,'component_dialog',NULL,TRUE),
									'script' => 'ui.discard("dialog");'
								);
				break;
			case 'interface' :
				$this->out = array(
									'interface' => $this->load->component_view($this,'component_interface',NULL,TRUE),
									'script' => 'ui.discard("interface");'
								);
				break;
			case 'element' :
				return array(
							'ajax' => $this->load->component_view($this,'component_element',NULL,TRUE),
							'script' => 'ui.discard("ajax");'
							);
				break;
			case 'assets' : 
					$this->load->component_model($this,'examplecomponent_model');
					$data = array('model' => $this->examplecomponent_model->example());
					$script = $this->load->component_view($this,'js_component_assets',$data,TRUE);
					$view = $this->load->component_view($this,'component_assets',$data,TRUE);
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
