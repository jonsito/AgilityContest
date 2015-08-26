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
 * @param {string} type Event Type
 * @param {object} data Event data
 */
function chrono_putEvent(type,data){
	// setup default elements for this event
	var obj= {
			'Operation':'chronoEvent',
			'Type': 	type,
			'TimeStamp': Date.now() - startDate,
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

var c_llamada = new Countdown({  
    seconds:15,  // number of seconds to count down
    onUpdateStatus: function(sec){ $('#chrono_Tiempo').html(sec); }, // callback for each second
    // onCounterEnd: function(){  $('#tdialog_Tiempo').html('<span class="blink" style="color:red">-out-</span>'); } // final action
    onCounterEnd: function(){ /* empty: let the tablet do the work */    }
});

var c_reconocimiento = new Countdown({
	seconds: 420, // 7 minutes
    onUpdateStatus: function(sec){
    	var time=sprintf('%02d:%02d', Math.floor(sec/60),sec%60);
    	$('#chrono_Tiempo').html( time ); 
    }, // callback for each second
    onCounterEnd: function(){ /* empty */    }
});

var c_sensorDate = 0;

function c_updateData(data) {
	if (data["Faltas"]!=-1) $('#chrono_Faltas').html(data["Faltas"]);
	if (data["Tocados"]!=-1) $('#chrono_Tocados').html(data["Tocados"]);
	if (data["Rehuses"]!=-1) $('#chrono_Rehuses').html(data["Rehuses"]);
	// if (data["Tiempo"]!=-1) $('#chrono_Tiempo').html(data["Tiempo"]);
	if (data["Eliminado"]==1)	$('#chrono_Tiempo').html('<span class="blink" style="color:red">Elim.</span>');
	if (data["NoPresentado"]==1) $('#chrono_Tiempo').html('<span class="blink" style="color:red">NoPr.</span>');
}

function c_showData(data) {
	var perro=$('#chrono_Perro').html();
	var dorsal=data['Dorsal'];
	var celo=data['Celo'];
	if (perro!==data['Perro']) {
		// if datos del participante han cambiado actualiza
		$.ajax({
			type: "GET",
			url: "/agility/server/database/dogFunctions.php",
			data: {
				'Operation' : 'getbyidperro',
				'ID'	: data['Perro']
			},
			async: true,
			cache: false,
			dataType: 'json',
			success: function(data){
				$('#chrono_Logo').attr("src","/agility/images/logos/"+data['LogoClub']);
				$('#chrono_Dorsal').html("Dors: "+dorsal );
				$('#chrono_Nombre').html(data["Nombre"]);
				$('#chrono_NombreGuia').html("Guia: "+data["NombreGuia"]);
				$('#chrono_NombreClub').html("Club: "+data["NombreClub"]);
				$('#chrono_Categoria').html(data["NombreCategoria"].replace(/.* - /g,""));
				$('#chrono_Grado').html(data["NombreGrado"]);
				$('#chrono_Celo').html((celo==1)?'<span class="blink">Celo</span>':'');
			},
			error: function(XMLHttpRequest,textStatus,errorThrown) {
				alert("error: "+textStatus + " "+ errorThrown );
			}
		});
	}
	// actualiza resultados del participante
	$('#chrono_Faltas').html(data["Faltas"]);
	$('#chrono_Tocados').html(data["Tocados"]);
	$('#chrono_Rehuses').html(data["Rehuses"]);
	$('#chrono_Tiempo').html(data["Tiempo"]);
	if (data["Eliminado"]==1)	$('#chrono_Tiempo').html('<span class="blink" style="color:red">Elim.</span>');
	if (data["NoPresentado"]==1) $('#chrono_Tiempo').html('<span class="blink" style="color:red">NoPre.</span>');
	
}

/**
 * send events from chronometer to console
 * @param {string} event type
 * @param {array} data event data
 */
function chrono_button(event,data) {
	data.Value=Date.now() - startDate;
	chrono_putEvent(event,data);
	doBeep();
}

/**
 * same as chrono_button, but do nothing if guard time hasn't expired
 * @param {string} Event type
 * @param {array} data Event data
 * @param {integer} guard Guard time
 */
function chrono_sensor(event,data,guard) {
	var cur= Date.now() - startDate;
	if ( (cur-c_sensorDate) < guard ) {
		// not yet guard time: ignore key/button
		return;
	}
	c_sensorDate=cur;
	data.Value=cur;
	chrono_putEvent(event,data);
	doBeep();
}

function isExpected(event) {
	// TODO: write
	return true;
}

function bindKeysToChrono() {
	// parse keypress event on every  button
	$(document).keydown(function(e) {
		switch(e.which) {
			// reconocimiento de pista
			case 55: // '7' -> comienzo del reconocimiento
			case 48: // '0' -> fin del reconocimiento
				chrono_button('crono_rec',{});
				break;
			// entrada de datos desde crono
			case 70: // 'F' -> falta
				chrono_button('crono_dat',{'Falta':1})
				break;
			case 82: // 'R' -> rehuse
				chrono_button('crono_dat',{'Rehuse':1})
				break;
			case 84: // 'T' -> tocado
				chrono_button('crono_dat',{'Tocado':1})
				break;
			case 69: // 'E' -> eliminado
				chrono_button('crono_dat',{'Eliminado':1})
				break;
			case 78: // 'N' -> no presentado
				chrono_button('crono_dat',{'NoPresentado':1})
				break;
			// arranque parada del crono
            case 8: // 'Del' -> chrono reset
                var cra=$('#cronoauto');
                cra.Chrono('stop');
                cra.Chrono('reset');
                break;
			case 36: // 'Begin' -> chrono start
				chrono_sensor('crono_start',{},4000);
				break;
			case 73: // 'I' -> chrono intermediate
				chrono_sensor('crono_int',{},4000);
				break;
			case 35: // 'End' -> chrono stop
				chrono_sensor('crono_stop',{},4000);
				break;
			case 13: // 'Enter' -> chrono start/stop
			case 83: // 'S' -> alternate chrono start/stop
				if ($('#cronoauto').Chrono('started')) chrono_sensor('crono_stop',{},4000);
				else chrono_sensor('crono_start',{},4000);
                break;
            case 27: // 'Esc' show/hide buttons
                var b=$('#chrono-simButtons');
                if(b.is(':visible')) b.css('display','none');
                else  b.css('display','inline-block');
                break;
            default:
                // alert("Unknow key code: "+ e.which);
				// pass to upper layer to caught and process
                return true;
		}
		return false;
	});
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
		c_updateData(event);
		return;
	case 'llamada':	// llamada a pista
        var cra=$('#cronoauto');
		cra.Chrono('stop');
		cra.Chrono('reset');
		c_showData(event);
		return;
	case 'salida': // orden de salida
		c_llamada.start();
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
		c_llamada.stop(); 
		c_reconocimiento.stop();
        var cra=$('#cronoauto');
		// arranca crono manual si no esta ya arrancado
		// si el crono manual ya esta arrancado, lo resetea y vuelve a empezar
		cra.Chrono('stop');
		cra.Chrono('reset');
		cra.Chrono('start',time);
		return;
	case 'crono_int':	// tiempo intermedio crono electronico
        $('#cronoauto').Chrono('pause'); setTimeout(function(){$('#cronoauto').Chrono('resume');},5000);
		// TODO: write
		return;
	case 'crono_stop':	// parada crono electronico
		c_llamada.stop(); // not really needed, but...
		c_reconocimiento.stop();// also, not really needed, but...
		$('#cronoauto').Chrono('stop',time);
		return;
	case 'crono_dat': // operador pulsa botonera del crono
		return;
	case 'crono_rec': // reconocimiento de pista
		if (c_reconocimiento.val()!==0) c_reconocimiento.stop();
		else c_reconocimiento.start();
		return;
	case 'cancelar': // operador pulsa cancelar en tablet
		return;
	case 'aceptar':	// operador pulsa aceptar en tablet
		return;
    case 'info':	// click on user defined tandas
        return;
	default:
		alert("Unknow Event type: "+event['Type']);
		return;
	}
}
