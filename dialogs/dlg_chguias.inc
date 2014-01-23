    <!-- FORMULARIO DE REASIGNACION DE GUIAS -->
    <div id="chguias-dialog" class="easyui-dialog" style="width:500px;height:350px;padding:10px 20px">
        <div id="chguias-title" class="ftitle">Reasignaci&oacute;n de un guia</div>
        <form id="chguias-header">
        	<div class="fitem">
                <label for="Search">Buscar: </label>
                <select id="chguias-Search" name="Search" class="easyui-combogrid" style="width:200px"></select>&nbsp;
                <a id="chguias-clearBtn" href="#" class="easyui-linkbutton">Limpiar</a>
                <input id="chguias-Parent" name="Parent" type="hidden" /> <!-- used to store parent datagrid if any -->
                <input id="chguias-newClub" name="newClub" type="hidden" /> <!-- used to store club -->
        	</div>
        </form>
        <hr/>
        <form id="chguias-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="chguias-Nombre" name="Nombre" class="easyui-validatebox" style="width:300px" />
                <input id="chguias-Club" name="Club" type="hidden" value="" /> <!-- hidden field to store related club -->
                <input id="chguias-Viejo" name="Viejo" type="hidden" value="" /> <!-- used to allow operator change guia's name -->
                <input id="chguias-Operation" name="Operation" type="hidden" value=""/> <!-- inser/update/delete -->
            </div>
            <div class="fitem">
                <label for="Email">Correo electr&oacute;nico:</label>
                <input id="chguias-Email" name="Email" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Telefono">Tel&eacute;fono:</label>
                <input id="chguias-Telefono" class="easyui-validatebox" name="Telefono" type="text" />
            </div>
            <div class="fitem">
                <label for="Observaciones">Observaciones:</label>
                <textarea id="chguias-Observaciones" name="Observaciones" style="height:50px;width:300px"></textarea>
            </div>
        </form>
    	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    	<div id="chguias-dlg-buttons">
    	    <a id="chguias-okBtn" href="#" class="easyui-linkbutton" onclick="assignGuia()">Guardar</a>
    	    <a id="chguias-cancelBtn" href="#" class="easyui-linkbutton" onclick="javascript:$('#chguias-dialog').dialog('close')">Cancelar</a>
    	</div>
    </div>

    <script type="text/javascript">
        // datos del formulario de nuevo/edit guia
        // - declaracion del formulario
        $('#chguias-form').form();
        // - botones
    	$('#chguias-clearBtn').linkbutton({ iconCls: 'icon-undo'}).tooltip({
    		position: 'top',
    		content: '<span style="color:#000">Limpiar formulario</span>',
    		onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
    		}
		});
    	$('#chguias-clearBtn').bind('click',function() {
    	    $('#chguias-form').form('clear'); 
    	    $('#chguias-Search').combogrid({ 'setValue' : '' });
			$('#chguias-Operation').val('insert'); // mark operation as 'insert'
			$('#chguias-Club').val($('#chguias-newClub').val()); // restore "Club" field
    	});
        $('#chguias-okBtn').linkbutton({ iconCls: 'icon-ok'}).tooltip({
            position: 'top',
            content: '<span style="color:#000">Aceptar datos y registrarlos en la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        $('#chguias-cancelBtn').linkbutton({ iconCls: 'icon-cancel'}).tooltip({
            position: 'top',
            content: '<span style="color:#000">Anular operaci&oacute;n. Cerrar ventana</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        
        // campos del formulario
        $('#chguias-dialog').dialog( {
                closed: true,
                buttons: '#chguias-dlg-buttons'
        });
        $('#chguias-Nombre').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#chguias-Email').validatebox({
            required: false,
            validType: 'email'
        });
        $('#chguias-Search').combogrid({
    		panelWidth: 350,
    		panelHeight: 200,
    		idField: 'Nombre',
    		textField: 'Nombre',
    		url: 'database/enumerate_GuiasClub.php',
    		method: 'get',
    		mode: 'remote',
    		columns: [[
    			{field:'Nombre',title:'Nombre',sortable:true,width:60,align:'right'},
    			{field:'Club',title:'Club',sortable:true,width:40,align:'right'}
    		]],
    		multiple: false,
    		fitColumns: true,
    		singleSelect: true,
    		selectOnNavigation: false ,
    		onSelect: function(index,row) {
    			var nombre=row.Nombre;
    			if (!nombre) return;
    	        $('#chguias-form').form('load','database/guiaFunctions.php?Operation=getbyname&Nombre='+nombre); // load form with json retrieved data
    			$('#chguias-Club').val($('#chguias-newClub').val()); // restore "Club" field
    			$('#chguias-Viejo').val(nombre);
    		}
    	});
</script>