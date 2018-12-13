<?php
/*
PrintListaPerros.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

/**
 * genera un pdf lista de perros seleccionada desde el menu de la base de datos en el orden especificado en la pantalla
*/

require_once(__DIR__."/../fpdf.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__."/../print_common.php");

class PrintHallOfFame extends PrintCommon {

    /**
	 * Constructor
	 * @throws Exception
	 */
	function __construct($prueba) {
		date_default_timezone_set('Europe/Madrid');
		parent::__construct('Landscape',"print_liga",$prueba,0);

		// obtenemos la lista de jornadas de la prueba
        $j=new Jornadas("HallOfFame",$prueba);
        $res=$j->selectByPrueba();
        if (!is_array($res)){
            $this->errormsg="HallOfFame: getJornadasByPrueba($prueba) failed";
            throw new Exception($this->errormsg);
        }
        $jornadas=$res['rows'];

        // por cada jornada buscamos las clasificaciones finales
        $inscobj=new Inscripciones("HallOfFame",$prueba);
        $datos=array();
        foreach ($jornadas as $jornada) {
            if ($jornada['Nombre']==='-- Sin asignar --') continue; // skip empty journeys
            $this->myLogger->trace("Collecting results from prueba:{$prueba} jornada:{$jornada['Nombre']}");
            // cogemos las inscripciones de la jornada
            // y las reindexamos por DogID
            $lista=$inscobj->inscritosByJornada($jornada['ID'],false,false)['rows'];
            $inscritos=array();
            foreach($lista as $perro) { $inscritos[$perro['Perro']]=$perro; }

            // cogemos todas las rondas de esta jornada
            $rondas=Jornadas::enumerateRondasByJornada($jornada['ID'])['rows'];

            // por cada ronda cogemos todas las clasificaciones de la jornada
            $clas=Competitions::getClasificacionesInstance("HallOfFame",$jornada['ID']);
            $results=array();
            foreach ($rondas as $ronda) {
                $mangas=array($ronda['Manga1'],$ronda['Manga2'],$ronda['Manga3'],$ronda['Manga4'],$ronda['Manga5'],$ronda['Manga6'],$ronda['Manga7'],$ronda['Manga8']);
                $clasifRonda=$clas->clasificacionFinal($ronda['Rondas'],$mangas,$ronda['Mode']);
                $results=array_merge($results,$clasifRonda['rows']);
            }
            // y vamos componiendo la tabla de perroguiaclub,categoria,grado,tiempo,penalizacion
            foreach($results as $perro) {
                // si el perro no esta en la lista de inscritos, marca error e ignora entrada
                $id=$perro['Perro'];
                if (!array_key_exists($id,$inscritos)) {
                    $this->myLogger->error("Encontrada Clasificacion para perro no inscrito:{$id}");
                    continue;
                }
                $pdata=$inscritos[$id];
                // ignoramos perros de pre-agility
                if ($perro['Grado']=="P.A.") {
                    $this->myLogger->info("Skipping Pre-Agility Dog:{$pdata['Nombre']}-{$id}");
                    continue;
                }
                if (!array_key_exists($id,$datos)) { // creamos datos del perro
                    $datos[$id]= array(
                        'Perro'     => $perro['Perro'],
                        'Dorsal'    => $perro['Dorsal'],
                        'Nombre'    => $pdata['Nombre'],
                        'NombreGuia'=> $pdata['NombreGuia'],
                        'NombreClub'=> $pdata['NombreClub'],
                        'Categoria' => $pdata['Categoria'],
                        'Grado'     => $perro['Grado'], // from results, as may change during contest
                        'Count'     =>    0,
                        'Tiempo'    =>    0,
                        'Penal'     =>    0
                    );
                }
                // anyadimos count,cat,grad,tiempo y penalizacion de cada manga
                for($n=1;$n<9;$n++) {
                    if (!array_key_exists("F{$n}",$perro)) continue; // no manga $n defined
                    $datos[$id]['Grado']=$perro['Grado']; // to track grade changes
                    $datos[$id]['Count']++;
                    $datos[$id]['Tiempo']+=$perro["T{$n}"]; // manga $n: tiempo
                    $datos[$id]['Penal']+=$perro["P{$n}"]; // manga $n: tiempo
                } // foreach manga
            } // foreach result
        } // foreach jornada
        // finalmente ordenamos por cuenta(num de mangas hechas), penalizacion y tiempo
        usort($datos,function($a,$b){
            if ($a['Count']==$b['Count']) {
                if ($a['Penal']==$b['Penal']) {
                    return sign($a['Tiempo']-$b['Tiempo']);
                } else {
                    return sign($a['Penal']-$b['Penal']);
                }
            } else return sign($b['Count']-$a['Count']);// more count goes first
        });
        // cogemos el resultado y lo hacemos accesible al resto del universo
        $this->datos=$datos;
	}
	
	// Cabecera de página
	function Header() {
		// cabecera comun
		$this->print_commonHeader(_("Hall Of Fame"));
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}
	
	function writeTableHeader($rowcount) {
		$this->myLogger->enter();
		$this->Ln();
		$this->myLogger->leave();
	}

    /**
     * print table data
     * @param $perro dog id
     * @return string
     */
	function composeTable() {
		$this->myLogger->enter();
		$this->myLogger->leave();
        return "";
	}
}
?>