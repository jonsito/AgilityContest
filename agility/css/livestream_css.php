<?php header ("Content-type: text/css");
require_once(__DIR__."/../server/auth/Config.php");
$config = Config::getInstance();
?>
/*
livestream_css.php

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

/*
* Estilos asociados a las diversas pantallas de live stream
*/
#osd_common {
    vertical-align: middle;
    font-size:1.25vw;
    font-family: "Arial Black", Gadget, sans-serif;
    border: none;
    border-width: 0px;
    z-index: 1;
}

#vwls_common {
    vertical-align: middle;
    font-size:1.0vw;
    font-weight: bold;
    // font-family: "Arial Black", Gadget, sans-serif;
    border: none;
    border-width: 0px;
    z-index: 1;
}

/* datos de informacion del perro */
.vwls_label {
text-align: left;
background-color: transparent;
color: white;
}

/* El dorsal un poco mas grande */
.vwls_dorsal {
    font-size:1.5vw;
}

/* labels de F/T/R */
.vwls_dlabel {
    text-align: left;
    background-color: transparent;
    color: white;
    z-index: 2;
}

/* datos de tiempo */
.vwls_dtime {
    text-align: center;
    background-color: transparent;
    color: white;
    font-size:1.2vw;
    z-index: 2;
}

/* datos de F/T/R */
.vwls_data {
    text-align: center;
    color: white;
    z-index: 2;
}

.vwls_logo {
    z-index: 1;
    background-color: transparent;
}


.vwls_fondo_combined {
    background-color: rgba(127,127,127,<?php echo $config->getEnv('vw_alpha')?>);
    border: 3px solid black;
    border-radius: 15px;
}

.vwls_fondo_chromakey {
    background-color: rgb(127,127,127);
    border: 3px solid black;
    border-radius: 12px;
    z-index: 0;
}

#vwls_video {
    width: 100%;
    height: auto;
    z-index: -1;
}
