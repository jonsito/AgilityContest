<?php header ("Content-type: text/css");
require_once(__DIR__."/../server/auth/Config.php");
$config = new Config();
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
* Estilos asociados a las diversas pantallas de visualizacion
*/

/**** estilos asociados al panel "LiveStream" */
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
    border-radius: 25px;
}

#vwls_video {
	width: 100%;
	height: auto;
	z-index: -1;
}

/******** elementos del panel  de llamada a pista */
.vwc_callEntry { /* elementos comunes */
	width:100%;
	border:0px;
	padding:0px;
	font-weight:bold;
	font-style:italic;
	text-align:left;
}
.vwc_callEntry tr { /* anchura de cada entrada */
	width:100%;
	height:5%;
}
.vwc_callTanda { /* fila de indicador de tanda */
	width:100%;
	background-color: <?php echo $config->getEnv('vw_hdrbg2')?>;
	color: <?php echo $config->getEnv('vw_hdrfg2')?>;
	text-align: right;
	font-size: 200%;
	padding:2%;
}
.vwc_callNumero { /* numero de orden de llamada a pista */
	width:10%;
	text-align:center;
	font-size:350%;
	font-style:none;
}
.vwc_callLogo { /* Logo (tiene tratamiento especial en videowall.php */
	width:10%;
}
.vwc_callDatos { /* datos del participante */
	width:15%;
	font-size:100%;
	padding-left:5px;
}
.vwc_callGuiaClub { /* nombre del guia y del club */
	width:25%;
	font-size:100%;
	padding-left:5px;
}
.vwc_callNombre { /* nombre del perro */
	width:40%;
	text-align:center;
	font-size:350%
}

/*********** elementos individuales de la tabla de resultados parciales */
.vwc_puesto {
    width: 50px;
	font-size: 30px;
}
.vwc_nombre {
    width: 200px;
	font-size: 20px;
}
.vwc_ftr {
    width: 30px;
	font-size: 15px;
}
.vwc_vel {
    width: 50px;
	font-size: 15px;
}
.vwc_rlarge {
    width: 75px;
	font-size: 15px;
}
.vwc_calif {
    width: 100px;
	font-size: 15px;
	font-style: italic;
}

/* cabecera de ventana de resultados parciales */
.vwc_trs {
	width:100%;
	padding:10px;
	background-color: <?php echo $config->getEnv('vw_hdrbg1')?>;
	color: <?php echo $config->getEnv('vw_hdrfg1')?>;
	font-weight: bold;
	font-style: italic;
	table-layout: fixed;
}
.vwc_trs th {
	text-align:left;
	font-size: 25px;
}
.vwc_trs td {
	text-align:right;
	font-size: 15px;
}

/* tabla de resultados parciales */
.vwc_tresultados {
	width: 100%;
	text-align: center;
	font-weight: bold;
}

/* subtabla de participantes en resultados parciales */
.vwc_trparticipantes {
	width: 350px;
	text-align: left;
	font-weight: bold;
}

/**********  cabecera flotante para resultados parciales, inscripciones y llamada a pista **********/
.vw_floatingheader {
	margin-top:0px;
	margin-bottom:0px;
	width:100%;
	padding:10px;
	background-color: <?php echo $config->getEnv('vw_hdrbg1')?>;
	color: <?php echo $config->getEnv('vw_hdrfg1')?>;
	font-weight: bold;
	font-style: italic;
	font-size:1.5em;
}

/************************** Elementos de la tabla de inscritos a la prueba ************/
	
td.vwi_club {
	width:90%;
	background-color: <?php echo $config->getEnv('vw_hdrbg2')?>;
	color: <?php echo $config->getEnv('vw_hdrfg2')?>;
	text-align:right;
	font-size:2em;
	font-style:italic;
	font-weight:bold;
	padding-right:25px;
}
