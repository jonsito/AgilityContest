<?php
class NAT3 extends Federations {

    function __construct() {
        parent::__construct();
        // combine global data with specific data for this federation
        $this->config= array_merge ($this->config, array(
            'ID'    => 3,
            'Name'  => 'Nat-3',
            'ClassName' => get_class($this),
            'LongName' => 'Competiciones nacionales - 3 alturas',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'        => 'pirineos_dog_festival.png',
            'Logo'        => 'almozara.png',
            'ParentLogo'  => 'almozara.png',
            'WebURL' => 'http://www.pirineosdogfestival.org',
            'ParentWebURL' => 'http://www.clubagilitylalmozara.com/',
            'Email' => 'info@pirineosdogfestival.com',
            'Heights' => 3,
            'Grades' => 3,
            'Games' => 0,
            'International' => 0,
            'WideLicense' => false, // some federations need extra print space to show license ID
            'RoundsG1' => 2,
            'Recorridos' => array(_("Common course"),_("Large / Med + Small"),_("Separate courses")),
            'ListaGradosShort' => array(
                '-' => '-',
                // 'Jr' => 'Jr.',
                // 'Sr' => 'Sr.',
                'GI' => 'G1',
                'GII'=> 'G2',
                'GIII' => 'G3',
                'P.A.' => 'P.A.',
                'P.B.' => 'P.B.' // "Test dog"
            ),
            'ListaGrados'    => array (
                '-' => 'Individual',
                // 'Jr' => 'Junior',
                // 'Sr' => 'Senior',
                'GI' => 'Grado I',
                'GII'=> 'Grado II',
                'GIII' => 'Grado III',
                'P.A.' => 'Pre-Agility',
                'P.B.' => 'Perro en Blanco'
            ),
            'ListaCategoriasShort' => array (
                '-' => '-',
                'L' => 'Large',
                'M' => 'Med',
                'S' => 'Small',
                // 'T' => 'Tiny'
            ),
            'ListaCategorias' => array (
                '-' => '-',
                // 'E' => 'Extra Large',
                'L' => 'Large',
                'M' => 'Medium',
                'S' => 'Small',
                // 'T' => 'Tiny'
            ),
            'ListaCatGuias' => array (
                '-' => 'Not specified',
                //'I' => 'Infantil',
                'J' => 'Junior',
                'A' => 'Adulto',
                'S' => 'Senior',
                'R' => 'Retirado',
                'P' => 'Para-Agility',
            ),
            'InfoManga' => array(
                array('L' => _('Large'),         'M' => _('Medium'),         'S' => _('Small'), 'T' => '', 'X' => ''), // separate courses
                array('L' => _('Large'),         'M' => _('Medium+Small'),   'S' => '',         'T' => '', 'X' => ''), // 2 group courses
                array('L' => _('Common course'), 'M' => '',                  'S' => '',         'T' => '', 'X' => ''), // common
                array('L' => '',                 'M' => '',                  'S' => '',         'T' => '', 'X' => '') // 3 group courses
            ),
            'Modes' => array(
                array(/* separado */ 0, 1, 2, -1, -1),
                array(/* mixto */    0, 3, 3, -1, -1),
                array(/* conjunto */ 4, 4, 4, -1,  4 ), // pre-agility is -XLMST in tandas cat assignment
                array(/* 3 grupos */-1,-1,-1, -1, -1 ) // invalid en rsce
            ),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ "Large", "Medium", "Small", "Invalid","Invalid"),
                array(/* 2 grupos */ "Large", "Medium+Small", "Medium+Small", "Invalid","Invalid"),
                array(/* conjunto */ "Common course", "Common course", "Common course", "Invalid","Invalid"),
                array(/* 3 grupos */ "Invalid", "Medium", "Small+Toy", "Invalid","XL+Large")
            ),
            'IndexedModes' => array ( // modes 5 to 12 are invalid in this federation
                "Large", "Medium", "Small", "Medium+Small", "Conjunta L/M/S", "Tiny", "Large+Medium", "Small+Tiny", "Common L/M/S/T",
                "Extra Large","Large + XL","Med+Small+Tiny","Common X/L/M/S/T"
            ),
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Large",
                "M"=>"Medium",
                "S"=>"Small",
                "T"=>"Tiny", // invalid
                "LM"=>"Large/Medium", // invalid
                "ST"=>"Small/Tiny", // invalid
                "MS"=>"Medium/Small",
                "LMS" => 'Common LMS',
                "-LMS" => 'Common LMS',
                "LMST" => 'Common LMST',
                "-LMST" => 'Common LMST',
                "X" => 'Extra L',
                "XL" => 'Common XL',
                "MST" => 'Med/Small/Tiny',
                "XLMST" => 'Common XLMST',
                "-XLMST" => ''
            )
        ));
    }

}
?>