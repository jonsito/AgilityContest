/*
easyui-patches.js

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
 * This file contains specific enhancements to jquery-easyui library
 * used in AgilityContest project
 */

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
            // if options starts with "*" mark as selected
            if (optstr.startsWith("*")) {
                str +='<br /><input type="radio" name="messager-radio" checked="checked" value="'+val+'">&nbsp;'+optstr.slice(1)+'\n';
            } else {
                str +='<br /><input type="radio" name="messager-radio" value="'+val+'">&nbsp;'+optstr+'\n';
            }
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
		 };
		var win = createDialog(title,content,buttons);
		win.children('input.messager-input').focus();
		return win;
	};
})(jQuery);

/**
 * Extension del datagrid groupview para ordenar los grupos
 */
var gview = $.extend({}, groupview, {
    onBeforeRender: function(target, rows){
        groupview.onBeforeRender.call(this, target, rows);

        var state = $.data(target, 'datagrid');
        var opts = state.options;
        var groups = this.groups;
        groups.sort(function(a,b){
            var p1 = sumValue(a.rows, 'Penalizacion');
            var p2 = sumValue(b.rows, 'Penalizacion');
            var t1 = sumValue(a.rows, 'Tiempo');
            var t2 = sumValue(b.rows, 'Tiempo');
            // compare penalization
            if (p1!=p2) return (opts.sortOrder=='asc'?1:-1)*(p1>p2?1:-1);
            // on equal penalization compare time
            return (opts.sortOrder=='asc'?1:-1)*(t1>t2?1:-1);
        });
        var index = 0;
        var newRows = [];
        for(var i=0; i<groups.length; i++){
            var group = groups[i];
            group.startIndex = index;
            index += group.rows.length;
            newRows = newRows.concat(group.rows);
        }
        state.data.rows = newRows;

        function sumValue(rows,field) {
            var tmode=isJornadaEq3()?3:4;
            var v = 0;
            for (var n = 0; n < tmode; n++) {
                if (typeof(rows[n]) === 'undefined') { v += 200; }
                else { v+=parseFloat(rows[n][field]); }
            }
            return v;
        }
    }
});
