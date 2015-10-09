/*
tablet.js

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

<?php
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
$config =Config::getInstance();
?>

function tandasStyler(val,row,idx) {
	var str="text-align:left; ";
	str += "font-weight:bold; ";
	str += ((idx&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
	return str;
}

/******************* funciones de manejo del panel de orden de tandas y orden de salida en el tablet *******************/

/**
 * expande/contrae activa/desactiva entrada de datos en el tablet
 * @param {boolean} flag true if activate; false on deactivate
 */
function setDataEntryEnabled(flag) {
	$('#tdialog-fieldset').prop('disabled',!flag);
	if (flag) $('#tablet-layout').layout('collapse','west');
	else $('#tablet-layout').layout('expand','west');
}

/******************* funciones de manejo del panel de entrada de resultados del tablet *****************/

/**
 * send events
 * @param {string} type Event Type
 * @param {object} data Event data
 */
function tablet_putEvent(type,data){
	var tds=$('#tdialog-Session').val();
	// setup default elements for this event
	var obj= {
			'Operation':'putEvent',
			'Type': 	type,
			'TimeStamp': Date.now() - startDate,
			'Source':	'tablet_'+tds,
			'Session':	tds,
			'Prueba':	$('#tdialog-Prueba').val(),
			'Jornada':	$('#tdialog-Jornada').val(),
			'Manga':	$('#tdialog-Manga').val(),
			'Tanda':	$('#tdialog-ID').val(),
			'Perro':	$('#tdialog-Perro').val(),
			'Dorsal':	$('#tdialog-Dorsal').val(),
			'Equipo':	$('#tdialog-Equipo').val(),
			'Celo':		$('#tdialog-Celo').val(),
			'Value':	0 // may be overriden with 'data' contents
	};
	// send "update" event to every session listeners
	$.ajax({
		type:'GET',
		url:"/agility/server/database/eventFunctions.php",
		dataType:'json',
		data: $.extend({},obj,data)
	});
}

function need_resetChrono(data) {
	if (! isJornadaEq4()) return true;
	// en equipos4 resetea si cambio de equipo
	var eq=workingData.teamsByJornada[data["Equipo"]].Nombre;
	if ($('#tdialog-Club').html()!==$eq) return false;
	return true;
}

function tablet_updateSession(row) {
	// update sesion info in database
	var data = {
			Operation: 'update',
			ID: workingData.sesion,
			Prueba: row.Prueba,
			Jornada: row.Jornada,
			Manga: row.Manga,
			Tanda: row.ID
	};
	if (parseInt(row.Manga)==0) {
		var str= row.Nombre.toLowerCase();
		if (str.indexOf("econo")>0) return false;
		else tablet_reconocimiento();
	}
	$.ajax({
		type:	'GET',
		url:	"/agility/server/database/sessionFunctions.php",
		// dataType:'json',
		data:	data,
		success: function() {
			data.Session=	data.ID;
			data.Operation=	'putEvent';
			// send proper event
			if (parseInt(row.Manga)==0) { //user defined tanda
				data.Nombre= row.Nombre;
				tablet_putEvent('info',data);
			} else {
				tablet_putEvent('open',data);
			}
		}
	});
}

function tablet_updateResultados(pendiente) {
	$('#tdialog-Pendiente').val(pendiente);
	var frm = $('#tdialog-form');
	$.ajax({
		type: 'GET',
		url: '/agility/server/database/resultadosFunctions.php',
		data: frm.serialize(),
		dataType: 'json',
		success: function (result) {
			if (result.errorMsg){
				$.messager.show({ width:300, height:200, title: 'Error', msg: result.errorMsg });
			}
			// NOTE: do not update parent tablet row on success
			// as form('reset') seems not to work as we want, we use it as backup
		}
	});
}

function doBeep() {
	if (ac_config.tablet_beep==="1")	setTimeout(function() {beep();},0);
}

function tablet_add(val) {
	doBeep();
	var tdt=$('#tdialog-Tiempo');
	var str=tdt.val();
	if (parseInt(str)==0) str=''; // clear espurious zeroes
	if(str.length>=6) return; // sss.xx 6 chars
	var n=str.indexOf('.');
	if (n>=0) {
		var len=str.substring(n).length;
		if (len>2) return; // only two decimal digits
	}
	tdt.val(''+str+val);
	tablet_updateResultados(1);
	// dont send event
	return false;
}

function tablet_dot() {
	doBeep();
	var str=$('#tdialog-Tiempo').val();
	if (str.indexOf('.')>=0) return;
	tablet_add('.');
	tablet_updateResultados(1);
	// dont send  event
	return false;
}

function tablet_del() {
	doBeep();
	var tdt=$('#tdialog-Tiempo');
	var str=tdt.val();
	if (str==='') return;
	tdt.val(str.substring(0, str.length-1));
	tablet_updateResultados(1);
	// dont send event
	return false;
}

function tablet_up(id){
	doBeep();
	var n= 1+parseInt($(id).val());
	var lbl = replaceAll('#tdialog-','',id);
	var datos = {};
	$(id).val(''+n);
	tablet_updateResultados(1);
	datos[lbl]=$(id).val();
	tablet_putEvent( 'datos', datos);
	return false;
}

function tablet_down(id){
	doBeep();
	var n= parseInt($(id).val());
	var m = (n<=0) ? 0 : n-1;
	var lbl = replaceAll('#tdialog-','',id);
	var datos = {};
	$(id).val(''+m);
	tablet_updateResultados(1);
	datos[lbl]=$(id).val();
	tablet_putEvent( 'datos', datos );
	return false;
}

function tablet_np() {
	doBeep();
	var tde=$('#tdialog-Eliminado');
	var tdestr=$('#tdialog-EliminadoStr');
	var tdnp=$('#tdialog-NoPresentado');
	var tdnpstr=$('#tdialog-NoPresentadoStr');
	var tdtime=$('#tdialog-Tiempo');
	var tdflt=$('#tdialog-Faltas');
	var tdtoc=$('#tdialog-Rehuses');
	var tdreh=$('#tdialog-Tocados');
	var n= parseInt(tdnp.val());
	if (n==0) {
		tdnp.val(1);
		tdnpstr.val("NP");
		// si no presentado borra todos los demas datos
		tde.val(0);
		tdestr.val("");
		tdflt.val(0);
		tdreh.val(0);
		tdtoc.val(0);
		tdtime.val(0);
	} else {
		tdnp.val(0);
		tdnpstr.val("");
	}
	tablet_updateResultados(1);
	tablet_putEvent(
		'datos',
		{
		'NoPresentado'	:	tdnp.val(),
		'Faltas'		:	tdflt.val(),
		'Tocados'		:	tdtoc.val(),
		'Rehuses'		:	tdreh.val(),
		'Tiempo'		:	tdtime.val(),
		'Eliminado'		:	tde.val()
		}
		);
	return false;
}

function tablet_elim() {
	doBeep();
	var tde=$('#tdialog-Eliminado');
	var tdestr=$('#tdialog-EliminadoStr');
	var tdnp=$('#tdialog-NoPresentado');
	var tdtime=$('#tdialog-Tiempo');
	var n= parseInt(tde.val());
	if (n==0) {
		tde.val(1);
		tdestr.val("EL");
		// si eliminado, poner nopresentado y tiempo a cero, conservar todo lo demas
		tdnp.val(0);
		$('#tdialog-NoPresentadoStr').val("");
		tdtime.val(0);
	} else {
		tde.val(0);
		tdestr.val("");
	}
	tablet_updateResultados(1);
	tablet_putEvent(
			'datos',
			{
			'NoPresentado'	:	tdnp.val(),
			'Tiempo'		:	tdtime.val(),
			'Eliminado'		:	tde.val()
			}
		);
	return false;
}

function tablet_cronometro(oper,time) {
	if (ac_config.tablet_chrono==="1") $('#cronometro').Chrono(oper,time);
}

var myCounter = new Countdown({  
	seconds:15,  // number of seconds to count down
	onUpdateStatus: function(tsec){
		$('#tdialog-Tiempo').val((tsec/10).toFixed(1));
	}, // callback for each tenth of second
	// onCounterEnd: function(){  $('#tdialog_Tiempo').html('<span class="blink" style="color:red">-out-</span>'); } // final action
	onCounterEnd: function(){  // at end of countdown start timer
		var time = Date.now() - startDate;
		switch (parseInt(ac_config.tablet_countdown)) {
			case 1: /* do nothing */ return;
			case 2: /* start crono */
				tablet_putEvent('start',{ 'Value' : time } );
				$('#tdialog-StartStopBtn').val("Stop");
				break;
			case 3: /* eliminado */
				$('#tdialog-Eliminado').val(0); //make sure that tablet sees not eliminado
				tablet_elim(); // call eliminado handler
				return;
		}
	}
});

function tablet_reconocimiento() {
	tablet_putEvent('crono_rec',{
		'Session': workingData.sesion,
		'Value' : Date.now() - startDate
	} );
	doBeep();
	return false;
}

function tablet_startstop() {
	var time = Date.now() - startDate;
	var ssb=$('#tdialog-StartStopBtn').val();
	if ( ssb==='Auto' ) return;  // crono auto started. ignore
	if ( ssb === "Start" ) {
		tablet_putEvent('start',{ 'Value' : time } );
	} else {
		tablet_putEvent('stop',{ 'Value' : time } );
	}
	doBeep();
	return false;
}

function tablet_salida() { // 15 seconds countdown
	var time = Date.now() - startDate;
	var ssb=$('#tdialog-StartStopBtn').val();
	if ( ssb==='Auto' ) return; // crono auto started. ignore
	if ( ssb==='Stop' ) return; // crono manual started. ignore
	tablet_putEvent('salida',{ 'Value' : time } );
	doBeep();
	return false;
}

function tablet_resetchrono() {
	var time = Date.now() - startDate;
	tablet_putEvent('crono_reset',{ 'Value' : time } );
	doBeep();
	return false;
}

function tablet_cancel() {
	doBeep();
	// retrieve original data from parent datagrid
	var dgname=$('#tdialog-Parent').val();
	var dg=$(dgname).datagrid();
	var row =dg.datagrid('getSelected');
	if (row) {
		// update database according row data
		row.Operation='update';
		$.ajax({
			type:'GET',
			url:"/agility/server/database/resultadosFunctions.php",
			dataType:'json',
			data: row,
			success: function () {
				// and fire up cancel event
				tablet_putEvent(
						'cancelar',
						{ 
							'NoPresentado'	:	row.NoPresentado,
							'Faltas'		:	row.Faltas,
							'Tocados'		:	row.Tocados,
							'Rehuses'		:	row.Rehuses,
							'Tiempo'		:	row.Tiempo,
							'Eliminado'		:	row.Eliminado
						} 
					);
			}
		});
		var index=row =dg.datagrid('getRowIndex',row);
		dg.datagrid('scrollTo',index);
	}
	// and close panel
	tablet_cronometro('stop');
	tablet_cronometro('reset');
	setDataEntryEnabled(false);
	return false;
}

function nextRow(dg, cb){
	var opts = dg.datagrid('options');
	var row = dg.datagrid('getSelected');
	var index = dg.datagrid('getRowIndex', row);
	if (index>=(opts.numRows-1)) return false;
	dg.datagrid('scrollTo', {
		index: index+1,
		callback: function(index){
			$(this).datagrid('selectRow', index);
			cb(index, $(this).datagrid('getRows')[index]);
		}
	});
	return true;
}

function tablet_accept() {
	doBeep();
	// save results
	tablet_updateResultados(0); // mark as result no longer pendiente
	// retrieve original data from parent datagrid
	var dgname = $('#tdialog-Parent').val();
	var dg = $(dgname);
	var row = dg.datagrid('getSelected');
	if (!row) return false; // nothing to do. should mark error

	// send back data to parent tablet datagrid form
	var obj = formToObject('#tdialog-form');
	// mark as no longer pending
	obj.Pendiente = 0;
	// now update and redraw data on
	var rowindex= dg.datagrid("getRowIndex", row);

	// update row
	dg.datagrid('updateRow', {index: rowindex, row: obj});
	// and fire up accept event
	tablet_putEvent(
		'aceptar',
		{
			// notice pass-by-reference: row now points to new values
			'NoPresentado': row.NoPresentado,
			'Faltas': row.Faltas,
			'Tocados': row.Tocados,
			'Rehuses': row.Rehuses,
			'Tiempo': row.Tiempo,
			'Eliminado': row.Eliminado
		}
	);
	// en jornadas por equipos 4 el crono sigue contando entre perro y perro
	// por ello no reseteamos el crono en el cambio de equipo
	if ( ! isJornadaEq4()) {
		tablet_cronometro('stop');
		tablet_cronometro('reset');
	}
	if (ac_config.tablet_next==="0") { // no go to next row entry
		setDataEntryEnabled(false);
		dg.datagrid('refreshRow',rowindex);
		return false;
	}
	// seleccionamos fila siguiente
	var res=nextRow(dg,function(index,data) {
		// alert ("index:"+index+" data:"+JSON.stringify(data));
		if (index<0) return false; // no selection
		if (data==null) { // at end of rows. should not occurs
			dg.datagrid('scrollTo',rowindex);
			setDataEntryEnabled(false);
			return false;
		}
		data.Session=workingData.sesion;
		data.RowIndex=index; // not really used, but....
		data.Parent=dgname; // store datagrid reference
		$('#tdialog-form').form('load',data);
	});
	if (res==false) { // at end of list
		setDataEntryEnabled(false);
		dg.datagrid('refreshRow',rowindex);
	}
	return false; // prevent follow onClick event chain
}

/**
 * retrieve from server data row on provided dorsal
 * call to callback(idx,row) provided function
 * @param {object} tanda current selected tanda
 * @param {object} dg datagrid for current selected tanda
 * @param {int} dorsal Dog dorsal to search for
 * @param cb(page) what to do if Dorsal found in tanda
 */
function loadDorsalPage(tanda,dg,dorsal,cb) {
	$.ajax({
		type:	'GET',
		url:	"/agility/server/database/tandasFunctions.php",
		dataType:'json',
		data: {
			Operation: 'getDataByDorsal',
			Prueba:		tanda.Prueba,
			Jornada:	tanda.Jornada,
			Sesion:		tanda.Sesion,
			ID:			tanda.ID,
			Dorsal:		dorsal
		},
		success: function(row) {
			var idx=row.RowIndex;
			if (idx<0) {
				$.messager.alert("Not found",'<?php _e("Dog with dorsal");?>'+": "+dorsal+" "+'<?php _e("does not run in this series");?>',"info");
				$('#tablet-datagrid-search').val('---- Dorsal ----');
				return false;
			}
			cb(idx);
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			alert("error: "+textStatus + " "+ errorThrown );
		}
	});
}

function tablet_editByDorsal() {
	var i,len;
	var dg=$('#tablet-datagrid');
	var drs=$('#tablet-datagrid-search');
	var rows=dg.datagrid('getRows');
	var dorsal=parseInt(drs.val());
	drs.blur();// remove focus to hide tooltip
	// si no hay tandas activas muestra error e ignora
	for (i=0,len=rows.length;i<len;i++) {
		if (typeof(rows[i].expanded)==="undefined") continue;
		if (rows[i].expanded==0) continue;
		// obtenemos el datagrid y buscamos el dorsal
		var dgname='#tablet-datagrid-'+rows[i].ID;
		var dg2=$(dgname);
		loadDorsalPage(rows[i],dg2,dorsal,function(idx){
			dg2.datagrid('scrollTo', {
				index: idx,
				callback: function (index) {
					if (index < 0) return false; // no selection
					dg2.datagrid('selectRow', index);
					var data = dg2.datagrid('getRows')[index];
					data.Session = workingData.sesion;
					data.RowIndex = index; // not really used, but....
					data.Parent = dgname; // store datagrid reference
					$('#tdialog-form').form('load', data);
					setDataEntryEnabled(true);
				}
			});
		});
		drs.val('---- Dorsal ----');
		return false;
	}
	// arriving here means that there are no expanded row
	$.messager.alert("No selection",'<?php _e("There is no selected round");?>',"error");
	drs.val('---- Dorsal ----');
}

function tablet_processEvents(id,evt) {
	var tbox=$('#tdialog-Tiempo');
	var ssb=$('#tdialog-StartStopBtn');
	var crm=$('#cronometro');
	var event=parseEvent(evt); // remember that event was coded in DB as an string
	event['ID']=id; // fix real id on stored eventData
	var time=event['Value']; // miliseconds 
	switch (event['Type']) {
	case 'null': // null event: no action taken
		return;
	case 'init': // operator starts tablet application
		return;
	case 'open': // operator select tanda:
		return;
	case 'datos': // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		return;
	case 'llamada':	// llamada a pista
		// todo: en 4 conjunta solo para crono si cambio de equipo
		if (need_resetChrono()) {
			tablet_cronometro('stop');
			tablet_cronometro('reset');
			ssb.val('Start');
		}
		return;
	case 'salida': // orden de salida
		myCounter.start();
		return;
	case 'start': // arranque manual del cronometro
		if (ssb.val()==="Auto") return;		// si crono automatico, ignora
		ssb.val("Stop");
		myCounter.stop();
		crm.Chrono('stop',time);
		crm.Chrono('reset');
		crm.Chrono('start',time);
		return;
	case 'stop': // parada manual del cronometro
		ssb.val("Start");
		tablet_cronometro('stop',time);
		return;// Value contiene la marca de tiempo
	case 'crono_start': // arranque crono electronico
		myCounter.stop();
		ssb.val('Auto');
		// si esta parado, arranca en modo automatico
		if (!crm.Chrono('started')) {
			crm.Chrono('stop',time);
			crm.Chrono('reset');
			crm.Chrono('start',time);
			return
		}
		// si no resync, resetea el crono y vuelve a contar
		if (ac_config.crono_resync==="0") {
			crm.Chrono('reset');
			crm.Chrono('start',time);
		} else {
			// send restart event. Use event queue to avoid blocking event parsing
			setTimeout(
				function() {
					tablet_putEvent("crono_restart",{'stop':(Date.now()-startDate), 'start':time } );
				}
			,0);
		}
		return;
	case 'crono_restart': // paso de tiempo intermedio a manual
		crm.Chrono('resync',event['stop'],event['start']);
		return;
	case 'crono_int':	// tiempo intermedio crono electronico
		crm.Chrono('pause',time); setTimeout(function(){crm.Chrono('resume');},5000);
		return;
    case 'crono_stop':	// parada crono electronico
		ssb.val("Start");
		crm.Chrono('stop',time);
		return;
	case 'crono_reset': // puesta a cero incondicional
		myCounter.stop();
		tablet_cronometro('stop',time);
		tablet_cronometro('reset');
		tbox.removeClass('blink');
		ssb.val("Start");
		return;
	case 'crono_dat':	// datos desde el crono electronico
		// at this moment, every crono_dat events are ignored:
		// this is a sample implementation and this crono is not designed
		// to work without tablet; so no sense to take care
		// on 'crono_dat' events: just use 'datos' event from tablet instead
		return;
	case 'crono_rec':	// reconocimiento de pista desde crono electronico
		// ignored, just for get noticed at chrono display
		return;
	case 'crono_error': // sensor alignment failed
		if (event['Value']==1) tbox.addClass('blink');
		else tbox.removeClass('blink');
		return;
		// show error message. Use reset to clear
	case 'cancelar': // operador pulsa cancelar
		return;
	case 'aceptar':	// operador pulsa aceptar
		return;
	case 'info':	// click on user defined tandas
		return;
	default:
		alert("Unknow Event type: "+event['Type']);
		return;
	}
}