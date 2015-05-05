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
        'Nombre' => 'No inscrito',
        'NombreGuia' => 'No inscrito',
        'NombreClub' => 'No inscrito',
        'Licencia' => '-',
        'Categoria' => '-','Grado' => '-',
        'F1' => 0, 'T1' => 0, 'R1' => 0, 'P1' => 0, 'V1' => 0, 'C1' => '',
        'F2' => 0, 'T2' => 0, 'R2' => 0, 'P2' => 0, 'V2' => 0, 'C2' => '',
        'Tiempo' => '0.0',
        'Velocidad' => '0.0',
        'Penalizacion' => 400.0,
        'Calificacion' => 'No inscrito',
        'CShort' => 'No inscrito',
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
		parent::__construct('Landscape',"print_clasificacion",$prueba,$jornada);
		$dbobj=new DBObject("print_clasificacion");
		$this->manga1=$dbobj->__getObject("Mangas",$mangas[0]);
		$this->manga2=null;
		if ($mangas[1]!=0) $this->manga2=$dbobj->__getObject("Mangas",$mangas[1]);
		$this->resultados=$results['rows'];
		$this->trs1=$results['trs1'];
		$this->trs2=null;
		if ($mangas[1]!=0) $this->trs2=$results['trs2'];
		$this->categoria = Mangas::$manga_modes[$mode][0];

        // Datos de equipos de la jornada
        $m=new Equipos("print_resultadosEquipos3",$prueba,$jornada);
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
            $teamid=$result['Equipo'];
            $equipo=&$this->equipos[$teamid];
            array_push($equipo['Resultados1'],array( 'T' => $result['T1'], 'P'=> $result['P1']));
            array_push($equipo['Resultados2'],array( 'T' => $result['T2'], 'P'=> $result['P2']));
            array_push($equipo['Perros'],$result);
        }
        // sort results on each manga
        // and evaluate results and penalization by adding first 3 results
        foreach($this->equipos as $team) {
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
		$this->SetFont('Arial','B',9); // bold 9px
		
		$jobj=new Jueces("print_Clasificaciones");
		$juez1=$jobj->selectByID($this->manga1->Juez1);
		$juez2=$jobj->selectByID($this->manga1->Juez2); // asume mismos jueces en dos mangas
		$tm1=Mangas::$tipo_manga[$this->manga1->Tipo][3] . " - " . $this->categoria;
		$tm2=null;
		if ($this->manga2!=null)
			$tm2=Mangas::$tipo_manga[$this->manga2->Tipo][3] . " - " . $this->categoria;

		$this->SetFont('Arial','B',11); // bold 9px
		$this->Cell(80,7,"Jornada: {$this->jornada->Nombre}",0,0,'',false);
		$this->SetFont('Arial','B',9); // bold 9px
		$this->Cell(20,7,"Juez 1:","LT",0,'L',false);
		$n=$juez1['Nombre'];
		$this->Cell(75,7,($n==="-- Sin asignar --")?"":$n,"T",0,'L',false);
		$this->Cell(20,7,"Juez 2:","T",0,'L',false);
		$n=$juez2['Nombre'];
		$this->Cell(80,7,($n==="-- Sin asignar --")?"":$n,"TR",0,'L',false);
		$this->Ln();
		$trs=$this->trs1;
		$this->SetFont('Arial','B',11); // bold 9px
		$this->Cell(80,7,"Fecha: {$this->jornada->Fecha}",0,0,'',false);
		$this->SetFont('Arial','B',9); // bold 9px
		$this->Cell(70,7,$tm1,"LTB",0,'L',false);
		$this->Cell(25,7,"Dist.: {$trs['dist']}m","LTB",0,'L',false);
		$this->Cell(25,7,"Obst.: {$trs['obst']}","LTB",0,'L',false);
		$this->Cell(25,7,"TRS: {$trs['trs']}s","LTB",0,'L',false);
		$this->Cell(25,7,"TRM: {$trs['trm']}s","LTB",0,'L',false);
		$this->Cell(25,7,"Vel.: {$trs['vel']}m/s","LTRB",0,'L',false);
		$this->Ln();
		if ($this->trs2==null) { $this->Ln(); return; }
		$trs=$this->trs2;
		$ronda=Mangas::$tipo_manga[$this->manga1->Tipo][4]; // la misma que la manga 2
		$this->SetFont('Arial','B',11); // bold 9px
		$this->Cell(80,7,"Ronda: $ronda - {$this->categoria}",0,0,'',false);
		$this->SetFont('Arial','B',9); // bold 9px
		$this->Cell(70,7,$tm2,"LTB",0,'L',false);
		$this->Cell(25,7,"Dist.: {$trs['dist']}m","LTB",0,'L',false);
		$this->Cell(25,7,"Obst.: {$trs['obst']}","LTB",0,'L',false);
		$this->Cell(25,7,"TRS: {$trs['trs']}s","LTB",0,'L',false);
		$this->Cell(25,7,"TRM: {$trs['trm']}s","LTB",0,'L',false);
		$this->Cell(25,7,"Vel.: {$trs['vel']}m/s","LTBR",0,'L',false);
		$this->Ln();
	}
	
	function Header() {
		$this->print_commonHeader(_("Clasificación Final"));
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
    }

    function writeTableHeader() {
		$tm1=Mangas::$tipo_manga[$this->manga1->Tipo][3];
		$tm2=null;
		if ($this->manga2!=null) $tm2=Mangas::$tipo_manga[$this->manga2->Tipo][3];
		
		$this->ac_header(2,8);
		
		$this->SetXY(10,($this->PageNo()==1)?65:40); // first page has 3 extra header lines
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		// first row of table header
		$this->SetFont('Arial','BI',12); // default font
		$this->Cell(115,5,'Datos del participante',0,0,'L',true);
		$this->Cell(59,5,$tm1,0,0,'C',true);
		$this->Cell(59,5,$tm2,0,0,'C',true);
		$this->Cell(42,5,'Clasificación',0,0,'C',true);
		$this->ln();
		$this->SetFont('Arial','',8); // default font
		// datos del participante
		$this->Cell(10,5,'Dorsal',0,0,'C',true); 	// dorsal
		$this->Cell(25,5,'Nombre',0,0,'C',true);	// nombre (20,y
		$this->Cell(15,5,'Lic.',0,0,'C',true);	// licencia
		$this->Cell(10,5,'Cat./Gr.',0,0,'C',true);	// categoria/grado
		$this->Cell(35,5,'Guía',0,0,'C',true);	// nombreGuia
		$this->Cell(20,5,'Club',0,0,'C',true);	// nombreClub
		// manga 1
		$this->Cell(7,5,'F/T',0,0,'C',true);	// 1- Faltas+Tocados
		$this->Cell(7,5,'Reh',0,0,'C',true);	// 1- Rehuses
		$this->Cell(12,5,'Tiempo',0,0,'C',true);	// 1- Tiempo
		$this->Cell(9,5,'Vel.',0,0,'C',true);	// 1- Velocidad
		$this->Cell(12,5,'Penal',0,0,'C',true);	// 1- Penalizacion
		$this->Cell(12,5,'Calif',0,0,'C',true);	// 1- calificacion
		// manga 2
		if ($this->manga2!=null) {
			$this->Cell(7,5,'F/T',0,0,'C',true);	// 2- Faltas+Tocados
			$this->Cell(7,5,'Reh',0,0,'C',true);	// 2- Rehuses
			$this->Cell(12,5,'Tiempo',0,0,'C',true);	// 2- Tiempo
			$this->Cell(9,5,'Vel.',0,0,'C',true);	// 2- Velocidad
			$this->Cell(12,5,'Penal',0,0,'C',true);	// 2- Penalizacion
			$this->Cell(12,5,'Calif',0,0,'C',true);	// 2- calificacion
		} else {
			$this->Cell(59,5,'',0,0,'C',true);	// espacio en blanco
		}
		// global
		$this->Cell(12,5,'Tiempo.',0,0,'C',true);	// Tiempo total
		$this->Cell(12,5,'Penaliz.',0,0,'C',true);	// Penalizacion
		$this->Cell(9,5,'Calific.',0,0,'C',true);	// Calificacion
		$this->Cell(9,5,'Puesto',0,0,'C',true);	// Puesto
		$this->Ln();	
		// restore colors
		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
        $this->ac_row(2,9);
	}
	
	function writeCell($idx,$row) {
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		$offset=($this->PageNo()==1)?80:55;
		$this->SetXY(10, $offset + 6*$idx ); // first page has 3 extra header lines
		$fill=(($idx%2)!=0)?true:false;
		
		// fomateamos datos
		$puesto= ($row['Penalizacion']>=200)? "-":"{$row['Puesto']}º";
		$penal=number_format($row['Penalizacion'],2);
		$v1= ($row['P1']>=200)?"-":number_format($row['V1'],1);
		$t1= ($row['P1']>=200)?"-":number_format($row['T1'],2);
		$p1=number_format($row['P1'],2);
		$v2= ($row['P2']>=200)?"-":number_format($row['V2'],1);
		$t2= ($row['P2']>=200)?"-":number_format($row['T2'],2);
		$p2=number_format($row['P2'],2);
		
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)

		$this->SetFont('Arial','',8); // default font
		// datos del participante
		$this->Cell(10,5,$row['Dorsal'],0,0,'L',$fill); 	// dorsal
		$this->SetFont('Arial','B',8); // Display Nombre in bold typeface
		$this->Cell(25,5,$row['Nombre'],0,0,'L',$fill);	// nombre (20,y
		$this->SetFont('Arial','',8); // default font
		$this->Cell(15,5,$row['Licencia'],0,0,'C',$fill);	// licencia
		$this->Cell(10,5,"{$row['Categoria']} {$row['Grado']}",0,0,'C',$fill);	// categoria/grado
		$this->Cell(35,5,$row['NombreGuia'],0,0,'R',$fill);	// nombreGuia
		$this->Cell(20,5,$row['NombreClub'],0,0,'R',$fill);	// nombreClub
		// manga 1
		$this->Cell(7,5,$row['F1'],0,0,'C',$fill);	// 1- Faltas+Tocados
		$this->Cell(7,5,$row['R1'],0,0,'C',$fill);	// 1- Rehuses
		$this->Cell(12,5,$t1,0,0,'C',$fill);	// 1- Tiempo
		$this->Cell(9,5,$v1,0,0,'C',$fill);	// 1- Velocidad
		$this->Cell(12,5,$p1,0,0,'C',$fill);	// 1- Penalizacion
		$this->Cell(12,5,$row['C1'],0,0,'C',$fill);	// 1- calificacion
		// manga 2
		if ($this->manga2!=null) {
			$this->Cell(7,5,$row['F2'],0,0,'C',$fill);	// 2- Faltas+Tocados
			$this->Cell(7,5,$row['R2'],0,0,'C',$fill);	// 2- Rehuses
			$this->Cell(12,5,$t2,0,0,'C',$fill);	// 2- Tiempo
			$this->Cell(9,5,$v2,0,0,'C',$fill);	// 2- Velocidad
			$this->Cell(12,5,$p2,0,0,'C',$fill);	// 2- Penalizacion
			$this->Cell(12,5,$row['C2'],0,0,'C',$fill);	// 2- calificacion
		} else {
			$this->Cell(59,5,'',0,0,'C',$fill);	// espacio en blanco
		}
		// global
		$this->Cell(11,5,number_format($row['Tiempo'],2),0,0,'C',$fill);	// Tiempo
		$this->Cell(11,5,$penal,0,0,'C',$fill);	// Penalizacion
		$this->Cell(11,5,$row['Calificacion'],0,0,'C',$fill);	// Calificacion
		$this->SetFont('Arial','B',10); // default font
		$this->Cell(9,5,$puesto,0,0,'C',$fill);	// Puesto
		// lineas rojas
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->Line(10,$offset + 5*$idx,10,$offset + 5*($idx+1));
		$this->Line(10+115,$offset + 5*$idx,10+115,$offset + 5*($idx+1));
		$this->Line(10+174,$offset + 5*$idx,10+174,$offset + 5*($idx+1));
		$this->Line(10+233,$offset + 5*$idx,10+233,$offset + 5*($idx+1));
		$this->Line(10+275,$offset + 5*$idx,10+275,$offset + 5*($idx+1));
		
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

                $this->myLogger->trace("imprimiendo datos del perro {$row['Perro']} - {$row['Nombre']}");
                $this->writeCell($n,$row);
                $fill = ! $fill;
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