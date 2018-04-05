/*
livestream.js.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

<?php
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
$config =Config::getInstance();
$custom_layout=json_encode(json_decode(file_get_contents(__DIR__."/osd_layout.json")));
?>

/**
 * Generic event handler for VideoWall and LiveStream screens
 * Every screen has a 'eventHandler' table with pointer to functions to be called
 * @param id {number} Event ID
 * @param evt {object} Event data
 */
function livestream_eventManager(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
    ac_config.pending_events[event['Type']]=event; // store received event
	var time=event['Value'];
	if (typeof(eventHandler[event['Type']])==="function") {
		setTimeout(function() {
			eventHandler[event['Type']](event,time);
		}, ac_config.ls_evtdelay*1000);
	}
}

function livestream_handlePendingEvent(event) {

    var running=$('#cronometro').Chrono('started');

    function ls_handleCallEvent() {
        // crono parado y perro pendiente: mostrar
        vwls_showResultsInfo(running);
        vwls_showData(ac_config.pending_events['llamada']);
    }

    switch(event['Type']) {
        case 'llamada':
            var flag=false;
            var eli=false;
            if (ac_config.pending_events['aceptar']===null) eli=false;
            else eli=(parseInt(ac_config.pending_events['aceptar']['Eliminado'])===1);
            // en pruebas equipos conjunta, se procesa como siempre
            // PENDING
            // si crono parado se procesa como siempre
            if (!running) flag=true;
            // si crono corriendo pero ultimo no eliminado se procesa como siempre
            // esto ocurre cuando se da aceptar o se selecciona directamente un perro
            // para que el resultado quede como "pendiente"
            if (running && !eli) flag=true;
            // si crono corriendo pero eliminado, se retiene la llamada
            if (running && eli) flag=false;
            if (flag) { // procesamos evento de llamada y luego lo borramos
                ls_handleCallEvent();
                ac_config.pending_events['llamada']=null;
                // eliminamos ultimo evento "aceptar"
                ac_config.pending_events['aceptar']=null;
            }
            break;
        case 'stop':
        case 'crono_stop':
        case 'reset':
            // si llamada pendiente se procesa la llamada
            if (ac_config.pending_events['aceptar']!==null) ls_handleCallEvent();
            // eliminamos ultimo evento llamada
            ac_config.pending_events['aceptar']=null;
            break;
        default: console.log("unexpected call to handle pending event: "+event['Type']);
    }
}

function vwls_enableOSD(val) {
	var title=document.title;
	var str="-";
	var dly=toFixedT(ac_config.ls_evtdelay,1);
	if (val==0) {
		str=" - OSD:OFF - delay: "+dly;
		$('#vwls_common').css('display','none');
		$('#osd_common').css('display','none'); // osd requires special namming due to layers
	} else {
		str=" - OSD:ON - delay: "+dly;
		$('#vwls_common').css('display','initial');
		$('#osd_common').css('display','initial');  // osd requires special namming due to layers
	}
	document.title=title.replace(/ -.*/,"")+str;
}

function vwls_setDelayOSD(dly) {
    dly=parseFloat(dly);
    if (dly<0.0) dly=0.0; if (dly>5.0) dly=5.0;
    // store new osd delay in configuration
    ac_config.ls_evtdelay=dly;
    // mark change in display
    document.title = document.title.replace(/delay: .*/,"delay: "+toFixedT(dly,1));
}

function vwls_setAlphaOSD(alpha,tableid) {
    alpha=parseFloat(alpha);
    if (alpha<0.0) alpha=0.0; if (alpha>1.0) alpha=1.0;
    // store new value
    ac_config.ls_alpha=alpha;
    // change css to activate new value
    // PENDING
    var a=parseFloat(ac_config.ls_alpha);
    var res=[127,127,127,a];
    if (typeof(tableid)==="undefined") return;
    if (tableid==="OSD") { // live video
        data=JSON.parse('<?php echo $custom_layout?>');
        if (parseInt(ac_config.ls_infoposition)!==3) res=data.InfoManga.bgcolor;
        $('#vwls_InfoManga').css('background-color',"rgba("+res[0]+","+res[1]+","+res[2]+","+res[3]+")");
        if (parseInt(ac_config.ls_dataposition)!==4) res=data.Resultados.bgcolor;
        $('#vwls_Resultados').css('background-color',"rgba("+res[0]+","+res[1]+","+res[2]+","+res[3]+")");
        if (parseInt(ac_config.ls_dataposition)!==4) res=data.Datos.bgcolor;
        $('#vwls_Datos').css('background-color',"rgba("+res[0]+","+res[1]+","+res[2]+","+res[3]+")");
    } else { // other livestream screens
        var rgb1=hexToRGB(ac_config.ls_rowcolor1);
        var rgb2=hexToRGB(ac_config.ls_rowcolor2);
        $(tableid+" .datagrid-btable tr:odd").css('background-color',"rgba("+rgb1.r+","+rgb1.g+","+rgb1.b+","+a+")");
        $(tableid+" .datagrid-btable tr:even").css('background-color',"rgba("+rgb2.r+","+rgb2.g+","+rgb2.b+","+a+")");
    }
}

function vwls_showRoundInfo(val) {
	var disp=(val==0)?'none':'initial';
	if (parseInt(ac_config.ls_infoposition)==0) disp='none';
	else $('#vwls_mangasInfo').css('display',disp);
}

function vwls_showCompetitorInfo(val) {
	var disp=(val==0)?'none':'initial';
	if (!ac_config.dogInRing) disp='none'; // to avoid show info data in course walk changes
	$('#vwls_competitorInfo').css('display',disp);
}

function vwls_showResultsInfo(val) {
	var disp=(val==0)?'none':'initial';
	if (parseInt(ac_config.ls_dataposition)==0) disp='none';
	if (!ac_config.dogInRing) disp='none'; // to avoid show info data in course walk changes
	$('#vwls_resultadosInfo').css('display',disp);
}

function vwls_keyBindings() {
	// capture <space> key to switch OSD on/off
	$(document).keydown(function(e) {
		var dly=parseFloat(ac_config.ls_evtdelay);
		var kcode=e.which;
		var state = ( document.title.indexOf("OSD:ON") >= 0) ? true : false;
		switch (parseInt(kcode)) {
			case 38: // key up
				dly+=0.1;
				if (ac_config.ls_evtdelay<4.9) ac_config.ls_evtdelay=(dly>5)?5:dly;
				vwls_enableOSD((state) ? 1 : 0);
				break;
			case 40: // key down
				dly-=0.1
				if (ac_config.ls_evtdelay>0.1)  ac_config.ls_evtdelay=(dly<0)?0:dly;
				vwls_enableOSD((state) ? 1 : 0);
				break;
			case 32: // space
				setTimeout(function () {
					vwls_enableOSD((state) ? 0 : 1);
				}, 0);
				break;
		}
		return true; // to allow continue event chain
	});
}

function vwls_showData(data) {
	var perro=$('#vwls_Perro').html();
	var vwls_tiempo=$('#vwls_Tiempo');
	var dorsal=data['Dorsal'];
	var numero=data['Numero'];
	var celo=parseInt(data['Celo']);

	function fillForm(res) {
		$('#vwls_Numero').html(numero);
		$('#vwls_Dorsal').html(dorsal );
		$('#vwls_Perro').html(res["ID"]);

		// take care on Test dog and intl contests
		var perro=res['Nombre']; // may contain "Test dog"
		if (perro!=="<?php _e('Test dog');?>") perro= useLongNames()? perro+" - "+res['NombreLargo'] : perro;
		$('#vwls_Nombre').html(perro.substr(0,40)); // limit length to avoid overflow assigned space
		$('#vwls_Logo').attr("src","../images/logos/getLogo.php?Federation="+res['Federation']+"&Logo="+res['LogoClub']);
		$('#vwls_NombreGuia').html(res["NombreGuia"]);
		$('#vwls_Cat').html(res["Categoria"]);
		$('#vwls_Categoria').html(toLongCategoria(res["Categoria"],res['Federation']));
		// hide "Grado" Information if not applicable
		$('#vwls_Grado').html(hasGradosByJornada(workingData.datosJornada)?res["NombreGrado"]:"");
		// on Team events, show Team info instead of Club
		$('#vwls_NombreClub').html((isJornadaEquipos(null))?workingData.teamsByJornada[data["Equipo"]].Nombre:res["NombreClub"]);
		$('#vwls_Celo').html((celo==1)?'<span class="blink">Celo</span>':'');
	}

	if (perro!==data['Perro']) {
		// if datos del participante han cambiado actualiza
		if (data['Nombre']==="<?php _e('Test dog'); ?>") { // perro en blanco???
			data.Equipo=Object.keys(workingData.teamsByJornada)[0]; // default team goes first
			// en caso de perro en blanco, usa datos del perro por defecto
			fillForm({
				Nombre: 	"<?php _e('Test dog'); ?>",
				NombreLargo:"",
				ID:			0,
				LogoClub:	"agilitycontest.png",
				Federation:	"0",
				NombreGuia:	"",
				Categoria:	"-",
				NombreGrado:"-",
				NombreClub:	""
			})
		} else { // no perro en blanco. 
			// Busca datos adicionales
			$.ajax({
				type: "GET",
				url: "../server/database/dogFunctions.php",
				data: {
					'Operation' : 'getbyidperro',
					'ID'	: data['Perro'],
					'Jornada': workingData.jornada
				},
				async: true,
				cache: false,
				dataType: 'json',
				success: function(res){
					if (typeof(res.errorMsg)==="undefined") fillForm(res);
					else $.messager.show({title:"error",msg:res.errorMsg,timeout:5000,showType:'slide'});
				},
				error: function(XMLHttpRequest,textStatus,errorThrown) {
					$.messager.show({title:"vwls_showData() error",msg:""+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus + " " + errorThrown,timeout:5000,showType:'slide'});
				}
			})
		}
	}
	// actualiza resultados del participante
	$('#vwls_Faltas').html(data["Faltas"]);
	$('#vwls_Tocados').html(data["Tocados"]);
	$('#vwls_FaltasTocados').html( parseInt(data["Faltas"]) + parseInt(data["Tocados"]) );
	// $('#vwls_Tocados').html(data["Tocados"]);
	$('#vwls_Rehuses').html(data["Rehuses"]);
	$('#vwls_TIntermedio').html(data["TIntermedio"]);
	var e=parseInt(data["Eliminado"]);
	if (e>=0) {
		$('#vwls_Eliminado').html(e);
		$('#vwls_EliminadoLbl').html((e==0)?'':'<span class="blink" style="color:red"><?php _e('Elim');?>.</span>');
	}
	var n=parseInt(data["NoPresentado"]);
	if (n>=0) {
		$('#vwls_NoPresentado').html(n);
		$('#vwls_NoPresentadoLbl').html((n==0)?'':'<span class="blink" style="color:red"><?php _e('NoPr');?>.</span>');
	}
	vwls_tiempo.html(data["Tiempo"]);
}

function vwls_displayToBeFirst(perro) {
    if (typeof(workingData.nombreRonda)==="undefined") return;
    if (workingData.nombreRonda==="") return; // no info on round yet. cannot evaluate
	$.ajax({
		type: 'GET',
		url: "../server/database/clasificacionesFunctions.php",
		dataType: 'json',
		data: {
			Operation: 'clasificacionIndividual',
			Prueba: workingData.prueba,
			Jornada: workingData.jornada,
			Manga: workingData.manga,
			Mode: workingData.datosRonda.Mode,
			Manga1: workingData.datosRonda.Manga1,
			Manga2: workingData.datosRonda.Manga2,
			Rondas: workingData.datosRonda.Rondas,
			Perro: perro
		},
		success: function (dat) {
			if (typeof(dat.errorMsg)!=="undefined") {
				console.log("vwls_displayToBeFirst(): " + dat.errorMsg);
				return;
			}
			if ( typeof(dat.current) === "undefined" ) return;
			if ( dat.current.toBeFirst==="" ) return;
			$("#vwls_PuestoLbl").html("&lt"+toFixedT(parseFloat(dat.current.toBeFirst),ac_config.numdecs));
		}
	});
}

/**
 * evaluate and display position for this dog
 * @param {boolean} flag: true:evaluate, false:clear
 * @param {float} time datatime from chronometer
 */
function vwls_displayPuesto(flag,time) {
	// if requested, turn off data
	var perro=$('#vwls_Perro').text();
	if (!flag || (perro==0) ) { $('#vwls_PuestoLbl').html(''); return; }
	// use set timeout to make sure data are already refreshed
	setTimeout(function(){
		// phase 1 retrieve results
		// use text() instead of html() avoid extra html code
		var datos= {
			'Perro':	perro,
			'Categoria':$('#vwls_Cat').text(),
			'Grado':	$('#vwls_Grado').text(),
			'Faltas':	$('#vwls_Faltas').text(),
			'Tocados':	$('#vwls_Tocados').text(),
			'Rehuses':	$('#vwls_Rehuses').text(),
			'Eliminado':$('#vwls_Eliminado').text(),
			'NoPresentado':$('#vwls_NoPresentado').text(),
			'Tiempo':	time
		};
		// phase 2: do not call server if eliminado or not presentado. Also don't display anything (already done)
		if ( (datos.NoPresentado=="1") || (datos.Eliminado=="1")) { $('#vwls_PuestoLbl').html(''); return; }
		// phase 3: call server to evaluate partial result position
		getPuestoFinal(datos,function(data,resultados){
			$('#vwls_PuestoLbl').html('- '+Number(resultados.puesto).toString()+' -');
		});
	},0);
}

function livestream_switchConsole(event) {
    var data=event['Value'].split(':');// ring : view : mode
    var url="../livestream/index.php?Ring="+data[0]+"&View="+data[1]+"&Mode="+data[2]+"&Timeout=2&SessionName="+ac_clientOpts.SessionName;
    window.location.replace(url);
}

function livestream_showMessage(event) {
    var msg=event['Value'];
    var timeout=event['Timeout'];
    $.messager.show({
        title:' ', // empty title
        msg:msg,
        showType:'fade',
        timeout: timeout,
        style:{ right:'', bottom:'' }
    });
}
