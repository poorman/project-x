<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package X
	Global Model: theme_model.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
class Example_model extends CI_Model
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
	function example() 
	{
		return '
	<div style="
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	border:1px solid #069;
	background:rgba(34,199,130,0.2);
	padding:5px 15px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	white-space:nowrap;
	">
	Global model works.
	</div>';
	}
}
?>