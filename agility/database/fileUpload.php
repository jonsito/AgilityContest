<?php
include('conn.php');

if(isset($_POST['Submit'])) {
	$path = "uploads/";

	function getExtension($str) {
		$i = strrpos($str,".");
		if (!$i) { return ""; }
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		return $ext;
	}   
	
	$valid_formats = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
	
	if(isset($_POST) && $_SERVER['REQUEST_METHOD'] == "POST") 	{
		$name = $_FILES['photog']['name'];
		$size = $_FILES['photog']['size'];
		if(!strlen($name)) die ("Please select image..!");
		$ext = getExtension($name);
		if( ! in_array($ext,$valid_formats)) die( "Invalid file format..");
		if($size >(1024*1024)) die( "Image file size max 1 MB");
		$actual_image_name = time().substr(str_replace(" ", "_", $txt), 5).".".$ext;
		$tmp = $_FILES['photog']['tmp_name'];
		if(move_uploaded_file($tmp, $path.$actual_image_name))	{
			mysql_query("UPDATE profile SET profile_image='$actual_image_name' WHERE uid='$session_id'");
			echo "<img src='uploads/".$actual_image_name."'  class='preview'-->";
		}
	}
}
?>