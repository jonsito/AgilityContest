

function tandasStyler(val,row,idx) {
	var str="text-align:left; ";
	str += "font-weight:bold; ";
	str += ((idx&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
	return str;
}

/******************* funciones de manejo de las ventana de orden de tandas y orden de salida en el tablet *******************/

function tablet_showOrdenSalida() {
	$('#tablet-panel').panel('open');
    $('#tdialog-panel').panel('close');
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
    // call 'submit' method of form plugin to submit the form
	// NOTE: do not update parent tablet row! 
	// as form('reset') seems not to work as we want, we use it as backup
    $('#tdialog-form').form('submit', 
    	{
    		url:'/agility/server/database/resultadosFunctions.php',
    		onSubmit: function() { return true; },
    		// success: function (data) { }
    	});
}

function tablet_add(val) {
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
	var str=$('#tdialog-Tiempo').val();
	if (str.indexOf('.')>=0) return;
	tablet_add('.');
	tablet_updateResultados(1);
	// dont send time event
}

function tablet_del() {
	var str=$('#tdialog-Tiempo').val();
	if (str==='') return;
	$('#tdialog-Tiempo').val(str.substring(0, str.length-1));
	tablet_updateResultados(1);
	// dont send time event
}

function tablet_up(id){
	var n= 1+parseInt($(id).val());
	var lbl = replaceAll('#tdialog-','',id);
	var datos = {};
	$(id).val(''+n);
	tablet_updateResultados(1);
	datos[lbl]=$(id).val();
	tablet_putEvent( 'datos', datos);
}

function tablet_down(id){
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
	var n= parseInt($('#tdialog-NoPresentado').val());
	if (n==0) {
		$('#tdialog-NoPresentado').val(1);
		// si no presentado borra todos los demas datos
		$('#tdialog-Eliminado').val(0);
		$('#tdialog-Faltas').val(0);
		$('#tdialog-Rehuses').val(0);
		$('#tdialog-Tocados').val(0);
		$('#tdialog-Tiempo').val(0);
	} else {
		$('#tdialog-NoPresentado').val(0);
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
	var n= parseInt($('#tdialog-Eliminado').val());
	if (n==0) {
		$('#tdialog-Eliminado').val(1);
		// si eliminado, poner nopresentado y tiempo a cero, conservar todo lo demas
		$('#tdialog-NoPresentado').val(0);
		$('#tdialog-Tiempo').val(0);
	} else {
		$('#tdialog-Eliminado').val(0);
		
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

function tablet_startstop() {
	var time = new Date().getTime();
	if ( $('#tdialog-StartStopBtn').val() === "Start" ) {
		tablet_putEvent('start',{ 'Value' : time } );
		$('#tdialog-StartStopBtn').val("Stop");
	} else {
		tablet_putEvent('stop',{ 'Value' : time } );
		$('#tdialog-StartStopBtn').val("Start");
	}
}

function tablet_salida() {
	tablet_putEvent('salida',{ 'Value' : new Date().getTime() } );
}

function tablet_cancel() {
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
	$('#tdialog-panel').panel('close');
}

function tablet_accept() {
	// save results 
	tablet_updateResultados(0); // mark as result no longer pendiente
	
	// close entradadatos window
	// this must be done BEFORE datagrid contents update
	// otherwise renderer will silently ignore actions
	$('#tdialog-panel').panel('close'); // and close window
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
}

