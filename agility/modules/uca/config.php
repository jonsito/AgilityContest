<?php
class UCA extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 2,
            'Name'  => 'UCA',
            'LongName' => 'Union de Clubes de Agility',
            'Logo'     => '/agility/modules/uca/uca.png',
            'ParentLogo'   => '/agility/modules/uca/rfec.png',
            'Heights' => 4,
            'Grades' => 2,
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
                'L' => 'Large - 60',
                'M' => 'Medium - 50',
                'S' => 'Small - 40',
                'T' => 'Tiny - 30'
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, 5 ), array(/* mixto */ 6, 6, 7, 7 ), array(/* conjunto */ 8, 8, 8, 8 )),
            'Puntuaciones' => function() {} // to point to a function to evaluate califications
        );
    }

    public function getInfoManga($rec) {
        switch ($rec) {
            case 0: return array('L' => _('Cat. 60'),       'M' => _('Cat. 50'),'S' => _('Cat. 40'),    'T' => _('Cat. 30')); // separate courses
            case 1: return array('L' => _('Cat. 60+50'),    'M' => '',          'S' => _('Cat. 40+30'), 'T' => ''); // mixed courses
            case 2: return array('L' => _('60+50+40+30'), 'M' => '',          'S' => '',              'T' => ''); // common
            default: return array('errorMsg' => "Invalid value for recorrido: $rec");
        }
    }
}
?>