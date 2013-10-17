<?php

function do_log($str) { 
	error_log($str); 
}

function disable_gzip() {
	@ini_set('zlib.output_compression', 'Off');
	@ini_set('output_buffering', 'Off');
	@ini_set('output_handler', '');
	@apache_setenv('no-gzip', 1);
}


ini_set("log_errors",1);
ini_set("error_log","/tmp/json.log");

// disable_gzip();
?>