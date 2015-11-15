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

class EquiposByJornada extends PrintCommon {
	protected $equipos; // lista de equipos de esta jornada
    protected $perros; // lista de participantes en esta jornada
	
	// geometria de las celdas
	protected $cellHeader;
    //                      Dorsal  nombre raza licencia Categoria guia club  celo  observaciones
	protected $pos	=array( 10,     25,     27,    10,    18,      40,   25,  10,    25);
	protected $align=array( 'R',    'C',    'R',    'C',  'C',     'R',  'R', 'C',   'R');
	
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
        $teams=$m->getTeamsByJornada();
        // reindexamos por ID y anyadimos un campo extra "Perros" con los perros del equipo
        $this->equipos=array();
        foreach ($teams as &$equipo) {
            $equipo['Perros']=$m->getPerrosByTeam($equipo['ID']);
            $this->equipos[$equipo['ID']]=$equipo;
        }

        // Datos de los participantes (indexados por ID de perro)
        $m=new DBObject("print_teamsByJornada");
        $r=$m->__select("*","Resultados","(Jornada=$jornada)","","");
        $this->perros=array();
        foreach($r['rows'] as $item) {
            $this->perros[intval($item['Perro'])]=$item;
        }
        // finalmente internacionalizamos cabeceras
		$this->cellHeader = 
				array(_('Dorsal'),_('Name'),_('Breed'),_('Lic'),_('Cat'),_('Handler'),_('Club'),_('Heat'),_('Comments'));
	}
	
	// Cabecera de página
	function Header() {
		$this->print_commonHeader(_("List of teams"));
        // pintamos "identificacion de la manga"
        $this->SetFont('Helvetica','B',12); // Helvetica bold 15
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
            foreach($team['Perros'] as $miembro) {
                $logo=$this->getLogoName($miembro['Logo']);
                if ( ( ! in_array($logo,$logos) ) && ($count<4) ) $logos[$count++]=$logo;
            }
        }
        $this->SetXY(10,45+5*$rowcount);
		$this->ac_header(1,17);
        $this->Image($logos[0],$this->getX(),$this->getY(),9);
        $this->Image($logos[1],$this->getX()+10,$this->getY(),9);
        $this->Image($logos[2],$this->getX()+20,$this->getY(),9);
        $this->Image($logos[3],$this->getX()+30,$this->getY(),9);
        $this->SetX($this->GetX()+40);
        $this->Cell(140,9,$team['Nombre'],'T',0,'R',true);
        $this->Cell(10,9,'','TR',0,'R',true); // empty space at right of page
        $this->Ln();
        $this->ac_header(2,8);
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

        // take care on wide license federations
        if ($this->federation->get('WideLicense')) {
            $this->pos[1]-=2; $this->pos[2]-=3; $this->pos[3]+=20; $this->pos[8]-=15;
        }
        $order=0;
        $rowcount=0;
		foreach($this->equipos as $equipo) {
            $miembros=$equipo['Perros'];
            $num=count($miembros);
            if ($num==0) continue; // skip empty teams
            // check for need newpage. each row has 5mm,so can handle up to 47 rows
            if ( ($rowcount+3+$num) >47 ) $rowcount=0;  // teamheader takes 3 rows
            if ($rowcount==0) $this->AddPage();
            $rowcount=$this->printTeamInformation($rowcount,$equipo);
            foreach($miembros as $row) {
                $this->ac_SetFillColor( (($order&0x01)==0)?$bg1:$bg2);
    			$this->Cell($this->pos[0],5,$row['Dorsal'],		'LR',0,$this->align[0],true);
                $this->SetFont('Helvetica','B',10); // bold 9px
                $this->Cell($this->pos[1],5,$row['Nombre'],		'LR',0,$this->align[1],true);
                $this->SetFont('Helvetica','',8); // remove bold 9px
                $this->Cell($this->pos[2],5,$row['Raza'],		'LR',0,$this->align[2],true);
                if ($this->federation->get('WideLicense')) $this->SetFont('Helvetica','',7);
                $this->Cell($this->pos[3],5,$row['Licencia'],	'LR',0,$this->align[3],true);
                $this->SetFont('Helvetica','',8); // restore normal size after wide license
                $this->Cell($this->pos[4],5,$this->cat[$row['Categoria']],	'LR',0,$this->align[4],true);
    			$this->Cell($this->pos[5],5,$row['NombreGuia'],	'LR',0,$this->align[5],true);
    			$this->Cell($this->pos[6],5,$row['NombreClub'],	'LR',0,$this->align[6],true);
    			$this->Cell($this->pos[7],5,($row['Celo']==0)?"":_("Celo"),	'LR',0,$this->align[7],true);
    			$this->Cell($this->pos[8],5,/*$row['Observaciones']*/ "",'LR',0,$this->align[8],true);
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
	$pdf = new EquiposByJornada($prueba,$jornada);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("equiposByJornada.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
};
?>