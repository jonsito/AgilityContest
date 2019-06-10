<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 1/02/19
 * Time: 11:53
 */

class PrintEtiquetasCNEAC extends PrintCommon  {

    protected $manga1;
    protected $manga2;
    protected $juez11;
    protected $juez12;
    protected $juez21;
    protected $juez22;

    protected $data = array(
        // datos generales
        'Organizer' => array ( 0.15,    0.17, "Organizador"),
        'Country'   => array ( 0.55,    0.17, "País"),
        'Date'      => array ( 0.83,    0.17, 'Fecha'),
        'Name'      => array ( 0.07,    0.21, 'Nombre'),
        'LOE_RRC'   => array ( 0.40,    0.21, 'LOE_RRC'),
        'Breed'     => array ( 0.62,    0.21, 'Raza'),
        'Gender'    => array ( 0.91,    0.21, 'Género'),
        'Handler'   => array ( 0.11,    0.25, 'Guía'),
        'Club'      => array ( 0.46,    0.25, 'Club'),
        'Province'  => array ( 0.77,    0.25, 'Provincia'),
        'License'   => array ( 0.11,    0.29, 'Licencia'),
        'Dorsal'    => array ( 0.36,    0.29, 'Dorsal'),
        'Category'  => array ( 0.60,    0.29, 'Categoria'),
        'Grade'     => array ( 0.82,    0.29, 'Grado'),
        // datos de la primera manga
        'Juez11'            => array ( 0.10,    0.45, 'Juez1 Agility'),
        'Juez12'            => array ( 0.10,    0.51, 'Juez2 Agility'),
        'Participantes1'    => array ( 0.28,    0.45, 'Num'),
        'Longitud1'         => array ( 0.36,    0.45, 'Long'),
        'Obstaculos1'       => array ( 0.43,    0.45, 'Obst'),
        'TRS1'              => array ( 0.50,    0.45, 'TRS'),
        'TRM1'              => array ( 0.54,    0.45, 'TRM'),
        'T1'                => array ( 0.59,    0.45, 'Tiempo'),
        'V1'                => array ( 0.65,    0.45, 'Veloc'),
        'PTiempo1'          => array ( 0.71,    0.45, 'P.Tiem'),
        'PRecorrido1'       => array ( 0.78,    0.45, 'P.Rec'),
        'P1'                => array ( 0.84,    0.45, 'Penal'),
        'Puesto1'           => array ( 0.91,    0.45, 'Puesto'),
        'C1'                => array ( 0.95,    0.45, 'Calif'),
        // datos de la segunda manga
        'Juez21'            => array ( 0.10,    0.63, 'Juez1 Jumping'),
        'Juez22'            => array ( 0.10,    0.69, 'Juez2 Jumping'),
        'Participantes2'    => array ( 0.28,    0.63, 'Num'),
        'Longitud2'         => array ( 0.36,    0.63, 'Long'),
        'Obstaculos2'       => array ( 0.43,    0.63, 'Obst'),
        'TRS2'              => array ( 0.50,    0.63, 'TRS'),
        'TRM2'              => array ( 0.54,    0.63, 'TRM'),
        'T2'                => array ( 0.59,    0.63, 'Tiempo'),
        'V2'                => array ( 0.65,    0.63, 'Veloc'),
        'PTiempo2'          => array ( 0.71,    0.63, 'P.Tiem'),
        'PRecorrido2'       => array ( 0.78,    0.63, 'P.Rec'),
        'P2'                => array ( 0.84,    0.63, 'Penal'),
        'Puesto2'           => array ( 0.91,    0.63, 'Puesto'),
        'C2'                => array ( 0.95,    0.63, 'Calif')
    );

    /**
     * Constructor
     * @param {integer} $prueba Prueba ID
     * @param {integer} $jornada Jornada ID
     * @param {integer} $m Print mode. 0:Trs/Trm evaluation calc sheet 1:Trsdata template to enter data
     * @throws Exception
     */
    function __construct($prueba,$jornada,$mangas) {
        date_default_timezone_set('Europe/Madrid');
        parent::__construct('Portrait',"print_cneac",$prueba,$jornada);
        if ( ($prueba<=0) || ($jornada<=0) ) {
            $this->errormsg="printTemplates: either prueba or jornada data are invalid";
            throw new Exception($this->errormsg);
        }
        $this->manga1=($mangas[0]!=0)?$this->myDBObject->__getObject("mangas",$mangas[0]):null;
        $this->manga2=($mangas[1]!=0)?$this->myDBObject->__getObject("mangas",$mangas[1]):null;
        $this->juez11=($mangas[0]!=0)?$this->myDBObject->__getArray('jueces',$this->manga1->Juez1):array('ID'=>1,'Nombre'=>"-- Sin asignar --");
        $this->juez12=($mangas[0]!=0)?$this->myDBObject->__getArray('jueces',$this->manga1->Juez2):array('ID'=>1,'Nombre'=>"-- Sin asignar --");
        $this->juez21=($mangas[1]!=0)?$this->myDBObject->__getArray('jueces',$this->manga2->Juez1):array('ID'=>1,'Nombre'=>"-- Sin asignar --");
        $this->juez22=($mangas[1]!=0)?$this->myDBObject->__getArray('jueces',$this->manga2->Juez2):array('ID'=>1,'Nombre'=>"-- Sin asignar --");
    }

    function Header() { /* empty */ }

    function Footer() {
        $this->print_commonFooter();
    }

    function getImage() {
        $img =imagecreatefrompng(__DIR__."/../cneac/cneac_result_form.png");

        // colores blanco y negro
        $black=imagecolorallocate($img,0,0,0);

        $font = __DIR__."/../../arial.ttf";
        foreach ( $this->data as $key =>$item) {
            // A4 page is 210*295
            // image size is 1007x715, so scale properly
            $x= intval ( 1003*$item[0]);
            $y= intval ( 715*$item[1]);
            imagettftext($img, 12, 0, $x, $y, $black, $font, $item[2]);
        }
        // finally set contest name and journey at bottom of page
        $name="{$this->prueba->Nombre} - {$this->jornada->Nombre}";
        imagettftext($img, 14, 0, 100, 625, $black, $font, $name);
        return $img;
    }

    /**
     * @param {integer} $rowcount contador par/impar
     * @param {array} $row competitor data
     * @throws Exception
     */
    function writeCell($rowcount,$row) {
        // datos que faltan de la manga
        $this->data['Participantes1'][2]=strval($row['Participantes']);
        $this->data['Participantes2'][2]=strval($row['Participantes']);
        // datos del participante
        $this->data['Name'][2] = "{$row['Nombre']} - {$row['NombreLargo']}";
        $this->data['License'][2] = $row['Licencia'];
        $this->data['LOE_RRC'][2] = $row['LOE_RRC'];
        $this->data['Handler'][2] = $row['NombreGuia'];
        $this->data['Club'][2] = $row['NombreClub'];

        $this->data['Category'][2] = $this->federation->getCategory($row['Categoria']);
        $this->data['Grade'][2] = $this->federation->getGrade($row['Grado']);
        $this->data['Dorsal'][2] = $row['Dorsal'];
        // tenemos que obtener "a mano" los datos de raza y provincia
        $pgc=$this->myDBObject->__getArray("perroguiaclub",$row['Perro']);
        $this->data['Breed'][2] = $pgc['Raza'];
        $this->data['Province'][2] = $pgc['Provincia'];
        $this->data['Gender'][2] = $pgc['Genero'];


        // datos del recorrido del participante
        if ($row['P1']>=100) {
            $this->data['PRecorrido1'][2]="-";
            $this->data['PTiempo1'][2]="-";
        } else {
            $this->data['PRecorrido1'][2]=5*($row['F1']+$row['R1']);
            $this->data['PTiempo1'][2]=number_format2($row['P1']-$this->data['PRecorrido1'][2],2);
        }
        if ($row['P2']>=100) {
            $this->data['PRecorrido2'][2]="-";
            $this->data['PTiempo2'][2]="-";
        } else {
            $this->data['PRecorrido2'][2]=5*($row['F2']+$row['R2']);
            $this->data['PTiempo2'][2]=number_format2($row['P2']-$this->data['PRecorrido2'][2],2);
        }
        $this->data['C1'][2]=$row['C1'];
        $this->data['C2'][2]=$row['C2'];
        $this->data['T1'][2]=number_format2($row['T1'],2);
        $this->data['T2'][2]=number_format2($row['T2'],2);
        $this->data['V1'][2]=number_format2($row['V1'],2);
        $this->data['V2'][2]=number_format2($row['V2'],2);
        $this->data['P1'][2]=number_format2($row['P1'],2);
        $this->data['P2'][2]=number_format2($row['P2'],2);
        $this->data['Puesto1'][2]=$row['Puesto1'];
        $this->data['Puesto2'][2]=$row['Puesto2'];

        $img=$this->getImage();
        $tmpfile=tempnam_sfx(__DIR__."/../../../../logs","cneac_","png");
        imagepng($img,$tmpfile);
        $this->SetX(10);
        $this->SetY(($rowcount==0)?10:150);
        $this->Image($tmpfile,$this->getX(),$this->getY(),190);
        imagedestroy($img);
        @unlink($tmpfile);

        // on first form in page add a separation bar
        if ($rowcount==0) $this->Line(5,147,205,147);
    }

    function setRoundData($resultados) {

        // set template global data for round 1
        if ($this->manga1!=null) {
            $this->data['Juez11'][2]=$this->juez11['Nombre'];
            $this->data['Juez12'][2]=$this->juez21['Nombre'];
            if ($this->juez11['Nombre']==$this->juez12['Nombre']) $this->data['Juez12'][2]="";
            if ($this->juez12['Nombre']=='-- Sin asignar --') $this->data['Juez12'][2]="";
            $this->data['Longitud1'][2]=$resultados['trs1']['dist'];
            $this->data['Obstaculos1'][2]=$resultados['trs1']['obst'];
            $this->data['TRS1'][2]=number_format2($resultados['trs1']['trs'],1); // no space in form for 2 decimal digits
            $this->data['TRM1'][2]=number_format2($resultados['trs1']['trm'],1);
        }
        // set template global data for round 2
        if ($this->manga2!=null) {
            $this->data['Juez21'][2]=$this->juez21['Nombre'];
            $this->data['Juez22'][2]=$this->juez22['Nombre'];
            if ($this->juez21['Nombre']==$this->juez22['Nombre']) $this->data['Juez22'][2]="";
            if ($this->juez22['Nombre']=='-- Sin asignar --') $this->data['Juez22'][2]="";
            $this->data['Longitud2'][2]=$resultados['trs2']['dist'];
            $this->data['Obstaculos2'][2]=$resultados['trs2']['obst'];
            $this->data['TRS2'][2]=number_format2($resultados['trs2']['trs'],1);
            $this->data['TRM2'][2]=number_format2($resultados['trs2']['trm'],1);
        }
        $this->data['Organizer'][2] = $this->club->Nombre;
        $this->data['Date'][2] = $this->jornada->Fecha;
        $this->data['Country'][2] = $this->club->Pais;
    }

    function composeTable($resultados,$rowcount=0,$listadorsales="",$discriminate=1) {
        $this->myLogger->enter();
        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));

        // iterate on available data
        foreach($resultados as $row) {
            if ($listadorsales!=="") {
                $aguja=",{$row['Dorsal']},";
                $pajar=",$listadorsales,";
                if (strpos($pajar,$aguja)===FALSE) continue; // Dorsal not in list
            } else {
                // if country discrimination is active check country and reject on no match
                if ( ($discriminate==1) && $row['Pais']!=="FRA") continue;
                // on double "not present" do not print label
                if ( ($row['P1']>=200.0) && ($row['P2']>=200.0) ) continue;
                // on double "eliminated", ( or eliminated+notpresent ) handle printing label accordind to configuration
                if ( (intval($this->config->getEnv('pdf_skipnpel'))!==0) && ($row['P1']>=100.0) && ($row['P2']>=100.0) ) continue;
            }
            if ( ($rowcount%2)==0) $this->AddPage(); // 16/13 etiquetas por pagina
            $this->writeCell($rowcount%2,$row);
            $rowcount++;
        }
        $this->myLogger->leave();
    }
}