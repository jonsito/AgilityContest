<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 2/04/16
 * Time: 16:20
PartialScoresReader.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require_once(__DIR__ . "/../../logging.php");
require_once(__DIR__ . "/../../tools.php");
require_once(__DIR__ . "/../../auth/Config.php");
require_once(__DIR__ . "/../../auth/AuthManager.php");
require_once(__DIR__ . "/../../modules/Federations.php");
require_once(__DIR__ . "/../../database/classes/DBObject.php");
require_once(__DIR__ . '/../Spout/Autoloader/autoload.php');
require_once(__DIR__ . '/DogReader.php');

class PartialScoresReader extends DogReader {

    protected $prueba;
    protected $jornada;
    protected $manga;
    protected $equipos;
    protected $sqlcats="";
    protected $heights;

    public function __construct($name,$options) {
        $this->myDBObject = new DBObject($name);
        $this->prueba=$this->myDBObject->__selectAsArray("*","pruebas","ID={$options['Prueba']}");
        $this->jornada=$this->myDBObject->__selectAsArray("*","jornada","ID={$options['Jornada']}");
        $this->manga=$this->myDBObject->__selectAsArray("*","mangas","ID={$options['Manga']}");
        $this->equipos=$this->myDBObject->__selectAsArray("*","equipos","Jornada={$options['Jornada']}");
        if (!is_array($this->manga)) // realmente prueba no es necesaria, pero por consistencia se pone
            throw new Exception("{$name}::construct(): invalid Manga ID: {$options['Manga']}");
        if (intval($this->jornada['Cerrada'])!==0) // do not allow import in closed journeys
            throw new Exception("{$name}::construct(): Cannot import when in a closed journey: {$options['Jornada']}");
        parent::__construct($name,$options);

        // extend default field list
        // name => index, required (1:true 0:false-to-evaluate -1:optional), default
        // add additional fields required to handle inscriptions
        $inscList= array(
            'Games'=>     array (  -18,-1, "i", "Games",    " `Games` int(4) NOT NULL DEFAULT 0, "), // required on games rounds
            'Faults' =>   array (  -19, 1, "i", "Faltas",   " `Faltas` int(4) NOT NULL DEFAULT 0, "), // faltas, requerido
            'Touchs' =>   array (  -20,-1, "i", "Tocados",  " `Tocados` int(4) NOT NULL DEFAULT 0, "), // tocados, opcional
            'Refusals' => array (  -21, 1, "i", "Rehuses",  " `Rehuses` int(4) NOT NULL DEFAULT 0, "), // rehuses, requerido
            'Eliminated'=>array (  -22, 1, "i", "Eliminado"," `Eliminado` int(4) NOT NULL DEFAULT 0, "), // eliminado, requerido
            'NotPresent'=>array (  -23, 1, "i", "NoPresentado", " `NoPresentado` int(4) NOT NULL DEFAULT 0, "), // nopresentado, requerido
            'Tiempo' =>   array (  -24, 1, "f", "Tiempo",   " `Tiempo` double NOT NULL DEFAULT 0, "), // tiempo, requerido
            // los campos penalizacion, calificacion, puntos, y estrellas se calculan en runtime, no se importan
        );
        foreach ($inscList as $key => $data) $this->fieldList[$key]=$data;
        // on games rounds, make games required
        if (isMangaGames($this->manga['Tipo'])) $this->fieldList['Games'][1]=1;
        $this->validPageNames=array("Results");
        $this->heights=Competitions::getHeights($options['Prueba'],$options['Jornada'],$options['Manga']);
        $this->sqlcats=sqlFilterCategoryByMode(intval($this->myOptions['Mode']),$this->heights,"resultados.");
    }

    private function removeTmpEntry($item) {
        $id=(is_array($item))?$item['ID']:intval($item);
        // remove entry from temporary table
        $this->myDBObject->__delete(TABLE_NAME,"ID={$id}");
        return null;
    }

    /*
     * In results import, no need to handle Team,Club/Country nor handler: they must be already declared
     * in inscriptions.
     * So we need to find dog ID in round results table by mean of search for license and name
     *
     * Notice that license may be empty or refer to several dogs.
     * When cannot decide dogID return instructions to call user for proper action ( create, select,or ignore )
     * PENDING: create should not be an option, but is returned by
     */
    protected function findAndSetResult($item) {
        $this->myLogger->enter();
        if ( ($item['Licencia']==="") && ($item['Nombre']==="") && ($item['NombreLargo']==="") ){
            // no way to assign result to anyone: remove from temporary table
            $this->myLogger->notice("findAndSetResult(): no data to parse row: ".json_encode($item));
            return $this->removeTmpEntry($item); // returns null
        }
        if ( ($this->myOptions['IgnoreNotPresent']==1) && ($item['NoPresentado']==1) ) {
            $this->myLogger->info("findAndSetResult(): ignore 'Not Present' row: ".json_encode($item));
            $this->saveStatus("Ignore 'Not Present' entry: {$item['Nombre']}");
            return $this->removeTmpEntry($item); // returns null
        };
        $l=$this->myDBObject->conn->real_escape_string($item['Licencia']);
        $n=$this->myDBObject->conn->real_escape_string($item['Nombre']);
        $nl=$this->myDBObject->conn->real_escape_string($item['NombreLargo']);
        if (! category_match($item['Categoria'],$this->heights,$this->myOptions['Mode'])) {
            $this->myLogger->info("findAndSetResult(): not matching category: ".json_encode($item));
            $this->saveStatus("Ignore entry with non-matching category: {$n} {$item['Categoria']}");
            return $this->removeTmpEntry($item); // returns null
        }
        $this->saveStatus("Analyzing result entry '$n'");
        $lic= ($l==="")?" 1": " (resultados.Licencia='{$l}')"; // en rsce a veces no hay licencia
        $ldog= ($nl==="")?" 0": " (perros.NombreLargo='{$nl}')"; // nombrelargo no esta en tabla "resultados"
        $dog= ($n==="")?" 0":" (resultados.Nombre='{$n}')"; // debe existir nombre o nombrelargo en el excel
        $search=$this->myDBObject->__select(
            "resultados.*,perros.NombreLargo",
            "resultados,perros",
            "(Manga={$this->manga['ID']}) AND (resultados.Perro=perros.ID) {$this->sqlcats} AND {$lic} AND ( {$dog} OR {$ldog} )",
            "",
            "");
        if ( !is_array($search) ) return "findAndSeResult(): Invalid search term: '{$l} - {$n}' "; // invalid search. mark error
        // if blind mode and cannot decide, just ignore and remove entry from tmptable
        $this->myLogger->trace("Blind: {$this->myOptions['Blind']} Search {$n} results:".json_encode($search));
        if ( ($search['total']!==1) && ($this->myOptions['Blind']!=0)) return $this->removeTmpEntry($item); // returns null
        if ($search['total']==0) return false; // no search found: ask user to select or ignore
        if ($search['total']>1) return $search; // more than 1 compatible item found. Ask user to choose

        // match found: update results entry with provided data
        $dogID=$search['rows'][0]['Perro'];
        $f=" Faltas={$item['Faltas']}";
        $t= (array_key_exists('Tocados',$item))?", Tocados={$item['Tocados']}":"";
        $r=", Rehuses={$item['Rehuses']}";
        $g= (array_key_exists('Games',$item))?", Games={$item['Games']}":"";
        $e=", Eliminado={$item['Eliminado']}";
        $n=", NoPresentado={$item['NoPresentado']}";
        $tim=", Tiempo={$item['Tiempo']}";
        $str="UPDATE resultados SET {$f} {$t} {$r} {$g} {$e} {$n} {$tim} , Pendiente=0  ".
            "WHERE Manga={$this->manga['ID']} AND Perro={$dogID}";
        $res=$this->myDBObject->query($str);
        if (!$res) return "findAndSetResult(): update result '{$l} - {$item['Nombre']}' error:".$this->myDBObject->conn->error;

        // mark entry done in temporary table.
        $str="UPDATE ".TABLE_NAME." SET DogID={$dogID} WHERE ID={$item['ID']}";
        $res=$this->myDBObject->query($str);
        if (!$res) return "findAndSetResult(): update tmptable '{$l} - {$item['Nombre']}' error:".$this->myDBObject->conn->error;

        // return true to notify caller item found. proceed with next
        $this->myLogger->leave();
        return true;
    }

    /**
     * Parse a result entry looking for dog by mean of search license and name
     * @return {array} data to be evaluated
     */
    public function parse() {
        $this->myLogger->enter();
        $res=$this->myDBObject->__select(
        /* SELECT */ "*",
            /* FROM   */ TABLE_NAME,
            /* WHERE  */ "( DogID = 0 )",
            /* ORDER BY */ "",
            /* LIMIT */  ""
        );
        foreach ($res['rows'] as $item ) {
            $found=$this->findAndSetResult($item); // overriden to just find entry in results database
            if (is_null($found)) continue; // cannot detect whose results belongs to: ignore and continue
            if (is_string($found)) throw new Exception("import parse: $found");
            if (is_bool($found)) {
                if ($found===true) // item found and match: notify and return
                    return array('operation'=> 'parse', 'success'=> 'ok', 'search' => $item, 'found' => $found['rows']);
                else // item not found: create a default item
                    return array('operation'=> 'parse', 'success'=> 'fail', 'search' => $item, 'found' => array());
            }
            // nultiple matching items found: ask
            return array('operation'=> 'parse', 'success'=> 'fail', 'search' => $item, 'found' => $found['rows']);
        }
        // arriving here means no more items to analyze. So tell user to proccedd with import
        $this->myLogger->leave();
        return array('operation'=> 'parse', 'success'=> 'done');
    }

    // just remove temporary table entry with provided ID
    public function ignoreEntry($options) {
        $this->myLogger->enter();
        $this->removeTmpEntry($options['ExcelID']);
        // tell client to continue parse
        $this->myLogger->leave();
        return array('operation'=> 'ignore', 'success'=> 'done');
    }

    public function updateEntry($options) {
        $this->myLogger->enter();
        $perro=$options['DatabaseID']; // results has no ID key, but manga-perro key
        $item=$this->myDBObject->__selectAsArray("*",TABLE_NAME,"ID={$options['ExcelID']}");
        if (!$item) return "UpdateEntry(): cannot locate tmpdata for perro: '$perro':".$this->myDBObject->conn->error;

        // use resultados::update to handle journey dependency and reuse code
        $data=array(
            'Faltas'    =>  $item['Faltas'],
            'Tocados'   => (array_key_exists('Tocados',$item))?$item['Tocados']:0,
            'Rehuses'   => $item['Rehuses'],
            'Games'     => (isMangaKO($this->manga['Tipo']) )? 1:(array_key_exists('Tocados',$item))?$item['Tocados']:0,
            'Eliminado' => $item['Eliminado'],
            'NoPresentado' => $item['NoPresentado'],
            'Tiempo'    => $item['Tiempo'],
            'Pendiente' => 0
        );
        $r=Competitions::getResultadosInstance('ImportResults',$this->manga['ID']);
        $res=$r->real_update($perro,$data);
        if (!$res) return "updateEntry(): update db result for perro '{$perro}' error:".$r->conn->error;

        // mark entry done in temporary table.
        $str="UPDATE ".TABLE_NAME." SET DogID={$perro} WHERE ID={$item['ID']}";
        $res=$this->myDBObject->query($str);
        if (!$res) return "updateEntry(): update tmptable for perro '{$perro}' error:".$this->myDBObject->conn->error;

        // return success to proceed with next
        $this->myLogger->leave();
        return array('operation'=> 'update', 'success'=> 'done');
    }

    function beginImport() {
        $this->myLogger->enter();
        // PENDING: si se han definido, se guardan los datos de la manga
        if ($this->myOptions['ParseCourseData']==0) {
            $this->saveStatus("Skip parse course data");
            $this->myLogger->info("Course data import cancelled by user");
            return array( 'operation'=>'import','success'=>'close');
        }
        $this->saveStatus("Parsing course data if available");
        // evaluate categories to store round information into
        switch($this->myOptions['Mode']) {
            case 0: $items=array('L'); break;
            case 1: $items=array('M'); break;
            case 2: $items=array('S'); break;
            case 3: $items=array('M','S'); break;
            case 4: $items=array('L','M','S'); break;
            case 5: $items=array('T'); break;
            case 6: $items=array('L','M'); break;
            case 7: $items=array('S','T'); break;
            case 8: $items=array('L','M','S','T'); break;
            default: $this->myLogger->error("Import Round Data error: invalid mode: {$this->myOptions['Mode']} ");
            return array( 'operation'=>'import','success'=>'close');
        }
        $vars= $this->loadExcelVars();
        $str="UPDATE mangas SET";
        foreach ($vars as $key => $value) {
            if ( ($key==='Dist') || ($key===_('Dist')) ) {
                $value=intval($value); // trick: notice that "XX mts" is evaluated as XX
                foreach($items as $cat){ $str .= " Dist_{$cat}={$value}, "; }
            } else if ( ($key==='Obst') || ($key===_('Obst')) ) {
                $value=intval($value);
                foreach($items as $cat){ $str .= " Obst_{$cat}={$value}, "; }
            } else if ( ($key==='SCT') || ($key===_('SCT')) ) { // force trs and trm to be as fixed value (seconds)
                $value=floatval($value);
                foreach($items as $cat) $str .= " TRS_{$cat}_Tipo=0, TRS_{$cat}_Factor={$value}, TRS_{$cat}_Unit='s', ";
            } else if ( ($key==='MCT') || ($key===_('MCT')) ) {
                $value=floatval($value);
                foreach($items as $cat)$str .= " TRM_{$cat}_Tipo=0, TRM_{$cat}_Factor={$value}, TRM_{$cat}_Unit='s', ";
            } else {
                $this->myLogger->info("Skip current excel variable: {$key}");
            }
        }
        $str .= " Observaciones='Excel Imported' WHERE ID={$this->manga['ID']}";
        $res=$this->myDBObject->query($str);
        if (!$res) $this->myLogger->error($this->myDBObject->conn->error);
        // PENDING: importar jueces

        $this->myLogger->leave();
        return array( 'operation'=>'import','success'=>'close');
    }
}
?>