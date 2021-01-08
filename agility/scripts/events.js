/*
events.js

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

var EVTCMD_NULL=0; // nothing; just ping
var EVTCMD_SWITCH_SCREEN=1; // switch videowall mode
var EVTCMD_SETFONTFAMILY=2;
var EVTCMD_NOTUSED3=3; // switch font family ) simplified videowalls )
var EVTCMD_SETFONTSIZE=4;
var EVTCMD_OSDSETALPHA=5; // increase/decrease OSD transparency level
var EVTCMD_OSDSETDELAY=6; // set response time to events ( to sync livestream OSD )
var EVTCMD_NOTUSED7=7;
var EVTCMD_MESSAGE=8; // prompt a message dialog on top of screen
var EVTCMD_ENABLEOSD=9; // enable / disable OnScreenDisplay

function parseEvent(data) {
	// var response= eval('(' + data + ')' );
	var response= JSON.parse(data);
	// si subconsulta expande
	if ( typeof(response.Data)==="undefined") {
		response.Prueba=response.Pru;
		response.Jornada=response.Jor;
		response.Manga=response.Mng;
		response.Tanda=response.Tnd;
		response.Perro=response.Dog;
		response.Dorsal=response.Drs;
		response.Celo=response.Hot;
		response.Faltas=response.Flt;
		response.Tocados=response.Toc;
		response.Rehuses=response.Reh;
		response.NoPresentado=response.NPr;
		response.Eliminado=response.Eli;
		response.Tiempo=response.Tim;
		response.Equipo=response.Eqp;
	}
	return response; 
}

// $(function(evtID,timestamp){
	function waitForEvents(evtID,timestamp,firstcall){
		// ejemplo: source:ringsessionID:view_type:round_mode:SessionName
		// source: videowall,tablet, chrono, xxxx
        // use inner vars to preserve scope in handleSuccess
		var mark=timestamp;
		var lastID=evtID;
		var fcall=firstcall;

		function handleSuccess(received,status,jqXHR){
			var data=JSON.parse(received);
			var row=null;
            mark=data.TimeStamp; // store last event timestamp
			for (var n=0;n<parseInt(data.total);n++) {
				var parse=true;
				row=data.rows[n];
                lastID=row.ID; // update last id
				switch(row.Type) {
					case 'reconfig' :
						// just call reconfiguration routine. Do not parse event
						setTimeout(loadConfiguration,0);
						continue;
					case 'init':
						// no break
					case 'open':
						if (ac_config.event_handler ) ac_config.event_handler(lastID,row.Data);
						break;
					default:
						// on first call ignore any event other than init or open
						if (! fcall && ac_config.event_handler) ac_config.event_handler(lastID,row.Data);
						break;
				}
			}
			// re-queue event if event handler is alive
			if (ac_config.event_handler!==null) {
                ac_config.backup_timeoutHandler = setTimeout(function(){ waitForEvents(lastID,mark,false);},1000);
            }
		}

		function handleError(XMLHttpRequest,textStatus,errorThrown) {
			// register and show error
			var msg= 'waitForEvent() error: '+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus + ' '+ errorThrown;
	        console.log(msg);
            $.messager.show({
                title: 'Error',
                msg: msg,
                timeout: 1000,
                showType: 'slide'
            });
			// and if event handler is still alive fire up again with extra delay
            if (ac_config.event_handler!==null){ // retry in 5 seconds
                ac_config.backup_timeoutHandler = setTimeout(function(){ waitForEvents(evtID,timestamp,fcall);},5000);
            }
		}

		$.ajax({
			type: "GET",
			url: "../ajax/database/eventFunctions.php",
			data: {
				Operation:	'getEvents',
				ID: 		evtID,
				Session:	workingData.session,
				TimeStamp:	mark,
				Source:		ac_clientOpts.Source,
				Destination: ac_clientOpts.Destination,
				Name: 		ac_clientOpts.Name,
				SessionName: ac_clientOpts.SessionName
			},
			async: true,
			cache: false,
			success: handleSuccess,
			error: handleError
		});
	}
	// waitForEvents(evtID,timestamp);
// });

/** 
 * Call "connect" to retrieve last "open" event for provided session ID
 * fill working data with received info
 * If no response wait two seconds and try again
 * On sucess invoke
 */
function startEventMgr() {
	// ejemplo: source:ringsesionID:view_type:round_mode:xxxxx@ipaddress
	// source: videowall,tablet, chrono, xxxx
	// xxxx: random(8)
    var sname=ac_clientOpts.Source+":"+ac_clientOpts.Ring+":"+ac_clientOpts.View+":"+ac_clientOpts.Mode+":"+ac_clientOpts.Name;
	$.ajax({
		type: "GET",
		url: "../ajax/database/eventFunctions.php",
		data: {
			Operation:	'connect',
			Session:	workingData.session,
            SessionName: sname,
			Source:		ac_clientOpts.Source,
			Destination: '', /* not specified */
			Name:		ac_clientOpts.Name
		},
		async: true,
		cache: false,
		dataType: 'json',
		success: function(response) {
			var timeout=5000;
			if (typeof(response['errorMsg'])!=="undefined") { // response indicates error, warn and try again
				console.log(response['errorMsg']);
				setTimeout(function(){ startEventMgr();},timeout );
				return;
			}
			if ( parseInt(response['total'])!==0) { // 'connect' ack. get data and start waiti events loop
				var row=response['rows'][0];
				var evtID=parseInt(row['ID'])-1; // make sure initial "init" event is received
                ac_config.backup_timeoutHandler = setTimeout(function(){ waitForEvents(evtID,0,true);},0);
			} else { // response has empty data: try again
				setTimeout(function(){ startEventMgr(); },timeout );
			}
		},
		error: function (XMLHttpRequest,textStatus,errorThrown) { // error in ajax call: notice and try again
			alert("startEventMgr() error: "+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus + " "+ errorThrown );
			setTimeout(function(){ startEventMgr(); },5000 );
		}
	});
	return false;
}

/**
 * Handle 'command' event
 * 'Operation' field in event args contains command to be parsed, 'Value' / 'start' / 'stop'  arguments, and so
 * @param {Array} event (id,value,start,stop, .... )
 * @param {Array} callbacks array functions
 */
function handleCommandEvent(event,callbacks) {
	var sessid=parseInt(event['Session']);
	var source=event['Source'];
	var destination=event['Destination'];
	var name=event['Name'];

	var isForMe=false;
	if (sessid===0) isForMe=true; /* broadcast */
	if (name==="") {
		if (sessid === parseInt(ac_clientOpts.Ring)) isForMe=true;
		if (destination === ac_clientOpts.Source) isForMe=true;
	}
	if (name===ac_clientOpts.Name) isForMe=true;
	// not for me, return
	if (!isForMe) return;
	// ok, it's for me: analyze
	var op=parseInt(event['Oper']);
	if ( typeof(callbacks[op]) !== "function") return; // function not declared int table
	setTimeout( function(){callbacks[op](event);},0);
}