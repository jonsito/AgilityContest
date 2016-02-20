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
        // use basename http absolute path for icons, as need to be used in client side
        'OrganizerLogo' => '',  // contest organizer logo
        'Logo'     => '',       // local federation logo
        'ParentLogo'   => '',   // global federation logo
        'WebURL' => '',
        'ParentWebURL' => '',
        'Heights' => 3,
        'Grades' => 3,
        'International' => 0,
        'WideLicense' => false, // some federations need extra print space to show license ID
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
        'InfoManga' => array (
            array('L' => 'Large',         'M' => 'Medium', 'S' => 'Small',      'T' => 'Tiny'), // separate courses
            array('L' => 'Large+Medium',  'M' => '',       'S' => 'Small+Tiny', 'T' => ''), // mixed courses
            array('L' => 'Common course', 'M' => '',       'S' => '',           'T' => '') // common
        ),
        'Modes' => array(array(/* separado */ 0, 1, 2, -1), array(/* mixto */ 0, 3, 3, -1), array(/* conjunto */ 4, 4, 4, -1 )),
        'ModeStrings' => array( // text to be shown on each category
            array(/* separado */ "Large", "Medium", "Small", "Invalid"),
            array(/* mixto */ "Large", "Medium+Small", "Medium+Small", "Invalid"),
            array(/* conjunto */ "Common course", "Common course", "Common course", "Invalid")
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
            "MS"=>"Medium/Small",
            "LMS" => 'Common LMS',
            "LMST",'Common LMST'
        )
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
    public function isInternational() { return ( intval($this->config['International']) !=0)?true:false; }

    /**
     * @return string either i18n'd 'Club' or 'Contry' according federation
     */
    public function getClubString() {
        return $this->isInternational()?_('Club'):_('Country');
    }

    /**
     * Generic data getter
     * @param {string} $key field to retrive
     * @return {object} requested object or null if not found
     */
    public function get($key) {
        if (array_key_exists($key,$this->config)) return $this->config[$key];
        return null;
    }

    /**
     * Retrieve text to be shown according course mode/category
     * @param {integer} 0:separate 1:mixed 2:common course
     * @return array requested data or error message
     */
    public function getInfoManga($rec) {
        if (!array_key_exists(intval($rec),$this->config->InfoManga) ) return array('errorMsg' => "Invalid recorrido: $rec");
        return $this->config->InfoManga[$rec];
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
        if ($perro['Penalizacion']>=200)  {
            $perro['Penalizacion']=200.0;
            $perro['Calificacion'] = _("Not Present");
            $perro['CShort'] = _("N.P.");
        }
        else if ($perro['Penalizacion']>=100) {
            $perro['Penalizacion']=100.0;
            $perro['Calificacion'] = _("Eliminated");
            $perro['CShort'] = _("Elim");
        }
        else if ($perro['Penalizacion']>=26)	{
            $perro['Calificacion'] = _("Not Clasified");
            $perro['CShort'] = _("N.C.");
        }
        else if ($perro['Penalizacion']>=16)	{
            $perro['Calificacion'] = _("Good");
            $perro['CShort'] = _("Good");
        }
        else if ($perro['Penalizacion']>=6)	{
            $perro['Calificacion'] = _("Very good");
            $perro['CShort'] = _("V.G.");
        }
        else if ($perro['Penalizacion']>0)	{
            $perro['Calificacion'] = _("Excellent");
            $perro['CShort'] = _("Exc");
        }
        else if ($perro['Penalizacion']==0)	{
            $perro['Calificacion'] = _("Excellent (p)");
            $perro['CShort'] = _("Ex P");
        }
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
        return; // should be overriden
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

    /**
     * Parse federations and compose bitmap mask on every international feds
     * @param $fed
     * @return int
     */
    static function getInternationalMask() {
        $list=Federations::getFederationList();
        $data=0;
        foreach ($list as $fed) {
            if(intval($fed['International'])==1) $data |= (1<< intval($fed['ID']));
        }
        return $data;
    }

    /*
     * Retrieve text and visibility info according federation and recorrido
     */
    static function infomanga($fed,$rec) {
        $fed=Federations::getFederation($fed);
        if (!$fed) return array('errorMsg' => 'Invalid federation ID');
        return $fed->getInfoManga($rec);
    }

    /**
     * Check if provided logo name matches with existing one
     * @param {string} $name logo to seach
     * @return {boolean} true or false
     */
    static function logoMatches($name) {
        $name=basename($name); // stip dir info
        $list=Federations::getFederationList();
        foreach ($list as $fed) {
            if (basename($fed['Logo'])===$name) return true;
            if (basename($fed['ParentLogo'])===$name) return true;
        }
        // arriving here means not found
        return false;
    }
}
?>