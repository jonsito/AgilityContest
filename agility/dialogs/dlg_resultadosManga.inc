<!--  dialogo de presentacion de resultados parciales de una manga -->
<div id="resultadosmanga-window" class="easyui-window" style="width:800px;height:450px;padding:0px 10px 100px 10px">
	<!-- BARRA DE TAREAS DE RESULTADOS PARCIALES DE MANGA -->
	<div id="resultadosmanga-toolbar">
 		<span style="float:right;padding:5px">
		   	<a id="resultadosmanga-reloadBtn" href="#" class="easyui-linkbutton" 
		   		data-options="iconCls:'icon-reload'" onclick="reloadParcial($('input:radio[name=rRecorrido]:checked').val())">Actualizar</a>
		   	<br />
    		<a id="resultadosmanga-printBtn" href="#" class="easyui-linkbutton" 
    			data-options="iconCls:'icon-print'" onclick="printParcial($('input:radio[name=rRecorrido]:checked').val())">Imprimir</a>
		</span>
	</div>
	<!-- TABLA DE DATOS DE TRS Y TRM DE LA MANGA -->
	<div id="resultadosmanga-trs">
		<form id="resultadosmanga-trs-form" method="get">
			<table style="float:left;">
				<tr><th colspan="6">Datos t&eacute;cnicos de la manga seleccionada(s)</th></tr>
				<tr id="resultadosmanga-LargeRow">
					<td><input type="radio" name="rRecorrido" id="resultadosmanga-LargeBtn" value="0" onclick="reloadParcial(0)"></td>
					<th id="resultadosmanga-LargeLbl">Large</th>
					<td>Distancia <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_DIST_L"></td>
					<td>Obst&aacute;culos <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_OBST_L"></td>
					<td>TRS <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRS_L"></td>
					<td>TRM <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRM_L"></td>
					<td>Vel. <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_VEL_L"></td>
				</tr>
				<tr id="resultadosmanga-MediumRow">
					<td><input type="radio" name="rRecorrido" id="resultadosmanga-LargeBtn" value="1" onclick="reloadParcial(1)"></td>
					<th id="resultadosmanga-MediumLbl">Medium</th>
					<td>Distancia <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_DIST_M"></td>
					<td>Obst&aacute;culos <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_OBST_M"></td>
					<td>TRS <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRS_M"></td>
					<td>TRM <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRM_M"></td>
					<td>Vel. <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_VEL_M"></td>
				</tr>
				<tr id="resultadosmanga-SmallRow">
					<td><input type="radio" name="rRecorrido" id="resultadosmanga-LargeBtn" value="2" onclick="reloadParcial(2)"></td>
					<th id="resultadosmanga-SmallLbl">Small</th>
					<td>Distancia <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_DIST_S"></td>
					<td>Obst&aacute;culos <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_OBST_S"></td>
					<td>TRS <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRS_S"></td>
					<td>TRM <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRM_S"></td>
					<td>Vel. <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_VEL_S"></td>
				</tr>
				<tr><th colspan="6">Resultados de la categor&iacute;a seleccionada</th></tr>
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
	modal:			true
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
	url: '/agility/database/resultadosFunctions.php',
    queryParams: {
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        //Manga: workingData.manga,
        Manga: 2,
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
        { field:'Puesto',		width:12, align:'left',  title: 'Puesto', formatter:formatPuesto},
        { field:'Nombre',		width:15, align:'left',  title: 'Nombre'},
        { field:'NombreGuia',	width:35, align:'right', title: 'Guia' },
        { field:'NombreClub',	width:25, align:'right', title: 'Club' },
      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' },
      	{ field:'Faltas',		width:10, align:'center', title: 'Faltas'},
      	{ field:'Tocados',		width:10, align:'center', title: 'Tocados'},
      	{ field:'Rehuses',		width:10, align:'center', title: 'Rehuses'},
      	{ field:'PRecorrido',	hidden:true },
      	{ field:'Tiempo',		width:15, align:'right', title: 'Tiempo', formatter:formatTiempo},
      	{ field:'PTiempo',		hidden:true },
      	{ field:'Velocidad',	width:10, align:'right', title: 'Vel.', formatter:formatVelocidad},
      	{ field:'Penalizacion',	width:15, align:'right', title: 'Penal.', formatter:formatPenalizacion}, 
      	{ field:'Calificacion',	width:30, align:'center',title: 'Calificacion'}, 
      	{ field:'CShort', hidden:true}
    ]],
    rowStyler:myRowStyler,
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
addTooltip($('#resultadosmanga-reloadBtn').linkbutton(),"Actualizar datos de la manga/categoria seleccionada");
addTooltip($('#resultadosmanga-printBtn').linkbutton(),"Imprimir resultados de la manga");

</script>