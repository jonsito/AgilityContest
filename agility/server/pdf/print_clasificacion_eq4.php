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

class PrintClasificacionEq4 extends PrintCommon {
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
        parent::__construct('Landscape',"print_clasificacion_eq4",$prueba,$jornada);
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
        $m=new Equipos("print_clasificacion_Equipos4",$prueba,$jornada);
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
            for ($n=0;$n<4;$n++) {
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

        $jobj=new Jueces("print_Clasificaciones_eq3");
        $juez1=$jobj->selectByID($this->manga1->Juez1);
        $juez2=$jobj->selectByID($this->manga1->Juez2); // asume mismos jueces en dos mangas
        $tm1=Mangas::$tipo_manga[$this->manga1->Tipo][3] . " - " . $this->categoria;
        $tm2=null;
        if ($this->manga2!=null)
            $tm2=Mangas::$tipo_manga[$this->manga2->Tipo][3] . " - " . $this->categoria;

        $this->SetFont('Arial','B',11); // bold 9px
        $this->Cell(80,5,"Jornada: {$this->jornada->Nombre}",0,0,'',false);
        $this->SetFont('Arial','B',9); // bold 9px
        $this->Cell(20,5,"Juez 1:","LT",0,'L',false);
        $n=$juez1['Nombre'];
        $this->Cell(75,5,($n==="-- Sin asignar --")?"":$n,"T",0,'L',false);
        $this->Cell(20,5,"Juez 2:","T",0,'L',false);
        $n=$juez2['Nombre'];
        $this->Cell(80,5,($n==="-- Sin asignar --")?"":$n,"TR",0,'L',false);
        $this->Ln();
        $trs=$this->trs1;
        $this->SetFont('Arial','B',11); // bold 9px
        $this->Cell(80,5,"Fecha: {$this->jornada->Fecha}",0,0,'',false);
        $this->SetFont('Arial','B',9); // bold 9px
        $this->Cell(70,5,$tm1,"LTB",0,'L',false);
        $this->Cell(25,5,"Dist.: {$trs['dist']}m","LTB",0,'L',false);
        $this->Cell(25,5,"Obst.: {$trs['obst']}","LTB",0,'L',false);
        $this->Cell(25,5,"TRS: {$trs['trs']}s","LTB",0,'L',false);
        $this->Cell(25,5,"TRM: {$trs['trm']}s","LTB",0,'L',false);
        $this->Cell(25,5,"Vel.: {$trs['vel']}m/s","LTRB",0,'L',false);
        $this->Ln();
        if ($this->trs2==null) { $this->Ln(); return; }
        $trs=$this->trs2;
        $ronda=Mangas::$tipo_manga[$this->manga1->Tipo][4]; // la misma que la manga 2
        $this->SetFont('Arial','B',11); // bold 9px
        $this->Cell(80,5,"Ronda: $ronda - {$this->categoria}",0,0,'',false);
        $this->SetFont('Arial','B',9); // bold 9px
        $this->Cell(70,5,$tm2,"LTB",0,'L',false);
        $this->Cell(25,5,"Dist.: {$trs['dist']}m","LTB",0,'L',false);
        $this->Cell(25,5,"Obst.: {$trs['obst']}","LTB",0,'L',false);
        $this->Cell(25,5,"TRS: {$trs['trs']}s","LTB",0,'L',false);
        $this->Cell(25,5,"TRM: {$trs['trm']}s","LTB",0,'L',false);
        $this->Cell(25,5,"Vel.: {$trs['vel']}m/s","LTBR",0,'L',false);
        $this->Ln();
    }

    function Header() {
        $this->print_commonHeader(_("Clasificación Final"));
        if ($this->PageNo()==1) $this->print_datosMangas();
    }

    // Pie de página: tampoco cabe
    function Footer() {
        $this->print_commonFooter();
    }

	function printTeamInfo($rowcount,$index,$team,$members) {
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
        $y=55+16*($rowcount);
        $this->SetXY(10,$y);
        // caja de datos de perros
        $this->ac_header(2,16);
        $this->Cell(12,14,1+$index,'LTB',0,'C',true);
        $this->Cell(48,14,"","TBR",0,'C',true);
        $this->SetY($y+1);
        $this->ac_header(2,16);
        foreach($members as $id => $perro) {
            $this->SetX(22);
            $this->ac_row($id,8);
            $this->Cell(6,3,$perro['Dorsal'],'LTBR',0,'L',true);
            $this->SetFont('Arial','B',8);
            $this->Cell(13,3,$perro['Nombre'],'LTBR',0,'C',true);
            $this->SetFont('Arial','',7);
            $this->Cell(28,3,$perro['NombreGuia'],'LTBR',0,'R',true);
            $this->Ln(3);
        }
        // caja de datos del equipo
        $this->SetXY(70,$y);
        $this->ac_header(1,14);
        $this->Cell(130,14,"","LTBR",0,'C',true);
        $this->SetXY(71,$y+1);
        $this->Cell(10,5,$this->Image(__DIR__.'/../../images/logos/'.$logos[0],$this->getX(),$this->getY(),5),"",0,'C',($logos[0]==='null.png')?true:false);
        $this->Cell(10,5,$this->Image(__DIR__.'/../../images/logos/'.$logos[1],$this->getX(),$this->getY(),5),"",0,'C',($logos[1]==='null.png')?true:false);
        $this->Cell(10,5,$this->Image(__DIR__.'/../../images/logos/'.$logos[2],$this->getX(),$this->getY(),5),"",0,'C',($logos[2]==='null.png')?true:false);
        $this->Cell(10,5,$this->Image(__DIR__.'/../../images/logos/'.$logos[3],$this->getX(),$this->getY(),5),"",0,'C',($logos[3]==='null.png')?true:false);
        $this->Cell(80,5,$team['Nombre'],'',0,'R',true);
        $this->Cell(8,5,'','',0,'',true); // empty space at right of page
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
        $this->Cell(28,6,"",'R',0,'L',true);

        $this->ac_SetFillColor("#c0c0c0"); // light gray
        $this->SetXY(71,7+$y+1);
        $this->SetFont('Arial','I',8); // italic 8px
        $this->Cell(15,2.5,"Flt",0,0,'L',false);
        $this->Cell(15,2.5,"Reh",0,0,'L',false);
        $this->Cell(15,2.5,"Toc",0,'L',false);
        $this->Cell(15,2.5,"Elim",0,'L',false);
        $this->Cell(15,2.5,"N.P.",0,'L',false);
        $this->Cell(25,2.5,"Tiempo",0,0,'L',false);
        $this->Cell(28,2.5,"Penaliz.",0,0,'L',false);

        $this->SetXY(71,6+$y+1);
        $this->SetFont('Arial','B',12); // italic 8px
        $this->Cell(15,7,$team['faltas'],0,0,'R',false);
        $this->Cell(15,7,$team['rehuses'],0,0,'R',false);
        $this->Cell(15,7,$team['tocados'],0,0,'R',false);
        $this->Cell(15,7,$team['eliminados'],0,0,'R',false);
        $this->Cell(15,7,$team['nopresentados'],0,0,'R',false);
        $this->Cell(25,7,$team['tiempo'],0,0,'R',false);
        $this->Cell(28,7,$team['penalizacion'],0,0,'R',false);
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
        $index=0;
        $rowcount=0;
        $this->categoria="-";
		foreach($this->equipos as $equipo) {
            $miembros=$equipo['Perros'];
            $num=count($miembros);
            if ($num==0) continue; // skip empty teams
            // 14 teams/page
            if ( ($rowcount%14==0) || ($equipo['Categorias']!=$this->categoria)) {
                $rowcount=0;
                $this->categoria=$equipo['Categorias'];
                $this->AddPage();
            }
            // pintamos el aspecto general de la celda
            $this->printTeamInfo($rowcount,$index,$equipo,$miembros);
            $rowcount++;
            $index++;
		}
		// Línea de cierre
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
    $pdf = new PrintClasificacionEq4($prueba,$jornada,$mangas,$resultados,$mode);
    $pdf->AliasNbPages();
    $pdf->composeTable();
    $pdf->Output("print_clasificacion_eq4.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
    do_log($e->getMessage());
    die ($e->getMessage());
}
?>