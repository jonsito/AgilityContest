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
 * Handle simplified panel header and footer
 * add proper logos, set texts, start footer sponsor rotation
 * @param {object} evt received 'init' event
 * @param {object} data data associated with event as received from setup_working_data()
 */
function vws_updateHeaderAndFooter(evt,data) {

}

function vws_setFinalIndividualOrTeamView(data) {
    var team=false;
    if (parseInt(data.Jornada.Equipos3)!=0) { team=true; }
    if (parseInt(data.Jornada.Equipos4)!=0) { team=true;  }
    // cargamos la pagina adecuada en funcion del tipo de evento
    var page='/agility/videowall/'+((team==true)?'vws_final_equipos.php':'vws_final_individual.php');
    $('#vws-window').window('refresh',page);
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
                workingData.vws_currentRow=null;
                dat['results'][0]['Orden']=dat['current'][0]['Orden']; // to properly fill form field
                for (n=0;n<4;n++) {
                    // notice 'results' and 'LogoClub' as we need dog data, not team data
                    dat['results'][n]['FaltasTocados']=parseInt(dat['results'][n]['Faltas'])+parseInt(dat['results'][n]['Tocados']);
                    $('#vws_current_'+n).form('load',dat['results'][n]);
                    // check for current dot to mark proper "current" row form as active
                    if (dat['results'][n]['Perro']==evt['Dog']) workingData.currentRow='#vws_current_'+n;
                }
                logo=dat['current'][0]['LogoTeam'];
                $('#vws_current_Logo_0').attr('src','/agility/images/logos/getLogo.php?Logo='+logo+'&Federation='+workingData.federation);
            } else {
                logo=dat['current'][0]['LogoClub'];
                dat['current'][0]['FaltasTocados']=parseInt(dat['current'][0]['Faltas'])+parseInt(dat['current'][0]['Tocados']);
                $('#vws_current').form('load',dat['current'][0]);
                workingData.currentRow='#vws_current';
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

            // rellenamos arrays 'result' y 'before'
            for (var n = 0; n < size; n++) {
                // el campo puesto no viene: lo obtenemos del orden de la lista
                items[n]['Puesto']=n+1;
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
            } // fill result & before
            // ahora indicamos puesto en el(los) campo(s) current, utilizando los datos de perros individuales
            for (n = 0; n < individual.length; n++) {
                var perro = individual[n]['Perro'];
                if (team) {
                    for (i = 0; i < 4; i++) {
                        if ($('#vws_current_Perro_' + i).val() != perro) continue;
                        $('#vws_current_Puesto' + i).val(individual[n]['Puesto']);
                    }
                } else {
                    if ($('#vws_current_Perro').val() != perro) continue;
                    $('#vws_current_Puesto').val(individual[n]['Puesto']);
                }
            } // fill current
        } // success
    }); // ajax
}

function vws_updateData(event) {
    
}

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