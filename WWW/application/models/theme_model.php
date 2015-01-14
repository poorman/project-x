<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package Cosmic 2
	Global Model: theme_model.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
class Theme_model extends CI_Model
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
	 * fetch indexed array of all themes for specified template
	 *
	 * @return	bool
	 */
	function fetch_indexed_array_of_themes( $template_id = false )
	{
		$indexed_themes = array();
		$themes = $this->fetch_themes( $template_id );
		foreach ( $themes as $theme ) {
			$indexed_themes[$theme['theme_id']] = $theme;
		}
		return $indexed_themes;
	}

	/**
	 * fetch all themes for specified template
	 *
	 * @return	bool
	 */
	function fetch_themes( $template_id = false )
	{
		if( !$template_id ) {
			return false;
		}
		$this->db->where( 'template_id', $template_id );
		$query = $this->db->get( 'themes' );
		$this->db->flush_cache();
		return $query->result_array();
	}
	
	/**
	 * function theme session
	 *
	 * @return	bool
	 */
	function get_theme( $template_id = false, $theme_id = false )
	{
		if( !$template_id ) {
			return false;
		}
		if ( $theme_id ) {
			$this->db->where( 'theme_id', $theme_id );
		}
		else {
			$this->db->where( 'template_id', $template_id );
			$this->db->where( 'is_default', 1 );
		}
		$this->db->limit(1);
		$this->db->get( 'themes' );
		$query = $this->db->get( 'themes' );
		$this->db->flush_cache();
		return $query->row_array();
	}
}
?>