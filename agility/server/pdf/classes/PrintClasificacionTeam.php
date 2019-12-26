<?php
/*
PrintClasificacionTeam.php

Copyright  2013-2019 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
 * genera un PDF con la clasificacion por equipos
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
require_once(__DIR__.'/../../database/classes/Equipos.php');
require_once(__DIR__.'/../../database/classes/Clasificaciones.php');
require_once(__DIR__."/../print_common.php");

class PrintClasificacionTeam extends PrintCommon {
	
	protected $manga1;
	protected $manga2;
	protected $resultados;
	protected $trs1;
	protected $trs2;
	protected $categoria;
    protected $equipos;
    protected $headerTitle;

    protected $defaultPerro = array( // participante por defecto para garantizar que haya 4perros/equipo
        'Dorsal' => '-',
        'Perro' => 0,
        'Nombre' => '-',
        'NombreLargo' => '-',
        'NombreGuia' => '-',
        'NombreClub' => '-',
        'Licencia' => '-',
        'Categoria' => '-','Grado' => '-',
        'F1' => 0, 'T1' => 0, 'R1' => 0, 'P1' => 0, 'V1' => 0, 'C1' => '','Out1'=>0,
        'F2' => 0, 'T2' => 0, 'R2' => 0, 'P2' => 0, 'V2' => 0, 'C2' => '','Out2'=>0,
        'Tiempo' => '0.0',
        'Velocidad' => '0.0',
        'Penalizacion' => 800.0,
        'Calificacion' => '-',
        'CShort' => '-',
        'Puesto' => '-'
    );

    public function pct_setParameters($mangas,$results,$mode,$title) {
        $dbobj=new DBObject("print_clasificacionEquipos");
        $this->manga1=null;
        $this->manga2=null;
        $this->trs1=null;
        $this->trs2=null;
        if ($mangas[0]!=0) {
            $this->manga1=$dbobj->__getObject("mangas",$mangas[0]);
            $this->trs1=$results['trs1'];
        }
        if ($mangas[1]!=0) {
            $this->manga2=$dbobj->__getObject("mangas",$mangas[1]);
            $this->trs2=$results['trs2'];
        }
        $this->categoria=$this->getModeString(intval($mode));
        $this->equipos=$results['equipos']; // recuerda que YA viene indexado por puesto
        // insertamos perros dentro de cada equipo.
        // para ello vamos a crear un array indexado por teamID
        $teams=array();
        foreach ($this->equipos as &$equipo) {
            $equipo['Perros']=array();
            $teams[$equipo['ID']]=$equipo;
        }
        // iteramos los perros insertandolos en el equipo. Recuerda que los perros ya vienen ordenados por resultados
        foreach($results['individual'] as &$perro) {
            if (!array_key_exists($perro['Equipo'],$teams)) {
                $this->myLogger->error("Prueba:{$this->prueba->ID} Jornada:{$this->jornada->ID} ".
                    "El perro {$perro['Perro']} esta asignado al equipo:{$perro['Equipo']} que no pertenece a la jornada");
                continue; // skip this item, to avoid pdf generation error
            }
            array_push($teams[$perro['Equipo']]['Perros'],$perro);
        }
        $this->equipos=$teams;
        $this->headerTitle=$title." - ".$this->categoria;
    }

	 /** Constructor
      * @param {int|obj} $prueba prueba id
      * @param {int|obj} $jornada jornada id
	 * @throws Exception
	 */
	function __construct($prueba,$jornada) {
		parent::__construct('Landscape',"print_clasificacion_teams",$prueba,$jornada);
	}

    function print_datosMangas() {

        // objeto para buscar jueces
        $jobj=new Jueces("print_Clasificaciones");

        // imprimimos informacion de la manga
        $this->setXY(10,37);
        $this->SetFont($this->getFontName(),'B',11); // bold 9px
        $this->Cell(80,5,_('Journey').": {$this->jornada->Nombre}",0,0,'',false);
        $this->Ln(6);
        $this->Cell(80,5,_('Date').": {$this->jornada->Fecha}",0,0,'',false);
        $this->Ln(6);
        // as some round can be null check both them to try to retrieve "Tipo"
        if ($this->manga1!==null)
            $ronda=_(Mangas::getTipoManga($this->manga1->Tipo,4,$this->federation)); // la misma que la manga 2
        else if ($this->manga2!==null)
            $ronda=_(Mangas::getTipoManga($this->manga2->Tipo,4,$this->federation)); // la misma que la manga 1
        else $ronda= "(undefined)";
        $this->Cell(80,5,_('Round').": $ronda - {$this->categoria}",0,0,'',false);

        // ahora los datos de cada manga individual
        // manga 1:
        if ($this->manga1!=null) {
            // pintamos los datos de TRS
            $trs=$this->trs1;
            $this->setXY(80,37);
            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell(90,8,"","LTB",0,'L',false);// caja vacia
            $this->Cell(20,8,_('Dist').".: {$trs['dist']}m","LTB",0,'L',false);
            $this->Cell(20,8,_('Obst').".: {$trs['obst']}","LTB",0,'L',false);
            $this->Cell(25,8,_('SCT').": {$trs['trs']}s","LTB",0,'L',false);
            $this->Cell(25,8,_('MCT').": {$trs['trm']}s","LTB",0,'L',false);
            $this->Cell(25,8,_('Vel').".: {$trs['vel']}m/s","LTRB",0,'L',false);
            // ahora el nombre de la manga y los jueces
            $nmanga=_(Mangas::getTipoManga($this->manga1->Tipo,3,$this->federation)) . " - " . $this->categoria;
            $juez1=$jobj->selectByID($this->manga1->Juez1); $juez2=$jobj->selectByID($this->manga1->Juez2);
            $this->setXY(81,38);
            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell( 88,4,$nmanga,"",0,'L',false);
            $this->setXY(81,41);
            $this->SetFont($this->getFontName(),'I',8); // bold 9px
            $jueces = _('Judge') .": ". $juez1['Nombre'];
            $jueces .= ($juez2['Nombre']==="-- Sin asignar --")? "" : " - {$juez2['Nombre']}";
            $this->Cell( 88,4,$jueces,"",0,'R',false);
        } else { $this->Ln(8); }
        // manga 2:
        if ($this->manga2!=null) {
            // pintamos los datos de TRS
            $trs = $this->trs2;
            $this->setXY(80, 45);
            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell(90,8,"","LTB",0,'L',false);// caja vacia
            $this->Cell(20, 8, _('Dist') . ".: {$trs['dist']}m", "LTB", 0, 'L', false);
            $this->Cell(20, 8, _('Obst') . ".: {$trs['obst']}", "LTB", 0, 'L', false);
            $this->Cell(25, 8, _('SCT') . ": {$trs['trs']}s", "LTB", 0, 'L', false);
            $this->Cell(25, 8, _('MCT') . ": {$trs['trm']}s", "LTB", 0, 'L', false);
            $this->Cell(25, 8, _('Vel') . ".: {$trs['vel']}m/s", "LTBR", 0, 'L', false);
            // ahora el nombre de la manga y los jueces
            $nmanga=_(Mangas::getTipoManga($this->manga2->Tipo,3,$this->federation)) . " - " . $this->categoria;
            $juez1=$jobj->selectByID($this->manga2->Juez1); $juez2=$jobj->selectByID($this->manga2->Juez2);
            $this->setXY(81,46);
            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell( 88,4,$nmanga,"",0,'L',false);
            $this->setXY(81,49);
            $this->SetFont($this->getFontName(),'I',8); // bold 9px
            $jueces = _('Judge') .": ". $juez1['Nombre'];
            $jueces .= ($juez2['Nombre']==="-- Sin asignar --")? "" : " - {$juez2['Nombre']}";
            $this->Cell( 88,4,$jueces,"",0,'R',false);
        } else { $this->Ln(8); }
        $this->Ln();
    }

	// on second and consecutive pages print a short description to avoid sheet missorder
	function print_datosMangas2() {
		$this->SetXY(35,15);
		$this->SetFont($this->getFontName(),'B',11); // bold 9px
		$this->Cell(80,7,"{$this->jornada->Nombre}",0,0,'',false);
		$this->SetXY(35,20);
		$this->Cell(80,7,"{$this->jornada->Fecha}",0,0,'',false);
		$ronda=_(Mangas::getTipoManga($this->manga1->Tipo,4,$this->federation)); // la misma que la manga 2
		$this->SetXY(35,25);
		$this->Cell(80,7,"$ronda - {$this->categoria}",0,0,'',false);
	}

	function Header() {
		$this->print_commonHeader($this->headerTitle);
		// si primera pagina, podium o estadisticas imprimimos datos de la manga
        if ($this->PageNo()==1) { $this->print_datosMangas(); return; }
        if ( strstr($this->headerTitle,_("Podium"))!==FALSE ) { $this->print_datosMangas(); return; }
        if ( strstr($this->headerTitle,_("Statistics"))!==FALSE ) { $this->print_datosMangas(); return; }
        // si no, solo ponemos una cabecera simple
        $this->print_datosMangas2();
	}
	
	// Pie de página: tampoco cabe
	function Footer() {
		$this->print_commonFooter();
	}

    function print_stats() {
	    $this->headerTitle=_("Statistics");
	    $this->AddPage(); // inner call to print_datosMangas()
        $this->Ln(10);
        $tm1=($this->manga1!==null)?_(Mangas::getTipoManga($this->manga1->Tipo,3,$this->federation)):"";
        $tm2=($this->manga2!==null)?_(Mangas::getTipoManga($this->manga2->Tipo,3,$this->federation)):"";
        $this->myStats->print_statsHeader(array($tm1,$tm2,"","","","","",""));
        $this->myStats->print_statsData();
    }

    function printTeamInformation($teamcount,$team) {
        // evaluate logos
        $logos=array('null.png','null.png','null.png','null.png','null.png');
        if ($team['Nombre']==="-- Sin asignar --") {
            $logos[0]=getIconPath($this->federation->get('Name'),"agilitycontest.png");
        } else {
            $count=0;
            for ($n=0;$n<count($team['Perros']);$n++) {
                $miembro=$team['Perros'][$n]['Perro'];
                $logo=$this->getLogoName(intval($miembro));
                if ( ( ! in_array($logo,$logos) ) && ($count<5) ) $logos[$count++]=$logo;
            }
        }
        $this->ac_header(1,18);
		$this->Cell(15,8,strval(1+$teamcount)." -",'LT',0,'C',true); // imprime puesto del equipo
		$x=$this->getX();
		$y=$this->getY();
        // if no logo is "null.png" don't try to insert logo, just add empty text with parent background
		for ($n=0;$n<$this->getMaxDogs();$n++) {
			if ($logos[$n]==="null.png") {
				$this->SetX($x+10*$n);
				$this->Cell(10,7,"",'T',0,'C',true);
			} else {
				$this->Image($logos[$n],$x+10*$n,$y,7);
			}
		}
		$this->SetX($x+40);
        $this->Cell(212,7,$team['Nombre'],'T',0,'R',true);
        $this->Cell(8,7,'','TR',0,'R',true); // empty space at right of page
        $this->Ln();
    }

    function writeTableHeader() {
		$wide=$this->federation->get('WideLicense');
        if ($this->useLongNames) $wide=false;
		$tm1=_(Mangas::getTipoManga($this->manga1->Tipo,3,$this->federation));
		$tm2=null;
		if ($this->manga2!==null) $tm2=_(Mangas::getTipoManga($this->manga2->Tipo,3,$this->federation));
		
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
        if ($this->useLongNames){ // long names means skip license
            $this->Cell(40,4,_('Name'),'B',0,'C',true);	// nombre (20,y
        } else {
            $this->Cell(($wide)?15:25,4,_('Name'),'B',0,'C',true);	// nombre (20,y
            $this->Cell(($wide)?25:15,4,_('Lic'),'B',0,'C',true);	// licencia
        }
		$this->Cell(8,4,_('Cat'),'B',0,'C',true);	// categoria ( en equipos no se considera el grado )
		$this->Cell(30,4,_('Handler'),'B',0,'C',true);	// nombreGuia
		$this->Cell(16,4,$this->strClub,'B',0,'C',true);	// nombreClub
		// manga 1
        if ($this->manga1!==null) {
		    $this->Cell(5,4,_('F/T'),'B',0,'C',true);	// 1- Faltas+Tocados
		    $this->Cell(5,4,_('Ref'),'B',0,'C',true);	// 1- Rehuses
		    $this->Cell(10,4,_('Time'),'B',0,'C',true);	// 1- Tiempo
		    $this->Cell(7,4,_('Vel'),'B',0,'C',true);	// 1- Velocidad
		    $this->Cell(10,4,_('Penal'),'B',0,'C',true);	// 1- Penalizacion
		    $this->Cell(10,4,_('Calif'),'B',0,'C',true);	// 1- calificacion
        } else {
            $this->Cell(47,4,'','B',0,'C',true);	// espacio en blanco
        }
		// manga 2
		if ($this->manga2!==null) {
			$this->Cell(5,4,_('F/T'),'B',0,'C',true);	// 2- Faltas+Tocados
			$this->Cell(5,4,_('Ref'),'B',0,'C',true);	// 2- Rehuses
			$this->Cell(10,4,_('Time'),'B',0,'C',true);	// 2- Tiempo
			$this->Cell(7,4,_('Vel'),'B',0,'C',true);	// 2- Velocidad
			$this->Cell(10,4,_('Penal'),'B',0,'C',true);	// 2- Penalizacion
			$this->Cell(10,4,_('Calif'),'B',0,'C',true);	// 2- calificacion
		} else {
			$this->Cell(47,4,'','B',0,'C',true);	// espacio en blanco
		}
		// global individual
		$this->Cell(9,4,_('Time'),'B',0,'C',true);	// Tiempo total
		$this->Cell(9,4,_('Penaliz'),'B',0,'C',true);	// Penalizacion
		$this->Cell(9,4,_('Calific'),'B',0,'C',true);	// Calificacion
		$this->Cell(7,4,_('Position'),'B',0,'C',true);	// Puesto
        // global equipos
        $this->Cell(21,4,_('Round'),'B',0,'C',true);	// Manga
        $this->Cell(12,4,_('Time'),'B',0,'C',true);	// Tiempo
        $this->Cell(12,4,_('Penaliz'),'BR',0,'C',true);// Penalizacion
		$this->Ln();
	}
	
	function writeCell($idx,$row,$team,$last) {
	    // $this->myLogger->trace("row: ".json_encode($row));
		$wide=$this->federation->get('WideLicense');
		if ($this->useLongNames) $wide=false;
        $this->ac_row($idx,8);
        $border=($idx==$last)?'B':'';
        $borderl=($idx==$last)?'LB':'L';
		if ( ($row==$this->defaultPerro) && ($idx>=$this->getMinDogs() ) ){
			// no dogs, and no dog to show as "no inscrito"
			$this->Cell(230,4,'',0,0,'',false);
		} else {
			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
			// fomateamos datos
			$puesto= ($row['Penalizacion']>=200)? "-":"{$row['Puesto']}º";
			$penal=number_format2($row['Penalizacion'],$this->timeResolution);
			$v1= ($row['P1']>=200)?"-":number_format2($row['V1'],1);
			$t1= ($row['P1']>=200)?"-":number_format2($row['T1'],$this->timeResolution);
			$p1=number_format2($row['P1'],$this->timeResolution);
			$v2= ($row['P2']>=200)?"-":number_format2($row['V2'],1);
			$t2= ($row['P2']>=200)?"-":number_format2($row['T2'],$this->timeResolution);
			$p2=number_format2($row['P2'],$this->timeResolution);

			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)

			$this->SetFont($this->getFontName(),'',8); // default font
			// datos del participante
			$this->Cell(8,4,$row['Dorsal'],$borderl,0,'L',true); 	// dorsal
            if ($this->useLongNames) {
                $this->SetFont($this->getFontName(),'B',8); // Display Nombre in bold typeface
                $nombre=$row['Nombre']." - ".$row['NombreLargo'];
                $this->Cell(40,4,$nombre,$border,0,'L',true);	// nombre (20,y
            } else {
                $this->SetFont($this->getFontName(),'B',8); // Display Nombre in bold typeface
                $this->Cell(($wide)?15:25,4,$row['Nombre'],$border,0,'L',true);	// nombre (20,y
                $this->SetFont($this->getFontName(),'',($wide)?6:8); // default font for licencia
                $this->Cell(($wide)?25:15,4,$row['Licencia'],$border,0,'C',true);	// licencia
            }
			$this->SetFont($this->getFontName(),'',8); // default font
			$this->Cell(8,4,"{$row['Categoria']}",$border,0,'C',true);	// categoria/grado
			$this->Cell(30,4,$this->getHandlerName($row),$border,0,'R',true);	// nombreGuia
			$this->Cell(16,4,$row['NombreClub'],$border,0,'R',true);	// nombreClub
			// manga 1
			if ($this->manga1!==null) {
			    $this->SetTextColor( ($row['Out1']==0)?0:128 );
				$this->Cell(5,4,$row['F1'],$borderl,0,'C',true);	// 1- Faltas+Tocados
				$this->Cell(5,4,$row['R1'],$border,0,'C',true);	// 1- Rehuses
				$this->Cell(10,4,$t1,$border,0,'C',true);	// 1- Tiempo
				$this->Cell(7,4,$v1,$border,0,'C',true);	// 1- Velocidad
				$this->Cell(10,4,$p1,$border,0,'C',true);	// 1- Penalizacion
				$this->Cell(10,4,$row['C1'],$borderl,0,'C',true);	// 1- calificacion
			} else {
				$this->Cell(47,4,'',$borderl,0,'C',true);	// espacio en blanco
			}
			// manga 2
			if ($this->manga2!==null) {
                $this->SetTextColor( ($row['Out2']==0)?0:128 );
				$this->Cell(5,4,$row['F2'],$borderl,0,'C',true);	// 2- Faltas+Tocados
				$this->Cell(5,4,$row['R2'],$border,0,'C',true);	// 2- Rehuses
				$this->Cell(10,4,$t2,$border,0,'C',true);	// 2- Tiempo
				$this->Cell(7,4,$v2,$border,0,'C',true);	// 2- Velocidad
				$this->Cell(10,4,$p2,$border,0,'C',true);	// 2- Penalizacion
				$this->Cell(10,4,$row['C2'],$border,0,'C',true);	// 2- calificacion
			} else {
				$this->Cell(47,4,'',$borderl,0,'C',true);	// espacio en blanco
			}
			// global
            $this->SetTextColor(0);
			$this->Cell(9,4,number_format2($row['Tiempo'],$this->timeResolution),$borderl,0,'C',true);	// Tiempo
			$this->Cell(9,4,number_format2($penal,$this->timeResolution),$border,0,'C',true);	// Penalizacion
			$this->Cell(9,4,$row['Calificacion'],$borderl,0,'C',true);	// Calificacion
			$this->SetFont($this->getFontName(),'B',8); // mark "puesto" in bold typeface
			$this->Cell(7,4,$puesto,$borderl.'R',0,'C',true);	// Puesto
		}
        // datos de la global por equipos
        $this->ac_header(2,8);
        switch($idx){
            case 0: // manga 1
                $this->SetFont($this->getFontName(),'BI',8); // default font
                $this->Cell(21,4,_(Mangas::getTipoManga($this->manga1->Tipo,3,$this->federation)),"L",0,'L',true);	// nombre manga 1
                $this->SetFont($this->getFontName(),'',8); // default font
                $this->Cell(12,4,number_format2($team['T1'],$this->timeResolution),0,0,'R',true);	// tiempo manga 1
                $this->Cell(12,4,number_format2($team['P1'],$this->timeResolution),'R',0,'R',true);	// penalizacion manga 1
                break;
            case 1: // manga 2
                $this->SetFont($this->getFontName(),'BI',8); // default font
                $this->Cell(21,4,_(Mangas::getTipoManga($this->manga2->Tipo,3,$this->federation)),"L",0,'L',true);	// nombre manga 2
                $this->SetFont($this->getFontName(),'',8); // default font
                $this->Cell(12,4,number_format2($team['T2'],$this->timeResolution),0,0,'R',true);	// tiempo manga 2
                $this->SetFont($this->getFontName(),'',8); // default font
                $this->Cell(12,4,number_format2($team['P2'],$this->timeResolution),'R',0,'R',true);	// penalizacion manga 2
                break;
            case 2: // global
                $this->SetFont($this->getFontName(),'BI',8); // default font
                $this->Cell(21,4,_("Final"),'LB',0,'L',true);
                $this->SetFont($this->getFontName(),'B',8); // default font
                $this->Cell(12,4,number_format2($team['Tiempo'],$this->timeResolution),'B',0,'R',true);	// tiempo final
                $this->Cell(12,4,number_format2($team['Penalizacion'],$this->timeResolution),'RB',0,'R',true);	// penalizacion final
                break;
            case 3: // puntos (si se requieren )
                if (!array_key_exists('Puntos',$team) || $team['Puntos']==0) break;
                $this->SetFont($this->getFontName(),'BI',8); // default font
                $this->Cell(21,4,_("Points"),'LB',0,'L',true);
                $this->SetFont($this->getFontName(),'B',8); // default font
                $this->Cell(12,4,"",'B',0,'R',true);	// tiempo final
                $this->Cell(12,4,$team['Puntos'],'RB',0,'R',true);	// penalizacion final
                break;
            case 4: // no usada. sirve para pruebas de cinco perros/equipo
                break;
        }
		$this->Ln(4);
	}

    /**
     * Imprime la tabla de clasificaciones hasta un maximo de $limit entradas
     * @param int $limit. 0:any; else print number teams ( used in podium )
     */
    function composeTable($limit=0) {
        $this->myLogger->enter();

        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
        $this->SetLineWidth(.3);

        // Datos
        $teamcount=0;
        $this->AddPage();
        $this->SetXY(10,60);
        foreach($this->equipos as $equipo) {
            if ( ($limit!=0) && ($teamcount>=$limit) ) break;
            $numdogs=count($equipo['Perros']);
            $this->myLogger->trace("Equipo: {$equipo['Nombre']} numdogs: {$numdogs}");
            // si el equipo no tiene participantes es que la categoria no es válida: skip
            if ($numdogs==0) continue;
            $size=2/*newline*/+7/*teaminfo*/+8/*header*/+4*$numdogs;
            // si no nos va a caber el equipo, saltamos pagina
            $y=$this->GetY();

            if ($y+$size>200) {
                $this->AddPage();
                $this->SetXY(10,40);
            }
            // $this->myLogger->trace("imprimiendo datos del equipo {$equipo['ID']} - {$equipo['Nombre']}");
            $this->printTeamInformation($teamcount,$equipo);
            $this->writeTableHeader(); // print team header/data
            // con independencia de los perros del equipo - normalmente $this->getMaxDogs()
            // imprimiremos siempre al menos 4 columnas (ag,jp,final,puntos)
            $count=max(4,count($equipo['Perros'])); // allways use at least 4 cells
            for ($n=0;$n<$count;$n++) {
                $row=$this->defaultPerro;
                if (array_key_exists($n,$equipo['Perros'])) $row=$equipo['Perros'][$n];
                // print team member's result
                // $this->myLogger->trace("imprimiendo datos del perro {$row['Perro']} - {$row['Nombre']}");
                $this->writeCell($n,$row,$equipo,$count-1);
            }
            $teamcount++; // extra space between teams depends of max dogx to use the entire page
            $this->Ln(2*(6-$this->getMaxDogs()) );
        }
        $this->myLogger->leave();
    }
}

?>