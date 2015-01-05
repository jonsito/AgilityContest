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

/** 
 * Call "connect" to retrieve last "open" event for provided session ID
 * fill working data with received info
 * If no response wait two seconds and try again
 * On sucess invoke                                                                                                                                                                                                                            
 * @param sesID
 * @param callback
 */
function startEventMgr(sesID,callback) {
	var timeout=2000;
	$.ajax({
		type: "GET",
		url: "/agility/server/database/eventFunctions.php",
		data: {
			'Operation' : 'connect',
			'Session'	: sesID
		},
		async: true,
		cache: false,
		success: function(data){
			var response= eval('(' + data + ')' );
			if ( response['total']!=0) {
				var row=response['rows'][0];
				var evtID=row['ID'];
				initWorkingData(row['Session']);
				setTimeout(function(){ waitForEvents(sesID,evtID,0,callback);},0);
			} else {
				setTimeout(function(){ startEventMgr(sesID,callback);},timeout );
			}
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			alert("error: "+textStatus + " "+ errorThrown );
			setTimeout(function(){  startEventMgr(sesID,callback);},timeout );
		}
	});
}

function waitForEvents(sesID,evtID,timestamp,callback){
	$.ajax({
		type: "GET",
		url: "/agility/server/database/eventFunctions.php",
		data: {
			'Operation' : 'getEvents',
			'ID'		: evtID,
			'Session'	: sesID,
			'TimeStamp' : timestamp
		},
		async: true,
		cache: false,
		success: function(data){
			var response= eval('(' + data + ')' );
			var timestamp= response['TimeStamp'];
			$.each(response['rows'],function(key,value){
				evtID=value['ID']; // store last evt id
				callback(evtID,value['Data']);
			});
			setTimeout(function(){ waitForEvents(sesID,evtID,timestamp,callback);},500);
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			// alert("error: "+textStatus + " "+ errorThrown );
			setTimeout(function(){ waitForEvents(sesID,evtID,timestamp,callback);},5000);
		}
	});
}

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
				$('#vwls_Logo').attr("src","/agility/images/logos/"+data['LogoClub']);
				$('#vwls_Dorsal').html("Dorsal: "+dorsal );
				$('#vwls_Nombre').html(data["Nombre"]);
				$('#vwls_NombreGuia').html("Guia: "+data["NombreGuia"]);
				$('#vwls_NombreClub').html("Club: "+data["NombreClub"]);
				$('#vwls_Categoria').html(data["NombreCategoria"].replace(/.* - /g,""));
				$('#vwls_Grado').html(data["NombreGrado"]);
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

/**
 * activa una secuencia de conteo hacia atras de 15 segundos
 */
function vwls_counter(){
	var myCounter = new Countdown({  
	    seconds:15,  // number of seconds to count down
	    onUpdateStatus: function(sec){ $('#vwls_Tiempo').html(sec); }, // callback for each second
	    onCounterEnd: function(){  $('#vwls_Tiempo').html('<span class="blink" style="color:red">-out-</span>'); } // final action
	});
	myCounter.start();
}

/**
 * Maneja el cronometro manual
 * @param oper 'start','stop','pause','resume','reset'
 */
function vwls_cronoManual(oper) {
	$('#cronomanual').Chrono(oper);
}

/**
 * Imprime los inscritos en la jornada marcada por la sesion activa
 * @param jornada
 */
function vwi_updateInscripciones(data) {
	// var t=new Date().getTime();
	// $('#vw_inscripcionesJornada').html('Jornada:'+jornada+' '+t);
	$.ajax( {
		type: "GET",
		dataType: 'html',
		url: "/agility/server/videowall.php",
		data: {
			Operation: 'inscripciones',
			Prueba: data.Prueba,
			Jornada: data.Jornada,
			Session: data.Session
		},
		success: function(data,status,jqxhr) {
			$('#vw_inscripcionesJornada').html(data);
		}
	});
}

function vwos_paintOrdenSalida(prueba,jornada,manga) {
	// alert("Prueba:"+prueba+" Jornada:"+jornada+" Manga:"+manga);
	$.ajax( {
		type: "GET",
		dataType: 'html',
		url: "/agility/server/videowall.php",
		data: {
			Operation: 'ordensalida',
			Prueba: prueba,
			Jornada: jornada,
			Manga: manga,
		},
		success: function(data,status,jqxhr) {
			$('#vw_inscripcionesJornada').html(data);
		}
	});
}

/**
 * Refresca periodicamente el orden de salida correspondiente
 * a la seleccion especificada
 * Se indica tambien si el perro esta o no pendiente de salir
 */
function vwos_updateOrdenSalida(data) {
	if (data.Jornada==0) { // nada que presentar - operador hace logout
		// TODO: borra datos
		workingData.jornada=0;
		workingData.prueba=0;
		workingData.manga=0;
		return;
	}
	if ( workingData.jornada != data.Jornada ) { // operador cambia de jornada - hace login 
		// recupera rondas de la nueva jornada y selecciona la primera
		// TODO: write
		// marca nueva jornada como activa
		workingData.jornada=data.Prueba;
		workingData.jornada=data.Jornada;
		workingData.manga=data.Manga;
	}
	vwos_paintOrdenSalida(workingData.prueba, workingData.jornada, workingData.manga);
}

function vwc_processCombinada(id,evt) {
	var event=eval('('+evt+')'); // remember that event was coded in DB as an string
	event['ID']=id;
	switch (event['Type']) {
	case 'null':		// null event: no action taken
		return; 
	case 'init':		// operator starts tablet application
		vwls_showOSD(0); 	// activa visualizacion de OSD
		return;
	case 'open':		// operator select tanda
		vwc_updateResults(event); // actualiza panel de resultados
		vwc_updatePendingQueue(event,10); // actualiza panel de llamadas 
		return;
	case 'datos':		// actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		vwls_updateData(event);
		return
	case 'llamada':		// operador abre panel de entrada de datos
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_showOSD(1); 	// activa visualizacion de OSD
		vwls_showData(event);
		return
	case 'salida':		// juez da orden de salida ( crono 15 segundos )
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_counter();
		return;
	case 'start':	// value: timestamp
		vwls_cronoManual('start');
		return;
	case 'stop':	// value: timestamp
		vwls_cronoManual('stop');
		return;
	case 'cronoauto':  	// value: timestamp
		return; // nada que hacer aqui: el crono automatico se procesa en el tablet
	case 'aceptar':		// operador pulsa aceptar
		vwls_cronoManual('stop');  // nos aseguramos de que los cronos esten parados
		vwls_showData(event); // actualiza pantall liveStream
		vwc_updateResults(); // actualiza panel de resultados
		vwc_updatePendingQueue(event,10); // actualiza panel de llamadas 
		return;
	case 'cancelar':	// operador pulsa cancelar
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_showOSD(0); // apaga el OSD
		vwc_updatePendingQueue(event,10); // actualiza panel de llamadas 
		return;
	}
}

function vwls_processLiveStream(id,evt) {
	var event=eval('('+evt+')'); // remember that event was coded in DB as an string
	event['ID']=id;
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
		return
	case 'llamada':		// operador abre panel de entrada de datos
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_showOSD(1); 	// activa visualizacion de OSD
		vwls_showData(event);
		return
	case 'salida':		// juez da orden de salida ( crono 15 segundos )
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_counter();
		return;
	case 'start':	// value: timestamp
		vwls_cronoManual('start');
		return;
	case 'stop':	// value: timestamp
		vwls_cronoManual('stop');
		return;
	case 'cronoauto':  	// value: timestamp nada que hacer
		return; // nada que hacer aqui: el crono automatico se procesa en el tablet
	case 'aceptar':		// operador pulsa aceptar
		vwls_cronoManual('stop');  // nos aseguramos de que los cronos esten parados
		vwls_showData(event); // actualiza pantall liveStream
		return;
	case 'cancelar':	// operador pulsa cancelar
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_showOSD(0); // apaga el OSD
		return;
	}
}

function vw_processLlamada(id,evt) {
	var event=eval('('+evt+')'); // remember that event was coded in DB as an string
	event['ID']=id;
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
		return
	case 'llamada':	// llamada a pista
		return
	case 'salida': // orden de salida
		return;
	case 'start': // start crono manual
		return;
	case 'stop': // stop crono manual
		return;
	case 'cronoauto':  	// value: timestamp nada que hacer
		return; // nada que hacer aqui: el crono automatico se procesa en el tablet
	case 'aceptar':	// operador pulsa aceptar
		vwc_updatePendingQueue(event,25);
		return;
	case 'cancelar': // operador pulsa cancelar
		vwc_updatePendingQueue(event,25);
		return;
	}
}

function vw_processParciales(id,evt) {
	var event=eval('('+evt+')'); // remember that event was coded in DB as an string
	event['ID']=id;
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
		return
	case 'llamada':	// llamada a pista
		return
	case 'salida': // orden de salida
		return;
	case 'start': // start crono manual
		return;
	case 'stop': // stop crono manual
		return;
	case 'cronoauto':  	// value: timestamp nada que hacer
		return; // nada que hacer aqui: el crono automatico se procesa en el tablet
	case 'aceptar':	// operador pulsa aceptar
		vwc_updateResults(event);
		return;
	case 'cancelar': // operador pulsa cancelar
		return;
	}
}

/**
 * (This process is executed every minute on 'inscripciones' videowall)
 * 
 * retrieve last 'connect' event for current sessionID
 * call updateInscripciones with retrieved data
 */
function vwi_procesaInscripciones() {
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
			var response= eval('(' + data + ')' );
			if ( response['total']!=0) {
				var row=response['rows'][0];
				var info= eval('(' + row.Data + ')' );
				vwi_updateInscripciones(info);
			}
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			alert("error: "+textStatus + " "+ errorThrown );
		}
	});
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
			var response= eval('(' + data + ')' );
			if ( response['total']!=0) {
				var row=response['rows'][0];
				var info= eval('(' + row.Data + ')' );
				vwos_updateOrdenSalida(info);
			}
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			alert("error: "+textStatus + " "+ errorThrown );
		}
	});
}