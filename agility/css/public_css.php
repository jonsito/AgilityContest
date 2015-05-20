<?php header ("Content-type: text/css");
require_once(__DIR__."/../server/auth/Config.php");
$config = Config::getInstance();
?>
/*
public_css.php

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
* Estilos asociados a las diversas pantallas de acceso internet
*/

/**********  cabeceras flotante para acceso publico **********/
.pb_floatingheader {
    margin-top:0px;
    margin-bottom:0px;
    padding:10px;
    background-color: <?php echo $config->getEnv('vw_hdrbg1')?>;
    color: <?php echo $config->getEnv('vw_hdrfg1')?>;
    font-weight: bold;
    font-style: italic;
    font-size:2.0em;
}

.pb_floatingfooter {
    margin-top:0px;
    margin-bottom:0px;
    padding:10px;
    background-color: <?php echo $config->getEnv('vw_hdrbg3')?>;
    color: <?php echo $config->getEnv('vw_hdrfg3')?>;
    font-weight: bold;
    font-style: italic;
    font-size:2.0em;
}
/************************** Elementos de la tabla de inscritos a la prueba ************/

td.pb_club {
    width:90%;
    background-color: <?php echo $config->getEnv('vw_hdrbg2')?>;
    color: <?php echo $config->getEnv('vw_hdrfg2')?>;
    text-align:right;
    font-size:2em;
    font-style:italic;
    font-weight:bold;
    padding-right:25px;
}

/*************** cabecera de ventana de resultados ************ */
.pb_trs {
    width:100%;
    padding:10px;
    background-color: <?php echo $config->getEnv('vw_hdrbg1')?>;
    color: <?php echo $config->getEnv('vw_hdrfg1')?>;
    font-weight: bold;
    font-style: italic;
    table-layout: fixed;
}
.pb_trs th {
    text-align:left;
    font-size: 18px;
}
.pb_trs td {
    text-align:right;
    font-size: 12px;
}

/************** datos de las tablas de clasificaciones por equipos */
.pb_equipos3 {
    border-width:0px;
    table-layout:fixed;
    width:1000px;
    overflow:hidden;
    white-space:nowrap;
}

.pb_equipos3 td {
    display:inline-block;
    padding-right: -5px;
    text-align:right;
    vertical-align:top;
    width:20%;
}

/************* estilos de la tabla de inscripciones por equipos
.pb_inscripciones_eq3_teamrow {
    background-color:<?php echo $config->getEnv('vw_hdrbg2')?>;
    color:<?php echo $config->getEnv('vw_hdrfg2')?>;
    font-weight:bold;
    height:40px;
    line-height: 40px;
}