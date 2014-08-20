<?php
require_once("DBObject.php");
require_once("OrdenSalida.php");
require_once("Mangas.php");
require_once("Jornadas.php");

class Resultados extends DBObject {
	protected $manga; // datos de la manga
	protected $IDManga; // ID de la manga
	protected $jornada;  // datos de la jornada
	protected $IDJornada; // ID de la jornada
	protected $cerrada; // indica si la jornada esta cerrada

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
		// obtenemos el id de jornada y vemos si la manga esta cerrada
		$obj=$this->__selectObject("*", "Mangas", "(ID=$manga)");
		if (!$obj) {
			$this->error("Manga $manga does not exists in database");
			throw new Exception($this->errormsg);
		}
		$this->manga=$obj;
		$this->IDJornada=$this->manga->Jornada;
		$obj=$this->__selectObject("*","Jornadas","(ID=$this->IDJornada)");
		if (!$obj) {
			$this->error("Cannot locate JornadaID: $this->IDJornada for MangaID:$manga in database");
			throw new Exception($this->errormsg);
		}
		$this->jornada=$obj;
		$this->cerrada=$this->jornada->Cerrada;
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
		if ($this->cerrada!=0) 
			return $this->error("Manga $idmanga comes from closed Jornada: $this->IDJornada");	
		
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
		if ($this->cerrada!=0) 
			return $this->error("Manga $idmanga comes from closed Jornada:$this->IDJornada");
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
		if ($this->cerrada!=0) 
			return $this->error("Manga $idmanga comes from closed Jornada: $this->IDJornada");
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
		$mng= new Mangas("Resultados::getResultados",$this->IDJornada,$this->IDManga);
		$tdata=$mng->evalTRS($this->manga->Recorrido,$mode); // 'dist' 'obst' 'trs' 'trm'
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