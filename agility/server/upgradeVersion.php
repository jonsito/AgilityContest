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
require_once(__DIR__."/database/classes/DBObject.php");
require_once(__DIR__."/i18n/Country.php");

define("MINVER","20150522_2300");
define('INSTALL_LOG',__DIR__."/../../logs/install.log");
define('FIRST_INSTALL',__DIR__."/../../logs/first_install");

class Updater {
    protected $config;
    public $current_version;
    public $last_version;
    protected $myLogger;
    protected $conn;
    protected $myDBObject;

    // used to store backup version info to handle remote updates
    protected $bckVersion="3.7.3";
    protected $bckRevision="20180212_1024";
    protected $bckLicense="00000000";
    protected $bckDate="20180215_0944";

    /**
     * Updater constructor.
     * @throws Exception if cannot create root connection with database
     */
    function __construct() {
        // extract version info from configuration file
        $this->config=Config::getInstance();
        $this->myLogger=new Logger("autoUpgrade",$this->config->getEnv("debug_level"));
        $this->current_version=$this->config->getEnv("version_date");

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
        $fp=fopen(__DIR__."/../../extras/agility.sql", "r");
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
        if ($num===3) { // newer backup files includes license number and creation date
            $str=fgets($fp);
            sscanf("$str","-- AgilityContest Backup Date: %s\n",$this->bckDate);
        } else {
            //older db backups lacks on third field
            $this->bckLicense="00000000";
            $this->bckDate=date("Ymd_Hi");
        }

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
        while ( ($str=fgets($fp))!==false ) {
            // avoid php to be killed on very slow systems
            set_time_limit($timeout);
            $line=trim($str); // remove spaces and newlines
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || trim($line) == '') continue;
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
        fclose($fp);

        // phase 5 update VersionHistory: set current sw version entry with restored backup creation date
        $bckd=toLongDateString($this->bckDate);
        $swver=$this->config->getEnv("version_date");
        $str="INSERT INTO VersionHistory (Version,Updated) VALUES ('{$swver}','{$bckd}') ".
            "ON DUPLICATE KEY UPDATE UPDATE Updated='{$bckd}'";
        $this->conn->query($str);

        // cleanup
        $this->install_log("Install Database Done<br/>");
        $this->myLogger->info("Database install success");
        return "";
    }

    function slaveMode() {
        if (intval($this->config->getEnv("restricted"))==0) return false;
        return true;
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
        $cv="CREATE TABLE IF NOT EXISTS `VersionHistory` (
          `Version` varchar(16) NOT NULL DEFAULT '{MINVER}',
          `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`Version`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $res=$this->conn->query($cv);
        if (!$res) throw new Exception ("upgrade::createHistoryTable(): ".$this->conn->error);

        // Retrieve last software version and last db update date from database
        $str="SELECT * FROM VersionHistory ORDER BY Version DESC LIMIT 1;";
        $rs=$this->conn->query($str);
        if (!$rs) throw new Exception ("upgrade::getVersionHistory(): ".$this->conn->error);
        $res = $rs->fetch_array(MYSQLI_ASSOC);
        $rs->free();
        $this->last_version = ($res)? $res['Version'] : MINVER; // get last db stored sw version
        $this->myLogger->trace("SW Version: current: {$this->current_version} database: {$this->last_version}");

        // check if current sw version is greater than installed db says (if so, return true)
        if (strcmp($this->current_version,$this->last_version) > 0 ) {

            // eval time of last DB update. Should include judges data, but enought for now
            // $str="SELECT MAX(PerroGuiaClub.LastModified) AS Updated FROM PerroGuiaClub";
            $str= "SELECT MAX(last) AS Updated FROM ( ". // this is the right way to do :-)
                        "      SELECT MAX(LastModified) AS last FROM Perros ".
                        "UNION SELECT MAX(LastModified) AS last FROM Guias ".
                        "UNION SELECT MAX(LastModified) AS last FROM Clubes ".
                        "UNION SELECT MAX(LastModified) AS last FROM Jueces) AS m";

            $rs=$this->conn->query($str);
            if (!$rs) throw new Exception ("upgrade::getLastDbUpdate(): ".$this->conn->error);
            $res = $rs->fetch_array(MYSQLI_ASSOC);
            $rs->free();
            $curdate = ($res)? $res['Updated'] : toLongDateString($this->last_version);

            // add new sw version entry into table with (newswver,lastdbupdate) values
            $str="INSERT INTO VersionHistory (Version,Updated) VALUES ('{$this->current_version}','{$curdate}') ";
            $res=$this->conn->query($str);
            if (!$res) throw new Exception ("upgrade::updateHistoryTable(): ".$this->conn->error);
            $this->myLogger->leave();
            return true; // new release: mark database to be updated
        }
        $this->myLogger->leave();
        // same version than installed. No need to add new SW version to db history
        return false;
    }

    function dropColumnIfExists($table,$field) {
        $this->myLogger->enter();
        $drop = "DROP PROCEDURE IF EXISTS DropColumnIfExists;";
        $create = "
        CREATE PROCEDURE DropColumnIfExists()
            BEGIN
                IF EXISTS (
                    SELECT * FROM information_schema.COLUMNS
                    WHERE column_name='$field'
                        AND table_name='$table'
                        AND table_schema='agility'
                    )
                THEN
                    ALTER TABLE `agility`.`$table` DROP COLUMN `$field`;
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
                        AND table_schema='agility'
                    )
                THEN
                    ALTER TABLE `agility`.`$table` ADD COLUMN `$field` $data $str;
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
            "DROP TABLE IF EXISTS `PerroCuiaClub`;",
            "DROP VIEW IF EXISTS `PerroGuiaClub`;",
            "CREATE VIEW `PerroGuiaClub` AS
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
                `grados_perro`.`Comentarios` AS `NombreGrado`,
                `perros`.`Guia` AS `Guia`,
                `guias`.`Nombre` AS `NombreGuia`,
                `guias`.`Club` AS `Club`,
                `guias`.`Categoria` AS `CatGuia`,
                `clubes`.`Nombre` AS `NombreClub`,
                `clubes`.`Provincia` AS `Provincia`,
                `clubes`.`Pais` AS `Pais`,
                `clubes`.`Logo` AS `LogoClub`,
                GREATEST(`Perros`.`LastModified`,`Guias`.`LastModified`,`Clubes`.`LastModified`) AS `LastModified`
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
            "ALTER TABLE `Inscripciones` DROP FOREIGN KEY `Inscripciones_ibfk_1`;",
            "ALTER TABLE `Inscripciones` ADD CONSTRAINT `Inscripciones_ibfk_1`
                FOREIGN KEY (`Perro`) REFERENCES `Perros` (`ID`) ON UPDATE CASCADE;",
            "ALTER TABLE `Inscripciones` DROP FOREIGN KEY `Inscripciones_ibfk_2`;",
            "ALTER TABLE `Inscripciones` ADD CONSTRAINT `Inscripciones_ibfk_2`
                FOREIGN KEY (`Prueba`) REFERENCES `Pruebas` (`ID`) ON UPDATE CASCADE ON DELETE CASCADE;"
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
        $str="SELECT count(*) AS Cuenta FROM Clubes WHERE Federations >=512;";
        $rs=$this->conn->query($str);
        if (!$rs) throw new Exception ("upgrade::addCountries(select): ".$this->conn->error);
        $item=$rs->fetch_row();
        if ($item[0]!=0) return; // already done
        // federation ID 9 is reserved for coutry list. Do not use
        $str="INSERT INTO CLUBES(Nombre,NombreLargo,Direccion1,Direccion2,Provincia,Pais,Contacto1,Contacto2,Contacto3,GPS,Web,Email,Facebook,Google,Twitter,Logo,Federations,Observaciones,Baja)
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
            "UPDATE `Jornadas` SET `Equipos3`=3 WHERE (`Equipos3`=1);",
            "UPDATE `Jornadas` SET `Equipos4`=4 WHERE (`Equipos4`=1);"
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
        $cmds= array(
            "ALTER TABLE `Mangas` MODIFY `TRS_L_Factor` float(5);",
            "ALTER TABLE `Mangas` MODIFY `TRM_L_Factor` float(5) NOT NULL DEFAULT '50.0';",
            "ALTER TABLE `Mangas` MODIFY `TRS_M_Factor` float(5);",
            "ALTER TABLE `Mangas` MODIFY `TRM_M_Factor` float(5) NOT NULL DEFAULT '50.0';",
            "ALTER TABLE `Mangas` MODIFY `TRS_S_Factor` float(5);",
            "ALTER TABLE `Mangas` MODIFY `TRM_S_Factor` float(5) NOT NULL DEFAULT '50.0';",
            "ALTER TABLE `Mangas` MODIFY `TRS_T_Factor` float(5);",
            "ALTER TABLE `Mangas` MODIFY `TRM_T_Factor` float(5) NOT NULL DEFAULT '50.0';"
        );
        // comprobamos si es necesario hacerlo
        $str= "SELECT Column_Default FROM information_schema.COLUMNS ".
            "WHERE table_schema='agility' AND table_name='Mangas' AND column_name='TRM_L_Factor'";
        $rs=$this->conn->query($str);
        if (!$rs) throw new Exception ("upgrade::setTRStoFloat(): ".$this->conn->error);
        $res = $rs->fetch_array(MYSQLI_ASSOC);
        if (floatval($res['Column_Default'])===50.0) return 0; // already done
        // not done: change every TRS/TRM field to float
        foreach ($cmds as $query) { $this->conn->query($query); }
        return 0;
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
        CREATE TABLE IF NOT EXISTS `Ligas` (
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
          CONSTRAINT `Ligas_ibfk1` FOREIGN KEY (`Jornada`) REFERENCES `Jornadas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `Ligas_ibfk2` FOREIGN KEY (`Grado`) REFERENCES `Grados_Perro` (`Grado`) ON DELETE CASCADE ON UPDATE CASCADE, 
          CONSTRAINT `Ligas_ibfk3` FOREIGN KEY (`Perro`) REFERENCES `Perros` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE 
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
        CREATE TABLE IF NOT EXISTS `Entrenamientos` (
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
          `Observaciones` varchar(255) DEFAULT '',
          `Estado` int(4) NOT NULL DEFAULT -1,
          PRIMARY KEY (`ID`),
          KEY `Entrenamientos_Prueba` (`Prueba`),
          KEY `Entrenamientos_Club` (`Club`),
          CONSTRAINT `Entrenamientos_ibfk_1` FOREIGN KEY (`Prueba`) REFERENCES `Pruebas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `Entrenamientos_ibfk_2` FOREIGN KEY (`Club`) REFERENCES `Clubes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE 
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
        $teams=$this->myDBObject->__select("*","Equipos","(Miembros='BEGIN,END') AND (DefaultTeam=0)","","");
        foreach ($teams['rows'] as $team) {
            $j=$team['Jornada'];
            $t=$team['ID'];
            $res=$this->myDBObject->__select(
                /*SELECT */ "GROUP_CONCAT(DISTINCT Perro SEPARATOR ',') AS Lista",
                /* FROM */  "Resultados",
                /* WHERE */ "(Jornada=$j) AND (equipo=$t)",
                "","","" // ORDER, LIMIT, GROUP BY
            );
            if($res['total']==0) continue; // no teams
            $data=$res['rows'][0]['Lista'];
            if ( is_null($data) || (trim($data)==="") ) continue;
            $lista="BEGIN,$data,END";
            $str="UPDATE Equipos SET Miembros='$lista' WHERE ID=$t";
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
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(17,'Agility Grado 1 Manga 3','GI')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(18,'K.O. Round 2','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(19,'K.O. Round 3','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(20,'K.O. Round 4','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(21,'K.O. Round 5','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(22,'K.O. Round 6','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(23,'K.O. Round 7','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(24,'K.O. Round 8','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(25,'Agility A','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(26,'Agility B','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(27,'Jumping A','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(28,'Jumping B','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(29,'Snooker','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(30,'Gumbler','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(31,'SpeedStakes','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(32,'Junior 1','Jr')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(33,'Junior 2','Jr')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(34,'Senior 1','Sr')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(35,'Senior 2','Sr')"
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
            "UPDATE Jornadas SET PreAgility=2 WHERE PreAgility2=1"
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
            "INSERT IGNORE INTO Grados_Perro (Grado,Comentarios) VALUES('Jr','Junior')",
            "INSERT IGNORE INTO Grados_Perro (Grado,Comentarios) VALUES('Sr','Senior')",
            // new model for module based competitions and federations
            // use numeric strings to allow easy int-to-string conversion
            "INSERT IGNORE INTO Grados_Perro (Grado,Comentarios) VALUES('0','Grade 0')", // old Pre-Agility
            "INSERT IGNORE INTO Grados_Perro (Grado,Comentarios) VALUES('1','Grade 1')", // old Grade 1
            "INSERT IGNORE INTO Grados_Perro (Grado,Comentarios) VALUES('2','Grade 2')", // old Grade 2
            "INSERT IGNORE INTO Grados_Perro (Grado,Comentarios) VALUES('3','Grade 3')", // old Grade 3
            "INSERT IGNORE INTO Grados_Perro (Grado,Comentarios) VALUES('4','Grade 4')",
            "INSERT IGNORE INTO Grados_Perro (Grado,Comentarios) VALUES('5','Grade 5')",
            "INSERT IGNORE INTO Grados_Perro (Grado,Comentarios) VALUES('6','Grade 6')",
            "INSERT IGNORE INTO Grados_Perro (Grado,Comentarios) VALUES('7','Grade 7')",
            "INSERT IGNORE INTO Grados_Perro (Grado,Comentarios) VALUES('8','Grade 8')",
            "INSERT IGNORE INTO Grados_Perro (Grado,Comentarios) VALUES('9','Grade 9')",
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
            "UPDATE Perros SET LOE_RRC=concat('AC_',Licencia), LastModified=LastModified ".
                " WHERE (Federation=0) AND (LOE_RRC='') AND (Licencia like '0%')",
            "UPDATE Perros SET LOE_RRC=concat('AC_',Licencia), LastModified=LastModified ".
                " WHERE (Federation=0) AND (LOE_RRC='') AND (Licencia like 'A%')",
            "UPDATE Perros SET LOE_RRC=concat('AC_',Licencia), LastModified=LastModified ".
                " WHERE (Federation=0) AND (LOE_RRC='') AND (Licencia like 'B%')",
            "UPDATE Perros SET LOE_RRC=concat('AC_',Licencia), LastModified=LastModifed ".
                " WHERE (Federation=0) AND (LOE_RRC='') AND (Licencia like 'C%')"
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        return 0;
    }

    function addMailList() {
        $this->myLogger->enter();
        $this->addColumnUnlessExists("Pruebas", "MailList", "TEXT"); // text column cannot have default values
        $cmds= array(
            "UPDATE Pruebas SET MailList='BEGIN,END' WHERE MailList IS NULL"
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        return 0;
    }

    // make default club and judge belong to every national federations (0..4)
    function updateDefaultJuezClub() {
        $this->myLogger->enter();
        $cmds= array(
            "UPDATE Clubes SET Federations=31, LastModified=LastModified WHERE ID=1",
            "UPDATE Jueces SET Federations=31, LastModified=LastModified WHERE ID=1",
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        return 0;
    }
}

$upg=new Updater();
if ($upg->slaveMode()==true) return; // restricted mode. do not try to update database anyway
// allow only localhost access
$white_list= array ("localhost","127.0.0.1","::1",$_SERVER['SERVER_ADDR'],"138.4.4.108");
if (!in_array($_SERVER['REMOTE_ADDR'],$white_list))  return; // upgrade is restricted to console
// check for database install request
$installdb = http_request("installdb", "i", 0);

try {
    // check for (re)install database request
    if ($installdb !== 0) {
        ob_implicit_flush(true);
        $res=$upg->installDB();
        if ($res!=="") {
            $upg->install_log('<script type="text/javascript">alert("Database installation failed: '.$res.'");</script>');
            $upg->install_log("Database installation failed: $res<br/>&nbsp;</p></div></body></html>");
            die("Install DB error: $res . Please contact author");
        }
        $upg->install_log('<script type="text/javascript">alert("Database installation OK");</script>');
        ob_implicit_flush(false);
    }
    // process database to make it compliant with sofwtare version
    $upg->removeUpdateMark();
    // as backup does not preserve procedures, always need to recreate
    $upg->updatePerroGuiaClub();
    $needToUpdate=$upg->updateVersionHistory();
    @unlink(FIRST_INSTALL);
    if ($needToUpdate===false) return; // database already updated. so just return
    // software version changed. make sure that database is upgraded
    // $upg->addCountries();
    $upg->addColumnUnlessExists("Mangas", "Orden_Equipos", "TEXT");
    $upg->addColumnUnlessExists("Resultados", "TIntermedio", "double", "0.0");
    $upg->addColumnUnlessExists("Resultados", "Games", "int(4)", "0");
    $upg->addColumnUnlessExists("Perros", "NombreLargo", "varchar(255)");
    $upg->addColumnUnlessExists("Perros", "Chip", "varchar(255)", "");
    $upg->addColumnUnlessExists("Perros", "Genero", "varchar(16)", "-"); // -,M,F
    $upg->addColumnUnlessExists("Guias", "Categoria", "varchar(16)","A");// -,I,J,A,S,V,P
    $upg->addColumnUnlessExists("Provincias", "Pais", "varchar(2)", "ES");
    $upg->dropColumnIfExists("Jornadas", "Orden_Tandas");
    $upg->addColumnUnlessExists("Jornadas", "Games", "int(4)", "0");
    $upg->addColumnUnlessExists("Jornadas", "Junior", "tinyint(1)", "0");
    $upg->addColumnUnlessExists("Jornadas", "Senior", "tinyint(1)", "0");
    $upg->addColumnUnlessExists("Jornadas", "Tipo_Competicion", "int(4)", "0");

    // on server edition need to track modification time and unique id set on server
    $upg->addColumnUnlessExists("Perros", "LastModified", "timestamp", "CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    $upg->addColumnUnlessExists("Perros", "ServerID", "int(4)", "0");
    $upg->addColumnUnlessExists("Guias", "LastModified", "timestamp", "CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    $upg->addColumnUnlessExists("Guias", "ServerID", "int(4)", "0");
    $upg->addColumnUnlessExists("Clubes", "LastModified", "timestamp", "CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    $upg->addColumnUnlessExists("Clubes", "ServerID", "int(4)", "0");
    $upg->addColumnUnlessExists("Jueces", "LastModified", "timestamp", "CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    $upg->addColumnUnlessExists("Jueces", "ServerID", "int(4)", "0");
    $upg->updatePreAgility();
    // $upg->updateInscripciones(); not needed and to many time wasted

    // for server edition, include inscription dates
    // notice that mysql does not support CURRENT_DATE as default value, so need to emulate
    // mariadb does, but not used in ubuntu nor xampp :-(
    $fdate=date("Y-m-d");
    $tdate=date("Y-m-d",time()+604800); // time + 1 week
    $upg->addColumnUnlessExists("Pruebas","OpeningReg", "date", $fdate);
    $upg->addColumnUnlessExists("Pruebas","ClosingReg", "date", $tdate);
    $upg->upgradeTeams();
    $upg->setTRStoFloat();
    $upg->createTrainingTable();
    $upg->createLeagueTable();
    $upg->populateTeamMembers();
    $upg->addNewGradeTypes();
    $upg->addNewMangaTypes();
    $upg->fixLOERRC2017();
    $upg->addColumnUnlessExists("Usuarios", "Club", "int(4)", "1");
    $upg->addMailList();
    $upg->updateDefaultJuezClub();
} catch (Exception $e) {
    syslog(LOG_ERR,$e);
}
?>
