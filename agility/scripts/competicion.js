/**
 * Funciones relacionadas con la ventana de competicion
 */

/************************** Gestion de datos de la ventana de manga activa */

/* formatters para el datagrid dlg_resultadosManga */

function formatPuesto(val,row,idx) { return (row.Penalizacion>=200)?"-":val; }
function formatVelocidad(val,row,idx) { return (row.Penalizacion>=100)?"-":parseFloat(val).toFixed(1); }
function formatTiempo(val,row,idx) { return (row.Penalizacion>=100)?"-":parseFloat(val).toFixed(2); }
function formatPenalizacion(val,row,idx) { return parseFloat(val).toFixed(2); }

/**
 * Actualiza el modo de visualizacion del panel de mangas
 * en funcion del tipo de recorrido seleccionado
 */
function dmanga_setRecorridos() {
	var val=$("input:radio[name=Recorrido]:checked").val();
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
 * Reload ventana de resultados
 * @param recorrido 0:L/M/S 1:L/M+S 2:/L+M+S
 */
function reloadResultadosManga(recorrido) {
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
	// recargamos el datagrid con los resultados pedidos
	// por defecto seleccionamos resultados de categoria "large"
    $('#resultadosmanga-datagrid').datagrid(
            'load',
            { 
            	Prueba: workingData.prueba,
            	Jornada: workingData.jornada , 
            	Manga: workingData.manga ,
            	Mode: (recorrido==2)?4:0,
            	Operation: 'getResultados'
            }
    );
    $('#resultadosmanga-LargeBtn').prop('checked',true);
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

/** actualiza el datagrid de resultados
 * @param mode 0:large/conjunto 1:medium/m+s 2:small
 */
function reloadParcial(val) {
	var mode=0;
	var recorrido=parseInt(workingData.datosManga.Recorrido);
	var value=parseInt(val); // stupid javascript!!
	switch (recorrido) {
	case 0: //  large / medium / small
		switch(value) {
		case 0: mode=0; break; 
		case 1: mode=1; break;
		case 2: mode=2; break;
		}
		break;
	case 1: // large / medium+small
		switch(value) {
		case 0: mode=0; break; 
		case 1: mode=3; break;
		case 2: mode=3; break; // invalido
		}
		break;
	case 2: // large+medium+small
		switch(value) {
		case 0: mode=4; break; 
		case 1: mode=4; break; // invalido
		case 2: mode=4; break; // invalido
		}
		break;
	}
	// reload resultados
	$('#resultadosmanga-datagrid').datagrid(
			'load',
			{
		        Prueba: workingData.prueba,
		        Jornada: workingData.jornada,
		        Manga: workingData.manga,
		        Mode: mode,
		        Operation: 'getResultados'
			}
	);
}

function reloadClasificacion() {
	$('#resultados-manga1-datagrid').datagrid('reload');
	$('#resultados-manga2-datagrid').datagrid('reload');
	$('#resultados-conjunta-datagrid').datagrid('reload');
}