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
	protected $cat  =array("-" => "","L"=>"Large","M"=>"Medium","S"=>"Small","T"=>"Tiny");
	
	/**
	 * Constructor
     * @param {integer} $prueba Prueba ID
     * @param {integer} $jornada Jormada ID
     * @param {integer} $manga Manga ID
	 * @throws Exception
	 */
	function __construct($prueba,$jornada) {
		parent::__construct('Portrait',"print_entradaDeDatosEquipos4",$prueba,$jornada);
		if ( ($prueba<=0) || ($jornada<=0) ) {
			$this->errormsg="print_datosEquipos4: either prueba or jornada data are invalid";
			throw new Exception($this->errormsg);
		}
        // comprobamos que estamos en una jornada por equipos
        $flag=intval($this->jornada->Equipos3)+intval($this->jornada->Equipos4);
        if ($flag==0) {
            $this->errormsg="print_datosEquipos4: Jornada $jornada has no Team competition declared";
            throw new Exception($this->errormsg);
        }
		// Datos de equipos de la jornada
        $m=new Equipos("print_datosEquipos4",$prueba,$jornada);
        $teams=$m->getTeamsByJornada();
        // reindexamos por ID y anyadimos un campo extra "Perros" con los perros del equipo
        $this->equipos=array();
        foreach ($teams as &$equipo) {
            $equipo['Perros']=$m->getPerrosByTeam($equipo['ID']);
            $this->equipos[$equipo['ID']]=$equipo;
        }

        // Datos de los participantes (indexados por ID de perro)
        $m=new DBObject("print_datosEquipos4");
        $r=$m->__select("*","Resultados","(Jornada=$jornada)","","");
        $this->perros=array();
        foreach($r['rows'] as $item) {
            $this->perros[intval($item['Perro'])]=$item;
        }
        // finalmente internacionalizamos cabeceras
		$this->cellHeader = 
				array(_('Dorsal'),_('Nombre'),_('Raza'),_('Lic.'),_('Cat.'),_('Guía'),_('Club'),_('Celo'),_('Observaciones'));
	}
	
	// Cabecera de página
	function Header() {
		$this->print_commonHeader(_("Entrada de datos (Equipos-4)"));
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
	
	function printTeamInfo($rowcount,$team,$members) {
        // evaluate logos
        $logos=array('null.png','null.png','null.png','null.png');
        if ($team['Nombre']==="-- Sin asignar --") {
            $logos[0]='agilitycontest.png';
        } else {
            $count=0;
            foreach($members as $miembro) {
                $logo=$miembro['Logo'];
                if ( ( ! in_array($logo,$logos) ) && ($count<4) ) $logos[$count++]=$logo;
            }
        }
        // posicion de la celda
        $y=50+15*($rowcount%10);
        $this->SetXY(10,$y);
        // caja de datos de perros
        $this->ac_header(2,16);
        $this->Cell(12,12,1+$rowcount,'LTBR',0,'C',true);
        $this->Cell(50,12,"","TBR",0,'C',true);
        $this->SetY($y+1);
        $this->ac_header(2,16);
        foreach($members as $id => $perro) {
            $this->SetX(22);
            $this->ac_row($id,6);
            $this->Cell(6,2.5,$perro['Dorsal'],'LTBR',0,'L',true);
            $this->Cell(13,2.5,$perro['Nombre'],'LTBR',0,'C',true);
            $this->Cell(28,2.5,$perro['NombreGuia'],'LTBR',0,'R',true);
            $this->Ln(2.5);
        }
        // caja de datos del equipo
        $this->SetXY(70,$y);
        $this->ac_header(1,10);
        $this->Cell(130,12,"","LTBR",0,'C',true);
        $this->SetXY(71,$y+1);
        $this->Cell(10,2.5,$this->Image(__DIR__.'/../../images/logos/'.$logos[0],$this->getX(),$this->getY(),2.5),"",0,'C',($logos[0]==='null.png')?true:false);
        $this->Cell(10,2.5,$this->Image(__DIR__.'/../../images/logos/'.$logos[1],$this->getX(),$this->getY(),2.5),"",0,'C',($logos[1]==='null.png')?true:false);
        $this->Cell(10,2.5,$this->Image(__DIR__.'/../../images/logos/'.$logos[2],$this->getX(),$this->getY(),2.5),"",0,'C',($logos[2]==='null.png')?true:false);
        $this->Cell(10,2.5,$this->Image(__DIR__.'/../../images/logos/'.$logos[3],$this->getX(),$this->getY(),2.5),"",0,'C',($logos[3]==='null.png')?true:false);
        $this->Cell(80,2.5,$team['Nombre'],'',0,'R',true);
        $this->Cell(8,2.5,'','',0,'',true); // empty space at right of page
        $this->Ln();
        // caja de faltas/rehuses/tiempos
        $this->ac_SetFillColor("#ffffff"); // white background
        $this->SetXY(71,4+$y);
        $this->Cell(49,7.5,"",'R',0,'L',true);
        $this->Cell(30,7.5,"",'R',0,'L',true);
        $this->Cell(20,7.5,"",'R',0,'L',true);
        $this->Cell(29,7.5,"",'',0,'L',true);
        $this->SetXY(71,4+$y+1);
        $this->SetFont('Arial','I',8); // italic 8px
        $this->Cell(49,2.5,"Faltas",0,0,'L',false);
        $this->Cell(30,2.5,"Rehuses",0,0,'L',false);
        $this->Cell(20,2.5,"Tocados",0,'L',false);
        $this->Cell(29,2.5,"Tiempo",0,0,'L',false);
        // TODO: write
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
        $bg1=$this->config->getEnv('pdf_rowcolor1');
        $bg2=$this->config->getEnv('pdf_rowcolor2');
        $this->ac_SetFillColor($bg1);
        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->SetLineWidth(.3);

        // take care on RFEC contests
        if ($this->federation->getFederation()==1) {
            $this->pos[1]-=2; $this->pos[2]-=3; $this->pos[3]+=20; $this->pos[8]-=15;
        }
        $order=0;
        $rowcount=0;
		foreach($this->equipos as $equipo) {
            $miembros=$equipo['Perros'];
            $num=count($miembros);
            if ($num==0) continue; // skip empty teams
            if ($rowcount%10==0) {
                $this->AddPage();
                // indicamos nombre del operador que rellena la hoja
                $this->ac_header(2,12);
                $this->Cell(90,7,'Apunta:','LTBR',0,'L',true);
                $this->Cell(10,7,'',0,'L',false);
                $this->Cell(90,7,'Revisa:','LTBR',0,'L',true);
                $this->Ln(8);
            }
            // pintamos el aspecto general de la celda
            $this->printTeamInfo($rowcount,$equipo,$miembros);
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