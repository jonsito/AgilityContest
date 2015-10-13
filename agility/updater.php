<?php
/*
upgrade.php

 Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
    }
    fclose($fp);
}

Class AgilityContestUpdater {
    var $version_name="1.0.0";
    var $version_date="20150101_0000";
    var $temp_file=TEMP_FILE;

    // list of files to be preserved across updates
    public static $user_files = array (
        "config.ini" => __DIR__."/../server/auth/config.ini",
        "registration.info" => __DIR__."/../server/auth/registration.info",
        "supporters.csv" => __DIR__."/images/supporters/supporters.csv"
    );

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
            $this->logProgress($str);
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
        // if enabled, use standard file_get_contents
        if (ini_get('allow_url_fopen') == true) {
            return file_get_contents($url);
        }
        // if not enable, try curl
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
            curl_setopt($ch, CURLOPT_CAINFO, __DIR__."/server/auth/cacert.pem");
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,$timeout);
            curl_setopt($ch, CURLOPT_URL, $url);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
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
    }

    public function getVersionName() { return $this->version_name; }

    public function getVersionDate() { return $this->version_date; }

    public function handleConfig($oper) {
        set_time_limit(ini_get('max_execution_time'));
        $res=true;
        foreach (AgilityContestUpdater::$user_files as $temp => $file) {
            $from = ($oper == true) ? $file : TEMP_DIR . $temp;
            $to = ($oper == true) ? TEMP_DIR . $temp : $file;
            $str = ($oper == true) ? "BACKUP: " : "RESTORE: ";
            // if $from doesn't exist notify and continue
            if (!file_exists($from)) {
                $this->logProgress("SKIP: $str $temp");
                continue;
            };
            // else try to copy
            $a = file_get_contents($from);
            $f = fopen($to, "w");
            if (!is_resource($f)) {
                $this->logProgress("WARNING: $str $temp");
                $res = false;
                continue;
            }
            fwrite($f, $a);
            fclose($f);
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
            $dir_name = dirname($file_name);
            // skip directories in zip file
            if ( substr($file_name,-1,1) == '/') continue; // not a file
            if ( strstr($file_name,"jquery-easyui")) continue; // skip easyui related files
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
                } else  $this->logProggress("FAILED: $oper $file_name");
                unset($contents); // clear data from memory
            }
        }
        // finally, if a post_install.php file is present, parse and execute it
        if (file_exists(POST_INSTALL)) {
            $this->logProgress("EXECUTE: post-install.php");
            include(POST_INSTALL);
            unlink(POST_INSTALL);
        }
        zip_close($zip);
        return true;
    }
};

// allow only localhost access
$white_list= array ("localhost","127.0.0.1","::1",$_SERVER['SERVER_ADDR']);
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
    json_encode($res);
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
unlink(TEMP_DIR."do_upgrade"); // remove "need-to-upgrade" mark
ob_end_clean();
header("Connection: close");
ignore_user_abort(); // optional
ob_start();

// generamos la pantalla principal una vez arrancado en background el actualizador
?>
<html>
    <head>
        <title>Actualizador de AgilityContest</title>
        <style>
            textarea {
                -moz-box-sizing:border-box;
                box-sizing:border-box;
                width:276px;
                height:172px;
                padding:20px;
                overflow:auto;
                border:none;
                background:#fff url(/agility/images/AgilityContest.png) no-repeat center scroll;
                background-size:100% 100%;
            }
        </style>
        <script type="text/javascript" charset="utf-8">
            function fireUpdater() {
                $.ajax({
                    type:'GET',
                    url:"/agility/updater.php",
                    dataType:'json',
                    data: {
                        Operation:	'progress'
                    },
                    success: function(res) {
                        setTimeout(fireUpdater,2500); // call myself in 2.5 seconds
                    },
                    error: function(XMLHttpRequest,textStatus,errorThrown) {
                        $.messager.alert("Restricted","Error: "+textStatus + " "+ errorThrown,'error' );
                    }
                });
            }
        </script>
    </head>
    <body onload="fireUpdater();">
        <h3>Updating AgilityContest...</h3>
        <h1>New version: <?php echo $up->getVersionName()." - ".$up->getVersionDate(); ?> </h1>
        <form id="updater" name="updater">
            <label for="progress">Progress status:</label>
            <textarea id="progress" form="updater" name="progress" cols="80" rows="40" readonly="readonly"></textarea>
            <input type="button" name="Done" value="Done"
        </form>
    </body>
</html>

<?php

// arrancamos actualizador cerrando la conexion con el navegador y dejando esto como tarea en background
ob_end_flush(); // Strange behaviour, will not work
flush();        // Unless both are called !
session_write_close(); // Added a line suggested in the comment

set_time_limit(ini_get('max_execution_time'));
$up = new AgilityContestUpdater();
$res=$up->downloadFile(false);
if ($res===FALSE) { $up->logProgress("FATAL: Download failed"); return; }
$res=$up->handleConfig(true); // backup
if ($res===FALSE) { $up->logProgress("NOTICE: Backup configuration failed"); return;}
$res=$up->doUpgrade();
if ($res===FALSE) { $up->logProgress("FATAL: Upgrade failed"); return; }
$res =$up->handleConfig(false); // restore
if ($res===FALSE) { $up->logProgress("NOTICE: Restore configuration failed"); return;}
$up->logProgress("DONE: Upgrade to Version: {$up->getVersionName()} Revision: {$up->getVersionDate()} ready");

?>