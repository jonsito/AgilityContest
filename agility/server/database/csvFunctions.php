<?php
/**
 * Created by PhpStorm. * User: jantonio
 * Date: 9/08/15
 * Time: 12:07
 *
 * csvFunctions.php
 *
 * Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation;
 * either version 2 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */
require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../auth/Config.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/classes/DBObject.php");

class csvHandler extends DBObject {
    protected $myAuthManager;
    protected $prueba; // prueba object
    protected $jornada; // jornada object

    protected $sep=','; // csv field separator
    protected $del='""'; // csv field delimiter
    protected $merge=true; // try to use pre-existing database data on import

    function __construct($am,$j)
    {
        parent::__construct("csvHandler");
        $this->myAuthManager = $am;
        $this->jornada = $this->__getObject("Jornadas", $j);
        if ($this->jornada === null) throw new Exception("csvHandler: cannot retrieve data for journey ID: $j");
        $this->prueba = $this->__getObject("Pruebas", $this->jornada->Prueba);
        if ($this->prueba === null) throw new Exception("csvHandler: cannot retrieve data for prueba ID: {$this->jornada->Prueba}");
        // retrieve rest of parameters
        $this->sep = http_request("Separator", "s", ','); // retrieve separator - or use default comma if not
        $this->del = http_request("Delimiter", "s", '""'); // retrieve delimiter - or use default doublequote if not provided
        $this->merge = http_request("Merge", "b", true); // tell app to merge with current database data or assume new ones
    }

    /**
     * Check that all required fields are present from first line of provided file
     * @param {array} $hdr CSV data from first line of csv file
     */
    function parseHeader($hdr) {

    }

    function importCSV() {
        // Leemos del cliente los datos a importar
        $data=http_request("Data","s",null);
        if (!$data) return array("errorMsg" => "importCSV(): No inscription data received");
        if (!preg_match('/data:([^;]*);base64,(.*)/', $data, $matches)) {
            return array("errorMsg" => "importCSV(): Invalid received data format");
        }
        // $type=$matches[1]; // 'application/octet-stream', or whatever. Not really used
        $contents=base64_decode( $matches[2] ); // decodes received data
        $temp = tmpfile();
        fwrite($temp,$contents);
        // ya tenemos los datos, ahora vamos a procesar linea por linea
        fseek($temp, 0);
        $lineno=1;
        while (($datos = fgetcsv($temp, 0, $this->sep,$this->del)) !== FALSE) {
            $numero = count($datos);
            if ($lineno==1) $this->parseHeader($datos);
            echo "$numero de campos en la línea $lineno:\n";
            $lineno++;
            for ($c=0; $c < $numero; $c++) {
                echo $datos[$c] . "\n";
            }
        }
        fclose($temp); // this also removes temporary file
        return "";
    }

    function exportCSV() {
        return "";
    }
}

$response="";
try {
    $result=null;
    $am= new AuthManager("importFunctions");
    $operation=http_request("Operation","s","");
    if ($operation===null) throw new Exception("Call to adminFunctions without 'Operation' requested");
    $handler=new csvHandler($am,$prueba,$jornada);
    switch ($operation) {
        case "import": $am->access(PERMS_OPERATOR); $result=$handler->importCSV(); break;
        case "export": $am->access(PERMS_OPERATOR); $result=$handler->exportCSV(); break;
        default:
            throw new Exception("importFunctions:: invalid operation: '$operation' provided");
    }
    if ($result===null)	throw new Exception($adm->errormsg); // error
    if ($result==="ok") return; // don't generate any aditional response
    if ($result==="") $result= array('success'=>true); // success
    echo json_encode($result);
} catch (Exception $e) {
    do_log($e->getMessage());
    echo json_encode(array('errorMsg'=>$e->getMessage()));
}
?>