<?php
// mandatory includes :-)
require_once("logging.php");
require_once("DBConnection.php");

// connect database
$conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
if (!$conn) {
	$msg='update_Manga() cannot contact database.';
	do_log($msg);
	echo json_encode(array('errorMsg'=>$msg));
	return;
}
// check for primary key
if (! isset($_GET['ID'])) {
	$str='Call update_Manga() with no ID declared.';
	do_log($str);
	echo json_encode(array('errorMsg'=>$str));
	DBConnection::closeConnection($conn);
	return;
}

// preparamos la query SQL
$sql= "UPDATE Mangas SET
 Recorrido=? ,
 Dist_L=? , Obst_L=? , Dist_M=? , Obst_M=? , Dist_S=? , Obst_S=? ,
 TRS_L_Tipo=? , TRS_L_Factor=? , TRS_L_Unit=? , TRM_L_Tipo=? , TRM_L_Factor=? , TRM_L_Unit=? ,
 TRS_M_Tipo=? , TRS_M_Factor=? , TRS_M_Unit=? , TRM_M_Tipo=? , TRM_M_Factor=? , TRM_M_Unit=? ,
 TRS_S_Tipo=? , TRS_S_Factor=? , TRS_S_Unit=? , TRM_S_Tipo=? , TRM_S_Factor=? , TRM_S_Unit=? ,
 Juez1=? , Juez2=? ,
 Observaciones=? , Cerrada=?
		WHERE (ID=?)";

$stmt=$conn->prepare($sql);
if (!$stmt) {
	$msg="update_Manga::prepare() failed ".$conn->error;
	do_log($msg);
	return $msg;
}
$res=$stmt->bind_param(
	'iiiiiiiiisiisiisiisiisiissssii',
	$recorrido,
	// distancias y obstaculos
	$dist_l,		$obst_l,		$dist_m,		$obst_m,		$dist_s,		$obst_s,
	// TRS y TRM Large
	$trs_l_tipo,	$trs_l_factor,	$trs_l_unit,	$trm_l_tipo,	$trm_l_factor,	$trm_l_unit,	
	// TRS Y TRM Medium	
	$trs_m_tipo,	$trs_m_factor,	$trs_m_unit,	$trm_m_tipo,	$trm_m_factor,	$trm_m_unit,
	// TRS y TRM Small
	$trs_s_tipo,	$trs_s_factor,	$trs_s_unit,	$trm_s_tipo,	$trm_s_factor,	$trm_s_unit,
	// Jueces y observaciones
	$juez1, 	$juez2, 	$observaciones,
	// cerrada
	$cerrada,
	// id
	$id
	);
if (!$res) {
	$msg="update_Manga::bind() failed ".$conn->error;
	do_log($msg);
	return $msg;
}

// retrieve GET vars
/*
 * ID		(PRIMARY KEY)
 * Jornada	(no debe ser modificada)
 * Tipo 	(no debe ser modificada) 
 * Recorrido 
 * Dist_L Obst_L Dist_M Obst_M Dist_S Obst_S
 * TRS_L_Tipo TRS_L_Factor TRS_L_Unit TRM_L_Tipo TRM_L_Factor TRM_L_Unit 
 * TRS_M_Tipo TRS_M_Factor TRS_M_Unit TRM_M_Tipo TRM_M_Factor TRM_M_Unit
 * TRS_S_Tipo TRS_S_Factor TRS_S_Unit TRM_S_Tipo TRM_S_Factor TRM_S_Unit
 * Juez1 Juez2
 * Observaciones Cerrada
 * Orden_Salida (se modifica en otro sitio)
 */
$id			= intval($_REQUEST['ID']);
$recorrido = (isset($_REQUEST['Recorrido']))?intval($_REQUEST['Recorrido']):0;
// distancias
$dist_l = (isset($_REQUEST['Dist_L']))?intval($_REQUEST['Dist_L']):0;
$dist_m = (isset($_REQUEST['Dist_M']))?intval($_REQUEST['Dist_M']):0;
$dist_s = (isset($_REQUEST['Dist_S']))?intval($_REQUEST['Dist_S']):0;
// obstaculos
$obst_l = (isset($_REQUEST['Obst_L']))?intval($_REQUEST['Obst_L']):0;
$obst_m = (isset($_REQUEST['Obst_M']))?intval($_REQUEST['Obst_M']):0;
$obst_s = (isset($_REQUEST['Obst_S']))?intval($_REQUEST['Obst_S']):0;
// tipo TRS
$trs_l_tipo = (isset($_REQUEST['TRS_L_Tipo']))?intval($_REQUEST['TRS_L_Tipo']):0;
$trs_m_tipo = (isset($_REQUEST['TRS_M_Tipo']))?intval($_REQUEST['TRS_M_Tipo']):0;
$trs_s_tipo = (isset($_REQUEST['TRS_S_Tipo']))?intval($_REQUEST['TRS_S_Tipo']):0;
// tipo TRM
$trm_l_tipo = (isset($_REQUEST['TRM_L_Tipo']))?intval($_REQUEST['TRM_L_Tipo']):0;
$trm_m_tipo = (isset($_REQUEST['TRM_M_Tipo']))?intval($_REQUEST['TRM_M_Tipo']):0;
$trm_s_tipo = (isset($_REQUEST['TRM_S_Tipo']))?intval($_REQUEST['TRM_S_Tipo']):0;
// factor TRS
$trs_l_factor = (isset($_REQUEST['TRS_L_Factor']))?intval($_REQUEST['TRS_L_Factor']):0;
$trs_m_factor = (isset($_REQUEST['TRS_M_Factor']))?intval($_REQUEST['TRS_M_Factor']):0;
$trs_s_factor = (isset($_REQUEST['TRS_S_Factor']))?intval($_REQUEST['TRS_S_Factor']):0;
// factor TRM
$trm_l_factor = (isset($_REQUEST['TRM_L_Factor']))?intval($_REQUEST['TRM_L_Factor']):0;
$trm_m_factor = (isset($_REQUEST['TRM_M_Factor']))?intval($_REQUEST['TRM_M_Factor']):0;
$trm_s_factor = (isset($_REQUEST['TRM_S_Factor']))?intval($_REQUEST['TRM_s_Factor']):0;
// Unidad TRS
$trs_l_unit = (isset($_REQUEST['TRS_L_Unit']))?strval($_REQUEST['TRS_L_Unit']):"s";
$trs_m_unit = (isset($_REQUEST['TRS_M_Unit']))?strval($_REQUEST['TRS_M_Unit']):"s";
$trs_s_unit = (isset($_REQUEST['TRS_S_Unit']))?strval($_REQUEST['TRS_S_Unit']):"s";
// Unidad TRM
$trm_l_unit = (isset($_REQUEST['TRM_L_Unit']))?strval($_REQUEST['TRM_L_Unit']):"s";
$trm_m_unit = (isset($_REQUEST['TRM_M_Unit']))?strval($_REQUEST['TRM_M_Unit']):"s";
$trm_s_unit = (isset($_REQUEST['TRM_S_Unit']))?strval($_REQUEST['TRM_S_Unit']):"s";
// Jueces y observaciones
$juez1 = (isset($_REQUEST['Juez1']))?strval($_REQUEST['Juez1']):null;
$juez2 = (isset($_REQUEST['Juez2']))?strval($_REQUEST['Juez2']):null;
$observaciones = (isset($_REQUEST['Observaciones']))?strval($_REQUEST['Observaciones']):null;
// cerrada
$cerrada = (isset($_REQUEST['Cerrada']))?intval($_REQUEST['Cerrada']):0;

// ejecutamos el query
$msg="";
do_log("update_Manga:: retrieved data from client");
// invocamos la orden SQL y devolvemos el resultado
$res=$stmt->execute();
do_log("update_Manga:: actualizadas $stmt->affected_rows filas");
if (!$res) {
	$msg="update_Manga:: Error: ".$conn->error;
	do_log($msg);
}
do_log("update_Manga::execute() resulted: $res");
$stmt->close();
$conn->close();
return $msg;
?>
