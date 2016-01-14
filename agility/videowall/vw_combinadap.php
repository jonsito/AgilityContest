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

<!--
Pantalla de de visualizacion combinada llamada/parciales
 prefijo: vwcp (video wall combinada parcial
-->
<div id="vwcp-window">
    <div id="vwcp-layout">
        <div data-options="region:'north'" class="vwcp_common vwcp_floatingheader" style="height:10%">
            <div style="display:inline-block;width=100%;padding:0px">
                <img  id="vwcp_header-logo" src="/agility/images/logos/rsce.png"/>
                <span id="vwcp_header-infoprueba"><?php _e('Contest'); ?></span>
                <span id="vwcp_header-infojornada"><?php _e('Journey'); ?></span>
                <span id="vwcp_header-combinadaFlag" style="display:none">true</span> <!--indicador de combinada-->
                <span id="vwcp_header-ring" style="text-align:right"><?php _e('Ring'); ?></span>
                <span id="vwcp_header-calltoring" style="text-align:left"><?php _e('Call to ring'); ?></span>
                <span id="vwcp_header-partialscores" style="text-align:right">
                    <?php _e('Partial scores'); ?> -
                    <span id="vwcp_header-infomanga">&nbsp;</span>
                </span>
            </div>
        </div>
        <div data-options="region:'west'" style="width:30%;">Llamada a pista</div>
        <div data-options="region:'center',border:false" class="vwcp_common">Espacio vacio</div>
        <div data-options="region:'east'" style="width:60%;">Resultados parciales</div>
        <div data-options="region:'south',border:false" style="height:30%;">
            <div id="vwcp-layout2">
                <div data-options="region:'north'" style="height:40%">Perro en pista</div>
                <div data-options="region:'west',border:false" style="width:30%">Patrocinadores</div>
                <div data-options="region:'center',border:false">Hueco central</div>
                <div data-options="region:'east'" style="width:60%">Ultimos resultados</div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $('#vwcp-layout').layout({fit:true});
    $('#vwcp-layout2').layout({fit:true});

    $('#vwcp-window').window({
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

    var layout= {'rows':110,'cols':1900};
    doLayout(layout,"#vwcp_header-logo",	        0,	    0,	100,	100	);
    doLayout(layout,"#vwcp_header-infoprueba",	100,	0,	1800,	25	);
    doLayout(layout,"#vwcp_header-infojornada",	100,	25,	1800,	25	);
    doLayout(layout,"#vwcp_header-ring",     	100,	50,	1780,	25	);
    doLayout(layout,"#vwcp_header-calltoring",	100,	75,	600,	25	);
    doLayout(layout,"#vwcp_header-partialscores",700,	75,	1180,	25	);
</script>