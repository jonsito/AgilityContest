<?php
/*
Sesiones.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once("Eventos.php");
require_once(__DIR__."/../../auth/AuthManager.php");

class Sesiones extends DBObject {
	
	/**
	 * retrieve list of stored sessions
     * @param {array} data received data query parameters
     * @param {boolean} ring: if true exclude "-- Sin asignar --" session 1
	 * @return {array} session list in easyui json expected format, or error string
	 */
	function select($data,$ring=false) {
		$this->myLogger->enter();
		//needed to properly handle multisort requests from datagrid
		$sort=getOrderString(
				http_request("sort","s",""),
				http_request("order","s",""),
				"Nombre ASC, Comentario ASC"
		);
		// search string
		$search =  isset($_GET['where']) ? strval($_GET['where']) : '';
		// evaluate offset and row count for query
		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$limit="";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit="".$offset.",".$rows;
		}
		// if hidden==0 hide console related sessions
        $ringstr=" AND 1";
        if ($ring) $ringstr=" AND (Sesiones.ID > 1)";
        $searchstr=" AND 1";
        if ($search!=="") $searchstr = "AND (Nombre LIKE '%$search%')  AND ( ( Comentario LIKE '%$search%' ) OR ( Operador LIKE '%$search%') ) ";
        $hiddenstr=" AND 1";
        if ($data['Hidden']==0 ) $hiddenstr = "AND (Nombre != 'Console')";
		$result=$this->__select(
				/* SELECT */ "Sesiones.ID AS ID,Nombre,Comentario,Operador,Prueba,Jornada,Manga,Tanda,Login,Background,LiveStream,LiveStream2,LiveStream3",
				/* FROM */ "Sesiones,Usuarios",
				/* WHERE */ "( Sesiones.Operador = Usuarios.ID ) $hiddenstr $searchstr $ringstr",
				/* ORDER BY */ $sort,
				/* LIMIT */ $limit
		);
		$this->myLogger->leave();
		return $result;
	}

	/**
	 * Insert a new session into database
	 * @return {string} "" if ok; null on error
	 */
	function insert($data) {
		$this->myLogger->enter();

        // extraemos los valores del parametro
        $nombre =	$data['Nombre'];
        $comentario=$data['Comentario'];
        $prueba =	$data['Prueba'];
        $jornada =  array_key_exists('Jornada',$data)? $data['Jornada']:0; // cannot be null
        $manga = 	array_key_exists('Manga',$data)? $data['Manga']:0; // cannot be null
        $tanda = 	array_key_exists('Tanda',$data)? $data['Tanda']:0; // cannot be null
        $operador = array_key_exists('Operador',$data)? $data['Operador']:1; // cannot be null
        $sessionkey=array_key_exists('SessionKey',$data)?$data['SessionKey']:null;

		// componemos un prepared statement
		$sql ="INSERT INTO Sesiones (Nombre,Comentario,Prueba,Jornada,Manga,Tanda,Operador,SessionKey)
			   VALUES(?,?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('ssiiiiis',$nombre,$comentario,$prueba,$jornada,$manga,$tanda,$operador,$sessionkey);
		if (!$res) return $this->error($this->conn->error);	

		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
		$stmt->close();
		$this->myLogger->leave();
		return ""; 
	}

    // send events on change camera data
    private function sendCameraEvents($id,$flags) {
        // retrieve session data
        $sdata=$this->__getObject("Sesiones",$id);
        if (!$sdata) {
            $this->myLogger->error("sendCameraEvents: invalid session id:$id");
            return;
        }
        $evtmgr=new Eventos("Session::sendCameraEvents",$id,new AuthManager("Sesiones::sendCameraEvent"));
        $data=array (
            // common data for senders and receivers
            'ID'		=>	$id,
            'Session'	=> 	$id,
            'TimeStamp'	=> 	time(), /* date('Y-m-d H:i:s'),*/
            'Type' 		=> 	"camera",
            'Source'	=> 	$sdata->Nombre,
            // datos identificativos del evento que se envia
            'Pru' 	=> 	$sdata->Prueba,
            'Jor'	=>	$sdata->Jornada,
            'Mng'	=>	$sdata->Manga,
            'Tnd'	=>	$sdata->Tanda
        );
        if ($flags & 0x01)
            $evtmgr->putEvent(array_merge($data,array("Mode"=>"bg","Value"=>$sdata->Background)));
        if ($flags & 0x02)
            $evtmgr->putEvent(array_merge($data,array("Mode"=>"h264","Value"=>$sdata->LiveStream)));
        if ($flags & 0x04)
            $evtmgr->putEvent(array_merge($data,array("Mode"=>"ogv","Value"=>$sdata->LiveStream2)));
        if ($flags & 0x08)
            $evtmgr->putEvent(array_merge($data,array("Mode"=>"webm","Value"=>$sdata->LiveStream3)));
    }

	/**
	 * Update session data
	 * @param {integer} $id session ID primary key
	 * @param {integer} $data datos a actualizar. si cero o null no se tocan
	 * @return {string} "" on success; null on error
	 */
	function update($id,$data) {
		$this->myLogger->enter();
		if ($id==0) return $this->error("Invalid Session ID:$id");
		$now=date('Y-m-d G:i:s');
		$evtflags=0;
		$sql="UPDATE Sesiones SET LastModified='$now'";
		if (isset($data['Nombre']))		$sql .=", Nombre='{$data['Nombre']}' ";
		if (isset($data['Comentario']))	$sql .=", Comentario='{$data['Comentario']}' ";
		if (isset($data['Prueba']))		$sql .=", Prueba={$data['Prueba']} ";
		if (isset($data['Jornada']))	$sql .=", Jornada={$data['Jornada']} ";
		if (isset($data['Manga']))		$sql .=", Manga={$data['Manga']} ";
		if (isset($data['Tanda']))		$sql .=", Tanda={$data['Tanda']} ";
		if (isset($data['Operador']))	$sql .=", Operador={$data['Operador']} ";
		if (isset($data['SessionKey']))	$sql .=", SessionKey='{$data['SessionKey']}' ";
		if (isset($data['Background']))	 { $sql .=", Background='{$data['Background']}' "; $evtflags |= 1; }
		if (isset($data['LiveStream']))	 { $sql .=", LiveStream='{$data['LiveStream']}' "; $evtflags |= 2; }
		if (isset($data['LiveStream2'])) { $sql .=", LiveStream2='{$data['LiveStream2']}' "; $evtflags |= 4; }
		if (isset($data['LiveStream3'])) { $sql .=", LiveStream3='{$data['LiveStream3']}' "; $evtflags |= 8; }
		$sql .= "WHERE (ID=$id);";
		$res= $this->query($sql);
		if (!$res) return $this->error($this->conn->error);
		// on camera data changes propagate generate camera events
		if ($evtflags!=0) $this->sendCameraEvents($id,$evtflags);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Delete session with provided name
	 * @param {integer} $id ID primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($id) {
		$this->myLogger->enter();
		if ($id<=1) return $this->error("Invalid Session ID"); // cannot delete if juez<=default 
		$str="DELETE FROM Sesiones WHERE ( ID=$id )";
		$res= $this->query($str);
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}	
	
	/**
	 * Delete events related with requested session ID
	 * @param {integer} $id Session ID primary key
	 * @return "" on success ; otherwise null
	 */
	function reset($id) {
		$this->myLogger->enter();
		if ($id<0) return $this->error("Invalid Session ID ");
		$str="DELETE FROM Eventos WHERE ( Session=$id )";
		$res= $this->query($str);
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Select sesion with provided ID
	 * @param {string} $juez name primary key
	 * @return {array} data on success ; otherwise error string
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Session ID:$id"); // session ID must be positive greater than 0 

		// make query
		$obj=$this->__getObject("Sesiones",$id);
		if (!is_object($obj))	return $this->error("No Session found with provided ID=$id");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}
	
	/**
	 * Select sesion with provided ID
	 * @param {string} $juez name primary key
	 * @return {array} data on success ; otherwise error string
	 */
	function selectByNombre($nombre) {
		$this->myLogger->enter();
		if ($nombre==="") return $this->error("Invalid Session Name"); // session name should not be empty
		// make query
		$obj=$this->__selectObject("*","Sesiones","Nombre=$nombre");
		if (!is_object($obj))	return $this->error("No Session found with provided name='$nombre'");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}
	
	/* no select() function */
	
	function enumerate($q="") { // like select but with fixed order
		$this->myLogger->enter();
		// evaluate search criteria for query
		// $where="( Nombre != 'Console') ";
		// if ($q!=="") $where="AND (Nombre LIKE '%".$q."%' )";
		$where="";
		if ($q!=="") $where="(Nombre LIKE '%".$q."%' )";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Sesiones",
				/* WHERE */ $where,
				/* ORDER BY */ "Nombre ASC",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}

    /**
     * Retrieve every $_SESSION['ac_clients'] elements
     * @param {string} $type: "": any type / "livestream" "videowall" "chrono" "tablet"
     * @return array{rows,total}
     */
	function getClients($type="") {
        $res=array();
        $timestamp=time();
        session_start();
        if (!isset($_SESSION['ac_clients'])) $_SESSION['ac_clients']=array();
        foreach ( $_SESSION['ac_clients'] as $client) {
            $this->myLogger->trace("Session::getClients() parsing clientSession: $client");
            $a=explode(':',$client);
            // comprobamos expiracion
            if ( ($timestamp - intval($a[4]) ) > 300 ) $a[4]=0; // expire after 5 minutes
            if (intval($a[4])==0) continue;  // if expired, skip
            // compose item and insert into response if requested
            $item=array('Source'=>$a[0],'Session'=>$a[1],'View'=>$a[2],'Name'=>$a[3],'LastCall'=>$a[4]);
            if ( ($type==="") || ($type==$a[0]) ) array_push($res,$item); // if not requested skip
        }
        session_write_close();
        return array('total'=>count($res),'rows'=>$res);
    }

    /**
     * @param $name session name: source_sesid_view_name ( timestamp not included
     * @return {string} empty on success; else error message
     */
    function testAndSet($name="") {
        if ($name==="") {
            $this->myLogger->error("Sesiones::testAndSet() null SessionName provided");
            return "";
        }
        $timestamp=time();
        $found=false;
        session_start();
        if (!isset($_SESSION['ac_clients'])) $_SESSION['ac_clients']=array();
        $this->myLogger->trace("Session::testAndSet() looking for clientSession: $name");
        foreach ( $_SESSION['ac_clients'] as &$client) { // pass by reference as need to be edited
            $this->myLogger->trace("Session::testAndSet() parsing clientSession: $client");
            if (strpos($client,$name)===FALSE) {  // client name does not match: evaluate expiration
                $a=explode(':',$client);
                if ( ($timestamp - intval($a[4]) ) <= 300 ) continue; // expire after 5 minutes
                $a[4]=0;
                $client=implode(':',$a);
            } else { // item found: update timestamp
                $client="{$name}:{$timestamp}";
                $found=true;
            }
        }
        // arriving here means item not found, so create and insert
        if (!$found) array_push($_SESSION['ac_clients'],"{$name}:{$timestamp}");
        session_write_close();
        return "";
    }
}
?>