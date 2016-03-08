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
	tablet_config.DataEntryEnabled=flag;
	$('#tdialog-fieldset').prop('disabled',!flag);
	if (flag) $('#tablet-layout').layout('collapse','west');
	else $('#tablet-layout').layout('expand','west');
}

function setStartStopMode(mode) {
	var ssb=$('#tdialog-StartStopBtn');
	tablet_config.StartStopMode=mode;
	if (mode<0) ssb.val("Auto"); // mark running in auto mode
	if (mode==0) ssb.val("Start"); // mark stopped (ready)
	if (mode>0) ssb.val("Stop"); // mark running in manual mode
}

function getStartStopMode() {
	return tablet_config.StartStopMode;
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
	if ($('#tdialog-Club').html()!==eq) return false;
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
	// setup infoheader on tablet
	$('#tdialog-InfoLbl').html(workingData.datosPrueba.Nombre + ' - ' + workingData.datosJornada.Nombre + ' - ' + row.Nombre);
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
	var maxlen=(ac_config.crono_miliseconds=="0")?6:7;
	var declen=(ac_config.crono_miliseconds=="0")?2:3;
	var tdt=$('#tdialog-Tiempo');
	var str=tdt.val();
	if (parseInt(str)==0) str=''; // clear espurious zeroes
	if(str.length>=maxlen) return; // sss.xx 6/7 chars according configuration
	var n=str.indexOf('.');
	if (n>=0) {
		var len=str.substring(n).length;
		if (len>declen) return; // only allowed decimal digits from config
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

function tablet_up(id,sendEvent){
	doBeep();
	var n= 1+parseInt($(id).val());
	var lbl = replaceAll('#tdialog-','',id);
	var datos = {};
	$(id).val(''+n);
	tablet_updateResultados(1);
	datos[lbl]=$(id).val();
	if (sendEvent){
		tablet_putEvent( 'datos', datos);
	}
	return false;
}

function tablet_down(id,sendEvent){
	doBeep();
	var n= parseInt($(id).val());
	var m = (n<=0) ? 0 : n-1;
	var lbl = replaceAll('#tdialog-','',id);
	var datos = {};
	$(id).val(''+m);
	tablet_updateResultados(1);
	datos[lbl]=$(id).val();
	if (sendEvent){
		tablet_putEvent( 'datos', datos );
	}
	return false;
}

function tablet_np(sendEvent) {
	doBeep();
	var tde=$('#tdialog-Eliminado');
	var tdestr=$('#tdialog-EliminadoStr');
	var tdnp=$('#tdialog-NoPresentado');
	var tdnpstr=$('#tdialog-NoPresentadoStr');
	var tdtime=$('#tdialog-Tiempo');
	var tdtint=$('#tdialog-TIntermedio');
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
		tdtint.val(0);
	} else {
		tdnp.val(0);
		tdnpstr.val("");
	}
	tablet_updateResultados(1);
	if (sendEvent){
		tablet_putEvent(
			'datos',
			{
				'NoPresentado'	:	tdnp.val(),
				'Faltas'		:	tdflt.val(),
				'Tocados'		:	tdtoc.val(),
				'Rehuses'		:	tdreh.val(),
				'Tiempo'		:	tdtime.val(),
				'TIntermedio'	:	tdtint.val(),
				'Eliminado'		:	tde.val()
			}
		);
	}
	return false;
}

function tablet_elim(sendEvent) {
	doBeep();
	var tde=$('#tdialog-Eliminado');
	var tdestr=$('#tdialog-EliminadoStr');
	var tdnp=$('#tdialog-NoPresentado');
	var tdtime=$('#tdialog-Tiempo');
	var tdtint=$('#tdialog-Tintermedio');
	var n= parseInt(tde.val());
	if (n==0) {
		tde.val(1);
		tdestr.val("EL");
		// si eliminado, poner nopresentado y tiempo a cero, conservar lo demas
		tdnp.val(0);
		$('#tdialog-NoPresentadoStr').val("");
		tdtime.val(0);
	} else {
		tde.val(0);
		tdestr.val("");
	}
	tablet_updateResultados(1);
	if (sendEvent) {
		tablet_putEvent(
			'datos',
			{
				'NoPresentado'	:	tdnp.val(),
				'Tiempo'		:	tdtime.val(),
				'TIntermedio'	:	tdtint.val(),
				'Eliminado'		:	tde.val()
			}
		);
	}
	return false;
}

/**
 * Parse data from electronic chronometer
 * @param data
 */
function tablet_updateChronoData(data) {
	var f=parseInt(data['Faltas']);
	var r=parseInt(data['Rehuses']);
	var t=parseInt(data['Tocados']);
	var e=parseInt(data['Eliminado']);
	var n=parseInt(data['NoPresentado']);
	if (f>=0) $('#tdialog-Faltas').val(''+f);
	if (t>=0) $('#tdialog-Tocados').val(''+t);
	if (r>=0) $('#tdialog-Rehuses').val(''+r);
	// if (data["Tiempo"]!=-1) $('#chrono_Tiempo').html(data["Tiempo"]);
	if(e>=0) {
		var str=(data['Eliminado']==0)?"":"EL";
		$('#tdialog-Eliminado').val(e);
		$('#tdialog-EliminadoStr').val(str);
		$('#tdialog-NoPresentado').val(0);
		$('#tdialog-NoPresentadoStr').val("");
	}
	if (n>=0) {
		var str=(data['NoPresentado']==0)?"":"NP";
		$('#tdialog-NoPresentado').val(n);
		$('#tdialog-NoPresentadoStr').val(str);
		$('#tdialog-Eliminado').val(0);
		$('#tdialog-EliminadoStr').val("");
		$('#tdialog-Tiempo').val(0);
		$('#tdialog-Tintermedio').val(0);
	}
	// call server to update results
	tablet_updateResultados(1);
	// DO NOT RESEND EVENT!!!
}

function tablet_cronometro(oper,time) {
	if (ac_config.tablet_chrono==="1") $('#cronometro').Chrono(oper,time);
}

var myCounter = new Countdown({  
	seconds:15,  // number of seconds to count down
	onUpdateStatus: function(tsec){
		$('#tdialog-Tiempo').val(toFixedT((tsec/10),1));
	}, // callback for each tenth of second
	// onCounterEnd: function(){  $('#tdialog_Tiempo').html('<span class="blink" style="color:red">-out-</span>'); } // final action
	onCounterEnd: function(){  // at end of countdown start timer
		var time = Date.now() - startDate;
		switch (parseInt(ac_config.tablet_countdown)) {
			case 1: /* do nothing */ return;
			case 2: /* start crono */
				tablet_putEvent('start',{ 'Value' : time } );
				setStartStopMode(1);
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
		'Value' : Date.now() - startDate,
		'start' : 60 * parseInt(ac_config.crono_rectime)
	} );
	doBeep();
	return false;
}

function tablet_startstop() {
	var time = Date.now() - startDate;
	var ssb=getStartStopMode();
	if (ssb<0) return; // crono auto started: ignore
	if (ssb==0) tablet_putEvent('start',{ 'Value' : time } );
	if (ssb>0) tablet_putEvent('stop',{ 'Value' : time } );
	doBeep();
	return false;
}

function tablet_salida() { // 15 seconds countdown
	var time = Date.now() - startDate;
	var ssb=getStartStopMode();
	if (ssb<0) return; // crono auto started. ignore
	if (ssb>0) return; // crono manual started. ignore
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
	var dg=$(dgname);
	var row =dg.datagrid('getSelected');
	if (!row) { // should not ocurrs
		tablet_cronometro('stop');
		tablet_cronometro('reset');
		console.log("INTERNAL ERROR tablet_cancel(): no selected row");
		setDataEntryEnabled(false);
		return false;
	}
	var idx=dg.datagrid('getRowIndex',row);
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
					'TIntermedio'	:	row.TIntermedio,
					'Eliminado'		:	row.Eliminado
				}
			);
			dg.datagrid('scrollTo',{
				index : idx,
				callback: function(index) {
					tablet_cronometro('stop');
					tablet_cronometro('reset');
					setDataEntryEnabled(false);
					dg.datagrid('refreshRow',idx);
				}
			});
		}
	});
}

function fillPending(dg,idx) {
	var data=dg.datagrid('getRows');
	var rows=[];
	for (var n=(idx==0)?0:-1;n<6;n++) {
		if ( typeof(data[idx+n])==='undefined') continue;
		var row=data[idx+n];
		rows.push({'Num':idx+n+1,'Dorsal':row.Dorsal,'Nombre':row.Nombre,'Guia':row.NombreGuia});
	}
	$('#tdialog-tnext').datagrid('loadData',rows);
	$('#tdialog-tnext').datagrid('selectRow',(idx==0)?0:1);
	$('#tdialog-NumberLbl').html('<p>'+(idx+1)+'</p>');
}

function nextRow(dg,row,index, cb){
	var opts = dg.datagrid('options');
	index++;
	if (index>=(opts.numRows)) return false; // at the end
	dg.datagrid('scrollTo', {
		index: index, // to allow view up to 4 next rows
		callback: function(idx){
			dg.datagrid('selectRow', idx);
			cb(idx, dg.datagrid('getRows')[idx]);
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
	if (!row) { // !no row selected!!. should mark error
		console.log("INTERNAL ERROR tablet_accept(): no selected row");
		return false;
	}

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
	// check "accept" behaviour in config. If 'tablet_next' = false, just return to round selection
	if (ac_config.tablet_next==="0") { // no go to next row entry
		setDataEntryEnabled(false);
		dg.datagrid('refreshRow',rowindex);
		return false;
	}
	// seleccionamos fila siguiente
	var res=nextRow(dg,row,rowindex,function(index,data) {
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
		fillPending(dg,parseInt(data.RowIndex));
	});
	if (res==false) { // at end of list
		var time = Date.now() - startDate;
		setDataEntryEnabled(false);
		dg.datagrid('refreshRow',rowindex);
		dg.datagrid('unselectAll');
		tablet_putEvent('close',{ 'Value' : time } );
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
				$('#tablet-datagrid-search').val('---- <?php _e("Dorsal"); ?> ----');
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
				index: idx, // to make sure extra rows are loaded
				callback: function (index) {
					if (index < 0) return false; // no selection
					dg2.datagrid('selectRow', index);
					var data = dg2.datagrid('getRows')[index];
					data.Session = workingData.sesion;
					data.RowIndex = index; // not really used, but....
					data.Parent = dgname; // store datagrid reference
					$('#tdialog-form').form('load', data);
					fillPending(dg2,parseInt(data.RowIndex));
					setDataEntryEnabled(true);
				}
			});
		});
		drs.val('---- <?php _e("Dorsal"); ?> ----');
		return false;
	}
	// arriving here means that there are no expanded row
	$.messager.alert("No selection",'<?php _e("There is no selected round");?>',"error");
	drs.val('---- <?php _e("Dorsal"); ?> ----');
}
function bindKeysToTablet() {
	if (isMobileDevice()) return; // disable key handling on tablet/mobile phone
	if (parseInt(ac_config.tablet_keyboard)==0) return; // on keyboard disabled, ignore
	// parse keypress event on every  button
	$(document).keydown(function(e) {
		// on round selection window focused, ignore
		if ($('#tdialog-fieldset').prop('disabled')) return true;
		switch(e.which) {
			/* you can check keycodes at http://www.cambiaresearch.com/articles/15/javascript-char-codes-key-codes */
			// numbers (querty/keypad)
			case 48:    /* 0 */
			case 96:	/* numpad 0 */ tablet_add(0); break;
			case 49:    /* 1 */
			case 97:	/* numpad 0 */	tablet_add(1); break;
			case 50:    /* 2 */
			case 98:	/* numpad 0 */	tablet_add(2); break;
			case 51:    /* 3 */
			case 99:	/* numpad 0 */	tablet_add(3); break;
			case 52:    /* 4 */
			case 100:	/* numpad 0 */	tablet_add(4); break;
			case 53:    /* 5 */
			case 101:	/* numpad 0 */	tablet_add(5); break;
			case 54:    /* 6 */
			case 102:	/* numpad 0 */	tablet_add(6); break;
			case 55:    /* 7 */
			case 103:	/* numpad 0 */	tablet_add(7); break;
			case 56:    /* 8 */
			case 104:	/* numpad 0 */	tablet_add(8); break;
			case 57:    /* 9 */
			case 105:	/* numpad 0 */	tablet_add(9); break;
			case 8:		/* del */
			case 46:	/* numpad supr */	tablet_del(); break;
			case 190:    /* dot */
			case 110:	/* numpad dot */	tablet_dot(); break;
			// entrada de datos desde tablet
			case 70: // 'F' -> falta
			case 32: // ' ' -> space also works as fault
				if (e.ctrlKey) tablet_down('#tdialog-Faltas',true);
				else 	tablet_up('#tdialog-Faltas',true);
				break;
			case 82: // 'R' -> rehuse
			case 225: // 'AltGr' -> also works as refusal
				if (e.ctrlKey) tablet_down('#tdialog-Rehuses',true);
				else 	tablet_up('#tdialog-Rehuses',true);
				break;
			case 84: // 'T' -> tocado
			case 18: // 'Alt' -> also works as "touch"
				if (e.ctrlKey) tablet_down('#tdialog-Tocados',true);
				else 	tablet_up('#tdialog-Tocados',true);
				break;
			case 69:	tablet_elim(); break; // 'E' -> eliminado
			case 78:	tablet_np(); break; // 'N' -> no presentado
			// arranque parada del crono
			case 80:	tablet_resetchrono(); break; // 'P' -> chrono (P)reset
			case 83:	tablet_startstop();	break; // 'S' -> chrono start/Stop
			case 66:	tablet_salida();	break; // 'B' -> 15 seconds countdown
			// aceptar cancelar
			case 13:	tablet_accept(); break; // 'Enter' -> Accept
            // use click event to make sure focus is properly set
			case 27:	$('#tdialog-CancelBtn').click(); break; // 'ESC' -> Cancel
                // tablet_cancel(); break; // 'Esc' -> Cancel
			default:
				// alert("Unknow key code: "+ e.which);
				// pass to upper layer to caught and process
				return true;
		}
		return false;
	});
}

function tablet_processEvents(id,evt) {
	var tbox=$('#tdialog-Tiempo');
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
	case 'close': // no more dogs in tanda
		return;
	case 'datos': // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		return;
	case 'llamada':	// llamada a pista
		// todo: en 4 conjunta solo para crono si cambio de equipo
		if (need_resetChrono()) {
			tablet_cronometro('stop');
			tablet_cronometro('reset');
			setStartStopMode(0); // mark ready to start
		}
		return;
	case 'salida': // orden de salida
		myCounter.start();
		return;
	case 'start': // arranque manual del cronometro
		if (getStartStopMode()<0) return;		// si crono automatico, ignora
		setStartStopMode(1); // mark running in manual mode
		myCounter.stop();
		crm.Chrono('stop',time);
		crm.Chrono('reset');
		crm.Chrono('start',time);
		return;
	case 'stop': // parada manual del cronometro
		setStartStopMode(0); // mark stopped
		tablet_cronometro('stop',time);
		return;// Value contiene la marca de tiempo
	case 'crono_start': // arranque crono electronico
		myCounter.stop();
		setStartStopMode(-1); // mark automatic crono
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
		// para el crono
		crm.Chrono('pause',time);
		// guarda tiempo intermedio
		$('#tdialog-TIntermedio').val(crm.Chrono('getValue')/1000.0);
		tablet_updateResultados(1);
		// re-arranca crono en cinco segundos
		setTimeout(function(){crm.Chrono('resume');},5000);
		return;
    case 'crono_stop':	// parada crono electronico
		setStartStopMode(0); // mark chrono stopped
		crm.Chrono('stop',time);
		console.log("tiempo final: "+crm.Chrono('getValue'));
		return;
	case 'crono_reset': // puesta a cero incondicional
		myCounter.stop();
		tablet_cronometro('stop',time);
		tablet_cronometro('reset');
		tbox.removeClass('blink');
		setStartStopMode(0); // mark chrono stopped
		return;
	case 'crono_dat':	// datos desde el crono electronico
		tablet_updateChronoData(event);
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
	case 'camera': // video source for live stream has changed
		return;
	default:
		alert("Unknow Event type: "+event['Type']);
		return;
	}
}