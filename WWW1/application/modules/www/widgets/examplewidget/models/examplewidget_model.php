<?php
class Examplewidget_model extends CI_Model {
	function __construct() {
		parent::__construct();
	}
	function example() {
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
	Widget model works.
</div>';
	}
}