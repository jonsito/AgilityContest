/**
 * Funciones relacionadas con la ventana de competicion
 */

/************************** Gestion de datos de la ventana de manga activa */

/**
 * Actualiza el modo de visualizacion del panel de mangas
 * en funcion del tipo de recorrido seleccionado
 */
function dmanga_setRecorridos() {
	var val=$("input:radio[name=Recorrido]:checked").val();
	switch (val) {
	case '0': // recorrido comun para std, mini y midi
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
		break;
	case '2': // recorridos separados para cada categoria
		$('#dmanga_DistM').removeAttr('readonly');
		$('#dmanga_ObstM').removeAttr('readonly');
		$('#dmanga_DistS').removeAttr('readonly');
		$('#dmanga_ObstS').removeAttr('readonly');
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
		reloadResultadosManga();
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
            	Jornada: workingData.jornada , 
            	Manga: workingData.manga , 
            	Operation: 'enumerate' 
            }
    );
}

function reloadResultadosManga() {
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
    $('#resultadosmanga-datagrid').datagrid(
            'load',
            { 
            	Jornada: workingData.jornada , 
            	Manga: workingData.manga , 
            	Operation: 'parcial'
            }
    );
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
			Licencia:	data['Licencia'],
			Nombre:		data['Nombre'],
			Guia:		data['Guia'],
			Club:		data['Club'],
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
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
	$.ajax({
		type:'GET',
		url:"database/ordenSalidaFunctions.php",
		dataType:'json',
		data: { 
			Jornada: workingData.jornada,
			Manga: workingData.manga,
			Operation: mode
		}
	}).done( function(msg) {
		reloadOrdenSalida();
	});
}

// reajusta el orden de salida 
// poniendo el dorsal "from" delante (where==0) o detras (where==1) del dorsal "to"
function dragAndDrop(from,to,where) {
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
	$.ajax({
		type:'GET',
		url:"database/ordenSalidaFunctions.php?Operation=dnd",
		dataType:'json',
		data: { 
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
        reloadResultadosManga();
    }
}