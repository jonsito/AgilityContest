<?php
class INTL4 extends Federations {

    function __construct() {
        parent::__construct();
        // combine global data with specific data for this federation
        $this->config= array_merge ($this->config, array(
            'ID'    => 8,
            'Name'  => 'Intl-4',
            'LongName' => 'International Contest - 4 heights',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => '/agility/server/modules/federaciones/intl4/wao.png',
            'Logo'     => '/agility/server/modules/federaciones/intl4/wao.png',
            'ParentLogo'   => '/agility/server/modules/federaciones/intl4/wao.png',
            'WebURL' => 'http://www.worldagilityopen.com/',
            'ParentWebURL' => 'http://www.worldagilityopen.com/',
            'Email' => 'info@worldagilityopen.com',
            'Heights' => 4,
            'Grades' => 2,
            'Games' => 1,
            'International' => 1,
            'WideLicense' => false, // some federations need extra print space to show license ID
            'RoundsG1' => 2,
            'Recorridos' => array(_('Common course'),_('Standard + Medium / Small + Toy'),_("Separate courses")),
            'ListaGradosShort' => array(
                '-' => 'Sin especificar',
                'Baja' => 'Out',
                'Jr' => 'Jr.',
                'Sr' => 'Sr.',
                'GI' => 'A1',
                'GII'=> 'A2',
                'GIII' => 'A3', //  invalid for 2-grades contests
                'P.A.' => 'A0',
                'P.B.' => 'T.d.', // "Test dog"
                'Ret.' => 'Ret.'
            ),
            'ListaGrados'    => array (
                '-' => 'Not specified ',
                'Jr' => 'Junior',
                'Sr' => 'Senior',
                'GI' => 'Grade I',
                'GII'=> 'Grade II',
                'GIII' => 'Grade III', // no existe
                'P.A.' => 'Grade 0',
                'P.B.' => 'Test dog',
                'Baja' => 'Temporary out',
                'Ret.' => 'Retired'
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
                array('L' => _('Large'),         'M' => _('Medium'), 'S' => _('Small'),      'T' => _('Tiny')), // separate courses
                array('L' => _('Large+Medium'),  'M' => '',          'S' => _('Small+Tiny'), 'T' => ''), // mixed courses
                array('L' => _('Common course'), 'M' => '',          'S' => '',              'T' => '') // common
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, 5 ), array(/* mixto */ 6, 6, 7, 7 ), array(/* conjunto */ 8, 8, 8, 8 )),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ "Large", "Medium", "Small", "Tiny"),
                array(/* mixto */ "Large+Medium", "Large+Medium", "Small+Tiny", "Small+Tiny"),
                array(/* conjunto */ _("Common course"), _("Common course"), _("Common course"), _("Common course"))
            ),
            'IndexedModes' => array (
                "Large", "Medium", "Small", "Medium+Small", "Common L/M/S", "Tiny", "Large+Medium", "Small+Tiny", "Common L/M/S/T"
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
                "-LMST" => ''
            )
        ));
    }

}
?>