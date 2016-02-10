/*
events.js

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

var ac_eventHandlers = {};

/**
 * As Function.name is not (yet) standard, use this ad-hoc method to obtain function name
 */
function getFunctionName(fn) {
	 var f = typeof fn == 'function';
	 var s = f && ((fn.name && ['', fn.name]) || fn.toString().match(/function ([^\(]+)/));
	 return (!f && 'not a function') || (s && s[1] || 'anonymous'); 
}

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

/** 
 * Call "connect" to retrieve last "open" event for provided session ID
 * fill working data with received info
 * If no response wait two seconds and try again
 * On sucess invoke                                                                                                                                                                                                                            
 * @param sesID
 * @param callback
 */
function startEventMgr(sesID,callback) {
	var timeout=2000;
	$.ajax({
		type: "GET",
		url: "/agility/server/database/eventFunctions.php",
		data: {
			'Operation' : 'connect',
			'Session'	: sesID
		},
		async: true,
		cache: false,
		dataType: 'json',
		success: function(response){
			if ( response['total']!=0) {
				var row=response['rows'][0];
				var evtID=row['ID'];
				var name=getFunctionName(callback);
				initWorkingData(row['Session']);
				ac_eventHandlers[name]=500; // recall getEvents() every 500 msecs
				setTimeout(function(){ waitForEvents(sesID,evtID,0,callback);},0);
			} else {
				setTimeout(function(){ startEventMgr(sesID,callback);},timeout );
			}
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			alert("error: "+textStatus + " "+ errorThrown );
			setTimeout(function(){  startEventMgr(sesID,callback);},timeout );
		}
	});
}

function stopEventMgr(callback) {
	var name=getFunctionName(callback);
	ac_eventHandlers[name]=-1; // mark stop polling on this callback
}

function waitForEvents(sesID,evtID,timestamp,callback){
	$.ajax({
		type: "GET",
		url: "/agility/server/database/eventFunctions.php",
		data: {
			'Operation' : 'getEvents',
			'ID'		: evtID,
			'Session'	: sesID,
			'TimeStamp' : timestamp
		},
		async: true,
		cache: false,
		success: function(data){
			var response= parseEvent(data);
			var timestamp= response['TimeStamp'];
			$.each(response['rows'],function(key,value){
				evtID=value['ID']; // store last evt id
				if (value['Type']==='reconfig') setTimeout(loadConfiguration,0);
				else callback(evtID,value['Data']);
			});
			// analyze event handler list to get poll time
			var name=getFunctionName(callback);
			if (typeof(ac_eventHandlers[name])==="undefined"){
				ac_eventHandlers[name]=500; // mark callback's eventhandler to be called every 500 msecs
			}
			if (ac_eventHandlers[name]<0) { // callback's event handler marked to stop polling
				console.log("Closing event handler: "+name);
				return;
			}
			// re-queue event
			setTimeout(function(){ waitForEvents(sesID,evtID,timestamp,callback);},ac_eventHandlers[name]);
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			// alert("error: "+textStatus + " "+ errorThrown );
			setTimeout(function(){ waitForEvents(sesID,evtID,timestamp,callback);},5000); // retry in 5 seconds
		}
	});
}