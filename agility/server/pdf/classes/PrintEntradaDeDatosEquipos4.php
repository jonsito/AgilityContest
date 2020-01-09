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
    protected $rango;
    protected $heights;
	
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
        if (!Jornadas::isJornadaEquipos($this->jornada)) {
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

    /**
     * @param {int} $index numero de orden del equipo
     * @param {array} $team datos del equipo
     * @param {array} $members componentes del equipo
     */
	function printTeamInfo($index,$team,$members) {
        // evaluate logos
        $nullpng=getIconPath($this->federation->get('Name'),'null.png');
        $logos=array();
        $maxdogs=$this->getMaxDogs();
        for($n=0;$n<$maxdogs;$n++) $logos[]=$nullpng;
        if ($team['Nombre']==="-- Sin asignar --") {
            $logos[0]=getIconPath($this->federation->get('Name'),'agilitycontest.png');
        } else {
            $count=0;
            foreach($members as $miembro) {
                $logo=$this->getLogoName($miembro['Perro']);
                if ( ( ! in_array($logo,$logos) ) && ($count<$maxdogs) ) $logos[$count++]=$logo;
            }
        }
        // posicion de la celda
        $y=$this->getY();
        $this->SetXY(10,$y);

        // cabecera del equipo

        // caja de datos de perros
        $boxsize=2+7+4*count($members); // border, team, data and dog data
        $this->ac_header(2,16);
        $this->Cell(12,$boxsize,1+$index,'LTB',0,'C',true);
        $this->Cell(48,$boxsize,"","TBR",0,'C',true);
        $this->SetY($y+1+7); // border + header
        $this->ac_header(2,16);
        foreach($members as $id => $perro) {
            $this->SetX(22);
            $this->ac_row($id,8);
            $this->Cell(6,4,$perro['Dorsal'],'LTBR',0,'L',true);
            $this->SetFont($this->getFontName(),'B',8);
            $this->Cell(13,4,$perro['Nombre'],'LTBR',0,'C',true);
            $this->SetFont($this->getFontName(),'',7);
            $this->Cell(28,4,$this->getHandlerName($perro),'LTBR',0,'R',true);
            $this->Ln(4);
        }

        // caja de datos del equipo
        $this->SetXY(70,$y);
        $this->ac_header(1,13);
        $this->Cell(130,$boxsize,"","LTBR",0,'C',true);
        // logos ( encima de los participantes )
        $this->SetXY(22,$y+1);
        for($n=0;$n<count($logos);$n++)
            $this->Image($logos[$n],$this->getX()+7*$n,$this->getY(),6);
        // nombre del equipo
        $this->SetX(91);
        // add extra space at end of name
        $this->Cell(108,5,$team['Nombre']."  ",'',0,'R',true);
        $this->Ln();

        // caja de faltas/rehuses/tiempos
        $this->ac_SetFillColor("#ffffff"); // white background
        for ($n=0;$n<count($members);$n++) {
            $this->SetXY(71,$y+7+1+4*$n); // top,header, border,dognumber
            $this->Cell(49,4,"",'LBR',0,'L',true);
            $this->Cell(20,4,"",'BR',0,'L',true);
            $this->Cell(15,4,"",'BR',0,'L',true);
            $this->Cell(15,4,"",'BR',0,'L',true);
            $this->Cell(29,4,"",'',0,'L',true);
        }
        $this->SetXY(71,$y+7-1.5);
        $this->SetFont($this->getFontName(),'I',7); // italic 8px
        $this->Cell(49,2.5,_("Faults"),0,0,'L',false);
        $this->Cell(20,2.5,_("Refusals"),0,0,'L',false);
        $this->Cell(15,2.5,_("Touchs"),0,'L',false);
        $this->Cell(15,2.5,_("Eliminated"),0,'L',false);
        $this->Cell(29,2.5,_("Time"),0,0,'L',false);
        // NOTE: fillData has no sense in Team-All contest,
        // as no way to get individual scores on current team

        // return next Y position
        return $y+$boxsize+3; // current Y position, box size plus extra space
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
        $bg1=$this->config->getEnv('pdf_rowcolor1');
        // $bg2=$this->config->getEnv('pdf_rowcolor2');
        $this->ac_SetFillColor($bg1);
        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->SetLineWidth(.3);

        // Rango
        $fromItem=1;
        $toItem=99999;
        if (($this->rango!=="") && preg_match('/^\d+-\d+$/',$this->rango)!==FALSE) {
            $a=explode("-",$this->rango);
            $fromItem=intval($a[0]);
            $toItem=intval($a[1]);
        }
        // initial values for team count and page position
        $index=0;
        $ypos=58;
        $this->categoria="-";
		foreach($this->equipos as $equipo) {
		    // if category is not in selected skip
            if(!category_match($equipo['Categorias'],$this->heights,$this->validcats)) continue;
            // if dogs not in range skip
            if ( (($index+1)<$fromItem) || (($index+1)>$toItem) ) { $index++; continue; }
            // if team has no dogs skip
            if (count($equipo['Perros'])==0) continue;
            // on category change force new page
            if ($equipo['Categorias']!=$this->categoria) {
                $index=0;
                $ypos=58;
                $this->categoria=$equipo['Categorias'];
                $this->AddPage();
            }
            // also, if next team does not fit in page force new page
            $boxsize=7+2+4*count($equipo['Perros']); // header, border, dogs, extra space
            if (($ypos+$boxsize) > 280 ) {
                $ypos=58;
                $this->AddPage();
            }
            // pintamos el aspecto general de la celda
            $this->setY($ypos);
            $ypos = $this->printTeamInfo($index,$equipo,$equipo['Perros']);
            $index++;
		}
		// Línea de cierre
		$this->myLogger->leave();
	}
}

?>

