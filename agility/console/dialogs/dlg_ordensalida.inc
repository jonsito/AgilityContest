<!-- 
dlg_ordensalida.inc

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

<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<!--  dialogo de orden de salida e introduccion de datos de una competicion -->
<div id="ordensalida-dialog" style="width:800px;height:450px;padding:5px">
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="ordensalida-datagrid" style="padding:5px"></table>
</div>

<!--  dialogo de ajuste manual del orden de equipos en competiciones modalidad eq3 -->
<div id="ordeneq3-dialog" style="width:640px;height:300px;padding:5px">
    <!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
    <table id="ordeneq3-datagrid" style="padding:5px"></table>
</div>

<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
<div id="ordensalida-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
	   	<a id="ordensalida-randomBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-dice'" onclick="evalOrdenSalida('random')"><?php _e('Random'); ?></a>
	   	<a id="ordensalida-reverseBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-updown'" onclick="evalOrdenSalida('reverse')"><?php _e('Reverse'); ?></a>
	   	<a id="ordensalida-cloneBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-downdown'" onclick="evalOrdenSalida('sameorder')"><?php _e('Clone'); ?></a>
	   	<a id="ordensalida-eqBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-huella'" onclick="$('#ordeneq3-dialog').dialog('open');"><?php _e('Teams'); ?></a>
	   	<a id="ordensalida-checkBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-help'" onclick="reloadAndCheck()"><?php _e('Check'); ?></a>
   		</span>
   		<span style="float:right;padding:5px">
   		<!-- estos elementos deben estar alineados a la derecha -->
   		<a id="ordensalida-reloadBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-reload'" onclick="reloadOrdenSalida();"><?php _e('Refresh'); ?></a>
   		<a id="ordensalida-printBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-print'" onclick="print_commonDesarrollo(1);"><?php _e('Print'); ?></a>
	</span>
</div>

<script type="text/javascript">

$('#ordeneq3-dialog').dialog({
    title:         '<?php _e( "Manual setting of teams order for this round"); ?>',
    collapsible:	false,
    minimizable:	false,
    maximizable:	false,
    resizable:		true,
    iconCls:		'icon-huella',
    closed:			true,
    modal:			true,
    onBeforeOpen:   reloadSimpleOrdenEquipos
});

// datos de la tabla de equipos
$('#ordeneq3-datagrid').datagrid({
    // propiedades del panel asociado
    fit: true,
    url: '/agility/server/database/ordenSalidaFunctions.php',
    queryParams: { Operation:'getTeams', Prueba:workingData.prueba, Jornada:workingData.jornada, Manga:workingData.manga, where:''	},
    loadMsg: '<?php _e('Updating teams starting order');?> ...',
    method: 'get',
    mode: 'remote',
    multiSort: false,
    remoteSort: false,
    idField: 'ID',
    columns: [[
        { field:'ID',			hidden:true },
        { field:'Prueba',		hidden:true },
        { field:'Jornada',		hidden:true },
        // { field:'Orden',		hidden:true },
        { field:'Nombre',		width:20, title: '<?php _e('Name');?>',formatter:formatBold },
        { field:'Categorias',	width:10, title: '<?php _e('Cat');?>.' },
        { field:'Observaciones',width:65, title: '<?php _e('Comments');?>'},
        { field:'Miembros',		hidden:true },
        { field:'DefaultTeam',	width:5, align: 'center', title: '<?php _e('Def'); ?>', formatter:formatOk }
    ]],
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    view: scrollview,
    pageSize: 25,
    rowStyler: myRowStyler, // function that personalize colors on alternate rows
    onBeforeLoad: function (params) {return (workingData.manga>0) }, // do not load if no manga selected
    onLoadSuccess: function(data) { $(this).datagrid('enableDnd'); },
    onDragEnter: function(dst,src) {
        return (dst.DefaultTeam!=1 && src.DefaultTeam!=1); // allow dnd if not from/to default team
    },
    onDrop: function(dst,src,updown) {
        dragAndDropOrdenEquipos(src.ID,dst.ID,(updown==='top')?0:1,reloadSimpleOrdenEquipos);
    }
});

$('#ordensalida-dialog').dialog({
	collapsible:	false,
	minimizable:	false,
	maximizable:	false,
	resizable:		true,
	iconCls:		'icon-order',
	closed:			true,
	modal:			true
});

$('#ordensalida-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: '/agility/server/database/ordenSalidaFunctions.php',
    queryParams: {
        Operation: 'getData',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Manga: workingData.manga
    },
    loadMsg: '<?php _e("Updating starting order"); ?> ...',
    toolbar: '#ordensalida-toolbar',
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    // toolbar: '#ordensalida-toolbar',
    columns:[[
		{ field:'Perro',		hidden:true },
		{ field:'Equipo',		hidden:true },
		{ field:'NombreEquipo',	width:35, sortable:false,	align:'left',  title: '<?php _e('Team'); ?>'},
		{ field:'Dorsal',		width:15, sortable:false,	align:'center',  title: '<?php _e('Dorsal'); ?>'},
		{ field:'Nombre',		width:25, sortable:false,	align:'left',  title: '<?php _e('Name'); ?>',formatter:formatBold},
		{ field:'Licencia',		width:15, sortable:false,   align:'right', title: '<?php _e('License'); ?>' },
		{ field:'Categoria',	width:10, sortable:false,   align:'center', title: '<?php _e('Cat'); ?>.' ,formatter:formatCategoria},
		{ field:'NombreGuia',	width:50, sortable:false,   align:'right', title: '<?php _e('Handler'); ?>' },
		{ field:'NombreClub',	width:30, sortable:false,   align:'right', title: '<?php _e('Club'); ?>' },
		{ field:'Celo',		    width:10, sortable:false, formatter: formatCelo,	align:'center', title:'<?php _e('Heat'); ?>'}
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onBeforeLoad: function(param) { return (workingData.manga > 0); }, // do not load if no manga selected
    onLoadSuccess: function(data) {
		// show/hide 'NombreEquipo' based of manga type
		if (isTeam(workingData.datosManga.Tipo) ) $('#ordensalida-datagrid').datagrid('showColumn','NombreEquipo');
		else  $('#ordensalida-datagrid').datagrid('hideColumn','NombreEquipo');
        // get focus on datagrid (to bind keystrokes) and enable drag and drop
    	$(this).datagrid('enableDnd');
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
    },
    onDragEnter: function(dst,src) {
        var from=":"+src.Equipo+":"+src.Categoria+":"+src.Celo+":";
        var to=":"+dst.Equipo+":"+dst.Categoria+":"+dst.Celo+":";
        if (isJornadaEqConjunta()) {
            // en jornadas por equipos conjunta, no hay que tener en cuenta ni categoria ni celo
            from=":"+src.Equipo+":";
            to=":"+dst.Equipo+":";
        }
		return (from === to);
    },
    onDrop: function(dst,src,updown) {
        dragAndDropOrdenSalida(src.Perro,dst.Perro,(updown==='top')?0:1,reloadOrdenSalida);
    }
});

//activa teclas up/down para navegar por el panel , esc para cerrar y ctrl+shift+enter para ver fila
addSimpleKeyHandler('#ordensalida-datagrid','#ordensalida-dialog');

// botones
addTooltip($('#ordensalida-checkBtn').linkbutton(),'<?php _e("Look for <em>proximity alerts</em>"); ?>'+" <br/>"+'<?php _e("with dogs belonging same handler"); ?>');
addTooltip($('#ordensalida-randomBtn').linkbutton(),'<?php _e("Generate a new, random, starting order"); ?>');
addTooltip($('#ordensalida-reverseBtn').linkbutton(),'<?php _e("Reverse starting order according with results in the coupled round"); ?>');
addTooltip($('#ordensalida-cloneBtn').linkbutton(),'<?php _e("Clone starting order from coupled round"); ?>');
addTooltip($('#ordensalida-eqBtn').linkbutton(),'<?php _e("Manual setting of teams starting order"); ?>');
addTooltip($('#ordensalida-printBtn').linkbutton(),'<?php _e("Print starting order"); ?>');
addTooltip($('#ordensalida-reloadBtn').linkbutton(),'<?php _e("Reload starting order. Update window"); ?>');

</script>