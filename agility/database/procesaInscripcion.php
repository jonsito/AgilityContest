<?php
/** mandatory requires for database and logging */
require_once("tools.php");
require_once("logging.php");
require_once("classes/DBConnection.php");
require_once("classes/Jornadas.php");
require_once("classes/Mangas.php");
require_once("classes/OrdenSalida.php");
require_once("classes/Resultados.php");

/* 
 * Cada vez que se anyade/borra/edita una inscripcion, se ejecuta este script, que ajusta los datos
 * de mangas, orden de salida y tablas de resultados
 */

/**
 * elimina las referencias de una inscripcion en la jornada dada
 * @param {object} $inscripcion Datos de la inscripcion
 * @param {object} $jornada Datos de la jornada
 */
function borraPerroDeJornada($inscripcion,$jornada) {
	$j=$jornada['ID'];
	$p=$jornada['Prueba'];
	// buscamos la lista de mangas de esta jornada
	$mobj=new Mangas("borraPerroDeJornada",$jornada['ID']);
	$mangas=$mobj->selectByJornada();
	if (!$mangas) throw new Exception("No hay mangas definidas para la jornada $j de la prueba $p");
	foreach($mangas['rows'] as $manga) {
		// eliminamos el perro del orden de salida de todas las mangas de esta jornada
		$os=new OrdenSalida("borraPerroDeJornada");
		$orden=$os->getOrden($manga['ID']);
		$os->removeFromList($orden, $inscripcion['Perro']);
		$os->setOrden($manga['ID'], $orden);
		// eliminamos el perro de la tabla de resultados de todas las mangas de esta jornada
		$rs=new Resultados("borraPerroDeJornada",$manga['ID']);
		$rs->delete($inscripcion['Perro']);
	}
}

/**
 * Comprueba y actualiza las referencias de una inscripcion en una jornada
 * @param {object} $inscripcion Datos de la inscripcion
 * @param {object} $jornada Datos de la jornada
 */
function inscribePerroEnJornada($inscripcion,$jornada) {
	$j=$jornada['ID'];
	$p=$jornada['Prueba'];
	// buscamos la lista de mangas de esta jornada
	$mobj=new Mangas("inscribePerroEnJornada",$jornada['ID']);
	$mangas=$mobj->selectByJornada();
	if (!$mangas) throw new Exception("No hay mangas definidas para la jornada $j de la prueba $p");
	foreach($mangas['rows'] as $manga) {
		// comprobamos si el perro tiene que estar en esta manga
		// TODO: write
		// si no tiene que estar, no hacemos nada
		// TODO: write
		// nos aseguramos de que el perro esta en el orden de salida
		$os=new OrdenSalida("borraPerroDeJornada");
		$orden=$os->getOrden($manga['ID']);
		$os->insertIntoList($orden, $inscripcion['Perro'], $inscripcion['Categoria'], $inscripcion['Celo']);
		$os->setOrden($manga['ID'], $orden);
		// nos aseguramos de que existe una entrada en la tabla de resultados de esta manga para este perro
		$rs=new Resultados("borraPerroDeJornada",$manga['ID']);
		// TODO: write
	}
}

/**
 * Funcion de procesado de inscripciones
 * revisa la inscripcion
 * ajusta orden de salida
 * crea entradas en tabla de resultados
 * @param {integer} $p ID de prueba
 * @param {integer} $i ID de inscripcion
 */
function procesaInscripcion($p,$i) {
	$myLogger=new Logger();
	// si la prueba o la inscripcion son nulas genera error
	try {
		if ($p<=0) throw new Exception("ID de prueba invalida: $p");
		if ($i<=0) throw new Exception("ID de inscripcion invalida");
		// buscamos las jornadas de que consta la Prueba
		$jobject= new Jornadas("procesaInscripcion",$p);
		$jp = $jobject->searchByPrueba();
		if (!$jp) throw new Exception("No encuentro jornadas para la prueba: $p");
		// buscamos las jornadas en las que esta inscrito
		$iobject= new Inscripciones("procesaInscripcion",$p);
		$inscripcion=$iobject->selectByID($i);
		if(!inscripcion) throw new Exception("No encuentro la inscripcion con ID: $i");
		$id=$inscripcion['Perro'];
		// contrastamos la lista de jornadas de la prueba con la lista de jornadas en las que esta inscrito
		foreach($jp['rows'] as $jornada) {
			$numj=$jornada['Numero']-1; // obtenemos el numero de jornada
			if ($jornada['Cerrada']==1) {
				$myLogger->info("La jornada $idj de la prueba $p esta cerrada");
				continue; // no tocamos las jornadas cerradas
			}
			$idj=$jornada['ID'];
			if ( ($inscripcion['Jornadas'] & (1<<$n)) != 0) {
				$myLogger->info("El perro $idp esta inscrito en la jornada $idj de la prueba $p");
				inscribePerroEnJornada($inscripcion,$jornada);
			} else {
				$myLogger->info("El perro $idp NO esta inscrito en la jornada $idj de la prueba $p");
				borraPerroDeJornada($inscripcion,$jornada);
			}
		}
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}
}

?>