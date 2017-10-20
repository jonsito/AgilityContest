<!-- 
dlg_competicion_games.inc

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
<div id="competicion-dialog" class="editable-datagrid" style="width:900px;height:450px;padding:5px">
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="competicion-datagrid"></table>
</div>

<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
<div id="competicion-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
	   	<a id="competicion-reloadBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-reload'" onclick="reloadCompeticion()"><?php _e('Update'); ?></a>
	   	<label for="competicion-autoUpdateBtn"><?php _e('Auto'); ?>:</label>
	   	<input id="competicion-autoUpdateBtn" type="checkbox" class="easyui-checkbox" value="0" onclick="autoUpdateCompeticion()"/>
   		<input id="competicion-search" type="text" value="--- Dorsal ---" class="search_textfield"  onchange="competicionSelectByDorsal();"/>
		<label for="competicion-categoria"><?php _e('Cat');?></label>
        <select id="competicion-categoria" style="width:100px"></select>
   	</span>
   	<span style="float:right;padding:5px">
   		<!-- estos elementos deben estar alineados a la derecha -->
	   	<a id="competicion-swapBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-updown'" onclick="swapMangas()"><?php _e('Swap'); ?></a>
	   	<a id="competicion-resetBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-brush'" onclick="resetCompeticion()"><?php _e('Reset'); ?></a>
   		<a id="competicion-printBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-print'" onclick="print_commonDesarrollo(8,'#competicion-categoria');"><?php _e('Printer forms'); ?></a>
	</span>
</div>

<script type="text/javascript">
$('#competicion-dialog').dialog({
	collapsible:false,
	minimizable:false,
	maximizable:false,
	resizable:true,
	iconCls:'icon-table',
	closed:true,
	modal:true,
	onBeforeClose: function() {
		var dg=$('#competicion-datagrid');
		var editIndex=dg.datagrid('options').editIndex;
		if (editIndex<0) {
			// disable autoupdate if any
			$('#competicion-autoUpdateBtn').prop('checked',false);
			autoUpdateCompeticion();
			return true;
		}
		// do not allow close on editing active
		$.messager.alert('<?php _e('Error'); ?>','<?php _e('Please cancel or save changes before closing window'); ?>','error');
		return false;
	}
});

$('#competicion-categoria').combobox({
	value: '-',
	panelHeight: 'auto',
	valueField: 'Categoria',
	textField: 'Nombre',
	data: getOrdenSalidaCategorias(),
	onChange: function(newValue,oldValue) { reloadCompeticion(); }
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
		Manga: workingData.manga,
		Categorias: $('#competicion-categoria').combobox('getValue'),
		TeamView: isTeam(workingData.datosManga.Tipo)?'true':'false'
    },
    loadMsg: '<?php _e("Updating competition data"); ?> ....',
    toolbar: '#competicion-toolbar',
    pagination: false,
	// these two fields are required in 1.4.2 editable datagrid, due to bad rownumber cell height evaluation
	// and poor rendering performance. To be removed in a newer jeasyui version
	autoRowHeight:false, // as specific css is needed for rownumbers, do not autoevaluate row height
    rownumbers: true, // fix rownumber cell height to take care on editable datagrid
    fitColumns: true,
	idField: 'Dorsal',
    singleSelect: true,
    editIndex: -1, // nonstandard added by me :-)
    // toolbar: '#competicion-toolbar',
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'Perro',		hidden:true },
		{ field:'Equipo',		hidden:true },
      	{ field:'Licencia',		hidden:true },
      	{ field:'Pendiente',	hidden:true },
        { field:'Dorsal',		width:12, align:'right',  title: '<?php _e('Dorsal'); ?>', styler:checkPending },
        { field:'Nombre',		width:20, align:'left',  title: '<?php _e('Name'); ?>',formatter:formatBold},
        { field:'NombreGuia',	width:40, align:'right', title: '<?php _e('Handler'); ?>' },
        { field:'NombreClub',	width:30, align:'right', title: '<?php _e('Club'); ?>' },
      	{ field:'Categoria',	width:10, align:'center',title: '<?php _e('Cat'); ?>.',formatter:formatCategoria },
        { field:'Grado',        hidden:true},
        // datos a editar
      	{ field:'Faltas',		width:15, align:'right', title: '<?php _e('Opening'); ?>.',	editor:'numberbox' },
      	{ field:'Tocados',		width:15, align:'right', title: '<?php _e('Closing'); ?>.',editor:'numberbox' },
        { field:'Rehuses',       hidden:true},
      	{ field:'Tiempo',		width:15, align:'right', title: '<?php _e('Time'); ?>',
          	editor:{
          		type:'numberbox',
          		options:{		
              		formatter:function(v){	return toFixedT( parseFloat(v),ac_config.numdecs); },
        			parser:function(s){	var v = parseFloat(s);	return isNaN(v)?'':v; }
            	}
          	}
        },
        { field:'Eliminado',hidden:true},
      	{ field:'NoPresentado',	width:10, align:'center',title: '<?php _e('N.P.'); ?>',
            formatter:formatNoPresentado, editor:{type:'checkbox',options:{on:1,off:0}}	},
      	{ field:'Observaciones',width:17, align:'left',	 title: '<?php _e('Comments'); ?>',	editor:'text'  },
        // botones de edicion
      	{ field:'action',		width:48, align:'center',title: '',
      		formatter:function(value,row,index){
				if(row.Dorsal==='*') return "&nbsp;";
          		if (row.editing){
          		    var h = '<a href="#" id="lnkb3_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-notes\'" onclick="showAssistantSheet(this);"></a>';
    				var s = '<a href="#" id="lnkb1_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-save\'" onclick="saverow(this);"><?php _e('Save'); ?></a> ';
    				var c = '<a href="#" id="lnkb2_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-undo\'" onclick="cancelrow(this);"><?php _e('Cancel'); ?></a>';
    				return h+s+c;
    			} else {
    				var e = '<a href="#" id="lnkb1_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-edit\'" onclick="editrow(this);"><?php _e('Edit'); ?></a> ';
    				var d = '<a href="#" id="lnkb2_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-trash\'" onclick="deleterow(this);"><?php _e('Clear'); ?></a>';
    				return "&nbsp;"+e+d;
    			}
      		}
      	}	
    ]],
    rowStyler:competicionRowStyler,
    onBeforeLoad: function(param) { return (workingData.manga > 0); }, // do not load if no manga selected
	onLoadSuccess:function(){
		// mark no row is being edited
		$(this).datagrid('options').editIndex=-1;
		// render datagrid buttons
		$(this).datagrid('getPanel').find('a.easyui-linkbutton').linkbutton();
		// enter focus to allow key binding
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
	},
	onDblClickRow:function(index,row) {
		if(row.Dorsal==='*') return; // not on editable row
		if( $(this).datagrid('options').editIndex!==-1) return; // on editing ignore double click
		$(this).datagrid('beginEdit',index);
		// focus and first field and select contents
		var ed = $(this).datagrid('getEditor', {index:index,field:'Faltas'});
		var input=$(ed.target).next().find('input');
		input.focus();
		input.select();
		// input.setSelectionRange(0, input.value.length);
	},
	// actualizacion de los iconos save/edit/delete/cancel
	onBeforeEdit:function(index,row){
		if(row.Dorsal==='*') return false;
		$(this).datagrid('options').editIndex=index;
		row.editing = true; 
		updateActions(index);
		return true;
	},
	onAfterEdit:function(index,row){
		$(this).datagrid('options').editIndex=-1;
		row.editing = false;
		updateActions(index);
	},
	onCancelEdit:function(index,row){
		$(this).datagrid('options').editIndex=-1;
		row.editing = false;
		updateActions(index);
	},
	onBeforeSelect: function(index,row) {
		return ($(this).datagrid('options').editIndex===-1);
	}
});

// update row contents on action requested: just force repaint row and reassign buttons
function updateActions(index){
	var cdg=$('#competicion-datagrid');
    var editIndex=cdg.datagrid('options').editIndex;
	cdg.datagrid('updateRow',{
		index: index,
		row:{}
	});
	$('#lnkb1_'+index).linkbutton();
    $('#lnkb2_'+index).linkbutton();
    $('#lnkb3_'+index).linkbutton();
	// enable/disable search textbox acording edit state
	$('#competicion-search').prop('disabled',( editIndex===-1)?false:true);
	$('#competicion-categoria').combobox( (editIndex===-1)?'enable':'disable');
}

function getRowIndex(target){
	var tr = $(target).closest('tr.datagrid-row');
	return parseInt(tr.attr('datagrid-row-index'));
}
function editrow(target){
	$('#competicion-datagrid').datagrid('beginEdit', getRowIndex(target));
}
function deleterow(target){
	$.messager.confirm('<?php _e('Confirm'); ?>','<?php _e('Clear competitor results');?>'+'<br/>'+'<?php _e('Sure?'); ?>',function(r){
		if (r){
			var editIndex=getRowIndex(target);
			var data=$('#competicion-datagrid').datagrid('getRows')[editIndex];
			data.Faltas=0;
			data.Tocados=0;
			data.Rehuses=0;
			data.Games=0;
            if (isJornadaKO()) data.Games=1; // trick to mark dog is runing in this round
			data.Tiempo=0.0;
			data.Eliminado=0;
			data.NoPresentado=0;
			data.Observaciones="";
			data.Pendiente=1;
			saveCompeticionData(editIndex,data);	
		}
	});
}
function saverow(target){
    var dg=$('#competicion-datagrid');
	var editIndex=getRowIndex(target);
	dg.datagrid('endEdit', editIndex );
	var data=dg.datagrid('getRows')[editIndex];
	data.Pendiente=0;
	if (isJornadaKO()) data.Games=1; // trick to mark dog is competing in this ko round
	saveCompeticionData(editIndex,data);
}

function cancelrow(target){
	$('#competicion-datagrid').datagrid('cancelEdit', getRowIndex(target));
}

function showAssistantSheet(target) {
    // si no estoy en edicion, ignorar
    var idx=getRowIndex(target);
    if ($('#competicion-datagrid').datagrid('options').editIndex!==idx) return false;
    alert('hola');
    // en funcion del tipo de juego abrimos dialogo de snooker o de gambler
    // PENDING: write
}

//- botones del panel de entrada de datos
addTooltip($('#competicion-reloadBtn').linkbutton(),'<?php _e("Reload competition data on this round"); ?>');
addTooltip($('#competicion-swapBtn').linkbutton(),'<?php _e("Swap data and results from Agility/Jumping matching round"); ?>');
addTooltip($('#competicion-resetBtn').linkbutton(),'<?php _e("Delete every competitors data on this round"); ?>');
addTooltip($('#competicion-printBtn').linkbutton(),'<?php _e("Print judge assistant forms"); ?>');
addTooltip($('#competicion-autoUpdateBtn'),'<?php _e("(de)activate periodic refresh of results for this round"); ?>');
addTooltip($('#competicion-search'),'<?php _e("Search a competitor by entering dorsal"); ?>');
addTooltip($('#competicion-categoria').combobox('textbox'),'<?php _e("Select categories to work with"); ?>');

// key handling
$('#competicion-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).bind('keydown',competicionKeyEventHandler);

</script>