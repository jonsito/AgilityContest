<!-- CLASIFICACIONES DE PRUEBA/JORNADA/RONDA -->
<div id="resultados-info" class="easyui-panel" title="Clasificaciones">
<div id="resultados-infolayout" class="easyui-layout" style="height:225px">
	<div data-options="region:'west',title:'Datos de la Prueba',split:true,collapsed:false" style="width:300px;padding:10px;font-size:9px">
		<form class="result_forms" id="resultados-info-prueba" method="get">
		<table>
		<tr>
		<td colspan="2"><label for="Nombre">Denominaci&oacute;n:</label><br />
		<input id="resultados-info-nombre" type="text" class="result_forms" readonly="readonly" name="Nombre" size="35"/></td>
		</tr>
		<tr>
		<td><label for="Club">Club Organizador:</label></td>
		<td><input id="resultados-info-club" type="text" class="result_forms" readonly="readonly" name="Club"/></td>
		</tr>
		<tr>
		<td><label for="Jornada">Jornada:</label></td>
		<td><input id="resultados-info-jornada" type="text" class="result_forms" readonly="readonly" name="Jornada"/></td>
		</tr>
		<tr>
		<td><label for="Fecha">Fecha:</label></td>
		<td><input id="resultados-info-fecha" type="text" class="result_forms" readonly="readonly" name="Fecha"/></td>
		</tr>
		<tr>
		<td><label for="Ronda">Ronda:</label></td>
		<td><input id="resultados-info-ronda" type="text" class="result_forms" readonly="readonly" name="Ronda"/></td>
		</tr>
		<tr>
		<td><label for="Observaciones">Observaciones:</label></td>
		<td><input id="resultados-info-comments" type="text" class="result_forms" readonly="readonly" name="Observaciones"/></td>
		</tr>
		</form>
		</table>
	</div> <!-- Datos de Prueba/Jornada/Ronda -->
	
	<div data-options="region:'center',title:'Datos de las Mangas de esta ronda'" style="width:500px">
		<!-- TABLA DE DATOS DE TRS Y TRM DE LA MANGA 1-->
		<form class="result_forms" id="resultados-manga1-trs-form" method="get">
			<label for="Tipo">Manga 1: </label>
			<input id="rm1t" type="text" name="Tipo" readonly="readonly" size="6">
			<label for="Juez">Juez: </label>
			<input id="rm1j" type="text" name="Juez" readonly="readonly" size="50">
			<table>
				<tr>
					<th>Large</th>
					<td>Distancia <input type="text" size=4" class="result_forms" readonly="readonly" name="DIST_L"></td>
					<td>Obst&aacute;culos <input type="text" size=4" class="result_forms" readonly="readonly" name="OBST_L"></td>
					<td>TRS <input type="text" size=4" class="result_forms" readonly="readonly" name="TRS_L"></td>
					<td>TRM <input type="text" size=4" class="result_forms" readonly="readonly" name="TRM_L"></td>
					<td>Vel. <input type="text" size=4" class="result_forms" readonly="readonly" name="VEL_L"></td>
				</tr>
				<tr>
					<th>Medium</th>
					<td>Distancia <input type="text" size=4" class="result_forms" readonly="readonly" name="DIST_M"></td>
					<td>Obst&aacute;culos <input type="text" size=4" class="result_forms" readonly="readonly" name="OBST_M"></td>
					<td>TRS <input type="text" size=4" class="result_forms" readonly="readonly" name="TRS_M"></td>
					<td>TRM <input type="text" size=4" class="result_forms" readonly="readonly" name="TRM_M"></td>
					<td>Vel. <input type="text" size=4" class="result_forms" readonly="readonly" name="VEL_M"></td>
				</tr>
				<tr>
					<th>Small</th>
					<td>Distancia <input type="text" size=4" class="result_forms" readonly="readonly" name="DIST_S"></td>
					<td>Obst&aacute;culos <input type="text" size=4" class="result_forms" readonly="readonly" name="OBST_S"></td>
					<td>TRS <input type="text" size=4" class="result_forms" readonly="readonly" name="TRS_S"></td>
					<td>TRM <input type="text" size=4" class="result_forms" readonly="readonly" name="TRM_S"></td>
					<td>Vel. <input type="text" size=4" class="result_forms" readonly="readonly" name="VEL_S"></td>
				</tr>
			</table>
		</form> <!-- Datos de la manga 1 -->
		<hr />
		<form class="result_forms" id="resultados-manga2-trs-form" method="get">
			<label for="Tipo">Manga 2: </label>
			<input id="rm2t" type="text" name="Tipo" readonly="readonly" size="6">
			<label for="Juez">Juez: </label>
			<input id="rm2j" type="text" name="Juez" readonly="readonly" size="50">
			<table>
				<tr>
					<th>Large</th>
					<td>Distancia <input type="text" size=4" class="result_forms" readonly="readonly" name="DIST_L"></td>
					<td>Obst&aacute;culos <input type="text" size=4" class="result_forms" readonly="readonly" name="OBST_L"></td>
					<td>TRS <input type="text" size=4" class="result_forms" readonly="readonly" name="TRS_L"></td>
					<td>TRM <input type="text" size=4" class="result_forms" readonly="readonly" name="TRM_L"></td>
					<td>Vel. <input type="text" size=4" class="result_forms" readonly="readonly" name="VEL_L"></td>
				</tr>
				<tr>
					<th>Medium</th>
					<td>Distancia <input type="text" size=4" class="result_forms" readonly="readonly" name="DIST_M"></td>
					<td>Obst&aacute;culos <input type="text" size=4" class="result_forms" readonly="readonly" name="OBST_M"></td>
					<td>TRS <input type="text" size=4" class="result_forms" readonly="readonly" name="TRS_M"></td>
					<td>TRM <input type="text" size=4" class="result_forms" readonly="readonly" name="TRM_M"></td>
					<td>Vel. <input type="text" size=4" class="result_forms" readonly="readonly" name="VEL_M"></td>
				</tr>
				<tr>
					<th>Small</th>
					<td>Distancia <input type="text" size=4" class="result_forms" readonly="readonly" name="DIST_S"></td>
					<td>Obst&aacute;culos <input type="text" size=4" class="result_forms" readonly="readonly" name="OBST_S"></td>
					<td>TRS <input type="text" size=4" class="result_forms" readonly="readonly" name="TRS_S"></td>
					<td>TRM <input type="text" size=4" class="result_forms" readonly="readonly" name="TRM_S"></td>
					<td>Vel. <input type="text" size=4" class="result_forms" readonly="readonly" name="VEL_S"></td>
				</tr>
			</table>
		</form> <!-- Datos de la manga 2 -->
	</div> <!-- Layout: center --> 
</div> <!-- informacion de layout -->
</div> <!-- panel de informacion -->

<script type="text/javascript">

//cabecera de la pagina
$('#Header_Operation').html('<p>Resultados y Clasificaciones</p>');
//inicializamos formularios
$('#resultados-info').panel({
	border:true,
	closable:false,
	collapsible:true,
	collapsed:false
});
$('#resultados-infolayout').layout();
$('#resultados-info-prueba').form('load',{
	Nombre:	workingData.datosPrueba.Nombre,
	Club:	workingData.datosPrueba.Club,
	Jornada: workingData.datosJornada.Nombre,
	Fecha:	workingData.datosJornada.Fecha,
	Ronda:	workingData.datosRonda.Nombre,
	Observaciones: workingData.datosPrueba.Observaciones
});
$('#resultados-manga1-trs-form').form({
		onLoadSuccess: function (data) {
			var j=" - "+workingData.datosRonda.Juez12;
			$('#rm1t').val(workingData.datosRonda.Nombre1);
			if (j===" - -- Sin asignar --") j=""; 
			$('#rm1j').val(workingData.datosRonda.Juez11+j);
			// data.Observaciones=workingData.datosRonda.Observaciones1;
		}
	});
$('#resultados-manga1-trs-form').form(
		'load',
		"database/mangaFunctions.php?Operation=getTRS&Jornada="+workingData.jornada+"&Manga="+workingData.datosRonda.Manga1
		);
$('#resultados-manga2-trs-form').form({
	onLoadSuccess: function (data) {
		var j=" - "+workingData.datosRonda.Juez22;
		$('#rm2t').val(workingData.datosRonda.Nombre2);
		if (j===" - -- Sin asignar --") j=""; 
		$('#rm2j').val(workingData.datosRonda.Juez21+j);
		// data.Observaciones=workingData.datosRonda.Observaciones2;
	}
});
$('#resultados-manga2-trs-form').form(
		'load',
		"database/mangaFunctions.php?Operation=getTRS&Jornada="+workingData.jornada+"&Manga="+workingData.datosRonda.Manga2
		);
</script>