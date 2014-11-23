<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package Cosmic 2
	Global Model: helpers/pagination_helper.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
/**
 * allows por native ci pagination fo use in ajax request
 *
 * @Param string
 * @Param string
 *
 * @return string
 */
	function ajax_pagination($html,$js_function)
	{
		$function = 'onclick="'.$js_function.'(this);return false;" ';
		$search = 'href="';
		$replace = $function.$search;
		return str_replace($search,$replace,$html);
	}