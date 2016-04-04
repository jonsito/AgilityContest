<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 2/04/16
 * Time: 16:20
dog_reader.php

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

require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../auth/Config.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/../../modules/Federations.php");
require_once(__DIR__."/../database/classes/DBObject.php");
require_once(__DIR__."/../database/classes/Dogs.php");
require_once(__DIR__."/../database/classes/Clubes.php");
require_once(__DIR__."/../database/classes/Guias.php");
require_once __DIR__.'/Spout/Autoloader/autoload.php';
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

define ('IMPORT_LOG',__DIR__."/../../../logs/import.log");
define ('TABLE_NAME',"ImportData"); // name of temporary table to store excel file data into

class DogReader {

    public $errormsg;
    protected $myLogger;
    protected $federation;
    protected $myAuthMgr;
    protected $tmpfile;
    protected $tablename;
    protected $fieldList;
    protected $myDBObject;

    public function __construct($fed) {
        $this->federation = Federations::getFederation($fed);
        $this->myConfig=Config::getInstance();
        $this->myLogger= new Logger("importExcel(dogs)",$this->myConfig->getEnv("debug_level"));
        if (php_sapi_name()!="cli") {
            $this->myAuthMgr= new AuthManager("importExcel(dogs)");
            if (! $this->myAuthMgr->allowed(ENABLE_IMPORT) )
                throw new Exception ("ImportExcel(dogs): Feature disabled: program not registered");
        }
        $this->tmpfile=tempnam_sfx(__DIR__."/../../../logs","import","xlsx");
        $this->tablename= TABLE_NAME;
        $this->myDBObject= new DBObject("ImportExcel(dogs)");
        $this->fieldList=array (
            // name => index, required (1:true 0:false-to-evaluate -1:optional), default
            // dog related data
            'DogID' =>      array (  -2,  0,  "i", "Perro",     " `Perro` int(4) NOT NULL DEFAULT 0, "), // to be filled by importer
            'Name'   =>      array (  -3,  1,  "s","Nombre",    " `Nombre` varchar(255) NOT NULL, "), // Dog name
            'LongName' =>   array (  -4,  -1, "s", "NombreLargo"," `NombreLargo` varchar(255) DEFAULT NULL, "), // dog pedigree long name
            'Gender' =>     array (  -5,  -1, "s", "Genero",    " `Genero` varchar(16) DEFAULT NULL, "), // M, F, Male/Female
            'Breed' =>      array (  -6,  -1, "s", "Raza",      " `Raza` varchar(255) DEFAULT NULL, "), // dog breed, optional
            'License' =>    array (  -7,  -1, "s", "Licencia",  " `Licencia` varchar(255) DEFAULT '--------', "), // dog license. required for A2-A3; else optional
            'KC_ID' =>      array (  -8,  -1, "s", "LOE_RRC",   " `LOE_RRC` varchar(255) DEFAULT NULL, "), // LOE_RRC kennel club dog id
            'Cat' =>        array (  -9,   1, "s", "Categoria", " `Categoria` varchar(1) NOT NULL DEFAULT '-', "), // required
            'Grad' =>       array (  -10,  1, "s", "Grado",     " `Grado` varchar(16) DEFAULT '-', "), // required
            // handler related data
            'HandlerID' =>  array (  -11,  0, "i", "Guia",      " `Guia` int(4) NOT NULL DEFAULT 1, "),  // to be evaluated by importer
            'Handler' =>    array (  -12,  1, "s", "NombreGuia"," `NombreGuia` varchar(255) NOT NULL, "), // Handler's name. Required
            // club related data
            'ClubID' =>     array (  -13,  0, "i", "Club",      " `Club` int(4) NOT NULL DEFAULT 1, "),  // to be evaluated by importer
            'Club' =>       array (  -14,  1, "s", "NombreClub"," `NombreClub` varchar(255) NOT NULL,")  // Club's Name. required
        );
    }

    public function retrieveExcelFile() {
        // phase 1 retrieve data from browser
        $this->myLogger->enter();
        // extraemos los datos de registro
        $data=http_request("Data","s",null);
        if (!$data) return array("errorMsg" => "importExcel(dogs)::download(): No data to import has been received");
        if (!preg_match('/data:([^;]*);base64,(.*)/', $data, $matches)) {
            return array("errorMsg" => "importExcel(dogs)::download() Invalid received data format");
        }
        // mimetype for excel file is be stored at $matches[1]: and should be checked
        // $type=$matches[1]; // 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', or whatever. Not really used
        $this->myLogger->leave();
        $contents= base64_decode( $matches[2] ); // decodes received data
        // phase 2 store it into temporary file
        $file=fopen($this->tmpfile,"wb");
        fwrite($file,$contents);
        fclose($file);
        return 0;
    }

    private function validateHeader($header) {
        // search required fields in header and store index when found
        foreach ($this->fieldList as $field =>&$data) {
            for($index=0; $index<count($header); $index++) {
                $fieldName=$header[$index];
                if ( ($fieldName==$field) || ($fieldName==_utf($field)) ) {
                    $data[0]=$index;
                }
            }
        }
        // iterate header fields
        // now check for required and not declared fields
        foreach ($this->fieldList as $key =>$val) {
            if ( ($val[0]<0) && ($val[1]>0) )
                throw new Exception ("ExcelImport(dogs)::required field '$key' => ".json_encode($val)." not found in Excel header");
        }
        return 0;
    }

    private function createTemporaryTable() {
        // To create database we need root DB access
        $rconn=DBConnection::getRootConnection();
        if ($rconn->connect_error)
            throw new Exception("Cannot perform import process: database::dbConnect()");
        // $str="DELETE FROM TABLE {$this->tablename} IF EXISTS";
        $str="CREATE TABLE {$this->tablename} (";
        foreach ($this->fieldList as $val) {
            if ($val[0]<0) continue; // field not provided
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
            if ($val[0]<0) continue; // field not provided or to be evaluated by importer
            $str1 .= "{$val[3]}, ";
            if ($val[2]=="s") { // string
                $a=mysqli_real_escape_string($this->myDBObject->conn,$row[$val[0]]);
                $str2.="'{$a}', ";
            }
            else $str2 .= " {$row[$val[0]]}, "; // integer
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

    public function validateFile( $filename=null) {
        $file=($filename==null)?$this->tmpfile:$filename;
        // open temporary file
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($file);
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
            $sheet=$reader->getCurrentSheet();
        }
        // OK: now parse sheet
        $index=0;
        foreach ($sheet->getRowIterator() as $row) {
            // first line must contain field names
            if ($index==0) {
                // check that every required field is present
                $this->validateHeader($row); // throw exception on fail
                // create temporary table in database to store and analyze Excel data
                $this->createTemporaryTable(); // throw exception when an import is already running
                $index++;
                continue; // jump to first row with data
            }
            // dump excel data into temporary database table
            $this->storeRow($index,$row);
            $index++;
        }
        // fine. we can start parsing data in DB database table
        $reader->close();
        if ($filename==null) unlink($file); // remove temporary file if no named file provided
        return 0;
    }

    public function parse($line) {
        return 0;
    }

    public function parseLine() {
        $line= array();
        // compose item with data received from browser
        return $this->parse($line);
    }

    public function initParser($index=0) {
        $line= array();
        // read first line from excel file
        // use it to simulate data from browser
        return $this->parse($line);
    }


    public function skipLine() {
        // ignore data: advance cursor index
        $index=http_request("index","i",0);
        return $this->initParser($index+1);
    }

    public function cancelImport() {
        // remove temporary files, do not perform import
        return 0;
    }
}


// skip web server parsing when running in local (CommandLineInterpreter - cli ) mode
if (php_sapi_name() != "cli" ) {

    // Consultamos la base de datos
    try {
        // 	Creamos generador de documento
        $fed=http_request("Federation","i",-1);
        if ($fed<0) throw new Exception("ImportExcel(dogs): invalid Federation ID: $fed");
        $op=http_request("Operation","s","");
        $dr=new DogReader($fed);
        $result="";
        switch ($op) {
            case "open":
                // download excel file from browser
                $dr->retrieveExcelFile();
                // check that received file matches PerroGuiaClub format
                $dr->validateFile();
                // create working file and start parsing
                $result = $dr->initParser();
                break;
            case "accept":
                // a new line has been accepted from user: insert and update temporary excel file
                $result = $dr->parseLine();
                break;
            case "skip":
                // received entry has been refused by user: remove and update temporary excel file
                $result = $dr->skipLine();
                break;
            case "cancel":
                // user has cancelled import file: clear and return temporary data
                $result = $dr->cancelImport();
                break;
            default: throw new Exception("excel_import(dogs): invalid operation '$op' requested");
        }
        if ($result===null)
            throw new Exception($dr->errormsg);
        if ($result==="") // return result when "done" or "cancelled"
            echo json_encode(array('success'=>true));
        else echo json_encode($result);
    } catch (Exception $e) {
        do_log($e->getMessage());
        echo json_encode(array('errorMsg'=>$e->getMessage()));
    }
}
