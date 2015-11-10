<?php
class UCA extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 2,
            'Name'  => 'UCA',
            'LongName' => 'Union de Clubes de Agility',
            'Logo'     => '/agility/modules/uca/uca.png',
            'ParentLogo'   => '/agility/modules/uca/rfec.png',
            'Heights' => 4,
            'Grados'    => array (
                '-' => 'Sin especificar',
                'Baja' => 'Baja temporal',
                'GI' => 'Grado I',
                'GII'=> 'Grado II',
                'GIII' => 'Grado III', // no existe
                'P.A.' => 'Grado 0',
                'P.B.' => 'Perro en Blanco',
                'Ret.' => 'Retirado',
            ),
            'Categorias' => array (
                '-' => 'Sin especificar',
                'L' => 'Large - 60',
                'M' => 'Medium - 50',
                'S' => 'Small - 40',
                'T' => 'Tiny - 30'
            ),
            'Puntuaciones' => function() {} // to point to a function to evaluate califications
        );
    }

}
?>