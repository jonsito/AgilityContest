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

/* for poedit */
$dummy= _('Common course');
$dummy= _('Separate courses');
/* for poedit */

class Federations {
    protected $config = array (
        'ID'    => 0,
        'Name'  => '',
        'LongName' => '',
        'Logo'     => '',
        'ParentLogo'   => '',
        'Heights' => 3,
        'Grades' => 3,
        'Recorridos' => array('Common course','Standard / Midi + Mini','Separate courses'),
        'ListaGrados'    => array (
            '-' => 'Sin especificar',
            'Baja' => 'Baja temporal',
            'GI' => 'Grado I',
            'GII'=> 'Grado II',
            'GIII' => 'Grado III',
            'P.A.' => 'Pre-Agility',
            'P.B.' => 'Perro en Blanco',
            'Ret.' => 'Retirado',
        ),
        'ListaCategorias' => array (
            '-' => 'Sin especificar',
            'L' => 'Large - Standard - 60',
            'M' => 'Medium - Midi - 50',
            'S' => 'Small - Mini - 40',
            'T' => 'Tiny - Toy - 30'
        ),
        'Modes' => array(array(/* separado */ 0, 1, 2, -1), array(/* mixto */ 0, 3, 3. -1), array(/* conjunto */ 4, 4, 4, -1 )),
        'Puntuaciones' => null // to point to a function to evaluate califications
    );

    public function getConfig() {
        return $this->config;
    }

    /**
     * Translate requested recorrido indexto federation dependent i18n'd one
     * @param {integer} $idx recorrido 0:common 1:mixed 2:separated
     * @return string resulting i18n'd string
     */
    public function getRecorrido($idx) {
        $a= $this->config['Recorridos'][$idx];
        syslog(LOG_ERR,"data: $a i18n: "._($a));
        return _($a);
    }

    /**
     * Translate requested grade key to federation dependent i18n'd one
     * @param {string} $key grade as stored in database
     * @return string resulting i18n'd string
     */
    public function getGrade($key) {
        if (!array_key_exists($key,$this->config['ListaGrados'])) return _($key);
        return _($this->config['ListaGrados'][$key]);
    }

    /**
     * Translate requested category key to federation dependent i18n'd one
     * @param {string} $key category as stored in database
     * @return string resulting i18n'd string
     */
    public function getCategory($key) {
        if (!array_key_exists($key,$this->config['ListaCategorias'])) return _($key);
        return _($this->config['ListaCategorias'][$key]);
    }

    /**
     * Reserve FedID 0..4 to national events; 5..9 to internationals
     * @return bool
     */
    public function isInternational() { return ( $this->config['ID'] >4)?true:false; }

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

    public function getInfoManga($rec) {
        switch ($rec) {
            case 0: return array('L' => _('Large'),         'M' => _('Medium'), 'S' => _('Small'),      'T' => _('Tiny')); // separate courses
            case 1: return array('L' => _('Large+Medium'),  'M' => '',          'S' => _('Small+Tiny'), 'T' => ''); // mixed courses
            case 2: return array('L' => _('Common course'), 'M' => '',          'S' => '',              'T' => ''); // common
            default: return array('errorMsg' => "Invalid recorrido: $rec");
        }
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
            $fedList[$id]=$fed->getConfig();
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
        $result=array('total' => count($data),'rows' => $data);
        return $result;
    }

    /*
     * Retrieve text and visibility info according federation and recorrido
     */

    static function infomanga($fed,$rec) {
        $fed=Federations::getFederation($fed);
        if (!$fed) return array('errorMsg' => 'Invalid federation ID');
        return $fed->getInfoManga($rec);
    }
}
?>