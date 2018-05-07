<?php
/*
mailFunctions.php

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

require_once(__DIR__ . "/../server/logging.php");
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/AuthManager.php");
require_once(__DIR__ . "/../server/mail/MailManager.php");

try {
    $result=null;
    $am= AuthManager::getInstance("mailFunctions");
    $operation=http_request("Operation","s",null);
    $data=array(
        // datos para operacion de enumerate
        'q' =>   http_request("q","s",""),
        // datos para envio de inscripciones
        'EmptyTemplate' => http_request("EmptyTemplate","i",0),
        // datos para envio de resultados
        'Prueba'    =>  http_request("Prueba","i",0),
        'Jornada'   =>  http_request("Jornada","i",0),
        'Club'      =>  http_request("Club","i",0), // club id on change mail operation
        'Juez'      =>  http_request("Juez","i",0), // Juez id on change mail operation
        'Email'     =>  http_request("Email","s",""), // mail list to send mail to
        'SendToFederation'  =>  http_request("SendToFederation","i",0), // flag to ask for send CC: to federation provided mail
        'PartialScores'  =>  http_request("PartialScores","i",0), // include partial scores (rounds) in mail
        'SendPreAgility' =>  http_request("SendPreAgility","i",0), // include pre-agility results
        'FedAddress'=>  http_request("FedAddress","s",""), // federation email address
        'Contents'  =>  http_request("Contents","s",""), // federation email address
        'ZipFile'  =>  http_request("ZipFile","i",1), // default is to create single zip file
        'SendToMe' =>  http_request("SendToMe","i",0), // add CC to sender
        // datos para prueba de configuracion de correo
        'email_server'  => http_request("email_server","s","127.0.0.1"),
        'email_port'    => http_request("email_port","i",25),
        'email_crypt'   => http_request("email_crypt","s","None"),
        'email_auth'    => http_request("email_auth","s","PLAIN"),
        'email_user'    => http_request("email_user","s",""),
        'email_pass'    => http_request("email_pass","s",""),
        'email_realm'   => http_request("email_realm","s",""),
        'email_workstation' => http_request("email_workstation","s","")
    );
    $mailer=new MailManager("mailFunctions",$am,$data);
    if ($operation===null) throw new Exception("Call to mailFunctions without 'Operation' requested");
    switch ($operation) {
        // update email from selected club
        case "updateclub": $am->access(PERMS_OPERATOR); $result=$mailer->updateClubMail(); break;
        // update email from selected club
        case "updateJuez": $am->access(PERMS_OPERATOR); $result=$mailer->updateJuezMail(); break;
        // clear sent mark from every clubs on this contest
        case "clearsent":  $am->access(PERMS_OPERATOR); $result=$mailer->clearSent(); break;
        // clear cache directory to force reload attachments
        case "clearcache":  $am->access(PERMS_OPERATOR); $result=$mailer->clearMailCache(); break;
        // replacement for clubs::enumerate to add info on mail sent
        case "enumerate": $result=$mailer->enumerateClubes(); break;
        // list all judges on provided journey
        case "enumerateJueces": $result=$mailer->enumerateJueces(); break;
        // try to send mail to sender using configuration preferences
        case "check": $am->access(PERMS_OPERATOR); $result=$mailer->check(); break;
        // send mail to AgilityContest server
        case "notify": $am->access(PERMS_OPERATOR); $result=$mailer->notify(); break;
        // iterate on selected clubs for sending inscription template
        case "sendInscriptions": $am->access(PERMS_OPERATOR); $result=$mailer->sendInscriptions(); break;
        // send results, scores and excels to federation ad judges
        case "sendResults": $am->access(PERMS_OPERATOR); $result=$mailer->sendResults(); break;
        // download zip file with results
        case "getZipFile": $result=$mailer->getZipFile(); return 0; // Don't send anything extra to server, just return
        default:
            throw new Exception("mailFunctions:: invalid operation: '$operation' provided");
            break;
    }
    if ($result==="")  echo json_encode(array('success'=>true)); // "": ok
    else if (is_string($result)) throw new Exception($result); // string: error string
    else echo json_encode($result);                            // encode response
} catch (Exception $e) {
    do_log($e->getMessage());
    echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>