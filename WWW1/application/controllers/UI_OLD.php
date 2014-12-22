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
	 */
	function load()
	{
		/*
			set parameters
		*/
		$params = NULL;
		if ($this->uri->segment(2) !== FALSE) {
			$params = array();
			$param = 2;
			$has_params = TRUE;
			while ($has_params) {
				$params[] = $this->uri->segment($param);
				$param++;
				if ($this->uri->segment($param) === FALSE) {
					$has_params = FALSE;
				}
			}
		}
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
			Get call
		*/
		$call = $this->uri->segment(2) ? $this->uri->segment(2) : false;
		/*
			is call global?
		*/
		$global = ($call) ? method_exists($this, $call) : true;
		$action = $this->ui['module'];
		/*
			this is ajax request
		*/
		if($this->ajaxload) {
			/*
				set function to default if it is not set.
			*/
			$call = $call ? $call : $this->default_ajax_call;
			/*
			All following segments convert to parameter ($args)
			*/
			if($global) {
				$this->out = $this->$call();
			}
			else {
				$this->out = $this->load->instance($this->ui, $action, $function, $params, true);
				log_msg($this->out,false);
			}
		}
		else {
			$call = $call ? $call : $this->default_call;
			/*
				this is echo call
			*/
			if(!$global) {
				/*
					this is call to component
				*/
				$this->load->instance($this->ui, $action, $function, $params, true);
			}
		}
		/*
			if there is ajax data to output
			output as json
		*/
		if ($this->out) {
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
		*/
		$module = $this->load->load_module($this->ui);
		$module->module();
		
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
