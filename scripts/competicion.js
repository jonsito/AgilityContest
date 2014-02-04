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
		$('#dmanga_DistM').attr('disabled',true);
		$('#dmanga_DistM').val(distl);
		$('#dmanga_ObstM').attr('disabled',true);
		$('#dmanga_ObstM').val(obstl);
		$('#dmanga_DistS').attr('disabled',true);
		$('#dmanga_DistS').val(distl);
		$('#dmanga_ObstS').attr('disabled',true);
		$('#dmanga_ObstS').val(obstl);
		break;
	case '1': // un recorrido para std y otro para mini-midi
		var distm=$('#dmanga_DistM').val();
		var obstm=$('#dmanga_ObstM').val();
		$('#dmanga_DistM').removeAttr('disabled');
		$('#dmanga_ObstM').removeAttr('disabled');
		$('#dmanga_DistS').attr('disabled',true);
		$('#dmanga_DistS').val(distm);
		$('#dmanga_ObstS').attr('disabled',true);
		$('#dmanga_ObstS').val(obstm);
		// set TRS relative to Midi TRS
		$('#dmanga_TRS_S_Tipo').val(4); 
		$('#dmanga_TRS_S_Factor').val(0);
		break;
	case '2': // recorridos separados para cada categoria
		$('#dmanga_DistM').removeAttr('disabled');
		$('#dmanga_ObstM').removeAttr('disabled');
		$('#dmanga_DistS').removeAttr('disabled');
		$('#dmanga_ObstS').removeAttr('disabled');
		break;
	}
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
    $('#competicion-orden-datagrid').datagrid(
            'load',
            { 
            	Jornada: workingData.jornada , 
            	Manga: workingData.manga , 
            	Operacion: 'getData' 
            }
    );
}
/**
 * Guarda las modificaciones a los datos de una manga
 * Notese que esto no debería modificar ni los datos del
 * orden de salida ni resultados de la competicion
 * @param {Integer} id Identificador de la manga
 */
function save_manga(id) {
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

// genera un nuevo orden aleatorio
function randomOrdenSalida() {
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
	$.ajax({
		type:'GET',
		url:"database/ordenSalidaFunctions.php",
		dataType:'json',
		data: { 
			Jornada: workingData.jornada,
			Manga: workingData.manga,
			Operacion: 'random'
			}
		}).done( function(msg) {
			reloadOrdenSalida();
		});
}

function competicionDialog() {
	// obtenemos datos de la manga seleccionada
	var row= $('#competicion-listamangas').datagrid('getSelected');
    if (!row) return; // no hay ninguna manga seleccionada. retornar
    var title = workingData.nombrePrueba + ' -- ' + workingData.nombreJornada + ' -- ' + workingData.nombreManga;
    // abrimos ventana de dialogo
    $('#competicion-window').dialog('open').window('setTitle',title);
    // cargamos ventana de orden de salida
    reload_ordenSalida();
    // cargamos ventana de resultados
}