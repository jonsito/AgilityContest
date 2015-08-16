<html>
<head>
    <title>Actualizador de AgilityContest</title>
</head>
<body>
<h3>Actualizando AgilityContest...</h3>
<pre>

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
define ('TEMP_DIR', __DIR__."/../logs/");
define ('CONFIG_DIR', __DIR__."/../server/auth/");
define ('POST_INSTALL', __DIR__."/../post-install.php");

Class AgilityContestUpdater {
    var $version_name="1.0.0";
    var $version_date="20150101_0000";
    var $temp_file=TEMP_FILE;

    /**
     * A replacement for file_get_contents to bypass
     * sites where allow_url_fopen is disabled in php.ini
     *
     * @param $url
     * @return {object}readed data
     */
    private function file_get($url) {
        // if enabled, use standard file_get_contents
        if (ini_get('allow_url_fopen') == true) {
            return file_get_contents($url);
        }
        // if not enable, try curl
        if (function_exists('curl_init')) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        $res=curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $res;
    }

    private function backupConfig($file) {
        if ( ! file_exists(CONFIG_DIR.$file)) return 1;
        $a=file_get_contents(CONFIG_DIR.$file);
        $f=fopen(TEMP_DIR.$file,"w");
        if (!$f) return -1;
        fwrite($f,$a);
        fclose($f);
        return 0;
    }

    private function restoreConfig($file) {
        if ( ! file_exists(TEMP_DIR.$file)) return 1;
        $a=file_get_contents(TEMP_DIR.$file);
        $f=fopen(CONFIG_DIR.$file,"w");
        if (!$f) return -1;
        fwrite($f,$a);
        fclose($f);
        return 0;
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
        echo "Version name: ".$this->version_name."<br />";
        echo "Version date: ".$this->version_date."<br />";
        $this->temp_file=TEMP_FILE . $this->version_date . ".zip";
    }

    public function getVersionName() { return $this->version_name; }

    public function getVersionDate() { return $this->version_date; }

    public function handleConfig($oper) {
        $res=true;
        if ($oper==true) { // backup
            if ($this->backupConfig("config.ini")<0) $res=false;
            if ($this->backupConfig("registration.info")<0) $res=false;
            if ($res==false) echo "FAILED ";
            echo "BACKUP configuration <br />";
        } else { // restore
            if ($this->restoreConfig("config.ini")<0) $res=false;;
            if ($this->restoreConfig("registration.info")<0) $res=false;
            if ($res==false) echo "FAILED ";
            echo "RESTORE configuration<br />";
        }
        return $res;
    }

    public function downloadFile($force=false) {
        if (file_exists($this->temp_file) && $force==false) return 0; // no need to download
        echo "DOWNLOAD ".UPDATE_FILE;
        return $this->file_save(UPDATE_FILE,$this->temp_file);
    }

    public function doUpgrade() {
        $root=__DIR__ . "/../";
        // open zip file
        $zip = zip_open($this->temp_file);
        if (!$zip) { echo "Open zipfile failed <br/>"; return false; }
        while ($aF = zip_read($zip) ) {
            // get file name and their directory
            $file_name = str_replace("AgilityContest-master/","",zip_entry_name($aF));
            $dir_name = dirname($file_name);
            // skip directories in zip file
            if ( substr($file_name,-1,1) == '/') continue; // not a file
            //Make the directory if we need to...
            if ( !is_dir ( $root . $dir_name ) ) {
                $res=mkdir ( $root . $dir_name );
                if (!$res) echo "FAILED ";
                echo "MAKEDIR: $dir_name<br />";
            }
            // create/overwrite file
            if ( !is_dir($root.$file_name) ) {
                $contents = zip_entry_read($aF, zip_entry_filesize($aF));
                $oper=(file_exists($root.$file_name))?"UPDATE":"CREATE";
                $file = fopen($root.$file_name, 'w');
                if ($file) {
                    fwrite($file, $contents);
                    fclose($file);
                    echo "$oper $file_name<br />";
                } else  echo "FAILED $oper $file_name<br />";
                unset($contents); // clear data from memory
            }
        }
        // finally, if a post_install.php file is present, parse and execute it
        if (file_exists(POST_INSTALL)) {
            echo "EXECUTE: post-install.php<br />";
            include(POST_INSTALL);
            unlink(POST_INSTALL);
        }
        zip_close($zip);
        return true;
    }
};

$up = new AgilityContestUpdater();
$res=$up->downloadFile(false);
if ($res===FALSE) { echo "Download failed<br />"; return; }
$res=$up->handleConfig(true); // backup
if ($res===FALSE) { echo "Backup configuration failed <br/>"; return;}
$res=$up->doUpgrade();
if ($res===FALSE) { echo "Upgrade failed <br/>"; return; }
$res =$up->handleConfig(false); // restore
if ($res===FALSE) { echo "Restore configuration failed <br/>"; return;}
echo "Upgrade to Version:".$up->getVersionName()." Revision:".$up->getVersionDate()." Success<br />";
?>
</pre>
<p>
    Pulsa para reiniciar AgilityContest <input type="button" onClick="window.location='/agility/console';" value="Reiniciar" />
</p>
</body>
</html>
