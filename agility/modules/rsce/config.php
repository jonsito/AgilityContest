<?php
class RSCE extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 0,
            'Name'  => 'RSCE',
            'LongName' => 'Real Sociedad Canina de España',
            'Logo'     => '/agility/modules/rsce/rsce.png',
            'ParentLogo'   => '/agility/modules/rsce/fci.png',
            'Heights' => 3,
            'Grades' => 3,
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
                'L' => 'Standard - 60',
                'M' => 'Midi - 50',
                'S' => 'Mini - 40',
                'T' => 'Toy - 30' // no existe
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, -1), array(/* mixto */ 0, 3, 3, -1), array(/* conjunto */ 4, 4, 4, -1 )),
            'Puntuaciones' => function() {} // to point to a function to evaluate califications
        );
    }


    public function getInfoManga($rec) {
        switch ($rec) {
            case 0: return array('L' => _('Standard'),         'M' => _('Midi'),         'S' => _('Mini'),      'T' => ''); // separate courses
            case 1: return array('L' => _('Standard'),         'M' => _('Midi+Mini'),   'S' => '',              'T' => ''); // mixed courses
            case 2: return array('L' => _('Conjunta'), 'M' => '',                  'S' => '',              'T' => ''); // common
            default: return array('errorMsg' => "Invalid recorrido: $rec");
        }
    }
}
?>