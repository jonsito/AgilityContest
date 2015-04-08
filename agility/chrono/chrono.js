/*
chrono.js

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

/******************* funciones de manejo de la ventana de entrada de resultados del tablet *****************/

/**
 * send events
 * @param {string} type: Event Type
 * @param {object} data: Event data
 */
function chrono_putEvent(type,data){
	// setup default elements for this event
	obj= {
			'Operation':'chronoEvent',
			'Type': 	type,
			'TimeStamp': Date.now(),
			'Source':	'chrono_'+workingData.sesion,
			'Session':	workingData.sesion,
			'Prueba':	workingData.prueba,
			'Jornada':	workingData.jornada,
			'Manga':	workingData.manga,
			'Tanda':	workingData.tanda	
	};
	// send "update" event to every session listeners
	$.ajax({
		type:'GET',
		url:"/agility/server/database/eventFunctions.php",
		dataType:'json',
		data: $.extend({},obj,data)
	});
}

function doBeep() {
	if (ac_config.tablet_beep)	setTimeout(function() {beep();},0);
}

var myCounter = new Countdown({  
    seconds:15,  // number of seconds to count down
    onUpdateStatus: function(sec){ $('#cdialog-Tiempo').val(sec); }, // callback for each second
    // onCounterEnd: function(){  $('#tdialog_Tiempo').html('<span class="blink" style="color:red">-out-</span>'); } // final action
    onCounterEnd: function(){ /* empty: let the tablet do the work */    }
});

function chrono_start() {
	chrono_putEvent('crono_start',{ 'Value' : Date.now() } );
	doBeep();
	return false;
}

function chrono_stop() {
	chrono_putEvent('crono_stop',{ 'Value' : Date.now() } );
	doBeep();
	return false;
}

function chrono_intermediate() {
	chrono_putEvent('crono_int',{ 'Value' : Date.now() } );
	doBeep();
	return false;
}

function isExpected(event) {
	// TODO: write
	return true;
}

function chrono_processEvents(id,evt) {
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
		$('#cronoauto').Chrono('stop');
		$('#cronoauto').Chrono('reset');
		return;
	case 'salida': // orden de salida
		myCounter.start();
		return;
	case 'start': // start crono manual
		return;
	case 'stop': // stop crono manual
		return;// Value contiene la marca de tiempo
	case 'crono_start': // arranque crono electronico
		// automatic chrono just overrides manual crono, 
		// except that bypasses configuration 'enabled' flag for it
		if (!isExpected(event)) return;
		// parar countdown
		myCounter.stop(); 
		// arranca crono manual si no esta ya arrancado
		// si el crono manual ya esta arrancado, lo resetea y vuelve a empezar
		$('#cronoauto').Chrono('stop');
		$('#cronoauto').Chrono('reset');
		$('#cronoauto').Chrono('start',time);
		return;
	case 'crono_int':	// tiempo intermedio crono electronico
		// TODO: write
		return;
	case 'crono_stop':	// parada crono electronico
		// si value!=0 parar countdown y crono manual; y enviar tiempo al crono del tablet 
		myCounter.stop();
		$('#cronoauto').Chrono('stop',time);
		return;
	case 'cancelar': // operador pulsa cancelar
		return;
	case 'aceptar':	// operador pulsa aceptar
		return;
	default:
		alert("Unknow Event type: "+event['Type']);
		return;
	}
}