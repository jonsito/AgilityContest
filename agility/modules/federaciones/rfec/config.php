<?php
class RFEC extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 1,
            'Name'  => 'RFEC',
            'LongName' => 'Real Federacion Española de Caza',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => '/agility/modules/federaciones/rfec/rfec.png',
            'Logo'     => '/agility/modules/federaciones/rfec/rfec.png',
            'ParentLogo'   => '/agility/modules/federaciones/rfec/csd.png',
            'WebURL' => 'http://www.fecaza.com/',
            'ParentWebURL' => 'http://www.csd.gob.es/',
            'Heights' => 4,
            'Grades' => 2,
            'International' => 0,
            'WideLicense' => true, // some federations need extra print space to show license ID
            'Recorridos' => array('Common course',"Standard + Medium / Small + Toy","Separate courses"),
            'ListaGradosShort' => array(
                '-' => 'Sin especificar',
                'Baja' => 'Baja',
                'GI' => 'G1',
                'GII'=> 'G2',
                'GIII' => 'G3',
                'P.A.' => 'G0',
                'P.B.' => 'P.B.', // "perro en blanco"
                'Ret.' => 'Ret.'
            ),
            'ListaGrados'    => array (
                '-' => 'Sin especificar',
                'GI' => 'Promocion (G1)',
                'GII'=> 'Competicion (G2)',
                // 'GIII' => 'Grado 3',
                'P.A.' => 'Iniciacion (G0)',
                'P.B.' => 'Perro en Blanco',
                'Baja' => 'Baja temporal ',
                'Ret.' => 'Retirado'
            ),
            'ListaCategoriasShort' => array (
                '-' => '-',
                'L' => 'L - 60',
                'M' => 'M - 50',
                'S' => 'S - 40',
                'T' => 'T - 30'
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Large - 60',
                'M' => 'Medium - 50',
                'S' => 'Small - 40',
                'T' => 'Toy - 30'
            ),
            'InfoManga' => array(
                array('L' => _('Large'),         'M' => _('Medium'), 'S' => _('Small'),     'T' => _('Toy')), // separate courses
                array('L' => _('Large+Medium'),  'M' => '',          'S' => _('Small+Toy'), 'T' => ''), // mixed courses
                array('L' => _('L+M+S+T'),     'M' => '',          'S' => '',              'T' => '') // common
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, 5 ), array(/* mixto */ 6, 6, 7, 7 ), array(/* conjunto */ 8, 8, 8, 8 )),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ "Large", "Medium", "Small", "Toy"),
                array(/* mixto */ "Large+Medium", "Large+Medium", "Small+Toy", "Small+Toy"),
                array(/* conjunto */ "Common course", "Common course", "Common course", "Common course")
            ),
            'IndexedModes' => array (
                "Large", "Medium", "Small", "Medium+Small", "Conjunta L/M/S", "Toy", "Large+Medium", "Small+Toy", "Conjunta L/M/S/T"
            ),
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Large",
                "M"=>"Medium",
                "S"=>"Small",
                "T"=>"Toy",
                "LM"=>"Large/Medium",
                "ST"=>"Small/Toy",
                "MS"=>"Medium/Small",
                "LMS" => 'Conjunta LMS',
                "LMST" => 'Conjunta LMST',
                "-LMST" => ''
            )
        );
    }


}
?>