<!--  dialogo de presentacion de resultados parciales de una manga -->

<!-- TABLA DE DATOS DE TRS Y TRM DE LA MANGA -->
<div id="vw_parcial-trs">
	<form id="vw_parcial-trs-form" method="get">
		<table style="float:left;">
			<tr><th colspan="6">Datos t&eacute;cnicos de la manga seleccionada(s)</th></tr>
			<tr id="vw_parcial-LargeRow">
				<td><input type="radio" name="rRecorrido" id="vw_parcial-LargeBtn" value="0" onclick="reloadParcial(0)"></td>
				<th id="vw_parcial-LargeLbl">Large</th>
				<td>Distancia <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_DIST_L"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_OBST_L"></td>
				<td>TRS <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRS_L"></td>
				<td>TRM <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRM_L"></td>
				<td>Vel. <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_VEL_L"></td>
			</tr>
			<tr id="vw_parcial-MediumRow">
				<td><input type="radio" name="rRecorrido" id="vw_parcial-LargeBtn" value="1" onclick="reloadParcial(1)"></td>
				<th id="vw_parcial-MediumLbl">Medium</th>
				<td>Distancia <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_DIST_M"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_OBST_M"></td>
				<td>TRS <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRS_M"></td>
				<td>TRM <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRM_M"></td>
				<td>Vel. <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_VEL_M"></td>
			</tr>
			<tr id="vw_parcial-SmallRow">
				<td><input type="radio" name="rRecorrido" id="vw_parcial-LargeBtn" value="2" onclick="reloadParcial(2)"></td>
				<th id="vw_parcial-SmallLbl">Small</th>
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
<table id="vw_parcial-datagrid"></table>

<script type="text/javascript">

$('#vw_parcial-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: '/agility/server/database/resultadosFunctions.php',
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
    // rowStyler:myRowStyler,
    // onBeforeLoad: function(param) { return (workingData.manga<=0)?false:true; }, // do not load if no manga selected
    onBeforeLoad: function(param) { param.Manga=2; return true; }, // do not load if no manga selected
});

</script>