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

function vwc_showOSD(val) {
	if (val==0) $('#videowall_data').css('display','none')
	else $('#videowall_data').css('display','initial');
}

function vwc_updateResults() {
	// TODO:write
}

function vwc_updatePendingQueue(data) {
	// TODO: write
}

function vwc_updateData(data) {
	if (data["Faltas"]!=-1) $('#vwc_Faltas').html(data["Faltas"]);
	if (data["Tocados"]!=-1) $('#vwc_Tocados').html(data["Tocados"]);
	if (data["Rehuses"]!=-1) $('#vwc_Rehuses').html(data["Rehuses"]);
	if (data["Tiempo"]!=-1) $('#vwc_Tiempo').html(data["Tiempo"]);
	if (data["Eliminado"]==1)	$('#vwc_Tiempo').html('<span class="blink" style="color:red">Elim.</span>');
	if (data["NoPresentado"]==1) $('#vwc_Tiempo').html('<span class="blink" style="color:red">N.P.</span>');
}

function vwc_showData(data) {
	var perro=$('#vwc_Perro').html();
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
				$('#vwc_Logo').attr("src","/agility/images/logos/"+data['LogoClub']);
				$('#vwc_Dorsal').html("Dorsal: "+dorsal );
				$('#vwc_Nombre').html(data["Nombre"]);
				$('#vwc_NombreGuia').html("Guia: "+data["NombreGuia"]);
				$('#vwc_NombreClub').html("Club: "+data["NombreClub"]);
				$('#vwc_Categoria').html(data["NombreCategoria"].replace(/.* - /g,""));
				$('#vwc_Grado').html(data["NombreGrado"]);
				$('#vwc_Celo').html((celo==1)?'<span class="blink">Celo</span>':'');
			},
			error: function(XMLHttpRequest,textStatus,errorThrown) {
				alert("error: "+textStatus + " "+ errorThrown );
			}
		});
	}
	// actualiza resultados del participante
	$('#vwc_Faltas').html(data["Faltas"]);
	$('#vwc_Tocados').html(data["Tocados"]);
	$('#vwc_Rehuses').html(data["Rehuses"]);
	$('#vwc_Tiempo').html(data["Tiempo"]);
	if (data["Eliminado"]==1)	$('#vwc_Tiempo').html('<span class="blink" style="color:red">Elim.</span>');
	if (data["NoPresentado"]==1) $('#vwc_Tiempo').html('<span class="blink" style="color:red">N.P.</span>');
	
}

/**
 * activa una secuencia de conteo hacia atras de 15 segundos
 */
function vwc_counter(){
	var myCounter = new Countdown({  
	    seconds:15,  // number of seconds to count down
	    onUpdateStatus: function(sec){ $('#vwc_Tiempo').html(sec); }, // callback for each second
	    onCounterEnd: function(){  $('#vwc_Tiempo').html('<span class="blink" style="color:red">-out-</span>'); } // final action
	});
	myCounter.start();
}

/**
 * Maneja el cronometro manual
 * @param oper 'start','stop','pause','resume','reset'
 */
function vwc_cronoManual(oper) {
	$('#cronomanual').Chrono(oper);
}

function vwc_cronoAutomatico() {
	return; // nothing to do here with automatic chrono events
}

function vwc_evalResult(event) {
	vwc_showData(event);
	vwc_updateResults();
}

function vwc_processLiveStream(id,evt) {
	var event=eval('('+evt+')'); // remember that event was coded in DB as an string
	event['ID']=id;
	switch (event['Type']) {
	case 'null':		// null event: no action taken
		return; 
	case 'init':		// operator starts tablet application
		vwc_showOSD(0); 	// activa visualizacion de OSD
		return;
	case 'open':		// operator select tanda
		vwc_updateResults();
		vwc_updatePendingQueue();
		return;
	case 'datos':		// actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		vwc_updateData(event);
		return
	case 'llamada':		// operador abre panel de entrada de datos
		vwc_cronoManual('stop');
		vwc_cronoManual('reset');
		vwc_showOSD(1); 	// activa visualizacion de OSD
		vwc_showData(event);
		return
	case 'salida':		// juez da orden de salida ( crono 15 segundos )
		vwc_cronoManual('stop');
		vwc_cronoManual('reset');
		vwc_counter();
		return;
	case 'start':	// value: timestamp
		vwc_cronoManual('start');
		return;
	case 'stop':	// value: timestamp
		vwc_cronoManual('stop');
		return;
	case 'cronoauto':  	// value: timestamp
		vwc_cronoAutomatico();
		return;
	case 'aceptar':		// operador pulsa aceptar
		vwc_cronoManual('stop');  // nos aseguramos de que los cronos esten parados
		vwc_evalResult(event); // presenta clasificacion provisional del perro
		return;
	case 'cancelar':	// operador pulsa cancelar
		vwc_cronoManual('stop');
		vwc_cronoManual('reset');
		vwc_showOSD(0);
		return;
	}
}