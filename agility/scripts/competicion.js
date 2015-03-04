/*
competicion.js

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
function formatEliminado(val,row,idx) { return (row.Eliminado==0)?"":"Elim"; }
function formatNoPresentado(val,row,idx) { return (row.NoPresentado==0)?"":"N.P."; }

/* formaters para el frm_clasificaciones */
function formatPuestoFinal(val,row,idx) { return (row.Penalizacion>=200)?"-":row.Puesto; }
function formatPenalizacionFinal(val,row,idx) { return parseFloat(val).toFixed(2); }

function formatV1(val,row,idx) { return (row.P1>=200)?"-":parseFloat(val).toFixed(1); }
function formatT1(val,row,idx) { return (row.P1>=200)?"-":parseFloat(val).toFixed(2); }
function formatP1(val,row,idx) { return parseFloat(val).toFixed(2); }
function formatV2(val,row,idx) { return (row.P2>=200)?"-":parseFloat(val).toFixed(1); }
function formatT2(val,row,idx) { return (row.P2>=200)?"-":parseFloat(val).toFixed(2); }
function formatP2(val,row,idx) { return parseFloat(val).toFixed(2); }
function formatTF(val,row,idx) {
	var t=parseFloat(row.T1)+parseFloat(row.T2);
	return (row.Penalizacion>=200)?"-":t.toFixed(2); 
}
function formatRSCE(val,row,idx) {
	switch(parseInt(val)) {
	case 0: return "RSCE";
	case 1: return "RFEC";
	case 2: return "UCA";
	default: return val;
	}
}
function formatOk(val,row,idx) { return (parseInt(val)==0)?"":"&#x2714;"; }
function formatCerrada(val,row,idx) { return (parseInt(val)==0)?"":"&#x26D4;"; }

/* stylers para formateo de celdas especificas */
function formatBorder(val,row,idx) { return 'border-left: 1px solid #000;'; }

function checkPending(val,row,idx) { return ( parseInt(row.Pendiente)!=0 )? 'color: #f00;': ''; }

function formatCelo(val,row,idx) { return (parseInt(val)==0)?" ":"&#x2665;"; }

function getMode(rec,cat) {
	var recorrido=parseInt(rec);
	var categoria=parseInt(cat);
	if (workingData.datosPrueba.RSCE==0) { // RSCE
		switch(recorrido) {
		case 0: // recorrido separado
			if (categoria==0) return 0;
			if (categoria==1) return 1;
			if (categoria==2) return 2;
			break;
		case 1: // large / small+medium
			if (categoria==0) return 0;
			if (categoria==1) return 3;
			if (categoria==2) return 3;
			break;
		case 2: // recorrido conjunto
			if (categoria==0) return 4;
			if (categoria==1) return 4;
			if (categoria==2) return 4;
			break;
		}
	} else { // RFEC & UCA
		switch(recorrido) {
		case 0: // recorrido separado
			if (categoria==0) return 0;
			if (categoria==1) return 1;
			if (categoria==2) return 2;
			if (categoria==3) return 5;
			break;
		case 1: // large+medium / small+tiny
			if (categoria==0) return 6;
			if (categoria==1) return 6;
			if (categoria==2) return 7;
			if (categoria==3) return 7;
			break;
		case 2: // recorrido conjunto
			if (categoria==0) return 8;
			if (categoria==1) return 8;
			if (categoria==2) return 8;
			if (categoria==3) return 8;
			break;
		}
	}
	return -1; // combinacion invalida
}

/**
 * Actualiza el modo de visualizacion del panel infomangas
 * en funcion del tipo de recorrido seleccionado
 * para las pruebas de Canina
 */
function dmanga_setRecorridos_rsce() {
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
		
		// set TRS and TRM for midi relative to Standard TRS
		$('#dmanga_TRS_M_Tipo').val(3); 
		$('#dmanga_TRS_M_Factor').val(0);
		$('#dmanga_TRM_M_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_M_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for small relative to Standard TRS
		$('#dmanga_TRS_S_Tipo').val(3); 
		$('#dmanga_TRS_S_Factor').val(0);
		$('#dmanga_TRM_S_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_S_Factor').val($('#dmanga_TRM_L_Factor').val());
		
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
		
		// set Small TRS and TRM relative to Midi TRS
		$('#dmanga_TRS_S_Tipo').val(4); 
		$('#dmanga_TRS_S_Factor').val(0);
		$('#dmanga_TRM_S_Tipo').val($('#dmanga_TRM_M_Tipo').val()); 
		$('#dmanga_TRM_S_Factor').val($('#dmanga_TRM_M_Factor').val());
		
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
 * Actualiza el modo de visualizacion del panel infomangas
 * en funcion del tipo de recorrido seleccionado
 * para las pruebas de Caza
 */
function dmanga_setRecorridos_rfec() {
	var val=$("input[name='Recorrido']:checked").val();
	workingData.datosManga.Recorrido=val;
	switch (val) {
	case '2': // recorrido comun para std, med, min, y tiny
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
		$('#dmanga_DistT').attr('readonly',true);
		$('#dmanga_DistT').val(distl);
		$('#dmanga_ObstT').attr('readonly',true);
		$('#dmanga_ObstT').val(obstl);
		
		// set TRS and TRM for midi relative to Standard TRS
		$('#dmanga_TRS_M_Tipo').val(3); 
		$('#dmanga_TRS_M_Factor').val(0);
		$('#dmanga_TRM_M_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_M_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for small relative to Standard TRS
		$('#dmanga_TRS_S_Tipo').val(3); 
		$('#dmanga_TRS_S_Factor').val(0);
		$('#dmanga_TRM_S_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_S_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for tiny relative to Standard TRS
		$('#dmanga_TRS_T_Tipo').val(3); 
		$('#dmanga_TRS_T_Factor').val(0);
		$('#dmanga_TRM_T_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_T_Factor').val($('#dmanga_TRM_L_Factor').val());
		
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','none');
		$('#dmanga_SmallRow').css('display','none');
		$('#dmanga_TinyRow').css('display','none');
		$('#dmanga_LargeLbl').html("Com&uacute;n");
		$('#dmanga_MediumLbl').html("&nbsp;");
		$('#dmanga_SmallLbl').html("&nbsp;");
		$('#dmanga_TinyLbl').html("&nbsp;");
		break;
	case '1': // un recorrido para std/midi y otro para mini/tiny
		var distl=$('#dmanga_DistL').val();
		var obstl=$('#dmanga_ObstL').val();
		var dists=$('#dmanga_DistS').val();
		var obsts=$('#dmanga_ObstS').val();
		$('#dmanga_DistM').attr('readonly',true);
		$('#dmanga_DistM').val(distl);
		$('#dmanga_ObstM').attr('readonly',true);
		$('#dmanga_ObstM').val(obstl);
		$('#dmanga_DistS').removeAttr('readonly');
		$('#dmanga_DistT').attr('readonly',true);
		$('#dmanga_DistT').val(dists);
		$('#dmanga_ObstT').attr('readonly',true);
		$('#dmanga_ObstT').val(obsts);
		
		// set TRS and TRM for midi relative to Standard TRS
		$('#dmanga_TRS_M_Tipo').val(3); 
		$('#dmanga_TRS_M_Factor').val(0);
		$('#dmanga_TRM_M_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_M_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for tiny relative to small TRS
		$('#dmanga_TRS_T_Tipo').val(5); 
		$('#dmanga_TRS_T_Factor').val(0);
		$('#dmanga_TRM_T_Tipo').val($('#dmanga_TRM_S_Tipo').val()); 
		$('#dmanga_TRM_T_Factor').val($('#dmanga_TRM_S_Factor').val());
		
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','none');
		$('#dmanga_SmallRow').css('display','table-row');
		$('#dmanga_TinyRow').css('display','none');
		$('#dmanga_LargeLbl').html("Large+Medium");
		$('#dmanga_MediumLbl').html("&nbsp;");
		$('#dmanga_SmallLbl').html("Small+Tiny");
		$('#dmanga_TinyLbl').html("&nbsp;");
		break;
	case '0': // recorridos separados para cada categoria
		$('#dmanga_DistM').removeAttr('readonly');
		$('#dmanga_ObstM').removeAttr('readonly');
		$('#dmanga_DistS').removeAttr('readonly');
		$('#dmanga_ObstS').removeAttr('readonly');
		$('#dmanga_DistT').removeAttr('readonly');
		$('#dmanga_ObstT').removeAttr('readonly');
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','table-row');
		$('#dmanga_SmallRow').css('display','table-row');
		$('#dmanga_TinyRow').css('display','table-row');
		$('#dmanga_LargeLbl').html("Large");
		$('#dmanga_MediumLbl').html("Medium");
		$('#dmanga_SmallLbl').html("Small");
		$('#dmanga_TinyLbl').html("Tiny");
		break;
	}
}
/**
 * Actualiza el modo de visualizacion del panel infomangas
 * en funcion del tipo de recorrido seleccionado
 * para las pruebas de UCA
 */
function dmanga_setRecorridos_uca() {
	var val=$("input[name='Recorrido']:checked").val();
	workingData.datosManga.Recorrido=val;
	switch (val) {
	case '2': // recorrido comun para std, med, min, y tiny
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
		$('#dmanga_DistT').attr('readonly',true);
		$('#dmanga_DistT').val(distl);
		$('#dmanga_ObstT').attr('readonly',true);
		$('#dmanga_ObstT').val(obstl);
		
		// set TRS and TRM for midi relative to Standard TRS
		$('#dmanga_TRS_M_Tipo').val(3); 
		$('#dmanga_TRS_M_Factor').val(0);
		$('#dmanga_TRM_M_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_M_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for small relative to Standard TRS
		$('#dmanga_TRS_S_Tipo').val(3); 
		$('#dmanga_TRS_S_Factor').val(0);
		$('#dmanga_TRM_S_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_S_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for tiny relative to Standard TRS
		$('#dmanga_TRS_T_Tipo').val(3); 
		$('#dmanga_TRS_T_Factor').val(0);
		$('#dmanga_TRM_T_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_T_Factor').val($('#dmanga_TRM_L_Factor').val());
		
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','none');
		$('#dmanga_SmallRow').css('display','none');
		$('#dmanga_TinyRow').css('display','none');
		$('#dmanga_LargeLbl').html("Com&uacute;n");
		$('#dmanga_MediumLbl').html("&nbsp;");
		$('#dmanga_SmallLbl').html("&nbsp;");
		$('#dmanga_TinyLbl').html("&nbsp;");
		break;
	case '1': // un recorrido para std/midi y otro para mini/tiny
		var distl=$('#dmanga_DistL').val();
		var obstl=$('#dmanga_ObstL').val();
		var dists=$('#dmanga_DistS').val();
		var obsts=$('#dmanga_ObstS').val();
		$('#dmanga_DistM').attr('readonly',true);
		$('#dmanga_DistM').val(distl);
		$('#dmanga_ObstM').attr('readonly',true);
		$('#dmanga_ObstM').val(obstl);
		$('#dmanga_DistS').removeAttr('readonly');
		$('#dmanga_DistT').attr('readonly',true);
		$('#dmanga_DistT').val(dists);
		$('#dmanga_ObstT').attr('readonly',true);
		$('#dmanga_ObstT').val(obsts);
		
		// set TRS and TRM for midi relative to Standard TRS
		$('#dmanga_TRS_M_Tipo').val(3); 
		$('#dmanga_TRS_M_Factor').val(0);
		$('#dmanga_TRM_M_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_M_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for tiny relative to small TRS
		$('#dmanga_TRS_T_Tipo').val(5); 
		$('#dmanga_TRS_T_Factor').val(0);
		$('#dmanga_TRM_T_Tipo').val($('#dmanga_TRM_S_Tipo').val()); 
		$('#dmanga_TRM_T_Factor').val($('#dmanga_TRM_S_Factor').val());
		
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','none');
		$('#dmanga_SmallRow').css('display','table-row');
		$('#dmanga_TinyRow').css('display','none');
		$('#dmanga_LargeLbl').html("60+50");
		$('#dmanga_MediumLbl').html("&nbsp;");
		$('#dmanga_SmallLbl').html("40+30");
		$('#dmanga_TinyLbl').html("&nbsp;");
		break;
	case '0': // recorridos separados para cada categoria
		$('#dmanga_DistM').removeAttr('readonly');
		$('#dmanga_ObstM').removeAttr('readonly');
		$('#dmanga_DistS').removeAttr('readonly');
		$('#dmanga_ObstS').removeAttr('readonly');
		$('#dmanga_DistT').removeAttr('readonly');
		$('#dmanga_ObstT').removeAttr('readonly');
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','table-row');
		$('#dmanga_SmallRow').css('display','table-row');
		$('#dmanga_TinyRow').css('display','table-row');
		$('#dmanga_LargeLbl').html("60");
		$('#dmanga_MediumLbl').html("50");
		$('#dmanga_SmallLbl').html("40");
		$('#dmanga_TinyLbl').html("30");
		break;
	}
}

function dmanga_shareJuez() {
    $('#dmanga_Operation').val('sharejuez');
    $('#dmanga_Jornada').val(workingData.jornada);
    $('#dmanga_Manga').val(0); // not really needed, but...
    var frm = $('#competicion-formdatosmanga');
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/mangaFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
            } else {// on submit success, reload results
    			var recorrido=$("input:radio[name=Recorrido]:checked").val();
    			$.messager.alert('Data saved','Exportados los datos de jueces','info');
    			workingData.datosManga.Recorrido=recorrido;
    			setupResultadosWindow(recorrido);
            }
        }
    });
}

/**
 * Guarda las modificaciones a los datos de una manga
 * Notese que esto no debería modificar ni los datos del
 * orden de salida ni resultados de la competicion
 * @param {Integer} id Identificador de la manga
 */
function save_manga(id) {
    $('#dmanga_Operation').val('update');
    $('#dmanga_Jornada').val(workingData.jornada);
    $('#dmanga_Manga').val(id);
    var frm = $('#competicion-formdatosmanga');
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/mangaFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
            } else {// on submit success, reload results
    			var recorrido=$("input:radio[name=Recorrido]:checked").val();
    			$.messager.alert('Data saved','Datos de la manga almacenados','info');
    			workingData.datosManga.Recorrido=recorrido;
    			setupResultadosWindow(recorrido);
            }
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

function proximityAlert() {
	var data=$('#ordensalida-datagrid').datagrid('getRows');
	var idx=0;
	var guias= [];
	var lista="<br />";
	for (idx=0;idx<data.length;idx++) {
		var NombreGuia=data[idx].NombreGuia;
		// not yet declared: store perro and orden
		if ( !(NombreGuia in guias) ) {
			guias[NombreGuia] = { 'index': idx, 'perro': data[idx].Nombre }; 
			continue; 
		} 
		// already declared: eval distance
		dist=idx-guias[NombreGuia].index;
		if (dist>ac_config.proximity_alert) {
			// declared but more than 5 dogs ahead. reset index and continue
			guias[NombreGuia] = { 'index': idx, 'perro': data[idx].Nombre }; 
			continue;
		}
		// arriving here means that a dog is closer than 5 steps from previous one from same guia.
		// store to notify later
		lista = lista + NombreGuia+": " +
				guias[NombreGuia].index+":" + guias[NombreGuia].perro +
				" ---  " + 
				idx +":" + data[idx].Nombre + 
				"<br />";
	}
	// arriving here means work done
	if (lista==="<br />") {
		$.messager.alert('Correcto','No aparecen perros del mismo guia pro&oacute;imos','info');
	} else {
		var w=$.messager.alert('Alerta de proximidad','<p>Lista de gu&iacute;as con perros demasiado juntos:</p><p>'+lista+'</p>','warning');
		w.window('resize',{width:350}).window('center');
	}
}

function reloadAndCheck() {
	proximityAlert();
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
		autoUpdateID=setInterval(function(){reloadCompeticion();}, 10000);
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
    	var res=true;
    	if (selected){
        	var index = t.datagrid('getRowIndex', selected);
        	index = index + (up ? -1 : 1);
        	if (index < 0) { res=false; index = 0;}
        	if (index >= count) { res=false; index = count - 1; }
        	t.datagrid('clearSelections');
        	t.datagrid('selectRow', index);
    	} else {
        	t.datagrid('selectRow', (up ? count-1 : 0));
    	}
    	return res; // tell caller if index overflows
	}

	function editRow(t) {
		var selected = t.datagrid('getSelected');
		if(!selected) return;
		var index = t.datagrid('getRowIndex', selected);
        t.datagrid('beginEdit',index);
		var ed = $(t).datagrid('getEditor', {index:index,field:'Faltas'});
		// mark as selected contents on first field
		var input=$(ed.target).next().find('input');
		input.focus();
		input.select();
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
        	if (evt.ctrlKey) displayRowData(dg); else editRow(dg); 
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
        	var res=selectRow(dg,false); // move down one row
        	if (res) editRow(dg); // and edit except if we already was at bottom
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
 * @param val 0:L 1:M 2:S 3:T
 */
function printParcial(val) {
	var mode=0;
	var value=parseInt(val); // stupid javascript!!
	// obtenemos informacion sobre los datos a imprimir
	var mode=getMode(workingData.datosManga.Recorrido,value);
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

/** 
 * actualiza los datos de TRS y TRM de la fila especificada
 * Si se le indica, rellena tambien el datagrid re resultados parciales
 * @param {integer} mode 0:L 1:M 2:S 3:T
 * @param {boolean} fill true to fill resultados datagrid; else false
 */
function reloadParcial(val,fill) {
	var value=parseInt(val); // stupid javascript!!
	var mode=getMode(workingData.datosManga.Recorrido,value);
	if (mode==-1) {
		$.messager.alert('Error','Internal error: invalid RSCE/Recorrido/Categoria combination','error');
		return;
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
			case 0: case 4: case 6: case 8: suffix='L'; break;
			case 1: case 3: suffix='M'; break;
			case 2: case 7: suffix='S'; break;
			case 5: suffix='T'; break;
			}
			$('#rm_DIST_'+suffix).val(dat['trs'].dist);
			$('#rm_OBST_'+suffix).val(dat['trs'].obst);
			$('#rm_TRS_'+suffix).val(dat['trs'].trs);
			$('#rm_TRM_'+suffix).val(dat['trs'].trm);
			$('#rm_VEL_'+suffix).val(dat['trs'].vel);
			if (fill) $('#resultadosmanga-datagrid').datagrid('loadData',dat);
		}
	});
}

/**
 * Inicializa ventana de resultados ajustando textos
 * borra datagrid previa
 * @param recorrido 0:L/M/S/T 1:L/M+S(RSCE) LM/ST(RFEC)  2:/L+M+S+T
 */
function setupResultadosWindow(recorrido) {
	var rsce=(workingData.datosPrueba.RSCE==0)?true:false;
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
    $('#resultadosmanga-LargeBtn').prop('checked',false);
    $('#resultadosmanga-MediumBtn').prop('checked',false);
    $('#resultadosmanga-SmallBtn').prop('checked',false);
    $('#resultadosmanga-TinyBtn').prop('checked',false);
    $('#resultadosmanga-datagrid').datagrid('loadData',{total:0, rows:{}});
    // actualizamos la informacion del panel de informacion de trs/trm
    switch(parseInt(recorrido)){
    case 0: // Large / Medium / Small / Tiny separados
    	// ajustar visibilidad
    	$('#resultadosmanga-LargeRow').css('display','table-row');
    	$('#resultadosmanga-MediumRow').css('display','table-row');
    	$('#resultadosmanga-SmallRow').css('display','table-row');
    	$('#resultadosmanga-TinyRow').css('display',(rsce)?'none':'table-row');
    	// ajustar textos
    	$('#resultadosmanga-LargeLbl').html("Large");
    	$('#resultadosmanga-MediumLbl').html("Medium");
    	$('#resultadosmanga-SmallLbl').html("Small");
    	$('#resultadosmanga-TinyLbl').html("Tiny");
    	break;
    case 1: // RSCE: Large / Medium+Small --------- RFEC: Large+Medium / Tiny+Small
    	// ajustar visibilidad
    	$('#resultadosmanga-LargeRow').css('display','table-row');
    	$('#resultadosmanga-MediumRow').css('display',(rsce)?'table-row':'none');
    	$('#resultadosmanga-SmallRow').css('display',(rsce)?'none':'table-row');
    	$('#resultadosmanga-TinyRow').css('display','none');
    	// ajustar textos
    	$('#resultadosmanga-LargeLbl').html((rsce)?"Large":"Large+Medium");
    	$('#resultadosmanga-MediumLbl').html((rsce)?"Medium+Small":"&nbsp;");
    	$('#resultadosmanga-SmallLbl').html("Small+Tiny");
    	$('#resultadosmanga-TinyLbl').html("&nbsp;");
    	break;
    case 2: // Large+Medium+Small (+Tiny) conjunta
    	// ajustar visibilidad
    	$('#resultadosmanga-LargeRow').css('display','table-row');
    	$('#resultadosmanga-MediumRow').css('display','none');
    	$('#resultadosmanga-SmallRow').css('display','none');
    	$('#resultadosmanga-TinyRow').css('display','none');
    	// ajustar textos
    	$('#resultadosmanga-LargeLbl').html((rsce)?"Conjunta L+M+S":"Conjunta L+M+S+T");
    	$('#resultadosmanga-MediumLbl').html("&nbsp;");
    	$('#resultadosmanga-SmallLbl').html("&nbsp;");
    	$('#resultadosmanga-TinyLbl').html("&nbsp;");
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
	if (mode==='random') {
		$.messager.confirm('Confirmar', 'Se perderan todos los ajustes hechos a mano<br />Desea continuar?', function(r){
			if (!r) return;
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
		});
	}
	if (mode==='reverse') {
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
}

/**
 * manda a la impresora el orden de salida
 * @returns {Boolean}
 */
function printOrdenSalida() {
	$.fileDownload(
			'/agility/server/pdf/print_ordenDeSalida.php',
			{
				httpMethod: 'GET',
				data: { 
					Prueba: workingData.prueba,
					Jornada: workingData.jornada,
					Manga: workingData.manga
				},
		        preparingMessageHtml: "Imprimiendo orden de salida; por favor espere...",
		        failMessageHtml: "There was a problem generating your report, please try again."
			}
		);
	    return false; //this is critical to stop the click event which will trigger a normal file download!
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
    $('#ordentandas-dialog').dialog('close');
    $('#ordensalida-dialog').dialog('close');
    $('#competicion-dialog').dialog('close');
    $('#resultadosmanga-dialog').dialog('close');
    if (name==='ordentandas') {
        // abrimos ventana de dialogo
        $('#ordentandas-dialog').dialog('open').dialog('setTitle',"Jornada: "+title);
        // cargamos ventana de orden de salida
        reloadOrdenTandas();
    }
    title = workingData.nombrePrueba + ' -- ' + workingData.nombreJornada + ' -- ' + workingData.nombreManga;
    if (name==='ordensalida') {
        // abrimos ventana de dialogo
        $('#ordensalida-dialog').dialog('open').dialog('setTitle'," Orden de Salida: "+title);
        // cargamos ventana de orden de salida
        reloadOrdenSalida();
    }
    if (name==='competicion') {
        // abrimos ventana de dialogo
        $('#competicion-dialog').dialog('open').dialog('setTitle'," Entrada de datos: "+title);
        // cargamos ventana de entrada de datos
        reloadCompeticion();
    }
    if (name==='resultadosmanga') {
        // abrimos ventana de dialogo
        $('#resultadosmanga-dialog').dialog('open').dialog('setTitle'," Resultados de la manga: "+title);
        // iniciamls ventana de presentacion de resultados parciales acorde al tipo de prueba (RSCE/RFEC) y recorrido
        setupResultadosWindow(row.Recorrido);
        // marcamos la primera opcion como seleccionada
        $('#resultadosmanga-LargeBtn').prop('checked','checked');
        // refrescamos datos de TRS y TRM
        if (workingData.datosPrueba.RSCE!=0) reloadParcial(3,false);
        reloadParcial(2,false);
        reloadParcial(1,false);
        reloadParcial(0,true); // pintamos el datagrid con los datos de categoria "large"
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
			case 0: case 4: case 6: case 8: suffix='L'; break;
			case 1: case 3: suffix='M'; break;
			case 2: case 7: suffix='S'; break;
			case 5: suffix='T'; break;
			}
			$('#dm'+idxmanga+'_Nombre').val(dat['manga'].TipoManga);
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
	var rsce=(workingData.datosPrueba.RSCE==0)?true:false;
	// FASE 0 ajustamos los jueces de la ronda
	$('#dm1_Juez1').val(row.Juez11);
	$('#dm1_Juez2').val(row.Juez12);
	$('#dm2_Juez1').val(row.Juez21);
	$('#dm2_Juez2').val(row.Juez22);
    // FASE 1 Ajustamos en funcion del tipo de recorrido lo que debemos ver en las mangas
    // Recordatorio: ambas mangas tienen siempre el mismo tipo de recorrido
    switch(parseInt(row.Recorrido1)){
    case 0: // Large / Medium / Small / Tiny
    	// Manga 1
    	$('#datos_manga1-LargeRow').css('display','table-row');
    	$('#datos_manga1-MediumRow').css('display','table-row');
    	$('#datos_manga1-SmallRow').css('display','table-row');
		$('#datos_manga1-TinyRow').css('display',(rsce)?'none':'table-row');
    	$('#datos_manga1-LargeLbl').html("Large");
    	$('#datos_manga1-MediumLbl').html("Medium");
    	$('#datos_manga1-SmallLbl').html("Small");
    	$('#datos_manga1-TinyLbl').html("tiny");
		resultados_fillForm(resultados,row.Manga1,'1',0);
		resultados_fillForm(resultados,row.Manga1,'1',1);
		resultados_fillForm(resultados,row.Manga1,'1',2);
		if (!rsce) resultados_fillForm(resultados,row.Manga1,'1',5);
		// set up categoria combobox 
		if (rsce) {
			$('#resultados-selectCategoria').combobox('loadData',
					[{mode:0,text:'Large',selected:true},{mode:1,text:'Medium'},{mode:2,text:'Small'}]);
		} else {
			$('#resultados-selectCategoria').combobox('loadData',
					[{mode:0,text:'Large',selected:true},{mode:1,text:'Medium'},{mode:2,text:'Small'},{mode:5,text:'Tiny'}]);
		}
    	// Manga 2
		if (row.Manga2<=0) {
			// esta ronda solo tiene una manga. desactiva la segunda
			$('#datos_manga2-InfoRow').css('display','none');
			$('#datos_manga2-LargeRow').css('display','none');
			$('#datos_manga2-MediumRow').css('display','none');
			$('#datos_manga2-SmallRow').css('display','none');
			$('#datos_manga2-TinyRow').css('display','none');
		} else {
			$('#datos_manga2-InfoRow').css('display','table-row');
			$('#datos_manga2-LargeRow').css('display','table-row');
    		$('#datos_manga2-MediumRow').css('display','table-row');
    		$('#datos_manga2-SmallRow').css('display','table-row');
    		$('#datos_manga2-TinyRow').css('display',(rsce)?'none':'table-row');
    		$('#datos_manga2-LargeLbl').html("Large");
    		$('#datos_manga2-MediumLbl').html("Medium");
    		$('#datos_manga2-SmallLbl').html("Small");
    		$('#datos_manga2-TinylLbl').html("Tiny");
    		resultados_fillForm(resultados,row.Manga2,'2',0);
    		resultados_fillForm(resultados,row.Manga2,'2',1);
    		resultados_fillForm(resultados,row.Manga2,'2',2);
    		if (!rsce) resultados_fillForm(resultados,row.Manga2,'2',5);
		}
    	break;
    case 1: // Large / Medium+Small (RSCE ) ---- Large+Medium / Small+Tiny (RFEC)
    	// Manga 1
    	$('#datos_manga1-LargeRow').css('display','table-row');
    	$('#datos_manga1-MediumRow').css('display',(rsce)?'table-row':'none');
    	$('#datos_manga1-SmallRow').css('display',(rsce)?'none':'table-row');
    	$('#datos_manga1-TinyRow').css('display','none');
    	
    	$('#datos_manga1-LargeLbl').html((rsce)?"Large":"Large+Medium");
    	$('#datos_manga1-MediumLbl').html((rsce)?"Medium+Small":"&nbsp;");
    	$('#datos_manga1-SmallLbl').html((rsce)?"&nbsp;":"Small+Tiny");
    	$('#datos_manga1-TinyLbl').html("&nbsp;");
    	if (rsce) {
    		resultados_fillForm(resultados,row.Manga1,'1',0); // l
    		resultados_fillForm(resultados,row.Manga1,'1',3); // m+s
    		$('#resultados-selectCategoria').combobox('loadData',
    				[{mode:0,text:'Large',selected:true},{mode:3,text:'Medium + Small'}]);
    	} else {
    		resultados_fillForm(resultados,row.Manga1,'1',6); // l+m
    		resultados_fillForm(resultados,row.Manga1,'1',7); // s+t
    		$('#resultados-selectCategoria').combobox('loadData',
    				[{mode:6,text:'Large+Medium',selected:true},{mode:7,text:'Small+Tiny'}]);
    	}
    	// Manga 2
		if (row.Manga2<=0) { // no hay segunda manga: oculta formulario
			$('#datos_manga2-InfoRow').css('display','none');
			$('#datos_manga2-LargeRow').css('display','none');
			$('#datos_manga2-MediumRow').css('display','none');
			$('#datos_manga2-SmallRow').css('display','none');
			$('#datos_manga2-TinyRow').css('display','none');
		} else {
	    	$('#datos_manga2-LargeRow').css('display','table-row');
	    	$('#datos_manga2-MediumRow').css('display',(rsce)?'table-row':'none');
	    	$('#datos_manga2-SmallRow').css('display',(rsce)?'none':'table-row');
	    	$('#datos_manga2-TinyRow').css('display','none');
	    	
	    	$('#datos_manga2-LargeLbl').html((rsce)?"Large":"Large+Medium");
	    	$('#datos_manga2-MediumLbl').html((rsce)?"Medium+Small":"&nbsp;");
	    	$('#datos_manga2-SmallLbl').html((rsce)?"&nbsp;":"Small+Tiny");
	    	$('#datos_manga2-TinyLbl').html("&nbsp;");
			if (rsce) {
				resultados_fillForm(resultados,row.Manga2,'2',0);
				resultados_fillForm(resultados,row.Manga2,'2',3);
			} else {
				resultados_fillForm(resultados,row.Manga2,'2',6);
				resultados_fillForm(resultados,row.Manga2,'2',7);
			}
		}
    	break;
    case 2: // Large+Medium+Small+tiny conjunta
    	// Manga 1
    	$('#datos_manga1-LargeRow').css('display','table-row');
    	$('#datos_manga1-MediumRow').css('display','none');
    	$('#datos_manga1-SmallRow').css('display','none');
    	$('#datos_manga1-TinyRow').css('display','none');
    	
    	$('#datos_manga1-LargeLbl').html((rsce)?'Conjunta L+M+S':'Conjunta L+M+S+T');
    	$('#datos_manga1-MediumLbl').html("&nbsp;");
    	$('#datos_manga1-SmallLbl').html("&nbsp;");
    	$('#datos_manga1-TinyLbl').html("&nbsp;");
    	if (rsce) {
    		resultados_fillForm(resultados,row.Manga1,'1',4);
    		$('#resultados-selectCategoria').combobox('loadData',
    				[{mode:4,text:'Conjunta L+M+S',selected:true}]);
    	} else {
    		resultados_fillForm(resultados,row.Manga1,'1',8);
    		$('#resultados-selectCategoria').combobox('loadData',
    				[{mode:8,text:'Conjunta L+M+S+T',selected:true}]);
    	}
    	// Manga 2
		if (row.Manga2<=0) {
			$('#datos_manga2-InfoRow').css('display','none');
			$('#datos_manga2-LargeRow').css('display','none');
			$('#datos_manga2-MediumRow').css('display','none');
			$('#datos_manga2-SmallRow').css('display','none');
			$('#datos_manga2-TinyRow').css('display','none');
		} else {
	    	$('#datos_manga2-LargeRow').css('display','table-row');
	    	$('#datos_manga2-MediumRow').css('display','none');
	    	$('#datos_manga2-SmallRow').css('display','none');
	    	$('#datos_manga2-TinyRow').css('display','none');
	    	
	    	$('#datos_manga2-LargeLbl').html((rsce)?'Conjunta L+M+S':'Conjunta L+M+S+T');
	    	$('#datos_manga2-MediumLbl').html("&nbsp;");
	    	$('#datos_manga2-SmallLbl').html("&nbsp;");
	    	$('#datos_manga2-TinyLbl').html("&nbsp;");
			if (rsce) resultados_fillForm(resultados,row.Manga2,'2',4);
			else resultados_fillForm(resultados,row.Manga2,'2',8);
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
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='/agility/server/pdf/print_podium.php';
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
				Rondas: ronda.Rondas
			},
	        preparingMessageHtml: "Generando PDF con los podios. Por favor, espere...",
	        failMessageHtml: "Ha habido problemas en la generacion del formulario\n. Por favor, intentelo de nuevo."
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Imprime los resultados finales separados por categoria y grado, tal y como pide la RSCE
 */
function resultados_printCanina() {
	// Client-side excel conversion
	// $('#resultados-datagrid').datagrid('toExcel',"clasificaciones.xls");
	
	// Server-side excel generation
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='/agility/server/pdf/print_clasificacion_excel.php';
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
	        preparingMessageHtml: "Generando fichero Excel con las clasificaciones. Por favor, espere...",
	        failMessageHtml: "Ha habido problemas en la generacion del fichero\n. Por favor, intentelo de nuevo."
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Imprime la secuencia de tandas de la jornada
 */
function printOrdenTandas() {
	$.fileDownload(
			'/agility/server/pdf/print_ordenTandas.php',
			{
				httpMethod: 'GET',
				data: { 
					Prueba: workingData.prueba,
					Jornada: workingData.jornada
				},
		        preparingMessageHtml: "We are preparing your report, please wait...",
		        failMessageHtml: "There was a problem generating your report, please try again."
			}
		);
	    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Imprime los resultados finales de la ronda seleccionada en formato CSV para su conversion en etiquetas
 * @param {integer} mode 0:CSV 1:PDF
 * @param {integer} start if mode==PDF first line in output
 * @param {string} list CSV dorsal list
 * @returns {Boolean} false 
 */
function resultados_printEtiquetas(flag,start,list) {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='/agility/server/pdf/print_etiquetas_csv.php';
	if (flag!=0) url='/agility/server/pdf/print_etiquetas_pdf.php';
	var mode=$('#resultados-selectCategoria').combobox('getValue');
	var strt=parseInt(start)-1;
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
				Mode: mode,
				Start: strt,
				List: list
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
 * Ajusta el menu de seleccion de metodo de impresion en funcion de la opcion seleccionada
 */
function r_selectOption(val) {
	switch (parseInt(val)) {
	case 0:
	case 1:
	case 3:
	case 4: $('#r_prfirst').numberspinner('disable'); $('#r_prlist').numberspinner('disable'); break;
	case 2: $('#r_prfirst').numberspinner('enable'); $('#r_prlist').numberspinner('disable'); break;
	case 5: $('#r_prfirst').numberspinner('enable'); $('#r_prlist').numberspinner('enable'); break;
	}
}

/**
 * Presenta un menu al usuario indicando que es lo que se quiere imprimir
 */
function resultados_doPrint() {
	var r=$('input:radio[name="r_prformat"]:checked').val();
	var line=$('#r_prfirst').numberspinner('getValue');
	var list=$('#r_prlist').textbox('getValue');
	$('#resultados-printDialog').dialog('close');
	switch(parseInt(r)) {
		case 0: resultados_printPodium(); break;
		case 1: resultados_printEtiquetas(0); break; // csv
		case 3: resultados_printCanina(); break;
		case 4: resultados_printClasificacion(); break; 
		case 5: resultados_printEtiquetas(1,line,list); break;
		case 2: resultados_printEtiquetas(1,line,''); break;
	}
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
