<?php

/*
upgradeVersion.php

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
define ('INSTALL_LOG',__DIR__."/../../logs/install.log");

class Updater {
    protected $config;
    public $current_version;
    public $last_version;
    protected $myLogger;
    protected $conn;
    protected $myDBObject;

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
        if (!$fp) die("Cannot load database file to be installed");

        // phase 2: verify received file
        $str=fgets($fp);
        if (strpos(substr($str,0,25),"-- AgilityContest")===FALSE)
            throw new Exception("Provided file is not an AgilityContest backup file");

        // phase 3: delete all tables and structures from database
        $this->conn->query('SET foreign_key_checks = 0');
        if ($result = $this->conn->query("SHOW TABLES")) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $this->install_log("Drop table {$row[0]} ");
                $res=$this->conn->query('DROP TABLE IF EXISTS '.$row[0]);
                $this->install_log(($res)? "OK<br/>": "Error: {$this->conn->error} <br/>");
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
        $this->install_log("Install Database Done<br/>");
        $this->myLogger->info("Database install success");
        return "";
    }

    function slaveMode() {
        if (intval($this->config->getEnv("restricted"))==0) return false;
        return true;
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

    function dropColumnIfExists($table,$field) {
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
        // $this->myLogger->enter();
        $str="";
        if (!is_null($def)) {
            // check for enclose default into single quotes
            $type=strtolower($data);
            $isStr=false;
            if (strpos($type,"text")!==FALSE) $isStr=true;
            if (strpos($type,"char")!==FALSE) $isStr=true;
            if (strpos($type,"time")!==FALSE) $isStr=true;
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
        $cmds=array(
            "DROP TABLE IF EXISTS `perroguiaclub`;",
            "DROP VIEW IF EXISTS `perroguiaclub`;",
            "CREATE VIEW `perroguiaclub` AS
                select `perros`.`ID` AS `ID`,
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
                `clubes`.`Logo` AS `LogoClub`
                from ((((`perros` join `guias`) join `clubes`) join `grados_perro`) join `categorias_perro`)
                where (
                    (`perros`.`Guia` = `guias`.`ID`)
                    and (`guias`.`Club` = `clubes`.`ID`)
                    and (`perros`.`Categoria` = `categorias_perro`.`Categoria`)
                    and (`perros`.`Grado` = `grados_perro`.`Grado`))
                    order by `clubes`.`Nombre`,`perros`.`Categoria`,`perros`.`Nombre`;"
        );
        foreach ($cmds as $query) { $this->conn->query($query); }
        return 0;
    }

    // 2.0.0 20150720_0135 add cascade to foreign keys on inscripciones
    function updateInscripciones() {
        $cmds=array(
            "ALTER TABLE `Inscripciones` DROP FOREIGN KEY `Inscripciones_ibfk_1`;",
            "ALTER TABLE `Inscripciones` ADD CONSTRAINT `Inscripciones_ibfk_1`
                FOREIGN KEY (`Perro`) REFERENCES `Perros` (`ID`) ON UPDATE CASCADE;",
            "ALTER TABLE `Inscripciones` DROP FOREIGN KEY `Inscripciones_ibfk_2`;",
            "ALTER TABLE `Inscripciones` ADD CONSTRAINT `Inscripciones_ibfk_2`
                FOREIGN KEY (`Prueba`) REFERENCES `Pruebas` (`ID`) ON UPDATE CASCADE ON DELETE CASCADE;"
        ,
        );
        foreach ($cmds as $query) { $this->conn->query($query); }
        return 0;
    }

    // add country list if not exist
    function addCountries() {
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
        if (!$res) throw new Exception("upgrade::addCountries(bind) ".$this->conn->error);
        foreach(Country::$countryList as $key => $val) {
            $country=$key;
            $lcountry=$val;
            $name=$val;
            $logo="../../server/i18n/$key.png";
            $stmt->execute();
        }
    }

    function upgradeTeams() {
        $cmds= array(
            "UPDATE `Jornadas` SET `Equipos3`=3 WHERE (`Equipos3`=1);",
            "UPDATE `Jornadas` SET `Equipos4`=4 WHERE (`Equipos4`=1);"
        );
        foreach ($cmds as $query) { $this->conn->query($query); }
        return 0;
    }

    // clear (if any) Application Upgrade request
    function removeUpdateMark() {
        $f=__DIR__."/../../logs/do_upgrade";
        if (file_exists($f)) unlink($f);
    }

    function setTRStoFloat() {
        $cmds= array(
            "ALTER TABLE `Mangas` MODIFY `TRS_L_Factor` float(5);",
            "ALTER TABLE `Mangas` MODIFY `TRM_L_Factor` float(5);",
            "ALTER TABLE `Mangas` MODIFY `TRS_M_Factor` float(5);",
            "ALTER TABLE `Mangas` MODIFY `TRM_M_Factor` float(5);",
            "ALTER TABLE `Mangas` MODIFY `TRS_S_Factor` float(5);",
            "ALTER TABLE `Mangas` MODIFY `TRM_S_Factor` float(5);",
            "ALTER TABLE `Mangas` MODIFY `TRS_T_Factor` float(5);",
            "ALTER TABLE `Mangas` MODIFY `TRM_T_Factor` float(5);"
        );
        // comprobamos si es necesario hacerlo
        $str= "SELECT data_type FROM information_schema.COLUMNS WHERE table_schema='agility' AND table_name='Mangas' AND column_name='TRS_L_Factor'";
        $rs=$this->conn->query($str);
        if (!$rs) throw new Exception ("upgrade::setTRStoFloat(): ".$this->conn->error);
        $res = $rs->fetch_array(MYSQLI_ASSOC);
        if (strpos($res['data_type'],'int')===false) return 0; // already done
        // not done: change every TRS/TRM field to float
        foreach ($cmds as $query) { $this->conn->query($query); }
        return 0;
    }

    // tabla de entrenamientos
    // definimos hasta cuatro rings por pais, indicando en cada ring
    // la categoria y el tiempo en segundos
    function createTrainingTable() {
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
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(32,'Junior 1','-')",
            "INSERT IGNORE INTO Tipo_Manga (ID,Descripcion,Grado) VALUES(33,'Junior 2','-')"
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        return 0;
    }

    /*
     * as license convention changes, use Loe/ RRC to check if a dog is elegible for puntuaction in selectives
     */
    function fixLOERRC2017() {
        $cmds= array(
            "UPDATE Perros SET LOE_RRC=concat('AC_',Licencia) WHERE (Federation=0) AND (LOE_RRC='') AND (Licencia like '0%')",
            "UPDATE Perros SET LOE_RRC=concat('AC_',Licencia) WHERE (Federation=0) AND (LOE_RRC='') AND (Licencia like 'A%')",
            "UPDATE Perros SET LOE_RRC=concat('AC_',Licencia) WHERE (Federation=0) AND (LOE_RRC='') AND (Licencia like 'B%')",
            "UPDATE Perros SET LOE_RRC=concat('AC_',Licencia) WHERE (Federation=0) AND (LOE_RRC='') AND (Licencia like 'C%')"
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        return 0;
    }

    function addMailList() {
        $this->addColumnUnlessExists("Pruebas", "MailList", "TEXT"); // text column cannot have default values
        $cmds= array(
            "UPDATE Pruebas SET MailList='BEGIN,END' WHERE MailList IS NULL"
        );
        foreach ($cmds as $query) { $this->myDBObject->query($query); }
        return 0;
    }

    // make default club and judge belong to every national federations (0..4)
    function updateDefaultJuezClub() {
        $cmds= array(
            "UPDATE Clubes SET Federations=31 WHERE ID=1",
            "UPDATE Jueces SET Federations=31 WHERE ID=1",
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
            $upg->install_log("Database installation failed: $res<br/>&nbsp;</p></div></body></html>");
            die("Install DB error: $res . Please contact author");
        }
        ob_implicit_flush(false);
    }
    // when not in first install, process database to make it compliant with sofwtare version
    $upg->removeUpdateMark();
    $upg->updateVersionHistory();
    $upg->addCountries();
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
    $upg->addColumnUnlessExists("Jornadas", "Tipo_Competicion", "int(4)", "0");
    $upg->updatePerroGuiaClub();
    $upg->updateInscripciones();
    $upg->upgradeTeams();
    $upg->setTRStoFloat();
    $upg->createTrainingTable();
    $upg->populateTeamMembers();
    $upg->addNewMangaTypes();
    $upg->fixLOERRC2017();
    $upg->addColumnUnlessExists("Usuarios", "Club", "int(4)", "1");
    $upg->addMailList();
    $upg->updateDefaultJuezClub();
} catch (Exception $e) {
    syslog(LOG_ERR,$e);
}
?>
