<?php
class NAT4 extends Federations {

    function __construct() {
        parent::__construct();
        // combine global data with specific data for this federation
        $this->config= array_merge ($this->config, array(
            'ID'    => 2,
            'ClassName' => get_class($this),
            'Name'  => 'Nat-4',
            'LongName' => 'Competiciones nacionales - 4 alturas',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => 'wao.png',
            'Logo'     => 'wao.png',
            'ParentLogo'   => 'wao.png',
            'WebURL' => 'http://formadog.com/club-agilty/selectiva%20wao%202017.html',
            'ParentWebURL' => 'http://www.worldagilityopen.com/',
            'Email' => '',
            'Heights' => 4,
            'Grades' => 2,
            'Games' => 1,
            'International' => 0,
            'WideLicense' => false, // some federations need extra print space to show license ID
            'RoundsG1' => 2, // on rfec may be 3
            'Recorridos' => array(
                _('Common course'),
                "60 + 50 / 40 + 30",
                _("Separate courses"),""
            ),
            'ListaGradosShort' => array(
                '-' => 'Sin especificar',
                //'Jr' => 'Jr.',
                // 'Sr' => 'Sr.',
                // 'Ch' => 'Ch.',
                // 'Par' => 'Par.',
                'GI' => 'G1',
                'GII'=> 'G2',
                // 'GIII' => 'G3',
                'P.A.' => 'G0',
                'P.B.' => 'P.B.' // "perro en blanco"
            ),
            'ListaGrados'    => array (
                '-' => ' ',
                // 'Jr' => 'Junior',
                // 'Sr' => 'Senior',
                // 'Ch' => 'Infantil',
                // 'Par' => 'ParaAgility',
                'GI' => 'Grado 1',
                'GII'=> 'Grado 2',
                // 'GIII' => 'G3 no disponible',
                'P.A.' => 'Grado 0',
                'P.B.' => 'Perro en Blanco'
            ),
            'ListaCategoriasShort' => array (
                '-' => '-',
                'L' => '600',
                'M' => '500',
                'S' => '400',
                'T' => '300'
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Cat. 600',
                'M' => 'Cat. 500',
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
                array('L' => _('Cat. 60'),     'M' => _('Cat. 50'),'S' => _('Cat. 40'),    'T' => _('Cat. 30'), 'X' => ''), // separate courses
                array('L' => _('Cat. 60+50'),  'M' => '',          'S' => _('Cat. 40+30'), 'T' => '', 'X' => ''), // 2-group courses
                array('L' => _('60+50+40+30'), 'M' => '',          'S' => '',              'T' => '', 'X' => ''), // common
                array('L' => '',               'M' => '',          'S' => '',              'T' => '', 'X' => '') // 3 group courses
            ),
            'Modes' => array(
                // categorias        L  M  S  T  X
                array(/* separado */ 0, 1, 2, 5, -1 ),
                array(/* 2 grupos */ 6, 6, 7, 7, -1 ),
                array(/* conjunto */ 8, 8, 8, 8, 8 ), // pre-agility is declared as -XLMST
                array(/* 3 grupos */-1,-1,-1,-1, -1 ),
            ),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ _('Cat. 60'), _('Cat. 50'),    _('Cat. 40'),   _('Cat. 30'),   "Invalid"),
                array(/* 2 grupos */ _('Cat. 60+50'), _('Cat. 60+50'), _('Cat. 40+30'), _('Cat. 40+30'), "Invalid"),
                array(/* conjunto */ _('60+50+40+30'), _('60+50+40+30'), _('60+50+40+30'),_('60+50+40+30'),"Invalid"),
                array(/* 3 grupos */ "Invalid",     "Invalid",      "Invalid",      "Invalid",      "Invalid")
            ),
            'IndexedModes' => array (
                "Cat. 600",
                "Cat. 500",
                "Cat 400",
                "Cat 500+400",
                "Conjunta 60+50+40",
                "Cat. 300",
                "Cat. 600+500",
                "Cat. 400+300",
                "Cat. 60+50+40+30",
                "Extra Large",
                "Large + XL",
                "Medium+Small+Tiny",
                "Common X/L/M/S/T"
            ),
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Cat. 600",
                "M"=>"Cat. 500",
                "S"=>"Cat. 400",
                "T"=>"Cat. 300",
                "LM"=>"Cat. 60+50",
                "ST"=>"Cat. 40+30",
                "MS"=>"Cat. 50+40", // invalid
                "LMS" => 'Conjunta 6+5+4', // invalid
                "-LMS" => 'Conjunta 6+5+4', // invalid
                "LMST" =>'Conjunta 6+5+4+3',
                "-LMST" =>'Conjunta 6+5+4+3',
                "X" => "", // invalid
                "XL" => "", // invalid
                "XLMST" => "", // invalid
                "MST" => "", // invalid on nat4
                "-XLMST"=> ''
            )
        ));
    }
}
?>