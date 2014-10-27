/** 
 * Call "connect" to retrieve last "open" event for provided session ID
 * fill working data with received info
 * If no response wait a second and try again
 * On sucess invoke                                                                                                                                                                                                                            
 * @param sesID
 * @param callback
 */
function startEventMgr(sesID,callback) {
	$.ajax({
		type: "GET",
		url: "/agility/server/database/eventFunctions.php",
		data: {
			'Operation' : 'connect',
			'Session'	: sesID
		},
		async: true,
		cache: false,
		success: function(data){
			var response= eval('(' + data + ')' );
			if ( response['total']!=0) {
				var row=response['rows'][0];
				var evtID=row['ID'];
				initWorkingData(row['Session']);
				setTimeout(function(){ waitForEvents(sesID,evtID,0,callback);},0);
			} else {
				setTimeout(function(){ startEventMgr(sesID,callback);},2000 );
			}
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			alert("error: "+textStatus + " "+ errorThrown );
			setTimeout(function(){  startEventMgr(sesID,callback);},2000 );
		}
	});
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
			var response= eval('(' + data + ')' );
			var timestamp= response['TimeStamp'];
			$.each(response['rows'],function(key,value){
				evtID=value['ID']; // store last evt id
				callback(value);
			});
			setTimeout(function(){ waitForEvents(sesID,evtID,timestamp,callback);},500);
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			// alert("error: "+textStatus + " "+ errorThrown );
			setTimeout(function(){ waitForEvents(sesID,evtID,timestamp,callback);},5000);
		}
	});
}

function vw_processLiveStream(event) {
	// alert(JSON.stringify(event));
}