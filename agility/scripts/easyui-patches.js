/*
easyui-patches.js

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

// this is a patch from easyui 1.4.5 to add extra functions. It's needed for newer scrollview code
$.easyui={
    indexOfArray:function(a,o,id){
        for(var i=0,_1=a.length;i<_1;i++){
            if(id==undefined){
                if(a[i]==o){
                    return i;
                }
            }else{
                if(a[i][o]==id){
                    return i;
                }
            }
        }
        return -1;
    },
    removeArrayItem:function(a,o,id){
        if(typeof o=="string"){
            for(var i=0,_2=a.length;i<_2;i++){
                if(a[i][o]==id){
                    a.splice(i,1);
                    return;
                }
            }
        }else{
            var _3=this.indexOfArray(a,o);
            if(_3!=-1){
                a.splice(_3,1);
            }
        }
    },
    addArrayItem:function(a,o,r){
        var _4=this.indexOfArray(a,o,r?r[o]:undefined);
        if(_4==-1){
            a.push(r?r:o);
        }else{
            a[_4]=r?r:o;
        }
    },
    getArrayItem:function(a,o,id){
        var _5=this.indexOfArray(a,o,id);
        return _5==-1?null:a[_5];
    },
    forEach:function(_6,_7,_8){
        var _9=[];
        for(var i=0;i<_6.length;i++){
            _9.push(_6[i]);
        }
        while(_9.length){
            var _a=_9.shift();
            if(_8(_a)==false){
                return;
            }
            if(_7&&_a.children){
                for(var i=_a.children.length-1;i>=0;i--){
                    _9.unshift(_a.children[i]);
                }
            }
        }
    }
};

/**
 * Extension of datagrid methods to add "align" property on array declared toolbars
 * http://www.jeasyui.com/forum/index.php?topic=3540.msg8090#msg8090
 * Básicamente lo que hace es redefinir el toolbar (remove()+prepend(),
 * y ajustar el style "float" de todos los elementos declarados, +appentTo()
 */
$.extend($.fn.datagrid.methods, {
    disableDnd: function(jq,index){
        return jq.each(function(){
            var target = this;
            var opts = $(this).datagrid('options');
            if (index != undefined){
                var trs = opts.finder.getTr(this, index);
            } else {
                var trs = opts.finder.getTr(this, 0, 'allbody');
            }
            trs.draggable('disable');
        });
    },
	buildToolbar: function(jq, items){
		return jq.each(function(){
			var p = $(this).datagrid('getPanel');
			p.children('div.datagrid-toolbar').remove();
			var tb = $('<div class="datagrid-toolbar"></div>').prependTo(p);
			$.map(items, function(item){
	            var t = $('<a href="javascript:void(0)"></a>').appendTo(tb);
	            t.linkbutton(
	                $.extend({}, item, {
	        	        onClick:function(){
	        		        if (item.handler) { item.handler.call(this); }
	        		        if (item.onClick){ item.onClick.call(this); }
	        	        }
	                })
                );
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
    },
    setFieldTitle: function(jq,data) {
        return jq.each(function(){
            // set datagrid options
            var cols=$(this).datagrid('options').columns[0]; // no extra headers in my code
            for (var n=0; n<cols.length;n++) {
                if (cols[n].field!==data.field) continue; // not found: continue
                cols[n].title=data.title; // found: set new title
                break; // no need to continue iteration
            }
            // change rendered layout
            var panel = $(this).datagrid('getPanel');
            var fld = $('td[field='+data.field+']',panel);
            if(fld.length) $('span', fld).eq(0).html(data.title);
        });
    },
    moveField: function(jq,data){
        return jq.each(function(){
            var columns = $(this).datagrid('options').columns;
            var cc = columns[data.idxHead];
            var c = _remove(data.idxFrom);
            if (c){
                _insert(data.idxTo,c);
            }
            function _remove(field){
                for(var i=0; i<cc.length; i++){
                    if (cc[i].field == field){
                        var c = cc[i];
                        cc.splice(i,1);
                        return c;
                    }
                }
                return null;
            }
            function _insert(field,c){
                var newcc = [];
                for(var i=0; i<cc.length; i++){
                    if (cc[i].field == field){
                        newcc.push(c);
                    }
                    newcc.push(cc[i]);
                }
                columns[data.idxHead] = newcc;
            }
        });
    }
});

/**
 * Extension del messager para permitir una coleccion de radiobuttons en lugar de un prompt
 * Se anyade la opcion $.messager.radio(title,text{val:text},callback)
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
	$.messager.radio = function(title,text, msg, fn, on_click ){
		var str="";
		var oc="";
		if (typeof(on_click) === "function") oc='onclick="'+on_click.name+'(this);"';
		$.each(msg,function(val,optstr){
            // if options starts with "*" mark as selected
            if (optstr.startsWith("*")) {
                str +='<br /><input type="radio" name="messager-radio" '+oc+' checked="checked" value="'+val+'">&nbsp;'+optstr.slice(1)+'\n';
            } else {
                str +='<br /><input type="radio" name="messager-radio" '+oc+'value="'+val+'">&nbsp;'+optstr+'\n';
            }
		});
		 var content = '<div class="messager-icon messager-question"></div>'
		                         + '<div>' + text + '</div>'
		                         + '<br/>'
		                         + str
		                         + '<div style="clear:both;"></div>';
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
    $.messager.password =function(title,msg,fn){
        var content='<div class="messager-icon messager-warning"></div>'+
            '<div>'+msg+'</div><br /><div style="clear:both;"/>'+
            '<div><input class="messager-input" type="password" style="width:50%"/></div>';
        var buttons={};
        buttons[$.messager.defaults.ok]=function(){
            win.window('close');
            if (fn) { fn($(".messager-input",win).val()); }
            return false;
        };
        buttons[$.messager.defaults.cancel]=function(){
            win.window("close");
            return false;
        };
        var win=createDialog(title,content,buttons);
        // seems that onchange is also fired when focus lost, so comment it
        // when enter is pressed in input box, assume "Accept" button
        // $(".messager-input",win).change(
        //    function(){
        //        win.window('close');
        //        if (fn) { fn($(".messager-input",win).val()); }
        //        return false;
        //    }
        // );
        win.find("input.messager-input").focus();
        return win;
    }
})(jQuery);

/**
 * Create a timespinner editor for datagrids
 */
$.extend($.fn.datagrid.defaults.editors, {
    timespinner: {
        init: function(container, options){
            var input = $('<input>').appendTo(container);
            input.timespinner(options);
            return input
        },
        destroy: function(target){
            $(target).timespinner('destroy');
        },
        getValue: function(target){
            return $(target).timespinner('getValue');
        },
        setValue: function(target, value){
            $(target).timespinner('setValue', value);
        },
        resize: function(target, width){
            $(target).timespinner('resize', width);
        }
    }
});


/**
 * Create a validatebox for number ranges in format XX-YY
 */
$.extend($.fn.validatebox.defaults.rules, {
    'intrange': {
        validator: function(value,param) {
            var reg=new RegExp('^[0-9]+-[0-9]+$');
            return reg.test(value);
        },
        message: 'Enter range in format XXXX-XXXX'
    },
    'validComboValue': {
        validator: function(value,param) {
            var val=$(param[0]).combogrid('getValue');
            return(!isNaN(val));
        },
        message: 'Must select an option from combobox'
    },
    'activationkey': {
        validator: function(value,param) {
            var reg=new RegExp('^[0-9a-zA-Z]{4}-[0-9a-zA-Z]{4}-[0-9a-zA-Z]{4}-[0-9a-zA-Z]{4}-[0-9a-zA-Z]{4}$');
            return reg.test(value);
        },
        message: 'Format: XXXX-XXXX-XXXX-XXXX-XXXX'
    },
    'journeyName': {
        validator: function(value,param) {
            return (value!=='-- Sin asignar --');
        },
        message: 'Must provide a valid Journey name'
    },
    inComboGrid:{
        // ignore value, cause is just shown text
        // param[0] combogrid id
        validator:function(value,param){
            var c = $(param[0]);
            var key = c.combogrid('options').idField;
            var val = c.combogrid('getValue');
            var rows = c.combogrid('grid').datagrid('getRows');
            for(var i=0; i<rows.length; i++){
                if (val === rows[i][key])  return true;
            }
            return false;
        },
        message:'Must select a value from combo'
    }
});