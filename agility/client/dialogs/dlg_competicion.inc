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
<div id="competicion-window"style="width:900px;height:450px;padding:5px">
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="competicion-datagrid" style="padding:5px"></table>
</div>

<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
<div id="competicion-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
	   	<a id="competicion-reloadBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-reload'" onclick="reloadCompeticion()">Actualizar</a>
	   	Auto:
	   	<input id="competicion-autoUpdateBtn" type="checkbox" class="easyui-checkbox" value="0" onclick="autoUpdateCompeticion()"/>
   	</span>
   	<span style="float:right;padding:5px">
   		<!-- estos elementos deben estar alineados a la derecha -->
   		<a id="competicion-printBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-print'">Formularios</a>
	</span>
</div>

<script type="text/javascript">
$('#competicion-window').window({
	collapsible:false,
	minimizable:false,
	maximizable:false,
	resizable:true,
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
    toolbar: '#competicion-toolbar',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    editIndex: -1, // nonstandard added by me :-)
    // toolbar: '#competicion-toolbar',
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'Perro',		hidden:true },
      	{ field:'Licencia',		hidden:true },
      	{ field:'Pendiente',	hidden:true },
        { field:'Dorsal',		width:10, align:'right',  title: 'Dorsal', styler:checkPending },
        { field:'Nombre',		width:20, align:'left',  title: 'Nombre'},
        { field:'NombreGuia',	width:40, align:'right', title: 'Guia' },
        { field:'NombreClub',	width:30, align:'right', title: 'Club' },
      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' },
      	{ field:'Faltas',		width:7, align:'right', title: 'Flt',	editor:'numberbox' },
      	{ field:'Tocados',		width:7, align:'right', title: 'Toc.',	editor:'numberbox' },
      	{ field:'Rehuses',		width:7, align:'right', title: 'Reh.',	editor:'numberbox' },
      	{ field:'Tiempo',		width:15, align:'right', title: 'Tiempo',
          	editor:{
          		type:'numberbox',
          		options:{		
              		formatter:function(v){	return parseFloat(v).toFixed(2); },
        			parser:function(s){	var v = parseFloat(s);	return isNaN(v)?'':v; }
            	}
          	}
        }, 
      	{ field:'Eliminado',	width:10, align:'center',title: 'Elim.',	editor:{type:'checkbox',options:{on:1,off:0}}	},
      	{ field:'NoPresentado',	width:10, align:'center',title: 'N.P.',		editor:{type:'checkbox',options:{on:1,off:0}}	}, 
      	{ field:'Observaciones',width:20, align:'left',	 title: 'Observaciones',	editor:'text'  },
      	{ field:'action',		width:35, align:'center',title: '',
      		formatter:function(value,row,index){
          		if (row.editing){
    				var s = '<a href="#" id="lnkb1_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-save\'" onclick="saverow(this);">Save</a> ';
    				var c = '<a href="#" id="lnkb2_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-undo\'" onclick="cancelrow(this);">Cancel</a>';
    				return s+c;
    			} else {
    				var e = '<a href="#" id="lnkb1_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-edit\'" onclick="editrow(this);">Edit</a> ';
    				var d = '<a href="#" id="lnkb2_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-trash\'" onclick="deleterow(this);">Clear</a>';
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
		// mark no row is being edited
		$(this).datagrid('options').editIndex=-1;
		// render datagrid buttons
		$(this).datagrid('getPanel').find('a.easyui-linkbutton').linkbutton();
		// enter focus to allow key binding
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
	},
	onDblClickRow:function(index,row) {
		if( $(this).datagrid('options').editIndex!=-1) return; // on editing ignore double click
		$(this).datagrid('beginEdit',index);
		var ed = $(this).datagrid('getEditor', {index:index,field:'Faltas'});
		$(ed.target).next().find('input').focus();
	},
	// actualizacion de los iconos save/edit/delete/cancel
	onBeforeEdit:function(index,row){ $(this).datagrid('options').editIndex=index; row.editing = true; updateActions(index); },
	onAfterEdit:function(index,row){ $(this).datagrid('options').editIndex=-1; row.editing = false; updateActions(index); },
	onCancelEdit:function(index,row){ $(this).datagrid('options').editIndex=-1; row.editing = false; updateActions(index); }
});

// update row contents on action requested: just force repaint row and reassign buttons
function updateActions(index){
	$('#competicion-datagrid').datagrid('updateRow',{
		index: index,
		row:{}
	});
	$('#lnkb1_'+index).linkbutton();
	$('#lnkb2_'+index).linkbutton();
}

function getRowIndex(target){
	var tr = $(target).closest('tr.datagrid-row');
	return parseInt(tr.attr('datagrid-row-index'));
};

function editrow(target){
	$('#competicion-datagrid').datagrid('beginEdit', getRowIndex(target));
};
function deleterow(target){
	$.messager.confirm('Confirmar','Borrar resultados del participante <br/> ¿Seguro?',function(r){
		if (r){
			var editIndex=getRowIndex(target);
			var data=$('#competicion-datagrid').datagrid('getRows')[editIndex];
			data.Faltas=0;
			data.Tocados=0;
			data.Rehuses=0;
			data.Tiempo=0.0;
			data.Eliminado=0;
			data.NoPresentado=0;
			data.Observaciones="";
			data.Pendiente=1;
			saveCompeticionData(editIndex,data);	
		}
	});
};
function saverow(target){
	var editIndex=getRowIndex(target);
	$('#competicion-datagrid').datagrid('endEdit', editIndex );
	var data=$('#competicion-datagrid').datagrid('getRows')[editIndex];
	data.Pendiente=0;
	saveCompeticionData(editIndex,data);
};
function cancelrow(target){
	$('#competicion-datagrid').datagrid('cancelEdit', getRowIndex(target));
};

//- botones del panel de entrada de datos
addTooltip($('#competicion-reloadBtn').linkbutton(),"Recargar datos de competicion de la manga");
addTooltip($('#competicion-printBtn').linkbutton(),"Imprimir formulario ayudante de pista");
addTooltip($('#competicion-autoUpdateBtn'),"(Des)activar actualizacion periodica de los datos");

// key handling
$('#competicion-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).bind('keydown',competicionKeyEventHandler);

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