<?php

function do_log($str) { 
	error_log($str); 
}

function log_enter($name="") {
	$parent=debug_backtrace()[1]['function'];
	do_log($name."::".$parent."() Enter");
}

function log_exit($name="") {
	$parent=debug_backtrace()[1]['function'];
	do_log($name."::".$parent."() Exit");
}

ini_set("log_errors",1);
ini_set("error_log","/tmp/json.log");

?>