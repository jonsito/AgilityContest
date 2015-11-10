<?php
class RSCE extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 0,
            'Name'  => 'RSCE',
            'LongName' => 'Real Sociedad Canina de España',
            'Logo'     => '/agility/modules/rsce/rsce.png',
            'ParentLogo'   => '/agility/modules/rsce/fci.png',
            'Heights' => 3,
            'Grados'    => array (
                '-' => 'Sin especificar',
                'Baja' => 'Baja temporal',
                'GI' => 'Grado I',
                'GII'=> 'Grado II',
                'GIII' => 'Grado III',
                'P.A.' => 'Pre-Agility',
                'P.B.' => 'Perro en Blanco',
                'Ret.' => 'Retirado',
            ),
            'Categorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Standard - 60',
                'M' => 'Midi - 50',
                'S' => 'Mini - 40',
                'T' => 'Toy - 30' // no existe
            ),
            'Puntuaciones' => function() {} // to point to a function to evaluate califications
        );
    }

}
?>