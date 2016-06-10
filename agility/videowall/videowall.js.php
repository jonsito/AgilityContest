/*
videowall.js

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

var myCounter = new Countdown({
	seconds:15,  // number of seconds to count down
	onUpdateStatus: function(tsec){
		$('#vwls_Tiempo').html(toFixedT((tsec/10),1));
	}, // callback for each tenth of second
	// onCounterEnd: function(){  $('#vwls_Tiempo').html('<span class="blink" style="color:red">-out-</span>'); } // final action
	onCounterEnd: function(){ /* let the tablet to tell us what to do */ }
});


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
			Sesion:workingData.datosSesion
		};
		if ( typeof(callback)==='function' ) setTimeout(callback(evt,data),0);
		return;
	}
	// data change: reset team counter
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
 * y si la prueba es open o no (grados)
 * @param {object} dg Datagrid al que aplicar la modificacion
 * @param {object} evt Evento recibido. Debe ser de tipo init
 * @param {object} data informacion de la prueba,jornada, my manga
 * @param {function} formatter: function to call for group formatter, or null
 */
function vw_formatResultadosDatagrid(dg,evt,data,formatter) {
    var team=false;
	var hasGrades=true;
    if (parseInt(data.Jornada.Equipos3)!=0) { team=true; hasGrades=false; }
    if (parseInt(data.Jornada.Equipos4)!=0) { team=true; hasGrades=false; }
	if (parseInt(data.Jornada.Open)!=0) { hasGrades=false; }
	if (parseInt(data.Jornada.KO)!=0) { hasGrades=false; }

    // clear datagrid as data no longer valid
    if (team){
        if (formatter) dg.datagrid({ view: gview, groupField: 'NombreEquipo', groupFormatter: formatter });
        dg.datagrid('hideColumn',"LogoClub");
        dg.datagrid('hideColumn',"Grado");
    } else {
        if (formatter) dg.datagrid({view:$.fn.datagrid.defaults.view});
        dg.datagrid('showColumn',"LogoClub");
		if (hasGrades)	dg.datagrid('showColumn',"Grado");
		else dg.datagrid('hideColumn',"Grado");
    }
    dg.datagrid('loadData', {"total":0,"rows":[]});
    dg.datagrid('fitColumns');
}

/**
 * Al recibir 'init' ajustamos el modo de visualización de la pantalla
 * de resultados finales para individual o equipos
 * los videowalls de clasificaciones finales no tienen campo "grado"
 * @param {object} dg Datagrid al que aplicar la modificacion
 * @param {object} evt Evento recibido. Debe ser de tipo init
 * @param {object} data informacion de la prueba,jornada, my manga
 * @param {function} formatter: function to call for group formatter, or null
 */
function vw_formatClasificacionesDatagrid(dg,evt,data,formatter) {
	var team=false;
	if (parseInt(data.Jornada.Equipos3)!=0) { team=true; }
	if (parseInt(data.Jornada.Equipos4)!=0) { team=true;  }
	// clear datagrid as data no longer valid
	if (team){
	} else {
		if (formatter) dg.datagrid({view:$.fn.datagrid.defaults.view});
		dg.datagrid('showColumn',"LogoClub");
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
function vw_updateHeaderAndFooter(evt,data) {
    // update header
    var infoprueba='<?php _e('Contest'); ?>'+': ' + data.Prueba.Nombre+' <br />'+'<?php _e('Journey'); ?>'+': '+ data.Jornada.Nombre;
    $('#vw_header-infoprueba').html(infoprueba);
    $('#vw_header-ring').html(data.Sesion.Nombre);
	// on international competitions, use federation Organizer logo
	var logo='/agility/images/logos/'+data.Club.Logo; // dont use "LogoClub" as direct translation from db
	if ( (data.Club.Logo==="") || isInternational(data.Prueba.RSCE)) {
		logo=ac_fedInfo[data.Prueba.RSCE].OrganizerLogo
	}
	$('#vw_header-logo').attr('src',logo);

    // this should be done in callback, as content is window dependent
    // actualiza informacion de manga
    var infomanga=(typeof(data.Tanda.Nombre)==='undefined')?'':data.Tanda.Nombre;
    $('#vwls_Manga').html(infomanga);

    // update footer
	logo=ac_fedInfo[workingData.federation].Logo;
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
function vwc_updateHeaderAndFooter(evt,data) {
	// update header
	$('#vwc_header-infoprueba').html(data.Prueba.Nombre);
	$('#vwc_header-infojornada').html(data.Jornada.Nombre);
	$('#vwc_header-ring').html(data.Sesion.Nombre);
	// on international competitions, use federation Organizer logo
	var logo=data.Club.Logo;
	var fed=data.Prueba.RSCE;
	if ( (logo==="") || isInternational(fed)) {
		logo=ac_fedInfo[fed].OrganizerLogo; // remember that absolute path is provided here
		$('#vwc_header-logo').attr('src',logo);
	} else {
		$('#vwc_header-logo').attr('src','/agility/images/logos/getLogo.php?Fed='+fed+'&Logo='+logo);
	}

	// this should be done in callback, as content is window dependent
	// actualiza informacion de manga
	var infotanda=(typeof(data.Tanda.Nombre)==='undefined')?'':data.Tanda.Nombre;
	var inforonda=(typeof(data.Manga.Nombre)==='undefined')?'':data.Ronda.Nombre;
	$('#vwc_header-NombreTanda').html(infotanda);
	$('#vwc_header-NombreRonda').html(inforonda);

	// update footer
	var fname=(ac_config.vw_combined==0)?"vw_footer.php":"vwc_footer.php";
	$('#vw_footer-footerData').load("/agility/videowall/"+fname,{},function(response,status,xhr){
		if (ac_config.vwc_simplified==0) {
			$('#vw_footer-logoFederation').attr('src',ac_fedInfo[workingData.federation].Logo);
			$('#vw_footer-urlFederation').attr('href',ac_fedInfo[workingData.federation].WebURL);
			$('#vw_footer-logoFederation2').attr('src',ac_fedInfo[workingData.federation].ParentLogo);
			$('#vw_footer-urlFederation2').attr('href',ac_fedInfo[workingData.federation].ParentWebURL);
		}
	});
}

function vwls_updateData(data) {
    // some versions of Safari and Chrome doesn't properly take care on html dom changes
    // so stupid .hide().show(0) is needed to take care on this
	if (data["Faltas"]!=-1) 	$('#vwls_Faltas').html(data["Faltas"]).hide().show(0);
	if (data["Tocados"]!=-1) 	$('#vwls_Tocados').html(data["Tocados"]).hide().show(0);
	if (data["Rehuses"]!=-1) 	$('#vwls_Rehuses').html(data["Rehuses"]).hide().show(0);
	if (data["Tiempo"]!=-1) 	$('#vwls_Tiempo').html(data["Tiempo"]).hide().show(0);
	if (data["TIntermedio"]!=-1)$("#vwls_TIntermedio").html(data['TIntermedio']);
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
}

function vwls_updateChronoData(data) {
	vwls_updateData(data);
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
 * evaluate position in hall of fame (final results)
 * When chrono stops this script is invoked instead of vwcf_evalPenalization()
 * Do not evaluate trs/trm. just iterate on datagrid results to find position
 * @param {boolean} flag display on/off
 * @param {float} time measured from chrono (do not read html dom content)
 */
function vwcf_displayPuesto(flag,time) {
	// if requested, turn off data
	var perro=$('#vwls_Perro').text();
	if (!flag || (perro==0) ) { $('#vwls_Puesto').html(''); return; }
	// use set timeout to make sure data are already refreshed
	setTimeout(function(){
		// phase 1 retrieve results
		// use text() instead of html() avoid extra html code
		var datos= {
			'Perro':	perro,
			'Categoria':$('#vwls_Categoria').text(),
			'Grado':	$('#vwls_Grado').text(),
			'Faltas':	$('#vwls_Faltas').text(),
			'Tocados':	$('#vwls_Tocados').text(),
			'Rehuses':	$('#vwls_Rehuses').text(),
			'Eliminado':$('#vwls_Eliminado').text(),
			'NoPresentado':$('#vwls_NoPresentado').text(),
			'Tiempo':	time
		};
		// phase 2: do not call server if eliminado or not presentado
		if (datos.NoPresentado=="1") {
			$('#vwls_Puesto').html('<span class="blink" style="color:red;"><?php _e('NoPr');?>.</span>');// no presentado
			return;
		}
		// on eliminado, do not blink error, as we don't know data on the other round.
		// phase 3: call server to evaluate partial result position
		getPuestoFinal(datos,function(data,resultados){
			$('#vwls_Puesto').html('- '+Number(resultados.puesto).toString()+' -');
		});
	},0);
}

/**
 * evaluate position in hall of fame (partial results)
 * When chrono stops this script is invoked instead of vwcp_evalPenalization()
 * Do not evaluate trs/trm. just iterate on datagrid results to find position
 * @param {boolean} flag display on/off
 * @param {float} time measured from chrono (do not read html dom content)
 */
function vwcp_displayPuesto(flag,time) {
    // if requested, turn off data
    var perro=$('#vwls_Perro').text();
    if (!flag || (perro==0) ) { $('#vwls_Puesto').html(''); return; }
    // use set timeout to make sure data are already refreshed
    setTimeout(function(){
        // phase 1 retrieve results
        // use text() instead of html() avoid extra html code
		var datos= {
            'Perro':	perro,
            'Categoria':$('#vwls_Categoria').text(),
            'Grado':	$('#vwls_Grado').text(),
			'Faltas':	$('#vwls_Faltas').text(),
			'Tocados':	$('#vwls_Tocados').text(),
			'Rehuses':	$('#vwls_Rehuses').text(),
			'Eliminado':$('#vwls_Eliminado').text(),
			'NoPresentado':$('#vwls_NoPresentado').text(),
			'Tiempo':	time
		};
		// phase 2: do not call server if eliminado or not presentado
		if (datos.NoPresentado=="1") {
			$('#vwls_Puesto').html('<span class="blink" style="color:red;"><?php _e('NoPr');?>.</span>');// no presentado
			return;
		}
		if (datos.Eliminado=="1") {
			$('#vwls_Puesto').html('<span class="blink" style="color:red;"><?php _e('Elim');?>.</span>');// eliminado
			return;
		}
        // phase 3: call server to evaluate partial result position
		getPuestoParcial(datos,function(data,resultados){
			$('#vwls_Puesto').html('- '+Number(resultados.puesto).toString()+' -');
		});
    },0);
}

/**
 * each time that "datos" or "chrono_int" arrives, evaluate position of current team
 */
function vwc_evalPenalizacion(trs,trm,time) {
	// use set timeout to make sure data are already refreshed
	setTimeout(function(){
		// phase 1 retrieve results
		var f=parseInt($('#vwls_Faltas').html());
		var t=parseInt($('#vwls_Tocados').html());
		var r=parseInt($('#vwls_Rehuses').html());
		var e=parseInt($('#vwls_Eliminado').html());
		var n=parseInt($('#vwls_NoPresentado').html());
		var pr=5*f+5*t+5*r+100*e+200*n;
		var pt=0;
		if (time<=trs) pt=0; // por debajo de TRS
		else if ((time>=trm) && (trm!=0) ) pt=100; // supera TRS
		else pt=time-trs;
		var pf=pt+pr;
		var str='';
		if (pf>=200) str='<span class="blink" style="color:red;"><?php _e('NoPr');?>.</span>'; // no presentado
		else if (pf>=100) str='<span class="blink" style="color:red;"><?php _e('Elim');?>.</span>'; // eliminado
		else str= Number(pf.toFixed(ac_config.numdecs)).toString();
		$('#vwls_Puesto').html(str);
	},0);
}

function vwcp_evalPenalizacion() {
    var time=parseFloat($('#vwls_Tiempo').text());
    var trs=parseFloat($('#vwcp_parciales-TRS').text());
    var trm=parseFloat($('#vwcp_parciales-TRM').text());
    if (isNaN(trs)) trs=0;
    if (isNaN(trm)) trm=0;
    if (isNaN(time)) time=0;
    vwc_evalPenalizacion(trs,trm,time);
}

function vwcf_evalPenalizacion () {
	var trs=0;
	var trm=0;
    var time=parseFloat($('#vwls_Tiempo').text());
	if ( isAgility(workingData.datosTanda.Tipo) ) {
		trs=parseFloat($('#vwcf_finales-TRS1').text());
		trm=parseFloat($('#vwcf_finales-TRM1').text());
	}
	if ( isJumping(workingData.datosTanda.Tipo) ) {
		trs=parseFloat($('#vwcf_finales-TRS2').text());
		trm=parseFloat($('#vwcf_finales-TRM2').text());
	}
	if (isNaN(trs)) trs=0;
	if (isNaN(trm)) trm=0;
	if (isNaN(time)) time=0;
	vwc_evalPenalizacion(trs,trm,time);
}

/**
 * Evalua y rellena los datos de penalizacion, calificacion y puesto
 * de los perros que acaban de salir
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
		if (dat.Penalizacion>=400.0) dat.Calificacion="-";
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
			Before: 3,
			After: 15,
			Perro: parseInt(evt['Dog']),
			Session: workingData.sesion
		},
		success: function(dat,status,jqxhr) {
			// componemos ventana de llamada
			$('#vwc_llamada-datagrid').datagrid('loadData',dat['after']).datagrid('scrollTo',dat['after'].length-1);
			var current=dat['current'][0];
			// rellenamos ventana de datos del perro en pista
			$("#vwls_Numero").html(current['Orden']);

			$("#vwls_Logo").attr('src','/agility/images/logos/'+current['LogoClub']);
			$("#vwls_Perro").html(current['Perro']);
			$("#vwls_Categoria").html(current['Categoria']);
			$("#vwls_Grado").html(current['Grado']);
			$("#vwls_Dorsal").html(current['Dorsal']);
			$("#vwls_Nombre").html(current['Nombre']);
			var celo=(current['Celo']!=0)?'<span class="blink"><?php _e("Heat");?></span>':"&nbsp";
			$("#vwls_Celo").html(celo);
			$("#vwls_NombreGuia").html(current['NombreGuia']);
			$("#vwls_NombreClub").html(current['NombreClub']);
			$("#vwls_Faltas").html(current['Faltas']);
			$("#vwls_Tocados").html(current['Tocados']);
			$("#vwls_Rehuses").html(current['Rehuses']);
			$("#vwls_Puesto").html(current['Puesto']);
			$("#vwls_TIntermedio").html(current['TIntermedio']);
			$("#vwls_Tiempo").html(current['Tiempo']);
			var e=parseInt(current["Eliminado"]);
			$('#vwls_Eliminado').html(e);
			$('#vwls_EliminadoLbl').html((e==0)?'':'<span class="blink" style="color:red"><?php _e('Elim');?>.</span>');
			var n=parseInt(current["NoPresentado"]);
			$('#vwls_NoPresentado').html(n);
			$('#vwls_NoPresentadoLbl').html((n==0)?'':'<span class="blink" style="color:red"><?php _e('NoPr');?>.</span>');

			// evaluamos velocidad, penalización, calificacion y puesto
			vwc_evalResultados(dat['before']);
			vwcp_evalPenalizacion(); // repaint penalization
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
			Before: (ac_config.vwc_simplified==0)?4:2,
			After: (ac_config.vwc_simplified==0)?15:10,
			Perro: parseInt(evt['Dog']),
			Session: workingData.sesion
		},
		success: function(dat,status,jqxhr) {
			function vwcf_evalBefore(res) {
                // take care on team rounds
                if (typeof(res['rows'])==="undefined") res['rows']=res['individual'];
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
			var current=dat['current'][0];
			// TODO: obtener datos de manga hermana y presentarlos
			$("#vwls_Numero").html(current['Orden']);
			$("#vwls_Logo").attr('src', '/agility/images/logos/' + current['LogoClub']);
			$("#vwls_Perro").html(current['Perro']);
			$("#vwls_Categoria").html(current['Categoria']);
			$("#vwls_Grado").html(current['Grado']);
			$("#vwls_Dorsal").html(current['Dorsal']);
			$("#vwls_Nombre").html(current['Nombre']);
			var celo = (current['Celo'] != 0) ? '<span class="blink"><?php _e("Heat");?></span>' : "&nbsp";
			$("#vwls_Celo").html(celo);
			$("#vwls_NombreGuia").html(current['NombreGuia']);
			$("#vwls_NombreClub").html(current['NombreClub']);
			$("#vwls_Faltas").html(current['Faltas']);
			$("#vwls_Tocados").html(current['Tocados']);
			$("#vwls_Rehuses").html(current['Rehuses']);
			$("#vwls_Puesto").html(current['Puesto']);
			$("#vwls_TIntermedio").html(current['TIntermedio']);
			$("#vwls_Tiempo").html(current['Tiempo']);
			var e=parseInt(current["Eliminado"]);
			$('#vwls_Eliminado').html(e);
			$('#vwls_EliminadoLbl').html((e==0)?'':'<span class="blink" style="color:red"><?php _e('Elim');?>.</span>');
			var n=parseInt(current["NoPresentado"]);
			$('#vwls_NoPresentado').html(n);
			$('#vwls_NoPresentadoLbl').html((n==0)?'':'<span class="blink" style="color:red"><?php _e('NoPr');?>.</span>');
			// rellenamos ventana de ultimos resultados
			vwcf_evalPenalizacion(); // repaint penalization
			// dado que necesitamos tener la clasificacion con los perros de la tabla "before",
			// lo que vamos a hacer es calcular dicha tabla aquí, en lugar de desde el evento "aceptar"
			updateFinales(data.Ronda, vwcf_evalBefore);
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

function vw_autoscroll(dg,pos) {
	var pTime=parseInt(ac_config.vw_polltime); // seconds
	if (pTime==0) { // autoscroll. stay on top
		dg.datagrid('scrollTo',0);
		return;
	}
	var size=dg.datagrid('getRows').length;
	setTimeout(function(){
		dg.datagrid('scrollTo',pos);
		if ( pos==(size-1)) pos=0; // at end: go to beging
		else pos+=10;
		if (pos>=size) pos=size-1; // next to end: pos at end
		vw_autoscroll(dg,pos);
	},1000*pTime);
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
