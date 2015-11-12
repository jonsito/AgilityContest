<?php
class RFEC extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 1,
            'Name'  => 'RFEC',
            'LongName' => 'Real Federacion Española de Caza',
            'Logo'     => '/agility/modules/rfec/rfec.png',
            'ParentLogo'   => '/agility/modules/rfec/csd.png',
            'Heights' => 4,
            'Grades' => 2,
            'Recorridos' => array('Common course',"Standard + Medium / Small + Toy","Separate courses"),
            'ListaGrados'    => array (
                '-' => 'Sin especificar',
                'Baja' => 'Baja temporal',
                'GI' => 'Iniciacion',
                'GII'=> 'Competicion',
                'GIII' => 'Grado III', // no existe
                'P.A.' => 'Promocion',
                'P.B.' => 'Perro en Blanco',
                'Ret.' => 'Retirado',
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Large - 60',
                'M' => 'Medium - 50',
                'S' => 'Small - 40',
                'T' => 'Toy - 30'
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, 5 ), array(/* mixto */ 6, 6, 7, 7 ), array(/* conjunto */ 8, 8, 8, 8 )),
            'Puntuaciones' => function() {} // to point to a function to evaluate califications
        );
    }

    public function getInfoManga($rec) {
        switch ($rec) {
            case 0: return array('L' => _('Large'),         'M' => _('Medium'), 'S' => _('Small'),      'T' => _('Toy')); // separate courses
            case 1: return array('L' => _('Large+Medium'),  'M' => '',          'S' => _('Small+Toy'), 'T' => ''); // mixed courses
            case 2: return array('L' => _('conjunta'), 'M' => '',          'S' => '',              'T' => ''); // common
            default: return array('errorMsg' => "Invalid recorrido: $rec");
        }
    }
}
?>