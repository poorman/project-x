<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package Cosmic 2
	Global Controller: ui.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
class Ui extends CI_Controller {
	protected $ui = array(); //entire current session data
	private $out = NULL; //output array
	protected $url = '';

/**
 * constructor
 *
 * @return json
 */
	function __construct() {
		parent::__construct();
		
		$this->load->request($this->ui);
		$this->url = APPTEMPLATE.'/';
		/* 
		if curl request return as ajax 
		*/
		if ($this->input->post('curl') == SECRET) {
			$this->do_action();
		}
		/*
		if ajax request return as array 
		*/
		if ($this->input->get('ajax')||$this->input->post('ajax')) {
			$this->do_action();
		}
		/*
			if there is ajax data to output
			output as json
		*/
		if($this->out) {//log_msg(json_encode($this->out));
			echo json_encode($this->out);
			exit(0);
		}
	}
/**
 * curl and ajax request processor
 *
 * @param void
 *
 * @return array
 */
	function do_action() {
		/*
			this is either this constructors own function
			or
			this is plugin name
		*/
		$action = $this->uri->segment(2) ? $this->uri->segment(2) : 'home';
		/*
			this is function called within the plugins controller
		*/
		$function = $this->uri->segment(3) ? $this->uri->segment(3) : NULL;
		/*
			is action call to this controller?
		*/
		if($action && method_exists($this, $action)) {
			$this->out = $this->$action();
			return;
		}
		/*
			All following segments convert to parameter ($args)
		*/
		$params = NULL;
		if ($this->uri->segment(4) !== FALSE) {
			$params = array();
			$param = 4;
			$has_params = TRUE;
			while ($has_params) {
				$params[] = $this->uri->segment($param);
				$param++;
				if ($this->uri->segment($param) === FALSE) {
					$has_params = FALSE;
				}
			}
		}
		
		/* Call instance of a component */
		$this->out = $this->load->instance($action, $function, $params);
		
		/*
			function call is not set then set to default
		*/
		$function = $function ? $function : 'index';
		/*
		default replacements before outputing
		*/
		if ($this->out) {
			foreach ($this->out as $k=>$v) {
				$this->out[$k] = str_replace('December 31st 1969',NOT_APPLICABLE,$v);
			}
		}
		return;
	}

/**
 * default index
 *
 * @return array
 */
	function index() {
		$this->ui['content'] = 'content';
		$view = APPTEMPLATE.'/main';
		$this->load->view($view,$this->ui);
	}
	/**
	 * home when refreshed
	 *
	 * @return array
	 */
	function home()
	{$out = array();
		$this->ui['interface']=true;
		$this->ui['content'] = 'content';
		$view = APPTEMPLATE.'/'.$this->ui['content'];
		$this->out['interface'] = $this->load->view($view,$this->ui, TRUE);
		return $this->out;
	}
	function language($args = array()) {
		$action = $this->uri->segment(3);
		$language = $this->uri->segment(4);
		$function = $action.'_language';
		$this->load->$function($this->ui,$language);
		$this->index();
	}
/**
 * test function
 *
 * @return array
 */
	function example() {
		switch ($this->uri->segment(3)) {
			case 'dialog' : 
				return array(
							'script' => 'ui.discard("dialog");',
							'dialog' => $this->load->view($this->url.'examples/dialog',$this->ui, TRUE)
							);
				break;
			case 'interface' :
				return array(
							'script' => 'ui.discard("interface");',
							'interface' => $this->load->view($this->url.'examples/interface',NULL, TRUE)
							
							);
				break;
			case 'element' :
				if($this->uri->segment(4)) {
					return array(
								'script' => 'ui.discard("ajax");',
								'ajax' => $this->load->view($this->url.'examples/element',$this->ui, TRUE).$this->uri->segment(4)
								);
				}
				else {
					return array(
								'script' => 'ui.discard("ajax");',
								'ajax' => $this->load->view($this->url.'examples/element',$this->ui, TRUE)
								); 
				} 
				break;
			case 'assets' : 
							$this->load->model('example_model');
							$this->ui['model'] = $this->example_model->example();
							if($this->uri->segment(4)) { 
								$out = array('script' => 'ui.discard("ajax");alert("Linked via javaScript");', 'ajax' => $this->load->view($this->url.'examples/element',$this->ui, TRUE).$this->uri->segment(4));
							}
							else {
								$out = array('script' => 'ui.discard("ajax");alert("Linked via javaScript");','ajax' => $this->load->view($this->url.'examples/assets',$this->ui, TRUE) ); 
							} 
							return $out; break;
			default:
				return array(
							'script' => 'alert("No tests selected")'
							);
				break;
		}
	}
/**
 * gearbox test function
 *
 * @return array
 */
	function gearbox_example() {
		$data = array();
		
		switch ($this->uri->segment(3)) {
			case 'dialog' : 
				$this->out = array(
									'dialog' => $this->load->gearbox_view($this,'gearbox_dialog',$data,true)
									);
				break;
			case 'interface' : 
				$this->out = array(
									'interface' => $this->load->gearbox_view($this,'gearbox_interface',$data,true)
									);
				break;
			case 'ajax' : 
				if($this->uri->segment(4)) {
					$this->out = array(
									'ajax' => $this->load->gearbox_view($this,'gearbox',$data,true).$this->uri->segment(4)
									);
				}
				else {
					$this->out = array(
									'ajax' => $this->load->gearbox_view($this,'gearbox',$data,true)
									);
				}
				break;
			case 'asset' : 
				$this->load->gearbox_model($this,'GearboxExample_model');
				$data['model'] = $this->GearboxExample_model->example();
				$script = $this->load->gearbox_view($this,'js_gearbox_assets',$data,TRUE);
				$view = $this->load->gearbox_view($this,'gearbox_assets',$data,TRUE);
				$this->out = array(
								'ajax' => $view,
								'script' => $script
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
/**
 * dashboard test function
 *
 * @return array
 */
	function dashboard_example() {
		$data = array();
		
		switch ($this->uri->segment(3)) {
			case 'dialog' : return array('dialog' => $this->load->dashboard_view($this,'dashboard_dialog',$data,true));break;
			case 'interface' : return array('interface' => $this->load->dashboard_view($this,'dashboard_interface',$data,true));break;
			case 'ajax' : if($this->uri->segment(4)) { return array('ajax' => $this->load->dashboard_view($this,'dashboard',$data,true).$this->uri->segment(4)); } else { return array('ajax' => $this->load->dashboard_view($this,'dashboard',$data,true)); } break;
			case 'asset' : 
				$this->load->dashboard_model($this,'exampledashboard_model');
				$data['model'] = $this->exampledashboard_model->example();
				$script = $this->load->dashboard_view($this,'js_dashboard_assets',$data,TRUE);
				$view = $this->load->dashboard_view($this,'dashboard_assets',$data,TRUE);
				return array('ajax' => $view, 'script' => $script);
		break;
			default:
				$this->out = array('script' => 'alert("No tests selected")');break;
		}
	}
/**
 * website test function
 *
 * @return array
 */
	function website_example() {
		$data = array();
		$data = $this->ui;
		switch ($this->uri->segment(3)) {
			case 'dialog' :
				return array(
							'script' => 'ui.discard("dialog");',
							'dialog' => $this->load->website_view($this,'website_dialog',$data,true)
							);
				break;
			case 'interface' :
				return array(
							'script' => 'ui.discard("interface");',
							'interface' => $this->load->website_view($this,'website_interface',$data,true)
							);break;
			case 'ajax' : 
				if($this->uri->segment(4)) {
					return array(
								'script' => 'ui.discard("ajax");',
								'ajax' => $this->load->website_view($this,'website',$data,true).$this->uri->segment(4)
								); 
				}
				else {
					return array(
								'script' => 'ui.discard("ajax");',
								'ajax' => $this->load->website_view($this,'website',$data,true)
								);
				} break;
			case 'asset' : 
				$this->load->website_model($this,'examplewebsite_model');
				$data['model'] = $this->examplewebsite_model->example();
				$script = 'ui.discard("ajax");'.$this->load->website_view($this,'js_website_assets',$data,TRUE);
				$view = $this->load->website_view($this,'website_assets',$data,TRUE);
				return array(
							'script' => $script,
							'ajax' => $view
							);
		break;
			default:
				$this->out = array('script' => 'alert("No tests selected")');break;
		}
	}

}
