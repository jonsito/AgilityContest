<?php

/*
Selectiva_eo_2019.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
require_once( __DIR__."/Selectiva_eo_2020.php");
class Selectiva_eo_2021 extends Selectiva_eo_2020 {

    function __construct($name="Selectiva European Open 2021") {
        parent::__construct($name);
        $this->federationID=0;
        $this->competitionID=22;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20210121_1120";
        $this->selectiva=1;
        $this->federationLogoAllowed=true;
    }

}