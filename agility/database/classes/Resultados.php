<?php
require_once("DBObject.php");
require_once("OrdenSalida.php");
require_once("Mangas.php");
require_once("Jornadas.php");

class Resultados extends DBObject {
	protected $IDManga; // ID de la manga
	protected $IDJornada; // ID de la jornada
	protected $dmanga=null; // datos de la manga
	protected $djornada=null;  // datos de la jornada

	private function getDatosManga() {
		if ($dmanga!=null) return $dmanga;
		$idmanga=$this->IDManga;
		// si no los tenemos todavia consultamos la base de datos
		$obj=$this->__selectObject("*", "Mangas", "(ID=$idmanga)");
		if (!is_object($obj)) {
			$this->error("Manga $idmanga does not exists in database");
			return null;
		}
		$this->dmanga=$obj;
		return $this->dmanga;
	}
	
	private function getDatosJornada() {
		if ($djornada!=null) return $djornada;
		$manga=$this->getDatosManga();
		$this->IDJornada=$manga->Jornada;
		$idjornada=$this->IDJornada;
		$idmanga=$this->IDManga;
		$obj=$this->__selectObject("*","Jornadas","(ID=$this->IDJornada)");
		if (!is_ibject($obj)) {
			$this->error("Cannot locate JornadaID: $idjornada for MangaID:$idmanga in database");
			return null;
		}
		$this->jornada=$obj;
		return $this->jornada;
	}
	
	private function isCerrada() {
		$jrd=getDatosJornada();
		return 	($jrd->Cerrada!=0)? true:false;
	}
	
	/**
	 * gets distance, obstacles, trs and trm
	 * @param {integer} $mode 0:Large 1:Medium 2:Small 3:M+S 4:L+M+S
	 * @param {array} current results data according $mode 
	 * @return array('dist','obst','trs','trm') or null on error
	 */
	private function evalTRS($mode,$results) {
		$dmanga=(array) $this->getDatosManga();
		$result= array();
		// vemos de donde tenemos que tomar los datos
		$suffix='L';
		switch(mode) {
			case 0: $suffix='L'; break;
			case 1: $suffix='M'; break;
			case 2: $suffix='S'; break;
			case 3: $suffix='M'; break; // M+S
			case 4: $suffix='L'; break; // L+M+S
		}
		// evaluamos distancia y obstaculos
		$result['dist']= $dmanga["Dist_$suffix"]; 
		$result['obst']= $dmanga["Obst_$suffix"];
		// evaluamos mejor tiempo y media de los tres mejores
		$best1=0;
		$best3=0;
		if (count($result==0)) { $best1=0; $best3=0;} // no hay ni resultados ni tiempos
		if (count($result==1)) { $best1=$result[0]['Tiempo']; $best3=$result[0]['Tiempo'];}
		if (count($result==2)) { $best1=$result[0]['Tiempo']; $best3=($result[0]['Tiempo']+$result[1]['Tiempo'])/2;}
		if (count($result>=3)) { $best1=$result[0]['Tiempo']; $best3=($result[0]['Tiempo']+$result[1]['Tiempo']+$result[2]['Tiempo'])/3;}
		// Evaluamos TRS
		switch ($dmanga["TRS_{$suffix}_Tipo"]) {
			case 0: // tiempo fijo
				$result['trs']=$dmanga["TRS_{$suffix}_Factor"];
				break;
			case 1: // mejor tiempo
				if ($dmanga["TRS_{$suffix}_Unit"]==="s") $result['trs']= $best1 + $dmanga["TRS_${suffix}_Factor"]; // ( + X segundos )
				else $result['trs']= $best1 * ( (100+$dmanga["TRS_{$suffix}_Factor"]) / 100) ; // (+ X por ciento)
				break;
			case 2: // media de los tres mejores tiempos
				if ($dmanga["TRS_{$suffix}_Unit"]==="s") $result['trs']= $best3 + $dmanga["TRS_${suffix}_Factor"]; // ( + X segundos )
				else $result['trs']= $best3 * ( (100+$dmanga["TRS_{$suffix}_Factor"]) / 100) ; // (+ X por ciento)
				break;
		}
		$result['trs']=ceil($result['trs']); // redondeamos hacia arriba
		// Evaluamos TRM
		switch($dmanga["TRM_{$suffix}_Tipo"]) {
			case 0: // TRM Fijo
				$result['trm']=$dmanga["TRM_{$suffix}_Factor"];
				break;
			case 1: // TRS + (segs o porcentaje)
				if ($dmanga["TRM_{$suffix}_Unit"]==="s") $result['trm']=$result['trs'] + $dmanga["TRM_{$suffix}_Factor"]; // ( + X segundos )
				else $result['trm'] = $result['trs'] * ( (100+$dmanga["TRM_{$suffix}_Factor"]) / 100) ; // (+ X por ciento)
				break;
		}
		$result['trm']=ceil($result['trm']); // redondeamos hacia arriba
		// esto es todo amigos
		return $result;
	}
	
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
			$this->errormsg="Resultados::Construct invalid Manga ID: $manga";
			throw new Exception($this->errormsg);
		}
		$this->IDManga=$manga;
		$this->IDJornada=0;
		$this->dmanga=null;
		$this->djornada=null;
	}
		
	/**
	 * Inserta perro en la lista de resultados de la manga
	 * los datos del perro se toman de la tabla perroguiaclub
	 * @param {array} $objperro datos perroguiaclub
	 * @param {integer} $ndorsal Dorsal con el que compite
	 * @return "" on success; else error string
	 */
	function insertByData($objperro,$ndorsal) {
		$error="";
		$idmanga=$this->IDManga;
		$this->myLogger->enter();
		if ($ndorsal<=0) return $this->error("No dorsal specified");
		if ($this->isCerrada()) 
			return $this->error("Manga $idmanga comes from closed Jornada:".$this->IDJornada);	
		
		// Insert into resultados. On duplicate ($manga,$idperro) key ignore
		$sql="INSERT INTO Resultados (Manga,Dorsal,Perro,Nombre,Licencia,Categoria,Grado,NombreGuia,NombreClub) 
				VALUES (?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE Manga=Manga";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->conn->error;
		$res=$stmt->bind_param('iiissssss',$manga,$dorsal,$perro,$nombre,$licencia,$categoria,$grado,$guia,$club);
		if (!$res) return $this->error($stmt->error);
		$manga=$idmanga;
		$dorsal=$ndorsal;
		$perro=$objperro['ID'];
		$nombre=$objperro['Nombre'];
		$licencia=$objperro['Licencia'];
		$categoria=$objperro['Categoria'];
		$grado=$objperro['Grado'];
		$guia=$objperro['NombreGuia'];
		$club=$objperro['NombreClub'];
		// ejecutamos el query
		$res=$stmt->execute();
		if (!$res) $error=$stmt->error;
		$stmt->close();
		$this->myLogger->leave();
		return $error;
	}
	
	/**
	 * Inserta perro en la lista de resultados de la manga
	 * @param {integer} $integer ID del perro
	 * @param {integer} $ndorsal Dorsal con el que compite
	 * @return "" on success; else error string
	 */
	function insert($idperro,$ndorsal) {
		// obtenemos los datos del perro
		$pobj=new Dogs("Resultados::insert");
		$perro=$pobj->selectByIDPerro($iderro);
		if (!$perro) throw new Exception("No hay datos para el perro a inscribir con id: $idp");
		return insertByData($perro,$ndorsal);
	}
	
	/**
	 * Borra el idperro de la lista de resultados de la manga
	 * @param {integer} $idperro
	 * @return "" on success; null on error
	 */
	function delete($idperro) {
		$this->myLogger->enter();
		$idmanga=$this->IDManga;
		if ($idperro<=0) return $this->error("No Perro ID specified");
		if ($this->isCerrada()) 
			return $this->error("Manga $idmanga comes from closed Jornada:".$this->IDJornada);
		$str="DELETE * FROM Resultados WHERE ( Perro=$idperro ) AND ( Manga=$idmanga)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * selecciona los datos del idperro indicado desde la lista de resultados de la manga
	 * @param {integer} $idperro
	 * @return {array} [key=>value,...] on success; null on error
	 */
	function select($idperro) {
		$this->myLogger->enter();
		$idmanga=$this->IDManga;
		if ($idperro<=0) return $this->error("No Perro ID specified");
		$row=$this->__singleSelect("*", "Resultados", "(Perro=$idperro) AND (Manga=$idmanga)");
		if(!$row) return $this->error("No Results for Perro:$idperro on Manga:$idmanga");
		$this->myLogger->leave();
		return $row;
	}
	
	/**
	 * Actualiza los resultados de la manga para el idperro indicado
	 * @param {integer} $idperro
	 * @return datos actualizados desde la DB; null on error
	 */
	function update($idperro) {
		$this->myLogger->enter();
		$idmanga=$this->IDManga;
		if ($idperro<=0) return $this->error("No Perro ID specified");
		if ($this->isCerrada()) 
			return $this->error("Manga $idmanga comes from closed Jornada:".$this->IDJornada);
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
		// efectuamos el update, marcando "pendiente" como false
		$sql="UPDATE Resultados 
			SET Entrada='$entrada' , Comienzo='$comienzo' , 
				Faltas=$faltas , Rehuses=$rehuses , Tocados=$tocados ,
				NoPresentado=$nopresentado , Eliminado=$eliminado , 
				Tiempo=$tiempo , Observaciones='$observaciones' , Pendiente=0
			WHERE (Perro=$idperro) AND (Manga=$this->IDManga)";
		$rs=$this->query($sql);
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return $this->select($idperro);
	}
	
	/**
	 * Presenta una tabla ordenada segun los resultados de la manga
	 * @return null on error else array en formato easyui datagrid
	 *@param {integer} mode 0:L 1:M 2:S 3:MS 4:LMS.
	 *@return requested data or error
	 */
	
	function getResultados($mode) {
		$this->myLogger->enter();
		$idmanga=$this->IDManga;
		
		// FASE 0: en funcion del tipo de recorrido y modo pedido
		// ajustamos el criterio de busqueda de la tabla de resultados
		$where="(Manga=$idmanga) AND (Pendiente=0) ";
		switch ($this->manga->Recorrido) {
			case 0: // Large, Medium, Small por separado
				switch($mode) {
					case 0: /* Large */		$where= "$where AND (Categoria='L')"; break;
					case 1: /* Medium */	$where= "$where AND (Categoria='M')"; break;
					case 2: /* Small */		$where= "$where AND (Categoria='S')"; break;
					case 3: // Medium+Small - invalido
					case 4: // Large+Medium+Small - invalido
					default:
						return $this->error("Tipo de recorrido 0 incompatible con datos pedidos");
				} 
			case 1: // Large por separado, Medium+Small conjunta
				switch($mode) {
					case 0: /* Large */		$where= "$where AND (Categoria='L')"; break;
					case 3: /* Medium+Small */ $where= "$where AND ( (Categoria='L') OR (Categoria='M') )"; break;
					case 1: // Medium - invalido
					case 2: // Small - invalido
					case 4: // Large+Medium+Small - invalido
					default:
						return $this->error("Tipo de recorrido 1 incompatible con datos pedidos");
				} 
			case 2: // Large+Medium+Small conjunta
				switch($mode) {
					case 4: // Large+Medium+Small */
						$where= "$where AND ( (Categoria='L') OR (Categoria='M') OR (Categoria='S') )"; break;
					case 0: // Large - invalido
					case 1: // Medium - invalido
					case 2: // Small - invalido
					case 3: // Medium+Small - invalido
					default:
						return $this->error("Tipo de recorrido 2 incompatible con datos pedidos");
				}
		}
		// FASE 1: recogemos resultados ordenados por precorrido y tiempo
		$res=$this->__select(
				"Dorsal,Perro,Nombre,Licencia,Categoria,NombreGuia,NombreClub,Faltas,Tocados,Rehuses,Tiempo,
					( 5*Faltas + 5*Rehuses + 5*Tocados + 100*Eliminado + 200*NoPresentado ) AS PRecorrido", 
				"Resultados", 
				$where, 
				" PRecorrido ASC, Tiempo ASC", 
				"");
		$table=$res['rows'];
		$this->myLogger->leave();
		// FASE 2: evaluamos TRS Y TRM
		$tdata=$this->evalTRS($mode,table); // array( 'dist' 'obst' 'trs' 'trm')
		$trs=$tdata['trs'];
		$trm=$tdata['trm'];
		// FASE 3: añadimos ptiempo, puntuacion y clasificacion
		foreach ($table as $row ) {
			// evaluamos penalizacion por tiempo y penalizacion final
			if ($row['Tiempo']<$trs)  // Por debajo del TRS
				{ $row['PTiempo']=0; $row['Penalizacion']=$row['PRecorrido']; }
			if ($row['Tiempo']>=$trs) // Superado TRS
				{ $row['PTiempo']=$row['Tiempo']-$trs; $row['Penalizacion']=$row['PRecorrido']+$row['PTiempo']; }
			if ($row['Tiempo']>$trm) // Superado TRM: eliminado
				{ $row['PTiempo']=100; $row['Penalizacion']=100; }
				
			// evaluamos velocidad y ajustamos numero de decimales
			if ($row['Tiempo']==0) $row['Velocidad']=0;
			else $row['Velocidad'] = number_format( $tdata['dist'] / $row['Tiempo'], 1); // velocidad con 1 decimal
			$row['Penalizacion'] =number_format($row['Penalizacion'],2); // penalizacion con 2 decimales
			
			// evaluamos calificacion
			if ($row['Penalizacion']==0)	$row['Calificacion'] = "Excelente (p)";
			if ($row['Penalizacion']>0)		$row['Calificacion'] = "Excelente";
			if ($row['Penalizacion']>=6)	$row['Calificacion'] = "Muy Bien";
			if ($row['Penalizacion']>=16)	$row['Calificacion'] = "Bueno";
			if ($row['Penalizacion']>=26)	$row['Calificacion'] = "No Clasificado";
			if ($row['Penalizacion']>=100) {$row['Penalizacion']=100; $row['Calificacion'] = "Eliminado"; }
			if ($row['Penalizacion']=200)  {$row['Penalizacion']=200; $row['Calificacion'] = "No Presentado"; }
		}
		// FASE 4: re-ordenamos los datos en base a la puntuacion y retornamos resultado
		usort($table, function($a, $b) {
			return $b['Penalizacion'] - $a['Penalizacion']; // sort in reverse (ASC) order
		});
		// finalmente retornamos array
		$this->myLogger->leave();
		return $res;
	}
}
?>