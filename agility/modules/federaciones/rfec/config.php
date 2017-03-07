<?php
class RFEC extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 1,
            'Name'  => 'RFEC',
            'LongName' => 'Real Federacion Española de Caza',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => '/agility/modules/federaciones/rfec/rfec.png',
            'Logo'     => '/agility/modules/federaciones/rfec/rfec.png',
            'ParentLogo'   => '/agility/modules/federaciones/rfec/csd.png',
            'WebURL' => 'http://www.fecaza.com/',
            'ParentWebURL' => 'http://www.csd.gob.es/',
            'Email' => 'yvonneagility@fecaza.com',
            'Heights' => 4,
            'Grades' => 2,
            'International' => 0,
            'WideLicense' => true, // some federations need extra print space to show license ID
            'RoundsG1' => 3,
            'Recorridos' => array('Common course',"Large+Medium / Small+Toy","Separate courses"),
            'ListaGradosShort' => array(
                '-' => 'Sin especificar',
                'Baja' => 'Baja',
                'GI' => 'G1',
                'GII'=> 'G2',
                'GIII' => 'G3',
                'P.A.' => 'G0',
                'P.B.' => 'P.B.', // "perro en blanco"
                'Ret.' => 'Ret.'
            ),
            'ListaGrados'    => array (
                '-' => 'Sin especificar',
                'GI' => 'Promocion (G1)',
                'GII'=> 'Competicion (G2)',
                'GIII' => '(G3) no disponible',
                'P.A.' => 'Iniciacion (G0)',
                'P.B.' => 'Perro en Blanco',
                'Baja' => 'Baja temporal ',
                'Ret.' => 'Retirado'
            ),
            'ListaCategoriasShort' => array (
                '-' => '-',
                'L' => 'L - 60',
                'M' => 'M - 50',
                'S' => 'S - 40',
                'T' => 'T - 30'
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Large - 60',
                'M' => 'Medium - 50',
                'S' => 'Small - 40',
                'T' => 'Toy - 30'
            ),
            'InfoManga' => array(
                array('L' => _('Large'),         'M' => _('Medium'), 'S' => _('Small'),     'T' => _('Toy')), // separate courses
                array('L' => _('Large+Medium'),  'M' => '',          'S' => _('Small+Toy'), 'T' => ''), // mixed courses
                array('L' => _('L+M+S+T'),     'M' => '',          'S' => '',              'T' => '') // common
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, 5 ), array(/* mixto */ 6, 6, 7, 7 ), array(/* conjunto */ 8, 8, 8, 8 )),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ "Large", "Medium", "Small", "Toy"),
                array(/* mixto */ "Large+Medium", "Large+Medium", "Small+Toy", "Small+Toy"),
                array(/* conjunto */ "Common course", "Common course", "Common course", "Common course")
            ),
            'IndexedModes' => array (
                "Large", "Medium", "Small", "Medium+Small", "Conjunta L/M/S", "Toy", "Large+Medium", "Small+Toy", "Conjunta L/M/S/T"
            ),
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Large",
                "M"=>"Medium",
                "S"=>"Small",
                "T"=>"Toy",
                "LM"=>"Large/Medium",
                "ST"=>"Small/Toy",
                "MS"=>"Medium/Small",
                "LMS" => 'Conjunta LMS',
                "LMST" => 'Conjunta LMST',
                "-LMST" => ''
            ),
            'NombreTandas' => array(
                0	=> '-- Sin especificar --',
                1	=> 'Iniciacion 1',
                2	=> 'Iniciacion 2',
                3	=> 'Agility-1 GI Large',
                4	=> 'Agility-1 GI Medium',
                5	=> 'Agility-1 GI Small',
                6	=> 'Agility-2 GI Large',
                7	=> 'Agility-2 GI Medium',
                8	=> 'Agility-2 GI Small',
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
                21	=> 'Ag. Equipos Large',// team combined
                22	=> 'Ag. Equipos Med/Small', // team combined
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
                // en jornadas por equipos conjunta 3 alturas se mezclan categorias M y S
                35	=> 'Jp. Equipos Large',// team combined
                36	=> 'Jp. Equipos Med/Small', // team combined
                // en las rondas KO, los perros compiten todos contra todos
                37	=> 'Manga K.O.',
                38	=> 'Manga Especial Large',
                39	=> 'Manga Especial Medium',
                40	=> 'Manga Especial Small',

                // "Tiny" support for Pruebas de cuatro alturas
                41	=> 'Agility-1 GI Toy',
                42	=> 'Agility-2 GI Toy',
                43	=> 'Agility GII Toy',
                44	=> 'Agility GIII Toy',	// no existe
                45	=> 'Agility Toy', //  Individual-Open
                46	=> 'Agility Equipos Toy',// team best
                // en equipos4  cuatro alturas  agrupamos por LM y ST
                47	=> 'Ag. Equipos Large/Medium', // team combined
                48	=> 'Ag. Equipos Small/Toy', // team combined
                49	=> 'Jumping GII Toy',
                50	=> 'Jumping GIII Toy', // no existe
                51	=> 'Jumping Toy', //  Individual-Open
                52	=> 'Jumping Equipos Toy',	 // team best
                53	=> 'Jp. Equipos Large/Medium',  // team combined
                54	=> 'Jp. Equipos Small/Toy',// team combined
                55	=> 'Manga Especial Toy',
                56	=> 'Agility-3 GI Large',	 // extra rounds for GI RFEC
                57	=> 'Agility-3 GI Medium',
                58	=> 'Agility-3 GI Small',
                59	=> 'Agility-3 GI Toy'
            ),
            'TipoMangas' => array(
                0 =>	array( 0, 'Nombre Manga largo',	'Grado corto',	'Nombre manga',	'Grado largo', 'IsAgility'),
                1 =>	array( 1, 'Iniciacion Manga 1', 		'P.A.',	'Iniciacion 1',	'Iniciacion',  true),
                2 => 	array( 2, 'Iniciacion Manga 2', 		'P.A.',	'Iniciacion 2',	'Iniciacion',  false),
                3 =>	array( 3, 'Agility Promocion Manga 1',	'GI',	'Agility-1 GI',	'Promocion',   true),
                4 => 	array( 4, 'Agility Promocion Manga 2',	'GI',	'Agility-2 GI',	'Promocion',   true),
                5 =>	array( 5, 'Agility Competicion', 		'GII',	'Agility GII',	'Competicion', true),
                6 =>	array( 6, 'Agility Grade III', 			'GIII',	'Agility GIII',	'Grade III',   true), // no existe en caza
                7 =>	array( 7, 'Agility', 	        		'-',	'Agility',		'Individual',  true), // Open
                8 =>	array( 8, 'Agility Equipos',			'-',	'Ag. Teams',	'Teams',       true), // team best
                9 =>	array( 9, 'Agility Equipos'				,'-',	'Ag. Teams.',	'Teams',       true), // team combined
                10 =>	array( 10,'Jumping Competicion',		'GII',	'Jumping GII',	'Competicion', false),
                11 =>	array( 11,'Jumping Grade III',			'GIII',	'Jumping GIII',	'Grade III',   false), // no existe en caza
                12 =>	array( 12,'Jumping',    				'-',	'Jumping',		'Individual',  false), // Open
                13 =>	array( 13,'Jumping Equipos'				,'-',   'Jp Equipos',	'Equipos',     false), // team best
                14 =>	array( 14,'Jumping Equipos'				,'-',  	'Jp Equipos',	'Equipos',     false), // team combined
                15 =>	array( 15,'Manga K.O.', 				'-',	'Manga K.O.',	'K.O.',        false),
                16 =>	array( 16,'Manga Especial', 			'-',	'Manga Especial','Individual', true), // special round, no grades
                17 => 	array( 17,'Agility Promocion Manga 3',	'GI',	'Agility-3 GI',	'Promocion',   true) // on RFEC special G1 3rd round
            )
        );
    }


}
?>