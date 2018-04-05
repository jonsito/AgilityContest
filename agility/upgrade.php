<?php
/*
upgrade.php

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

// Github redirects links, and make curl fail.. so use real ones
// define ('UPDATE_INFO','https://github.com/jonsito/AgilityContest/raw/master/agility/server/auth/system.ini');
// define ('UPDATE_FILE','https://github.com/jonsito/AgilityContest/archive/master.zip');
define ('UPDATE_INFO','https://raw.githubusercontent.com/jonsito/AgilityContest/master/agility/server/auth/system.ini');
define ('UPDATE_FILE','https://codeload.github.com/jonsito/AgilityContest/zip/master');
define ('TEMP_FILE', __DIR__."/../logs/AgilityContest-");
define ('LOG_FILE', __DIR__."/../logs/update.log");
define ('PROGRESS_FILE', __DIR__."/../logs/progress.log");
define ('TEMP_DIR', __DIR__."/../logs/");
define ('CONFIG_DIR', __DIR__."/../server/auth/");
define ('POST_INSTALL', __DIR__."/../post-install.php");

function logTrace($str) {
    $fp=fopen(LOG_FILE,"a");
    if (is_resource($fp)) {
        $fecha=date("Ymd_His:");
        if (flock($fp, LOCK_EX)) {  // adquirir un bloqueo exclusivo
            fwrite($fp, $fecha.":". $str."\n");
            fflush($fp);     // volcar la salida antes de liberar el bloqueo
            flock($fp, LOCK_UN);    // libera el bloqueo
        }
        fclose($fp);
    }
}

/**
 * Try to get a file from url
 * Depending on config try several methods
 *
 * @param $url file URL to retrieve
 */
function retrieveFileFromURL($url) {
    // if enabled, use standard file_get_contents
    if (ini_get('allow_url_fopen') == true) {
        $res=file_get_contents($url);
        // on fail, try to use old way to retrieve data
        if ($res!==FALSE) return $res;
        echo "file_getContents() failed<br/>";
    }
    // if not enable, try curl
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        if ($ch===FALSE) { echo "curl_init() failed:"; return FALSE; }
        $timeout = 5;
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_CAINFO, __DIR__."/server/auth/cacert.pem");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,$timeout);
        $data = curl_exec($ch);
        if ($data===FALSE) { echo "curl_exec() failed:".curl_error($ch)."<br/>"; return FALSE; }
        curl_close($ch);
        return $data;
    }
    // arriving here means error
    return FALSE;
}

Class AgilityContestUpdater {
    var $version_name="1.0.0";
    var $version_date="20150101_0000";
    var $temp_file=TEMP_FILE;
    var $user_files=null;// list of files to be preserved across updates

    function logProgress($str) {
        $res=true;
        $fp=fopen(PROGRESS_FILE,"a");
        if ($fp===false) {
            logTrace('ERROR: logProgress(): fopen() failed');
            return false;
        }
        if (flock($fp, LOCK_EX)) {  // adquirir un bloqueo exclusivo
            fwrite($fp,$str."\n");
            fflush($fp);     // volcar la salida antes de liberar el bloqueo
            flock($fp, LOCK_UN);    // libera el bloqueo
            logTrace($str);
        } else {
            $res=false;
            logTrace('ERROR: logProgress(): flock() failed');
        }
        fclose($fp);
        return $res;
    }

    /**
     * A replacement for file_get_contents to bypass
     * sites where allow_url_fopen is disabled in php.ini
     *
     * @param $url
     * @return {array} readed data
     */
    private function file_get($url) {
        $timeout = 300;
        set_time_limit(350);
        $res=retrieveFileFromURL($url);
        if ($res!==FALSE) return $res;
        // arriving here means no way to load file from remote site
        die( 'Cannot retrieve update information. Check your internet connection');
    }

    // retrieve file from url and store as local one
    private function file_save($remote,$local) {
        $ch = curl_init();
        $fp = fopen ($local, 'w+');
        curl_setopt($ch, CURLOPT_URL, $remote);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_CAINFO, __DIR__."/server/auth/cacert.pem");
        set_time_limit(350);
        $res=curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $res;
    }

    public function __construct() {
        $info = $this->file_get(UPDATE_INFO);
        $info = str_replace("\r\n", "\n", $info);
        $info = str_replace(" ", "", $info);
        $data = explode("\n",$info);
        foreach ($data as $line) {
            if (strpos($line,"version_name=")===0) $this->version_name = trim(substr($line,13),'"');
            if (strpos($line,"version_date=")===0) $this->version_date = trim(substr($line,13),'"');
        }
        $this->logProgress("Version name: {$this->version_name}");
        $this->logProgress("Version date: {$this->version_date}");
        $this->temp_file=TEMP_FILE . $this->version_date . ".zip";
        $this->user_files = array (
            "config.ini" => __DIR__."/server/auth/config.ini",
            "registration.info" => __DIR__."/server/auth/registration.info",
            "supporters.csv" => __DIR__."/images/supporters/supporters.csv"
        );
    }

    public function getVersionName() { return $this->version_name; }

    public function getVersionDate() { return $this->version_date; }

    /**
     * Clear tmp files
     */
    public function prepare() {
        mkdir(TEMP_DIR);
        // clear old config files from tmpdir
        foreach ($this->user_files as $temp => $file) {
            if (file_exists(TEMP_DIR.$temp)) unlink(TEMP_DIR.$temp);
        }
        // clear log and progress
        if (file_exists(LOG_FILE) ) unlink(LOG_FILE);
        if (file_exists(PROGRESS_FILE) ) unlink(PROGRESS_FILE);
        return true;
    }

    /**
     * backup/restore configuration files
     * @param $oper true:backup false:restore
     * @return bool true:success false:fail
     */
    public function handleConfig($oper) {
        set_time_limit(ini_get('max_execution_time'));
        $res=true;
        foreach ($this->user_files as $temp => $file) {
            $from = ($oper == true) ? $file : TEMP_DIR . $temp;
            $to = ($oper == true) ? TEMP_DIR . $temp : $file;
            $str = ($oper == true) ? "BACKUP: " : "RESTORE: ";
            // if $from doesn't exist notify and continue
            if (!file_exists($from)) {
                $this->logProgress("SKIP $str $temp");
                continue;
            };
            // else try to copy. We can use copy() cause both files are local
            $res=copy($from,$to);
            if ($res===FALSE) {
                $this->logProgress("WARNING: $str $temp");
                $res = false;
                continue;
            }
            $this->logProgress($str.$temp);
        }
        return $res;
    }

    public function downloadFile($force=false) {
        set_time_limit(ini_get('max_execution_time'));
        if (file_exists($this->temp_file) && $force==false) return 0; // no need to download
        $this->logProgress("DOWNLOAD: ".UPDATE_FILE);
        return $this->file_save(UPDATE_FILE,$this->temp_file);
    }

    public function doUpgrade() {
        $root=__DIR__ . "/../";
        // open zip file
        $zip = zip_open($this->temp_file);
        if (! is_resource($zip) ) { $this->logProgress("ERROR: zipfile failed errno: $zip"); return false; }
        while ($aF = zip_read($zip) ) {
            set_time_limit(ini_get('max_execution_time'));
            // get file name and their directory
            $file_name = str_replace("AgilityContest-master/","",zip_entry_name($aF));
            $file_size = zip_entry_filesize($aF);
            $dir_name = dirname($file_name);
            // skip directories in zip file
            if ( substr($file_name,-1,1) == '/') continue; // not a file
            if ( strstr($file_name,"jquery-easyui")) {
                // only update library files on file change
                if (file_exists($root.$file_name)) {
                    if (filesize($root.$file_name)==$file_size) continue; // same size
                }
            } // skip easyui related files
            //Make the directory if we need to...
            if ( !is_dir ( $root . $dir_name ) ) {
                $res=mkdir ( $root . $dir_name );
                if (!$res) $this->logProgress("FAILED MAKEDIR: $dir_name");;
                $this->logProgress("MAKEDIR: $dir_name");
            }
            // create/overwrite file
            if ( !is_dir($root.$file_name) ) {
                $contents = zip_entry_read($aF, zip_entry_filesize($aF));
                $oper=(file_exists($root.$file_name))?"UPDATE":"CREATE";
                $file = fopen($root.$file_name, 'w');
                if (is_resource($file)) {
                    fwrite($file, $contents);
                    fclose($file);
                    $this->logProgress("$oper $file_name");
                } else  $this->logProgress("FAILED $oper $file_name");
                unset($contents); // clear data from memory
            }
        }
        // finally, if a post_install.php file is present, parse and execute it
        if (file_exists(POST_INSTALL)) {
            $this->logProgress("EXECUTE: post-install.php");
            include(POST_INSTALL);
            @unlink(POST_INSTALL);
        }
        zip_close($zip);
        return true;
    }
};

// allow only localhost access
$white_list= array ("localhost","127.0.0.1","::1",$_SERVER['SERVER_ADDR'],"138.4.4.108");
if (!in_array($_SERVER['REMOTE_ADDR'],$white_list)) {
    die("<p>Esta operacion debe ser realizada desde la consola del servidor</p></pre>");
}

// si la peticion incluye el argumento "progress" actualiza datos de la barra de progreso y retorna
if (isset($_REQUEST["Operation"]) && ($_REQUEST["Operation"]==="progress" ) ) {
    $data="";
    $fp=fopen(PROGRESS_FILE,"r+");
    if ( !$fp || flock($fp, LOCK_EX)) {  // adquirir un bloqueo exclusivo
        $len=filesize(PROGRESS_FILE);
        if ($len>0) {
            // leemos contenido del fichero
            $data=fread($fp,$len);
            // truncamos fichero
            ftruncate($fp, 0);
            fflush($fp);     // volcar la salida antes de liberar el bloqueo
        }
        flock($fp, LOCK_UN);    // libera el bloqueo
    } else {
        $data="WARNING: fopen()/flock() failed";
    }
    fclose($fp);
    $res=explode("\n",$data);
    echo json_encode($res);
    return 0;
}

// Comprobaciones previas antes de arrancar

$f=fopen(TEMP_DIR."do_upgrade",'r'); // check for previous update request
if ( !$f || !isset($_REQUEST['sessionkey'])) {
    die("<p>Debe solicitar la actualizacion desde el panel de administracion</p></pre>");
}

$sk=fread($f,1024); // check session key
fclose($f);
if ( $sk !== $_REQUEST['sessionkey']) {
    die("<p>No ha proporcionado un identificador de sesion valido</p></pre>");
}

// everything ok.
set_time_limit(ini_get('max_execution_time'));
$up = new AgilityContestUpdater();
@unlink(TEMP_DIR."do_upgrade"); // remove "need-to-upgrade" mark
ob_end_clean();
header("Connection: close");
ignore_user_abort(); // optional
ob_start();
// generamos la pantalla principal una vez arrancado en background el actualizador
echo '
<html>
    <head>
        <title>Actualizador de AgilityContest</title>
        <style>
            .waiting {
                cursor: progress;
            }

            textarea {
                -moz-box-sizing:border-box;
                box-sizing:border-box;
                width:800px;
                height:300px;
                padding:20px;
                overflow:auto;
                border:none;
                background:#eee url(images/AgilityContest.png) no-repeat right scroll;
                background-size:50% 100%;
            }
        </style>
        <script src="lib/jquery-2.2.4.min.js" type="text/javascript" charset="utf-8" > </script>
        <script type="text/javascript" charset="utf-8">
            jQuery.fn.putCursorAtEnd = function() {
                return this.each(function() {
                    $(this).focus();
                    // If this function exists...
                    if (this.setSelectionRange) {
                        // ... then use it (Doesnt work in IE)
                        // Double the length because Opera is inconsistent about whether a carriage return is one character or two. Sigh.
                        var len = $(this).val().length * 2;
                        this.setSelectionRange(len, len);

                    } else {
                        // ... otherwise replace the contents with itself
                        // (Doesnt work in Google Chrome)
                        $(this).val($(this).val());
                    }
                    // Scroll to the bottom, in case we are in a text area
                    // (Necessary for Firefox and Google Chrome)
                    this.scrollTop = 99999;
                });
            };

            function fireUpdater() {
                var txarea=$("#progress");
                txarea.blur();
                $.ajax({
                    type:"GET",
                    url:"upgrade.php",
                    dataType:"json",
                    data: {
                        Operation:	"progress"
                    },
                    success: function(data) {
                        var done=false;
                        for(var n=0 ; n<data.length; n++) {
                            var a=data[n].trim();
                            if (a!=="") txarea.val(txarea.val()+"\\n"+a);
                            if (a.indexOf("DONE")==0) done=true;
                            if (a.indexOf("FATAL")==0) done=true;
                        }
                        if (!done) {
                            setTimeout(fireUpdater,500); // call myself in 0.5 second
                        } else {
                            txarea.removeClass("waiting");
                            $("#doneBtn").css("display","inline");
                        }
                        txarea.putCursorAtEnd();
                    },
                    error: function(XMLHttpRequest,textStatus,errorThrown) {
                        alert("fireUpdater Error:"+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus+" - "+errorThrown);
                    }
                });
            }

            function start() {
                $("#progress").addClass("waiting");
                fireUpdater();
            }

            function restart() {
                // document.location.href="https://localhost/agility/console/index.php";
                document.location.href="index.php";
            }

        </script>
    </head>
    <body onload="start();">
        <h2>Updating AgilityContest...</h2>
        <h3>New version: '.$up->getVersionName().' - '.$up->getVersionDate().' </h3>
        <form id="updater" name="updater" action="console/index.php">
            <label for="progress">Progress status:</label><br/>
            <textarea id="progress" form="updater" name="progress" cols="80" rows="40" readonly="readonly"></textarea><br/>
            <span id="doneBtn" style="display:none;">
                Update completed. Press restart to reload AgilityContest
                <input type="button" name="Restart" value="Restart" onclick="restart();">
            </span>

        </form>
    </body>
</html>
';
$size= ob_get_length();
header("Content-Length: $size");
// arrancamos actualizador cerrando la conexion con el navegador y dejando esto como tarea en background
ob_end_flush(); // Strange behaviour, will not work
flush();        // Unless both are called !
ob_end_clean();
session_write_close();

$res=$up->prepare();
$res=$up->downloadFile(false);
if ($res===FALSE) { $up->logProgress("FATAL: Download failed"); return; }
$res=$up->handleConfig(true); // backup
if ($res===FALSE) { $up->logProgress("NOTICE: Backup configuration failed"); return;}
$res=$up->doUpgrade();
if ($res===FALSE) { $up->logProgress("FATAL: Upgrade failed"); return; }
$res =$up->handleConfig(false); // restore
if ($res===FALSE) { $up->logProgress("NOTICE: Restore configuration failed"); return;}
@unlink($up->temp_file); // remove downloaded zip file
$up->logProgress("DONE: Upgrade to Version: {$up->getVersionName()} Revision: {$up->getVersionDate()} ready");

?>