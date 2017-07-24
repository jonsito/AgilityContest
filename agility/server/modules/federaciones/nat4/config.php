<?php
class NAT4 extends Federations {

    function __construct() {
        parent::__construct();
        // combine global data with specific data for this federation
        $this->config= array_merge ($this->config, array(
            'ID'    => 2,
            'Name'  => 'Nat-4',
            'LongName' => 'Competiciones nacionales - 4 alturas',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => '/agility/server/modules/federaciones/nat4/uca.png',
            'Logo'     => '/agility/server/modules/federaciones/nat4/uca.png',
            'ParentLogo'   => '/agility/server/modules/federaciones/nat4/rfec.png',
            'WebURL' => 'http://www.agilityuca.org/',
            'ParentWebURL' => 'http://www.fecaza.com/',
            'Email' => '',
            'Heights' => 4,
            'Grades' => 2,
            'International' => 0,
            'WideLicense' => false, // some federations need extra print space to show license ID
            'RoundsG1' => 2, // on rfec may be 3
            'Recorridos' => array('Common course',"60 + 50 / 40 + 30","Separate courses"),
            'ListaGradosShort' => array(
                '-' => 'Sin especificar',
                'Baja' => 'Baja',
                // 'Jr' => 'Jr.',
                // 'Sr' => 'Sr.',
                'GI' => 'G1',
                'GII'=> 'G2',
                // 'GIII' => 'G3',
                'P.A.' => 'G0',
                'P.B.' => 'P.B.', // "perro en blanco"
                'Ret.' => 'Ret.'
            ),
            'ListaGrados'    => array (
                '-' => ' ',
                'Baja' => 'Baja temporal',
                // 'Jr' => 'Junior',
                // 'Sr' => 'Senior',
                'GI' => 'Grado 1',
                'GII'=> 'Grado 2',
                // 'GIII' => 'G3 no disponible',
                'P.A.' => 'Grado 0',
                'P.B.' => 'Perro en Blanco',
                'Ret.' => 'Retirado',
            ),
            'ListaCategoriasShort' => array (
                '-' => '-',
                'L' => '60',
                'M' => '50',
                'S' => '40',
                'T' => '30'
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Cat. 60',
                'M' => 'Cat. 50',
                'S' => 'Cat. 40',
                'T' => 'Cat. 30'
            ),
            'ListaCatGuias' => array (
                '-' => 'Sin especificar',
                // 'I' => 'Infantil',
                // 'J' => 'Junior',
                'A' => 'Adulto',
                // 'S' => 'Senior',
                'R' => 'Retirado',
                // 'P' => 'Para-Agility',
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
                "LMST" =>'Conjunta 6+5+4+3',
                "-LMST"=> ''
            )
        ));
    }
}
?>