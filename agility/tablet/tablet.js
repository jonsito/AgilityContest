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



function tandasStyler(val,row,idx) {
	var str="text-align:left; ";
	str += "font-weight:bold; ";
	str += ((idx&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
	return str;
}

/******************* funciones de manejo de las ventana de orden de tandas y orden de salida en el tablet *******************/

function tablet_showOrdenSalida() {
	$('#tablet-window').window('open');
    $('#tdialog-window').window('close');
}

/******************* funciones de manejo de la ventana de entrada de resultados del tablet *****************/

/**
 * send events
 * @param {string} type: Event Type
 * @param {object} data: Event data
 */
function tablet_putEvent(type,data){
	// setup default elements for this event
	obj= {
			'Operation':'putEvent',
			'Type': 	type,
			'Source':	'tablet_'+$('#tdialog-Session').val(),
			'Session':	$('#tdialog-Session').val(),
			'Prueba':	$('#tdialog-Prueba').val(),
			'Jornada':	$('#tdialog-Jornada').val(),
			'Manga':	$('#tdialog-Manga').val(),
			'Tanda':	$('#tdialog-ID').val(),
			'Perro':	$('#tdialog-Perro').val(),
			'Celo':		$('#tdialog-Celo').val()	
	};
	// send "update" event to every session listeners
	$.ajax({
		type:'GET',
		url:"/agility/server/database/eventFunctions.php",
		dataType:'json',
		data: $.extend({},obj,data)
	});
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
	$.ajax({
		type:	'GET',
		url:	"/agility/server/database/sessionFunctions.php",
		// dataType:'json',
		data:	data,
		success: function() {
			// send event
			data.Operation=	'putEvent';
			data.Session=	data.ID;
			tablet_putEvent('open',data);
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
	if (ac_config.tablet_beep) beep();
}

function tablet_add(val) {
	doBeep();
	var str=$('#tdialog-Tiempo').val();
	if (parseInt(str)==0) str=''; // clear espurious zeroes
	if(str.length>=6) return; // sss.xx 6 chars
	var n=str.indexOf('.');
	if (n>=0) {
		var len=str.substring(n).length;
		if (len>2) return; // only two decimal digits
	}
	$('#tdialog-Tiempo').val(''+str+val);
	tablet_updateResultados(1);
	// dont send time event
}

function tablet_dot() {
	doBeep();
	var str=$('#tdialog-Tiempo').val();
	if (str.indexOf('.')>=0) return;
	tablet_add('.');
	tablet_updateResultados(1);
	// dont send time event
}

function tablet_del() {
	doBeep();
	var str=$('#tdialog-Tiempo').val();
	if (str==='') return;
	$('#tdialog-Tiempo').val(str.substring(0, str.length-1));
	tablet_updateResultados(1);
	// dont send time event
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
}

function tablet_np() {
	doBeep();
	var n= parseInt($('#tdialog-NoPresentado').val());
	if (n==0) {
		$('#tdialog-NoPresentado').val(1);
		$('#tdialog-NoPresentadoStr').val("NP");
		// si no presentado borra todos los demas datos
		$('#tdialog-Eliminado').val(0);
		$('#tdialog-EliminadoStr').val("");
		$('#tdialog-Faltas').val(0);
		$('#tdialog-Rehuses').val(0);
		$('#tdialog-Tocados').val(0);
		$('#tdialog-Tiempo').val(0);
	} else {
		$('#tdialog-NoPresentado').val(0);
		$('#tdialog-NoPresentadoStr').val("");
	}
	tablet_updateResultados(1);
	tablet_putEvent(
		'datos',
		{
		'NoPresentado'	:	$('#tdialog-NoPresentado').val(),
		'Faltas'		:	$('#tdialog-Faltas').val(),
		'Tocados'		:	$('#tdialog-Tocados').val(),
		'Rehuses'		:	$('#tdialog-Rehuses').val(),
		'Tiempo'		:	$('#tdialog-Tiempo').val(),
		'Eliminado'		:	$('#tdialog-Eliminado').val()
		}
		);
}

function tablet_elim() {
	doBeep();
	var n= parseInt($('#tdialog-Eliminado').val());
	if (n==0) {
		$('#tdialog-Eliminado').val(1);
		$('#tdialog-EliminadoStr').val("EL");
		// si eliminado, poner nopresentado y tiempo a cero, conservar todo lo demas
		$('#tdialog-NoPresentado').val(0);
		$('#tdialog-NoPresentadoStr').val("");
		$('#tdialog-Tiempo').val(0);
	} else {
		$('#tdialog-Eliminado').val(0);
		$('#tdialog-EliminadoStr').val("");
	}
	tablet_updateResultados(1);
	tablet_putEvent(
			'datos',
			{
			'NoPresentado'	:	$('#tdialog-NoPresentado').val(),
			'Tiempo'		:	$('#tdialog-Tiempo').val(),
			'Eliminado'		:	$('#tdialog-Eliminado').val()
			}
		);
}

function tablet_chrono(oper,time) {
	if (ac_config.tablet_chrono) $('#cronomanual').Chrono(oper,time);
}

var myCounter = new Countdown({  
    seconds:15,  // number of seconds to count down
    onUpdateStatus: function(sec){ $('#tdialog-Tiempo').val(sec); }, // callback for each second
    // onCounterEnd: function(){  $('#tdialog_Tiempo').html('<span class="blink" style="color:red">-out-</span>'); } // final action
    onCounterEnd: function(){  // at end of countdown start timer
    	var time = new Date().getTime(); 
		tablet_putEvent('start',{ 'Value' : time } );
		$('#tdialog-StartStopBtn').val("Stop");
		tablet_chrono('start',time);
    }
});

function tablet_startstop() {
	var time = new Date().getTime(); 
	if ( $('#tdialog-StartStopBtn').val() === "Start" ) {
		tablet_putEvent('start',{ 'Value' : time } );
		$('#tdialog-StartStopBtn').val("Stop");
		myCounter.stop();
		tablet_chrono('start',time)
	} else {
		tablet_putEvent('stop',{ 'Value' : time } );
		$('#tdialog-StartStopBtn').val("Start");
		tablet_chrono('stop',time);
	}
	doBeep();
}

function tablet_salida() {
	tablet_putEvent('salida',{ 'Value' : new Date().getTime() } );
	tablet_chrono('stop',0);
	tablet_chrono('reset',0);
	myCounter.start();
	doBeep();
}

function tablet_cancel() {
	doBeep();
	// retrieve original data from parent datagrid
	var dgname=$('#tdialog-Parent').val();
	var row =$(dgname).datagrid('getSelected');
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
	}
	// and close panel
	tablet_chrono('stop',0);
	tablet_chrono('reset',0);
	$('#tdialog-window').window('close');
}

function tablet_accept() {
	doBeep();
	tablet_chrono('stop',0);
	// save results 
	tablet_updateResultados(0); // mark as result no longer pendiente
	
	// retrieve original data from parent datagrid
	var dgname = $('#tdialog-Parent').val();
	var row = $(dgname).datagrid('getSelected');
	if (!row) return; // nothing to do. should mark error
	
	// now update and redraw data on
	var rowindex= $(dgname).datagrid("getRowIndex", row);
	// send back data to parent tablet datagrid form
	var obj=formToObject('#tdialog-form');
	// mark as no longer pending
	obj.Pendiente=0;
	// update row
	$(dgname).datagrid('updateRow',{index: rowindex, row: obj});
	$(dgname).datagrid('refreshRow',rowindex);
	// and fire up accept event
	tablet_putEvent(
			'aceptar',
			{ 
				'NoPresentado'	:	row.NoPresentado,
				'Faltas'		:	row.Faltas,
				'Tocados'		:	row.Tocados,
				'Rehuses'		:	row.Rehuses,
				'Tiempo'		:	row.Tiempo,
				'Eliminado'		:	row.Eliminado
			} 
		);
	tablet_chrono('stop',0);
	if (!ac_config.tablet_next) { // no go to next row entry
		$('#tdialog-window').window('close'); // close window
		$(dgname).datagrid('refreshRow',rowindex);
		return;
	}
	var data=selectNextRow(dgname);
	if (!data) { // no more data rows
		$('#tdialog-window').window('close'); // close window
		$(dgname).datagrid('refreshRow',rowindex);
		return;
	}
	data.Session=workingData.sesion;
    data.Parent=dgname; // store datagrid reference
    $('#tdialog-form').form('load',data);
}

