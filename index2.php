<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>El diario de "La Cachorrera"</title>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/datagrid.css" />
<link rel="stylesheet" type="text/css" href="lib/jquery-easyui-1.3.4/themes/default/easyui.css" />
<link rel="stylesheet" type="text/css" href="lib/jquery-easyui-1.3.4/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="lib/jquery-easyui-1.3.4/demo/demo.css" />
<script src="lib/jquery-1.10.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="lib/jquery-easyui-1.3.4/jquery.easyui.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="scripts/loadContents.js" type="text/javascript" charset="utf-8" > </script>
<script src="scripts/perros.js" type="text/javascript" charset="utf-8" > </script>
</head>
<body>

<!-- CABECERA -->
<div id="myheader">
	<p> El diario de "La Cachorrera" </p>
	<span id="Header_Operation"></span>
</div>

<!-- LOGO -->
<div id="mylogo">
	<p><img src="icons/logo_welpe.jpg" height="120" alt="logo welpe" /></p>
</div>

<!-- MENU LATERAL -->
<div id="mysidebar">
<ul>
<li>BASE DE DATOS
	<ul>
	<li><a href="javascript:loadContents('#contenido','clubes.php');">Clubes</a></li>
	<li><a href="javascript:loadContents('#contenido','guias.php');">Gu&iacute;as</a></li>
	<li><a href="javascript:loadContents('#contenido','perros.php');">Perros</a></li>
	<li><a href="javascript:loadContents('#contenido','jueces.php');">Jueces</a></li>
	</ul>
</li>
<li>PRUEBAS
	<ul>
	<li><a href="javascript:loadContents('#contenido','alta_pruebas.php');">Creaci&oacute;n de pruebas</a></li>
	<li><a href="javascript:loadContents('#contenido','edicion_pruebas.php');">Edici&oacute;n. Inscripciones</a></li>
	<li><a href="javascript:loadContents('#contenido','desarrollo_pruebas.php');">Desarrollo de la prueba</a></li>
	</ul>
</li>
<li>CONSULTAS
	<ul>
	<li><a href="javascript:loadContents('#contenido','resultados.php');">Resultados</a></li>
	<li><a href="javascript:loadContents('#contenido','estadisticas.php');">Estad&iacute;sticas</a></li>
	</ul>
</li>
</ul>
</div>
	
<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->
<div id="mycontent">
<span id="contenido">
<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE PERROS -->

	<!-- CABECERA -->
    <h2>Gesti&oacute;n de datos de Perros</h2>
    
    <!-- INFORMACION ADICIONAL 
    <div class="demo-info" style="margin-bottom:10px">
        <div class="demo-tip icon-tip">&nbsp;</div>
        <div>Selecciona con el rat&oacute;n las acciones a realizar en la barra de tareas</div>
    </div>
    -->
    
    <!-- DECLARACION DE LA TABLA -->
    <table id="dg" 
    	title="The dogs" 
    	class="easyui-datagrid" 
    	style="width:800px;height:375px"
    	url="database/json/get_dogs.php"
    	toolbar="#toolbar"
    	pagination="true"
    	rownumbers="false"
    	fitColumns="true"
    	singleSelect="true">
        <thead>
            <tr>
                <th field="Dorsal" width="5 align="right" sortable="true">Dorsal</th>
                <th field="Nombre" width="20" sortable="true">Nombre</th>
                <th field="Raza" width="30">Raza</th>
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
</span>
</div>

</body>

</html> 
