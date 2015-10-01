<?php
/*
print_equiposByJornada.php

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
 * genera un pdf ordenado con los participantes en jornada de prueba por equipos
*/

require_once(__DIR__."/fpdf.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Equipos.php');
require_once(__DIR__."/print_common.php");

class ResultadosByEquipos4 extends PrintCommon {

    protected $manga; // informacion de la manga
    protected $resultados; // tabla de resultados individuales
    protected $equipos; // lista de equipos
    protected $mode; // modo de la manga
    protected $eqmgr; // objeto "Equipos"
    protected $defaultPerro = array( // participante por defecto para garantizar que haya 4perros/equipo
        'Dorsal' => '-',
        'Perro' => 0,
        'Nombre' => '-',
        'NombreGuia' => '-',
        'NombreClub' => '-',
        'Licencia' => '-',
        'Categoria' => '-',
        'Faltas' => 0,
        'Tocados' => 0,
        'Rehuses' => 0,
        'Tiempo' => '-',
        'Velocidad' => '-',
        'Penalizacion' => 400,
        'Calificacion' => '-',
        'CShort' => '-',
        'Puesto' => '-'
    );

	/**
	 * Constructor
     * @param {integer} $prueba Prueba ID
     * @param {integer} $jornada Jormada ID
     * @param {integer} $manga Manga ID
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$manga,$resultados,$mode) {
        parent::__construct('Portrait',"print_resultadosEquipos4",$prueba,$jornada);
        $this->manga=$manga;
        $this->resultados=$resultados;
        $this->mode=$mode;
        $tmode=($this->jornada->Equipos3!=0)?3:4;
        $this->equipos=Resultados::getTeamResults($resultados['rows'],$prueba,$jornada,$tmode);
        $this->eqmgr=new Equipos("print_resultadosByEquipos4",$prueba,$jornada);
	}
	
	// Cabecera de página
	function Header() {
        $this->print_commonHeader(_("Round scores")." ("._("Teams").")");
        $this->print_identificacionManga($this->manga,$this->modestr[intval($this->mode)]);

        // Si es la primera hoja pintamos datos tecnicos de la manga
        if ($this->PageNo()!=1) return;

        $this->SetFont('Arial','B',9); // bold 9px
        $jobj=new Jueces("print_resultadosEquipos3");
        $juez1=$jobj->selectByID($this->manga->Juez1);
        $juez2=$jobj->selectByID($this->manga->Juez2);
        $this->Cell(20,5,_("Judge")." 1:","LT",0,'L',false);
        $str=($juez1['Nombre']==="-- Sin asignar --")?"":$juez1['Nombre'];
        $this->Cell(70,5,$str,"T",0,'L',false);
        $this->Cell(20,5,_("Judge")." 2:","T",0,'L',false);
        $str=($juez2['Nombre']==="-- Sin asignar --")?"":$juez2['Nombre'];
        $this->Cell(78,5,$str,"TR",0,'L',false);
        $this->Ln(5);
        $this->Cell(20,5,_("Distance").":","LB",0,'L',false);
        $this->Cell(25,5,"{$this->resultados['trs']['dist']} mts","B",0,'L',false);
        $this->Cell(20,5,_("Obstacles").":","B",0,'L',false);
        $this->Cell(25,5,$this->resultados['trs']['obst'],"B",0,'L',false);
        $this->Cell(10,5,_("SCT").":","B",0,'L',false);
        $this->Cell(20,5,"{$this->resultados['trs']['trs']} seg.","B",0,'L',false);
        $this->Cell(10,5,"MCT".":","B",0,'L',false);
        $this->Cell(20,5,"{$this->resultados['trs']['trm']} seg.","B",0,'L',false);
        $this->Cell(20,5,_("Speed").":","B",0,'L',false);
        $this->Cell(18,5,"{$this->resultados['trs']['vel']} m/s","BR",0,'L',false);
        $this->Ln(5);
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}
	
	function printTeamInfo($rowcount,$index,$team) {

        // valores por defecto
        $team['Faltas']=0;
        $team['Tocados']=0;
        $team['Rehuses']=0;
        $team['Eliminados']=0;
        $team['NoPresentados']=0;

        $members=$team['Resultados'];
        // evaluate logos
        $logos=array('null.png','null.png','null.png','null.png');
        if ($team['Nombre']==="-- Sin asignar --") {
            $logos[0]='agilitycontest.png';
        } else {
            $count=0;
            foreach($members as $miembro) {
                $logo=$this->getLogoName($miembro['Perro']);
                if ( ( ! in_array($logo,$logos) ) && ($count<4) ) $logos[$count++]=$logo;
            }
        }
        // posicion de la celda
        $y=58+16*($rowcount);
        $this->SetXY(10,$y);
        // caja de datos de perros
        $this->ac_header(2,16);
        $this->Cell(12,14,1+$index,'LTB',0,'C',true);
        $this->Cell(48,14,"","TBR",0,'C',true);
        $this->SetY($y+1);
        $this->ac_header(2,16);
        foreach($members as $id => $perro) {
            // imprimimos datos del perro
            $this->SetX(22);
            $this->ac_row($id,8);
            $this->Cell(6,3,$perro['Dorsal'],'LTBR',0,'L',true);
            $this->SetFont('Arial','B',8);
            $this->Cell(13,3,$perro['Nombre'],'LTBR',0,'C',true);
            $this->SetFont('Arial','',7);
            $this->Cell(28,3,$perro['NombreGuia'],'LTBR',0,'R',true);
            $this->Ln(3);
            // sumamos faltas, tocados y rehuses
            $team['Faltas']+=$perro['Faltas'];
            $team['Tocados']+=$perro['Tocados'];
            $team['Rehuses']+=$perro['Rehuses'];
            $team['Eliminados']+=$perro['Eliminado'];
            $team['NoPresentados']+=$perro['NoPresentado'];
        }
        $team['NoPresentados'] += 4-count($members);

        // caja de datos del equipo
        $this->SetXY(70,$y);
        $this->ac_header(1,14);
        $this->Cell(128,14,"","LTBR",0,'C',true);
        $this->SetXY(71,$y+1);
        $this->Cell(10,5,$this->Image(__DIR__.'/../../images/logos/'.$logos[0],$this->getX(),$this->getY(),5),"",0,'C',($logos[0]==='null.png')?true:false);
        $this->Cell(10,5,$this->Image(__DIR__.'/../../images/logos/'.$logos[1],$this->getX(),$this->getY(),5),"",0,'C',($logos[1]==='null.png')?true:false);
        $this->Cell(10,5,$this->Image(__DIR__.'/../../images/logos/'.$logos[2],$this->getX(),$this->getY(),5),"",0,'C',($logos[2]==='null.png')?true:false);
        $this->Cell(10,5,$this->Image(__DIR__.'/../../images/logos/'.$logos[3],$this->getX(),$this->getY(),5),"",0,'C',($logos[3]==='null.png')?true:false);
        $this->Cell(80,5,$team['Nombre'],'',0,'R',true);
        $this->Cell(6,5,'','',0,'',true); // empty space at right of page
        $this->Ln();
        // caja de faltas/rehuses/tiempos
        $this->ac_SetFillColor("#ffffff"); // white background
        $this->SetXY(71,7+$y);
        $this->Cell(15,6,"",'R',0,'L',true);
        $this->Cell(15,6,"",'R',0,'L',true);
        $this->Cell(15,6,"",'R',0,'L',true);
        $this->Cell(15,6,"",'R',0,'L',true);
        $this->Cell(15,6,"",'R',0,'L',true);
        $this->Cell(25,6,"",'R',0,'L',true);
        $this->Cell(26,6,"",'R',0,'L',true);

        $this->ac_SetFillColor("#c0c0c0"); // light gray
        $this->SetXY(71,7+$y+1);
        $this->SetFont('Arial','I',8); // italic 8px
        $this->Cell(15,2.5,_("Flt"),0,0,'L',false);
        $this->Cell(15,2.5,_("Ref"),0,0,'L',false);
        $this->Cell(15,2.5,_("Tch"),0,'L',false);
        $this->Cell(15,2.5,_("Elim"),0,'L',false);
        $this->Cell(15,2.5,_("N.P."),0,'L',false);
        $this->Cell(25,2.5,_("Time"),0,0,'L',false);
        $this->Cell(26,2.5,_("Penaliz"),0,0,'L',false);

        $this->SetXY(71,6+$y+1);
        $this->SetFont('Arial','B',10); // italic 8px
        $this->Cell(15,7,$team['Faltas'],0,0,'R',false);
        $this->Cell(15,7,$team['Rehuses'],0,0,'R',false);
        $this->Cell(15,7,$team['Tocados'],0,0,'R',false);
        $this->Cell(15,7,$team['Eliminados'],0,0,'R',false);
        $this->Cell(15,7,$team['NoPresentados'],0,0,'R',false);
        $this->Cell(25,7,$team['Tiempo'],0,0,'R',false);
        $this->Cell(26,7,$team['Penalizacion'],0,0,'R',false);
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
        $bg1=$this->config->getEnv('pdf_rowcolor1');
        // $bg2=$this->config->getEnv('pdf_rowcolor2');
        $this->ac_SetFillColor($bg1);
        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->SetLineWidth(.3);

        $index=0;
        $rowcount=0;
		foreach($this->equipos as $equipo) {
            // si el equipo no tiene participantes es que la categoria no es válida: skip
            if (count($equipo['Resultados'])==0) continue;
            // 14 teams/page
            if ( $rowcount%14==0) { $rowcount=0; $this->AddPage(); } // 14 teams /page
            // pintamos el aspecto general de la celda
            $this->printTeamInfo($rowcount,$index,$equipo);
            $rowcount++;
            $index++;
		}
		// Línea de cierre
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
    $resultados=$resobj->getResultados($mode); // throw exception if pending dogs

    // 	Creamos generador de documento
    $pdf=new ResultadosByEquipos4($idprueba,$idjornada,$manga,$resultados,$mode);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("resultadosEquipos4.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
    die ("Error accessing database: ".$e->getMessage());
};
?>