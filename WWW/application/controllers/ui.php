<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
	protected $url = '';

	/**
	 * constructor
	 *
	 * @return json
	 */
	function __construct()
	{
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
		if ($this->out) {//log_msg(json_encode($this->out));
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
	function do_action()
	{
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
	 * test function
	 *
	 * @return array
	 */
	function example()
	{
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
	 * home when refreshed
	 *
	 * @return array
	 */
	function home()
	{
		$this->ui['interface']=true;
		$this->ui['content'] = 'content';
		$view = APPTEMPLATE.'/'.$this->ui['content'];
		$this->out['interface'] = $this->load->view($view,$this->ui, TRUE);
		return $this->out;
	}
	
	/**
	 * default index
	 *
	 * @return array
	 */
	function index()
	{
		if($module = $this->load->load_module($this->ui['module'])) {
			$module->index($this->ui);
		}
		else {
			$this->ui['content'] = 'content';
			$view = APPTEMPLATE.'/main';
			$this->load->view($view,$this->ui);
		}
	}
	
	/**
	 * Function is a php starting point for internationalization language switch
	 * 
	 */
	function language($args = array()) {//args should be /language/change/english';
		$action = $this->uri->segment(3);
		$language = $this->uri->segment(4);
		$function = $action.'_language';
		$this->load->$function($this->ui,$language);
		return;
	}
	
	/**
	 * Function is a php starting point for internationalization language switch
	 * 
	 */
	function template($args = array()) {//args should be /template/change/sometemplate';
		$action = $this->uri->segment(3);
		$template = $this->uri->segment(4);
		$function = $action.'_template';
		$this->load->$function($this->ui,$template);
		return;
	}
	
	/**
	 * Function is a php starting point for internationalization language switch
	 * 
	 */
	function theme($args = array()) {//args should be /template/change/sometemplate';
		$action = $this->uri->segment(3);
		$template = $this->uri->segment(4);
		$function = $action.'_template';
		$this->load->$function($this->ui,$template);
		return;
	}
}
