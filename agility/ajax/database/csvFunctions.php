<?php
/**
 * Created by PhpStorm. * User: jantonio
 * Date: 9/08/15
 * Time: 12:07
 *
 * CSVHandler.php
 *
 * Copyright  2013-2019 by Juan Antonio Martinez ( juansgaviota at gmail dot com )
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
require_once(__DIR__."/../../server/logging.php");
require_once(__DIR__."/../../server/tools.php");
require_once(__DIR__."/../../server/auth/Config.php");
require_once(__DIR__."/../../server/auth/AuthManager.php");
require_once(__DIR__."/../../server/database/CSVHandler.php");

$response="";
try {
    $result=null;
    $am= AuthManager::getInstance("importFunctions");
    $operation=http_request("Operation","s","");
    $prueba=http_request("Prueba","i",0);
    if ($operation===null) throw new Exception("Call to adminFunctions without 'Operation' requested");
    $handler=new CSVHandler($am,$prueba);
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