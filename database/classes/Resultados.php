<?php
require_once("DBObject.php");
require_once("OrdenSalida.php");
require_once("Mangas.php");

class Resultados extends DBObject {
	protected $manga;
	protected $jornada;
	protected $cerrada;

	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {string} $manga Manga ID
	 * @throws Exception when
	 * - cannot contact database 
	 * - invalid manga ID
	 * - manga is closed
	 */
	function __construct($file,$manga) {
		parent::__construct($file);
		if ($manga<=0) {
			$this->errormsg="Resultados::Construct invalid Manga ID";
			throw new Exception($this->errormsg);
		}
		$this->manga=$manga;
		// obtenemos el id de jornada y vemos si la manga esta cerrada
		$str="SELECT Jornada,Cerrada FROM Mangas WHERE ID=$manga";
		$rs=$this->query($str);
		if (!$rs) {
			$this->errormsg("Cannot retrieve data for manga $manga");
			throw new Exception($this->errormsg);
		}
		$row=$rs->fetch_object();
		$rs->free();
		if (!$row) {
			$this->errormsg("Manga $manga does not exists in database");
			throw new Exception($this->errormsg);
		}
		$this->jornada=$row->Jornada;
		$this->cerrada=$row->Cerrada;
	}
		
	
	/**
	 * Inserta perro en la lista de resultados de la manga
	 * los datos del perro se toman de la tabla perroguiaclub
	 * @param {integer} $dorsal
	 * @return "" on success; null on error
	 */
	function insert($dorsal) {
		$this->myLogger->enter();
		if ($dorsal<=0) return $this->error("No dorsal specified");
		if ($this->cerrada!=0) return $this->error("Manga ".$this->manga." is closed");
		
		// phase 1: retrieve dog data
		$str="SELECT * FROM PerroGuiaClub WHERE ( Dorsal = $dorsal )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$perro =$rs->fetch_object(); // should be only one item
		$rs->free();
		if (!$perro) return $this->error("No information on Dorsal: $dorsal");
		
		// phase 2: insert into resultados. On duplicate ($manga,$dorsal) key an error will occur
		$str="INSERT INTO Resultados ( Manga , Nombre , Dorsal , Licencia , Categoria , Grado , Guia , Club ) VALUES ("
				.$this->manga.		",'"	.$perro->Nombre. 	"'," 	.$perro->Dorsal.	",'"	.$perro->Licencia.	"','"
				.$perro->Categoria. "','" 	.$perro->Grado.		"','"	.$perro->Guia.		"','"	.$perro->Club .	"')";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Borra el dorsal de la lista de resultados de la manga
	 * @param {integer} $dorsal
	 * @return "" on success; null on error
	 */
	function delete($dorsal) {
		$this->myLogger->enter();
		if ($dorsal<=0) return $this->error("No Dorsal specified");
		if ($this->cerrada!=0) return $this->error("Manga ".$this->manga." is closed");
		$str="DELETE * FROM Resultados WHERE ( Dorsal = $dorsal ) AND ( Manga=".$this->manga.")";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * selecciona los datos del dorsal indicado desde la lista de resultados de la manga
	 * @param {integer} $dorsal
	 * @return {array} [key=>value,...] on success; null on error
	 */
	function select($dorsal) {
		$this->myLogger->enter();
		if ($dorsal<=0) return $this->error("No Dorsal specified");
		$str="SELECT * FROM Resultados WHERE ( Dorsal = $dorsal ) AND ( Manga=".$this->manga.")";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_array();
		$rs->free();
		if(!$row) return $this->error("No Results for Dorsal:$dorsal on Manga:".$this->manga);
		$this->myLogger->leave();
		return $row;
	}
	
	/**
	 * Actualiza los resultados de la manga para el dorsal indicado
	 * @param {integer} $dorsal
	 * @return datos actualizados desde la DB; null on error
	 */
	function update($dorsal) {
		$this->myLogger->enter();
		if ($dorsal<=0) return $this->error("No Dorsal specified");
		if ($this->cerrada!=0) return $this->error("Manga ".$this->manga." is closed");
		// buscamos la lista de parametros a actualizar
		$entrada=http_request("Entrada","s",date("Y-m-d H:i:s"));
		$comienzo=http_request("Comienzo","s",date("Y-m-d H:i:s"));
		$faltas=http_request("Faltas","i",0);
		$rehuses=http_request("Rehuses","i",0);
		$tocados=http_request("Tocados","i",0);
		$nopresentado=http_request("NoPresentado","i",0);
		$eliminado=http_request("Eliminado","i",0);
		$tiempo=http_request("Tiempo","d",0.0);
		$observaciones=http_request("Observaciones","s","");
		// comprobamos la coherencia de los datos recibidos y ajustamos
		// NOTA: el orden de estas comprobaciones es MUY importante
		if ($rehuses>=3) { $tiempo=0; $eliminado=1; $nopresentado=0;}
		if ($tiempo>0) {$nopresentado=0;}
		if ($eliminado==1) { $tiempo=0; $nopresentado=0; }
		if ($nopresentado==1) { $tiempo=0; $eliminado=0; $faltas=0; $rehuses=0; $tocados=0; }
		if ( ($tiempo==0) && ($eliminado==0)) { $nopresentado=1; $faltas=0; $rehuses=0; $tocados=0; }
		if ( ($tiempo==0) && ($eliminado==1)) { $nopresentado=0; }
		// efectuamos el update
		$sql="UPDATE Resultados 
			SET Entrada='$entrada' , Comienzo='$comienzo' , 
				Faltas=$faltas , Rehuses=$rehuses , Tocados=$tocados ,
				NoPresentado=$nopresentado , Eliminado=$eliminado , 
				Tiempo=$tiempo , Observaciones='$observaciones' 
			WHERE (Dorsal=$dorsal) AND (Manga=".$this->manga.")";
		$rs=$this->query($sql);
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return $this->select($dorsal);
	}
	
	/**
	 * Presenta una tabla ordenada segun el orden de salida asociado a la manga
	 * @return null on error else array en formato easyui datagrid
	 */
	function enumerate() {
		$this->myLogger->enter();
		
		// fase 1: obtenemos el orden de salida
		$os=new OrdenSalida("Competicion");
		$orden=$os->getOrden($this->manga);
		if ($orden==="") {
			// si no hay orden de salida predefinido, genera uno al azar
			$this->myLogger->notice("There is no OrdenSalida predefined for manga:".$this->manga);
			$orden= $os->random($this->jornada,$this->manga);
		}
		$this->myLogger->debug("El orden de salida es: \n$orden");
		$lista = explode ( ",", $orden );
		
		// fase 2: obtenemos todos los resultados de esta manga 
		$str="SELECT * FROM Resultados WHERE (Manga=".$this->manga.")";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// y los guardamos en un array indexado por el dorsal
		$data=array();
		while($row=$rs->fetch_array()) $data[$row["Dorsal"]]=$row;
		$rs->free();
		
		// fase 3 componemos el resultado siguiendo el orden de salida
		$items=array();
		$count=0;
		foreach ($lista as $dorsal) {
			// ignore separators
			if (strpos ( $dorsal, "BEGIN" ) !== false) continue;
			if (strpos ( $dorsal, "END" ) !== false) continue;
			if (strpos ( $dorsal, "TAG_" ) !== false)	continue;
			if (!isset($data[$dorsal])){
				$this->myLogger->warn("No Results for dorsal:$dorsal. Creating default one");
				$this->insert($dorsal);
				$data[$dorsal]=$this->select($dorsal);
			}
			array_push($items,$data[$dorsal]);
			$count++;
		}
		$result = array();
		$result["total"] = $count;
		$result["rows"] = $items;
		$this->myLogger->leave();
		return $result;
	}
}
?>