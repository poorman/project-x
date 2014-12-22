<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	Date: 10/14/2014
	framework Codeigniter 2
	Package Cosmic 2
	Global Model: helpers/dev_helper.php
	Author: Sebastian Rzeszowicz for system-work.com
	Email: sebastian@system-work.com
*/
function log_error($msg='no messge received') {
	ob_start();
	echo date('m-d-Y H:i:s').' url: '.$_SERVER['REQUEST_URI']."\r\n";
	dump($msg);
	$msg = ob_get_clean();
	$date = date('m_d_Y');
	$handle = fopen('errors_'.$date.'_.txt', 'a+');
	fwrite($handle,$msg."\r\n\r\n");   
}
function log_msg($msg='no messge received', $truncate_first = TRUE) {
	ob_start();
	echo '<h3>url: '.$_SERVER['REQUEST_URI'].'</h3><br/>';
	dump($msg);
	$msg = ob_get_clean();
	if($truncate_first) $handle = fopen('debug.html', 'w+');
	else $handle = fopen('debug.html', 'a+');
	fwrite($handle,$msg.'<br/>');   
}
function dump($data) {
	echo '<pre>'; var_dump($data); echo '</pre>';
}
function account_dropdown() {
	$ci =& get_instance();
	$ci->db->order_by('name','ASC');
	$query = $ci->db->get('accounts');
	$accounts = $query->result();
	$dropdown = '<select id="account" name="account"><option value="">Select Account</option>';
	foreach($accounts as $account) {
		$dropdown .= '<option value="'.$account->system_name.'">'.$account->name.'</option>';
	}
	$dropdown .= '</select>';
	return $dropdown;
}
/**
 * Quick Echo
 *
 * Outputs values for debugging in development. Does not do anything
 * in a production environment.
 *
 * @param mixed $s
 */
function qe($value) {
	if (ENVIRONMENT==='production') {
		return;
	}
	$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	$value = print_r($value, true);
	$id = uniqid('qe-');
	echo "
		<style>
			#{$id} {
				font-family:monospace !important;
				font-size:10px;
				white-space:pre-wrap;
				word-wrap: break-word;
			}
		</style>
		<pre id='{$id}'>qe() in {$backtrace[0]['file']} at line {$backtrace[0]['line']}:\n\n{$value}</pre>
	";
}
if ( ! function_exists('vd') ) {
	function vd($s) {
		if (ENVIRONMENT==='production') {
			return;
		}
		echo '<pre>'.var_dump($s, true).'</pre>';
	}
}
if ( ! function_exists('qee')) {
	function qee() {
		if (ENVIRONMENT==='production') {
			return;
		}
		$s = func_get_args();
		for ($i=0,$cnt=count($s); $i<$cnt; $i++) {
			 if (!is_object($s[$i]) and $i==0) {
				 echo '*** ' . (string) $s[$i] . ' ***';
			 }
			 else {
				 echo '*** Object ***';
			}
			if(is_array($s[$i]) or is_object($s[$i])) {
				echo '<pre>'.print_r($s[$i], true).'</pre>';
			}
			else {
				echo '<h4 style="font-weight:700;">' . $s[$i] . '</h4>';
			}
		}
	}
}

#Quick echo but a little upgraded and it exits after the echo -- Masterjedi 2/16/2013
function qeex($var,$class=NULL) {
	if (ENVIRONMENT==='production') {
		return;
	}
	if (is_array($var)) {
		print '<pre>';print_r($var);print'</pre>';
	}
	else {
		if($class!=NULL) $class=' class="'.$class.'"';
		echo "<div$class>$var</div>";
	}
	exit;
}

/**
 * Logs a message with filename and line number
 *
 * @param string $m message
 * @param string $file filename
 * @param string $line line number
 */
function debug($m='debug',$file='unknown',$line='unknown') {
	log_message('error',"$m ~~~ file: $file on line: $line");
}

function timestamp($time) {
	$CI = &get_instance();
	$UserModel = $CI->load->model('UserModel');
	if (isset($UserModel->timezone)) {
		$new_time = $time + date('Z');
		return $new_time;
	}
	else {
		return $time;
	}
}
if (! function_exists('_db_debug_write_log')) {
	function _db_debug_write_log($msg) {
		$filepath = '/tmp/db_log-'.date('Y-m-d').'.php';
		$message='';
		if ( ! file_exists($filepath)) {
			$newfile = TRUE;
			$message .= '<'."?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
		}
		if ( ! $fp = @fopen($filepath, FOPEN_WRITE_CREATE)) {
			return FALSE;
		}
		$message=' '.date('Y-m-d').' --> '.$msg."\n";
		flock($fp, LOCK_EX);
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);
		if (isset($newfile) && $newfile === TRUE) {
			@chmod($filepath, FILE_WRITE_MODE);
		}
		return TRUE;
	}
}

function debug_sidebar() {
	if (ENVIRONMENT == 'production') {
		return;
	}
	$data = array();
	$data['controller'] = get_instance();
	$data['render_time_ms'] = number_format((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2);
	$data['memory_mb'] = number_format(memory_get_peak_usage(true) / 1000 / 1000, 2);
	$data['query_count'] = count($data['controller']->db->conn_id->queries);
	$query_time = 0;
	foreach($data['controller']->db->conn_id->queries as $query) {
		$query_time += $query['duration'];
	}
	$data['query_time_ms'] = number_format($query_time * 1000, 2);
	$data['controller']->load->view('debug_sidebar', $data);
}
