    <!-- FORMULARIO DE REASIGNACION DE UN PERRO-->
    <div id="chperros-dialog" class="easyui-dialog" style="width:550px;height:400px;padding:10px 20px">
        <div id="chperros-title" class="ftitle">Reasignaci&oacute;n de un perro</div>
        <form id="chperros-header">
        	<div class="fitem">
                <label for="Search">Buscar: </label>
                <select id="chperros-Search" name="Search" class="easyui-combogrid" style="width:250px"></select>&nbsp;
                <a id="chperros-clearBtn" href="#" class="easyui-linkbutton">Limpiar</a>
        	</div>
        </form>
        <hr/>
        <form id="chperros-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="chperros-ID" name="ID" type="hidden" /> <!-- dog id -->
                <input id="chperros-Nombre" name="Nombre" class="easyui-validatebox" /> <!-- dog name -->
                <input id="chperros-Operation" name="Operation" type="hidden" value="update"/> <!-- inser/update/delete -->
                <input id="chperros-Guia" name="Guia" type="hidden" /> <!-- id of the guia -->
                <input id="chperros-newGuia" name="Guia" type="hidden" /> <!-- id original of the guia -->
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
    	buttons: '#chperros-dlg-buttons',
        iconCls: 'icon-dog'
    });
    $('#chperros-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
    	if (e.keyCode == 27){ $('#chperros-dialog').dialog('close');
    	}
    });
    $('#chperros-Search').combogrid({
		panelWidth: 350,
		panelHeight: 200,
		idField: 'ID',
		textField: 'Nombre',
		url: 'database/dogFunctions.php?Operation=enumerate',
		method: 'get',
		mode: 'remote',
		columns: [[
			{field:'ID',hidden:'true'},
			{field:'Nombre',title:'Perro',width:20,align:'right'},
			{field:'Categoria',title:'Cat.',width:10,align:'center'},
			{field:'Grado',title:'Grado',width:10,align:'center'},
			{field:'NombreGuia',title:'Guia',width:40,align:'right'},
			{field:'NombreClub',title:'Club',width:20,align:'right'},
		]],
		multiple: false,
		fitColumns: true,
		singleSelect: true,
		selectOnNavigation: false ,
		onSelect: function(index,row) {
			var idperro=row.ID;
			if (!idperro) return;
	        $('#chperros-form').form('load','database/dogFunctions.php?Operation=getbyidperro&ID='+idperro); // load form with json retrieved data
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
    	url:'database/dogFunctions.php?Operation=grados',
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
		url:'database/dogFunctions.php?Operation=categorias'
    });
    </script>