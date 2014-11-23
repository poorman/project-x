<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package Cosmic 2
	Global Model: theme_model.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
class Language_model extends CI_Model {
/**
 * constructor
 *
 * @Param void
 *
 * @Return void
 */
	function __construct() {
		parent::__construct();
	}
/** loads language definition files
 *
 * @param array
 *
 * @return void
 */
	function load_language($ui = false) {
		if (empty($ui['language']['language'])) {
			return false;
		}
		if (isset($_SESSION['component']) && file_exists(APPPATH.'/language/' . $ui['language']['language'] . '/' . $_SESSION['component'] . '.php')) {
			require_once(APPPATH.'language/' . $ui['language']['language'] . '/' . $_SESSION['component'] . '.php');
		}
		require_once(APPPATH.'language/' . $ui['language']['language'] . '/default.php');
		if (empty($ui['user_components']) && count($ui['user_components'])) {
			foreach ($ui['user_components'] as $component) {
				if (file_exists(APPPATH.'language/' . $ui['language']['language'] . '/' . $component['system_name'] . '.php')) {
					require_once(APPPATH.'language/' . $ui['language']['language'] . '/' . $component['system_name'] . '.php');
				}
			}
		}
	}
/**
 * function returns language data array
 *
 * @Param int (language id)
 *
 * @return array
 */
	function get_language($language = false) {
		if ($language) {
			is_numeric($language) ? $this->db->where('language_id',$language_id) : $this->db->where('system_name',str_replace(' ','_',$language));
		}
		else {
			$this->db->where('is_default',1);
		}
		$this->db->limit(1);
		$query=$this->db->get('languages');
		$this->db->flush_cache();
		return $query->row_array();
	}
/**
 * function returns languages data array
 *
 * @Param int (language id)
 *
 * @return array
 */
	function fetch_languages($module_id = false)
	{
		if($module_id) {
			$this->db->where('module_id',$module_id);
		}
		$query=$this->db->get('languages');
		$this->db->flush_cache();
		return $query->result_array();
	}
	function fetch_indexed_array_of_languages($module_id = false) {
		$indexed_languages = array();
		$languages = $this->fetch_languages($module_id);
		foreach($languages as $language) {
			$indexed_languages[$language['language_id']] = $language;
		}
		return $indexed_languages;
	}
}
?>