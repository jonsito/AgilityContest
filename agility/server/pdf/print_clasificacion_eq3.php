<?php
/*
print_clasificacion_eq3.php

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
 * genera un CSV con los datos para las etiquetas
 */

require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__."/fpdf.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Clubes.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jueces.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Resultados.php');
require_once(__DIR__.'/../database/classes/Equipos.php');
require_once(__DIR__.'/../database/classes/Clasificaciones.php');
require_once(__DIR__."/print_common.php");

class PrintClasificacionEq3 extends PrintCommon {
	
	protected $manga1;
	protected $manga2;
	protected $resultados;
	protected $trs1;
	protected $trs2;
	protected $categoria;
    protected $equipos;

    protected $defaultPerro = array( // participante por defecto para garantizar que haya 4perros/equipo
        'Dorsal' => '-',
        'Perro' => 0,
        'Nombre' => '-',
        'NombreGuia' => '-',
        'NombreClub' => '-',
        'Licencia' => '-',
        'Categoria' => '-','Grado' => '-',
        'F1' => 0, 'T1' => 0, 'R1' => 0, 'P1' => 0, 'V1' => 0, 'C1' => '',
        'F2' => 0, 'T2' => 0, 'R2' => 0, 'P2' => 0, 'V2' => 0, 'C2' => '',
        'Tiempo' => '0.0',
        'Velocidad' => '0.0',
        'Penalizacion' => 400.0,
        'Calificacion' => '-',
        'CShort' => '-',
        'Puesto' => '-'
    );

	 /** Constructor
      * @param {int} $prueba prueba id
      * @param {int} $jornada jornada id
	 * @param {array} $mangas datos de la manga
	 * @param {array} $results resultados asociados a la manga/categoria pedidas
      * @param {int} $mode manga mode
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas,$results,$mode) {
		parent::__construct('Landscape',"print_clasificacion_eq3",$prueba,$jornada);
		$dbobj=new DBObject("print_clasificacion");
		$this->manga1=$dbobj->__getObject("Mangas",$mangas[0]);
		$this->manga2=null;
		if ($mangas[1]!=0) $this->manga2=$dbobj->__getObject("Mangas",$mangas[1]);
		$this->resultados=$results['rows'];
		$this->trs1=$results['trs1'];
		$this->trs2=null;
		if ($mangas[1]!=0) $this->trs2=$results['trs2'];
		$this->categoria=$this->getModeString(intval($mode));

        // Datos de equipos de la jornada
        $m=new Equipos("print_clasificacion_Equipos3",$prueba,$jornada);
        $teams=$m->getTeamsByJornada();
        // reindexamos por ID y anyadimos un campo extra con el array de resultados de cada manga
        $this->equipos=array();
        foreach ($teams as &$equipo) {
            $equipo['Resultados1']=array();
            $equipo['T1']=0.0;
            $equipo['P1']=0.0;
            $equipo['Resultados2']=array();
            $equipo['T2']=0.0;
            $equipo['P2']=0.0;
            $equipo['Tiempo']=0.0;
            $equipo['Penalizacion']=0.0;
            $equipo['Perros']=array();
            $this->equipos[$equipo['ID']]=$equipo;
        }
        // now fill team members array.
        foreach($this->resultados as &$result) {
            $teamid=&$result['Equipo'];
            $equipo=&$this->equipos[$teamid];
            array_push($equipo['Resultados1'],array( 'T' => $result['T1'], 'P'=> $result['P1']));
            array_push($equipo['Resultados2'],array( 'T' => $result['T2'], 'P'=> $result['P2']));
            array_push($equipo['Perros'],$result);
        }
        // sort results on each manga
        // and evaluate results and penalization by adding first 3 results
        foreach($this->equipos as &$team) {
            // finally sort equipos by result instead of id
            usort($team['Resultados1'],function($a,$b){
                return ($a['P']==$b['P'])? ($a['T']-$b['T']): ($a['P']-$b['P']);
            });
            usort($team['Resultados2'],function($a,$b){
                return ($a['P']==$b['P'])? ($a['T']-$b['T']): ($a['P']-$b['P']);
            });
            // compose manga team's result
            for ($n=0;$n<3;$n++) {
                // TODO: si no hay participantes en el equipo, ignora
                if (array_key_exists($n,$team['Resultados1'])) {
                    $team['P1']+=$team['Resultados1'][$n]['P'];
                    $team['T1']+=$team['Resultados1'][$n]['T'];
                } else  $team['P1']+=200.0;
                if (array_key_exists($n,$team['Resultados2'])) {
                    $team['P2']+=$team['Resultados2'][$n]['P'];
                    $team['T2']+=$team['Resultados2'][$n]['T'];
                } else  $team['P2']+=200.0;
            }
            // and evaluate final team's results
            $team['Penalizacion']=$team['P1']+$team['P2'];
            $team['Tiempo']=$team['T1']+$team['T2'];
        }
        // finalmente ordenamos los equipos en funcion de la clasificacion
        usort($this->equipos,function($a,$b){
            return ($a['Penalizacion']==$b['Penalizacion'])? ($a['Tiempo']-$b['Tiempo']): ($a['Penalizacion']-$b['Penalizacion']);
        });
	}
	
	function print_datosMangas() {
		$this->setXY(10,40);
		$this->SetFont($this->getFontName(),'B',9); // bold 9px
		
		$jobj=new Jueces("print_Clasificaciones_eq3");
		$juez1=$jobj->selectByID($this->manga1->Juez1);
		$juez2=$jobj->selectByID($this->manga1->Juez2); // asume mismos jueces en dos mangas
		$tm1=Mangas::$tipo_manga[$this->manga1->Tipo][3] . " - " . $this->categoria;
		$tm2=null;
		if ($this->manga2!=null)
			$tm2=Mangas::$tipo_manga[$this->manga2->Tipo][3] . " - " . $this->categoria;

		$this->SetFont($this->getFontName(),'B',11); // bold 9px
		$this->Cell(80,5,_('Journey').": {$this->jornada->Nombre}",0,0,'',false);
		$this->SetFont($this->getFontName(),'B',9); // bold 9px
		$this->Cell(20,5,_('Judge')." 1:","LT",0,'L',false);
		$n=$juez1['Nombre'];
		$this->Cell(75,5,($n==="-- Sin asignar --")?"":$n,"T",0,'L',false);
		$this->Cell(20,5,_('Judge')." 2:","T",0,'L',false);
		$n=$juez2['Nombre'];
		$this->Cell(80,5,($n==="-- Sin asignar --")?"":$n,"TR",0,'L',false);
		$this->Ln();
		$trs=$this->trs1;
		$this->SetFont($this->getFontName(),'B',11); // bold 9px
		$this->Cell(80,5,_('Date').": {$this->jornada->Fecha}",0,0,'',false);
		$this->SetFont($this->getFontName(),'B',9); // bold 9px
		$this->Cell(70,5,$tm1,"LTB",0,'L',false);
		$this->Cell(25,5,_('Dist').".: {$trs['dist']}m","LTB",0,'L',false);
		$this->Cell(25,5,_('Obst').".: {$trs['obst']}","LTB",0,'L',false);
		$this->Cell(25,5,_('SCT').": {$trs['trs']}s","LTB",0,'L',false);
		$this->Cell(25,5,_('MCT').": {$trs['trm']}s","LTB",0,'L',false);
		$this->Cell(25,5,_('Vel').".: {$trs['vel']}m/s","LTRB",0,'L',false);
		$this->Ln();
		if ($this->trs2==null) { $this->Ln(); return; }
		$trs=$this->trs2;
		$ronda=$this->getGradoString(intval($this->manga1->Tipo));
		$this->SetFont($this->getFontName(),'B',11); // bold 9px
		$this->Cell(80,5,_('Round').": $ronda - {$this->categoria}",0,0,'',false);
		$this->SetFont($this->getFontName(),'B',9); // bold 9px
		$this->Cell(70,5,$tm2,"LTB",0,'L',false);
		$this->Cell(25,5,_('Dist').".: {$trs['dist']}m","LTB",0,'L',false);
		$this->Cell(25,5,_('Obst').".: {$trs['obst']}","LTB",0,'L',false);
		$this->Cell(25,5,_('SCT').": {$trs['trs']}s","LTB",0,'L',false);
		$this->Cell(25,5,_('MCT').": {$trs['trm']}s","LTB",0,'L',false);
		$this->Cell(25,5,_('Vel').".: {$trs['vel']}m/s","LTBR",0,'L',false);
		$this->Ln();
	}

	// on second and consecutive pages print a short description to avoid sheet missorder
	function print_datosMangas2() {
		$this->SetXY(35,15);
		$this->SetFont($this->getFontName(),'B',11); // bold 9px
		$this->Cell(80,7,"{$this->jornada->Nombre}",0,0,'',false);
		$this->SetXY(35,20);
		$this->Cell(80,7,"{$this->jornada->Fecha}",0,0,'',false);
		$ronda=Mangas::$tipo_manga[$this->manga1->Tipo][4]; // la misma que la manga 2
		$this->SetXY(35,25);
		$this->Cell(80,7,"$ronda - {$this->categoria}",0,0,'',false);
	}

	function Header() {
		$this->print_commonHeader(_("Final scores"));
        if ($this->PageNo()==1) $this->print_datosMangas();
		else $this->print_datosMangas2();
	}
	
	// Pie de página: tampoco cabe
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
            for ($n=0;$n<count($team['Perros']);$n++) {
                $miembro=$team['Perros'][$n]['Perro'];
                $logo=$this->getLogoName(intval($miembro));
                if ( ( ! in_array($logo,$logos) ) && ($count<4) ) $logos[$count++]=$logo;
            }
        }
        $offset=($this->PageNo()==1)?60:40;
        $this->SetXY(10,$offset+33*($teamcount%$numrows));
        $this->ac_header(1,18);
		$this->Cell(15,8,strval(1+$teamcount)." -",'LT',0,'C',true); // imprime puesto del equipo
		$x=$this->getX();
		$y=$this->getY();
		for ($n=0;$n<4;$n++) {
			if ($logos[$n]==="null.png") {
				$this->SetX($x+10*$n);
				$this->Cell(10,8,"",'T',0,'C',true);
			} else {
				$this->Image($logos[$n],$x+10*$n,$y,8);
			}
		}
		$this->SetX($x+40);
        $this->Cell(212,8,$team['Nombre'],'T',0,'R',true);
        $this->Cell(8,8,'','TR',0,'R',true); // empty space at right of page
        $this->Ln();
    }

    function writeTableHeader() {
		$wide=$this->federation->get('WideLicense');
		$tm1=Mangas::$tipo_manga[$this->manga1->Tipo][3];
		$tm2=null;
		if ($this->manga2!=null) $tm2=Mangas::$tipo_manga[$this->manga2->Tipo][3];
		
		$this->ac_header(2,8);
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		// first row of table header
		$this->SetFont($this->getFontName(),'BI',10); // default font
        $this->Cell( ($wide)?110:95,4,_('Competitor data'),'L',0,'L',true);
		$this->Cell(47,4,$tm1,0,0,'C',true);
		$this->Cell(47,4,$tm2,0,0,'C',true);
        $this->Cell(34,4,_('Scr. Individual'),'0',0,'C',true);
        $this->Cell(($wide)?37:52,4,_('Scr. Teams'),'R',0,'C',true);
		$this->ln();
		$this->SetFont($this->getFontName(),'',8); // default font
		// datos del participante
		$this->Cell(8,4,_('Dorsal'),'BL',0,'C',true); 	// dorsal
		$this->Cell(20,4,_('Name'),'B',0,'C',true);	// nombre (20,y
		$this->Cell(($wide)?28:13,4,_('Lic'),'B',0,'C',true);	// licencia
		$this->Cell(8,4,_('Cat'),'B',0,'C',true);	// categoria ( en equipos no se considera el grado )
		$this->Cell(30,4,_('Handler'),'B',0,'C',true);	// nombreGuia
		$this->Cell(16,4,$this->strClub,'B',0,'C',true);	// nombreClub
		// manga 1
		$this->Cell(5,4,_('F/T'),'B',0,'C',true);	// 1- Faltas+Tocados
		$this->Cell(5,4,_('Ref'),'B',0,'C',true);	// 1- Rehuses
		$this->Cell(10,4,_('Time'),'B',0,'C',true);	// 1- Tiempo
		$this->Cell(7,4,_('Vel'),'B',0,'C',true);	// 1- Velocidad
		$this->Cell(10,4,_('Penal'),'B',0,'C',true);	// 1- Penalizacion
		$this->Cell(10,4,_('Calif'),'B',0,'C',true);	// 1- calificacion
		// manga 2
		if ($this->manga2!=null) {
			$this->Cell(5,4,_('F/T'),'B',0,'C',true);	// 2- Faltas+Tocados
			$this->Cell(5,4,_('Ref'),'B',0,'C',true);	// 2- Rehuses
			$this->Cell(10,4,_('Time'),'B',0,'C',true);	// 2- Tiempo
			$this->Cell(7,4,_('Vel'),'B',0,'C',true);	// 2- Velocidad
			$this->Cell(10,4,_('Penal'),'B',0,'C',true);	// 2- Penalizacion
			$this->Cell(10,4,_('Calif'),'B',0,'C',true);	// 2- calificacion
		} else {
			$this->Cell(59,4,'','B',0,'C',true);	// espacio en blanco
		}
		// global individual
		$this->Cell(9,4,_('Time'),'B',0,'C',true);	// Tiempo total
		$this->Cell(9,4,_('Penaliz'),'B',0,'C',true);	// Penalizacion
		$this->Cell(9,4,_('Calific'),'B',0,'C',true);	// Calificacion
		$this->Cell(7,4,_('Position'),'B',0,'C',true);	// Puesto
        // global equipos
        $this->Cell(($wide)?17:22,4,_('Round'),'B',0,'C',true);	// Puesto
        $this->Cell(($wide)?10:15,4,_('Time'),'B',0,'C',true);	// Puesto
        $this->Cell(($wide)?10:15,4,_('Penaliz'),'BR',0,'C',true);	// Puesto
		$this->Ln();
	}
	
	function writeCell($idx,$row,$team) {
		$wide=$this->federation->get('WideLicense');
        $this->ac_row($idx,8);
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		// fomateamos datos
		$puesto= ($row['Penalizacion']>=200)? "-":"{$row['Puesto']}º";
		$penal=number_format($row['Penalizacion'],$this->timeResolution);
		$v1= ($row['P1']>=200)?"-":number_format($row['V1'],1);
		$t1= ($row['P1']>=200)?"-":number_format($row['T1'],$this->timeResolution);
		$p1=number_format($row['P1'],$this->timeResolution);
		$v2= ($row['P2']>=200)?"-":number_format($row['V2'],1);
		$t2= ($row['P2']>=200)?"-":number_format($row['T2'],$this->timeResolution);
		$p2=number_format($row['P2'],$this->timeResolution);
		
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)

		$this->SetFont($this->getFontName(),'',8); // default font
		// datos del participante
		$this->Cell(8,4,$row['Dorsal'],'L',0,'L',true); 	// dorsal
		$this->SetFont($this->getFontName(),'B',8); // Display Nombre in bold typeface
		$this->Cell(20,4,$row['Nombre'],0,0,'L',true);	// nombre (20,y
		$this->SetFont($this->getFontName(),'',($wide)?6:8); // default font for licencia
		$this->Cell(($wide)?28:13,4,$row['Licencia'],0,0,'C',true);	// licencia
        $this->SetFont($this->getFontName(),'',8); // default font
		$this->Cell(8,4,"{$row['Categoria']}",0,0,'C',true);	// categoria/grado
		$this->Cell(30,4,$row['NombreGuia'],0,0,'R',true);	// nombreGuia
		$this->Cell(16,4,$row['NombreClub'],0,0,'R',true);	// nombreClub
		// manga 1
		$this->Cell(5,4,$row['F1'],'L',0,'C',true);	// 1- Faltas+Tocados
		$this->Cell(5,4,$row['R1'],0,0,'C',true);	// 1- Rehuses
		$this->Cell(10,4,$t1,0,0,'C',true);	// 1- Tiempo
		$this->Cell(7,4,$v1,0,0,'C',true);	// 1- Velocidad
		$this->Cell(10,4,$p1,0,0,'C',true);	// 1- Penalizacion
		$this->Cell(10,4,$row['C1'],0,0,'C',true);	// 1- calificacion
		// manga 2
		if ($this->manga2!=null) {
			$this->Cell(5,4,$row['F2'],'L',0,'C',true);	// 2- Faltas+Tocados
			$this->Cell(5,4,$row['R2'],0,0,'C',true);	// 2- Rehuses
			$this->Cell(10,4,$t2,0,0,'C',true);	// 2- Tiempo
			$this->Cell(7,4,$v2,0,0,'C',true);	// 2- Velocidad
			$this->Cell(10,4,$p2,0,0,'C',true);	// 2- Penalizacion
			$this->Cell(10,4,$row['C2'],0,0,'C',true);	// 2- calificacion
		} else {
			$this->Cell(47,4,'','L',0,'C',true);	// espacio en blanco
		}
		// global
		$this->Cell(9,4,number_format($row['Tiempo'],$this->timeResolution),'L',0,'C',true);	// Tiempo
		$this->Cell(9,4,number_format($penal,$this->timeResolution),0,0,'C',true);	// Penalizacion
		$this->Cell(9,4,$row['Calificacion'],0,0,'C',true);	// Calificacion
		$this->SetFont($this->getFontName(),'B',8); // mark "puesto" in bold typeface
		$this->Cell(7,4,$puesto,'R',0,'C',true);	// Puesto
        // equipos
        $this->ac_header(2,8);
        switch($idx){
            case 0: // manga 1
                $this->SetFont($this->getFontName(),'BI',8); // default font
                $this->Cell(($wide)?17:22,4,Mangas::$tipo_manga[$this->manga1->Tipo][3],0,0,'L',true);	// nombre manga 1
                $this->SetFont($this->getFontName(),'',8); // default font
                $this->Cell(($wide)?10:15,4,number_format($team['T1'],$this->timeResolution),0,0,'R',true);	// tiempo manga 1
                $this->Cell(($wide)?10:15,4,number_format($team['P1'],$this->timeResolution),'R',0,'R',true);	// penalizacion manga 1
                break;
            case 1: // manga 2
                $this->SetFont($this->getFontName(),'BI',8); // default font
                $this->Cell(($wide)?17:22,4,Mangas::$tipo_manga[$this->manga2->Tipo][3],0,0,'L',true);	// nombre manga 2
                $this->SetFont($this->getFontName(),'',8); // default font
                $this->Cell(($wide)?10:15,4,number_format($team['T2'],$this->timeResolution),0,0,'R',true);	// tiempo manga 2
                $this->SetFont($this->getFontName(),'',8); // default font
                $this->Cell(($wide)?10:15,4,number_format($team['P2'],$this->timeResolution),'R',0,'R',true);	// penalizacion manga 2
                break;
            case 2: // global
                $this->SetFont($this->getFontName(),'BI',8); // default font
                $this->Cell(($wide)?17:22,4,"Final",'B',0,'L',true);
                $this->SetFont($this->getFontName(),'B',8); // default font
                $this->Cell(($wide)?10:15,4,number_format($team['Tiempo'],$this->timeResolution),'B',0,'R',true);	// tiempo final
                $this->Cell(($wide)?10:15,4,number_format($team['Penalizacion'],$this->timeResolution),'RB',0,'R',true);	// penalizacion final
                break;
        }
		$this->Ln();
	}

    // Tabla coloreada
    function composeTable() {
        $this->myLogger->enter();

        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
        $this->SetLineWidth(.3);

        // Datos
        $teamcount=0;

        foreach($this->equipos as $equipo) {
            $numrows=4; // no space for 5 teams/page
            // $numrows=($this->PageNo()==1)?4:4;
            // si el equipo no tiene participantes es que la categoria no es válida: skip
            if (count($equipo['Perros'])==0) continue;
            // REMINDER: $this->cell( width, height, data, borders, where, align, fill)
            if( ($teamcount%$numrows) == 0 ) { // assume 40mmts/team)
                $this->addPage();
            }
            // evaluate puesto del equipo
            // $this->myLogger->trace("imprimiendo datos del equipo {$equipo['ID']} - {$equipo['Nombre']}");
            $this->printTeamInformation($teamcount,$numrows,$equipo);
            $this->writeTableHeader();
            // print team header/data
            for ($n=0;$n<4;$n++) {
                // con independencia de los perros del equipo imprimiremos siempre 4 columnas
                $row=$this->defaultPerro;
                if (array_key_exists($n,$equipo['Perros'])) $row=$equipo['Perros'][$n];
                // print team member's result
                // $this->myLogger->trace("imprimiendo datos del perro {$row['Perro']} - {$row['Nombre']}");
                $this->writeCell($n,$row,$equipo);
            }
            $teamcount++;
        }
        $this->myLogger->leave();
    }
}

try {
	$result=null;
	$mangas=array();
	$prueba=http_request("Prueba","i",0);
	$jornada=http_request("Jornada","i",0);
	$rondas=http_request("Rondas","i","0"); // bitfield of 512:Esp 256:KO 128:Eq4 64:Eq3 32:Opn 16:G3 8:G2 4:G1 2:Pre2 1:Pre1
	$mangas[0]=http_request("Manga1","i",0); // single manga
	$mangas[1]=http_request("Manga2","i",0); // mangas a dos vueltas
	$mangas[2]=http_request("Manga3","i",0);
	$mangas[3]=http_request("Manga4","i",0); // 1,2:GII 3,4:GIII
	$mangas[4]=http_request("Manga5","i",0);
	$mangas[5]=http_request("Manga6","i",0);
	$mangas[6]=http_request("Manga7","i",0);
	$mangas[7]=http_request("Manga8","i",0);
	$mangas[8]=http_request("Manga9","i",0); // mangas 3..9 are used in KO rondas
	$mode=http_request("Mode","i","0"); // 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
	$c= new Clasificaciones("print_clasificacion_pdf",$prueba,$jornada);
	$resultados=$c->clasificacionFinal($rondas,$mangas,$mode);

	// Creamos generador de documento
	$pdf = new PrintClasificacionEq3($prueba,$jornada,$mangas,$resultados,$mode);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("print_clasificacion_eq3.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}
?>