/*
common.js

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

/**
 * Set text of 'header' field on main window
 * @param {string} msg text to be shown
 */
function setHeader(msg) { $('#Header_Operation').html('<p>'+msg+'</p>'); } 

/**
 * nombres de las categorias en funcion de la federacion
 */
var nombreCategorias = {
		'rsce': { 'L': 'Standard',	'M': 'Midi',	'S': 'Mini',	'T': '-',	'logo': 'rsce.png' },
		'rfec': { 'L': 'Large',		'M': 'Medium',	'S': 'Small',	'T': 'Tiny','logo': 'rfec.png' },
		'uca': 	{ 'L': '60',		'M': '50',		'S': '40',		'T': '30',	'logo': 'uca.png' },
		0: { 'L': 'Standard',	'M': 'Midi',	'S': 'Mini',	'T': '-',	'logo': 'rsce.png' },
		1: { 'L': 'Large',		'M': 'Medium',	'S': 'Small',	'T': 'Tiny','logo': 'rfec.png' },
		2: { 'L': '60',			'M': '50',		'S': '40',		'T': '30',	'logo': 'uca.png' }
};

function toLongCategoria(sort) {
	switch (sort) {
	case 'L': return 'Large';
	case 'M': return 'Medium';
	case 'S': return 'Small';
	case 'T': return 'Tiny';
	}
	return sort;
}

function isTeam(tipomanga) {
    switch(parseInt(tipomanga)) {
		case 8: case 9: case 13: case 14: return true;
		default: return false;
    }
}

var slaveDialogs = new Object();

/**
 * Load html contents from 'page' URL and set as contents on '#contenido' tag
 * @param page URL where to retrieve HTML data
 * @param title new page title
 * @param slaves list of dialogs to .destroy() on next loadContents
 */
function loadContents(page,title,slaves) {
	$('#mymenu').panel('collapse');
	$.each(slaveDialogs,function(key,val) {
		$(val).dialog('panel').panel('clear'); 
	} ); 
	slaveDialogs=(typeof(slaves)==='undefined')?{}:slaves;
	$('#contenido').panel('clear');
	$('#contenido').panel('refresh',page);
	setHeader(title);
}

/**
 * Poor's man javascript implementation of php's replaceAll()
 */
function replaceAll(find,replace,from) {
	return from.replace(new RegExp(find, 'g'), replace);
}

/**
 * Poor's man javascript implementation of php's strpos()
 * @param {string} pajar
 * @param {string} aguja
 * @param {integer} offset
 * @returns
 */
function strpos (pajar, aguja, offset) {
	var i = (pajar + '').indexOf(aguja, (offset || 0));
	return i === -1 ? false : i;
}

/*
 * A downcounter in seconds
 * from: http://stackoverflow.com/questions/1191865/code-for-a-simple-javascript-countdown-timer
 * usage:
 * var myCounter = new Countdown({  
 *   seconds:5,  // number of seconds to count down
 *   onUpdateStatus: function(sec){console.log(sec);}, // callback for each second
 *   onCounterEnd: function(){ alert('counter ended!');} // final action
 * });
 * myCounter.start();
 */
function Countdown(options) {
	var timer=null;
	var instance = this;
	var seconds = options.seconds || 10;
	var updateStatus = options.onUpdateStatus || function () {};
	var counterEnd = options.onCounterEnd || function () {};

	function decrementCounter() {
		updateStatus(seconds);
		if (seconds === 0) {
			counterEnd();
			instance.stop();
		}
		seconds--;
	}

	this.start = function () {
		clearInterval(timer);
		timer = 0;
		seconds = options.seconds;
		timer = setInterval(decrementCounter, 1000);
	};

	this.stop = function () {
		clearInterval(timer);
	};
}

function beep() {
    var snd = new Audio("data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=");  
    snd.play();
}

/**
 * indica si una variable, funcion u objeto está definido
 * @param {string} variable objeto buscar
 * @returns {Boolean} true si existe el objeto 'variable'
 */
function isDefined(variable) { return (typeof(window[variable]) != "undefined");}

/**
 * Convierte los campos de un formulario en un array
 * @param {#string} formId ID del formulario
 * @returns {obj} objeto que contiene los datos
 */
function formToObject(formId) {
    var formObj = {};
    var inputs = $(formId).serializeArray();
    $.each(inputs, function (i, input) {
        formObj[input.name] = input.value;
    });
    return formObj;
}

/**
 * Clone (deep-clone) an object
 * Posibly not the most efficient way to do, but enought for me
 * @param obj Object to be cloned
 */
function cloneObj(obj) {
	return JSON.parse(JSON.stringify(obj));
	// return jQuery.extend(true, {}, obj);
}

/*
 * Check for execution in mobile devices
 */
function isMobileDevice() { 
	 if( navigator.userAgent.match(/Android/i)
	 || navigator.userAgent.match(/webOS/i)
	 || navigator.userAgent.match(/iPhone/i)
	 || navigator.userAgent.match(/iPad/i)
	 || navigator.userAgent.match(/iPod/i)
	 || navigator.userAgent.match(/BlackBerry/i)
	 || navigator.userAgent.match(/Windows Phone/i)
	 ){
	    return true;
	  }
	 else {
	    return false;
	 }
}

/**
 * Set prueba from selection dialogs
 * On change also reset jornada info
 * @param {object} data prueba data
 */
function setPrueba(data) {
	var old=workingData.prueba;
	workingData.prueba=Number(data.ID);
	workingData.nombrePrueba=data.Nombre;
	workingData.datosPrueba=data;
	if(workingData.prueba!=old) {
		workingData.jornada=0;
		workingData.nombreJornada="";
		workingData.datosJornada=new Object();
	}
}

/**
 * Set jornada from selection dialogs
 * On change also reset manga info
 * @param {object} data jornada info
 */
function setJornada(data) {
	var old=workingData.jornada;
	workingData.jornada=Number(data.ID);
	workingData.nombreJornada=data.Nombre;
	workingData.datosJornada=data;
	if(workingData.jornada!=old) {
		workingData.manga=0;
		workingData.nombreManga="";
		workingData.datosJornada=new Object();
	}
}

/**
 * @param {integer} id SessionID
 * Initialize working data information object
 */
function initWorkingData(id) {
	workingData.perro= 0; // IDPerro del perro en edicion
	workingData.guia= 0; // ID del guia en edicion
	workingData.club= 0; // ID del club activo
	workingData.juez= 0; // ID del juez activo
	workingData.prueba= 0; // ID de la prueba en curso
	workingData.nombrePrueba= ""; // nombre de la prueba en curso
	workingData.jornada= 0; // ID de la jornada en curso
	workingData.nombreJornada= ""; // nombre de la jornada
	workingData.manga= 0; // ID de la manga en curso
	workingData.nombreManga = ""; // denominacion de la manga
	workingData.manga2= 0; // ID de la segunda manga para el calculo de resultados
	workingData.tanda=0; // tanda (pareja manga/categoria) activa
	workingData.nombreTanda = ""; 
	workingData.sesion=0; // ID de la sesion para videomarcadores
	workingData.nombreSesion=""; // nombre de la sesion
	workingData.datosPrueba= new Object(); // last selected prueba data
	workingData.datosJornada= new Object(); // last selected jornada data
	workingData.datosManga= new Object(); // last selected jornada data
	workingData.datosRonda= new Object(); // last selected ronda (grade, manga1, manga2)
	if (id!==undefined) {
		$.ajax({
			url: '/agility/server/database/sessionFunctions.php',
			data: { Operation: 'getByID', ID: id },
			dataType: 'json',
	        async: false,
	        cache: false,
	        timeout: 30000,
			success: function(data) {
				workingData.perro	= data.Perro;
				workingData.prueba	= data.Prueba;
				workingData.nombrePrueba= ""; // nombre de la prueba
				workingData.jornada	= data.Jornada;
				workingData.nombreJornada= ""; // nombre de la jornada
				workingData.manga	= data.Manga;
				workingData.nombreManga = ""; // denominacion de la manga
				workingData.manga2= 0; // ID de la segunda manga para el calculo de resultados
				workingData.tanda	= data.Tanda;
				workingData.nombreTanda = ""; 
				workingData.sesion	= data.ID;
				workingData.nombreSesion	= data.Nombre;
			},
			error: function(msg){ alert("error setting workingData: "+msg);}
		});
	}
}

function initAuthInfo(id) {
	authInfo.ID=0;
	authInfo.Login="";
	authInfo.Gecos="";
	authInfo.SessionKey=null;
	authInfo.Perms=5;
	authInfo.SessionID=0;
	if (id!==undefined) {
		authInfo.ID=id.UserID;
		authInfo.Login=id.Login;
		authInfo.Gecos=id.Gecos;
		authInfo.SessionKey=id.SessionKey;
		authInfo.Perms=id.Perms;
		authInfo.SessionID=id.SessionID;
	}
}

/**
 * Actualiza la sesion con el id dado en la tabla de sesiones de la bbdd
 * @param id id de la sesion
 * @param param parametros a actualizar en la sesion
 */
function updateSessionInfo(id, parameters) {
	parameters.Operation='update',
	parameters.ID=id;
	$.ajax({
		url: '/agility/server/database/sessionFunctions.php',
		data: parameters,
		dataType: 'json',
	    async: false,
	    cache: false,
	    timeout: 30000,
		success: function(data) { initWorkingData(id);	},
		error: function(msg){ alert("error setting sessionDatabase: "+msg);}
		});
	return true;
}

/**
* Declare and initialize Object to store working data primary keys
*/
var workingData = new Object();
initWorkingData();
var authInfo =new Object();
initAuthInfo();

/**
 * Used to evaluate position, width and heigh on an element to be 
 * layed out in a grid
 * @param dg datagrid { cols, rows }
 * @param id id of element to be layed out
 * @param x start col
 * @param y start row
 * @param w nuber of cols
 * @param h number of rows
 */
function doLayout(dg,id,x,y,w,h) {
	var elem=$(id);
	elem.css('display','inline-block');
	elem.css('position','absolute');
	elem.css('float','left');
	// elem.css('padding','5px');
	elem.css('-webkit-box-sizing','border-box');
	elem.css('-moz-box-sizing','border-box');
	elem.css('box-sizing','border-box');
	elem.css('left',  ((25+x*100)/dg.cols)+'%');
	elem.css('top',   ((100+y*100)/dg.rows)+'%');
	elem.css('width', ((w*100)/dg.cols)+'%');
	elem.css('height',((h*100)/dg.rows)+'%');
}

/**
 * Add a tooltip provided element, with given text
 * @param {easyui-object} obj Element suitable to add a tooltip
 * @param {string} text Data text to be shown
 */
function addTooltip(obj,text) {
	obj.tooltip({
    	position: 'top',
		deltaX: 30, // shift tooltip 30px right from top/center
    	content: '<span style="color:#000">'+text+'</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
    	}
	});
}

/**
 * Add support for img.naturalWidth and img.naturalHeight in browsers that
 * lack of this (IE<9)
 */

(function($) {
	function img(url) {	var i=new Image; i.src=url;	return i; }
 
	if ('naturalWidth' in (new Image)) {
		$.fn.naturalWidth = function() { return this[0].naturalWidth; };
		$.fn.naturalHeight = function() { return this[0].naturalHeight; };
	} else {
		$.fn.naturalWidth = function() { return img(this.src).width; };
		$.fn.naturalHeight = function() { return img(this.src).height; };
	}
})(jQuery); 

/**
 * Extension of datagrid methods to add "align" property on array declared toolbars
 * http://www.jeasyui.com/forum/index.php?topic=3540.msg8090#msg8090
 * Básicamente lo que hace es redefinir el toolbar (remove()+prepend(),
 * y ajustar el style "float" de todos los elementos declarados, +appentTo()
 */
$.extend($.fn.datagrid.methods, {
	buildToolbar: function(jq, items){
		return jq.each(function(){
			var p = $(this).datagrid('getPanel');
			p.children('div.datagrid-toolbar').remove();
			var tb = $('<div class="datagrid-toolbar"></div>').prependTo(p);
			$.map(items, function(item){
	        var t = $('<a href="javascript:void(0)"></a>').appendTo(tb);
	        t.linkbutton($.extend({}, item, {
	        	onClick:function(){
	        		if (item.handler) { item.handler.call(this); }
	        		if (item.onClick){ item.onClick.call(this); }
	        	}
	        }));
	        t.css('float', item.align || '');
			});
		});
	},
    toExcel: function(jq, filename){
        return jq.each(function(){
            var uri = 'data:application/vnd.ms-excel;base64,';
            var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
            var base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))); };
            var format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }); };

            var alink = $('<a style="display:none"></a>').appendTo('body');
            var view = $(this).datagrid('getPanel').find('div.datagrid-view');
            var table = view.find('div.datagrid-view2 table.datagrid-btable').clone();
            var tbody = table.find('>tbody');
            view.find('div.datagrid-view1 table.datagrid-btable>tbody>tr').each(function(index){
                $(this).clone().children().prependTo(tbody.children('tr:eq('+index+')'));
            });
            var ctx = { worksheet: name || 'Worksheet', table: table.html()||'' };
            alink[0].href = uri + base64(format(template, ctx));
            alink[0].download = filename;
            alink[0].click();
            alink.remove();
        });
    }
});

/**
 * Extension del messager para permitir una coleccion de radiobuttons en lugar de un prompt
 * Se anyade la opcion $messager.radio(title,text{val:text},callback)
 */
(function($){
	function createDialog(title,content, buttons){
		var win = $('<div class="messager-body"></div>').appendTo('body');
		win.append(content);
		if (buttons){
			var tb = $('<div class="messager-button"></div>').appendTo(win);
			for(var label in buttons){
				$('<a></a>').attr('href', 'javascript:void(0)').text(label)
							.css('margin-left', 10)
							.bind('click', eval(buttons[label]))
							.appendTo(tb).linkbutton();
			}
		}
		win.window({
			title: title,
			noheader: (title?false:true),
			width: 300,
			height: 'auto',
			modal: true,
			collapsible: false,
			minimizable: false,
			maximizable: false,
			resizable: false,
			onClose: function(){
				setTimeout(function(){
					win.window('destroy');
				}, 100);
			}
		});
		win.window('window').addClass('messager-window');
		win.children('div.messager-button').children('a:first').focus();
		return win;
	}
	$.messager.radio = function(title,text, msg, fn){
		var str="";
		$.each(msg,function(val,optstr){
			str +='<br /><input type="radio" name="messager-radio" value="'+val+'">&nbsp;'+optstr+'\n';
		});
		 var content = '<div class="messager-icon messager-question"></div>'
		                         + '<div>' + text + '</div>'
		                         + '<br/>'
		                         + str
		                         + '<div style="clear:both;"/>';
		 var buttons = {};
		 buttons[$.messager.defaults.ok] = function(){
		         win.window('close');
		         if (fn){
		     			var val=$('input:radio[name="messager-radio"]:checked').val();
		                 fn(val);
		                 return false;
		         }
		 };
		 buttons[$.messager.defaults.cancel] = function(){
		         win.window('close');
		         if (fn){
		                 fn();
		                 return false;
		         }
		 };
		var win = createDialog(title,content,buttons);
		win.children('input.messager-input').focus();
		return win;
	};
})(jQuery);

/**
 * Function : print_r()
 * Arguments: The data - array,hash(associative array),object
 *    The level - OPTIONAL
 * Returns  : The textual representation of the array.
 * This function was inspired by the print_r function of PHP.
 * This will accept some data as the argument and return a
 * text that will be a more readable version of the
 * array/hash/object that is given.
 * Docs: http://www.openjs.com/scripts/others/dump_function_php_print_r.php
 */
function print_r(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...<br />";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"<br />";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

/**
 * display selected cell including hidden fields
 * @param dg jquery datagrid object
 */ 
function displayRowData(dg) {
	var selected = dg.datagrid('getSelected');
	if (!selected) return;
	var index = dg.datagrid('getRowIndex', selected);
	var w=$.messager.alert(dg.attr('id')+" - Row Info",
			"<p>Contenido de la fila<br /></p><p>"+print_r(selected)+"</p>",
			"info",
			function() {
				dg.datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
				dg.datagrid('selectRow', index);
			}
		);    		
}

/**
 * activa teclas up/down para navegar por el panel , esc para cerrar y ctrl+shift+enter para ver fila
 * @param {string} datagrid '#datagrid-name' source datagrid
 * @param {function} onEnter function(source, row) to be called on enter press
 */
function addSimpleKeyHandler(datagrid,onEnter){
	
	$(datagrid).datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){

		// move cursor
	    function selectRow(t,up){
	    	var count = t.datagrid('getRows').length;    // row count
	    	var selected = t.datagrid('getSelected');
	    	if (selected){
	        	var index = t.datagrid('getRowIndex', selected);
	        	index = index + (up ? -1 : 1);
	        	if (index < 0) index = 0;
	        	if (index >= count) index = count - 1;
	        	t.datagrid('clearSelections');
	        	t.datagrid('selectRow', index);
	    	} else {
	        	t.datagrid('selectRow', (up ? count-1 : 0));
	    	}
		}
		//main code
		var t = $(datagrid);
	    switch(e.keyCode){
	    case 27:    /* Esc */   $(dialog).window('close'); return false;
	    case 38:	/* Up */	selectRow(t,true); return false;
	    case 40:    /* Down */	selectRow(t,false); return false;
	    case 13:	/* Enter */	if (e.ctrlKey) { displayRowData(t); return false; }
	    						if (typeof(onEnter)!=='undefined') onEnter(datagrid,$(datagrid).datagrid('getSelected'));
	    default:    // no break
	    			return false;
	    }
	});
    return false;
}

/**
 * Generic function for adding key handling to datagrids
 * 
 * Create key bindings for edit,new,delete, and search actions on datagrid
 * assume that search textbox has 'dgid'-search as id
 * Called functions have a pointer to base datagrid
 * @param {string} dgid id(ie xxxx-datagrid) 
 * @param {function(dgid,searchval} insertfn new/insert function
 * @param {function(dgid) } updatefn edit function
 * @param {function(dgid) } deletefn delete function
 * @returns true on success, else false
 */
function addKeyHandler(dgid,insertfn,updatefn,deletefn) {
    // activa teclas up/down para navegar por el panel
    $(dgid).datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
    	
    	// up & down
        function selectRow(t,up){
        	var count = t.datagrid('getRows').length;    // row count
        	var selected = t.datagrid('getSelected');
        	if (selected){
            	var index = t.datagrid('getRowIndex', selected);
            	index = index + (up ? -1 : 1);
            	if (index < 0) index = 0;
            	if (index >= count) index = count - 1;
            	t.datagrid('clearSelections');
            	t.datagrid('selectRow', index);
        	} else {
            	t.datagrid('selectRow', (up ? count-1 : 0));
        	}
    	}
    	
        // pgup & pg down
		function selectPage(t,offset) {
        	var count = t.datagrid('getRows').length;    // row count
        	var selected = t.datagrid('getSelected');
        	if (selected){
            	var index = t.datagrid('getRowIndex', selected);
            	switch(offset) {
            	case 1: index+=10; break;
            	case -1: index-=10; break;
            	case 2: index=count -1; break;
            	case -2: index=0; break;
            	}
            	if (index<0) index=0;
            	if (index>=count) index=count-1;
            	t.datagrid('clearSelections');
            	t.datagrid('selectRow', index);
        	} else {
            	t.datagrid('selectRow', 0);
        	}
		}
		
    	var t = $(dgid);
        switch(e.keyCode){
        case 38:	/* Up */	 selectRow(t,true); return false;
        case 40:    /* Down */	 selectRow(t,false); return false;
        case 13:	/* Enter */	 if (e.ctrlKey) displayRowData(t); else updatefn(dgid); return false;
        case 45:	/* Insert */ insertfn(dgid,$(dgid+'-search').val()); return false;
        case 46:	/* Supr */	 deletefn(dgid); return false;
        case 33:	/* Re Pag */ selectPage(t,-1); return false;
        case 34:	/* Av Pag */ selectPage(t,1); return false;
        case 35:	/* Fin */    selectPage(t,2); return false;
        case 36:	/* Inicio */ selectPage(t,-2); return false;
        case 9: 	/* Tab */
            // if (e.shiftkey) return false; // shift+Tab
            return false;
        case 16:	/* Shift */
        case 17:	/* Ctrl */
        case 18:	/* Alt */
        case 27:	/* Esc */
            return false;
        }
	}); 

    // - activar la tecla "Enter" en la casilla de busqueda
    $(dgid+'-search').keydown(function(event){
        if(event.keyCode != 13) return;
      	// reload data adding search criteria
        $(dgid).datagrid('load',{
            where: $(dgid+'-search').val()
        });
    });
}
