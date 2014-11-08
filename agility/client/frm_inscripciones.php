<!-- 
frm_inscripciones.php

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->
 
<!-- background image -->
<img class="mainpage" src="/agility/images/wallpapers/foto_klein.jpg" alt="Klein" width="800" height="400" align="middle"/>

<!-- FORMULARIO DE SELECCION DE PRUEBAS ABIERTAS-->
<div id="selprueba-window" class="easyui-window" style="position:relative,width:500px;height:150px;padding:20px 20px">
	<div id="selprueba-Layout" class="easyui-layout" data-options="fit:true'">
		<div id="selprueba-Content" data-options="region:'center',border:'true'">
			<form id="selprueba-Prueba">
        		<div class="fitem">
            		<label for="Search">Buscar:</label>
            		<select id="selprueba-Search" name="Search" style="width:200px"></select>
        		</div>
			</form>
		</div> <!-- contenido -->
		<div id="selprueba-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
    	    <a id="selprueba-okBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls:'icon-ok'" onclick="acceptSelectPrueba()">Aceptar</a>
    	    <a id="selprueba-cancelBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls:'icon-cancel'" onclick="cancelSelectPrueba()">Cancelar</a>
		</div>	<!-- botones -->
	</div> <!-- Layout -->
</div> <!-- Window -->

<script type="text/javascript">

$('#selprueba-window').window({
	title: 'Selecciona la prueba activa',
	collapsible: false,
	minimizable: false,
	maximizable: false,
	closable: true,
	closed: true,
	shadow: true,
	modal: true,
	onClose: function () {
		var title="";
		var page="/agility/client/frm_main.php";
		if(workingData.prueba!=0) {
			page="/agility/client/frm_inscripciones2.php";
			title="Inscripciones - Formulario de registro"
		}
		loadContents(page,title);
	}
});

addTooltip($('#selprueba-okBtn').linkbutton(),"Trabajar con la prueba seleccionada");
addTooltip($('#selprueba-cancelBtn').linkbutton(),"Cancelar selecci&oacute;n. Cerrar ventana");

$('#selprueba-Search').combogrid({
	panelWidth: 450,
	panelHeight: 100,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/pruebaFunctions.php?Operation=enumerate',
	method: 'get',
	mode: 'remote',
	required: true,
	editable: isMobileDevice()?false:true, //disable keyboard deploy on mobile devices
	columns: [[
	    {field:'ID',hidden:true},
		{field:'Nombre',title:'Nombre',width:50,align:'right'},
		{field:'Club',hidden:true},
		{field:'NombreClub',title:'Club',width:20,align:'right'},
		{field:'Observaciones',title:'Observaciones.',width:30,align:'right'}
	]],
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	selectOnNavigation: false 
});

function acceptSelectPrueba() {
	// si no hay ninguna prueba valida seleccionada aborta
	var p=$('#selprueba-Search').combogrid('grid').datagrid('getSelected');
	if (p==null) {
		// indica error
		$.messager.alert("Error","Debe indicar una prueba v&aacute;lida","error");
		return;
	} 
	workingData.prueba=p.ID;
	workingData.nombrePrueba=p.Nombre;
	workingData.datosPrueba=p;
	$('#selprueba-Search').combogrid('setValue','');
	$('#selprueba-window').window('close');	
}

function cancelSelectPrueba() {
	initWorkingData();
	$('#selprueba-Search').combogrid('setValue','');
	$('#selprueba-window').window('close');
}

// make sure working data are clean
initWorkingData();

// display prueba selection dialog
$('#selprueba-window').window('open');

</script>