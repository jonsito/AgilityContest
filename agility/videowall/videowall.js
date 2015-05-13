/*
videowall.js

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

function vwls_showOSD(val) {
	if (val==0) $('#vwls_common').css('display','none');
	else $('#vwls_common').css('display','initial');
}

function vwc_updateResults(event) {
	$.ajax( {
		type: "GET",
		dataType: 'html',
		url: "/agility/server/videowall.php",
		data: {
			Operation: 'resultados',
			Session: workingData.sesion
		},
		success: function(data,status,jqxhr) {
			$('#vwc_resultadosParciales').html(data);
		}
	});
}

function vwc_updatePendingQueue(event,pendientes) {
	$.ajax( {
		type: "GET",
		dataType: 'html',
		url: "/agility/server/videowall.php",
		data: {
			Operation: 'llamada',
			Pendientes: pendientes,
			Session: workingData.sesion
		},
		success: function(data,status,jqxhr) {
			$('#vwc_listaPendientes').html(data);
			var str=$('#vw_NombrePrueba').val()+" - "+$('#vw_NombreJornada').val();
			$('#vw_llamada-infocabecera').html(str);
		}
	});
}

function vwls_updateData(data) {
	if (data["Faltas"]!=-1) $('#vwls_Faltas').html(data["Faltas"]);
	if (data["Tocados"]!=-1) $('#vwls_Tocados').html(data["Tocados"]);
	if (data["Rehuses"]!=-1) $('#vwls_Rehuses').html(data["Rehuses"]);
	if (data["Tiempo"]!=-1) $('#vwls_Tiempo').html(data["Tiempo"]);
	if (data["Eliminado"]==1)	$('#vwls_Tiempo').html('<span class="blink" style="color:red">Elim.</span>');
	if (data["NoPresentado"]==1) $('#vwls_Tiempo').html('<span class="blink" style="color:red">N.P.</span>');
}

function vwls_showData(data) {
	var perro=$('#vwls_Perro').html();
	var dorsal=data['Dorsal'];
	var celo=parseInt(data['Celo']);
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
				$('#vwls_Logo').attr("src","/agility/images/logos/"+res['LogoClub']);
				$('#vwls_Dorsal').html("Dorsal: "+dorsal );
				$('#vwls_Nombre').html(res["Nombre"]);
				$('#vwls_NombreGuia').html("Guia: "+res["NombreGuia"]);
                $('#vwls_Categoria').html("Cat: "+toLongCategoria(res["Categoria"],res['Federation']));
                // hide "Grado" Information if not applicable
                $('#vwls_Grado').html(hasGradosByJornada(workingData.datosJornada)?res["NombreGrado"]:"");
                // on Team events, show Team info instead of Club
                var eq=workingData.teamsByJornada[data["Equipo"]].Nombre
                // como en el videowall no tenemos datos de la jornada, lo que hacemos es
                // contar el numero de equipos de esta para saber si es prueba por equipos o no
                $('#vwls_NombreClub').html((Object.keys(workingData.teamsByJornada).length>1)?"Eq: "+eq:"Club: "+res["NombreClub"]);
				$('#vwls_Celo').html((celo==1)?'<span class="blink">Celo</span>':'');
			},
			error: function(XMLHttpRequest,textStatus,errorThrown) {
				alert("error: "+textStatus + " "+ errorThrown );
			}
		});
	}
	// actualiza resultados del participante
	$('#vwls_Faltas').html(data["Faltas"]);
	$('#vwls_Tocados').html(data["Tocados"]);
	$('#vwls_Rehuses').html(data["Rehuses"]);
	$('#vwls_Tiempo').html(data["Tiempo"]);
	if (data["Eliminado"]==1)	$('#vwls_Tiempo').html('<span class="blink" style="color:red">Elim.</span>');
	if (data["NoPresentado"]==1) $('#vwls_Tiempo').html('<span class="blink" style="color:red">N.P.</span>');
	
}

var myCounter = new Countdown({  
    seconds:15,  // number of seconds to count down
    onUpdateStatus: function(sec){ $('#vwls_Tiempo').html(sec); }, // callback for each second
    // onCounterEnd: function(){  $('#vwls_Tiempo').html('<span class="blink" style="color:red">-out-</span>'); } // final action
    onCounterEnd: function(){ /* let the tablet to tell us what to do */ }
});

/**
 * Maneja el cronometro manual
 * @param {string} oper 'start','stop','pause','resume','reset'
 * @param {int} tstamp timestamp mark
 */
function vwls_cronoManual(oper,tstamp) {
	myCounter.stop();
	$('#cronomanual').Chrono(oper,tstamp);
}

/**
 * Refresca periodicamente el orden de salida correspondiente
 * a la seleccion especificada
 * Se indica tambien si el perro esta o no pendiente de salir
 */
function vwos_updateOrdenSalida(data) {
	$.ajax( {
		type: "GET",
		dataType: 'html',
		url: "/agility/server/videowall.php",
		data: {
			Operation: 'ordensalida',
			Prueba: data.Prueba,
			Jornada: data.Jornada,
			Session: data.Session
		},
		success: function(data,status,jqxhr) {
			$('#vw_ordensalida-data').html(data);
			var str=$('#vw_NombrePrueba').val()+" - "+$('#vw_NombreJornada').val()+" - "+$('#vw_NombreManga').val();
			$('#vw_ordensalida-infocabecera').html(str);
		}
	});
}

function vwc_processCombinada(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
	switch (event['Type']) {
	case 'null':		// null event: no action taken
		return; 
	case 'init':		// operator starts tablet application
		vwls_showOSD(0); 	// activa visualizacion de OSD
		return;
	case 'open':		// operator select tanda
		vwc_updateResults(event); // actualiza panel de resultados
		vwc_updatePendingQueue(event,15); // actualiza panel de llamadas 
		return;
	case 'datos':		// actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		vwls_updateData(event);
		return;
	case 'llamada':		// operador abre panel de entrada de datos
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_showOSD(1); 	// activa visualizacion de OSD
		vwls_showData(event);
		return;
	case 'salida':		// juez da orden de salida ( crono 15 segundos )
		myCounter.start();
		return;
	case 'start':	// value: timestamp
		vwls_cronoManual('start',event['Value']);
		return;
	case 'stop':	// value: timestamp
		vwls_cronoManual('stop',event['Value']);
		return;
	case 'crono_start': // arranque crono electronico
		myCounter.stop(); 
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_cronoManual('start',event['Value']);
		return;
	case 'crono_int':	// tiempo intermedio crono electronico
		// TODO: write
		return;
	case 'crono_stop':	// parada crono electronico
		myCounter.stop(); 
		vwls_cronoManual('stop',event['Value']);
		return;
	case 'aceptar':		// operador pulsa aceptar
		vwls_cronoManual('stop',event['Value']);  // nos aseguramos de que los cronos esten parados
		vwls_showData(event); // actualiza pantall liveStream
		vwc_updateResults(); // actualiza panel de resultados
		vwc_updatePendingQueue(event,15); // actualiza panel de llamadas 
		return;
	case 'cancelar':	// operador pulsa cancelar
		vwls_cronoManual('stop',event['Value']);
		vwls_cronoManual('reset');
		vwls_showOSD(0); // apaga el OSD
		vwc_updatePendingQueue(event,15); // actualiza panel de llamadas 
		return;
    case 'info':	// click on user defined tandas
        return;
	}
}

function vwls_processLiveStream(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
	switch (event['Type']) {
	case 'null':		// null event: no action taken
		return; 
	case 'init':		// operator starts tablet application
		vwls_showOSD(0); 	// activa visualizacion de OSD
		return;
	case 'open':		// operator select tanda: nothing to do here
		return;
	case 'datos':		// actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		vwls_updateData(event);
		return;
	case 'llamada':		// operador abre panel de entrada de datos
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_showOSD(1); 	// activa visualizacion de OSD
		vwls_showData(event);
		return;
	case 'salida':		// juez da orden de salida ( crono 15 segundos )
		myCounter.start();
		return;
	case 'start':	// value: timestamp
		myCounter.stop(); 
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_cronoManual('start',event['Value']);
		return;
	case 'stop':	// value: timestamp
		myCounter.stop(); 
		vwls_cronoManual('stop',event['Value']);
		return;
	case 'crono_start': // arranque crono electronico
		myCounter.stop(); 
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_cronoManual('start',event['Value']);
		return;
	case 'crono_int':	// tiempo intermedio crono electronico
		// TODO: write
		return;
	case 'crono_stop':	// parada crono electronico
		myCounter.stop(); 
		vwls_cronoManual('stop',event['Value']);
		return;
	case 'aceptar':		// operador pulsa aceptar
		vwls_cronoManual('stop',event['Value']);  // nos aseguramos de que los cronos esten parados
		// vwls_showData(event); // actualiza pantall liveStream
		return;
	case 'cancelar':	// operador pulsa cancelar
		vwls_cronoManual('stop',event['Value']);
		vwls_cronoManual('reset');
		vwls_showOSD(0); // apaga el OSD
		return;
    case 'info':	// click on user defined tandas
        return;
	}
}

function vw_processLlamada(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
	switch (event['Type']) {
	case 'null': // null event: no action taken
		return; 
	case 'init': // operator starts tablet application
		// TODO: muestra pendientes desde primera tanda
		return;
	case 'open': // operator select tanda:
		vwc_updatePendingQueue(event,25);
		return;
	case 'datos': // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		vwls_updateData(event);
		return;
	case 'llamada':	// llamada a pista
		return;
	case 'salida': // orden de salida
		return;
	case 'start': // start crono manual
		return;
	case 'stop': // stop crono manual
		return;
	case 'crono_start':  // arranque crono automatico
	case 'crono_int':  	// tiempo intermedio crono electronico
	case 'crono_stop':  // parada crono electronico
		return; // nada que hacer aqui: el crono automatico se procesa en el tablet
	case 'aceptar':	// operador pulsa aceptar
		vwc_updatePendingQueue(event,25);
		return;
	case 'cancelar': // operador pulsa cancelar
		vwc_updatePendingQueue(event,25);
		return;
    case 'info':	// click on user defined tandas
        return;
	}
}

function vw_processParciales(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
	switch (event['Type']) {
	case 'null': // null event: no action taken
		return; 
	case 'init': // operator starts tablet application
		// TODO: muestra pendientes desde primera tanda
		return;
	case 'open': // operator select tanda:
		vwc_updateResults(event);
		return;
	case 'datos': // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		return;
	case 'llamada':	// llamada a pista
		return;
	case 'salida': // orden de salida
		return;
	case 'start': // start crono manual
		return;
	case 'stop': // stop crono manual
		return;
	case 'crono_start':  // arranque crono automatico
	case 'crono_int':  	// tiempo intermedio crono electronico
	case 'crono_stop':  // parada crono electronico
		return; // nada que hacer aqui: el crono automatico se procesa en el tablet
	case 'aceptar':	// operador pulsa aceptar
		vwc_updateResults(event);
		return;
	case 'cancelar': // operador pulsa cancelar
		return;
    case 'info':	// click on user defined tandas
        return;
	}
}

/**
 * (This process is executed every minute on 'ordensalida' videowall)
 * 
 * retrieve last 'connect' event for current sessionID
 * call updateInscripciones with retrieved data
 */
function vwos_procesaOrdenSalida() {
	$.ajax({
		type: "GET",
		url: "/agility/server/database/eventFunctions.php",
		data: {
			'Operation' : 'connect',
			'Session'	: workingData.sesion
		},
		async: true,
		cache: false,
		success: function(data){
			var response= parseEvent(data);
			if ( response['total']!=0) {
				var row=response['rows'][0];
				var info= parseEvent(row.Data);
				vwos_updateOrdenSalida(info);
			}
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			alert("error: "+textStatus + " "+ errorThrown );
		}
	});
}