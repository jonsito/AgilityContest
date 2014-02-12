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
	 * Calcula los Clasificaciones parciales de la manga indicada
	 * @param {integer} $manga Manga ID
	 * @return null on error; "" on success
	 */
	function clasificacion($tablename,$manga) {
		$this->myLogger->enter();
		// FASE 0: creamos una tabla temporal en la que ir almacenando los diversos resultados parciales
		$str="CREATE TEMPORARY TABLE $tablename (
			`Dorsal` int(4) NOT NULL,
			`Categoria` varchar(1) NOT NULL DEFAULT '-',
			`PRecorrido` double NOT NULL DEFAULT '0',
			`PTiempo` double NOT NULL DEFAULT '0',
			`Tiempo` double NOT NULL DEFAULT '0',
			`Velocidad` double NOT NULL DEFAULT '0',
			`Puntos` double NOT NULL DEFAULT '0',
			`Calificacion` varchar(16) DEFAULT NULL,
			PRIMARY KEY ( `Dorsal` )
		)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		
		// FASE 1: hacemos un calculo de las penalizaciones por recorrido
		$str="INSERT INTO $tablename (Dorsal,Categoria,Tiempo,PRecorrido) 
			SELECT Dorsal,Categoria,Tiempo, ( 5*Faltas + 5*Rehuses + 5*Tocados + 100*Eliminado + 200*NoPresentado ) AS PRecorrido
			FROM Resultados WHERE ( Manga =$manga )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		
		// Fase 2: extraemos los tres mejores tiempos de cada categoria
		$str="SELECT * FROM $tablename ORDER BY Categoria ASC , PRecorrido ASC , Tiempo ASC ";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		
		$count=0;		// orden de clasificacion en funcion de la categoria
		// preparamos el almacen de los tres mejores tiempos de cada categoria
		$tiempos=array();	
		$tiempos["-"]=array();
		$tiempos["L"]=array();
		$tiempos["M"]=array();
		$tiempos["S"]=array();
		$tiempos["T"]=array();
		foreach ( array("-","L","M","S","T") as $c ) { $tiempos[$c][0]=0;$tiempos[$c][1]=0;$tiempos[$c][2]=0; }

		// analizamos el resultado de la bbdd y guardamos los tres mejores tiempos de cada categoria en $tiempos
		$lastCategoria="*";
		$data=array();
		while($row = $rs->fetch_object()) {
			if ( $row->Categoria !== $lastCategoria ) { 
				$count=0;
				$lastCategoria=$row->Categoria;
			}
			if ($count<3) $tiempos[$lastCategoria][$count++]=$row->Tiempo;
			array_push($data,$row);
		}
		$rs->free();
		$this->myLogger->info("Fase 2. numero de filas: ".count($data));
		// FASE 3: obtenemos el TRS y evaluamos puntuacion y calificacion
		$jornada= $this->getJornada($manga);
		if (!$jornada) return null; // error log already done
		$mng= new Mangas("Clasificaciones.php",$jornada);
		$datos_trs= $mng->datosTRS($manga,$tiempos);
		
		// FASE 4: revisamos el array evaluando penalizacion y calificacion 
		// y lo reinsertamos en la tabla temporal		
		
		// componemos un prepared statement
		$sql ="UPDATE $tablename SET PTiempo=? , Puntos=? ,Velocidad=?, Calificacion=? WHERE ( Dorsal=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('dddsi',$pt,$p,$v,$c,$d);
		if (!$res) return $this->error($this->conn->error); 
		foreach($data as $row) {
			// reevaluamos la penalizacion y obtenemos puntos en funcion del TRS
			$trs=$datos_trs[$row->Categoria]['TRS'];
			$trm=$datos_trs[$row->Categoria]['TRM'];
			// si tiempo > TRM  Puntos==100; eliminado
			if ($row->Tiempo<$trs) { $row->PTiempo=0; $row->Puntos=$row->PRecorrido; }
			if ($row->Tiempo>=$trs) { $row->PTiempo=$row->Tiempo-$trs; $row->Puntos=$row->PRecorrido+$row->PTiempo; }
			if ($row->Tiempo>$trm) { $row->PTiempo=100; $row->Puntos=100; } // eliminado por superar el TRM
			// evaluamos velocidad y ajustamos a un decimal
			if ($row->Tiempo==0) $row->Velocidad=0;
			else $row->Velocidad = $datos_trs[$row->Categoria]['Dist'] / $row->Tiempo;
			$row->Velocidad=number_format($row->Velocidad,1);
			// evaluamos calificacion
			if ($row->Puntos==0)	$row->Calificacion = "Excelente (p)";
			if ($row->Puntos>0)		$row->Calificacion = "Excelente";
			if ($row->Puntos>=6)	$row->Calificacion = "Muy Bien";
			if ($row->Puntos>=16)	$row->Calificacion = "Bien";
			if ($row->Puntos>=26)	$row->Calificacion = "No Clasificado";
			if (($row->Puntos>=100) && ($row->Puntos<200)){
				$row->Puntos=100;
				$row->Calificacion = "Eliminado";
			}
			if ($row->Puntos>=200)	{
				$row->puntos=200;
				$row->Calificacion = "No Presentado";
			}
			
			// y ejecutamos el update en la tabla temporal
			$pt=$row->PTiempo;
			$p=$row->Puntos;
			$v=$row->Velocidad;
			$c=$row->Calificacion;
			$d=$row->Dorsal;	
			$res=$stmt->execute();
			if (!$res) return $this->error($this->conn->error); 
		}
		$stmt->close(); // cerramos el prepared statement
		$this->myLogger->leave();
		return ""; // retornamos OK
	}
	
	/**
	 * Presenta los Clasificaciones parciales de la manga indicada
	 * @param {integer} $manga Manga ID
	 * @return null on error; on success result in easyui datagrid compatible format
	 */
	function parcial($manga) {
		$this->myLogger->enter();
		// Fase 1: generamos la clasificacion
		$tablename="Manga_".$manga."_".random_password(8);
		$res=$this->clasificacion($tablename,$manga);
		// FASE 2: hacemos un query en base a un join de la tabla de resultados 
		// y la de calificaciones ordenado por categoria/puntos/tiempo
		$str= "SELECT Manga, Resultados.Dorsal AS Dorsal, Nombre, Licencia, Resultados.Categoria AS Categoria, Guia, Club,
				Faltas, Rehuses, Tocados, Resultados.Tiempo AS Tiempo, Velocidad, Puntos, Calificacion
				FROM Resultados,$tablename
				WHERE ( Resultados.Dorsal = $tablename.Dorsal ) AND (Manga=$manga)
				ORDER BY Categoria ASC, Puntos ASC, Tiempo ASC";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// y devolvemos el resultado
		$lastCategoria="*";
		$puesto=1;
		$rows=array();
		while ($item=$rs->fetch_array()) {
			if ($lastCategoria!==$item['Categoria']) {
				$lastCategoria=$item['Categoria'];
				$puesto=1;
			}
			// TODO: puesto debe ser el mismo si mismos puntos y tiempo
			$item['Puesto']=$puesto++;
			array_push($rows,$item);
		}
		$rs->free();
		$result=array();
		$result['total']=count($rows);
		$result['rows']=$rows;
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Calcula el orden de una manga en funcion del resultado
	 * @param unknown $manga
	 * @return multitype:multitype: NULL
	 */
	function reverseOrden($manga) {
		$this->myLogger->enter();
		// Fase 1: generamos la clasificacion
		$tablename="Manga_".$manga."_".random_password(8);
		$res=$this->clasificacion($tablename,$manga);
		// FASE 2: hacemos un query en base a un join de la tabla de resultados
		// y la de calificaciones ordenado por categoria/celo/puntos/tiempo
		$str= "SELECT Resultados.Dorsal AS Dorsal, Resultados.Categoria AS Categoria, Celo, Resultados.Tiempo AS Tiempo, Puntos
		FROM Resultados,$tablename
		WHERE ( Resultados.Dorsal = $tablename.Dorsal ) AND (Manga=$manga);
		ORDER BY Categoria ASC, Celo ASC, Puntos DESC, Tiempo DESC";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// y devolvemos el resultado
		$rows=array();
		while ($item=$rs->fetch_array()) array_push($rows,$item);
		$rs->free();
		$result=array();
		$result['total']=count($rows);
		$result['rows']=$rows;
		$this->myLogger->leave();
		return $result;
	}
}
?>