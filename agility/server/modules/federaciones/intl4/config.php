<?php
class INTL4 extends Federations {

    function __construct() {
        parent::__construct();
        // combine global data with specific data for this federation
        $this->config= array_merge ($this->config, array(
            'ID'    => 8,
            'Name'  => 'Intl-4',
            'ClassName' => get_class($this),
            'LongName' => 'International Contest - 4 heights',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => 'wao.png',
            'Logo'     => 'wao.png',
            'ParentLogo'   => 'wao.png',
            'WebURL' => 'http://www.worldagilityopen.com/',
            'ParentWebURL' => 'http://www.worldagilityopen.com/',
            'Email' => 'info@worldagilityopen.com',
            'Heights' => 4,
            'Grades' => 2,
            'Games' => 1,
            'International' => 1,
            'WideLicense' => false, // some federations need extra print space to show license ID
            'RoundsG1' => 2,
            'Recorridos' => array(_('Common course'),_('Standard + Medium / Small + Toy'),_("Separate courses"),/*5heigths*/""),
            'ListaGradosShort' => array(
                '-' => 'Sin especificar',
                'Jr' => 'Jr.',
                'Sr' => 'Sr.',
                'GI' => 'A1',
                'GII'=> 'A2',
                'GIII' => 'A3', //  invalid for 2-grades contests
                'P.A.' => 'A0',
                'P.B.' => 'T.d.' // "Test dog"
            ),
            'ListaGrados'    => array (
                '-' => 'Not specified ',
                'Jr' => 'Junior',
                'Sr' => 'Senior',
                'GI' => 'Grade I',
                'GII'=> 'Grade II',
                'GIII' => 'Grade III', // no existe
                'P.A.' => 'Grade 0',
                'P.B.' => 'Test dog'
            ),
            'ListaCategoriasShort' => array (
                '-' => '-',
                'L' => 'Large',
                'M' => 'Medium',
                'S' => 'Small',
                'T' => 'Toy'
            ),
            'ListaCategorias' => array (
                '-' => 'Not especified',
                'L' => 'Large - 60',
                'M' => 'Medium - 50',
                'S' => 'Small - 40',
                'T' => 'Toy - 30'
            ),
            'ListaCatGuias' => array (
                '-' => 'Not specified',
                'I' => 'Children',
                'J' => 'Junior',
                'A' => 'Adult',
                'S' => 'Senior',
                'R' => 'Retired',
                'P' => 'Para-Agility',
            ),
            'InfoManga' => array(
                array('L' => _('Large'),         'M' => _('Medium'), 'S' => _('Small'),      'T' => _('Tiny'), 'X' =>''), // separate courses
                array('L' => _('Large+Medium'),  'M' => '',          'S' => _('Small+Tiny'), 'T' => '', 'X' =>''), // 2group courses
                array('L' => _('Common course'), 'M' => '',          'S' => '',              'T' => '', 'X' =>''), // common
                array('L' => '',                 'M' => '',          'S' => '',              'T' => '', 'X' =>'')  // 3group courses
            ),
            'Modes' => array(
                // categoria:         L   M   S   T   X
                array(/* separado */  0,  1,  2,  5, -1 ),
                array(/* 2 grupos */  6,  6,  7,  7, -1 ),
                array(/* conjunto */  8,  8,  8,  8, -1 ),
                array(/* 3 groups */ -1, -1, -1, -1, -1 )
            ),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ "Large",       "Medium",       "Small",        "Tiny",         "Invalid"),
                array(/* 2 grupos */ "Large+Medium","Large+Medium", "Small+Tiny",   "Small+Tiny",   "Invalid"),
                array(/* conjunto */ _("Common course"), _("Common course"), _("Common course"), _("Common course"),"Invalid"),
                array(/* 3 grupos */ "Invalid",     "Invalid",      "Invalid",      "Invalid",      "Invalid"),
            ),
            'IndexedModes' => array (
                "Large",
                "Medium",
                "Small",
                "Medium+Small",
                "Common L/M/S",
                "Tiny",
                "Large+Medium",
                "Small+Tiny",
                "Common L/M/S/T",
                "Extra Large",
                "Large + XL",
                "Medium+Small+Tiny",
                "Common X/L/M/S/T"
            ),
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Large",
                "M"=>"Medium",
                "S"=>"Small",
                "T"=>"Tiny",
                "LM"=>"Large/Medium",
                "ST"=>"Small/Tiny",
                "MS"=>"Medium/Small", // invalid
                "LMS" => 'Common LMS',
                "LMST" => 'Common LMST',
                "X" => '',
                "XL" => '',
                "MST" => '',
                "XLMST" => '',
                "-XLMST" => ''
            )
        ));
    }
}
?>