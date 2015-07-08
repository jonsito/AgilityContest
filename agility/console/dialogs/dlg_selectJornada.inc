<!-- 
dlg_selectJornada.inc

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<?php
require_once(__DIR__."/../../server/auth/Config.php");
require_once(__DIR__."/../../server/tools.php");
$config =Config::getInstance();
?>

<!-- FORMULARIO DE SELECCION DE PRUEBAS ABIERTAS-->
<div id="seljornada-window" style="position:relative;width:500px;height:auto;padding:20px 20px">
	<div id="seljornada-Layout" class="easyui-layout" data-options="fit:'true'">
		<form id="seljornada-Selection">
			<div id="seljornada-Content" data-options="region:'north',border:'true'">
        		<div class="fitem">
            		<label for="Prueba">Selecciona Prueba:</label>
            		<select id="seljornada-Prueba" name="Prueba" style="width:200px"></select>
        		</div>        		
        		<div class="fitem">
            		<label for="Jornada">Selecciona Jornada:</label>
            		<select id="seljornada-Jornada" name="Jornada" style="width:200px"></select>
        		</div>
			</div> <!-- contenido -->
		</form>
		<div data-options="region:'center'"></div>
		<div id="seljornada-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
    	    <a id="seljornada-okBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls: 'icon-ok'" onclick="acceptSelectJornada()">Aceptar</a>
    	    <a id="seljornada-cancelBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls: 'icon-cancel'" onclick="cancelSelectJornada()">Cancelar</a>
		</div>	<!-- botones -->
	</div> <!-- Layout -->
</div> <!-- Window -->

<script type="text/javascript">
$('#seljornada-window').window({
	title: 'Selecciona la Prueba y Jornada a desplegar',
	collapsible: false,
	minimizable: false,
	maximizable: false,
	closable: true,
	closed: true,
	shadow: true,
	modal: true
});

$('#seljornada-form').form();
$('#seljornada-layout').layout();

addTooltip($('#seljornada-okBtn').linkbutton(),"Trabajar con la jornada seleccionada");
addTooltip($('#seljornada-cancelBtn').linkbutton(),"Cancelar selecci&oacute;n. Cerrar ventana");

$('#seljornada-Prueba').combogrid({
	panelWidth: 550,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/pruebaFunctions.php?Operation=enumerate',
	method: 'get',
	mode: 'remote',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	selectOnNavigation: true, // let use cursor keys to interactive select
	editable: isMobileDevice()?false:true, //disable keyboard deploy on mobile devices
	columns: [[
	   	    {field:'ID',			hidden:true},
			{field:'Nombre',		title:'Nombre',			width:50,	align:'right'},
            {field:'Club',			hidden:true},
			{field:'NombreClub',	title:'Club',			width:20,	align:'right'},
			{field:'RSCE',			title:'Fed.',			width:15,	align:'center', formatter:formatRSCE},
			{field:'Observaciones',	title:'Observaciones.',	width:30,	align:'right'},
            {field:'Inscritos',		hidden:true},
            {field:'UserLimit',		hidden:true}
	]],
	onChange:function(value){
		var p=$('#seljornada-Prueba').combogrid('grid').datagrid('getSelected');
		if (p===null) return; // no selection
		setPrueba(p); // retorna jornada, o 0 si la prueba ha cambiado
		$('#seljornada-Jornada').combogrid('clear');
		$('#seljornada-Jornada').combogrid('grid').datagrid('load',{Prueba:p.ID});
	},
	onLoadSuccess: function(data) {
		if (workingData.prueba!=0) $('#seljornada-Prueba').combogrid('setValue',workingData.prueba);
		else {
			$('#seljornada-Prueba').combogrid('grid').datagrid('clearSelections');
			$('#seljornada-Jornada').combogrid('grid').datagrid('clearSelections');
		}
	}
});

$('#seljornada-Jornada').combogrid({
	panelWidth: 550,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/jornadaFunctions.php',
	method: 'get',
	mode: 'remote',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	editable: isMobileDevice()?false:true, //disable keyboard deploy on mobile devices
	columns: [[
	    { field:'ID',			hidden:true }, // ID de la jornada
	    { field:'Prueba',		hidden:true }, // ID de la prueba
	    { field:'Numero',		width:4, sortable:false,	align:'center', title: '#'},
		{ field:'Nombre',		width:30, sortable:false,   align:'right',  title: 'Nombre/Comentario' },
		{ field:'Fecha',		hidden:true},
		{ field:'Hora',			hidden:true},
		{ field:'Grado1',		width:8, sortable:false,	align:'center', title: 'G-I    ' },
		{ field:'Grado2',		width:8, sortable:false,	align:'center', title: 'G-II   ' },
		{ field:'Grado3',		width:8, sortable:false,	align:'center', title: 'G-III  ' },
		{ field:'Open',		    width:8, sortable:false,	align:'center', title: 'Open   ' },
		{ field:'Equipos3',		width:8, sortable:false,	align:'center', title: 'Eq.3x4 ' },
		{ field:'Equipos4',		width:8, sortable:false,	align:'center', title: 'Eq.Conj' },
		{ field:'PreAgility',	width:8, sortable:false,	align:'center', title: 'Pre. 1 ' },
		{ field:'PreAgility2',	width:8, sortable:false,	align:'center', title: 'Pre. 2 ' },
		{ field:'KO',			width:8, sortable:false,	align:'center', title: 'K.O.   ' },
		{ field:'Especial',		width:8, sortable:false,	align:'center', title: 'Show   ' }
    ]],
	onBeforeLoad: function(param) { 
		param.Operation='enumerate';
		param.Prueba=workingData.prueba;
		param.AllowClosed=<?php echo $allowClosed;?>;
		param.HideUnassigned=1;
		return true;
	},
	onLoadSuccess: function(data) {
		if (workingData.jornada!=0) $('#seljornada-Jornada').combogrid('setValue',workingData.jornada);
	}
});

function acceptSelectJornada() {
	// si prueba invalida cancelamos operacion
	var p=$('#seljornada-Prueba').combogrid('grid').datagrid('getSelected');
	var j=$('#seljornada-Jornada').combogrid('grid').datagrid('getSelected');
	if ( (p==null) || (j==null) ) {
		// indica error
		$.messager.alert("Error","Debe indicar una prueba y jornada v&aacute;lidas","error");
		return;
	}
    if (parseInt(p.Inscritos) > parseInt(p.UserLimit)) {
        var message='<img src="/agility/images/sad_dog.png" width="100" alt="sad dog" style="float:right;"/>'+
            '<p style="font-weight:bold;">Los permisos de la licencia instalada<br/> no permiten la gesti&oacute;n de pruebas</br> con m&aacute;s de '+p.UserLimit+' inscripciones</p>'
        $.messager.alert({
            title: 'Access denied',
            msg: message,
            icon: 'error',
            width: 450
        });
        $('#seljornada-Prueba').combogrid('grid').datagrid('clearSelections');
        $('#seljornada-Jornada').combogrid('grid').datagrid('clearSelections');
        return; // forbidden selection
    }
	// store selected data into global structure
	setPrueba(p);
	setJornada(j);
	$('#seljornada-window').window('close');	
}

function cancelSelectJornada() {
	$('#seljornada-window').window('close');
}
</script>