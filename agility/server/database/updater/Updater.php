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
                "WHERE ServerID={$juez['ServerID']}";
            $res=$this->myDBObject->query($str);
            if (!$res) { $this->myLogger->error($this->myDBObject->conn->error); continue; }
            if ($this->myDBObject->conn->affected_rows!=0) continue; // next juez

            // fase 2: si no existe el Server ID se busca por nombre (exacto) entre los que no tienen serial id definido
            $str="UPDATE Jueces {$sid} {$dir1} {$dir2} {$pais} {$tel} {$intl} {$pract} {$email} {$feds} {$comments} ".
                "WHERE Nombre={$nombre} AND (ServerID=0)";
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
            if (!$res) $this->myLogger->error($this->myDBObject->conn->error);
        }
    }

    /**
     * find club with serial id=0 and nearest name than provided one
     * @param {string} Club name to search
     * @param int ServerID
     * @return {array} found club data or null if not found
     */
    function searchClub($search,$serverid=0) {
        $search=strtolower(trim($search));
        $search=str_replace("agility","",$search);
        $search=str_replace("club","",$search);
        // phase 1: search for server id
        if ($serverid!==0) {
            $res=$this->myDBObject->__select("*","Clubes","(ServerID={$serverid})");
            if ($res && $res['total']!=0 ) return $res['rows'][0];
        }
        // phase 2:
        // if server id not found search by name on whose clubs without server id
        // handle null club
        if ($search==="") $search="-- sin asignar --"; // remind lowercase!
        // remove extra chars to properly make club string likeness evaluation
        $search=preg_replace("/[^A-Za-z0-9 ]/", '', $search);
        $res=$this->myDBObject->__select("*","Clubes","(ServerID=0)");
        $better=array(0,array('ID'=>0,'Nombre'=>'') ); // percentage, data
        for ($idx=0; $idx<$res['total']; $idx++) {
            $club=$res['rows'][$idx];
            $dclub=strtolower($club['Nombre']);
            $dclub=str_replace("agility","",$dclub);
            $dclub=str_replace("club","",$dclub);
            $dclub=preg_replace("/[^A-Za-z0-9 ]/", '', $dclub);
            if ($dclub==='') continue; // skip blank. should not occur
            similar_text ( $search ,$dclub, $p );
            if ($p==100) return $club; // found. no need to continue search
            if (bccomp($p,$better[0])<=0) continue; // el nuevo "se parece menos", skip
            $better[0]=$p; $better[1]=$res['rows'][$idx]; // el nuevo "se parece mas", almacena
        }
        if ($better[0]<90) return null; // assume "not found" if similarity is less than 90%
        return $better[1];
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
            $str="UPDATE Clubes ".
                "{$nombre} {$nlargo} {$dir1} {$dir2} {$prov} {$pais} {$c1} {$c2} {$c3} ".
                "{$gps} {$web} {$mail} {$face} {$gogl} {$twit} {$logo} {$feds} {$comments} {$baja}".
                "WHERE ServerID={$club['ServerID']}";
            $res=$this->myDBObject->query($str);
            if (!$res) { $this->myLogger->error($this->myDBObject->conn->error); continue; }
            if ($this->myDBObject->conn->affected_rows!=0) continue; // next club

            // fase 2: buscar por Nombre entre los clubes que no tengan serial id
            // buscamos el ID del club que mas se parece
            $found=$this->searchClub($club['Nombre']);
            if ($found !== null) {
                $str="UPDATE Clubes ".
                    "{$sid} {$nombre} {$nlargo} {$dir1} {$dir2} {$prov} {$pais} {$c1} {$c2} {$c3} ".
                    "{$gps} {$web} {$mail} {$face} {$gogl} {$twit} {$logo} {$feds} {$comments} {$baja}".
                    "WHERE ID={$found['ID']}";
                $res=$this->myDBObject->query($str);
                if (!$res) { $this->myLogger->error($this->myDBObject->conn->error); continue; }
                if ($this->myDBObject->conn->affected_rows != 0) continue; //should allways occurs, but....
            }

            // arriving here means no serial id match nor club name match
            // fase 3: si no se encuentra se crea. Ajustar el logo

            // escapamos los textos
            $sid= $this->setForInsert($club,"ServerID",false);
            $nombre= $this->setForInsert($club,"Nombre",true);
            $nlargo= $this->setForInsert($club,"NombreLargo",true);
            $dir1= $this->setForInsert($club,"Direccion1",true);
            $dir2= $this->setForInsert($club,"Direccion2",true);
            $prov= $this->setForInsert($club,"Provincia",true);
            $pais= $this->setForInsert($club,"Pais",true);
            $c1= $this->setForInsert($club,"Contacto1",true);
            $c2= $this->setForInsert($club,"Contacto2",true);
            $c3= $this->setForInsert($club,"Contacto3",true);
            $gps= $this->setForInsert($club,"GPS",true);
            $web= $this->setForInsert($club,"Web",true);
            $mail= $this->setForInsert($club,"Email",true);
            $face= $this->setForInsert($club,"Facebook",true);
            $gogl= $this->setForInsert($club,"Google",true);
            $twit= $this->setForInsert($club,"Twitter",true);
            $logo= $this->setForInsert($club,"Logo",true);
            $feds= $this->setForInsert($club,"Federations",false);
            $comments= $this->setForInsert($club,"Observaciones",true);
            $baja= $this->setForInsert($club,"Baja",false);

            $str="INSERT INTO Clubes (".
                "ServerID,Nombre,NombreLargo,Direccion1,Direccion2,Provincia,Pais,Contacto1,Contacto2,Contacto3,".
                "GPS,Web,Email,Facebook,Google,Twitter,Logo,Federations,Observaciones,Baja".
                ") VALUES (".
                "{$sid},{$nombre},{$nlargo},{$dir1},{$dir2},{$prov},{$pais},{$c1},{$c2},{$c3},".
                "{$gps},{$web},{$mail},{$face},{$gogl},{$twit},{$logo},{$feds},{$comments},{$baja}".
                ")";

            $res=$this->myDBObject->query($str);
            if (!$res) $this->myLogger->error($this->myDBObject->conn->error);
        }
    }

    function handleGuias($data) {
        foreach($data as $guia) {
            // buscamos el club al que corresponde el serverid dado
            $found=$this->searchClub($guia['NombreClub'],$guia['ClubesServerID']);
            if (!$found) $guia['Club']=1; // Club not found, use ID:1 -> '-- Sin asignar --';

            // preparamos el update por
            $sid= $this->setForUpdate($guia,"ServerID",false);
            $nombre= $this->setForUpdate($guia,"Nombre",true);
            $tel= $this->setForUpdate($guia,"Telefono",true);
            $mail= $this->setForUpdate($guia,"Email",true);
            $club= $this->setForUpdate($found,"ID",false); // get ClubID from found club object
            $fed= $this->setForUpdate($guia,"Federation",false);
            $comments= $this->setForUpdate($guia,"Observaciones",true);
            $cat= $this->setForUpdate($guia,"Categoria",true);

            // fase 1: buscar por ServerID
            $str="UPDATE Guias ".
                "{$nombre} {$tel} {$mail} {$club} {$fed} {$comments} {$cat} ".
                "WHERE ServerID={$guia['ServerID']}";
            $res=$this->myDBObject->query($str);
            if (!$res) { $this->myLogger->error($this->myDBObject->conn->error); continue; }
            if ($this->myDBObject->conn->affected_rows!=0) continue; // next club

            // fase 2: buscar por nombre/federacion/club
            // en este caso buscamos coincidencia exacta, pues la posibilidad de nombres repetidos es alta
            $name=$this->setForInsert($guia,"Nombre",true);
            $str="UPDATE Guias ".
                "{$sid} {$nombre} {$tel} {$mail} {$club} {$fed} {$comments} {$cat} ".
                "WHERE (Nombre={$name}) AND (Federation={$guia['Federation']}) AND (Club={$found['ID']})";
            $res=$this->myDBObject->query($str);
            if (!$res) { $this->myLogger->error($this->myDBObject->conn->error); continue; }
            if ($this->myDBObject->conn->affected_rows!=0) continue; // next club

            // si llegamos hasta aquí, cortamos por lo sano y hacemos un insert
            $sid= $this->setForInsert($guia,"ServerID",false);
            $nombre= $this->setForInsert($guia,"Nombre",true);
            $tel= $this->setForInsert($guia,"Telefono",true);
            $mail= $this->setForInsert($guia,"Email",true);
            $club= $this->setForInsert($found,"ID",false); // get ClubID from found club object
            $fed= $this->setForInsert($guia,"Federation",false);
            $comments= $this->setForInsert($guia,"Observaciones",true);
            $cat= $this->setForInsert($guia,"Categoria",true);

            $str="INSERT INTO Guias ".
                "( ServerID,Nombre,Telefono,Email,Club,Federation,Observaciones,Categoria ".
                ") VALUES (".
                "{$sid},{$nombre},{$tel},{$mail},{$club},{$fed},{$comments},{$cat} ".
                ")";
            $res=$this->myDBObject->query($str);
            if (!$res) { $this->myLogger->error($this->myDBObject->conn->error); continue; }
            if ($this->myDBObject->conn->affected_rows!=0) continue; // next club
        }
    }

    private function searchGuia($nombre,$fed,$serverid) {
        // phase 1: search for server id
        if ($serverid!==0) {
            $res=$this->myDBObject->__select("*","Guias","(ServerID={$serverid})");
            if ($res && $res['total']!=0 ) return $res['rows'][0];
        }
        if ($nombre==="") {
            $res=$this->myDBObject->__select("*","Guias","(ID=1)"); // "-- Sin asignar --"
            if ($res && $res['total']!=0 ) return $res['rows'][0];
        }
        // phase 2:
        // if server id not found search by name on whose handlers without server id
        $name=$this->myDBObject->real_escape_string($nombre);
        $res=$this->myDBObject->__select("*","Guias","(Nombre='{$name}' AND (Federation=$fed)");
        if (!$res) return null;
        if ($res['total']==0) return null;
        if ($res['total']==1) return $res['rows'][0];
        // arriving here means several handlers without server id and same name on same federation.
        // Just a duplicate handler or two handlers with same name and different club ? What can i do here ?
        return null; // PENDING: what to do now ? AnyWay to discriminate by club
    }

    function handlePerros($data) {
        /*
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
         */
        foreach ($data as $perro) {
            // obtenemos el guia local a partir de los datos del servidor ( Nombre, GuiaServerID )

        }
    }
}