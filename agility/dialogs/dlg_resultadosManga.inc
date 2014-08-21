<!--  dialogo de presentacion de resultados parciales de una manga -->
<div id="resultadosmanga-window" class="easyui-window" style="width:800px;height:450px;padding:0px 10px 100px 10px">
	<!-- BARRA DE TAREAS DE RESULTADOS PARCIALES DE MANGA -->
	<div id="resultadosmanga-toolbar">
 		<span style="float:right;padding:5px">
		   	<a id="resultadosmanga-reloadBtn" href="#" class="easyui-linkbutton" 
		   		data-options="iconCls:'icon-reload'" onclick="reloadResultadosManga()">Actualizar</a>
		   	<br />
    		<a id="resultadosmanga-printBtn" href="#" class="easyui-linkbutton" 
    			data-options="iconCls:'icon-print'" onclick="printResultadosManga()">Imprimir</a>
		</span>
	</div>
	<!-- TABLA DE DATOS DE TRS Y TRM DE LA MANGA -->
	<div id="resultadosmanga-trs">
		<form id="resultadosmanga-trs-form" method="get">
			<table style="float:left;">
				<tr><th colspan="6">Datos de la manga y categor&iacute;a(s) seleccionada(s)</th></tr>
				<tr id="resultadosmanga-LargeRow">
					<td><input type="radio" name="Recorrido" id="resultadosmanga-LargeBtn" value="0" checked="checked"></td>
					<th id="resultadosmanga-LargeLbl">Large</th>
					<td>Distancia <input type="text" size="4" class="trstrm" readonly="readonly" name="DIST_L"></td>
					<td>Obst&aacute;culos <input type="text" size="4" class="trstrm" readonly="readonly" name="OBST_L"></td>
					<td>TRS <input type="text" size="4" class="trstrm" readonly="readonly" name="TRS_L"></td>
					<td>TRM <input type="text" size="4" class="trstrm" readonly="readonly" name="TRM_L"></td>
					<td>Vel. <input type="text" size="4" class="trstrm" readonly="readonly" name="VEL_L"></td>
				</tr>
				<tr id="resultadosmanga-MediumRow">
					<td><input type="radio" name="Recorrido" id="resultadosmanga-LargeBtn" value="1"></td>
					<th id="resultadosmanga-MediumLbl">Medium</th>
					<td>Distancia <input type="text" size="4" class="trstrm" readonly="readonly" name="DIST_M"></td>
					<td>Obst&aacute;culos <input type="text" size="4" class="trstrm" readonly="readonly" name="OBST_M"></td>
					<td>TRS <input type="text" size="4" class="trstrm" readonly="readonly" name="TRS_M"></td>
					<td>TRM <input type="text" size="4" class="trstrm" readonly="readonly" name="TRM_M"></td>
					<td>Vel. <input type="text" size="4" class="trstrm" readonly="readonly" name="VEL_M"></td>
				</tr>
				<tr id="resultadosmanga-SmallRow">
					<td><input type="radio" name="Recorrido" id="resultadosmanga-LargeBtn" value="2"></td>
					<th id="resultadosmanga-SmallLbl">Small</th>
					<td>Distancia <input type="text" size="4" class="trstrm" readonly="readonly" name="DIST_S"></td>
					<td>Obst&aacute;culos <input type="text" size="4" class="trstrm" readonly="readonly" name="OBST_S"></td>
					<td>TRS <input type="text" size="4" class="trstrm" readonly="readonly" name="TRS_S"></td>
					<td>TRM <input type="text" size="4" class="trstrm" readonly="readonly" name="TRM_S"></td>
					<td>Vel. <input type="text" size="4" class="trstrm" readonly="readonly" name="VEL_S"></td>
				</tr>
			</table>
		</form>
	</div>
	<!-- TABLA DE PRESENTACION DE RESULTADOS DE CADA MANGA -->
	<table id="resultadosmanga-datagrid"></table>
</div>

<script type="text/javascript">


$('#resultadosmanga-window').window({
	collapsible:	false,
	minimizable:	false,
	maximizable:	false,
	resizable:		false,
	iconCls:		'icon-endflag',
	closed:			true,
	modal:			false
});

$('#resultadosmanga-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: 'database/resultadosFunctions.php',
    queryParams: {
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Manga: workingData.manga,
        Mode: (workingData.datosManga.Recorrido!=2)?0:4, // def to 'Large' or 'LMS' depending of datosmanga
        Operation: 'getResultados'
    },
    loadMsg: "Actualizando resultados de la manga ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    // toolbar: '#resultadosmanga-toolbar',
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'Perro',		hidden:true },
        { field:'Dorsal',		hidden:true},
      	{ field:'Licencia',		hidden:true },
        { field:'Puesto',		width:12, align:'left',  title: 'Puesto'},
        { field:'Nombre',		width:15, align:'left',  title: 'Nombre'},
        { field:'NombreGuia',	width:40, align:'right', title: 'Guia' },
        { field:'NombreClub',	width:25, align:'right', title: 'Club' },
      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' },
      	{ field:'Faltas',		width:10, align:'center', title: 'Faltas'},
      	{ field:'Rehuses',		width:10, align:'center', title: 'Rehuses'},
      	{ field:'Tocados',		width:10, align:'center', title: 'Tocados'},
      	{ field:'Tiempo',		width:15, align:'right', title: 'Tiempo'},
      	{ field:'Velocidad',	width:10, align:'right', title: 'Vel.'},
      	{ field:'Penalizacion',	width:15, align:'right', title: 'Penal.'}, 
      	{ field:'Calificacion',	width:25, align:'center',title: 'Calificacion'}
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onBeforeLoad: function(param) { return (workingData.manga<=0)?false:true; }, // do not load if no manga selected
    onLoadSuccess: function() { // get focus on datagrid (to bind keystrokes)
    	$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
    }
});

//Activa tecla ESC (escape) para cerrar ventana
$('#resultadosmanga-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
	if (e.keyCode==27) $('#resultadosmanga-window').window('close'); 
	return false;
});

//- botones del panel de resultados de la manga
addTooltip($('#resultadosmanga-reloadBtn').linkbutton(),"Actualizar resultados de la manga");
addTooltip($('#resultadosmanga-printBtn').linkbutton(),"Imprimir resultados de la manga");

</script>