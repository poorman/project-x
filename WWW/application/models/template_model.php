<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package X
	Global Model: template_model.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
class Template_model extends CI_Model
{
/**
 * constructor
 *
 * @Param void
 *
 * @Return void
 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * function returns indexed template data array
	 *
	 * @Param int (template id)
	 *
	 * @return array
	 */
	function fetch_indexed_array_of_templates($module_id = false)
	{
		$indexed_templates = array();
		$templates = $this->fetch_templates($module_id);
		foreach($templates as $template) {
			$indexed_templates[$template['template_id']] = $template;
		}
		return $indexed_templates;
	}
	
	/**
	 * function returns template data array
	 *
	 * @Param int (template id)
	 *
	 * @return array
	 */
	function fetch_templates($module_id = false) // fetch templates for module or all or global
	{
		if($module_id) {
			$this->db->where('module_id',$module_id);
		}
		$query=$this->db->get('templates');
		$this->db->flush_cache();
		return $query->result_array();
	}
	
	/**
	 * function returns default template data for specific module array
	 *
	 * @Param int (template id)
	 *
	 * @return array
	 */
	function get_module_template($module_id=false)
	{
		$this->db->where('module_id',$module_id);
		$this->db->limit(1);
		$query=$this->db->get('templates');
		$this->db->flush_cache();
		return $query->row_array();
	}
	
	/**
	 * function returns template data array for specific template or template default to global
	 *
	 * @Param int (template id)
	 *
	 * @return array
	 */
	function get_template($template_id=false)
	{
		if($template_id) {
			$this->db->where('template_id',$template_id);
		}
		else {
			$this->db->where('is_default',1);
			$this->db->where('module_id',1);
		}
		$this->db->limit(1);
		$query=$this->db->get('templates');
		$this->db->flush_cache();
		return $query->row_array();
	}
}
?>