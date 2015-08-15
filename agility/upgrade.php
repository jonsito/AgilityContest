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
define ('UPDATE_INFO','https://github.com/jonsito/AgilityContest/tree/master/agility/server/auth/system.ini');
define ('UPDATE_FILE','https://github.com/jonsito/AgilityContest/archive/master.zip');
define ('TEMP_FILE', __DIR__."/../logs/AgilityContest-");
define ('POST_INSTALL', __DIR__."/../post-install.php");

Class AgilityContestUpdater {
    var $version_name="1.0.0";
    var $version_date="20150101_0000";
    var $temp_file=TEMP_FILE;

    public function __construct() {
        $info = file_get_contents(UPDATE_INFO) or die ('Cannot retrieve update information. Check your internet connection');
        $data = explode("\n",$info);
        $this->version_name=$data['version_name'];
        $this->version_date=$data['version_date'];
        $this->temp_file=TEMP_FILE . $this->version_date . ".zip";
    }

    public function getVersionName() { return $this->version_name; }

    public function getVersionDate() { return $this->version_date; }

    public function downloadFile() {
        if (file_exists($this->temp_file)) return true; // no need to download
        $data = file_get_contents(UPDATE_FILE);
        $file = fopen($this->temp_file,"w");
        if (!fwrite($file,$data)) return false;
        fclose($file);
        return true;
    }

    public function doUpgrade() {
        $root=__DIR__ . "/../";
        // open zip file
        $zip = zip_open($this->temp_file);
        while ($aF = zip_read($zip) ) {
            // get file name and their directory
            $file_name = zip_entry_name($aF);
            $dir_name = dirname($file_name);
            // skip directories in zip file
            if ( substr($file_name,-1,1) == '/') continue; // not a file
            //Make the directory if we need to...
            if ( !is_dir ( $root . $dir_name ) ) mkdir ( $root . $dir_name );
            // create/overwrite file
            if ( !is_dir($root.$file_name) ) {
                $contents = zip_entry_read($aF, zip_entry_filesize($aF));
                $file = fopen($root.$file_name, 'w');
                fwrite($file, $contents);
                fclose($file);
                unset($contents); // clear data from memory
            }
        }
        // finally, if a post_install.php file is present, parse and execute it
        if (file_exists(POST_INSTALL)) {
            include(POST_INSTALL);
            unlink(POST_INSTALL);
        }
        zip_close($zip);
    }
};

$up = new AgilityContestUpdater();
$res=$up->downloadFile();
if($res) $up->doUpgrade();

?>