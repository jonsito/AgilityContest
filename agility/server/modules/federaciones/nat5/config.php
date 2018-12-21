<?php
class NAT4 extends Federations {

    function __construct() {
        parent::__construct();
        // combine global data with specific data for this federation
        $this->config= array_merge ($this->config, array(
            'ID'    => 5,
            'ClassName' => get_class($this),
            'Name'  => 'Nat-5',
            'LongName' => 'Competiciones nacionales - 5 alturas',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => 'agilitycontest.png',
            'Logo'     => 'agilitycontest.png',
            'ParentLogo'   => 'agilitycontest.png',
            'WebURL' => 'http://www.agilitycontest.es/index.html',
            'ParentWebURL' => 'http://www.agilitycontest.com/index.html',
            'Email' => '',
            'Heights' => 5,
            'Grades' => 2,
            'Games' => 0,
            'International' => 0,
            'WideLicense' => false, // some federations need extra print space to show license ID
            'RoundsG1' => 2, // on rfec may be 3
            'Recorridos' => array(_('Common course'),"X+L / M+S+T", "X+L / M / S+T", _("Separate courses")),
            'ListaGradosShort' => array(
                '-' => 'Sin especificar',
                // 'Jr' => 'Jr.',
                // 'Sr' => 'Sr.',
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
                'T' => 'Toy - 20'
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
                array('X' => _('Xlrg 60'), 'L' => _('Lrg 50'),  'M' => _('Med 40'),  'S' => _('Sml 30'), 'T' => _('Toy 20')), // separate courses
                array('X' => _('60+50'),   'L' => '',           'M' => _('40+30+20'),'S' => '',          'T' => ''), // mixed (2 groups) courses
                array('X' => _('6+5+4+3+2'),'L' => '',          'M' => '',          'S' => '',          'T' => ''), // common ( single height ) course
                array('X' => _('60+50'),   'L' => '',           'M' => _('Med 40'),  'S' => _('30+20'),  'T' => '') // 3 group courses
            ),
            'Modes' => array( /* modos con los que se trabaja en cada categoria */
                array(/* 0: separado */   0,  1,  2,  5,  9 ),
                array(/* 1: 2 groups */  10, 11, 11, 11, 10 ),
                array(/* 2: conjunto */  12, 12, 12, 12, 12 ),
                array(/* 3: 3 grupos */  10,  1,  7,  7, 10 )
            ),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado  */ _('Cat. 60'),    _('Cat. 50'),    _('Cat. 40'), _('Cat. 30'),    _('Cat. 20')),
                array(/* 2 grupos */ _('Cat. 60+50'), _('Cat. 60+50'), _('Cat. 40+30+20'), _('Cat. 40+30+20'), _('Cat. 40+30+20')),
                array(/* conjunto  */ _('60+50+40+30+20'), _('60+50+40+30+20'), _('60+50+40+30+20'), _('60+50+40+30+20'),_('60+50+40+30+20')),
                array(/* 3 grupos */ _('Cat. 60+50'), _('Cat. 60+50'), _('Cat. 40'), _('Cat. 30+20'), _('Cat. 30+20'))
            ),
            'IndexedModes' => array (
                /* 0 - L    */ "large 50",
                /* 1 - M    */ "Medium 40",
                /* 2 - S    */ "Small 30",
                /* 3 - MS   */ "MS 40+30",
                /* 4 - LMS  */ "LMS 50+40+30",
                /* 5 - T    */ "Toy 20",
                /* 6 - LM   */ "LM 50+40",
                /* 7 - ST   */ "ST 30+20",
                /* 8 - LMST */ "LMST 50+40+30+20",
                /* 9 - X    */ "Xtra large 60",
                /*10 - XL   */ "X/L 60+50",
                /*11 - MST  */ "M/S/T 40+30+20",
                /*11 - XLMST*/ "X+L+M+S+T"
            ),
            // igual que el anterior, pero la busqueda es por letra, no por indice
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
                "LMST" =>'Conjunta 6+5+4+3',
                "X" => "", // invalid
                "XL" => "", // invalid
                "MST" => "", // invalid in rsce
                "XLMST" => "", // invalid
                "-XLMST"=> ""
            )
        ));
    }
}
?>