/**
 * Load html contents from 'page' URL and set as contents on 'id' tag
 * @param id tag to replace DOM contents
 * @param page URL where to retrieve HTML data
 */
function loadContents(id,page) {
	$('#mymenu').panel('collapse');
	$(id).load(page);
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

/**
* Object to store working data primary keys
*/
var workingData = new Object({
	perro: 0, // IDPerro del perro en edicion
	guia: '', // nombre del guia en edicion
	club: '', // nombre del club activo
	juez: '', // nombre del juez activo
	prueba: 0, // ID de la prueba en curso
	jornada: 0, // ID de la jornada en curso
	manga: 0, // ID de la manga en curso
	manga2: 0, // ID de la segunda manga para el calculo de resultados
	datosPrueba: new Object(), // last selected prueba data
	datosJornada: new Object(), // last selected jornada data
	datosRonda: new Object(), // last selected ronda (grade, manga1, manga2)
});

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
	elem.css('padding','5px');
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
 * Common rowStyler function for AgilityContest datagrids
 * @paramm {integer} idx Row index
 * @param {Object} row Row data
 * @return {string} proper row style for given idx
 */
function myRowStyler(idx,row) {
	return ((idx&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
}


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
	}
});