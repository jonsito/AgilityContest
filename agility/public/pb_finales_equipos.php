<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Public::finales_eq3");
if ( ! $am->allowed(ENABLE_PUBLIC)) { include_once("unregistered.php"); return 0;}
?>
<!--
pb_finales_equipos.php

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


<!-- Acceso publico a Presentacion de resultados finales en pruebas por equipòs -->

<div id="pb_finales-window">
    <div id="pb_finales-layout" style="width:100%">
        <div id="pb_finales-Cabecera" style="height:20%;" class="pb_floatingheader"
             data-options="
                region:'north',
                split:true,
                title:'<?php _e('Final scores');?>',
                collapsed:false,
                onCollapse:function(){
                	setTimeout(function(){
				    	var top = $('#pb_finales-layout').layout('panel','expandNorth');
				    	var round = $('#enumerateFinales').combogrid('getText');
					    top.panel('setTitle','<?php _e('Final scores');?>: '+round);
				    },0);
                }
                ">
            <a id="pb_header-link" class="easyui-linkbutton" onClick="updateFinales();" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/agilitycontest.png" width="40" />
            </a>
            <span style="float:left;padding:5px" id="pb_header-infocabecera"><?php _e('Header'); ?></span>
            <span style="float:right" id="pb_header-texto">
                <?php _e('Final scores'); ?><br/>
                <label for="enumerateFinales" style="font-size:1.2vw"><?php _e('Series'); ?>:</label>
		        <select id="enumerateFinales" style="width:200px"></select>
            </span>
            <!-- Datos de TRS y TRM -->
            <table class="pb_trs">
                <tbody>
                <tr>
                    <th id="finales-NombreRonda" colspan="2">(<?php _e('No round selected'); ?>)</th>
                    <th id="finales-Juez1" colspan="2" style="text-align:center"><?php _e('Judge'); ?> 1:</th>
                    <th id="finales-Juez2" colspan="2" style="text-align:center"><?php _e('Judge'); ?> 2:</th>
                </tr>
                <tr style="text-align:right">
                    <td id="finales-Ronda1"><?php _e('Data info for round'); ?> 1:</td>
                    <td id="finales-Distancia1"><?php _e('Distance'); ?>:</td>
                    <td id="finales-Obstaculos1"><?php _e('Obstacles'); ?>:</td>
                    <td id="finales-TRS1"><?php _e('Standard C. Time'); ?>:</td>
                    <td id="finales-TRM1"><?php _e('Maximum C. Time'); ?>:</td>
                    <td id="finales-Velocidad1"><?php _e('Speed'); ?>:</td>
                </tr>
                <tr style="text-align:right">
                    <td id="finales-Ronda2"><?php _e('Data info for round'); ?> 2:</td>
                    <td id="finales-Distancia2"><?php _e('Distance'); ?>:</td>
                    <td id="finales-Obstaculos2"><?php _e('Obstacles'); ?>:</td>
                    <td id="finales-TRS2"><?php _e('Standard C. Time'); ?>:</td>
                    <td id="finales-TRM2"><?php _e('Maximum C. Time'); ?>:</td>
                    <td id="finales-Velocidad2"><?php _e('Speed'); ?>:</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="pb_table" data-options="region:'center'">
            <div id="team_table">
                <?php require(__DIR__."/../console/templates/final_teams.inc.php"); ?>
            </div>
        </div>
        <div id="pb_finales-footer" data-options="region:'south',split:false" style="height:10%;" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
    </div>
</div> <!-- finales-window -->

<script type="text/javascript">
    
// in a mobile device, increase north window height
if (isMobileDevice()) {
    $('#pb_finales-Cabecera').css('height','90%');
}

addTooltip($('#pb_header-link').linkbutton(),'<?php _e("Update scores"); ?>');

$('#enumerateFinales').combogrid({
	panelWidth: 250,
	panelHeight: 150,
	idField: 'Nombre',
	textField: 'Nombre',
	url: '/agility/server/database/jornadaFunctions.php',
	method: 'get',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	editable: false,  // to disable tablet keyboard popup
	selectOnNavigation: true, // let use cursor keys to interactive select
	columns: [[
			{field:'Nombre',title:'<?php _e('Available scores'); ?>',width:50,align:'right'},
			{field:'Prueba',hidden:true},
			{field:'Jornada',hidden:true},
			{field:'Manga1',hidden:true},
			{field:'Manga2',hidden:true},
			{field:'NombreManga1',hidden:true},
			{field:'NombreManga2',hidden:true},
			{field:'Recorrido',hidden:true},
			{field:'Mode',hidden:true}
	]],
	onBeforeLoad: function(param) { 
		param.Operation='enumerateRondasByJornada';
		param.Prueba= workingData.prueba;
		param.ID= workingData.jornada;
		return true;
	},
	onChange:function(value){
		updateFinales();
        $('#pb_finales-layout').layout('collapse','north');
	}
});

$('#pb_finales-layout').layout({fit:true});
$('#pb_finales-window').window({
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
        // update footer info
        pb_setFooterInfo();
	}
});

// fire autorefresh if configured
var rtime=parseInt(ac_config.web_refreshtime);
if (rtime!=0) {
    function update() {
        if ( $('#finales_equipos-datagrid').datagrid('options').expandCount <= 0 ) updateFinales();
        workingData.timeout=setTimeout(update,1000*rtime);
    }
    if (workingData.timeout!=null) clearTimeout(workingData.timeout);
    update();
}

</script>