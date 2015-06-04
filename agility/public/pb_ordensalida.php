<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Public::ordensalida");
if ( ! $am->allowed(ENABLE_PUBLIC)) { include_once("unregistered.html"); return 0;}
// tool to perform automatic upgrades in database when needed
require_once(__DIR__."/../server/upgradeVersion.php");
?>
<!--
pb_ordensalida.inc

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

<!-- Presentacion del orden de salida de la jornada -->
<div id="pb_ordensalida-window">
	<div id="pb_ordensalida-layout" style="width:100%">
		<div id="pb_ordensalida-Cabecera" data-options="region:'north',split:false" style="height:80px" class="pb_floatingheader">
            <a id="pb_header-link" class="easyui-linkbutton" onClick="pb_updateOrdenSalida();" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/rsce.png" width="50" />
            </a>
		    <span style="float:left;padding:10px;" id="pb_header-infocabecera">Cabecera</span>
			<span style="float:right;" id="pb_header-texto">
                Orden de Salida<br />
                <label for="pb_enumerateMangas">&nbsp;</label>
                <select id="pb_enumerateMangas" style="width:200px"></select>
            </span>
		</div>
		<div id="pb_tabla" data-options="region:'center'">
			<table id="pb_ordensalida-datagrid"></table>
		</div>
        <div id="pb_ordensalida-footer" data-options="region:'south',split:false" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
	</div>
</div> <!-- pb_ordensalida-window -->

<script type="text/javascript">

addTooltip($('#pb_header-link').linkbutton(),"Actualizar orden de salida");
$('#pb_ordensalida-layout').layout({fit:true});

$('#pb_ordensalida-window').window({
    fit:true,
    noheader:true,
    border:false,
    closable:false,
    collapsible:false,
    collapsed:false,
    resizable:true,
    callback: null,
    // 1 minute poll is enouth for this, as no expected changes during a session
    onOpen: function() {
        // update header
        pb_getHeaderInfo();
        // update footer
        pb_setFooterInfo();
    }
});

$('#pb_enumerateMangas').combogrid({
    panelWidth: 350,
    panelHeight: 150,
    idField: 'ID',
    textField: 'Nombre',
    method: 'get',
    url: '/agility/server/database/tandasFunctions.php',
    queryParams: {
        Operation: 'getTandas',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 1 // show only non user defined tandas
    },
    required: true,
    multiple: false,
    fitColumns: true,
    singleSelect: true,
    editable: false,  // to disable tablet keyboard popup
    selectOnNavigation: true, // let use cursor keys to interactive select
    columns:[[
        { field:'ID',		hidden:true },
        { field:'Sesion',	hidden:true },
        { field:'Prueba',	hidden:true },
        { field:'Jornada',	hidden:true },
        { field:'Manga',	hidden:true },
        { field:'Categoria',hidden:true },
        { field:'Grado',	hidden:true },
        { field:'Sesion',	hidden:true },
        { field:'Tipo',	    hidden:true },
        { field:'Horario',	hidden:true },
        { field:'Nombre',	width:150, sortable:false, align:'left',title:'Lista de Tandas de la jornada'},
        { field:'Comentario', hidden:true}
    ]],
    rowStyler: myRowStyler,
    onSelect: function(index,row) {
        $('#pb_ordensalida-datagrid').datagrid('reload',{
            Operation: 'getDataByTanda',
            Prueba: workingData.prueba,
            Jornada: workingData.jornada,
            Sesion: 1, // defaults to "-- sin asignar --"
            ID:  row.ID // Tanda ID
        });
    }
});

$('#pb_ordensalida-datagrid').datagrid({
    method: 'get',
    url: '/agility/server/database/tandasFunctions.php',
    queryParams: {
        Operation: 'getDataByTanda',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 1, // defaults to "-- sin asignar --"
        ID: 0 // Tanda 0 defaults to every tandas
    },
    loadMsg: "Actualizando orden de salida ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: true,
    nowrap: false,
    width: '100%',
    height: 'auto',
    columns:[[
        { field:'Prueba',		width:0, hidden:true }, // extra field to be used on form load/save
        { field:'Jornada',		width:0, hidden:true }, // extra field to be used on form load/save
        { field:'Manga',		width:0, hidden:true },
        { field:'Tanda',		width:0, hidden:true }, // string with tanda's name
        { field:'ID',			width:0, hidden:true }, // tanda ID
        { field:'Perro',     	width:'5%', align:'center',	title: '#',formatter: formatOrdenSalida },
        { field:'Pendiente',	width:0, hidden:true },
        { field:'Tanda',		width:0, hidden:true },
        { field:'Equipo',		width:0, hidden:true },
        { field:'Logo',     	width:'5%', align:'center',	title: '',formatter: formatLogoPublic },
        { field:'NombreEquipo',	width:'12%', align:'center',title: 'Equipo',hidden:true},
        { field:'Dorsal',		width:'5%', align:'center',	title: 'Dorsal', styler:checkPending },
        { field:'Nombre',		width:'15%', align:'center',title: 'Nombre',formatter: formatBoldBig},
        { field:'Raza',         width:'12%', align:'center',title: 'Raza' },
        { field:'Licencia',		width:'5%', align:'center',	title: 'Licencia'},
        { field:'NombreGuia',	width:'23%', align:'right',	title: 'Guia' },
        { field:'NombreClub',	width:'19%', align:'right',	title: 'Club' },
        { field:'Categoria',	width:'4%', align:'center',	title: 'Categ.' },
        { field:'Grado',		width:'4%', align:'center',	title: 'Grado' },
        { field:'Celo',			width:'4%', align:'center',	title: 'Celo', formatter:formatCelo },
        { field:'Observaciones',width:0, hidden:true }
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:myRowStyler,
    onBeforeLoad:function(param) {
        var row=$('#pb_enumerateMangas').combogrid('grid').datagrid('getSelected');
        if (!row) return false;
        return true;
    },
    onLoadSuccess:function(){
        mySelf=$('#pb_ordensalida-datagrid');
        // show/hide team name
        if (isTeamByJornada(workingData.datosJornada) ) {
            mySelf.datagrid('showColumn','NombreEquipo');
            mySelf.datagrid('hideColumn','Observaciones');
        } else  {
            mySelf.datagrid('hideColumn','NombreEquipo');
            mySelf.datagrid('showColumn','Observaciones');
        }
        mySelf.datagrid('fitColumns'); // expand to max width
    }
});

</script>