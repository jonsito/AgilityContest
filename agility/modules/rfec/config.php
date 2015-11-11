<?php
class RFEC extends Federations {

    function __construct() {
        $this->config= array (
            'ID'    => 1,
            'Name'  => 'RFEC',
            'LongName' => 'Real Federacion Española de Caza',
            'Logo'     => '/agility/modules/rfec/rfec.png',
            'ParentLogo'   => '/agility/modules/rfec/csd.png',
            'Heights' => 4,
            'Grades' => 2,
            'Recorridos' => array('Common course',"Standard + Medium / Small + Toy","Separate courses"),
            'ListaGrados'    => array (
                '-' => 'Sin especificar',
                'Baja' => 'Baja temporal',
                'GI' => 'Iniciacion',
                'GII'=> 'Competicion',
                'GIII' => 'Grado III', // no existe
                'P.A.' => 'Promocion',
                'P.B.' => 'Perro en Blanco',
                'Ret.' => 'Retirado',
            ),
            'ListaCategorias' => array (
                '-' => 'Sin especificar',
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