<?php
class INTL4 extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 8,
            'Name'  => 'Intl-4',
            'LongName' => 'International Contest - 4 heights',
            'Logo'     => '/agility/modules/intl4/wao.png',
            'ParentLogo'   => '/agility/modules/intl4/wao.png',
            'Heights' => 4,
            'Grades' => 2, // no really sense in international contests, but....
            'Recorridos' => array('Common course',"Standard + Medium / Small + Toy","Separate courses"),
            'ListaGrados'    => array (
                '-' => 'Not especified',
                'Baja' => 'Retired',
                'GI' => 'Grade I',
                'GII'=> 'Grade II',
                'GIII' => 'Grade III', // no existe
                'P.A.' => 'Grade 0',
                'P.B.' => 'Trial dog',
                'Ret.' => 'Retirado',
            ),
            'ListaCategorias' => array (
                '-' => 'Not especified',
                'L' => 'Large - 60',
                'M' => 'Medium - 50',
                'S' => 'Small - 40',
                'T' => 'Toy - 30'
            ),
            'Puntuaciones' => function() {} // to point to a function to evaluate califications
        );
    }

    public function getInfoManga($rec) {
        switch ($rec) {
            case 0: return array('L' => _('Large'),         'M' => _('Medium'), 'S' => _('Small'),      'T' => _('Tiny')); // separate courses
            case 1: return array('L' => _('Large+Medium'),  'M' => '',          'S' => _('Small+Tiny'), 'T' => ''); // mixed courses
            case 2: return array('L' => _('Common course'), 'M' => '',          'S' => '',              'T' => ''); // common
            default: return array('errorMsg' => "Invalid recorrido: $rec");
        }
    }
}
?>