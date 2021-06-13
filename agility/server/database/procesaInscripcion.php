<?php
/*
procesaInscripcion.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__."/classes/Jornadas.php");
require_once(__DIR__."/classes/Mangas.php");
require_once(__DIR__."/classes/OrdenSalida.php");
require_once(__DIR__."/classes/Resultados.php");
require_once(__DIR__."/classes/Dogs.php");
require_once(__DIR__."/classes/Equipos.php");
require_once(__DIR__."/classes/Inscripciones.php");

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
		$os=Competitions::getOrdenSalidaInstance("borraPerroDeJornada",$manga['ID']);
		$os->removeFromList($perro);
		// eliminamos el perro de la tabla de resultados de todas las mangas de esta jornada
		$rs=Competitions::getResultadosInstance("borraPerroDeJornada",$manga['ID']);
		$rs->delete($perro);
	}
	// eliminamos al perro del orden de equipos
    $teamlist=$mobj->__select( // buscamos el equipo en el que esta inscrito
        "ID,Miembros",
        "equipos",
        "( Jornada = {$j} ) AND ( Miembros LIKE '%,{$perro},%' )"
    );
    // realmente solo debería devolver un resultado (o ninguno si esta en el equipo por defecto)
    foreach ($teamlist['rows'] as $team) {
        // lo borramos de la lista de miembros
        $nuevalista=list_remove($perro,$team['Miembros']);
        // actualizamos el equipo
        $str="UPDATE equipos SET Miembros='{$nuevalista}' WHERE ID={$team['ID']}";
        $mobj->query($str);
    }
    return "";
}

function canInscribeIn($listamangas,$gradmanga,$gradperro,$catguia) {
    switch ($gradmanga) {
        case 'P.A.':
            // en pre-agility, con independencia del guia se puede inscribir si el perro es de pre-agility
            return ($gradperro === 'P.A.');
        case 'GI':
            // si el perro no es de grado 1 no se puede inscribir
            if ($gradperro !== 'GI') return false;
            // si el guia es adulto se puede inscribir
            if ($catguia === 'A') return true;
            // si el guia es infantil, junior, senior o para-agility y NO hay dichas mangas, se puede inscribir
            if (($catguia === 'I') && empty(array_intersect([36, 37], $listamangas))) return true; // mangas infantil
            if (($catguia === 'J') && empty(array_intersect([32, 33], $listamangas))) return true; // mangas junior
            if (($catguia === 'S') && empty(array_intersect([34, 35], $listamangas))) return true; // mangas senior
            if (($catguia === 'P') && empty(array_intersect([38, 39], $listamangas))) return true; // mangas paraagility
            // arriving here means do not inscribe in this round
            return false;
        case 'GII':
            // si el perro no es de grado 2 no se puede inscribir
            if ($gradperro !== 'GII') return false;
            // si el guia es adulto se puede inscribir
            if ($catguia === 'A') return true;
            // si el guia es infantil, junior, senior o para-agility y NO hay dichas mangas, se puede inscribir
            if (($catguia === 'I') && empty(array_intersect([36, 37], $listamangas))) return true; // mangas infantil
            if (($catguia === 'J') && empty(array_intersect([32, 33], $listamangas))) return true; // mangas junior
            if (($catguia === 'S') && empty(array_intersect([34, 35], $listamangas))) return true; // mangas senior
            if (($catguia === 'P') && empty(array_intersect([38, 39], $listamangas))) return true; // mangas paraagility
            // arriving here means do not inscribe in this round
            return false;
        case 'GIII':
            // si el perro no es de grado 3 no se puede inscribir
            if ($gradperro !== 'GIII') return false;
            // si el guia es adulto se puede inscribir
            if ($catguia === 'A') return true;
            // si el guia es infantil, junior, senior o para-agility y NO HAY dichas mangas, se puede inscribir
            if (($catguia === 'I') && empty(array_intersect([36, 37], $listamangas))) return true; // mangas infantil
            if (($catguia === 'J') && empty(array_intersect([32, 33], $listamangas))) return true; // mangas junior
            if (($catguia === 'S') && empty(array_intersect([34, 35], $listamangas))) return true; // mangas senior
            if (($catguia === 'P') && empty(array_intersect([38, 39], $listamangas))) return true; // mangas paraagility
            // arriving here means do not inscribe in this round
            return false;
        case 'Ch':
            // si el guia no es infantil no se le puede inscribir
            return ($catguia === 'I');
        case 'Jr':
            // si el guia es junior se le inscribe
            if ($catguia === 'J') return true;
            // si el guia es infantil pero NO HAY manga de infantil se le inscribe
            if (($catguia === 'I') && empty(array_intersect([36, 37], $listamangas))) return true; // mangas infantil
        // else do not inscribe
        case 'Sr':
            // solo se inscribe si el guia es senior
            return ($catguia === 'S');
        case 'Par':
            // solo se inscribe si el guia es para-agility
            return ($catguia === 'P');
        default: // default is not inscribe
            return false;
    }
}

/**
 * Comprueba y actualiza las referencias de una inscripcion en una jornada
 * @param {object} $inscripcion Datos de la inscripcion
 * @param {object} $jornada Datos de la jornada
 * @param {array} $perro Datos del perro (perroguiaclub)
 * @throws Exception
 */
function inscribePerroEnJornada($inscripcion,$jornada,$perro) {
	$myConfig=Config::getInstance();
	$myLogger=new Logger("inscribePerroEnJornada",$myConfig->getEnv("debug_level"));
	$j=$jornada['ID'];
	$p=$jornada['Prueba'];
	$idperro=$inscripcion['Perro'];
	$g=$perro['Grado'];
	$c=$perro['CatGuia']; // I nfantil, J uvenil, A dulto, S enior, R etirado, P araAgility

	// buscamos la lista de mangas de esta jornada
	$mobj=new Mangas("inscribePerroEnJornada",$jornada['ID']);
	$mangas=$mobj->selectByJornada();
	if (!$mangas) throw new Exception("No hay mangas definidas para la jornada $j de la prueba $p");
	// extraemos la lista de tipos de manga que hay en la jornada
    $listamangas=array_column($mangas,'Tipo');
	foreach($mangas['rows'] as $manga) {
		$mid=$manga['ID'];
		$mtype=$manga['Tipo'];
		$mgrado=$manga['Grado'];
		$inscribir=false;
		// comprobamos si el perro tiene que estar en esta manga
		switch ($mtype) {
			case 1: // 'Pre Agility Manga 1', 'P.A.'
			case 2: // 'Pre Agility Manga 2', 'P.A.'
                $inscribir=canInscribeIn($listamangas,'P.A.',$g,$c); break;
			case 3: // 'Agility Grado I Manga 1', 'GI'
		 	case 4: // 'Agility Grado I Manga 2', 'GI'
            case 17: // 'Agility Grado I Manga 3', 'GI'
                $inscribir=canInscribeIn($listamangas,'GI',$g,$c); break;
			case 5: // 'Agility Grado II', 'GII'
            case 10:// 'Jumping Grado II', 'GII'
                $inscribir=canInscribeIn($listamangas,'GII.',$g,$c); break;
			case 6: // 'Agility Grado III', 'GIII'
            case 11:// 'Jumping Grado III', 'GIII'
                $inscribir=canInscribeIn($listamangas,'GIII',$g,$c); break;
			case 7: // 'Agility Abierta', '-'
			case 8: // 'Agility Equipos (3 mejores)', '-'
			case 9: // 'Agility Equipos (Conjunta)', '-'
			case 12:// 'Jumping Abierta', '-'
			case 13:// 'Jumping Equipos (3 mejores)', '-'
			case 14:// 'Jumping Equipos (Conjunta)', '-'
                $inscribir=true; // default
                // en teoria no se deberian inscribir perros de grado 1, pero como las pruebas de equipos
                // no suelen ser homologadas, dejamos la verificacion al organizador
                if($g==='GI') $myLogger->warn("Inscription of grade 1 dog: {$perro['Nombre']} into team journey");
                // los perros de pre-agility no se pueden inscribir en ningun caso
                if($g==='P.A.') $inscribir=false;
                break;
			case 16:// 'Manga especial', '-'
				$inscribir=true;
                // los perros de pre-agility no se pueden inscribir en ningun caso
                if($g==='P.A.') $inscribir=false;
				break;
            case 15:// 'Ronda K.O. 1', '-'
            case 18:// 'Ronda K.O. 2', '-'
            case 19:// 'Ronda K.O. 3', '-'
            case 20:// 'Ronda K.O. 4', '-'
            case 21:// 'Ronda K.O. 5', '-'
            case 22:// 'Ronda K.O. 6', '-'
            case 23:// 'Ronda K.O. 7', '-'
            case 24:// 'Ronda K.O. 8', '-'
                $inscribir=true;
                // los perros de pre-agility no se pueden inscribir en ningun caso
                if($g==='P.A.') $inscribir=false;
                break;
            case 25:// WAO Agility A
            case 26:// WAO Agility B
            case 27:// WAO Jumping A
            case 28:// WAO Jumping B
            case 29:// Snooker
            case 30:// Gambler
            case 31:// SpeedSTakes
                // los perros de pre-agility no se pueden inscribir en ningun caso
                if($g==='P.A.') $inscribir=false;
                $inscribir=true;
                break;
            case 32: // Junior Agility
            case 33: // Junior Jumping
                $inscribir=canInscribeIn($listamangas,'Jr',$g,$c);
                break; // junior
            case 34: // Senior Agility
            case 35: // Senior Jumping
                $inscribir=canInscribeIn($listamangas,'Sr',$g,$c);
                break;
            case 36: // Infantil Agility
            case 37: // Infantil Jumping
                $inscribir=canInscribeIn($listamangas,'Ch',$g,$c);
                break;
            case 38: // ParaAgility Agility
            case 39: // ParaAgility Jumping
                $inscribir=canInscribeIn($listamangas,'P.A.',$g,$c);
                break;
			default: 
				throw new Exception("Tipo de manga $mtype desconocido. Manga:$mid Jornada:$j Prueba:$p");
				break;
		}
		
		// Verificamos el orden de salida de la manga	
		$os=Competitions::getOrdenSalidaInstance("inscribePerroEnJornada",$manga['ID']);
		if ($inscribir==false) {
			$myLogger->info("Borrando Perro:$idperro Grado:$g catGuia:$c del orden de salida de la manga $mid grado:$mgrado");
			$os->removeFromList($idperro);
		} else {
			$myLogger->info("Insertando Perro:$idperro Grado:$g catGuia$c en el orden de salida de la manga $mid grado:$mgrado");
			$os->insertIntoList($idperro);
		}
		
		// verificamos la tabla de resultados de esta manga
		$rs=Competitions::getResultadosInstance("inscribePerroEnJornada::Resultados",$mid);
		if ($inscribir==false) {
			$myLogger->info("Borrando Perro:$idperro Grado:$g catGuia:$c de Resultados manga:$mid grado:$mgrado");
			// borramos entrada del perro en la tabla de resultados de la manga
			$rs->delete($idperro);
		} else {
            $eqobj =new Equipos("inscribePerroEnJornada",$p,$j);
			// nos aseguramos de que existe una entrada 
			$myLogger->info("Insertando Perro:$idperro Grado:$g catGuia:$c en Resultados manga:$mid grado:$mgrado");
			// en la tabla de resultados de esta manga para este perro
			$res = $rs->insertByData($perro, $inscripcion,$eqobj->getTeamByPerro($idperro));
			if ($res!=="") {
				// esta funcion es del tipo "insert on duplicate key update"...
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
	// si la prueba o la inscripcion son nulas genera error
	try {
		if ($p<=0) throw new Exception("ID de prueba invalida: $p");
		if ($i<=0) throw new Exception("ID de inscripcion invalida");
        $am= AuthManager::getInstance("procesaInscripcion"); // may throw exception
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
				borraPerroDeJornada($inscripcion,$jornada);
			}
		}
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}
}

?>