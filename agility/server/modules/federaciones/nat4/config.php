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
            'OrganizerLogo'     => '/agility/server/modules/federaciones/nat4/wao.png',
            'Logo'     => '/agility/server/modules/federaciones/nat4/wao.png',
            'ParentLogo'   => '/agility/server/modules/federaciones/nat4/wao.png',
            'WebURL' => 'http://formadog.com/club-agilty/selectiva%20wao%202017.html',
            'ParentWebURL' => 'http://www.worldagilityopen.com/',
            'Email' => '',
            'Heights' => 4,
            'Grades' => 2,
            'Games' => 1,
            'International' => 0,
            'WideLicense' => false, // some federations need extra print space to show license ID
            'RoundsG1' => 2, // on rfec may be 3
            'Recorridos' => array(_('Common course'),"65 + 52 / 40 + 30",_("Separate courses")),
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
                'L' => '650',
                'M' => '525',
                'S' => '400',
                'T' => '300'
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Cat. 650',
                'M' => 'Cat. 525',
                'S' => 'Cat. 400',
                'T' => 'Cat. 300'
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
                array('L' => _('Cat. 65'),     'M' => _('Cat. 52'),'S' => _('Cat. 40'),    'T' => _('Cat. 30')), // separate courses
                array('L' => _('Cat. 65+52'),  'M' => '',          'S' => _('Cat. 40+30'), 'T' => ''), // mixed courses
                array('L' => _('65+52+40+30'), 'M' => '',          'S' => '',              'T' => '') // common
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, 5 ), array(/* mixto */ 6, 6, 7, 7 ), array(/* conjunto */ 8, 8, 8, 8 )),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ _('Cat. 65'), _('Cat. 52'), _('Cat. 40'), _('Cat. 30')),
                array(/* mixto */ _('Cat. 65+52'), _('Cat. 65+52'), _('Cat. 40+30'), _('Cat. 40+30')),
                array(/* conjunto */ _('65+52+40+30'), _('65+52+40+30'), _('65+52+40+30'),_('65+52+40+30'))
            ),
            'IndexedModes' => array (
                "Cat. 650", "Cat. 525", "Cat 400", "Cat 525+400", "Conjunta 65+52+40", "Cat. 30", "Cat. 65+52", "Cat. 40+30", "Cat. 65+52+40+30"
            ),
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Cat. 650",
                "M"=>"Cat. 525",
                "S"=>"Cat. 400",
                "T"=>"Cat. 300",
                "LM"=>"Cat. 65+52",
                "ST"=>"Cat. 40+30",
                "MS"=>"Cat. 52+40", // invalid
                "LMS" => 'Conjunta 6+5+4', // invalid
                "LMST" =>'Conjunta 6+5+4+3',
                "-LMST"=> ''
            )
        ));
    }
}
?>