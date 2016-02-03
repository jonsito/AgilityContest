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
	// TODO: do not call server if no change from current data
	var flag=true;
	if (workingData.prueba!=evt.Prueba) flag=false;
	if (workingData.jornada!=evt.Jornada) flag=false;
	if (workingData.manga!=evt.Manga) flag=false;
	if (workingData.tanda!=evt.Tanda) flag=false;
	if (workingData.sesion!=evt.Sesion) flag=false;
	if (flag) {
		var data={
			Prueba:workingData.datosPrueba,
			Jornada:workingData.datosJornada,
			Manga:workingData.datosManga,
			Tanda:workingData.datosTanda,
			Sesion:workingData.datosSesion,
		};
		setTimeout(callback(evt,data),0);
	}
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
			workingData.sesion=data.Sesion.ID;
			workingData.datosSesion=data.Sesion;
            // and finally invoke callback
            if ( typeof(callback)==='function' ) callback(evt,data);
        }
    });
}

/**
 * Al recibir 'init' ajustamos el modo de visualización de la pantalla
 * de resultados parciales/finales para individual o equipos
 * y si la prueba es open o no (grados)
 * @param {object} evt Evento recibido. Debe ser de tipo init
 * @param data informacion de la prueba,jornada, my manga
 */
function vw_formatResultadosDatagrid(evt,data) {
    var team=false;
	var hasGrades=true;
    var dg=$('#vw_parciales-datagrid');
    if (parseInt(data.Jornada.Equipos3)==1) { team=true; hasGrades=false; }
    if (parseInt(data.Jornada.Equipos4)==1) { team=true; hasGrades=false; }
	if (parseInt(data.Jornada.Open)==1) { hasGrades=false; }
	if (parseInt(data.Jornada.KO)==1) { hasGrades=false; }

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
		if (hasGrades)	dg.datagrid('showColumn',"Grado");
		else dg.datagrid('hideColumn',"Grado");
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
	var infotanda=(typeof(data.Tanda.Nombre)==='undefined')?'':data.Tanda.Nombre;
	var infomanga=(typeof(data.Manga.Nombre)==='undefined')?'':data.Manga.Nombre;
	$('#vwcp_header-NombreRonda').html(infomanga);
	$('#vwcp_header-NombreTanda').html(infotanda);

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
	if (data["Faltas"]!=-1) 	$('#vwls_Faltas').html(data["Faltas"]).hide().show(0);
	if (data["Tocados"]!=-1) 	$('#vwls_Tocados').html(data["Tocados"]).hide().show(0);
	if (data["Rehuses"]!=-1) 	$('#vwls_Rehuses').html(data["Rehuses"]).hide().show(0);
	if (data["Tiempo"]!=-1) 	$('#vwls_Tiempo').html(data["Tiempo"]).hide().show(0);
	if (data["TIntermedio"]!=-1)$("#vwls_TIntermedio").html(data['TIntermedio']);
	if (data["Eliminado"]!=-1) 	$("#vwls_Eliminado").html(data['Eliminado']);
	if (data["Eliminado"]==1) 	$('#vwls_Tiempo').html('<span class="blink" style="color:red">Elim.</span>').hide().show(0);
	if (data["NoPresentado"]!=-1) $("#vwls_NoPresentado").html(data['NoPresentado']);
	if (data["NoPresentado"]==1) $('#vwls_Tiempo').html('<span class="blink" style="color:red">N.P.</span>').hide().show(0);
}

 // actualizar datos desde crono -1:decrease 0:ignore 1:increase
function vwls_updateChronoData(data){
	var i=$('#vwls_Faltas');
	var res=parseInt( i.html() )+parseInt(data["Faltas"]);
	if (res<0) res=0;
	i.html( res.toString() ).hide().show(0);
	i=$('#vwls_Tocados');
	res=parseInt( i.html() )+parseInt(data["Tocados"]);
	if (res<0) res=0;
	i.html( res.toString() ).hide().show(0);
	i=$('#vwls_Rehuses');
	res=parseInt( i.html() )+parseInt(data["Rehuses"]);
	if (res<0)res=0;
	i.html( res.toString() ).hide().show(0);
	$("#vwls_TIntermedio").html(data['TIntermedio']);
	$("#vwls_Eliminado").html( (data['Eliminado']==0)?0:1);
	$("#vwls_NoPresentado").html( (data['NoPresentado']==0)?0:1);
	// TODO: repaint timestamp when elim/notpresent mark is removed
	if (data["Eliminado"]==1)	$('#vwls_Tiempo').html('<span class="blink" style="color:red"><?php _e("Elim");?>.</span>').hide().show(0);
	if (data["NoPresentado"]==1) $('#vwls_Tiempo').html('<span class="blink" style="color:red"><?php _e("NoPr");?>.</span>').hide().show(0);
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
	$('#vwls_TIntermedio').html(data["TIntermedio"]);
	$('#vwls_Eliminado').html(data["Eliminado"]);
	$('#vwls_NoPresentado').html(data["NoPresentado"]);
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
 * each time that "datos" or "chrono_int" arrives, evaluate position of current team
 */
function vwls_evalPuestoIntermedio() {
	var trs=parseInt($('#vwcp_parciales-TRS').text());
	var trm=parseInt($('#vwcp_parciales-TRM').text());
	// phase 1 retrieve results
	var f=parseInt($('#vwls_Faltas').html());
	var t=parseInt($('#vwls_Tocados').html());
	var r=parseInt($('#vwls_Rehuses').html());
	var e=parseInt($('#vwls_Eliminado').html());
	var n=parseInt($('#vwls_NoPresentado').html());
	var time=parseFloat($('#vwls_Tiempo').html());
	var pr=5*f+5*t+4*r+100*e+200*n;
	var pt=0;
	var pf=0;
	if (time<=trs) pt=0; // por debajo de TRS
	else if ((time>=trm) && (trm!=0) ) pt=100; // supera TRS
	else pt=time-trs;
	pf=pt+pr;
	if (pf>=200) pf=200; // no presentado
	else if (pf>=100) pf=100; // eliminado
	// phase 2
	console.log("trs:"+trs+" trm:"+trm+" f:"+f+" t:"+t+" r:"+r+" e:"+e+" n:"+n+" pr:"+pr+" pt:"+pt+" pf:"+pf);
	$('#vwls_Puesto').html(pf);
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
	var trm=parseInt($('#vwcp_parciales-TRM').text());
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
		if (dat.Tiempo<=trs) dat.PTiempo=0; // por debajo de TRS
		else if ((dat.Tiempo>=trm) && (trm!=0) ) dat.PTiempo=100; // supera TRS
		else dat.PTiempo=dat.Tiempo-trs;
		dat.Penalizacion=dat.PRecorrido+dat.PTiempo;
	    if (dat.Penalizacion>=200) dat.Penalizacion=200; // no presentado
		else if (dat.Penalizacion>=100) dat.Penalizacion=100; // eliminado
		// evaluamos calificacion
		if (dat.Penalizacion==0.0) dat.Calificacion="<?php _e('Ex P');?>";
		if (dat.Penalizacion>=0.0) dat.Calificacion="<?php _e('Exc');?>";
		if (dat.Penalizacion>=6.0) dat.Calificacion="<?php _e('V.G.');?>";
		if (dat.Penalizacion>=16.0) dat.Calificacion="<?php _e('Good');?>";
		if (dat.Penalizacion>=26.0) dat.Calificacion="<?php _e('N.C.');?>";
		if (dat.Penalizacion>=100.0) dat.Calificacion="<?php _e('Elim');?>";
		if (dat.Penalizacion>=200.0) dat.Calificacion="<?php _e('N.P.');?>";
		// evaluamos posicion
		var results=$('#vw_parciales-datagrid').datagrid('getData')['rows'];
		if (typeof(results)==="undefined") return; // no data yet
		for (var n=0; n<results.length;n++) {
			if(results[n].Perro==dat.Perro) {
				dat.Puesto=results[n].Puesto; break;
			}
		}
	}
}

/**
 * Gestion de llamada a pista en combinada (parcial)
 * Actualiza el datagrid de llamada a pista con los datos recibidos
 * Actualiza el frame de datos de perro en pista
 * Actualiza el datagrid de ultimos perros en salir
 * @param {object} evt event
 * @param {object} data system status data info
 */
function vwcp_updateLlamada(evt,data) {
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
			$('#vwc_llamada-datagrid').datagrid('loadData',dat['after']).datagrid('scrollTo',dat['after'].length-1);
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
			$("#vwls_TIntermedio").html(data['TIntermedio']);
			$("#vwls_Eliminado").html(data['Eliminado']);
			$("#vwls_NoPresentado").html(data['NoPresentado']);
			// evaluamos velocidad, penalización, calificacion y puesto
			vwc_evalResultados(dat['before']);
			// rellenamos ventana de ultimos resultados
			$('#vwcp_ultimos-datagrid').datagrid('loadData',dat['before']).datagrid('scrollTo',0);
		}
	});
}

var vwcf_emptyFinalResults= {
	// manga 1
	'F1':0, 'R1':0, 'E1':0,	'N1':0,	'T1':0,	'P1':0,	'V1':0,	'C1':'', 'Puesto1':0,'Pcat1':0,
	// manga 2
	'F2':0, 'R2':0, 'E2':0,	'N2':0,	'T2':0,	'P2':0,	'V2':0,	'C2':'', 'Puesto2':0,'Pcat2':0,
	// datos globales
	'Tiempo':"", 'Penalizacion':0,	'Calificacion':"",	'Puntos':'', 'Puesto':"-", 'Pcat':0
};

/**
 * Gestion de llamada a pista en combinada (final)
 * Actualiza el datagrid de llamada a pista con los datos recibidos
 * Actualiza el frame de datos de perro en pista
 * Actualiza el datagrid de ultimos perros en salir
 * @param {object} evt event
 * @param {object} data system status data info
 */
function vwcf_updateLlamada(evt,data) {
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
			function vwcf_evalBefore(res) {
				var count = 0; // to track when before fill gets complete
				// iterate on results and fill "before" data
				for (var n = 0; n < res['rows'].length; n++) {
					var item = res['rows'][n];
					for (var i = 0; i < dat['before'].length; i++) {
						var b = dat['before'][i];
						if (b['Perro']==0) { // no data, fill with empty values
							$.extend(b, vwcf_emptyFinalResults);
							count++;
						}
						if (b['Perro'] == item['Perro']) { // found dog. merge data
							$.extend(b, item);
							count++;
						}
					}
					if (count >= dat['before'].lenght) break; // already done, do not longer iterate
				}
				// una vez evaluadas las clasificaciones de los 'before' perros, las presentamos
				var ret= {'total':dat['before'].length,'rows':dat['before']};
				$('#vwcf_ultimos-datagrid').datagrid('loadData', ret ).datagrid('scrollTo', 0);
			}

			// componemos ventana de llamada
			$('#vwc_llamada-datagrid').datagrid('loadData', dat['after']).datagrid('scrollTo', dat['after'].length - 1);

			// rellenamos ventana de datos del perro en pista
			// TODO: obtener datos de manga hermana y presentarlos
			$("#vwls_Numero").html(dat['current'][0]['Orden']);
			$("#vwls_Logo").attr('src', '/agility/images/logos/' + dat['current'][0]['Logo']);
			$("#vwls_Dorsal").html(dat['current'][0]['Dorsal']);
			$("#vwls_Nombre").html(dat['current'][0]['Nombre']);
			var celo = (dat['current'][0]['Celo'] != 0) ? '<span class="blink"><?php _e("Heat");?></span>' : "&nbsp";
			$("#vwls_Celo").html(celo);
			$("#vwls_NombreGuia").html(dat['current'][0]['NombreGuia']);
			$("#vwls_NombreClub").html(dat['current'][0]['NombreClub']);
			$("#vwls_Faltas").html(dat['current'][0]['Faltas']);
			$("#vwls_Tocados").html(dat['current'][0]['Tocados']);
			$("#vwls_Rehuses").html(dat['current'][0]['Rehuses']);
			$("#vwls_Tiempo").html(dat['current'][0]['Tiempo']);
			$("#vwls_Puesto").html(dat['current'][0]['Puesto']);

			// rellenamos ventana de ultimos resultados
			// dado que necesitamos tener la clasificacion con los perros de la tabla "before",
			// lo que vamos a hacer es calcular dicha tabla aquí, en lugar de desde el evento "aceptar"
			vwcf_updateFinales(evt, data, vwcf_evalBefore);
		}
	});
}

/**
 * (Old-style combinada)
 * Actualiza el datagrid de resultados con los datos asociados al evento recibido
 * @param {object} evt event
 * @param {object} data system status data info
 */
function vw_updateParciales(evt,data) {
    // en lugar de invocar al datagrid, lo que vamos a hacer es
    // una peticion ajax, para obtener a la vez los datos tecnicos de la manga
    // y de los jueces
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
			workingData.teamCounter=1; // reset team's puesto counter
            $('#vw_parciales-datagrid').datagrid('loadData',dat);
        }
    });
}

/**
 * Actualiza datos de la clasificacion general
 * @param {object} evt event
 * @param {object} data system status data info
 * @param {function} callback what to do when data loaded
 */
function vwcf_updateFinales(evt,data,callback) {
	$.ajax({
		type:'GET',
		url:"/agility/server/database/clasificacionesFunctions.php",
		dataType:'json',
		data: {
			Prueba:	data.Prueba.ID,
			Jornada:data.Jornada.ID,
			Manga1:	data.Ronda.Manga1,
			Manga2:	data.Ronda.Manga2,
			Rondas: data.Ronda.Rondas,
			Mode: 	data.Ronda.Mode
		},
		success: function(dat) {
			// nombres de las mangas
			$('#vwcf_header-NombreTanda').html(data.Tanda.Nombre);
			$('#vwcf_header-NombreRonda').html(data.Ronda.Nombre);
			$('#vwcf_finales_thead_m1').html(data.Ronda.NombreManga1);
			$('#vwcf_finales_thead_m2').html(data.Ronda.NombreManga2);
			// datos de trs manga 1
			$('#vwcf_finales-Manga1').html(data.Ronda.NombreManga1);
			$('#vwcf_finales-Distancia1').html('<?php _e('Dist');?>: ' + dat['trs1'].dist + 'm.');
			$('#vwcf_finales-Obstaculos1').html('<?php _e('Obst');?>: ' + dat['trs1'].obst);
			$('#vwcf_finales-TRS1').html('<?php _e('SCT');?>: ' + dat['trs1'].trs + 's.');
			$('#vwcf_finales-TRM1').html('<?php _e('MCT');?>: ' + dat['trs1'].trm + 's.');
			$('#vwcf_finales-Velocidad1').html('<?php _e('Vel');?>: ' + dat['trs1'].vel + 'm/s');
			// datos de trs manga 2
			if (data.Ronda.Manga2==0) { // single round
				$('#vwcf_finales-Manga2').html("");
				$('#vwcf_finales-Distancia2').html("");
				$('#vwcf_finales-Obstaculos2').html("");
				$('#vwcf_finales-TRS2').html("");
				$('#vwcf_finales-TRM2').html("");
				$('#vwcf_finales-Velocidad2').html("");
			} else {
				$('#vwcf_finales-Manga2').html(data.Ronda.NombreManga2);
				$('#vwcf_finales-Distancia2').html('<?php _e('Dist');?>: ' + dat['trs2'].dist + 'm.');
				$('#vwcf_finales-Obstaculos2').html('<?php _e('Obst');?>: ' + dat['trs2'].obst);
				$('#vwcf_finales-TRS2').html('<?php _e('SCT');?>: ' + dat['trs2'].trs + 's.');
				$('#vwcf_finales-TRM2').html('<?php _e('MCT');?>: ' + dat['trs2'].trm + 's.');
				$('#vwcf_finales-Velocidad2').html('<?php _e('Vel');?>: ' + dat['trs2'].vel + 'm/s');
			}
			// rellena tabla de clasificaciones
			$('#vwcf_clasificacion-datagrid').datagrid('loadData',dat);
			if (typeof(callback)==='function') callback(dat);
		}
	});
}

/**
 * ( new style combinada parcial)
 * Actualiza el datagrid de resultados con los datos asociados al evento recibido
 * @param {object} evt event
 * @param {object} data system status data info
 */
function vwcp_updateParciales(evt,data) {
	// en lugar de invocar al datagrid, lo que vamos a hacer es
	// una peticion ajax, para obtener a la vez los datos tecnicos de la manga
	// y de los jueces
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
            var nRonda=$('#vwcp_header-NombreRonda');
			if (typeof(dat.rows)==="undefined") {
				// no data yet.
				var errMsg="<?php _e('No data yet');?>";
				nRonda.text(errMsg);
                return;
			}
			// informacion de la manga
			var str=dat['manga'].TipoManga + " - " + modestr;
			nRonda.text(str);
			// datos de TRS
			$('#vwcp_parciales-Distancia').text(dat['trs'].dist + 'm.');
			$('#vwcp_parciales-Obstaculos').text(dat['trs'].obst);
			$('#vwcp_parciales-TRS').text(dat['trs'].trs + 's.');
			$('#vwcp_parciales-TRM').text(dat['trs'].trm + 's.');
			$('#vwcp_parciales-Velocidad').text( dat['trs'].vel + 'm/s');
			// actualizar datagrid
			workingData.teamCounter=1; // reset team's puesto counter
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

/**
 * Generic event handler for VideoWall and LiveStream screens
 * Every screen has a 'eventHandler' table with pointer to functions to be called
 * @param id {integer} Event ID
 * @param evt {array} Event data
 */
function videowall_eventManager(id,evt) {
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
	var time=event['Value'];
	if (typeof(eventHandler[event['Type']])==="function") eventHandler[event['Type']](event,time);
}
