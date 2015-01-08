<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package X
	Global Model: component_model.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/

class Component_model extends CI_Model
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
	 * function returns components data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	 */
	function fetch_components()
	{
		$this->db->from( 'components as c' );
		$this->db->join( 'component_structure as s', 's.component_id = c.component_id', 'left' );
		$query = $this->db->get();
		return $query->result_array();
	}
	
	/**
	 * function returns components data array
	 *
	 * @Param int (language id)
	 *
	 * @return array
	 */
	function fetch_user_components($ui = false)
	{
		if ( !$ui ) {
			return false;
		}
		$user_components = array();
		if ( $ui['group_id'] == GROUP_CUSTOM ) {
			$this->db->where( 'user_id', $ui['id'] );
		}
		else {
			$this->db->where( 'user_id', $ui['group_id'] );
		}
		$this->db->from( 'user_components' );
		$query = $this->db->get();
		$results = $query->result_array();
		foreach ( $results as $user_component ) {
			foreach ( $ui['components'] as $component ) {
				if ( $component['component_id'] == $user_component['component_id'] ) {
					$user_components[$user_component['component_id']] = $component;
					$user_components[$user_component['component_id']]['crud'] = $user_component['crud'];
				}
			}
		}
		return $user_components;
	}
}
?>