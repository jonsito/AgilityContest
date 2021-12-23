<?php
/*
PrintParcialGeneral.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
 * genera un pdf con los participantes ordenados segun los resultados de la manga
 */

require_once(__DIR__."/../fpdf.php");
require_once(__DIR__."/../../tools.php");
require_once (__DIR__."/../../logging.php");
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jueces.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__.'/../../database/classes/Mangas.php');
require_once(__DIR__.'/../../database/classes/Resultados.php');
require_once(__DIR__."/../print_common.php");

class PrintParcialGeneral extends PrintCommon {
	
	protected $manga;
    protected $resultados;
    protected $modes;
	protected $headertitle;
	protected $hasGrades;
	
	// geometria de las celdas
	protected $cellHeader;
    protected $pos;
    protected $align;

	/**
	 * Constructor
     * @param integer $prueba prueba ID
     * @param integer $jornada Jornada ID
     * @param array $manga datos tecnicos de la manga
	 * @param array $resultados array de los diversos resultados asociados a cada manga/categoria
     * @param array $modes lista de modos de los resultados anteriores
     * @param string $title header title
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$manga,$resultados,$modes,$title) {
		parent::__construct('Portrait',"print_resultadosByManga",$prueba,$jornada);
		$this->manga=$manga;
		$this->resultados=$resultados;
		$this->modes=$modes;
		$this->headertitle=$title;
        $this->hasGrades=Jornadas::hasGrades($this->jornada);
		$catgrad=($this->hasGrades)?_('Cat').'/'._('Grade'):_('Cat').".";
        if (!isMangaWAO($this->manga->Tipo)) {
            $this->cellHeader=
                array(_('Dorsal'),_('Name'),_('Lic'),_('Handler'),$this->strClub,$catgrad,_('Flt'),_('Tch'),_('Ref'),_('Time'),_('Vel'),_('Penal'),_('Calification'),_('Pos'));
            $this->pos=
                array(  8,		17,		15,		30,		20,		15,		   6,      6,    6,       12,     7,    12,      24,			10 );
            $this->align=
                array(  'L',    'L',    'C',    'R',   'R',    'C',       'C',   'C',   'C',     'R',    'R',  'R',     'L',			'C');
        } else {
            $this->cellHeader=
                array(_('Dorsal'),_('Name'),_('Handler'),_('Cat'),$this->strClub,$catgrad,_('Flt'),_('Tch'),_('Ref'),_('Time'),_('Vel'),_('Penal'),_('Calification'),_('Pos'));
            $this->pos	=array(  8,		27,		   35,		   10,		20,		       15,		   6,      6,    6,       12,     7,    12,      15,			9 );
            $this->align=array(  'L',   'L',       'R',        'C',     'R',           'C',       'C',    'C',   'C',     'R',    'R',  'R',     'L',			'C');
        }
        // set file name
        $grad=$this->federation->getTipoManga($this->manga->Tipo,3); // nombre de la manga
        $res=normalize_filename($grad);
        $this->set_FileName("ResultadosManga_{$res}.pdf");
        // do not show fed icon in pre-agility, special, or ko
        if (in_array($this->manga->Tipo,array(0,1,2,15,16,18,19,20,21,22,23,24,))) {
            $this->icon2=getIconPath($this->federation->get('Name'),"null.png");
        }
	}
	
	// Cabecera de página
	function Header() {
        $str = ($this->manga->Tipo == 16) ? _("Results") : $this->headertitle; // on special round just show "results" string
        $this->print_commonHeader($str);
        $this->Ln(5);
    }

    /**
     * @param $data array { trs,manga,resultados }
     * @throws Exception
     */
    function printRoundData($data,$mode){
        $this->print_identificacionManga($this->manga,$this->getModeString(intval($mode)));
		$this->SetFont($this->getFontName(),'B',9); // bold 9px
        $juez1=$this->myDBObject->__getArray("jueces",$data['manga']->Juez1);
        $juez2=$this->myDBObject->__getArray("jueces",$data['manga']->Juez2);
		$this->Cell(20,7,_('Judge')." 1:","LT",0,'L',false);
		$str=($juez1['Nombre']==="-- Sin asignar --")?"":$juez1['Nombre'];
		$this->Cell(70,7,$str,"T",0,'L',false);
		$this->Cell(20,7,_('Judge')." 2:","T",0,'L',false);
		$str=($juez2['Nombre']==="-- Sin asignar --")?"":$juez2['Nombre'];
		$this->Cell(78,7,$str,"TR",0,'L',false);
		$this->Ln(7);
		$this->Cell(20,7,_('Distance').":","LB",0,'L',false);
		$this->Cell(25,7,"{$data['trs']['dist']} mts","B",0,'L',false);
		$this->Cell(20,7,_('Obstacles').":","B",0,'L',false);
		$this->Cell(25,7,$data['trs']['obst'],"B",0,'L',false);
		$this->Cell(10,7,_('SCT').":","B",0,'L',false);
		$this->Cell(20,7,"{$data['trs']['trs']} "._('Secs'),"B",0,'L',false);
		$this->Cell(10,7,_('MCT').":","B",0,'L',false);
		$this->Cell(20,7,"{$data['trs']['trm']} "._('Secs'),"B",0,'L',false);
		$this->Cell(20,7,_('Speed').":","B",0,'L',false);
		$this->Cell(18,7,"{$data['trs']['vel']} m/s","BR",0,'L',false);
		$this->Ln(7); // linea extra antes de empezar los resultados de la manga
	}
	
	// Pie de página
	function Footer() {
        // add judge signing box
        $this->SetXY(80,-20);
        $this->SetFont($this->getFontName(),'IB',10);
        $this->Cell(58,7,_('Judge signature / Date').":  ",0,0,'R',false);
        $this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg2')); // azul
        $this->Cell(60,7,"","TBRL",0,'C',true);
        $this->Ln();
		$this->print_commonFooter();
	}
	
	function writeTableHeader() {
		$this->myLogger->enter();
		// Colores, ancho de línea y fuente en negrita de la cabecera de tabla
		$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // azul
		$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg1')); // blanco
		$this->ac_SetDrawColor("0x000000"); // line color
		$this->SetFont($this->getFontName(),'B',8); // bold 9px
		for($i=0;$i<count($this->cellHeader);$i++) {
			// en la cabecera texto siempre centrado
			if ($this->pos[$i]!=0) $this->Cell($this->pos[$i],7,$this->cellHeader[$i],1,0,'C',true);
		}
		// Restauración de colores y fuentes
		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont(''); // remove bold
		$this->Ln();
		$this->myLogger->leave();
	}

	function writeCell($row,$rowcount) {
        $puesto= ($row['Penalizacion']>=200)? "-":"{$row['Puesto']}";
        $veloc= ($row['Penalizacion']>=200)?"-":number_format2($row['Velocidad'],2);
        $tiempo= ($row['Penalizacion']>=200)?"-":number_format2($row['Tiempo'],$this->timeResolution);
        $penal=number_format2($row['Penalizacion'],$this->timeResolution);
        $this->ac_row($rowcount,8);
        // print row data
        $this->SetFont($this->getFontName(),'',8); // set data font size
        $this->Cell($this->pos[0],6,$row['Dorsal'],			'LR',	0,		$this->align[0],	true);
        $this->SetFont($this->getFontName(),'B',8); // mark Nombre as bold
        $nombre=$row['Nombre'];
        if ($this->useLongNames) $nombre .= " - " . $row['NombreLargo'];
        $this->Cell($this->pos[1],6,$nombre,			'LR',	0,		$this->align[1],	true);
        $this->SetFont($this->getFontName(),'',8); // set data font size
        if ( !isMangaWAO($this->manga->Tipo)) {
            if ($this->pos[2]!=0) $this->Cell($this->pos[2],6,$row['Licencia'],		'LR',	0,		$this->align[2],	true);
            $this->Cell($this->pos[3],6,$this->getHandlerName($row),		'LR',	0,		$this->align[3],	true);
        } else { // en wao se substituye la licencia por la categoria del guia
            $this->Cell($this->pos[2],6,$row['NombreGuia'],		'LR',	0,		$this->align[2],	true);
            $this->Cell($this->pos[3],6,$this->getHandlerCategory($row),		'LR',	0,		$this->align[3],	true);
        }
        $this->Cell($this->pos[4],6,$row['NombreClub'],		'LR',	0,		$this->align[4],	true);
        if ($this->hasGrades) {
            $cat=$this->federation->getCategoryShort($row['Categoria']);
            $grad=$this->federation->getGradeShort($row['Grado']);
            $this->Cell($this->pos[5],6,"{$cat} - {$grad}",	'LR',	0,		$this->align[5],	true);
        } else {
            // $catstr=$row['Categoria'];
            $catstr=$this->federation->getCategory($row['Categoria']);
            $this->Cell($this->pos[5],6,$catstr,	'LR',	0,		$this->align[5],	true);
        }
        $this->Cell($this->pos[6],6,$row['Faltas'],			'LR',	0,		$this->align[6],	true);
        $this->Cell($this->pos[7],6,$row['Tocados'],		'LR',	0,		$this->align[7],	true);
        $this->Cell($this->pos[8],6,$row['Rehuses'],		'LR',	0,		$this->align[8],	true);
        $this->Cell($this->pos[9],6,$tiempo,				'LR',	0,		$this->align[9],	true);
        $this->Cell($this->pos[10],6,$veloc,				'LR',	0,		$this->align[10],	true);
        $this->Cell($this->pos[11],6,$penal,				'LR',	0,		$this->align[11],	true);
        $this->Cell($this->pos[12],6,$row['Calificacion'],	'LR',	0,		$this->align[12],	true);
        $this->SetFont($this->getFontName(),'B',11); // bold 11px
        $this->Cell($this->pos[13],6,$puesto,			'LR',	0,		$this->align[13],	true);
        $this->Ln();
    }

    /**
     * pinta las mangas una detras de otra
     * @throws Exception
     */
	function composeTable() {
		$this->myLogger->enter();
		
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->SetLineWidth(.3);
        if ( !isMangaWAO($this->manga->Tipo)) {
            if ($this->federation->get('WideLicense')) {
                $this->pos[1]+=5;$this->pos[2]=0;$this->pos[3]+=5;$this->pos[4]+=5;
            } else if ($this->useLongNames) {
                $this->pos[1]+=20;$this->pos[2]=0;$this->pos[4]-=5; // remove license. leave space for LongName
            }
        }

		// Datos
        // iteramos sobre cada ronda disponible en la manga
        $this->AddPage();
        foreach ($this->resultados as $index => $result) {
            if ($result['total']==0) continue; // skip categories with no competitors
            // si estamos acabando la pagina, empezamos la ronda en una pagina nueva
            $y=$this->GetY();
            if ($y>240) $this->AddPage();
            $this->printRoundData($result,$this->modes[$index]);

            // iteramos sobre los resultados
            $this->writeTableHeader();
            foreach($result['rows'] as $row) {
                $rowcount=0;
                if ($this->GetY()>270) {
                    $this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre
                    $this->AddPage();
                    $this->writeTableHeader();
                }
                $this->writeCell($row,$rowcount++);
            }
            $this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre
            $this->Ln(7);
        }
		$this->myLogger->leave();
	}

	private function mergeResults($indexes) {
        $this->myLogger->enter();
	    // fase 1 agrupa los arrays
	    $result=array();
	    foreach($indexes as $index) {
	        $size=count($this->resultados[$index]['rows']);
	        $this->myLogger->trace("Adding index:{$index} count:{$size}");
	        $result = array_merge($result,$this->resultados[$index]['rows']);
        }
	    // fase 2 ordena el resultado
        usort($result, function($a, $b) {
            if ( $a['Penalizacion'] == $b['Penalizacion'] )	{
                // para el caso de los eliminados, ponemos primero a los que se pasan de TRM
                // el programa asigna PTiempo=0 a los eliminados "normales" y PTiempo=X a
                // y PTiempo=XX a los
                if ($a['Penalizacion']==100.0) {
                    $pta=($a['PTiempo']==0)?150:$a['PTiempo'];
                    $ptb=($b['PTiempo']==0)?150:$b['PTiempo'];
                    return ($pta > $ptb)? 1:-1;
                }
                // para los demas casos comparamos tiempo
                return ($a['Tiempo'] > $b['Tiempo'])? 1:-1;
            }
            return ( $a['Penalizacion'] > $b['Penalizacion'])?1:-1;
        });
	    $puesto=0;
	    $last_t=0;$last_p=0;
	    // fase 3: ajusta puestos
        for ($n=0;$n<count($result);$n++) {
            if ( ($result[$n]['Tiempo']!=$last_t) || ($result[$n]['Penalizacion']!=$last_p) ){
                // time or penalization differs: next puesto
                $result[$n]['Puesto']=$n+1;
                $puesto=$n+1;
                $last_t=$result[$n]['Tiempo'];
                $last_p=$result[$n]['Penalizacion'];
            } else { // same data: preserve puesto
                $result[$n]['Puesto']=$puesto;
            }
        }
	    $this->myLogger->leave();
	    return $result;
    }

    /**
     * imprime agrupando categorias segun el argumento solicitado
     * Se asume que la manga está configurada como recorridos separados
     * con lo que tenemos 5 alturas con las siguientes posibilidades de agrupacion (indicadas en mergecats:
     * [0],[1],[2],[3],[4] X-L-M-S-T
     * [0,1],[2],[3,4]     XL-M-ST
     * [0,1],[2,3,4]       XL-MST
     * [0,1,2,3,4]         XLMST
     * @param {array} $mergecats array with cats to be merged
     * @throws Exception
     */
    function composeMergedTable($mergecats) {
        $this->myLogger->enter();
        // ajustamos tamaño de las celdas
        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
        $this->SetLineWidth(.3);
        if ($this->federation->get('WideLicense')) {
            $this->pos[1]+=5;$this->pos[2]=0;$this->pos[3]+=5;$this->pos[4]+=5;
        } else if ($this->useLongNames) {
            $this->pos[1]+=20;$this->pos[2]=0;$this->pos[4]-=5; // remove license. leave space for LongName
        }
        // iteramos sobre cada grupo de alturas disponible en la manga
        foreach ($mergecats as $indexes) {
            // mezclamos los resultados
            $result=$this->mergeResults($indexes);
            if (count($result)==0) continue; // no data, skip to next group

            $this->AddPage();
            // imprimimos los datos de trs de cada una de las alturas a mezclar
            foreach($indexes as $index) {
                $this->printRoundData($this->resultados[$index],$this->modes[$index]);
            }
            $this->Ln(5);
            // imprimimos el resultado mezclado iterando sobre ellos
            $this->writeTableHeader();
            foreach($result as $row) {
                $rowcount=0;
                if ($this->GetY()>270) {
                    $this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre
                    $this->AddPage();
                    $this->writeTableHeader();
                }
                $this->writeCell($row,$rowcount++);
            }
            $this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre
            $this->Ln(7);
        }
        $this->myLogger->leave();
    }
}
?>