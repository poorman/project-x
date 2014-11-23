<?php session_start();
unset($_SESSION['lang']);
$_SESSION['lang'] = $_GET['language'];
 include_once('index.php');?>