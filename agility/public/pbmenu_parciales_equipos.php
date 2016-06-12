<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Public::parciales_eq3");
if ( ! $am->allowed(ENABLE_PUBLIC)) { include_once("unregistered.php"); return 0;}
?>

<!--
pbmenu_parciales_eq3.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<!-- Presentacion de resultados parciales en pruebas por equipos (3 mejores de cuatro)  -->

<div id="pb_parciales-panel">
    <div id="pb_parciales-layout" style="width:100%">
        <div id="pb_parciales-Cabecera"  style="height:15%;" class="pb_floatingheader" data-options="region:'north',split:false,collapsed:false">

            <a id="pb_header-link" class="easyui-linkbutton" onClick="updateParciales(workingData.datosManga.Mode,workingData.datosManga);" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/agilitycontest.png" width="40" />
            </a>
            <span id="header-combinadaFlag" style="display:none">false</span> <!--indicador de combinada:false-->
            <span style="float:left;padding:5px" id="pb_header-infocabecera"><?php _e('Header'); ?></span>
            <span style="float:right;padding:5px" id="pb_header-texto">
                <?php _e('Partial scores'); ?><br/>
                <span id="enumerateParciales" style="width:200px"></span>
            </span><br/>
            <!-- Datos de TRS y TRM -->
            <?php include_once(__DIR__."/../lib/templates/parcial_round_data.inc.php");?>
        </div>
        <div id="pb_parciales-data" data-options="region:'center'" >
            <div class="scores_table">
                <?php include_once(__DIR__."/../lib/templates/parcial_teams.inc.php");?>
            </div>
        </div>
        <div id="pb_parciales-footer" data-options="region:'south',split:false" style="height:10%;" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
    </div>
</div> <!-- pb_parciales-window -->

<script type="text/javascript">

if (isMobileDevice()) {
    $('#pb_parciales-Cabecera').css('height','90%');
}

addTooltip($('#pb_header-link').linkbutton(),'<?php _e("Update partial scores table"); ?>');
$('#pb_parciales-layout').layout({fit:true});

$('#pb_parciales-panel').panel({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true,
	// 1 minute poll is enouth for this, as no expected changes during a session
	onOpen: function() {
        // update header
        pb_getHeaderInfo();
        // update footer
        pb_setFooterInfo();
	}
});


// fire autorefresh if configured
function pbmenu_updateParcialesEquipos() {
    var rtime=parseInt(ac_config.web_refreshtime);
    var options=$('#parciales_equipos-datagrid').datagrid('options');
    if ( options.expandCount <= 0 ){
        options.expandCount=0;
        updateParciales(workingData.datosManga.Mode,workingData.datosManga);
    }
    if (rtime!=0) workingData.timeout=setTimeout(pbmenu_updateParcialesEquipos,1000*rtime);
}

setTimeout(function(){ $('#enumerateParciales').text(workingData.datosManga.Nombre)},0);
if (workingData.timeout!=null) clearTimeout(workingData.timeout);
pbmenu_updateParcialesEquipos();
    
</script>