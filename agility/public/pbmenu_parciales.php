<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Public::parciales");
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

<div id="pb_parciales-panel">
    <div id="pb_parciales-layout" style="width:100%">
        <div id="pb_parciales-Cabecera"  style="height:15%;" class="pb_floatingheader" data-options="region:'north',split:false,collapsed:false">
            
            <a id="pb_header-link" class="easyui-linkbutton" onClick="updateParciales(workingData.datosManga);" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/agilitycontest.png" width="40" />
            </a>
            <span id="header-combinadaFlag" style="display:none">false</span> <!--indicador de combinada-->
            <span style="float:left;padding:5px" id="pb_header-infocabecera"><?php _e('Header'); ?></span>
            <span style="float:right;padding:5px" id="pb_header-texto">
                <?php _e('Partial scores'); ?><br/>
                <span id="pb_enumerateParciales" style="width:200px"></span>
            </span><br/>
            <!-- Datos de TRS y TRM -->
            <table class="pb_trs">
                <thead>
                <tr>
                    <th id="pb_parciales-NombreManga" colspan="2" style="display:none">(<?php _e('No round selected'); ?>)</th>
                    <th id="pb_parciales-Juez1" colspan="2" style="text-align:center"><?php _e('Judge'); ?> 1:</th>
                    <th id="pb_parciales-Juez2" colspan="2" style="text-align:center"><?php _e('Judge'); ?> 2:</th>
                </tr>
                </thead>
                <tbody>
                <tr style="text-align:right">
                    <td><?php _e('Round data info'); ?>:</td>
                    <td id="pb_parciales-Distancia"><?php _e('Distance'); ?>:</td>
                    <td id="pb_parciales-Obstaculos"><?php _e('Obstacles'); ?>:</td>
                    <td id="pb_parciales-TRS"><?php _e('Standard C. Time'); ?>:</td>
                    <td id="pb_parciales-TRM"><?php _e('Maximum C. Time'); ?>:</td>
                    <td id="pb_parciales-Velocidad"><?php _e('Speed'); ?>:</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="pb_parciales-footer" data-options="region:'south',split:false" style="height:10%;" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
        <div id="pb_parciales-data" data-options="region:'center'" >
            <table id="pb_parciales-datagrid"></table>
        </div>
    </div>
</div> <!-- pb_parciales-window -->

<script type="text/javascript">

// in a mobile device, increase north window height
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
	onOpen: function() {
        // update header
        pb_getHeaderInfo();
        // update footer
        pb_setFooterInfo();
	}
});

$('#pb_parciales-datagrid').datagrid({
    // propiedades del panel asociado
    fit: true,
    border: false,
    closable: false,
    collapsible: false,
    collapsed: false,
    // propiedades del datagrid
    method: 'get',
    url: '/agility/server/database/resultadosFunctions.php',
    queryParams: {
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Manga: workingData.manga,
        Mode: workingData.datosManga.Mode,
        Operation: 'getResultados'
    },
    loadMsg:  "<?php _e('Updating partial scores');?> ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: true,
    // view: gview,
    groupField: 'NombreEquipo',
    groupFormatter: formatPbTeamResults,
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'Perro',		hidden:true },
        { field:'Raza',		    hidden:true },
        { field:'Equipo',		hidden:true },
        { field:'NombreEquipo',	hidden:true },
        { field:'Dorsal',		width:'5%', align:'center', title: '<?php _e('Dorsal'); ?>'},
        { field:'LogoClub',		width:'5%', align:'center', title: '', formatter:formatLogo },
        { field:'Licencia',		width:'5%%', align:'center',  title: '<?php _e('License'); ?>'},
        { field:'Nombre',		width:'10%', align:'center',  title: '<?php _e('Name'); ?>',formatter:formatBoldBig},
        { field:'NombreGuia',	width:'15%', align:'right', title: '<?php _e('Handler'); ?>' },
        { field:'NombreClub',	width:'12%', align:'right', title: '<?php _e('Club'); ?>' },
        { field:'Categoria',	width:'4%', align:'center', title: '<?php _e('Cat'); ?>.',formatter:formatCategoria },
        { field:'Grado',	    width:'4%', align:'center', title: '<?php _e('Grade'); ?>', formatter:formatGrado },
        { field:'Faltas',		width:'4%', align:'center', title: '<?php _e('Fault'); ?>'},
        { field:'Rehuses',		width:'4%', align:'center', title: '<?php _e('Refusal'); ?>'},
        { field:'Tocados',		width:'4%', align:'center', title: '<?php _e('Touch'); ?>'},
        { field:'PRecorrido',	hidden:true },
        { field:'Tiempo',		width:'6%', align:'right', title: '<?php _e('Time'); ?>', formatter:formatTiempo},
        { field:'PTiempo',		hidden:true },
        { field:'Velocidad',	width:'4%', align:'right', title: '<?php _e('Vel'); ?>.', formatter:formatVelocidad},
        { field:'Penalizacion',	width:'6%%', align:'right', title: '<?php _e('Penal'); ?>.', formatter:formatPenalizacion},
        { field:'Calificacion',	width:'8%', align:'center',title: '<?php _e('Calification'); ?>'},
        { field:'Puesto',		width:'4%', align:'center',  title: '<?php _e('Position'); ?>', formatter:formatPuestoBig},
        { field:'CShort',       hidden:true}
    ]],
    rowStyler:myRowStyler,
    onBeforeLoad: function(param) { // do not load if no manga selected
        if (workingData.manga==0) return false; // do not try to load if not variable initialized
        return true;
    }
});

// fire autorefresh if configured
setTimeout(function(){ $('#pb_enumerateParciales').text(workingData.nombreManga)},0);
var rtime=parseInt(ac_config.web_refreshtime);
if (rtime!=0) {

    function update() {
        updateParciales(workingData.datosManga.Mode);
        workingData.timeout= setTimeout(update,1000*rtime);
    }

    if (workingData.timeout!=null) clearTimeout(workingData.timeout);
    update();
}
    
</script>