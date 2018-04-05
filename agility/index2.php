<?php
// just a simple redirector to public/index2.php
// from: https://stackoverflow.com/questions/15110355/how-to-safely-get-full-url-of-parent-directory-of-current-php-page
$url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$url .= $_SERVER['SERVER_NAME'];
$url .= htmlspecialchars($_SERVER['REQUEST_URI']);
$url= str_replace("index2.php","public/index2.php",$url);
header("Location: {$url}",false);
die();
?>
