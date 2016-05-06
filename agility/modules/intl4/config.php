<?php
class INTL4 extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 8,
            'Name'  => 'Intl4',
            'LongName' => 'International Contest - 4 heights',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'     => '/agility/modules/intl4/wao.png',
            'Logo'     => '/agility/modules/intl4/wao.png',
            'ParentLogo'   => '/agility/modules/intl4/wao.png',
            'WebURL' => 'http://www.worldagilityopen.com/',
            'ParentWebURL' => 'http://www.worldagilityopen.com/',
            'Heights' => 4,
            'Grades' => 2,
            'International' => 1,
            'WideLicense' => false, // some federations need extra print space to show license ID
            'Recorridos' => array('Common course',"Standard + Medium / Small + Toy","Separate courses"),
            'ListaGradosShort' => array(
                '-' => 'Sin especificar',
                'Baja' => 'Out',
                'GI' => 'A1',
                'GII'=> 'A2',
                'GIII' => 'A3', //  invalid for 2-grades contests
                'P.A.' => 'A0',
                'P.B.' => 'T.d.', // "Test dog"
                'Ret.' => 'Ret.'
            ),
            'ListaGrados'    => array (
                '-' => 'Not specified ',
                'GI' => 'Grade I',
                'GII'=> 'Grade II',
                // 'GIII' => 'Grade III', // no existe
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
            'InfoManga' => array(
                array('L' => _('Large'),         'M' => _('Medium'), 'S' => _('Small'),      'T' => _('Tiny')), // separate courses
                array('L' => _('Large+Medium'),  'M' => '',          'S' => _('Small+Tiny'), 'T' => ''), // mixed courses
                array('L' => _('Common course'), 'M' => '',          'S' => '',              'T' => '') // common
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, 5 ), array(/* mixto */ 6, 6, 7, 7 ), array(/* conjunto */ 8, 8, 8, 8 )),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ "Large", "Medium", "Small", "Tiny"),
                array(/* mixto */ "Large+Medium", "Large+Medium", "Small+Tiny", "Small+Tiny"),
                array(/* conjunto */ "Common course", "Common course", "Common course", "Common course")
            ),
            'IndexedModes' => array (
                "Large", "Medium", "Small", "Medium+Small", "Conjunta L/M/S", "Tiny", "Large+Medium", "Small+Tiny", "Common L/M/S/T"
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
        );
    }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $p datos de la prueba
     * @param {object} $j datos de la jornada
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($p,$j,$m,&$perro,$puestocat) {
        parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
    }

    /**
     * Evalua la calificacion final del perro
     * @param {object} $p datos de la prueba
     * @param {object} $j datos de la jornada
     * @param {object} $m1 datos de la primera manga
     * @param {object} $m2 datos de la segunda manga
     * @param {array} $c1 resultados de la primera manga
     * @param {array} $c2 resultados de la segunda manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalFinalCalification($p,$j,$m1,$m2,$c1,$c2,&$perro,$puestocat){
        parent::evalFinalCalification($p,$j,$m1,$m2,$c1,$c2,$perro,$puestocat);
    }
}
?>