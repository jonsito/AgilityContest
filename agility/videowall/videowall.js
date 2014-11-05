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
				callback(evtID,value['Data']);
			});
			setTimeout(function(){ waitForEvents(sesID,evtID,timestamp,callback);},500);
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			// alert("error: "+textStatus + " "+ errorThrown );
			setTimeout(function(){ waitForEvents(sesID,evtID,timestamp,callback);},5000);
		}
	});
}

function vwls_showOSD(val) {
	if (val==0) $('#vwls_common').css('display','none');
	else $('#vwls_common').css('display','initial');
}

function vwc_updateResults(event) {
	$.ajax( {
		type: "GET",
		dataType: 'html',
		url: "/agility/server/database/videowall.php",
		data: {
			Operation: 'resultados',
			Session: workingData.sesion
		},
		success: function(data,status,jqxhr) {
			$('#vwc_resultadosParciales').html(data);
		}
	});
}

function vwc_updatePendingQueue(event) {
	$.ajax( {
		type: "GET",
		dataType: 'html',
		url: "/agility/server/database/videowall.php",
		data: {
			Operation: 'llamada',
			Session: workingData.sesion
		},
		success: function(data,status,jqxhr) {
			$('#vwc_listaPendientes').html(data);
		}
	});
}

function vwls_updateData(data) {
	if (data["Faltas"]!=-1) $('#vwls_Faltas').html(data["Faltas"]);
	if (data["Tocados"]!=-1) $('#vwls_Tocados').html(data["Tocados"]);
	if (data["Rehuses"]!=-1) $('#vwls_Rehuses').html(data["Rehuses"]);
	if (data["Tiempo"]!=-1) $('#vwls_Tiempo').html(data["Tiempo"]);
	if (data["Eliminado"]==1)	$('#vwls_Tiempo').html('<span class="blink" style="color:red">Elim.</span>');
	if (data["NoPresentado"]==1) $('#vwls_Tiempo').html('<span class="blink" style="color:red">N.P.</span>');
}

function vwls_showData(data) {
	var perro=$('#vwls_Perro').html();
	var dorsal=data['Dorsal'];
	var celo=data['Celo'];
	if (perro!==data['Perro']) {
		// if datos del participante han cambiado actualiza
		$.ajax({
			type: "GET",
			url: "/agility/server/database/dogFunctions.php",
			data: {
				'Operation' : 'getbyidperro',
				'ID'	: data['Perro']
			},
			async: true,
			cache: false,
			dataType: 'json',
			success: function(data){
				$('#vwls_Logo').attr("src","/agility/images/logos/"+data['LogoClub']);
				$('#vwls_Dorsal').html("Dorsal: "+dorsal );
				$('#vwls_Nombre').html(data["Nombre"]);
				$('#vwls_NombreGuia').html("Guia: "+data["NombreGuia"]);
				$('#vwls_NombreClub').html("Club: "+data["NombreClub"]);
				$('#vwls_Categoria').html(data["NombreCategoria"].replace(/.* - /g,""));
				$('#vwls_Grado').html(data["NombreGrado"]);
				$('#vwls_Celo').html((celo==1)?'<span class="blink">Celo</span>':'');
			},
			error: function(XMLHttpRequest,textStatus,errorThrown) {
				alert("error: "+textStatus + " "+ errorThrown );
			}
		});
	}
	// actualiza resultados del participante
	$('#vwls_Faltas').html(data["Faltas"]);
	$('#vwls_Tocados').html(data["Tocados"]);
	$('#vwls_Rehuses').html(data["Rehuses"]);
	$('#vwls_Tiempo').html(data["Tiempo"]);
	if (data["Eliminado"]==1)	$('#vwls_Tiempo').html('<span class="blink" style="color:red">Elim.</span>');
	if (data["NoPresentado"]==1) $('#vwls_Tiempo').html('<span class="blink" style="color:red">N.P.</span>');
	
}

/**
 * activa una secuencia de conteo hacia atras de 15 segundos
 */
function vwls_counter(){
	var myCounter = new Countdown({  
	    seconds:15,  // number of seconds to count down
	    onUpdateStatus: function(sec){ $('#vwls_Tiempo').html(sec); }, // callback for each second
	    onCounterEnd: function(){  $('#vwls_Tiempo').html('<span class="blink" style="color:red">-out-</span>'); } // final action
	});
	myCounter.start();
}

/**
 * Maneja el cronometro manual
 * @param oper 'start','stop','pause','resume','reset'
 */
function vwls_cronoManual(oper) {
	$('#cronomanual').Chrono(oper);
}

function vwc_processCombinada(id,evt) {
	var event=eval('('+evt+')'); // remember that event was coded in DB as an string
	event['ID']=id;
	switch (event['Type']) {
	case 'null':		// null event: no action taken
		return; 
	case 'init':		// operator starts tablet application
		vwls_showOSD(0); 	// activa visualizacion de OSD
		return;
	case 'open':		// operator select tanda
		vwc_updateResults(event); // actualiza panel de resultados
		vwc_updatePendingQueue(event); // actualiza panel de llamadas 
		return;
	case 'datos':		// actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		vwls_updateData(event);
		return
	case 'llamada':		// operador abre panel de entrada de datos
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_showOSD(1); 	// activa visualizacion de OSD
		vwls_showData(event);
		return
	case 'salida':		// juez da orden de salida ( crono 15 segundos )
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_counter();
		return;
	case 'start':	// value: timestamp
		vwls_cronoManual('start');
		return;
	case 'stop':	// value: timestamp
		vwls_cronoManual('stop');
		return;
	case 'cronoauto':  	// value: timestamp
		return; // nada que hacer aqui: el crono automatico se procesa en el tablet
	case 'aceptar':		// operador pulsa aceptar
		vwls_cronoManual('stop');  // nos aseguramos de que los cronos esten parados
		vwls_showData(event); // actualiza pantall liveStream
		vwc_updateResults(); // actualiza panel de resultados
		return;
	case 'cancelar':	// operador pulsa cancelar
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_showOSD(0); // apaga el OSD
		return;
	}
}

function vwls_processLiveStream(id,evt) {
	var event=eval('('+evt+')'); // remember that event was coded in DB as an string
	event['ID']=id;
	switch (event['Type']) {
	case 'null':		// null event: no action taken
		return; 
	case 'init':		// operator starts tablet application
		vwls_showOSD(0); 	// activa visualizacion de OSD
		return;
	case 'open':		// operator select tanda: nothing to do here
		return;
	case 'datos':		// actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		vwls_updateData(event);
		return
	case 'llamada':		// operador abre panel de entrada de datos
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_showOSD(1); 	// activa visualizacion de OSD
		vwls_showData(event);
		return
	case 'salida':		// juez da orden de salida ( crono 15 segundos )
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_counter();
		return;
	case 'start':	// value: timestamp
		vwls_cronoManual('start');
		return;
	case 'stop':	// value: timestamp
		vwls_cronoManual('stop');
		return;
	case 'cronoauto':  	// value: timestamp nada que hacer
		return; // nada que hacer aqui: el crono automatico se procesa en el tablet
	case 'aceptar':		// operador pulsa aceptar
		vwls_cronoManual('stop');  // nos aseguramos de que los cronos esten parados
		vwls_showData(event); // actualiza pantall liveStream
		return;
	case 'cancelar':	// operador pulsa cancelar
		vwls_cronoManual('stop');
		vwls_cronoManual('reset');
		vwls_showOSD(0); // apaga el OSD
		return;
	}
}