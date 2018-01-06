<html>
<head>

</head>
<body>
<?php
echo "<br/>Server address ".$_SERVER['SERVER_ADDR'];
echo "<br/>Server getHostByAddress ".gethostbyaddr($_SERVER['SERVER_ADDR']);
echo "<br/>Server name ".$_SERVER['SERVER_NAME'];
echo "<br/>http host ".$_SERVER['HTTP_HOST'];
?>
</body>
</html>