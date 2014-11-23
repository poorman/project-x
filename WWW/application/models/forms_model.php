<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package X
	Global Model: theme_model.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
class Forms_model extends CI_Model
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
	 * Select box	
	 *
	*/
	function select_input($properties=array())
	{
		/* settings will include
			values -multidimensional array of keys and values or simple array of values where value is also a key
			label - message
			class = class name or array of class names applying to this element
			id of element
			name of element
		*/
		if (!empty($properties['label'])) {
			$out = '<label ';
			if (!empty($properties['id'])) {
				$out .= 'for="'.$properties['id'].'" ';
			}
			$out .= '>'.$properties['label'].'</label><select ';
		}
		else {
			$out = '<select ';
		}
		if (!empty($properties['id'])) {
			$out .= 'id = "'.$properties['id'].'" ';
		}
		if (!empty($properties['name'])) {
			$out .= 'name = "'.$properties['name'].'" ';
		}
		if (!empty($properties['class'])) {
			$out .= 'class ="';
			if (is_array($properties['class'])) {
				$out .= 'class =" ';
				foreach ($properties['class'] as $class) {
					$out .=$class.' ';
				}
			}
			else {
				$out .= 'class ="'.$properties['class'];
			}
			$out .= '" ';
		}
		if (!empty($properties['script'])) {
			if (is_array($properties['script'])) {
				foreach ($properties['script'] as $event => $script) {
					$out .= $event.' = " ';
					if (is_array($script)) {
						foreach ($script as $call) {
							$out .= $call.'; ';
						}
					}
					else {
						$out .= $script.'; "';
					}
				}
			}
		}
		$out .= '>';
		if(!empty($properties['title'])) {
			$out .= '<option id="0" ';
			if(empty($properties['selected'])) {
				$out .= 'selected="selected" ';
			}
			$out .= '>'.$properties['title'].'</option>';
		}
		foreach ($properties['values'] as $id => $value) {
			$out .= '<option id = "'.$id.'" ';
			if (!empty($properties['selected'])) {
				if ($properties['selected'] == $id) {
					$out .= 'selected="selected" ';
				}
			} 
			$out .= '>'.$value['name'].'</option>';
		}
		$out .= '</select>';
		return $out;
	}
}
?>