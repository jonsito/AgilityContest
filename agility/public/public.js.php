/*
public.js

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

/*********************************************** funciones de formateo de pantalla */
<?php
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
$config =Config::getInstance();
?>


/**
 * Obtiene la informacion de la prueba para cabecera y pie de pagina
 * @
 */
function pb_getHeaderInfo(showJourney) {
    if (typeof(showJourney)==="undefined") showJourney=true;
    $.ajax( {
        type: "GET",
        dataType: 'json',
        url: "/agility/server/web/publicFunctions.php",
        data: {
            Operation: 'infodata',
            Prueba: workingData.prueba,
            Jornada: workingData.jornada,
            Manga: workingData.manga,
            Tanda: workingData.tanda,
            Mode: workingData.mode
        },
        success: function(data,status,jqxhr) {
            var str= data.Prueba.Nombre;
            if (data.Jornada) { // training session has no journey :-)
                if (showJourney) str = str + '<br />' +  data.Jornada.Nombre;
            }
            $('#pb_header-infocabecera').html(str);
            // on international competitions, use federation Organizer logo
            var logo='/agility/images/logos/'+data.Club.Logo;
            if ( (data.Club.logo==="") || isInternational(data.Prueba.RSCE)) {
                logo=ac_fedInfo[data.Prueba.RSCE].OrganizerLogo
            }
            $('#pb_header-logo').attr('src',logo);
        }
    });
}

function pb_setFooterInfo() {
    var logo=ac_fedInfo[workingData.federation].Logo;
    var logo2=ac_fedInfo[workingData.federation].ParentLogo;
    var url=ac_fedInfo[workingData.federation].WebURL;
    var url2=ac_fedInfo[workingData.federation].ParentWebURL;
    $('#pb_footer-footerData').load("/agility/public/pb_footer.php",{},function(response,status,xhr){
        $('#pb_footer-logoFederation').attr('src',logo);
        $('#pb_footer-urlFederation').attr('href',url);
        $('#pb_footer-logoFederation2').attr('src',logo2);
        $('#pb_footer-urlFederation2').attr('href',url2);
    });
}

function pb_updateEntrenamientos() {
    $('#entrenamientos-datagrid').datagrid('reload');
}

function pb_updateOrdenSalida2(id) {
    $('#ordensalida-datagrid').datagrid('reload',{
        Operation: 'getDataByTanda',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 1, // defaults to "-- sin asignar --"
        ID:  id // Tanda ID
    });
}

/**
 * Imprime el orden de salida de la prueba y jornada seleccionada por el usuario
 * ejecutada desde la ventana con combogrid
 */
function pb_updateOrdenSalida() {
    var row=$('#pb_enumerateMangas').combogrid('grid').datagrid('getSelected');
    if (row) pb_updateOrdenSalida2(row.ID);
}

/**
 * Imprime los inscritos en la prueba y jornada seleccionada por el usuario
 */
function pb_updateInscripciones() {
    $('#pb_inscripciones-datagrid').datagrid('reload', {
        Operation:'inscritosbyjornada',
        Prueba:workingData.prueba,
        Jornada:workingData.jornada
    });
}

/**
 * Imprime los inscritos en la prueba y jornada por equipos seleccionada por el usuario
 */
function pb_updateInscripciones_eq3() {
    $('#pb_inscripciones_eq3-datagrid').datagrid('reload', {
        Operation:'select',
        Prueba:workingData.prueba,
        Jornada:workingData.jornada,
        where:'',
        HideDefault:1, // do not show default team
        AddLogo:1 // generate LogoTeam
    });
}

/**
 * imprime el programa de la jornada
 */
function pb_updatePrograma() {
    $('#pb_programa-datagrid').datagrid('reload',{
        Operation: 'getTandas',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 0 // Set Session ID to 0 to include everything
    });
}

/**
 * En funcion de public, videowall, tablet o livestream, ajustamos el datagrid y los contenidos
 * En funcion de federacion ajustamos, club, pais, categorias
 *
 * @param {object} dg jquery easyui datagrid object
 */
function pb_setTrainingLayout(dg) {
    $('#vw_header-infomanga').html("(<?php _e('No round selected');?>)");
    // fix country/club and reload datagrid
    dg.datagrid('setFieldTitle', {'field': 'NombreClub', 'title': clubOrCountry()});
    // en funcion de la federacion se ajusta el numero de categorias
    var cats = howManyHeights(workingData.federation);
    dg.datagrid((cats == 3) ? 'hideColumn' : 'showColumn', 'Value4');
    dg.datagrid('fitColumns');
}

function send_Notification(msg){
    if ( !('serviceWorker' in navigator) ) { // si no tenemos service worker pero si notificaciones
        return new Notification(msg,{icon: "/agility/images/logos/agilitycontest.png"});
    }
    // si tenemos service worker enviamos mensaje con el texto a presentar
    return new Promise(function(resolve, reject){
        var msg_chan = new MessageChannel(); // Create a Message Channel
        // Handler for recieving message reply from service worker
        msg_chan.port1.onmessage = function(event){
            if(event.data.error){ reject(event.data.error); }
            else { resolve(event.data); }
        };
        // Send message to service worker along with port for reply
        navigator.serviceWorker.controller.postMessage(msg, [msg_chan.port2]);
    });
}

/**
 * Call server for events
 * This is done every RefreshTime
 * Remember: internet events are not received by push, just by polling.
 * So need to last received event id to query news
 * At server side, receiving as last event "0" means return a "hello world" message with last event
 * for requested contest ( to avoid receiving all the contest event history )
 * @param {function} callback if defined, invoke instead of displaying mesage at bottom right
 */
function pb_lookForMessages(callback) {

    if (workingData.Prueba==0) return; // no choosen contest, so do not enable reception
    // call to server for new events
    $.ajax( {
        type: "GET",
        dataType: 'json',
        url: "/agility/server/web/publicFunctions.php",
        data: {
            Operation: 'getEvents',
            Prueba: pb_config.PruebaID,
            Jornada: workingData.jornada, // or cero, just ignored
            Manga: workingData.manga,
            Tanda: workingData.tanda,
            LastEvent: pb_config.LastEvent
        },
        success: function(data,status,jqxhr) {
            function isForMe(list) {
                var l=','+list+',';
                var me=','+$('#pbmenu-Dorsal').numberbox('getValue')+',';
                if (l.indexOf(',0,') >=0 ) return true; // 0 means "any"
                return ( l.indexOf(me) >= 0 );
            }

            if (data.errorMsg) {
                console.log("Error: "+data.errorMsg);
                return;
            }
            for (var n=0;n<data.total;n++) {
                // extract message
                var item=data.rows[n];
                // dorsal_list:timeout:message
                var a=item.Message.split(':');
                var msg=item.Message.substr(item.Message.lastIndexOf(':')+1);
                if (!isForMe(a[0]) ) continue;
                // store lastEvent and save msg into message buffer
                pb_config.ConsoleMessages +=
                    "<hr/>" + item.TimeStamp + "<br/>"  + msg +"<br/>&nbsp;<br/>";
                pb_config.LastEvent=item.LastEvent;

                // decide what to do: show message or call callback
                if (typeof (callback)!=="undefined") continue; // on callback defined do not notify

                // null:->do noting; true->notifications; false->messager
                // if system notifications are enabled, use it
                if (pb_config.Notifications===true) {
                    send_Notification(msg);
                }
                if (pb_config.Notifications===false) {
                    // otherwise show message in botton rignt corner
                    $.messager.show({
                        width: 300, height: 100, title:  "<?php _e('Message');?>",
                        msg: msg, timeout:1000*parseInt(a[1]), showType:'slide'
                    });
                }
            }
            if (typeof (callback)!=="undefined") callback();
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            alert("pb_lookForMessages() error: "+textStatus + " "+ errorThrown );
        }
    });

}

function pbmenu_displayNofifications() {
    pb_lookForMessages(function(){
            $.messager.alert({
                title: "<?php _e('Received messages');?>",
                icon: null,
                msg: '<div style="height:325px;overflow:auto">'+pb_config.ConsoleMessages+'</div>',
                width: 480,
                // height: 'auto',
                maxHeight: 425
            });
        }
    );
}


/**
 * Handle enable/disable notification button
 * On disable set to null
 * On enable, try to use system notifier; on unavailable or denied fall down into $.messager
 * Start timer to monitorize events
 */
function pbmenu_enableSystemNotifications() {
    // fire autorefresh if configured
    function pbmenu_notificationTimer() {
        // if asked to stop grant request
        var rtime=parseInt(ac_config.web_refreshtime);
        if ((rtime===0) || (pb_config.Notifications===null)) return;
        pb_lookForMessages();
        // re-trigger timeout
        setTimeout(pbmenu_notificationTimer,1000*rtime);
    }

    var bstate= $('#pbmenu-Notifications').prop('checked');
    if (bstate===false) {
        // disable notifications
        pb_config.Notifications=null;
        return;
    }

    // register service worker in client
    if('serviceWorker' in navigator){
        // Register service worker
        navigator.serviceWorker.register('/agility/service-worker.js').then(function(reg){
            console.log("SW registration succeeded. Scope is "+reg.scope);
        }).catch(function(err){
            console.error("SW registration failed with error "+err);
        });
    }

    // if browser support notifications use it; else use $.messager.show
    if (!("Notification" in window)) {
        $.messager.show({
            width: 300,
            height: 110,
            title:  'warn',
            msg:  '<?php _e("This browser does not support notifications");?><br/>'+
                    '<?php _e("Using internal messager");?>',
            timeout:1000,
            showType:'slide'
        });
        pb_config.Notifications = false; // mark use $.messager
    }
    // Let's check whether notification permissions have already been granted
    else if (Notification.permission === "granted") {
        // If it's okay let's create a notification
        pb_config.Notifications = true;
    }
     // Otherwise, we need to ask the user for permission
    else if (Notification.permission !== "denied") {
        Notification.requestPermission(function (permission) {
            if (permission === "granted") { // If the user accepts, let's create a notification
                pb_config.Notifications = true;
            } else { // user denied system notifications: use messager
                pb_config.Notifications = false;
            }
        });
    }

    // fire up timer
    var ntimer=parseInt(ac_config.web_refreshtime);
    if (ntimer!==0) setTimeout(pbmenu_notificationTimer,0); // do not wait for timeout
}

function pbmenu_notificationOptions() {
    // TO BE DONE
}