<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 2/04/16
 * Time: 16:20
excelReaderFunctions.php

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

require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../auth/Config.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/../modules/Federations.php");
require_once(__DIR__."/../database/classes/DBObject.php");
require_once(__DIR__.'/Spout/Autoloader/autoload.php');
require_once(__DIR__.'/dog_reader.php');
require_once(__DIR__.'/inscription_reader.php');
require_once(__DIR__.'/trainingtable_reader.php');
require_once(__DIR__.'/partialscores_reader.php');

$options=array();
$options['Suffix']=http_request("Suffix","s","");
$op=http_request("Operation","s","");
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

// Consultamos la base de datos
try {
    // 	Creamos generador de documento
    $mode=http_request("Mode","s","");
    if ($mode==="") throw new Exception("excelReaderFunctions(): no mode selected");
    if ($mode==="resultados") throw new Exception("excelResultsReader(): not yet implemented");

    $options['Blind']=http_request("Blind","i",0);
    $options['DBPriority']=http_request("DBPriority","i",1);
    $options['WordUpperCase']=http_request("WordUpperCase","i",1);
    $options['IgnoreWhiteSpaces']=http_request("IgnoreWhitespaces","i",1);
    $options['Object']=http_request("Object","s",""); // 'Perro' 'Guia' 'Club'
    $options['DatabaseID']=http_request("DatabaseID","i",0); // -1:ignore 0:create else:update
    $options['ExcelID']=http_request("ExcelID","i",0); // ID of affected ExcelImport table row
    $options['ParseCourseData']=http_request("ParseCourseData","i",0); // on result import handle SCT data
    $options['AllowNoLicense']=http_request("AllowNoLicense","i",0); // do not ignore result when no license provided
    $options['Federation']=http_request("Federation","i",-1);
    $options['Prueba']=http_request("Prueba","i",0);
    $options['Jornada']=http_request("Jornada","i",0);
    $options['Manga']=http_request("Manga","i",0);

    // some shortcuts
    $f=intval($options['Federation']);
    $p=intval($options['Prueba']);
    $j=intval($options['Jornada']);
    $m=intval($options['Manga']);
    // create propper importer instance
    if ($mode==="perros") {
        if ($f<0) throw new Exception("dog_reader::ImportExcel(): invalid Federation ID: $f");
        $er=new DogReader("ImportExcel(dogs)",$options);
    } else  if ($mode==="inscripciones") {
        if ($p==0) throw new Exception("inscription_reader::ImportExcel(): invalid Prueba ID: $p");
        $er=new InscriptionReader("ExcelImport(inscriptions)",$options);
    } else if ($mode==="entrenamientos") {
        if ($p==0) throw new Exception("inscription_reader::ImportExcel(): invalid Prueba ID: $p");
        $er=new EntrenamientosReader("ExcelImport(training session)",$options);
    } else if ($mode==="resultados") {
        if ($p==0) throw new Exception("partialscores_reader::ImportExcel(): invalid Prueba ID: $p");
        if ($j==0) throw new Exception("partialscores_reader::ImportExcel(): invalid Jornada ID: $j");
        if ($m==0) throw new Exception("partialscores_reader::ImportExcel(): invalid Manga ID: $m");
        $er=new PartialScoresReader("ExcelImport(round results)",$options);
    } else {
        throw new Exception("excelReaderFunctions(): invalid mode selected: ".$mode);
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
        default: throw new Exception("excel_import($mode): invalid operation '$op' requested");
    }
    if ($result===null) throw new Exception($er->errormsg);// null on error
    if (is_string($result)) throw new Exception($result);
    if ( ($result==="") || ($result===0) )  // empty or zero on success
         $retcode= json_encode(array('operation'=> $op, 'success'=>'ok'));
    else $retcode= json_encode($result);         // else return data already has been set
    do_log("Excel $mode Reader returns: '$retcode'");
    echo $retcode;
} catch (Exception $e) {
    do_log("Excel $mode Reader Exception: ".$e->getMessage());
    echo json_encode(array("operation"=>$op, 'success'=>'fail', 'errorMsg'=>$e->getMessage()));
}
