<?php
class RFEC extends Federations {

    function __construct() {
        parent::__construct();
        // combine global data with specific data for this federation
        $this->config= array_merge ($this->config, array(
            'ID'    => 1,
            'Name'  => 'RFEC',
            'ClassName' => get_class($this),
            'LongName' => 'Real Federacion Española de Caza',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => 'rfec.png',
            'Logo'     => 'rfec.png',
            'ParentLogo'   => 'csd.png',
            'WebURL' => 'http://www.fecaza.com/',
            'ParentWebURL' => 'http://www.csd.gob.es/',
            'Email' => 'yvonneagility@fecaza.com',
            'Heights' => 4,
            'Grades' => 2,
            'Games' => 0,
            'International' => 0,
            'WideLicense' => true, // some federations need extra print space to show license ID
            'RoundsG1' => 3,
            'ReverseXLMST' => true, // default order is TSMLX instead of XLMST
            'Recorridos' => array(
                _('Common course'),
                "Clases 60+50 / 40+30", // 2 groups
                _("Separate courses"),
                ""
            ),
            'ListaGradosShort' => array(
                '-' => 'Sin especificar',
                'Jr' => 'Junr',
                //'Sr' => 'Sr.',
                'GI' => 'Prom',
                'GII'=> 'Comp',
                // 'GIII' => 'G3',
                'P.A.' => 'Inic',
                'P.B.' => 'P.B.' // "perro en blanco"
            ),
            'ListaGrados'    => array (
                '-' => 'Sin especificar',
                'Jr' => 'Junior',
                // 'Sr' => 'Senior',
                'GI' => 'Promocion (G1)',
                'GII'=> 'Competicion (G2)',
                // 'GIII' => '(G3) no disponible',
                'P.A.' => 'Iniciacion (G0)',
                'P.B.' => 'Perro en Blanco'
            ),
            'ListaCategoriasShort' => array (
                '-' => '-',
                // 'E' => 'Extra',
                'L' => '60',
                'M' => '50',
                'S' => '40',
                'T' => '30'
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                // 'E' => 'Extra Large',
                'L' => 'Clase 60',
                'M' => 'Clase 50',
                'S' => 'Clase 40',
                'T' => 'Clase 30'
            ),
            'ListaCatGuias' => array (
                '-' => 'Sin especificar',
                'I' => 'Infantil',
                'J' => 'Junior',
                'A' => 'Adulto',
                // 'S' => 'Senior',
                'R' => 'Retirado',
                'P' => 'Para-Agility',
            ),
            'InfoManga' => array(
                array('L' => _('Clase 60'),   'M' => _('Clase 50'),'S' => _('Clase 40'), 'T' => _('Clase 30'),'X' => ""), // separate courses
                array('L' => _('Cl. 60+50'),  'M' => '',           'S' => _('Cl. 40+30'),'T' => '',         'X' => ""), // 2 groups
                array('L' => _('60+50+40+30'),'M' => '',           'S' => '',            'T' => '',         'X' => ""), // common
                array('L' => "",              'M' => "",           'S' => "",            'T' => "",         'X' => "") // 3 groups
            ),
            'Modes' => array(
                // categorias        L  M  S  T  X
                array(/* separado */ 0, 1, 2, 5,-1 ),
                array(/* 2 grupos */ 6, 6, 7, 7,-1 ),
                array(/* conjunto */ 8, 8, 8, 8, 8 ), // pre-agility is declared as -XLMST
                array(/* 3 grupos */-1,-1,-1,-1,-1 )
            ),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ "Clase 60",    "Clase 50",     "Clase 40",     "Clase 30",     "Invalid"),
                array(/* 2 grupos */ "Clase 60+50", "Clase 60+50",  "Clase 40+30",  "Clase 40+30",  "Invalid"),
                array(/* conjunto */ "Recorrido comun", "Recorrido comun", "Recorrido comun", "Recorrido comun","Invalid"),
                array(/* 3 grupos */ "Invalid",     "Invalid",      "Invalid",      "Invalid",      "Invalid"),
            ),
            'IndexedModes' => array (
                "Clase 60",
                "Clase 50",
                "Clase 40",
                "Cl. 50+40",
                "Conjunta 60/50/40",
                "Clase 30",
                "Cl. 60+50",
                "Cl. 40+30",
                "Conjunta 60/50/40/30",
                "Extra Large",
                "Large + XL",
                "Common M/S/T",
                "Common X/L/M/S/T"
            ),
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Clase 60",
                "M"=>"Clase 50",
                "S"=>"Clase 40",
                "T"=>"Clase 30",
                "LM"=>"Clase 60/50",
                "ST"=>"Clase 40/30",
                "MS"=>"Clase 50/40",
                "LMS" => 'Conjunta 60/50/40',
                "-LMS" => 'Conjunta 60/50/40',
                "LMST" => 'Conjunta 60/50/40/30',
                "-LMST" => 'Conjunta 60/50/40/30',
                "X" => '', // invalid
                "XL" => '', // invalid
                "MST" => '', // invalid
                "XLMST" => '', // invalid
                "-XLMST" => ''
            ),
            'NombreTandas' => array(
                0	=> '-- Sin especificar --',
                1	=> 'Iniciacion 1',
                2	=> 'Iniciacion 2',
                3	=> 'Agility-1 Promocion 60',
                4	=> 'Agility-1 Promocion 50',
                5	=> 'Agility-1 Promocion 40',
                6	=> 'Agility-2 Promocion 60',
                7	=> 'Agility-2 Promocion 50',
                8	=> 'Agility-2 Promocion 40',
                9	=> 'Agility Competicion 60',
                10	=> 'Agility Competicion 50',
                11	=> 'Agility Competicion 40',
                12	=> 'Agility GIII 60', // no existe en rfec
                13	=> 'Agility GIII 50', // no existe en rfec
                14	=> 'Agility GIII 40', // no existe en rfec
                15	=> 'Agility Clase 60', //  Individual-Open
                16	=> 'Agility Clase 50',	//  Individual-Open
                17	=> 'Agility Clase 40', //  Individual-Open
                18	=> 'Agility Equipos 60', // team best
                19	=> 'Agility Equipos 50',// team best
                20	=> 'Agility Equipos 40',	 // team best
                // en jornadas por equipos conjunta tres alturas se mezclan categorias M y S
                21	=> 'Ag. Equipos 60',// team combined
                22	=> 'Ag. Equipos 50/40', // team combined
                23	=> 'Jumping Competicion 60',
                24	=> 'Jumping Competicion 50',
                25	=> 'Jumping Competicion 40',
                26	=> 'Jumping GIII 60', // no existe en rfec
                27	=> 'Jumping GIII 50', // no existe en rfec
                28	=> 'Jumping GIII 40', // no existe en rfec
                29	=> 'Jumping Clase 60',//  Individual-Open
                30	=> 'Jumping Clase 50',	//  Individual-Open
                31	=> 'Jumping Clase 40', //  Individual-Open
                32	=> 'Jumping Equipos 60',	// team best
                33	=> 'Jumping Equipos 50',// team best
                34	=> 'Jumping Equipos 40',	// team best
                // en jornadas por equipos conjunta 3 alturas se mezclan categorias M y S
                35	=> 'Jp. Equipos 60',// team combined
                36	=> 'Jp. Equipos 50/40', // team combined
                // en las rondas KO, los perros compiten todos contra todos
                37	=> 'K.O. Manga 1',
                38	=> 'Manga Especial 60',
                39	=> 'Manga Especial 50',
                40	=> 'Manga Especial 40',

                // "Tiny" support for Pruebas de cuatro alturas
                41	=> 'Agility-1 Promocion 30',
                42	=> 'Agility-2 Promocion 30',
                43	=> 'Agility Competicion 30',
                44	=> 'Agility GIII 30',	// no existe
                45	=> 'Agility Clase 30', //  Individual-Open
                46	=> 'Agility Equipos 30',// team best
                // en equipos4  cuatro alturas  agrupamos por LM y ST
                47	=> 'Ag. Equipos 60/50', // team combined
                48	=> 'Ag. Equipos 40/30', // team combined
                49	=> 'Jumping Competicion 30',
                50	=> 'Jumping GIII 30', // no existe
                51	=> 'Jumping Clase 30', //  Individual-Open
                52	=> 'Jumping Equipos 30',	 // team best
                53	=> 'Jp. Equipos 60/50',  // team combined
                54	=> 'Jp. Equipos 40/30',// team combined
                55	=> 'Manga Especial 30',
                56	=> 'Agility-3 Promocion 60',	 // extra rounds for GI RFEC
                57	=> 'Agility-3 Promocion 50',
                58	=> 'Agility-3 Promocion 40',
                59	=> 'Agility-3 Promocion 30',
                // resto de las rondas KO. Los perros compiten todos contra todos
                60	=> 'K.O. Manga 2',
                61	=> 'K.O. Manga 3',
                62	=> 'K.O. Manga 4',
                63	=> 'K.O. Manga 5',
                64	=> 'K.O. Manga 6',
                65	=> 'K.O. Manga 7',
                66	=> 'K.O. Manga 8',
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
                95  => 'Junior 1 60',
                96  => 'Junior 1 50',
                97  => 'Junior 1 40',
                98  => 'Junior 1 30',
                99   => 'Junior 2 60',
                100  => 'Junior 2 50',
                101  => 'Junior 2 40',
                102  => 'Junior 2 30',
            ),
            'TipoMangas' => array(
                0 =>	array( 0, 'Nombre Manga largo',	'Grado corto',	'Nombre manga',	'Grado largo', 'IsAgility'),
                1 =>	array( 1, 'Iniciacion Manga 1', 		'P.A.',	'Iniciacion 1',	'Iniciacion',  1),
                2 => 	array( 2, 'Iniciacion Manga 2', 		'P.A.',	'Iniciacion 2',	'Iniciacion',  2),
                3 =>	array( 3, 'Agility Promocion Manga 1',	'GI',	'Ag-1 Promocion',	'Promocion',   1),
                4 => 	array( 4, 'Agility Promocion Manga 2',	'GI',	'Ag-2 Promocion',	'Promocion',   2),
                5 =>	array( 5, 'Agility Competicion', 		'GII',	'Ag. Competicion',	'Competicion', 1),
                6 =>	array( 6, 'Agility Grade III', 			'GIII',	'Agility GIII',	'Grade III',   1), // no existe en caza
                7 =>	array( 7, 'Agility', 	        		'-',	'Agility',		'Individual',  1), // Open
                8 =>	array( 8, 'Agility Equipos',			'-',	'Ag. Teams',	'Teams',       1), // team best
                9 =>	array( 9, 'Agility Equipos'				,'-',	'Ag. Teams.',	'Teams',       1), // team combined
                10 =>	array( 10,'Jumping Competicion',		'GII',	'Jp. Competicion',	'Competicion', 2),
                11 =>	array( 11,'Jumping Grade III',			'GIII',	'Jumping GIII',	'Grado III',   2), // no existe en caza
                12 =>	array( 12,'Jumping',    				'-',	'Jumping',		'Individual',  2), // Open
                13 =>	array( 13,'Jumping Equipos'				,'-',   'Jp. Equipos',	'Equipos',     2), // team best
                14 =>	array( 14,'Jumping Equipos'				,'-',  	'Jp. Equipos',	'Equipos',     2), // team combined
                15 =>	array( 15,'K.O. Manga 1', 				'-',	'K.O. - 1',	    'K.O.',        1),
                16 =>	array( 16,'Manga Especial', 			'-',	'Manga Especial','Individual', 1), // special round, no grades
                17 => 	array( 17,'Agility Promocion Manga 3',	'GI',	'Ag-3 Promocion',	'Promocion',   3), // on RFEC special G1 3rd round
                // mangas extra para K.O.
                18 =>	array( 18,'K.O. Segunda manga',			'-',	'K.O. - 2',     'K.O.',         2),
                19 =>	array( 19,'K.O. Tercera manga',			'-',	'K.O. - 3',	    'K.O.',         3),
                20 =>	array( 20,'K.O. Cuarta manga',			'-',	'K.O. - 4',	    'K.O.',         4),
                21 =>	array( 21,'K.O. Quinta manga',			'-',	'K.O. - 5',	    'K.O.',         5),
                22 =>	array( 22,'K.O. Sexta manga',			'-',	'K.O. - 6',	    'K.O.',         6),
                23 =>	array( 23,'K.O. Septima manga',			'-',	'K.O. - 7',	    'K.O.',         7),
                24 =>	array( 24,'K.O. Octava manga',			'-',	'K.O. - 8',	    'K.O.',         8),
                // mandas extras para wao
                25 =>	array( 25,'Agility A',			        '-',	'Agility A',	'Ag. A',        1),
                26 =>	array( 26,'Agility B',			        '-',	'Agility B',	'Ag. B',        3),
                27 =>	array( 27,'Jumping A',			        '-',	'Jumping A',	'Jp. A',        2),
                28 =>	array( 28,'Jumping B',			        '-',	'Jumping B',	'Jp. B',        4),
                29 =>	array( 29,'Snooker',			        '-',	'Snooker',	    'Snkr ',        5),
                30 =>	array( 30,'Gambler',			        '-',	'Gambler',	    'Gmblr',        6),
                31 =>	array( 31,'SpeedStakes',			    '-',	'SpeedStakes',	'SpdStk',       7), // single round
                // PENDING: revise grade. perhaps need to create an specific 'Jr' grade for them
                32 =>	array( 32,'Junior Manga 1',	            'Jr',	'Junior 1',	    'Jr. 1',        1),
                33 => 	array( 33,'Junior Manga 2',	            'Jr',	'Junior 2',	    'Jr. 2',        2),
                34 =>	array( 34,'Senior Manga 1',	            'Sr',	'Senior 1',	    'Sr. 1',        1),
                35 => 	array( 35,'Senior Manga 2',	            'Sr',	'Senior 2',	    'Sr. 2',        2),
            ),
            'TipoRondas' => array(
                /* 0 */ array(0,	''),
                /* 1 */ array(1,	_('Iniciacion') ),
                /* 2 */ array(2,	_('Iniciacion') ), // 2-round preagility. no longer use since 3.4.X
                /* 3 */ array(4,	_('Promocion') ),
                /* 4 */ array(8,	_('Competicion') ),
                /* 5 */ array(16,	_('Grade III') ),   // not used in RFEC
                /* 6 */ array(32,	_('Individual') ), // Open
                /* 7 */ array(64,	_('Teams Best') ),
                /* 8 */ array(128,	_('Teams All') ),
                /* 9 */ array(256,	_('K.O. Round') ),
                /*10 */ array(512,	_('Special Round') ),
                /*11 */ array(24,	_('Grade II-III') ), // not used in RFEC
                /*12 */ array(1024,	_('Teams 2best') ), // not used since 4.2.x
                /*13 */ array(2048,	_('Teams 2') ), // not used since 4.2.x
                /*14 */ array(4096,	_('Teams 3') ), // not used since 4.2.x
                /*15 */ array(8192,	_('Games / WAO') ),
                /*16 */ array(16384,_('Children / Young') ),
                /*17 */ array(32768,_('Senior') ),
            )
        ));
    }


}
?>