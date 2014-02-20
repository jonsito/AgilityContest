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
	function clasificacion($tablename,$manga1,$manga2=0) {
		$cfinal=($manga2==0)?false:true;
		$this->myLogger->enter();
		// FASE 0: creamos una tabla temporal en la que ir almacenando los diversos resultados parciales
		$str="CREATE TEMPORARY TABLE $tablename (
			`Dorsal` int(4) NOT NULL,
			`Nombre` varchar(255) NOT NULL,
			`Licencia` varchar(255) NOT NULL DEFAULT '--------',
			`Categoria` varchar(1) NOT NULL DEFAULT '-',
			`Guia` varchar(255) NOT NULL DEFAULT '-- Sin asignar --',
			`Club` varchar(255) NOT NULL DEFAULT '-- Sin asignar --',
			
			`Faltas` int(4) NOT NULL DEFAULT '0',
			`Tocados` int(4) NOT NULL DEFAULT '0',
			`Rehuses` int(4) NOT NULL DEFAULT '0',
			`PRecorrido` double NOT NULL DEFAULT '0',
			`PTiempo` double NOT NULL DEFAULT '0',
			`Tiempo` double NOT NULL DEFAULT '0',
			`Velocidad` double NOT NULL DEFAULT '0',
			`Penalizacion` double NOT NULL DEFAULT '0',
			`Calificacion` varchar(16) DEFAULT NULL,
			
			`Faltas2` int(4) NOT NULL DEFAULT '0',
			`Tocados2` int(4) NOT NULL DEFAULT '0',
			`Rehuses2` int(4) NOT NULL DEFAULT '0',
			`PRecorrido2` double NOT NULL DEFAULT '0',
			`PTiempo2` double NOT NULL DEFAULT '0',
			`Tiempo2` double NOT NULL DEFAULT '0',
			`Velocidad2` double NOT NULL DEFAULT '0',
			`Penalizacion2` double NOT NULL DEFAULT '0',
			`Calificacion2` varchar(16) DEFAULT NULL,
			PRIMARY KEY ( `Dorsal` )
		)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		
		// FASE 1:
		// Recopilamos datos "personales"  (comunes a ambas mangas)
		// Y hacemos un calculo de las penalizaciones por recorrido de la primera manga
		$str="INSERT INTO $tablename (Dorsal,Nombre,Licencia,Categoria,Guia,Club,Faltas,Tocados,Rehuses,Tiempo,PRecorrido) 
			SELECT Dorsal,Nombre,Licencia,Categoria,Guia,Club,Faltas,Tocados,Rehuses,Tiempo, 
			( 5*Faltas + 5*Rehuses + 5*Tocados + 100*Eliminado + 200*NoPresentado ) AS PRecorrido
			FROM Resultados WHERE ( Manga =$manga1 )";
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
		
		// FASE 3: obtenemos el TRS y evaluamos puntuacion y calificacion
		$jornada= $this->getJornada($manga1);
		if (!$jornada) return $this->error("Cannot find jornada for manga: $manga1"); // error log already done
		$mng= new Mangas("Clasificaciones.php",$jornada);
		$datos_trs= $mng->datosTRS($manga1,$tiempos);
		
		// FASE 4: revisamos el array evaluando penalizacion y calificacion 
		// y lo reinsertamos en la tabla temporal		
	
		// componemos un prepared statement
		$sql ="UPDATE $tablename SET PTiempo=? , Penalizacion=? ,Velocidad=?, Calificacion=? WHERE ( Dorsal=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('dddsi',$pt,$p,$v,$c,$d);
		if (!$res) return $this->error($this->conn->error); 
		foreach($data as $row) {	
			// $this->myLogger->trace("before fase 4: ".print_r($row,true));
			// reevaluamos la penalizacion y obtenemos penalizacion en funcion del TRS
			$trs=$datos_trs[$row->Categoria]['TRS'];
			$trm=$datos_trs[$row->Categoria]['TRM'];
			// si tiempo > TRM  Penalizacion==100; eliminado
			if ($row->Tiempo<$trs) { $row->PTiempo=0; $row->Penalizacion=$row->PRecorrido; }
			if ($row->Tiempo>=$trs) { $row->PTiempo=$row->Tiempo-$trs; $row->Penalizacion=$row->PRecorrido+$row->PTiempo; }
			if ($row->Tiempo>$trm) { $row->PTiempo=100; $row->Penalizacion=100; } // eliminado por superar el TRM
			// evaluamos velocidad y ajustamos a un decimal
			if ($row->Tiempo==0) $row->Velocidad=0;
			else $row->Velocidad = $datos_trs[$row->Categoria]['Dist'] / $row->Tiempo;
			$row->Velocidad=number_format($row->Velocidad,1);
			$row->Penalizacion=number_format($row->Penalizacion,2);
			// evaluamos calificacion
			if ($row->Penalizacion==0)	$row->Calificacion = ($cfinal)?"EX P":" Excelente (p)";
			if ($row->Penalizacion>0)	$row->Calificacion = ($cfinal)?"EX":"Excelente";
			if ($row->Penalizacion>=6)	$row->Calificacion = ($cfinal)?"MB":"Muy Bien";
			if ($row->Penalizacion>=16)	$row->Calificacion = ($cfinal)?"BU":"Bueno";
			if ($row->Penalizacion>=26)	$row->Calificacion = ($cfinal)?"N.C.":"No Clasificado";
			if (($row->Penalizacion>=100) && ($row->Penalizacion<200)){
				$row->Penalizacion=100;
				$row->Calificacion = ($cfinal)?"Elim":"Eliminado";
			}
			if ($row->Penalizacion>=200)	{
				$row->penalizacion=200;
				$row->Calificacion = ($cfinal)?"N.P.":"No Presentado";
			}
			
			// y ejecutamos el update en la tabla temporal
			$pt=$row->PTiempo;
			$p=$row->Penalizacion;
			$v=$row->Velocidad;
			$c=$row->Calificacion;
			$d=$row->Dorsal;	
			// $this->myLogger->trace("after fase 4: ".print_r($row,true));
			$res=$stmt->execute();
			// $this->myLogger->trace("update clasificacion on Dorsal: $d");
			if (!$res) return $this->error($this->conn->error); 
		}
		$stmt->close(); // cerramos el prepared statement
		
		if ($manga2==0) {
			$this->myLogger->leave();
			return "";
		}
		
		/******************************** calculo de la segunda manga (si esta declarada ) *******************/
		// FASE 1: hacemos un calculo de las penalizaciones por recorrido
		// como esta vez ya estan creadas las filas, hacemos un select from resultados y luego un update por cada dorsal
		$str="SELECT Dorsal, Faltas, Tocados, Rehuses, Tiempo, 
		( 5*Faltas + 5*Rehuses + 5*Tocados + 100*Eliminado + 200*NoPresentado ) AS PRecorrido
		FROM Resultados WHERE ( Manga=$manga2 )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		
		$str="UPDATE $tablename SET Faltas2=?, Tocados2=?, Rehuses2=?, Tiempo2=?, PRecorrido2=? WHERE ( Dorsal=?)";
		$stmt=$this->conn->prepare($str);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('iiidii',$f,$t,$r,$t,$pr,$d);
		if (!$res) return $this->error($this->conn->error);
		while ($row=$rs->fetch_object()) {
			$f=$row->Faltas;
			$t=$row->Tocados;
			$r=$row->Rehuses;
			$t=$row->Tiempo;
			$pr=$row->PRecorrido;
			$d=$row->Dorsal;
			$res=$stmt->execute();
			if (!$res) return $this->error($this->conn->error);
		}
		$rs->free();
		
		// Fase 2: extraemos los tres mejores tiempos de cada categoria
		$str="SELECT * FROM $tablename ORDER BY Categoria ASC , PRecorrido2 ASC , Tiempo2 ASC ";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		
		// re-preparamos el almacen de los tres mejores tiempos de cada categoria
		foreach ( array("-","L","M","S","T") as $c ) { $tiempos[$c][0]=0;$tiempos[$c][1]=0;$tiempos[$c][2]=0; }
		$count=0;
		// analizamos el resultado de la bbdd y guardamos los tres mejores tiempos de cada categoria en $tiempos
		$lastCategoria="*";
		$data=array();
		while($row = $rs->fetch_object()) {
			if ( $row->Categoria !== $lastCategoria ) {
				$count=0;
				$lastCategoria=$row->Categoria;
			}
			if ($count<3) $tiempos[$lastCategoria][$count++]=$row->Tiempo2;
			array_push($data,$row);
		}
		$rs->free();
		
		// FASE 3: obtenemos el TRS y evaluamos puntuacion y calificacion2
		// dado que la jornada es la misma, reusamos la variable $mng
		$datos_trs= $mng->datosTRS($manga2,$tiempos);
		
		// FASE 4: revisamos el array evaluando penalizacion y calificacion
		// y lo reinsertamos en la tabla temporal
		
		// componemos un prepared statement
		$sql ="UPDATE $tablename SET PTiempo2=? , Penalizacion2=? ,Velocidad2=?, Calificacion2=? WHERE ( Dorsal=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('dddsi',$pt,$p,$v,$c,$d);
		if (!$res) return $this->error($this->conn->error);
		foreach($data as $row) {
			// $this->myLogger->trace("before fase 4: ".print_r($row,true));
			// reevaluamos la penalizacion y obtenemos penalizacion en funcion del TRS
			$trs=$datos_trs[$row->Categoria]['TRS'];
			$trm=$datos_trs[$row->Categoria]['TRM'];
			// si tiempo > TRM  Penalizacion==100; eliminado
			if ($row->Tiempo2<$trs)	{ $row->PTiempo2=0; $row->Penalizacion2=$row->PRecorrido2; }
			if ($row->Tiempo2>=$trs){ $row->PTiempo2=$row->Tiempo2-$trs; $row->Penalizacion2=$row->PRecorrido2+$row->PTiempo2; }
			if ($row->Tiempo2>$trm) { $row->PTiempo2=100; $row->Penalizacion2=100; } // eliminado por superar el TRM
			// evaluamos velocidad y ajustamos a un decimal
			if ($row->Tiempo2==0) $row->Velocidad2=0;
			else $row->Velocidad2 = $datos_trs[$row->Categoria]['Dist'] / $row->Tiempo2;
			$row->Velocidad2=number_format($row->Velocidad2,1);
			$row->Penalizacion2=number_format($row->Penalizacion2,2);
			// evaluamos calificacion

			// evaluamos calificacion
			if ($row->Penalizacion2==0)	$row->Calificacion2 = ($cfinal)?"EX P":" Excelente (p)";
			if ($row->Penalizacion2>0)	$row->Calificacion2 = ($cfinal)?"EX":"Excelente";
			if ($row->Penalizacion2>=6)	$row->Calificacion2 = ($cfinal)?"MB":"Muy Bien";
			if ($row->Penalizacion2>=16)	$row->Calificacion2 = ($cfinal)?"BU":"Bueno";
			if ($row->Penalizacion2>=26)	$row->Calificacion2 = ($cfinal)?"N.C.":"No Clasificado";
			if (($row->Penalizacion2>=100) && ($row->Penalizacion2<200)){
				$row->Penalizacion2=100;
				$row->Calificacion2 = ($cfinal)?"Elim":"Eliminado";
			}
			if ($row->Penalizacion2>=200)	{
				$row->penalizacion2=200;
				$row->Calificacion2 = ($cfinal)?"N.P.":"No Presentado";
			}
			
			// y ejecutamos el update en la tabla temporal
			$pt=$row->PTiempo2;
			$p=$row->Penalizacion2;
			$v=$row->Velocidad2;
			$c=$row->Calificacion2;
			$d=$row->Dorsal;
			// $this->myLogger->trace("after fase 4: ".print_r($row,true));
			$res=$stmt->execute();
			// $this->myLogger->trace("update clasificacion on Dorsal: $d");
			if (!$res) return $this->error($this->conn->error);
		}
		$stmt->close(); // cerramos el prepared statement
		return ""; // retornamos OK
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
		if ($res===null) return $this->error("reverse::clasificacion returned null");
		// FASE 2: hacemos un query en base a un join de la tabla de resultados
		// y la de calificaciones ordenado por categoria/celo/penalizacion/tiempo
	
		$str= "SELECT $manga AS Manga, Dorsal, Categoria, Tiempo, Penalizacion
		FROM $tablename
		ORDER BY Categoria ASC, Penalizacion DESC, Tiempo DESC";
		$rs=$this->query($str);
		if (!$rs) {
		$this->error("-------------error");
				return $this->error($this->conn->error);
		}
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
	
	/**
	 * Presenta los Clasificaciones parciales de la manga indicada
	 * @param {integer} $manga Manga ID
	 * @param {string} $cat 0:Todas 1:Large 2:Midi 3:Small 4:Midi+Small 5:Large+Midi+Small
	 * @return null on error; on success result in easyui datagrid compatible format
	 */
	function clasificacionParcial($manga,$cat="0") {
		$this->myLogger->enter();
		// Fase 1: generamos la clasificacion
		$tablename="Manga_".$manga."_".random_password(8);
		$res=$this->clasificacion($tablename,$manga);
		if ($res===null) return $this->error("parcial::clasificacion returned null");
		// FASE 2: hacemos un query en base a un join de la tabla de resultados 
		// y la de calificaciones ordenado por categoria/penalizacion/tiempo
		$rows=array();
		if ($cat==="0") { // select every categorias in a separate order
			$str= "SELECT $manga AS Manga, Dorsal, Nombre, Licencia, Categoria, Guia, Club,
				Faltas, Rehuses, Tocados, Tiempo, Velocidad, Penalizacion, Calificacion
				FROM $tablename
				ORDER BY Categoria ASC, Penalizacion ASC, Tiempo ASC";
			$rs=$this->query($str);
			if (!$rs) return $this->error($this->conn->error);
			// y devolvemos el resultado
			$lastCategoria="*";
			$puesto=1;
			while ($item=$rs->fetch_array()) {
				if ($lastCategoria!==$item['Categoria']) {
					$lastCategoria=$item['Categoria'];
					$puesto=1;
				}
				// TODO: puesto debe ser el mismo si mismos penalizacion y tiempo
				$item['Puesto']=$puesto++;
				array_push($rows,$item);
			}
			$rs->free();
		} else {
			switch($cat) {
			case "1": // Only enumerate Large
				$str= "SELECT $manga AS Manga, Dorsal, Nombre, Licencia, Categoria, Guia, Club,
					Faltas, Rehuses, Tocados, Tiempo, Velocidad, Penalizacion, Calificacion
					FROM $tablename WHERE ( Categoria = 'L' )
					ORDER BY Penalizacion ASC, Tiempo ASC";
				break;
			case "2": // Only enumerate Medium
				$str= "SELECT $manga AS Manga, Dorsal, Nombre, Licencia, Categoria, Guia, Club,
					Faltas, Rehuses, Tocados, Tiempo, Velocidad, Penalizacion, Calificacion
					FROM $tablename WHERE ( Categoria = 'M' )
					ORDER BY Penalizacion ASC, Tiempo ASC";
				break;
			case "3": // Only enumerate Small
				$str= "SELECT $manga AS Manga, Dorsal, Nombre, Licencia, Categoria, Guia, Club,
					Faltas, Rehuses, Tocados, Tiempo, Velocidad, Penalizacion, Calificacion
					FROM $tablename WHERE ( Categoria = 'S' )
					ORDER BY Penalizacion ASC, Tiempo ASC";
				break;
			case "4": // Enumerate Medium+Small
				$str= "SELECT $manga AS Manga, Dorsal, Nombre, Licencia, Categoria, Guia, Club,
					Faltas, Rehuses, Tocados, Tiempo, Velocidad, Penalizacion, Calificacion
					FROM $tablename WHERE ( Categoria = 'M' ) OR ( Categoria = 'S' )
					ORDER BY Penalizacion ASC, Tiempo ASC";
				break;
			case "5": // Enumerate Large+Medium+Small
				$str= "SELECT $manga AS Manga, Dorsal, Nombre, Licencia, Categoria, Guia, Club,
					Faltas, Rehuses, Tocados, Tiempo, Velocidad, Penalizacion, Calificacion
					FROM $tablename 
					ORDER BY Penalizacion ASC, Tiempo ASC";
				break;
			default: return $this->error("Invalid sort category: $cat");
			}
			// efectuamos el query
			$rs=$this->query($str);
			if (!$rs) return $this->error($this->conn->error);
			// y devolvemos el resultado
			$puesto=1;
			while ($item=$rs->fetch_array()) {
				// TODO: puesto debe ser el mismo si mismos penalizacion y tiempo
				$item['Puesto']=$puesto++;
				array_push($rows,$item);
			}
			$rs->free();	
		}
		$result=array();
		$result['total']=count($rows);
		$result['rows']=$rows;
		$this->myLogger->leave();
		return $result;
	}

	/**
	 * Presenta los Clasificaciones parciales de la manga indicada
	 * @param {integer} $manga1 Manga 1 ID
	 * @param {integer} $manga2 Manga 2 ID
	 * @param {string} $cat 0:Todas 1:Large 2:Midi 3:Small 4:Midi+Small 5:Large+Midi+Small
	 * @return null on error; on success result in easyui datagrid compatible format
	 */
	function clasificacionFinal($manga1,$manga2,$cat="0") {
		$this->myLogger->enter();
		// Fase 1: generamos la clasificacion de cada manga
		$tablename="Manga_".$manga1."_".$manga2."_".random_password(8);
		$res=$this->clasificacion($tablename,$manga1,$manga2);
		if ($res===null) return $this->error("final::clasificacionFinal() on mangas $manga1 & $manga2 returned null");
		$rows=array();
		if ($cat==="0") {
			// efectuamos el query
			$str="SELECT * , (Penalizacion+Penalizacion2) AS PFinal, (Tiempo+Tiempo2) AS TFinal
			FROM $tablename
			ORDER BY Categoria ASC, PFinal ASC, TFinal ASC";
			$rs=$this->query($str);
			if (!$rs) return $this->error($this->conn->error);
			// y devolvemos el resultado separando por categorias
			$lastCategoria="*";
			$puesto=1;
			while ($item=$rs->fetch_array()) {
				if ($lastCategoria!==$item['Categoria']) {
					$lastCategoria=$item['Categoria'];
					$puesto=1;
				}
				// TODO: puesto debe ser el mismo si mismos penalizacion y tiempo
				$item['Puesto']=$puesto++;
				$item['Puntos']= ($item['PFinal']==0)?"P":"";
				array_push($rows,$item);
			}
			$rs->free();
		} else {
			switch($cat){
				case "1": // Large
					$str="SELECT * , (Penalizacion+Penalizacion2) AS PFinal, (Tiempo+Tiempo2) AS TFinal
					FROM $tablename WHERE ( Categoria = 'L' )
					ORDER BY PFinal ASC, TFinal ASC";
					break;
				case "2": // Medium
					$str="SELECT * , (Penalizacion+Penalizacion2) AS PFinal, (Tiempo+Tiempo2) AS TFinal
					FROM $tablename WHERE ( Categoria = 'M' )
					ORDER BY PFinal ASC, TFinal ASC";
					break;
				case "3": // Small
					$str="SELECT * , (Penalizacion+Penalizacion2) AS PFinal, (Tiempo+Tiempo2) AS TFinal
					FROM $tablename WHERE ( Categoria = 'S' )
					ORDER BY PFinal ASC, TFinal ASC";
					break;
				case "4": // Medium+Small
					$str="SELECT * , (Penalizacion+Penalizacion2) AS PFinal, (Tiempo+Tiempo2) AS TFinal
					FROM $tablename WHERE ( Categoria = 'M' ) OR ( Categoria = 'S' )
					ORDER BY PFinal ASC, TFinal ASC";
					break;
				case "5": // Large+Medium+Small
					$str="SELECT * , (Penalizacion+Penalizacion2) AS PFinal, (Tiempo+Tiempo2) AS TFinal
					FROM $tablename
					ORDER BY PFinal ASC, TFinal ASC";
					break;
				default: return $this->error("Invalid sort category: $cat");	
			}
			// efectuamos el query
			$rs=$this->query($str);
			if (!$rs) return $this->error($this->conn->error);
			// y devolvemos el resultado separando por categorias
			$puesto=1;
			while ($item=$rs->fetch_array()) {
				// TODO: puesto debe ser el mismo si mismos penalizacion y tiempo
				$item['Puesto']=$puesto++;
				$item['Puntos']= ($item['PFinal']==0)?"P":"";
				array_push($rows,$item);
			}
			$rs->free();
		} // else
		$result=array();
		$result['total']=count($rows);
		$result['rows']=$rows;
		$this->myLogger->leave();
		return $result;
	}
}
?>