<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE JUECES -->

    <!-- INFORMACION ADICIONAL 
    <div class="demo-info" style="margin-bottom:10px">
        <div class="demo-tip icon-tip">&nbsp;</div>
        <div>Selecciona con el rat&oacute;n las acciones a realizar en la barra de tareas</div>
    </div>
    -->
    
    <!-- DECLARACION DE LA TABLA -->
    <table id="jueces-datagrid" class="easyui-datagrid" style="width:800px;height:400px" />
    
    <!-- BARRA DE TAREAS -->
    <div id="jueces-toolbar">
        <a id="jueces-newBtn" href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newJuez()">Nuevo Juez</a>
        <a id="jueces-editBtn" href="#" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editJuez()">Editar Juez</a>
        <a id="jueces-delBtn" href="#" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyJuez()">Borrar Juez</a>
    </div>
    
    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE PERROS -->
    <div id="jueces-dialog" class="easyui-dialog" style="width:450px;height:450px;padding:10px 20px"
            closed="true" buttons="#jueces-dlg-buttons">
        <div class="ftitle">Informaci&oacute;n del perro</div>
        <form id="jueces-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="jueces-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	required="true"
                	style="width:250px" />
                <input id="jueces-Viejo" name="Viejo" type="hidden" /> <!-- used to allow operator change juez name -->
            </div>
            <div class="fitem">
                <label for="Direccion1">Direccion 1:</label>
                <input id="jueces-Direccion1" class="easyui-validatebox" name="Direccion1" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="Direccion2">Direccion 2:</label>
                <input id="jueces-Direccion2" class="easyui-validatebox" name="Direccion2" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="Telefono">Tel&eacute;fono:</label>
                <input id="jueces-Telefono" class="easyui-validatebox" name="Telefono" type="text" />
            </div>
            <div class="fitem">
                <label for="Internacional">Juez internacional:</label>
                <input id="jueces-Internacional" name="Internacional" class="easyui-checkbox" type="checkbox" />
            </div>
            <div class="fitem">
                <label for="Practicas">Juez en pr&aacute;cticas:</label>
                <input id="jueces-Practicas" name="Practicas" class="easyui-checkbox" type="checkbox" />
            </div>
            <div class="fitem">
                <label for="Email">Correo electr&oacute;nico:</label>
                <input id="jueces-Email" name="Email" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Observaciones">Observaciones:</label>
                <input id="jueces-Observaciones" name="Observaciones" type="textarea" style="height:50px;width:300px";/>
            </div>
        </form>
    </div>
    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="jueces-dlg-buttons">
        <a id="jueces-okBtn" href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveJuez()">Guardar</a>
        <a id="jueces-cancelBtn" href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#jueces-dialog').dialog('close')">Cancelar</a>
    </div>
    
    <script language="javascript">
    
    	// set up operation header content
        $('#Header_Operation').html('<p>Gesti&oacute;n de Jueces</p>');
        
        // tell jquery to convert declared elements to jquery easyui Objects
        
        // datos de la tabla de perros
        // - tabla
        $('#jueces-datagrid').datagrid({
        	title: 'Gesti&oacute;n de datos de Jueces',
        	url: 'database/json/get_jueces.php',
        	method: 'get',
            toolbar: '#jueces-toolbar',
            pagination: true,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            columns: [[
            	{ field:'Nombre',		width:20, sortable:true,	title: 'Nombre:' },
            	{ field:'Direccion1',	width:12,					title: 'Direcci&oacute;n 1:' },
            	{ field:'Direccion2',	width:12,                   title: 'Direcci&oacute;n 2' },
            	{ field:'Telefono',		width:11, sortable:true,	title: 'Tel&eacute;fono' },
            	{ field:'Internacional',width:5, align:'center',	title: 'Int.' 	},
            	{ field:'Practicas',	width:5, align:'center',	title: 'Pract.' },
            	{ field:'Email',		width:18, sortable:true,    title: 'Correo Electr&oacute;nico' },
                { field:'Observaciones',width:15,					title: 'Observaciones'}
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:function(index,row) { 
                return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
            },
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editJuez();
            }
        }); 
        // - botones de la toolbar de la tabla
        $('#jueces-newBtn').linkbutton(); // nuevo juez
        $('#jueces-editBtn').linkbutton(); // editar juez
        $('#jueces-delBtn').linkbutton(); // borrar juez
        
        // datos del formulario de nuevo/edit perros
        // - declaracion del formulario
        $('#jueces-form').form();
        // - botones
        $('#jueces-okBtn').linkbutton();
        $('#jueces-cancelBtn').linkbutton();
        $('#jueces-dialog').dialog();
        // campos del formulario
        // $('#jueces-Practicas').checkbox(); // checkboxes doesn't exist in easyui ?
        // $('#jueces-Internacional').checkbox(); // checkboxes doesn't exist in easyui ?
        $('#jueces-Nombre').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#jueces-Email').validatebox({
            required: false,
            validType: 'email'
        });
	</script>