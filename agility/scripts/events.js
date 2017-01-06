/*
events.js

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
	function waitForEvents(evtID,timestamp){

		function handleSuccess(data,status,jqXHR){
			var response= parseEvent(data);
			var timestamp= response.TimeStamp;
			var lastID=evtID;
			for (var n=0;n<parseInt(response.total);n++) {
				var row=response.rows[n];
				lastID=row['ID'];// store last evt id
				if (row['Type']==='reconfig') setTimeout(loadConfiguration,0);
				else workingData.datosSesion.callback(lastID,row['Data']);
			}
			// re-queue event
			setTimeout(function(){ waitForEvents(lastID,timestamp);},1000);
		}

		function handleError(data,status,jqXHR) {
			console.log(status);
			setTimeout(function(){ waitForEvents(evtID,timestamp);},5000); // retry in 5 seconds
		}

		$.ajax({
			type: "GET",
			url: "/agility/server/database/eventFunctions.php",
			data: {
				'Operation' : 'getEvents',
				'ID'		: evtID,
				'Session'	: workingData.sesion,
				'TimeStamp' : timestamp
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
	$.ajax({
		type: "GET",
		url: "/agility/server/database/eventFunctions.php",
		data: {
			'Operation' : 'connect',
			'Session'	: workingData.sesion
		},
		async: true,
		cache: false,
		dataType: 'json',
		success: function(response) {
			var timeout=5000;
			if (typeof(response['errorMsg'])!=="undefined") {
				console.log(response['errorMsg']);
				setTimeout(function(){ startEventMgr();},timeout );
				return;
			}
			if ( parseInt(response['total'])!=0) {
				var row=response['rows'][0];
				var evtID=parseInt(row['ID'])-1; // make sure initial "init" event is received
				setTimeout(function(){ waitForEvents(evtID,0);},0);
			} else {
				setTimeout(function(){ startEventMgr(); },timeout );
			}
		},
		error: function (XMLHttpRequest,textStatus,errorThrown) {
			alert("startEventMgr() error: "+textStatus + " "+ errorThrown );
			setTimeout(function(){ startEventMgr(); },5000 );
		}
	});
	return false;
}
