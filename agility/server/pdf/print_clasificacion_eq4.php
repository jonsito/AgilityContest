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
            $equipo['F1']=0;
            $equipo['R1']=0;
            $equipo['E1']=0;
            $equipo['N1']=0;
            $equipo['T1']=0.0;
            $equipo['P1']=0.0;
            $equipo['Resultados2']=array();
            $equipo['F2']=0;
            $equipo['R2']=0;
            $equipo['E2']=0;
            $equipo['N2']=0;
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
            $equipo['F1']+=$result['F1'];
            $equipo['F2']+=$result['F2'];
            $equipo['R1']+=$result['R1'];
            $equipo['R2']+=$result['R2'];
            $equipo['E1']+=$result['E1'];
            $equipo['E2']+=$result['E2'];
            $equipo['N1']+=$result['N1'];
            $equipo['N2']+=$result['N2'];
            array_push($equipo['Resultados1'],array( 'T' => $result['T1'], 'P'=> $result['P1']));
            array_push($equipo['Resultados2'],array( 'T' => $result['T2'], 'P'=> $result['P2']));
            array_push($equipo['Perros'],$result);
        }
        // Evaluate results and penalization by adding first 4 entries
        foreach($this->equipos as &$team) {
            // compose manga team's result. no need to sort
            for ($n=0;$n<4;$n++) {
                // TODO: si no hay participantes en el equipo, ignora
                if (array_key_exists($n,$team['Resultados1'])) {
                    $team['P1']+=$team['Resultados1'][$n]['P'];
                    $team['T1']+=$team['Resultados1'][$n]['T'];
                } else  $team['P1']+=200.0;
                if ($this->manga2!=null) {
                    if (array_key_exists($n,$team['Resultados2'])) {
                        $team['P2']+=$team['Resultados2'][$n]['P'];
                        $team['T2']+=$team['Resultados2'][$n]['T'];
                    } else  $team['P2']+=200.0;
                }
            }
            // and evaluate final team's results
            $team['Penalizacion']=$team['P1']+$team['P2'];
            $team['Tiempo']=$team['T1']+$team['T2'];
        }
        // finalmente ordenamos los equipos en funcion de la clasificacion
        usort($this->equipos,function($a,$b){
            return ($a['Penalizacion']==$b['Penalizacion'])? ($a['Tiempo']-$b['Tiempo']): ($a['Penalizacion']-$b['Penalizacion']);
        });
        // notice that teams from wrong manga are still here: they should be detected and removed by mean of count($team['Resultados'])
    }

    function print_datosMangas() {
        $this->setXY(10,40);
        $this->SetFont('Helvetica','B',9); // bold 9px

        $jobj=new Jueces("print_Clasificaciones_eq4");
        $juez1=$jobj->selectByID($this->manga1->Juez1);
        $juez2=$jobj->selectByID($this->manga1->Juez2); // asume mismos jueces en dos mangas
        $tm1=Mangas::$tipo_manga[$this->manga1->Tipo][3] . " - " . $this->cat[$this->categoria];
        $tm2=null;
        if ($this->manga2!=null)
            $tm2=Mangas::$tipo_manga[$this->manga2->Tipo][3] . " - " . $this->cat[$this->categoria];

        $this->SetFont('Helvetica','B',11); // bold 9px
        $this->Cell(80,5,_("Journey").": {$this->jornada->Nombre}",0,0,'',false);
        $this->SetFont('Helvetica','B',9); // bold 9px
        $this->Cell(20,5,_("Judge")." 1:","LT",0,'L',false);
        $n=$juez1['Nombre'];
        $this->Cell(75,5,($n==="-- Sin asignar --")?"":$n,"T",0,'L',false);
        $this->Cell(20,5,_("Judge")." 2:","T",0,'L',false);
        $n=$juez2['Nombre'];
        $this->Cell(80,5,($n==="-- Sin asignar --")?"":$n,"TR",0,'L',false);
        $this->Ln();
        $trs=$this->trs1;
        $this->SetFont('Helvetica','B',11); // bold 9px
        $this->Cell(80,5,_("Date").": {$this->jornada->Fecha}",0,0,'',false);
        $this->SetFont('Helvetica','B',9); // bold 9px
        $this->Cell(70,5,$tm1,"LTB",0,'L',false);
        $this->Cell(25,5,_("Dist").".: {$trs['dist']}m","LTB",0,'L',false);
        $this->Cell(25,5,_("Obst").".: {$trs['obst']}","LTB",0,'L',false);
        $this->Cell(25,5,_("SCT").": {$trs['trs']}s","LTB",0,'L',false);
        $this->Cell(25,5,_("MCT").": {$trs['trm']}s","LTB",0,'L',false);
        $this->Cell(25,5,_("Vel").".: {$trs['vel']}m/s","LTRB",0,'L',false);
        $this->Ln();
        if ($this->trs2==null) { $this->Ln(); return; }
        $trs=$this->trs2;
        $ronda=Mangas::$tipo_manga[$this->manga1->Tipo][4]; // la misma que la manga 2
        $this->SetFont('Helvetica','B',11); // bold 9px
        $this->Cell(80,5,_("Round").": $ronda - {$this->cat[$this->categoria]}",0,0,'',false);
        $this->SetFont('Helvetica','B',9); // bold 9px
        $this->Cell(70,5,$tm2,"LTB",0,'L',false);
        $this->Cell(25,5,_("Dist").".: {$trs['dist']}m","LTB",0,'L',false);
        $this->Cell(25,5,_("Obst").".: {$trs['obst']}","LTB",0,'L',false);
        $this->Cell(25,5,_("SCT").": {$trs['trs']}s","LTB",0,'L',false);
        $this->Cell(25,5,_("MCT").": {$trs['trm']}s","LTB",0,'L',false);
        $this->Cell(25,5,_("Vel").".: {$trs['vel']}m/s","LTBR",0,'L',false);
        $this->Ln();
    }

    // on second and consecutive pages print a short description to avoid sheet missorder
    function print_datosMangas2() {
        $this->SetXY(35,15);
        $this->SetFont('Helvetica','B',11); // bold 9px
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
        $base=($this->PageNo()==1)?60:44;
        $y=$base+16*($rowcount);
        $this->SetXY(10,$y);
        // caja de datos de perros
        $this->ac_header(1,16);
        $this->Cell(12,14,1+$index,'LTBR',0,'C',true);
        $this->ac_header(2,16);
        $this->Cell(48,14,"","TBR",0,'C',true);
        $this->SetY($y+1);
        $this->ac_header(2,16);
        foreach($members as $id => $perro) {
            $this->SetX(22);
            $this->ac_row($id,8);
            $this->Cell(6,3,$perro['Dorsal'],'LTBR',0,'L',true);
            $this->SetFont('Helvetica','B',8);
            $this->Cell(13,3,$perro['Nombre'],'LTBR',0,'C',true);
            $this->SetFont('Helvetica','',7);
            $this->Cell(28,3,$perro['NombreGuia'],'LTBR',0,'R',true);
            $this->Ln(3);
        }
        // caja de datos del equipo
        $this->SetXY(70,$y);
        $this->ac_header(1,14);
        $this->Cell(215,14,"","LTBR",0,'C',true);
        $this->SetXY(70,$y);
        $this->Image(__DIR__.'/../../images/logos/'.$logos[0],$this->getX(),$this->getY(),5);
        $this->Image(__DIR__.'/../../images/logos/'.$logos[1],$this->getX()+5,$this->getY(),5);
        $this->Image(__DIR__.'/../../images/logos/'.$logos[2],$this->getX()+10,$this->getY(),5);
        $this->Image(__DIR__.'/../../images/logos/'.$logos[3],$this->getX()+15,$this->getY(),5);
        $this->SetX($this->GetX()+20);
        $this->Cell(160,5,$team['Nombre'],'T',0,'R',true);
        $this->Cell(18,5,'','',0,'',true); // empty space at right of page
        // cabeceras de las celdas de resultados
        $this->ac_header(2,8);
        $this->SetXY(70,$y+5);
        $tm1=Mangas::$tipo_manga[$this->manga1->Tipo][3] . " - " . $this->cat[$this->categoria];
        $tm2=($this->manga2!=null)? Mangas::$tipo_manga[$this->manga2->Tipo][3] . " - " . $this->cat[$this->categoria] : "";
        $this->Cell(88,3,$tm1,'LTRB',0,'C',true);
        $this->Cell(2,3,"",'TR',0,'C',true);
        $this->Cell(88,3,$tm2,'TRB',0,'C',true);
        $this->Cell(2,3,"",'TR',0,'C',true);
        $this->Cell(35,3,_("Final scores"),'TRB',0,'C',true);
        $this->Ln();
        // caja de faltas/rehuses/tiempos
        $this->ac_SetFillColor("#ffffff"); // white background
        $this->SetXY(70,8+$y);
        // manga 1
        $this->Cell(12.5,6,"",'R',0,'L',true); // Flt
        $this->Cell(12.5,6,"",'R',0,'L',true); // Reh
        $this->Cell(12.5,6,"",'R',0,'L',true); // Elim
        $this->Cell(12.5,6,"",'R',0,'L',true); // N.P
        $this->Cell(20,6,"",'R',0,'L',true); // tiempo
        $this->Cell(20,6,"",'R',0,'L',true); // Penalizacion
        // manga 2
        $this->Cell(12.5,6,"",'R',0,'L',true); // Flt
        $this->Cell(12.5,6,"",'R',0,'L',true); // Reh
        $this->Cell(12.5,6,"",'R',0,'L',true); // Elim
        $this->Cell(12.5,6,"",'R',0,'L',true); // N.P
        $this->Cell(20,6,"",'R',0,'L',true); // tiempo
        $this->Cell(20,6,"",'R',0,'L',true); // Penalizacion
        // final
        $this->Cell(17,6,"",'R',0,'L',true); // tiempo
        $this->Cell(18,6,"",'R',0,'L',true); // Penalizacion

        $this->SetXY(70,8+$y);
        // manga 1
        $this->SetFont('Helvetica','I',7); // italic 7px
        $this->Cell(12.5,2.5,_("Flt"),0,0,'L',false);
        $this->Cell(12.5,2.5,_("Ref"),0,0,'L',false);
        $this->Cell(12.5,2.5,_("Elim"),0,0,'L',false);
        $this->Cell(12.5,2.5,_("N.P."),0,0,'L',false);
        $this->Cell(20,2.5,_("Time"),0,0,'L',false);
        $this->Cell(20,2.5,_("Penaliz"),0,0,'L',false);
        // manga 2
        $this->SetFont('Helvetica','I',8); // italic 8px
        $this->Cell(12.5,2.5,_("Flt"),0,0,'L',false);
        $this->Cell(12.5,2.5,_("Ref"),0,0,'L',false);
        $this->Cell(12.5,2.5,_("Elim"),0,0,'L',false);
        $this->Cell(12.5,2.5,_("N.P."),0,0,'L',false);
        $this->Cell(20,2.5,_("Time"),0,0,'L',false);
        $this->Cell(20,2.5,_("Penaliz"),0,0,'L',false);
        // final
        $this->Cell(17,2.5,_("Time"),'R',0,'L',true); // tiempo
        $this->Cell(18,2.5,_("Penaliz"),'R',0,'L',true); // Penalizacion

        $this->SetXY(70,9+$y);
        $this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg2'));
        // manga 1
        $this->SetFont('Helvetica','B',9); // italic 8px
        $this->Cell(12.5,6,$team['F1'],0,0,'R',false);
        $this->Cell(12.5,6,$team['R1'],0,0,'R',false);
        $this->Cell(12.5,6,$team['E1'],0,0,'R',false);
        $this->Cell(12.5,6,$team['N1'],0,0,'R',false);
        $this->Cell(20,6,$team['T1'],0,0,'R',false);
        $this->Cell(18,6,$team['P1'],0,0,'R',false);
        $this->SetXY(158,8+$y); $this->Cell(2,6,"",0,0,'R',true); $this->SetXY(160,9+$y); // barra separadora
        // manga 2
        $this->Cell(12.5,6,$team['F2'],0,0,'R',false);
        $this->Cell(12.5,6,$team['R2'],0,0,'R',false);
        $this->Cell(12.5,6,$team['E2'],0,0,'R',false);
        $this->Cell(12.5,6,$team['N2'],0,0,'R',false);
        $this->Cell(20,6,$team['T2'],0,0,'R',false);
        $this->Cell(18,6,$team['P2'],0,0,'R',false);
        $this->SetXY(248,8+$y); $this->Cell(2,6,"",0,0,'R',true); $this->SetXY(250,9+$y); // barra separadora
        // final
        $this->SetFont('Helvetica','BI',10); // italic 8px
        $this->Cell(17,6,$team['Tiempo'],0,0,'R',false);
        $this->Cell(18,6,$team['Penalizacion'],0,0,'R',false);
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
            $rp= ($this->PageNo()==1)?8:9; // number of rows per page
            if ( $rowcount % $rp==0) {
                $rowcount=0;
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