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
    /*line-height: 25px; */
    font-weight: bold;
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

/* labels de F/T/R */
.vwls_dlabel {
    text-align: left;
    background-color: transparent;
    color: white;
}

/* datos de tiempo */
.vwls_dtime {
    text-align: center;
    background-color: transparent;
    color: white;
}

/* datos de F/T/R */
.vwls_data {
    text-align: center;
    color: white;
}

.vwls_logo {
    background-color: transparent;
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

/************* nueva generacion de vistas combinadas **************/
.vwc_top {
    padding: 0px;
    background-color: <?php echo $config->getEnv('vw_hdrbg1')?>;
    color: <?php echo $config->getEnv('vw_hdrfg1')?>;
}

.vwc_live {
    padding:0px;
    background-color: <?php echo $config->getEnv('vw_hdrbg2')?>;
    color: <?php echo $config->getEnv('vw_hdrfg2')?>;
    vertical-align: middle;
    font-weight: bold;
    border: none;
    border-width: 0px;
}

.vwc_bottom {
    padding:0px;
    background-color: <?php echo $config->getEnv('vw_hdrbg3')?>;
    color: <?php echo $config->getEnv('vw_hdrfg3')?>;
}

.vwc_header {
    margin-top:0px;
    margin-bottom:0px;
    padding:5px;
    font-weight: bold;
    font-style: italic;
    font-size:1.3vw;
}

.vwc_footer {
    height:60px;
    margin-top:0px;
    margin-bottom:0px;
    font-weight: bold;
    font-style: italic;
    font-size:1.8em;
}

/* datos de informacion del perro */
.vwc_label {
    text-align: left;
    font-size:1.5vw;
}

/* labels de F/T/R */
.vwc_dlabel {
    text-align: right;
    font-size:2.2vw;
}

/* datos de F/T/R */
.vwc_data {
    text-align: left;
    font-size:2.2vw;
}

/* datos de tiempo */
.vwc_dtime {
    text-align: center;
    font-size:2.2vw;
}

.vwc_logo {
    background-color: transparent;
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
    padding:5px;
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
    overflow:hidden;
    white-space:nowrap;
}

.vw_equipos3 span {
    display:inline-block;
    padding-right: -5px;
    text-align:right;
    vertical-align:top;
}

/************* estilos de la tabla de inscripciones por equipos */
.vw_inscripciones_eq3_teamrow {
    background-color:<?php echo $config->getEnv('vw_hdrbg2')?>;
    color:<?php echo $config->getEnv('vw_hdrfg2')?>;
    font-weight:bold;
    height:40px;
    line-height: 40px;
}