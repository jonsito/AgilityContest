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
		$rs=$this->query($sql);
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
				// THIS SHOULD NEVER OCCURS. IT'S ONLY FOR TESTING
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
	
	/**
	 * Presenta los resultados de la manga asociados las categorias indicadas
	 * Se calculan la puntuacion a partir de la informacion de TRS/TRM de la manga
	 * TODO: manejar inconsistencias entre la solicitud y datos de TRS/TRM de la manga
	 * @param {string} $cat lista de categorias. eg ("L", "MS" "LMS" "-" )
	 * @return null on error; else array en formato easyui datagrid
	 */
	function resultados($cat) {
		$this->myLogger->enter();
		
		// FASE 1: obtenemos todos los resultados de esta manga pre-ordenados sin tener en cuenta el TRS
		$str="SELECT * , ( 5*Faltas + 5*Rehuses + 5*Tocados + 100*Eliminado + 200*NoPresentado ) AS Penalizacion
			FROM Resultados WHERE ( Manga =".$this->manga." )
			ORDER BY Categoria ASC , Penalizacion ASC , Tiempo ASC ";	
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// y los guardamos en un array indexado por el dorsal
		// guardando los tres mejores tiempos de cada categoria
		$data=array();	// almacen de datos leidos desde la base de datos
		$count=0;		// orden de clasificacion en funcion de la categoria
		
		// preparamos el almacen de los tres mejores tiempos de cada categoria
		$tiempos=array();	
		$tiempos["-"]=array();$tiempos["L"]=array();$tiempos["M"]=array();$tiempos["S"]=array();$tiempos["T"]=array();
		foreach ( array("-","L","M","S","T") as $c ) { $tiempos[$c][0]=0;$tiempos[$c][1]=0;$tiempos[$c][2]=0; }

		// analizamos el resultado de la bbdd y guardamos los tres mejores tiempos de cada categoria en $tiempos
		$lastCategoria="*";
		while($row = $rs->fetch_array()) {
			if ( $row['Categoria'] !== $lastCategoria ) { 
				$count=0;
				$lastCategoria=$row['Categoria'];
			}
			if ($count<3) $tiempos[$lastCategoria][$count]=$row['Tiempo'];
			$row["Orden"]=$count+1;
			array_push($data,$row);
			$count++;
		}
		$rs->free();
		
		// FASE 2: obtenemos el TRS y evaluamos puntuacion y calificacion
		$t1=$data[0]['Tiempo'];
		$t2=$data[1]['Tiempo'];
		$t3=$data[2]['Tiempo']; // tres mejores tiempos
		$mng= new Manga("Resultados.php",$this->jornada);
		$datos_trs= $mng->datos_TRS($this->manga,$tiempos);
		
		// FASE 3: revisamos el array evaluando penalizacion y calificacion 
		// y lo reindexamos en un nuevo array  generando un indice en funcion del orden
		$data2=array();
		foreach($data as $index => $row) {
			// reevaluamos la penalizacion en funcion del TRS
			$trs=$datos_trs[$row['Categoria']]['TRS'];
			// si el tiempo es nulo, asumimos Elim o NC
			if ($row['Tiempo']!=0 && $row['Tiempo']>$trs) {
				$row['Penalizacion'] = $row['Penalizacion']+ $row->Tiempo - $trs;
			}
			$puntos=$row['Penalizacion'];
			if ($puntos>=200) $row['Calificacion']="No Presentado";
			else if ($puntos>=100) $row['Calificacion']="Eliminado";
			else if ($puntos>=26) $row['Calificacion']="No Clasificado";
			else if ($puntos>=16) $row['Calificacion']="Bien";
			else if ($puntos>=6) $row['Calificacion']="Muy Bien";
			else $row['Calificacion']="Excelente";
			if ($tiempo==0) $row['Velocidad']="-";
			else $row['Velocidad']=$datos_trs[$row['Categoria']]['Dist']/$tiempo;
			
			// para garantizar que nuevo el array esta ordenado, vamos a crear un indice
			// basado en 10000*puntos+tiempo. 
			// Para separar categorias, sumaremos un offset de 1000000 a cada una
			switch ($row['Categoria']) {
				case "-": $data2[			10000*$puntos+100*$row['Tiempo']]=$row; break;
				case "L": $data2[1000000 +	10000*$puntos+100*$row['Tiempo']]=$row; break;
				case "M": $data2[2000000 +	10000*$puntos+100*$row['Tiempo']]=$row; break;
				case "S": $data2[3000000 +	10000*$puntos+100*$row['Tiempo']]=$row; break;
				case "T": $data2[4000000 +	10000*$puntos+100*$row['Tiempo']]=$row; break;
			}
			
		}
		// FASE 4 Ordenamos el segundo array
		ksort($data2);
		// preparamos el resultado a devolver
		
		// FASE 5: retornamos datos en formato datagrid
		$result=array();
		$result['total']=count($data2);
		$result['rows']=$data2;
		// $result['datos_trs']=$datos_trs;
		$this->myLogger->leave();
		return $result;
	}
}
?>