<?php
class INTL3 extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 9,
            'Name'  => 'Intl-3',
            'LongName' => 'International Contest - 3 heights',
            'Logo'     => '/agility/modules/intl3/fci.png',
            'ParentLogo'   => '/agility/modules/intl3/fci.png',
            'Heights' => 3,
            'Grades' => 3, // not really sense in internatiolnal contests, but...
            'Recorridos' => array('Common course',"Standard / Midi + Mini","Separate courses"),
            'ListaGrados'    => array (
                '-' => 'Not especified',
                'Baja' => 'Temporary out',
                'GI' => 'Grade I',
                'GII'=> 'Grade II',
                'GIII' => 'Grade III', // no existe
                'P.A.' => 'Pre-Agility',
                'P.B.' => 'Trial dog',
                'Ret.' => 'Retired',
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Standard',
                'M' => 'Medium',
                'S' => 'Small',
                'T' => 'Tiny' // not used
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, -1), array(/* mixto */ 0, 3, 3. -1), array(/* conjunto */ 4, 4, 4, -1 )),
            'Puntuaciones' => function() {} // to point to a function to evaluate califications
        );
    }

    public function getInfoManga($rec) {
        switch ($rec) {
            case 0: return array('L' => _('Large'),         'M' => _('Medium'),         'S' => _('Small'),      'T' => ''); // separate courses
            case 1: return array('L' => _('Large'),         'M' => _('Medium+Small'),   'S' => '',              'T' => ''); // mixed courses
            case 2: return array('L' => _('Common course'), 'M' => '',                  'S' => '',              'T' => ''); // common
            default: return array('errorMsg' => "Invalid recorrido: $rec");
        }
    }
}
?>