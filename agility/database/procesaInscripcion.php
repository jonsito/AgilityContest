<?php
/** mandatory requires for database and logging */
require_once("tools.php");
require_once("logging.php");
require_once("classes/DBConnection.php");
require_once("classes/Jornadas.php");
require_once("classes/Mangas.php");
require_once("classes/OrdenSalida.php");
require_once("classes/Resultados.php");
require_once("classes/Dogs.php");

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
	$myLogger=new Logger();
	$j=$jornada['ID'];
	$p=$jornada['Prueba'];
	$idperro=$inscripcion['Perro'];
	// obtenemos los datos del perro
	$pobj=new Dogs("inscribePerroEnJornada");
	$perro=$pobj->selectByIDPerro($idperro);
	if (!$perro) throw new Exception("No hay datos para el perro a inscribir con id: $idperro");
	$g=$perro['Grado'];
	// buscamos la lista de mangas de esta jornada
	$mobj=new Mangas("inscribePerroEnJornada",$jornada['ID']);
	$mangas=$mobj->selectByJornada();
	if (!$mangas) throw new Exception("No hay mangas definidas para la jornada $j de la prueba $p");
	foreach($mangas['rows'] as $manga) {
		$mid=$manga['ID'];
		$mtype=$manga['Tipo'];
		$inscribir=false;
		// comprobamos si el perro tiene que estar en esta manga
		switch ($mtype) {
			case 1: //  'Manga sin tipo definido', '-'
				$inscribir=true; break;
			case 2: // 'Ronda de Pre-Agility', 'P.A.'
				if ($g==='P.A') $inscribir=true; break;
			case 3: // 'Agility Grado I Manga 1', 'GI'
		 	case 4: // 'Agility Grado I Manga 2', 'GI'
				if ($g==='GI') $inscribir=true; break;
			case 5: // 'Agility Grado II', 'GII'
				if ($g==='GII') $inscribir=true; break;
			case 6: // 'Agility Grado III', 'GIII'
				if ($g==='GIII') $inscribir=true; break;
			case 7: // 'Agility Abierta (Open)', '-'
			case 8: // 'Agility Equipos (3 mejores)', '-'
			case 9: // 'Agility Equipos (Conjunta)', '-'
				$inscribir=true; break;
			case 10:// 'Jumping Grado II', 'GII'
				if ($g==='GII') $inscribir=true; break;
			case 11:// 'Jumping Grado III', 'GIII'
				if ($g==='GIII') $inscribir=true; break;
			case 12:// 'Jumping Abierta (Open)', '-'
			case 13:// 'Jumping por Equipos (3 mejores)', '-'
			case 14:// 'Jumping por Equipos (Conjunta)', '-'
			case 15:// 'Ronda K.O.', '-'
			case 16:// 'Ronda de Exhibición', '-'
				$inscribir=true; break;
			default: 
				throw new Exception("Tipo de manga $mtype desconocido. Manga:$mid Jornada:$j Prueba:$p");
				break;
		}
		
		// si no tiene que estar, no hacemos nada
		if ($inscribir==false) {
			$myLogger->info("Ignorando inscripcion Perro:$idperro Prueba:$p Jornada:$j Manga:$mid Tipo:$mtype");
			continue;
		}
		$myLogger->info("Generando registros para Perro:$idperro Prueba:$p Jornada:$j Manga:$mid Tipo:$mtype");
		// nos aseguramos de que el perro esta en el orden de salida
		$os=new OrdenSalida("inscribePerroEnJornada");
		$orden=$os->getOrden($manga['ID']);
		$os->insertIntoList($orden, $idperro, $perro['Categoria'], $inscripcion['Celo']);
		$os->setOrden($manga['ID'], $orden);
		// nos aseguramos de que existe una entrada en la tabla de resultados de esta manga para este perro
		$rs=new Resultados("inscribePerroEnJornada",$manga['ID']);
		// TODO: write
	} /* foreach */
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