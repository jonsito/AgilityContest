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

class OrdenSalidaReader extends DogReader {

    protected $prueba;
    protected $jornada;
    protected $manga;
    protected $equipos;
    protected $sqlcats="";
    protected $heights;

    /**
     * OrdenSalidaReader constructor.
     * @param $name dboject name
     * @param $options prueba,jornada, manda id's
     * @throws Exception on invalid data
     */
    public function __construct($name,$options) {
        $this->myDBObject = new DBObject($name);
        $this->prueba=$this->myDBObject->__selectAsArray("*","pruebas","ID={$options['Prueba']}");
        $this->jornada=$this->myDBObject->__selectAsArray("*","jornada","ID={$options['Jornada']}");
        $this->manga=$this->myDBObject->__selectAsArray("*","mangas","ID={$options['Manga']}");
        $this->equipos=$this->myDBObject->__selectAsArray("*","equipos","Jornada={$options['Jornada']}");
        if (!is_array($this->manga))
            throw new Exception("{$name}::construct(): invalid Manga ID: {$options['Manga']}");
        if (intval($this->jornada['Cerrada'])!==0) // do not allow import in closed journeys
            throw new Exception("{$name}::construct(): Cannot import when in a closed journey: {$options['Jornada']}");
        parent::__construct($name,$options);

        // extend default field list
        // name => index, required (1:true 0:false-to-evaluate -1:optional), default
        // PENDING: future implementation will take care on "Order" value.
        // in the meanwhile, just set orden to be just excel rows order
        $inscList= array(
            'Order'=>     array (  -18,-1, "i", "Orden",    " `Orden` int(4) NOT NULL DEFAULT 0, "),
            'Dorsal'=>    array (  -19, 1, "i", "Dorsal",    " `Dorsal` int(4) NOT NULL DEFAULT 0, ")
        );
        foreach ($inscList as $key => $data) $this->fieldList[$key]=$data;
        // fix fields according contest type
        $fedobj=Federations::getFederation($this->federation);
        if ($fedobj->isInternational()) { $this->fieldList['Club'][1]=0; $this->fieldList['Country'][1]=1; } // country/club
        $this->validPageNames=array("StartingOrder",_("StartingOrder"),"Starting order",_("Starting order"));
        $this->heights=Competitions::getHeights($this->prueba['ID'],$this->jornada['ID'],$this->manga['ID']);
        $this->sqlcats=sqlFilterCategoryByMode(intval($this->myOptions['Mode']), $this->heights,"resultados.");
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
     * So we need to find dog ID in round results table by mean of search for dorsal and name
     * 2018-01-21: changed License search to Dorsal, to avoid unambiguities, as dorsal is allways unique
     *
     * When cannot decide dogID return instructions to call user for proper action ( create, select,or ignore )
     * PENDING: create should not be an option, but is returned by
     */
    protected function findAndSetEntry($item) {
        $this->myLogger->enter();
        if ( ($item['Dorsal']==0) && ($item['Nombre']==="") && ($item['NombreLargo']==="") ){
            // no way to assign result to anyone: remove from temporary table
            $this->myLogger->notice("findAndSetEntry(): no data to parse row: ".json_encode($item));
            return $this->removeTmpEntry($item); // returns null
        }
        $d=intval($item['Dorsal']);
        $n=$this->myDBObject->conn->real_escape_string($item['Nombre']);
        $nl=$this->myDBObject->conn->real_escape_string($item['NombreLargo']);
        if (! category_match($item['Categoria'],$this->heihgts,$this->myOptions['Mode'])) {
            $this->myLogger->info("findAndSetEntry(): not matching category: ".json_encode($item));
            $this->saveStatus("Skip category missmatch entry {$n} found: {$item['Categoria']} expected:{$this->myOptions['Mode']}");
            return $this->removeTmpEntry($item); // returns null
        }
        $this->saveStatus("Analyzing result entry '$n'");
        $dorsal= ($d===0)?" 1": " (resultados.Dorsal='{$d}')"; // por si acaso el dorsal esta en blanco, pero no deberia
        $ldog= ($nl==="")?" 0": " (perros.NombreLargo='{$nl}')"; // nombrelargo no existe en tabla "resultados"
        $dog= ($n==="")?" 0":" (resultados.Nombre='{$n}')";
        $search=$this->myDBObject->__select("resultados.*,perros.ID,perros.NombreLargo",
            "resultados,perros",
            "(resultados.Perro = perros.ID) AND (manga={$this->manga['ID']}) {$this->sqlcats} AND {$dorsal} AND ( {$dog} OR {$ldog} )",
            "",
            "");
        if ( !is_array($search) ) return "findAndSetOrdenSalida(): Invalid search term: '{$d} - {$n}' "; // invalid search. mark error
        // if blind mode and cannot decide, just ignore and remove entry from tmptable
        $this->myLogger->trace("Blind: {$this->myOptions['Blind']} Search {$n} results:".json_encode($search));
        if ( ($search['total']!==1) && ($this->myOptions['Blind']!=0)) return $this->removeTmpEntry($item); // returns null
        if ($search['total']==0) return false; // no search found: ask user to select or ignore
        if ($search['total']>1) return $search; // more than 1 compatible item found. Ask user to choose

        // match found: store found DogIDinto temporary table.
        $dogID=$search['rows'][0]['Perro'];
        $str="UPDATE ".TABLE_NAME." SET DogID={$dogID} WHERE ID={$item['ID']}";
        $res=$this->myDBObject->query($str);
        if (!$res) return "findAndSetEntry(): update tmptable '{$d} - {$item['Nombre']}' error:".$this->myDBObject->conn->error;

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
            /* ORDER BY */ "ID ASC",
            /* LIMIT */  ""
        );
        foreach ($res['rows'] as $item ) {
            $found=$this->findAndSetEntry($item); // overriden to just find entry in results database
            if (is_null($found)) continue; // cannot detect whose results belongs to: ignore and continue
            if (is_string($found)) throw new Exception("import parse: $found");
            if (is_bool($found)) {
                if ($found===true) // item found and match: notify and return
                    return array('operation'=> 'parse', 'success'=> 'ok', 'search' => $item, 'found' => array());
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
        // Just simple: store DatabaseID into temporary table to mark row entry done in temporary table.
        $str="UPDATE ".TABLE_NAME." SET DogID={$options['DatabaseID']} WHERE ID={$options['ExcelID']}";
        $res=$this->myDBObject->query($str);
        if (!$res) return "updateEntry(): update tmptable for perro '{$options['DatabaseID']}' error:".$this->myDBObject->conn->error;

        // return success to proceed with next
        $this->myLogger->leave();
        return array('operation'=> 'update', 'success'=> 'done');
    }

    /*
     * cuando llega hasta aquí, tenemos todos los perros del excel identificados
     * con el ID de la base de datos.
     * Lo que vamos a hacer es coger los perros por orden e irlos insertando en el orden
     * de salida SI existen en dicho orden. De lo contrario aviso e ignoramos
     *
     * PENDIENTE:
     * - filtrar por categorias segun el cuadro de dialogo
     * - Ajustar el orden de equipos segun se han ido guardando en la variable de sesion
     */
    function beginImport() {
        $this->myLogger->enter();
        // retrieve al dogs from temporary table in insertion order
        $order= ($this->myOptions['ParseCourseData']===0)?"ID ASC":"Orden ASC, ID ASC";
        $res=$this->myDBObject->__select("DogID,Categoria",TABLE_NAME,"(DogID!=0)",$order );
        $ordensalida=$this->manga['Orden_Salida'];
        foreach ($res['rows'] as $entry) {
            $perro = $entry['DogID'];
            // insertamos al final
            if (strstr($ordensalida, $perro) !== FALSE) $ordensalida = list_insert($perro, list_remove($perro, $ordensalida));
            else $this->myLogger->error("SetOrdenSalida: dog {$perro} is not present in Orden:{$this->manga['Orden_Salida']}");
        }
        // finally update ordensalida
        $str="UPDATE mangas SET Orden_Salida='{$ordensalida}' WHERE ID={$this->manga['ID']}";
        $res=$this->myDBObject->query($str);
        if (!$res) return "beginImport(): update OrdenSalida error:".$this->myDBObject->conn->error;
        // that's all folks
        $this->myLogger->leave();
        return array( 'operation'=>'import','success'=>'close');
    }
}
?>