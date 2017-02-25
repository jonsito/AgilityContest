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
    $mailer=new MailManager("mailFunctions",$am);
    if ($operation===null) throw new Exception("Call to mailFunctions without 'Operation' requested");
    switch ($operation) {
        // try to send mail to sender using configuration preferences
        case "check": $am->access(PERMS_OPERATOR); $result=$mailer->check(); break;
        // send mail to AgilityContest server
        case "notify": $am->access(PERMS_OPERATOR); $result=$mailer->notify(); break;
        // iterate on selected clubs for sending inscription template
        case "sendInscriptions": $am->access(PERMS_OPERATOR); $result=$mailer->sendInscriptions($prueba); break;
        // send results, scores and excels to federation ad judges
        case "sendResults": $result=$mailer->sendResults($jornada); break;
        default: throw new Exception("mailFunctions:: invalid operation: '$operation' provided");
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