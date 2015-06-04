/*
tablet.js

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

function tandasStyler(val,row,idx) {
	var str="text-align:left; ";
	str += "font-weight:bold; ";
	str += ((idx&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
	return str;
}

/******************* funciones de manejo de las ventana de orden de tandas y orden de salida en el tablet *******************/

function tablet_showOrdenSalida() {
	$('#tablet-window').window('open');
    $('#tdialog-window').window('close');
}

/******************* funciones de manejo de la ventana de entrada de resultados del tablet *****************/

/**
 * send events
 * @param {string} type Event Type
 * @param {object} data Event data
 */
function tablet_putEvent(type,data){
    var tds=$('#tdialog-Session').val();
	// setup default elements for this event
	var obj= {
			'Operation':'putEvent',
			'Type': 	type,
			'TimeStamp': Date.now() - startDate,
			'Source':	'tablet_'+tds,
			'Session':	tds,
			'Prueba':	$('#tdialog-Prueba').val(),
			'Jornada':	$('#tdialog-Jornada').val(),
			'Manga':	$('#tdialog-Manga').val(),
			'Tanda':	$('#tdialog-ID').val(),
			'Perro':	$('#tdialog-Perro').val(),
            'Dorsal':	$('#tdialog-Dorsal').val(),
            'Equipo':	$('#tdialog-Equipo').val(),
			'Celo':		$('#tdialog-Celo').val()	
	};
	// send "update" event to every session listeners
	$.ajax({
		type:'GET',
		url:"/agility/server/database/eventFunctions.php",
		dataType:'json',
		data: $.extend({},obj,data)
	});
}

function tablet_updateSession(row) {
	// update sesion info in database
	var data = {
			Operation: 'update',
			ID: workingData.sesion,
			Prueba: row.Prueba,
			Jornada: row.Jornada,
			Manga: row.Manga,
			Tanda: row.ID
	};
    if (parseInt(row.Manga)==0) {
        var str= strval(row.Nombre).toLowerCase();
        if ( strpos(str,"econo")>0 ) return false;
        else tablet_reconocimiento();
    }
	$.ajax({
		type:	'GET',
		url:	"/agility/server/database/sessionFunctions.php",
		// dataType:'json',
		data:	data,
		success: function() {
            data.Session=	data.ID;
            data.Operation=	'putEvent';
            // send proper event
            if (parseInt(row.Manga)==0) { //user defined tanda
                data.Nombre= row.Nombre;
                tablet_putEvent('info',data);
            } else {
                tablet_putEvent('open',data);
            }
		}
	});
}

function tablet_updateResultados(pendiente) {
	$('#tdialog-Pendiente').val(pendiente);
    var frm = $('#tdialog-form');
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/resultadosFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({ width:300, height:200, title: 'Error', msg: result.errorMsg });
            } 
        	// NOTE: do not update parent tablet row on success 
        	// as form('reset') seems not to work as we want, we use it as backup
        }
    });
}

function doBeep() {
	if (ac_config.tablet_beep)	setTimeout(function() {beep();},0);
}

function tablet_add(val) {
	doBeep();
    var tdt=$('#tdialog-Tiempo');
	var str=tdt.val();
	if (parseInt(str)==0) str=''; // clear espurious zeroes
	if(str.length>=6) return; // sss.xx 6 chars
	var n=str.indexOf('.');
	if (n>=0) {
		var len=str.substring(n).length;
		if (len>2) return; // only two decimal digits
	}
	tdt.val(''+str+val);
	tablet_updateResultados(1);
	// dont send event
	return false;
}

function tablet_dot() {
	doBeep();
	var str=$('#tdialog-Tiempo').val();
	if (str.indexOf('.')>=0) return;
	tablet_add('.');
	tablet_updateResultados(1);
	// dont send  event
	return false;
}

function tablet_del() {
	doBeep();
    var tdt=$('#tdialog-Tiempo');
	var str=tdt.val();
	if (str==='') return;
	tdt.val(str.substring(0, str.length-1));
	tablet_updateResultados(1);
	// dont send event
	return false;
}

function tablet_up(id){
	doBeep();
	var n= 1+parseInt($(id).val());
	var lbl = replaceAll('#tdialog-','',id);
	var datos = {};
	$(id).val(''+n);
	tablet_updateResultados(1);
	datos[lbl]=$(id).val();
	tablet_putEvent( 'datos', datos);
	return false;
}

function tablet_down(id){
	doBeep();
	var n= parseInt($(id).val());
	var m = (n<=0) ? 0 : n-1;
	var lbl = replaceAll('#tdialog-','',id);
	var datos = {};
	$(id).val(''+m);
	tablet_updateResultados(1);
	datos[lbl]=$(id).val();
	tablet_putEvent( 'datos', datos );
	return false;
}

function tablet_np() {
	doBeep();
    var tde=$('#tdialog-Eliminado');
    var tdestr=$('#tdialog-EliminadoStr');
    var tdnp=$('#tdialog-NoPresentado');
    var tdnpstr=$('#tdialog-NoPresentadoStr');
    var tdtime=$('#tdialog-Tiempo');
    var tdflt=$('#tdialog-Faltas');
    var tdtoc=$('#tdialog-Rehuses');
    var tdreh=$('#tdialog-Tocados');
	var n= parseInt(tdnp.val());
	if (n==0) {
		tdnp.val(1);
		tdnpstr.val("NP");
		// si no presentado borra todos los demas datos
		tde.val(0);
		tdestr.val("");
		tdflt.val(0);
		tdreh.val(0);
		tdtoc.val(0);
		tdtime.val(0);
	} else {
		tdnp.val(0);
		tdnpstr.val("");
	}
	tablet_updateResultados(1);
	tablet_putEvent(
		'datos',
		{
		'NoPresentado'	:	tdnp.val(),
		'Faltas'		:	tdflt.val(),
		'Tocados'		:	tdtoc.val(),
		'Rehuses'		:	tdreh.val(),
		'Tiempo'		:	tdtime.val(),
		'Eliminado'		:	tde.val()
		}
		);
	return false;
}

function tablet_elim() {
	doBeep();
    var tde=$('#tdialog-Eliminado');
    var tdestr=$('#tdialog-EliminadoStr');
    var tdnp=$('#tdialog-NoPresentado');
    var tdtime=$('#tdialog-Tiempo');
	var n= parseInt(tde.val());
	if (n==0) {
		tde.val(1);
		tdestr.val("EL");
		// si eliminado, poner nopresentado y tiempo a cero, conservar todo lo demas
		tdnp.val(0);
		$('#tdialog-NoPresentadoStr').val("");
		tdtime.val(0);
	} else {
		tde.val(0);
		tdestr.val("");
	}
	tablet_updateResultados(1);
	tablet_putEvent(
			'datos',
			{
			'NoPresentado'	:	tdnp.val(),
			'Tiempo'		:	tdtime.val(),
			'Eliminado'		:	tde.val()
			}
		);
	return false;
}

function tablet_cronoManual(oper,time) {
	if (ac_config.tablet_chrono) $('#cronomanual').Chrono(oper,time);
}

function tablet_restartCronoManual(time) {
    if (!ac_config.tablet_chrono) return false;
    var crm=$('#cronomanual');
    crm.Chrono('stop',time);
    crm.Chrono('reset',time);
    crm.Chrono('start',time);
}

var myCounter = new Countdown({  
    seconds:15,  // number of seconds to count down
    onUpdateStatus: function(sec){ $('#tdialog-Tiempo').val(sec); }, // callback for each second
    // onCounterEnd: function(){  $('#tdialog_Tiempo').html('<span class="blink" style="color:red">-out-</span>'); } // final action
    onCounterEnd: function(){  // at end of countdown start timer
    	var time = Date.now() - startDate;
    	switch (parseInt(ac_config.tablet_countdown)) {
    		case 1: /* do nothing */ return;
    		case 2: /* start crono */
    			tablet_putEvent('start',{ 'Value' : time } );
    			$('#tdialog-StartStopBtn').val("Stop");
    			tablet_cronoManual('start',time);
    			break;
    		case 3: /* eliminado */
    			$('#tdialog-Eliminado').val(0); //make sure that tablet sees not eliminado
    			tablet_elim(); // call eliminado handler
    			return;
    	}
    }
});

function tablet_reconocimiento() {
    tablet_putEvent('crono_rec',{
        'Session': workingData.sesion,
        'Value' : Date.now() - startDate
    } );
    doBeep();
    return false;
}

function tablet_startstop() {
	var time = Date.now() - startDate;
	if ( $('#tdialog-StartStopBtn').val() === "Start" ) {
		tablet_putEvent('start',{ 'Value' : time } );
	} else {
		tablet_putEvent('stop',{ 'Value' : time } );
	}
	doBeep();
	return false;
}

function tablet_salida() {
	tablet_putEvent('salida',{ 'Value' : Date.now() - startDate } );
	doBeep();
	return false;
}

function tablet_cancel() {
	doBeep();
	// retrieve original data from parent datagrid
	var dgname=$('#tdialog-Parent').val();
	var row =$(dgname).datagrid('getSelected');
	if (row) {
		// update database according row data
		row.Operation='update';
		$.ajax({
			type:'GET',
			url:"/agility/server/database/resultadosFunctions.php",
			dataType:'json',
			data: row,
			success: function () {
				// and fire up cancel event
				tablet_putEvent(
						'cancelar',
						{ 
							'NoPresentado'	:	row.NoPresentado,
							'Faltas'		:	row.Faltas,
							'Tocados'		:	row.Tocados,
							'Rehuses'		:	row.Rehuses,
							'Tiempo'		:	row.Tiempo,
							'Eliminado'		:	row.Eliminado
						} 
					);
			}
		});
	}
	// and close panel
	tablet_cronoManual('stop');
	tablet_cronoManual('reset');
	$('#tdialog-window').window('close');
	return false;
}

function tablet_accept() {
	doBeep();
	// save results 
	tablet_updateResultados(0); // mark as result no longer pendiente
	// retrieve original data from parent datagrid
	var dgname = $('#tdialog-Parent').val();
	var dg=$(dgname);
	var row = dg.datagrid('getSelected');
	if (!row) return false; // nothing to do. should mark error
	
	// now update and redraw data on
	var rowindex= dg.datagrid("getRowIndex", row);
	// send back data to parent tablet datagrid form
	var obj=formToObject('#tdialog-form');
	// mark as no longer pending
	obj.Pendiente=0;
	// update row
	dg.datagrid('updateRow',{index: rowindex, row: obj});
	// and fire up accept event
	tablet_putEvent(
			'aceptar',
			{ 
				'NoPresentado'	:	row.NoPresentado,
				'Faltas'		:	row.Faltas,
				'Tocados'		:	row.Tocados,
				'Rehuses'		:	row.Rehuses,
				'Tiempo'		:	row.Tiempo,
				'Eliminado'		:	row.Eliminado
			} 
		);
	tablet_cronoManual('stop');
	tablet_cronoManual('reset');
	if (!ac_config.tablet_next) { // no go to next row entry
		$('#tdialog-window').window('close'); // close window
		dg.datagrid('refreshRow',rowindex);
		return false;
	}
	// seleccionamos fila siguiente
	var count=dg.datagrid('getRows').length;    // row count
	if ( (rowindex+1)>=count ) { // at end of datagrid
		$('#tdialog-window').window('close'); // close window
		dg.datagrid('refreshRow',rowindex);
		return false;
	}
	// dg.datagrid('clearSelections');
	dg.datagrid('selectRow', rowindex+1);
	var data=dg.datagrid('getSelected');
	data.Session=workingData.sesion;
    data.Parent=dgname; // store datagrid reference
    $('#tdialog-form').form('load',data);
    return false; // prevent follow onClick event chain
}

function isExpected(event) {
	/*
	// Si la manga y el perro no coinciden con el mostrado en el tablet, log error e ignora evento
	if ( (event['Manga']!=$('#tdialog-Manga').val()) || (event['Perro']!=$('#tdialog-Perro').val()) ) return false;
	*/
	return true;
}

function tablet_processEvents(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
	var time=event['Value']; // miliseconds 
	switch (event['Type']) {
	case 'null': // null event: no action taken
		return;
	case 'init': // operator starts tablet application
		return;
	case 'open': // operator select tanda:
		return;
	case 'datos': // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		return;
	case 'llamada':	// llamada a pista
		tablet_cronoManual('stop');
		tablet_cronoManual('reset');
		return;
	case 'salida': // orden de salida
		myCounter.start();
		return;
	case 'start': // start crono manual
		if (!isExpected(event)) return;
		$('#tdialog-StartStopBtn').val("Stop");
		myCounter.stop();
        tablet_restartCronoManual(time);
		return;
	case 'stop': // stop crono manual
		if (!isExpected(event)) return;
		$('#tdialog-StartStopBtn').val("Start");
		tablet_cronoManual('stop',time);
		return;// Value contiene la marca de tiempo
	case 'crono_start': // arranque crono electronico
		// automatic chrono just overrides manual crono, 
		// except that bypasses configuration 'enabled' flag for it
		if (!isExpected(event)) return;
		// parar countdown
		$('#tdialog-StartStopBtn').val("Stop");
		myCounter.stop();
        var crm=$('#cronomanual');
		// arranca crono manual si no esta ya arrancado
		// si el crono manual ya esta arrancado, lo resetea y vuelve a empezar
		crm.Chrono('stop');
		crm.Chrono('reset');
		crm.Chrono('start',time);
		return;
	case 'crono_int':	// tiempo intermedio crono electronico
        $('#cronomanual').Chrono('pause'); setTimeout(function(){$('#cronomanual').Chrono('resume');},5000);
		return;
	case 'crono_stop':	// parada crono electronico
		// si value!=0 parar countdown y crono manual; y enviar tiempo al crono del tablet 
		myCounter.stop();
		$('#tdialog-StartStopBtn').val("Start");
		$('#cronomanual').Chrono('stop',time);
		return;
	case 'crono_dat':	// datos desde el crono electronico
	case 'crono_rec':	// reconocimiento de pista desde crono electronico
	case 'cancelar': // operador pulsa cancelar
		return;
    case 'aceptar':	// operador pulsa aceptar
        return;
    case 'info':	// click on user defined tandas
        return;
	default:
		alert("Unknow Event type: "+event['Type']);
		return;
	}
}