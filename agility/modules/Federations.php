<?php
/**
 * Federations.php
 *
Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require_once(__DIR__."/../server/database/classes/Tandas.php");
require_once(__DIR__."/../server/database/classes/Mangas.php");

/* for poedit */
$dummy= _('Common course');
$dummy= _('Separate courses');
$dummy= _('LongName');

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
        'RoundsG1' => 2, // on rfec may be 3
        'Recorridos' => array('Common course','Standard / Midi + Mini','Separate courses'),
        'ListaGradosShort' => array(
            '-' => 'Sin especificar',
            'Baja' => 'Baja',
            'GI' => 'GI',
            'GII'=> 'GII',
            'GIII' => 'GIII',
            'P.A.' => 'P.A.',
            'P.B.' => 'P.B',
            'Ret.' => 'Ret.'
            
        ),
        'ListaGrados'    => array (
            '-' => 'Sin especificar',
            'Baja' => 'Baja temporal',
            'GI' => 'Grado I',
            'GII'=> 'Grado II',
            'GIII' => 'Grado III',
            'P.A.' => 'Pre-Agility',
            'P.B.' => 'Perro en Blanco',
            'Ret.' => 'Retirado'
        ),
        'ListaCategoriasShort' => array (
            '-' => '-',
            'L' => 'Large',
            'M' => 'Medium',
            'S' => 'Small',
            'T' => 'Tiny'
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
        ),
        'NombreTandas' => array(
            0	=> '-- Sin especificar --',
            1	=> 'Pre-Agility 1',
            2	=> 'Pre-Agility 2',
            3	=> 'Agility-1 GI Large',
            4	=> 'Agility-1 GI Medium',
            5	=> 'Agility-1 GI Small',
            6	=> 'Agility-2 GI Large',
            7	=> 'Agility-2 GI Medium',
            8	=> 'Agility-2 GI Small',
            9	=> 'Agility GII Large',
            10	=> 'Agility GII Medium',
            11	=> 'Agility GII Small',
            12	=> 'Agility GIII Large',
            13	=> 'Agility GIII Medium',
            14	=> 'Agility GIII Small',
            15	=> 'Agility Large', //  Individual-Open
            16	=> 'Agility Medium',	//  Individual-Open
            17	=> 'Agility Small', //  Individual-Open
            18	=> 'Agility team Large', // team best
            19	=> 'Agility team Medium',// team best
            20	=> 'Agility team Small',	 // team best
            // en jornadas por equipos conjunta tres alturas se mezclan categorias M y S
            21	=> 'Ag. Teams Large',// team combined
            22	=> 'Ag. Teams Med/Small', // team combined
            23	=> 'Jumping GII Large',
            24	=> 'Jumping GII Medium',
            25	=> 'Jumping GII Small',
            26	=> 'Jumping GIII Large',
            27	=> 'Jumping GIII Medium',
            28	=> 'Jumping GIII Small',
            29	=> 'Jumping Large',//  Individual-Open
            30	=> 'Jumping Medium',	//  Individual-Open
            31	=> 'Jumping Small', //  Individual-Open
            32	=> 'Jumping team Large',	// team best
            33	=> 'Jumping team Medium',// team best
            34	=> 'Jumping team Small',	// team best
            // en jornadas por equipos conjunta 3 alturas se mezclan categorias M y S
            35	=> 'Jp. Teams Large',// team combined
            36	=> 'Jp. Teams Med/Small', // team combined
            // en las rondas KO, los perros compiten todos contra todos
            37	=> 'K.O. Round',
            38	=> 'Special Round Large',
            39	=> 'Special Round Medium',
            40	=> 'Special Round Small',

            // "Tiny" support for Pruebas de cuatro alturas
            41	=> 'Agility-1 GI Tiny',
            42	=> 'Agility-2 GI Tiny',
            43	=> 'Agility GII Tiny',
            44	=> 'Agility GIII Tiny',	// no existe
            45	=> 'Agility Tiny', //  Individual-Open
            46	=> 'Agility team Tiny',// team best
            // en equipos4  cuatro alturas  agrupamos por LM y ST
            47	=> 'Ag. teams Large/Medium', // team combined
            48	=> 'Ag. teams Small/Tiny', // team combined
            49	=> 'Jumping GII Tiny',
            50	=> 'Jumping GIII Tiny', // no existe
            51	=> 'Jumping Tiny', //  Individual-Open
            52	=> 'Jumping team Tiny',	 // team best
            53	=> 'Jp. teams Large/Medium',  // team combined
            54	=> 'Jp. teams Small/Tiny',// team combined
            55	=> 'Special round Tiny',
            56	=> 'Agility-3 GI Large',	 // extra rounds for GI RFEC
            57	=> 'Agility-3 GI Medium',
            58	=> 'Agility-3 GI Small',
            59	=> 'Agility-3 GI Tiny'
        ),
        'TipoMangas' => array(
            0 =>	array( 0, 'Nombre Manga largo',	'Grado corto',	'Nombre manga',	'Grado largo',  'IsAgility'),
            1 =>	array( 1, 'Pre-Agility Round 1', 		'P.A.',	'PreAgility 1',	'Pre-Agility',  true),
            2 => 	array( 2, 'Pre-Agility Round 2', 		'P.A.',	'PreAgility 2',	'Pre-Agility',  false),
            3 =>	array( 3, 'Agility Grade I Round 1',	'GI',	'Agility-1 GI',	'Grade I',      true),
            4 => 	array( 4, 'Agility Grade I Round 2',	'GI',	'Agility-2 GI',	'Grade I',      true),
            5 =>	array( 5, 'Agility Grade II', 			'GII',	'Agility GII',	'Grade II',     true),
            6 =>	array( 6, 'Agility Grade III', 			'GIII',	'Agility GIII',	'Grade III',    true),
            7 =>	array( 7, 'Agility', 	        		'-',	'Agility',		'Individual',   true), // Open
            8 =>	array( 8, 'Agility Teams',			    '-',	'Ag. Teams',	'Teams',        true), // team best
            9 =>	array( 9, 'Agility Teams'				,'-',	'Ag. Teams.',	'Teams',        true), // team combined
            10 =>	array( 10,'Jumping Grade II',			'GII',	'Jumping GII',	'Grade II',     false),
            11 =>	array( 11,'Jumping Grade III',			'GIII',	'Jumping GIII',	'Grade III',    false),
            12 =>	array( 12,'Jumping',    				'-',	'Jumping',		'Individual',   false), // Open
            13 =>	array( 13,'Jumping Teams'				,'-',   'Jmp Teams',	'Teams',        false), // team best
            14 =>	array( 14,'Jumping Teams'				,'-',  	'Jmp Teams',	'Teams',        false), // team combined
            15 =>	array( 15,'K.O. Round', 				'-',	'K.O. Round',	'K.O.',         false),
            16 =>	array( 16,'Special Round', 			    '-',	'Special Round','Individual',   true), // special round, no grades
            17 => 	array( 17,'Agility Grade I Round 3',	'GI',	'Agility-3 GI',	'Grade I',      true) // on RFEC special G1 3rd round
        )
    );

    public function getConfig() {
        return $this->config;
    }

    /**
     * Translate requested tanda index to federation dependent i18n'd Tanda Name
     * @param {integer} $idx tanda index 0..45 as declared in Tandas.php
     * @return string resulting i18n'd string
     */
    public function getTandaName($idx) {
        if (!array_key_exists('NombreTandas',$this->config)) return _(Tandas::$tipo_tanda[$idx]['Nombre']);
        if (!array_key_exists($idx,$this->config['NombreTandas'])) return _(Tandas::$tipo_tanda[$idx]['Nombre']);
        return $this->config['NombreTandas'][$idx];
    }

    /**
     * Translate requested manga type and index to federation dependent i18n'd Manga data
     * @param {integer} $type manga type 0..17
     * @param {integer} $idx data index index 0..5 as declared in Mangas.php
     * @return {mixed} requested data
     */
    public function getTipoManga($type,$idx) {
        if (!array_key_exists('TipoMangas',$this->config)) return Mangas::$tipo_manga[$type][$idx];
        if (!array_key_exists($type,$this->config['TipoMangas'])) return Mangas::$tipo_manga[$type][$idx];
        return $this->config['TipoMangas'][$type][$idx];
    }

    /**
     * Translate requested manga mode to federation dependent i18n'd Manga mode data
     * @param {integer} $type manga mode 0..8
     * @return {string} requested data
     */
    public function getMangaMode($mode) {
        if (!array_key_exists('IndexedModes',$this->config)) return Mangas::$manga_modes[$mode][0];
        if (!array_key_exists($mode,$this->config['IndexedModes'])) return Mangas::$manga_modes[$mode][0];
        return $this->config['IndexedModes'][$mode];
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
        return $this->isInternational()?_('Country'):_('Club');
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
     * Search federation data by providing ID/Name
     * @param {int} $id Federation ID
     * @return {object} requested federation or null if not found
     */
    static function getFederation($id) {
        $fedList=array();
        // analize sub-directories looking for matching ID or name
        // Notice that module class name should be the same as uppercase'd module directory name
        foreach( glob(__DIR__.'/federaciones/*',GLOB_ONLYDIR) as $federation) {
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
        foreach( glob(__DIR__.'/federaciones/*',GLOB_ONLYDIR) as $federation) {
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