<?php
require_once("DBObject.php");
require_once("OrdenSalida.php");

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
		$str="INSERT INTO Resultados ( Manga , Dorsal , Licencia , Categoria , Grado , Guia , Club ) VALUES ("
				.$this->manga. 		"," 	.$perro->Dorsal.	",'"	.$perro->Licencia.	"','"
				.$perro->Categoria. "','" 	.$perro->Grado.		"','"	.$perro->Guia.		"','"	.$perro->club .	"')";
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
		$str="DELETE * FROM Resultados WHERE ( Dorsal = $dorsal )";
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
		$str="SELECT * FROM Resultados WHERE ( Dorsal = $dorsal )";
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
	 * @return "" on success; null on error
	 */
	function update($dorsal) {
		$this->myLogger->enter();
		if ($dorsal<=0) return $this->error("No Dorsal specified");
		if ($this->cerrada!=0) return $this->error("Manga ".$this->manga." is closed");
		// buscamos la lista de parametros a actualizar
		$str="";
		if (isset($_REQUEST["Entrada"]))		$str .= ", Entrada='" 	. strval($_REQUEST["Entrada"]) . "'";
		if (isset($_REQUEST["Comienzo"]))		$str .= ", Comienzo='"	. strval($_REQUEST["Comienzo"]) . "'";
		if (isset($_REQUEST["Faltas"]))			$str .= ", Faltas=" 	. intval($_REQUEST["Faltas"]) . "";
		if (isset($_REQUEST["Rehuses"]))		$str .= ", Rehuses="	. intval($_REQUEST["Rehuses"]) . "";
		if (isset($_REQUEST["Tocados"]))		$str .= ", Tocados="	. intval($_REQUEST["Tocados"]) . "";
		if (isset($_REQUEST["Eliminado"]))		$str .= ", Eliminado="	. intval($_REQUEST["Eliminado"]) . "";
		if (isset($_REQUEST["NoPresentado"])) 	$str .= ", NoPresentado=".intval($_REQUEST["NoPresentado"]) . "";
		if (isset($_REQUEST["Tiempo"]))			$str .= ", Tiempo="		. doubleval($_REQUEST["Tiempo"]) . "";
		if (isset($_REQUEST["Observaciones"]))	$str .= ", Observaciones='" . strval($_REQUEST["Observaciones"]) . "'";
		if ($str==="") return $this->error("No resultados to update for Dorsal:$dorsal on Manga:".$this->manga);
		else $str= substr($str,1); // skip initial ','
		// efectuamos el update
		$sql="UPDATE Resultados SET $str WHERE (Dorsal=$dorsal) AND (Manga=".$this->manga.")";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Presenta una tabla ordenada segun el orden de salida asociado a la manga
	 * @return null on error else array en formato easyui datagrid
	 */
	function enumerate() {
		$this->myLogger->enter();
		
		// fase 1: obtenemos el orden de salida
		$os=new OrdenSalida();
		$orden=$os->getOrden($this->manga);
		if ($orden==="") {
			// si no hay orden de salida predefinido, genera uno al azar
			$this->myLogger->notice("There is no OrdenSalida predefined for manga:".$this->manga);
			$orden= $os->random($this->jornada,$this->manga);
		}
		$lista = explode ( ",", $ordensalida );
		
		// fase 2: obtenemos todos los resultados de esta manga y los guardamos
		$str="SELECT * FROM Resultados WHERE (Manga=".$this->manga.")";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$data=array();
		while($row=$rs->fetch_array()) $data[$row["Dorsal"]]=$row;
		$rs->free();
		
		// fase 3 componemos el resultado siguiendo el orden de salida
		$items=array();
		$count=0;
		foreach ($orden as $dorsal) {
			// ignore separators
			if (strpos ( $dorsal, "BEGIN" ) !== false) continue;
			if (strpos ( $dorsal, "END" ) !== false) continue;
			if (strpos ( $dorsal, "TAG_" ) !== false)	continue;
			if (!isset($data[$dorsal])){
				// THIS SHOULD NEVER OCCURS. IT'S ONLY FOR TESTING
				$this->myLogger->warning("No Results for dorsal:$dorsal. Creating default one");
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
	
	/**
	 * Presenta los resultados de la manga asociados las categorias indicadas
	 * Se calculan la puntuacion a partir de la informacion de TRS/TRM de la manga
	 * TODO: manejar inconsistencias entre la solicitud y datos de TRS/TRM de la manga
	 * @param {string} $cat lista de categorias. eg ("L", "MS" "LMS" "-" )
	 * @return null on error; else array en formato easyui datagrid
	 */
	function resultados($cat) {
		$this->myLogger->enter();
		$this->myLogger->leave();
	}
}
?>