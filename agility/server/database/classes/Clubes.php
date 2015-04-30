<?php
/*
clubes.php

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


require_once("DBObject.php");

class Clubes extends DBObject {

	/* use parent constructor and destructors */
	
	/**
	 * insert a new club into database
	 * @return {string} empty string if ok; else null
	 */
	function insert() {
		$this->myLogger->enter();
		// componemos un prepared statement
		$sql ="INSERT INTO Clubes (Nombre,Direccion1,Direccion2,Provincia,Contacto1,Contacto2,Contacto3,GPS,
				Web,Email,Federations,Facebook,Google,Twitter,Observaciones,Baja)
			   VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssssssssisssss',$nombre,$direccion1,$direccion2,$provincia,$contacto1,$contacto2,$contacto3,$gps,
				$web,$email,$federations,$facebook,$google,$twitter,$observaciones,$baja);
		if (!$res)  return $this->error($this->conn->error);
		
		// iniciamos los valores, chequeando su existencia
		$nombre 	= http_request("Nombre","s",null,false);
		$direccion1 = http_request('Direccion1',"s",null,false);
		$direccion2 = http_request('Direccion2',"s",null,false); 
		$provincia	= http_request('Provincia',"s",null,false);
		$contacto1	= http_request('Contacto1',"s",null,false);
		$contacto2	= http_request('Contacto2',"s",null,false);
		$contacto3	= http_request('Contacto3',"s",null,false);
		$gps		= http_request('GPS',"s",null,false);
		$web		= http_request('Web',"s",null,false);
		$email		= http_request('Email',"s",null,false);
		$federations= http_request('Federations',"i",1);
		$facebook	= http_request('Facebook',"s",null,false);
		$google		= http_request('Google',"s",null,false);
		$twitter	= http_request('Twitter',"s",null,false);
		$observaciones = http_request('Observaciones',"s",null,false);
		$baja		= http_request('Baja',"i",0);
		$this->myLogger->debug("Nombre: $nombre Direccion1: $direccion1 Contacto1: $contacto1 Observaciones: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return ""; // return ok
	}
	
	/**
	 * Update entry in database table "Clubs"
	 * @return string "" empty if ok; null on error
	 */
	function update($id) {
		$this->myLogger->enter();
		// cannot delete default club id or null club id
		if ($id<=1)  return $this->error("No club or invalid Club ID '$id' provided");
		// componemos un prepared statement
		$sql ="UPDATE Clubes
				SET Nombre=? , Direccion1=? , Direccion2=? , Provincia=? ,
				Contacto1=? , Contacto2=? , Contacto3=? , GPS=? , Web=? ,
				Email=? , Federations=?, Facebook=? , Google=? , Twitter=? , Observaciones=? , Baja=?
				WHERE ( ID=$id )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssssssssissssi',$nombre,$direccion1,$direccion2,$provincia,$contacto1,$contacto2,$contacto3,$gps,
				$web,$email,$federations,$facebook,$google,$twitter,$observaciones,$baja);
		if (!$res) return $this->error($stmt->error);
		// iniciamos los valores, chequeando su existencia
		$nombre 	= http_request("Nombre","s",null,false);
		$direccion1 = http_request('Direccion1',"s",null,false);
		$direccion2 = http_request('Direccion2',"s",null,false); 
		$provincia	= http_request('Provincia',"s",null,false);
		$contacto1	= http_request('Contacto1',"s",null,false);
		$contacto2	= http_request('Contacto2',"s",null,false);
		$contacto3	= http_request('Contacto3',"s",null,false);
		$gps		= http_request('GPS',"s",null,false);
		$web		= http_request('Web',"s",null,false);
		$email		= http_request('Email',"s",null,false);
		$federations= http_request('Federations',"i",2);
		$facebook	= http_request('Facebook',"s",null,false);
		$google		= http_request('Google',"s",null,false);
		$twitter	= http_request('Twitter',"s",null,false);
		$observaciones = http_request('Observaciones',"s",null,false);
		$baja		= http_request('Baja',"i",0);
		
		$this->myLogger->debug("Nombre: $nombre ID: $id Provincia: $provincia Direccion1: $direccion1 Contacto1: $contacto1 ");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
		$this->myLogger->leave();
		$stmt->close();
		return "";
	}
	
	function delete($id) {
		$this->myLogger->enter();
		// cannot delete default club id or null club id
		if ($id<=1)  return $this->error("No club or invalid Club ID '$id' provided");
		// fase 1: desasignar guias del club (assign to default club with ID=1)
		$res= $this->query("UPDATE Guias SET Club=1  WHERE (Club=$id)");
		if (!$res) return $this->error($this->conn->error);
		// fase 2: borrar el club de la BBDD
		$res= $this->query("DELETE FROM Clubes WHERE (ID=$id)");
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * retrieve all clubes from table, according sort, search and limit requested
	 */
	function select() {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$sort=getOrderString( //needed to properly handle multisort requests from datagrid
			http_request("sort","s",""),
			http_request("order","s",""),
			"Nombre ASC"
		);
		$search=http_Request("where","s","");
		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$fed=http_request("Federation","i",-1); // -1: any 0:rsce 1:rfec 2:uca
		$fedstr = "1";
		switch (intval($fed)) {
			case -1: break; // any
			case 0: $fedstr="((Federations & 1)!=0)"; break; // rsce
			case 1: $fedstr="((Federations & 2)!=0)"; break; // rfec
			case 2: $fedstr="((Federations & 4)!=0)"; break; // uca
			default: return $this->error("Clubes::select() Invalid Federation:$fed");
		}
		$limit = "";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit="".$offset.",".$rows;
		}
		$where="1";
		if ($search!=='') $where="( (Nombre LIKE '%$search%') OR ( Email LIKE '%$search%') OR ( Facebook LIKE '%$search%') ) ";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Clubes",
				/* WHERE */ "$fedstr AND $where",
				/* ORDER BY */ $sort,
				/* LIMIT */ $limit
		);
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Obtiene los datos del club con el ID indicado
	 * Usado para rellenar formularios:  formid.form('load',url);
	 * @param {integer} $id Club primary key
	 * @return null on error; array() with data on success
	 */
	function selectByID($id){
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Club ID:$id");
		// make query
		$obj=$this->__getObject("Clubes",$id);
		if (!is_object($obj))	return $this->error("No Dog found with ID=$id");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}
	/** 
	 * return a dupla ID Nombre,Provincia list according select criteria
	 * return data if success; null on error
	 */
	function enumerate() {
		$this->myLogger->enter();
		// evaluate search query string
		$q=http_request("q","s","");
		$f=http_request("Federation","i",-1);
		$fedstr = "1";
		switch (intval($f)) {
			case -1: break; // any
			case 0: $fedstr="((Federations & 1)!=0)"; break; // rsce
			case 1: $fedstr="((Federations & 2)!=0)"; break; // rfec
			case 2: $fedstr="((Federations & 4)!=0)"; break; // uca
			default: return $this->error("Clubes::enumerate() Invalid Federation:$f");
		}
		$where="1";
		if ($q!=="") $where="( Nombre LIKE '%".$q."%' )";
		$result=$this->__select(
				/* SELECT */ "ID,Nombre,Provincia",
				/* FROM */ "Clubes",
				/* WHERE */ "$fedstr AND $where",
				/* ORDER BY */ "Nombre ASC",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
	
	/** 
	 * Retorna el logo asociado al club de id indicado
	 * NOTA: esto no retorna una respuesta json, sino una imagen
	 * @param {integer} $id club id
	 */
	function getLogo($id) {
		$this->myLogger->enter();
		if ($id==0) $id=1; // on insert, select default logo
		$row=$this->__selectObject("Logo","Clubes","ID=$id");
		if (!$row) return $this->error($this->conn->error);
		$name=$row->Logo;
		$fname=__DIR__."/../../../images/logos/$name";
		if (!file_exists($fname)) {
			$this->myLogger->notice("Logo file $fname does not exists");
			$fname=__DIR__."/../../../images/logos/rsce.png"; // use default name
		}
		$size = getimagesize($fname);
		header('Content-Type: '.$size['mime']);
		readfile($fname);
        return "";
	}
	
	/**
	 * Retorna el logo del club asociado al guia del perro de id indicado
	 * NOTA: esto no retorna una respuesta json, sino una imagen
	 * @param {integer} $id perro id
	 */
	function getLogoByPerro($id) {
		$this->myLogger->enter();
		if ($id==0) $id=1; // on insert, select default logo
		$row=$this->__selectObject("Logo","Perros,Guias,Clubes","(Perros.Guia=Guias.ID ) AND (Guias.Club=Clubes.ID) AND (Perros.ID=$id)");
		if (!$row) return $this->error($this->conn->error);
		$name=$row->Logo;
		$fname=__DIR__."/../../../images/logos/$name";
		if (!file_exists($fname)) {
			$this->myLogger->notice("Logo file $fname does not exists");
			$fname=__DIR__."/../../../images/logos/rsce.png"; // use default name
		}
		$size = getimagesize($fname);
		header('Content-Type: '.$size['mime']);
		readfile($fname);
        return "";
	}
	
	/**
	 * Retorna el logo del club asociado al guia de id indicado
	 * NOTA: esto no retorna una respuesta json, sino una imagen
	 * @param {integer} $id perro id
	 */
	function getLogoByGuia($id) {
		$this->myLogger->enter();
		if ($id==0) $id=1; // on insert, select default logo
		$row=$this->__selectObject("Logo","Guias,Clubes","(Guias.Club=Clubes.ID) AND (Guias.ID=$id)");
		if (!$row) return $this->error($this->conn->error);
		$name=$row->Logo;
		$fname=__DIR__."/../../../images/logos/$name";
		if (!file_exists($fname)) {
			$this->myLogger->notice("Logo file $fname does not exists");
			$fname=__DIR__."/../../../images/logos/rsce.png"; // use default name
		}
		$size = getimagesize($fname);
		header('Content-Type: '.$size['mime']);
		readfile($fname);
        return "";
	}
	
	function setLogo($id) {
		$this->myLogger->enter();
		// el logo 1 NO es editable y debe ser siempre "rsce.png"
		if ($id<=1) return $this->error("Cannot change Logo for club ID:$id");
		// 1- leemos la imagen que nos viene en el post extrayendo el tipo y los datos
		$imgstr=http_request("imagedata","s",null);
		if (!$imgstr) return $this->error("No image data received for club ID:$id");
		if (!preg_match('/data:([^;]*);base64,(.*)/', $imgstr, $matches)) {
			return $this->error("Invalid received image string data:'$imgstr'");
		}
		$type=$matches[1]; // 'image/png' , 'image/jpeg', or whatever. Not really used
		$image=base64_decode( str_replace(' ', '+', $matches[2]) ); // also replace '+' to spaces or newlines 
		$img=imagecreatefromstring( $image  ); 
		if (!$img) return $this->error("Invalid received image string data:'$imgstr'");
		// imagepng($img,__DIR__."/../../../images/logos/image_tmp.png");
		
		// 2- creamos una imagen de 150x150, le anyadimos canal alfa, y hacemos un copyresampled
		$newImage = imagecreatetruecolor(150,150);
		imagealphablending($newImage, true);
		imagesavealpha($newImage, true);
		// Allocate a transparent color and fill the new image with it.
		// Without this the image will have a black background instead of being transparent.
		$transparent = imagecolorallocatealpha( $newImage, 0, 0, 0, 127 );
		imagefill( $newImage, 0, 0, $transparent ); 
		imagecopyresampled($newImage, $img, 0, 0, 0, 0, 150, 150, imagesx($img), imagesy($img));
		
		// 3- obtenemos el nombre del logo actual
		$row=$this->__selectObject("Nombre,Logo","Clubes","ID=$id");
		if (!$row) return $this->error($this->conn->error);
		$logo=$row->Logo;
		$name=$row->Nombre;
		// 4- si es igual al default, generamos un nuevo nombre para el logo basado en el nombre del club
		// 	y actualizamos el nombre en la bbdd 
		if ( ($logo==="rsce.png") || ($logo==="uca.png") || ($logo==="rfec.png") ){
			// compose logo file name based in club name, instead (old) club ID
			// Remove all (back)slashes from name
			$logo = str_replace('\\', '', $name);
			$logo = str_replace('/', '', $logo);
			// Remove all characters that are not the separator, a-z, 0-9, or whitespace
			$logo = preg_replace('![^'.preg_quote('-').'a-z0-_9\s]+!', '', strtolower($logo));
			// Replace all separator characters and whitespace by a single separator
			$logo = preg_replace('!['.preg_quote('-').'\s]+!u', '_', $logo);
			$logo="$logo.png";
			
			$sql="UPDATE Clubes SET Logo='$logo' WHERE (ID=$id)";
			$res=$this->query($sql);
			if (!$res) return $this->error($this->conn->error);
		}
		// 5- finalmente guardamos el logo en el fichero especificado en formato png
		$fname=__DIR__."/../../../images/logos/$logo";
		// $this->myLogger->info("Trying to save png file to:'$fname'");
		imagepng($newImage, $fname);
		// seems that imagepng fails on save to file due to strange permission related issue
		// ob_start();// store output
		// imagePNG($newImage);// output to buffer
		// file_put_contents($fname, ob_get_contents(), FILE_BINARY);// write buffer to file
		// ob_end_clean();// clear and turn off buffer
		
		// 6- limpiamos y retornamos OK
		imagedestroy($img); 
		imagedestroy($newImage);
		$this->myLogger->leave();
		return "";
	}
	
	function testLogo($id) {
		// just receive, resample and resend received image
		$this->myLogger->enter();
		// extraemos la imagen
		$imgstr=http_request("imagedata","s",null);
		if (!$imgstr) return $this->error("No image data received for club ID:$id");
		if (!preg_match('/data:([^;]*);base64,(.*)/', $imgstr, $matches)) {
			return $this->error("Invalid received image string data:'$imgstr'");
		}
		$type=$matches[1]; // 'image/png' , 'image/jpeg', or whatever. Not really used
		$image=base64_decode( str_replace(' ', '+', $matches[2]) ); // also replace '+' to spaces or newlines
		$img=imagecreatefromstring($image);
		if (!$img) return $this->error("Invalid received image string data:'$imgstr'");
		
		// creamos una imagen de 150x150, le anyadimos canal alfa, y hacemos un copyresampled
		$newImage = imagecreatetruecolor(150,150);
		imagealphablending($newImage, true);
		imagesavealpha($newImage, true);
		// Allocate a transparent color and fill the new image with it.
		// Without this the image will have a black background instead of being transparent.
		$transparent = imagecolorallocatealpha( $newImage, 0, 0, 0, 127 );
		imagefill( $newImage, 0, 0, $transparent );
		imagecopyresampled($newImage, $img, 0, 0, 0, 0, 150, 150, imagesx($img), imagesy($img));
		
		// Now, time to send image back to navigator
		// due to stupid ajax, we need to base64 encode image before send it
		ob_start();
		// enable oytput buffering
		imagepng($newImage);
		$imagedata = ob_get_contents();	// Capture the output
		ob_end_clean();	// Clear the output buffer
		echo 'data:image/png;base64,'.base64_encode($imagedata); // y la reenviamos ya codificada
		
		// cleanup
		imagedestroy($img); 
		imagedestroy($newImage);
		$this->myLogger->leave();
        return "";
	}
} /* end of class "Clubes" */

?>