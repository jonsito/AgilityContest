<?php
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
$config =Config::getInstance();
?>
/*
chrono.js

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
function chrono_putEvent(type,dat){
	// setup default elements for this event
	var obj= {
			'Operation':'chronoEvent',
			'Type': 	type,
			'TimeStamp': Math.floor(Date.now() / 1000),
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
		data: $.extend({},obj,dat),
		success: function(data) {
			if (data.errorMsg)  $.messager.show({ width:300, height:150, title: 'Error', msg: data.errorMsg });
		}
	});
}

function need_resetChrono(data) {
	if (! isJornadaEqConjunta()) return true;
	// en equipos4 resetea si cambio de equipo
	var eq=workingData.teamsByJornada[data["Equipo"]].Nombre;
	if ($('#chrono_NombreClub').html()!==eq) return false;
	return true;
}

function doBeep() {
	if (ac_config.tablet_beep==="1")	setTimeout(function() {beep();},0);
}

var c_llamada = new Countdown({  
    seconds:15,  // number of seconds to count down
    onUpdateStatus: function(tsec){
		var dta=sprintf('%d.%d', Math.floor(tsec/10),tsec%10);
		$('#chrono_Tiempo').html(dta);
	}, // callback for each second
    // onCounterEnd: function(){  $('#tdialog_Tiempo').html('<span class="blink">-out-</span>'); } // final action
    onCounterEnd: function(){
    	/* start automatic chronometer.
    	    IMPORTANT NOTICE:
    	    This order is conflicting with start manual cronometer from tablet
    	    So the golden rule is:
    	    - IF using electronic chronometer
    	    - THEN make sure tablet&crono options has "what to do when 15sec countdown goes to zero"
    	      IS SET TO "eliminated" or "do nothing"
    	 */
		chrono_sensor('crono_start',{},5000); // 5 seconds salvaguard to skip start jump
	}
});

var c_reconocimiento = new Countdown({
	seconds: 60*parseInt(ac_config.crono_rectime),
	onStart: function () {
		var crr=$('#chrono_Reconocimiento');  // Textro "reconocimiento de pista"
		crr.text('<?php _e('Course walk');?>').addClass('blink');
	},
	onStop: function () {
		var crr=$('#chrono_Reconocimiento');  // Textro "reconocimiento de pista"
		crr.text('').removeClass('blink');
	},
    onUpdateStatus: function(tsec){
		var sec=tsec/10; // remove tenths of seconds
    	var time=sprintf('%02d:%02d', Math.floor(sec/60),sec%60);
    	$('#chrono_Tiempo').html( time );
    }, // callback for each tenth of second
    onCounterEnd: function(){ /* empty */    }
});

var c_sensorDate = 0;

function c_updateHeader() {
	var mng=workingData.datosManga.Nombre;
	var jor=workingData.datosJornada.Nombre;
	var pru=workingData.datosPrueba.Nombre;
	var club=workingData.datosPrueba.NombreClub;
	var logo=workingData.datosPrueba.LogoClub;
	// en pruebas internacionales, se pone el logo de la federacion
	if (isInternational(workingData.federation)) {
		logo=ac_fedInfo[workingData.federation].Logo;
		$('#chrono_LogoClub').attr('src',logo);
	} else { // en pruebas "nacionales" se pone el logo del club organizador
		$('#chrono_LogoClub').attr('src',"/agility/images/logos/"+logo);
	}
	$('#chrono_PruebaLbl').html( pru + ' - ' + jor + ' - ' + mng );
	$('#chrono_Club').html(club);
}

function c_updateData(data) {
	if (data["Faltas"]!=-1) $('#chrono_Faltas').html(data["Faltas"]);
	if (data["Tocados"]!=-1) $('#chrono_Tocados').html(data["Tocados"]);
	if (data["Rehuses"]!=-1) $('#chrono_Rehuses').html(data["Rehuses"]);
	// if (data["Tiempo"]!=-1) $('#chrono_Tiempo').html(data["Tiempo"]);
	// on update hide puesto
	$('#chrono_PuestoLbl').html('');
	var e=parseInt(data["Eliminado"]);
	if (e>=0) {
		$('#chrono_Eliminado').html(e);
		$('#chrono_EliminadoLbl').html((e==0)?'':'<span class="blink"><?php _e('Elim');?>.</span>');
	}
	var n=parseInt(data["NoPresentado"]);
	if (n>=0) {
		$('#chrono_NoPresentado').html(n);
		$('#chrono_NoPresentadoLbl').html((n==0)?'':'<span class="blink"><?php _e('NoPr');?>.</span>');
	}
}

function c_updateDataFromChrono(data) {
	// just call c_updateData()
	c_updateData(data);
}

function c_clearData(event) {
	$('#chrono_Logo').attr("src","/agility/images/logos/agilitycontest.png");
	$('#chrono_Dorsal').html("<?php _e('Dors');?>: " );
	$('#chrono_Nombre').html("<?php _e('Name');?>: ");
	$('#chrono_NombreGuia').html("<?php _e('Hndlr');?>: ");
	$('#chrono_Categoria').html("<?php _e('Cat');?>: ");
	// hide "Grado" Information if not applicable
	$('#chrono_Grado').html(hasGradosByJornada(workingData.datosJornada)?"<?php _e('Grad');?>: ":"");
	// ajustamos el texto del club/pais/equipo
	// si el numero de equipos de la jornada es mayor que 1 estamos en una jornada por equipos
	if (Object.keys(workingData.teamsByJornada).length>1){
		$('#chrono_NombreClub').html("<?php _e('Team')?>: ");
	}
	// else si estamos en una prueba internacional ponemos el nombre del pais
	else if (isInternational(workingData.federation)) {
		$('#chrono_NombreClub').html("<?php _e('Country')?>: ");
	}
	// else ponemos el nombre del club
	else {
		$('#chrono_NombreClub').html("<?php _e('Club')?>: ");
	}
	$('#chrono_Celo').html('');	// ajustamos el celo
	// mark no dog active
	var perro=$('#chrono_Perro').html(0);
	// clear results frame
	$('#chrono_Faltas').html("0");
	$('#chrono_Tocados').html("0");
	$('#chrono_Rehuses').html("0");
	$('#chrono_Tiempo').html((parseInt(ac_config.numdecs)==2)?"00.00":"00.000");
}

/**
 * evaluate and display position for this dog
 * @param {boolean} flag: true:evaluate, false:clear
 * @param {float} tiempo datatime from chronometer
 */
function c_displayPuesto(flag,time) {
	if ( !flag) {// if requested, turn off data
		$('#chrono_PuestoLbl').html('');
		return false;
	}

	// use set timeout to make sure data are already refreshed
	setTimeout(function(){
		// phase 1 retrieve results
		// use text() instead of html() avoid extra html code
		var datos= {
			'Perro':	$('#chrono_Perro').text(),
			'Categoria':$('#chrono_Cat').text(),
			'Grado':	$('#chrono_Grado').text(),
			'Faltas':	$('#chrono_Faltas').text(),
			'Tocados':	$('#chrono_Tocados').text(),
			'Rehuses':	$('#chrono_Rehuses').text(),
			'Eliminado':$('#chrono_Eliminado').text(),
			'NoPresentado':$('#chrono_NoPresentado').text(),
			'Tiempo':	time
		};
		// phase 2: do not call server if no perro or eliminado or not presentado
		if (datos.Perro=="" || parseInt(datos.Perro)<=0) {
			$('#chrono_PuestoLbl').html('');
			return;
		}
		if (parseInt(datos.NoPresentado)==1) {
			$('#chrono_PuestoLbl').html('<span class="blink" style="color:red;"><?php _e('NoPr');?>.</span>');// no presentado
			return;
		}
		if (parseInt(datos.Eliminado)==1) {
			$('#chrono_PuestoLbl').html('<span class="blink" style="color:red;"><?php _e('Elim');?>.</span>');// eliminado
			return;
		}
		// phase 2: call server to evaluate partial result position
		getPuestoParcial(datos,function(data,resultados){
			$('#chrono_PuestoLbl').html('- '+Number(resultados.puesto).toString()+' -');
		});
	},0);
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
				'Federation': workingData.federation,
				'ID'	: data['Perro']
			},
			async: true,
			cache: false,
			dataType: 'json',
			success: function(res){
				$('#chrono_Logo').attr("src","/agility/images/logos/"+res['LogoClub']);
				$('#chrono_Dorsal').html("<?php _e('Dors');?>: "+dorsal );
				$('#chrono_Nombre').html(res["Nombre"]);
				$('#chrono_Perro').html(res["ID"]);
				$('#chrono_Cat').html(res["Categoria"]);
				$('#chrono_NombreGuia').html("<?php _e('Hndlr');?>: "+res["NombreGuia"]);
				$('#chrono_Categoria').html("<?php _e('Cat');?>: "+toLongCategoria(res["Categoria"],res['Federation']));
				// hide "Grado" Information if not applicable
				$('#chrono_Grado').html(hasGradosByJornada(workingData.datosJornada)?res["NombreGrado"]:"");
				// ajustamos el texto del club/pais/equipo
				// si el numero de equipos de la jornada es mayor que 1 estamos en una jornada por equipos
				if (Object.keys(workingData.teamsByJornada).length>1){
					var eq=workingData.teamsByJornada[data["Equipo"]].Nombre;
					$('#chrono_NombreClub').html("<?php _e('Team')?>: "+eq);
				}
				// else si estamos en una prueba internacional ponemos el nombre del pais
				else if (isInternational(workingData.federation)) {
					$('#chrono_NombreClub').html("<?php _e('Country')?>: "+res["NombreClub"]);
				}
				// else ponemos el nombre del club
				else {
					$('#chrono_NombreClub').html("<?php _e('Club')?>: "+res["NombreClub"]);
				}
				// ajustamos el celo
				$('#chrono_Celo').html((celo==1)?'<span class="blink"><?php _e("Heat");?></span>':'');
			},
			error: function(XMLHttpRequest,textStatus,errorThrown) {
				alert("c_showData() error: "+textStatus + " "+ errorThrown );
			}
		});
	}
	// actualiza resultados del participante
	$('#chrono_Faltas').html(data["Faltas"]);
	$('#chrono_Tocados').html(data["Tocados"]);
	$('#chrono_Rehuses').html(data["Rehuses"]);
	$('#chrono_Tiempo').html(data["Tiempo"]);
	$('#chrono_PuestoLbl').html(''); // solo se muestra puesto al final
	var e=parseInt(data["Eliminado"]);
	if (e>=0) {
		$('#chrono_Eliminado').html(e);
		$('#chrono_EliminadoLbl').html((e==0)?'':'<span class="blink"><?php _e('Elim');?>.</span>');
	}
	var n=parseInt(data["NoPresentado"]);
	if (n>=0) {
		$('#chrono_NoPresentado').html(n);
		$('#chrono_NoPresentadoLbl').html((n==0)?'':'<span class="blink"><?php _e('NoPr');?>.</span>');
	}
}

/**
 * send events from chronometer to console
 * @param {string} item button name
 */
function chrono_button(item) {
	var val=1+parseInt($("#chrono_"+item).html());
	var data={
		'Faltas': (item=="Faltas")?val:-1,
		'Tocados':(item=="Tocados")?val:-1,
		'Rehuses':(item=="Rehuses")?val:-1,
		'Eliminado':(item=="Eliminado")?val%2:-1,
		'NoPresentado':(item=="NoPresentado")?val%2:-1
	};
	chrono_putEvent('crono_dat',data);
	doBeep();
}

function chrono_rec() {
	var val=1;
	if (c_reconocimiento.started()) val=0;
	var data= {
		'Value' : Date.now() - startDate,
		'start' : val * 60 * parseInt(ac_config.crono_rectime)
	};
	chrono_putEvent('crono_rec',data);
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
 * @param {string} event Event type
 * @param {object} data Event data
 * @param {int} guard Guard time
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
		var val=0;
		var inc=(e.ctrlKey)?-1:1; // take care on control key
		switch(e.which) {
			// reconocimiento de pista
			case 55: // '7' -> comienzo del reconocimiento
			case 48: // '0' -> fin del reconocimiento
				var data= {
					'Value' : Date.now() - startDate,
					'start' : 60 * parseInt(ac_config.crono_rectime)
					};
				chrono_putEvent('crono_rec',data);
				break;
			// entrada de datos desde crono -1:dec +1:inc 0:nochange
			case 70: // 'F' -> falta
				val=inc + parseInt($("#chrono_Faltas").html());
				if (val<0) val=0;
				$("#chrono_Faltas").html(val);
				chrono_putEvent('crono_dat',{'Faltas':val,'Tocados':-1,'Rehuses':-1,'NoPresentado':-1,'Eliminado':-1});
				break;
			case 82: // 'R' -> rehuse
				val=inc + parseInt($("#chrono_Rehuses").html());
				if (val<0) val=0;
				$("#chrono_Rehuses").html(val);
				chrono_putEvent('crono_dat',{'Faltas':-1,'Tocados':-1,'Rehuses':val,'NoPresentado':-1,'Eliminado':-1});
				break;
			case 84: // 'T' -> tocado
				val=inc + parseInt($("#chrono_Tocados").html());
				if (val<0) val=0;
				$("#chrono_Tocados").html(val);
				chrono_putEvent('crono_dat',{'Faltas':-1,'Tocados':val,'Rehuses':-1,'NoPresentado':-1,'Eliminado':-1});
				break;
			case 69: // 'E' -> eliminado
				val=inc + parseInt($("#chrono-Eliminado").html());
				val=(val<=0)?0:1;
				$("#chrono_Eliminado").html(val);
				$('#chrono_EliminadoLbl').html((val==0)?'':'<span class="blink" ><?php _e('Elim');?>.</span>');
				chrono_putEvent('crono_dat',{'Faltas':-1,'Tocados':-1,'Rehuses':-1,'NoPresentado':-1,'Eliminado':val});
				break;
			case 78: // 'N' -> no presentado
				val=inc + parseInt($("#chrono-NoPresentado").html());
				val=(val<=0)?0:1;
				$("#chrono_NoPresentado").html(val);
				$('#chrono_NoPreseentadoLbl').html((val==0)?'':'<span class="blink"><?php _e('NoPr');?>.</span>');
				chrono_putEvent('crono_dat',{'Faltas':-1,'Tocados':-1,'Rehuses':-1,'NoPresentado':val,'Eliminado':-1});
				break;
			// arranque parada del crono
			case 71: // 'G' -> Start 15 seconds countdown
				chrono_sensor('salida',{},1000);
				break;
			// arranque parada del crono
            case 8: // 'Del' -> chrono reset
                chrono_putEvent('crono_reset',{});
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
		doBeep();
		return false;
	});
}

function chrono_eventManager(id,evt) {
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
		c_clearData(event);
		return;
	case 'open': // operator select tanda:
		// update working data. when done update header
	 	setupWorkingData(event['Pru'],event['Jor'],(event['Mng']>0)?event['Mng']:1,c_updateHeader);
		// remove puesto info as no sense here
		c_displayPuesto(false,0); // clear puesto
		return;
	case 'close': // no more dogs in tabla
		c_clearData(event);
		return;
	case 'datos': // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		c_updateData(event);
		return;
	case 'llamada':	// llamada a pista
		c_showData(event);
		return;
	case 'salida': // orden de salida
        crm.text('').removeClass('blink');
		c_displayPuesto(false,0); // clear puesto
		c_llamada.start();
		return;
	case 'start': // arranque manual del cronometro
		c_displayPuesto(false,0); // clear puesto
		if (ssf.text()==="Auto") return; // si crono automatico, ignora
		c_llamada.stop();
		c_reconocimiento.stop();
		ssf.text("Stop");
		crm.text("<?php _e('Manual');?>").addClass('blink'); // add 'Manual' mark
		cra.Chrono('stop',time);
		cra.Chrono('reset');
		cra.Chrono('start',time);
		return;
	case 'stop': // parada manual del cronometro
		c_llamada.stop(); // not really needed, but...
		c_reconocimiento.stop();// also, not really needed, but...
		ssf.text("Start");
		cra.Chrono('stop',time);
		c_displayPuesto(true,cra.Chrono('getValue')/1000);
		return;// Value contiene la marca de tiempo
	case 'crono_start': // arranque crono electronico
		c_displayPuesto(false,0);
		ssf.text('Auto');
		// si esta parado, arranca en modo automatico
		if (!cra.Chrono('started')) {
			c_llamada.stop();
			c_reconocimiento.stop();
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
		c_displayPuesto(false,0);
		return;
	case 'crono_int':	// tiempo intermedio crono electronico
		if (!cra.Chrono('started')) return;		// si crono no esta activo, ignorar
		cra.Chrono('pause',time); setTimeout(function(){cra.Chrono('resume');},5000);
		return;
	case 'crono_error': // sensor error detected
		if (event['Value']==1)
			cre.text('<?php _e("Sensor failure");?>').addClass('blink'); // error: show it
		else
			cre.text('').removeClass('blink'); // error solved
		return;
    case 'crono_stop':	// parada crono electronico
		ssf.text("Start");
		cra.Chrono('stop',time);
		c_displayPuesto(true,cra.Chrono('getValue')/1000);
		return;
	case 'crono_dat': // operador pulsa botonera del crono
		c_updateDataFromChrono(event);
		return;
	case 'crono_rec': // reconocimiento de pista
		// si crono esta activo, ignora
		if (cra.Chrono('started')) c_reconocimiento.stop();
		// si no, vemos si hay que arrancar o parar
		if (parseInt(event['start'])!=0)  { // arrancar crono
			c_reconocimiento.reset(event['start']);
			c_reconocimiento.start();
		} else {
			c_reconocimiento.stop();
		}
		return;
	case 'crono_ready': // el crono avisa de conectado/escuchando
		return;
	case 'cancelar': // operador pulsa cancelar en tablet
		c_clearData(event);
		return;
	case 'aceptar':	// operador pulsa aceptar en tablet
		return;
    case 'info':	// click on user defined tandas
        return;
	case 'camera': // video source for live stream has changed
		return;
	case 'command': // videowall remote control
        return;
	case 'reconfig': // reload configuration from server
		loadConfiguration();
		return;
	default:
		alert("Unknow Event type: "+event['Type']);
		return;
	}
}
