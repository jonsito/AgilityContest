<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE PERROS -->

	<!-- CABECERA -->
    <h2>Gesti&oacute;n de datos de Perros</h2>
    
    <!-- INFORMACION ADICIONAL -->
    <div class="demo-info" style="margin-bottom:10px">
        <div class="demo-tip icon-tip">&nbsp;</div>
        <div>Selecciona con el rat&oacute;n las acciones a realizar en la barra de tareas</div>
    </div>
    
    <!-- DECLARACION DE LA TABLA -->
    <table id="dg" 
    	title="The dogs" 
    	class="easyui-datagrid" 
    	style="width:800px;height:250px"
    	url="database/json/get_dogs.php"
    	toolbar="#toolbar"
    	pagination="true"
    	rownumbers="true"
    	fitColumns="true"
    	singleSelect="true">
        <thead>
            <tr>
                <th field="Dorsal" width="5 align="right" sortable="true">Dorsal</th>
                <th field="Nombre" width="20" sortable="true">Nombre</th>
                <th field="Raza" width="50">Raza</th>
                <th field="LOE/RRC" width="15" sortable="true">LOE/RRC</th>
                <th field="Licencia" width="10" sortable="true">Licencia</th>
                <th field="Categoria" width="5">Categor&iacute;a</th>
                <th field="Grado" width="5">Grado</th>
                <th field="Guia" width="75" sortable="true">Nombre del Gu&iacute;a</th>
            </tr>
        </thead>
    </table>
    
    <!-- BARRA DE TAREAS -->
    <div id="toolbar">
        <a href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newDog()">Nuevo Perro</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editDog()">Editar Perro</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyDog()">Borrar Perro</a>
    </div>
    
    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE PERROS -->
    <div id="dlg" class="easyui-dialog" style="width:500px;height:280px;padding:10px 20px"
            closed="true" buttons="#dlg-buttons">
        <div class="ftitle">Dog Information</div>
        <form id="fm" method="post" novalidate>
            <div class="fitem">
                <label>Nombre:</label>
                <input name="Nombre" class="easyui-validatebox" required="true">
            </div>
            <div class="fitem">
                <label>Raza:</label>
                <input name="Raza">
            </div>
            <div class="fitem">
                <label>Num LOE/RRC:</label>
                <input name="LOE/RRC">
            </div>
            <div class="fitem">
                <label>Num Licencia:</label>
                <input name="Licencia">
            </div>
            <div class="fitem">
                <label>Categor&iacute;a:</label>
                <?php include("database/Select-Categorias_Perro.php")?>
            </div>
            <div class="fitem">
                <label>Grado:</label>
                <?php include("database/Select-Grados_Perro.php")?>
            </div>
            <div class="fitem">
                <label>Nombre del Gu&iacute;a:</label>
                <input name="Guia">
            </div>
        </form>
    </div>
    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="dlg-buttons">
        <a href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveUser()">Save</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')">Cancel</a>
    </div>
    
    <script language="javascript">
        $('#Header_Operation').html('<p>Gesti&oacute;n de Perros</p>');
	</script>