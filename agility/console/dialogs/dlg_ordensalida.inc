<!-- 
dlg_ordensalida.inc

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
	   		data-options="iconCls:'icon-dice'" onclick="evalOrdenSalida('random')">Aleatorio</a>
	   	<a id="ordensalida-reverseBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-updown'" onclick="evalOrdenSalida('reverse')">Invertir</a>
	   	<a id="ordensalida-cloneBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-downdown'" onclick="evalOrdenSalida('sameorder')">Clonar</a>
	   	<a id="ordensalida-eqBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-huella'" onclick="$('#ordeneq3-dialog').dialog('open');">Equipos</a>
	   	<a id="ordensalida-checkBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-help'" onclick="reloadAndCheck()">Revisar</a>
   		</span>
   		<span style="float:right;padding:5px">
   		<!-- estos elementos deben estar alineados a la derecha -->
   		<a id="ordensalida-reloadBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-reload'" onclick="reloadOrdenSalida();">Actualizar</a>
   		<a id="ordensalida-printBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-print'" onclick="print_commonDesarrollo(1);">Imprimir</a>
	</span>
</div>

<script type="text/javascript">

$('#ordeneq3-dialog').dialog({
    title:          "Ajuste manual del orden de los equipos de la manga",
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
    loadMsg: '<?php _e('Actualizando lista de equipos');?> ...',
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
        { field:'Nombre',		width:20, title: '<?php _e('Nombre');?>' },
        { field:'Categorias',	width:10, title: '<?php _e('Cat.');?>' },
        { field:'Observaciones',width:65, title: '<?php _e('Observaciones');?>'},
        { field:'Miembros',		hidden:true },
        { field:'DefaultTeam',	width:5, align: 'center', title: 'Def', formatter:formatOk }
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
    loadMsg: "Actualizando orden de salida...",
    toolbar: '#ordensalida-toolbar',
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    // toolbar: '#ordensalida-toolbar',
    columns:[[
		{ field:'Perro',		hidden:true },
		{ field:'Equipo',		hidden:true },
		{ field:'NombreEquipo',	width:35, sortable:false,	align:'left',  title: 'Equipo'},
		{ field:'Dorsal',		width:15, sortable:false,	align:'center',  title: 'Dorsal'},
		{ field:'Nombre',		width:25, sortable:false,	align:'left',  title: 'Nombre'},
		{ field:'Licencia',		width:15, sortable:false,   align:'right', title: 'Licencia' },
		{ field:'Categoria',	width:10, sortable:false,   align:'center', title: 'Cat.' },
		{ field:'NombreGuia',	width:50, sortable:false,   align:'right', title: 'Guia' },
		{ field:'NombreClub',	width:30, sortable:false,   align:'right', title: 'Club' },
		{ field:'Celo',		    width:10, sortable:false, formatter: formatCelo,	align:'center', title: 'Celo' }
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
        if (isJornadaEq4()) {
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
addTooltip($('#ordensalida-checkBtn').linkbutton(),"Verificar 'alertas de proximidad' <br/>entre perros del mismo gu&iacute;a");
addTooltip($('#ordensalida-randomBtn').linkbutton(),"Generar un nuevo orden de salida aleatorio");
addTooltip($('#ordensalida-reverseBtn').linkbutton(),"Orden de salida inverso a resultados de primera manga");
addTooltip($('#ordensalida-cloneBtn').linkbutton(),"Clonar el orden de salida de la primera manga");
addTooltip($('#ordensalida-eqBtn').linkbutton(),"Ajuste manual del orden de salida de los equipos");
addTooltip($('#ordensalida-printBtn').linkbutton(),"Imprimir el orden de salida");
addTooltip($('#ordensalida-reloadBtn').linkbutton(),"Recargar orden de salida. Actualizar ventana");

</script>