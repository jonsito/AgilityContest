<?php

/*
Provincias.php

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

require_once(__DIR__."/../../logging.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/DBObject.php");

class Provincias extends DBObject {

	function select() {
		// evaluate offset and row count for query
		$q=http_request("q","s","");
		$c=http_request("Country","s","");
		$prov = ($q!=="")? "Provincia LIKE '%$q%'" : "1";
		$ctry = ($c!=="")? "AND Pais = '$c' " : "";
		$result = $this->__select(
			/* SELECT */ "*",
			/* FROM */	"provincias",
			/* WHERE */	"($prov $ctry) OR ( Codigo=0 )",
			/* ORDER */ "Provincia ASC",
			/* LIMIT */ ""
		);
		return $result;
	}
}
	
?>