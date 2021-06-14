<?php
class INTL3 extends Federations {

    function __construct() {
        parent::__construct();
        // combine global data with specific data for this federation
        $this->config= array_merge ($this->config, array(
            'ID'    => 9,
            'Name'  => 'Intl-3',
            'ClassName' => get_class($this),
            'LongName' => 'International Contest - 3 heights',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'        => 'fciawc2016.png',
            'Logo'        => 'rsce.png',
            'ParentLogo'  => 'fci.png',
            'WebURL' => 'http://www.fci.org',
            'ParentWebURL' => 'http://www.fci.org',
            'Email' => 'info@fci.be',
            'Heights' => 3,
            'Grades' => 3,
            'Games' => 0,
            'International' => 1,
            'WideLicense' => false, // some federations need extra print space to show license ID
            'RoundsG1' => 2,
            'Recorridos' => array(_('Common course'),_("Large / Med + Small"),_("Separate courses"), /*5heights*/""),
            'ListaGradosShort' => array(
                '-' => '-',
                // 'Jr' => 'Jr.',
                // 'Sr' => 'Sr.',
                // 'Ch' => 'Ch.',
                // 'Par' => 'Par.',
                'GI' => 'A1',
                'GII'=> 'A2',
                'GIII' => 'A3',
                // 'P.A.' => 'A0',
                'P.B.' => 'T.d.' // "Test dog"
            ),
            'ListaGrados'    => array (
                '-' => 'Individual',
                // 'Jr' => 'Junior',
                // 'Sr' => 'Senior',
                // 'Ch' => 'Children',
                // 'Par' => 'ParaAgility',
                'GI' => 'Grade I',
                'GII'=> 'Grade II',
                'GIII' => 'Grade III',
                // 'P.A.' => 'Pre-Agility',
                'P.B.' => 'Test dog'
            ),
            'ListaCategoriasShort' => array (
                '-' => '-',
                // 'E' => 'Extra',
                'L' => 'Large',
                'M' => 'Med',
                'S' => 'Small',
                // 'T' => 'Tiny'
            ),
            'ListaCategorias' => array (
                '-' => '-',
                // 'E' => 'Extra',
                'L' => 'Large',
                'M' => 'Medium',
                'S' => 'Small',
                // 'T' => 'Tiny'
            ),
            'ListaCatGuias' => array (
                '-' => 'Not specified',
                // 'I' => 'Children',
                // 'J' => 'Junior',
                'A' => 'Adult',
                // 'S' => 'Senior',
                'R' => 'Retired',
                'P' => 'Para-Agility',
            ),
            'InfoManga' => array(
                array('L' => _('Large'),         'M' => _('Medium'),         'S' => _('Small'), 'T' => '', 'X' => ''), // separate courses
                array('L' => _('Large'),         'M' => _('Medium+Small'),   'S' => '',         'T' => '', 'X' => ''), // 2 groups
                array('L' => _('Common course'), 'M' => '',                  'S' => '',         'T' => '', 'X' => ''), // common
                array('L' => '',                 'M' => '',                  'S' => '',         'T' => '', 'X' => '') // 3 groups
            ),
            // modos en funcion de la categoria y el recorrido
            'Modes' => array(
                //  categorias          L  M  S  T  X
                array(/* 0: separado */ 0, 1, 2,-1,-1),
                array(/* 1: 2 grupos */ 0, 3, 3,-1,-1),
                array(/* 2: conjunto */ 4, 4, 4,-1, 4), // pre-agility is -xlmst in cats assignment
                array(/* 3: 3 grupos */-1,-1,-1,-1,-1)
            ),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ "Large",       "Medium",       "Small",        "Invalid",  "Invalid"),
                array(/* 2 grupos */ "Large",       "Medium+Small", "Medium+Small", "Invalid",  "Invalid"),
                array(/* conjunto */ "Common course","Common course","Common course","Invalid", "Invalid"),
                array(/* 3 grupos */ "Invalid",     "Invalid",      "Invalid",      "Invalid",  "Invalid")
            ),
            'IndexedModes' => array ( // modes 5 to 12 are invalid in this federation
                /* 0 */ "Large",
                /* 1 */ "Medium",
                /* 2 */ "Small",
                /* 3 */ "Medium+Small",
                /* 4 */ "Conjunta L/M/S",
                /* 5 */ "Tiny",
                /* 6 */ "Large+Medium",
                /* 7 */ "Small+Tiny",
                /* 8 */ "Common L/M/S/T",
                /* 9 */ "Extra Large",
                /*10 */ "Large + XL",
                /*11 */ "Medium+Small+Tiny",
                /*12 */ "Common X/L/M/S/T"
            ),
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Large",
                "M"=>"Medium",
                "S"=>"Small",
                "T"=>"Tiny",
                "LM"=>"Large/Medium",
                "ST"=>"Small/Tiny",
                "MS"=>"Medium/Small",
                "LMS" => 'Common LMS',
                "-LMS" => 'Common LMS',
                "LMST" => 'Common LMST',
                "-LMST" => 'Common LMST',
                "X" => 'Extra Large',
                "XL" => 'XLarge/Large',
                "MST" => 'Med/Sml/XSml',
                "XLMST" => 'XL/L/M/S/XS',
                "-XLMST" => 'XL/L/M/S/XS'
            )
        ));
    }

}
?>