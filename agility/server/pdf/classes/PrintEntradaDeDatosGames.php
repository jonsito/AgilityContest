<?php
/*
PrintEntradaDeDatosGames.php

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
 * genera un pdf con las hojas de asistente de pista en jornadas Snooker/Gambler
*/

require_once(__DIR__."/../fpdf.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__."/../print_common.php");

class PrintEntradaDeDatosGames extends PrintCommon {
    protected $perros; // lista de participantes en esta jornada
    protected $manga; // datos de la manga
    protected $orden=null; // orden de salida de la manga
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

	protected $default_row=array(
	    'Dorsal'    => "",
        'Perro'     => 0,
        'Nombre'    => "",
        'NombreLargo' => "",
        'NombreGuia'=> "",
        'NombreClub'=> "",
	    'Licencia'  => "",
        'Categoria' => "-",
        'Grado'     => "-",
        'Observaciones' => "",
        'Celo'      => 0
    );

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
		parent::__construct('Portrait',"print_entradaDeDatosGames",$data['prueba'],$data['jornada'],$data['comentarios']);
		if ( ($data['prueba']<=0) || ($data['jornada']<=0) ) {
			$this->errormsg="print_entradaDeDatosGames: either prueba or jornada data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->heights=Competitions::getHeights($data['prueba'],$data['jornada'],$data['manga']);
		// si el orden de salida es null ( ojo, no es lo mismo que vacio)
        // significa que tenemos que rellenar una plantilla vacia
        if ($data['orden']===null) {
		    $data['orden']=array();
		    for ($n=0;$n<8;$n++) array_push($data['orden'],$this->default_row);
        }
        $this->orden=$data['orden'];
        // guardamos info de la manga
        $this->manga=$data['datosmanga'];
        $this->numrows=$data['numrows']; // should be 8
        $this->validcats=$data['cats'];
        $this->fillData=($data['fill']==0)?false:true;
        $this->rango= (preg_match('/^\d+-\d+$/',$data['rango']))? $data['rango'] : "1-99999";

        // comprobamos que estamos en una jornada de tipo Games
        if (! in_array($this->manga->Tipo,array(29,30))) {
            $this->errormsg="print_entradaDeDatosGames: Jornada {$data['jornada']} has no Games competition declared";
            throw new Exception($this->errormsg);
        }
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
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}

	private function writeTableCell_common($row,$orden) {
        $wide=$this->federation->hasWideLicense(); // if required use long cell for license
        if (intval($row['Perro'])!==0) $logo=$this->getLogoName($row['Perro']);
        else $logo= getIconPath('rsce','agilitycontest.png');
        $this->ac_header(1,20);
        // save cursor position
        $x=$this->getX();
        $y=$this->GetY();

        // fase 1: contenido de cada celda de la cabecera
        // Cell( width,height,message,border,cursor,align,fill)
        // pintamos logo
        $this->Cell(15,19,'','LTBR',0,'L',false);
        $this->SetXY($x+1,$y+2); // restore cursor position
        $this->Image($logo,$this->getX()+0.5,$this->getY(),12);
        // pintamos numero de orden
        $this->ac_header(2,12);
        // $this->SetXY($x+16,$y+7);
        $this->SetXY($x+1.5,$y+14);
        $this->Cell(12,4,$orden,'',0,'R',true);

        // bordes cabecera de celda
        $this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // color de fondo 2
        $this->SetXY($x+15,$y); // restore cursor position
        $this->SetFont($this->getFontName(),'B',10); // bold 10px
        $this->Cell(15,6,'',	'LTR',0,'L',true); // dorsal
        $this->Cell(10,6,'',	'TR',0,'L',true); // celo
        if ($wide) {
            $this->Cell(50,6,'',	'TR',0,'L',true); // perro
        } else {
            $this->Cell(20, 6, '', 'TR', 0, 'L', true); // licencia
            $this->Cell(30,6,'',	'TR',0,'L',true); // perro
        }
        $this->Cell(60,6,'',	'TR',0,'L',true); // guia
        $this->Cell(40,6,'',	'TR',0,'L',true); // club
        // datos cabecera de celda
        $this->SetXY($x+15,$y+2); // restore cursor position
        $this->Cell(15,4,$row['Dorsal'],		'',0,'R',false); // display order
        $this->Cell(10,4,($row['Celo']!=0)?"Celo":"",'',0,'R',false);
        if ($wide) {
            $this->Cell(50,4,$row['Nombre'],		'',0,'R',false);
        } else {
            $this->Cell(20,4,$row['Licencia'],		'',0,'R',false);
            $this->Cell(30,4,$row['Nombre'],		'',0,'R',false);
        }
        $this->Cell(60,4,$this->getHandlerName($row),	'',0,'R',false);
        $this->Cell(40,4,$row['NombreClub'],	'',0,'R',false);

        // titulos cabecera de celda
        $this->SetXY($x+15,$y); // restore cursor position
        $this->SetTextColor(0,0,0); // negro
        $this->SetFont($this->getFontName(),'I',8); // italic 8px
        $this->Cell(15,4,_('Dorsal'),	'',0,'L',false); // display order
        $this->Cell(10,4,_('Heat'),	'',0,'L',false);
        if ($wide) {
            $this->Cell(50,4,_('Name'),	'',0,'L',false);
        } else {
            $this->Cell(20,4,_('Lic'),'',0,'L',false);
            $this->Cell(30,4,_('Name'),	'',0,'L',false);
        }
        $this->Cell(60,4,_('Handler'),	'',0,'L',false);
        $this->Cell(40,4,$this->strClub,	'',0,'L',false);
    }

    private function paintBall($x,$y,$fg,$bg,$txt,$w=6){
        $img=createNumberedBall($fg,$bg,$txt);
        $tmpfile=tempnam_sfx(__DIR__."/../../../../logs","ball_","png");
        imagepng($img,$tmpfile);
        $this->Image($tmpfile,$x,$y,$w,$w);
        imagedestroy($img);
        @unlink($tmpfile);
    }

	private function writeTableCell_snooker($row,$orden) {        // save cursor position
        $x=$this->getX();
        $y=$this->GetY();

        $h0=array("1",     " ","1",     " ","1",     " ","2","3","4","5","6","7");
        $h1=array(_('red')," ",_('red')," ",_('red')," ",_('yellow'), _('green'), _('brown'), _('blue'), _('pink'), _('black'));

        $this->writeTableCell_common($row,$orden);

        // ahora pintamos zona de escritura de palotes
        $this->SetXY($x+15,$y+6); // pintamos recuadro cabecera
        $this->ac_header(0,9);
        for($n=0;$n<count($h0);$n++) { $this->Cell(10.5,7,"",'LT',0,'C',($n&1)); }
        $this->SetXY($x+15,$y+6); // pintamos bolitas cabecera
        $c1=$this->config->getEnv('pdf_rowcolor1');
        $c2=$this->config->getEnv('pdf_rowcolor2');
        for($n=0;$n<count($h0);$n++) {
            switch($n){
                case 0: $bg=$c1; $fg="F00"; break;// 1- red
                case 1: $bg=$c2; $fg="FFF"; break;// ?- white,grey
                case 2: $bg=$c1; $fg="F00"; break;// 1- red
                case 3: $bg=$c2; $fg="FFF"; break;// ?- white,grey
                case 4: $bg=$c1; $fg="F00"; break;// 1- red
                case 5: $bg=$c2; $fg="FFF"; break;// ?- white,grey
                case 6: $bg=$c1; $fg="FF0"; break;// 2- yellow
                case 7: $bg=$c2; $fg="0F0"; break;// 3- green,grey
                case 8: $bg=$c1; $fg="841"; break;// 4- brown
                case 9: $bg=$c2; $fg="00F"; break;// 5- blue,grey
                case 10: $bg=$c1; $fg="F19"; break;// 6- pink
                case 11: $bg=$c2; $fg="000"; break;// 7- black,grey
            }
            $this->paintBall($this->GetX()+10.5*$n+2,0.5+$this->GetY(),$fg,$bg,$h0[$n]);
        }
        $this->ac_row(0,7);
        $this->SetXY($x+15,$y+13);
        for($n=0;$n<count($h1);$n++) { $this->Cell(10.5,3,$h1[$n],'LTB',0,'C',($n&1)); }
        $this->SetXY($x+15,$y+16);
        for($n=0;$n<count($h1);$n++) { $this->Cell(10.5,5,"",'LTB',0,'C',($n&1)); }
        // tiempo  y total
        $this->SetXY($x+141,$y+6);
        $this->ac_header(0,9);
        $this->Cell(29,5,_('Time').":",'LT',0,'L',true);
        $this->Cell(20,5,"",'LTR',0,'C',false);
        // gambler 10 + puntos
        $this->SetXY($x+141,$y+11);
        $this->Cell(29,5,_('Opening points'),'LTB',0,'L',true);
        $this->Cell(20,5,"",'LTRB',0,'L',false);
        // gambler 20 + Puntos
        $this->SetXY($x+141,$y+16);
        $this->Cell(29,5,_('Closing points'),'LB',0,'L',true);
        $this->Cell(20,5,"",'LBR',0,'L',false);
        // next row
        $this->Ln(12);
    }

    private function writeTableCell_gambler($row,$orden) {
        $c=$this->config;
        // save cursor position
        $x=$this->getX();
        $y=$this->GetY();
        $h0=array("1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","A","B","C","D","E","X");
        $h1=array("5","5","4","3","3","2","2","2","2", "2", "2", "1", "1", "1", "1", "1", "1", "1", "1", "1", "1",
            $c->getEnv('gambler_bonus1'), $c->getEnv('gambler_bonus2'), $c->getEnv('gambler_bonus3'),
            $c->getEnv('gambler_bonus4'),$c->getEnv('gambler_bonus5'),$c->getEnv('gambler_extra'));
        $h2=array("5","5","4","3","3","2","2","2","2", "2", "2", "1", "1", "1", "1", "1", "1", "1", "1", "1", "1",
            $c->getEnv('gambler_bonus1'), $c->getEnv('gambler_bonus2'), $c->getEnv('gambler_bonus3'),
            $c->getEnv('gambler_bonus4'),$c->getEnv('gambler_bonus5'),$c->getEnv('gambler_extra'));
        $h3=array("00F","00F","841","0F0","0F0","FF0","FF0","FF0","FF0","FF0","FF0",
                  "F00","F00","F00","F00","F00","F00","F00","F00","F00","F00", "0F0", "841", "00F", "F19","000",'F00');

        $this->writeTableCell_common($row,$orden);

        // ahora pintamos zona de escritura de palotes
        $this->SetXY($x+15,$y+6);
        // paint open part for gambler
        $this->ac_header(0,9);
        for($n=0;$n<count($h0);$n++) { $this->Cell(5,5,/*$h0[$n]*/"",'TR',0,'C',!($n&1)); }
        // paint balls
        $this->SetXY($x+15,$y+6);
        $c1=$this->config->getEnv('pdf_rowcolor1');
        $c2=$this->config->getEnv('pdf_rowcolor2');
        for($n=0;$n<count($h0);$n++) {
            $this->paintBall($this->GetX()+5*$n,$this->GetY(),$h3[$n],($n&1)?$c1:$c2,$h0[$n],5);
        }

        $this->ac_row(0,9);
        $this->SetXY($x+15,$y+11);
        for($n=0;$n<count($h1);$n++) { $this->Cell(5,5,$h1[$n],'RTB',0,'C',false); }
        $this->SetXY($x+15,$y+16);
        for($n=0;$n<count($h2);$n++) { $this->Cell(5,5,$h2[$n],'LBR',0,'C',false); }
        // cajas para tiempo y total
        $this->ac_header(2,9);
        $this->SetXY($x+165,$y+6);
        $this->Cell(25,7.5,"",'LTRB',0,'L',true);
        $this->SetXY($x+165,$y+13.5);
        $this->Cell(25,7.5,"",'LTRB',0,'L',true);
        // gambler, tiempo  y total
        // primera fila
        $this->SetXY($x+150,$y+6);
        $this->Cell(15,7.5,_('Gambler'),'TR',0,'L',false);
        $this->ac_row(0,9);
        $this->Cell(15,4,_('Time').":",'',0,'L',false);
        // segunda fila
        $this->SetXY($x+150,$y+13.5);
        $this->Cell(5,7.5,"0",'TRB',0,'C',false);
        $this->Cell(5,7.5,$c->getEnv('gambler_seq1'),'TRB',0,'C',false);
        $this->Cell(5,7.5,$c->getEnv('gambler_seq2'),'TRB',0,'C',false);
        $this->Cell(20,4,_('Pts').". "._("Opening seq").":",'',0,'L',false);
        // next row
        $this->Ln(12);
    }

	// Tabla coloreada
	function composeTable() {
        $this->myLogger->enter();

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
        // Datos
        $orden=1;
        $rowcount=0;
        foreach($this->orden as $row) {
            if (!category_match($row['Categoria'],$this->heights,$this->validcats)) continue;
            // if change in categoria, reset orden counter and force page change
            if ($row['Categoria'] !== $this->categoria) {
                // $this->myLogger->trace("Nueva categoria es: ".$row['Categoria']);
                $this->categoria = $row['Categoria'];
                // $this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre de categoria
                $rowcount=0;
                $orden=1;
            }
            if (($orden<$fromItem) || ($orden>$toItem) ) { $orden++; continue; } // not in range; skip
            // REMINDER: $this->cell( width, height, data, borders, where, align, fill)
            if( ($rowcount % $this->numrows) == 0 ) { // assume $numrows entries per page
                $this->AddPage();
                if($this->numrows!=1) {
                    // indicamos nombre del operador que rellena la hoja
                    $this->ac_header(2,12);
                    $this->Cell(90,7,_('Record by').':','LTBR',0,'L',true);
                    $this->Cell(10,7,'',0,'L',false);
                    $this->Cell(90,7,_('Review by').':','LTBR',0,'L',true);
                    $this->Ln(15);
                }
            }
            if ($this->manga->Tipo==29) $this->writeTableCell_snooker($row,$orden);
            if ($this->manga->Tipo==30) $this->writeTableCell_gambler($row,$orden);
            $rowcount++;
            $orden++;
        }
        // Línea de cierre
        // $this->Cell(array_sum($this->pos),0,'','T');
        $this->myLogger->leave();
	}
}

?>

