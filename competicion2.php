
<?php include_once("dialogs/dlg_perros.inc");?>
<?php include_once("dialogs/dlg_guias.inc");?>
<?php include_once("dialogs/dlg_clubes.inc");?>
<?php include_once("dialogs/dlg_jornadas.inc");?>
 	
<!-- PANEL INFORMATIVO SOBRE LA MANGAS DE ESTA JORNADA -->
<div id="competicion_info" class="easyui-panel" title="Informaci&oacute;n de la jornada de competici&oacute;n">
<div id="competicion_infolayout" class="easyui-layout" style="height:180px">
	<div data-options="region:'west',title:'Mangas de la jornada',split:true,collapsed:false" style="width:200px">
		<table id="competicion-listamangas" class="easyui-datagrid"></table>
	</div>
	<div data-options="region:'center',title:'Datos de la manga'" style="width:600px;font-size:8pt;">
			<form id="competicion-formdatosmanga">
		<input type="hidden" id="dmanga_Manga" name="Manga"/>
		<table id="competicion-tabladatosmanga">
		<tr> <!-- fila 0: datos de los jueces -->
			<td>Juez 1:</td>
			<td colspan="4"><input id="dmanga_Juez1" type="text" name="Juez1"></td>
			<td>Juez 2:</td>
			<td colspan="4"><input id="dmanga_Juez2" type="text" name="Juez2"></td>
		</tr>
		<tr> <!-- fila 1 tipos de recorrido -->
			<td colspan="2">Recorridos: </td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_0" name="Recorrido" value="0" checked="checked">
				<label for="dmanga_Recorrido_0">Recorrido com&uacute;n</label>
			</td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_1" name="Recorrido" value="1">
				<label for="dmanga_Recorrido_1">Std / Mini-Midi</label>
			</td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_2" name="Recorrido" value="2">
				<label for="dmanga_Recorrido_2">Std / Midi / Mini</label>
			</td>
		</tr>
		<tr style="background-color:#c0c0c0"> <!-- fila 2: titulos  -->
			<td>Categor&iacute;a</td>
			<td>Distancia</td>
			<td>Obst&aacute;culos</td>
			<td colspan="4">Tiempo de recorrido Standard</td>
			<td colspan="3">Tiempo de recorrido M&aacute;ximo</td>
		</tr>
		<tr> <!-- fila 3: recorrido comun datos standard -->
			<td>Standard</td>
			<td><input type="text" id="dmanga_DistL" name="DistanciaL" size="4" value="0"/></td>
			<td><input type="text" id="dmanga_ObstL" name="ObstaculosL" size="4" value="0"/></td>
			<!-- datos para TRS standard -->
			<td colspan="2"> 
				<select id="dmanga_TRS_L_Tipo" name="TRS_L_Tipo">
				<option value="0" selected="selected">TRS Fijo</option>
				<option value="1">Mejor recorrido + </option>
				<option value="2">Media 3 mejores + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRS_L_Factor" name="TRS_L_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRS_L_Unit" name="TRS_L_Unit">
				<option value="s" selected="selected">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>
			<!-- datos para TRM standard -->
			<td>
				<select id="dmanga_TRM_L_Tipo" name="TRS_M_Tipo">
				<option value="0" selected="selected">TRM Fijo</option>
				<option value="1">TRS + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRM_L_Factor" name="TRM_L_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRM_L_Unit" name="TRM_L_Unit">
				<option value="s" selected="selected">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>
		</tr>
		<tr> <!-- fila 4: recorrido std / mini+midi datos midi -->
			<td>Medium</td>
			<td><input type="text" id="dmanga_DistM" name="DistanciaM" size="4" value="0"/></td>
			<td><input type="text" id="dmanga_ObstM" name="ObstaculosM" size="4" value="0"/></td>
			<!-- datos para TRS medium -->
			<td colspan="2"> 
				<select id="dmanga_TRS_M_Tipo" name="TRS_M_Tipo">
				<option value="0" selected="selected">TRS Fijo</option>
				<option value="1">Mejor recorrido + </option>
				<option value="2">Media 3 mejores + </option>
				<option value="3">TRS Standard + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRS_M_Factor" name="TRS_M_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRS_M_Unit" name="TRS_M_Unit">
				<option value="s">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>
			<!-- datos para TRM medium -->
			<td>
				<select id="dmanga_TRM_M_Tipo" name="TRS_M_Tipo">
				<option value="0" selected="selected">TRM Fijo</option>
				<option value="1">TRS + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRM_M_Factor" name="TRM_M_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRM_M_Unit" name="TRM_M_Unit">
				<option value="s" selected="selected">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>		
		</tr>
		<tr> <!-- fila 5: recorrido std / mini / midi + datos mini -->
			<td>Small</td>
			<td><input type="text" id="dmanga_DistS" name="DistanciaS" size="4" value="0"/></td>
			<td><input type="text" id="dmanga_ObstS" name="ObstaculosS" size="4" value="0"/></td>
			<!-- datos para TRS small -->
			<td colspan="2"> 
				<select id="dmanga_TRS_S_Tipo" name="TRS_S_Tipo">
				<option value="0" selected="selected">TRS Fijo</option>
				<option value="1">Mejor recorrido + </option>
				<option value="2">Media 3 mejores + </option>
				<option value="3">TRS Standard + </option>
				<option value="4">TRS Medium + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRS_S_Factor" name="TRS_S_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRS_S_Unit" name="TRS_S_Unit">
				<option value="s">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>
			<!-- datos para TRM small -->
			<td>
				<select id="dmanga_TRM_S_Tipo" name="TRS_S_Tipo">
				<option value="0" selected="selected">TRM Fijo</option>
				<option value="1">TRS + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRM_S_Factor" name="TRM_S_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRM_S_Unit" name="TRM_S_Unit">
				<option value="s" selected="selected">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>
		</tr>
		<tr> <!-- fila 6: observaciones -->
			<td colspan="2">Observaciones</td>
			<td colspan="11"><input type="text" id="dmanga_Observaciones" name="Observaciones" size="64" value=""/></td>
		</tr>
		<tr> <!-- fila 7: manga cerrada. botones reset y save -->
			<td colspan="2">
				<label for="dmanga_Cerrada">Cerrar manga</label>
				<input type="checkbox" id="dmanga_Cerrada" name="Cerrada" value="1">
			</td>
			<td colspan="2">&nbsp;</td>
			<td>
				<input type="button" id="dmanga_Restaurar" name="Restaurar" value="Restaurar">
			</td>
			<td colspan="3">&nbsp;</td>
			<td>
				<input type="button" id="dmanga_Guardar" name="Guardar" value="Guardar">
			</td>
		</tr>
		</table>
		</form>
	</div> <!-- datos de la manga -->
</div> <!-- informacion de layout -->
</div> <!-- panel de informacion -->

<!-- DECLARACION DEL ORDEN DE SALIDA DE CADA MANGA -->
<div id="competicion_ordensalida" class="easyui-panel" title="Orden de salida de los participantes en la manga">
<table id="competicion-orden-datagrid" class="competicion-orden-datagrid"></table>
    <!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
    <div id="competicion-orden-toolbar">
    	<span style="float:left">
    	<a id="competicion-orden-randomBtn" href="#" class="easyui-linkbutton" onclick="newOrdenSalida()">Aleatorio</a>
    	<a id="competicion-orden-saveBtn" href="#" class="easyui-linkbutton" onclick="saveOrdenSalida()">Guardar</a>
    	<a id="competicion-orden-reloadBtn" href="#" class="easyui-linkbutton" onclick="reloadOrdenSalida()">Actualizar</a>
    	</span>
    	<span style="float:right">
    	<!-- estos elementos deben estar alineados a la derecha -->
    	<a id="competicion-orden-printBtn" href="#" class="easyui-linkbutton" onclick="printOrdenSalida()">Imprimir</a>
	   	</span>
    </div>
</div> <!-- panel del orden de salida -->

<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
<div id="competicion_resultados" class="easyui-panel" title="Introducci&oacute;n de resultados de cada participante">
<table id="competicion-resultados-datagrid" class="competicion-resultados-datagrid"></table>
    <!-- BARRA DE TAREAS DE TABLA DE RESULTADOS-->
    <div id="competicion-resultados-toolbar">
    	<span style="float:right">
    	<!-- estos elementos deben estar alineados a la derecha -->
    	<a id="competicion-resultados-printBtn" href="#" class="easyui-linkbutton" onclick="printResultados()">Imprimir</a>
	   	</span>
    </div>
    
<script type="text/javascript">
// cargamos nombre de la jornada y de la prueba
$('#Header_Operation').html('<p>Desarrollo de la prueba</p>');

// declaracion de cada elemento grafico
$('#competicion_info').panel({
	title:workingData.nombrePrueba+' -- '+workingData.nombreJornada,
	border:true,
	closable:false,
	collapsible:true,
	collapsed:false
});
$('#competicion_infolayout').layout();
$('#competicion_ordensalida').panel({
	border:true,
	closable:false,
	collapsible:true,
	collapsed:false
});
$('#competicion_resultados').panel({
	border:true,
	closable:false,
	collapsible:true,
	collapsed:true
});

$('#competicion-listamangas').datagrid({
	url: 'database/select_MangasByJornada.php?Jornada='+workingData.jornada,
	method: 'get',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    showHeader: false,
    columns:[[
            { field:'ID',			hidden:true }, // ID de la jornada
      	    { field:'Tipo',			hidden:true }, // ID de la prueba
      	    { field:'Descripcion',	width:50, sortable:false, align:'right'},
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onSelect: function (index,row) {
        if (index<0) { // no manga selected
            // TODO: clear panels
            return; 
        }
        // guardamos el id y el nombre de la manga
        workingData.manga=row.ID;
        workingData.nombreManga=row.Descripcion;
        // load lateral panel with manga data
    }
});

$('#competicion-orden-datagrid').datagrid({
	url: 'database/select_JornadasByPrueba.php?Prueba='+workingData.prueba,
	method: 'get',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    toolbar: '#competicion-orden-toolbar',
    columns:[[
            { field:'ID',			hidden:true }, // ID de la jornada
      	    { field:'Prueba',		hidden:true }, // ID de la prueba
      	    { field:'Numero',		width:4, sortable:false,	align:'center', title: '#'},
      		{ field:'Nombre',		width:40, sortable:false,   align:'right', title: 'Nombre/Comentario' },
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    }
});

$('#competicion-resultados-datagrid').datagrid({
	url: 'database/select_JornadasByPrueba.php?Prueba='+workingData.prueba,
	method: 'get',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    toolbar: '#competicion-resultados-toolbar',
    columns:[[
            { field:'ID',			hidden:true }, // ID de la jornada
      	    { field:'Prueba',		hidden:true }, // ID de la prueba
      	    { field:'Numero',		width:4, sortable:false,	align:'center', title: '#'},
      		{ field:'Nombre',		width:40, sortable:false,   align:'right', title: 'Nombre/Comentario' },
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    }
});

//- botones del panel de orden de mangas
$('#competicion-orden-reloadBtn').linkbutton({plain:true,iconCls:'icon-reload'}); // nueva inscricion 
$('#competicion-orden-reloadBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Actualizar el orden de salida desde base de datos</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#competicion-orden-randomBtn').linkbutton({plain:true,iconCls:'icon-dice'}); // nueva inscricion 
$('#competicion-orden-randomBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Generar un nuevo orden de salida aleatorio</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#competicion-orden-saveBtn').linkbutton({plain:true,iconCls:'icon-save'}); // editar inscripcion      
$('#competicion-orden-saveBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Guardar orden de salida en base de datos</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#competicion-orden-printBtn').linkbutton({plain:true,iconCls:'icon-print'}); // imprimir listado 
$('#competicion-orden-printBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Imprimir el orden de salida</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

// botones del panel de resultados
$('#competicion-resultados-printBtn').linkbutton({plain:true,iconCls:'icon-print'}); // imprimir listado 
$('#competicion-resultados-printBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Imprimir resultados de la manga</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
</script>
    