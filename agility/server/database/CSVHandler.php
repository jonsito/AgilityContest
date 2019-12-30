<?php
/**
 * Created by PhpStorm. * User: jantonio
 * Date: 9/08/15
 * Time: 12:07
 *
 * CSVHandler.php
 *
 * Copyright  2013-2020 by Juan Antonio Martinez ( juansgaviota at gmail dot com )
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

class CSVHandler extends DBObject {
    protected $headers=null;
    protected $myAuthManager;
    protected $prueba; // prueba object

    protected $sep=','; // csv field separator
    protected $del='""'; // csv field delimiter
    protected $merge=true; // try to use pre-existing database data on import

    function __construct($am,$p)
    {
        parent::__construct("csvHandler");
        $this->myAuthManager = $am;
        if ($p<=0) throw new Exception("csvHandler: invalid Prueba ID:$p");
        $this->prueba=$this->__getObject("pruebas",$p);
        if ($this->prueba === null) throw new Exception("csvHandler: cannot retrieve data for prueba ID:$p");
        // retrieve rest of parameters
        $this->sep = http_request("Separator", "s", ','); // retrieve separator - or use default comma if not
        $this->del = http_request("Delimiter", "s", '""'); // retrieve delimiter - or use default doublequote if not provided
        $this->merge = http_request("Merge", "b", true); // tell app to merge with current database data or assume new ones
    }

    /**
     * comprobamos que tenemos todos los campos necesarios. En caso necesario junta nombre y apellido
     * campos obligatorios:
     * - Perro, Guia (Nombre/Apellidos), Club, Categoria, Jornadas
     * Campos dependientes del tipo de jornada
     * - Grado, Equipo
     * Campos opcionales
     * - Raza, Celo, Dorsal,
     * @param $datos
     * @return string "" on success else array(errorMsg => errorString)
     */
    function checkHeader($datos) {
        $count=0;
        // look for mandatory data
        foreach($datos as $item) {
            if ($item=="Perro") { $count++; continue; }
            if ($item=="Guia") { $count++; continue; }
            if ($item=="Club") { $count++; continue; }
            if ($item=="Categoria") { $count++; continue; }
            // TODO: evaluate required journeys
            if ($item=="Jornadas") { $count++; continue; }
        }
        return "";
    }

    function importCSV() {
        if ($this->prueba->Cerrada == 1) {
            return array("errorMsg" => "csvHandler::import(): cannot import data into a closed contest");
        }
        // FASE 1: Leemos del cliente los datos a importar
        $data=http_request("Data","s",null);
        if (!$data) {
            return array("errorMsg" => "importCSV(): No inscription data received");
        }
        if (!preg_match('/data:([^;]*);base64,(.*)/', $data, $matches)) {
            return array("errorMsg" => "csvHandler::import(): Invalid received data format");
        }
        // $type=$matches[1]; // 'application/octet-stream', or whatever. Not really used
        $contents=base64_decode( $matches[2] ); // decodes received data
        $temp = tmpfile();
        fwrite($temp,$contents);
        // ya tenemos los datos, ahora vamos a procesar linea por linea
        fseek($temp, 0);
        $lineno=1;
        $entries=array();
        while (($datos = fgetcsv($temp, 0, $this->sep,$this->del)) !== FALSE) {
            if ($this->headers==null) {
                $res=$this->checkHeader($datos);
                if ($res!== "" ) return $res;
                $this->headers=$datos;
                $headerlen = count($datos);
                continue;
            }
            $nitems=count($datos);
            if ($nitems!=$headerlen) {
                return array("errorMsg" => "csvHandler::import(): Invalid field count at line:$lineno found:$nitems expected:$headerlen");
            }
            array_push($entries,$datos);
        }
        fclose($temp); // this also removes temporary file
        // FASE 2: normaliza datos
        // FASE 3: calcula el Club ID
        // FASE 4: calcula el Guia ID
        // FASE 5: evalua Dorsales
        return "";
    }

    function exportCSV() {
        return "";
    }
}

?>