<?php
require_once("DBConnection.php");
require_once("Jornadas.php");
require_once("OrdenSalida.php"); // to insert/remove inscriptions from mangas

class Inscripciones {
	protected $conn;
	protected $file;
	public $errormsg; // should be public to access to from caller
	protected $prueba;
	protected $jornadas; // array of jornadas for this prueba
	
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $prueba Prueba ID
	 * @param {integer} $jornada Jornada ID
	 * @throws Exception if cannot contact database or invalid prueba/jornada ID
	 */
	function __construct($file,$prueba) {
		if ($prueba<=0 || $jornada<=0 ) {
			$this->errormsg="$file::construct() invalid prueba:$prueba or jornada:$jornada ID";
			throw new Exception($this->errormsg);
		}
		// connect database
		$this->file=$file;
		$this->prueba=$prueba;
		$this->conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
		if (!$this->conn) {
			$this->errormsg="$file::construct() cannot contact database";
			throw new Exception($this->errormsg);
		}
		// obtenemos la lista de jornadas asociadas a esta prueba
		$j=new Jornadas("inscripciones",$prueba);
		$res=$j->selectByPrueba();
		if ( ($res===null) || ($res==="") ) {
			$this->errormsg="$file::construct() cannot get list of Jornadas for this prueba";
			throw new Exception($this->errormsg);
		}
		$this->jornadas=array();
		foreach($res["rows"] as $item) { 
			$this->jornadas[$item["Numero"]]=$item;
		} 
	}
	
	/**
	 * Destructor
	 * Just disconnect from database
	 */
	function  __destruct() {
		DBConnection::closeConnection($this->conn);
	}

	/**
	 * Actualiza el orden de salida si es necesario
	 * TODO: move this function to OrdenSalida.php class
	 * @param {integer} $jornada
	 * @param {integer} $dorsal
	 * @param {integer} $celo
	 * @param {integer} $mode 0:insert 1:update 2:delete
	 * @return "" on success; null on error
	 */
	function updateOrdenSalida($jornada,$dorsal,$celo,$mode) {
		// obtenemos datos del perro
		$str="SELECT * from PerroGuiaClub WHERE (Dorsal=$dorsal)";
		do_log("inscripcion::updateOrdenSalida() $str");
		$rs=$this->conn->query($str);
		if ($rs===false) { // no deberia ocurrir
			$this->errormsg="inscripcion::updateOrdenSalida() getDogData() Error".$this->conn->error;
			return null;
		}
		$perro=$rs->fetch_object();
		$rs->free();
	
		// buscamos la lista de mangas que tiene la jornada
		$str="SELECT ID, Tipo_Manga.Grado AS Grado
		FROM Mangas,Tipo_Manga
		WHERE ( ( Jornada = $jornada ) AND ( Mangas.Tipo = Tipo_Manga.Tipo) )
		ORDER BY Descripcion ASC";
		do_log("inscriptionFunctions::updateOrdenSalida() $str");
		$rs=$this->conn->query($str);
		if(!$rs) {
			$this->errormsg="inscripcion::updateOrdenSalida() getListaMangas() Error".$this->conn->error;
			return null;
		}
		// retrieve result into an array
		while($row = $rs->fetch_object()){
			$mangaid=$row->ID;
			$mangagrado=$row->Grado;
			
			// obtenemos un manejador de ordenes de salida
			$os=new OrdenSalida($conn,"inscriptionFunctions");
			// si la categoria no es compatible, intentamos eliminar el perro de la manga
			if (($mangagrado !== '-') && ($mangagrado !== $perro->Grado)) {
				do_log("Grado del dorsal ".$dorsal." (".$perro->Grado.") no compatible con grado de manga (".$mangagrado.") " );
				$os->remove($jornada,$mangaid,$dorsal);
				continue;
			}
			// si la categoria es compatible compatible: obtenemos el orden de salida
			$orden=$os->getOrden($mangaid);
			// si el orden es nulo, quiere decir manga no iniciada -> no hace falta hacer nada
			if ($orden==="") continue;
			// si orden no nulo, vemos que hay que hacer con el perro
			switch($mode) {
				case 0: $os->insert($jornada,$mangaid,$dorsal); break;
				case 1: $os->update($jornada,$mangaid,$dorsal); break;
				case 2: $os->remove($jornada,$mangaid,$dorsal); break;
			}
		}
		$rs->free();
		return "";
	}
	
		
	/**
	 * insert/update/delete a new inscripcion into database
	 * @return empty string if ok; else null
	 */
	function doit() {
		do_log("inscripcion::doit() enter");

		// variables comunes a todas las jornadas
		$dorsal=http_request("Dorsal","i",0);
		if ($dorsal==0) {
			$this->errormsg="inscripcion::doit() Error: invalid Dorsal ID ";
			return null;	
		}
		$celo=http_request("Celo","i",0);
		$observaciones=http_request("Observaciones","s","");
		$equipo=http_request("Equipo","i",0);
		$pagado=http_request("Pagado","i",0);
		
		// si el ID de equipo es cero, buscamos el equipo por defecto para la prueba solicitada
		if ($equipo==0) {
			do_log("insertInscripcion() no equipo selected on prueba ".$this->prueba."; get default");
			$sql="SELECT ID FROM Equipos WHERE ( Prueba = ".$this->prueba." ) AND ( Nombre = '-- Sin asignar --' )";
			$rs=$this->conn->query($sql);		
			if (!$rs) {
				$this->errormsg="inscripcion::doit() cannot get default team: ".$this->conn->error;
				return null;	
			}
			$row=$rs->fetch_row();
			$equipo = $row[0];
			$rs->free();
		}
		// inscribimos en cada una de las jornadas solicitadas
		for ($numero=1;$numero<9;$numero++) {
			// si la jornada esta cerrada no se hace nada
			if ($this->jornadas[$numero]["Cerrada"]!=0) {
				do_log("inscripcion::doit() la jornada $numero esta cerrada");
				continue;
			}
			// obtenemos el JornadaID
			$jornada=$this->jornadas[$numero]["ID"];
			// vemos si pide inscribirse en esta jornada
			$solicita=http_request("J$numero","i",0);
			if ($solicita) {
				// vamos a ver si esta ya inscrito. 
				// Para ello lo que haremos sera intentar un update, y ver si se modifica alguna fila
				$sql="UPDATE Inscripciones
					SET Celo=$celo , Observaciones='$observaciones' , Equipo=$equipo , Pagado=$pagado
					WHERE ( (Dorsal=$dorsal) AND (Jornada=$jornada))";
				do_log("inscription::doit(update) trying to update Dorsal $dorsal from Jornada #$numero: ($jornada)");
				$rs=$this->conn->query($sql);
				if (!$rs) { // error en query
					$this->errormsg="inscripcion::doit(update) check already Error: ".$this->conn->error;
					return null;
				}
				if ($this->conn->affected_rows != 0) { // ya estaba inscrito
					do_log("inscripcion::doit(update) Dorsal $dorsal already registered in Jornada #$numero ($jornada)");
					$res=updateOrdenSalida($jornada,$dorsal,$celo,1 /*update*/);
					if ($res===null) {
						$this->errormsg="inscripcion::doit(update) updateOrdenSalida Error: ".$this->errormsg;
						return null;
					}
					continue; // go to next jornada
				}
					
				// si no esta inscrito, vamos a hacer la inscripcion
				$sql="INSERT INTO Inscripciones ( Jornada , Dorsal , Celo , Observaciones , Equipo , Pagado )
					VALUES ($jornada,$dorsal,$celo,'$observaciones',$equipo,$pagado)";
				do_log("inscription::doit(insert) insert into Jornada $numero: ID: $jornada Dorsal $dorsal");
				$rs=$this->conn->query($sql);
				if (!$rs) {
					$this->errormsg="inscripcion::doit(insert) Error: ".$this->conn->error;
					return null;
				}
				$res=updateOrdenSalida($jornada,$dorsal,$celo,0 /* insert */);
				if ($res===null) {
					$this->errormsg="inscripcion::doit(insert) updateOrdenSalida Error: ".$this->errormsg;
					return null;
				}
				
			} else {
				// no solicita inscripcion: borrar datos
				$sql="DELETE FROM Inscripciones where ( (Dorsal=$dorsal) AND (Jornada=$jornada))";
				do_log("inscription::doit(delete) Delete from Jornada $numero: ID: $jornada Dorsal $dorsal");
				$rs=$this->conn->query($sql);
				if (!$rs) {
					$this->errormsg="inscripcion::doit(delete) Error: ".$this->conn->error;
					return null;
				}
				$res=updateOrdenSalida($jornada,$dorsal,$celo,0 /* insert */);
				if ($res===null) {
					$this->errormsg="inscripcion::doit(delete) updateOrdenSalida Error: ".$this->errormsg;
					return null;
				}
			}
			
		}
		// all right return ok
		do_log("inscripcion::doit() exit ok");
		return ""; // return ok
	}
	
	/**
	 * Remove all inscriptions of Dorsal in non-closed jornadas from provided prueba 
	 * @return {string} "" on success; null on error
	 */
	function remove() {
		do_log("inscripcion::delete() enter");
		$dorsal=http_request("Dorsal","i",0);
		if ($dorsal==0) {
			$this->errormsg="inscripcion::remove() Error: invalid Dorsal ID ";
			return null;
		}
		for ($n=1;$n<9;$n++) {
			$jornada=$this->jornadas[$n]["ID"];
			if ($this->jornadas[$n]["Cerrada"]!=0) {
				do_log("inscripcion::remove() skip delete Dorsal $dorsal on closed Jornada $jornada");
				continue;
			}
			$sql="DELETE FROM Inscripciones where ( (Dorsal=$dorsal) AND (Jornada=$jornada))";
			$res=$this->conn->query($sql);
			if (!$res) {
				$this->errormsg="inscription::remove() execute query failed :".$this->conn->error;
				return null;
			}
			$res=updateOrdenSalida($jornada,$dorsal,0,2 /*remove*/);
			if ($res===null) {
				$this->errormsg="inscripcion::remove() updateOrdenSalida Error: ".$this->errormsg;
				return null;
			}
		} // for every jornada on provided prueba
		do_log("inscripcion::delete() exit OK");
		return "";
	}
	
	/**
	 * retrieve all inscriptions
	 */
	function select() {
		do_log("inscripcion::select() enter");
		
		do_log("inscripcion::select():: exit OK");
		return $result;
	}
	
} /* end of class "Clubes" */

?>