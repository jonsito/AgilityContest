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

define ('IMPORT_LOG',__DIR__."/../../../logs/import.log");

class DogReader {

    public $errormsg;
    protected $myLogger;
    protected $federation;
    protected $myAuthMgr;
    protected $fromfile;
    protected $tofile;

    public function __construct($fed) {
        $this->federation = Federations::getFederation($fed);
        $this->myAuthMgr= new AuthManager("importExcel(dogs)");
        $this->myConfig=Config::getInstance();
        $this->myLogger= new Logger("importExcel(dogs)",$this->myConfig->getEnv("debug_level"));
        if (! $this->myAuthMgr->allowed(ENABLE_IMPORT) )
            throw new Exception ("ImportExcel(dogs): Feature disabled: program not registered");
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
        return base64_decode( $matches[2] ); // decodes received data
        // phase 2 store it into temporary file
    }

    public function validateFile() {
        // read first line of temporary file
        // check that every required field is present
        // add optional ( as from pèrroGuiaClub DB view ) and generated fields ( dog/handler/club ID )
        // create temporary field with every fields sorted as we want to
    }

    public function parse($line) {
    }

    public function initParser($index=0) {
        $line= array();
        // read first line from excel file
        // use it to simulate data from browser
        return parseLine($line);
    }

    public function parseLine() {
        $line= array();
        // compose item with data received from browser
        return parse($line);
    }

    public function skipLine() {
        // ignore data: advance cursor index
        $index=http_request("index","i",0);
        return initParser($index+1);
    }

    public function cancelImport() {
        // remove temporary files, do not perform import
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
        default: throw new Exception("excel_import(dogs): invalid operation '$oper' requested");
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