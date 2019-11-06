<?php

/*
upgradeVersion.php

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

/**
 * This script is executed at every start of console app, to make sure that database is consistent with current
 * application version
 *
 */

require_once(__DIR__."/tools.php");
require_once(__DIR__."/logging.php");
require_once(__DIR__."/auth/Config.php");
require_once(__DIR__."/auth/SymmetricCipher.php");
require_once(__DIR__."/database/classes/DBObject.php");
require_once(__DIR__."/i18n/Country.php");

define("MINVER","20150522_2300");
define('INSTALL_LOG',__DIR__."/../../logs/install.log");
define('FIRST_INSTALL',__DIR__."/../../logs/first_install");
define('DO_NOT_BACKUP',__DIR__."/../../logs/do_not_backup");

class Updater {
    protected $myConfig;
    public $current_version; // Software version from system.ini
    public $last_version; // database version from VersionHistory table
    public $myLogger;
    protected $conn;
    protected $myDBObject;

    // used to store backup version info to handle remote updates
    // these values comes from Backup database header
    protected $bckVersion="0.0.0";              // software version that created backup file
    protected $bckRevision="00000000_0000";     // software revision that created backup file
    protected $bckLicense="00000000";           // software license number that created backup file
    protected $bckDate="20180215_0944";         // Date of backup file creation

    /**
     * Updater constructor.
     * @throws Exception if cannot create root connection with database
     */
    function __construct() {
        // extract version info from configuration file
        $this->myConfig=Config::getInstance();
        $this->myLogger=new Logger("autoUpgrade",$this->myConfig->getEnv("debug_level"));
        $this->bckVersion=$this->myConfig->getEnv('version_name'); // extracted from sql file on restore. defaults to current
        $this->bckRevision=$this->myConfig->getEnv('version_date'); // extracted from sql file on restore. defaults to current
        $this->current_version=$this->myConfig->getEnv("version_date");

        // connect database with proper permissions
        $this->conn = DBConnection::getRootConnection();
        if ($this->conn->connect_error) {
            $str="Cannot perform upgrade check process: database::dbConnect() error. Retrying....";
            $this->install_log("$str <br/>");

            // wait 5 seconds and retry
            sleep(5);
            $this->conn = DBConnection::getRootConnection();
            if ($this->conn->connect_error) {
                $str="Cannot perform upgrade check process: database::dbConnect() error. Exiting";
                $this->install_log("$str <br/>");
                throw new Exception($str);
            }
        }

        // create procedures:

        $this->myDBObject=new DBObject("upgradeDatabase");
    }

    function install_log($str) {
        $f=fopen(INSTALL_LOG,"a"); // open for append-only
        if (!$f) { $this->myLogger->error("fopen() cannot create file: ".INSTALL_LOG); return;}
        echo "$str\n"; flush(); ob_flush();
        fwrite($f,$str."\n");
        fclose($f);
    }

    function installDB() {

        // phase 1: retrieve database file from "extras" directory
        $filename=__DIR__."/../../extras/agility.sql";
        $fp=fopen($filename, "r");
        if (!$fp) return "Cannot load database file to be installed";

        // phase 2: verify received file to be a proper AgilityContest backup and extract header info
        $str=fgets($fp);
        // first line is copyright info
        $num=sscanf($str,
            "-- AgilityContest Version: %s Revision: %s License: %s\n",
            $this->bckVersion, $this->bckRevision, $this->bckLicense);
        if ($num<2) {
            fclose($fp);
            return "Provided install file is not an AgilityContest backup file";
        }
        $keystr="";
        if ($num===3) { // newer backup files includes license number, creation date and optionaly encryption key
            $str=fgets($fp);
            $num=sscanf("$str","-- AgilityContest Backup Date: %s Hash: %s\n",$this->bckDate,$keystr);
            if ($num==1) $keystr=""; // no decrypting key on intermediate (3.7.3) backup format
        } else {
            //older db backups lacks on third field
            $this->bckLicense="00000000";
            $this->bckDate=date("Ymd_Hi");
            $keystr="";
        }
        // rest of file is database backup
        $data=fread($fp,filesize($filename));
        fclose($fp); // no longer needed
        // if encryption key found in header, decrypt file
        if ($keystr!=="") {
            // encryption key
            $key= base64_encode(substr("{$this->bckLicense}{$this->bckRevision}{$this->bckDate}",-32));
            // check key hash
            if ($keystr!== hash("md5",$key,false))return ("Restore failed: Key hash does not match");
            $data=SymmetricCipher::decrypt($data,$key);
        }
        // Read entire file into an array
        $lines = explode("\n",$data);

        // phase 3: delete all tables and structures from database
        $this->conn->query('SET foreign_key_checks = 0');
        if ($result = $this->conn->query("SHOW TABLES")) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $this->install_log("Drop table {$row[0]} ");
                $res=$this->conn->query('DROP TABLE IF EXISTS '.$row[0]);
                $this->install_log(($res)? "OK<br/>": "Error: {$this->conn->error} <br/>");
                if (!$res) return $this->conn->error;
            }
        }
        $this->conn->query('SET foreign_key_checks = 1');

        // phase 4: parse sql file and populate tables into database
        // Temporary variable, used to store current query
        $templine = '';
        $trigger=false;
        $timeout=ini_get('max_execution_time');
        // Loop through each line
        $need_ack=false; // to handle printing of OK/Error in log

        foreach ($lines as $idx => $str) {
            // avoid php to be killed on very slow systems
            set_time_limit($timeout);
            $line=trim($str); // remove spaces and newlines
            // Skip it if it's a comment
            if (substr($line, 0, 2) === '--' || trim($line) === '') continue;
            // properly handle "DELIMITER ;;" command
            if ($line==="DELIMITER ;;") { $trigger=true; continue; }
            else if ($line==="DELIMITER ;") { $trigger=false; }
            else $templine .= $line;    // Add this line to the current segment
            if ($trigger) continue;
            // log every create/insert
            if (strpos($line,"CREATE")===0) { $need_ack=true; $this->install_log("$line "); }
            if (strpos($line,"INSERT")===0) { $need_ack=true; $this->install_log("$line "); }
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
                // Perform the query
                if (! $this->conn->query($templine) ){
                    $error=$this->conn->error;
                    $this->myLogger->error("Error performing query '$templine': $error <br />");
                    $this->install_log( " Error: $error <br/>");
                    return $error;
                } else {
                    if ($need_ack) $this->install_log(" OK<br/>");
                    $need_ack=false;
                }
                // Reset temp variable to empty
                $templine = '';
            }
        }

        // phase 5 update VersionHistory: set Updated field of latest stored swversion row with backup date
        // IMPORTANT:
        // Version: latest version running on this database
        // Updated: lastModified timestamp usage for this database.
        // Version is to handle database upgrades to new version. DO NOT change on restore/install to assure db sructure upgrade
        // Updated is to handle database entries updates from server. DO update on restore/install to avoid server pull

        $bckd=toLongDateString($this->bckDate); // retrieve database backup date
        // trick to update only newest version record
        // https://stackoverflow.com/questions/12242466/update-row-with-max-value-of-field
        $str= "UPDATE versionhistory SET Updated='{$bckd}' ORDER BY Version DESC LIMIT 1";
        $this->conn->query($str);

        // cleanup
        $this->install_log("Install Database Done<br/>");
        $this->myLogger->info("Database install success");
        return "";
    }

    function slaveMode() {
        return (intval($this->myConfig->getEnv('running_mode'))===AC_RUNMODE_SLAVE)? true:false;
    }

    /**
     * Check database stored sw version agains real current sw version
     * If not matches add new sw version entry with latest updated db date
     *
     * @return bool true if need to handle database structure updates
     * @throws Exception on sql query failures
     */
    function updateVersionHistory() {
        $this->myLogger->enter();
        // make sure database provides version history table
        $cv="CREATE TABLE IF NOT EXISTS `versionhistory` (
          `Version` varchar(16) NOT NULL DEFAULT '".MINVER."',
          `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`Version`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $rs=$this->conn->query($cv);
        if (!$rs) throw new Exception ("upgrade::createHistoryTable(): ".$this->conn->error);

        // Retrieve last software version and last db update date from database
        $str="SELECT * FROM versionhistory ORDER BY Version DESC LIMIT 1;";
        $rs=$this->conn->query($str);
        if (!$rs) throw new Exception ("upgrade::getVersionHistory(): ".$this->conn->error);
        $res = $rs->fetch_array(MYSQLI_ASSOC);
        $rs->free();
        // on empty history table or error $res gets null
        $this->last_version = ($res)? $res['Version'] : MINVER; // get last db stored sw version

        // evaluate needToUpdateMark according software vs database version
        $this->myLogger->trace("SW Version: current: {$this->current_version} database: {$this->last_version}");
        $retflag= (strcmp($this->current_version,$this->last_version) > 0 )?true:false;

        // retrieve date of last database modification
        $str= "SELECT MAX(last) AS Updated FROM ( ". // this is the right way to do :-)
            "      SELECT MAX(LastModified) AS last FROM perros ".
            "UNION SELECT MAX(LastModified) AS last FROM guias ".
            "UNION SELECT MAX(LastModified) AS last FROM clubes ".
            "UNION SELECT MAX(LastModified) AS last FROM jueces) AS m";

        $rs=$this->conn->query($str);
        if ($rs){
            $res = $rs->fetch_array(MYSQLI_ASSOC);
            $rs->free();
            $curdate = ($res)? $res['Updated'] : toLongDateString($this->last_version);
            $this->myLogger->trace("Last database contents update was on: {$curdate}");
        } else { // when no LastModified fields exists mark to update database structure
            $this->myLogger->trace("Database too old: {$this->last_version}");
            $curdate = toLongDateString($this->last_version);
            $retflag=true;
        }
        // add new sw version entry into table with (newswver,lastdbupdate) values
        $str="INSERT INTO versionhistory (Version,Updated) VALUES ('{$this->current_version}','{$curdate}') ".
            "ON DUPLICATE KEY UPDATE Updated='{$curdate}'";
        $res=$this->conn->query($str);
        if (!$res) throw new Exception ("upgrade::updateHistoryTable(): ".$this->conn->error);

        // return with evaluated needToUpdateMark
        $this->myLogger->leave();
        return $retflag;
    }

    function dropColumnIfExists($table,$field) {
        $this->myLogger->enter();
        $dbname=$this->myConfig->getEnv('database_name');
        $drop = "DROP PROCEDURE IF EXISTS DropColumnIfExists;";
        $create = "
        CREATE PROCEDURE DropColumnIfExists()
            BEGIN
                IF EXISTS (
                    SELECT * FROM information_schema.COLUMNS
                    WHERE column_name='$field'
                        AND table_name='$table'
                        AND table_schema='$dbname'
                    )
                THEN
                    ALTER TABLE `$dbname`.`$table` DROP COLUMN `$field`;
                END IF;
            END;
        ";
        $call="CALL DropColumnIfExists()";
        $this->conn->query($drop);
        $this->conn->query($create);
        $this->conn->query($call);
        // $this->myLogger->leave();
    }

    function addColumnUnlessExists($table,$field,$data,$def=null) {
        $this->myLogger->enter();
        $dbname=$this->myConfig->getEnv('database_name');
        $str="";
        if (!is_null($def)) {
            // check for enclose default into single quotes
            $type=strtolower($data);
            $isStr=false;
            if (strpos($type,"text")!==FALSE) $isStr=true;
            if (strpos($type,"char")!==FALSE) $isStr=true;
            if (strpos($type,"time")!==FALSE) $isStr=true;
            if (strpos($type,"timestamp")!==FALSE) $isStr=false; // needed to autoupdate
            if (strpos($type,"date")!==FALSE) $isStr=true;
            if ($isStr) $str=" NOT NULL DEFAULT '$def'";
            else        $str=" NOT NULL DEFAULT $def";
        }
        $drop = "DROP PROCEDURE IF EXISTS AddColumnUnlessExists;";
        $create = "
        CREATE PROCEDURE AddColumnUnlessExists()
            BEGIN
                IF NOT EXISTS (
                    SELECT * FROM information_schema.COLUMNS
                    WHERE column_name='$field'
                        AND table_name='$table'
                        AND table_schema='$dbname'
                    )
                THEN
                    ALTER TABLE `$dbname`.`$table` ADD COLUMN `$field` $data $str;
                END IF;
            END;
        ";
        $call="CALL AddColumnUnlessExists()";
        $res=$this->conn->query($drop);
        if(!$res) do_log($this->conn->error);
        $res=$this->conn->query($create);
        if(!$res) do_log($this->conn->error);
        $res=$this->conn->query($call);
        if(!$res) do_log($this->conn->error);
        // $this->myLogger->leave();
    }


    //2.0.0b stupid mysql doesn't properly dump views
    function updatePerroGuiaClub() {
        $this->myLogger->enter();
        $cmds=array(
            "DROP TABLE IF EXISTS `perroguiaclub`;",
            "DROP VIEW IF EXISTS `perroguiaclub`;",
            "CREATE VIEW `perroguiaclub` AS
                SELECT `perros`.`ID` AS `ID`,
                `perros`.`Federation` AS `Federation`,
                `perros`.`Nombre` AS `Nombre`,
                `perros`.`NombreLargo` AS `NombreLargo`,
                `perros`.`Genero` AS `Genero`,
                `perros`.`Raza` AS `Raza`,
                `perros`.`Chip` AS `Chip`,
                `perros`.`Licencia` AS `Licencia`,
                `perros`.`LOE_RRC` AS `LOE_RRC`,
                `perros`.`Categoria` AS `Categoria`,
                `categorias_perro`.`Observaciones` AS `NombreCategoria`,
                `perros`.`Grado` AS `Grado`,
                `perros`.`Baja` AS `Baja`,
                `grados_perro`.`Comentarios` AS `NombreGrado`,
                `perros`.`Guia` AS `Guia`,
                `guias`.`Nombre` AS `NombreGuia`,
                `guias`.`Club` AS `Club`,
                `guias`.`Categoria` AS `CatGuia`,
                `clubes`.`Nombre` AS `NombreClub`,
                `clubes`.`Provincia` AS `Provincia`,
                `clubes`.`Pais` AS `Pais`,
                `clubes`.`Logo` AS `LogoClub`,
                GREATEST(`perros`.`LastModified`,`guias`.`LastModified`,`clubes`.`LastModified`) AS `LastModified`
                FROM ((((`perros` join `guias`) join `clubes`) join `grados_perro`) join `categorias_perro`)
                WHERE (
                    (`perros`.`Guia` = `guias`.`ID`)
                    AND (`guias`.`Club` = `clubes`.`ID`)
                    AND (`perros`.`Categoria` = `categorias_perro`.`Categoria`)
                    AND (`perros`.`Grado` = `grados_perro`.`Grado`))
                    ORDER BY `clubes`.`Nombre`,`perros`.`Categoria`,`perros`.`Nombre`;"
        );
        foreach ($cmds as $query) { $this->conn->query($query); }
        return 0;
    }

    // 2.0.0 20150720_0135 add cascade to foreign keys on inscripciones
    // no longer used as takes too much time in reindex
    function updateInscripciones() {
        $this->myLogger->enter();
        $cmds=array(
            "ALTER TABLE `inscripciones` DROP FOREIGN KEY `Inscripciones_ibfk_1`;",
            "ALTER TABLE `inscripciones` ADD CONSTRAINT `Inscripciones_ibfk_1`
                FOREIGN KEY (`Perro`) REFERENCES `perros` (`ID`) ON UPDATE CASCADE;",
            "ALTER TABLE `inscripciones` DROP FOREIGN KEY `Inscripciones_ibfk_2`;",
            "ALTER TABLE `inscripciones` ADD CONSTRAINT `Inscripciones_ibfk_2`
                FOREIGN KEY (`Prueba`) REFERENCES `pruebas` (`ID`) ON UPDATE CASCADE ON DELETE CASCADE;"
        );
        foreach ($cmds as $query) { $this->conn->query($query); }
        return 0;
    }

    /**
     * add country list if not exist
     * @throws Exception on sql query failure
     */
    function addCountries() {
        $this->myLogger->enter();
        $country="";
        $lcountry="";
        $name="";
        $logo="";
        // check if countries are already added
        $str="SELECT count(*) AS Cuenta FROM clubes WHERE Federations >=512;";
        $rs=$this->conn->query($str);
        if (!$rs) throw new Exception ("upgrade::addCountries(select): ".$this->conn->error);
        $item=$rs->fetch_row();
        if ($item[0]!=0) return; // already done
        // federation ID 9 is reserved for coutry list. Do not use
        $str="INSERT INTO clubes(Nombre,NombreLargo,Direccion1,Direccion2,Provincia,Pais,Contacto1,Contacto2,Contacto3,GPS,Web,Email,Facebook,Google,Twitter,Logo,Federations,Observaciones,Baja)
              VALUES (?,?,'','','-- Sin asignar --',?,'','','','','','','','','',?,512,'',0)";
        // prepare "prepared statement"
        $stmt=$this->conn->prepare($str);
        if (!$stmt) throw new Exception("upgrade::addCountries(prepare) ".$this->conn->error);
        $res=$stmt->bind_param('ssss',$name,$lcountry,$country,$logo);
        if (!$res) throw new Exception("upgrade::addCountries(bind) ".$stmt->error);
        foreach(Country::$countryList as $key => $val) {
            $country=$key;
            $lcountry=$val;
            $name=$val;
            $logo="../../server/i18n/$key.png";
            $stmt->execute();
        }
    }

    function upgradeTeams() {
        $this->myLogger->enter();
        $cmds= array(
            "UPDATE `jornadas` SET `Equipos3`=3 WHERE (`Equipos3`=1);",
            "UPDATE `jornadas` SET `Equipos4`=4 WHERE (`Equipos4`=1);"
        );
        foreach ($cmds as $query) { $this->conn->query($query); }
        return 0;
    }

    // clear (if any) Application Upgrade request
    function removeUpdateMark() {
        $this->myLogger->enter();
        $f=__DIR__."/../../logs/do_upgrade";
        if (file_exists($f)) unlink($f);
    }

    /**
     * replace all instances of manga data from integer to float if required
     * @return int when done
     * @throws Exception on sql query failure
     */
    function setTRStoFloat() {
        $this->myLogger->enter();
        $dbname=$this->myConfig->getEnv('database_name');
        $cmds= array(
            "ALTER TABLE `mangas` MODIFY `TRS_L_Factor` float(5);",
            "ALTER TABLE `mangas` MODIFY `TRM_L_Factor` float(5) NOT NULL DEFAULT '50.0';",
            "ALTER TABLE `mangas` MODIFY `TRS_M_Factor` float(5);",
            "ALTER TABLE `mangas` MODIFY `TRM_M_Factor` float(5) NOT NULL DEFAULT '50.0';",
            "ALTER TABLE `mangas` MODIFY `TRS_S_Factor` float(5);",
            "ALTER TABLE `mangas` MODIFY `TRM_S_Factor` float(5) NOT NULL DEFAULT '50.0';",
            "ALTER TABLE `mangas` MODIFY `TRS_T_Factor` float(5);",
            "ALTER TABLE `mangas` MODIFY `TRM_T_Factor` float(5) NOT NULL DEFAULT '50.0';"
        );
        // comprobamos si es necesario hacerlo
        $str= "SELECT Column_Default FROM information_schema.COLUMNS ".
            "WHERE table_schema='$dbname' AND table_name='mangas' AND column_name='TRM_L_Factor'";
        $rs=$this->conn->query($str);
        if (!$rs) throw new Exception ("upgrade::setTRStoFloat(): ".$this->conn->error);
        $res = $rs->fetch_array(MYSQLI_ASSOC);
        if (floatval($res['Column_Default'])===50.0) return 0; // already done
        // not done: change every TRS/TRM field to float
        foreach ($cmds as $query) { $this->conn->query($query); }
        return 0;
    }

    /**
    * this function adds additional user info required to operate in server mode
    * password, email, capabilities and so
    * Due to RGPD, this table should not be exported when requesting updates from server
    */
    function createUserInfoTable () {
        $this->myLogger->enter();
        $str="
        CREATE TABLE IF NOT EXISTS `user_info` (
          `ID` int(4) NOT NULL AUTO_INCREMENT,
          `ServerID`  int(4) NOT NULL,
          `Password`  varchar(255) NOT NULL DEFAULT '',
          `Capabilities`  varchar(255) NOT NULL DEFAULT '',
          `Email` varchar(255) NOT NULL DEFAULT '',
          `Phone` varchar(16) NOT NULL DEFAULT '',
          PRIMARY KEY (`ID`),
          KEY `user_info_serverid` (`ServerID`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ";
        $res=$this->conn->query($str);
        if (!$res) throw new Exception("upgrade::createUserInfoTable(): ".$this->conn->error);
        $this->myLogger->leave();
    }

    /**
     * tabla de gestion de puntos de liga
     * Necesitamos como clave unica la tupla Jornada,Perro,Grado
     * La categoria se supone que no cambia en la vida del perro, pero el grado si
     * En cada entrada guardamos los puntos y estrellas de cada manga
     * El valor de estos depende de cada competicion
     * No hace falta indicar ni prueba ni federacion: estan implicitos en la jornada
     */
    function createLeagueTable() {
        $this->myLogger->enter();
        $str="
        CREATE TABLE IF NOT EXISTS `ligas` (
          `Jornada` int(4) NOT NULL,
          `Grado`   varchar(16) NOT NULL,
          `Perro`   int(4) NOT NULL,
          `Pt1`      int(4) NOT NULL DEFAULT 0,
          `Pt2`      int(4) NOT NULL DEFAULT 0,
          `Pt3`      int(4) NOT NULL DEFAULT 0,
          `Pt4`      int(4) NOT NULL DEFAULT 0,
          `Pt5`      int(4) NOT NULL DEFAULT 0,
          `Pt6`      int(4) NOT NULL DEFAULT 0,
          `Pt7`      int(4) NOT NULL DEFAULT 0,
          `Pt8`      int(4) NOT NULL DEFAULT 0,
          `St1`      int(4) NOT NULL DEFAULT 0,
          `St2`      int(4) NOT NULL DEFAULT 0,
          `St3`      int(4) NOT NULL DEFAULT 0,
          `St4`      int(4) NOT NULL DEFAULT 0,
          `St5`      int(4) NOT NULL DEFAULT 0,
          `St6`      int(4) NOT NULL DEFAULT 0,
          `St7`      int(4) NOT NULL DEFAULT 0,
          `St8`      int(4) NOT NULL DEFAULT 0,
          `Xt1`      int(4) NOT NULL DEFAULT 0,
          `Xt2`      int(4) NOT NULL DEFAULT 0,
          `Xt3`      int(4) NOT NULL DEFAULT 0,
          `Xt4`      int(4) NOT NULL DEFAULT 0,
          `Xt5`      int(4) NOT NULL DEFAULT 0,
          `Xt6`      int(4) NOT NULL DEFAULT 0,
          `Xt7`      int(4) NOT NULL DEFAULT 0,
          `Xt8`      int(4) NOT NULL DEFAULT 0,
          `C1`       varchar(16) NOT NULL DEFAULT '',
          `C2`       varchar(16) NOT NULL DEFAULT '',
          `C3`       varchar(16) NOT NULL DEFAULT '',
          `C4`       varchar(16) NOT NULL DEFAULT '',
          `C5`       varchar(16) NOT NULL DEFAULT '',
          `C6`       varchar(16) NOT NULL DEFAULT '',
          `C7`       varchar(16) NOT NULL DEFAULT '',
          `C8`       varchar(16) NOT NULL DEFAULT '',
          `Puntos`   int(4) NOT NULL DEFAULT 0,
          `Estrellas` int(4) NOT NULL DEFAULT 0,
          `Extras`   int(4) NOT NULL DEFAULT 0,
          `Calificacion` varchar(16) NOT NULL DEFAULT '',
          PRIMARY KEY (`Jornada`,`Grado`,`Perro`),
          KEY `Ligas_Jornada` (`Jornada`),
          KEY `Ligas_Grado` (`Grado`),
          KEY `Ligas_Perro` (`Perro`),
          CONSTRAINT `Ligas_ibfk1` FOREIGN KEY (`Jornada`) REFERENCES `jornadas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `Ligas_ibfk2` FOREIGN KEY (`Grado`) REFERENCES `grados_perro` (`Grado`) ON DELETE CASCADE ON UPDATE CASCADE, 
          CONSTRAINT `Ligas_ibfk3` FOREIGN KEY (`Perro`) REFERENCES `perros` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE 
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ";
        $res=$this->conn->query($str);
        if (!$res) throw new Exception("upgrade::createLeagueTable(): ".$this->conn->error);
        $this->myLogger->leave();
    }

    /**
     * tabla de entrenamientos
     * definimos hasta cuatro rings por pais, indicando en cada ring
     * la categoria y el tiempo en segundos
     * @throws Exception
     */
    function createTrainingTable() {
        $this->myLogger->enter();
        $str="
        CREATE TABLE IF NOT EXISTS `entrenamientos` (
          `ID` int(4) NOT NULL AUTO_INCREMENT,
          `Prueba`  int(4) NOT NULL DEFAULT 1,
          `Orden`   int(4) NOT NULL DEFAULT 0,
          `Club`    int(4) NOT NULL,
          `Fecha`   date DEFAULT '2016-01-01' ,
          `Firma` datetime  DEFAULT 0 ,
          `Veterinario` datetime DEFAULT  0 ,
          `Comienzo` datetime DEFAULT  0 ,
          `Duracion` int(4) NOT NULL DEFAULT 0,
          `Key1` varchar(32) DEFAULT '',
          `Value1` int(4) NOT NULL DEFAULT 0,
          `Key2` varchar(32) DEFAULT '',
          `Value2` int(4) NOT NULL DEFAULT 0,
          `Key3` varchar(32) DEFAULT '',
          `Value3` int(4) NOT NULL DEFAULT 0,
          `Key4` varchar(32) DEFAULT '',
          `Value4` int(4) NOT NULL DEFAULT 0,
          `Key5` varchar(32) DEFAULT '',
          `Value5` int(4) NOT NULL DEFAULT 0,
          `Observaciones` varchar(255) DEFAULT '',
          `Estado` int(4) NOT NULL DEFAULT -1,
          PRIMARY KEY (`ID`),
          KEY `Entrenamientos_Prueba` (`Prueba`),
          KEY `Entrenamientos_Club` (`Club`),
          CONSTRAINT `Entrenamientos_ibfk_1` FOREIGN KEY (`Prueba`) REFERENCES `pruebas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `Entrenamientos_ibfk_2` FOREIGN KEY (`Club`) REFERENCES `clubes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE 
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ";
        $res=$this->conn->query($str);
        if (!$res) throw new Exception("upgrade::createTrainingTable(): ".$this->conn->error);
        $this->myLogger->leave();
    }

    /**
     * In order to show a team logo we use 'Miembros' field of Table 'Equipos' to store team members in non-default teams
     * Remember that when adding/removeing a team member, you should invoke "Equipos::updateTeam($perro,$team), to retain
     * consistency with 'Resultados' table
     *
     * This is a dirty trick for databases prior to 2016-08-14 to populate Miembros field in Equipos table.
     * till now these field was unused with default value 'BEGIN,END'
     */
    function populateTeamMembers() {
        $this->myLogger->enter();
        $teams=$this->myDBObject->__select("*","equipos","(Miembros='BEGIN,END') AND (DefaultTeam=0)","","");
        foreach ($teams['rows'] as $team) {
            $j=$team['Jornada'];
            $t=$team['ID'];
            $res=$this->myDBObject->__select(
                /*SELECT */ "GROUP_CONCAT(DISTINCT Perro SEPARATOR ',') AS Lista",
                /* FROM */  "resultados",
                /* WHERE */ "(Jornada=$j) AND (equipo=$t)",
                "","","" // ORDER, LIMIT, GROUP BY
            );
            if($res['total']==0) continue; // no teams
            $data=$res['rows'][0]['Lista'];
            if ( is_null($data) || (trim($data)==="") ) continue;
            $lista="BEGIN,$data,END";
            $str="UPDATE equipos SET Miembros='$lista' WHERE ID=$t";
            $this->myDBObject->query($str);
        }
    }

    /**
     * insert into database information for Agility Grade 1 Round 3
     * @return
     */
    function addNewMangaTypes() {
        $this->myLogger->enter();
        $cmds= array(
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(17,'Agility Grado 1 Manga 3','GI')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(18,'K.O. Round 2','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(19,'K.O. Round 3','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(20,'K.O. Round 4','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(21,'K.O. Round 5','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(22,'K.O. Round 6','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(23,'K.O. Round 7','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(24,'K.O. Round 8','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(25,'Agility A','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(26,'Agility B','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(27,'Jumping A','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(28,'Jumping B','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(29,'Snooker','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(30,'Gumbler','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(31,'SpeedStakes','-')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(32,'Junior 1','Jr')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(33,'Junior 2','Jr')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(34,'Senior 1','Sr')",
            "INSERT IGNORE INTO tipo_manga (ID,Descripcion,Grado) VALUES(35,'Senior 2','Sr')"
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        return 0;
    }

    /**
     * Update database to release 3.4 by remove usage of redundant PreAgility2 field
     * @return 0 on success
     */
    function updatePreAgility() {
        $this->myLogger->enter();
        $cmds= array(
            // new usage is PreAgility: 0:none 1:single_round 2:double_round
            // leave PreAgility2 unchanged to maintain backward compatibility, but next thing is to remove
            "UPDATE jornadas SET PreAgility=2 WHERE PreAgility2=1"
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        return 0;
    }

    /**
     * Prepare Grados_Perro to new module based grades
     * @return {int} 0 on success
     */
    function addNewGradeTypes() {
        $this->myLogger->enter();
        $cmds= array(
            // temporary hack for Junior and Senior grades
            // this sucks: Jr and Sr are handler categories, not dog ones, but...
            // also children runs together with junior. so use same "grade"
            "INSERT IGNORE INTO grados_perro (Grado,Comentarios) VALUES('Jr','Children / Young')",
            "INSERT IGNORE INTO grados_perro (Grado,Comentarios) VALUES('Sr','Senior')",
            // new model for module based competitions and federations
            // use numeric strings to allow easy int-to-string conversion
            "INSERT IGNORE INTO grados_perro (Grado,Comentarios) VALUES('0','Grade 0')", // old Pre-Agility
            "INSERT IGNORE INTO grados_perro (Grado,Comentarios) VALUES('1','Grade 1')", // old Grade 1
            "INSERT IGNORE INTO grados_perro (Grado,Comentarios) VALUES('2','Grade 2')", // old Grade 2
            "INSERT IGNORE INTO grados_perro (Grado,Comentarios) VALUES('3','Grade 3')", // old Grade 3
            "INSERT IGNORE INTO grados_perro (Grado,Comentarios) VALUES('4','Grade 4')",
            "INSERT IGNORE INTO grados_perro (Grado,Comentarios) VALUES('5','Grade 5')",
            "INSERT IGNORE INTO grados_perro (Grado,Comentarios) VALUES('6','Grade 6')",
            "INSERT IGNORE INTO grados_perro (Grado,Comentarios) VALUES('7','Grade 7')",
            "INSERT IGNORE INTO grados_perro (Grado,Comentarios) VALUES('8','Grade 8')",
            "INSERT IGNORE INTO grados_perro (Grado,Comentarios) VALUES('9','Grade 9')",
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        return 0;
    }

    /*
     * as license convention changes, use Loe/ RRC to check if a dog is elegible for puntuaction in selectives
     */
    function fixLOERRC2017() {
        $this->myLogger->enter();
        $cmds= array(
            "UPDATE perros SET LOE_RRC=concat('AC_',Licencia), LastModified=LastModified ".
                " WHERE (Federation=0) AND (LOE_RRC='') AND (Licencia like '0%')",
            "UPDATE perros SET LOE_RRC=concat('AC_',Licencia), LastModified=LastModified ".
                " WHERE (Federation=0) AND (LOE_RRC='') AND (Licencia like 'A%')",
            "UPDATE perros SET LOE_RRC=concat('AC_',Licencia), LastModified=LastModified ".
                " WHERE (Federation=0) AND (LOE_RRC='') AND (Licencia like 'B%')",
            "UPDATE perros SET LOE_RRC=concat('AC_',Licencia), LastModified=LastModifed ".
                " WHERE (Federation=0) AND (LOE_RRC='') AND (Licencia like 'C%')"
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        return 0;
    }

    function addMailList() {
        $this->myLogger->enter();
        $this->addColumnUnlessExists("pruebas", "MailList", "TEXT"); // text column cannot have default values
        $cmds= array(
            "UPDATE pruebas SET MailList='BEGIN,END' WHERE MailList IS NULL"
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        return 0;
    }

    // make default club and judge belong to every national federations (0..4)
    function updateDefaultJuezClub() {
        $this->myLogger->enter();
        $cmds= array(
            "UPDATE clubes SET Federations=31, LastModified=LastModified WHERE ID=1",
            "UPDATE jueces SET Federations=31, LastModified=LastModified WHERE ID=1",
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        return 0;
    }

    /**
     *change passwors after database update
     *@param {string} $user login
     *@param {string} $pw user password ( clear text )
     */
    function setupPassword($user,$pw) {
        if ($pw=="") return 0;
        $p=base64_encode(password_hash($pw,PASSWORD_DEFAULT));
        $str="UPDATE usuarios SET Password='{$p}' WHERE Login='{$user}'";
        $this->myDBObject->query($str);
        return 0;
    }

    function addExtraLargeCategory() {
        $this->myLogger->enter();
        // phase 1: replace category 'E' to 'X' in db. Not used anymore, but for coherency
        $cmds= array(
            "UPDATE categorias_perro SET Categoria='X' WHERE Categoria='E'",
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        // phase 2: add XLarge fields data into mangas table
        $this->addColumnUnlessExists("mangas", "Dist_X", "int(4)", "0");
        $this->addColumnUnlessExists("mangas", "Obst_X", "int(4)", "0");
        $this->addColumnUnlessExists("mangas", "TRS_X_Tipo", "int(4)", "0");
        $this->addColumnUnlessExists("mangas", "TRS_X_Factor", "double", "0.0");
        $this->addColumnUnlessExists("mangas", "TRS_X_Unit", "varchar(1)", "s");
        $this->addColumnUnlessExists("mangas", "TRM_X_Tipo", "int(4)", "1");
        $this->addColumnUnlessExists("mangas", "TRM_X_Factor", "double", "50");
        $this->addColumnUnlessExists("mangas", "TRM_X_Unit", "varchar(1)", "%");
        return 0;
    }

    // New rules for rfec 5 heights
    function updateHeightsRFEC() {
        $this->myLogger->enter();
        $cmds= array(
            // promote L->X and so
            "UPDATE perros SET Categoria='X' WHERE Categoria='L' AND Federation=1",
            "UPDATE perros SET Categoria='L' WHERE Categoria='M' AND Federation=1",
            "UPDATE perros SET Categoria='M' WHERE Categoria='S' AND Federation=1",
            "UPDATE perros SET Categoria='S' WHERE Categoria='T' AND Federation=1",
            // mark every existing RFEC journeys as closed
            // DON'T update pruebas's closed flag: contest wont be available anymore
            // "UPDATE pruebas SET Cerrada=1 WHERE RSCE=1",
            "UPDATE jornadas SET Cerrada=1 WHERE jornadas.Prueba=pruebas.ID AND pruebas.RSCE=1"
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        return 0;
    }
}

$upg=new Updater();
if ($upg->slaveMode()==true) return; // slave server mode. do not try to update database anyway
// allow only localhost access
$white_list= array ("localhost","127.0.0.1","::1",$_SERVER['SERVER_ADDR'],"138.4.4.108");
if (!in_array($_SERVER['REMOTE_ADDR'],$white_list))  return; // upgrade is restricted to console
// check for database install request
$installdb = http_request("installdb", "i", 0);

try {
    // check for (re)install database request
    if ($installdb !== 0) {
        if ( ! @file_exists(FIRST_INSTALL)) {
            // on manual request first install force user consent by mean of F5 (reload page)
            @touch(FIRST_INSTALL);
            $upg->install_log('<script type="text/javascript">confirmInstallDB();</script>');
            $upg->install_log("Confirmation required<br/>&nbsp;</p></div></body></html>");
            die("Reinstall request must be confirmed by user");
        }
        ob_implicit_flush(true);
        $res=$upg->installDB();
        if ($res!=="") {
            $upg->install_log('<script type="text/javascript">alert("Database installation failed: '.$res.'");</script>');
            $upg->install_log("Database installation failed: $res<br/>&nbsp;</p></div></body></html>");
            die("Install DB error: $res . Please contact author");
        }
        $upg->install_log('<script type="text/javascript">alert("Database installation OK");</script>');
        ob_implicit_flush(false);
        @touch(DO_NOT_BACKUP); // mark installDB=1 to disable autobackup at first login
        // after install db check for need to change admin , operator and assistant passwords
        $upg->setupPassword("admin", http_request("admin","s",""));
        $upg->setupPassword("operator", http_request("operator","s",""));
        $upg->setupPassword("assisstant",http_request("assistant","s",""));
    }
    // process database to make it compliant with sofwtare version
    $upg->removeUpdateMark();
    $needToUpdate=$upg->updateVersionHistory();
    @unlink(FIRST_INSTALL);
    if ($needToUpdate===false) { // database already updated
        $upg->myLogger->info("Database version is equal or greater than installed sw version");
        // as backup does not preserve views and procedures, always need to recreate
        $upg->updatePerroGuiaClub();
        return;
    }
    set_time_limit(ini_get('max_execution_time'));
    // software version changed. make sure that database is upgraded
    $upg->myLogger->info("Database version is lower than installed sw version. Updating DB structure");
    // $upg->addCountries();
    $upg->addExtraLargeCategory();
    $upg->addColumnUnlessExists("mangas", "Orden_Equipos", "TEXT");
    $upg->addColumnUnlessExists("resultados", "TIntermedio", "double", "0.0");
    $upg->addColumnUnlessExists("resultados", "Games", "int(4)", "0");
    $upg->addColumnUnlessExists("perros", "NombreLargo", "varchar(255)");
    $upg->addColumnUnlessExists("perros", "Chip", "varchar(255)", "");
    $upg->addColumnUnlessExists("perros", "Genero", "varchar(16)", "-"); // -,M,F
    $upg->addColumnUnlessExists("perros", "Baja", "tinyint(1)", "0"); // 1:baja 0:activo
    $upg->addColumnUnlessExists("guias", "Categoria", "varchar(16)","A");// -,I,J,A,S,V,P
    $upg->addColumnUnlessExists("provincias", "Pais", "varchar(2)", "ES");
    $upg->dropColumnIfExists("jornadas", "Orden_Tandas");
    $upg->addColumnUnlessExists("jornadas", "Games", "int(4)", "0");
    $upg->addColumnUnlessExists("jornadas", "Junior", "tinyint(1)", "0");
    $upg->addColumnUnlessExists("jornadas", "Senior", "tinyint(1)", "0");
    $upg->addColumnUnlessExists("jornadas", "Tipo_Competicion", "int(4)", "0");

    // on server edition need to track modification time and unique id set on server
    // additionally add password and capabilities to handlers
    // notice that these fields shouldn't be exported to clients from server
    $upg->addColumnUnlessExists("perros", "LastModified", "timestamp", "CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    $upg->addColumnUnlessExists("perros", "ServerID", "int(4)", "0");
    $upg->addColumnUnlessExists("guias", "LastModified", "timestamp", "CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    $upg->addColumnUnlessExists("guias", "ServerID", "int(4)", "0");
    $upg->addColumnUnlessExists("clubes", "LastModified", "timestamp", "CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    $upg->addColumnUnlessExists("clubes", "ServerID", "int(4)", "0");
    $upg->addColumnUnlessExists("jueces", "LastModified", "timestamp", "CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    $upg->addColumnUnlessExists("jueces", "ServerID", "int(4)", "0");

    // as backup does not preserve views and procedures, always need to recreate
    $upg->updatePerroGuiaClub();
    // Pre-Agility2 is no longer used, just use Pre-Agility field
    $upg->updatePreAgility();

    // $upg->updateInscripciones(); not needed and to many time wasted
    // si la versión de la db es inferior a la 4.0.0, mover los perros de caza
    if(strcmp($upg->last_version,"20190923_1123")<0) $upg->updateHeightsRFEC();

    // for server edition, include inscription dates
    // notice that mysql does not support CURRENT_DATE as default value, so need to emulate
    // mariadb does, but not used in ubuntu nor xampp :-(
    $fdate=date("Y-m-d");
    $tdate=date("Y-m-d",time()+604800); // time + 1 week
    $upg->addColumnUnlessExists("pruebas","OpeningReg", "date", $fdate);
    $upg->addColumnUnlessExists("pruebas","ClosingReg", "date", $tdate);
    $upg->upgradeTeams();
    $upg->setTRStoFloat();
    $upg->createTrainingTable();
    // after 3.9 5-heights come up
    $upg->addColumnUnlessExists("entrenamientos", "Key5", "varchar(32)", "");
    $upg->addColumnUnlessExists("entrenamientos", "Value5", "int(4)", "0");
    $upg->createUserInfoTable();
    $upg->createLeagueTable();
    $upg->populateTeamMembers();
    $upg->addNewGradeTypes();
    $upg->addNewMangaTypes();
    $upg->fixLOERRC2017();
    $upg->addColumnUnlessExists("usuarios", "Club", "int(4)", "1");
    $upg->addMailList();
    $upg->updateDefaultJuezClub();
} catch (Exception $e) {
    syslog(LOG_ERR,$e);
}
?>
