<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 2/04/16
 * Time: 16:20
excelReaderFunctions.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require_once(__DIR__ . "/../../server/logging.php");
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
require_once(__DIR__ . "/../../server/auth/AuthManager.php");
require_once(__DIR__ . "/../../server/modules/Federations.php");
require_once(__DIR__ . "/../../server/database/classes/DBObject.php");
require_once(__DIR__ . '/../../server/excel/Spout/Autoloader/autoload.php');
require_once(__DIR__ . '/../../server/excel/classes/DogReader.php');
require_once(__DIR__ . '/../../server/excel/classes/InscriptionReader.php');
require_once(__DIR__ . '/../../server/excel/classes/EntrenamientosReader.php');
require_once(__DIR__ . '/../../server/excel/classes/PartialScoresReader.php');
require_once(__DIR__ . '/../../server/excel/classes/OrdenSalidaReader.php');

$options=array();
$options['Suffix']=http_request("Suffix","s","");
$op=http_request("Operation","s","");

// do not process entire data to just retrieve progress info
if ($op==='progress') {
    // retrieve last line of progress file
    $importFileName=IMPORT_DIR."import_{$options['Suffix']}.log";
    if (!file_exists($importFileName)) {
        echo json_encode( array( 'operation'=>'progress','success'=>'ok', 'status' => "Waiting for progress info..." ) );
        return;
    }
    $lines=file($importFileName,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$lines)
        echo json_encode( array( 'operation'=>'progress','success'=>'fail', 'status' => "Error reading progress file: $importFileName" ) );
    else echo json_encode( array( 'operation'=>'progress','success'=>'ok', 'status' => strval($lines[count($lines)-1]) ) );
    return;
}

// retrieve parameters from http request
$options['Blind']=http_request('Blind',"i",0);
// DB Priority is global and states where to extract cat, grad, club, license and so
$options['DBPriority']=http_request('DBPriority',"i",1);
// UseExcelName is particular for each request and says which handler/dog name is to be preserved
$options['UseExcelNames']=http_request('UseExcelNames',"i",0);
$options['WordUpperCase']=http_request('WordUpperCase',"i",1);
$options['IgnoreWhiteSpaces']=http_request('IgnoreWhitespaces',"i",1);
$options['Object']=http_request('Object',"s",""); // 'Perro' 'Guia' 'Club'
$options['DatabaseID']=http_request('DatabaseID',"i",0); // -1:ignore 0:create else:update
$options['ExcelID']=http_request('ExcelID',"i",0); // ID of affected ExcelImport table row
$options['ParseCourseData']=http_request('ParseCourseData',"i",0); // on result import handle SCT data
$options['IgnoreNotPresent']=http_request('IgnoreNotPresent',"i",0); // skip results marked as "No Presentado"
$options['Federation']=http_request('Federation',"i",-1);
$options['Prueba']=http_request('Prueba',"i",0);
$options['Jornada']=http_request('Jornada',"i",0);
$options['Manga']=http_request('Manga',"i",0);
$options['Mode']=http_request('Mode',"i",0); // manga mode

// some shortcuts
$type=http_request('Type',"s","");
$f=intval($options['Federation']);
$p=intval($options['Prueba']);
$j=intval($options['Jornada']);
$m=intval($options['Manga']);
try {
    // 	Creamos generador de documento
    if ($type==="") throw new Exception("excelReaderFunctions(): no import type selected");
    // create propper importer instance
    if ($type==="perros") {
        if ($f<0) throw new Exception("DogReader::ImportExcel(): invalid Federation ID: $f");
        $er=new DogReader("ImportExcel(dogs)",$options);
    } else  if ($type==="inscripciones") {
        if ($p==0) throw new Exception("InscriptionReader::ImportExcel(): invalid Prueba ID: $p");
        $er=new InscriptionReader("ExcelImport(inscriptions)",$options);
    } else if ($type==="entrenamientos") {
        if ($p==0) throw new Exception("InscriptionReader::ImportExcel(): invalid Prueba ID: $p");
        $er=new EntrenamientosReader("ExcelImport(training session)",$options);
    } else if ($type==="resultados") {
        if ($p==0) throw new Exception("PartialScoresReader::ImportExcel(): invalid Prueba ID: $p");
        if ($j==0) throw new Exception("PartialScoresReader::ImportExcel(): invalid Jornada ID: $j");
        if ($m==0) throw new Exception("PartialScoresReader::ImportExcel(): invalid Manga ID: $m");
        $er=new PartialScoresReader("ExcelImport(round results)",$options);
    }  else if ($type==="ordensalida") {
        if ($p==0) throw new Exception("OrdenSalidaReader::ImportExcel(): invalid Prueba ID: $p");
        if ($j==0) throw new Exception("OrdenSalidaReader::ImportExcel(): invalid Jornada ID: $j");
        if ($m==0) throw new Exception("OrdenSalidaReader::ImportExcel(): invalid Manga ID: $m");
        $er=new OrdenSalidaReader("ExcelImport(Starting order)",$options);
    } else {
        throw new Exception("excelReaderFunctions(): invalid $type selected: ".$type);
    }

    switch ($op) {
        case "upload":
            // download excel file from browser
            $result = $er->retrieveExcelFile();
            break;
        case "check":
            // check that received file matches PerroGuiaClub format
            // and store in temporary database table
            $file=http_request("Filename","s",null);
            $result = $er->validateFile($file);
            break;
        case "parse":
            // start analysis
            $result = $er->parse();
            break;
        case "create":
            // a new line has been accepted from user: insert and update temporary excel file
            $result = $er->createEntry($options);
            break;
        case "update":
            // a new line has been accepted from user: insert and update temporary excel file
            $result = $er->updateEntry($options);
            break;
        case "ignore":
            // received entry has been refused by user: remove and update temporary excel file
            $result = $er->ignoreEntry($options);
            break;
        case "abort":
            // user has cancelled import file: clear and return temporary data
            $result = $er->cancelImport();
            break;
        case "import":
            // every entries have been corrected and have proper entry ID's: start importing
            $result = $er->beginImport();
            break;
        case "teams":
            // every entries have been corrected and have proper entry ID's: start importing teams
            $result = $er->createTeams();
            break;
        case "inscribe":
            // every entries have been corrected and have proper entry ID's: start importing inscriptions
            $result = $er->doInscription();
            break;
        case "close":
            // end of import. clear and return;
            $result = $er->endImport();
            break;
        default: throw new Exception("excel_import($type): invalid operation '$op' requested");
    }
    if ($result===null) throw new Exception($er->errormsg);// null on error
    if (is_string($result)) throw new Exception($result);
    if ( ($result==="") || ($result===0) )  // empty or zero on success
         $retcode= json_encode(array('operation'=> $op, 'success'=>'ok'));
    else $retcode= json_encode($result);         // else return data already has been set
    do_log("Excel '$type' Reader returns: '$retcode'");
    echo $retcode;
} catch (Exception $e) {
    do_log("Excel '$type'' Reader Exception: ".$e->getMessage());
    echo json_encode(array("operation"=>$op, 'success'=>'fail', 'errorMsg'=>$e->getMessage()));
}
