<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 2/04/16
 * Time: 16:20
trainingtable_reader.php

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

require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../auth/Config.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/../../modules/Federations.php");
require_once(__DIR__."/../database/classes/DBObject.php");
require_once(__DIR__.'/Spout/Autoloader/autoload.php');
require_once(__DIR__.'/dog_reader.php');

class EntrenamientosReader extends DogReader {

    protected $prueba;

    public function __construct($name,$pruebaID,$options) {
        $this->myDBObject = new DBObject($name);
        $this->prueba=$this->myDBObject->__selectAsArray("*","Pruebas","ID=$pruebaID");
        if (!is_array($this->prueba))
            throw new Exception("EntrenamientosReader::construct(): invalid Prueba ID: $pruebaID");
        parent::__construct("ImportExcel(training session)",$this->prueba['RSCE'],$options);

        // instead of using parent field list, use our own one
        $this->fieldList= array(
            // name => index, required (1:true 0:false-to-evaluate -1:optional), default
            // 'ID' =>      array (  -1,  0,  "i", "ID",        " `ID` int(4) UNIQUE NOT NULL, "), // automatically added
            // Prueba: fixed
            // Orden: to be evaluated
            // club related data
            // in international contests user can provide ISO country name either in "Club" or in "Country" field
            'ClubID' =>     array (  -2,    0, "i", "ClubID",    " `ClubID` int(4) NOT NULL DEFAULT 0, "),  // to be evaluated by importer
            'Club' =>       array (  -3,    1, "s", "NombreClub"," `NombreClub` varchar(255) NOT NULL,"),  // Club's Name. required
            'Country' =>    array (  -4,   -1, "s", "Pais",      " `Pais` varchar(255) NOT NULL,"),  // Country. optional
            'Date' =>       array (  -5,    1, "s", "Fecha",     " `Fecha` date DEFAULT '2016-01-01', "), // required
            'CheckIn' =>    array (  -6,    1, "s", "Firma",     " `Firma` timestamp  DEFAULT 0 , "), // required
            'Veterinary' => array (  -7,    1, "s", "Veterinario"," `Veterinario` timestamp DEFAULT  0 , "), // required
            'Start' =>      array (  -8,    1, "s", "Inicio",    " `Entrada` timestamp DEFAULT  0 , "), // required
            'Duration' =>   array (  -9,    1, "s", "Duracion",  " `Duracion` int(4) NOT NULL DEFAULT 0, "), // required segundos
            // datos de los cuatro rings
            'Key1' =>       array (  -10,   1, "s", "Key1",      " `Key1` varchar(32) DEFAULT 'L', "), // required
            'Value1' =>     array (  -11,   1, "s", "Value1",    " `Value1` int(4) NOT NULL DEFAULT 0, "), // required
            'Key2' =>       array (  -12,   1, "s", "Key2",      " `Key2` varchar(32) DEFAULT 'M', "), // required
            'Value2' =>     array (  -13,   1, "s", "Value2",    " `Value2` int(4) NOT NULL DEFAULT 0, "), // required
            'Key3' =>       array (  -14,   1, "s", "Key3",      " `Key3` varchar(32) DEFAULT 'S', "), // required
            'Value3' =>     array (  -15,   1, "s", "Value3",    " `Value3` int(4) NOT NULL DEFAULT 0, "), // required
            'Key4' =>       array (  -16,  -1, "s", "Key4",      " `Key4` varchar(32) DEFAULT 'T', "), // 4th ring is optional in 3 height
            'Value4' =>     array (  -17,  -1, "s", "Value4",    " `Value4` int(4) NOT NULL DEFAULT 0, "), // 4th ring is optional in 3 height
            // comentarios
            'Comments' =>   array (  -18,  0, "i", "Observaciones", " `Observaciones` varchar(255) DEFAULT '', "),  // optional
            // Estado: default -1
        );
        // fix fields according contest type
        $fedobj=Federations::getFederation($this->federation);
        if ($fedobj->isInternational()) { $this->fieldList['Club'][1]=-1; $this->fieldList['Country'][1]=1; } // country/club
        if ($fedobj->get('Heights')==4) { $this->fieldList['Key4'][1]=1; $this->fieldList['Value4'][1]=1; } // required on 4 heights
    }

}
?>