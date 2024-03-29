<?php
class NAT5 extends Federations {

    function __construct() {
        parent::__construct();
        // combine global data with specific data for this federation
        $this->config= array_merge ($this->config, array(
            'ID'    => 5,
            'ClassName' => get_class($this),
            'Name'  => 'Nat-5',
            'LongName' => 'Competiciones nacionales - 5 alturas',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => 'wao.png',
            'Logo'     => 'wao.png',
            'ParentLogo'   => 'wao.png',
            'WebURL' => 'http://www.agilitycontest.es/index.html',
            'ParentWebURL' => 'http://www.agilitycontest.com/index.html',
            'Email' => '',
            'Heights' => 5,
            'Grades' => 3,
            'Games' => 1,
            'International' => 0,
            'LicenseType' => Federations::$LICENSE_REQUIRED_NONE, // no license required
            'RoundsG1' => 2, // on rfec may be 3
            'Recorridos' => array(
                _('Common course'),
                "X+L / M+S+T",
                _("Separate courses"),
                "X+L / M / S+T"
            ),
            'ListaGradosShort' => array(
                '-' => 'Sin especificar',
                // 'Jr' => 'Jr.',
                // 'Sr' => 'Sr.',
                // 'Ch' => 'Ch.',
                // 'Par' => 'Par.',
                'GI' => 'G1',
                'GII'=> 'G2',
                'GIII' => 'G3',
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
                'GIII' => 'Grado 3',
                'P.A.' => 'Pre-Agility',
                'P.B.' => 'Perro en Blanco'
            ),
            'ListaCategoriasShort' => array (
                '-' => '-',
                'X' => 'Xlarge',
                'L' => 'Large',
                'M' => 'Medium',
                'S' => 'Small',
                'T' => 'Toy'
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                'X' => 'Extra Large - 60',
                'L' => 'Large - 50',
                'M' => 'Medium - 40',
                'S' => 'Small - 30',
                'T' => 'Toy - 25'
            ),
            'ListaCatGuias' => array (
                '-' => 'Sin especificar',
                // 'I' => 'Infantil',
                'J' => 'Junior',
                'A' => 'Adulto',
                // 'S' => 'Senior',
                'R' => 'Retirado',
                // 'P' => 'Para-Agility',
            ),
            'InfoManga' => array(
                array('L' => _('Lrg 50'), 'M' => _('Med 40'),  'S' => _('Sml 30'), 'T' => _('Toy 25'),'X' => _('Xlrg 60') ), // separate courses
                array('L' => '',          'M' => _('40+30+25'),'S' => '',          'T' => '',       'X' => _('60+50')), // mixed (2 groups) courses
                array('L' => '',          'M' => '',           'S' => '',          'T' => '',       'X' => _('6+5+4+3+2')), // common ( single height ) course
                array('L' => '',          'M' => _('Med 40'),  'S' => _('30+25'),  'T' => '',       'X' => _('60+50'),   ) // 3 group courses
            ),
            'Modes' => array( /* modos con los que se trabaja en cada categoria */
                //                        L   M   S   T   X
                array(/* 0: separado */   0,  1,  2,  5,  9 ),
                array(/* 1: 2 groups */  10, 11, 11, 11, 10 ),
                array(/* 2: conjunto */  12, 12, 12, 12, 12 ),
                array(/* 3: 3 grupos */  10,  1,  7,  7, 10 )
            ),
            'ModeStrings' => array( // text to be shown on each category
                // category  L M S T X
                array(/* separado  */_('Cat. 50'),    _('Cat. 40'), _('Cat. 30'),    _('Cat. 25'),_('Cat. 60') ),
                array(/* 2 grupos */ _('Cat. 60+50'), _('Cat. 40+30+25'), _('Cat. 40+30+25'), _('Cat. 40+30+25'), _('Cat. 60+50') ),
                array(/* conjunto  */_('60+50+40+30+25'), _('60+50+40+30+25'), _('60+50+40+30+25'),_('60+50+40+30+25'),_('60+50+40+30+25')),
                array(/* 3 grupos */ _('Cat. 60+50'), _('Cat. 40'), _('Cat. 30+25'), _('Cat. 30+25'),_('Cat. 60+50'))
            ),
            'IndexedModes' => array (
                /* 0 - L    */ "large 50",
                /* 1 - M    */ "Medium 40",
                /* 2 - S    */ "Small 30",
                /* 3 - MS   */ "MS 40+30",
                /* 4 - LMS  */ "LMS 50+40+30",
                /* 5 - T    */ "Toy 25",
                /* 6 - LM   */ "LM 50+40",
                /* 7 - ST   */ "ST 30+25",
                /* 8 - LMST */ "LMST 50+40+30+25",
                /* 9 - X    */ "Xtra large 60",
                /*10 - XL   */ "X/L 60+50",
                /*11 - MST  */ "M/S/T 40+30+25",
                /*12 - XLMST*/ "X+L+M+S+T"
            ),
            // igual que el anterior, pero la busqueda es por letra, no por indice
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Cat. 50",
                "M"=>"Cat. 40",
                "S"=>"Cat. 30",
                "T"=>"Cat. 25",
                "LM"=>"Cat. 60+50", // // invalid in 5 heights
                "ST"=>"Cat. 30+25", // 3 groups mode
                "MS"=>"Cat. 40+30", // invalid in 5 heights
                "LMS" => 'Conjunta 5+4+3', // invalid in 5 heights
                "-LMS" => 'Conjunta 5+4+3', // invalid in 5 heights
                "LMST" =>'Conjunta 5+4+3+2', // invalid in 5 heights
                "-LMST" =>'Conjunta 5+4+3+2', // invalid in 5 heights
                "X" => "Cat. 60",
                "XL" => "Cat. 60+50", // 3 groups mode
                "MST" => "Cat. 40+30+25", // 2 groups mode
                "XLMST" => "", // common course
                "-XLMST"=> ""
            )
        ));
    }
}
?>