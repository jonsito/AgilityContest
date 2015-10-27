<?php
class INTL4 extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 8,
            'Name'  => 'Intl-4',
            'LongName' => 'International Contest - 4 heights',
            'Logo'     => 'wao.png',
            'ParentLogo'   => 'wao.png',
            'Grados'    => array (
                '-' => 'Not especified',
                'Baja' => 'Retired',
                'GI' => 'Grade I',
                'GII'=> 'Grade II',
                'GIII' => 'Grade III', // no existe
                'P.A.' => 'Grade 0',
                'P.B.' => 'Trial dog',
                'Ret.' => 'Retirado',
            ),
            'Categorias' => array (
                '-' => 'Not especified',
                'L' => 'Large - 60',
                'M' => 'Medium - 50',
                'S' => 'Small - 40',
                'T' => 'Toy - 30'
            ),
            'Puntuaciones' => function() {} // to point to a function to evaluate califications
        );
    }

}
?>