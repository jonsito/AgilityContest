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
            // fill "after" columns
            for(var n=0;n<nitems;n++) {
                $('#vws_call_'+n).form('load',dat['after'][n]);
            }
            // fill "current" columns
            // fill "after" ( but will be revisited on updateResults )
        }
    });
}