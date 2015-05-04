<?php
/*
print_resultadosByManga.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


header('Set-Cookie: fileDownload=true; path=/');
// mandatory 'header' to be the first element to be echoed to stdout

/**
 * genera un pdf con los participantes ordenados segun los resultados de la manga
 */

require_once(__DIR__."/fpdf.php");
require_once(__DIR__."/../tools.php");
require_once (__DIR__."/../logging.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jueces.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Resultados.php');
require_once(__DIR__.'/../database/classes/Equipos.php');
require_once(__DIR__."/print_common.php");

class PDF extends PrintCommon {
	
	protected $manga;
	protected $resultados;
    protected $equipos;
	protected $mode;
    protected $defaultPerro = array( // participante por defecto para garantizar que haya 4perros/equipo
        'Dorsal' => '-',
        'Perro' => 0,
        'Nombre' => 'No inscrito',
        'NombreGuia' => 'No inscrito',
        'NombreClub' => 'No inscrito',
        'Licencia' => '-',
        'Categoria' => '-',
        'Faltas' => 0,
        'Tocados' => 0,
        'Rehuses' => 0,
        'Tiempo' => '-',
        'Velocidad' => '-',
        'Penalizacion' => 400,
        'Calificacion' => 'No inscrito',
        'CShort' => 'No inscrito',
        'Puesto' => '-'
    );
	// geometria de las celdas
	protected $cellHeader;
                    //     Dors    Nombre  Lic     Guia   Club    Cat     Flt    Toc    Reh     Tiempo   vel   penal calif   puesto, equipo
	protected $pos	=array(  7,		18,		10,		30,		25,	    7,	   5,      5,    5,       10,     7,    12,    10,	 7,  30);
	protected $align=array(  'L',   'L',    'C',    'R',   'R',    'C',    'C',   'C',   'C',     'R',    'R',  'R',   'C',	 'C', 'R');

    protected $cat  =array("-" => "","L"=>"Large","M"=>"Medium","S"=>"Small","T"=>"Tiny");
	protected $modestr  
		=array("Large","Medium","Small","Medium+Small","Conjunta L/M/S","Tiny","Large+Medium","Small+Tiny","Conjunta L/M/S/T");
	
	/**
	 * Constructor
	 * @param {obj} $manga datos de la manga
	 * @param {obj} $resultados resultados asociados a la manga/categoria pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$manga,$resultados,$mode) {
		parent::__construct('Portrait',"print_resultadosEquipos3",$prueba,$jornada);
		$this->manga=$manga;
		$this->resultados=$resultados;
        $this->mode=$mode;
        $this->cellHeader=
            array(_('Dorsal'),_('Nombre'),_('Lic.'),_('Guía'),_('Club'),_('Cat.'),_('Flt.'),_('Toc.'),_('Reh.'),
                  _('Tiempo'),_('Vel.'),_('Penal.'),_('Calificación'),_('Puesto'),_('Global Equipo'));

        // Datos de equipos de la jornada
        $m=new Equipos("print_resultadosEquipos3",$prueba,$jornada);
        $teams=$m->getTeamsByJornada();
        // reindexamos por ID y anyadimos un campo extra con el array de resultados
        $this->equipos=array();
        foreach ($teams as &$equipo) {
            $equipo['Resultados']=array();
            $equipo['Tiempo']=0.0;
            $equipo['Penalizacion']=0.0;
            $this->equipos[$equipo['ID']]=$equipo;
        }
        // now fill team members array.
        // notice that $resultados is already sorted by results
        foreach($this->resultados['rows'] as &$result) {
            $teamid=$result['Equipo'];
            $equipo=&$this->equipos[$teamid];
            array_push($equipo['Resultados'],$result);
            // suma el tiempo y penalizaciones de los tres primeros
            if (count($equipo['Resultados'])<4) {
                $equipo['Tiempo']+=floatval($result['Tiempo']);
                $equipo['Penalizacion']+=floatval($result['Penalizacion']);
            }
        }
        // rastrea los equipos con menos de tres participantes y marca los que faltan
        // no presentados
        foreach($this->equipos as &$equipo) {
            switch(count($equipo['Resultados'])){
                case 0: continue; // TODO: remove team from array as this team should not be shown
                case 1: $equipo['Penalizacion']+=200.0; // add pending "No presentado"
                    // no break
                case 2: $equipo['Penalizacion']+=200.0; // add pending "No presentado"
                    // no break;
                case 3:case 4: break;
                default:$this->myLogger->error("Equipo {$equipo['ID']} : '{$equipo['Nombre']}' con exceso de participantes:".count($equipo['Resultados']));
                    break;
            }
        }
        // finally sort equipos by result instead of id
        usort($this->equipos,function($a,$b){
            if ($a['Penalizacion']==$b['Penalizacion']) return ($a['Tiempo']>$b['Tiempo'])?1:-1;
            return ($a['Penalizacion']>$b['Penalizacion'])?1:-1;
        });
	}
	
	// Cabecera de página
	function Header() {
		$this->print_commonHeader(_("Resultados Parciales"));
		$this->print_identificacionManga($this->manga,$this->modestr[intval($this->mode)]);
		
		// Si es la primera hoja pintamos datos tecnicos de la manga
		if ($this->PageNo()!=1) return;

		$this->SetFont('Arial','B',9); // bold 9px
		$jobj=new Jueces("print_resultadosEquipos3");
		$juez1=$jobj->selectByID($this->manga->Juez1);
		$juez2=$jobj->selectByID($this->manga->Juez2);
		$this->Cell(20,7,"Juez 1:","LT",0,'L',false);
		$str=($juez1['Nombre']==="-- Sin asignar --")?"":$juez1['Nombre'];
		$this->Cell(70,7,$str,"T",0,'L',false);
		$this->Cell(20,7,"Juez 2:","T",0,'L',false);
		$str=($juez2['Nombre']==="-- Sin asignar --")?"":$juez2['Nombre'];
		$this->Cell(78,7,$str,"TR",0,'L',false);
		$this->Ln(7);
		$this->Cell(20,7,"Distancia:","LB",0,'L',false);
		$this->Cell(25,7,"{$this->resultados['trs']['dist']} mts","B",0,'L',false);
		$this->Cell(20,7,"Obstáculos:","B",0,'L',false);
		$this->Cell(25,7,$this->resultados['trs']['obst'],"B",0,'L',false);
		$this->Cell(10,7,"TRS:","B",0,'L',false);
		$this->Cell(20,7,"{$this->resultados['trs']['trs']} seg.","B",0,'L',false);
		$this->Cell(10,7,"TRM:","B",0,'L',false);
		$this->Cell(20,7,"{$this->resultados['trs']['trm']} seg.","B",0,'L',false);
		$this->Cell(20,7,"Velocidad:","B",0,'L',false);
		$this->Cell(18,7,"{$this->resultados['trs']['vel']} m/s","BR",0,'L',false);
		$this->Ln(14); // en total tres lineas extras en la primera hoja
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}

    function printTeamInformation($teamcount,$numrows,$team) {
        // evaluate logos
        $logos=array('null.png','null.png','null.png','null.png');
        if ($team['Nombre']==="-- Sin asignar --") {
            $logos[0]='agilitycontest.png';
        } else {
            $count=0;
            foreach( explode(",",$team['Miembros']) as $miembro) {
                if ($miembro==="BEGIN") continue;
                if ($miembro==="END") continue;
                $logo=$this->getLogoName(intval($miembro));
                if ( ( ! in_array($logo,$logos) ) && ($count<4) ) $logos[$count++]=$logo;
            }
        }
        $offset=($this->PageNo()==1)?65:45;
        $this->SetXY(10,$offset+40*($teamcount%$numrows));
        $this->ac_header(1,18);
        $this->Cell(15,10,strval(1+$teamcount)." -",'LT',0,'C',true); // imprime puesto del equipo
        $this->Cell(10,10,$this->Image(__DIR__.'/../../images/logos/'.$logos[0],$this->getX(),$this->getY(),12),"T",0,'C',($logos[0]==='null.png')?true:false);
        $this->Cell(10,10,$this->Image(__DIR__.'/../../images/logos/'.$logos[1],$this->getX(),$this->getY(),12),"T",0,'C',($logos[1]==='null.png')?true:false);
        $this->Cell(10,10,$this->Image(__DIR__.'/../../images/logos/'.$logos[2],$this->getX(),$this->getY(),12),"T",0,'C',($logos[2]==='null.png')?true:false);
        $this->Cell(10,10,$this->Image(__DIR__.'/../../images/logos/'.$logos[3],$this->getX(),$this->getY(),12),"T",0,'C',($logos[3]==='null.png')?true:false);
        $this->Cell(125,10,$team['Nombre'],'T',0,'R',true);
        $this->Cell(8,10,'','TR',0,'R',true); // empty space at right of page
        $this->Ln();
        $this->ac_header(2,8);
        for($i=0;$i<count($this->cellHeader);$i++) {
            // en la cabecera texto siempre centrado
            $this->Cell($this->pos[$i],5,$this->cellHeader[$i],1,0,'C',true);
        }
        $this->ac_row(2,9);
        $this->Ln();
    }

	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
		
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->SetLineWidth(.3);
		
		// Datos
		$fill = false;
		$teamcount=0;
        foreach($this->equipos as $equipo) {
            $numrows=($this->PageNo()==1)?5:6;
            // si el equipo no tiene participantes es que la categoria no es válida: skip
            if (count($equipo['Resultados'])==0) continue;
            // REMINDER: $this->cell( width, height, data, borders, where, align, fill)
            if( ($teamcount%$numrows) == 0 ) { // assume 40mmts/team)
                $this->addPage();
            }
            // evaluate puesto del equipo
            $this->myLogger->trace("imprimiendo datos del equipo {$equipo['ID']} - {$equipo['Nombre']}");
            $this->printTeamInformation($teamcount,$numrows,$equipo);
            // print team header/data
            for ($n=0;$n<4;$n++) {
                // con independencia de los perros del equipo imprimiremos siempre 4 columnas
                $row=$this->defaultPerro;
                if (array_key_exists($n,$equipo['Resultados'])) $row=$equipo['Resultados'][$n];
                // print team member's result
                $this->myLogger->trace("imprimiendo datos del perro {$row['Perro']} - {$row['Nombre']}");
                // properly format special fields
                $puesto= ($row['Penalizacion']>=200)? "-":"{$row['Puesto']}º";
                $veloc= ($row['Penalizacion']>=200)?"-":number_format($row['Velocidad'],1);
                $tiempo= ($row['Penalizacion']>=200)?"-":number_format($row['Tiempo'],2);
                $penal=number_format($row['Penalizacion'],2);

                // print row data
                $this->SetFont('Arial','',8); // set data font size
                $this->Cell($this->pos[0],5,$row['Dorsal'],			'LBR',	0,		$this->align[0],	$fill);
                $this->SetFont('Arial','B',8); // mark Nombre as bold
                $this->Cell($this->pos[1],5,$row['Nombre'],			'LBR',	0,		$this->align[1],	$fill);
                $this->SetFont('Arial','',8); // set data font size
                $this->Cell($this->pos[2],5,$row['Licencia'],		'LBR',	0,		$this->align[2],	$fill);
                $this->Cell($this->pos[3],5,$row['NombreGuia'],		'LBR',	0,		$this->align[3],	$fill);
                $this->Cell($this->pos[4],5,$row['NombreClub'],		'LBR',	0,		$this->align[4],	$fill);
                // en pruebas por equipos el grado se ignora
                // $this->Cell($this->pos[5],6,$row['Categoria'].' - '.$row['Grado'],	'LR',	0,		$this->align[5],	$fill);
                $this->Cell($this->pos[5],5,$row['Categoria'],  	'LBR',	0,		$this->align[5],	$fill);
                $this->Cell($this->pos[6],5,$row['Faltas'],			'LBR',	0,		$this->align[6],	$fill);
                $this->Cell($this->pos[7],5,$row['Tocados'],		'LBR',	0,		$this->align[7],	$fill);
                $this->Cell($this->pos[8],5,$row['Rehuses'],		'LBR',	0,		$this->align[8],	$fill);
                $this->Cell($this->pos[9],5,$tiempo,				'LBR',	0,		$this->align[9],	$fill);
                $this->Cell($this->pos[10],5,$veloc,				'LBR',	0,		$this->align[10],	$fill);
                $this->Cell($this->pos[11],5,$penal,				'LBR',	0,		$this->align[11],	$fill);
                $this->Cell($this->pos[12],5,$row['CShort'],	'LBR',	0,		$this->align[12],	$fill);
                $this->Cell($this->pos[13],5,$puesto,			'LBR',	0,		$this->align[13],	$fill);
                $this->ac_header(2,8);
                // en las dos primeras filas imprimimos informacion de resultados del equipo
                if ($n==0) {
                    $this->Cell($this->pos[14],5,"Penaliz.: ".$equipo['Penalizacion'],	'LBR',	0,		$this->align[14],	true);
                }
                if ($n==1) {
                    $this->Cell($this->pos[14],5,"Tiempo: ".$equipo['Tiempo'],	'LBR',	0,		$this->align[14],	true);
                }
                $this->ac_row(2,9);
                $this->Ln();
                $fill = ! $fill;
            }
            $teamcount++;
        }
        $this->myLogger->leave();
	}
}

// Consultamos la base de datos
try {
	$idprueba=http_request("Prueba","i",0);
	$idjornada=http_request("Jornada","i",0);
	$idmanga=http_request("Manga","i",0);
	$mode=http_request("Mode","i",0);
	
	$mngobj= new Mangas("printResultadosByManga",$idjornada);
	$manga=$mngobj->selectByID($idmanga);
	$resobj= new Resultados("printResultadosByManga",$idprueba,$idmanga);
	$resultados=$resobj->getResultados($mode,true); // throw exception if pending dogs
	// Creamos generador de documento
	$pdf = new PDF($idprueba,$idjornada,$manga,$resultados,$mode);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("resultadosByManga.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
	die($e->getMessage());
}
?>