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
	protected $mname;

    protected $cell_header=array();
    protected $cell_width=array();
    protected $cell_align=array();
    protected $cell_field=array();
    protected $cell_bars=array();
    protected $cell_htop=array();
    protected $cell_wtop=array();

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
		    $this->trs_data[$n]=null;
		    $k="trs".strval($n+1);
		    if (array_key_exists($k,$results))  $this->trs_data[$n]=$results[$k];
            $this->tipo_mangas[$n]=($mangas[$n]!=0)?_(Mangas::getTipoManga($this->mangas[$n]->Tipo,3,$this->federation)):"";
        }
		$this->categoria=$this->getModeString(intval($mode));
		$this->hasGrades=false; // games has no grades
        // evaluamos array de contenidos
        switch (intval($this->jornada->Tipo_Competicion)) {
            case 1: // penthathlon
                $this->mname='Penthatlon';
                $this->cell_htop=array(
                 _('Competitor data'),'Agility A','Agility B','Jumping A','Jumping B','SpeedStakes',_('Final Scores')
                );
                $this->cell_wtop=array(99,28,28,28,28,28,36 );
                $this->cell_header=array(
                    _('Dorsal'),_('Name'),_('Handler'),$this->strClub,
                    _('Time'),_('Penal'),_('Pos'), // Agility A
                    _('Time'),_('Penal'),_('Pos'), // Agility B
                    _('Time'),_('Penal'),_('Pos'), // Jumping A
                    _('Time'),_('Penal'),_('Pos'), // Jumping B
                    _('Time'),_('Penal'),_('Pos'), // SpeedStakes
                    _('Time'),_('Penalization'),_('Position') // final
                    );
                $this->cell_width=array(
                    10 /* dorsal*/, 20 /* perro */, 44 /*guia*/,25, /*club */
                    11,11,6, /*agility A*/ 11,11,6, /* agility B */
                    11,11,6, /*jumping A*/ 11,11,6, /* jumping B */
                    11,11,6, /*speedstakes*/
                    13,13,10 /*final */
                );
                $this->cell_bars=array(10,89,28,28,28,28,28);
                $this->cell_align=array(
                    'R' /* dorsal*/, 'L' /* perro */, 'L' /*guia*/,'R', /*club */
                    'R','R','C', /*agility A*/ 'R','R','C', /* agility B */
                    'R','R','C', /*jumping A*/ 'R','R','C', /* jumping B */
                    'R','R','C', /*speedstakes*/
                    'R','R','C' /*final */
                );
                $this->cell_field=array(
                    'Dorsal' /* dorsal*/, 'Nombre' /* perro */, 'NombreGuia' /*guia*/,'NombreClub', /*club */
                    'T1','P1','Puesto1', /*agility A*/ 'T2','P2','Puesto2', /* agility B */
                    'T3','P3','Puesto3', /*jumping A*/ 'T4','P4','Puesto4', /* jumping B */
                    'T5','P5','Puesto5', /*speedstakes*/
                    'Tiempo','Penalizacion','Puesto' /*final */
                );
                break;
            case 2: // biathlon
                $this->mname='Biathlon';
                $this->cell_header=array(
                    _('Dorsal'),_('Name'),_('Handler'),$this->strClub,
                    _('Time'),_('Penal'),_('Pts'),_('Pos'), // Agility A
                    _('Time'),_('Penal'),_('Pts'),_('Pos'), // Agility B
                    _('Time'),_('Penal'),_('Pts'),_('Pos'), // Jumping A
                    _('Time'),_('Penal'),_('Pts'),_('Pos'), // Jumping B
                    _('Time'),_('Penalization'),_('Points'),_('Position') // final
                );
                $this->cell_width=array(
                    10 /* dorsal*/, 20 /* perro */, 41 /*guia*/,23, /*club */
                    12,10,6,6, /*agility A*/ 12,10,6,6, /* agility B */
                    12,10,6,6, /*jumping A*/ 12,10,6,6, /* jumping B */
                    13,13,10,10 /*final */
                );
                $this->cell_bars=array(10,84,34,34,34,34);
                $this->cell_htop=array(
                    _('Competitor data'),'Agility A','Agility B','Jumping A','Jumping B',_('Final Scores')
                );
                $this->cell_wtop=array(94,34,34,34,34,46 );
                $this->cell_align=array(
                    'R' /* dorsal*/, 'L' /* perro */, 'L' /*guia*/,'R', /*club */
                    'R','R','R','C', /*agility A*/ 'R','R','R','C', /* agility B */
                    'R','R','R','C', /*jumping A*/ 'R','R','R','C', /* jumping B */
                    'R','R','R','C' /*final */
                );
                $this->cell_field=array(
                    'Dorsal' /* dorsal*/, 'Nombre' /* perro */, 'NombreGuia' /*guia*/,'NombreClub', /*club */
                    'T1','P1','Pt1','Puesto1', /*agility A*/ 'T2','P2','Pt2','Puesto2', /* agility B */
                    'T3','P3','Pt3','Puesto3', /*jumping A*/ 'T4','P4','Pt4','Puesto4', /* jumping B */
                    'Tiempo','Penalizacion','Puntos','Puesto' /*final */
                );
                break;
            case 3: // games
                $this->mname='Games';
                $this->cell_header=array(
                    _('Dorsal'),_('Name'),_('Handler'),$this->strClub,
                    _('Time'),_('Opening'),_('Closing'),_('Points'),_('Pos'), // Snooker
                    _('Time'),_('Opening'),_('Gambler'),_('Points'),_('Pos'), // Gambler
                    _('Total Time'),_('Total Points'),_('Position') // final
                );
                $this->cell_width=array(
                    10 /* dorsal*/, 18 /* perro */, 41 /*guia*/,28, /*club */
                    13,14,14,13,9, /*agility A*/ 13,14,14,13,9, /* agility B */
                    20,20,12 /*final */
                );
                $this->cell_bars=array(10,87,63,63);
                $this->cell_htop=array(
                    _('Competitor data'),'Snooker','Gambler',_('Final Scores')
                );
                $this->cell_wtop=array(97,63,63,52 );
                $this->cell_align=array(
                    'R' /* dorsal*/, 'L' /* perro */, 'L' /*guia*/,'R', /*club */
                    'R','R','R','R','C', /* snooker */
                    'R','R','R','R','C', /* gambler */
                    'R','R','C' /*final */
                );
                $this->cell_field=array(
                    'Dorsal' /* dorsal*/, 'Nombre' /* perro */, 'NombreGuia' /*guia*/,'NombreClub', /*club */
                    'T1','F1','R1','P1','Puesto1', /* snooker*/
                    'T2','F2','R2','P2','Puesto2', /* gambler */
                    'Tiempo','Penalizacion','Puesto' /*final */
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
        $this->Cell(80,6,_('Round').": {$this->mname} - {$this->categoria}",0,0,'',false);

        // ahora los datos de cada manga individual
        $valid=array(); // lista de mangas de las que hay que presentar datos
        switch (intval($this->jornada->Tipo_Competicion)) {
            case 1: $valid=array(0,1,2,3,4); break;
            case 2: $valid=array(0,1,2,3); break;
            case 3: $valid=array(0,1); break;
        }
        $count=0;
        foreach ($valid as $n) {
            if ($this->mangas[$n]==null) continue;
            $trs=$this->trs_data[$n];
            $this->setXY(80,40+7*$count);

            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell(90,7,"","LTB",0,'L',false);// caja vacia
            $this->Cell(20,7,_('Dist').".: {$trs['dist']}m","LTB",0,'L',false);
            $this->Cell(20,7,_('Obst').".: {$trs['obst']}","LTB",0,'L',false);
            $this->Cell(25,7,_('SCT').": {$trs['trs']}s","LTB",0,'L',false);
            $this->Cell(25,7,_('MCT').": {$trs['trm']}s","LTB",0,'L',false);
            $this->Cell(25,7,_('Vel').".: {$trs['vel']}m/s","LTRB",0,'L',false);
            // ahora el nombre de la manga y los jueces
            $nmanga=_(Mangas::getTipoManga($this->mangas[$n]->Tipo,3,$this->federation)) . " - " . $this->categoria;
            $juez1=$jobj->selectByID($this->mangas[$n]->Juez1); $juez2=$jobj->selectByID($this->mangas[$n]->Juez2);
            $this->setXY(81,41+7*$count);
            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell( 88,3.5,$nmanga,"",0,'L',false);
            $this->setXY(81,43.5+7*$count);
            $this->SetFont($this->getFontName(),'I',8); // bold 9px
            $jueces = _('Judge') .": ". $juez1['Nombre'];
            $jueces .= ($juez2['Nombre']==="-- Sin asignar --")? "" : " - {$juez2['Nombre']}";
            $this->Cell( 88,3.5,$jueces,"",0,'R',false);
            $this->Ln();
            $count++;
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
		$this->Ln();
	}

	function Header() {
		$this->print_commonHeader(_("Final scores"));
	}
	
	// Pie de página: tampoco cabe
	function Footer() {
		$this->print_commonFooter();
	}
	
	function writeTableHeader() {
	    $this->ac_header(1,8);
        for($n=0;$n<count($this->cell_htop);$n++) {
            $this->Cell($this->cell_wtop[$n],6,$this->cell_htop[$n],'LTR',0,'C',true);
        }
        $this->Ln(6);
	    for($n=0;$n<count($this->cell_header);$n++) {
	        $this->Cell($this->cell_width[$n],6,$this->cell_header[$n],'LTR',0,$this->cell_align[$n],true);
        }
        // rayas de separacion entre mangas
        $x=10.1;
        for($n=0;$n<count($this->cell_bars);$n++){
            $x+=$this->cell_bars[$n];
            $this->Line($x,$this->GetY(),$x,$this->GetY() + 6);
        }
        $this->Ln(6);
	}
	
	function writeCell($idx,$row) {
        $fill=(($idx%2)!=0)?true:false;
        // contenidos
        for($n=0;$n<count($this->cell_header);$n++) {
            $this->ac_row($idx,8);
            $this->SetFont($this->getFontName(),'',8);
            if ($this->cell_header[$n]==_('Name')) $this->SetFont($this->getFontName(),'B',8);
            if ($this->cell_header[$n]==_('Pos')) $this->SetFont($this->getFontName(),'B',8);
            if ($this->cell_header[$n]==_('Position')) $this->SetFont($this->getFontName(),'B',10);
            $this->Cell($this->cell_width[$n],6,$row[$this->cell_field[$n]],'LTR',0,$this->cell_align[$n],$fill);
        }
        // rayas de separacion entre mangas
        $x=10.2;
        for($n=0;$n<count($this->cell_bars);$n++){
            $x+=$this->cell_bars[$n];
            $this->Line($x,$this->GetY(),$x,$this->GetY() + 6);
        }
        $this->Ln(6);
	}
	
	function composeTable() {
		$this->myLogger->enter();
        $len=array_sum($this->cell_width);

		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont($this->getFontName(),'',8); // default font
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		$this->SetLineWidth(.5);
		
		$rowcount=0;
		$this->AddPage();
		$this->print_datosMangas();
		switch(intval($this->jornada->Tipo_Competicion)) {
            case 1: $numrows=15; break;//penthathlon
            case 2: $numrows=16; break;//biathlon
            case 3: $numrows=18; break;//games
            default:$numrows=22; break; // should not happen
        }
		foreach($this->resultados as $row) {
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
                $numrows=22;
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