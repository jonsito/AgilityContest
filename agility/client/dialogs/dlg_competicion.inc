<!-- 
dlg_competicion.inc

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
<div id="competicion-window"style="width:800px;height:450px;padding:0px 10px 40px 10px">
	<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
	<div id="competicion-toolbar" style="padding:5px">
 		<span style="float:left">
		   	<a id="competicion-reloadBtn" href="#" class="easyui-linkbutton" 
		   		data-options="iconCls:'icon-reload'" onclick="reloadCompeticion()">Actualizar</a>
    		</span>
    		<span style="float:right">
    		<!-- estos elementos deben estar alineados a la derecha -->
    		<a id="competicion-printBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-print'">Formularios</a>
		</span>
	</div>
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="competicion-datagrid" style="padding:5px"></table>
</div>

<script type="text/javascript">
$('#competicion-window').window({
	collapsible:false,
	minimizable:false,
	maximizable:false,
	resizable:false,
	iconCls:'icon-table',
	closed:true,
	modal:true
});

$('#competicion-datagrid').datagrid({
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
    loadMsg: "Actualizando datos de competicion ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    // toolbar: '#competicion-toolbar',
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'Perro',		hidden:true },
      	{ field:'Licencia',		hidden:true },
      	{ field:'Pendiente',	hidden:true },
        { field:'Dorsal',		width:10, align:'right',  title: 'Dorsal', styler:checkPending },
        { field:'Nombre',		width:20, align:'left',  title: 'Nombre'},
        { field:'NombreGuia',	width:50, align:'right', title: 'Guia' },
        { field:'NombreClub',	width:30, align:'right', title: 'Club' },
      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' },
      	{ field:'Faltas',		width:15, align:'right', title: 'Faltas',	editor:'numberbox' },
      	{ field:'Tocados',		width:15, align:'right', title: 'Tocad.',	editor:'numberbox' },
      	{ field:'Rehuses',		width:15, align:'right', title: 'Rehus.',	editor:'numberbox', },
      	{ field:'Tiempo',		width:20, align:'right', title: 'Tiempo',	editor:{type:'numberbox',options:{precision:2}}	}, 
      	{ field:'Eliminado',	width:10, align:'center',title: 'Elim.',	editor:{type:'checkbox',options:{on:1,off:0}}	},
      	{ field:'NoPresentado',	width:10, align:'center',title: 'N.P.',		editor:{type:'checkbox',options:{on:1,off:0}}	}, 
      	{ field:'Observaciones',width:20, align:'left',	 title: 'Observaciones',	editor:'text'  },
      	{ field:'action',		width:20, align:'center',title:'',
      		formatter:function(value,row,index){
      			if (row.editing){
      				var s = '<a href="#" class="easyui-linkbutton" data-options="iconCls:\'icon-save\'" onclick="saverow(this);">Save</a> ';
      				var c = '<a href="#" class="easyui-linkbutton" data-options="iconCls:\'icon-undo\'" onclick="cancelrow(this);">Cancel</a>';
      				return s+c;
      			} else {
      				var e = '<a href="#" class="easyui-linkbutton" data-options="iconCls:\'icon-edit\'" onclick="editrow(this);">Edit</a> ';
      				var d = '<a href="#" class="easyui-linkbutton" data-options="iconCls:\'icon-remove\'" onclick="deleterow(this);">Delete</a>';
      				return e+d;
      			}
      		}
      	}	
    ]],
    rowStyler:function(index,row) { // colorize rows. Equivalent to "striped: true", but better look&feel
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onBeforeLoad: function(param) { return (workingData.manga<=0)?false:true; }, // do not load if no manga selected
	onLoadSuccess:function(){ 
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
	},
	onDblClickRow:function(index,row) {
		$('#competicion-datagrid').datagrid('beginEdit',index);
	},
	// actualizacion de los iconos save/edit/delete/cancel
	onBeforeEdit:function(index,row){ row.editing = true; updateActions(index); },
	onAfterEdit:function(index,row){ row.editing = false; updateActions(index); },
	onCancelEdit:function(index,row){ row.editing = false; updateActions(index); }
});

// update row contents on action requested: just force repaint row
function updateActions(index){
		$('#competicion-datagrid').datagrid('updateRow',{
		index: index,
		row:{}
	});
}

function getRowIndex(target){
	var tr = $(target).closest('tr.datagrid-row');
	return parseInt(tr.attr('datagrid-row-index'));
};
function editrow(target){
	$('#competicion-datagrid').datagrid('beginEdit', getRowIndex(target));
};
function deleterow(target){
	$.messager.confirm('Confirmar','Borrar resultados del participante<<br/> ¿Seguro?',function(r){
		if (r){
			alert("TODO: clearResultData() pending")// TODO: clear entry in database and refresh row with new data	
		}
	});
};
function saverow(target){
	$('#competicion-datagrid').datagrid('endEdit', getRowIndex(target));
	// TODO: update database
};
function cancelrow(target){
	$('#competicion-datagrid').datagrid('cancelEdit', getRowIndex(target));
};

//- botones del panel de orden de mangas
addTooltip($('#competicion-reloadBtn').linkbutton(),"Recargar datos de competicion de la manga");
addTooltip($('#competicion-printBtn').linkbutton(),"Imprimir formulario ayudante de pista");

$('#competicion-printBtn').on("click", function () {
	 $.messager.radio(
			 'Selecciona formulario',
			 'Indica el tipo de formulario que quieres generar:', 
			 {1:'Extendido (1 perro/pagina)',5:'Normal (5 perros/pagina)',15:'Compacto (15 perros/pagina)'}, 
			 function(r){
	 	if (!r) return;
		$.fileDownload(
			'/agility/server/pdf/print_entradaDeDatos.php',
			{
				httpMethod: 'GET',
				data: { 
					Prueba: workingData.prueba,
					Jornada: workingData.jornada,
					Manga: workingData.manga,
					Mode: r
				},
		        preparingMessageHtml: "We are preparing your report, please wait...",
		        failMessageHtml: "There was a problem generating your report, please try again."
			}
		);
	}).window({width:300});
    return false; //this is critical to stop the click event which will trigger a normal file download!
});

</script>