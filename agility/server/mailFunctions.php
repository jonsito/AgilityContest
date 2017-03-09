<?php

/*
mailFunctions.php

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


require_once(__DIR__."/logging.php");
require_once(__DIR__."/tools.php");
require_once(__DIR__."/auth/AuthManager.php");
require_once(__DIR__."/i18n/Country.php");
require_once(__DIR__."/mail/MailManager.php");

try {
    $result=null;
    $am= new AuthManager("mailFunctions");
    $operation=http_request("Operation","s",null);
    $prueba=http_request("Prueba","i",0);
    $jornada=http_request("Jornada","i",0);
    $club=http_request("Club","i",0);
    $juez=http_request("Juez","i",0);
    $email=http_request("Email","s","");
    $mailer=new MailManager("mailFunctions",$am,$prueba);
    if ($operation===null) throw new Exception("Call to mailFunctions without 'Operation' requested");
    switch ($operation) {
        // update email from selected club
        case "updateclub": $am->access(PERMS_OPERATOR); $result=$mailer->updateClubMail($club,$email); break;
        // update email from selected club
        case "updateJuez": $am->access(PERMS_OPERATOR); $result=$mailer->updateJuezMail($juez,$email); break;
        // clear sent mark from every clubs on this contest
        case "clearsent":  $am->access(PERMS_OPERATOR); $result=$mailer->clearSent(); break;
        // replacement for clubs::enumerate to add info on mail sent
        case "enumerate": $result=$mailer->enumerate(); break;
        // list all judges on provided journey
        case "enumerateJueces": $result=$mailer->enumerateJueces($jornada); break;
        // try to send mail to sender using configuration preferences
        case "check": $am->access(PERMS_OPERATOR); $result=$mailer->check(); break;
        // send mail to AgilityContest server
        case "notify": $am->access(PERMS_OPERATOR); $result=$mailer->notify(); break;
        // iterate on selected clubs for sending inscription template
        case "sendInscriptions":
            $am->access(PERMS_OPERATOR);
            $result=$mailer->sendInscriptions($club,$email);
            break;
        // send results, scores and excels to federation ad judges
        case "sendResults":
            $am->access(PERMS_OPERATOR);
            $partialscores=http_request("PartialScores","i",0);
            $result=$mailer->sendResults($jornada,$partialscores); break;
        default: throw new Exception("mailFunctions:: invalid operation: '$operation' provided");
    }
    if ($result==="")  echo json_encode(array('success'=>true)); // "": ok
    else if (is_string($result)) throw new Exception($result); // string: error string
    else echo json_encode($result);                            // encode response
} catch (Exception $e) {
    do_log($e->getMessage());
    echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>