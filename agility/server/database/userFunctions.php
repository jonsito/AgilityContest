<?php

/*
userFunctions.php

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
require_once (__DIR__."/../auth/Config.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/classes/Usuarios.php");
require_once (__DIR__."/classes/Admin.php");
require_once (__DIR__."/updater/Uploader.php");

$response="";
try {
	$result=null;
    $config=Config::getInstance();
	$users= new Usuarios("userFunctions");
	$am= new AuthManager("userFunctions");
	$operation=http_request("Operation","s",null);
	$id=http_request("ID","i",0);
	$user=http_request("Username","s",null);
	$pass=http_request("Password","s",null);
	$sid=http_request("Session","i",0);
	$sk=http_request("SessionKey","s","");
	if ($operation===null) throw new Exception("Call to userFunctions without 'Operation' requested");
	switch ($operation) {
		case "insert": $am->access(PERMS_ADMIN); $result=$users->insert(); break;
		case "update": $am->access(PERMS_ADMIN); $result=$users->update($id); break;
		case "delete": $am->access(PERMS_ADMIN); $result=$users->delete($id); break;
		case "password": $result=$am->setPassword($id,$pass,$sk); break; // access checks are done inside method
		case "selectbyid": $result=$users->selectByID($id); break;
		case "select": $result=$users->select(); break; // list with order, index, count and where
		case "enumerate": $result=$users->enumerate(); break; // list with where
		case "login":
		    $result = $am->login($user,$pass,$sid);
            // if configured to do, search for updates at login success
		    $result['NewVersion']="0.0.0-19700101_0000";
		    if (intval($config->getEnv("search_updates"))!==0) {
                $adm=new Admin("CheckUpdatesAtLogin",$am,"");
                $v = $adm->checkForUpgrades(false);
                $result['NewVersion'] = "{$v['version_name']}-{$v['version_date']}";
            }
            // if configured to do search for new database entries in master server
            $result['NewEntries']=0;
            if (intval($config->getEnv("search_updatedb"))!==0) {
                // if not admin throw exception, but allow to continue
                try{
                    $am->access(PERMS_ADMIN);
                    $up=new Uploader("CheckUpdateDBAtLogin");
                    $res= $up->doCheckForUpdates($result['Serial']);
                    $result['NewEntries'] = $res['rows'][0]['NewEntries'];
                } catch (Exception $e) { do_log("CheckUpdateDBAtLogin: ".$e->getMessage()); }
            }
            break;
		case "pwcheck": $result=$am->checkPassword($user,$pass); break; // just check pass, dont create session
        case "logout": $result=$am->logout(); break;
        case "reset": $result=$am->resetAdminPassword(); break;
		default:
		    throw new Exception("userFunctions:: invalid operation: '$operation' provided");
	}
	if ($result===null) 
		throw new Exception($users->errormsg);
	if ($result==="")
		$response= array('success'=>true,'insert_id'=>$users->conn->insert_id,'affected_rows'=>$users->conn->affected_rows); 
	else $response=$result;
} catch (Exception $e) {
	do_log($e->getMessage());
	$response = array('errorMsg'=>$e->getMessage());
}
// take care on jsonp request to handle https cross domain for login and setPassword
if(isset($_GET['callback'])) echo $_GET['callback'].'('.json_encode($response).')'; // jsonp
else echo json_encode($response); // json

?>