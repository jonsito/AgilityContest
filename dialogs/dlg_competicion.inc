<!--  dialogo de orden de salida e introduccion de datos de una competicion -->
<div id="competicion-window" class="easyui-window" style="width:800px;height:450px;padding:20px 10px 20px 10px">
	<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
	<div id="competicion-toolbar">
 		<span style="float:left">
		   	<a id="competicion-saveBtn" href="#" class="easyui-linkbutton" onclick="endEditing()">Guardar</a>
		   	<a id="competicion-reloadBtn" href="#" class="easyui-linkbutton" onclick="reloadCompeticion()">Actualizar</a>
    		</span>
    		<span style="float:right">
    		<!-- estos elementos deben estar alineados a la derecha -->
    		<a id="competicion-printBtn" href="#" class="easyui-linkbutton" onclick="printCompeticion()">Imprimir</a>
		</span>
	</div>
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="competicion-datagrid" class="easyui-datagrid"></table>
</div>

<script type="text/javascript">

$('#competicion-window').window({
	collapsible:	false,
	minimizable:	false,
	maximizable:	false,
	resizable:		false,
	iconCls:		'icon-table',
	closed:			true,
	modal:			true
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
	url: 'database/resultadosFunctions.php?Operation=enumerate',
    queryParams: {
        Jornada: workingData.jornada,
        Manga: workingData.manga
    },
    loadMsg: "Actualizando datos de competicion ...",
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    // toolbar: '#competicion-toolbar',
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'Dorsal',		hidden:true },
      	{ field:'Licencia',		hidden:true },
        { field:'Nombre',		width:20, align:'left',  title: 'Nombre'},
        { field:'Guia',			width:50, align:'right', title: 'Guia' },
        { field:'Club',			width:30, align:'right', title: 'Club' },
      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' },
      	{ field:'Faltas',		width:15, align:'right', title: 'Faltas',	editor:'numberbox' },
      	{ field:'Rehuses',		width:15, align:'right', title: 'Rehuses',	editor:'numberbox', },
      	{ field:'Tocados',		width:15, align:'right', title: 'Tocados',	editor:'numberbox' },
      	{ field:'Tiempo',		width:20, align:'right', title: 'Tiempo',	editor:{type:'numberbox',options:{precision:2}}	}, 
      	{ field:'Eliminado',	width:10, align:'center',title: 'Elim.',	editor:{type:'checkbox',options:{on:1,off:0}}	},
      	{ field:'NoPresentado',	width:10, align:'center',title: 'N.P.',		editor:{type:'checkbox',options:{on:1,off:0}}	},		
      	{ field:'Observaciones',width:30, align:'left',	 title: 'Observaciones',	editor:'text'  }
    ]],
    rowStyler:function(index,row) { // colorize rows. Equivalent to "striped: true", but better look&feel
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onBeforeLoad: function(param) { return (workingData.manga<=0)?false:true; }, // do not load if no manga selected
	onLoadSuccess:function(){ 
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
	},
    onClickRow: onClickRow // start edit
});

var editIndex = undefined;

//activa teclas up/down para navegar por el panel de gestion de jornadas
$('#competicion-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
    function selectRow(t,up){
    	var count = t.datagrid('getRows').length;    // row count
    	var selected = t.datagrid('getSelected');
    	if (selected){
        	var index = t.datagrid('getRowIndex', selected);
        	index = index + (up ? -1 : 1);
        	if (index < 0) index = 0;
        	if (index >= count) index = count - 1;
        	t.datagrid('clearSelections');
        	t.datagrid('selectRow', index);
    	} else {
        	t.datagrid('selectRow', (up ? count-1 : 0));
    	}
	}
	var t = $('#competicion-datagrid');
    switch(e.keyCode){
    case 38:	/* Up */	if (editIndex===undefined) selectRow(t,true); return false;
    case 40:    /* Down */	if (editIndex===undefined) selectRow(t,false); return false;
    case 13:	/* Enter */ 
        if (editIndex!==undefined) {
            endEditing(); // si estamos editando: finalizamos edicion
            selectRow(t,false); // ponemos el indice en la siguiente fila
        } 
        // abrimos edicion
        onClickRow(t.datagrid('getRowIndex', t.datagrid('getSelected')));// no estamos editando: pasamos a editar
        return false;
    case 27:	/* Escape */ $('#competicion-window').window('close'); return false;
    }
});

function endEditing(){
	if (editIndex == undefined){return true; }
	if ($('#competicion-datagrid').datagrid('validateRow', editIndex)) {
		$('#competicion-datagrid').datagrid('endEdit', editIndex);
		// TODO: obtener y grabar los datos de la fila que se estaba editando
		var data=$('#competicion-datagrid').datagrid('getRows')[editIndex];
		saveCompeticionData(editIndex,data);
		editIndex = undefined;
		return true;
	} else {
		return false;
	}
}

function onClickRow(index){
	if (editIndex != index){
		if (endEditing()){
			$('#competicion-datagrid').datagrid('selectRow', index).datagrid('beginEdit', index);
			editIndex = index;
		} else {
			$('#competicion-datagrid').datagrid('selectRow', editIndex);
		}
	}
}

//- botones del panel de orden de mangas
$('#competicion-reloadBtn').linkbutton({plain:true,iconCls:'icon-reload'}); // nueva inscricion 
$('#competicion-reloadBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Actualizar datos de competicion de la manga</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#competicion-saveBtn').linkbutton({plain:true,iconCls:'icon-save'}); // editar inscripcion      
$('#competicion-saveBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Guardar datos de competicion</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#competicion-printBtn').linkbutton({plain:true,iconCls:'icon-print'}); // imprimir listado 
$('#competicion-printBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Imprimir formulario ayudante de pista</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

</script>