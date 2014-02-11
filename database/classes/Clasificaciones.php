<?php
require_once("DBObject.php");
require_once("OrdenSalida.php");
require_once("Mangas.php");

class Clasificaciones extends DBObject {
	protected $manga;

	/**
	 * Funcion auxiliar que obtiene la jornada asociada a una manga
	 * @param {integer} $manga Manga ID
	 * @return Jornada ID on success; null on error
	 */
	function getJornada($manga) {
		$this->myLogger->leave();
		// obtenemos el id de jornada y vemos si la manga esta cerrada
		$str="SELECT Jornada FROM Mangas WHERE ID=$manga";
		$rs=$this->query($str);
		if (!$rs) return $this->errormsg("Cannot retrieve data for manga $manga");
		$row=$rs->fetch_object();
		$rs->free();
		if (!$row) return $this->errormsg("Manga $manga does not exists in database");
		$this->myLogger->leave();
		return $row->Jornada;
	}

	/**
	 * Presenta los Clasificaciones parciales de la manga indicada
	 * @param {integer} $cat lista de categorias. eg ("L", "MS" "LMS" "-" )
	 * @return null on error; else array en formato easyui datagrid
	 */
	function parcial($manga) {
		$this->myLogger->enter();
		// FASE 0: creamos una tabla temporal en la que ir almacenando los diversos resultados parciales
		$table_name="".$manga."_".random_password(8);
		$str="CREATE TEMPORARY TABLE $table_name (
			`Dorsal` int(4) NOT NULL,
			`Categoria` varchar(1) NOT NULL DEFAULT '-',
			`PRecorrido` double NOT NULL DEFAULT '0',
			`PTiempo` double NOT NULL DEFAULT '0',
			`Tiempo` double NOT NULL DEFAULT '0',
		)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		
		// FASE 1: obtenemos todos los Clasificaciones de esta manga pre-ordenados sin tener en cuenta el TRS
		$str="SELECT * , ( 5*Faltas + 5*Rehuses + 5*Tocados + 100*Eliminado + 200*NoPresentado ) AS Penalizacion
			FROM Clasificaciones WHERE ( Manga =".$this->manga." )
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
		$mng= new Mangas("Clasificaciones.php",$this->jornada);
		$datos_trs= $mng->datosTRS($this->manga,$tiempos);
		
		// FASE 3: revisamos el array evaluando penalizacion y calificacion 
		// y lo reindexamos en un nuevo array  generando un indice en funcion del orden
		$data2=array();
		foreach($data as $index => $row) {
			// reevaluamos la penalizacion en funcion del TRS
			$trs=$datos_trs[$row['Categoria']]['TRS'];
			// si el tiempo es nulo, asumimos Elim o NC
			if ($row['Tiempo']!=0 && $row['Tiempo']>$trs) {
				$row['Penalizacion'] = $row['Penalizacion']+ $row['Tiempo'] - $trs;
			}
			// Comprobamos si nos hemos pasado del TRM
			if ($row['Tiempo']>$datos_trs[$row['Categoria']]['TRM']) {
				$row['Penalizacion']=100;
				// $row['Comentarios'] .= " - TRM excedido";
			}
			// evaluamos calificacion
			$puntos=$row['Penalizacion'];
			if ($puntos>=200) $row['Calificacion']="No Presentado";
			else if ($puntos>=100) $row['Calificacion']="Eliminado";
			else if ($puntos>=26) $row['Calificacion']="No Clasificado";
			else if ($puntos>=16) $row['Calificacion']="Bien";
			else if ($puntos>=6) $row['Calificacion']="Muy Bien";
			else $row['Calificacion']="Excelente";
			// evaluamos velocidad
			if ($row['Tiempo']==0) $row['Velocidad']="-";
			else $row['Velocidad']=$datos_trs[$row['Categoria']]['Dist']/$row['Tiempo'];
			
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
		// FASE 4 Ordenamos el segundo array y retornamos datos en formato datagrid
		ksort($data2);
		$data3=array();
		foreach($data2 as $row) { array_push($data3,$row); }
		$result=array();
		$result['total']=count($data3);
		$result['rows']=$data3;
		// $result['datos_trs']=$datos_trs;
		$this->myLogger->leave();
		return $result;
	}
}
?>