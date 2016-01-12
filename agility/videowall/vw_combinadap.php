<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Videowall::combinada");
if ( ! $am->allowed(ENABLE_VIDEOWALL)) { include_once("unregistered.php"); return 0;}
?>
<!--
vw_llamada.inc

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<!-- Pantalla de de visualizacion combinada llamada/parciales -->
<div id="vw_combinadap-window">
    <div id="vw_combinadap-layout"">
        <div data-options="region:'north'" style="height:10%">Cabecera</div>
        <div data-options="region:'west'" style="width:30%;">Llamada a pista</div>
        <div data-options="region:'center',border:false" style="padding:5px">Espacio vacio</div>
        <div data-options="region:'east'" style="width:60%;">Resultados parciales</div>
        <div data-options="region:'south',border:false" style="height:30%;">
            <div id="vw_combinadap2-layout">
                <div data-options="region:'north'" style="height:50px">Perro en pista</div>
                <div data-options="region:'west',border:false" style="width:30%">Patrocinadores</div>
                <div data-options="region:'center',border:false">Hueco central</div>
                <div data-options="region:'east'" style="width:60%">Ultimos resultados</div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $('#vw_combinadap-layout').layout({fit:true});
    $('#vw_combinadap2-layout').layout({fit:true});

    $('#vw_combinadap-window').window({
        fit:true,
        noheader:true,
        border:false,
        closable:false,
        collapsible:false,
        collapsed:false,
        resizable:true,
        onOpen: function() {
            // startEventMgr(workingData.sesion,vw_procesaCombinada);
        }
    });

</script>