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
            ",
            "Clubs"=> " CREATE TABLE IF NOT EXISTS `MergeClubes` (
                `ID`          int(4) NOT NULL AUTO_INCREMENT,
                `ServerID`    int(4)   NOT NULL DEFAULT 0,
                `Nombre`      varchar(255) NOT NULL ,
                `NombreLargo` varchar(255) NOT NULL DEFAULT '\"\"',
                `Direccion1`  varchar(255) NOT NULL DEFAULT '',
                `Direccion2`  varchar(255) NOT NULL DEFAULT '',
                `Provincia`   varchar(32)  NOT NULL DEFAULT '-- Sin asignar --',
                `Pais`        varchar(32)  NOT NULL DEFAULT 'España',
                `Contacto1`   varchar(255) NOT NULL DEFAULT '',
                `Contacto2`   varchar(255) NOT NULL DEFAULT '',
                `Contacto3`   varchar(255) NOT NULL DEFAULT '',
                `GPS`         varchar(255) NOT NULL DEFAULT '',
                `Web`         varchar(255) NOT NULL DEFAULT '',
                `Email`       varchar(255) NOT NULL DEFAULT '',
                `Facebook`    varchar(255) NOT NULL DEFAULT '',
                `Google`      varchar(255) NOT NULL DEFAULT '',
                `Twitter`     varchar(255) NOT NULL DEFAULT '',
                `Logo`        varchar(255) NOT NULL DEFAULT 'agilitycontest.png',
                `Federations` int(4)       NOT NULL DEFAULT 1,
                `Observaciones` varchar(255) NOT NULL DEFAULT '',
                `Baja`        tinyint(1)   NOT NULL DEFAULT 0,
                `LastModified` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB AUTO_INCREMENT=692 DEFAULT CHARSET=utf8;
            ",
            "Judges"=>"CREATE TABLE IF NOT EXISTS `MergeJueces` (
                `ID`          int(4) NOT NULL AUTO_INCREMENT,
                `ServerID`    int(4)   NOT NULL DEFAULT 0,
                `Nombre`      varchar(255) NOT NULL,
                `Direccion1`  varchar(255) NOT NULL DEFAULT '',
                `Direccion2`  varchar(255) NOT NULL DEFAULT '',
                `Pais`        varchar(32)  NOT NULL DEFAULT 'España',
                `Telefono`    varchar(32)  NOT NULL DEFAULT '',
                `Internacional` tinyint(1) NOT NULL DEFAULT 0,
                `Practicas`   tinyint(1)   NOT NULL DEFAULT 0,
                `Email`       varchar(255) NOT NULL DEFAULT '',
                `Federations` int(4)       NOT NULL DEFAULT 1,
                `Observaciones` varchar(255) NOT NULL DEFAULT '',
                `LastModified` timestamp   NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;
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

}