<?php
/** mandatory requires for database and logging */
require_once("logging.php");
require_once("DBConnection.php");

/* boolval is only supported in PHP > 5.3 */
if( ! function_exists('boolval')) {
	function boolval($var)	{
		return !! $var;
	}
}

/* execute query */
function execute($conn,$query) {
	do_log("ordensalida::query() ".$query);
	$rs=$conn->query($query);
	if ($rs===false) {
		$msg="ordensalida::query() Error: ".$conn->error;
		do_log($msg);
		echo json_encode(array('errorMsg'=>$msg));
		DBConnection::closeConnection($conn);
		exit(0);
	}
	return $rs;
}

/* Retrieve Mangas.Orden_Salida */
function getOrden($conn,$manga) {
	$sql="SELECT Orden_Salida FROM Mangas WHERE ( ID=$manga )";
	$rs=execute($conn,$sql);
	$row=$rs->fetch_row();
	$result=$row['Orden'];
	$rs->free();
	if ($result===null) return "";
	return $result;
}

/* Update Mangas.Orden_Salida with new value */
function setOrden($conn,$manga,$orden) {
	$sql="UPDATE Mangas SET Orden_Salida = '".$orden."' WHERE ( ID=$manga )";
	$rs=execute($conn,$sql);
	// do not call $rs->free() as no resultset returned
}

/**
 * Obtiene la lista (actualizada) de perros de una manga
 *
 * @param {mysqli-connection} conn Conexion con la base de datos
 * @param {int} $jornada ID de jornada
 * @param {int} $manga ID de manga
 * @param {boolean} $orden false->large-medium-small  true->small-medium-large
 */
function getLista($conn,$jornada,$manga) {
	// fase 0: vemos si ya hay una lista definida
	$ordensalida=getOrden($conn,$manga);
	if ($ordensalida==="") { // no hay orden predefinido
		// TODO: comprobamos si estamos en la segunda manga y usamos resultados como orden de salida
		$ordensalida=random($conn,$jornada,$manga,$orden);
	}

	// ok tenemos orden de salida. vamos a convertirla en un array asociativo
	$registrados=explode(",",$ordensalida);

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
		// si dorsal en lista se inserta; si no esta en lista implica error de consistencia
		if ( $idx !== false ) $registrados[$idx]=$row;
		else do_log("ordensalida::getLista() El dorsal ".$row->Dorsal." esta inscrito pero no aparece en el orden de salida");
	}
	$rs1->free();
	// as DB Connection is no loger needed, close it
	DBConnection::closeConnection($conn);
	
	// fase 4: construimos la tabla de resultados
	$count=0;
	$data=array();
	foreach($registrados as $item) {
		// si es un tag, lo anyadimos
		if (is_string($item)) {
			if (strstr($item,"TAG_")===false) { // dorsal no registrado: error
				do_log("ordensalida::getLista() El dorsal $item esta en el orden de salida, pero no esta inscrito");
			}
		}
		// si es un objeto anyadimos el dorsal
		if (is_object($item)) {
			array_push($data,$item);
			$count++;
		}
	}
	// finally encode result and send to client
	$result=array();
	$result["total"]=$count;
	$result["rows"]=$data;
	echo json_encode($result);
}

/**
 * Reordena el orden de salida de una manga al azar
 * 
 * @param {mysqli-connection} conn Conexion con la base de datos
 * @param {int} jornada ID de jornada
 * @param {int} manga ID de manga
 * @param {boolean} orden false->large-medium-small  true->small-medium-large
 */
function random($conn,$jornada,$manga,$orden) {
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
	
	// fase 3: generar la lista de perros "ordenada" al azar
	$tagindex=0;
	$ordensalida=$tags[$tagindex++]; // "begin" mark
	$count=0;
	while($row = $rs1->fetch_object()){
		// only add to list when grado is '-' (Any) or grado matches requested
		if ( ($grado!=="-") && ($grado!==$row->Grado) ) continue;
		// elaborate ordensalida
		$mytag="TAG_" . $row->Categoria . $row->Celo;
		while ($mytag!==$tags[$tagindex]) $ordensalida = $ordensalida . "," . $tags[$tagindex++];
		$ordensalida = $ordensalida . "," . $row->Dorsal;
	}
	// anyadimos los tags que queden en la lista
	while (isset($tags[$tagindex])) $ordensalida = $ordensalida.",".$tags[$tagindex++];
	$rs1->free();
	// fase 4: almacenar el orden de salida en los datos de la manga
	setOrden($conn,$manga,$ordensalida);
	// fase 5: limpieza y retorno de resultados
	DBConnection::closeConnection($conn);
	echo json_encode(array('success'=>true));
	return 0;
}


/**
 * Elimina un dorsal del orden de salida
 * si esta inscrito indica error y devuelve lista actual
 * 
 * @param unknown $conn conexion con la base de datos
 * @param unknown $jornada ID de jornada
 * @param unknown $manga ID de manga
 * @param unknown $dorsal ID de dorsal
 */
function remove($conn,$jornada,$manga,$dorsal){
	// TODO: si el dorsal esta inscrito damos error
	
	// recuperamos el orden de salida
	$ordensalida=getOrden($conn,$manga);
	$str=",".$dorsal.",";
	$nuevoorden=str_replace($str , "," , $ordensalida);
	setOrden($conn,$manga,$nuevoorden);
}

/**
 * Inserta un perro en la lista al final de su categoria 
 * 
 * Comprueba que el dorsal esta inscrito; si no, devuelve lista actual
 * Si esta ya en la lista lo saca de donde esta
 * Inserta el dorsal en el ultimo puesto de los perros de su misma categoria/celo 
 * 
 * @param unknown $conn conexion con la base de datos
 * @param unknown $jornada ID de jornada
 * @param unknown $manga ID de manga
 * @param unknown $dorsal ID de dorsal
 */
function insert($conn,$jornada,$manga,$dorsal) {
	// TODO: write
	// si el dorsal no esta inscrito da error
	// recuperamos orden de salida y adivinamos si el orden es LMS o SML
	// obtener datos de categoria y celo para obtener el tag a buscar
	// substituimos ",tag" por ",dorsal,tag"
	// actualizamos orden de salida
}

/**
 * Intercambia el orden de dos dorsales siempre que esten consecutivos
 * 
 * @param unknown $conn Conexion con la ddbb
 * @param unknown $jornada ID de jornada
 * @param unknown $manga ID de manga
 * @param unknown $dorsal1 Dorsal del primer perro
 * @param unknown $dorsal2 Dorsal del segundo perro
 */
function swap($conn,$jornada,$manga,$dorsal1,$dorsal2) {
	// componemos strings
	$str1=",".$dorsal1.",".$dorsal2.",";
	$str2=",".$dorsal2.",".$dorsal1.",";
	// recuperamos el orden de salida
	$ordensalida=getOrden($conn,$manga);
	if ($ordensalida==="") return;
	// si encontramos str1 lo substituimos por str2
	if (strpos($ordensalida,$str1)!==false) $nuevoorden=str_replace($str1,$str2,$ordensalida);
	// si encontramos str2 lo substituimos por str1
	else if (strpos($ordensalida,$str2)!==false) $nuevoorden=str_replace($str2,$str1,$ordensalida);
	// si no encontramos ninguno de los dos lo dejamos como estaba
	else $nuevoorden=$ordensalida;
	// actualizamos orden de salida
	setOrden($conn,$manga,$nuevoorden);
}

/**
 * Invierte el orden de salida Large/Medium/Small to Small/Medium/Large y viceversa
 * @param unknown $conn Conexion con la base de datos
 * @param unknown $jornada ID de jornada
 * @param unknown $manga ID de manga
 */
function reverse($conn,$jornada,$manga) {
	// TODO: write
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
$d2 = (isset($_REQUEST['Dorsal2']))?intval($_REQUEST['Dorsal2']):-1;
if (($j<0)||($m<0)) {
	$str="ordensalida() invalid jornada:$j or manga:$m ID.";
	do_log($str);
	echo json_encode(array('errorMsg'=>$str));
	return;
}
$oper=strval($_REQUEST['Operacion']);
// call operation$oper = strval($_REQUEST['Operation']);
if ($oper==='random') return random($conn,$j,$m,$o);
if ($oper==='remove') return remove($conn,$j,$m,$d);
if ($oper==='insert') return insert($conn,$j,$m,$d);
if ($oper==='swap') 	return swap($conn,$j,$m,$d,$d2);
if ($oper==='reverse') return reverse($conn,$j,$m);
if ($oper==='getData') return getData($conn,$j,$m);

// arriving here means invalid operation
$result="ordensalida() Invalid operation requested: $oper";
do_log($result);
echo json_encode(array('errorMsg'=>$result));
DBConnection::closeConnection($conn);
?>