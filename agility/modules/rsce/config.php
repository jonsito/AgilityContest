<?php
class RSCE extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 0,
            'Name'  => 'RSCE',
            'LongName' => 'Real Sociedad Canina de España',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => '/agility/modules/rsce/rsce.png',
            'Logo'     => '/agility/modules/rsce/rsce.png',
            'ParentLogo'   => '/agility/modules/rsce/fci.png',
            'WebURL' => 'http://www.rsce.es/',
            'ParentWebURL' => 'http://www.fci.org/',
            'Heights' => 3,
            'Grades' => 3,
            'International' => 0,
            'WideLicense' => false, // some federations need extra print space to show license ID
            'Recorridos' => array('Common course',"Standard / Midi + Mini","Separate courses"),
            'ListaGrados'    => array (
                '-' => 'Sin especificar',
                'Baja' => 'Baja temporal',
                'GI' => 'Grado I',
                'GII'=> 'Grado II',
                'GIII' => 'Grado III',
                'P.A.' => 'Pre-Agility',
                'P.B.' => 'Perro en Blanco',
                'Ret.' => 'Retirado',
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Standard',
                'M' => 'Midi',
                'S' => 'Mini',
                'T' => 'Toy' // no existe
            ),
            'InfoManga' => array(
                array('L' => _('Standard'), 'M' => _('Midi'),        'S' => _('Mini'),  'T' => ''), // separate courses
                array('L' => _('Standard'), 'M' => _('Midi+Mini'),   'S' => '',         'T' => ''), // mixed courses
                array('L' => _('Std+Midi+Mini'), 'M' => '',               'S' => '',         'T' => '') // common
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, -1), array(/* mixto */ 0, 3, 3, -1), array(/* conjunto */ 4, 4, 4, -1 )),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ "Standard", "Midi", "Mini", "Invalid"),
                array(/* mixto */ "Standard", "Midi+Mini", "Midi+Mini", "Invalid"),
                array(/* conjunto */ "Common course", "Common course", "Common course", "Invalid")
            ),
            'IndexedModes' => array (
                "Standard", "Midi", "Mini", "Midi+Mini", "Conjunta L/M/S", "Tiny", "Standard+Midi", "Mini+Tiny", "Conjunta L/M/S/T"
            ),
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Standard",
                "M"=>"Midi",
                "S"=>"Mini",
                "T"=>"Tiny", // invalid
                "LM"=>"Standard/Midi", // invalid
                "ST"=>"Mini/Tiny", // invalid
                "MS"=>"Midi/Mini",
                "LMS" => 'Conjunta LMS',
                "LMST",'Conjunta LMST' // invalid
            )
        );
    }

    /**
     * Evalua la calificacion final del perro
     * @param {object} $p datos de la prueba
     * @param {object} $j datos de la jornada
     * @param {array} $c1 datos de la primera manga
     * @param {array} $c2 datos de la segunda manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalCalification($p,$j,$c1,$c2,&$perro,$puestocat){
        $grad=$perro['Grado']; // cogemos la categoria

        if ($grad==="GI") { // en grado uno se puntua por cada manga
            $pts=0;
            if ($perro['P1']==0.0) $pts++;
            if ($perro['P2']==0.0) $pts++;
            $perro['Calificacion'] = "";
            if ($pts==1) $perro['Calificacion'] = "1 Punto";
            if ($pts==2) $perro['Calificacion'] = "2 Puntos";
            return;
        }
        if ($grad==="GII") { // grado dos puntua normalmente
            $perro['Calificacion'] = ($perro['Penalizacion'] == 0.0) ? 'Punto' : '';
            return;
        }
        if ($grad!=="GIII") {
            return; // ignore other extrange grades
        }
        // arriving here means grado III
        if ($p->Selectiva==0){
            $perro['Calificacion'] = ($perro['Penalizacion']==0.0)?'Punto':'';
            return;
        }
        // arriving here means prueba selectiva and Grado III
        // comprobamos si el perro es mestizo
        if ( Dogs::isMixBreed($perro['Licencia']) ) {
            $perro['Calificacion'] = ($perro['Penalizacion']==0.0)?'Punto':'';
            return;
        }
        // TODO: Tener en cuenta perros extranjeros
        $pts=array("20","16","12","8","7","6","5","4","3","2");
        // manga 1 - puntuan los 10 primeros en cada manga con excelente
        $pt1=" ";
        if ( ($perro['P1']<6.0) && ($perro['Pcat1']<=10) ) {
            $pt1=$pts[$perro['Pcat1']-1];
        }
        // manga 2 - puntuan los 10 primeros en cada manga con excelente
        $pt2=0;
        if ( ($c2!=null) && ($perro['P2']<6.0) && ($perro['Pcat2']<=10) ) {
            $pt2=$pts[$perro['Pcat2']-1];
        }
        // conjunta - puntuan los 10 primeros si tienen doble excelente
        $pfin=" ";
        if ( ($perro['P1']<6.0) && ($perro['P2']<6.0)  && ($perro['Pcat']<=10) ) {
            $pfin=$pts[$perro['Pcat']-1];
        }
        // finalmente componemos el string a presentar
        $perro['Calificacion']=$str=strval($pt1)."-".strval($pt2)."-".strval($pfin);
        return; // should be overriden
    }
}
?>