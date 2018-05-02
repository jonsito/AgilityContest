<?php
class NAT3 extends Federations {

    function __construct() {
        parent::__construct();
        // combine global data with specific data for this federation
        $this->config= array_merge ($this->config, array(
            'ID'    => 3,
            'Name'  => 'Nat-3',
            'LongName' => 'Competiciones nacionales - 3 alturas',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'        => '../ajax/images/getLogo.php?Federation=3&Logo=pirineos_dog_festival.png',
            'Logo'        => '../ajax/images/getLogo.php?Federation=3&Logo=almozara.png',
            'ParentLogo'  => '../ajax/images/getLogo.php?Federation=3&Logo=almozara.png',
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
                'Baja' => 'Out',
                // 'Jr' => 'Jr.',
                // 'Sr' => 'Sr.',
                'GI' => 'G1',
                'GII'=> 'G2',
                'GIII' => 'G3',
                'P.A.' => 'P.A.',
                'P.B.' => 'P.B.', // "Test dog"
                'Ret.' => 'Ret.'
            ),
            'ListaGrados'    => array (
                '-' => 'Individual',
                // 'Jr' => 'Junior',
                // 'Sr' => 'Senior',
                'GI' => 'Grado I',
                'GII'=> 'Grado II',
                'GIII' => 'Grado III',
                'P.A.' => 'Pre-Agility',
                'P.B.' => 'Perro en Blanco',
                'Baja' => 'Baja temporal',
                'Ret.' => 'Retirado',
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
                array('L' => _('Large'),         'M' => _('Medium'),         'S' => _('Small'), 'T' => ''), // separate courses
                array('L' => _('Large'),         'M' => _('Medium+Small'),   'S' => '',         'T' => ''), // mixed courses
                array('L' => _('Common course'), 'M' => '',                  'S' => '',         'T' => '') // common
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, -1), array(/* mixto */ 0, 3, 3, -1), array(/* conjunto */ 4, 4, 4, -1 )),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ "Large", "Medium", "Small", "Invalid"),
                array(/* mixto */ "Large", "Medium+Small", "Medium+Small", "Invalid"),
                array(/* conjunto */ "Common course", "Common course", "Common course", "Invalid")
            ),
            'IndexedModes' => array ( // modes 5 to 8 are invalid in this federation
                "Large", "Medium", "Small", "Medium+Small", "Conjunta L/M/S", "Tiny", "Large+Medium", "Small+Tiny", "Common L/M/S/T"
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
                "LMST" => 'Common LMST',
                "-LMST" => ''
            )
        ));
    }

}
?>