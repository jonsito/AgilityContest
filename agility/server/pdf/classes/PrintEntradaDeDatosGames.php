<?php
/*
PrintEntradaDeDatosGames.php

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

class PrintEntradaDeDatosGames extends PrintCommon {
	protected $equipos; // lista de equipos de esta jornada
    protected $perros; // lista de participantes en esta jornada
    protected $manga; // datos de la manga
    protected $categoria;
    protected $validcats; // categorias de las que se solicita impresion
    protected $fillData;
    protected $rango;
	
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
     * {string} cats categorias -LMST
     * {string} rango [\d]-[\d]
     * {string} comentarios
	 * @throws Exception
	 */
    function __construct($data) {
    //    function __construct($prueba,$jornada,$manga,$cats,$fill=0) {
		parent::__construct('Portrait',"print_entradaDeDatosEquipos4",$data['prueba'],$data['jornada'],$data['comentarios']);
		if ( ($data['prueba']<=0) || ($data['jornada']<=0) ) {
			$this->errormsg="print_datosEquipos4: either prueba or jornada data are invalid";
			throw new Exception($this->errormsg);
		}
        // comprobamos que estamos en una jornada por equipos
        $flag=intval($this->jornada->Equipos3)+intval($this->jornada->Equipos4);
        if ($flag==0) {
            $this->errormsg="print_datosEquipos4: Jornada {$data['jornada']} has no Team competition declared";
            throw new Exception($this->errormsg);
        }
        // guardamos info de la manga
        $this->manga=$this->myDBObject->__getObject("Mangas",$data['manga']);
        // Datos del orden de salida de equipos
        $m = Competitions::getOrdenSalidaInstance("entradaDeDatosEquipos4",$data['manga']);
        $teams= $m->getTeams();
        $this->equipos=$teams['rows'];
        // anyadimos el array de perros del equipo
        foreach($this->equipos as &$equipo) {$equipo['Perros']=array();}
        $r= $this->myDBObject->__select("*","Resultados","(Manga={$data['manga']})","","");
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
        $str=_("Data entry"). " (".$this->federation->getTipoManga($this->manga->Tipo,3).")";
		$this->print_commonHeader($str);

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

	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
		// PENDING: write
		$this->myLogger->leave();
	}
}

?>

