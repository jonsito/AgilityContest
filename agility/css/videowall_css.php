<?php header ("Content-type: text/css");
require_once(__DIR__."/../server/auth/Config.php");
$config = Config::getInstance();
?>
/*
videowall_css.php

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
* Estilos asociados a las diversas pantallas de videomarcadores
*/
/********** Estilos de la pantalla liveStream *****************/
#vwls_common {
    vertical-align: middle;
    line-height: 25px;
    font-weight: bold;
    border: none;
    border-width: 0px;
    z-index: 1;
}

.vwls_label {
    text-align: left;
    background-color: transparent;
    color: white;
}

.vwls_logo {
    background-color: transparent;
}

.vwls_data {
    text-align: center;
    background-color: #c0c0c0;
    color: white;
}

.vwls_fondo {
    background-color: rgba(127,127,127,<?php echo $config->getEnv('vw_alpha')?>);
    border: 3px solid black;
    border-radius: 10px;
}

#vwls_video {
    width: 100%;
    height: auto;
    z-index: -1;
}


/**********  cabeceras flotante para videomarcadores **********/
.vw_floatingheader {
    margin-top:0px;
    margin-bottom:0px;
    padding:10px;
    background-color: <?php echo $config->getEnv('vw_hdrbg1')?>;
    color: <?php echo $config->getEnv('vw_hdrfg1')?>;
    font-weight: bold;
    font-style: italic;
    font-size:1.8em;
}

.vw_floatingfooter {
    height:60px;
    margin-top:0px;
    margin-bottom:0px;
    padding:5px;
    background-color: <?php echo $config->getEnv('vw_hdrbg3')?>;
    color: <?php echo $config->getEnv('vw_hdrfg3')?>;
    font-weight: bold;
    font-style: italic;
    font-size:1.8em;
}
/************************** Elementos de la tabla de inscritos a la prueba ************/

td.vw_club {
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
.vw_trs {
    width:100%;
    padding:10px;
    background-color: <?php echo $config->getEnv('vw_hdrbg1')?>;
    color: <?php echo $config->getEnv('vw_hdrfg1')?>;
    font-weight: bold;
    font-style: italic;
    table-layout: fixed;
}
.vw_trs th {
    text-align:left;
    font-size: 18px;
}
.vw_trs td {
    text-align:right;
    font-size: 12px;
}

/************** datos de las tablas de clasificaciones por equipos */
.vw_equipos3 {
    border-width:0px;
    table-layout:fixed;
    width:1000px;
    overflow:hidden;
    white-space:nowrap;
}

.vw_equipos3 span {
    display:inline-block;
    padding-right: -5px;
    text-align:right;
    vertical-align:top;
    width:20%;
}

/************* estilos de la tabla de inscripciones por equipos
.vw_inscripciones_eq3_teamrow {
    background-color:<?php echo $config->getEnv('vw_hdrbg2')?>;
    color:<?php echo $config->getEnv('vw_hdrfg2')?>;
    font-weight:bold;
    height:40px;
    line-height: 40px;
}