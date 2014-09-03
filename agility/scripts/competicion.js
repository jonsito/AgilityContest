/**
 * Funciones relacionadas con la ventana de competicion
 */

/************************** Gestion de datos de la ventana de manga activa */

/* formatters para el datagrid dlg_resultadosManga */

function formatPuesto(val,row,idx) { return (row.Penalizacion>=200)?"-":val; }
function formatVelocidad(val,row,idx) { return (row.Penalizacion>=200)?"-":parseFloat(val).toFixed(1); }
function formatTiempo(val,row,idx) { return (row.Penalizacion>=200)?"-":parseFloat(val).toFixed(2); }
function formatPenalizacion(val,row,idx) { return parseFloat(val).toFixed(2); }

/**
 * Actualiza el modo de visualizacion del panel infomangas
 * en funcion del tipo de recorrido seleccionado
 */
function dmanga_setRecorridos() {
	var val=$("input[name='Recorrido']:checked").val();
	workingData.datosManga.Recorrido=val;
	switch (val) {
	case '2': // recorrido comun para std, mini y midi
		var distl=$('#dmanga_DistL').val();
		var obstl=$('#dmanga_ObstL').val();
		$('#dmanga_DistM').attr('readonly',true);
		$('#dmanga_DistM').val(distl);
		$('#dmanga_ObstM').attr('readonly',true);
		$('#dmanga_ObstM').val(obstl);
		$('#dmanga_DistS').attr('readonly',true);
		$('#dmanga_DistS').val(distl);
		$('#dmanga_ObstS').attr('readonly',true);
		$('#dmanga_ObstS').val(obstl);
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','none');
		$('#dmanga_SmallRow').css('display','none');
		$('#dmanga_LargeLbl').html("Com&uacute;n");
		$('#dmanga_MediumLbl').html("&nbsp;");
		$('#dmanga_SmallLbl').html("&nbsp;");
		break;
	case '1': // un recorrido para std y otro para mini-midi
		var distm=$('#dmanga_DistM').val();
		var obstm=$('#dmanga_ObstM').val();
		$('#dmanga_DistM').removeAttr('readonly');
		$('#dmanga_ObstM').removeAttr('readonly');
		$('#dmanga_DistS').attr('readonly',true);
		$('#dmanga_DistS').val(distm);
		$('#dmanga_ObstS').attr('readonly',true);
		$('#dmanga_ObstS').val(obstm);
		// set TRS relative to Midi TRS
		$('#dmanga_TRS_S_Tipo').val(4); 
		$('#dmanga_TRS_S_Factor').val(0);
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','table-row');
		$('#dmanga_SmallRow').css('display','none');
		$('#dmanga_LargeLbl').html("Large");
		$('#dmanga_MediumLbl').html("Med.+Small");
		$('#dmanga_SmallLbl').html("&nbsp;");
		break;
	case '0': // recorridos separados para cada categoria
		$('#dmanga_DistM').removeAttr('readonly');
		$('#dmanga_ObstM').removeAttr('readonly');
		$('#dmanga_DistS').removeAttr('readonly');
		$('#dmanga_ObstS').removeAttr('readonly');
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','table-row');
		$('#dmanga_SmallRow').css('display','table-row');
		$('#dmanga_LargeLbl').html("Large");
		$('#dmanga_MediumLbl').html("Medium");
		$('#dmanga_SmallLbl').html("Small");
		break;
	}
}

/**
 * Guarda las modificaciones a los datos de una manga
 * Notese que esto no debería modificar ni los datos del
 * orden de salida ni resultados de la competicion
 * @param {Integer} id Identificador de la manga
 */
function save_manga(id) {
	$("#competicion-formdatosmanga").bind('ajax:complete', function() {
		// on submit success, reload results
		var recorrido=$("input:radio[name=Recorrido]:checked").val();
		workingData.datosManga.Recorrido=val;
		reloadResultadosManga(recorrido);
	});
	$('#competicion-formdatosmanga').form('submit', {
		url: 'database/mangaFunctions.php',
		onSubmit: function(param) {
			param.Operation='update';
			param.Jornada=workingData.jornada;
			param.Manga=id;
			return true; // to continue submitting
		}
	});
}

/**
 * Recarga los datos asociados a una manga
 * Restaura ventana de informacion, orden de salida y competicion
 * @param id identificador de la manga
 */
function reload_manga(id) {
	// ventana de datos
	var url='database/mangaFunctions.php?Operation=getbyid&Jornada='+workingData.jornada+"&Manga="+id;
    $('#competicion-formdatosmanga').form('load',url);
}

function reloadOrdenSalida() {
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
    $('#ordensalida-datagrid').datagrid(
            'load',
            { 
            	Prueba: workingData.prueba,
            	Jornada: workingData.jornada , 
            	Manga: workingData.manga , 
            	Operation: 'getData' 
            }
    );
}

function reloadCompeticion() {
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
    $('#competicion-datagrid').datagrid(
            'load',
            { 
            	Prueba: workingData.prueba ,
            	Jornada: workingData.jornada , 
            	Manga: workingData.manga , 
            	Operation: 'getData' 
            }
    );
}

/**
 * imprime los resultados de la manga/categoria solicitadas
 * @param val 0:large/conjunto 1:medium/m+s 2:small
 */
function printParcial(val) {
	var mode=0;
	var value=parseInt(val); // stupid javascript!!
	// obtenemos informacion sobre los datos a imprimir
	switch(parseInt(workingData.datosManga.Recorrido)) {
	case 0: //  large / medium / small
		switch(value) {	case 0: mode=0; break; case 1: mode=1; break; case 2: mode=2; break; }
		break;
	case 1: // large / medium+small
		switch(value) {	case 0: mode=0; break; case 1: mode=3; break; case 2: mode=3; break; }
		break;
	case 2: // large+medium+small
		switch(value) {	case 0: mode=4; break; case 1: mode=4; break; case 2: mode=4; break; }
		break;
	}
	// imprimimos los datos de la manga y categoria solicitada
	$.fileDownload(
		'pdf/print_resultadosByManga.php',
		{
			httpMethod: 'GET',
			data: { 
				Prueba: workingData.prueba,
				Jornada: workingData.jornada,
				Manga: workingData.manga,
				Mode: mode,
				Operation: 'print'
			},
	        preparingMessageHtml: "We are preparing your report, please wait...",
	        failMessageHtml: "There was a problem generating your report, please try again."
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/** actualiza el datagrid de resultados
 * @param mode 0:large/conjunto 1:medium/m+s 2:small
 */
function reloadParcial(val) {
	var mode=0;
	var value=parseInt(val); // stupid javascript!!
	switch (parseInt(workingData.datosManga.Recorrido)) {
	case 0: //  large / medium / small
		switch(value) {	case 0: mode=0; break; case 1: mode=1; break; case 2: mode=2; break; }
		break;
	case 1: // large / medium+small
		switch(value) {	case 0: mode=0; break; case 1: mode=3; break; case 2: mode=3; break; /* invalido*/	}
		break;
	case 2: // large+medium+small
		switch(value) {	case 0: mode=4; break; 	case 1: mode=4; break; /* invalido */ case 2: mode=4; break; /* invalido */	}
		break;
	}
	// reload resultados
	// en lugar de invocar al datagrid, lo que vamos a hacer es
	// una peticion ajax, para obtener a la vez los datos tecnicos de la manga

	$.ajax({
		type:'GET',
		url:"database/resultadosFunctions.php",
		dataType:'json',
		data: {
			Operation:	'getResultados',
			Prueba:		workingData.prueba,
			Jornada:	workingData.jornada,
			Manga:		workingData.manga,
			Mode: mode
		},
		success: function(dat) {
			var suffix='L';
			switch (mode) {
			case 0: case 4: suffix='L'; break;
			case 1: case 3: suffix='M'; break;
			case 2: suffix='S'; break;
			}
			$('#rm_DIST_'+suffix).val(dat['trs'].dist);
			$('#rm_OBST_'+suffix).val(dat['trs'].obst);
			$('#rm_TRS_'+suffix).val(dat['trs'].trs);
			$('#rm_TRM_'+suffix).val(dat['trs'].trm);
			$('#rm_VEL_'+suffix).val(dat['trs'].vel);
			$('#resultadosmanga-datagrid').datagrid('loadData',dat);
		}
	});
}

/**
 * Inicializa ventana de resultados
 * borra datagrid previa y recalcula datos de TRS 
 * @param recorrido 0:L/M/S 1:L/M+S 2:/L+M+S
 */
function reloadResultadosManga(recorrido) {
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
    $('#resultadosmanga-LargeBtn').prop('checked',false);
    $('#resultadosmanga-MediumBtn').prop('checked',false);
    $('#resultadosmanga-SmallBtn').prop('checked',false);
    $('#resultadosmanga-datagrid').datagrid('loadData',{total:0, rows:{}});
    // actualizamos la informacion del panel de informacion de trs/trm
    switch(parseInt(recorrido)){
    case 0: // Large / Medium / Small
    	// ajustar textos
    	$('#resultadosmanga-MediumRow').css('display','table-row');
    	$('#resultadosmanga-SmallRow').css('display','table-row');
    	$('#resultadosmanga-LargeLbl').html("Large");
    	$('#resultadosmanga-MediumLbl').html("Medium");
    	$('#resultadosmanga-SmallLbl').html("Small");
    	// obtener datos de trs y trm para cada categoria
    	break;
    case 1: // Large / Medium+Small
    	// ajustar textos
    	$('#resultadosmanga-MediumRow').css('display','table-row');
    	$('#resultadosmanga-SmallRow').css('display','none');
    	$('#resultadosmanga-LargeLbl').html("Large");
    	$('#resultadosmanga-MediumLbl').html("Medium+Small");
    	$('#resultadosmanga-SmallLbl').html("&nbsp;");
    	// obtener datos de trs y trm para cada categoria
    	break;
    case 2: // Large+Medium+Small conjunta
    	// ajustar textos
    	$('#resultadosmanga-MediumRow').css('display','none');
    	$('#resultadosmanga-SmallRow').css('display','none');
    	$('#resultadosmanga-LargeLbl').html('Conjunta L+M+S');
    	$('#resultadosmanga-MediumLbl').html("&nbsp;");
    	$('#resultadosmanga-SmallLbl').html("&nbsp;");
    	// obtener datos de trs y trm para cada categoria
    	break;
    }
}

function saveCompeticionData(idx,data) {
	$.ajax({
		type:'GET',
		url:"database/resultadosFunctions.php",
		dataType:'json',
		data: {
			Operation:	'update',
			Prueba:		workingData.prueba,
			Jornada:	workingData.jornada,
			Manga:		workingData.manga,
			Dorsal: 	data['Dorsal'],
			Perro: 		data['Perro'],
			Licencia:	data['Licencia'],
			Nombre:		data['Nombre'],
			NombreGuia:	data['NombreGuia'],
			NombreClub:	data['NombreClub'],
			Categoria:	data['Categoria'],
			Tocados:	data['Tocados'],
			Faltas:		data['Faltas'],
			Rehuses:	data['Rehuses'],
			Tiempo:		data['Tiempo'],
			Eliminado:	data['Eliminado'],
			NoPresentado:	data['NoPresentado'],
			Observaciones:	data['Observaciones']
		},
		success: function(dat) {
			if (dat.Manga!=workingData.manga) return; // window changed
			$('#competicion-datagrid').datagrid('updateRow',{index: idx,row: dat});
		}
	});
}

// genera un nuevo orden aleatorio
function evalOrdenSalida(mode) {
	if (workingData.prueba==0) return;
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
	$.ajax({
		type:'GET',
		url:"database/ordenSalidaFunctions.php",
		dataType:'json',
		data: {
			Prueba: workingData.prueba,
			Jornada: workingData.jornada,
			Manga: workingData.manga,
			Operation: mode
		}
	}).done( function(msg) {
		reloadOrdenSalida();
	});
}

// reajusta el orden de salida 
// poniendo el idperro "from" delante (where==0) o detras (where==1) del idperro "to"
function dragAndDrop(from,to,where) {
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
	$.ajax({
		type:'GET',
		url:"database/ordenSalidaFunctions.php",
		dataType:'json',
		data: {
			Operation: 'dnd',
			Prueba: workingData.prueba,
			Jornada: workingData.jornada,
			Manga: workingData.manga,
			From: from,
			To: to,
			Where: where
		}
	}).done( function(msg) {
		reloadOrdenSalida();
	});
}

/**
 * Abre la ventana de competicion requerida 'ordensalida','competicion','resultadosmanga'
 * @param name
 */
function competicionDialog(name) {
	// obtenemos datos de la manga seleccionada
	var row= $('#competicion-listamangas').datagrid('getSelected');
    if (!row) {
    	$.messager.alert('Error','No hay ninguna manga seleccionada','info');
    	return; // no hay ninguna manga seleccionada. retornar
    }
    var title = workingData.nombrePrueba + ' -- ' + workingData.nombreJornada + ' -- ' + workingData.nombreManga;
    $('#ordensalida-window').dialog('close');
    $('#competicion-window').dialog('close');
    $('#resultadosmanga-window').dialog('close');
    if (name==='ordensalida') {
        // abrimos ventana de dialogo
        $('#ordensalida-window').dialog('open').window('setTitle'," Orden de Salida: "+title);
        // cargamos ventana de orden de salida
        reloadOrdenSalida();
    }
    if (name==='competicion') {
        // abrimos ventana de dialogo
        $('#competicion-window').dialog('open').window('setTitle'," Entrada de datos: "+title);
        // cargamos ventana de orden de salida
        reloadCompeticion();
    }
    if (name==='resultadosmanga') {
        // abrimos ventana de dialogo
        $('#resultadosmanga-window').dialog('open').window('setTitle'," Resultados de la manga: "+title);
        // cargamos ventana de orden de salida
        reloadResultadosManga(row.Recorrido);
    }
}

/************************************* funciones para la ventana de resultados **************************/

/*
 * Recordatorio de los campos de las tablas de resultados y clasificaciones
 * 
 * Resultados-datagrid
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
      	{ field:'Rehuses',		width:10, align:'center', title: 'Rehuses'},
      	{ field:'Tocados',		width:10, align:'center', title: 'Tocados'},
      	{ field:'PRecorrido',	hidden:true },
      	{ field:'Tiempo',		width:15, align:'right', title: 'Tiempo', formatter:formatTiempo},
      	{ field:'PTiempo',		hidden:true },
      	{ field:'Velocidad',	width:10, align:'right', title: 'Vel.', formatter:formatVelocidad},
      	{ field:'Penalizacion',	width:15, align:'right', title: 'Penal.', formatter:formatPenalizacion}, 
      	{ field:'Calificacion',	width:30, align:'center',title: 'Calificacion'}, 
      	{ field:'CShort', hidden:true}
      	
 *Clasificaciones-datagrid
        { field:'Perro',		hidden:true },
        { field:'Dorsal',		hidden:true},
      	{ field:'Licencia',		hidden:true },
        { field:'Nombre',		width:15, align:'left',  title: 'Nombre'},
        { field:'NombreGuia',	width:35, align:'right', title: 'Guia' },
        { field:'NombreClub',	width:25, align:'right', title: 'Club' },
      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' },
      	{ field:'F1',		width:10, align:'center', title: 'Faltas 1'},
      	{ field:'F2',		width:10, align:'center', title: 'Faltas 2'},
      	{ field:'R1',		width:10, align:'center', title: 'Rehuses 1'},
      	{ field:'R2',		width:10, align:'center', title: 'Rehuses 2'},
      	{ field:'T1',		width:10, align:'center', title: 'Tocados 1'},
      	{ field:'T2',		width:10, align:'center', title: 'Tocados 2'},
      	{ field:'PR1',	hidden:true }, // Penalizacion recorrido 1
      	{ field:'PR2',	hidden:true }, // Penalizacion recorrido 2
      	{ field:'Tiempo1',		width:15, align:'right', title: 'Tiempo', formatter:formatTiempo},
      	{ field:'Tiempo2',		width:15, align:'right', title: 'Tiempo', formatter:formatTiempo},
      	{ field:'PT1',		hidden:true }, // Penalizacion tiempo 1
      	{ field:'PT2',		hidden:true }, // Penalizacion tiempo 2
      	{ field:'V1',	width:10, align:'right', title: 'Vel. 1', formatter:formatVelocidad},
      	{ field:'V2',	width:10, align:'right', title: 'Vel. 2', formatter:formatVelocidad},
      	{ field:'P1',	width:15, align:'right', title: 'Penal 1', formatter:formatPenalizacion}, 
      	{ field:'P2',	width:15, align:'right', title: 'Penal 2', formatter:formatPenalizacion},  
      	{ field:'C1', hidden:true} // Calificacion 1
      	{ field:'C2', hidden:true} // Calificacion 2
        { field:'Puesto',		width:12, align:'left',  title: 'Puesto', formatter:formatPuesto},
      	{ field:'Calificacion',	width:30, align:'center',title: 'Calificacion'},
      	{ field:'Penalizacion',	width:30, align:'center',title: 'Penalizacion'},
 */

/**
 * extrae los datos de la manga 1 de la fila asociada y compone la primera parte de la tabla clasificaciones
 * asociada al modo indicado 
 */
function parseManga1(clasificaciones,mode,row) {
	clasificaciones[mode][row['Perro']]= {
	        'Perro':		row['Perro'],
	        'Dorsal':		row['Dorsal'],
	      	'Licencia':		row['Licencia'],
	        'Nombre':		row['Nombre'],
	        'NombreGuia':	row['NombreGuia'],
	        'NombreClub':	row['NombreClub'],
	      	'Categoria':	row['Categoria'],
	      	'F1':			row['Faltas'],
	      	'F2':			0,
	      	'R1':			row['Rehuses'],
	      	'R2':			0,
	      	'T1':			row['Tocados'],
	      	'T2':			0,
	      	'PR1':			row['PRecorrido'],
	      	'PR2':			0,
	      	'Tiempo1':		row['Tiempo'],
	      	'Tiempo2':		0,
	      	'PT1':			row['PTiempo'],
	      	'PT2':			0,
	      	'V1':			row['Velocidad'],
	      	'V2':			0,
	      	'P1':			row['Penalizacion'],
	      	'P2':			0,  
	      	'C1':			row['CShort'],
	      	'C2':			0,
	        'Puesto':		0,
	      	'Calificacion':	0,
	      	'Penalizacion':	0
	};
}

/**
 * extrae los datos de la manga 2 de la fila asociada y compone segunda parte de la tabla clasificaciones
 * asociada al modo indicado 
 */
function parseManga2(clasificaciones,mode,row) {
	// Juntamos y mezclamos los datos de la manga 1 con la 2
	var res=$.extend(clasificaciones[mode][row['Perro']],{
	      	'F2':			row['Faltas'],
	      	'R2':			row['Rehuses'],
	      	'T2':			row['Tocados'],
	      	'PR2':			row['PRecorrido'],
	      	'Tiempo2':		row['Tiempo'],
	      	'PT2':			row['PTiempo'],
	      	'V2':			row['Velocidad'],
	      	'P2':			row['Penalizacion'],
	      	'C2':			row['CShort']
	});
	// save evaluated data into our object
	clasificaciones[mode][row['Perro']]=res;
}

/**
 * rellena los diversos formularios de informacion de resultados
 * resultados: almacen de resultados (array[mode][manga]
 * idmanga: Manga ID
 * idxmanga: 1..2 manga index
 * mode: 0..4
 */
function resultados_fillForm(resultados,idmanga,idxmanga,mode) {
	$.ajax({
		type:'GET',
		url:"database/resultadosFunctions.php",
		dataType:'json',
		data: {	Operation:'getResultados', Prueba:workingData.prueba, Jornada:workingData.jornada, Manga:idmanga, Mode: mode },
		success: function(dat) {
			var suffix='L';
			switch (mode) {
			case 0: case 4: suffix='L'; break;
			case 1: case 3: suffix='M'; break;
			case 2: suffix='S'; break;
			}
			$('#dm'+idxmanga+'_Nombre').val(dat['manga'].TipoManga);
			$('#dm'+idxmanga+'_Juez1').val('(juez 1 pendiente)');
			$('#dm'+idxmanga+'_Juez2').val('(juez 2 pendiente)');
			$('#dm'+idxmanga+'_DIST_'+suffix).val(dat['trs'].dist);
			$('#dm'+idxmanga+'_OBST_'+suffix).val(dat['trs'].obst);
			$('#dm'+idxmanga+'_TRS_'+suffix).val(dat['trs'].trs);
			$('#dm'+idxmanga+'_TRM_'+suffix).val(dat['trs'].trm);
			$('#dm'+idxmanga+'_VEL_'+suffix).val(dat['trs'].vel);
			// store manga results
			if (typeof resultados[mode] === "undefined") resultados[mode]=[];
			resultados[mode][idxmanga]=dat['rows'];
			// alert(JSON.stringify(resultados[mode]));
		}
	});
}

/**
 * rellena la ventana de informacion con los datos definitivos de cada manga de la ronda seleccionada
 */
function resultados_doSelectRonda(row) {
	var resultados=[];
	var clasificaciones=[];
    // FASE 1 Ajustamos en funcion del tipo de recorrido lo que debemos ver en las mangas
    // Recordatorio: ambas mangas tienen siempre el mismo tipo de recorrido
    switch(parseInt(row.Recorrido1)){
    case 0: // Large / Medium / Small
    	// Manga 1
    	$('#datos_manga1-MediumRow').css('display','table-row');
    	$('#datos_manga1-SmallRow').css('display','table-row');
    	$('#datos_manga1-LargeLbl').html("Large");
    	$('#datos_manga1-MediumLbl').html("Medium");
    	$('#datos_manga1-SmallLbl').html("Small");
		resultados_fillForm(resultados,row.Manga1,'1',0);
		resultados_fillForm(resultados,row.Manga1,'1',1);
		resultados_fillForm(resultados,row.Manga1,'1',2);
		$('#resultados-selectCategoria').combobox('loadData',
				[{mode:0,text:'Large',selected:true},{mode:1,text:'Medium'},{mode:2,text:'Small'}]);
    	// Manga 2
		if (row.Manga2<=0) {
			// esta ronda solo tiene una manga. desactiva la segunda
			$('#datos_manga2-InfoRow').css('display','none');
			$('#datos_manga2-LargeRow').css('display','none');
			$('#datos_manga2-MediumRow').css('display','none');
			$('#datos_manga2-SmallRow').css('display','none');
		} else {
			$('#datos_manga2-InfoRow').css('display','table-row');
			$('#datos_manga2-LargeRow').css('display','table-row');
    		$('#datos_manga2-MediumRow').css('display','table-row');
    		$('#datos_manga2-SmallRow').css('display','table-row');
    		$('#datos_manga2-LargeLbl').html("Large");
    		$('#datos_manga2-MediumLbl').html("Medium");
    		$('#datos_manga2-SmallLbl').html("Small");
    		resultados_fillForm(resultados,row.Manga2,'2',0);
    		resultados_fillForm(resultados,row.Manga2,'2',1);
    		resultados_fillForm(resultados,row.Manga2,'2',2);
		}
    	break;
    case 1: // Large / Medium+Small
    	// Manga 1
    	$('#datos_manga1-MediumRow').css('display','table-row');
    	$('#datos_manga1-SmallRow').css('display','none');
    	$('#datos_manga1-LargeLbl').html("Large");
    	$('#datos_manga1-MediumLbl').html("Medium+Small");
    	$('#datos_manga1-SmallLbl').html("&nbsp;");
		resultados_fillForm(resultados,row.Manga1,'1',0);
		resultados_fillForm(resultados,row.Manga1,'1',3);
		$('#resultados-selectCategoria').combobox('loadData',
				[{mode:0,text:'Large',selected:true},{mode:3,text:'Medium + Small'}]);
    	// Manga 2
		if (row.Manga2<=0) { // no hay segunda manga: oculta formulario
			$('#datos_manga2-InfoRow').css('display','none');
			$('#datos_manga2-LargeRow').css('display','none');
			$('#datos_manga2-MediumRow').css('display','none');
			$('#datos_manga2-SmallRow').css('display','none');
		} else {
			$('#datos_manga2-InfoRow').css('display','table-row');
			$('#datos_manga2-LargeRow').css('display','table-row');
			$('#datos_manga2-MediumRow').css('display','table-row');
			$('#datos_manga2-SmallRow').css('display','none');
			$('#datos_manga2-LargeLbl').html("Large");
			$('#datos_manga2-MediumLbl').html("Medium+Small");
			$('#datos_manga2-SmallLbl').html("&nbsp;");
			resultados_fillForm(resultados,row.Manga2,'2',0);
			resultados_fillForm(resultados,row.Manga2,'2',3);
		}
    	break;
    case 2: // Large+Medium+Small conjunta
    	// Manga 1
    	$('#datos_manga1-MediumRow').css('display','none');
    	$('#datos_manga1-SmallRow').css('display','none');
    	$('#datos_manga1-LargeLbl').html('Conjunta L+M+S');
    	$('#datos_manga1-MediumLbl').html("&nbsp;");
    	$('#datos_manga1-SmallLbl').html("&nbsp;");
		resultados_fillForm(resultados,row.Manga1,'1',4);
		$('#resultados-selectCategoria').combobox('loadData',
				[{mode:4,text:'Large + Medium + Small',selected:true}]);
    	// Manga 2
		if (row.Manga2<=0) {
			$('#datos_manga2-InfoRow').css('display','none');
			$('#datos_manga2-LargeRow').css('display','none');
			$('#datos_manga2-MediumRow').css('display','none');
			$('#datos_manga2-SmallRow').css('display','none');
		} else {
			$('#datos_manga2-InfoRow').css('display','table-row');
			$('#datos_manga2-LargeRow').css('display','table-row');
			$('#datos_manga2-MediumRow').css('display','none');
			$('#datos_manga2-SmallRow').css('display','none');
			$('#datos_manga2-LargeLbl').html('Conjunta L+M+S');
			$('#datos_manga2-MediumLbl').html("&nbsp;");
			$('#datos_manga2-SmallLbl').html("&nbsp;");
			resultados_fillForm(resultados,row.Manga2,'2',4);
		}
    	break;
    } 
    // FASE 2: cargamos informacion sobre resultados globales
    // tenemos la variable resultados, con los datos de cada Recorrido y cada manga '1' ['2'] de cada recorrido
    // lo que vamos a hacer es añadir una 'manga" extra 'C' con la suma de datos de las dos
    for (var mode=0;mode<5;mode++) {
    	if(typeof resultados[mode] === "undefined") continue;
    	$.each(resultados[mode]['1'],function(key,value) {
    		parseManga1(clasificaciones,mode,value);
    	});
    	if(typeof resultados[mode]['2'] === "undefined") continue; // no datos for manga 2
    	$.each(resultados[mode]['2'],function(key,value) {
    		parseManga2(clasificaciones,mode,value);
    	});
    }
    // Computamos los datos finales ahora que tenemos todo tabulado
    
    // y finalmente ordenaremos dicha manga 'Conjunta' alimentando al datagrid correspondiente
    // TODO: write
}

/**
 * Imprime una hoja con los podio de esta ronda
 */
function resultados_printPodium() {
	alert("competicion.js::resultados_printPodium() {PENDING}");
	// TODO: write
}

/**
 * Imprime los resultados finales separados por categoria y grado, tal y como pide la RSCE
 */
function resultados_printCanina() {
	alert("competicion.js::resultados_printCanina() {PENDING}");
	// TODO: write
}

/**
 * Imprime los resultados finales de la ronda seleccionada en formato CSV para su conversion en etiquetas
 * @returns {Boolean} false 
 */
function resultados_printEtiquetas() {
	$.fileDownload(
		'pdf/clasificaciones.php',
		{
			httpMethod: 'GET',
			data: { 
		        Prueba: workingData.prueba,
		        Jornada: workingData.jornada,
		        Manga: workingData.manga,
		        Manga2: workingData.manga2,
		        Operation: 3, // 0:manga1, 1:manga2, 2:final, 3:etiquetas
		        Categorias: $('#resultados-cat-form-select').val()
			},
	        preparingMessageHtml: "Generando ficheros de clasificaciones. Por favor, espere...",
	        failMessageHtml: "Ha habido problemas en la generacion del informe\n. Por favor, intentelo de nuevo."
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Imprime los resultados finales de la ronda seleccionada en formato pdf
 * @return false
 */
function resultados_printClasificacion() {
	// vemos cual es el panel activo
	var tab = $('#resultados-datatabs').tabs('getSelected');
	var index = $('#resultados-datatabs').tabs('getTabIndex',tab);
	$.fileDownload(
		'pdf/print_clasificaciones.php',
		{
			httpMethod: 'GET',
			data: { 
		        Prueba: workingData.prueba,
		        Jornada: workingData.jornada,
		        Manga: workingData.manga,
		        Manga2: workingData.manga2,
		        Operation: index, // 0:manga1, 1:manga2, 2:final, 3:etiquetas
		        Categorias: $('#resultados-cat-form-select').val()
			},
	        preparingMessageHtml: "Generando PDF con clasificaciones. Por favor, espere...",
	        failMessageHtml: "Ha habido problemas en la generacion del informe\n. Por favor, intentelo de nuevo."
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Presenta un menu al usuario indicando que es lo que se quiere imprimir
 */
function resultados_doPrint() {
	 $.messager.radio(
			 'Selecciona modelo',
			 'Selecciona el tipo de documento a generar:',
			 { 1:'Podium',2:'Etiquetas',3:'Informes R.S.C.E',4:'Clasificación'}, 
			 function(r){ 
				 switch(r) {
				 case 0: resultados_printPodio(); break;
				 case 1: resultados_printEtiquetas(); break;
				 case 2: resultados_printCanina(); break;
				 case 3: resultados_printClasificacion(); break;
				 }
			 }
		).window({width:250});
	    return false; //this is critical to stop the click event which will trigger a normal file download!
}

function reloadClasificaciones() {
	alert("competicion.js::reloadClasificaciones() {PENDING}");
	// TODO: write
	// check for valid manga combogrid selection, and invoke onSelect method
}
