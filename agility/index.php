<?php
    // just a simple redirector to public/index.php
    // from: https://stackoverflow.com/questions/15110355/how-to-safely-get-full-url-of-parent-directory-of-current-php-page
    $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'];
    $path= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $res=str_replace("index.php","public/index.php",$path,$count);
    if ($count===0) $url .= $res ."public/index.php";
    else $url .= $res;
    header("Location: {$url}",false);
    die();
?>
