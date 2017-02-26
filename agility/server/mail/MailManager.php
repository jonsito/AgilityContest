<?php

require_once __DIR__.'/PHPMailer-5.2.22/PHPMailerAutoload.php';
require_once __DIR__.'/../auth/Config.php';
require_once __DIR__.'/../auth/AuthManager.php';
/*
mailManager.php

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

class MailManager {
    protected $myConfig;
    protected $myAuthManager;
    protected $myLogger;

    public function __construct($filename,$am) {
        $this->myAuthManager=$am;
        $this->myConfig=Config::getInstance();
        $this->myLogger= new Logger($filename,$this->myConfig->getEnv("debug_level"));
    }

    public function check() {
        $this->myLogger->enter();
        $myMailer = new PHPMailer; //Create a new PHPMailer instance
        $myMailer->isSMTP(); //Tell PHPMailer to use SMTP
        //Enable SMTP debugging
        $myMailer->SMTPDebug = 0; // 0 = off (for production use) // 1 = client messages // 2 = client and server messages // 3=trace connection
        $myMailer->Debugoutput = 'html';
        // $myMailer->Host = gethostbyname(http_request("email_server","s",""));
        $myMailer->Host = http_request("email_server","s","127.0.0.1");
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $myMailer->Port = intval(http_request("email_port","i",25));
        /* http_request("email_auth","s","PLAIN") */
        //Set the encryption system to use - ssl (deprecated) or tls
        $myMailer->SMTPSecure = 'tls';
        //Whether to use SMTP authentication
        $myMailer->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $myMailer->Username = http_request("email_user","s","");
        //Password to use for SMTP authentication
        $myMailer->Password = http_request("email_pass","s","");
        // retrieve data from current license and use it to initialize sender and replyTo info
        $data=$this->myAuthManager->getRegistrationInfo();
        $myMailer->setFrom($data['Email'], $data['Name']);
        $myMailer->addReplyTo($data['Email'], $data['Name']);
        // compose a dummy message to be sent to sender :-)
        //Set who the message is to be sent to
        $myMailer->addAddress($myMailer->From, $myMailer->FromName);
        //Set the subject line
        $myMailer->Subject = 'AgilityContest e-mail test';
        //convert HTML into a basic plain-text alternative body
        $d=date("Ymd Hi");
        $myMailer->msgHTML("<h4>Test</h4><p>Just a simple <em>HTML</em> text to test send mail in this format</p><p>Mail sent at:$d</p><hr/>");
        //Replace the plain text body with one created manually
        $myMailer->AltBody = "This is a plain-text message body for mail testing.\nMail sent at $d";
        // allways attach AgiltiyContest logo . use absolute paths as phpmailer does not handle relative ones
        $myMailer->addAttachment(__DIR__.'/../../images/logos/agilitycontest.png');
        //send the message, check for errors
        if (!$myMailer->send()) {
            return "Mailer Error: " . $myMailer->ErrorInfo;
        }
        $this->myLogger->leave();
        return "";
    }

    public function notify() {
        $this->myLogger->enter();
        $this->myLogger->leave();
        return "";
    }

    public function sendInscriptions($prueba) {
        $this->myLogger->enter();
        $this->myLogger->leave();
        return "";
    }
    public function sendResults($jornada) {
        $this->myLogger->enter();
        $this->myLogger->leave();
        return "";
    }
}