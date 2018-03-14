/*
Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


/***************************************************************************************************************/
/*
* A very simple app to connect AgilityContest Event API with OBS Studio websocket remote protocol
* to enable handling of streaming just by controls from tablet and chronometer
*
* Requires nodejs >=7
* Required npm modules http sync-request querystring os ip obs-websocket-js
*
*/
/***************************************************************************************************************/
const querystring = require('querystring');
const http = require('http');
const OBSWebSocket = require('obs-websocket-js');
const obs = new OBSWebSocket();

// Video sources
var videoSources = {
    ChromaKey : {
        name: 'ChromaKey'
    },
    Panoramica: {
        name: 'Panoramica'
    },
    Salida: {
        name: 'Salida'
    },
    Pasarela: {
        name: 'Pasarela'
    }
};

var scenes = [
    'Principal',
    'Salida',
    'Listados'
];


function obs_setCurrentScene(scene) {
    obs.setCurrentScene({'scene-name':scene});
}

function obs_setSourceRender (source,visibility) {
    obs.setSceneItemProperties({scene:'Principal',item:{name:source,visible:visibility}});
}

var ac_config = {
	// OBS Studio parameters
	obshost: 'localhost:4444',
	obspass: '',

	// AgilityContest connection parameters
	ring: 1,
     hostname: '0.0.0.0', // set to '0.0.0.0' to let the app find server by itself
	// hostname: 'localhost',
	sessionID: 0, // will be evaluated from ac server response

	// variables to store start timestamp mark
	cronomanual: 0,
	cronoauto: 0,
	coursewalk: 0,
	running: false,
	pending_events: { 'llamada':null,'aceptar':null },
	// method to handle received events
	callback: event_parser
};

function obs_handlePendingEvent(event) {

    // console.log ("obs_handlePendingEvent called");
    // console.log ("event received: "+JSON.stringify(event));
    // console.log ("chrono running: "+ac_config.running);

    var last_event=ac_config.pending_events['llamada'];

    function obs_handleCallEvent() { // real parsing of 'llamada' event
        console.log(event['Type'] + " - Competitor call to ring");
        console.log("    Dog name  : "+ event.Nombre);
        console.log("    Long name : "+ event.NombreLargo);
        console.log("    Handler   : "+ event.NombreGuia);
        console.log("    Club      : "+ event.NombreClub);
        console.log("    Team      : "+ event.NombreEquipo);
        console.log("    Category  : "+ event.Categoria);
        console.log("    Grade     : "+ event.Grado);
        console.log("    Heat      : "+ event.Hot);

        console.log("    - Previously stored competitor data ");
        console.log("    Faults       : "+ event.Flt);
        console.log("    Touchs       : "+ event.Toc);
        console.log("    Refusals     : "+ event.Reh);
        console.log("    Eliminated   : "+ event.Eli);
        console.log("    Not present  : "+ event.NPr);
        console.log("    Time         : "+ event.Tim);
        ac_config.running=false;
        // OBS: switch to Scene 1
        obs_setCurrentScene('Principal');
        obs_setSourceRender('Pasarela',false);
    }

    switch(event['Type']) {
        case 'llamada':
            var flag=false;
            var eli=false;
            // notice 'Eli' instead of 'Eliminado' due to incomplete events handle (differs from gui apps)
            if (ac_config.pending_events['aceptar']===null) eli=false;
            else eli=(parseInt(ac_config.pending_events['aceptar']['Eli'])===1);
            // en pruebas equipos conjunta, se procesa como siempre
            // PENDING

            // si crono parado se procesa como siempre
            if (!ac_config.running) flag=true;
            // si crono corriendo pero ultimo no eliminado se procesa como siempre
            // esto ocurre cuando se da aceptar o se selecciona directamente un perro
            // para que el resultado quede como "pendiente"
            if (ac_config.running && !eli) flag=true;
            // si crono corriendo pero eliminado, se retiene la llamada
            if (ac_config.running && eli) flag=false;
            if (flag) { // procesamos evento de llamada y luego lo borramos
                obs_handleCallEvent();
                ac_config.pending_events['llamada']=null;
                // eliminamos ultimo evento "aceptar"
                ac_config.pending_events['aceptar']=null;
            }
            break;
        case 'stop':
        case 'crono_stop':
        case 'reset':
            // si llamada pendiente se procesa la llamada
            if (ac_config.pending_events['aceptar']!==null) obs_handleCallEvent();
            // eliminamos ultimo evento llamada
            ac_config.pending_events['aceptar']=null;
            break;
        default: console.log("unexpected call to handle pending event: "+event['Type']);
    }

}

var eventHandler= {
	'null': null,// null event: no action taken
	'init': function(event,time) { // operator starts tablet application
		console.log(event['Type'] + "- Tablet connected");
	},
	'open': function(event,time){
		console.log(event['Type'] + " - Round selected");
		console.log("    Contest : "+ event.NombrePrueba);
		console.log("    Journey : "+ event.NombreJornada);
		console.log("    Round   : "+ event.NombreManga);
		console.log("    Ring    : "+ event.NombreRing);
        ac_config.cronomanual=0;
		ac_config.cronoauto=0;
		ac_config.coursewalk=0;
		ac_config.running=false;
	},
	'close': function(event,time){ // no more dogs in tabla
		console.log(event['Type'] + " - Operator ends tablet session");
	},
	'datos': function(event,time) {      // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		console.log(event['Type'] + " - Competitor's data received");
		console.log("    Faults       : "+ event.Flt);
		console.log("    Touchs       : "+ event.Toc);
		console.log("    Refusals     : "+ event.Reh);
		console.log("    Eliminated   : "+ event.Eli);
		console.log("    Not present  : "+ event.NPr);
		console.log("    Time         : "+ event.Tim);
	},
	'llamada': function(event,time) {    // llamada a pista
        // check crono and eliminated status before doing anything
		obs_handlePendingEvent(event);
	},
	'salida': function(event,time){     // orden de salida
		console.log(event['Type'] + " - Start 15 seconds countdown");
	},
	'start': function(event,time) {      // start crono manual
		console.log(event['Type'] + " - Manual chrono started");
        console.log("    Value: "+event.Value);
        ac_config.cronomanual=parseInt(event.Value);
        ac_config.running=true;

        // OBS: Show start main scene scene
        obs_setCurrentScene('Principal');
        obs_setSourceRender('Pasarela',false);

	},
	'stop': function(event,time){      // stop crono manual
		console.log(event['Type'] + " - Manual chrono stopped");
        console.log("    Value: "+event.Value);
        console.log("    Time:  "+(parseInt(event.Value)-ac_config.cronomanual).toString()+" msecs");
        ac_config.running=false;

        // OBS: Show start scene after 2 second to allow show timer
        setTimeout(function(){obs_setCurrentScene('Salida')},2000);
	},
	// nada que hacer aqui: el crono automatico se procesa en el tablet
	'crono_start':  function(event,time){ // arranque crono automatico
		console.log(event['Type'] + " - Electronic chrono starts");
        console.log("    Value: "+event.Value);
        ac_config.cronoauto=parseInt(event.Value);
        ac_config.running=true;

        // OBS: switch to Main. Just to be sure in case of user forgets to play
		obs_setCurrentScene('Principal');
        obs_setSourceRender('Pasarela',false);
	},
	'crono_restart': function(event,time){	// paso de tiempo manual a automatico
		console.log(event['Type'] + " - Manual-to-Electronic chrono transition");
        console.log("    Start: "+event.start);
        console.log("    Stop:  "+event.stop);
        ac_config.running=true;
	},
	'crono_int':  	function(event,time){	// tiempo intermedio crono electronico
		console.log(event['Type'] + "- Intermediate time mark");
        console.log("    Value:             "+event.Value);
        console.log("    Intermediate Time: "+(parseInt(event.Value)-ac_config.cronoauto).toString()+" msecs");
        ac_config.running=true;
	},
	'crono_stop':  function(event,time){	// parada crono electronico
		console.log(event['Type'] + " - Electronic chrono stop");
        console.log("    Value: "+event.Value);
        console.log("    Time:  "+(parseInt(event.Value)-ac_config.cronoauto).toString()+" msecs");
        ac_config.running=false;

        // OBS: Show start scene after 2 second to allow show timer
        setTimeout(function(){obs_setCurrentScene('Salida')},2000);

        // OBS: and switch to next competitor if pending
        obs_handlePendingEvent(event);
	},
	'crono_reset':  function(event,time){	// puesta a cero del crono electronico
		console.log(event['Type'] + "- Reset all counters");
        ac_config.cronomanual=0;
		ac_config.cronoauto=0;
		ac_config.coursewalk=0;
        ac_config.running=false;

        // OBS: switch to next competitor if pending
        obs_handlePendingEvent(event);
	},
	'crono_dat': function(event,time) {      // actualizar datos -1:decrease 0:ignore 1:increase
		console.log(event['Type'] + " - Competitor data from electronic chronometer");
		console.log("    Faults       : "+ event.Flt);
		console.log("    Touchs       : "+ event.Toc);
		console.log("    Refusals     : "+ event.Reh);
		console.log("    Eliminated   : "+ event.Eli);
		console.log("    Not present  : "+ event.NPr);
		console.log("    Interm. time : "+ event.TInt);
		console.log("    Time         : "+ event.Tim);
	},
	'crono_rec':  function(event,time) { // course walk
		var val=event.start;
		if (val===0) {
			var elapsed=(parseInt(event.Value)-ac_config.coursewalk)/1000;
			ac_config.coursewalk=0;
			console.log(event['Type'] + " - Course walk stop");
            console.log("    Timestamp value:"+event.Value);
			console.log("    Elapsed time: "+elapsed+" secs.");
		} else {
			ac_config.coursewalk=parseInt(event.Value);
			console.log(event['Type'] + " - Course walk start");
            console.log("    Timestamp value:"+event.Value);
			console.log("    Remaining Time: "+val+" secs");
		}
	},
	'crono_error':  function(event,time) { // fallo en los sensores de paso
		var val=parseInt(event.Value);
		console.log(event['Type'] + " - Chronometer notifies sensor error event:"+val);
        if (val===1) console.log("    Sensor status: Failed");
        else console.log("    Sensor status: OK");
	},
	'crono_ready':  function(event,time) { // crono activo y escuchando
		var val=parseInt(event.Value);
		console.log(event['Type'] + " - Chronometer notifies chrono is ready and listening state:"+val);
		if (val===1) console.log("    Sensor status: Failed");
		else console.log("    Sensor status: OK");
	},
    'user':  function(event,time) { // user defined event
        var val=parseInt(event.Value);
        console.log(event['Type'] + " - User defined event: "+val);
        switch(val) {
            case 0: // F1
                obs_setCurrentScene('Salida');
                break;
            case 1: // F2
                obs_setCurrentScene('Principal');
                obs_setSourceRender('Pasarela',false);
                break;
            case 2: // F3 --> show pasarela
                obs_setSourceRender('Pasarela',true);
                break;
            case 3: // F4 -->hide pasarela
                obs_setSourceRender('Pasarela',false);
                break;
        }
    },
	'aceptar':	function(event,time){ // operador pulsa aceptar
		console.log(event['Type'] + " - Assistant console operator accepts competitor result");
        console.log("Stored competitor data:");
        console.log("    Faults       : "+ event.Flt);
        console.log("    Touchs       : "+ event.Toc);
        console.log("    Refusals     : "+ event.Reh);
        console.log("    Eliminated   : "+ event.Eli);
        console.log("    Not present  : "+ event.NPr);
        console.log("    Interm. time : "+ event.TInt);
        console.log("    Time         : "+ event.Tim);
	},
	'cancelar': function(event,time){  // operador pulsa cancelar
		console.log(event['Type'] + " - Assistant console operator cancels current competitor data");
	},
	'camera':	function(event,time){ // change video source
		console.log(event['Type'] + "- Video source for embedded livestream screans changed");
	},
    'command':	function(event,time) {  // videowall remote control
        console.log(event['Type'] + " - Received remote comand from main console");
        var str=""+event['Oper']+"EVT_CMD_UNKNOWN";
        switch (parseInt(event['Oper'])) {
			case 0: str= "0 EVTCMD_NULL"; break;
            case 1: str= "1 EVTCMD_SWITCH_SCREEN"; break;
            case 2: str= "2 EVTCMD_SETFONTFAMILY"; break;
            case 3: str= "3 EVTCMD_NOTUSED3"; break;
            case 4: str= "4 EVTCMD_SETFONTSIZE"; break;
            case 5: str= "5 EVTCMD_OSDSETALPHA"; break;
            case 6: str= "6 EVTCMD_OSDSETDELAY"; break;
            case 7: str= "7 EVTCMD_NOTUSED7"; break;
            case 8: str= "8 EVTCMD_MESSAGE"; break;
            case 9: str= "9 EVTCMD_ENABLEOSD"; break;
		}
        console.log("Command: "+str+ "Value: "+event['Value']);
    },
    'reconfig':	function(event,time) {  // reload configuration from server
        console.log(event['Type'] + " - Configuration changed from main console");
    },
	'info':	function(event,time) { // click on user defined tandas
		console.log(event['Type'] + " - Assistant console operator choose a --sin asignar-- ring round");
		console.log("    Contest : "+ event.NombrePrueba);
		console.log("    Journey : "+ event.NombreJornada);
		console.log("    Round   : "+ event.NombreManga);
		console.log("    Ring    : "+ event.NombreRing);
	}
};

/**
 * Generic event handler browser-less event monitor
 * Every screen has a 'eventHandler' table with pointer to functions to be called
 * @param {int} id Event ID
 * @param {array} event Event data
 */
function event_parser(id,event) {
	event['ID']=id; // fix real id on stored eventData
    ac_config.pending_events[event['Type']]=event; // store received event
	var time=event['Value'];
	if (typeof(eventHandler[event['Type']])==="function") eventHandler[event['Type']](event,time);
}

/**
 * Try to connect AgilityContest server.
 * on success retrieve Session ID and update working Data
 * @param hostaddr IP address of host to check
 * @param ring Ring number ( 1..4, different from SessionID )
 * @return true on success; otherwise false
 */
function connectServer(hostaddr,ring){
    var url="http://"+hostaddr+"/agility/server/database/sessionFunctions.php?Operation=selectring";
    var request = require('sync-request');
    try {
        var res = request('GET', url, {
            timeout: 250,
            headers: { // this is for some routers that return "Auth required" to fail and send proper error code
                authorization: 'Basic ' + new Buffer('AgilityContest' + ':' + 'AgilityContest', 'ascii').toString('base64')
            }
        });
        if (res.statusCode!==200) return false; // http request failed
        console.log("Found AgilityContest server at: "+hostaddr);
        var data = JSON.parse(res.getBody('utf8'));
        // this code assumes that first returned row matches ring 1, second ring 2 and so
        ac_config.hostname=hostaddr;
        ac_config.sessionID=parseInt(data['rows'][ring-1]['ID']);
        console.log("SessionID for ring:"+ring+" is:"+ac_config.sessionID);
        return true;
    } catch (err) {
        console.log("Host: '"+hostaddr+"' Error: "+err);
        return false;
    }
}

/**
 * This function explores network trying to locate an AgilityContest server
 * When found, deduce desired sesion ID and set up working data parameters
 *
 * Notice that due to async nature of http requests, a dirty trick is needed
 * to wait for host polling to finish. this should be revisited in a later revision
 *
 * @param {int} ring Ring number (1..4) to search their sessionID for
 * @returns {boolean} true when server and session found. otherwise false
 */
function findServer(ring) {
	var addresses = []; // to be evaluated

	// locate any non local interface ipaddress/mask
	// take care on multiple interfaced hosts
	function getInterfaces() {
		var os = require('os');
		var interfaces = os.networkInterfaces();
		for (var k in interfaces) {
			for (var k2 in interfaces[k]) {
				var address = interfaces[k][k2];
				if (address.family === 'IPv4' && !address.internal) {
					addresses.push(address);
				}
			}
		}
	}

	getInterfaces(); // retrieve network interface information
	var ip=require('ip');
	for (var item in addresses) { // iterate on every ip/mask found
		var ipaddr=addresses[item].address;
		var mask=addresses[item].netmask;
		var start=ip.toLong(ip.subnet(ipaddr,mask).firstAddress);
		var stop=ip.toLong(ip.subnet(ipaddr,mask).lastAddress);
		for (var n=start;n<=stop;n++) {
			var hostname=ip.fromLong(n).toString();
			if (connectServer(hostname,ring) ) return true;
		}
	}
	// arriving here means server not found. Notify and return
	console.log("Cannot locate server. exiting");
	return false;
}


/**
 * This function call to server and waits for events to be returned ( or empty data on timeout )
 * When one or several events are received iterate on them by calling callback function declared
 * in ac_config structure
 * @param evtID Last received event ID
 * @param timestamp last timestamp mark received from server
 */
function waitForEvents(evtID,timestamp){

    var postData = querystring.stringify({
        'Operation'   : 'getEvents',
        'ID'		  : evtID,
        'Session'     : ac_config.sessionID,
        'TimeStamp'   : timestamp
    });

    var options = {
        protocol: 'http:',
        hostname: ac_config.hostname,
        port: 80,
        path: '/agility/server/database/eventFunctions.php',
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Content-Length': postData.length
        }
    };

	function handleSuccess(data){
        var response=JSON.parse(data);
		var timestamp= response.TimeStamp;
		var lastID=evtID;
		for (var n=0;n<parseInt(response.total);n++) {
			var row=response.rows[n];
			lastID=row['ID'];// store last evt id
			if (row['Type']==='reconfig') setTimeout(loadConfiguration,0);
			else ac_config.callback(lastID,JSON.parse(row['Data']));
		}
		// re-queue event
		setTimeout(function(){ waitForEvents(lastID,timestamp);},1000);
	}

	function handleError(data) {
		console.log("WaitForEvents() Error: response.message");
		setTimeout(function(){ waitForEvents(evtID,timestamp);},5000); // retry in 5 seconds
	}

    var request = http.request(options);
    request.on('response', function (response) {
        var body = '';
        response.on('data', function (chunk) {
            body += chunk;
        });
        response.on('end', function () {
            handleSuccess(body);
        });
    });
    request.on('error',handleError);

    // write data to request body
    request.write(postData);
    request.end();
}

/**
 * Call "connect" to retrieve last "open" event for provided session ID
 * fill working data with received info
 * If no response wait two seconds and try again
 * On sucess invoke waitForEvents and enter in event parsing loop
 */
function startEventMgr() {

    var postData = querystring.stringify({
        'Operation':'connect',
        'Session': ac_config.sessionID
    });

    var options = {
        protocol: 'http:',
        hostname: ac_config.hostname,
        port: 80,
        path: '/agility/server/database/eventFunctions.php',
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Content-Length': postData.length
        }
    };

    var req = http.request(options,function(res){
        res.setEncoding('utf8');
        res.on('data', function(data) {
            var response=JSON.parse(data);
            var timeout=5000;
            if (typeof(response['errorMsg'])!=="undefined") {
                console.log(response['errorMsg']);
                setTimeout(function(){ startEventMgr();},timeout );
                return;
            }
            if ( parseInt(response['total'])!==0) {
                var row=response['rows'][0];
                var evtID=parseInt(row['ID'])-1; /* dont loose first "init" event */
				console.log("Connected to AgilityContest Event bus");
                setTimeout(function(){ waitForEvents(evtID,0);},0);
            } else {
                setTimeout(function(){ startEventMgr(); },timeout );
            }
        });
        res.on('end', function() { /* empty */  });
    });

    req.on('error',function(e){
        console.log('problem with request: ' + e.message);
        setTimeout(function(){ startEventMgr(); },5000 );
    });

    // write data to request body
    req.write(postData);
    req.end();

	return false;
}

// try to connect to OBS-Studio
obs.connect({ address: ac_config.obshost, password: ac_config.obspass });

// Declare some events to listen for.
obs.onConnectionOpened(function() {
    console.log('OBS Studio Connection Opened');

    // retrieve scene list (PENDING: store for later use )
    obs.getSceneList(null, function (err, data) { console.log(err, data); });

    // not really needed, just for sample
    //  obs.onSwitchScenes(function (err, data) { console.log(err, data); });
});

// if AgilityContest server address not declared try to locate server. on fail exit
if (ac_config.hostname==='0.0.0.0') {
	if (!findServer(ac_config.ring)) process.exit(1);
} else {
    if (!connectServer(ac_config.hostname,ac_config.ring)) process.exit(1);
}

// start event manager
startEventMgr();
