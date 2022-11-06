<?php
class RSCE extends Federations {

    function __construct() {
        parent::__construct();
        // combine global data with specific data for this federation
        $this->config= array_merge ($this->config, array(
            'ID'    => 0,
            'Name'  => 'RSCE',
            'ClassName' => get_class($this),
            'LongName' => 'Real Sociedad Canina de España',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => 'rsce.png',
            'Logo'     => 'rsce.png',
            'ParentLogo'   => 'fci.png',
            'WebURL' => 'http://www.rsce.es/',
            'ParentWebURL' => 'http://www.fci.org/',
            'Email' => 'agility@rsce.es',
            'Heights' => 5,
            'Grades' => 3,
            'Games' => 0,
            'International' => 0,
            'LicenseType' => Federations::$LICENSE_REQUIRED_SHORT, // no license required
            'RoundsG1' => 3, // on rfec may be 3
            'Recorridos' => array(
                _('Common course'),
                _('XL+L / M+S+XS'),
                _('Separate courses'),
                _('XL+L / M / S+XS')
            ),
            'ListaGradosShort' => array(
                '-' => 'Sin especificar',
                // 'Jr' => 'Jr.',
                'Sr' => 'Sr.',
                // 'Ch' => 'Ch.',
                // 'Par' => 'Par.',
                'GI' => 'GI',
                'GII'=> 'GII',
                'GIII' => 'GIII',
                'P.A.' => 'P.A.',
                'P.B.' => 'P.B.' // "perro en blanco"
            ),
            'ListaGrados'    => array (
                '-' => 'Sin especificar',
                // 'Jr' => 'Junior',
                // 'Sr' => 'Senior',
                // 'Ch' => 'Infantil',
                // 'Par' => 'ParaAgility',
                'GI' => 'Grado I',
                'GII'=> 'Grado II',
                'GIII' => 'Grado III',
                'P.A.' => 'Pre-Agility',
                'P.B.' => 'Perro en Blanco'
            ),
            'ListaCategoriasShort' => array (
                '-' => '-',
                'X' => 'XL',
                'L' => 'Lrg',
                'M' => 'Med',
                'S' => 'Sml',
                'T' => 'XS'
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                'X' => 'Extra Large',
                'L' => 'Large',
                'M' => 'Medium',
                'S' => 'Small',
                'T' => 'Extra Small'
            ),
            'ListaCategoriasShort3' => array (
                '-' => '-',
                // 'X' => 'XL',
                'L' => 'Lrg',
                'M' => 'Med',
                'S' => 'Sml',
                // 'T' => 'XS'
            ),
            'ListaCategorias3' => array (
                '-' => 'Sin especificar',
                // 'X' => 'Extra Large',
                'L' => 'Large',
                'M' => 'Medium',
                'S' => 'Small',
                // 'T' => 'Extra Small'
            ),
            'ListaCatGuias' => array (
                '-' => 'Sin especificar',
                // 'I' => 'Infantil',
                'J' => 'Junior',
                'A' => 'Adulto',
                'S' => 'Senior',
                'R' => 'Retirado',
                // 'P' => 'Para-Agility',
            ),
            'InfoManga3' => array( // 3 alturas
                array('L' => 'Large',   'M' => 'Medium',      'S' => 'Small',       'T' => '',    'X' => ''), // separate courses
                array('L' => 'Large',   'M' => 'Med+Sml',     'S' => '',           'T' => '',    'X' => ''), // mixed 2 courses
                array('L' => 'Recorrido comun','M' => '',      'S' => '',           'T' => '',    'X' => ''), // common
                array('L' => '',            'M' => '',         'S' => '',           'T' => '',    'X' => '') // mixed 3 courses
            ),
            // RSCE no tiene recorridos a cuatro alturas
            'InfoManga5' => array( // 5 alturas
                array('L' => 'Large',   'M' => 'Medium',      'S' => 'Small',       'T' => 'XSmall',    'X' => 'XLarge'), // separate courses
                array('L' => '',        'M' => 'Med+Sml+XSml','S' => '',            'T' => '',          'X' => 'XLarge+Large'), // mixed 2 courses
                array('L' => '',        'M' => '',            'S' => '',            'T' => '',          'X' => 'XL+L+M+S+XS'), // common
                array('L' => '',        'M' => 'Medium',      'S' => 'Small+XSmall','T' => '',          'X' => 'XLarge+Large') // mixed 3 courses
            ),
            'Modes3' => array(
                //  categorias            L   M   S   T   X
                array(/* separado */ 0, 1, 2, -1, -1),
                array(/* mixto */    0, 3, 3, -1, -1),
                array(/* conjunto */ 4, 4, 4, 4,  4 ), // pre-agility is -XLMST in tandas cat assignment
                array(/* 3 grupos */-1,-1,-1, -1, -1 ) // not used in 3 heights
            ),
            'Modes' => array(
                //  categorias            L   M   S   T   X
                array(/* 0: separado */   0,  1,  2,  5,  9 ),
                array(/* 1: 2 groups */  10, 11, 11, 11, 10 ),
                array(/* 2: conjunto */  12, 12, 12, 12, 12 ), // pre-agility is declared as -XLMST
                array(/* 3: 3 grupos */  10,  1,  7,  7, 10 ) // not used in 3 heights
            ),
            'ModeStrings3' => array( // text to be shown on each category on 3 heights rounds
                array(/* separado */ "Large", "Medium", "Small", "Invalid","Invalid"),
                array(/* 2 grupos */ "Large", "Med+Sml", "Med+Sml", "Invalid","Invalid"),
                array(/* conjunto */ "Conjunta", "Conjunta", "Conjunta", "Conjunta","Conjunta"),
                array(/* 3 grupos */ "Invalid", "Medium", "Small+XS", "Invalid","XL+Large") // not used in 3 heights
            ),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ "Large", "Medium", "Small", "XSmall","XLarge"),
                array(/* mixto */    "XLarge+Large", "Med+Small+XSmall", "Med+Small+XSmall", "Med+Small+XSmall","XLarge+Large"),
                array(/* conjunto */ "Conjunta", "Conjunta", "Conjunta", "Conjunta","Conjunta"),
                array(/* conjunto */ "XLarge+Large", "Medium", "Small+XSmall", "Small+XSmall","XLarge+Large")
            ),
            'IndexedModes' => array (
                /* 0 - L    */ "Large",
                /* 1 - M    */ "Medium",
                /* 2 - S    */ "Small",
                /* 3 - MS   */ "Medium+Small",
                /* 4 - LMS  */ "Conjunta L/M/S",
                /* 5 - T    */ "XSmall",
                /* 6 - LM   */ "Large+Medium",
                /* 7 - ST   */ "Small+XSmall",
                /* 8 - LMST */ "Conjunta L/M/S/XS",
                /* 9 - X    */ "Extra Large",
                /*10 - XL   */ "Extra Large + Large",
                /*11 - MST  */ "Med+Small + XSmall",
                /*12 - XLMST*/ "Conjunta XL/L/M/S/XS"
            ),
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Large",
                "M"=>"Medium",
                "S"=>"Small",
                "T"=>"XSmall",
                "LM"=>"Large/Medium",
                "ST"=>"Small/XSmall",
                "MS"=>"Medium/Small",
                "LMS" => 'Conjunta LMS',
                "-LMS" => 'Conjunta LMS',
                "LMST" => 'Conjunta LMST',
                "-LMST" => 'Conjunta LMST',
                "X" => 'XLarge',
                "XL" => 'XLarge/Large',
                "MST" => 'Medium/Small/XSmall',
                "XLMST" => 'XL/L/M/S/XS',
                "-XLMST" => 'XL/L/M/S/XS'
            ),
            'NombreTandas' => array(
                0	=> '-- Sin especificar --',
                1	=> 'Pre-Agility 1',
                2	=> 'Pre-Agility 2',
                3	=> 'Grado 1 Manga 1 Large',
                4	=> 'Grado 1 Manga 1 Medium',
                5	=> 'Grado 1 Manga 1 Small',
                6	=> 'Grado 1 Manga 2 Large',
                7	=> 'Grado 1 Manga 2 Medium',
                8	=> 'Grado 1 Manga 2 Small',
                9	=> 'Agility GII Large',
                10	=> 'Agility GII Medium',
                11	=> 'Agility GII Small',
                12	=> 'Agility GIII Large',
                13	=> 'Agility GIII Medium',
                14	=> 'Agility GIII Small',
                15	=> 'Agility Large', //  Individual-Open
                16	=> 'Agility Medium',	//  Individual-Open
                17	=> 'Agility Small', //  Individual-Open
                18	=> 'Agility Equipos Large', // team best
                19	=> 'Agility Equipos Medium',// team best
                20	=> 'Agility Equipos Small',	 // team best
                // en jornadas por equipos conjunta tres alturas se mezclan categorias M y S
                21	=> 'Ag. Equipos XL / L',// team combined
                22	=> 'Ag. Equipos M / S / XS', // team combined
                23	=> 'Jumping GII Large',
                24	=> 'Jumping GII Medium',
                25	=> 'Jumping GII Small',
                26	=> 'Jumping GIII Large',
                27	=> 'Jumping GIII Medium',
                28	=> 'Jumping GIII Small',
                29	=> 'Jumping Large',//  Individual-Open
                30	=> 'Jumping Medium',	//  Individual-Open
                31	=> 'Jumping Small', //  Individual-Open
                32	=> 'Jumping Equipos Large',	// team best
                33	=> 'Jumping Equipos Medium',// team best
                34	=> 'Jumping Equipos Small',	// team best
                // en jornadas por equipos conjunta 3 alturas se mezclan categorias X,L y M,S,T
                35	=> 'Jp. Equipos XL / L ',// team combined
                36	=> 'Jp. Equipos M / S / XS', // team combined
                // en las rondas KO, los perros compiten todos contra todos
                37	=> 'Manga K.O. - 1',
                38	=> 'Manga Especial Large',
                39	=> 'Manga Especial Medium',
                40	=> 'Manga Especial Small',
                // "XSmall" support for Pruebas de cuatro alturas
                41	=> 'Grado 1 Manga 1 XSmall',
                42	=> 'Grado 1 Manga 2 XSmall',
                43	=> 'Agility GII XSmall',
                44	=> 'Agility GIII XSmall',
                45	=> 'Agility XSmall', //  Individual-Open
                46	=> 'Agility Equipos XSmall',// team best

                // en equipos4  cuatro alturas  agrupamos por LM y ST
                47	=> 'Ag. Equipos Large/Medium', // team combined
                48	=> 'Ag. Equipos Small/XSmall', // team combined
                49	=> 'Jumping GII XSmall',
                50	=> 'Jumping GIII XSmall',
                51	=> 'Jumping XSmall', //  Individual-Open
                52	=> 'Jumping team XSmall',	 // team best
                53	=> 'Jp. teams Large/Medium',  // team combined
                54	=> 'Jp. teams Small/XSmall',// team combined
                55	=> 'Special round XSmall',
                56	=> 'Grado 1 Manga 3 Large',	 // extra rounds for GI temporada 2020
                57	=> 'Grado 1 Manga 3 Medium',
                58	=> 'Grado 1 Manga 3 Small',
                59	=> 'Grado 1 Manga 3 XSmall',
                // resto de las mangas k.o.
                60	=> 'Manga K.O. - 2',
                61	=> 'Manga K.O. - 3',
                62	=> 'Manga K.O. - 4',
                63	=> 'Manga K.O. - 5',
                64	=> 'Manga K.O. - 6',
                65	=> 'Manga K.O. - 7',
                66	=> 'Manga K.O. - 8',

                // las siguientes mangas no tienen sentido en RSCE, pero las dejamos por compatibilidad
                // tandas para games/wao ( cuatro categorias, siete mangas distintas )
                67	=> 'Agility A 650',
                68	=> 'Agility A 525',
                69	=> 'Agility A 400',
                70	=> 'Agility A 300',
                71	=> 'Agility B 650',
                72	=> 'Agility B 525',
                73	=> 'Agility B 400',
                74	=> 'Agility B 300',
                75	=> 'Jumping A 650',
                76	=> 'Jumping A 525',
                77	=> 'Jumping A 400',
                78	=> 'Jumping A 300',
                79	=> 'Jumping B 650',
                80	=> 'Jumping B 525',
                81	=> 'Jumping B 400',
                82	=> 'Jumping B 300',
                83	=> 'Snooker 650',
                84	=> 'Snooker 525',
                85	=> 'Snooker 400',
                86	=> 'Snooker 300',
                87	=> 'Gambler 650',
                88	=> 'Gambler 525',
                89	=> 'Gambler 400',
                90	=> 'Gambler 300',
                91	=> 'SpeedStakes 650',
                92	=> 'SpeedStakes 525',
                93	=> 'SpeedStakes 400',
                94	=> 'SpeedStakes 300',
                // mangas para junior
                95  => 'Junior 1 Large',
                96  => 'Junior 1 Medium',
                97  => 'Junior 1 Small',
                98  => 'Junior 1 XSmall',
                99  => 'Junior 2 Large',
                100  => 'Junior 2 Medium',
                101  => 'Junior 2 Small',
                102  => 'Junior 2 XSmall',
                103 => 'Senior 1 Large',
                104 => 'Senior 1 Medium',
                105 => 'Senior 1 Small',
                106 => 'Senior 1 XSmall',
                107 => 'Senior 2 Large',
                108 => 'Senior 2 Medium',
                109 => 'Senior 2 Small',
                110 => 'Senior 2 XSmall',
                // tandas nuevas para xl y XSmall en rfec 5 alturas
                111	=> 'Junior 1 XLarge',
                112	=> 'Junior 2 XLarge',
                113	=> 'Senior 1 XLarge',
                114	=> 'Senior 2 XLarge',
                115	=> 'Grado 1 Manga 1 XLarge',
                116	=> 'Grado 1 Manga 2 XLarge',
                117	=> 'Grado 1 Manga 3 XLarge',
                118	=> 'Agility GII XLarge',
                119	=> 'Jumping GII XLarge',
                120	=> 'Agility GIII XLarge',
                121	=> 'Jumping GIII XLarge',
                122	=> 'Agility XLarge', // open X
                123	=> 'Jumping XLarge', // open X
                124	=> 'Agility Equipos XLarge', // team X
                125	=> 'Jumping Equipos XLarge',
                126	=> 'Manga especial XLarge',
                // jornadas team mixtas extras para cinco alturas
                127	=> 'Ag. Eq. XLarge/Large', // team combined
                128	=> 'Jp. Eq. XLarge/Large', // team combined
                129	=> 'Ag. Eq. Medium/Small/XSmall',  // team combined
                130	=> 'Jp. Eq. Medium/Small/XSmall', // team combined
                // JAMC 2021-06-11 add children and para-agility rounds
                131	=> 'Children Agility XLarge',
                132	=> 'Children Jumping XLarge',
                133	=> 'Children Agility Large',
                134	=> 'Children Jumping Large',
                135	=> 'Children Agility Medium',
                136	=> 'Children Jumping Medium',
                137	=> 'Children Agility Small',
                138	=> 'Children Jumping Small',
                139	=> 'Children Agility Toy',
                140	=> 'Children Jumping Toy',
                141	=> 'ParaAgility Agility XLarge',
                142	=> 'ParaAgility Jumping XLarge',
                143	=> 'ParaAgility Agility Large',
                144	=> 'ParaAgility Jumping Large',
                145	=> 'ParaAgility Agility Medium',
                146	=> 'ParaAgility Jumping Medium',
                147	=> 'ParaAgility Agility Small',
                148	=> 'ParaAgility Jumping Small',
                149	=> 'ParaAgility Agility Toy',
                150	=> 'ParaAgility Jumping Toy'
            ),
            'TipoMangas' => array(
                0 =>	array( 0, 'Nombre Manga largo',	'Grado corto',	'Nombre manga',	'Grado largo',  'IsAgility'),
                1 =>	array( 1, 'Pre-Agility Manga 1', 		'P.A.',	'PreAgility 1',	'Pre-Agility',  1),
                2 => 	array( 2, 'Pre-Agility Manga 2', 		'P.A.',	'PreAgility 2',	'Pre-Agility',  2),
                3 =>	array( 3, 'Grado I Manga 1',	        'GI',	'Manga 1 GI',	'Grado I',      1),
                4 => 	array( 4, 'Grado I Manga 2',	        'GI',	'Manga 2 GI',	'Grado I',      2),
                5 =>	array( 5, 'Agility Grado II', 			'GII',	'Agility GII',	'Grado II',     1),
                6 =>	array( 6, 'Agility Grado III', 			'GIII',	'Agility GIII',	'Grado III',    1),
                7 =>	array( 7, 'Agility', 	        		'-',	'Agility',		'Individual',   1), // Open
                8 =>	array( 8, 'Agility Equipos',			'-',	'Ag. Equipos',	'Equipos',      1), // team best
                9 =>	array( 9, 'Agility Equipos'				,'-',	'Ag. Equipos.',	'Equipos',      1), // team combined
                10 =>	array( 10,'Jumping Grado II',			'GII',	'Jumping GII',	'Grado II',     2),
                11 =>	array( 11,'Jumping Grado III',			'GIII',	'Jumping GIII',	'Grado III',    2),
                12 =>	array( 12,'Jumping',    				'-',	'Jumping',		'Individual',   2), // Open
                13 =>	array( 13,'Jumping Equipos'				,'-',   'Jp. Equipos',	'Equipos',      2), // team best
                14 =>	array( 14,'Jumping Equipos'				,'-',  	'Jp. Equipos',	'Equipos',      2), // team combined
                15 =>	array( 15,'K.O. Primera manga',			'-',	'K.O. manga 1',	'K.O. 1',       1),
                16 =>	array( 16,'Manga Especial', 			'-',	'Manga Especial','Individual',  1), // special round, no grades
                17 => 	array( 17,'Grado I Manga 3',	        'GI',	'Manga 3 GI',	'Grado I',      3), // special G1 3rd round
                // mangas extra para K.O.
                18 =>	array( 18,'K.O. Segunda manga',			'-',	'K.O. Manga 2',	'K.O. - 2',      2),
                19 =>	array( 19,'K.O. Tercera manga',			'-',	'K.O. Manga 3',	'K.O. - 3',      3),
                20 =>	array( 20,'K.O. Cuarta manga',			'-',	'K.O. Manga 4',	'K.O. - 4',      4),
                21 =>	array( 21,'K.O. Quinta manga',			'-',	'K.O. Manga 5',	'K.O. - 5',      5),
                22 =>	array( 22,'K.O. Sexta manga',			'-',	'K.O. Manga 6',	'K.O. - 6',      6),
                23 =>	array( 23,'K.O. Septima manga',			'-',	'K.O. Manga 7',	'K.O. - 7',      7),
                24 =>	array( 24,'K.O. Octava manga',			'-',	'K.O. Manga 8',	'K.O. - 8',      8),
                /**** estas mangas no existen en rsce. se ponen por compatibilidad *********/
                // mandas extras para wao
                25 =>	array( 25,'Agility A',			        '-',	'Agility A',	'Ag. A',        1),
                26 =>	array( 26,'Agility B',			        '-',	'Agility B',	'Ag. B',        2),
                27 =>	array( 27,'Jumping A',			        '-',	'Jumping A',	'Jp. A',        3),
                28 =>	array( 28,'Jumping B',			        '-',	'Jumping B',	'Jp. B',        4),
                29 =>	array( 29,'Snooker',			        '-',	'Snooker',	    'Snkr',         5),
                30 =>	array( 30,'Gambler',			        '-',	'Gambler',	    'Gmblr',        6),
                31 =>	array( 31,'SpeedStakes',			    '-',	'SpeedStakes',	'SpdStk',       7), // single round
                // junior ( really should be a separate journey with every cats and grades, but people doesn't follow rules... )
                32 =>	array( 32,'Agility Junior',	            'Jr',	'Ag Junior',	    'Ag. Jr.',   1),
                33 => 	array( 33,'Jumping Junior',	            'Jr',	'Jp Junior',	    'Jp. Jr.',   2),
                34 =>	array( 34,'Agility Senior',	            'Sr',	'Senior Ag',	    'Ag. Sr.',   1),
                35 => 	array( 35,'Jumping Senior',	            'Sr',	'Senior Jp',	    'Ag. Sr',    2),
                36 =>	array( 36,'Agility Infantil',	        'Ch',	'Ag Infantil',	    'Ag Inf',    1),
                37 => 	array( 37,'Jumping Infantil',	        'Ch',	'Jp Infantil',	    'Jp Inf' ,   2),
                38 =>	array( 38,'Ag. ParaAgility',	        'PA',	'P.Agility Ag',	    'PA. Ag',    1),
                39 => 	array( 30,'Jp. ParaAgility',	        'PA',	'P.Agility Jp',	    'PA. Jp',    2),
            ),
            'TipoRondas' => array(
                /* 0 */ array(0,	''),
                /* 1 */ array(1,	_('Pre-Agility') ),
                /* 2 */ array(2,	_('Pre-Agility') ), // 2-round preagility. no longer use since 3.4.X
                /* 3 */ array(4,	_('Grade I') ),
                /* 4 */ array(8,	_('Grade II') ),
                /* 5 */ array(16,	_('Grade III') ),   // not used in RFEC
                /* 6 */ array(32,	_('Individual') ), // Open
                /* 7 */ array(64,	_('Teams Best') ),
                /* 8 */ array(128,	_('Teams All') ),
                /* 9 */ array(256,	_('K.O. Round') ),
                /*10 */ array(512,	_('Special Round') ),
                /*11 */ array(24,	_('Grade II-III') ), // not used in RFEC
                /*12 */ array(1024,	_('Teams 2best') ), // not used since 4.2.x
                /*13 */ array(2048,	_('Teams 2') ),     // not used since 4.2.x
                /*14 */ array(4096,	_('Teams 3') ),     // not used since 4.2.x
                /*15 */ array(8192,	_('Games / WAO') ),
                /*16 */ array(16384,_('Young') ),
                /*17 */ array(32768,_('Senior') ),
                /*18 */ array(65536,  _('Children') ),
                /*19 */ array(131072, _('ParaAgility') ),
            )
        ));
    }
}
?>