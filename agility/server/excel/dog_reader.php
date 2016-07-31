<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 2/04/16
 * Time: 16:20
dog_reader.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../auth/Config.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/../../modules/Federations.php");
require_once(__DIR__."/../database/classes/DBObject.php");
require_once __DIR__.'/Spout/Autoloader/autoload.php';
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

define ('IMPORT_LOG',__DIR__."/../../../logs/import.log");
define ('TABLE_NAME',"ImportData"); // name of temporary table to store excel file data into

class DogReader {

    public $errormsg;
    protected $myLogger;
    protected $federation;
    protected $blindMode;
    protected $myAuthMgr;
    protected $tablename;
    protected $fieldList;
    protected $myDBObject;

    public function __construct($fed,$blind=0) {
        $this->federation = Federations::getFederation($fed);
        $this->blindMode = intval($blind);
        $this->myConfig=Config::getInstance();
        $this->myLogger= new Logger("importExcel(dogs)",$this->myConfig->getEnv("debug_level"));
        if (php_sapi_name()!="cli") {
            $this->myAuthMgr= new AuthManager("importExcel(dogs)");
            if (! $this->myAuthMgr->allowed(ENABLE_IMPORT) )
                throw new Exception ("ImportExcel(dogs): Feature disabled: program not registered");
        }
        $this->tablename= TABLE_NAME;
        $this->myDBObject= new DBObject("ImportExcel(dogs)");
        $this->fieldList=array (
            // name => index, required (1:true 0:false-to-evaluate -1:optional), default
            // dog related data
            // 'ID' =>      array (  -1,  0,  "i", "ID",        " `ID` int(4) UNIQUE NOT NULL, "), // automatically added
            'DogID' =>      array (  -2,  0,  "i", "DogID",     " `DogID` int(4) NOT NULL DEFAULT 0, "), // to be filled by importer
            'Name'   =>      array (  -3,  1,  "s","Nombre",    " `Nombre` varchar(255) NOT NULL, "), // Dog name
            'LongName' =>   array (  -4,  -1, "s", "NombreLargo","`NombreLargo` varchar(255) DEFAULT NULL, "), // dog pedigree long name
            'Gender' =>     array (  -5,  -1, "s", "Genero",    " `Genero` varchar(16) DEFAULT NULL, "), // M, F, Male/Female
            'Breed' =>      array (  -6,  -1, "s", "Raza",      " `Raza` varchar(255) DEFAULT NULL, "), // dog breed, optional
            'License' =>    array (  -7,  -1, "s", "Licencia",  " `Licencia` varchar(255) DEFAULT '--------', "), // dog license. required for A2-A3; else optional
            'KC_ID' =>      array (  -8,  -1, "s", "LOE_RRC",   " `LOE_RRC` varchar(255) DEFAULT NULL, "), // LOE_RRC kennel club dog id
            'Category' =>   array (  -9,   1, "s", "Categoria", " `Categoria` varchar(1) NOT NULL DEFAULT '-', "), // required
            'Grade' =>       array (  -10,  1, "s", "Grado",     " `Grado` varchar(16) DEFAULT '-', "), // required
            // handler related data
            'HandlerID' =>  array (  -11,  0, "i", "HandlerID", " `HandlerID` int(4) NOT NULL DEFAULT 0, "),  // to be evaluated by importer
            'Handler' =>    array (  -12,  1, "s", "NombreGuia"," `NombreGuia` varchar(255) NOT NULL, "), // Handler's name. Required
            // club related data
            'ClubID' =>     array (  -13,  0, "i", "ClubID",    " `ClubID` int(4) NOT NULL DEFAULT 0, "),  // to be evaluated by importer
            'Club' =>       array (  -14,  1, "s", "NombreClub"," `NombreClub` varchar(255) NOT NULL,")  // Club's Name. required
        );
    }

    public function saveStatus($str){
        $f=fopen(IMPORT_LOG,"a"); // open for append-only
        if (!$f) {
            $this->myLogger->error("fopen() cannot create file: ".IMPORT_LOG);
            return;
        }
        fwrite($f,"$str\n");
        fclose($f);
    }

    public function retrieveExcelFile() {
        // phase 1 retrieve data from browser
        $this->myLogger->enter();
        $this->saveStatus("Loading file...");
        // extraemos los datos de registro
        $data=http_request("Data","s",null);
        if (!$data) return array("errorMsg" => "importExcel(dogs)::download(): No data to import has been received");
        if (!preg_match('/data:([^;]*);base64,(.*)/', $data, $matches)) {
            return array("operation"=>"upload","errorMsg" => "importExcel(dogs)::download() Invalid received data format");
        }
        // mimetype for excel file is be stored at $matches[1]: and should be checked
        // $type=$matches[1]; // 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', or whatever. Not really used
        $this->myLogger->leave();
        $contents= base64_decode( $matches[2] ); // decodes received data
        // phase 2 store it into temporary file
        $tmpfile=tempnam_sfx(__DIR__."/../../../logs","import","xlsx");
        $file=fopen($tmpfile,"wb");
        fwrite($file,$contents);
        fclose($file);
        return array("operation"=>"upload","success"=>true,"filename"=>$tmpfile);
    }

    private function validateHeader($header) {
        $this->saveStatus("Validating header...");
        // search required fields in header and store index when found
        foreach ($this->fieldList as $field =>&$data) {
            for($index=0; $index<count($header); $index++) {
                $fieldName=$header[$index];
                if ( ($fieldName==$field) || ($fieldName==_utf($field)) ) {
                    $data[0]=$index;
                    break;
                }
            }
        }
        // now check for required but not declared fields
        foreach ($this->fieldList as $key =>$val) {
            if ( ($val[0]<0) && ($val[1]>0) )
                throw new Exception ("ExcelImport(dogs)::required field '$key' => ".json_encode($val)." not found in Excel header");
        }
        return 0;
    }

    private function createTemporaryTable() {
        $this->saveStatus("Creating temporary table...");
        // To create database we need root DB access
        $rconn=DBConnection::getRootConnection();
        if ($rconn->connect_error)
            throw new Exception("Cannot perform import process: database::dbConnect()");
        // $str="DELETE FROM TABLE {$this->tablename} IF EXISTS";
        $str="CREATE TABLE {$this->tablename} (";
        foreach ($this->fieldList as $key => $val) {
            if ( ($val[1]!=0) && ($val[0]<0) ) continue; // field neither required nor provided
            $str .=$val[4];
        }
        $str .=" ID int(4) UNIQUE NOT NULL "; // to get an unique id in database
        $str .=");";
        $res=$rconn->query($str);
        if (!$res) {
            $error=$rconn->error;
            $str="ImportExcel(dogs)::createTemporaryTable(): Error creating temporary table: '$error'";
            $this->myLogger->error($str);
            throw new Exception($str);
        }
        return 0;
    }

    private function storeRow($index,$row) {
        // compose insert sequence
        $str1= "INSERT INTO {$this->tablename} (";
        $str2= "ID ) VALUES (";
        // for each row evaluate field name and get content from provided row
        // notice that
        foreach ($this->fieldList as $key => $val) {
            if ( ($val[0]<0) || ($val[1]==0)) continue; // field not provided or to be evaluated by importer
            $str1 .= "{$val[3]}, "; // add field name
            $item=$row[$val[0]];
            if ($key==='Grade') $item=parseGrade($item);
            if ($key==='Category') $item=parseCategory($item);
            if ($key==='Gender') $item=parseGender($item);
            switch ($val[2]) {
                case "s": // string
                    $a=mysqli_real_escape_string($this->myDBObject->conn,$item);
                    $str2.="'{$a}', ";
                    break;
                case "i":
                    $a=intval($item);
                    $str2 .= " {$a}, "; // integer
                    break;
                case "b":
                    $a=(toBoolean($item))?1:0;
                    $str2 .= " {$a}, "; // boolean as 1/0
                    break;
                case "f":
                    $a=floatval($item);
                    $str2 .= " {$a}, "; // float
                    break;
                default:
                    // escape to avoid sql injection issues
                    $a=mysqli_real_escape_string($this->myDBObject->conn,$item);
                    $str2 .= " {$a}, ";
            }
        }
        $str ="$str1 $str2 {$index} );"; // compose insert string
        $res=$this->myDBObject->query($str);
        if (!$res) {
            $error=$this->myDBObject->conn->error;
            throw new Exception("ImportExcel(dogs)::populateTable(): Error inserting row $index ".json_encode($row));
        }
        return 0;
    }

    public function dropTable() {
        // To create database we need root DB access
        $rconn=DBConnection::getRootConnection();
        if ($rconn->connect_error)
            throw new Exception("Cannot perform import process: database::dbConnect()");
        $str="DROP Table IF EXISTS {$this->tablename};";
        $res=$rconn->query($str);
        if (!$res) {
            $error=$rconn->error;
            $str="ImportExcel(dogs)::dropTable(): Error deleting temporary table: '$error'";
            $this->myLogger->error($str);
            throw new Exception($str);
        }
        return 0;
    }

    // stupid spout that has no sheet /row count function
    private function sheetCount($reader) {
        $count=0;
        foreach ($reader->getSheetIterator() as $sheet) $count++;
        return $count;
    }

    public function validateFile( $filename,$droptable=true) {
        $this->saveStatus("Validating received file...");
        // unlink(IMPORT_LOG);
        // open temporary file
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($filename);
        // if there are only one sheet assume it is what we are looking for
        if ($this->sheetCount($reader)>1) {
            $sheet=null;
            // else look for a sheet named _("Dogs") or _("Inscriptions")
            foreach ($reader->getSheetIterator() as $sheet) {
                $name = $sheet->getName();
                if ($name=="Dogs" || ($name==_("Dogs")) ) break;
                if ($name=="Inscriptions" || ($name==_("Inscriptions")) ) break;
            }
            // arriving here means "Dogs" page not found
            if ($sheet==null) throw new Exception ("No sheet named 'Dogs' or 'Inscriptions' found in excel file");
        } else {
            // getCurrentSheet() is not available for reader. so dirty trick
            // $sheet=$reader->getCurrentSheet();
            foreach ($reader->getSheetIterator() as $sheet) {
                if ($sheet->getIndex()==0) break;
            }
        }
        // OK: now parse sheet
        $index=0;
        $timeout=ini_get('max_execution_time');
        foreach ($sheet->getRowIterator() as $row) {
            // first line must contain field names
            if ($index==0) {
                // check that every required field is present
                $this->validateHeader($row); // throw exception on fail
                // create temporary table in database to store and analyze Excel data
                if ($droptable) $this->dropTable();
                $this->createTemporaryTable(); // throw exception when an import is already running
                $index++;
                continue; // jump to first row with data
            }
            // dump excel data into temporary database table
            set_time_limit($timeout); // avoid php to be killed on very slow systems
            $this->saveStatus("#$index");
            $this->storeRow($index,$row);
            $index++;
        }
        $this->saveStatus("Read Excel Done.");
        // fine. we can start parsing data in DB database table
        $reader->close();
        unlink($filename); // remove temporary file if no named file provided
        return 0;
    }

    private function findAndSetClub($item) {
        $this->myLogger->enter();

        $a=$item['NombreClub'];
        // TODO: search and handle also club's longnames
        $this->saveStatus("Importing club '$a'");
        if ($this->blindMode==0) $search=$this->myDBObject->__select("*","Clubes","( Nombre LIKE '%$a%')","","");
        else                     $search=$this->myDBObject->__select("*","Clubes","( Nombre = '$a')","","");
        if ( !is_array($search) ) return "findAndSetClub(): Invalid search term: '$a'"; // invalid search. mark error
        if ($search['total']==0) {
            // to create club/country we need aditional info, so cannot auto-create in blind mode
            return false;
        } // no search result; ask user to select or create as new
        if ($search['total']>1) return $search; // more than 1 compatible item found. Ask user to choose
        if ($search['rows'][0]['Federations'] & (1<<$this->federation->get('ID')) == 0 ) return $search; // federation missmatch. ask user to fix
        // arriving here means match found. So replace all instances with found data and return to continue import
        $t=TABLE_NAME;
        $i=$search['rows'][0]['ID']; // Club ID
        $n=$search['rows'][0]['Nombre']; // Club Name
        $this->myLogger->trace("Found club '$a' => Name:$n ID:$i");
        $str="UPDATE $t SET ClubID=$i, NombreClub='$n' WHERE (NombreClub = '$a')";
        $res=$this->myDBObject->query($str);
        if (!$res) return "findAndSetClub(): update club '$a' error:".$this->myDBObject->conn->error; // invalid search. mark error
        return true; // tell parent item found. proceed with next
    }

    private function findAndSetHandler($item) {
        $this->myLogger->enter();
        $t=TABLE_NAME;
        // notice that arriving here means all clubs has been parsed and analyzed
        $a=$item['NombreGuia'];
        $this->saveStatus("Importing handler '$a'");
        $f=$this->federation->get('ID');
        if ($this->blindMode==0)
                $search=$this->myDBObject->__select("*","Guias","( Nombre LIKE '%$a%' ) AND ( Federation = $f ) ","","");
        else    $search=$this->myDBObject->__select("*","Guias","( Nombre = '$a' ) AND ( Federation = $f ) ","","");
        if ( !is_array($search) ) return "findAndSetHandler(): Invalid search term: '$a'"; // invalid search. mark error
        if ($search['total']==0) {
            if ($this->blindMode==0) return false;
            $c=$item['ClubID'];
            // if not found and in blind mode create handler "on the fly"
            $str="INSERT INTO Guias (Nombre,Club,Federation) VALUES ( '$a',$c,$f)";
            $res=$this->myDBObject->query($str);
            if (!$res) return "findAndSetHandler(): blindInsertGuia '$a' error:".$this->myDBObject->conn->error;
            $id=$this->myDBObject->conn->insert_id; // retrieve insertID and update temporary table
            $str="UPDATE $t SET HandlerID=$id WHERE (NombreGuia = '$a')";
            $res=$this->myDBObject->query($str);
            if (!$res) return "findAndSetHandler(): update guia '$a' error:".$this->myDBObject->conn->error; // invalid update; mark error
            return true; // tell parent item found. proceed with next
        } // no search result; ask user to select or create as new
        for ($index=0;$index<$search['total'];$index++) {
            // find right entry. if not found ask user
            if ($search['rows'][$index]['Club']!=$item['ClubID']) continue;
            // arriving here means match found. So replace all instances with found data and return to continue import
            $i=$search['rows'][$index]['ID']; // id del guia
            $n=$search['rows'][$index]['Nombre']; // nombre del guia
            $str="UPDATE $t SET HandlerID=$i, NombreGuia='$n' WHERE (NombreGuia = '$a')"; // exact match
            $res=$this->myDBObject->query($str);
            if (!$res) return "findAndSetHandler(): update guia '$a' error:".$this->myDBObject->conn->error; // invalid update; mark error
            return true; // tell parent item found. proceed with next
        }
        // arriving here means item(s) found, but none compatible. Ask user
        return $search;
    }

    private function findAndSetDog($item) {
        $this->myLogger->enter();
        $t=TABLE_NAME;
        // notice that arriving here means all clubs and handlers has been parsed and analyzed
        // TODO: search and handle also dog's long (pedigree) name
        $a=$item['Nombre'];
        $this->saveStatus("Importing dog '$a'");
        $f=$this->federation->get('ID');
        if ($this->blindMode==0)
             $search=$this->myDBObject->__select("*","Perros","( Nombre LIKE '%$a%') AND ( Federation = $f ) ","","");
        else $search=$this->myDBObject->__select("*","Perros","( Nombre = '$a') AND ( Federation = $f ) ","","");
        if ( !is_array($search) ) return "findAndSetDog(): Invalid search term: '$a'"; // invalid search. mark error
        if ($search['total']==0) {
            if ($this->blindMode==0) return false;  // no search result; ask user to select or create as new
            // if not found and in blind mode create handler "on the fly"
            $h=$item['HandlerID'];
            $c=$item['Categoria'];
            $g=$item['Grado'];
            $r=isset($item['Raza'])?$item['Raza']:"";
            $str="INSERT INTO Perros (Nombre,Guia,Categoria,Grado, Raza,Federation) VALUES ( '$a',$h,'$c','$g','$r',$f)";
            $res=$this->myDBObject->query($str);
            if (!$res) return "findAndSetDog(): blindInsertDog '$a' error:".$this->myDBObject->conn->error;
            $id=$this->myDBObject->conn->insert_id; // retrieve insertID and update temporary table
            $str="UPDATE $t SET HandlerID=$id WHERE (Nombre = '$a')";
            $res=$this->myDBObject->query($str);
            if (!$res) return "findAndSetDog(): update guia '$a' error:".$this->myDBObject->conn->error; // invalid update; mark error
            return true; // tell parent item found. proceed with next
        }
        for ($index=0;$index<$search['total'];$index++) {
            // find right entry. if not found ask user
            if ($search['rows'][$index]['Guia']!=$item['HandlerID']) continue;
            // arriving here means match found. So replace all instances with found data and return to continue import
            $i=$search['rows'][$index]['ID']; // id del guia
            $n=$search['rows'][$index]['Nombre']; // nombre del guia
            $str="UPDATE $t SET DogID=$i, Nombre='$n' WHERE (Nombre LIKE '%$a%')";
            $res=$this->myDBObject->query($str);
            if (!$res) return "findAndSetDog(): update dog '$a' error:".$this->myDBObject->conn->error; // invalid search. mark error
            return true; // tell parent item found. proceed with next
        }
        // arriving here means item(s) found, but none compatible. Ask user
        return $search;
    }

    /**
     * @return {array} data to be evaluated
     */
    public function parse() {
        $res=$this->myDBObject->__select(
            /* SELECT */ "*",
            /* FROM   */ TABLE_NAME,
            /* WHERE  */ "( ClubID = 0) || ( HandlerID = 0 ) || ( DogID = 0 )",
            /* ORDER BY */ "ClubID ASC, HandlerID ASC, DogID ASC",
            /* LIMIT */  ""
        );
        foreach ($res['rows'] as $item ) {
            $found=null;
            // if club==0 try to locate club ID. on fail ask user
            if ($item['ClubID']==0) $found=$this->findAndSetClub($item);
            // if handler== 0 try to locate handler ID. on fail or missmatch ask user
            else if ($item['HandlerID']==0) $found=$this->findAndSetHandler($item);
            // if dog == 0 try to locate dog ID. On fail or misssmatch ask user
            else $found=$this->findAndSetDog($item);
            if (is_string($found)) throw new Exception("import parse: $found");
            if (is_bool($found)) {
                if ($found===true) // item found and match: notify and return
                    return array('operation'=> 'parse', 'success'=> 'ok', 'search' => $item, 'found' => $found['rows']);
                else // item not found: create a default item
                    return array('operation'=> 'parse', 'success'=> 'fail', 'search' => $item, 'found' => array());
            }
            return array('operation'=> 'parse', 'success'=> 'fail', 'search' => $item, 'found' => $found['rows']);
        }
        // arriving here means no more items to analyze. So tell user to proccedd with import
        return array('operation'=> 'parse', 'success'=> 'done');
    }

    public function createEntry() {
        // add a new entry into database
        
        // tell client to continue parse
        return array('operation'=> 'create', 'success'=> 'done');
    }    
    
    public function updateEntry() {
        // update existing entry from database

        // tell client to continue parse
        return array('operation'=> 'update', 'success'=> 'done');
    }

    public function ignoreEntry() {
        // ignore entry from imported table

        // tell client to continue parse
        return array('operation'=> 'ignore', 'success'=> 'done');
    }

    public function beginImport() {
        // start import process
        $this->saveStatus("Begin importing analyzed data");
        // TODO: here comes import process
        return array( 'operation'=>'import','success'=>'ok');
    }

    public function endImport() {
        $this->saveStatus("Done.");
        return array( 'operation'=>'close','success'=>'ok');
    }

    public function cancelImport() {
        // remove temporary files, do not perform import
        return 0;
    }
}


// skip web server parsing when running in local (CommandLineInterpreter - cli ) mode
if (php_sapi_name() != "cli" ) {
    $op=http_request("Operation","s","");
    if ($op==='progress') {
        // retrieve last line of progress file
        $lines=file(IMPORT_LOG,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) {
            echo json_encode( array( 'operation'=>'progress','success'=>'fail', 'status' => "Error reading progress file" ) );
        } else {
            echo json_encode( array( 'operation'=>'progress','success'=>'ok', 'status' => strval($lines[count($lines)-1]) ) );
        }
        return;
    }
    // Consultamos la base de datos
    try {
        // 	Creamos generador de documento
        $fed=http_request("Federation","i",-1);
        $blind=http_request("Blind","i",0);
        if ($fed<0) throw new Exception("dog_reader::ImportExcel(): invalid Federation ID: $fed");

        $dr=new DogReader($fed,$blind);
        $result="";
        switch ($op) {
            case "upload":
                // download excel file from browser
                $result = $dr->retrieveExcelFile();
                break;
            case "check":
                // check that received file matches PerroGuiaClub format
                // and store in temporary database table
                $file=http_request("Filename","s",null);
                $result = $dr->validateFile($file);
                break;
            case "parse":
                // start analysis
                $result = $dr->parse();
                break;
            case "create":
                // a new line has been accepted from user: insert and update temporary excel file
                $result = $dr->createEntry();
                break;            
            case "update":
                // a new line has been accepted from user: insert and update temporary excel file
                $result = $dr->updateEntry();
                break;
            case "ignore":
                // received entry has been refused by user: remove and update temporary excel file
                $result = $dr->ignoreEntry();
                break;
            case "abort":
                // user has cancelled import file: clear and return temporary data
                $result = $dr->cancelImport();
                break;
            case "import":
                // every entries have been corrected and have proper entry ID's: start importing
                $result = $dr->beginImport();
                break;
            case "close":
                // end of import. clear and return;
                $result = $dr->endImport();
                break;
            default: throw new Exception("excel_import(dogs): invalid operation '$op' requested");
        }
        if ($result===null)                     // null on error
            throw new Exception($dr->errormsg);
        if ( ($result==="") || ($result===0) )  // empty or zero on success
            echo json_encode(array('operation'=> $op, 'success'=>'ok'));
        else echo json_encode($result);         // else return data already has been set
    } catch (Exception $e) {
        do_log($e->getMessage());
        echo json_encode(array("operation"=>$op, 'success'=>'fail', 'errorMsg'=>$e->getMessage()));
    }
}
