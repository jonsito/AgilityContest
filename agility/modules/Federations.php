<?php
class Federations {
    protected $config = array (
        'ID'    => 0,
        'Name'  => '',
        'LongName' => '',
        'Logo'     => '',
        'ParentLogo'   => '',
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
            'L' => 'Large - Standard - 60',
            'M' => 'Medium - Midi - 50',
            'S' => 'Small - Mini - 40',
            'T' => 'Tiny - Toy - 30'
        ),
        'Puntuaciones' => null // to point to a function to evaluate califications
    );

    public function get($key) {
        if (array_key_exists($key,$this->config)) return $this->config[$key];
        return null;
    }

    static function getFederation($id) {
        $fedList=array();
        // analize sub-directories looking for matching ID or name
        // Notice that module class name should be the same as uppercase'd module directory name
        foreach( glob(__DIR__.'/*',GLOB_ONLYDIR) as $federation) {
            $name=strtoupper( basename($federation));
            require_once($federation."/config.php");
            $fed=new $name;
            if (!$fed) continue;
            if ($fed->get('ID')===$id) return $fed;
            if ($fed->get('Name')===$id) return $fed;
        }
        // arriving here means requested federation not found
        return null;
    }

    static function getFederationList() {
        $fedList=array();
        foreach( glob(__DIR__.'/*',GLOB_ONLYDIR) as $federation) {
            $name=strtoupper( basename($federation));
            require_once($federation."/config.php");
            $fed=new $name;
            if (!$fed) continue;
            $id=$fed->get('ID');
            $fedList[$id]=$fed;
        }
        return $fedList;
    }
}
?>