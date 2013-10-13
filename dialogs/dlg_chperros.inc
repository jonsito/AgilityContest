    <!-- FORMULARIO DE REASIGNACION DE UN PERRO-->
    <div id="chperros-dialog" class="easyui-dialog" style="width:500px;height:375px;padding:10px 20px">
        <div id="chperros-title" class="ftitle">Reasignaci&oacute;n de un perro</div>
        <form id="chperros-header">
        	<div class="fitem">
                <label for="Search">Buscar: </label>
                <select id="chperros-Search" name="Search" class="easyui-combogrid" style="width:250px"></select>&nbsp;
                <a id="chperros-clearBtn" href="#" class="easyui-linkbutton">Limpiar</a>
                <input id="chperros-Parent" name="Parent" type="hidden" /> <!-- used to store parent datagrid if any -->
                <input id="chperros-newGuia" name="newGuia" type="hidden" /> <!-- used to store guia -->
        	</div>
        </form>
        <hr/>
        <form id="chperros-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="chperros-Nombre" name="Nombre" class="easyui-validatebox" />
                <input id="chperros-Dorsal" name="Dorsal" type="hidden" /> <!-- hide dorsal, as only used for edit and is readonly -->
                <input id="chperros-Operation" name="Operation" type="hidden" value="insert"/> <!-- inser/update/delete -->
                <input id="chperros-Guia" name="Guia" type="hidden" /> <!-- name of the guia -->
            </div>
            <div class="fitem">
                <label for="Raza">Raza:</label>
                <input id="chperros-Raza" class="easyui-validatebox" name="Raza" type="text" />
            </div>
            <div class="fitem">
                <label for="LOE_RRC">Num. LOE / RRC:</label>
                <input id="chperros-LOE_RRC" class="easyui-validatebox" name="LOE_RRC" type="text" />
            </div>
            <div class="fitem">
                <label for="Licencia">Num. Licencia:</label>
                <input id="chperros-Licencia" class="easyui-validatebox" name="Licencia" type="text" />
            </div>
            <div class="fitem">
                <label for="Categoria">Categor&iacute;a:</label>
                <select id="chperros-Categorias_Perro" 
                		name="Categoria" 
                		class="easyui-combobox" 
                		style="width:155px" ></select>
            </div>
            <div class="fitem">
                <label for="Grado">Grado:</label>
                <select id="chperros-Grados_Perro" 
                		name="Grado" 
                		class="easyui-combobox" 
                		style="width:155px" ></select>
            </div>
        </form>
    	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    	<div id="chperros-dlg-buttons">
    	    <a id="chperros-okBtn" href="#" class="easyui-linkbutton" onclick="assignDog()">Reasignar</a>
    	    <a id="chperros-cancelBtn" href="#" class="easyui-linkbutton"  
    	    	onclick="javascript:$('#chperros-dialog').dialog('close')">Cancelar</a>
    	</div>
    </div>
    
    <script type="text/javascript">

    // datos del formulario de nuevo/edit perros
    // - declaracion del formulario
    $('#chperros-form').form();
    // - botones
    $('#chperros-clearBtn').linkbutton({ iconCls: 'icon-undo'}).tooltip({
    	position: 'top',
    	content: '<span style="color:#000">Limpiar formulario</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
    	}
	});
    $('#chperros-clearBtn').bind('click',function() {
        $('#chperros-form').form('clear'); 
        $('#chperros-Search').combogrid({ 'setValue' : '' });
		$('#chperros-Operation').val('insert'); // mark operation as 'insert'
    });
    $('#chperros-okBtn').linkbutton({ iconCls: 'icon-ok'}).tooltip({
    	position: 'top',
    	content: '<span style="color:#000">Aceptar datos y actualizar la BBDD</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
    	}
	});
    $('#chperros-cancelBtn').linkbutton({ iconCls: 'icon-cancel'}).tooltip({
    	position: 'top',
    	content: '<span style="color:#000">Cancelar edicion. Cerrar ventana</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
    	}
	});
    // campos del formulario
    $('#chperros-dialog').dialog({
    	closed: true,
    	buttons: '#chperros-dlg-buttons'
    });
    $('#chperros-Search').combogrid({
		panelWidth: 350,
		panelHeight: 200,
		idField: 'Nombre',
		textField: 'Nombre',
		url: 'database/enumerate_Perros.php',
		method: 'get',
		mode: 'remote',
		columns: [[
			{field:'Dorsal',hidden:'true'},
			{field:'Nombre',title:'Perro',width:20,align:'right'},
			{field:'Categoria',title:'Cat.',width:10,align:'center'},
			{field:'Grado',title:'Grado',width:10,align:'center'},
			{field:'Guia',title:'Guia',width:40,align:'right'},
			{field:'Club',title:'Club',width:20,align:'right'},
		]],
		multiple: false,
		fitColumns: true,
		singleSelect: true,
		selectOnNavigation: false ,
		onSelect: function(index,row) {
			var dorsal=row.Dorsal;
			if (!dorsal) return;
	        $('#chperros-form').form('load','database/get_dogByDorsal.php?Dorsal='+dorsal); // load form with json retrieved data
		}
	});
    $('#chperros-Nombre').validatebox({
        required: true,
        validType: 'length[1,255]'
    });
    $('#chperros-Grados_Perro').combobox({
		panelHeight: 'auto',
    	valueField:'Grado',
    	textField:'Comentarios',
    	method: 'get',
    	mode: 'remote',
		required: true,
    	url:'database/enumerate_Grados_Perro.php',
    	// TODO: this should work. study why doesn't
		onLoadSuccess: function(data){
			for(var i=0; i<data.length; i++){
				var row = data[i];
				// the row with 'selected' property set to true will be acted as the selected row
				if (row.selected){  
    				// alert('selected value is: '+row.Grado);
					$(this).combobox('setValue',row.Grado);
				}
			}
		}
    });
    $('#chperros-Categorias_Perro').combobox({
		panelHeight: 'auto',
		valueField:'Categoria',
		textField:'Observaciones',
		method: 'get',
		mode: 'remote',
		required: true,
		url:'database/enumerate_Categorias_Perro.php'
    });
    </script>