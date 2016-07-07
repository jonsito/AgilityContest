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
 * Update header information
 * Fill contest, journey, round and sct according requested operation
 * @param {string} mode what to update
 * @param {object} data where to take information from
 */
function vws_updateHeader(mode,data) {
    mode=mode.toLowerCase();
    if (mode.indexOf("prueba")>=0) {
        var imgurl="/agility/images/logos/agilitycontest.png";
        if (parseInt(ac_config.vws_uselogo)) imgurl=ac_config.vws_logourl;
        $('#vws_hdr_logoprueba').val(imgurl);
        $('#vws_hdr_prueba').val(workingData.datosPrueba.Nombre);
        $('#vws_hdr_jornada').val(workingData.datosJornada.Nombre);
    }
    if (mode.indexOf("manga")>=0) { // fix round name
        var team=(isJornadaEquipos())?"<?php _e('Teams');?>":"<?php _e('Individual');?>";
        $('#vws_hdr_manga').val(data.Tanda.Nombre+" - "+team);
    }
    if (mode.indexOf("trs")>=0) { // fix sct/mct
        // current round is always first one:
        var trs=data.trs1.dist+ "m. / " +data.trs1.trs+"s.";
        $('#vws_hdr_trs').val(trs);
    }
}

function vws_setFinalIndividualOrTeamView(data) {
    var team=false;
    if (parseInt(data.Jornada.Equipos3)!=0) { team=true; }
    if (parseInt(data.Jornada.Equipos4)!=0) { team=true;  }
    // cargamos la pagina adecuada en funcion del tipo de evento
    var page='/agility/videowall/'+((team==true)?'vws_final_equipos.php':'vws_final_individual.php');
    $('#vws-window').window('refresh',page);
}


function vws_displayData(row,flag) {
    // faltas, tocados, rehuses y tiempo
    var f=parseInt($('#vws_current_Faltas'+row).val());
    var t=parseInt($('#vws_current_Tocados'+row).val());
    var r=parseInt($('#vws_current_Rehuses'+row).val());
    var tim=parseFloat($('#vws_current_Tiempo'+row).val());
    $('#vws_current_FaltasTocados'+row).html("F/T: "+(f+t));
    $('#vws_current_Refusals'+row).html("R: "+(r));
    if (flag) $('#vws_current_Time'+row).html("Time: "+ toFixedT(tim,ac_config.numdecs));
    // eliminado, no presentado, puesto
    var e=parseInt($('#vws_current_Eliminado'+row).val());
    var n=parseInt($('#vws_current_NoPresentado'+row).val());
    var p=parseInt($('#vws_current_Puesto'+row).val());
    var r=$('#vws_current_Result'+row);
    if (n>0) { r.html('<?php _e('NoPr');?>.'); return; }
    if (e>0) { r.html('<?php _e('Elim');?>.'); return; }
    r.html(p);
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
    var team=isJornadaEquipos();
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
                    // notice 'results' and 'LogoClub' as we need dog data, not team data
                    $('#vws_current_'+n).form('load',dat['results'][n]);
                    vws_displayData("_"+n,true);
                    // check for current dot to mark proper "current" row form as active
                    if (dat['results'][n]['Perro']==evt['Dog']) workingData.vws_currentRow="_"+n;
                }
                logo=dat['current'][0]['LogoTeam'];
                $('#vws_current_Logo_0').attr('src','/agility/images/logos/getLogo.php?Logo='+logo+'&Federation='+workingData.federation);
            } else {
                logo=dat['current'][0]['LogoClub'];
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
            if (typeof(callback)==="function") callback(data);
        }
    });
}

/**
 * funcion para rellenar los resultados en la pantalla simplificada
 * @param {object} data Datos de la sesion ( recibidos desde vws_updateWorkingData() )
 */
function vws_updateFinales(data) {
    // ajustamos contadores
    var team=isJornadaEquipos();
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
            Mode: data.Ronda.Mode
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
            vws_updateHeader('trs',dat);
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

function vwsf_evalPenalizacion() {

}

function vwsp_evalPenalizacion() {

}

/**
 * evaluate position in hall of fame (final results)
 * When chrono stops this script is invoked instead of vwcf_evalPenalization()
 * Do not evaluate trs/trm. just iterate on datagrid results to find position
 * @param {boolean} flag display on/off
 * @param {float} time measured from chrono (do not read html dom content)
 */
function vwsf_displayPuesto(flag,time) {
    
}

/**
 * evaluate position in hall of fame (final results)
 * When chrono stops this script is invoked instead of vwcf_evalPenalization()
 * Do not evaluate trs/trm. just iterate on datagrid results to find position
 * @param {boolean} flag display on/off
 * @param {float} time measured from chrono (do not read html dom content)
 */
function vwsp_displayPuesto(flag,time) {

}


function vwsf_evalPenalizacion() {
    
}