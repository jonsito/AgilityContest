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

<?php
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
$config =Config::getInstance();
?>

/**
 * Presenta el logo en pantalla
 * @param {int} val nombre delo logo
 * @param {Object} row datos de la fila
 * @param {int} idx indice de la fila
 * @returns {string} texto html a imprimir
 */
function formatLogoVideoWall(val,row,idx) {
    // TODO: no idea why idx:0 has no logo declared
    if (typeof(val)==='undefined') return '<img height="30" alt="empty.png" src="/agility/images/logos/empty.png"/>';
    return '<img height="30" alt="'+val+'" src="/agility/images/logos/'+val+'"/>';
}

/**
 * Obtiene la informacion de la prueba para cabecera y pie de pagina
 * @param {object} evt Event data
 * @param {function} callback what to do with retrieved event and data
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
			setTanda(data.Tanda);
            // and finally invoke callback
            if ( typeof(callback)==='function' ) callback(evt,data);
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
 * Al recibir 'init' ajustamos el modo de visualización de la pantalla
 * de resultados parciales para individual o equipos
 * @param {object} evt Evento recibido. Debe ser de tipo init
 * @param data informacion de la prueba,jornada, my manga
 */
function vwcp_initParcialesDatagrid(evt,data) {
	var team=false;
	var dg=$('#vwcp_parciales-datagrid');
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
    var infoprueba='<?php _e('Contest'); ?>'+': ' + data.Prueba.Nombre+' <br />'+'<?php _e('Journey'); ?>'+': '+ data.Jornada.Nombre;
    $('#vw_header-infoprueba').html(infoprueba);
    $('#vw_header-ring').html(data.Sesion.Nombre);
	// on international competitions, use federation Organizer logo
	var logo='/agility/images/logos/'+data.Club.Logo;
	if ( (data.Club.logo==="") || isInternational(data.Prueba.RSCE)) {
		logo=ac_fedInfo[data.Prueba.RSCE].OrganizerLogo
	}
	$('#vw_header-logo').attr('src',logo);

    // this should be done in callback, as content is window dependent
    // actualiza informacion de manga
    var infomanga=(typeof(data.Manga.Nombre)==='undefined')?'':data.Manga.Nombre;
    $('#vwls_Manga').html(infomanga);

    // update footer
	var logo=ac_fedInfo[workingData.federation].Logo;
	var logo2=ac_fedInfo[workingData.federation].ParentLogo;
	var url=ac_fedInfo[workingData.federation].WebURL;
	var url2=ac_fedInfo[workingData.federation].ParentWebURL;
	$('#vw_footer-footerData').load("/agility/videowall/vw_footer.php",{},function(response,status,xhr){
		$('#vw_footer-logoFederation').attr('src',logo);
		$('#vw_footer-urlFederation').attr('href',url);
		$('#vw_footer-logoFederation2').attr('src',logo2);
		$('#vw_footer-urlFederation2').attr('href',url2);
	});
}

/**
 * New combined videowall Information panel handler for header and footer:
 * update info on prueba, jornada set up header and footer
 * @param {object} evt received 'init' event
 * @param {object} data data associated with event
 */
function vwc_updateDataInfo(evt,data) {
	// update header
	$('#vwc_header-infoprueba').html(data.Prueba.Nombre);
	$('#vwc_header-infojornada').html(data.Jornada.Nombre);
	$('#vwc_header-ring').html(data.Sesion.Nombre);
	// on international competitions, use federation Organizer logo
	var logo='/agility/images/logos/'+data.Club.Logo;
	if ( (data.Club.logo==="") || isInternational(data.Prueba.RSCE)) {
		logo=ac_fedInfo[data.Prueba.RSCE].OrganizerLogo
	}
	$('#vwc_header-logo').attr('src',logo);

	// this should be done in callback, as content is window dependent
	// actualiza informacion de manga
	var infomanga=(typeof(data.Manga.Nombre)==='undefined')?'':data.Manga.Nombre;
	$('#vwls_Manga').html(infomanga);

	// update footer
	var logo=ac_fedInfo[workingData.federation].Logo;
	var logo2=ac_fedInfo[workingData.federation].ParentLogo;
	var url=ac_fedInfo[workingData.federation].WebURL;
	var url2=ac_fedInfo[workingData.federation].ParentWebURL;
	$('#vw_footer-footerData').load("/agility/videowall/vw_footer.php",{},function(response,status,xhr){
		$('#vw_footer-logoFederation').attr('src',logo);
		$('#vw_footer-urlFederation').attr('href',url);
		$('#vw_footer-logoFederation2').attr('src',logo2);
		$('#vw_footer-urlFederation2').attr('href',url2);
	});
}

function vwls_showOSD(val) {
	if (val==0) $('#vwls_common').css('display','none');
	else $('#vwls_common').css('display','initial');
}

function vwls_updateData(data) {
    // some versions of Safari and Chrome doesn't properly take care on html dom changes
    // so stupid .hide().show(0) is needed to take care on this
	if (data["Faltas"]!=-1) $('#vwls_Faltas').html(data["Faltas"]).hide().show(0);
	if (data["Tocados"]!=-1) $('#vwls_Tocados').html(data["Tocados"]).hide().show(0);
	if (data["Rehuses"]!=-1) $('#vwls_Rehuses').html(data["Rehuses"]).hide().show(0);
	if (data["Tiempo"]!=-1) $('#vwls_Tiempo').html(data["Tiempo"]).hide().show(0);
	if (data["Eliminado"]==1)	$('#vwls_Tiempo').html('<span class="blink" style="color:red">Elim.</span>').hide().show(0);
	if (data["NoPresentado"]==1) $('#vwls_Tiempo').html('<span class="blink" style="color:red">N.P.</span>').hide().show(0);
}

function vwls_showData(data) {
	var perro=$('#vwls_Perro').html();
	var vwls_tiempo=$('#vwls_Tiempo');
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
				$('#vwls_Dorsal').html("<?php _e('Dorsal');?>: "+dorsal );
				$('#vwls_Nombre').html(res["Nombre"]);
				$('#vwls_NombreGuia').html("<?php _e('Handler');?>: "+res["NombreGuia"]);
                $('#vwls_Categoria').html("<?php _e('Cat');?>: "+toLongCategoria(res["Categoria"],res['Federation']));
                // hide "Grado" Information if not applicable
                $('#vwls_Grado').html(hasGradosByJornada(workingData.datosJornada)?res["NombreGrado"]:"");
                // on Team events, show Team info instead of Club
                var eq=workingData.teamsByJornada[data["Equipo"]].Nombre;
                // como en el videowall no tenemos datos de la jornada, lo que hacemos es
                // contar el numero de equipos de esta para saber si es prueba por equipos o no
                $('#vwls_NombreClub').html((Object.keys(workingData.teamsByJornada).length>1)?"<?php _e('Eq');?>: "+eq:"<?php _e('Club');?>: "+res["NombreClub"]);
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
	vwls_tiempo.html(data["Tiempo"]);
	if (data["Eliminado"]==1)	 vwls_tiempo.html('<span class="blink" style="color:red">Elim.</span>');
	if (data["NoPresentado"]==1) vwls_tiempo.html('<span class="blink" style="color:red">N.P.</span>');
}

var myCounter = new Countdown({  
    seconds:15,  // number of seconds to count down
    onUpdateStatus: function(tsec){
		$('#vwls_Tiempo').html((tsec/10).toFixed(1));
	}, // callback for each tenth of second
    // onCounterEnd: function(){  $('#vwls_Tiempo').html('<span class="blink" style="color:red">-out-</span>'); } // final action
    onCounterEnd: function(){ /* let the tablet to tell us what to do */ }
});

/**
 * Maneja el cronometro manual
 * @param {string} oper 'start','stop','pause','resume','reset'
 * @param {int} tstamp timestamp mark
 */
function vwls_cronometro(oper,tstamp) {
	myCounter.stop();
	$('#cronometro').Chrono(oper,tstamp);
}

/**
 * Actualiza el datagrid de llamada a pista con los datos recibidos
 * @param {object} evt event
 * @param {object} data system status data info
 */
function vw_updateLlamada(evt,data) {
	$.ajax({
		type: "GET",
		dataType: 'json',
		url: "/agility/server/web/videowall.php",
		data: {
			Operation: 'llamada',
			Pendientes: 25,
			Session: workingData.sesion
		},
		success: function (dat, status, jqxhr) {
			$('#vw_llamada-datagrid').datagrid('loadData', dat);
		}
	});
}

/**
 * Evalua y rellena los datos de penalizacion, calificacion y puesto
 * de un perro dado
 * @param {array} items array de datos Datos de perro a evaluar
 */
function vwc_evalResultados(items) {
	// extraemos distancia, trs y trm. con parseInt eliminamos textos extras
	var dist=parseInt($('#vwcp_parciales-Distancia').text());
	var trs=parseInt($('#vwcp_parciales-TRS').text());
	var trm=parseInt($('#vwcp_parciales-Distancia').text());
	for (var idx=0;idx<items.length;idx++) {
		var dat=items[idx];
		if (dat.Orden=="") { // entrada vacia
			dat.PTiempo=400;
			dat.PRecorrido=0;
			dat.Penalizacion=400;
			dat.Calificacion="";
			dat.Puesto="";
			continue;
		}
		// evaluamos velocidad
		if (dat.Tiempo==0) dat.Velocidad=0;
		else dat.Velocidad=parseFloat(dist)/parseFloat(dat.Tiempo);
		// evaluamos penalizacion
		dat.PRecorrido=( 5*dat.Faltas + 5*dat.Rehuses + 5*dat.Tocados + 100*dat.Eliminado + 200*dat.NoPresentado );
		if (dat.Tiempo<=trs) dat.PTiempo=0;
		else if (dat.Tiempo>=trm) dat.PTiempo=100;
		else dat.PTiempo=dat.Tiempo-trs;
		dat.Penalizacion=dat.PRecorrido+dat.PTiempo;
		// evaluamos calificacion
		if (dat.Penalizacion==0.0) dat.Calificacion="<?php _e('Ex P');?>";
		if (dat.Penalizacion>=0.0) dat.Calificacion="<?php _e('Exc');?>";
		if (dat.Penalizacion>=6.0) dat.Calificacion="<?php _e('V.G.');?>";
		if (dat.Penalizacion>=16.0) dat.Calificacion="<?php _e('Good');?>";
		if (dat.Penalizacion>=26.0) dat.Calificacion="<?php _e('N.C.');?>";
		if (dat.Penalizacion>=100.0) dat.Calificacion="<?php _e('Elim');?>";
		if (dat.Penalizacion>=200.0) dat.Calificacion="<?php _e('N.P.');?>";
		// evaluamos posicion
		var results=$('#vwcp_parciales-datagrid').datagrid('getData')['rows'];
		// alert("results:\n"+JSON.stringify(results));
		for (var n=0; n<results.length;n++) {
			if(results[n].Perro==dat.Perro) {dat.Puesto=results[n].Puesto; break;}
		}
	}
}

/**
 * Actualiza el datagrid de llamada a pista con los datos recibidos
 * @param {object} evt event
 * @param {object} data system status data info
 */
function vwc_updateLlamada(evt,data) {
	$.ajax( {
		type: "GET",
		dataType: 'json',
		url: "/agility/server/web/videowall.php",
		data: {
			Operation: 'window',
			Before: 4,
			After: 15,
			Perro: parseInt(evt['Dog']),
			Session: workingData.sesion
		},
		success: function(dat,status,jqxhr) {
			// componemos ventana de llamada
			$('#vwc_llamada-datagrid').datagrid('loadData',dat['after']).datagrid('scrollTo',dat['after']-1);
			// rellenamos ventana de datos del perro en pista
			$("#vwls_Numero").html(dat['current'][0]['Orden']);

			$("#vwls_Logo").attr('src','/agility/images/logos/'+dat['current'][0]['Logo']);
			$("#vwls_Dorsal").html(dat['current'][0]['Dorsal']);
			$("#vwls_Nombre").html(dat['current'][0]['Nombre']);
			var celo=(dat['current'][0]['Celo']!=0)?'<span class="blink"><?php _e("Heat");?></span>':"&nbsp";
			$("#vwls_Celo").html(celo);
			$("#vwls_NombreGuia").html(dat['current'][0]['NombreGuia']);
			$("#vwls_NombreClub").html(dat['current'][0]['NombreClub']);
			$("#vwls_Faltas").html(dat['current'][0]['Faltas']);
			$("#vwls_Tocados").html(dat['current'][0]['Tocados']);
			$("#vwls_Rehuses").html(dat['current'][0]['Rehuses']);
			$("#vwls_Tiempo").html(dat['current'][0]['Tiempo']);
			$("#vwls_Puesto").html(dat['current'][0]['Puesto']);
			// evaluamos velocidad, penalización, calificacion y puesto
			// rellenamos ventana de ultimos resultados
			vwc_evalResultados(dat['before']);
			$('#vwcp_ultimos-datagrid').datagrid('loadData',dat['before']).datagrid('scrollTo',0);;
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
            $('#vw_parciales-Juez1').text((dat['manga'].Juez1<=1)?"":'<?php _e('Judge');?> 1: ' + dat['manga'].NombreJuez1);
            $('#vw_parciales-Juez2').text((dat['manga'].Juez2<=1)?"":'<?php _e('Judge');?> 2: ' + dat['manga'].NombreJuez2);
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
 * Actualiza el datagrid de resultados con los datos asociados al evento recibido
 * @param {object} evt event
 * @param {object} data system status data info
 */
function vwcp_updateParciales(evt,data) {
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
			$('#vwcp_header-infomanga').text(str);
			// datos de TRS
			$('#vwcp_parciales-Distancia').text(dat['trs'].dist + 'm.');
			$('#vwcp_parciales-Obstaculos').text(dat['trs'].obst);
			$('#vwcp_parciales-TRS').text(dat['trs'].trs + 's.');
			$('#vwcp_parciales-TRM').text(dat['trs'].trm + 's.');
			$('#vwcp_parciales-Velocidad').text( dat['trs'].vel + 'm/s');
			// actualizar datagrid
			$('#vwcp_parciales-datagrid').datagrid('loadData',dat);
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
        $('#vw_header-infoprueba').html('<?php _e("Header"); ?>');
        $('#vw_header-infomanga').html("(<?php _e('No round selected');?>)");
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
	case 'crono_restart': // paso de tiempo intermedio a manual
		return;
	case 'crono_int':	// tiempo intermedio crono electronico
		return;
	case 'crono_stop':	// parada crono electronico
		return;
	case 'crono_reset':  // puesta a cero del crono electronico
		return;
	case 'crono_error':  // puesta a cero del crono electronico
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

// new combined videoWall (Partial results) eventMgr
function vwcp_procesaCombinada(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
	var time=event['Value'];
	var ssf=$('#vwls_StartStopFlag');
	var crm=$('#cronometro');
	switch (event['Type']) {
		case 'null':		// null event: no action taken
			return;
		case 'init': // operator starts tablet application
			$('#vwcp_header-infoprueba').html('<?php _e("Contest"); ?>');
			$('#vwcp_header-infojornada').html('<?php _e("Journey"); ?>');
			$('#vwcp_header-infomanga').html("(<?php _e('No round selected');?>)");
			vw_updateWorkingData(event,function(e,d){
				vwc_updateDataInfo(e,d);
				vwcp_initParcialesDatagrid(e,d);
				vwc_updateLlamada(e,d);
			});
			return;
		case 'open': // operator select tanda
			vw_updateWorkingData(event,function(e,d){
				vwc_updateDataInfo(e,d);
				vwc_updateLlamada(e,d);
				vwcp_updateParciales(e,d);
			});
			return;
		case 'datos':		// actualizar datos (si algun valor es -1 o nulo se debe ignorar)
			vwls_updateData(event);
			return;
		case 'llamada':		// operador abre panel de entrada de datos
			myCounter.stop();
			vwls_cronometro('stop',time);
			vwls_cronometro('reset',time);
			vw_updateWorkingData(event,function(e,d){
				vwc_updateLlamada(e,d);
			});
			return;
		case 'salida':		// juez da orden de salida ( crono 15 segundos )
			myCounter.start();
			return;
		case 'start':	// value: timestamp
			// si crono automatico, ignora
			if (ssf.text()==="Auto") return;
			myCounter.stop();
			ssf.text("Stop");
			crm.Chrono('stop',time);
			crm.Chrono('reset');
			crm.Chrono('start',time);
			return;
		case 'stop':	// value: timestamp
			ssf.text("Start");
			myCounter.stop();
			vwls_cronometro('stop',time);
			return;
		case 'crono_start': // arranque crono electronico
			myCounter.stop();
			ssf.text('Auto');
			// si esta parado, arranca en modo automatico
			if (!crm.Chrono('started')) {
				crm.Chrono('stop',time);
				crm.Chrono('reset');
				crm.Chrono('start',time);
				return
			}
			if (ac_config.crono_resync==="0") {
				crm.Chrono('reset'); // si no resync, resetea el crono y vuelve a contar
				crm.Chrono('start',time);
			} // else wait for chrono restart event
			return;
		case 'crono_restart': // paso de tiempo intermedio a manual
			crm.Chrono('resync',event['stop'],event['start']);
			return;
		case 'crono_int':	// tiempo intermedio crono electronico
			if (!crm.Chrono('started')) return;	// si crono no esta activo, ignorar
			crm.Chrono('pause',time); setTimeout(function(){crm.Chrono('resume');},5000);
			return;
		case 'crono_stop':	// parada crono electronico
			ssf.text("Start");
			vwls_cronometro('stop',time);
			return;
		case 'crono_reset':  // puesta a cero del crono electronico
			myCounter.stop();
			ssf.text("Start");
			vwls_cronometro('stop',time);
			vwls_cronometro('reset',time);
			return;
		case 'crono_error':  // puesta a cero del crono electronico
			return;
		case 'aceptar':		// operador pulsa aceptar
			vwls_cronometro('stop',event['Value']);  // nos aseguramos de que los cronos esten parados
			vw_updateWorkingData(event,function(e,d){
				vwcp_updateParciales(e,d);
			});
			return;
		case 'cancelar': // back to Series and dog selection in tablet without save
			vwls_cronometro('stop',time);
			vwls_cronometro('reset',time);
			vwls_showOSD(0); // apaga el OSD
			return;
		case 'info':	// click on user defined tandas
			return;
	}
}

function vwls_processLiveStream(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
    var time=event['Value'];
	var ssf=$('#vwls_StartStopFlag');
	var crm=$('#cronometro');
	switch (event['Type']) {
	case 'null':		// null event: no action taken
		return; 
	case 'init':		// operator starts tablet application
        setupWorkingData(event['Pru'],event['Jor'],(event['Mng']>0)?event['Mng']:1); // use shortname to ensure data exists
		vwls_showOSD(0); 	// activa visualizacion de OSD
		return;
	case 'open':		// operator select tanda reset info
        vw_updateWorkingData(event,function(e,d){vw_updateDataInfo(e,d);});
		return;
	case 'datos':		// actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		vwls_updateData(event);
		return;
	case 'llamada':		// operador abre panel de entrada de datos
		myCounter.stop();
		vwls_cronometro('stop',time);
		vwls_cronometro('reset',time);
		vwls_showOSD(1); 	// activa visualizacion de OSD
		vwls_showData(event);
		return;
	case 'salida':		// juez da orden de salida ( crono 15 segundos )
		myCounter.start();
		return;
	case 'start':	// arranque manual del cronometro
		// si crono automatico, ignora
		if (ssf.text()==="Auto") return;
		myCounter.stop();
		ssf.text("Stop");
		crm.Chrono('stop',time);
		crm.Chrono('reset');
		crm.Chrono('start',time);
		return;
	case 'stop':	// value: timestamp
		ssf.text("Start");
		myCounter.stop(); 
		vwls_cronometro('stop',time);
		return;
	case 'crono_start': // arranque crono electronico
		myCounter.stop();
		ssf.text('Auto');
		// si esta parado, arranca en modo automatico
		if (!crm.Chrono('started')) {
			crm.Chrono('stop',time);
			crm.Chrono('reset');
			crm.Chrono('start',time);
			return
		}
		if (ac_config.crono_resync==="0") {
			crm.Chrono('reset'); // si no resync, resetea el crono y vuelve a contar
			crm.Chrono('start',time);
		} // else wait for chrono restart event
		return;
	case 'crono_restart': // paso de tiempo intermedio a manual
		crm.Chrono('resync',event['stop'],event['start']);
		return;
	case 'crono_int':	// tiempo intermedio crono electronico
		if (!crm.Chrono('started')) return;	// si crono no esta activo, ignorar
        crm.Chrono('pause',time); setTimeout(function(){crm.Chrono('resume');},5000);
		return;
	case 'crono_stop':	// parada crono electronico
		ssf.text("Start");
		vwls_cronometro('stop',time);
		return;
	case 'crono_reset':  // puesta a cero del crono electronico
		myCounter.stop();
		ssf.text("Start");
		vwls_cronometro('stop',time);
		vwls_cronometro('reset',time);
		return;
	case 'crono_error':  // fallo en los sensores de paso
		return; // no need to show sensor fail in videowall, just in chrono / tablet
	case 'aceptar':		// operador pulsa aceptar
		vwls_cronometro('stop',event['Value']);  // nos aseguramos de que los cronos esten parados
		// vwls_showData(event); // actualiza pantall liveStream
		return;
	case 'cancelar':	// operador pulsa cancelar
		vwls_cronometro('stop',time);
		vwls_cronometro('reset',time);
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
            $('#vw_header-infoprueba').html('<?php _e("Header"); ?>');
            vw_updateDataInfo(e,d);
        });
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
	case 'crono_restart': // paso de tiempo intermedio a manual
	case 'crono_int':  	// tiempo intermedio crono electronico
	case 'crono_stop':  // parada crono electronico
	case 'crono_reset':  // puesta a cero del crono electronico
	case 'crono_error':  // fallo en los sensores de paso
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
            $('#vw_header-infoprueba').html('<?php _e("Header"); ?>');
            $('#vw_header-infomanga').html("(<?php _e('No round selected');?>)");
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
	case 'crono_restart': // paso de tiempo intermedio a manual
	case 'crono_int':  	// tiempo intermedio crono electronico
	case 'crono_stop':  // parada crono electronico
	case 'crono_reset':  // puesta a cero del crono electronico
	case 'crono_error':  // fallo en los sensores de paso
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
                $('#vw_header-infomanga').html("(<?php _e('No round selected');?>)");
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
		case 'crono_restart': // paso de tiempo intermedio a manual
        case 'crono_int':  	// tiempo intermedio crono electronico
		case 'crono_stop':  // parada crono electronico
		case 'crono_reset':  // puesta a cero del crono electronico
		case 'crono_error':  // fallo en los sensores de paso
            return; // nada que hacer aqui: el crono automatico se procesa en el tablet
        case 'aceptar':	// operador pulsa aceptar
            return;
        case 'cancelar': // operador pulsa cancelar
            return;
        case 'info':	// click on user defined tandas
            return;
    }
}