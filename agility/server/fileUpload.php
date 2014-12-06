<?php

/*
fileUpload.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

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