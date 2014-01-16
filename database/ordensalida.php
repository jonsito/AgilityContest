<?php
/** mandatory requires for database and logging */
require_once("logging.php");
require_once("DBConnection.php");

if( ! function_exists('boolval')) {
	function boolval($var)	{
		return !! $var;
	}
}

function execute($conn,$query) {
	do_log("ordensalida::query() ".$query);
	$rs=$conn->query($query);
	if (!$rs) {
		$msg="ordensalida::query() Error: ".$conn->error;
		do_log($msg);
		echo json_encode(array('errorMsg'=>$msg));
		DBConnection::closeConnection($conn);
		exit(0);
	}
	return $rs;
}

/**
 * Obtiene la lista de perros de una manga en orden aleatorio
 * @param {mysqli-connection} conn Conexion con la base de datos
 * @param {int} jornada ID de jornada
 * @param {int} manga ID de manga
 * @param {boolean} orden false->large-medium-small  true->small-medium-large
 */
function getAleatorio($conn,$jornada,$manga,$orden) {
	$asc=" ASC";
	$tags=array("TAG_BEGIN","TAG_-0","TAG_-1","TAG_L0","TAG_L1","TAG_M0","TAG_M1","TAG_S0","TAG_S1","TAG_END");
	if ($orden) {
		$asc=" DESC";
		$tags=array("TAG_BEGIN","TAG_S0","TAG_S1","TAG_M0","TAG_M1","TAG_L0","TAG_L1","TAG_-0","TAG_-1","TAG_END");
	}
	// fase 1: obtener los perros inscritos en la jornada
	$sql1="SELECT * FROM InscritosJornada WHERE ( Jornada=$jornada ) ORDER BY Categoria $asc , Celo ASC, Equipo, Orden";
	$rs1=execute($conn,$sql1);
	// fase 2: obtener las categorias de perros que debemos aceptar
	$sql2="SELECT Grado FROM Mangas,Tipo_Manga WHERE (Mangas.Tipo=Tipo_Manga.Tipo) AND ( ID=$manga )";
	$rs2=execute($conn,$sql2);
	$obj2=$rs2->fetch_object();
	$rs2->free();
	$grado= $obj2->Grado;
	// fase 3: crear el array y la lista de perros a devolver
	$data=array();
	$tagindex=0;
	$ordensalida=$tags[$tagindex]; // "begin" mark
	$count=0;
	while($row = $rs1->fetch_object()){
		// only add to list when grado is '-' (Any) or grado matches requested
		if ( ($grado!=="-") && ($grado!==$row->Grado) ) continue;
		array_push($data, $row);
		$count++;
		// elaborate ordensalida
		$mytag="TAG_" . $row->Categoria . $row->Celo;
		while ($mytag!==$tags[$tagindex]) $ordensalida = $ordensalida . "," . $tags[$tagindex++];
		$ordensalida = $ordensalida . "," . $row->Dorsal;
	}
	$rs1->free();
	$result=array();
	$result["total"]=$count;
	$result["rows"]=$data;
	// fase 4: almacenar el orden de salida en los datos de la manga
	$sql3="UPDATE Mangas SET Orden_Salida='".$ordensalida."' WHERE ( ID=$manga )";
	$rs3=execute($conn,$sql3); // don't call free() on $rs3: is just a boolean
	// fase 5: limpieza y retorno de resultados
	DBConnection::closeConnection($conn);
	echo json_encode($result);
	return 0;
}

/**
 * Obtiene la lista (actualizada) de perros de una manga
 * 
 * Si orden previamente establecido
 * - usa este orden
 * - revisa la lista de inscritos y actualiza/borra entradas
 * Si no hay orden previo:
 * - si primera manga, orden aleatorio
 * - si segunda manga, orden de clasificacion
 * 
 * @param {mysqli-connection} conn Conexion con la base de datos
 * @param {int} $jornada ID de jornada
 * @param {int} $manga ID de manga
 * @param {boolean} $orden false->large-medium-small  true->small-medium-large
 */
function getLista($conn,$jornada,$manga,$orden) {
	// fase 0: vemos si ya hay una lista definida
	$sql0="SELECT Orden_Salida FROM Mangas WHERE ( ID=$manga )";
	$rs0=execute($conn,$sql1);
	$row=$rs0->fetch_row();
	$ordensalida=$row['Orden'];
	$rs0->free();
	if ($ordensalida==="") { // no hay orden predefinido
		// TODO: comprobamos si estamos en la segunda manga
		return getAleatorio($conn,$jornada,$manga,$orden);
	}
	
	// ok tenemos orden de salida. vamos a convertirla en un array asociativo
	$registrados=explode(",",$ordensalida);
	$pendientes=array();
	
	// fase 1: obtener los perros inscritos en la jornada
	$asc=($orden)?" DESC":" ASC";
	$sql1="SELECT * FROM InscritosJornada WHERE ( Jornada=$jornada ) ORDER BY Categoria $asc , Celo ASC, Equipo, Orden";
	$rs1=execute($conn,$sql1);
	// fase 2: obtener las categorias de perros que debemos aceptar
	$sql2="SELECT Grado FROM Mangas,Tipo_Manga WHERE (Mangas.Tipo=Tipo_Manga.Tipo) AND ( ID=$manga )";
	$rs2=execute($conn,$sql2);
	$obj2=$rs2->fetch_object();
	$rs2->free();
	$grado= $obj2->Grado;
	
	// fase 3: crear el array y la lista de perros y contrastarlo con la tabla y orden registrado
	$data=array();
	while($row = $rs1->fetch_object()){
		// only add to list when grado is '-' (Any) or grado matches requested
		if ( ($grado!=="-") && ($grado!==$row->Grado) ) continue;
		$idx=array_search($row->Dorsal,$registrados);
		if ( $idx !== false ) {
			// si el dorsal esta ya registrado lo insertamos en su sitio
			$registrados[$idx]=$row;
		} else {
			// si no esta registrado lo ponemos como pendiente
			array_push($pendientes,$row);
		}
	}
	$rs1->free();
	
	// fase 4: reconstruimos el orden de salida
	$ordensalida="";
	foreach($registrados as $item) {
		// si es un tag, lo anyadimos
		if (is_string($item)) {
			if (strstr($item,"TAG_")===false) continue; // elemento eliminado; skip
			if ($ordensalida === "") $ordensalida = $item;
			else $ordensalida = $ordensalida . "," . $item;
		}
		// si es un objeto anyadimos el dorsal
		if (is_object($item)) {
			if ($ordensalida === "") $ordensalida = $item->Dorsal;
			else $ordensalida = $ordensalida . "," . $item->Dorsal;
		}
	}
	// TODO: insert unregistered entries
	$result=array();
	$result["total"]=$count;
	$result["rows"]=$data;
}

/**
 * Elimina un dorsal del orden de salida
 * si esta inscrito indica error y devuelve lista actual
 * 
 * @param unknown $conn
 * @param unknown $jornada
 * @param unknown $manga
 * @param unknown $dorsal
 */
function remove($conn,$jornada,$manga,$dorsal){
	
}

/**
 * Mueve / inserta un perro en la lista
 * 
 * Comprueba que el dorsal esta inscrito; si no, devuelve lista actual
 * Remueve el dorsal $dorsal de donde está (si existe)
 * Lo inserta _despues_ del dorsal $index
 * Si index==-1, inserta al final de la lista teniendo en cuenta categoría y celo
 * 
 * @param {int} $dorsal dorsal a mover/insertar
 * @param {int} $index insertar despues del éste dorsal.
 */
function insertAfter($jornada,$manga,$dorsal,$index) {
	
}

// connect database
$conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
if (!$conn) {
	$str='ordensalida() cannot contact database.';
	do_log($str);
	echo json_encode(array('errorMsg'=>$str));
	return;
}
if (! isset($_REQUEST['Operacion'])) {
	$str='Call ordensalida() with no operation declared.';
	do_log($str);
	echo json_encode(array('errorMsg'=>$str));
	DBConnection::closeConnection($conn);
	return;
}

// retrieve variables
$j = (isset($_REQUEST['Jornada']))?intval($_REQUEST['Jornada']):-1;
$m = (isset($_REQUEST['Manga']))?intval($_REQUEST['Manga']):-1;
$o = (isset($_REQUEST['Orden']))?boolval($_REQUEST['Orden']):false; 
$d = (isset($_REQUEST['Dorsal']))?intval($_REQUEST['Dorsal']):-1;
$i = (isset($_REQUEST['Index']))?intval($_REQUEST['Index']):-1;
if (($j<0)||($m<0)) {
	$str="ordensalida() invalid jornada:$j or manga:$m ID.";
	do_log($str);
	echo json_encode(array('errorMsg'=>$str));
	return;
}
$oper=strval($_REQUEST['Operacion']);
// call operation$oper = strval($_REQUEST['Operation']);
if ($oper==='random') return getAleatorio($conn,$j,$m,$o);
if ($oper==='update') return getLista($conn,$j,$m,$o);
if ($oper==='remove') return remove($conn,$j,$m,$d);
if ($oper==='insert') return insertAfter($conn,$j,$m,$d,$i);

// arriving here means invalid operation
$result="ordensalida() Invalid operation requested: $oper";
do_log($result);
echo json_encode(array('errorMsg'=>$result));
DBConnection::closeConnection($conn);
?>