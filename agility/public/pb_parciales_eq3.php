<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Public::parciales_eq3");
if ( ! $am->allowed(ENABLE_PUBLIC)) { include_once("unregistered.html"); return 0;}
?>

<!--
pb_parciales.inc

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

<!-- Presentacion de resultados parciales en pruebas por equipos (3 mejores de cuatro)  -->

<div id="pb_parciales-window">
    <div id="pb_parciales-layout" style="width:100%">
        <div id="pb_parciales-Cabecera" data-options="region:'north',split:false" style="height:140px" class="pb_floatingheader">
            <a id="pb_header-link" class="easyui-linkbutton" onClick="pb_updateParciales();" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/rsce.png" width="50" />
            </a>
            <span style="float:left;padding:10px" id="pb_header-infocabecera">Cabecera</span>
            <span style="float:right;padding:10px" id="pb_header-texto">
                Resultados provisionales<br/>
                <label for="pb_enumerateParciales" style="font-size:0.7em;">Manga:</label>
                <select id="pb_enumerateParciales" style="width:200px"></select>
            </span><br/>
            <!-- Datos de TRS y TRM -->
            <table class="pb_trs">
                <thead>
                <tr>
                    <th id="pb_parciales-NombreManga" colspan="2">(no hay manga seleccionada)</th>
                    <th id="pb_parciales-Juez1" colspan="2" style="text-align:center">Juez 1:</th>
                    <th id="pb_parciales-Juez2" colspan="2" style="text-align:center">Juez 2:</th>
                </tr>
                </thead>
                <tbody>
                <tr style="text-align:right">
                    <td>Datos de la manga:</td>
                    <td id="pb_parciales-Distancia">Distancia:</td>
                    <td id="pb_parciales-Obstaculos">Obst&aacute;culos:</td>
                    <td id="pb_parciales-TRS">T.R.Standard:</td>
                    <td id="pb_parciales-TRM">T.R.M&aacute;ximo:</td>
                    <td id="pb_parciales-Velocidad">Velocidad:</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="pb_parciales-data" data-options="region:'center'" >
            <table id="pb_parciales-datagrid"></table>
        </div>
        <div id="pb_parciales-footer" data-options="region:'south',split:false" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
    </div>
</div> <!-- pb_parciales-window -->

<script type="text/javascript">

addTooltip($('#pb_header-link').linkbutton(),"Actualizar datos de resultados parciales");
$('#pb_parciales-layout').layout({fit:true});

$('#pb_enumerateParciales').combogrid({
	panelWidth: 300,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/resultadosFunctions.php',
	method: 'get',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	editable: false,  // to disable tablet keyboard popup
	selectOnNavigation: true, // let use cursor keys to interactive select
	columns: [[
	   	    {field:'ID',hidden:true},
			{field:'Nombre',title:'Resultados disponibles',width:50,align:'right'},
			{field:'Prueba',hidden:true},
			{field:'Jornada',hidden:true},
			{field:'Manga',hidden:true},
			{field:'TipoManga',hidden:true},
			{field:'Mode',hidden:true}
	]],
	onBeforeLoad: function(param) {
		param.Operation='enumerateResultados';
		param.Prueba= workingData.prueba;
		param.Jornada= workingData.jornada;
		param.Manga= 1; // fake data to get Resultados constructor working
		return true;
	},
    onOpen: function() {
        pb_updateParciales(); // notice no results will still be reported. just to update header info
    },
	onChange:function(value){
		pb_updateParciales();
	}
});

$('#pb_parciales-window').window({
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
        Mode: (workingData.datosManga.Recorrido!=2)?0:4, // def to 'Large' or 'LMS' depending of datosmanga
        Operation: 'getResultados'
    },
    loadMsg: "Actualizando resultados de la manga ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: true,
    view: gview,
    groupField: 'NombreEquipo',
    groupFormatter: formatTeamResults,
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'Perro',		hidden:true },
        { field:'Raza',		    hidden:true },
        { field:'Equipo',		hidden:true },
        { field:'NombreEquipo',	hidden:true },
        { field:'Dorsal',		width:'5%', align:'center', title: 'Dorsal'},
        { field:'LogoClub',		hidden:true},
        { field:'Licencia',		width:'5%%', align:'center',  title: 'Licencia'},
        { field:'Nombre',		width:'10%', align:'center',  title: 'Nombre',formatter:formatBoldBig},
        { field:'NombreGuia',	width:'15%', align:'right', title: 'Guia' },
        { field:'NombreClub',	width:'12%', align:'right', title: 'Club' },
        { field:'Categoria',	width:'4%', align:'center', title: 'Cat.' },
        { field:'Grado',	    hidden:true },
        { field:'Faltas',		width:'4%', align:'center', title: 'Faltas'},
        { field:'Rehuses',		width:'4%', align:'center', title: 'Rehuses'},
        { field:'Tocados',		width:'4%', align:'center', title: 'Tocados'},
        { field:'PRecorrido',	hidden:true },
        { field:'Tiempo',		width:'6%', align:'right', title: 'Tiempo', formatter:formatTiempo},
        { field:'PTiempo',		hidden:true },
        { field:'Velocidad',	width:'4%', align:'right', title: 'Vel.', formatter:formatVelocidad},
        { field:'Penalizacion',	width:'6%%', align:'right', title: 'Penal.', formatter:formatPenalizacion},
        { field:'Calificacion',	width:'7%', align:'center',title: 'Calificacion'},
        { field:'Puesto',		width:'4%', align:'center',  title: 'Puesto', formatter:formatPuestoBig},
        { field:'CShort',       hidden:true}
    ]],
    rowStyler:myRowStyler,
    onBeforeLoad: function(param) { // do not load if no manga selected
        var row=$('#pb_enumerateParciales').combogrid('grid').datagrid('getSelected');
        if (!row) return false;
        return true;
    }
});

</script>