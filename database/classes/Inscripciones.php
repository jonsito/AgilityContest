<?php
require_once("DBObject.php");
require_once("Jornadas.php");
require_once("OrdenSalida.php"); // to insert/remove inscriptions from mangas

class Inscripciones extends DBObject {
	
	protected $prueba;
	protected $jornadas; // array of jornadas for this prueba
	
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $prueba Prueba ID
	 * @throws Exception if cannot contact database or invalid prueba/jornada ID
	 */
	function __construct($file,$prueba) {
		parent::__construct($file);
		if ( $prueba<=0 ) {
			$this->errormsg="$file::construct() invalid prueba:$prueba ID";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$prueba;
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
	 * Actualiza el orden de salida si es necesario
	 * TODO: move this function to OrdenSalida.php class
	 * @param {integer} $jornada
	 * @param {integer} $dorsal
	 * @param {integer} $celo
	 * @param {integer} $mode 0:insert 1:update 2:delete
	 * @return "" on success; null on error
	 */
	function updateOrdenSalida($jornada,$dorsal,$celo,$mode) {
		$this->myLogger->enter();
		// obtenemos datos del perro
		$str="SELECT * from PerroGuiaClub WHERE (Dorsal=$dorsal)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$perro=$rs->fetch_object();
		$rs->free();
	
		// buscamos la lista de mangas que tiene la jornada
		$str="SELECT ID, Grado FROM Mangas	WHERE ( Jornada = $jornada ) ORDER BY Descripcion ASC";
		$rs=$this->query($str);
		if(!$rs) return $this->error($this->conn->error); 
		// retrieve result into an array
		while($row = $rs->fetch_object()){
			$mangaid=$row->ID;
			$mangagrado=$row->Grado;
			
			// obtenemos un manejador de ordenes de salida
			$os=new OrdenSalida("inscriptionFunctions");
			// si la categoria no es compatible, intentamos eliminar el perro de la manga
			if (($mangagrado !== '-') && ($mangagrado !== $perro->Grado)) {
				$this->myLogger->debug("Grado del dorsal ".$dorsal." (".$perro->Grado.") no compatible con grado de manga (".$mangagrado.") " );

				$os->remove($jornada,$mangaid,$dorsal);
				continue;
			}
			// si la categoria es compatible compatible: obtenemos el orden de salida
			$orden=$os->getOrden($mangaid);
			// si el orden es nulo, quiere decir manga no iniciada -> no hace falta hacer nada
			if ($orden==="") continue;
			// si orden no nulo, vemos que hay que hacer con el perro
			switch($mode) {
				case 0: $os->insert($jornada,$mangaid,$dorsal); 
					break;
				case 1: // remove and insert to make sure changes are properly reflected
						$os->remove($jornada,$mangaid,$dorsal);
						$os->insert($jornada,$mangaid,$dorsal); 
					break;
				case 2: $os->remove($jornada,$mangaid,$dorsal);
					break;
			}
		}
		$rs->free();
		$this->myLogger->leave();
		return "";
	}
	
		
	/**
	 * insert/update/delete a new inscripcion into database
	 * @return empty string if ok; else null
	 */
	function doit() {
		$this->myLogger->enter();

		// variables comunes a todas las jornadas
		$dorsal=http_request("Dorsal","i",0);
		if ($dorsal==0) return $this->error("Invalid Dorsal ID"); 
		$celo=http_request("Celo","i",0);
		$observaciones=http_request("Observaciones","s","");
		$equipo=http_request("Equipo","i",0);
		$pagado=http_request("Pagado","i",0);
		
		// si el ID de equipo es cero, buscamos el equipo por defecto para la prueba solicitada
		if ($equipo==0) {
			$this->myLogger->info("No equipo selected on prueba ".$this->prueba."; get default");
			$sql="SELECT ID FROM Equipos WHERE ( Prueba = ".$this->prueba." ) AND ( Nombre = '-- Sin asignar --' )";
			$rs=$this->query($sql);		
			if (!$rs) return $this->error($this->conn->error);
			$row=$rs->fetch_row();
			$equipo = $row[0];
			$rs->free();
		}
		// inscribimos en cada una de las jornadas solicitadas
		for ($numero=1;$numero<9;$numero++) {
			// si la jornada esta cerrada no se hace nada
			if ($this->jornadas[$numero]["Cerrada"]!=0) {
				$this->myLogger->info("La jornada $numero esta cerrada");
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
				$rs=$this->query($sql);
				if (!$rs) return $this->error($this->conn->error); 
				if ($this->conn->affected_rows != 0) { // ya estaba inscrito
					$this->myLogger->info("Dorsal $dorsal already registered in Jornada #$numero ($jornada)");
					$res=$this->updateOrdenSalida($jornada,$dorsal,$celo,1 /*update*/);
					if ($res===null) return $this->error($this->errormsg);
					continue; // go to next jornada
				}
					
				// si no esta inscrito, vamos a hacer la inscripcion
				$sql="INSERT INTO Inscripciones ( Jornada , Dorsal , Celo , Observaciones , Equipo , Pagado )
					VALUES ($jornada,$dorsal,$celo,'$observaciones',$equipo,$pagado)";
				$this->myLogger->debug("Insert into Jornada $numero: ID: $jornada Dorsal $dorsal");
				$rs=$this->query($sql);
				if (!$rs) return $this->error($this->conn->error);
				$res=$this->updateOrdenSalida($jornada,$dorsal,$celo,0 /* insert */);
				if ($res===null) return $this->error($this->errormsg); 
				
			} else {
				// no solicita inscripcion: borrar datos
				$sql="DELETE FROM Inscripciones where ( (Dorsal=$dorsal) AND (Jornada=$jornada))";
				$this->myLogger->debug("Delete from Jornada $numero: ID: $jornada Dorsal $dorsal");
				$rs=$this->query($sql);
				if (!$rs) return $this->error($this->conn->error);
				$res=$this->updateOrdenSalida($jornada,$dorsal,$celo,2 /* remove */);
				if ($res===null) return $this->error($this->errormsg);
			}
			
		}
		// all right return ok
		$this->myLogger->leave();
		return ""; // return ok
	}
	
	/**
	 * Remove all inscriptions of Dorsal in non-closed jornadas from provided prueba 
	 * @return {string} "" on success; null on error
	 */
	function remove() {
		$this->myLogger->enter();
		$dorsal=http_request("Dorsal","i",0);
		if ($dorsal==0) return $this->error("Invalid Dorsal ID"); 
		for ($n=1;$n<9;$n++) {
			$jornada=$this->jornadas[$n]["ID"];
			if ($this->jornadas[$n]["Cerrada"]!=0) {
				$this->myLogger->info("Skip delete Dorsal $dorsal on closed Jornada $jornada");
				continue;
			}
			$sql="DELETE FROM Inscripciones where ( (Dorsal=$dorsal) AND (Jornada=$jornada))";
			$res=$this->query($sql);
			if (!$res) return $this->error($this->conn->error); 
			$res=$this->updateOrdenSalida($jornada,$dorsal,0,2 /*remove*/);
			if ($res===null) $this->conn->error($this->errormsg);
		} // for every jornada on provided prueba
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * retrieve all inscriptions of stored prueba
	 */
	function select() {
		$this->myLogger->enter();
		
		// evaluate offset and row count for query
		$id = $this->prueba;
		$page = http_request("page","i",1); 
		$rows = http_request("rows","i",20);
		$sort = http_request("sort","s","Club");
		$order = http_request("order","s","ASC"); 
		$search =  http_request("where","s","");
		$extra = ')';
		if ($search!=='') $extra=" AND ( (PerroGuiaClub.Nombre LIKE '%$search%') OR ( Club LIKE '%$search%') OR ( Guia LIKE '%$search%' ) ) )";
		$offset = ($page-1)*$rows;
		
		// FASE 1: obtener lista de perros inscritos con sus datos
		$str="SELECT Numero , Inscripciones.Dorsal AS Dorsal , PerroGuiaClub.Nombre AS Nombre,
			Categoria , Grado , Celo , Guia , Club , Equipo , Observaciones , Pagado
			FROM Inscripciones,PerroGuiaClub,Jornadas
			WHERE ( ( Inscripciones.Dorsal = PerroGuiaClub.Dorsal)
			AND ( Inscripciones.Jornada = Jornadas.ID )
			AND ( Prueba= $id )
			$extra ORDER BY $sort $order"; // a single ')' or name search criterion
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		
		// Fase 2: la tabla de resultados a devolver
		$result = array(); // result { total(numberofrows), data(arrayofrows)
		$count = 0;
		$dorsales = array();
		while($row = $rs->fetch_array()){
			if (!isset($dorsales[$row['Dorsal']])) {
				$count++;
				$dorsales[$row['Dorsal']]= array(
					'Dorsal' => $row['Dorsal'],
					'Nombre' => $row['Nombre'],
					'Categoria' => $row['Categoria'],
					'Grado' => $row['Grado'],
					'Celo' => $row['Celo'],
					'Guia' => $row['Guia'],
					'Club' => $row['Club'],
					'Equipo' => $row['Equipo'],
					'Observaciones' => $row['Observaciones'],
					'Pagado' => $row['Pagado'],
					'J1' => 0, 'J2' => 0, 'J3' => 0, 'J4' => 0, 'J5' => 0, 'J6' => 0, 'J7' => 0,'J8' => 0
				);
			} // create row if not exists
			// store wich jornada is subscribed into array
			$jornada=$row['Numero'];
			$dorsales[$row['Dorsal']]["J$jornada"]=1;
		}
		$rs->free();
		$items=array();
		$index=0;
		foreach($dorsales as $key => $item) {
			if ($index<$offset) { // not yet on requested rows
				$index++;
				continue;
			}
			if (($index-$offset)>=$rows) break; // we already have enought rows
			array_push($items,$item);
			$index++;
		}
		$result['total']=$count; // number of rows retrieved
		$result['rows']=$items;
		// and return json encoded $result variable
		$this->myLogger->leave();
		return $result;
	}
	
} /* end of class "Clubes" */

?>