/*
qrcode.js

Copyright  2013-2020 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
header('Content-Type: text/javascript');
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
$config =Config::getInstance();
?>

var lastQRCodeReceived;
var lastDorsalSent=0;

workingData.qrcodeData= {
    'Prueba':		0,
    'Jornada':		0,
    'Manga':		0,
    'Tanda':		"",
    'Perro':		0,
    'Equipo':		0,
    'Dorsal':		0, // to be set by qrcode reader
    'Celo':			0,
    'Categoria':	"-", // to be set by qrcode reader
    'Grado':		"-" // to be set by qrcode reader
};

function qrcode_putEvent(){
    // setup default elements for this event
    var obj= {
        'Operation':'putEvent',
        'Type': 	'llamada',
        'TimeStamp': Math.floor(Date.now() / 1000),
        'Source':	ac_clientOpts.Source,
        'Destination': ac_clientOpts.Destination, /* not specified: use name or session */
        'Session':	ac_clientOpts.Ring,
        'Name':     ac_clientOpts.Name,
        'SessionName': ac_clientOpts.SessionName,
        'Value':	0 // may be overriden with 'data' contents
    };
    // send "update" event to every session listeners
    var dta=$.extend({},obj,workingData.qrcodeData);
    // console.log("putEvent: "+JSON.stringify(dta));
    $.ajax({
        type:'GET',
        url:"../ajax/database/eventFunctions.php",
        dataType:'json',
        timeout: 5000, // response should arrive in this time. more delay usually means connection problem
        data: dta,
        // on system errors ( connection lost, timeouts, or so ) display an alarm
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            if (errorThrown.indexOf("imeout")>=0) {
                $.messager.show({
                    title: "Timeout",
                    msg: '<?php _e("No server response");?><br/><?php _e("Please, check connection");?>',
                    timeout: 1500,
                    showType: 'slide',
                    style:{ right:'', bottom:'' }
                });
            } else {
                $.messager.show({
                    title:'putEvent',
                    msg:'tablet::putEvent( '+type+' ) error: '+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus + ' '+ errorThrown,
                    timeout:500,
                    showType:'slide'
                });
            }
        }
    });
}

function handleReceivedData(msg) {
    if (msg===lastQRCodeReceived) return;
    lastQRCodeReceived=msg;
    beep();
    // received data is in format [Dorsal,DogID]
    let data=JSON.parse(msg);
    $('#qr_dorsal').textbox('setValue',data[0]);
    $('#qr_ID').val(data[1]);
    // call to server to retrieve remaining data
    $.ajax({
        type: "GET",
        url: '../ajax/database/dogFunctions.php',
        data: {
            Operation : 'getbyidperro',
            Federation: workingData.federation,
            ID	: data[1]
        },
        async: true,
        cache: false,
        dataType: 'json',
        success: function(res){
            $('#qr_perro').textbox('setValue',res["Nombre"]);
            $('#qr_cat').textbox('setValue',res["Categoria"]+" - "+res["Grado"] );
            $('#qr_guia').textbox('setValue',res["NombreGuia"]);
            $('#qr_club').textbox('setValue',res["NombreClub"]);
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            alert("c_showData() error: "+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus + " "+ errorThrown );
        }
    });
}

function qrcode_clear() {
    $('#scanned').form('clear');
    lastQRCodeReceived="";
}

function qrcode_send(){
    if (lastDorsalSent===workingData.qrcodeData['Dorsal']) return; // already sent
    lastDorsalSent=workingData.qrcodeData['Dorsal'];
    qrcode_putEvent();
}

// set prueba/jornada/manga to event values ( event may be null )
function handle_openclose(event) {
    $('#qrcode_prueba').html( (event===null)?'':event['NombrePrueba']);
    $('#qrcode_ring').html( (event===null)?'':event['NombreRing']);
    $('#qrcode_jornada').html( (event===null)?'':event['NombreJornada']);
    $('#qrcode_manga').html( (event===null)?'':event['NombreManga']);
    $('#qrcode_runningdog').html('');
    if(event!==null) {
        workingData.qrcodeData['Prueba']=event['Pru'];
        workingData.qrcodeData['Jornada']=event['Jor'];
        workingData.qrcodeData['Manga']=event['Mng'];
        workingData.qrcodeData['Tanda']=event['Tnd'];
        workingData.qrcodeData['Dorsal']=0;
    }
}

function handle_llamada(event) {
    // really only is needed dogID and Dorsal. Rest of data are sent for legibility in logs
    $('#qrcode_runningdog').html( (event===null)?'':event['Drs']+" - "+event['Nombre'] );
    if (event!==null) workingData.qrcodeData['Dorsal']=event['Drs'];
    if (event!==null) workingData.qrcodeData['Perro']=event['Dog'];
    if (event!==null) workingData.qrcodeData['Celo']=event['Hot'];
    if (event!==null) workingData.qrcodeData['Categoria']=event['Categoria'];
    if (event!==null) workingData.qrcodeData['Grado']=event['Grado'];
    if (event!==null) workingData.qrcodeData['Equipo']=event['Eqp'];
}

function handle_init(event) {
    $.messager.show({
        title:'Init',
        msg:'Session started from '+event['Source'],
        timeout:2000,
        showType:'slide'
    });
    handle_openclose(null); // also clears running dog
}

// on qrcode reader we only use open,close, and llamada
var eventHandler= {
    null:       null, // null event: no action taken
    init:       function(event) { // open session
        let src=event['Source'].toLowerCase();
        if (src.indexOf('tablet')<0) return; // ignore init events that doesn't come from tablet
        handle_init(event);
    },
    open:       function(event){ handle_openclose(event); }, // operator select tanda
    close:      function(event){ handle_openclose(null); }, // no more dogs in tanda
    datos:      null, // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
    llamada:    function(event) { // llamada a pista
        let src=event['Source'].toLowerCase();
        if (src.indexOf('tablet')<0) return; // ignore init events that doesn't come from tablet
        handle_llamada(event);
    },
    salida:     null, // orden de salida
    start:      null, // start crono manual
    stop:       null, // stop crono manual
    crono_start:    null, // arranque crono automatico
    crono_restart:  null,// paso de tiempo intermedio a manual
    crono_int:  	null, // tiempo intermedio crono electronico
    crono_stop:     null, // parada crono electronico
    crono_reset:    null, // puesta a cero del crono electronico
    crono_error:    null, // fallo en los sensores de paso
    crono_dat:      null, // datos desde crono electronico
    crono_ready:    null, // chrono ready and listening
    user:       null, // user defined event
    aceptar:	null, // operador pulsa aceptar
    cancelar:   null, // operador pulsa cancelar
    camera:	    null, // change video source
    command:    function(event){ // videowall remote control
        handleCommandEvent
        (
            event,
            [
                /* EVTCMD_NULL:         */ function(e) {console.log("Received null command"); },
                /* EVTCMD_SWITCH_SCREEN:*/ null,
                /* EVTCMD_SETFONTFAMILY:*/ null,
                /* EVTCMD_NOTUSED3:     */ null,
                /* EVTCMD_SETFONTSIZE:  */ null,
                /* EVTCMD_OSDSETALPHA:  */ null,
                /* EVTCMD_OSDSETDELAY:  */ null,
                /* EVTCMD_NOTUSED7:     */ null,
                /* EVTCMD_MESSAGE:      */ function(e) {console_showMessage(e); },
                /* EVTCMD_ENABLEOSD:    */ null
            ]
        )
    },
    reconfig:	function(event) { loadConfiguration(); }, // reload configuration from server
    info:	    null // click on user defined tandas
};

/**
 * Generic event handler for VideoWall and LiveStream screens
 * Every screen has a 'eventHandler' table with pointer to functions to be called
 * @param id {number} Event ID
 * @param evt {object} Event data
 */
function qrcode_eventManager(id,evt) {
    var event=parseEvent(evt); // remember that event was coded in DB as an string
    event['ID']=id; // fix real id on stored eventData
    var time=event['Value'];
    if (typeof(eventHandler[event['Type']])==="function") {
        setTimeout(function() {
            eventHandler[event['Type']](event,time);
        }, 5); // 5 seconds between every parsed event
    }
}