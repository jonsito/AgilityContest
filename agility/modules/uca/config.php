<?php
class UCA extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 2,
            'Name'  => 'UCA',
            'LongName' => 'Union de Clubes de Agility',
            // use basename http absolute path for icons, as need to be used in client side
            'Logo'     => '/agility/modules/uca/uca.png',
            'ParentLogo'   => '/agility/modules/uca/rfec.png',
            'WebURL' => 'http://www.agilityuca.org/',
            'ParentWebURL' => 'http://www.fecaza.com/',
            'Heights' => 4,
            'Grades' => 2,
            'International' => 0,
            'WideLicense' => false, // some federations need extra print space to show license ID
            'Recorridos' => array('Common course',"60 + 50 / 40 + 30","Separate courses"),
            'ListaGrados'    => array (
                '-' => 'Sin especificar',
                'Baja' => 'Baja temporal',
                'GI' => 'Grado I',
                'GII'=> 'Grado II',
                'GIII' => 'Grado III', // no existe
                'P.A.' => 'Grado 0',
                'P.B.' => 'Perro en Blanco',
                'Ret.' => 'Retirado',
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Cat. 60',
                'M' => 'Cat. 50',
                'S' => 'Cat. 40',
                'T' => 'Cat. 30'
            ),
            'InfoManga' => array(
                array('L' => _('Cat. 60'),     'M' => _('Cat. 50'),'S' => _('Cat. 40'),    'T' => _('Cat. 30')), // separate courses
                array('L' => _('Cat. 60+50'),  'M' => '',          'S' => _('Cat. 40+30'), 'T' => ''), // mixed courses
                array('L' => _('60+50+40+30'), 'M' => '',          'S' => '',              'T' => '') // common
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, 5 ), array(/* mixto */ 6, 6, 7, 7 ), array(/* conjunto */ 8, 8, 8, 8 )),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ _('Cat. 60'), _('Cat. 50'), _('Cat. 40'), _('Cat. 30')),
                array(/* mixto */ _('Cat. 60+50'), _('Cat. 60+50'), _('Cat. 40+30'), _('Cat. 40+30')),
                array(/* conjunto */ _('60+50+40+30'), _('60+50+40+30'), _('60+50+40+30'),_('60+50+40+30'))
            ),
            'IndexedModes' => array (
                "Cat. 60", "Cat. 50", "Cat 40", "Cat 50+40", "Conjunta 60+50+40", "Cat. 30", "Cat. 60+50", "Cat. 40+30", "Cat. 60+50+40+30"
            ),
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Cat. 60",
                "M"=>"Cat. 50",
                "S"=>"Cat. 40",
                "T"=>"Cat. 30",
                "LM"=>"Cat. 60+50",
                "ST"=>"Cat. 40+30",
                "MS"=>"Cat. 50+40", // invalid
                "LMS" => 'Conjunta 6+5+4', // invalid
                "LMST",'Conjunta 6+5+4+3'
            ),
            'Puntuaciones' => function() {} // to point to a function to evaluate califications
        );
    }

    /**
     * Evalua la calificacion final del perro
     * @param {array} $c1 datos de la primera manga
     * @param {array} $c2 datos de la segunda manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     * @param {boolean} $selectiva
     */
    public function evalCalification($c1,$c2,&$perro,$puestocat){
        $grad=$perro['Grado']; // cogemos el grado
        $cat=$perro['Categoria']; // cogemos la categoria
        if ($grad!=="GII") { // solo se puntua en grado II
            $perro['Calificacion']=$perro['C1'];
            if ($perro['P1']<$perro['P2']) $perro['Calificacion']=$perro['C2'];
            return;
        }
        $pts=array("10","8","6","4","3","2","1");
        $pt1=0;
        // manga 1
        if ($perro['P1']>=26) $pt1=0; // NC o eliminado: no puntua
        if ($perro['P1']<26) $pt1=2;
        if ($perro['P1']<16) $pt1=3;
        if ($perro['P1']<6) $pt1=4;
        if ($perro['P1']==0) $pt1=5;
        // manga 2
        $pt2=0;
        if ($c2!=null) {
            if ($perro['P2']>=26) $pt2=0; // NC o eliminado: no puntua
            if ($perro['P2']<26) $pt2=2;
            if ($perro['P2']<16) $pt2=3;
            if ($perro['P2']<6) $pt2=4;
            if ($perro['P2']==0) $pt2=5;
        }
        // final
        $str=$str=strval($pt1)."-".strval($pt2)."-";
        // solo puntuan en la global los siete primeros con dobles excelentes
        if (($pt1<4) || ($pt2<4) || ($puestocat[$cat]>7) ) {
            $perro['Calificacion']=$str;
        } else {
            $perro['Calificacion']= $str . $pts[ $puestocat[$cat]-1 ];
        }
    }
}
?>