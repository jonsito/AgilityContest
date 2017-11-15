<?php
/*
print_clasificacion.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
 * genera un PDF con la clasificacion final individual
 */

require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__."/../fpdf.php");
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Clubes.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jueces.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__.'/../../database/classes/Mangas.php');
require_once(__DIR__.'/../../database/classes/Resultados.php');
require_once(__DIR__.'/../../database/classes/Clasificaciones.php');
require_once(__DIR__."/../print_common.php");

class PrintClasificacionGames extends PrintCommon {

    protected $mangas=array();
    protected $tipo_mangas=array();
    protected $trs_data=array();
	protected $resultados;
	protected $categoria;
	protected $hasGrades;

    protected $cell_headers=array();
    protected $cell_sizes=array();
    protected $cell_align=array();
    protected $cell_fields=array();

	 /** Constructor
      * @param {int} $prueba prueba id
      * @param {int} $jornada jornada id
      * @param {array} $mangas datos de la manga
	  * @param {array} $results resultados asociados a la manga/categoria pedidas
      * @param {int} $mode manga mode
	  * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas,$results,$mode) {
		parent::__construct('Landscape',"print_clasificacion_games",$prueba,$jornada);
		$dbobj=new DBObject("print_clasificacion_games");
		$this->resultados=$results['rows'];
		for($n=0;$n<8;$n++){
		    $this->mangas[$n]=($mangas[$n]!=0)?$dbobj->__getObject("Mangas",$mangas[$n]):null;
		    $this->trs_data[$n]=($mangas[$n]!=0)?$results["trs".strval($n+1)]:null;
            $this->tipo_mangas[$n]=($mangas[$n]!=0)?_(Mangas::getTipoManga($this->mangas[$n]->Tipo,3,$this->federation)):"";
        }
		$this->categoria=$this->getModeString(intval($mode));
		$this->hasGrades=false; // games has no grades
        // evaluamos array de contenidos
        switch (intval($this->jornada->Tipo_Competicion)) {
            case 1: // penthathlon
                $this->cell_headers=array(
                    _('Dorsal'),_('Name'),_('Cat'),_('Handler'),$this->strClub,
                    _('Time'),_('Penal'),_('Vel'),_('Pos'), // Agility A
                    _('Time'),_('Penal'),_('Vel'),_('Pos'), // Agility B
                    _('Time'),_('Penal'),_('Vel'),_('Pos'), // Jumping A
                    _('Time'),_('Penal'),_('Vel'),_('Pos'), // Jumping B
                    _('Time'),_('Penal'),_('Vel'),_('Pos'), // SpeedStakes
                    _('Time'),_('Penalization'),_('Position') // final
                    );
                break;
            case 2: // biathlon
                $this->cell_headers=array(
                    _('Dorsal'),_('Name'),_('Cat'),_('Handler'),$this->strClub,
                    _('Time'),_('Penal'),_('Vel'),_('Pts'),_('Pos'), // Agility A
                    _('Time'),_('Penal'),_('Vel'),_('Pts'),_('Pos'), // Agility B
                    _('Time'),_('Penal'),_('Vel'),_('Pts'),_('Pos'), // Jumping A
                    _('Time'),_('Penal'),_('Vel'),_('Pts'),_('Pos'), // Jumping B
                    _('Time'),_('Penalization'),_('Points'),_('Position') // final
                );
                break;
            case 3: // games
                $this->cell_headers=array(
                    _('Dorsal'),_('Name'),_('Cat'),_('Handler'),$this->strClub,
                    _('Time'),_('Opening'),_('Closing'),_('Points'),_('Pos'), // Snooker
                    _('Time'),_('Opening'),_('Gambler'),_('Points'),_('Pos'), // Gambler
                    _('Total Time'),_('Total Points'),_('Position') // final
                );
                break;
            default:
                throw new Exception("PrintClasificationGames: Invalid Competition type: ".$this->jornada->Tipo_Competicion);
        }
	}
	
	function print_datosMangas() {

	    // objeto para buscar jueces
		$jobj=new Jueces("print_Clasificaciones");

        // imprimimos informacion de la manga
        $this->setXY(10,40);
		$this->SetFont($this->getFontName(),'B',11); // bold 9px
		$this->Cell(80,6,_('Journey').": {$this->jornada->Nombre}",0,0,'',false);
		$this->Ln(6);
        $this->Cell(80,6,_('Date').": {$this->jornada->Fecha}",0,0,'',false);
        $this->Ln(6);
        $ronda=$this->getGradoString(intval($this->mangas[0]->Tipo)); // todas las mangas comparten grado
        $this->Cell(80,6,_('Round').": $ronda - {$this->categoria}",0,0,'',false);

        // ahora los datos de cada manga individual
        for ($n=0;$n<8;$n++) {
            if ($this->mangas[$n]==null) continue;
            $trs=$this->trs_data[$n];
            $this->setXY(80,40+8*$n);

            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell(90,8,"","LTB",0,'L',false);// caja vacia
            $this->Cell(20,8,_('Dist').".: {$trs['dist']}m","LTB",0,'L',false);
            $this->Cell(20,8,_('Obst').".: {$trs['obst']}","LTB",0,'L',false);
            $this->Cell(25,8,_('SCT').": {$trs['trs']}s","LTB",0,'L',false);
            $this->Cell(25,8,_('MCT').": {$trs['trm']}s","LTB",0,'L',false);
            $this->Cell(25,8,_('Vel').".: {$trs['vel']}m/s","LTRB",0,'L',false);
            // ahora el nombre de la manga y los jueces
            $nmanga=_(Mangas::getTipoManga($this->mangas[$n]->Tipo,3,$this->federation)) . " - " . $this->categoria;
            $juez1=$jobj->selectByID($this->mangas[$n]->Juez1); $juez2=$jobj->selectByID($this->mangas[$n]->Juez2);
            $this->setXY(81,41+8*$n);
            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell( 88,4,$nmanga,"",0,'L',false);
            $this->setXY(81,44+8*$n);
            $this->SetFont($this->getFontName(),'I',8); // bold 9px
            $jueces = _('Judge') .": ". $juez1['Nombre'];
            $jueces .= ($juez2['Nombre']==="-- Sin asignar --")? "" : " - {$juez2['Nombre']}";
            $this->Cell( 88,4,$jueces,"",0,'R',false);
        }
        $this->Ln();
	}

	// on second and consecutive pages print a short description to avoid sheet missorder
	function print_datosMangas2() {
		$this->SetXY(35,20);
		$this->SetFont($this->getFontName(),'B',11); // bold 9px
		$this->Cell(80,7,"{$this->jornada->Nombre}",0,0,'',false);
		$this->SetXY(35,25);
		$this->Cell(80,7,"{$this->jornada->Fecha}",0,0,'',false);
		$ronda=_(Mangas::getTipoManga($this->mangas[0]->Tipo,4,$this->federation)); // misma que en todas las mangas
		$this->SetXY(35,30);
		$this->Cell(80,7,"$ronda - {$this->categoria}",0,0,'',false);
	}

	function Header() {
		$this->print_commonHeader(_("Final scores"));
	}
	
	// Pie de página: tampoco cabe
	function Footer() {
		$this->print_commonFooter();
	}
	
	function writeTableHeader() {
	    // to be written
	}
	
	function writeCell($idx,$row) {
        // to be written
	}
	
	function composeTable() {
		$this->myLogger->enter();
        $len=(($this->mangas[0])!==null)?115+(57*3+46)*0.75:115+57*2+46; // lenght of closing line

		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont($this->getFontName(),'',8); // default font
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		$this->SetLineWidth(.3);
		
		$rowcount=0;
		$this->AddPage();
		$this->print_datosMangas();
		foreach($this->resultados as $row) {
			$numrows=($this->PageNo()==1)?18:22;
			if($rowcount==0) $this->writeTableHeader();	
			$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
			$this->writeCell( $rowcount % $numrows,$row);
			$rowcount++;
			if ($rowcount>=$numrows) {
				// pintamos linea de cierre 	
				$this->setX(10);
				$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
				$this->cell($len,0,'','T'); // celda sin altura y con raya
				$this->AddPage();
				$rowcount=0;
			}
		}
		// pintamos linea de cierre final
		$this->setX(10);
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		$this->cell($len,0,'','T'); // celda sin altura y con raya
		$this->myLogger->leave();
	}
}

?>