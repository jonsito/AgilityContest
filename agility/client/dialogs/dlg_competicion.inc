<!--  dialogo de orden de salida e introduccion de datos de una competicion -->
<div id="competicion-window" class="easyui-window" style="width:800px;height:450px;padding:0px 10px 40px 10px">
	<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
	<div id="competicion-toolbar" style="padding:5px">
 		<span style="float:left">
		   	<a id="competicion-saveBtn" href="#" class="easyui-linkbutton" 
		   		data-options="iconCls:'icon-save'" onclick="endEditing()">Guardar</a>
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
	url: 'server/database/ordenSalidaFunctions.php',
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
      	{ field:'Observaciones',width:20, align:'left',	 title: 'Observaciones',	editor:'text'  }
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

var competicion_editIndex = undefined;

var pressed={};

// stupid trick to avoid auto-repeat of keydown event
/*
$('#competicion-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keyup',function(e){
	var keycode = (e.keyCode ? e.keyCode : e.which);
    pressed[keycode] = null; // mark key as released
    return false;
});
*/
//activa teclas up/down para navegar por el panel de gestion de jornadas
$('#competicion-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keyup',function(e){
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

    var keycode = (e.keyCode ? e.keyCode : e.which);
/*
    if (pressed[keycode]!=null) return false; // already fired; ignore
    pressed[keycode]=true;
*/
	var t = $('#competicion-datagrid');
    switch(keycode){
    case 38:	/* Up */	if (competicion_editIndex===undefined) selectRow(t,true); return false;
    case 40:    /* Down */	if (competicion_editIndex===undefined) selectRow(t,false); return false;
    case 13:	/* Enter */ 
        if (competicion_editIndex!==undefined) {
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
	if (competicion_editIndex == undefined){return true; }
	if ($('#competicion-datagrid').datagrid('validateRow', competicion_editIndex)) {
		$('#competicion-datagrid').datagrid('endEdit', competicion_editIndex);
		// TODO: obtener y grabar los datos de la fila que se estaba editando
		var data=$('#competicion-datagrid').datagrid('getRows')[competicion_editIndex];
		saveCompeticionData(competicion_editIndex,data);
		competicion_editIndex = undefined;
		return true;
	} else {
		return false;
	}
}

function onClickRow(index){
	if (competicion_editIndex != index){
		if (endEditing()){
			$('#competicion-datagrid').datagrid('selectRow', index).datagrid('beginEdit', index);
			competicion_editIndex = index;
		} else {
			$('#competicion-datagrid').datagrid('selectRow', competicion_editIndex);
		}
	}
}

//- botones del panel de orden de mangas
addTooltip($('#competicion-reloadBtn').linkbutton(),"Recargar datos de competicion de la manga");
addTooltip($('#competicion-saveBtn').linkbutton(),"Guardar datos de competicion");
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