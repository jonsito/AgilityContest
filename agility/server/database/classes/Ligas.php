<?php
/*
Ligas.php

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
require_once("DBObject.php");
require_once("Resultados.php");

class Ligas extends DBObject {

    /**
     * Ligas constructor.
     * @param $file object name used for debbugging
     * @param $jornadaid Jornada ID
     * @throws Exception on invalid or not found jornada
     */
    function __construct($file,$jornadaid) {
        parent::__construct($file);
        if ($jornadaid <= 0) throw new Exception("Ligas::construct() invalid League ID");
    }

    function update() {

    }
}