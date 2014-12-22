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
	protected $default_ajax_call = 'ajax_home';
	protected $default_call = 'index';
	protected $ajaxload = false;
	protected $params = false;
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
		$this->load();
	}
	/**
	 * curl and ajax request processor
	 *
	 * @param void
	 *
	 * @return array
	 *
	 * global echo call should bypass all function calls in this function (this note is for non-ajax calls only)
	 * if no segments set then it is  a call to function within this controller, this call is echo only
	 * segment 1 will either be name of function in this controller<br />
	 * or it will become collection of names for
	 * controller and function within one of the components
	 * call maybe ajax request or echo
	 */
	function load()
	{
		/* 
			if curl request return as ajax
			$this->out will get set
		*/
		$this->ajaxload = ($this->input->post('curl') == SECRET) ? $this->ajaxload = true : $this->ajaxload;
		/*
			if ajax request return as array
			$this->out will get set
		*/
		$this->ajaxload = ($this->input->get('ajax')||$this->input->post('ajax')) ? $this->ajaxload = true : $this->ajaxload;
		/*
			set parameters
		*/
		$this->params = NULL;
		if ($this->uri->segment(4) !== FALSE) {
			$this->params = array();
			$param = 2;
			$has_params = TRUE;
			while ($has_params) {
				$this->params[] = $this->uri->segment($param);
				$param++;
				if ($this->uri->segment($param) === FALSE) {
					$has_params = FALSE;
				}
			}
		}
		
		/*
			Get call
		*/
		$controller = $this->uri->segment(2) ? $this->uri->segment(2) : false;
		/*
			is call global?
			global when $function exists within this class
			or when segment 1 does not exist
		*/
		$global = ($controller) ? method_exists($this, $controller) : true;
		
		/*
			check if calling global function
		*/
		if (!$global) {
			/*
				What controller and function to envoke
			*/
			$controller = ($this->uri->segment(2)) ? $this->ui['links'][$this->uri->segment(2)] : false;
			
			if($this->uri->segment(3)) {
				$function = (!empty($this->ui['functions'][$this->uri->segment(3)])) ? $this->ui['functions'][$this->uri->segment(3)] : $this->uri->segment(3);
			}
		}
		/*
			this is ajax request
		*/
		if($this->ajaxload) {
			if($global) {
				/*
					Global ajax call
				*/
				$this->out = $this->$controller();
			}
			else {
				/*
					component ajax call
				*/
				$this->out = $this->load->instance($this->ui, $controller, $function, $params, true);
			}
		}
		else {
			if(!$global) {
				/*
					echo component
				*/
				$this->load->instance($this->ui, $controller, $function, $params, true);
			}
		}
		/*
			if there is ajax data to output
			output as json
		*/
		if ($this->out) {log_msg($this->out);
			echo json_encode($this->out);
			exit(0);
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
	 * @return void
	 */
	function index()
	{
		/*
			process in module
		
		$module = $this->load->load_module($this->ui);
		$module->module();*/
		$this->ui['content'] = APPTEMPLATE.'/content';
		$view = APPTEMPLATE.'/main';
		$this->load->view($view,$this->ui,$this->ui);
	}
	
	/**
	 * Language switch
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
	 * Template switch
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
	 * Theme switch
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
