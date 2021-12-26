<?php
require_once (__DIR__."/PrintInscripciones.php");

class PrintCatalogo extends PrintInscripciones {
    protected $inscritos;
    protected $jornadas;

    protected $width;
    protected $cellHeader;

    /**
     * Constructor
     * @param {integer} $prueba Prueba ID
     * @param {array} $inscritos Lista de inscritos en formato jquery array[count,rows[]]
     * @throws Exception
     */
    function __construct($prueba,$inscritos,$jornadas) {
        parent::__construct('Portrait',"print_catalogo",$prueba,0);
        if ( ($prueba==0) || ($inscritos===null) ) {
            $this->errormsg="printInscritosByPrueba: either prueba or inscription data are invalid";
            throw new Exception($this->errormsg);
        }
        /* make sure that catalog is group'd by club  */
        $this->inscritos=$inscritos['rows'];
        usort($this->inscritos, function($a, $b) {
            if ( strcmp($a['NombreClub'],$b['NombreClub']) == 0) return ($a['Dorsal']>$b['Dorsal'])?1:-1;
            return strcmp($a['NombreClub'],$b['NombreClub']);
        });
        $this->jornadas=$jornadas['rows'];
        $this->set_FileName("Catalogo_inscripciones.pdf");
        // ajustamos campos del catalogo
        $this->cellHeader=
            array(_('Name'),_('Breed'),_('License'),_('Cat').'/'._('Grado'),_('Handler'),_('Cat'), _('Comments'),'J1','J2','J3','J4','J5','J6','J7','J8');
        // nombre raza licencia cat/grad guia comments
        switch ($this->federation->getLicenseType()) {
            case Federations::$LICENSE_REQUIRED_NONE: // en pruebas no federacion no hay ni licencia ni grado del parro, pero si categoria del guia
                    //  0         1         2         3          4         5        6       7    8   9  10  11  12  13  14
                    // name     breed    License     cat        handler   catGuia  comments J1  J2  J3  J4  J5  J6  J7  J8
                $this->width =
                    array(42,    18,       0,         19,      33,        10,     0,       6,  6,  6,  6,   6,  6,  6,  6);
                    $this->cellHeader[3]=_('Category');
                break;
            case Federations::$LICENSE_REQUIRED_WIDE: // en pruebas de caza la licencia es larga , el nombre corto y la categoria del guia se junta con el nombre
                //  0         1             2         3             4         5       6       7    8   9  10  11  12  13  14
                // name     breed       License     catgrad      handler   catGuia  comments  J1  J2  J3  J4  J5  J6  J7  J8
                $this->width =
                    array(28,    15,       24,         18,         27,        10,     0,      6,  6,  6,  6,   6,  6,  6,  6);
                break;
            default:
                //  0         1             2         3             4         5     6       7    8   9  10  11  12  13  14
                // name     breed       License    catgrad      handler   catGuia  comments J1  J2  J3  J4  J5  J6  J7  J8
                $this->width =
                    array(35,    15,       16,         19,         27,       10,    0,      6,  6,  6,  6,   6,  6,  6,  6);
                break;
        }
        // las jornadas vacias, las vamos metiendo en el campo comentarios
        // las que tienen datos, ajustamos el nombre
        $skip=intval($this->config->getEnv('pdf_journeys'));
        foreach($this->jornadas as $jornada) {
            $index=6+$jornada['Numero'];
            // contamos las jornadas sin asignar
            if (($skip==0) || ($jornada['Nombre']==='-- Sin asignar --')) {
                $this->cellHeader[$index]='';
                $this->width[6]+=$this->width[$index];
                $this->width[$index]=0;
            } else {
                $this->cellHeader[$index]=$jornada['Nombre'];
            }
        }
    }

    // Cabecera de página
    function Header() {
        $this->myLogger->enter();
        $this->SetTextColor(0,0,0); // negro
        $this->print_commonHeader(_('Contest catalog'));
        $this->Ln(5);
        $this->myLogger->leave();
    }

    // Pie de página
    function Footer() {
        $this->print_commonFooter();
    }

    function printClub($cmgr,$fedName,$id) {
        $y=$this->GetY();
        // retrieve club data
        $club=$cmgr->selectByID($id);

        // evaluate logo
        $icon=getIconPath($fedName,"agilitycontest.png");
        if ( $club['Logo']==="") {
            $this->myLogger->error("inscritosByPrueba::printClub() club:$id {$club['Nombre']} no logo declared");
            $icon = getIconPath($fedName, $this->federation->get('Logo')); // default is federation logo
        } else {
            $icon = $icon = getIconPath($fedName, $club['Logo']);
        }
        $this->myLogger->trace("ID:".$id." Club: ".$club['Nombre']);

        $this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // azul
        $this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg1')); // blanco
        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
        $this->SetLineWidth(.3); // ancho de linea

        // pintamos logo
        $this->SetXY(10,$y);
        $this->Cell(22,22,'','LTB',0,'C',false);
        $this->Image($icon,12,2+$y,18,18);

        // pintamos info del club
        $this->SetFont($this->getFontName(),'B',9);
        $this->SetXY(32,$y);
        $this->Cell( 50, 5, $club['Direccion1'],	'LT', 0, 'L', true); // pintamos direccion1
        $this->SetXY(32,5+$y);
        $this->Cell( 50, 5, $club['Direccion2'],	'L', 0, 'L',	true);	// pintamos direccion2
        $this->SetXY(32,10+$y);
        $prov=$club['Provincia'];
        if ($prov==="-- Sin asignar --") $prov="";
        $this->Cell( 50, 5,$prov ,	'L', 0, 'L',	true);	// pintamos provincia
        $this->SetFont($this->getFontName(),'IB',24);
        $this->SetXY(82,$y);
        $this->Cell( 110, 15, $club['Nombre'],	'T', 0, 'R',	true);	// pintamos Nombre
        $this->Cell( 10, 15, '',	'TR', 0, 'R',	true);	// caja vacia de relleno

        // pintamos cabeceras de la tabla
        $this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg2')); // gris
        $this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg2')); // negro
        $this->SetFont($this->getFontName(),'B',9);
        $this->SetXY(32,15+$y);
        $this->Cell( $this->width[0], 7, $this->cellHeader[0],'LTB', 0, 'C',true); // nombre
        $this->Cell( $this->width[1], 7, $this->cellHeader[1],'LTB', 0, 'C',true); // raza
        if ($this->width[2]>0) // skip license on international contests
            $this->Cell( $this->width[2], 7,  $this->cellHeader[2],'LTB', 0, 'C',true); // licencia
        $this->Cell( $this->width[3], 7, $this->cellHeader[3],'LTB', 0, 'C',true); // cat [ / grad ]
        $this->Cell( $this->width[4], 7, $this->cellHeader[4],'LTBR', 0, 'C',true); // Guia
        $this->Cell( $this->width[5], 7, $this->cellHeader[5],'LTBR', 0, 'C',true); // Catguia
        if ($this->width[6]>0)
            $this->Cell( $this->width[6], 7, $this->cellHeader[6],'LTBR', 0, 'C',true); // Comments
        // print names of each declared journeys
        for($i=7;$i<count($this->width);$i++) {
            // en la cabecera texto siempre centrado
            if ($this->width[$i]<=0) continue;
            $this->Cell($this->width[$i],7,$this->cellHeader[$i],1,0,'C',true);
        }
        $this->Ln();
    }

    function printParticipante($count,$row) {
        // evaluate data to be printed
        // nombre del perro
        $name= $row['Nombre'];
        if (!is_null($row['NombreLargo']) && $row['NombreLargo']!=="") $name = $name . " - " .$row['NombreLargo'];
        else $row['NombreLargo']="";
        // grado. miramos si la federacion tiene licencia/grado y si en la configuracion se pide imprimir grado
        $grad="";
        if  ($this->federation->getLicenseType()!=Federations::$LICENSE_REQUIRED_NONE) {
            if ( intval($this->config->getEnv("pdf_grades"))!=0) { // if config requires print grade
                $grad=" - {$this->federation->getGradeShort($row['Grado'])}";
                if ($grad==" - -") $grad="";
            }
        }
        // categoria del perro
        $cat=$this->federation->getCategoryShort($row['Categoria']);
        // print hidden json data to allow exporting
        $this->printHiddenRowData($count,$row);

        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
        $this->SetLineWidth(.3); // ancho de linea

        $this->SetX(20);
        // REMINDER: $this->cell( width, height, data, borders, where, align, fill)
        $this->SetFont($this->getFontName(),'B',12); //
        // Dorsal
        $this->Cell( 12, 7, $row['Dorsal'],	'TLB', 0, 'C',	true);
        // nombre del perro
        $this->SetFont($this->getFontName(),'BI',8); // bold 9px italic
        $this->Cell( $this->width[0], 7, " {$name}",	'LB', 0, 'L',	true);
        // raza
        $this->SetFont($this->getFontName(),'',7); // normal 8px
        $this->Cell( $this->width[1], 7, $row['Raza'],		'LB', 0, 'C',	true);
        // licencia ( si requerida )
        if ($this->width[2]>0) {
            if ($this->federation->hasWideLicense()) $this->SetFont($this->getFontName(),'',6); // bold 6px
            $this->Cell( $this->width[2], 7, $row['Licencia'],	'LB', 0, 'C',	true);
        }
        // categoria [ / grado ]
        $this->SetFont($this->getFontName(),'',7); // notmal 7px
        $this->Cell( $this->width[3], 7, $cat.$grad,	'LB', 0, 'C',	true);
        // nombre y categoria del guia
        $this->SetFont($this->getFontName(),'B',7); // bold 7px
        if ($this->width[5]>0) {
            $this->Cell( $this->width[4], 7, $row['NombreGuia'],'LBR', 0, 'R',	true);
            $this->SetFont($this->getFontName(),'',7); // bold 7px
            $this->Cell( $this->width[5], 7, $this->getHandlerCategory($row),'LBR', 0, 'R',	true);
        } else {
            $this->Cell( $this->width[4], 7, $this->getHandlerName($row),'LBR', 0, 'R',	true);
        }
        // comentarios
        $this->SetFont($this->getFontName(),'',7); // bold 7px
        if ($this->width[6]>0) {
            $this->Cell( $this->width[6], 7, $row['Observaciones'],'LBR', 0, 'R',	true);
        }
        // print inscrption data on each declared journeys
        for($i=7;$i<count($this->width);$i++) {
            // en la cabecera texto siempre centrado
            if ($this->width[$i]==0) continue;
            $j=$i-6;
            $this->Cell($this->width[$i],7,($row["J$j"]==0)?"No":"Si",'LBR',0,'C',true);
        }
        $this->Ln(7);
    }

    function composeTable() {
        $this->myLogger->enter();

        $this->AddPage(); // start page
        $club=0;
        $count=0;
        $fedName=$this->federation->get('Name');
        $cmgr=new Clubes('printCatalogo',$this->prueba->RSCE);
        foreach($this->inscritos as $row) {
            $pos = $this->GetY();
            if (($club == $row['Club'])) {
                // no hay cambio de club
                if ($pos > 270) {
                    $this->AddPage();
                    $this->printClub($cmgr,$fedName,$club);
                    $count = 0;
                }
            } else {
                $club = $row['Club'];
                // cambio de club
                $this->ln(7); // extra newline
                if ($pos > 250) $this->AddPage();
                $this->printClub($cmgr,$fedName,$club);
                $count = 0;
            }
            $this->printParticipante($count, $row);
            $count++;
        }
        $this->myLogger->leave();
    }
}
?>