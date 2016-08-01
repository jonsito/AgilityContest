<?php
class INTL3 extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 9,
            'Name'  => 'Intl3',
            'LongName' => 'International Contest - 3 heights',
            // use basename http absolute path for icons, as need to be used in client side
            'OrganizerLogo'        => '/agility/modules/intl3/fciawc2016.png',
            'Logo'        => '/agility/modules/intl3/rsce.png',
            'ParentLogo'  => '/agility/modules/intl3/fci.png',
            'WebURL' => 'http://www.fci.org',
            'ParentWebURL' => 'http://www.fci.org',
            'Heights' => 3,
            'Grades' => 3,
            'International' => 1,
            'WideLicense' => false, // some federations need extra print space to show license ID
            'Recorridos' => array('Common course',"Standard / Midi + Mini","Separate courses"),
            'ListaGradosShort' => array(
                '-' => '-',
                'Baja' => 'Out',
                'GI' => 'A1',
                'GII'=> 'A2',
                'GIII' => 'A3',
                'P.A.' => 'A0',
                'P.B.' => 'T.d.', // "Test dog"
                'Ret.' => 'Ret.'
            ),
            'ListaGrados'    => array (
                '-' => 'Not specified',
                'GI' => 'Grade I',
                'GII'=> 'Grade II',
                'GIII' => 'Grade III',
                'P.A.' => 'Pre-Agility',
                'P.B.' => 'Test dog',
                'Baja' => 'Temporary out',
                'Ret.' => 'Retired',
            ),
            'ListaCategoriasShort' => array (
                '-' => '-',
                'L' => 'Std',
                'M' => 'Med',
                'S' => 'Small',
                // 'T' => 'Tiny'
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Standard',
                'M' => 'Medium',
                'S' => 'Small',
                // 'T' => 'Tiny'
            ),
            'InfoManga' => array(
                array('L' => _('Standard'),         'M' => _('Medium'),         'S' => _('Small'), 'T' => ''), // separate courses
                array('L' => _('Standard'),         'M' => _('Medium+Small'),   'S' => '',         'T' => ''), // mixed courses
                array('L' => _('Common course'), 'M' => '',                  'S' => '',         'T' => '') // common
            ),
            'Modes' => array(array(/* separado */ 0, 1, 2, -1), array(/* mixto */ 0, 3, 3, -1), array(/* conjunto */ 4, 4, 4, -1 )),
            'ModeStrings' => array( // text to be shown on each category
                array(/* separado */ "Standard", "Medium", "Small", "Invalid"),
                array(/* mixto */ "Standard", "Medium+Small", "Medium+Small", "Invalid"),
                array(/* conjunto */ "Common course", "Common course", "Common course", "Invalid")
            ),
            'IndexedModes' => array ( // modes 5 to 8 are invalid in this federation
                "Standard", "Medium", "Small", "Medium+Small", "Conjunta L/M/S", "Tiny", "Standard+Medium", "Small+Tiny", "Common L/M/S/T"
            ),
            'IndexedModeStrings' => array(
                "-" => "",
                "L"=>"Standard",
                "M"=>"Medium",
                "S"=>"Small",
                "T"=>"Tiny", // invalid
                "LM"=>"Standard/Medium", // invalid
                "ST"=>"Small/Tiny", // invalid
                "MS"=>"Medium/Small",
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
     * @param {object} $m22 datos de la segunda manga
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