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
    protected $myMailer;
    protected $myConfig;
    protected $myAuthManager;
    protected $myLogger;

    private function configureMailer($server,$port,$method,$user,$pass) {
        // $this->myLogger->trace("ConfigureMailer: Server:'$server' Port:'$port' Auth:'$method' User:'$user' Password:'$pass'");
        //Set the hostname of the mail server
        // use $this->myMailer->Host = gethostbyname('smtp.gmail.com');
        $this->myMailer->Host = gethostbyname($server);
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $this->myMailer->Port = intval($port);
        //Set the encryption system to use - ssl (deprecated) or tls
        $this->myMailer->SMTPSecure = 'tls';
        //Whether to use SMTP authentication
        $this->myMailer->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $this->myMailer->Username = $user;
        //Password to use for SMTP authentication
        $this->myMailer->Password = $pass;
    }

    public function __construct($filename,$am) {
        $this->myAuthManager=$am;
        $this->myConfig=Config::getInstance();
        $this->myLogger= new Logger($filename,$this->myConfig->getEnv("debug_level"));
        $this->myMailer = new PHPMailer; //Create a new PHPMailer instance
        $this->myMailer->isSMTP(); //Tell PHPMailer to use SMTP
        //Enable SMTP debugging
        $this->myMailer->SMTPDebug = 3; // 0 = off (for production use) // 1 = client messages // 2 = client and server messages // 3=trace connection
        $this->myMailer->Debugoutput = 'html'; //Ask for HTML-friendly debug output
        $this->configureMailer(
        // configure mailer with configuration file contents
            $this->myConfig->getEnv('email_server'),
            $this->myConfig->getEnv('email_port'),
            $this->myConfig->getEnv('email_auth'),
            $this->myConfig->getEnv('email_user'),
            $this->myConfig->getEnv('email_pass')
        );
        // retrieve data from current license and use it to initialize sender and replyTo info
        $data=$this->myAuthManager->getRegistrationInfo();
        $this->myMailer->setFrom($data['Email'], $data['Name']);
        $this->myMailer->addReplyTo($data['Email'], $data['Name']);
        // allways attach AgiltiyContest logo
        $this->myMailer->addAttachment('../images/logos/agilitycontest.png');
    }

    public function check() {
        $this->myLogger->enter();
        // configure mailer with configuration data from client form
        $this->configureMailer(
            http_request("email_server","s",""),
            http_request("email_port","i",25),
            http_request("email_auth","s","PLAIN"),
            http_request("email_user","s",""),
            http_request("email_pass","s","")
        );
        // compose a dummy message to be sent to sender :-)
        //Set who the message is to be sent to
        $this->myMailer->addAddress($this->myMailer->From, $this->myMailer->FromName);
        //Set the subject line
        $this->myMailer->Subject = 'AgilityContest e-mail test';
        //convert HTML into a basic plain-text alternative body
        $this->myMailer->Body ="<h4>Test</h4><p>Just a simple <em>HTML</em> text to test send mail in this format</p><hr/>";
        //Replace the plain text body with one created manually
        $this->myMailer->AltBody = 'This is a plain-text message body for mail testing';
        //send the message, check for errors
        $this->myLogger->trace("before send");
        if (!$this->myMailer->send()) return "Mailer Error: " . $this->myMailer->ErrorInfo;
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