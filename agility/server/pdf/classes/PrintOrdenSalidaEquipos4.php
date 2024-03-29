<?php
/*
print_equiposByJornada.php

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
 * genera un pdf con el orden de salida de los participantes en jornada de prueba por equipos 4
*/
require_once(__DIR__."/../fpdf.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__.'/../../database/classes/Equipos.php');
require_once(__DIR__."/../print_common.php");

class PrintOrdenSalidaEquipos4 extends PrintCommon {
	protected $equipos; // lista de equipos de esta jornada
    protected $perros; // lista de participantes en esta jornada
    protected $manga; // datos de la manga
    protected $categoria;
    protected $validcats;
    protected $heights;
	
	// geometria de las celdas
	protected $cellHeader;

    /**
     * Constructor
     * @param {array} data (prueba,jornada, manga, categorias, rango, comentarios)
     * {integer} Prueba ID
     * {integer} $jornada Jormada ID
     * {integer} $manga Manga ID
     * {string} categorias -XLMST
     * {string} rango [\d]-[\d]
     * {string} comentarios
     * @throws Exception
     */
	function __construct($data) {
        parent::__construct('Portrait',"print_ordenDeSalidaEquipos4",$data['prueba'],$data['jornada'],$data['comentarios']);
		if ( ($data['prueba']<=0) || ($data['jornada']<=0) ) {
			$this->errormsg="print_ordenDeSalidaEquipos4: either prueba or jornada data are invalid";
			throw new Exception($this->errormsg);
		}
        // comprobamos que estamos en una jornada por equipos
        if (!Jornadas::isJornadaEquipos($this->jornada)) {
            $this->errormsg="print_ordenDeSalidaEquipos4: Jornada {$data['jornada']} has no Team competition declared";
            throw new Exception($this->errormsg);
        }
        // guardamos info de la manga
        $this->manga=$this->myDBObject->__getObject("mangas",$data['manga']);
        $this->heights=Competitions::getHeights($this->prueba->ID,$this->jornada->ID,$this->manga->ID);
        // Datos del orden de salida de equipos
        $m = Competitions::getOrdenSalidaInstance("ordenSalidaEquipos4",$data['manga']);
        $teams= $m->getTeams();
        $this->equipos=$teams['rows'];
        // anyadimos el array de perros del equipo
        foreach($this->equipos as &$equipo) {$equipo['Perros']=array();}
        $r= $this->myDBObject->__select("*","resultados","(Manga={$data['manga']})","","");
        foreach($r['rows'] as $perro) {
            foreach($this->equipos as &$equipo) {
                if ($perro['Equipo']==$equipo['ID']) {
                    array_push($equipo['Perros'],$perro);
                    break;
                }
            }
        }
        $this->validcats=$data['categorias'];
        $this->rango= (preg_match('/^\d+-\d+$/',$data['rango']))? $data['rango'] : "1-99999";

        // set pdf file name
        $grad=$this->federation->getTipoManga($this->manga->Tipo,3); // nombre de la manga
        $cat=$this->validcats; // categorias del listado
        $str=($cat=='-')?$grad:"{$grad}_{$cat}";
        $res=normalize_filename($str);
        $this->set_FileName("OrdenDeSalida_{$res}.pdf");
	}
	
	// Cabecera de página
	function Header() {
		$this->print_commonHeader(_("Starting order")." ("._("Teams").")");

        // pintamos datos de la jornada
        $this->SetFont($this->getFontName(),'B',12); // bold 15
        $str  = $this->jornada->Nombre . " - " . $this->jornada->Fecha;
        $this->Cell(90,9,$str,0,0,'L',false);

        // pintamos tipo y categoria de la manga
        $tmanga= _(Mangas::getTipoManga($this->manga->Tipo,1,$this->federation));
        // $categoria=$this->getCatString($this->categoria); // no funciona en equipos4
        $str2 = "{$tmanga} - {$this->categoria}";
        $this->Cell(100,9,$str2,0,0,'R',false); // al otro lado tipo y categoria de la manga
        $this->Ln(12);
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}
	
	function printTeamInfo($ypos,$index,$team,$members) {
        // evaluate logos
        $nullpng=getIconPath($this->federation->get('Name'),"null.png");
        $logos=array($nullpng,$nullpng,$nullpng,$nullpng);
        $numlogos=Jornadas::getTeamDogs($this->jornada)[1]; // maxdogs
        if ($team['Nombre']==="-- Sin asignar --") {
            $logos[0]=getIconPath($this->federation->get('Name'),"agilitycontest.png");
        } else {
            $count=0;
            foreach($members as $miembro) {
                $logo=$this->getLogoName($miembro['Perro']);
                if ( ( ! in_array($logo,$logos) ) && ($count<$numlogos) ) $logos[$count++]=$logo;
            }
        }
        // posicion de la celda
        $y=$ypos;
        $boxsize=2+4*max($this->getMaxDogs(),count($members));
        $this->SetXY(10,$y);
        // caja de datos de perros
        $this->ac_header(1,16);
        $this->Cell(12,$boxsize,1+$index,'LTBR',0,'C',true);
        $this->ac_header(2,16);
        $this->Cell(103,$boxsize,"","TB",0,'C',true);
        $this->SetY($y+1);
        foreach($members as $id => $perro) {
            $this->SetX(23);
            $this->ac_row($id,8);
            $this->Cell(6,4,$perro['Dorsal'],'LTBR',0,'L',true);
            $this->SetFont($this->getFontName(),'B',8);
            $this->Cell(13,4,$perro['Nombre'],'LTBR',0,'C',true);
            $this->SetFont($this->getFontName(),'',7);
            $this->Cell(20,4,$perro['Raza'],'LTBR',0,'R',true);
            $this->SetFont($this->getFontName(),'',7);
            $this->Cell(15,4,$this->getCatString($perro['Categoria']),'LTBR',0,'C',true);
            $this->SetFont($this->getFontName(),'',7);
            $this->Cell(27,4,$this->getHandlerName($perro),'LTBR',0,'R',true);
            $this->SetFont($this->getFontName(),'',7);
            $this->Cell(20,4,$perro['NombreClub'],'LTBR',0,'R',true);
            $this->Ln(4);
        }
        // caja de datos del equipo
        $this->SetXY(125,$y);
        $this->ac_header(2,14);
        $this->Cell(65,7,$team['Nombre'],'TB',0,'R',true);
        $this->Cell(10,7,'','TRB',0,'',true); // empty space at right of page

        $this->SetXY(125,7+$y);
        $this->Image($logos[0],$this->getX(),$this->getY(),11);
        $this->Image($logos[1],$this->getX()+11,$this->getY(),11);
        $this->Image($logos[2],$this->getX()+22,$this->getY(),11);
        $this->Image($logos[3],$this->getX()+33,$this->getY(),11);

        return $y+$boxsize+3; // evaluate next Y position and return
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
        $index=0;
        $rowcount=0;
        $this->categoria="-";
        // Rango
        $fromItem=1;
        $toItem=99999;
        if (preg_match('/^\d+-\d+$/',$this->rango)!==FALSE) {
            $a=explode("-",$this->rango);
            $fromItem=intval($a[0]);
            $toItem=intval($a[1]);
        }
        $ypos=60;
		foreach($this->equipos as $equipo) {
            // skip "-- Sin asignar --" team. Do not print team on unrequested categories
            if ($equipo['Nombre']==="-- Sin asignar --") continue;
            // $this->myLogger->trace("Team:{$equipo['Nombre']} cats:{$equipo['Categorias']} compare to:{$this->validcats}");
            if (!category_match($equipo['Categorias'],$this->heights,$this->validcats)) continue;
            if ( (($index+1)<$fromItem) || (($index+1)>$toItem) ) { $index++; continue; } // team index not in range; skip
            $miembros=$equipo['Perros'];
            $num=count($miembros);
            if ($num==0) continue; // skip empty teams
            // si cambia de categoria, pagina nueva
            if ($equipo['Categorias']!=$this->categoria) {
                $this->categoria=$equipo['Categorias'];
                $ypos=295; // force page break on next "if"
            }
            // si el nuevo equipo no cabe en la pagina pagina nueva
            if ($ypos+2+4*count($miembros) > 280) {
                $ypos=60;
                $this->AddPage();
            }
            // pintamos el aspecto general de la celda
            $ypos=$this->printTeamInfo($ypos,$index,$equipo,$miembros);
            $index++;
		}
		// Línea de cierre
		$this->myLogger->leave();
	}
}
?>