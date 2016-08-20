/*
 vws.js.php

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

// javascript code for simplified videowall related functions

/**
 * declare vwsCounter as replacement of myCounter
 */
var vwsCounter = new Countdown({
	seconds:15,  // number of seconds to count down
	onUpdateStatus: function(tsec){
		$('#vws_current_Time'+workingData.vws_currentRow).html(toFixedT((tsec/10),1));
	}
});

/**
 * use keys up/down to increase/decrease font size
 * NOT WORKING: REVISE
 */
function vws_keyBindings() {
    // capture <space> key to switch OSD on/off
    $(document).keydown(function(e) {
        var delta=0;
        if (e.which == 38) delta=0.1; //  key up
        if (e.which == 40) delta=-0.1; // key down
        if (delta==0) return true; // let continue keybind event chain
        size=parseFloat(ac_config.vws_fontsize)+delta;
        if ( (size>=1.0) && (size<=5.0) ) ac_config.vws_fontsize=size;
        $('.vws_entry').css('font-size',''+size+'vw');
        $('.vws_entry input').css('font-size',''+size+'vw');
        document.title="font-size: "+toFixedT(size,1);
        return true; // to allow continue event chain
    });
}

/**
 * Provide a null, empty object with required fields to fill templates in combined videowall
 * @param {boolean} final to notice partial or final results
 * @param {boolean} team  to indicate individual or team journey
 */
function vws_getEmptyResults(final,team) {
    if (final && team) return { // team final
            'Logo':'none.png', 'Nombre':'',
            'T1':'','P1':'','Puesto1':'',
            'T2':'','P2':'','Puesto2':'',
            'Tiempo':'','Penalizacion':'','Puesto':''
        };
    if (final && !team) return  { // individual final
            'Logo':'none.png','Dorsal':'','Nombre':'', 'NombreGuia':'',
            'T1':'','P1':'','Puesto1':'',
            'T2':'','P2':'','Puesto2':'',
            'Tiempo':'','Penalizacion':'','Puesto':''
        };
    if (!final && team) return { // team partial
            'Logo':'none.png', 'Nombre':'',
            'PRecorrido':'','PTiempo':'', 'Tiempo':'',
            'Penalizacion':'','Puesto':''
        };
    if (!final && !team ) return { // individual partial
            'Logo':'none.png','Dorsal':'','Nombre':'', 'NombreGuia':'',
            'Faltas':0,'Tocados':0,'Rehuses':'','Tiempo':'',
            'Penalizacion':'','Puesto':''
        };
}

/**
 * Show/Hide result fields according selected round
 * @param agility
 */
function vws_selectAgilityOrJumping(agility) {
    $('#vws_hdr_lastround').html((agility)?"Agility":"Jumping");
    var toShow=(agility)?"1":"2";
    var toHide=(agility)?"2":"1";
    var size=isJornadaEquipos(null)?7:10;
    for (var n=0;n<size;n++) { // results
        $("#vws_results_T"+toShow+"_"+n).css("display","inline-block");
        $("#vws_results_P"+toShow+"_"+n).css("display","inline-block");
        $("#vws_results_Puesto"+toShow+"_"+n).css("display","inline-block");
        $("#vws_results_T"+toHide+"_"+n).css("display","none");
        $("#vws_results_P"+toHide+"_"+n).css("display","none");
        $("#vws_results_Puesto"+toHide+"_"+n).css("display","none");
    }
    for (n=0;n<2;n++) { // last results
        $("#vws_before_T"+toShow+"_"+n).css("display","inline-block");
        $("#vws_before_P"+toShow+"_"+n).css("display","inline-block");
        $("#vws_before_Puesto"+toShow+"_"+n).css("display","inline-block");
        $("#vws_before_T"+toHide+"_"+n).css("display","none");
        $("#vws_before_P"+toHide+"_"+n).css("display","none");
        $("#vws_before_Puesto"+toHide+"_"+n).css("display","none");
    }
}

/**
 * Update header information
 * Fill contest, journey, round and sct according requested operation
 * @param {string} mode what to update
 * @param {object} data where to take information from
 */
function vwsf_updateHeader(mode,data) {
    mode=mode.toLowerCase();
    if (mode.indexOf("prueba")>=0) {
        var imgurl="/agility/images/logos/agilitycontest.png";
        if (parseInt(ac_config.vws_uselogo)) imgurl=ac_config.vws_logourl;
        else imgurl="/agility/images/logos/"+workingData.datosPrueba.LogoClub;
        $('#vws_hdr_logoprueba').val(imgurl);
        $('#vws_hdr_logo').attr('src',imgurl);
        $('#vws_hdr_prueba').val(workingData.datosPrueba.Nombre);
        $('#vws_hdr_jornada').val(workingData.datosJornada.Nombre);
    }
    if (data==null) return; // cannot go further without data
    if (mode.indexOf("manga")>=0) { // fix round name
        var team=(isJornadaEquipos(null))?"":" - <?php _e('Individual');?>";
        $('#vws_hdr_manga').val(data.Tanda.Nombre+team);
        vws_selectAgilityOrJumping(isAgility(data.Tanda.Tipo));
    }
    if ( mode.indexOf("trs")>=0) { // fix sct/mct
        // current round is always first one:
        var trs1=(typeof(data.trs1)==="undefined")?"<?php _e('Dist');?>/<?php _e('SCT');?>": data.trs1.dist+ "m. / " +data.trs1.trs+"s.";
        var trs2=(typeof(data.trs2)==="undefined")?"<?php _e('Dist');?>/<?php _e('SCT');?>": data.trs2.dist+ "m. / " +data.trs2.trs+"s.";
        $('#vws_hdr_trs').val(isAgility(workingData.datosTanda.Tipo)?trs1:trs2);
    }
}

/**
 * Update header information for partial results
 * Fill contest, journey, round and sct according requested operation
 * @param {string} mode what to update
 * @param {object} data where to take information from
 */
function vwsp_updateHeader(mode,data) {
    mode=mode.toLowerCase();
    if (mode.indexOf("prueba")>=0) {
        var imgurl="/agility/images/logos/agilitycontest.png";
        if (parseInt(ac_config.vws_uselogo)) imgurl=ac_config.vws_logourl;
        else imgurl="/agility/images/logos/"+workingData.datosPrueba.LogoClub;
        $('#vws_hdr_logoprueba').val(imgurl);
        $('#vws_hdr_logo').attr('src',imgurl);
        $('#vws_hdr_prueba').val(workingData.datosPrueba.Nombre);
        $('#vws_hdr_jornada').val(workingData.datosJornada.Nombre);
    }
    if (data==null) return; // cannot go further without data
    if (mode.indexOf("manga")>=0) { // fix round name
        var team=(isJornadaEquipos(null))?"":" - <?php _e('Individual');?>";
        $('#vws_hdr_manga').val(data.Tanda.Nombre+team);
    }
    if ( mode.indexOf("trs")>=0) { // fix sct/mct
        var trs=(typeof(data.trs)==="undefined")?"<?php _e('Dist');?>/<?php _e('SCT');?>": data.trs.dist+ "m. / " +data.trs.trs+"s.";
        $('#vws_hdr_trs').val(trs);
    }
}
/**
 * Load and render proper final scores page acording individual or team journey
 * @param {object} data journey information
 */
function vws_setFinalIndividualOrTeamView(data) {
    var team=isJornadaEquipos(data.Jornada);
    // cargamos la pagina adecuada en funcion del tipo de evento
    var page='/agility/videowall/'+((team==true)?'vws_final_equipos.php':'vws_final_individual.php');
    $('#vws-window').window('refresh',page);
}

/**
* Load and render proper partial results page acording individual or team journey
* @param {object} data journey information
*/
function vws_setPartialIndividualOrTeamView(data) {
    var team=isJornadaEquipos(data.Jornada);
    // cargamos la pagina adecuada en funcion del tipo de evento
    var page='/agility/videowall/'+((team==true)?'vws_parcial_equipos.php':'vws_parcial_individual.php');
    $('#vws-window').window('refresh',page);
}

/**
 *
 * @param {string} row Row suffix ( or empty if infvidual round )
 * @param {boolean} flag (tell routine to use Time flag. On false, time will be handle by chronometer )
 * @param {float} toBeFirst if defined, on call to ring show time required to first place
 */
function vws_displayData(row,flag,toBeFirst) {
    // faltas, tocados, rehuses y tiempo
    var f=parseInt($('#vws_current_Faltas'+row).val());
    var t=parseInt($('#vws_current_Tocados'+row).val());
    var r=parseInt($('#vws_current_Rehuses'+row).val());
    var tim=parseFloat($('#vws_current_Tiempo'+row).val());
    $('#vws_current_FaltasTocados'+row).html("F: "+(f+t));
    $('#vws_current_Refusals'+row).html("R: "+(r));
    if (flag) $('#vws_current_Time'+row).html("T: "+ toFixedT(tim,ac_config.numdecs));
    // eliminado, no presentado, puesto
    var e=parseInt($('#vws_current_Eliminado'+row).val());
    var n=parseInt($('#vws_current_NoPresentado'+row).val());
    var p=parseInt($('#vws_current_Puesto'+row).val());
    if (isNaN(p)) p=0; // on first dog no results, so no position yet.... thus don't show anything
    var rs=$('#vws_current_Result'+row);
    if (n>0) { rs.html('<?php _e('NoPr');?>.'); return; }
    if (e>0) { rs.html('<?php _e('Elim');?>.'); return; }
    if (typeof(toBeFirst)!=="undefined") {
        if (toBeFirst==="") {rs.html(""); return; }
        rs.html('<span class="blink">&lt; '+toFixedT(toBeFirst,ac_config.numdecs)+'</span>');
        return;
    }
    rs.html('- '+p+' -');
}

/**
 * Gestion de llamada a pista en combinada simplificada(final)
 *
 * Al contrario que en las combinadas normales, en las simplificadas no se usan datagrids
 * sino que se manejan directamente arrays de formularios
 * Actualiza el array de formularios de llamada a pista con los datos recibidos
 * Actualiza el array de formularios para perro/equipo en pista
 * Actualiza el array de ultimos perros en salir
 *
 * Al contrario que en la combinada, la simplificada no gestiona resultados desde aqui
 * @param {object} evt event
 * @param {object} data system status data info
 * @param {function} callback function to be called on updateLlamada success
 */
function vws_updateLlamada(evt,data,callback) {
    var team=isJornadaEquipos(null);
    var nitems=(team)?5:8; // resultados a evaluar en el orden de llamada a pista
    $.ajax( {
        type: "GET",
        dataType: 'json',
        url: "/agility/server/web/videowall.php",
        data: {
            Operation: (team)?'teamwindow':'window',
            Before: 2,
            After: nitems,
            Perro: parseInt(evt['Dog']),
            Session: workingData.sesion
        },
        success: function(dat,status,jqxhr) {
            var logo="null.png";
            // fill "after" columns
            for(var n=0;n<nitems;n++) {
                logo=dat['after'][n][(team)?'LogoTeam':'LogoClub'];
                $('#vws_call_'+n).form('load',dat['after'][n]);
                $('#vws_call_Logo_'+n).attr('src','/agility/images/logos/getLogo.php?Logo='+logo+'&Federation='+workingData.federation);
            }
            // fill "current" columns
            if(team) {
                workingData.vws_currentRow="";
                dat['results'][0]['Orden']=dat['current'][0]['Orden']; // to properly fill form field
                for (n=0;n<4;n++) {
                    var cur='_'+n;
                    // check for current dot to mark proper "current" row form as active
                    if (dat['results'][n]['Perro']==evt['Dog']) {
                        workingData.vws_currentRow=cur;
                        $('#vws_current_Active'+cur).html('<img width="80%" src="'+ac_fedInfo[workingData.federation]['Logo']+'"/>');
                    } else  $('#vws_current_Active'+cur).html("");
                    // notice 'results' and 'LogoClub' as we need dog data, not team data
                    $('#vws_current'+cur).form('load',dat['results'][n]);
                    vws_displayData(cur,true);
                    if (n==3) {  // check for test dog on last row and fix team logo
                        if (evt['Nombre']==="<?php _e('Test dog');?>") {
                            $('#vws_current_Nombre_3').val(evt['Nombre']);
                            workingData.vws_currentRow='_3';
                            logo="agilitycontest.png";
                        } else {
                            logo=dat['current'][0]['LogoTeam'];
                        }
                    }
                }
                // set team icon. on test dog use AC logo
                $('#vws_current_Logo_0').attr('src','/agility/images/logos/getLogo.php?Logo='+logo+'&Federation='+workingData.federation);
            } else { /* individual */
                if (evt['Nombre']==="<?php _e('Test dog');?>") {
                    logo="agilitycontest.png";
                    dat['current'][0]['Nombre']=evt['Nombre'];
                } else {
                    logo=dat['current'][0]['LogoClub'];
                }
                $('#vws_current').form('load',dat['current'][0]);
                vws_displayData("",true);
                workingData.vws_currentRow="";
                $('#vws_current_Logo').attr('src','/agility/images/logos/getLogo.php?Logo='+logo+'&Federation='+workingData.federation);
            }
            // fill "before" ( but will be revisited on updateResults )
            for(n=0;n<2;n++) {
                logo=dat['before'][n][(team)?'LogoTeam':'LogoClub'];
                $('#vws_before_'+n).form('load',dat['before'][n]);
                $('#vws_before_Logo_'+n).attr('src','/agility/images/logos/getLogo.php?Logo='+logo+'&Federation='+workingData.federation);
            }
            if (typeof(callback)==="function") callback(parseInt(evt['Dog']),data);
        }
    });
}

/**
 * funcion para rellenar los resultados en la pantalla simplificada
 * @param {integer} perro id del perro sobre el que calcular el tiempo que necesita para quedar primero
 * @param {object} data Datos de la sesion ( recibidos desde vws_updateWorkingData() )
 */
function vws_updateFinales(perro,data) {
    // ajustamos contadores
    var team=isJornadaEquipos(null);
    var nitems=(team)?7:10; // clasificaciones a presentar en funcion de individual/equipos
    // buscamos clasificaciones
    $.ajax({
        type: 'GET',
        url: "/agility/server/database/clasificacionesFunctions.php",
        dataType: 'json',
        data: {
            Operation: (team) ? 'clasificacionEquipos' : 'clasificacionIndividual',
            Prueba: data.Ronda.Prueba,
            Jornada: data.Ronda.Jornada,
            Manga1: data.Ronda.Manga1,
            Manga2: data.Ronda.Manga2,
            Rondas: data.Ronda.Rondas,
            Mode: data.Ronda.Mode,
            Perro: perro
        },
        success: function (dat) {
            var items = dat.rows; // resultados que hay que coger para rellenar tablas
            var individual = dat.rows; // resultados de clasificacion individual
            if (team) {
                items = dat.equipos;
                individual = dat.individual;
            }
            var size = items.length; // longitud de datos a analizar

            // ajustamos cabeceras ( nombre mangas, trs y trm )
            vwsf_updateHeader('trs',dat);
            // rellenamos arrays 'result' y 'before'
            for (var n = 0; n < size; n++) {
                // el campo puesto no viene: lo obtenemos del orden de la lista
                items[n]['Puesto']=n+1;
                items[n]['Result']=n+1;
                // fill if required 'result' table data
                if (n < nitems) {
                    var logo = items[n][(team) ? 'LogoTeam' : 'LogoClub'];
                    $('#vws_results_' + n).form('load', items[n]);
                    $('#vws_results_Logo_' + n).attr('src', '/agility/images/logos/getLogo.php?Logo=' + logo + '&Federation=' + workingData.federation);
                }
                // fill if required 'before' table data
                for (var i = 0; i < 2; i++) {
                    if (team) { if ($('#vws_before_Equipo_' + i).val() != items[n]['ID']) continue; }
                    if (!team) { if ($('#vws_before_Perro_' + i).val() != items[n]['Perro']) continue; }
                    $('#vws_before_' + i).form('load',items[n]);
                }
            }
            // si size < nitems, completamos con datos vacios
            for (;n<nitems;n++) {
                $('#vws_results_' + n).form('load', vws_getEmptyResults(/*final*/true,team));
                $('#vws_results_Logo_' + n).attr('src', '/agility/images/logos/null.png');
            }
            // limpiamos datos vacios en tabla "before"
            for (i = 0; i < 2; i++) {
                if ($('#vws_before_Orden_' + i).val() !== '') continue;
                $('#vws_before_' + i).form('load', vws_getEmptyResults(/*final*/true,team));
                $('#vws_before_Logo_' + i).attr('src', '/agility/images/logos/null.png');
            }
            // ahora indicamos puesto en el(los) campo(s) current, utilizando los datos de perros individuales
            for (n = 0; n < individual.length; n++) {
                var perro = individual[n]['Perro'];
                if (team) {
                    for (i = 0; i < 4; i++) {
                        if ($('#vws_current_Perro_' + i).val() != perro) continue;
                        $('#vws_current_Puesto_' + i).val(individual[n]['Puesto']);
                        if (typeof(dat.current)==="undefined") vws_displayData("_"+i,false);
                        else vws_displayData("_"+i,false,dat.current['toBeFirst']);
                    }
                } else {
                    if ($('#vws_current_Perro').val() != perro) continue;
                    $('#vws_current_Puesto').val(individual[n]['Puesto']);
                    if (typeof(dat.current)==="undefined") vws_displayData("",false);
                    else vws_displayData("",false,dat.current['toBeFirst']);
                }
            } // fill current
        } // success
    }); // ajax
}

/**
 * funcion para rellenar los resultados en la pantalla simplificada
 * @param {object} data Datos de la sesion ( recibidos desde vws_updateWorkingData() )
 */
function vws_updateParciales(data) {
    // ajustamos contadores
    var team=isJornadaEquipos(null);
    var nitems=(team)?7:10; // clasificaciones a presentar en funcion de individual/equipos
    // buscamos resultados parciales de la manga
    $.ajax({
            type:'GET',
            url:"/agility/server/database/resultadosFunctions.php",
            dataType:'json',
            data: {
            Operation:	(isJornadaEquipos(null))?'getResultadosEquipos':'getResultados',
            Prueba:		workingData.prueba,
            Jornada:	workingData.jornada,
            Manga:		workingData.manga,
            Mode:       workingData.datosTanda.Mode
        },
        success: function (dat) {
            var items = dat.rows; // resultados que hay que coger para rellenar tablas
            var individual = dat.rows; // resultados de clasificacion individual
            if (team) {
                items = dat.equipos;
                individual = dat.individual;
            }
            var size = items.length; // longitud de datos a analizar

            // ajustamos cabeceras ( nombre mangas, trs y trm )
            vwsp_updateHeader('trs',dat);
            // rellenamos arrays 'result' y 'before'
            for (var n = 0; n < size; n++) {
                // fill if required 'result' table data
                if (n < nitems) {
                    var logo = items[n][(team) ? 'LogoTeam' : 'LogoClub'];
                    // en pruebas por equipos getTeamResults, retorna datos tanto para equipos completos
                    // como sin completar e incluso vacios. Por ello lo tenemos en cuenta
                    var data=items[n];
                    if (team && items[n]['Penalizacion']>=(400*getMinDogsByTeam())) data=vws_getEmptyResults(/*final*/false,team);
                    $('#vws_results_' + n).form('load', data);
                    $('#vws_results_Logo_' + n).attr('src', '/agility/images/logos/getLogo.php?Logo=' + logo + '&Federation=' + workingData.federation);
                    $('#vws_results_FaltasTocados_' + n).html(parseInt(items[n]['Faltas'])+parseInt(items[n]['Tocados']));
                }
                // fill if found 'before' table data
                for (var i = 0; i < 2; i++) {
                    if (team) { if ($('#vws_before_Equipo_' + i).val() != items[n]['ID']) continue; }
                    if (!team) { if ($('#vws_before_Perro_' + i).val() != items[n]['Perro']) continue; }
                    $('#vws_before_' + i).form('load',items[n]);
                    if (!team) $('#vws_before_FaltasTocados_' + i).html(parseInt(items[n]['Faltas'])+parseInt(items[n]['Tocados']))
                }
            }
            // si size < nitems, completamos con datos vacios
            for (;n<nitems;n++) {
                $('#vws_results_' + n).form('load', vws_getEmptyResults(/*final*/false,team));
                $('#vws_results_FaltasTocados_' + n).html('');
                $('#vws_results_Logo_' + n).attr('src', '/agility/images/logos/null.png');
            }
            // limpia los campos "before que no hayan sido utilizados
            for (i = 0; i < 2; i++) {
                if ($('#vws_before_Orden_' + i).val() !== '') continue;
                $('#vws_before_' + i).form('load', vws_getEmptyResults(/*final*/false,team));
                $('#vws_before_FaltasTocados_' + i).html('');
                $('#vws_before_Logo_' + i).attr('src', '/agility/images/logos/null.png');
            }
            // ahora indicamos puesto en el(los) campo(s) current, utilizando los datos de perros individuales
            for (n = 0; n < individual.length; n++) {
                var perro = individual[n]['Perro'];
                if (team) {
                    for (i = 0; i < 4; i++) {
                        if ($('#vws_current_Perro_' + i).val() != perro) continue;
                        $('#vws_current_Puesto_' + i).val(individual[n]['Puesto']);
                        vws_displayData("_"+i,false);
                    }
                } else {
                    if ($('#vws_current_Perro').val() != perro) continue;
                    $('#vws_current_Puesto').val(individual[n]['Puesto']);
                    vws_displayData("",false);
                }
            } // fill current
        } // success
    }); // ajax
}

/**
 * Callled from tablet/chrono "data/cronodata" event
 * @param {object} data Event received data
 */
function vws_updateData(data) {
    // take care on F/T changes
    if (data["Faltas"]!=-1) 	$('#vws_current_Faltas'+workingData.vws_currentRow).val(data["Faltas"]);
    if (data["Tocados"]!=-1) 	$('#vws_current_Tocados'+workingData.vws_currentRow).val(data["Tocados"]);
    // now update faltastocados field
    var flt=$('#vws_current_Faltas'+workingData.vws_currentRow).val();
    var toc=$('#vws_current_Tocados'+workingData.vws_currentRow).val();
    $('#vws_current_FaltasTocados'+workingData.vws_currentRow).val(parseInt(flt)+parseInt(toc));
    // fix refusals and times
    if (data["Rehuses"]!=-1) 	$('#vws_current_Rehuses'+workingData.vws_currentRow).val(data["Rehuses"]);
    if (data["Tiempo"]!=-1) 	$('#vws_current_Tiempo'+workingData.vws_currentRow).val(data["Tiempo"]);
    if (data["TIntermedio"]!=-1) $('#vws_current_TIntermedio'+workingData.vws_currentRow).val(data["TIntermedio"]);
    if (data["Eliminado"]!=-1) $('#vws_current_Eliminado'+workingData.vws_currentRow).val(data["Eliminado"]);
    if (data["NoPresentado"]!=-1) $('#vws_current_NoPresentado'+workingData.vws_currentRow).val(data["NoPresentado"]);
    // handle eliminated and not presented overriding (if needed ) Puesto field
    vws_displayData(workingData.vws_currentRow,(data["tiempo"]>=0));
}

function vws_updateChronoData(data) { vws_updateData(data); } // just call updateData as from tablet

/**
 * evaluate position in hall of fame (final results)
 * When chrono stops this script is invoked instead of vwcf_evalPenalization()
 * Do not evaluate trs/trm. just iterate on datagrid results to find position
 * @param {boolean} flag display on/off
 * @param {float} time measured from chrono (do not read html dom content)
 * @param {boolean} final evaluate final or partial position
 */
function vws_displayPuesto(flag,time,final) {
    var cur=workingData.vws_currentRow;

    // if requested, or test dog (id==0) do not try to evaluate final scores
    var perro=$('#vws_current_Perro'+cur).val();
    if (typeof(perro)==="undefined") perro=0;
    if (!flag || (perro==0)) { $('#vws_current_Result'+cur).html(''); return; }

    // use set timeout to make sure data are already refreshed
    setTimeout(function(){
        // phase 1 retrieve results
        // use text() instead of html() avoid extra html code
        var datos= {
            'Perro':	perro,
            'Categoria':$('#vws_current_Categoria'+cur).val(),
            'Grado':	$('#vws_current_Grado'+cur).val(),
            'Faltas':	$('#vws_current_Faltas'+cur).val(),
            'Tocados':	$('#vws_current_Tocados'+cur).val(),
            'Rehuses':	$('#vws_current_Rehuses'+cur).val(),
            'Eliminado':$('#vws_current_Eliminado'+cur).val(),
            'NoPresentado':$('#vws_current_NoPresentado'+cur).val(),
            'Tiempo':	time
        };
        // phase 2: do not call server if not presentado ( but do it on eliminated (as final score to evaluate first round )
        if (datos.NoPresentado=="1") {
            $('#vws_current_Result'+cur).html('<span class="blink" style="color:red;"><?php _e('NoPr');?>.</span>');// no presentado
            return;
        }
        // on eliminado, do not blink error, as we don't know data on the other round.
        // phase 3: call server to evaluate partial result position
        // notice that getPuestoFinal returns lowercase data ('mejortiempo,'penalizacion','puesto')
        if (final) {
            getPuestoFinal(datos,function(data,resultados){
                $('#vws_current_Result'+cur).html('- '+Number(resultados.puesto).toString()+' -');
            });
        } else {
            getPuestoParcial(datos,function(data,resultados){
                $('#vws_current_Result'+cur).html('- '+Number(resultados.puesto).toString()+' -');
            });
        }
    },0);
}
