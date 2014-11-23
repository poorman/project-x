<?php
class Exampleplugin_model extends CI_Model {
	function __construct() {
		parent::__construct();
	}
	function example() {
		return 'Plugin Model works...';
	}
}