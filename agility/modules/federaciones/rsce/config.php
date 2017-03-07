<?php
class RSCE extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 0,
            'Name'  => 'RSCE',
            'LongName' => 'Real Sociedad Canina de España',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => '/agility/modules/federaciones/rsce/rsce.png',
            'Logo'     => '/agility/modules/federaciones/rsce/rsce.png',
            'ParentLogo'   => '/agility/modules/federaciones/rsce/fci.png',
            'WebURL' => 'http://www.rsce.es/',
            'ParentWebURL' => 'http://www.fci.org/',
            'Email' => 'agility@rsce.es',
            'Heights' => 3,
            'Grades' => 3,
            'International' => 0,
            'WideLicense' => false, // some federations need extra print space to show license ID
            'RoundsG1' => 2, // on rfec may be 3
            'Recorridos' => array('Common course',"Standard / Midi + Mini","Separate courses"),
            'ListaGradosShort' => array(
                '-' => 'Sin especificar',
                'Baja' => 'Out',
                'GI' => 'GI',
                'GII'=> 'GII',
                'GIII' => 'GIII',
                'P.A.' => 'P.A.',
                'P.B.' => 'P.B.', // "perro en blanco"
                'Ret.' => 'Ret.'
            ),
            'ListaGrados'    => array (
                '-' => 'Sin especificar',
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
                'L' => 'Std',
                'M' => 'Midi',
                'S' => 'Mini',
                // 'T' => 'Toy' // no existe
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Standard',
                'M' => 'Midi',
                'S' => 'Mini',
                // 'T' => 'Toy' // no existe
            ),
            'InfoManga' => array(
                array('L' => _('Standard'), 'M' => _('Midi'),        'S' => _('Mini'),  'T' => ''), // separate courses
                array('L' => _('Standard'), 'M' => _('Midi+Mini'),   'S' => '',         'T' => ''), // mixed courses
                array('L' => _('Std+Midi+Mini'), 'M' => '',               'S' => '',         'T' => '') // common
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, -1), array(/* mixto */ 0, 3, 3, -1), array(/* conjunto */ 4, 4, 4, -1 )),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ "Standard", "Midi", "Mini", "Invalid"),
                array(/* mixto */ "Standard", "Midi+Mini", "Midi+Mini", "Invalid"),
                array(/* conjunto */ "Conjunta", "Conjunta", "Conjunta", "Invalid")
            ),
            'IndexedModes' => array (
                "Standard", "Midi", "Mini", "Midi+Mini", "Conjunta L/M/S", "Tiny", "Standard+Midi", "Mini+Tiny", "Conjunta L/M/S/T"
            ),
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Standard",
                "M"=>"Midi",
                "S"=>"Mini",
                "T"=>"Tiny", // invalid
                "LM"=>"Standard/Midi", // invalid
                "ST"=>"Mini/Tiny", // invalid
                "MS"=>"Midi/Mini",
                "LMS" => 'Conjunta LMS',
                "LMST" => 'Conjunta LMST', // invalid
                "-LMST" => '' // team4
            ),
            'NombreTandas' => array(
                0	=> '-- Sin especificar --',
                1	=> 'Pre-Agility 1',
                2	=> 'Pre-Agility 2',
                3	=> 'Agility-1 GI Standard',
                4	=> 'Agility-1 GI Midi',
                5	=> 'Agility-1 GI Mini',
                6	=> 'Agility-2 GI Standard',
                7	=> 'Agility-2 GI Midi',
                8	=> 'Agility-2 GI Mini',
                9	=> 'Agility GII Standard',
                10	=> 'Agility GII Midi',
                11	=> 'Agility GII Mini',
                12	=> 'Agility GIII Standard',
                13	=> 'Agility GIII Midi',
                14	=> 'Agility GIII Mini',
                15	=> 'Agility Standard', //  Individual-Open
                16	=> 'Agility Midi',	//  Individual-Open
                17	=> 'Agility Mini', //  Individual-Open
                18	=> 'Agility Equipos Std', // team best
                19	=> 'Agility Equipos Midi',// team best
                20	=> 'Agility Equipos Mini',	 // team best
                // en jornadas por equipos conjunta tres alturas se mezclan categorias M y S
                21	=> 'Ag. Equipos Std',// team combined
                22	=> 'Ag. Equipos Midi/Mini', // team combined
                23	=> 'Jumping GII Standard',
                24	=> 'Jumping GII Midi',
                25	=> 'Jumping GII Mini',
                26	=> 'Jumping GIII Standard',
                27	=> 'Jumping GIII Midi',
                28	=> 'Jumping GIII Mini',
                29	=> 'Jumping Standard',//  Individual-Open
                30	=> 'Jumping Midi',	//  Individual-Open
                31	=> 'Jumping Mini', //  Individual-Open
                32	=> 'Jumping Equipos Std',	// team best
                33	=> 'Jumping Equipos Midi',// team best
                34	=> 'Jumping Equipos Mini',	// team best
                // en jornadas por equipos conjunta 3 alturas se mezclan categorias M y S
                35	=> 'Jp. Equipos Std',// team combined
                36	=> 'Jp. Equipos Midi/Mini', // team combined
                // en las rondas KO, los perros compiten todos contra todos
                37	=> 'Ronda K.O.',
                38	=> 'Manga Especial Standard',
                39	=> 'Manga Especial Midi',
                40	=> 'Manga Especial Mini',

                // LAS SIGUIENTES CONFIGURACIONES NO EXISTEN EN RSCE (3 alturas)
                // "Tiny" support for Pruebas de cuatro alturas
                41	=> 'Agility-1 GI Tiny',
                42	=> 'Agility-2 GI Tiny',
                43	=> 'Agility GII Tiny',
                44	=> 'Agility GIII Tiny',	// no existe
                45	=> 'Agility Tiny', //  Individual-Open
                46	=> 'Agility Equipos Tiny',// team best
                // en equipos4  cuatro alturas  agrupamos por LM y ST
                47	=> 'Ag. Equipos Large/Medium', // team combined
                48	=> 'Ag. Equipos Small/Tiny', // team combined
                49	=> 'Jumping GII Tiny',
                50	=> 'Jumping GIII Tiny', // no existe
                51	=> 'Jumping Tiny', //  Individual-Open
                52	=> 'Jumping team Tiny',	 // team best
                53	=> 'Jp. teams Large/Medium',  // team combined
                54	=> 'Jp. teams Small/Tiny',// team combined
                55	=> 'Special round Tiny',
                56	=> 'Agility-3 GI Large',	 // extra rounds for GI RFEC
                57	=> 'Agility-3 GI Medium',
                58	=> 'Agility-3 GI Small',
                59	=> 'Agility-3 GI Tiny'
            ),
            'TipoMangas' => array(
                0 =>	array( 0, 'Nombre Manga largo',	'Grado corto',	'Nombre manga',	'Grado largo',  'IsAgility'),
                1 =>	array( 1, 'Pre-Agility Manga 1', 		'P.A.',	'PreAgility 1',	'Pre-Agility',  true),
                2 => 	array( 2, 'Pre-Agility Manga 2', 		'P.A.',	'PreAgility 2',	'Pre-Agility',  false),
                3 =>	array( 3, 'Agility Grado I Manga 1',	'GI',	'Agility-1 GI',	'Grado I',      true),
                4 => 	array( 4, 'Agility Grado I Manga 2',	'GI',	'Agility-2 GI',	'Grado I',      true),
                5 =>	array( 5, 'Agility Grado II', 			'GII',	'Agility GII',	'Grado II',     true),
                6 =>	array( 6, 'Agility Grado III', 			'GIII',	'Agility GIII',	'Grado III',    true),
                7 =>	array( 7, 'Agility', 	        		'-',	'Agility',		'Individual',   true), // Open
                8 =>	array( 8, 'Agility Equipos',			'-',	'Ag. Equipos',	'Equipos',      true), // team best
                9 =>	array( 9, 'Agility Equipos'				,'-',	'Ag. Equipos.',	'Equipos',      true), // team combined
                10 =>	array( 10,'Jumping Grado II',			'GII',	'Jumping GII',	'Grade II',     false),
                11 =>	array( 11,'Jumping Grado III',			'GIII',	'Jumping GIII',	'Grade III',    false),
                12 =>	array( 12,'Jumping',    				'-',	'Jumping',		'Individual',   false), // Open
                13 =>	array( 13,'Jumping Equipos'				,'-',   'Jp. Equipos',	'Equipos',      false), // team best
                14 =>	array( 14,'Jumping Equipos'				,'-',  	'Jp. Equipos',	'Equipos',      false), // team combined
                15 =>	array( 15,'Manga K.O.', 				'-',	'Manga K.O.',	'K.O.',         false),
                16 =>	array( 16,'Manga Especial', 			'-',	'Manga Especial','Individual',  true), // special round, no grades
                17 => 	array( 17,'Agility Grado I Manga 3',	'GI',	'Agility-3 GI',	'Grado I',      true) // on RFEC special G1 3rd round
            )
        );
    }

}
?>