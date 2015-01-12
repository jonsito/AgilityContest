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

<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
<div id="ordensalida-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
	   	<a id="ordensalida-randomBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-dice'" onclick="evalOrdenSalida('random')">Aleatorio</a>
	   	<a id="ordensalida-resultBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-updown'" onclick="evalOrdenSalida('reverse')">Manga anterior</a>
	   	<a id="ordensalida-saveBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-save'" onclick="saveOrdenSalida()">Guardar</a>
	   	<a id="ordensalida-reloadBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-reload'" onclick="reloadOrdenSalida()">Actualizar</a>
   		</span>
   		<span style="float:right;padding:5px">
   		<!-- estos elementos deben estar alineados a la derecha -->
   		<a id="ordensalida-printBtn" href="#" 
   			data-options="iconCls:'icon-print'" class="easyui-linkbutton">Imprimir</a>
	</span>
</div>

<script type="text/javascript">
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
          	{ field:'Dorsal',		width:10, sortable:false,	align:'left',  title: 'Dorsal'},
          	{ field:'Nombre',		width:20, sortable:false,	align:'left',  title: 'Nombre'},
      		{ field:'Licencia',		width:15, sortable:false,   align:'right', title: 'Licencia' },
      		{ field:'Categoria',	width:10, sortable:false,   align:'center', title: 'Cat.' },
      		{ field:'NombreGuia',	width:50, sortable:false,   align:'right', title: 'Guia' },
      		{ field:'NombreClub',	width:30, sortable:false,   align:'right', title: 'Club' },
      		{ field:'Celo',		    width:10, sortable:false,   align:'center', title: 'Celo' },
      		{ field:'Observaciones',width:30, sortable:false,   align:'right', title: 'Observaciones' }
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onBeforeLoad: function(param) { return (workingData.manga<=0)?false:true; }, // do not load if no manga selected
    onLoadSuccess: function() { // get focus on datagrid (to bind keystrokes) and enable drag and drop
    	$(this).datagrid('enableDnd');
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
    },
    onDragEnter: function(dst,src) {
        var from=src.Categoria+src.Celo;
        var to=dst.Categoria+dst.Celo;
        return (from==to)?true:false;
    },
    onDrop: function(dst,src,updown) {
        dragAndDropOrdenSalida(src.Perro,dst.Perro,(updown==='top')?0:1,reloadOrdenSalida);
    }
});

// Activa tecla ESC (escape) para cerrar ventana
$('#ordensalida-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
	if (e.keyCode==27) $('#ordensalida-dialog').window('close'); 
	return false;
});

// botones
addTooltip($('#ordensalida-reloadBtn').linkbutton(),"Actualizar el orden de salida desde base de datos");
addTooltip($('#ordensalida-randomBtn').linkbutton(),"Generar un nuevo orden de salida aleatorio");
addTooltip($('#ordensalida-resultBtn').linkbutton(),"Orden de salida inverso a resultados de primera manga");
addTooltip($('#ordensalida-saveBtn').linkbutton(),"Guardar orden de salida en base de datos");
addTooltip($('#ordensalida-printBtn').linkbutton(),"Imprimir el orden de salida");
$('#ordensalida-printBtn').on("click", function () {
	$.fileDownload(
		'/agility/server/pdf/print_ordenDeSalida.php',
		{
			httpMethod: 'GET',
			data: { 
				Prueba: workingData.prueba,
				Jornada: workingData.jornada,
				Manga: workingData.manga
			},
	        preparingMessageHtml: "Imprimiendo orden de salida; por favor espere...",
	        failMessageHtml: "There was a problem generating your report, please try again."
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
});
</script>