<?php
/*
print_ordenTandas.php

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
 * genera un pdf con la secuencia ordenada de tandas de la jornada y los participantes de cada tanda
*/

require_once(__DIR__."/fpdf.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Tandas.php');
require_once(__DIR__."/print_common.php");

class PrintTandas extends PrintCommon {

	protected $mode; // orden de tandas
	
	/**
	 * Constructor
	 * @param {integer} $prueba Prueba ID
	 * @param {integer} $jornada Jornada ID
     * @param {integer} $m Print mode. 0:Trs/Trm evaluation calc sheet 1:Trsdata template to enter data
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$m) {
		date_default_timezone_set('Europe/Madrid');
		parent::__construct(($m==0)?'Landscape':'Portrait',"print_Templates",$prueba,$jornada);
		if ( ($prueba<=0) || ($jornada<=0) ) {
			$this->errormsg="printTemplates: either prueba or jornada data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->mode=$m;
	}
	
	// Cabecera de página
	function Header() {
        if ($this->mode==0) {
            $this->ac_header(1,12);
            $this->SetXY(10,10);
            $this->Cell(100,7,'Hoja de Cálculo para el TRS y TRM','LTBR',0,'C',true); // cabecera muy simple :-)
        } else {
            // cabecera comun
            $this->print_commonHeader("Datos de TRS y TRM");
            // pintamos identificacion de la jornada
            $this->SetFont('Arial','B',12); // Arial bold 15pt
            $str  = "Jornada: {$this->jornada->Nombre} - {$this->jornada->Fecha}";
            $this->Cell(100,7,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
            $this->Ln(5);
            $str  = "Hora de comienzo: {$this->jornada->Hora}";
            $this->Cell(90,7,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
            $this->Ln(10);
        }
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
        $this->addPage();
        // TODO: write
		$this->myLogger->leave();
        return "";
	}
}

// Consultamos la base de datos
try {
	$prueba=http_request("Prueba","i",0);
    $jornada=http_request("Jornada","i",0);
    $mode=http_request("Mode","i",0);
	// 	Creamos generador de documento
	$pdf = new PrintTandas($prueba,$jornada,$mode);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("plantilla_datos.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
};
?>