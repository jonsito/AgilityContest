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
    console.log("Equipos:"+team+"\n"+JSON.stringify(data));
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
 */
function vws_updateLlamada(evt,data) {
    var team=isJornadaEquipos();
    var nitems=(team)?5:8;
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
        }
    });
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