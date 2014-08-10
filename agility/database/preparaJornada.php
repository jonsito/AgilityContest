<?php
/** mandatory requires for database and logging */
require_once("tools.php");
require_once("logging.php");
require_once("classes/DBConnection.php");
require_once("classes/Jornadas.php");
require_once("classes/Mangas.php");


/* Cada vez que se pullsa en competicion y se selecciona prueba y jornada, se invoca este script,
 * que prepara revisa y corrige los datos que hagan falta
 */

/* fase 0: preparacion */

/* - Obtenemos variables */
try {
	$p = http_request("Prueba","i",0);
	$j = http_request("Jornada","i",0);
	if (($p<=0) || ($j<=0) ) 
		throw new Exception("Call to prepareJornada with Invalid Prueba:$p Jornada:$j ID");	
 /* - Si la jornada ya esta cerrada no hacemos nada */
	$jornada=new Jornada("preparaJornada::testforclosed",$p);
	$jdata=$jornada->selectByID($j);
	if ($jdata['Cerrada']==1)
		throw new Exception("Call to prepareJornada on a closed Jornada $j in prueba $p ");
	
/* fase 1: Ajuste de las mangas de que consta la jornada */

/* vemos las mangas que son necesarias */
/* vemos las mangas que ya existen */
/* si existe y no es necesaria, se avisa y se borra */
/* si no existe y no es necesaria no se hace nada */
/* si existe y es necesaria no se hace nada */
/* si no existe y es necesaria, se crea */
	$mangas =new Mangas("preparaJornada",$j);
	$mangas->prepareMangas($jdata['ID'],
			$jdata['Grado1'],	$jdata['Grado2'],	$jdata['Grado3'],
			$jdata['Open'],		$jdata['Equipos3'],	$jdata['Equipos4'],
			$jdata['PreAgility'],$jdata['KO'],	$jdata['Exhibicion'],
			$jdata['Otras']);
				

/* fase 2: comprobamos la tabla de inscritos/resultados por cada manga */

/* foreach manga
 * obtener lista de los que deberian estar inscritos
 * obtener la lista de los que estan inscritos
 * Si no esta y no deberia estar no se hace nada
 * Si no esta y deberia estar se crea una entrada
 * Si esta y no deberia estar se avisa y se borra
 * Si esta y deberia estar no se hace nada
 */

} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}
?>