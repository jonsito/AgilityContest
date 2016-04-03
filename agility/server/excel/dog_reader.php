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
require_once(__DIR__."/../database/classes/Dogs.php");
require_once(__DIR__."/../database/classes/Clubes.php");
require_once(__DIR__."/../database/classes/Guias.php");
require_once __DIR__.'/Spout/Autoloader/autoload.php';
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

define ('IMPORT_LOG',__DIR__."/../../../logs/import.log");

class DogReader {

    public $errormsg;
    protected $myLogger;
    protected $federation;
    protected $myAuthMgr;
    protected $tmpfile;

    public function __construct($fed) {
        $this->federation = Federations::getFederation($fed);
        $this->myAuthMgr= new AuthManager("importExcel(dogs)");
        $this->myConfig=Config::getInstance();
        $this->myLogger= new Logger("importExcel(dogs)",$this->myConfig->getEnv("debug_level"));
        if (! $this->myAuthMgr->allowed(ENABLE_IMPORT) )
            throw new Exception ("ImportExcel(dogs): Feature disabled: program not registered");
        $this->tmpfile=tempnam_sfx(__DIR__."/../../../logs","import","xlsx");
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

    }

    private function createTemporaryTable() {

    }

    private function storeRow($row) {

    }

    public function validateFile() {
        // open temporary file
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($this->tmpfile);
        // if there are only one sheet assume it is what we are looking for
        if (count($reader->getSheets())>1) {
            // else look for a sheet named _("Dogs")
            foreach ($reader->getSheetIterator() as $sheet) {
                $name = $sheet->getName();
                if ($name=="Dogs" || (name==_("Dogs")) ) break;
            }
            // arriving here means "Dogs" page not found
            throw new Exception ("No sheet named 'Dogs' found in excel file");
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
            }
            // dump excel data into temporary database table
            $this->storeRow($row);
        }
        // fine. we can start parsing data in DB database table
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