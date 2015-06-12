<?php

/*
upgradeVersion.php

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

require_once(__DIR__."/tools.php");
require_once(__DIR__."/logging.php");
require_once(__DIR__."/auth/Config.php");
require_once(__DIR__."/database/classes/DBObject.php");

define("MINVER","20150522_2300");

class Updater {
    protected $config;
    public $current_version;
    public $last_version;
    protected $myLogger;
    protected $conn;

    function __construct() {
        // extract version info from configuration file
        $this->config=Config::getInstance();
        $this->myLogger=new Logger("autoUpgrade",$this->config->getEnv("debug_level"));
        $this->current_version=$this->config->getEnv("version_date");

        // connect database with proper permissions
        $this->conn = new mysqli("localhost","agility_admin","admin@cachorrera","agility");
        if ($this->conn->connect_error) throw new Exception("Cannot perform upgrade process: database::dbConnect()");
    }

    function updateVersionHistory() {
        $this->myLogger->enter();
        // make sure database provides version history table
        $cv="CREATE TABLE IF NOT EXISTS `VersionHistory` (
          `Version` varchar(16) NOT NULL DEFAULT '{MINVER}',
          `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`Version`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $res=$this->conn->query($cv);
        if (!$res) throw new Exception ("upgrade::createHistoryTable(): ".$this->conn->error);

        // Retrieve current database version from history table
        $str="SELECT * FROM VersionHistory ORDER BY Version DESC LIMIT 1;";
        $rs=$this->conn->query($str);
        if (!$rs) throw new Exception ("upgrade::getVersionHistory(): ".$this->conn->error);
        $res = $rs->fetch_array(MYSQLI_ASSOC);
        $this->last_version = ($res)? $res['Version'] : MINVER;
        $this->myLogger->trace("current: {$this->current_version} last_stored: {$this->last_version}");
        if (strcmp($this->current_version,$this->last_version) > 0 ) {
            // on version change update history table
            $str="INSERT INTO VersionHistory (Version) VALUES ('{$this->current_version}') ";
            $res=$this->conn->query($str);
            if (!$res) throw new Exception ("upgrade::updateHistoryTable(): ".$this->conn->error);
        }
        $rs->free();
        $this->myLogger->leave();
    }

    function addColumnUnlessExists($table,$field,$data) {
        $this->myLogger->enter();
        $drop = "DROP PROCEDURE IF EXISTS AddColumnUnlessExists;";
        $create = "
        CREATE PROCEDURE AddColumnUnlessExists()
            BEGIN
                IF NOT EXISTS (
                    SELECT * FROM information_schema.COLUMNS
                    WHERE column_name='$field'
                        AND table_name='$table'
                        AND table_schema='agility'
                    )
                THEN
                    ALTER TABLE `agility`.`$table` ADD COLUMN `$field` $data;
                END IF;
            END;
        ";
        $call="CALL AddColumnUnlessExists()";
        $this->conn->query($drop);
        $this->conn->query($create);
        $this->conn->query($call);
        $this->myLogger->leave();
    }
}

$upg=new Updater();
try {
    $upg->updateVersionHistory();
    if ( strcmp($upg->current_version, $upg->last_version) > 0) {
        $upg->addColumnUnlessExists("Mangas","Orden_Equipos","TEXT");
    }
} catch (Exception $e) {
    syslog(LOG_ERR,$e);
}
?>
