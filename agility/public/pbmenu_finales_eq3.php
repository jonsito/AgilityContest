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
pb_parciales.inc

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


<!-- Presentacion de resultados parciales -->

<div id="pb_finales-panel">
    <div id="pb_finales-layout" style="width:100%">
        <div id="pb_finales-Cabecera"  style="height:20%;" class="pb_floatingheader" data-options="region:'north',split:false,collapsed:false">
            <a id="pb_header-link" class="easyui-linkbutton" onClick="pb_updateFinales2(workingData.datosRonda);" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/agilitycontest.png" width="50" />
            </a>
            <span style="float:left;padding:5px" id="pb_header-infocabecera"><?php _e('Header'); ?></span>
            <span style="float:right" id="pb_header-texto">
                <?php _e('Final scores'); ?><br/>
                <span id="pb_enumerateFinales" style="width:200px"></span>
            </span>
            <!-- Datos de TRS y TRM -->
            <table class="pb_trs">
                <tbody>
                <tr>
                    <th id="pb_finales-NombreRonda" colspan="2" style="display:none">(<?php _e('No round selected'); ?>)</th>
                    <th id="pb_finales-Juez1" colspan="2" style="text-align:center"><?php _e('Judge'); ?> 1:</th>
                    <th id="pb_finales-Juez2" colspan="2" style="text-align:center"><?php _e('Judge'); ?> 2:</th>
                </tr>
                <tr style="text-align:right">
                    <td id="pb_finales-Ronda1"><?php _e('Data info for round'); ?> 1:</td>
                    <td id="pb_finales-Distancia1"><?php _e('Distance'); ?>:</td>
                    <td id="pb_finales-Obstaculos1"><?php _e('Obstacles'); ?>:</td>
                    <td id="pb_finales-TRS1"><?php _e('Standard C. Time'); ?>:</td>
                    <td id="pb_finales-TRM1"><?php _e('Maximum C. Time'); ?>:</td>
                    <td id="pb_finales-Velocidad1"><?php _e('Speed'); ?>:</td>
                </tr>
                <tr style="text-align:right">
                    <td id="pb_finales-Ronda2"><?php _e('Data info for round'); ?> 2:</td>
                    <td id="pb_finales-Distancia2"><?php _e('Distance'); ?>:</td>
                    <td id="pb_finales-Obstaculos2"><?php _e('Obstacles'); ?>:</td>
                    <td id="pb_finales-TRS2"><?php _e('Standard C. Time'); ?>:</td>
                    <td id="pb_finales-TRM2"><?php _e('Maximum C. Time'); ?>:</td>
                    <td id="pb_finales-Velocidad2"><?php _e('Speed'); ?>:</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="pb_tabla" data-options="region:'center'">
                <table id="pb_resultados-datagrid"></table>
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

$('#pb_finales-layout').layout({fit:true});
$('#pb_finales-panel').panel({
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

$('#pb_resultados-datagrid').datagrid({
    // propiedades del panel asociado
    fit: true,
    border: false,
    closable: false,
    collapsible: true,
    collapsed: false,
    // no tenemos metodo get ni parametros: directamente cargamos desde el datagrid
    loadMsg:  "<?php _e('Updating final scores');?>...",
    // propiedades del datagrid
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    idField: 'ID',
    rowStyler:myRowStyler,
    view:detailview,
    columns:[[
        { field:'ID',			hidden:true }, // ID del equipo
        { field:'Prueba',		hidden:true }, // ID de la prueba
        { field:'Jornada',		hidden:true }, // ID de la jornada
        { field:'Logo',		    width:'15%', sortable:false,  title: '',formatter:formatTeamLogos },
        { field:'Nombre',		width:'20%', sortable:false,  title: '<?php _e('Team'); ?>' },
        { field:'Categorias',	width:'5%', sortable:false,   title: '<?php _e('Cat'); ?>' },
        { field:'T1',		    width:'7%', sortable:false,   title: '<?php _e('Time'); ?> 1'},
        { field:'P1',		    width:'7%', sortable:false,   title: '<?php _e('Penal'); ?> 1' },
        { field:'T2',		    width:'7%', sortable:false,   title: '<?php _e('Time'); ?> 2'},
        { field:'P2',		    width:'7%', sortable:false,   title: '<?php _e('Penal'); ?> 2' },
        { field:'Tiempo',		width:'10%', sortable:false,  title: '<?php _e('Time'); ?>',formatter:formatBold },
        { field:'Penalizacion',	width:'10%', sortable:false,  title: '<?php _e('Penalization'); ?>',formatter:formatBold }
    ]],
    // especificamos un formateador especial para desplegar la tabla de perros por equipos
    detailFormatter:function(idx,row){
        var dgname="pb_resultados-datagrid-" + replaceAll(' ','_',row.ID);
        // return '<div style="padding:2px"><table id="'+dgname+'"></table></div>';
        return '<table id="'+dgname+'"></table>';
    },
    onExpandRow: function(idx,row) {
        pb_showClasificacionesByTeam(idx,row);
    }
});

// fire autorefresh if configured
setTimeout(function(){ $('#pb_enumerateFinales').text(workingData.datosRonda.Nombre)},0);
var rtime=parseInt(ac_config.web_refreshtime);
if (rtime!=0) {

    function update() {
        pb_updateFinales2(workingData.datosRonda);
        // workingData.timeout=setTimeout(update,1000*rtime);
    }

    if (workingData.timeout!=null) clearTimeout(workingData.timeout);
    update();
}

</script>