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
		if ($this->dmanga!=null) return $this->dmanga;
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
		if ($this->djornada!=null) return $this->djornada;
		$manga=$this->getDatosManga();
		$this->IDJornada=$manga->Jornada;
		$idjornada=$this->IDJornada;
		$idmanga=$this->IDManga;
		$obj=$this->__selectObject("*","Jornadas","(ID=$this->IDJornada)");
		if (!is_object($obj)) {
			$this->error("Cannot locate JornadaID: $idjornada for MangaID:$idmanga in database");
			return null;
		}
		$this->djornada=$obj;
		return $this->djornada;
	}
	
	private function isCerrada() {
		$jrd=$this->getDatosJornada();
		return 	($jrd->Cerrada!=0)? true:false;
	}
	
	/**
	 * gets distance, obstacles, trs and trm
	 * @param {integer} $mode 0:Large 1:Medium 2:Small 3:M+S 4:L+M+S
	 * @param {array} current results data according $mode 
	 * @return array('dist','obst','trs','trm','vel') or null on error
	 */
	private function evalTRS($mode,$data) {
		$dmanga=(array) $this->getDatosManga();
		$result= array();
		// vemos de donde tenemos que tomar los datos
		$suffix='L';
		switch($mode) {
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
		if (count($data)==0) { $best1=0; $best3=0;} // no hay ni resultados ni tiempos
		if (count($data)==1) { $best1=$data[0]['Tiempo']; $best3=$data[0]['Tiempo'];}
		if (count($data)==2) { $best1=$data[0]['Tiempo']; $best3=($data[0]['Tiempo']+$data[1]['Tiempo'])/2;}
		if (count($data)>=3) { $best1=$data[0]['Tiempo']; $best3=($data[0]['Tiempo']+$data[1]['Tiempo']+$data[2]['Tiempo'])/3;}
		// Evaluamos TRS
		switch ($dmanga["TRS_{$suffix}_Tipo"]) {
			// NOTA IMPORTANTE: 
			// No se hace chequeo de tipos, con lo que si por error en un calculo de TRS standard se pide un tipo STD+XX
			// la aplicacion entrará en un bucle infinito
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
			case 3: // trs standard +xxx						
				$result_std=getResultados(0)['trs'];
				if ($dmanga["TRS_{$suffix}_Unit"]==="s") 
					$result['trs']= $result_std['trs'] + $dmanga["TRS_${suffix}_Factor"]; // ( + X segundos )
				else $result['trs']= $result_std['trs'] * ( (100+$dmanga["TRS_{$suffix}_Factor"]) / 100) ; // (+ X por ciento)
				break;
			case 4: // trs medium + xx						
				$result_med=getResultados(1)['trs'];
				if ($dmanga["TRS_{$suffix}_Unit"]==="s") 
					$result['trs']= $result_med['trs'] + $dmanga["TRS_${suffix}_Factor"]; // ( + X segundos )
				else $result['trs']= $result_med['trs'] * ( (100+$dmanga["TRS_{$suffix}_Factor"]) / 100) ; // (+ X por ciento)
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
		// Finalmente evaluamos la velocidad de la ronda
		$result['vel']= ($result['trs']==0)?0:number_format($result['dist']/$result['trs'],1);
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
		switch ($mode) {
			case 0: /* Large */		$where= "$where AND (Categoria='L')"; break;
			case 1: /* Medium */	$where= "$where AND (Categoria='M')"; break;
			case 2: /* Small */		$where= "$where AND (Categoria='S')"; break;
			case 3: /* Med+Small */ $where= "$where AND ( (Categoria='M') OR (Categoria='S') )"; break;
			case 4: /* L+M+S */ 	$where= "$where AND ( (Categoria='L') OR (Categoria='M') OR (Categoria='S') )"; break;
			default: return $this->error("modo de recorrido desconocido:$mode");
		}
		// FASE 1: recogemos resultados ordenados por precorrido y tiempo
		$res=$this->__select(
				"Dorsal,Perro,Nombre,Licencia,Categoria,Grado,NombreGuia,NombreClub,Faltas,Tocados,Rehuses,Tiempo,
					( 5*Faltas + 5*Rehuses + 5*Tocados + 100*Eliminado + 200*NoPresentado ) AS PRecorrido,
					0 AS PTiempo, 0 AS Penalizacion, '' AS Calificacion, 0 AS Velocidad", 
				"Resultados", 
				$where, 
				" PRecorrido ASC, Tiempo ASC", 
				"");
		$table=$res['rows'];
		$this->myLogger->leave();
		// FASE 2: evaluamos TRS Y TRM
		$tdata=$this->evalTRS($mode,$table); // array( 'dist' 'obst' 'trs' 'trm', 'vel')
		$res['trs']=$tdata; // store trs data into result
		$trs=$tdata['trs'];
		$trm=$tdata['trm'];
		// FASE 3: añadimos ptiempo, puntuacion y clasificacion
		$size=count($table);
		for ($idx=0;$idx<$size;$idx++ ){ 
			// importante: las asignaciones se hacen en base a $table[$idx], 
			// pues si no solo se actualiza la copia
			
			// evaluamos penalizacion por tiempo y penalizacion final
			if ($table[$idx]['Tiempo']<$trs) { // Por debajo del TRS
				$table[$idx]['PTiempo']		= 	0.0; 
				$table[$idx]['Penalizacion']=	$table[$idx]['PRecorrido'];
			}
			if ($table[$idx]['Tiempo']>=$trs) { // Superado TRS
				$table[$idx]['PTiempo']		=	$table[$idx]['Tiempo'] 		-	$trs; 
				$table[$idx]['Penalizacion']=	floatval($table[$idx]['PRecorrido'])	+	$table[$idx]['PTiempo'];
			}
			if ($table[$idx]['Tiempo']>$trm) { // Superado TRM: eliminado
				$table[$idx]['Penalizacion']=	100.0;
			}
				
			// evaluamos velocidad 
			if ($table[$idx]['Tiempo']==0)	$table[$idx]['Velocidad'] = 0;
			else 	$table[$idx]['Velocidad'] =  $tdata['dist'] / $table[$idx]['Tiempo'];
			
			// evaluamos calificacion 
			if ($table[$idx]['Penalizacion']>=200)  {
				$table[$idx]['Penalizacion']=200.0; 
				$table[$idx]['Calificacion'] = "No Presentado"; 
			}
			else if ($table[$idx]['Penalizacion']>=100) {
				$table[$idx]['Penalizacion']=100.0; 
				$table[$idx]['Calificacion'] = "Eliminado"; 
			}
			else if ($table[$idx]['Penalizacion']>=26)	$table[$idx]['Calificacion'] = "No Clasificado";
			else if ($table[$idx]['Penalizacion']>=16)	$table[$idx]['Calificacion'] = "Bueno";
			else if ($table[$idx]['Penalizacion']>=6)	$table[$idx]['Calificacion'] = "Muy Bien";
			else if ($table[$idx]['Penalizacion']>0)	$table[$idx]['Calificacion'] = "Excelente";
			else if ($table[$idx]['Penalizacion']==0)	$table[$idx]['Calificacion'] = "Excelente (p)";
		}
		// FASE 4: re-ordenamos los datos en base a la puntuacion y calculamos campo "Puesto"
		usort($table, function($a, $b) {
			if ( $a['Penalizacion'] == $b['Penalizacion'] )	return ($a['Tiempo'] > $b['Tiempo'])? 1:-1;
			return ( $a['Penalizacion'] > $b['Penalizacion'])?1:-1;
		});
		
		// format output data and take care con duplicated penalizacion and time
		$puesto=1;
		$last=0;
		for($idx=0;$idx<$size;$idx++) {
			// ajustamos puesto
			$now=100*$table[$idx]['Penalizacion']+$table[$idx]['Tiempo'];
			if ($last!=$now) { $last=$now; $puesto=1+$idx; }
			$table[$idx]['Puesto']=$puesto;
			/*
			// This should be done at javascript view level
			// ajustamos penalizacion y tiempo con 2 decimales
			$table[$idx]['Penalizacion'] =number_format($table[$idx]['Penalizacion'],2);
			$table[$idx]['Velocidad'] =number_format($table[$idx]['Velocidad'],1); 
			$table[$idx]['Tiempo'] =number_format($table[$idx]['Tiempo'],2);
			if ($table[$idx]['Penalizacion']>=100) { $table[$idx]['Tiempo']="-"; $table[$idx]['Velocidad']="-"; }
			if ($table[$idx]['Penalizacion']>=200) $table[$idx]['Puesto']="-";
			*/
		}
		// finalmente retornamos array
		$this->myLogger->leave();
		$res['rows']=$table;
		return $res;
	}
	
	function getTRS($mode) {
		$this->myLogger->enter();
		$trs=getResultados($mode)['trs'];
		$this->myLogger->leave();
		return $trs;
	}
}
?>