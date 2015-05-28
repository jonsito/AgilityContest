<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Public::programa");
if ( ! $am->allowed(ENABLE_PUBLIC)) { include_once("unregistered.html"); return 0;}
// tool to perform automatic upgrades in database when needed
require_once(__DIR__."/../server/upgradeVersion.php");
?>

<!--
pb_inscripciones.inc

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
<!-- Presentacion de las inscripciones de la jornada -->
<div id="pb_programa-window">
	<div id="pb_programa-layout" style="width:100%">
		<div id="pb_programa-Cabecera" data-options="region:'north',split:false" style="height:80px" class="pb_floatingheader">
            <a id="pb_header-link" class="easyui-linkbutton" onClick="pb_updatePrograma();" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/rsce.png" width="50" />
            </a>
		    <span style="float:left;padding:10px" id="pb_header-infocabecera">Cabecera</span>
			<span style="float:right" id="pb_header-texto">Programa de la Jornada</span>
		</div>
		<div id="pb_tabla" data-options="region:'center'">
            <table id="pb_programa-datagrid"></table>
		</div>
        <div id="pb_programa-footer" data-options="region:'south',split:false" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
	</div>
</div> <!-- pb_inscripciones-window -->

<script type="text/javascript">

addTooltip($('#pb_header-link').linkbutton(),"Actualizar informaci&aocute;n del programa");

$('#pb_programa-layout').layout({fit:true});
$('#pb_programa-window').window({
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
    loadMsg: "Actualizando programa de la Jornada .....",
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
        { field:'Horario',	width:'10%', sortable:false, align:'center',title:'Horario'},
        { field:'Nombre',	width:'35%', sortable:false, align:'left',title:'Actividad'},
        { field:'Sesion',	hidden:true },
        { field:'NombreSesion',width:'15%', sortable:false, align:'left',title:'Sesion - Ring', formatter: formatRing},
        { field:'Participantes',width:'5%', sortable:false, align:'center',title:'# Equip.'},
        { field:'Comentario',width:'30%', sortable:false, align:'right',title:'Observaciones'},
        { field:'Categoria',hidden:true },
        { field:'Grado',	hidden:true }
    ]],
    rowStyler:myRowStyler
});
</script>