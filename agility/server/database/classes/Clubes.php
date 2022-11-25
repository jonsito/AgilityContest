<?php
/*
clubes.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__ . "/../../modules/Federations.php");
require_once(__DIR__."/../procesaInscripcion.php");// to update inscription data

class Clubes extends DBObject {

	protected $curFederation=null;

	function __construct($file,$federation=0) {
		parent::__construct($file);
		if ($federation == -1) return; // do not initialize
		$this->curFederation=Federations::getFederation(intval($federation));
		if ($this->curFederation==null)
			throw new Exception("Clubes::construct() Federation ID:$federation does not exist");
	}

    protected $logoCache=array( "Perros" => array(), "Guias" => array(), "Clubes" => array(), "NombreClub" => array() );

	protected function checkForDuplicates($name,$id) {
        $name=$this->conn->real_escape_string($name);
        $where=($id===0)?"":" AND (ID!={$id})";
        $res=$this->__selectObject(
        /* select */ "count(*) AS Items",
            /* from */   "clubes",
            /* where */  "Nombre='{$name}' {$where}"
        );
        return $res->Items;
    }

    /**
	 * insert a new club into database
	 * @return {string} empty string if ok; else null
	 */
	function insert() {
		$this->myLogger->enter();
        // iniciamos los valores, chequeando su existencia
        // no need to escape strings as using prepared statements
        $nombre 	= http_request("Nombre","s",null,false);
        $direccion1 = http_request('Direccion1',"s",null,false);
        $direccion2 = http_request('Direccion2',"s",null,false);
        $provincia	= http_request('Provincia',"s",null,false);
        $pais	    = http_request('Pais',"s",'ESP');
        $contacto1	= http_request('Contacto1',"s",null,false);
        $contacto2	= http_request('Contacto2',"s",null,false);
        $contacto3	= http_request('Contacto3',"s",null,false);
        $gps		= http_request('GPS',"s",null,false);
        $web		= http_request('Web',"s",null,false);
        $email		= http_request('Email',"s",null,false);
        $federations= http_request('Federations',"i",1,false);
        $facebook	= http_request('Facebook',"s",null,false);
        $google		= http_request('Google',"s",null,false);
        $twitter	= http_request('Twitter',"s",null,false);
        $observaciones = http_request('Observaciones',"s",null,false);
        $baja		= http_request('Baja',"i",0);
        $logo       = $this->composeLogoName($nombre);
        // no permitimos insert/update cuando el club no se asigna a ninguna federacion
        if ($federations==0) return $this->error("Clubes::insert(): Federations field cannot be empty");
        // check for duplicated name
        $dups=$this->checkForDuplicates($nombre,0);
        if ($dups!=0) return $this->error(_("There is already a registered club with provided name"));
		// componemos un prepared statement
		$sql ="INSERT INTO clubes (Nombre,Direccion1,Direccion2,Provincia,Pais,Contacto1,Contacto2,Contacto3,GPS,
				Web,Email,Federations,Facebook,Google,Twitter,Observaciones,Baja,Logo)
			   VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('sssssssssssissssis',$nombre,$direccion1,$direccion2,$provincia,$pais,$contacto1,$contacto2,$contacto3,$gps,
				$web,$email,$federations,$facebook,$google,$twitter,$observaciones,$baja,$logo);
		if (!$res)  return $this->error($stmt->error);
		

		$this->myLogger->debug("Nombre: $nombre Direccion1: $direccion1 Contacto1: $contacto1 Observaciones: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
        if (!$res) return $this->error($stmt->error);
        // if running on master server set ServerID as insert_id
        $this->setServerID("clubes",$stmt->insert_id);
		$stmt->close();
		$this->myLogger->leave();
		return ""; // return ok
	}

	function updateInscripciones($id) {
        set_time_limit(0);
		// miramos las pruebas en las que el perro esta inscrito
		$res=$this->__select(
		/* SELECT */"inscripciones.*",
			/* FROM */	"inscripciones,pruebas,perroguiaclub",
			/* WHERE */	"(pruebas.ID=inscripciones.Prueba) AND (pruebas.Cerrada=0) AND (inscripciones.Perro=perroguiaclub.ID) AND (perroguiaclub.Club=$id)",
			/* ORDER BY */	"",
			/* LIMIT*/	""
		);
		if (!is_array($res)) return $this->conn->error;
		// actualizamos los datos de inscripcion de la prueba
		foreach($res['rows'] as $inscripcion) {
			procesaInscripcion($inscripcion['Prueba'],$inscripcion['ID']);
		}
		return "";
	}

	/**
	 * Update entry in database table "Clubs"
	 * @return string "" empty if ok; null on error
	 */
	function update($id) {
		$this->myLogger->enter();
		// cannot delete default club id or null club id
		if ($id<=1)  return $this->error("Clubes::update(): No club or invalid Club ID '$id' provided");

        // iniciamos los valores, chequeando su existencia
        // no need to escape http data: using prepared statements
        $nombre 	= http_request("Nombre","s",null,false);
        $direccion1 = http_request('Direccion1',"s",null,false);
        $direccion2 = http_request('Direccion2',"s",null,false);
        $provincia	= http_request('Provincia',"s",null,false);
        $pais	    = http_request('Pais',"s",'ESP');
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
        // no permitimos insert/update cuando el club no se asigna a ninguna federacion
        if ($federations==0) return $this->error("Clubes::update(): Federations field cannot be empty");
		// componemos un prepared statement
		$sql ="UPDATE clubes
				SET Nombre=? , Direccion1=? , Direccion2=? , Provincia=? , Pais=?,
				Contacto1=? , Contacto2=? , Contacto3=? , GPS=? , Web=? ,
				Email=? , Federations=?, Facebook=? , Google=? , Twitter=? , Observaciones=? , Baja=?
				WHERE ( ID=$id )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('sssssssssssissssi',$nombre,$direccion1,$direccion2,$provincia,$pais,$contacto1,$contacto2,$contacto3,$gps,
				$web,$email,$federations,$facebook,$google,$twitter,$observaciones,$baja);
		if (!$res) return $this->error($stmt->error);
		
		$this->myLogger->debug("Nombre: $nombre ID: $id Provincia: $provincia Direccion1: $direccion1 Contacto1: $contacto1 ");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
		$stmt->close();
		// update data on inscripciones
		$res=$this->updateInscripciones($id);
		$this->myLogger->leave();
		return $res;
	}
	
	function delete($id) {
		$this->myLogger->enter();
		// cannot delete default club id or null club id
		if ($id<=1)  return $this->error("No club or invalid Club ID '$id' provided");
		// fase 1: desasignar guias del club (assign to default club with ID=1)
		$res= $this->query("UPDATE guias SET Club=1  WHERE (Club=$id)");
		if (!$res) return $this->error($this->conn->error);
		// fase 2: borrar el club de la BBDD
		$res= $this->__delete("clubes","(ID={$id})");
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * retrieve all clubes from table, according sort, search and limit requested
	 * Also use current federation flag to filter national/international clubes
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
		// evaluate federation for club/country search according intl status
		$fedstr = "1";
		if ($this->curFederation!==null) {
			$fed=intval($this->curFederation->get('ID'));
			$intlmask=Federations::getInternationalMask(); // select non-international fedmask
			$natmask=~$intlmask;
			$fedstr=$this->curFederation->isInternational()?"((Federations & $intlmask)!=0)":"((Federations & $natmask)!=0)";
		}
		$limit = "";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit="".$offset.",".$rows;
		}
		$where="1";
		if ($search!=='') $where="( (Nombre LIKE '%$search%') OR (Provincia LIKE '%$search%') OR (Pais LIKE '%$search%') ) ";
		$result=$this->__select(
				/* SELECT */ "*, Logo AS LogoClub",
				/* FROM */ "clubes",
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
		$obj=$this->__getObject("clubes",$id);
		if (!is_object($obj))	return $this->error("No Club found with ID=$id");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}

	/** 
	 * return a dupla ID Nombre,Provincia list according select and federation criteria
	 * return data if success; null on error
	 */
	function enumerate() {
		$this->myLogger->enter();
		// evaluate search query string
		$q=http_request("q","s","");
        $c=http_request("Combo","i",0);
		// evaluate federation for club/country filtering
		$fedstr = "1";
		if ($this->curFederation!==null) {
			$fed=intval($this->curFederation->get('ID'));
			$mask=1<<$fed;
			$intlmask=Federations::getInternationalMask();
			$fedstr=$this->curFederation->isInternational()?"((Federations & $intlmask)!=0)":"((Federations & $mask)!=0)";
		}
		$where="1";
		if ($q!=="") $where="( Nombre LIKE '%".$q."%' )";
		$result=$this->__select(
				/* SELECT */ "ID,Nombre,Provincia,Pais,Federations,Email",
				/* FROM */ "clubes",
				/* WHERE */ "$fedstr AND $where",
				/* ORDER BY */ "Nombre ASC",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return ($c==0)?$result:$result['rows']; // in combo mode just result rows
	}

    // compose logo file name based in club name, instead (old) club ID
	private function composeLogoName($name) {
        // Remove all (back)slashes from name
        $logo = str_replace('\\', '', $name);
        $logo = str_replace('/', '', $logo);
        // Remove all characters that are not the separator, a-z, 0-9, or whitespace
        $logo = preg_replace('![^'.preg_quote('-').'a-z0-_9\s]+!', '', strtolower($logo));
        // Replace all separator characters and whitespace by a single separator
        $logo = preg_replace('!['.preg_quote('-').'\s]+!u', '_', $logo);
        $logo="$logo.png";
        return $logo;
    }

    /**
     * Retorna el logo asociado al club de id indicado
     * NOTA: esto no retorna una respuesta json, sino una imagen
     * @param $id id club
     * @return null|string {string} "" on success; else error string
     */
	function getLogo($id,$perro=0) {
		$this->myLogger->enter();
		if ($id==0) $id=1;
        $row=$this->__selectObject("Logo","clubes","ID={$id}");
		if (!$row) return $this->error($this->conn->error);
		$name=$row->Logo;
		$fname=getIconPath($this->curFederation->get('Name'),$name);
		if (!file_exists($fname)) {
			$this->myLogger->notice("Logo file $fname does not exists");
			$fname=getIconPath($this->curFederation->get('Name'),$this->curFederation->get('Logo')); // use default name
		}
		$size = getimagesize($fname);
        header("Access-Control-Allow-Origin: https://{$_SERVER['SERVER_NAME']}",false);
		header('Content-Type: '.$size['mime']);
		readfile($fname);
        return "";
	}
	
	/**
	 * Retorna el logo del club asociado al guia del perro de id indicado
	 * NOTA: esto no retorna una respuesta json, sino una imagen
	 * @param {integer} $id perro id
     * @return "" on success; else error string
	 */
	function getLogoByPerro($id) {
		$this->myLogger->enter();
		if ($id==0) $id=1; // on insert, select default logo
		$row=$this->__selectObject("LogoClub AS Logo","perroguiaclub","(ID=$id)");
		if (!$row) return $this->error($this->conn->error);
		$name=$row->Logo;
		$fname=getIconPath($this->curFederation->get('Name'),$name);
		if (!file_exists($fname)) {
			$this->myLogger->notice("Logo file $fname does not exists");
			$fname=getIconPath($this->curFederation->get('Name'),$this->curFederation->get('Logo')); // use default name
		}
		$size = getimagesize($fname);
        header("Access-Control-Allow-Origin: https://{$_SERVER['SERVER_NAME']}",false);
		header('Content-Type: '.$size['mime']);
		readfile($fname);
        return "";
	}
	
	/**
	 * Retorna el logo del club asociado al guia de id indicado
	 * NOTA: esto no retorna una respuesta json, sino una imagen
	 * @param {integer} $id perro id
     * @return "" on success; else error string
	 */
	function getLogoByGuia($id) {
		$this->myLogger->enter();
		if ($id==0) $id=1; // on insert, select default logo
		$row=$this->__selectObject("Logo","guias,clubes","(guias.club=clubes.ID) AND (guias.ID=$id)");
		if (!$row) return $this->error($this->conn->error);
		$name=$row->Logo;
		$fname=getIconPath($this->curFederation->get('Name'),$name);
		if (!file_exists($fname)) {
			$this->myLogger->notice("Logo file $fname does not exists");
			$fname=getIconPath($this->curFederation->get('Name'),$this->curFederation->get('Logo')); // use default name
		}
		$size = getimagesize($fname);
        header("Access-Control-Allow-Origin: https://{$_SERVER['SERVER_NAME']}",false);
		header('Content-Type: '.$size['mime']);
		readfile($fname);
        return "";
	}

    /**
     * Store logo image and name into database
     * @param {integer} $id Club ID
     * @return {string} "" on success; else error
     */
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
		// $type=$matches[1]; // 'image/png' , 'image/jpeg', or whatever. Not really used
		$image=base64_decode( str_replace(' ', '+', $matches[2]) ); // also replace '+' to spaces or newlines 
		$img=imagecreatefromstring( $image  ); 
		if (!$img) return $this->error("Invalid received image string data:'$imgstr'");
		
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
		$row=$this->__selectObject("Nombre,Logo","clubes","ID=$id");
		if (!$row) return $this->error($this->conn->error);
		$logo=$row->Logo;
		$name=$row->Nombre;
		// 4- si es igual al default, generamos un nuevo nombre para el logo basado en el nombre del club
		// 	y actualizamos el nombre en la bbdd
		if (Federations::logoMatches($logo)) {
		    $logo=$this->composeLogoName($logo);
			$sql="UPDATE clubes SET Logo='$logo' WHERE (ID=$id)";
			$res=$this->query($sql);
			if (!$res) return $this->error($this->conn->error);
		}
		// 5- finalmente guardamos el logo en el fichero especificado en formato png
		$fname=__DIR__."/../../../images/logos/$logo"; // default path to store logos
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

    /**
     * A sort of logo name cache to store and retrieve logo names
     * @param {string} $key search family ( club, perro, guia, ...)
     * @param {mixed} $id id or name
     * @return {string} found logo, or federation related one
     */
    function getLogoName($key,$id) {
        if (!array_key_exists($key,$this->logoCache)){
            $this->myLogger->error("getLogoName(): invalid search key: $key");
            return "agilitycontest.png"; // defaults to app logo
        }
        // if already exists just return
        if (array_key_exists($id,$this->logoCache[$key])) {
            // $this->myLogger->trace("getLogoName(): cache hit '$key' => '$id' ");
            return $this->logoCache[$key][$id];
        }
        // $this->myLogger->trace(json_encode($this->logoCache));
        // $this->myLogger->trace("getLogoName(): cache miss '$key' => '$id' ");
        // else ask database and fill cache
        switch($key) {
            case "Perros": // $id= Dog ID
                $data= $this->__selectAsArray("*","perroguiaclub","(perroguiaclub.ID=$id)");
                $logo=$data['LogoClub'];
                $this->logoCache['Perros'][$id] = $logo;
                $this->logoCache['Guias'][$data['Guia']] =  $logo;
                $this->logoCache['NombreClub'][$data['NombreClub']] = $logo;
                $this->logoCache['Clubes'][$data['Club']] = $logo;
                break;
            case "Guias"; // $id: Guia ID
                $data= $this->__selectAsArray(
                    "clubes.ID as Club, clubes.Nombre AS NombreClub, clubes.Logo as Logo",
                    "guias,clubes",
                    " (guias.ID=$id) AND (guias.Club=clubes.ID) ");
                $logo=$data['Logo'];
                $this->logoCache['Guias'][$id] = $logo;
                $this->logoCache['NombreClub'][$data['NombreClub']] = $logo;
                $this->logoCache['Clubes'][$data['Club']] = $logo;
                break;
            case "Clubes": // provided Club ID
                $data= $this->__selectAsArray("*","clubes"," (ID=$id) ");
                $logo=$data['Logo'];
                $this->logoCache['NombreClub'][$data['Nombre']] = $logo;
                $this->logoCache['Clubes'][$id] = $logo;
                break;
            case "NombreClub": // Provided Club Name
                $nombre=escapeString($id); //escape to avoid sql errors
                $data= $this->__selectAsArray("*","clubes"," (Nombre='$nombre') ");
                if ($data==null) return "agilitycontest.png";
                else $logo=$data['Logo'];
                $this->logoCache['NombreClub'][$id] =$logo;
                $this->logoCache['Clubes'][$data['ID']] = $logo;
                break;
        }
        return $this->logoCache[$key][$id];
    }
} /* end of class "Clubes" */

?>