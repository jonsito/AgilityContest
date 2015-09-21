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
			'Tanda':	workingData.tanda,
			'Value':	0 // may be overridden by received 'data' contents
	};
	// send "update" event to every session listeners
	$.ajax({
		type:'GET',
		url:"/agility/server/database/eventFunctions.php",
		dataType:'json',
		data: $.extend({},obj,data),
		success: function(data) {
			if (data.errorMsg)  $.messager.show({ width:300, height:150, title: 'Error', msg: data.errorMsg });
		}
	});
}

function need_resetChrono(data) {
	if (! isJornadaEq4()) return true;
	// en equipos4 resetea si cambio de equipo
	var eq=workingData.teamsByJornada[data["Equipo"]].Nombre;
	if ($('#chrono_NombreClub').html()!==$eq) return false;
	return true;
}

function doBeep() {
	if (ac_config.tablet_beep==="1")	setTimeout(function() {beep();},0);
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

function c_updateHeader() {
	var mng=workingData.datosManga.Nombre;
	var jor=workingData.datosJornada.Nombre;
	var pru=workingData.datosPrueba.Nombre;
	var club=workingData.datosPrueba.NombreClub
	var logo=workingData.datosPrueba.LogoClub;
	$('#chrono_PruebaLbl').html( pru + ' - ' + jor + ' - ' + mng );
	$('#chrono_LogoClub').attr('src','/agility/images/logos/'+logo);
	$('#chrono_Club').html(club);
}

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
			success: function(res){
				$('#chrono_Logo').attr("src","/agility/images/logos/"+res['LogoClub']);
				$('#chrono_Dorsal').html("Dors: "+dorsal );
				$('#chrono_Nombre').html(res["Nombre"]);
				$('#chrono_NombreGuia').html("Gu&iacute;a: "+res["NombreGuia"]);
				$('#chrono_Categoria').html("Cat: "+toLongCategoria(res["Categoria"],res['Federation']));
				// hide "Grado" Information if not applicable
				$('#chrono_Grado').html(hasGradosByJornada(workingData.datosJornada)?res["NombreGrado"]:"");
				// on Team events, show Team info instead of Club
				var eq=workingData.teamsByJornada[data["Equipo"]].Nombre;
				// como en el videowall no tenemos datos de la jornada, lo que hacemos es
				// contar el numero de equipos de esta para saber si es prueba por equipos o no
				$('#chrono_NombreClub').html((Object.keys(workingData.teamsByJornada).length>1)?"Eq: "+eq:"Club: "+res["NombreClub"]);
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
	if (data["NoPresentado"]==1) $('#chrono_Tiempo').html('<span class="blink" style="color:red">NoPr.</span>');
	
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
 * handle sensor errors
 */
function chrono_markError() {
	if($('#chrono_Error').text()==="") chrono_putEvent('crono_error',{Value:1});
	else chrono_putEvent('crono_error',{Value:0});
}

/**
 * same as chrono_button, but do nothing if guard time hasn't expired
 * @param {string} Event type
 * @param {object} data Event data
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
                chrono_button('crono_reset',{});
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
                // si crono manual started: start crono auto
                if ($('#chrono_Manual').text() !== '' ) {
                    chrono_sensor('crono_start',{},4000);
                } else {
                    // else handle
                    if ($('#cronoauto').Chrono('started')) chrono_sensor('crono_stop',{},4000);
                    else chrono_sensor('crono_start',{},4000);
                }
                break;
            case 27: // 'Esc' show/hide buttons
				var b=$('#chrono-buttons');
				var c=$('#chrono-copyright');
                if(b.is(':visible')) { b.css('display','none'); c.css('display','inline-block'); }
                else  { b.css('display','inline-block'); c.css('display','none'); }
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
	var cra=$('#cronoauto');
	var crm=$('#chrono_Manual'); // Texto "manual"
	var cre=$('#chrono_Error');  // Textro "comprobar sensores"
	var ssf=$('#chrono_StartStopFlag');
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
	var time=event['Value']; // miliseconds 
	switch (event['Type']) {
	case 'null': // null event: no action taken
		return;
	case 'init': // operator starts tablet application
		return;
		case 'open': // operator select tanda:
		// update working data. when done update header
	 	setupWorkingData(event['Pru'],event['Jor'],(event['Mng']>0)?event['Mng']:1,c_updateHeader);
		// actualizar datos de prueba, jornada, manga y logotipo del club
		return;
	case 'datos': // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		c_updateData(event);
		return;
	case 'llamada':	// llamada a pista
        crm.text('').removeClass('blink');
		// todo: en 4 conjunta solo para crono si cambio de equipo
		if (need_resetChrono()) {
			cra.Chrono('stop',time);
			cra.Chrono('reset');
		}
		c_showData(event);
		return;
	case 'salida': // orden de salida
        crm.text('').removeClass('blink');
		c_llamada.start();
		return;
	case 'start': // arranque manual del cronometro
		if (ssf.text()==="Auto") return; // si crono automatico, ignora
		c_llamada.stop();
		c_reconocimiento.stop();
		ssf.text("Stop");
		crm.text('Manual').addClass('blink'); // add 'Manual' mark
		cra.Chrono('stop',time);
		cra.Chrono('reset');
		cra.Chrono('start',time);
		return;
	case 'stop': // parada manual del cronometro
		c_llamada.stop(); // not really needed, but...
		c_reconocimiento.stop();// also, not really needed, but...
		ssf.text("Start");
		cra.Chrono('stop',time);
		return;// Value contiene la marca de tiempo
	case 'crono_start': // arranque crono electronico
		c_llamada.stop();
		c_reconocimiento.stop();
		ssf.text('Auto');
		// si esta parado, arranca en modo automatico
		if (!cra.Chrono('started')) {
			crm.text('').removeClass('blink'); // clear 'Manual' mark
			cra.Chrono('stop',time);
			cra.Chrono('reset');
			cra.Chrono('start',time);
			return;
		}
		// si no resync, resetea el crono y vuelve a contar
		if (ac_config.crono_resync==="0") {
			crm.text('').removeClass('blink'); // clear 'Manual' mark
			cra.Chrono('reset');
            cra.Chrono('start',time);
		} // else wait for chrono restart event
		return;
    case 'crono_restart': // paso de tiempo intermedio a manual
        cra.Chrono('resync',event['stop'],event['start']);
        return;
	case 'crono_reset': //puesta a cero del crono
		// parar countdown
		c_llamada.stop();
		c_reconocimiento.stop();
		crm.text('').removeClass('blink'); // clear 'Manual' mark
		cre.text('').removeClass('blink'); // clear 'Sensor Error' mark
		cra.Chrono('stop',time);
		cra.Chrono('reset');
		return;
	case 'crono_int':	// tiempo intermedio crono electronico
		if (!cra.Chrono('started')) return;		// si crono no esta activo, ignorar
		cra.Chrono('pause',time); setTimeout(function(){cra.Chrono('resume');},5000);
		return;
	case 'crono_error': // sensor error detected
		if (event['Value']==1)
			cre.text('Fallo Sensores').addClass('blink'); // error: show it
		else
			cre.text('').removeClass('blink'); // error solved
		return;
    case 'crono_stop':	// parada crono electronico
		ssf.text("Start");
		cra.Chrono('stop',time);
		return;
	case 'crono_dat': // operador pulsa botonera del crono
			// at this moment, every crono_dat events are ignored:
			// this is a sample implementation and this crono is not designed
			// to work without tablet; so no sense to take care
			// on 'crono_dat' events: just use 'datos' event from tablet instead
		return;
	case 'crono_rec': // reconocimiento de pista
		// si crono esta activo, ignorar
		if (cra.Chrono('started')) return;
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
