<?php
class INTL3 extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 9,
            'Name'  => 'Intl-3',
            'LongName' => 'International Contest - 3 heights',
            'Logo'     => 'fci.png',
            'ParentLogo'   => 'fci.png',
            'Grados'    => array (
                '-' => 'Not especified',
                'Baja' => 'Temporary out',
                'GI' => 'Grade I',
                'GII'=> 'Grade II',
                'GIII' => 'Grade III', // no existe
                'P.A.' => 'Pre-Agility',
                'P.B.' => 'Trial dog',
                'Ret.' => 'Retired',
            ),
            'Categorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Standard',
                'M' => 'Medium',
                'S' => 'Small',
                'T' => 'Tiny' // not used
            ),
            'Puntuaciones' => function() {} // to point to a function to evaluate califications
        );
    }

}
?>