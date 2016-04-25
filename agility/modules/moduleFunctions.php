<?php
/**
 * moduleFunctions.php
 *
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

require_once(__DIR__."/../server/logging.php");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/Federations.php");

try {
    $result=null;
    $federation=http_request("Federation","i",-1); // -1 defaults to all federations
    $operation=http_request("Operation","s",null); // retrieve requested operation
    $recorrido=http_request("Recorrido","i",0); // 0:separate 1:mixed 2:common
    if ($operation===null) throw new Exception("Call to moduleFunctions without 'Operation' requested");
    switch ($operation) {
        case "list": $result= Federations::getFederationList(); break;
        case "info": $result= Federations::getFederation($federation); break;
        case "enumerate": $result= Federations::enumerate(); break;
        case "infomanga": $result= Federations::infomanga($federation,$recorrido); break;
        default: throw new Exception("moduleFunctions:: invalid operation: '$operation' provided");
    }
    if ($result===null)
        throw new Exception($jueces->errormsg);
    if ($result==="")
        echo json_encode(array('success'=>true));
    else echo json_encode($result);
} catch (Exception $e) {
    do_log($e->getMessage());
    echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>