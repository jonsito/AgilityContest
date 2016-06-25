<?php

/*
upgradeVersion.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
        $this->conn = DBConnection::getRootConnection();
        if ($this->conn->connect_error) throw new Exception("Cannot perform upgrade process: database::dbConnect()");
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

    function addColumnUnlessExists($table,$field,$data,$def=null) {
        // $this->myLogger->enter();
        $str="";
        if ($def!=null) {
            // check for enclose default into single quotes
            $type=strtolower($data);
            $isStr=false;
            if (strpos($data,"text")!==FALSE) $isStr=true;
            if (strpos($data,"char")!==FALSE) $isStr=true;
            if (strpos($data,"time")!==FALSE) $isStr=true;
            if (strpos($data,"date")!==FALSE) $isStr=true;
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
        $this->conn->query($drop);
        $this->conn->query($create);
        $this->conn->query($call);
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
                `perros`.`Licencia` AS `Licencia`,
                `perros`.`LOE_RRC` AS `LOE_RRC`,
                `perros`.`Categoria` AS `Categoria`,
                `categorias_perro`.`Observaciones` AS `NombreCategoria`,
                `perros`.`Grado` AS `Grado`,
                `grados_perro`.`Comentarios` AS `NombreGrado`,
                `perros`.`Guia` AS `Guia`,
                `guias`.`Nombre` AS `NombreGuia`,
                `guias`.`Club` AS `Club`,
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

    function updateResultados() {
        // this fails if primary key doesn't exists, so detect and avoid
        $cmds= array(
            "ALTER TABLE `Resultados` DROP PRIMARY KEY;"
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
}

$upg=new Updater();
if ($upg->slaveMode()==true) return; // restricted mode. do not try to update database anyway
try {
    $upg->removeUpdateMark();
    $upg->updateVersionHistory();
    $upg->updatePerroGuiaClub();
    $upg->addCountries();
    $upg->addColumnUnlessExists("Mangas","Orden_Equipos","TEXT");
    $upg->addColumnUnlessExists("Resultados","TIntermedio","double","0.0");
    $upg->addColumnUnlessExists("Resultados","Games","int(4)",0);
    $upg->addColumnUnlessExists("Perros","NombreLargo","varchar(255)");
    $upg->addColumnUnlessExists("Perros","Genero","varchar(16)");
    $upg->addColumnUnlessExists("Provincias","Pais","varchar(2)","ES");
    $upg->updateInscripciones();
    $upg->updateResultados();
    $upg->upgradeTeams();
    $upg->setTRStoFloat();
} catch (Exception $e) {
    syslog(LOG_ERR,$e);
}
?>
