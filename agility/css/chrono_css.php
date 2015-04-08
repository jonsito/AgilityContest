<?php header ("Content-type: text/css");
require_once(__DIR__."/../server/auth/Config.php");
$config = Config::getInstance();
?>
/*
chrono_css.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

/*
* Estilos asociados a las diversas pantallas de visualizacion
*/

/**** estilos asociados al panel "LiveStream" */
/* font-size depende de si el livestream es combinado o autonomo */
#chrono_common {
	vertical-align: middle;
	line-height: 25px;
	font-weight: bold;
	border: none;
	border-width: 0px;
	z-index: 1;
}

.chrono_label {
	text-align: left;
	background-color: transparent;
	color: white;
}

.chrono_logo {
	background-color: transparent;
}

.chrono_data {
	text-align: center;
	background-color: #c0c0c0;
	color: white;
}

.chrono_tiempo {
	text-align: center;
	background-color: #c0c0c0;
	color: red;
	font-size: 5.5em;
	letter-spacing: 0.11em;
}
.chrono_fondo {
    background-color: rgba(127,127,127,<?php echo $config->getEnv('vw_alpha')?>);
    border: 3px solid black;
    border-radius: 10px;
}

#chrono_video {
	width: 100%;
	height: auto;
	z-index: -1;
}
