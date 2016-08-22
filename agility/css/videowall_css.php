<?php header ("Content-type: text/css");
require_once(__DIR__."/../server/auth/Config.php");
$config = Config::getInstance();

$fontfamily=$config->getEnv("vws_font");
$fontsize=''.$config->getEnv("vws_fontsize").'vw';
?>
/*
videowall_css.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

/**********  cabeceras flotante para videomarcadores **********/

.vws_theader {
    /* font-style: italic; */
    font-family: Arial Black;
}

.vw_floatingheader {
    margin-top:0px;
    margin-bottom:0px;
    padding:5px;
    background-color: <?php echo $config->getEnv('vw_hdrbg1')?>;
    color: <?php echo $config->getEnv('vw_hdrfg1')?>;
    font-weight: bold;
    font-style: italic;
    font-size:1.4vw;
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
    font-size:1.4vw;
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
    padding-top:0px;
    background-color: <?php echo $config->getEnv('vw_hdrbg3')?>;
    color: <?php echo $config->getEnv('vw_hdrfg3')?>;
}

.vw_equipos3 span {
    vertical-align:top;
    display:inline-block;
}

/************* estilos de la tabla de inscripciones por equipos */
.vw_inscripciones_eq3_teamrow {
    background-color:<?php echo $config->getEnv('vw_hdrbg2')?>;
    color:<?php echo $config->getEnv('vw_hdrfg2')?>;
    font-weight:bold;
    height:40px;
    line-height: 40px;
}

/************ estilos asociados a las vistas simplificadas **************/

#vws-window { /* color de fondo y textos genericos de la pantalla */
    color:<?php echo $config->getEnv('vws_hdrfg1')?>;
    background-color: <?php echo $config->getEnv('vws_hdrbg1')?>;
}

.vws_theader { /* cabeceras de las tablas */
    height:6vh;
    color:<?php echo $config->getEnv('vws_hdrfg2')?>;
    background-color: <?php echo $config->getEnv('vws_hdrbg2')?>;
    text-align:center;
    font-size:1.9vw;
    padding-top:0.8vw;
}

#vws_hdr_form input { /* formularios de la cabecera */
    color:<?php echo $config->getEnv('vws_hdrfg1')?>;
    background-color: <?php echo $config->getEnv('vws_hdrbg1')?>;
    text-align:left;
    font-weight: bold;
    margin:0;
    padding:0;
    border:none;
    outline:none;
    font-size:2.2vw;
}

#vws_hdr_form input.trs  {
    font-size:1.5vw;
}

.vws_css_call_0 {
    background-color: <?php echo $config->getEnv('vws_rowcolor1')?>;
}
.vws_css_call_1 {
    background-color: <?php echo $config->getEnv('vws_rowcolor2')?>;
}
.vws_css_results_0 {
    background-color: <?php echo $config->getEnv('vws_rowcolor3')?>;
}
.vws_css_results_1 {
    background-color: <?php echo $config->getEnv('vws_rowcolor4')?>;
}
.vws_css_current_0 {
    background-color: <?php echo $config->getEnv('vws_rowcolor5')?>;
}
.vws_css_current_1 {
    background-color: <?php echo $config->getEnv('vws_rowcolor6')?>;
}

.vws_imgpadding {
    padding:5px;
}

.vws_entry .left { text-align: left; }
.vws_entry .right { text-align: right; }
.vws_entry .center { text-align: center; }

/* borde a la izquierda o a la derecha */
.vws_entry .lborder {
    border-left:2px solid <?php echo $config->getEnv('vws_linecolor')?>;
    padding-left:0.5vw;
}

.vws_entry .rpadding {
    padding-right:0.5vw;
}

.vws_entry {
    font-family:<?php echo $fontfamily; ?>;
    font-size: <?php echo $fontsize ?>;
    font-weight: bold;
    font-stretch:condensed;
    text-align:center;
}

.vws_entry input[type="text"] {
    margin:0;
    padding:0;
    border:none;
    outline:none;
    color:inherit;
    background-color:inherit;
    font-family: <?php echo $fontfamily; ?> ;
    font-size: <?php echo $fontsize ?>;
    font-weight: bold;
    font-stretch:condensed;
    text-align: left;
}

.vws_entry span {
    margin:0;
    padding-top:0.9vw;
    border:none;
    outline:none;
    color:inherit;
    background-color:inherit;
}

.ws_rowStylerDown {
    color:<?php echo $config->getEnv('vws_linecolor'); ?>;
    height: 10vw;
    line-height:10vw;
    font-size: 5vw;
    background-color:<?php echo $config->getEnv('vws_rowcolor5'); ?>;
}

.vws_entrenamientos .datagrid-row {
    color:<?php echo $config->getEnv('vws_linecolor'); ?>;
    height: 3.5vw;
}

.vws_entrenamientos .datagrid-row .datagrid-cell {
    font-family: <?php echo $fontfamily; ?>;
    font-weight: bold;
    font-stretch:condensed;
    text-align:center;
    font-size: 1.7vw;
    line-height: 1.8vw;
}