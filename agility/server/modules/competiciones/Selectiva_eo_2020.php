<?php

/*
Selectiva_eo_2019.php

Copyright  2013-2020 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
require_once( __DIR__."/Selectiva_eo_2018.php");
class Selectiva_eo_2020 extends Selectiva_eo_2018 {

    function __construct($name="Selectiva European Open 2020") {
        parent::__construct($name);
        $this->federationID=0;
        $this->competitionID=18;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20191115_2109";
        $this->selectiva=1;
        $this->federationLogoAllowed=true;
    }

    // grados 1 y 2 son a cinco alturas; la selectiva (grado 3) es a 3 alturas
    public function getRoundHeights($manga) {
        $myDbObject= new DBObject("getRoundHeights");
        $result=$myDbObject->__getObject('mangas',$manga);
        if (!is_object($result)) return parent::getRoundHeights($manga);
        if(in_array($result->Tipo,array(6,11) ) ) return 3; // grado 3
        return 5;
    }
}