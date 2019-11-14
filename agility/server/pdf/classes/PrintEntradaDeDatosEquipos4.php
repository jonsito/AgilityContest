<?php
/*
PrintEntradaDeDatosEquipos4.php

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
 * genera un pdf con las hojas de asistente de pista en jornadas equipos4
*/

require_once(__DIR__."/../fpdf.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__.'/../../database/classes/Equipos.php');
require_once(__DIR__."/../print_common.php");

class PrintEntradaDeDatosEquipos4 extends PrintCommon {
	protected $equipos; // lista de equipos de esta jornada
    protected $perros; // lista de participantes en esta jornada
    protected $manga; // datos de la manga
    protected $categoria;
    protected $validcats; // categorias de las que se solicita impresion
    protected $fillData;
    protected $rango;
    protected $heights;
	
	// geometria de las celdas
	protected $cellHeader;
    //                      Dorsal  nombre raza licencia Categoria guia club  celo  observaciones
	protected $pos	=array( 10,     25,     27,    10,    18,      40,   25,  10,    25);
	protected $align=array( 'R',    'C',    'R',    'C',  'C',     'R',  'R', 'C',   'R');
	
	/**
	 * Constructor
     * @param {array} $data constructor parameters: 'prueba','jornada','manga','cats','fill','rango','comentarios'
     * {integer} prueba Prueba ID
     * {integer} jornada Jornada ID
     * {integer} manga Manga ID
     * {string} cats categorias -XLMST
     * {string} rango [\d]-[\d]
     * {string} comentarios
	 * @throws Exception
	 */
    function __construct($data) {
    //    function __construct($prueba,$jornada,$manga,$cats,$fill=0) {
		parent::__construct('Portrait',"print_entradaDeDatosEquipos4",$data['prueba'],$data['jornada'],$data['comentarios']);
		if ( ($data['prueba']<=0) || ($data['jornada']<=0) ) {
			$this->errormsg="print_entradaDeDatosEquipos4: either prueba or jornada data are invalid";
			throw new Exception($this->errormsg);
		}
        // comprobamos que estamos en una jornada por equipos
        $flag=intval($this->jornada->Equipos3)+intval($this->jornada->Equipos4);
        if ($flag==0) {
            $this->errormsg="print_entradaDeDatosEquipos4: Jornada {$data['jornada']} has no Team competition declared";
            throw new Exception($this->errormsg);
        }
        // guardamos info de la manga
        $this->manga=$this->myDBObject->__getObject("mangas",$data['manga']);
        // numero de alturas
        $this->heights=Competitions::getHeights($data['prueba'],$data['jornada'],$data['manga']);
        // Datos del orden de salida de equipos
        $m = Competitions::getOrdenSalidaInstance("entradaDeDatosEquipos4",$data['manga']);
        $teams= $m->getTeams();
        $this->equipos=$teams['rows'];
        // anyadimos el array de perros del equipo
        foreach($this->equipos as &$equipo) {$equipo['Perros']=array();}
        $r= $this->myDBObject->__select("*","resultados","(Manga={$data['manga']})","","");
        foreach($r['rows'] as $perro) {
            foreach($this->equipos as &$equipo) {
                if ($perro['Equipo']==$equipo['ID']) {
                    array_push($equipo['Perros'],$perro);
                    break;
                }
            }
        }
        $this->validcats=$data['cats'];
        $this->fillData=($data['fill']==0)?false:true;
        $this->rango= (preg_match('/^\d+-\d+$/',$data['rango']))? $data['rango'] : "1-99999";

        // set pdf file name
        $grad=$this->federation->getTipoManga($this->manga->Tipo,3); // nombre de la manga
        $cat=$this->validcats; // categorias del listado
        $str=($cat=='-')?$grad:"{$grad}_{$cat}";
        $res=normalize_filename($str);
        $this->set_FileName("HojasAsistente_{$res}.pdf");
	}

	// Cabecera de página
	function Header() {
		$this->print_commonHeader(_("Data entry (Teams-4)"));

        // pintamos datos de la jornada
        $this->SetFont($this->getFontName(),'B',12); // bold 15
        $str  = $this->jornada->Nombre . " - " . $this->jornada->Fecha;
        $this->Cell(90,9,$str,0,0,'L',false);

        // pintamos tipo y categoria de la manga
        $tmanga= _(Mangas::getTipoManga($this->manga->Tipo,1,$this->federation));
        $categoria=$this->getCatString($this->categoria);
        $str2 = "$tmanga - $categoria";
        $this->Cell(100,9,$str2,0,0,'R',false); // al otro lado tipo y categoria de la manga
        $this->Ln(12);
        // indicamos nombre del operador que rellena la hoja
        $this->ac_header(2,12);
        $this->Cell(90,7,_("Record by").":",'LTBR',0,'L',true);
        $this->Cell(10,7,'',0,'L',false);
        $this->Cell(90,7,_("Review by").":",'LTBR',0,'L',true);
        $this->Ln();
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}
	
	function printTeamInfo($rowcount,$index,$team,$members) {
        // evaluate logos
        $nullpng=getIconPath($this->federation->get('Name'),'null.png');
        $logos=array($nullpng,$nullpng,$nullpng,$nullpng);
        if ($team['Nombre']==="-- Sin asignar --") {
            $logos[0]=getIconPath($this->federation->get('Name'),'agilitycontest.png');
        } else {
            $count=0;
            foreach($members as $miembro) {
                $logo=$this->getLogoName($miembro['Perro']);
                if ( ( ! in_array($logo,$logos) ) && ($count<4) ) $logos[$count++]=$logo;
            }
        }
        // posicion de la celda
        $y=60+22*($rowcount);
        $this->SetXY(10,$y);
        // caja de datos de perros
        $this->ac_header(2,16);
        $this->Cell(12,18,1+$index,'LTB',0,'C',true);
        $this->Cell(48,18,"","TBR",0,'C',true);
        $this->SetY($y+1);
        $this->ac_header(2,16);
        foreach($members as $id => $perro) {
            $this->SetX(22);
            $this->ac_row($id,8);
            $this->Cell(6,4,$perro['Dorsal'],'LTBR',0,'L',true);
            $this->SetFont($this->getFontName(),'B',8);
            $this->Cell(13,4,$perro['Nombre'],'LTBR',0,'C',true);
            $this->SetFont($this->getFontName(),'',7);
            $this->Cell(28,4,$this->getHandlerName($row),'LTBR',0,'R',true);
            $this->Ln(4);
        }
        // caja de datos del equipo
        $this->SetXY(70,$y);
        $this->ac_header(1,14);
        $this->Cell(130,18,"","LTBR",0,'C',true);
        $this->SetXY(71,$y+1);
        $this->Image($logos[0],$this->getX(),$this->getY(),5);
        $this->Image($logos[1],$this->getX()+5,$this->getY(),5);
        $this->Image($logos[2],$this->getX()+10,$this->getY(),5);
        $this->Image($logos[3],$this->getX()+15,$this->getY(),5);
        $this->SetX($this->GetX()+20);
        $this->Cell(100,5,$team['Nombre'],'',0,'R',true);
        $this->Cell(8,5,'','',0,'',true); // empty space at right of page
        $this->Ln();
        // caja de faltas/rehuses/tiempos
        $this->ac_SetFillColor("#ffffff"); // white background
        $this->SetXY(71,7+$y);
        $this->Cell(49,10,"",'R',0,'L',true);
        $this->Cell(20,10,"",'R',0,'L',true);
        $this->Cell(15,10,"",'R',0,'L',true);
        $this->Cell(15,10,"",'R',0,'L',true);
        $this->Cell(29,10,"",'',0,'L',true);
        $this->SetXY(71,7+$y+1);
        $this->SetFont($this->getFontName(),'I',8); // italic 8px
        $this->Cell(49,2.5,_("Faults"),0,0,'L',false);
        $this->Cell(20,2.5,_("Refusals"),0,0,'L',false);
        $this->Cell(15,2.5,_("Touchs"),0,'L',false);
        $this->Cell(15,2.5,_("Eliminated"),0,'L',false);
        $this->Cell(29,2.5,_("Time"),0,0,'L',false);
        if ( ! $this->fillData) return;
        // to be done: on fill mode populate team results in assistant sheets
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
        $bg1=$this->config->getEnv('pdf_rowcolor1');
        // $bg2=$this->config->getEnv('pdf_rowcolor2');
        $this->ac_SetFillColor($bg1);
        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->SetLineWidth(.3);

        // take care on wide license federation contests
        if ($this->federation->get('WideLicense')) {
            $this->pos[1] -= 2;
            $this->pos[2] -= 3;
            $this->pos[3] += 20;
            $this->pos[8] -= 15;
        }
        // Rango
        $fromItem=1;
        $toItem=99999;
        if (($this->rango!=="") && preg_match('/^\d+-\d+$/',$this->rango)!==FALSE) {
            $a=explode("-",$this->rango);
            $fromItem=intval($a[0]);
            $toItem=intval($a[1]);
        }
        $index=0;
        $rowcount=0;
        $this->categoria="-";
		foreach($this->equipos as $equipo) {
		    // if category is not in selected skip
            if(!category_match($equipo['Categorias'],$this->heights,$this->validcats)) continue;
            // if dogs not in range skip
            if ( (($index+1)<$fromItem) || (($index+1)>$toItem) ) { $index++; continue; }
            // if team has no dogs skip
            if (count($equipo['Perros'])==0) continue;
            if ( ($rowcount%10==0) || ($equipo['Categorias']!=$this->categoria)) {
                $rowcount=0;
                $this->categoria=$equipo['Categorias'];
                $this->AddPage();
            }
            // pintamos el aspecto general de la celda
            $this->printTeamInfo($rowcount,$index,$equipo,$equipo['Perros']);
            $rowcount++;
            $index++;
		}
		// Línea de cierre
		$this->myLogger->leave();
	}
}

?>

