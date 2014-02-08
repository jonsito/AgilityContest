<!--  dialogo de orden de salida e introduccion de datos de una competicion -->
<div id="competicion-window" class="easyui-window" style="width:800px;height:400px;padding:10px 10px">
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="competicion-datagrid" class="easyui-datagrid"></table>
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
</div>

<script type="text/javascript">

$('#competicion-window').window({
	collapsible:	false,
	minimizable:	false,
	maximizable:	false,
	resizable:		true,
	closed:			true,
	modal:			true
});

$('#competicion-datagrid').datagrid({
	// propiedades del panel asociado
	noheader: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	url: 'database/resultadosFunctions.php?Operation=enumerate',
    queryParams: {
        Jornada: workingData.jornada,
        Manga: workingData.manga
    },
	method: 'get',
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    pageSize:10,
    toolbar: '#competicion-toolbar',
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'Dorsal',		hidden:true },
      	{ field:'Licencia',		hidden:true },
        { field:'Nombre',		width:20, align:'left',  title: 'Nombre'},
        { field:'Guia',			width:50, align:'right', title: 'Guia' },
        { field:'Club',			width:30, align:'right', title: 'Club' },
      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' },
      	{ field:'Faltas',		width:15, align:'right', title: 'Faltas',	
          	editor:'numberbox',
      		formatter: function(value, row, index){ return '<span title=\"Faltas\" class=\"easyui-tooltip\">'+value+'</span>';}
        },
      	{ field:'Rehuses',		width:15, align:'right', title: 'Rehuses',
          	editor:'numberbox', 
      		formatter: function(value, row, index){ return '<span title=\"Rehuses\" class=\"easyui-tooltip\">'+value+'</span>';}
        },
      	{ field:'Tocados',		width:15, align:'right', title: 'Tocados',
          	editor:'numberbox', 
      		formatter: function(value, row, index){ return '<span title=\"Tocados\" class=\"easyui-tooltip\">'+value+'</span>';}
        },
      	{ field:'Tiempo',		width:20, align:'right', title: 'Tiempo',
          	editor:{type:'numberbox',options:{precision:2}},
  			formatter: function(value, row, index){ return '<span title=\"Tiempo\" class=\"easyui-tooltip\">'+value+'</span>';}
      	}, 
      	{ field:'Eliminado',	width:10, align:'center',title: 'Elim.',
          	editor:{type:'checkbox',options:{on:1,off:0}},
      		formatter: function(value, row, index){ return '<span title=\"Eliminado\" class=\"easyui-tooltip\">'+value+'</span>';} 
      	},
      	{ field:'NoPresentado',	width:10, align:'center',title: 'N.P.',
          	editor:{type:'checkbox',options:{on:1,off:0}},
      		formatter: function(value, row, index){ return '<span title=\"No presentado\" class=\"easyui-tooltip\">'+value+'</span>';} 
      	},		
      	{ field:'Observaciones',width:30, align:'left',	 title: 'Observaciones', 
          	editor:'text',
      		formatter: function(value, row, index){ return '<span title=\"Observaciones\" class=\"easyui-tooltip\">'+value+'</span>';} 
        }
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onBeforeLoad: function(param) { return (workingData.manga<=0)?false:true; }, // do not load if no manga selected
	onLoadSuccess:function(){ $(this).datagrid('getPanel').find('.easyui-tooltip').tooltip({showDelay: 500}); },
    loadMsg: "Actualizando datos de competicion ...",
    onClickRow: onClickRow // start edit
});

var editIndex = undefined;
function endEditing(){
	if (editIndex == undefined){return true}
	if ($('#competicion-datagrid').datagrid('validateRow', editIndex)) {
		$('#competicion-datagrid').datagrid('endEdit', editIndex);
		// TODO: obtener y grabar los datos de la fila que se estaba editando
		var data=$('#competicion-datagrid').datagrid('getRows')[editIndex];
		saveCompeticionData(data);
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