<?php

ini_set("log_errors",1);
ini_set("error_log","/tmp/json.log");

function do_log($str) { 
	error_log($str); 
}
?>