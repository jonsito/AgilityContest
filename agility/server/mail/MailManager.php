<?php

require_once __DIR__.'/PHPMailer-5.2.22/PHPMailerAutoload.php';
require_once __DIR__.'/../auth/Config.php';
require_once __DIR__.'/../auth/AuthManager.php';
require_once __DIR__.'/../database/classes/Mangas.php';
require_once __DIR__.'/../database/classes/Resultados.php';
require_once __DIR__.'/../excel/classes/Excel_Inscripciones.php';
require_once __DIR__.'/../excel/classes/Excel_Clasificaciones.php';
require_once __DIR__.'/../pdf/classes/PrintInscripciones.php';
require_once __DIR__.'/../pdf/classes/PrintResultadosByEquipos3.php';
require_once __DIR__.'/../pdf/classes/PrintResultadosByEquipos4.php';
require_once __DIR__.'/../pdf/classes/PrintResultadosByManga.php';
require_once __DIR__.'/../pdf/classes/PrintClasificacion.php';
require_once __DIR__.'/../pdf/classes/PrintClasificacionTeam.php';
require_once __DIR__.'/../web/PublicWeb.php';
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
    protected $jornadaObj;
    protected $myData; // argumentos recibidos por http_request

    public function __construct($filename,$am,$data) {
        $this->myAuthManager=$am;
        $this->myConfig=Config::getInstance();
        $this->myLogger= new Logger($filename,$this->myConfig->getEnv("debug_level"));
        $this->myData=$data;
        $this->myDBObj=new DBOBject("MailManager::Enumerate");
        $this->pruebObj=null;
        if ($this->myData['Prueba']!=0)
            $this->pruebaObj=$this->myDBObj->__selectObject("*","Pruebas","ID={$this->myData['Prueba']}");
        if ($this->myData['Jornada']!=0)
            $this->jornadaObj=$this->myDBObj->__selectObject("*","Jornadas","ID={$this->myData['Jornada']}");
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
        $myMailer->Host = $this->myConfig->getEnv("email_server");
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $myMailer->Port = intval($this->myConfig->getEnv("email_port"));
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
        // $myMailer->Host = gethostbyname($this->myData["email_server"],"s","127.0.0.1"));
        $myMailer->Host = $this->myData['email_server'];
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $myMailer->Port = intval($this->myData["email_port"]);
        $crypt=$this->myData["email_crypt"];
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
        $myMailer->AuthType = $this->myData["email_auth"];
        $myMailer->SMTPAuth = ($myMailer->AuthType == "PLAIN" )?false:true;
        //Username to use for SMTP authentication - use full email address for gmail
        $myMailer->Username = $this->myData["email_user"];
        $myMailer->Password = $this->myData["email_pass"];
        $myMailer->Realm = $this->myData["email_realm"];
        $myMailer->Workstation = $this->myData["email_workstation"];
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
    public function enumerateClubes() {
        $this->myLogger->enter();
        $curFederation=Federations::getFederation(intval($this->pruebaObj->RSCE));
        // evaluate search query string
        $q=$this->myData["q"];
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
    public function enumerateJueces() {
        $this->myLogger->enter();
        if ($this->myData['Jornada']<=0) return $this->error("enumerateJuecesByJornada(): Invalid Jornada ID: {$this->myData['Jornada']}");
        // evaluate search query string
        $q=$this->myData["q"];
        // evaluate judge list by parsing rounds in journey
        $jueces=$this->myDBObj->__select("Juez1,Juez2","Mangas","Jornada={$this->myData['Jornada']}");
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
     * Clear every preloaded files ( Poster, Triptych and so, to force reload
     */
    function clearMailCache() {
        $maildir=__DIR__."/../../../logs/mail_{$this->pruebaObj->ID}";
        array_map('unlink',glob("{$maildir}/*"));
        return "";
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
     * @return {string} empty on success, else error msg
     */
    public function updateClubMail() {
        if ($this->myData['Club']<=1)
            throw new Exception("updateClubMail(): Invalid Club ID {$this->myData['Club']}");
        if (!filter_var($this->myData['Email'],FILTER_VALIDATE_EMAIL))
            throw new Exception ("updateClubMail() provided data `{$this->myData['Email']}` is not a valid email address");
        $str="UPDATE Clubes SET Email='{$this->myData['Email']}' WHERE ID={$this->myData['Club']}";
        $res=$this->myDBObj->query($str);
        if (!$res) return $this->myDBObj->error($this->myDBObj->conn->error);
        return "";
    }

    /**
     * Update juez email with provided data
     * @return {string} empty on success, else error msg
     */
    public function updateJuezMail() {
        if ($this->myData['Juez']<=1)
            throw new Exception("updateJuezMail(): Invalid Juez ID {$this->myData['Juez']}");
        if (!filter_var($this->myData['Email'],FILTER_VALIDATE_EMAIL))
            throw new Exception ("updateJuezbMail() provided data `{$this->myData['Email']}` is not a valid email address");
        $str="UPDATE Jueces SET Email='{$this->myData['Email']}' WHERE ID={$this->myData['Juez']}";
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
    public function sendInscriptions() {
        $this->myLogger->enter();
        $timeout=ini_get('max_execution_time');
        $maildir=__DIR__."/../../../logs/mail_{$this->pruebaObj->ID}";
        $this->myLogger->trace("Sending mail for club:`{$this->myData['Club']}` to address:`{$this->myData['Email']}`");
        if ($this->myData['Email']=="") return "Error: no email address set";

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
        $empty=intval($this->myData["EmptyTemplate"]);
        $excelclub=( $empty!=0 )? $this->myData['Club']:0;
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
        $myMailer->addAddress($this->myData['Email']);
        //Set the subject line to Contest Name
        $myMailer->Subject = $this->pruebaObj->Nombre;

        // prepare message body
        $d=date("Y/m/d H:i");
        $htmlmsg="<h4>Test</h4><p>Just a simple <em>HTML</em> text to test send mail in this format</p><p>Mail sent at:$d</p><hr/>";
        $htmlmsg=$this->myData["Contents"];
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
        $res=list_insert($this->myData['Club'],$this->pruebaObj->MailList);
        $str="UPDATE Pruebas SET MailList='{$res}' WHERE ID={$this->pruebaObj->ID}";
        $this->myDBObj->query($str);
        $this->pruebaObj->MailList=$res;
        $this->myLogger->leave();
        return "";
    }

    /**
     * Creates a compressed zip file
     * @param {string} $maildir base directory to retrieve files
     * @param {array} $files file list
     * @param {string} $destination Destinantion zipfile name
     * @param {bool} $overwrite true to override zip file
     * @return bool true on success, else false
     */
    function create_zip($maildir,$files = array(),$destination = '') {

        $valid_files = array(); // where to store valid files to insert into zip
        if(is_array($files)) {
            //cycle through each file
            foreach($files as $file) {
                if(file_exists("{$maildir}/{$file}"))  $valid_files[] = $file;  //make sure the file exists
            }
        }
        if(count($valid_files)) { // we have some files to insert into zip, so process
            //create the archive
            $zip = new ZipArchive();
            @unlink($destination);
            $res=$zip->open($destination,ZipArchive::CREATE);
            if($res!== true) { // allways clear archive if exists
                $this->myLogger->trace( "canot zipfile open() {$destination} Error code: $res");
                return false;
            }
            //add the files
            foreach($valid_files as $file)  {
                // $this->myLogger->trace("Adding {$file} to zipfile");
                $zip->addFile("{$maildir}/{$file}",$file);
            }
            //close the zip -- done!
            $zip->close();
            //check to make sure the file exists
            return file_exists($destination);
        }
        // arriving here means no files to add to zipfile, so no zipfile created
        $this->myLogger->trace("No files to add to zipfile");
        return false;
    }

    /**
     * Create all requested result files files
     * @return {array} file list
     */
    private function create_result_files($maildir) {

        $timeout=ini_get('max_execution_time');
        // create compose directory. ignore errors if file already exists
        @mkdir($maildir,0777,true); // create subdirectories

        // generate pdf files
        $filelist=array();
        // ask for availabla rounds/series in this journey
        $pb= new PublicWeb($this->myData['Prueba'],$this->myData['Jornada']);
        $pbdata=$pb->publicweb_deploy();
        foreach ($pbdata['Jornadas'] as $j) { //buscamos la nuestra en la lista de jornadas
            if ($j['ID']!=$this->myData['Jornada']) continue;
            set_time_limit($timeout);
            // clasificacion final
            foreach($j['Series'] as $s) {
                $mangas=array(
                    intval($s['Manga1']), intval($s['Manga2']), intval($s['Manga3']), intval($s['Manga4']),
                    intval($s['Manga5']), intval($s['Manga6']), intval($s['Manga7']), intval($s['Manga8'])
                );
                $cobj=new Clasificaciones("EmailClasificaciones",$this->pruebaObj->ID,$this->jornadaObj->ID);
                switch(intval($s['Tipo1'])) {
                    case 8: case 9: case 13: case 14: // prueba por equipos
                        $clasificaciones=$cobj->clasificacionFinalEquipos($s['Rondas'],$mangas,$s['Mode']);
                        $pdf = new PrintClasificacionTeam($this->pruebaObj->ID,$this->jornadaObj->ID,$mangas,$clasificaciones,$s['Mode']);
                        break;
                    case 1: case 2: // pre-agility
                        if ($this->myData['SendPreAgility']==0) continue;
                        // no break
                    default: // individual
                        $clasificaciones=$cobj->clasificacionFinal($s['Rondas'],$mangas,$s['Mode']);
                        $pdf = new PrintClasificacion($this->pruebaObj->ID,$this->jornadaObj->ID,$mangas,$clasificaciones,$s['Mode']);
                        break;
                }
                // Creamos generador de documento
                $pdf->AliasNbPages();
                $pdf->composeTable();
                $pdfname=str_replace(" ","_",$s['Nombre']);
                $pdfname=str_replace("_-_","_",$pdfname); // prettyformat file name from "manga - categoria"
                array_push($filelist,"{$pdfname}.pdf");
                $pdf->Output("$maildir/$pdfname.pdf","F"); // "F" means save to file; "D" send to client; "O" store in variable
            }

            // check for need to generate partial scores
            if ($this->myData['PartialScores']==0) continue;

            foreach ($j['Mangas'] as $m) {
                set_time_limit($timeout);
                // analizamos la lista de rondas de la jornada GIstd,GIms,etc
                // $m['ID'] es de la forma "mangaid,mode"; el ID viene en $m['Manga']
                $mngobj= new Mangas("EmailResultadosByManga",$this->myData['Jornada']);
                $manga=$mngobj->selectByID($m['Manga']);
                $resobj= new Resultados("EmailResultadosByManga",$this->myData['Prueba'],$m['Manga']);
                switch(intval($m['TipoManga'])) {
                    // miramos si es una prueba por equipos
                    case 8: case 13:
                        $resultados=$resobj->getResultadosEquipos($m['Mode']);
                        $pdf=new PrintResultadosByEquipos3($this->myData['Prueba'],$this->myData['Jornada'],$manga,$resultados,$m['Mode']);
                        break;
                    case 9: case 14:
                        $resultados=$resobj->getResultadosEquipos($m['Mode']);
                        $pdf=new PrintResultadosByEquipos4($this->myData['Prueba'],$this->myData['Jornada'],$manga,$resultados,$m['Mode']);
                        break;
                    default:
                        $resultados=$resobj->getResultados($m['Mode']);
                        $pdf=new PrintResultadosByManga($this->myData['Prueba'],$this->myData['Jornada'],$manga,$resultados,$m['Mode']);
                        break;
                }
                $pdf->AliasNbPages();
                $pdf->composeTable();
                $pdfname=str_replace(" ","_",$m['Nombre']);
                $pdfname=str_replace("_-_","_",$pdfname); // prettyformat file name from "manga - categoria"
                array_push($filelist,"{$pdfname}.pdf");
                $pdf->Output("$maildir/$pdfname.pdf","F"); // "D" means open download dialog
            }
        }

        // Datos de inscripciones en PDF
        $jmgr= new Jornadas("printInscritosByPrueba",$this->myData['Prueba']);
        $jornadas=$jmgr->selectByPrueba();
        $inscripciones = new Inscripciones("printInscritosByPrueba",$this->myData['Prueba']);
        $inscritos= $inscripciones->enumerate();
        $pdf=new PrintInscritosByJornada($this->myData['Prueba'],$inscritos,$jornadas,$this->myData['Jornada']);
        $pdf->AliasNbPages();
        $pdf->composeTable();
        $pdf->Output("$maildir/Inscripciones.pdf","F"); // "F" means output to file
        array_push($filelist,"Inscripciones.pdf");

        // generate excel clasifications file
        $excelObj=new Excel_Clasificaciones($this->myData['Prueba']);
        $excelObj->open("$maildir/Clasificaciones.xlsx");
        $excelObj->composeTable();
        $excelObj->close();
        array_push($filelist,"Clasificaciones.xlsx");

        // generate excel inscriptions file
        $excelObj=new Excel_Inscripciones($this->myData['Prueba'],0); // 0 means everyone
        $excelObj->open("$maildir/Inscripciones.xlsx");
        $excelObj->composeTable();
        $excelObj->close();
        array_push($filelist,"Inscripciones.xlsx");

        // return list of composed files
        $this->myLogger->trace("filelist is ".json_encode($filelist));
        return $filelist;
    }

    public function getZipFile() {
        $this->myLogger->enter();
        $maildir=__DIR__."/../../../logs/results_{$this->myData['Prueba']}_{$this->myData['Jornada']}";
        // create files
        $filelist=$this->create_result_files($maildir);
        // compose zipfile
        $zipfile="ResultsAndScores.zip";
        $res=$this->create_zip($maildir,$filelist,"{$maildir}/{$zipfile}");
        if (!$res) throw new Exception ("Mailer error: cannot create zip file");
        // download to client
        header('Set-Cookie: fileDownload=true; path=/');
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=$zipfile");
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile("{$maildir}/{$zipfile}");
        // clear temporary data
        array_map('unlink',glob("{$maildir}/*.pdf"));
        array_map('unlink',glob("{$maildir}/*.xlsx"));
        $this->myLogger->leave();
        return ""; // result will be ignored due to file download operation
    }

    // send results scores and pdf to judge and federation
    public function sendResults() {
        $this->myLogger->enter();
        $prueba=$this->pruebaObj->ID;
        $maildir=__DIR__."/../../../logs/results_{$this->myData['Prueba']}_{$this->myData['Jornada']}";
        // create files
        $filelist=$this->create_result_files($maildir);

        // configure mail
        $myMailer = new PHPMailer; //Create a new PHPMailer instance
        $this->setup_mailer_from_config($myMailer); // myMailer is passed by reference
        $myMailer->Subject = _('Results').": {$this->pruebaObj->Nombre} -  {$this->jornadaObj->Nombre}";
        // add judges in rcpt_to ( from client we receive comma separated list of judges
        $list=$this->myData['Email'];
        $jueces=explode(',',$list);
        foreach ($jueces as $juez ) $myMailer->addAddress($juez);

        // if requested add federation in Cc:
        if ( $this->myData['SendToFederation'] != 0) {
            $fedm=$this->myData["FedAddress"];
            if ($fedm!="") $myMailer->addCC($fedm);
        }

        // prepare message body
        $d=date("Y/m/d H:i");
        $htmlmsg=$this->myData["Contents"];
        if ($htmlmsg=="")
            $htmlmsg="<h4>Results</h4><p>Here comes <em>Results and Scores</em> for journey {$this->myData['Jornada']}</p><p>Mail sent at:$d</p><hr/>";
        $version = $this->myConfig->getEnv("version_name");
        $release = $this->myConfig->getEnv("version_date");
        $htmlmsg .= "<hr/><p>". _("Email sent with") .  "AgilityContest-$version $release at $d</p> ";
        $htmlmsg .= "<p>CopyRight &copy; 2013-2017 by Juan Antonio Martinez &lt; jonsito at gmail dot com &gt;</p>";
        $myMailer->msgHTML($htmlmsg);
        // set plain text to notify to use an html-enabled email browser
        $myMailer->AltBody = _("Please enable HTML view in your email application");

        if($this->myData['ZipFile']!=0) {
            $zipfile="ResultsAndScores.zip";
            $res=$this->create_zip($maildir,$filelist,"{$maildir}/{$zipfile}");
            if (!$res) return "Mailer error: cannot create zip file";
            $myMailer->addAttachment("{$maildir}/{$zipfile}",$zipfile);
        } else {
            // attach files
            foreach($filelist as $file) {
                $this->myLogger->trace("Attaching file $file");
                $myMailer->addAttachment("{$maildir}/{$file}",$file);
            }
        }

        // allways attach AgiltiyContest logo . Use absolute paths as phpmailer does not handle relative ones
        $myMailer->addAttachment(__DIR__.'/../../images/logos/agilitycontest.png');
        //send the message, check for errors
        $ret="";

        if (!$myMailer->send()) {
            $this->myLogger->error($myMailer->ErrorInfo);
            $ret= "Mailer Error: " . $myMailer->ErrorInfo;
        }

        // clear temporary directory to remove pdf's and excels
        array_map('unlink',glob("{$maildir}/*"));

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