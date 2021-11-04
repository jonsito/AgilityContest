<?php

/*
adminFunctions.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

define ('RESTORE_DIR', __DIR__ . "/../../logs/");

require_once(__DIR__ . "/../server/logging.php");
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/ProgressHandler.php");
require_once(__DIR__ . "/../server/auth/Config.php");
require_once(__DIR__ . "/../server/auth/AuthManager.php");
require_once(__DIR__ . "/../server/database/classes/Admin.php");
require_once(__DIR__ . "/../server/printer/RawPrinter.php");

$response="";
try {
	$result=null;
    $config=Config::getInstance();
	$operation=http_request("Operation","s","");
    $perms=http_request("Perms","i",PERMS_NONE);
    $mode=http_request("Mode","i",-1); // default for autobackup is do not handle user config, just backup
	$fed=http_request("Federation","i",-1); // default for erase is "All federations"
    $suffix=http_request("Suffix","s","");
    $version=http_request("Version","s","");
    $serial=http_request("Serial","s","00000000");
    $directory=http_request("Directory","s",""); // where to store user backup or null to use defaults
	if ($operation===null) throw new Exception("Call to adminFunctions without 'Operation' requested");
	if ($operation==="progress") {
		// retrieve progress handler
		$ph=ProgressHandler::getHandler( ($mode===0)?"restore":"upgrade",$suffix);
        // return last line of progress file
        echo $ph->getData();
        return;
	}
	$am= AuthManager::getInstance("adminFunctions");
    $adm= new Admin("adminFunctions",$am);
	switch ($operation) {
		case "searchClub":
            $result=$am->searchClub(); break;
		case "userlevel":
			$am->access($perms); $result=array('success'=>true); break;
		case "permissions":
			$am->permissions($perms); $result=array('success'=>true); break;
		case "capabilities":
			$am->access(PERMS_NONE); $result=$am->getLicensePerms(); break;
        case "backup":
        	/* $am->access(PERMS_ADMIN); */ $result=$adm->backup();	break;
        case "autobackup":
        	/* $am->access(PERMS_ADMIN); */ $result=$adm->autobackup($mode,$directory);	break;
		case "restore":
			$am->access(PERMS_ADMIN);
            $adm->setProgressHandler(0,$suffix); // 0:restore 1:upgrade
            $result=$adm->restore(); break;
		case "reset":
			$am->access(PERMS_ADMIN); $result=$adm->clearDatabase($fed); break;
		case "clear":
			$am->access(PERMS_ADMIN); $result=$adm->clearContests(-1); break;
        case "upgrade":
            $am->access(PERMS_ADMIN); $result=$adm->checkForUpgrades(); break;
        case "download":
            $am->access(PERMS_ADMIN);
            $adm->setProgressHandler(1,$suffix); // 0:restore 1:upgrade
            $result=$adm->downloadUpgrades($version);
            break;
		case "listLicenses":
			$result=$am->listRegisteredLicenses(); break;
		case "activateLicense":
			$am->access(PERMS_ADMIN);
			if ($mode==0) $result=$am->activateSelectedLicense($serial);
			else $result=$am->removeSelectedLicense($serial);
			break;
		case "reginfo": 
			$result=$am->getRegistrationInfo(); if ($result==null) $adm->errormsg="Cannot retrieve license information"; break;
		case "register":
			$am->access(PERMS_ADMIN); $result=$am->registerApp(); if ($result==null) $adm->errormsg="Cannot import license data"; break;
		case "loadConfig": // send configuration to browser
			$result=$config->loadConfig();
			break;
		case "backupConfig": // generate and download a "config.ini" file
			$result=$config->backupConfig();
			break;
		case "restoreConfig": // receive, analyze and save configuration from file
			$am->access(PERMS_ADMIN);
			$result=$config->restoreConfig();
			$ev=new Eventos("RestoreConfig",1,$am);
			$ev->reconfigure();
			break;
		case "setEnv":
            $am->access(PERMS_ADMIN);
            $key=http_request("Key","s","");
            $value=http_request("Value","s","");
            $result="";
            if ($key!=="") {
            	$config->setEnv($key,$value);
                $result=$config->saveConfig();
                $ev=new Eventos("SetEnv: {$key}",1,$am);
                $ev->reconfigure();
            }
            break;
		case "saveConfig": 
			$am->access(PERMS_ADMIN);
			$result=$config->saveConfig();
			$ev=new Eventos("SaveConfig",1,$am);
			$ev->reconfigure();
			break;
		case "defaultConfig": 
			$am->access(PERMS_ADMIN);
			$result=$config->defaultConfig();
			$ev=new Eventos("DefaultConfig",1,$am);
			$ev->reconfigure();
			break;
        case "getAvailableLanguages":
            $result= $config->getAvailableLanguages();
            break;
		case "printerCheck":
			$am->access(PERMS_OPERATOR);
			$pname=http_request("event_printer","s","");
			$pwide=http_request("wide_printer","i",-1);
			$printer=new RawPrinter($pname,$pwide);
			$printer->rawprinter_Check();
			break;
		case "viewlog":
            $result=$adm->dumpLog();
			break;
        case "resetlog":
            $am->access(PERMS_ADMIN);
            $result=$adm->resetLog();
            break;
        case "cleartmpdir":
            $am->access(PERMS_ADMIN);
            $result=$adm->clearTemporaryDirectory();
            break;
		default:
			throw new Exception("adminFunctions:: invalid operation: '$operation' provided");
	}
	if ($result===null)	throw new Exception($adm->errormsg); // error
	if (is_string($result)) {
        if ($result==="ok") return; // don't generate any aditional response
		if ($result==="") $result= array('success'=>true); // success
		else $result=array('errorMsg'=>$result); // non empty string means error message
	}
	echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}
?>
