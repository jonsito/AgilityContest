<?php
/*
procesaInscripcion.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


/** mandatory requires for database and logging */
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../auth/Config.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/classes/DBConnection.php");
require_once(__DIR__."/classes/Jornadas.php");
require_once(__DIR__."/classes/Mangas.php");
require_once(__DIR__."/classes/OrdenSalida.php");
require_once(__DIR__."/classes/Resultados.php");
require_once(__DIR__."/classes/Dogs.php");
require_once(__DIR__."/classes/Equipos.php");

/* 
 * Cada vez que se anyade/borra/edita una inscripcion, se ejecuta este script, que ajusta los datos
 * de mangas, orden de salida y tablas de resultados
 */

/**
 * elimina las referencias de una inscripcion en la jornada dada
 * @param {object} $inscripcion Datos de la inscripcion
 * @param {object} $jornada Datos de la jornada
 * @throws Exception on invalid jornada ID
 */
function borraPerroDeJornada($inscripcion,$jornada) {
	$j=$jornada['ID'];
	$p=$jornada['Prueba'];
	// buscamos la lista de mangas de esta jornada
	$mobj=new Mangas("borraPerroDeJornada",$jornada['ID']);
	$mangas=$mobj->selectByJornada();
	if (!$mangas) throw new Exception("No hay mangas definidas para la jornada $j de la prueba $p");
	$perro=$inscripcion['Perro'];
	foreach($mangas['rows'] as $manga) {
		// eliminamos el perro del orden de salida de todas las mangas de esta jornada
		$os=OrdenSalida::getInstance("borraPerroDeJornada",$manga['ID']);
		$os->removeFromList($perro);
		// eliminamos el perro de la tabla de resultados de todas las mangas de esta jornada
		$rs=Resultados::getInstance("borraPerroDeJornada",$manga['ID']);
		$rs->delete($perro);
	}
}

/**
 * Comprueba y actualiza las referencias de una inscripcion en una jornada
 * @param {object} $inscripcion Datos de la inscripcion
 * @param {object} $jornada Datos de la jornada
 * @param {object} $perro Datos del perro
 */
function inscribePerroEnJornada($inscripcion,$jornada,$perro) {
	$myConfig=Config::getInstance();
	$myLogger=new Logger("inscribePerroEnJornada",$myConfig->getEnv("debug_level"));
	$j=$jornada['ID'];
	$p=$jornada['Prueba'];
	$idperro=$inscripcion['Perro'];
	$g=$perro['Grado'];

	// buscamos la lista de mangas de esta jornada
	$mobj=new Mangas("inscribePerroEnJornada",$jornada['ID']);
	$mangas=$mobj->selectByJornada();
	if (!$mangas) throw new Exception("No hay mangas definidas para la jornada $j de la prueba $p");
	foreach($mangas['rows'] as $manga) {
		$mid=$manga['ID'];
		$mtype=$manga['Tipo'];
		$mgrado=$manga['Grado'];
		$inscribir=false;
		// comprobamos si el perro tiene que estar en esta manga
		switch ($mtype) {
			case 1: //  'Pre Agility Manga 1', 'P.A.'
				if ($g==='P.A.') $inscribir=true; break;
			case 2: // 'Pre Agility Manga 2', 'P.A.'
				if ($g==='P.A.') $inscribir=true; break;
			case 3: // 'Agility Grado I Manga 1', 'GI'
		 	case 4: // 'Agility Grado I Manga 2', 'GI'
				if ($g==='GI') $inscribir=true; break;
			case 5: // 'Agility Grado II', 'GII'
				if ($g==='GII') $inscribir=true; break;
			case 6: // 'Agility Grado III', 'GIII'
				if ($g==='GIII') $inscribir=true; break;
			case 7: // 'Agility Abierta', '-'
			case 8: // 'Agility Equipos (3 mejores)', '-'
			case 9: // 'Agility Equipos (Conjunta)', '-'
				$inscribir=true; break;
			case 10:// 'Jumping Grado II', 'GII'
				if ($g==='GII') $inscribir=true; break;
			case 11:// 'Jumping Grado III', 'GIII'
				if ($g==='GIII') $inscribir=true; break;
			case 12:// 'Jumping Abierta', '-'
			case 13:// 'Jumping Equipos (3 mejores)', '-'
			case 14:// 'Jumping Equipos (Conjunta)', '-'
			case 15:// 'Ronda K.O.', '-'
			case 16:// 'Manga especial', '-'
				$inscribir=true; break;
            case 17: // 'Agility Grado I Manga 3', 'GI'
                if ($g==='GI') $inscribir=true; break;
			default: 
				throw new Exception("Tipo de manga $mtype desconocido. Manga:$mid Jornada:$j Prueba:$p");
				break;
		}
		
		// Verificamos el orden de salida de la manga	
		$os=OrdenSalida::getInstance("inscribePerroEnJornada",$manga['ID']);
		$orden=$os->getOrden();
		$myLogger->info("OrdenDeSalida Prueba:$p Jornada:$j Manga:$mid Tipo:$mtype Grado:$mgrado es:\n$orden");
		if ($inscribir==false) {
			$myLogger->info("Eliminando Perro:$idperro Grado:$g del orden de salida grado:$mgrado");
			$os->removeFromList($idperro);
		} else {
			$myLogger->info("Insertando Perro:$idperro Grado:$g en del orden de salida gradp:$mgrado");
			$os->insertIntoList($idperro);
		
		}
		$orden=$os->getOrden();
		$myLogger->info("Nuevo OrdenDeSalidada: \n$orden");
		
		// verificamos la tabla de resultados de esta manga
		$rs=Resultados::getInstance("inscribePerroEnJornada::Resultados",$mid);
		if ($inscribir==false) {
			$myLogger->info("Borrando Perro:$idperro Grado:$g de Resultados manga:$mid");
			// borramos entrada del perro en la tabla de resultados de la manga
			$rs->delete($idperro);
		} else {
            $eqobj =new Equipos("inscribePerroEnJornada",$p,$j);
			// nos aseguramos de que existe una entrada 
			$myLogger->info("Insertando Perro:$idperro Grado:$g en Resultados manga:$mid");
			// en la tabla de resultados de esta manga para este perro
			$res = $rs->insertByData($perro, $inscripcion,$eqobj->getTeamByPerro($idperro));
			if ($res!=="") {
				// esta funcion es in "insert on duplicate key update"...
				// no deberia fallar si ya existe una entrada en la tabla de resultados
				$myLogger->error("Failed: Insert into Resultados perro:$idperro Prueba:$p Jornada:$j Manga:$mid");
				$myLogger->error($res);
			}
		}
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
	$myConfig=Config::getInstance();
	$myLogger=new Logger("procesaInscripcion",$myConfig->getEnv("debug_level"));
	$am= new AuthManager("procesaInscripcion");
	// si la prueba o la inscripcion son nulas genera error
	try {
		if ($p<=0) throw new Exception("ID de prueba invalida: $p");
		if ($i<=0) throw new Exception("ID de inscripcion invalida");
		$am->access(PERMS_OPERATOR); // grant access or throw exception
		
		// buscamos las jornadas de que consta la Prueba
		$jobject= new Jornadas("procesaInscripcion",$p);
		$jp = $jobject->searchByPrueba();
		if (!$jp) throw new Exception("No encuentro jornadas para la prueba: $p");
		
		// buscamos las jornadas en las que esta inscrito
		$iobject= new Inscripciones("procesaInscripcion",$p);
		$inscripcion=$iobject->selectByID($i);
		if(!$inscripcion) throw new Exception("No encuentro la inscripcion con ID: $i");
		$idp=$inscripcion['Perro'];

		// obtenemos los datos del perro
		$pobj=new Dogs("procesaInscripcion()"/* no need to include federation info */);
		$perro=$pobj->selectByID($idp);
		if (!$perro) throw new Exception("No hay datos para el perro a inscribir con id: $idp");
		// TODO: check Dog Federation against Prueba Federation
		// contrastamos la lista de jornadas de la prueba con la lista de jornadas en las que esta inscrito
		foreach($jp['rows'] as $jornada) {
			$numj=$jornada['Numero']-1; // obtenemos el numero de jornada
			$idj=$jornada['ID'];
			if ($jornada['Cerrada']==1) {
				$myLogger->info("La jornada $idj de la prueba $p esta cerrada");
				continue; // no tocamos las jornadas cerradas
			}
			if ( ($inscripcion['Jornadas'] & (1<<$numj)) != 0) {
				$myLogger->info("El perro $idp esta inscrito en la jornada $idj de la prueba $p");
				inscribePerroEnJornada($inscripcion,$jornada,$perro);
			} else {
				$myLogger->info("El perro $idp NO esta inscrito en la jornada $idj de la prueba $p");
				borraPerroDeJornada($inscripcion,$jornada,$perro);
			}
		}
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}
}

?>