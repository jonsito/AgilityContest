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
            if (typeof(callback)==='function') callback(evt,data);
        }
    });
}

/**
 * Al recibir 'init' ajustamos el modo de visualización de la pantalla
 * de resultados parciales para individual o equipos
 * @param {object} evt Evento recibido. Debe ser de tipo init
 * @param data informacion de la prueba,jornada, my manga
 */
function vw_initParcialesDatagrid(evt,data) {
    var team=false;
    var dg=$('#vw_parciales-datagrid');
    if (parseInt(data.Jornada.Equipos3)==1) team=true;
    if (parseInt(data.Jornada.Equipos4)==1) team=true;
    // clear datagrid as data no longer valid
    if (team){
        dg.datagrid({
            view: gview,
            groupField: 'NombreEquipo',
            groupFormatter: formatTeamResults
        });
        dg.datagrid('hideColumn',"LogoClub");
        dg.datagrid('hideColumn',"Grado");
    } else {
        dg.datagrid({view:$.fn.datagrid.defaults.view});
        dg.datagrid('showColumn',"LogoClub");
        dg.datagrid('showColumn',"Grado");
    }
    dg.datagrid('loadData', {"total":0,"rows":[]});
    dg.datagrid('fitColumns');
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
    $('#vw_header-ring').html(data.Sesion.Nombre);

    // this should be done in callback, as content is window dependent
    // actualiza informacion de manga
    var infomanga=(typeof(data.Manga.Nombre)==='undefined')?'':data.Manga.Nombre;
    $('#vwls_Manga').html(infomanga);

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
 * Actualiza el datagrid de llamada a pista con los datos recibidos
 * @param {object} evt event
 * @param {object} data system status data info
 */
function vw_updateLlamada(evt,data) {
    $.ajax( {
        type: "GET",
        dataType: 'json',
        url: "/agility/server/web/videowall.php",
        data: {
            Operation: 'llamada',
            Pendientes: 25,
            Session: workingData.sesion
        },
        success: function(dat,status,jqxhr) {
            $('#vw_llamada-datagrid').datagrid('loadData',dat);
        }
    });
}

/**
 * Actualiza el datagrid de resultados con los datos asociados al evento recibido
 * @param {object} evt event
 * @param {object} data system status data info
 */
function vw_updateParciales(evt,data) {
    // en lugar de invocar al datagrid, lo que vamos a hacer es
    // una peticion ajax, para obtener a la vez los datos tecnicos de la manga
    // y de los jueces
    workingData.teamCounter=1; // reset team's puesto counter
    var mode=getMangaMode(data.Prueba.RSCE,data.Manga.Recorrido,data.Tanda.Categoria);
    var modestr=getMangaModeString(data.Prueba.RSCE,data.Manga.Recorrido,data.Tanda.Categoria);
    $.ajax({
        type:'GET',
        url:"/agility/server/database/resultadosFunctions.php",
        dataType:'json',
        data: {
            Operation:	'getResultados',
            Prueba:		data.Prueba.ID,
            Jornada:	data.Jornada.ID,
            Manga:		data.Manga.ID,
            Mode:       mode
        },
        success: function(dat) {
            // informacion de la manga
            var str=dat['manga'].TipoManga + " - " + modestr;
            $('#vw_header-infomanga').text(str);
            $('#vw_parciales-Juez1').text((dat['manga'].Juez1<=1)?"":'Juez 1: ' + dat['manga'].NombreJuez1);
            $('#vw_parciales-Juez2').text((dat['manga'].Juez2<=1)?"":'Juez 2: ' + dat['manga'].NombreJuez2);
            // datos de TRS
            $('#vw_parciales-Distancia').text(dat['trs'].dist + 'm.');
            $('#vw_parciales-Obstaculos').text(dat['trs'].obst);
            $('#vw_parciales-TRS').text(dat['trs'].trs + 's.');
            $('#vw_parciales-TRM').text(dat['trs'].trm + 's.');
            $('#vw_parciales-Velocidad').text( dat['trs'].vel + 'm/s');
            // actualizar datagrid
            $('#vw_parciales-datagrid').datagrid('loadData',dat);
        }
    });
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

function vw_autoscroll(id,target) {
    $(id).animate({
        scrollTop: $(target).offset().top
    }, 1000);
}

function vw_procesaCombinada(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
	switch (event['Type']) {
	case 'null':		// null event: no action taken
		return;
    case 'init': // operator starts tablet application
        $('#vw_header-infoprueba').html("Cabecera");
        $('#vw_header-infomanga').html("(Manga no definida)");
        vw_updateWorkingData(event,function(e,d){
            vw_updateDataInfo(e,d);
            vw_initParcialesDatagrid(e,d);
            vw_updateLlamada(e,d);
        });
        return;
    case 'open': // operator select tanda
        vw_updateWorkingData(event,function(e,d){
            vw_updateDataInfo(e,d);
            vw_updateParciales(e,d);
            vw_updateLlamada(e,d);
        });
        return;
	case 'datos':		// actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		vwls_updateData(event);
		return;
	case 'llamada':		// operador abre panel de entrada de datos
		return;
	case 'salida':		// juez da orden de salida ( crono 15 segundos )
		return;
	case 'start':	// value: timestamp
		return;
	case 'stop':	// value: timestamp
		return;
	case 'crono_start': // arranque crono electronico
		return;
	case 'crono_int':	// tiempo intermedio crono electronico
		return;
	case 'crono_stop':	// parada crono electronico
		return;
	case 'aceptar':		// operador pulsa aceptar
        vw_updateWorkingData(event,function(e,d){
            vw_updateLlamada(e,d);
            vw_updateParciales(e,d);
        });
		return;
	case 'cancelar':	// operador pulsa cancelar
        vw_updateWorkingData(event,function(e,d){
            vw_updateLlamada(e,d);
            vw_updateParciales(e,d);
        });
		return;
    case 'info':	// click on user defined tandas
        return;
	}
}

function vwls_processLiveStream(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
    var time=event['Value'];
	switch (event['Type']) {
	case 'null':		// null event: no action taken
		return; 
	case 'init':		// operator starts tablet application
        setupByJornada(event['Pru'],event['Jor']); // use shortname to ensure data exists
		vwls_showOSD(0); 	// activa visualizacion de OSD
		return;
        case 'open':		// operator select tanda reset info
        vw_updateWorkingData(event,function(e,d){vw_updateDataInfo(e,d);});
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
		vwls_cronoManual('start',time);
		return;
	case 'stop':	// value: timestamp
		myCounter.stop(); 
		vwls_cronoManual('stop',time);
		return;
	case 'crono_start': // arranque crono electronico
		myCounter.stop(); 
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_cronoManual('start',time);
		return;
	case 'crono_int':	// tiempo intermedio crono electronico
        $('#cronomanual').Chrono('pause'); setTimeout(function(){$('#cronomanual').Chrono('resume');},5000);
		return;
	case 'crono_stop':	// parada crono electronico
		myCounter.stop(); 
		vwls_cronoManual('stop',time);
		return;
	case 'aceptar':		// operador pulsa aceptar
		vwls_cronoManual('stop',event['Value']);  // nos aseguramos de que los cronos esten parados
		// vwls_showData(event); // actualiza pantall liveStream
		return;
	case 'cancelar':	// operador pulsa cancelar
		vwls_cronoManual('stop',time);
		vwls_cronoManual('reset');
		vwls_showOSD(0); // apaga el OSD
		return;
    case 'info':	// click on user defined tandas
        return;
	}
}

function vw_procesaLlamada(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
	switch (event['Type']) {
	case 'null': // null event: no action taken
		return; 
	case 'init': // operator starts tablet application
        vw_updateWorkingData(event,function(e,d){
            $('#vw_header-infoprueba').html("Cabecera");
            vw_updateDataInfo(e,d);
        });
		// TODO: muestra pendientes desde primera tanda
		return;
	case 'open': // operator select tanda:
        vw_updateWorkingData(event,function(e,d){
            vw_updateDataInfo(e,d);
            vw_updateLlamada(e,d);
        });
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
        vw_updateWorkingData(event,vw_updateLlamada);
		return;
	case 'cancelar': // operador pulsa cancelar
        vw_updateWorkingData(event,vw_updateLlamada);
		return;
    case 'info':	// click on user defined tandas
        return;
	}
}

function vw_procesaParciales(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
	switch (event['Type']) {
	case 'null': // null event: no action taken
		return; 
	case 'init': // operator starts tablet application
        vw_updateWorkingData(event,function(e,d){
            $('#vw_header-infoprueba').html("Cabecera");
            $('#vw_header-infomanga').html("(Manga no definida)");
            vw_updateDataInfo(e,d);
            vw_initParcialesDatagrid(e,d);
        });
        return;
	case 'open': // operator select tanda:
        vw_updateWorkingData(event,function(e,d){
            vw_updateDataInfo(e,d);
            vw_updateParciales(e,d);
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
        vw_updateWorkingData(event,vw_updateParciales);
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