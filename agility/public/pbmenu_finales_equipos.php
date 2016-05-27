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
            <table id="pb_finales-datagrid">
                <thead>
                <tr>
                    <th colspan="3"> <span class="main_theader"><?php _e('Team data'); ?></span></th>
                    <th colspan="2"> <span class="main_theader" id="pb_resultados_thead_m1"><?php _e('Round'); ?> 1</span></th>
                    <th colspan="2"> <span class="main_theader" id="pb_resultados_thead_m2"><?php _e('Round'); ?> 2</span></th>
                    <th colspan="2"> <span class="main_theader"><?php _e('Final scores'); ?></span></th>
                </tr>
                <tr> <!--
                        <th data-options="field:'ID',			hidden:true"></th>
                        <th data-options="field:'Prueba',		hidden:true"></th>
                        <th data-options="field:'Jornada',		hidden:true"></th>
                        -->
                    <th data-options="field:'Logo',		    width:'19%', sortable:false, formatter:formatTeamLogos">&nbsp</th>
                    <th data-options="field:'Nombre',		width:'20.5%', sortable:false, formatter:formatBold"><?php _e('Team'); ?></th>
                    <th data-options="field:'Categorias',	width:'4%', sortable:false"><?php _e('Cat'); ?></th>
                    <th data-options="field:'T1',		    align:'center', width:'9.5%', sortable:false"><?php _e('Time'); ?> 1</th>
                    <th data-options="field:'P1',		    align:'center',width:'10%', sortable:false"><?php _e('Penal'); ?> 1</th>
                    <th data-options="field:'T2',		    align:'center',width:'9.5%', sortable:false"><?php _e('Time'); ?> 2</th>
                    <th data-options="field:'P2',		    align:'center',width:'10%', sortable:false"><?php _e('Penal'); ?> 2</th>
                    <th data-options="field:'Tiempo',		align:'center',width:'9%', sortable:false,formatter:formatBold"><?php _e('Time'); ?></th>
                    <th data-options="field:'Penalizacion',	align:'center',width:'8.5%', sortable:false,formatter:formatBold"><?php _e('Penalization'); ?></th>
                </tr>
                </thead>
            </table>
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

$('#pb_finales-datagrid').datagrid({
    expandCount: 0,
    // propiedades del panel asociado
    fit: false,
    border: false,
    closable: false,
    collapsible: false,
    collapsed: false,
    // no tenemos metodo get ni parametros: directamente cargamos desde el datagrid
    loadMsg:  "<?php _e('Updating final scores');?>...",
    // propiedades del datagrid
    width:'100%',
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: false,
    idField: 'ID',
    view:detailview,
    pageSize: 500, // enought bit to make it senseless
    // columns declared at html section to show additional headers
    rowStyler:myRowStyler,
    // especificamos un formateador especial para desplegar la tabla de perros por equipos
    detailFormatter:function(idx,row){
        var dgname="pb_finales-datagrid-"+parseInt(row.ID);
        return '<div style="padding:2px"><table id="'+dgname+'"></table></div>';
    },
    onExpandRow: function(idx,row) {
        $(this).datagrid('options').expandCount++;
        showClasificacionesByTeam("#pb_finales-datagrid",idx,row);
    },
    onCollapseRow: function(idx,row) {
        $(this).datagrid('options').expandCount--;
        var dg="#pb_finales-datagrid-" + parseInt(row.ID);
        $(dg).remove();
    }
});

// fire autorefresh if configured
setTimeout(function(){ $('#pb_enumerateFinales').text(workingData.datosRonda.Nombre)},0);
var rtime=parseInt(ac_config.web_refreshtime);
if (rtime!=0) {

    function update() {
        if ( $('#pb_finales-datagrid').datagrid('options').expandCount <= 0 )pb_updateFinales2(workingData.datosRonda);
        workingData.timeout=setTimeout(update,1000*rtime);
    }

    if (workingData.timeout!=null) clearTimeout(workingData.timeout);
    update();
}

</script>