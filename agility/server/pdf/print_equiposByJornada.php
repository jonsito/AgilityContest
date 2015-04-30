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

class PDF extends PrintCommon {
	protected $equipos; // lista de equipos de esta jornada
    protected $perros; // lista de participantes en esta jornada
	
	// geometria de las celdas
	protected $cellHeader;
    //                      Dorsal  nombre raza licencia Categoria guia club  celo  observaciones
	protected $pos	=array( 10,     25,     27,    10,    18,      40,   25,  10,    25);
	protected $align=array( 'R',    'C',    'R',    'C',  'C',     'R',  'R', 'C',   'R');
	protected $cat  =array("-" => "","L"=>"Large","M"=>"Medium","S"=>"Small","T"=>"Tiny");
	
	/**
	 * Constructor
     * @param {integer} $prueba Prueba ID
     * @param {integer} $jornada Jormada ID
     * @param {integer} $manga Manga ID
	 * @throws Exception
	 */
	function __construct($prueba,$jornada) {
		parent::__construct('Portrait',"print_equiposByJornada",$prueba,$jornada);
		if ( ($prueba<=0) || ($jornada<=0) ) {
			$this->errormsg="print_teamsByJornada: either prueba or jornada data are invalid";
			throw new Exception($this->errormsg);
		}
        // comprobamos que estamos en una jornada por equipos
        $flag=intval($this->jornada->Equipos3)+intval($this->jornada->Equipos4);
        if ($flag==0) {
            $this->errormsg="print_teamsByJornada: Jornada $jornada has no Team competition declared";
            throw new Exception($this->errormsg);
        }
		// Datos de equipos de la jornada
        $m=new Equipos("print_teamsByJornada",$prueba,$jornada);
        $this->equipos=$m->getTeamsByJornada();
        // Datos de los participantes (indexados por ID de perro)
        $m=new DBObject("print_teamsByJornada");
        $r=$m->__select("*","Resultados","(Jornada=$jornada)","","");
        $this->perros=array();
        foreach($r['rows'] as $item) $this->perros[intval($item['Perro'])]=$item;
        // finalmente internacionalizamos cabeceras
		$this->cellHeader = 
				array(_('Dorsal'),_('Nombre'),_('Raza'),_('Lic.'),_('Cat.'),_('Guía'),_('Club'),_('Celo'),_('Observaciones'));
	}
	
	// Cabecera de página
	function Header() {
		$this->print_commonHeader(_("Listado de Equipos"));
        // pintamos "identificacion de la manga"
        $this->SetFont('Arial','B',12); // Arial bold 15
        $str  = "Jornada: ". $this->jornada->Nombre . " - " . $this->jornada->Fecha;
        $this->Cell(90,6,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
        $this->Ln(6);
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}
	
	function printTeamInformation($rowcount,$team) {
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
        $this->SetXY(10,45+6*$rowcount);
		$this->ac_header(1,18);
        $this->Cell(12,12,$this->Image(__DIR__.'/../../images/logos/'.$logos[0],$this->getX(),$this->getY(),12),"LT",0,'C',($logos[0]==='null.png')?true:false);
        $this->Cell(12,12,$this->Image(__DIR__.'/../../images/logos/'.$logos[1],$this->getX(),$this->getY(),12),"T",0,'C',($logos[1]==='null.png')?true:false);
        $this->Cell(12,12,$this->Image(__DIR__.'/../../images/logos/'.$logos[2],$this->getX(),$this->getY(),12),"T",0,'C',($logos[2]==='null.png')?true:false);
        $this->Cell(12,12,$this->Image(__DIR__.'/../../images/logos/'.$logos[3],$this->getX(),$this->getY(),12),"T",0,'C',($logos[3]==='null.png')?true:false);
        $this->Cell(132,12,$team['Nombre'],'T',0,'R',true);
        $this->Cell(10,12,'','TR',0,'R',true); // empty space at right of page
        $this->Ln();
        $this->ac_header(2,9);
        for($i=0;$i<count($this->cellHeader);$i++) {
            // en la cabecera texto siempre centrado
            $this->Cell($this->pos[$i],6,$this->cellHeader[$i],1,0,'C',true);
        }
		$this->ac_row(2,9);
		$this->Ln();
        $rowcount+=3;
        return $rowcount;
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
        $bg1=$this->config->getEnv('pdf_rowcolor1');
        $bg2=$this->config->getEnv('pdf_rowcolor2');
        $this->ac_SetFillColor($bg1);
        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->SetLineWidth(.3);
        $order=0;
        $rowcount=0;
		foreach($this->equipos as $equipo) {
            $miembros=explode(",",$equipo['Miembros']);
            $num=count($miembros)-2;
            if ($num==0) continue; // skip empty teams
            // check for need newpage.
            if ( ($rowcount+3+$num) >40 ) $rowcount=0; // BUG: assume that num is allways less than 34
            if ($rowcount==0) $this->AddPage();
            $rowcount=$this->printTeamInformation($rowcount,$equipo);
            foreach($miembros as $id) {
                if ($id==="BEGIN") continue;
                if ($id==="END") continue;
                if (!array_key_exists(intval($id),$this->perros)) {
                    $this->myLogger->error("El equipo {$equipo['ID']} declara perro $id no inscrito");
                    continue;
                }
                $row=$this->perros[$id];
                $this->ac_SetFillColor( (($order&0x01)==0)?$bg1:$bg2);
    			$this->Cell($this->pos[0],6,$row['Dorsal'],		'LR',0,$this->align[0],true);
                $this->SetFont('Arial','B',11); // bold 9px
                $this->Cell($this->pos[1],6,$row['Nombre'],		'LR',0,$this->align[1],true);
                $this->SetFont('Arial','',9); // remove bold 9px
                $this->Cell($this->pos[2],6,$row['Raza'],		'LR',0,$this->align[2],true);
                $this->Cell($this->pos[3],6,$row['Licencia'],	'LR',0,$this->align[3],true);
                $this->Cell($this->pos[4],6,$this->cat[$row['Categoria']],	'LR',0,$this->align[4],true);
    			$this->Cell($this->pos[5],6,$row['NombreGuia'],	'LR',0,$this->align[5],true);
    			$this->Cell($this->pos[6],6,$row['NombreClub'],	'LR',0,$this->align[6],true);
    			$this->Cell($this->pos[7],6,($row['Celo']==0)?"":_("Celo"),	'LR',0,$this->align[7],true);
    			$this->Cell($this->pos[8],6,$row['Observaciones'],'LR',0,$this->align[8],true);
    			$this->Ln();
    			$order++;
                $rowcount++;
            }
            $this->Cell(array_sum($this->pos),0,'','T'); // Línea de cierre
            $this->Ln(); // add extra newline
            $rowcount++;
		}
		// Línea de cierre
		$this->myLogger->leave();
	}
}

// Consultamos la base de datos
try {
	$prueba=http_request("Prueba","i",0);
	$jornada=http_request("Jornada","i",0);
	// 	Creamos generador de documento
	$pdf = new PDF($prueba,$jornada);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("equiposByJornada.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
};
?>