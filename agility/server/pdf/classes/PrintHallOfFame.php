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

    protected $datos=null;

    static $empty=array(
        'Perro'     => 0 , 'Count'     => 0,
        'Dorsal'    => "", 'Nombre'    => "",'NombreGuia'=> "",'NombreClub'=> "",
        'Categoria' => "", 'Grado'     => "",
        'Tiempo'    => 0 , 'Penal'     => 0
    );

    /**
	 * Constructor
	 * @throws Exception
	 */
	function __construct($prueba) {
		date_default_timezone_set('Europe/Madrid');
		parent::__construct('Landscape',"print_liga",$prueba,0);
        $this->icon2=getIconPath($this->federation->get('Name'),"null.png"); // no fed logo

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
            if ($jornada['SlaveOf']!=0) continue; // skip subordinate journeys
            if ($jornada['Equipos4']!=0) continue; // skip team4 journeys as time data cannot be evaluated
            if ($jornada['KO']!=0) continue; // skip KO journeys cause cannot compute regularity
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

    /**
     * Devuelve un array con los tres mejores perros ( o en blanco si no los hay )
     *
     * @param {array} $cats lista de categorias a buscar
     * @param {array} $grads lista de grados a buscar
     */
	private function tresMejores($cats,$grads) {
	    $mejores=array();
	    foreach ($this->datos as $item) {
            if (! in_array($item['Categoria'],$cats)) continue;
            if (! in_array($item['Grado'],$grads)) continue;
            array_push($mejores,$item);
            if (count($mejores)>=3) return $mejores;
        }
	    // fill remaining data with null entries to complete 3 items
        for ($n=count($mejores);$n<3;$n++) {
            array_push($mejores,PrintHallOfFame::$empty);
        }
        return $mejores;
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

	function writeBlock($x,$y,$grads,$cats) {
        // get text of header
        $catstr=(count($cats)==1)?$this->federation->getCategory($cats[0]):"";
        $gradstr=(count($grads)==1)?$this->federation->getGrade($grads[0]):"";
        $hdrstr="{$catstr} {$gradstr}";
        if ($hdrstr==" ") $hdrstr=_('Simply The Bests');
        // get 3 best of requested cat/grad
        $items= $this->tresMejores($cats,$grads);

        $heights=intval($this->federation->get('Heights'));

        // REMINDER: $this->cell( width, height, data, borders, newline, align, fill)
        // paint box
        if ( ($x==0) && ($y==0)) {
            $this->myLogger->trace($hdrstr);
            // cabecera
            $this->SetXY(12,36);
            $this->ac_header(1,13);
            $this->SetFont($this->getFontName(),'BI',13); // default font
            $this->Cell(80,8,$hdrstr,'TBLR',0,'C',true);
            $this->ac_row(1,11);
            // tres mejores absolutos
            for($n=0;$n<3;$n++) {
                $this->myLogger->trace("Entry {$n}:".json_encode($items[$n]));
                $penal=number_format2($items[$n]['Penal'],$this->timeResolution);
                $tiempo=number_format2($items[$n]['Tiempo'],$this->timeResolution);
                $this->SetXY(12,44+7*$n);
                $this->SetFont($this->getFontName(),'B',10);
                $this->Cell(15,7.5,$items[$n]['Nombre'],'L',0,'L',false);
                $this->SetFont($this->getFontName(),'',9);
                $this->Cell(25,7.5,$items[$n]['NombreGuia'],'C',0,'L',false);
                $this->Cell(20,7.5,$items[$n]['NombreClub'],'R',0,'R',false);
                $this->Cell(10,7.5,$tiempo,'R',0,'R',false);
                $this->Cell(10,7.5,$penal,'R',0,'R',false);
            }
            // linea de cierre
            $this->Line(12,66,92,66);
        } else {
            // cuadro para datos "normales"
            $this->myLogger->trace($hdrstr);
            // cabecera
            $this->SetXY(30+65*$x,45+24*$y);
            $this->ac_header(1,12);
            $this->SetFont($this->getFontName(),'BI',11); // default font
            $this->Cell(63,5.5,$hdrstr,'TBLR',0,'C',true);
            $this->ac_row(1,10);
            // tres mejores de la categoria/grado
            for($n=0;$n<3;$n++) {
                $this->myLogger->trace("Entry {$n}:".json_encode($items[$n]));
                $penal=number_format2($items[$n]['Penal'],$this->timeResolution);
                $tiempo=number_format2($items[$n]['Tiempo'],$this->timeResolution);
                $this->SetXY(30+65*$x,45+24*$y+5.5*($n+1));
                $this->SetFont($this->getFontName(),'B',7);
                $this->Cell(11,5.5,$items[$n]['Nombre'],'L',0,'L',false);
                $this->SetFont($this->getFontName(),'',7);
                $this->Cell(20,5.5,$items[$n]['NombreGuia'],'C',0,'L',false);
                $this->Cell(14,5.5,$items[$n]['NombreClub'],'R',0,'R',false);
                $this->Cell(9,5.5,$tiempo,'R',0,'R',false);
                $this->Cell(9,5.5,$penal,'R',0,'R',false);
            }
            // linea de cierre
            $this->Line(30+65*$x,67+24*$y,93+65*$x,67+24*$y);
        }
    }

    /**
     * print table data
     * @param $perro dog id
     * @return string
     */
	function composeTable() {
		$this->myLogger->enter();
        $this->AddPage();
        $heights=intval($this->federation->get('Heights'));
		if ($heights==3) {
            $this->writeBlock(3,3,array('GI'),array('L'));
            $this->writeBlock(3,2,array('GI'),array('M'));
            $this->writeBlock(3,1,array('GI'),array('S'));
            $this->writeBlock(3,0,array('GI'),array('L','M','S'));
            $this->writeBlock(2,3,array('GII'),array('L'));
            $this->writeBlock(2,2,array('GII'),array('M'));
            $this->writeBlock(2,1,array('GII'),array('S'));
            $this->writeBlock(2,0,array('GII'),array('L','M','S'));
            $this->writeBlock(1,3,array('GIII'),array('L'));
            $this->writeBlock(1,2,array('GIII'),array('M'));
            $this->writeBlock(1,1,array('GIII'),array('S'));
            $this->writeBlock(1,0,array('GIII'),array('L','M','S'));
            $this->writeBlock(0,3,array('GI','GII','GIII'),array('L'));
            $this->writeBlock(0,2,array('GI','GII','GIII'),array('M'));
            $this->writeBlock(0,1,array('GI','GII','GIII'),array('S'));
            $this->writeBlock(0,0,array('GI','GII','GIII'),array('L','M','S'));
        }
        if ($heights==4) {
            $this->writeBlock(3,4,array('Jr'),array('L'));
            $this->writeBlock(3,3,array('Jr'),array('M'));
            $this->writeBlock(3,2,array('Jr'),array('S'));
            $this->writeBlock(3,1,array('Jr'),array('T'));
            $this->writeBlock(3,0,array('Jr'),array('L','M','S','T'));
            $this->writeBlock(2,4,array('GI'),array('L'));
            $this->writeBlock(2,3,array('GI'),array('M'));
            $this->writeBlock(2,2,array('GI'),array('S'));
            $this->writeBlock(2,1,array('GI'),array('T'));
            $this->writeBlock(2,0,array('GI'),array('L','M','S','T'));
            $this->writeBlock(1,4,array('GII'),array('L'));
            $this->writeBlock(1,3,array('GII'),array('M'));
            $this->writeBlock(1,2,array('GII'),array('S'));
            $this->writeBlock(1,1,array('GII'),array('T'));
            $this->writeBlock(1,0,array('GII'),array('L','M','S','T'));
            $this->writeBlock(0,4,array('Jr','GI','GII'),array('L'));
            $this->writeBlock(0,3,array('Jr','GI','GII'),array('M'));
            $this->writeBlock(0,2,array('Jr','GI','GII'),array('S'));
            $this->writeBlock(0,1,array('Jr','GI','GII'),array('T'));
            $this->writeBlock(0,0,array('Jr','GI','GII'),array('L','M','S','T'));
        }
        if ($heights==5) {
            $this->writeBlock(3,5,array('GI'),array('X'));
            $this->writeBlock(3,4,array('GI'),array('L'));
            $this->writeBlock(3,3,array('GI'),array('M'));
            $this->writeBlock(3,2,array('GI'),array('S'));
            $this->writeBlock(3,1,array('GI'),array('T'));
            $this->writeBlock(3,0,array('GI'),array('X','L','M','S','T'));
            $this->writeBlock(2,5,array('GII'),array('X'));
            $this->writeBlock(2,4,array('GII'),array('L'));
            $this->writeBlock(2,3,array('GII'),array('M'));
            $this->writeBlock(2,2,array('GII'),array('S'));
            $this->writeBlock(2,1,array('GII'),array('T'));
            $this->writeBlock(2,0,array('GII'),array('X','L','M','S','T'));
            $this->writeBlock(1,5,array('GIII'),array('X'));
            $this->writeBlock(1,4,array('GIII'),array('L'));
            $this->writeBlock(1,3,array('GIII'),array('M'));
            $this->writeBlock(1,2,array('GIII'),array('S'));
            $this->writeBlock(1,1,array('GIII'),array('T'));
            $this->writeBlock(1,0,array('GIII'),array('X','L','M','S','T'));
            $this->writeBlock(0,5,array('GI','GII','GIII'),array('X'));
            $this->writeBlock(0,4,array('GI','GII','GIII'),array('L'));
            $this->writeBlock(0,3,array('GI','GII','GIII'),array('M'));
            $this->writeBlock(0,2,array('GI','GII','GIII'),array('S'));
            $this->writeBlock(0,1,array('GI','GII','GIII'),array('T'));
            $this->writeBlock(0,0,array('GI','GII','GIII'),array('X','L','M','S','T'));
        }

		$this->myLogger->leave();
        return "";
	}
}
?>