<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package X
	Global Model: plugin_model.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/

class Plugin_model extends CI_Model
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
	 * function returns plugins data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	 */
	function fetch_plugins()
	{
		$this->db->from('plugins as c');
		$this->db->join('plugin_structure as s', 's.plugin_id = c.plugin_id','left');
		$query = $this->db->get();
		return $query->result_array();
	}
	
	/**
	 * function returns plugins data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	 */
	function fetch_user_plugins($ui = false)
	{
		if (!$ui) {
			return false;
		}
		$user_plugins = array();
		if ($ui['group_id'] == GROUP_CUSTOM) {
			$this->db->where('user_id',$ui['id']);
		}
		else {
			$this->db->where('user_id',$ui['group_id']);
		}
		$this->db->from('user_plugins');
		$query = $this->db->get();
		$results = $query->result_array();
		foreach ($results as $user_plugin) {
			foreach ($ui['plugins'] as $plugin) {
				if ($plugin['plugin_id'] == $user_plugin['plugin_id']) {
					$user_plugins[$user_plugin['plugin_id']] = $plugin;
					$user_plugins[$user_plugin['plugin_id']]['crud'] = $user_plugin['crud'];
				}
			}
		}
		return $user_plugins;
	}
}
?>