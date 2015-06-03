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

function c_updateData(data) {
	if (data["Faltas"]!=-1) $('#chrono_Faltas').html(data["Faltas"]);
	if (data["Tocados"]!=-1) $('#chrono_Tocados').html(data["Tocados"]);
	if (data["Rehuses"]!=-1) $('#chrono_Rehuses').html(data["Rehuses"]);
	// if (data["Tiempo"]!=-1) $('#chrono_Tiempo').html(data["Tiempo"]);
	if (data["Eliminado"]==1)	$('#chrono_Tiempo').html('<span class="blink" style="color:red">Elim.</span>');
	if (data["NoPresentado"]==1) $('#chrono_Tiempo').html('<span class="blink" style="color:red">NoPre.</span>');
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
function chrono_button(event,data) {
	data.Value=Date.now() - startDate;
	chrono_putEvent(event,data);
	doBeep();
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