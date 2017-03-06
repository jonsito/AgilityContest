<?php

require_once __DIR__.'/PHPMailer-5.2.22/PHPMailerAutoload.php';
require_once __DIR__.'/../auth/Config.php';
require_once __DIR__.'/../auth/AuthManager.php';
require_once __DIR__.'/../excel/classes/Excel_Inscripciones.php';
require_once __DIR__.'/../excel/classes/Excel_Clasificaciones.php';
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
    protected $myDBObj;
    protected $pruebaObj;

    public function __construct($filename,$am,$prueba) {
        $this->myAuthManager=$am;
        $this->myConfig=Config::getInstance();
        $this->myLogger= new Logger($filename,$this->myConfig->getEnv("debug_level"));
        $this->myDBObj=new DBOBject("MailManager::Enumerate");
        $this->pruebaObj=$this->myDBObj->__selectObject("*","Pruebas","ID=$prueba");
    }

    /**
     * Initialize mailer parameters according configuration file
     *
     * @param $myMailer PHPMailer object. passed by reference
     */
    private function setup_mailer_from_config(&$myMailer) {
        $myMailer->isSMTP(); //Tell PHPMailer to use SMTP
        //Enable SMTP debugging. Notice that output is sent to client, so json_parse() fails
        $myMailer->SMTPDebug = 0; // 0 = off (for production use) // 1 = client messages // 2 = client and server messages // 3=trace connection
        $myMailer->Debugoutput = 'html';
        // $myMailer->Host = gethostbyname(http_request("email_server","s","127.0.0.1"));
        $myMailer->Host = $this->myConfig->getEnv("email_server");
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $myMailer->Port = intval($this->myConfig->getEnv("email_port"));
        /* http_request("email_crypt","s","none") */
        $crypt=$this->myConfig->getEnv("email_crypt");
        switch($crypt) {
            case 'NONE':
                $myMailer->SMTPSecure='';
                $myMailer->SMTPAutoTLS=false;
                break;
            case 'STARTTLS':
                $myMailer->SMTPSecure='tls';
                $myMailer->SMTPAutoTLS=true;
                break;
            case 'TLS':
                $myMailer->SMTPSecure=($myMailer->Port==465)?'ssl':'tls';
                $myMailer->SMTPAutoTLS=false;
                break;
            default:
                $this->myLogger->error("Invalid encryption method: $crypt");
                break;
        }
        // Whether to use SMTP authentication
        $myMailer->AuthType = $this->myConfig->getEnv("email_auth");
        $myMailer->SMTPAuth = ($myMailer->AuthType == "PLAIN" )?false:true;
        //Username to use for SMTP authentication - use full email address for gmail
        $myMailer->Username = $this->myConfig->getEnv("email_user");
        $myMailer->Password = $this->myConfig->getEnv("email_pass");
        $myMailer->Realm = $this->myConfig->getEnv("email_realm");
        $myMailer->Workstation = $this->myConfig->getEnv("email_workstation");
        // retrieve data from current license and use it to initialize sender and replyTo info
        $data=$this->myAuthManager->getRegistrationInfo();
        $myMailer->setFrom($data['Email'], $data['Name']);
        $myMailer->addReplyTo($data['Email'], $data['Name']);
    }

    /**
     * Use http parameters to try to configure and send a test email to sender's address
     * @return string empty on success, else error string
     */
    public function check() {
        $this->myLogger->enter();
        $myMailer = new PHPMailer; //Create a new PHPMailer instance
        $myMailer->isSMTP(); //Tell PHPMailer to use SMTP
        //Enable SMTP debugging. Notice that output is sent to client, so json_parse() fails
        $myMailer->SMTPDebug = 0; // 0 = off (for production use) // 1 = client messages // 2 = client and server messages // 3=trace connection
        $myMailer->Debugoutput = 'html';
        // $myMailer->Host = gethostbyname(http_request("email_server","s","127.0.0.1"));
        $myMailer->Host = http_request("email_server","s","127.0.0.1");
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $myMailer->Port = intval(http_request("email_port","i",25));
        /* http_request("email_crypt","s","none") */
        $crypt=http_request("email_crypt","s","None");
        switch($crypt) {
            case 'NONE':
                $myMailer->SMTPSecure='';
                $myMailer->SMTPAutoTLS=false;
                break;
            case 'STARTTLS':
                $myMailer->SMTPSecure='tls';
                $myMailer->SMTPAutoTLS=true;
                break;
            case 'TLS':
                $myMailer->SMTPSecure=($myMailer->Port==465)?'ssl':'tls';
                $myMailer->SMTPAutoTLS=false;
                break;
            default:
                $this->myLogger->error("Invalid encryption method: $crypt");
                break;
        }
        // Whether to use SMTP authentication
        $myMailer->AuthType = http_request("email_auth","s","PLAIN");
        $myMailer->SMTPAuth = ($myMailer->AuthType == "PLAIN" )?false:true;
        //Username to use for SMTP authentication - use full email address for gmail
        $myMailer->Username = http_request("email_user","s","");
        $myMailer->Password = http_request("email_pass","s","");
        $myMailer->Realm = http_request("email_realm","s","");
        $myMailer->Workstation = http_request("email_workstation","s","");
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

    /**
     * Retrieve a list of clubs for this federation indicating whether email has already been sent
     * @return array|null null on error; array ['total', 'rows'] on success
     */
    public function enumerate() {
        $this->myLogger->enter();
        $curFederation=Federations::getFederation(intval($this->pruebaObj->RSCE));
        // evaluate search query string
        $q=http_request("q","s","");
        // evaluate federation for club/country filtering
        $fedstr = "1";
        if ($curFederation!=null) {
            $fed=intval($curFederation->get('ID'));
            $mask=1<<$fed;
            $intlmask=Federations::getInternationalMask();
            $fedstr=$curFederation->isInternational()?"((Federations & $intlmask)!=0)":"((Federations & $mask)!=0)";
        }
        $where="1";
        if ($q!=="") $where="( Nombre LIKE '%".$q."%' )";
        $result=$this->myDBObj->__select(
        /* SELECT */ "ID,Nombre,Provincia,Pais,Federations,Email",
        /* FROM */ "Clubes",
        /* WHERE */ "$fedstr AND (ID>1) AND $where", // do not include default club in listing
        /* ORDER BY */ "Nombre ASC",
        /* LIMIT */ ""
        );
        // get MailSent field on pruebaID and add "Sent" field on each row
        foreach ($result['rows'] as &$row) {
            $row['Sent']=list_isMember($row['ID'],$this->pruebaObj->MailList)?1:0;
        }
        $this->myLogger->leave();
        return $result;
    }

    /**
     * Retrieve a list of clubs for this federation indicating whether email has already been sent
     * @param {integer} $jornada Jornada ID
     * @return array|null null on error; array ['total', 'rows'] on success
     */
    public function enumerateJueces($jornada) {
        $this->myLogger->enter();
        if ($jornada<=0) return $this->error("enumerateJuecesByJornada(): Invalid Jornada ID: $jornada");
        // evaluate search query string
        $q=http_request("q","s","");
        // evaluate judge list by parsing rounds in journey
        $jueces=$this->myDBObj->__select("Juez1,Juez2","Mangas","Jornada=$jornada");
        $list=array();
        foreach ($jueces['rows'] as $item) { $list[]=$item['Juez1']; $list[]=$item['Juez2']; }
        $data=array_unique($list,SORT_NUMERIC); // elimina duplicados
        $list=join(",",$data); // compone la lista de jueces
        $where="1";
        if ($q!=="") $where="( Nombre LIKE '%".$q."%' )";
        $result=$this->myDBObj->__select(
        /* SELECT */ "*",
            /* FROM */ "Jueces",
            /* WHERE */ "(ID>1) AND (ID IN ($list) ) AND $where", // do not include default juez in listing
            /* ORDER BY */ "Nombre ASC",
            /* LIMIT */ ""
        );
        $this->myLogger->leave();
        return $result;
    }

    /**
     * mark every club on this contest as pending to send mail
     * @return string empty on success; null on error
     */
    public function clearSent() {
        $str="UPDATE Pruebas SET MailList='BEGIN,END' WHERE ID={$this->pruebaObj->ID}";
        $res=$this->myDBObj->query($str);
        if (!$res) {
            $error=$this->myDBObj->conn->error;
            $this->myLogger->error($error);
            return $error;
        }
        // also clear stored files from cache
        delTree(__DIR__."/../../../logs/mail_{$this->pruebaObj->ID}");
        return "";
    }

    /**
     * Update club email with provided data
     * @param {integer} $club Club ID
     * @param {string} $email new Email Address ( escapechar'd by http_request )
     * @return {string} empty on success, else error msg
     */
    public function updateClubMail($club,$email) {
        if ($club<=1)
            throw new Exception("updateClubMail(): Invalid Club ID $club");
        if (!filter_var($email,FILTER_VALIDATE_EMAIL))
            throw new Exception ("updateClubMail() provided data '$email' is not a valid email address");
        $str="UPDATE Clubes SET Email='$email' WHERE ID=$club";
        $res=$this->myDBObj->query($str);
        if (!$res) return $this->myDBObj->error($this->myDBObj->conn->error);
        return "";
    }

    /**
     * Update juez email with provided data
     * @param {integer} $club Juez ID
     * @param {string} $email new Email Address ( escapechar'd by http_request )
     * @return {string} empty on success, else error msg
     */
    public function updateJuezMail($juez,$email) {
        if ($juez<=1)
            throw new Exception("updateJuezMail(): Invalid Juez ID $juez");
        if (!filter_var($email,FILTER_VALIDATE_EMAIL))
            throw new Exception ("updateJuezbMail() provided data '$email' is not a valid email address");
        $str="UPDATE Jueces SET Email='$email' WHERE ID=$juez";
        $res=$this->myDBObj->query($str);
        if (!$res) return $this->myDBObj->error($this->myDBObj->conn->error);
        return "";
    }

    /**
     * send inscription poster, tryptich and excel template to club
     * @param {integer} $club
     * @param {string} $email
     * @return string empty on success; else error code
     */
    public function sendInscriptions($club,$email) {
        $this->myLogger->enter();
        $timeout=ini_get('max_execution_time');
        $maildir=__DIR__."/../../../logs/mail_{$this->pruebaObj->ID}";
        $this->myLogger->trace("Sending mail for club:'$club' to address:'$email'");
        if ($email=="") return "Error: no email address set";

        // create compose directory. ignore errors if file already exists
        @mkdir($maildir,0777,true); // create subdirectories
        // try to retrieve poster into compose directory
        $poster_ext="jpg";
        if ($this->pruebaObj->Cartel=="") {
            $this->myLogger->info("No Poster declared for prueba {$this->pruebaObj->ID} {$this->pruebaObj->Nombre}");
        } else {
            set_time_limit($timeout);
            // get extension for file to be downloaded
            $poster_ext=pathinfo( parse_url($this->pruebaObj->Cartel,PHP_URL_PATH), PATHINFO_EXTENSION );
            if (!file_exists("$maildir/Poster.{$poster_ext}")) {
                $data=retrieveFileFromURL($this->pruebaObj->Cartel);
                file_put_contents("$maildir/Poster.{$poster_ext}",$data);
            }
        }
        // try to retrieve tryptich into compose directory
        $tryptich_ext="pdf";
        if ($this->pruebaObj->Triptico=="") {
            $this->myLogger->info("No Tryptich declared for prueba {$this->pruebaObj->ID} {$this->pruebaObj->Nombre}");
        }else {
            set_time_limit($timeout);
            // get extension for file to be downloaded
            $tryptich_ext=pathinfo( parse_url($this->pruebaObj->Triptico,PHP_URL_PATH), PATHINFO_EXTENSION );
            if (!file_exists("$maildir/Triptico.{$tryptich_ext}")) {
                $data=retrieveFileFromURL($this->pruebaObj->Triptico);
                file_put_contents("$maildir/Triptico.{$tryptich_ext}",$data);
            }
        }
        // check for empty template mark request and retrieve excel file
        $empty=intval(http_request("EmptyTemplate","i","0"));
        $excelclub=( $empty!=0 )? $club:0;
        if ( ! file_exists("$maildir/Inscripciones_{$excelclub}.xlsx") ) {
            $excelObj=new Excel_Inscripciones($this->pruebaObj->ID,$excelclub);
            $excelObj->open("$maildir/Inscripciones_${excelclub}.xlsx");
            $excelObj->composeTable();
            $excelObj->close();
        }
        // ok: download files is done. Now comes prepare and send mail

        // Configure email
        $myMailer = new PHPMailer; //Create a new PHPMailer instance
        $this->setup_mailer_from_config($myMailer); // myMailer is passed by reference

        // compose a dummy message to be sent to sender :-)
        //Set who the message is to be sent to
        $myMailer->addAddress($email);
        //Set the subject line to Contest Name
        $myMailer->Subject = $this->pruebaObj->Nombre;

        // prepare message body
        $d=date("Y/m/d H:i");
        $htmlmsg="<h4>Test</h4><p>Just a simple <em>HTML</em> text to test send mail in this format</p><p>Mail sent at:$d</p><hr/>";
        $htmlmsg=http_request("Contents","s",$htmlmsg);
        $version = $this->myConfig->getEnv("version_name");
        $release = $this->myConfig->getEnv("version_date");
        $htmlmsg .= "<hr/><p>". _("Email sent with") .  "AgilityContest-$version $release at $d</p> ";
        $htmlmsg .= "<p>CopyRight &copy; 2013-2017 by Juan Antonio Martinez &lt; jonsito at gmail dot com &gt;</p>";
        $myMailer->msgHTML($htmlmsg);
        // set plain text to notify to use an html-enabled email browser
        $myMailer->AltBody = _("Please enable HTML view in your email application");

        // iterate on directory to search for files to attach into mail
        $files= array ( "Poster.{$poster_ext}", "Triptico.{$tryptich_ext}","Inscripciones_{$excelclub}.xlsx" );
        foreach ( $files as $file ) {
            if (!file_exists("$maildir/$file")) continue;
            $this->myLogger->trace("Attaching file: $maildir/$file");
            $myMailer->addAttachment("$maildir/$file",$file);
        }
        // allways attach AgiltiyContest logo . use absolute paths as phpmailer does not handle relative ones
        $myMailer->addAttachment(__DIR__.'/../../images/logos/agilitycontest.png');
        //send the message, check for errors
        if (!$myMailer->send()) {
            $this->myLogger->error($myMailer->ErrorInfo);
            $this->myLogger->leave();
            return "Mailer Error: " . $myMailer->ErrorInfo;
        }
        // if send mail gets ok, mark club sent in prueba
        $res=list_insert($club,$this->pruebaObj->MailList);
        $str="UPDATE Pruebas SET MailList='{$res}' WHERE ID={$this->pruebaObj->ID}";
        $this->myDBObj->query($str);
        $this->pruebaObj->MailList=$res;
        $this->myLogger->leave();
        return "";
    }

    // send results scores and pdf to judge and federation
    public function sendResults($jornada) {
        $this->myLogger->enter();
        $timeout=ini_get('max_execution_time');
        $maildir=__DIR__."/../../../logs/results_{$this->pruebaObj->ID}_{$jornada}";
        // create compose directory. ignore errors if file already exists
        @mkdir($maildir,0777,true); // create subdirectories

        // generate pdf files

        // generate excel file
        $excelObj=new Excel_Clasificaciones($this->pruebaObj->ID);
        $excelObj->open("$maildir/Resultados.xlsx");
        $excelObj->composeTable();
        $excelObj->close();

        // configure mail
        $myMailer = new PHPMailer; //Create a new PHPMailer instance
        $this->setup_mailer_from_config($myMailer); // myMailer is passed by reference

        // add judges in rcpt_to

        // if requested add federation in Cc:

        // prepare message body
        $d=date("Y/m/d H:i");
        $htmlmsg="<h4>Results</h4><p>Here comes <em>Results and Scores</em> for journey $jornada</p><p>Mail sent at:$d</p><hr/>";
        $htmlmsg=http_request("Contents","s",$htmlmsg);
        $version = $this->myConfig->getEnv("version_name");
        $release = $this->myConfig->getEnv("version_date");
        $htmlmsg .= "<hr/><p>". _("Email sent with") .  "AgilityContest-$version $release at $d</p> ";
        $htmlmsg .= "<p>CopyRight &copy; 2013-2017 by Juan Antonio Martinez &lt; jonsito at gmail dot com &gt;</p>";
        $myMailer->msgHTML($htmlmsg);
        // set plain text to notify to use an html-enabled email browser
        $myMailer->AltBody = _("Please enable HTML view in your email application");

        // attach files

        // allways attach AgiltiyContest logo . Use absolute paths as phpmailer does not handle relative ones
        $myMailer->addAttachment(__DIR__.'/../../images/logos/agilitycontest.png');
        //send the message, check for errors
        $ret="";
        if (!$myMailer->send()) {
            $this->myLogger->error($myMailer->ErrorInfo);
            $ret= "Mailer Error: " . $myMailer->ErrorInfo;
        }

        // clear temporary directory to remove pdf's and excels

        $this->myLogger->leave();
        return $ret;
    }

    // send some report to www.agilitycontest.es
    public function notify() {
        $this->myLogger->enter();
        // PENDING
        $this->myLogger->leave();
        return "";
    }

}