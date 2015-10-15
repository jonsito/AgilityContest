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

/**** handle 16:9 aspect ratio */
#chrono_Screen-dialog {
	position:relative;
	width:90%;
	height:480px;
	padding:5px 5px
}

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

.chrono_logo {
	background-color: transparent;
}

/* datos de Flt/Toc/Reh/Tim */
.chrono_data {
	text-align: center;
	background-color: transparent;
	font-size:2.0em;
	font-size:3.0vw;
	color: <?php echo $config->getEnv('vw_hdrfg2'); ?>;
}
.chrono_dataLbl { text-align: left; }

/* datos del equipo participante */
.chrono_info {
	text-align: left;
	background-color: transparent;
	font-size:1.3em;
	font-size:2.0vw;
	color: <?php echo $config->getEnv('vw_hdrfg3'); ?>;
}

/* informacion de la manga */
.chrono_header {
	text-align: left;
	background-color: transparent;
	font-size:1.2em;
	font-size:1.5vw;
	color: <?php echo $config->getEnv('vw_hdrfg1'); ?>;
}

/* Numerajos del tiempo */
.chrono_tiempo {
	text-align: center;
	background-color: transparent;
	color: red;
	font-size: 11.0em;
	font-size: 20vw;
	letter-spacing: 0.11em;
}

/* indicadores auxiliares (manual, reconocimiento, error, etc) */
.chrono_flags {
	text-align: right;
	background-color: transparent;
	color: red;
	font-size: 0.9em;
	font-size: 1.2vw;
}
.chrono_fondo {
    border: 3px solid black;
    border-radius: 10px;
}

.chrono_flogo { background-color: '#ffffff'; }
.chrono_fheader { background-color: <?php echo $config->getEnv('vw_hdrbg1'); ?>; }
.chrono_ftiempo { background-color: rgba(192,192,192,<?php echo $config->getEnv('vw_alpha')?>); }
.chrono_fdata {	background-color: <?php echo $config->getEnv('vw_hdrbg2'); ?>; }
.chrono_finfo {	background-color: <?php echo $config->getEnv('vw_hdrbg3'); ?>; }

#chrono_video {
	width: 100%;
	height: auto;
	z-index: -1;
}
