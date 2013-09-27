<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE PERROS -->

    <!-- INFORMACION ADICIONAL 
    <div class="demo-info" style="margin-bottom:10px">
        <div class="demo-tip icon-tip">&nbsp;</div>
        <div>Selecciona con el rat&oacute;n las acciones a realizar en la barra de tareas</div>
    </div>
    -->
    
    <!-- DECLARACION DE LA TABLA -->
    <table id="perros-datagrid" 
    	title="Gesti&oacute;n de datos de Perros" 
    	class="easyui-datagrid" 
    	style="width:800px;height:375px"
    	url="database/json/get_dogs.php"
    	toolbar="#perros-toolbar"
    	pagination="true"
    	rownumbers="false"
    	fitColumns="true"
    	singleSelect="true">
        <thead>
            <tr>
                <th field="Dorsal" width="5" align="right" sortable="true">Dorsal</th>
                <th field="Nombre" width="10" sortable="true">Nombre</th>
                <th field="Raza" width="15">Raza</th>
                <th field="LOE_RRC" width="10" sortable="true" align="right">LOE / RRC</th>
                <th field="Licencia" width="10" sortable="true" align="right">Lic.</th>
                <th field="Categoria" width="5" align="center">Cat.</th>
                <th field="Grado" width="5" align="center">Grado</th>
                <th field="Guia" width="40" sortable="true">Nombre del Gu&iacute;a</th>
            </tr>
        </thead>
    </table>
    
    <!-- BARRA DE TAREAS -->
    <div id="perros-toolbar">
        <a id="perros-newBtn" href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newDog()">Nuevo Perro</a>
        <a id="perros-editBtn" href="#" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editDog()">Editar Perro</a>
        <a id="perros-delBtn" href="#" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyDog()">Borrar Perro</a>
    </div>
    
    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE PERROS -->
    <div id="perros-dialog" class="easyui-dialog" style="width:500px;height:375px;padding:10px 20px"
            closed="true" buttons="#perros-dlg-buttons">
        <div class="ftitle">Informaci&oacute;n del perro</div>
        <form id="fm" method="post" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input name="Nombre" class="easyui-validatebox" required="true">
            </div>
            <div class="fitem">
                <label for="Raza">Raza:</label>
                <input name="Raza">
            </div>
            <div class="fitem">
                <label for="LOE/RRC">Num LOE/RRC:</label>
                <input name="LOE/RRC">
            </div>
            <div class="fitem">
                <label for="Licencia">Num Licencia:</label>
                <input name="Licencia">
            </div>
            <div class="fitem">
                <label for="Categoria">Categor&iacute;a:</label>
                <?php include("database/Select-Categorias_Perro.php")?>
            </div>
            <div class="fitem">
                <label for="Grado">Grado:</label>
                <?php include("database/Select-Grados_Perro.php")?>
            </div>
            <div class="fitem">
                <label for="Guia">Nombre del Gu&iacute;a:</label>
                <select id="perros-guia" name="Guia" class="easyui-combogrid" style="width:300px"
                	data-options="
            			panelWidth: 350,
            			panelHeight: 200,
            			idField: 'Nombre',
            			textField: 'Nombre',
            			url: 'database/json/enumerate_GuiasClub.php',
            			method: 'get',
            			mode: 'remote',
            			columns: [[
                			{field:'Nombre',title:'Nombre del gu&iacute;a',width:80,align:'right'},
                			{field:'Club',title:'Club',width:40,align:'right'},
            			]],
            			multiple: false,
            			fitColumns: true,
            			selectOnNavigation: false
                ">
                </select>
            </div>
        </form>
    </div>
    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="perros-dlg-buttons">
        <a id="perros-okBtn" href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveUser()">Guardar</a>
        <a id="perros-cancelBtn" href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#perros-dialog').dialog('close')">Cancelar</a>
    </div>
    
    <script language="javascript">
    
    	// set up operation header content
        $('#Header_Operation').html('<p>Gesti&oacute;n de Perros</p>');
        
        // tell jquery to convert declared elements to jquery easyui Objects
        $('#perros-datagrid').datagrid(); 
        $('#perros-newBtn').linkbutton();
        $('#perros-editBtn').linkbutton();
        $('#perros-delBtn').linkbutton();
        $('#perros-okBtn').linkbutton();
        $('#perros-cancelBtn').linkbutton();
        $('#perros-dialog').dialog();
        $('#perros-guia').combogrid();
        
        // on double click fireup editor dialog
        $('#perros-datagrid').datagrid({
            onDblClickRow:function() { 
                editDog();
        	}
        });
        // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
        $('#perros-datagrid').datagrid({
            rowStyler:function(index,row) { 
                return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
            }
        });
       
	</script>