<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Public::programa");
if ( ! $am->allowed(ENABLE_PUBLIC)) { include_once("unregistered.php"); return 0;}
?>

<!--
pb_inscripciones.inc

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
<!-- Presentacion de las inscripciones de la jornada -->
<div id="pb_programa-panel">
	<div id="pb_programa-layout" style="width:100%">
		<div id="pb_programa-Cabecera" data-options="region:'north',split:false" style="height:10%;" class="pb_floatingheader">
            <a id="pb_back-link" class="easyui-linkbutton" onClick="pbmenu_expandMenu(true);" href="#" style="float:left">
                <img id="pb_back-logo" src="/agility/images/backtomenu.png" width="50" />
            </a> &nbsp;
            <a id="pb_header-link" class="easyui-linkbutton" onClick="pb_updatePrograma();" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/agilitycontest.png" width="50" />
            </a>
		    <span style="float:left;padding:10px" id="pb_header-infocabecera"><?php _e('Header'); ?></span>
			<span style="float:right" id="pb_header-texto"><?php _e('Journey activities info'); ?></span>
		</div>
		<div id="team_table" data-options="region:'center'">
            <table id="pb_programa-datagrid"></table>
		</div>
        <div id="pb_programa-footer" data-options="region:'south',split:false" style="height:10%;" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
	</div>
</div> <!-- pb_inscripciones-window -->

<script type="text/javascript">

addTooltip($('#pb_header-link').linkbutton(),'<?php _e("Update schedule info on this journey"); ?>');
addTooltip($('#pb_back-link').linkbutton(),'<?php _e("Back to contest menu"); ?>');

$('#pb_programa-layout').layout({fit:true});
$('#pb_programa-panel').panel({
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

$('#pb_programa-datagrid').datagrid({
    // propiedades del panel asociado
    fit: true,
    border: false,
    closable: false,
    collapsible: false,
    collapsed: false,
    // propiedades del datagrid
    method: 'get',
    url: '/agility/server/database/tandasFunctions.php',
    queryParams: {
        Operation: 'getTandas',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 0 // include every item o this jornada
    },
    loadMsg: "<?php _e('Updating schedule time on this journey'); ?> ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: true,
    columns:[[
        { field:'ID',		width:'5%', align:'center',	title: '#',formatter: formatOrdenSalida },
        { field:'Prueba',	hidden:true },
        { field:'Jornada',	hidden:true },
        { field:'Manga',	hidden:true },
        { field:'Horario',	width:'20%', sortable:false, align:'center',title:'<?php _e('Timetable'); ?>'},
        { field:'Nombre',	width:'35%', sortable:false, align:'left',title:'<?php _e('Activity'); ?>'},
        { field:'Sesion',	hidden:true },
        { field:'NombreSesion',width:'15%', sortable:false, align:'left',title:'<?php _e('Session - Ring'); ?>', formatter: formatRing},
        { field:'Participantes',width:'5%', sortable:false, align:'center',title:'# <?php _e('Dogs');?>'},
        { field:'Comentario',width:'20%', sortable:false, align:'right',title:'<?php _e('Comments'); ?>'},
        { field:'Categoria',hidden:true },
        { field:'Grado',	hidden:true }
    ]],
    rowStyler:pbRowStyler
});
</script>