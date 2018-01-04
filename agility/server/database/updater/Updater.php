<?php
/**
 * Updater.php
 * Created by PhpStorm.
 * User: jantonio
 * Date: 02/01/18
 * Time: 17:02

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

require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__."/../../auth/Config.php");
require_once(__DIR__."/../../database/classes/DBObject.php");
/**
 * Class Updater
 * Creates structures and handle process to perform database upgrade
 * with data received from master server
 */
class Updater {

    protected $myLogger;
    protected $myConfig;
    protected $myDBObject;

    function __construct($name) {
        $this->myDBObject=new DBObject($name);
        $this->myConfig=Config::getInstance();
        $this->myLogger=new Logger($name,$this->myConfig->getEnv("debug_level"));
    }

    /**
     * Creacion de las tablas de importacion de datos desde servidor
     * Realmente son las tablas de perros, guias, clubes y jueces,
     * pero sin dependencias y sin id de las relaciones
     * El contenido se borra (DELETE FROM ) cada vez que se va a realizar una importación
     * Adicionalmente, los datos nunca se modifican, solo se insertan
     *
     * @throws Exception on error
     */
    function createMergeTables() {
        $tables = array (
            "Dogs"=>"CREATE TABLE IF NOT EXISTS `MergePerros` (
                `ID` int(4)   NOT NULL AUTO_INCREMENT,
                `ServerID` int(4)   NOT NULL DEFAULT 0,
                `GuiasServerID` int(4)   NOT NULL DEFAULT 0,
                `Federation`  tinyint(1)   NOT NULL DEFAULT 0,
                `Nombre`      varchar(255) NOT NULL ,
                `NombreLargo` varchar(255) NOT NULL DEFAULT '',
                `Genero`      varchar(16)  NOT NULL DEFAULT '-',
                `Raza`        varchar(255) NOT NULL DEFAULT '',
                `Chip`        varchar(255) NOT NULL DEFAULT '',
                `Licencia`    varchar(255) NOT NULL DEFAULT '',
                `LOE_RRC`     varchar(255) NOT NULL DEFAULT '',
                `Categoria`   varchar(1)   NOT NULL DEFAULT '-',
                `Grado`       varchar(16)  NOT NULL DEFAULT '-',
                `Guia`        int(4)       NOT NULL DEFAULT 1,
                `NombreGuia`  varchar(255) NOT NULL DEFAULT '',
                `LastModified` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
            ",
            "Handlers"=> "CREATE TABLE IF NOT EXISTS `MergeGuias` (
                `ID`          int(4)        NOT NULL AUTO_INCREMENT,
                `ServerID`    int(4)   NOT NULL DEFAULT 0,
                `ClubesServerID`    int(4)   NOT NULL DEFAULT 0,
                `Nombre`      varchar(255)  NOT NULL,
                `Telefono`    varchar(16)   NOT NULL DEFAULT '',
                `Email`       varchar(255)  NOT NULL DEFAULT '',
                `Club`        int(4)        NOT NULL DEFAULT 1,
                `NombreClub`  varchar(255)  NOT NULL DEFAULT '',
                `Federation`  tinyint(1)    NOT NULL DEFAULT 0,
                `Observaciones` varchar(255) NOT NULL DEFAULT '',
                `Categoria`   varchar(16)   NOT NULL DEFAULT 'A',
                `LastModified` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
            "
        );
        $conn=DBConnection::getRootConnection(); // need root connection for "create table"
        if (!$conn)
            throw new Exception("upgrade::createMergeTable(getConnection): ".$this->conn->error);
        foreach($tables as $key => $sql) {
            $res=$conn->query($sql);
            if (!$res) throw new Exception("upgrade::createMergeTable({$key}): ".$conn->error);
        }
    }

    private function setForUpdate($data,$key,$quote) {
        if ($quote) { // text fields. quote and set if not empty
            if ($data[$key]=="") return "";
            $q=$this->myDBObject->conn->real_escape_string($data[$key]);
            return ($data[$key]=="")?"":"SET {$key}='{$q}' ,";
        } else { // integer fields are allways set
            return "SET {$key}={$data[$key]},";
        }
    }

    private function setForInsert($data,$key,$quote) {
        if ($quote) { // text fields. quote and set if not empty
            if ($data[$key]=="") return "''";
            $q=$this->myDBObject->conn->real_escape_string($data[$key]);
            return "'{$q}'";
        } else { // integer fields are allways set
            return "{$data[$key]}";
        }
    }

    function handleJueces($data) {
        foreach ($data as $juez) {
            // extraemos datos
            $sid= $this->setForUpdate($juez,"ServerID",false);
            $nombre= $this->setForUpdate($juez,"Nombre",true);
            $dir1= $this->setForUpdate($juez,"Direccion1",true);
            $dir2= $this->setForUpdate($juez,"Direccion2",true);
            $pais= $this->setForUpdate($juez,"Pais",true);
            $tel= $this->setForUpdate($juez,"Telefono",true);
            $intl= $this->setForUpdate($juez,"Internacional",false);
            $pract= $this->setForUpdate($juez,"Practicas",false);
            $email= $this->setForUpdate($juez,"Email",true);
            $feds= $this->setForUpdate($juez,"Federations",false);
            $comments= $this->setForUpdate($juez,"Observaciones",true);

            // fase 1: si existe el ServerID se asigna "a saco"
            $str="UPDATE Jueces {$nombre} {$dir1} {$dir2} {$pais} {$tel} {$intl} {$pract} {$email} {$feds} {$comments} ".
                "WHERE ServerID={$sid}";
            $res=$this->myDBObject->query($str);
            if (!$res) { $this->myLogger->error($this->myDBObject->conn->error); continue; }
            if ($this->myDBObject->conn->affected_rows!=0) continue; // next juez

            // fase 2: si no existe el Server ID se busca por nombre (exacto)
            $str="UPDATE Jueces {$sid} {$dir1} {$dir2} {$pais} {$tel} {$intl} {$pract} {$email} {$feds} {$comments} ".
                "WHERE Nombre={$nombre}";
            $res=$this->myDBObject->query($str);
            if (!$res) { $this->myLogger->error($this->myDBObject->conn->error); continue; }
            if ($this->myDBObject->conn->affected_rows!=0) continue; // next juez

            // fase 3: si no existe el nombre, se crea la entrada

            $sid= $this->setForInsert($juez,"ServerID",false);
            $nombre= $this->setForInsert($juez,"Nombre",true);
            $dir1= $this->setForInsert($juez,"Direccion1",true);
            $dir2= $this->setForInsert($juez,"Direccion2",true);
            $pais= $this->setForInsert($juez,"Pais",true);
            $tel= $this->setForInsert($juez,"Telefono",true);
            $intl= $this->setForInsert($juez,"Internacional",false);
            $pract= $this->setForInsert($juez,"Practicas",false);
            $email= $this->setForInsert($juez,"Email",true);
            $feds= $this->setForInsert($juez,"Federations",false);
            $comments= $this->setForInsert($juez,"Observaciones",true);
            $str="INSERT INTO Jueces ".
                "( ServerID,Nombre,Direccion1,Direccion2,Pais,Telefono,Internacional,Practicas,Email,Federations,Observaciones )".
                "VALUES ({$sid},{$nombre},{$dir1},{$dir2},{$pais},{$tel},{$intl},{$pract},{$email},{$feds},{$comments})";
            $res=$this->myDBObject->query($str);
            if (!$res) { $this->myLogger->error($this->myDBObject->conn->error); continue; }
        }
    }

    function handleClubes($data) {
        foreach ($data as $club) {
            // escapamos los textos
            $sid= $this->setForUpdate($club,"ServerID",false);
            $nombre= $this->setForUpdate($club,"Nombre",true);
            $nlargo= $this->setForUpdate($club,"NombreLargo",true);
            $dir1= $this->setForUpdate($club,"Direccion1",true);
            $dir2= $this->setForUpdate($club,"Direccion2",true);
            $prov= $this->setForUpdate($club,"Provincia",true);
            $pais= $this->setForUpdate($club,"Pais",true);
            $c1= $this->setForUpdate($club,"Contacto1",true);
            $c2= $this->setForUpdate($club,"Contacto2",true);
            $c3= $this->setForUpdate($club,"Contacto3",true);
            $gps= $this->setForUpdate($club,"GPS",true);
            $web= $this->setForUpdate($club,"Web",true);
            $mail= $this->setForUpdate($club,"Email",true);
            $face= $this->setForUpdate($club,"Facebook",true);
            $gogl= $this->setForUpdate($club,"Google",true);
            $twit= $this->setForUpdate($club,"Twitter",true);
            $logo= $this->setForUpdate($club,"Logo",true);
            $feds= $this->setForUpdate($club,"Federations",false);
            $comments= $this->setForUpdate($club,"Observaciones",true);
            $baja= $this->setForUpdate($club,"Baja",false);

            // fase 1: buscar por ServerID
            // fase 2: buscar por Nombre (exacto)
            // PENDING: buscar el nombre "mas parecido", y obtener el ID
            // fase 3: si no se encuentra se crea. Ajustar el logo
        }
    }
}