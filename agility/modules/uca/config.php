<?php
class UCA extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 2,
            'Name'  => 'UCA',
            'LongName' => 'Union de Clubes de Agility',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => '/agility/modules/uca/uca.png',
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
                '-' => ' ',
                'Baja' => 'Baja temporal',
                'GI' => 'Grado I',
                'GII'=> 'Grado II',
                // 'GIII' => 'Grado III', // no existe
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
            )
        );
    }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $p datos de la prueba
     * @param {object} $j datos de la jornada
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($p,$j,$m,&$perro,$puestocat) {
        if ($perro['Grado']!=="GII") { // solo se puntua en grado II
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
            return;
        }
        if ($perro['Penalizacion']>=400)  { // tiene manga pendiente de salir
            $perro['Penalizacion']=400.0;
            $perro['Calificacion'] = "";
            $perro['CShort'] = "";
        }
        if ($perro['Penalizacion']>=200)  { // no presentado: no puntua
            $perro['Penalizacion']=200.0;
            $perro['Calificacion'] = _("Not Present");
            $perro['CShort'] = _("N.P.");
        }
        else if ($perro['Penalizacion']>=100) { // eliminado: no puntua
            $perro['Penalizacion']=100.0;
            $perro['Calificacion'] = _("Eliminated");
            $perro['CShort'] = _("Elim");
        }
        else if ($perro['Penalizacion']>=26) { // No clasificado: no puntua
            $perro['Calificacion'] = _("Not Clasified");
            $perro['CShort'] = _("N.C.");
        }
        else if ($perro['Penalizacion']>=16)	{ // Bien: 2 puntos
            $perro['Calificacion'] = _("Good")." - 2";
            $perro['CShort'] = _("Good");
        }
        else if ($perro['Penalizacion']>=6)	{ // Muy bien: 3 puntos
            $perro['Calificacion'] = _("Very good")." - 3";
            $perro['CShort'] = _("V.G.");
        }
        else if ($perro['Penalizacion']>0)	{ // Excelente: 4 puntos
            $perro['Calificacion'] = _("Excellent")." - 4";
            $perro['CShort'] = _("Exc");
        }
        else if ($perro['Penalizacion']==0)	{ // Cero: 5 puntos
            $perro['Calificacion'] = _("Excellent")." - 5";
            $perro['CShort'] = _("Exc");
        }
    }

    /**
     * Evalua la calificacion final del perro
     * @param {object} $p datos de la prueba
     * @param {object} $j datos de la jornada
     * @param {object} $m1 datos de la primera manga
     * @param {object} $m2 datos de la segunda manga
     * @param {array} $c1 resultados de la primera manga
     * @param {array} $c2 resultados de la segunda manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalFinalCalification($p,$j,$m1,$m2,$c1,$c2,&$perro,$puestocat){
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
        $perro['C1']=($pt1==0)?" ":strval($pt1);
        // manga 2
        $pt2=0;
        if ($c2!=null) {
            if ($perro['P2']>=26) $pt2=0; // NC o eliminado: no puntua
            if ($perro['P2']<26) $pt2=2;
            if ($perro['P2']<16) $pt2=3;
            if ($perro['P2']<6) $pt2=4;
            if ($perro['P2']==0) $pt2=5;
        }
        $perro['C2']=($pt2==0)?" ":strval($pt2);
        // final
        // solo puntuan en la global los siete primeros con dobles excelentes
        if (($pt1<4) || ($pt2<4) || ($puestocat[$cat]>7) || ($puestocat[$cat]<=0) ) {
            $perro['Calificacion']="";
        } else {
            $perro['Calificacion']= $pts[ $puestocat[$cat]-1 ];
        }
    }
}
?>