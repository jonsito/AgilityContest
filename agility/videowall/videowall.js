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
 * Obtiene la informacion de la prueba para cabecera y pie de pagina
 * @param {object} evt Event data
 * @param {function(event,data)} what to do with retrieved event and data
 */
function vw_updateWorkingData(evt,callback) {
    $.ajax( {
        type: "GET",
        dataType: 'json',
        url: "/agility/server/web/videowall.php",
        data: {
            Operation: 'infodata',
            Prueba: evt.Prueba,
            Jornada: evt.Jornada,
            Manga: evt.Manga,
            Tanda: evt.Tanda,
            Session: workingData.sesion
        },
        success: function(data,status,jqxhr) {
            // common updates on every videowall:
            setPrueba(data.Prueba);
            setJornada(data.Jornada);
            setManga(data.Manga);
            // and finally invoke callback
            callback(evt,data);
        }
    });
}

/**
 * update info on prueba, jornada...
 * set up header and footer
 * @param {object} evt received 'init' event
 * @param {object} data data associated with event
 */
function vw_updateDataInfo(evt,data) {

    // update header
    var infoprueba='Prueba: ' + data.Prueba.Nombre+" <br /> Jornada: "+ data.Jornada.Nombre;
    $('#vw_header-infoprueba').html(infoprueba);
    $('#vw_header-logo').attr('src','/agility/images/logos/'+data.Club.Logo);
    // this should be done in callback, as content is window dependent
    // var infomanga=(typeof(data.Manga.Nombre)==='undefined')?'':data.Manga.Nombre
    // $('#vw_header-infomanga').html(data.Manga.Nombre);

    // update footer
    var logo=nombreCategorias[workingData.federation]['logo'];
    var logo2=nombreCategorias[workingData.federation]['logo2'];
    var url=nombreCategorias[workingData.federation]['url'];
    var url2=nombreCategorias[workingData.federation]['url2'];
    $('#vw_footer-footerData').load("/agility/videowall/vw_footer.php",{},function(response,status,xhr){
        $('#vw_footer-logoFederation').attr('src','/agility/images/logos/'+logo);
        $('#vw_footer-urlFederation').attr('href',url);
        $('#vw_footer-logoFederation2').attr('src','/agility/images/logos/'+logo2);
        $('#vw_footer-urlFederation2').attr('href',url2);
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
		url: "/agility/server/web/videowall.php",
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
		url: "/agility/server/web/videowall.php",
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
 * @param {object} evt received event
 * @param {object} data environment info
 */
function vw_updateOrdenSalida(evt,data) {
    if (data.Tanda.ID==0) return; // no data yet
    // update header info
    var infomanga=(typeof(data.Tanda.Nombre)==='undefined')?'':data.Tanda.Nombre;
    $('#vw_header-infomanga').html(data.Tanda.Nombre);
    // and update orden salida related to this tanda
    $('#vw_ordensalida-datagrid').datagrid('load',{
        Operation: 'getDataByTanda',
        Prueba: data.Prueba.ID,
        Jornada:data.Jornada.ID,
        Manga:data.Manga.ID,
        Sesion: 0, // don't extract info from session, just use ours
        ID:  data.Tanda.ID // Tanda ID
    });
}

function vwc_processCombinada(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
	switch (event['Type']) {
	case 'null':		// null event: no action taken
		return; 
	case 'init':		// operator starts tablet application
        setupByJornada(event['Pru'],event['Jor']); // use shortname to ensure data exists
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
        setupByJornada(event['Pru'],event['Jor']); // use shortname to ensure data exists
		vwls_showOSD(0); 	// activa visualizacion de OSD
		return;
	case 'open':		// operator select tanda reset info
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
        setupByJornada(event['Pru'],event['Jor']); // use shortname to ensure data exists
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
        setupByJornada(event['Pru'],event['Jor']); // use shortname to ensure data exists
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
 * Tracks Tanda selection changes ('open' event) and updates related screen
 */
function vw_procesaOrdenSalida(id,evt) {
    var event=parseEvent(evt); // remember that event was coded in DB as an string
    event['ID']=id; // fix real id on stored eventData
    switch (event['Type']) {
        case 'null': // null event: no action taken
            return;
        case 'init': // operator starts tablet application
            vw_updateWorkingData(event,function(evt,data){
                vw_updateDataInfo(evt,data);
                $('#vw_header-infomanga').html("(Manga no definida)");
                // clear datagrid
                $('#vw_ordensalida-datagrid').datagrid('loadData', {"total":0,"rows":[]});
            });
            return;
        case 'open': // operator select tanda
            vw_updateWorkingData(event,function(evt,data){
                vw_updateDataInfo(evt,data);
                vw_updateOrdenSalida(evt,data);
            });
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
            return;
        case 'cancelar': // operador pulsa cancelar
            return;
        case 'info':	// click on user defined tandas
            return;
    }
}