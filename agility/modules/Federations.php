<?php
/**
 * Federations.php
 *
Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

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

    /**
     * Translate requested grade key to federation dependent i18n'd one
     * @param {string} $key grade as stored in database
     * @return string resulting string
     */
    public function getGrade($key) {
        if (!array_key_exists($key,$this->Grados)) return _($key);
        return _($this->Grados[$key]);
    }

    /**
     * Translate requested category key to federation dependent i18n'd one
     * @param {string} $key category as stored in database
     * @return string resulting string
     */
    public function getCategory($key) {
        if (!array_key_exists($key,$this->Categorias)) return _($key);
        return _($this->Categorias[$key]);
    }

    /**
     * Reserve FedID 0..4 to national events; 5..9 to internationals
     * @return bool
     */
    public function isInternational() { return ( $this->ID >4)?true:false; }

    /**
     * @return string either i18n'd 'Club' or 'Contry' according federation
     */
    public function getClubString() {
        return $this->isInternational()?_('Club'):_('Country');
    }

    public function get($key) {
        if (array_key_exists($key,$this->config)) return $this->config[$key];
        return null;
    }

    /**
     * Search federation data by providing ID/Name
     * @param {int} $id Federation ID
     * @return {object} requested federation or null if not found
     */
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

    /**
     * Retrieve list of available federation modules
     * @return array $id => $fedData
     */
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

    /*
     * As getFederationList, but return data as expected by jquery-easyui
     */
    static function enumerate() {
        $list=Federations::getFederationList();
        $data=array();
        foreach ($list as $fed) { array_push($data,$fed); }
        $result=array('total' => count($data),$data);
        return $result;
    }
}
?>