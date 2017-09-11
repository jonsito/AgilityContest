<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 2/04/16
 * Time: 16:20
PartialScoresReader.php

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

require_once(__DIR__ . "/../../logging.php");
require_once(__DIR__ . "/../../tools.php");
require_once(__DIR__ . "/../../auth/Config.php");
require_once(__DIR__ . "/../../auth/AuthManager.php");
require_once(__DIR__ . "/../../modules/Federations.php");
require_once(__DIR__ . "/../../database/classes/DBObject.php");
require_once(__DIR__ . "/../../database/classes/Entrenamientos.php");
require_once(__DIR__ . '/../Spout/Autoloader/autoload.php');
require_once(__DIR__ . '/DogReader.php');

class PartialScoresReader extends DogReader {

    protected $prueba;
    protected $jornada;
    protected $manga;
    protected $equipos;

    public function __construct($name,$options) {
        $this->myDBObject = new DBObject($name);
        $this->prueba=$this->myDBObject->__selectAsArray("*","Pruebas","ID={$options['Prueba']}");
        $this->jornada=$this->myDBObject->__selectAsArray("*","Jornada","ID={$options['Jornada']}");
        $this->manga=$this->myDBObject->__selectAsArray("*","Mangas","ID={$options['Manga']}");
        $this->equipos=$this->myDBObject->__selectAsArray("*","Equipos","Jornada={$options['Jornada']}");
        if (!is_array($this->manga)) // realmente prueba y jornada no son necesarias, pero por consistencia se ponen
            throw new Exception("{$name}::construct(): invalid Manga ID: {$options['Manga']}");
        parent::__construct($name,$options);

        // extend default field list
        // name => index, required (1:true 0:false-to-evaluate -1:optional), default
        // add additional fields required to handle inscriptions
        $inscList= array(
            'Games'=>     array (  -17,-1, "i", "Games",    " `Games` int(4) NOT NULL DEFAULT 0, "), // required on games rounds
            'Faults' =>   array (  -18, 1, "i", "Faltas",   " `Faltas` int(4) NOT NULL DEFAULT 0, "), // faltas, requerido
            'Touchs' =>   array (  -19,-1, "i", "Tocados",  " `Tocados` int(4) NOT NULL DEFAULT 0, "), // tocados, opcional
            'Refusals' => array (  -20, 1, "i", "Rehuses",  " `Rehuses` int(4) NOT NULL DEFAULT 0, "), // rehuses, requerido
            'Eliminated'=>array (  -21, 1, "i", "Eliminado"," `Eliminado` int(4) NOT NULL DEFAULT 0, "), // eliminado, requerido
            'NotPresent'=>array (  -22, 1, "i", "NoPresentado", " `NoPresentado` int(4) NOT NULL DEFAULT 0, "), // nopresentado, requerido
            'Tiempo' =>   array (  -23, 1, "f", "Tiempo",   " `Tiempo` double NOT NULL DEFAULT 0, "), // tiempo, requerido
            // los campos penalizacion, calificacion, puntos, y estrellas se calculan en runtime, no se importan
        );
        foreach ($inscList as $key => $data) $this->fieldList[$key]=$data;
        // fix fields according contest type
        $fedobj=Federations::getFederation($this->federation);
        if ($fedobj->isInternational()) { $this->fieldList['Club'][1]=0; $this->fieldList['Country'][1]=1; } // country/club
        // on team rounds, make teamname required
        if (isMangaEquipos($this->manga['Tipo'])) $this->fieldList['NombreEquipo'][1]=0;
        // on games rounds, make games required
        if (isMangaGames($this->manga['Tipo'])) $this->fieldList['Games'][1]=1;
        $this->validPageNames=array("Results");
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
        if ( ($item['Licencia']==="") && ($item['Nombre']==="") ){
            // no way to assign result to anyone: remove from temporary table
            $this->myLogger->notice("findAndSetResult(): not enought data to parse row: ".json_encode($item));
            // remove entry from temporary table
            $str="DELETE FROM ".TABLE_NAME." WHERE ID={$item['ID']}";
            $this->myDBObject->conn->query($str);
            return null;
        }
        $l=$this->myDBObject->conn->real_escape_string($item['Licencia']);
        $n=$this->myDBObject->conn->real_escape_string($item['Nombre']);
        $this->saveStatus("Analyzing result entry '$n'");
        $lic= ($l==="")?"": " AND (Licencia='{$l}')";
        $dog= ($n==="")?"":" AND (Nombre='{$n}')";
        $search=$this->myDBObject->__select("*",
            "Resultados",
            "(Manga={$this->manga['ID']}) {$lic} {$dog}",
            "",
            "");
        if ( !is_array($search) ) return "findAndSeResult(): Invalid search term: '{$l} - {$n}' "; // invalid search. mark error
        if ($this->myOptions['Blind']!=0){ // blind mode on
            if($search['total']!==1) {  // not found or multiple posibility: in blind mode ignore(delete) entry
                $str="DELETE FROM ".TABLE_NAME." WHERE ID={$item['ID']}";
                $this->myDBObject->conn->query($str);
                return null;
            }
        }
        if ($search['total']==0) return false; // no search found ask user to select or create
        if ($search['total']>1) return $search; // more than 1 compatible item found. Ask user to choose
        // match found: update results entry with provided data
        $f=" Faltas={$item['Faltas']}";
        $t= (array_key_exists('Tocados',$item))?", Tocados={$item['Tocados']}":"";
        $r=", Rehuses={$item['Rehuses']}";
        $g= (array_key_exists('Games',$item))?", Games={$item['Games']}":"";
        $e=", Eliminado={$item['Eliminado']}";
        $n=", NoPresentado={$item['NoPresentado']}";
        $tim=", Tiempo={$item['Tiempo']}";
        $str="UPDATE Resultados SET {$f} {$t} {$r} {$g} {$e} {$n} {$tim} , Pendiente=0  ".
            "WHERE Manga={$this->manga['ID']} AND Perro={$search['rows'][0]['Perro']}";
        $res=$this->myDBObject->conn->query($str);
        if (!$res) return "findAndSetResult(): update result '{$l} - {$item['Nombre']}' error:".$this->myDBObject->conn->error; // invalid search. mark error
        $this->myLogger->leave();
        return true; // tell parent item found. proceed with next
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
            /* ORDER BY */ "DogID ASC",
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

    function beginImport() {
        $this->myLogger->enter();
        // PENDING: si se han definido, se guardan los datos de la manga
        // ahora guardamos los datos de resultados
        $from=$this->myDBObject->__select(
          "*",
          TABLE_NAME,
          "( DogID != 0 )",
          "DogID ASC",
          ""
        );
        $mid=$this->manga['ID'];
        $is_ko=isMangaKO($this->manga['Tipo']);
        $resobj=Competitions::getResultadosInstance("update round:{$mid} on journey:{$this->jornada['ID']}",$mid);
        foreach ($from['rows'] as $resultado) {
            $resultado['Pendiente']=0;
            if ($is_ko) $resultado['Games']=1;
            $resobj->real_update($resultado['DogID'],$resultado);
        }
        $this->myLogger->leave();
        return array( 'operation'=>'import','success'=>'close');
    }
}
?>