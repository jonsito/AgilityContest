/*
competicion.js

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

/**
 * Funciones relacionadas con la ventana de competicion
 */

/************************** Gestion de datos de la ventana de manga activa */

/* formatters para el datagrid dlg_resultadosManga */

function formatPuesto(val,row,idx) { return (row.Penalizacion>=200)?"-":val; }
function formatVelocidad(val,row,idx) { return (row.Penalizacion>=200)?"-":parseFloat(val).toFixed(1); }
function formatTiempo(val,row,idx) { return (row.Penalizacion>=200)?"-":parseFloat(val).toFixed(2); }
function formatPenalizacion(val,row,idx) { return parseFloat(val).toFixed(2); }

/* formaters para el frm_clasificaciones */
function formatPuestoFinal(val,row,idx) { return (row.Penalizacion>=200)?"-":row.Puesto; }
function formatPenalizacionFinal(val,row,idx) { return parseFloat(val).toFixed(2); }

function formatV1(val,row,idx) { return (row.P1>=200)?"-":parseFloat(val).toFixed(1); }
function formatT1(val,row,idx) { return (row.P1>=200)?"-":parseFloat(val).toFixed(2); }
function formatP1(val,row,idx) { return parseFloat(val).toFixed(2); }
function formatV2(val,row,idx) { return (row.P2>=200)?"-":parseFloat(val).toFixed(1); }
function formatT2(val,row,idx) { return (row.P2>=200)?"-":parseFloat(val).toFixed(2); }
function formatP2(val,row,idx) { return parseFloat(val).toFixed(2); }

/* stylers para formateo de celdas especificas */
function formatBorder(val,row,idx) { return 'border-left: 1px solid #000;'; }

function checkPending(val,row,idx) { 
	return ( parseInt(row.Pendiente)!=0 )? 'color: #f00;': '';
}

function checkCelo(val,row) {
	return (parseInt(val)==0)?" ":"X";
}
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
		url: '/agility/server/database/mangaFunctions.php',
		onSubmit: function(param) {
			param.Operation='update';
			param.Jornada=workingData.jornada;
			param.Manga=id;
			return true; // to continue submitting
		},
		success: function(data) {
			$.messager.alert('Data saved','Datos de la manga almacenados','info');
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
	var url='server/database/mangaFunctions.php?Operation=getbyid&Jornada='+workingData.jornada+"&Manga="+id;
    $('#competicion-formdatosmanga').form('load',url);
}

function reloadOrdenTandas() {
	if (workingData.prueba==0) return;
	if (workingData.jornada==0) return;
    $('#ordentandas-datagrid').datagrid(
            'load',
            { 
            	Prueba: workingData.prueba,
            	Jornada: workingData.jornada ,
            	Operation: 'getTandas'
            }
    );
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
	// si hay alguna celda en edicion, ignorar
	if ($('#competicion-datagrid').datagrid('options').editIndex!=-1) return;
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

var autoUpdateID=null;

function autoUpdateCompeticion() {
	var enabled=$('#competicion-autoUpdateBtn').prop('checked');
	if (enabled) {
		if (autoUpdateID!==null) return; // already activated
		autoUpdateID=setInterval(function(){reloadCompeticion()}, 10000);
	} else {
		if (autoUpdateID==null) return; // already deactivated
		clearInterval(autoUpdateID);
		autoUpdateID=null;
	}
}

/**
 * Key bindings para uso de teclas en el dialogo de entrada de datos
 * @param evt Key Event
 */

function competicionKeyEventHandler(evt) {

	// up & down keys
    function selectRow(t,up){
    	var count = t.datagrid('getRows').length;    // row count
    	var selected = t.datagrid('getSelected');
    	if (selected){
        	var index = t.datagrid('getRowIndex', selected);
        	index = index + (up ? -1 : 1);
        	if (index < 0) index = 0;
        	if (index >= count) index = count - 1;
        	t.datagrid('clearSelections');
        	t.datagrid('selectRow', index);
    	} else {
        	t.datagrid('selectRow', (up ? count-1 : 0));
    	}
	}

	function editRow(t) {
		var selected = t.datagrid('getSelected');
		if(!selected) return;
		var index = t.datagrid('getRowIndex', selected);
        t.datagrid('beginEdit',index);
		var ed = $(t).datagrid('getEditor', {index:index,field:'Faltas'});
		$(ed.target).next().find('input').focus();
	}
	
	var dg=$('#competicion-datagrid');
	var editIndex=dg.datagrid('options').editIndex; // added by me
	if (editIndex==-1) { // not editing
		switch (evt.keyCode) {
        case 38:	/* Up */	 
            selectRow(dg,true); 
            return false;
        case 40:    /* Down */	 
            selectRow(dg,false); 
            return false;
        case 13:	/* Enter */  
            editRow(dg); 
            return false;
        case 27:	/* Esc */
            // disable autorefresh if any
            $('#competicion-autoUpdateBtn').prop('checked',false);
            autoUpdateCompeticion(); // fire event 
            // and close window  	 
            $('#competicion-window').window('close'); 
            return false;
        default: 
        	return false;
		}
	} else { //on edit
		switch (evt.keyCode) {
        case 13:	/* Enter */
        	// save data
        	dg.datagrid('endEdit', editIndex );
        	var data=dg.datagrid('getRows')[editIndex];
        	data.Pendiente=0;
        	saveCompeticionData(editIndex,data);
        	// and open edition on next row
        	dg.datagrid('selectRow', editIndex); // previous focus is lost
        	selectRow(dg,false); // move down one row
        	editRow(dg);
        	return false;
        case 27:	/* Esc */ 
            dg.datagrid('cancelEdit', editIndex);	
            return false;
		}
	}
	return true; // to allow follow key binding chain
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
		'/agility/server/pdf/print_resultadosByManga.php',
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
		url:"/agility/server/database/resultadosFunctions.php",
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
		url:"/agility/server/database/resultadosFunctions.php",
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
			Observaciones:	data['Observaciones'],
			Pendiente: data['Pendiente']
		},
		success: function(dat) {
			if (dat.Manga!=workingData.manga) return; // window changed
			$('#competicion-datagrid').datagrid('updateRow',{index: idx,row: dat});
			$('#lnkb1_'+idx).linkbutton();
			$('#lnkb2_'+idx).linkbutton();
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
		url:"/agility/server/database/ordenSalidaFunctions.php",
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
// al retornar la funcion se invoca whenDone, que normalmente recargara el formulario padre
function dragAndDropOrdenSalida(from,to,where,whenDone) {
	if (workingData.prueba==0) return;
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
	$.ajax({
		type:'GET',
		url:"/agility/server/database/ordenSalidaFunctions.php",
		dataType:'json',
		data: {	
			Operation: 'dnd', Prueba: workingData.prueba, Jornada: workingData.jornada,	Manga: workingData.manga, From: from,To: to,Where: where
		}
	}).done( function(msg) {
		whenDone();
	});
}
//reajusta el programa de la jornada
//poniendo la tanda "from" delante (where==0) o detras (where==1) de la tanda "to"
function dragAndDropOrdenTandas(from,to,where) {
	if (workingData.prueba==0) return;
	if (workingData.jornada==0) return;
	$.ajax({
		type:'GET',
		url:"/agility/server/database/ordenTandasFunctions.php",
		dataType:'json',
		data: {	
			Operation: 'dnd', 
			Prueba: workingData.prueba, 
			Jornada: workingData.jornada, 
			From: from,
			To: to,
			Where: where
		}
	}).done( function(msg) {
		reloadOrdenTandas();
	});
}
/**
 * Abre la ventana de competicion requerida 'ordensalida','competicion','resultadosmanga'
 * @param name
 */
function competicionDialog(name) {
	// obtenemos datos de la manga seleccionada
	var row= $('#competicion-listamangas').datagrid('getSelected');
    if (!row && name!== 'ordentandas') {
    	$.messager.alert('Error','No hay ninguna manga seleccionada','info');
    	return; // no hay ninguna manga seleccionada. retornar
    }
    var title = workingData.nombrePrueba + ' -- ' + workingData.nombreJornada;
    $('#ordentandas-window').window('close');
    $('#ordensalida-window').window('close');
    $('#competicion-window').window('close');
    $('#resultadosmanga-window').window('close');
    if (name==='ordentandas') {
        // abrimos ventana de dialogo
        $('#ordentandas-window').window('open').window('setTitle',"Jornada: "+title);
        // cargamos ventana de orden de salida
        reloadOrdenTandas();
    }
    title = workingData.nombrePrueba + ' -- ' + workingData.nombreJornada + ' -- ' + workingData.nombreManga;
    if (name==='ordensalida') {
        // abrimos ventana de dialogo
        $('#ordensalida-window').window('open').window('setTitle'," Orden de Salida: "+title);
        // cargamos ventana de orden de salida
        reloadOrdenSalida();
    }
    if (name==='competicion') {
        // abrimos ventana de dialogo
        $('#competicion-window').window('open').window('setTitle'," Entrada de datos: "+title);
        // cargamos ventana de entrada de datos
        reloadCompeticion();
    }
    if (name==='resultadosmanga') {
        // abrimos ventana de dialogo
        $('#resultadosmanga-window').window('open').window('setTitle'," Resultados de la manga: "+title);
        // cargamos ventana de presentacion de resultados parciales
        reloadResultadosManga(row.Recorrido);
        // marcamos la primera opcion como seleccionada
        $('#resultadosmanga-LargeBtn').prop('checked','checked');
        // y recargarmos resultados parciales
        reloadParcial(0);
    }
}

/************************************* funciones para la ventana de clasificaciones **************************/

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
		url:"/agility/server/database/resultadosFunctions.php",
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
    // FASE 2: cargamos informacion sobre resultados globales y la volcamos en el datagrid
    mode=$('#resultados-selectCategoria').combobox('getValue');
	$.ajax({
		type:'GET',
		url:"/agility/server/database/clasificacionesFunctions.php",
		dataType:'json',
		data: {	
			Prueba:workingData.prueba,
			Jornada:workingData.jornada,
			Manga1:row.Manga1,
			Manga2:row.Manga2,
			Rondas: row.Rondas,
			Mode: mode
		},
		success: function(dat) {
			$('#resultados_thead_m1').text(row.NombreManga1);
			$('#resultados_thead_m2').text(row.NombreManga2);
			$('#resultados-datagrid').datagrid('loadData',dat);
		}
	});
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
 * Imprime la secuencia de tandas de la jornada
 */
function competicion_printTandas() {
	alert("competicion.js::competicion_printTandas() {PENDING}");
	// TODO: write
}

/**
 * Imprime los resultados finales de la ronda seleccionada en formato CSV para su conversion en etiquetas
 * @param {integer} mode 0:CSV 1:PDF
 * @returns {Boolean} false 
 */
function resultados_printEtiquetas(flag) {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='/agility/server/pdf/print_etiquetas_csv.php';
	if (flag!=0) url='/agility/server/pdf/print_etiquetas_pdf.php';
	var mode=$('#resultados-selectCategoria').combobox('getValue');
	if (ronda==null) {
    	$.messager.alert("Error:","!No ha seleccionado ninguna ronda de esta jornada!","warning");
    	return false; // no way to know which ronda is selected
	}
	$.fileDownload(
		url,
		{
			httpMethod: 'GET',
			data: { 
				Prueba:workingData.prueba,
				Jornada:workingData.jornada,
				Manga1:ronda.Manga1,
				Manga2:ronda.Manga2,
				Rondas: ronda.Rondas,
				Mode: mode
			},
	        preparingMessageHtml: "Generando formulario con las etiquetas. Por favor, espere...",
	        failMessageHtml: "Ha habido problemas en la generacion del formulario\n. Por favor, intentelo de nuevo."
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Imprime los resultados finales de la ronda seleccionada en formato pdf
 * @return false
 */
function resultados_printClasificacion() {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='/agility/server/pdf/print_clasificacion.php';
	var mode=$('#resultados-selectCategoria').combobox('getValue');
	if (ronda==null) {
    	$.messager.alert("Error:","!No ha seleccionado ninguna ronda de esta jornada!","warning");
    	return false; // no way to know which ronda is selected
	}
	$.fileDownload(
		url,
		{
			httpMethod: 'GET',
			data: { 
				Prueba:workingData.prueba,
				Jornada:workingData.jornada,
				Manga1:ronda.Manga1,
				Manga2:ronda.Manga2,
				Rondas: ronda.Rondas,
				Mode: mode
			},
	        preparingMessageHtml: "Generando PDF con las clasificaciones. Por favor, espere...",
	        failMessageHtml: "Ha habido problemas en la generacion del formulario\n. Por favor, intentelo de nuevo."
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
			 { 0:'Podium',1:'Etiquetas (CSV)',2:'Etiquetas (PDF)',3:'Informes R.S.C.E',4:'Clasificación'}, 
			 function(r){ 
				 switch(parseInt(r)) {
				 case 0: resultados_printPodio(); break;
				 case 1: resultados_printEtiquetas(0); break;
				 case 2: resultados_printEtiquetas(1); break;
				 case 3: resultados_printCanina(); break;
				 case 4: resultados_printClasificacion(); break;
				 }
			 }
		).window({width:250});
	    return false; //this is critical to stop the click event which will trigger a normal file download!
}

function reloadClasificaciones() {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	if (ronda==null) {
    	$.messager.alert("Error:","!No ha seleccionado ninguna ronda de esta jornada!","warning");
    	return; // no way to know which ronda is selected
	}
	// obtenemos el modo activo
	var mode=$('#resultados-selectCategoria').combobox('getValue');
	// calculamos y recargamos tabla de clasificaciones
	$.ajax({
		type:'GET',
		url:"/agility/server/database/clasificacionesFunctions.php",
		dataType:'json',
		data: {	
			Prueba:workingData.prueba,
			Jornada:workingData.jornada,
			Manga1:ronda.Manga1,
			Manga2:ronda.Manga2,
			Rondas: ronda.Rondas,
			Mode: mode
		},
		success: function(dat) {
			$('#resultados_thead_m1').text(ronda.NombreManga1);
			$('#resultados_thead_m2').text(ronda.NombreManga2);
			$('#resultados-datagrid').datagrid('loadData',dat);
		}
	});
}
