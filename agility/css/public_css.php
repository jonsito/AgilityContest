<?php header ("Content-type: text/css");
require_once(__DIR__."/../server/auth/Config.php");
$config = Config::getInstance();
?>
/*
public_css.php

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
* Estilos asociados a las diversas pantallas de acceso internet
*/

/**********  cabeceras flotante para acceso publico **********/
.pb_floatingheader {
    margin-top:0px;
    margin-bottom:0px;
    padding:5px 5px 0px 5px;
    background-color: <?php echo $config->getEnv('pb_hdrbg1')?>;
    color: <?php echo $config->getEnv('pb_hdrfg1')?>;
    font-weight: bold;
    font-style: italic;
    font-size:1.5vw;
}

.pb_floatingfooter {
    margin-top:0px;
    margin-bottom:0px;
    padding:5px;
    background-color: <?php echo $config->getEnv('pb_hdrbg1')?>;
    color: <?php echo $config->getEnv('pb_hdrfg1')?>;
    font-weight: bold;
    font-style: italic;
    font-size:1.5vw;
}

/*************** cabecera de ventana de resultados ************ */
.pb_trs {
    width:100%;
    padding:0px 5px 5px 5px;
    background-color: <?php echo $config->getEnv('pb_hdrbg2')?>;
    color: <?php echo $config->getEnv('pb_hdrfg2')?>;
    font-weight: bold;
    font-style: italic;
    table-layout: fixed;
}
.pb_trs th {
    text-align:left;
    font-size: 1.1vw;
}
.pb_trs td {
    text-align:right;
    font-size: 1.0vw;
}

/* tip for fix data size in smartphones ----------- */
@media only screen and (max-width: 760px) {
    .pb_trs th {
        text-align:left;
        font-size: 1.4vw;
    }
    .pb_trs td {
        text-align:right;
        font-size: 1.3vw;
    }
}
