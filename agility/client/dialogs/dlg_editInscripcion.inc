<!-- FORMULARIO DE EDICION DE INSCRIPCIONES -->
<div id="edit_inscripcion-dialog" style="width:525px;padding:10px">
	<div style="height:425px" class="dialog_forms">
        <form id="edit_inscripcion-form" method="get" novalidate="novalidate">
        	<input id="edit_inscripcion-ID" type="hidden" name="ID"/>
        	<input id="edit_inscripcion-Prueba" type="hidden" name="Prueba"/>
        	<input id="edit_inscripcion-Jornadas" type="hidden" name="Jornadas"/>
        	<input id="edit_inscripcion-Operation" type="hidden" name="Operation" value="update"/>
            <input id="edit_inscripcion-LOE_RRC" name="LOE_RRC" type="hidden" />
            <input id="edit_inscripcion-Raza" name="Raza" type="hidden" />
           	<input id="edit_inscripcion-Perro" name="Perro" type="hidden" />
           	<span  id="edit_inscripcion-dogInfo" 	class="ftitle">Informaci&oacute;n del perro:</span>
           	<span  id="edit_inscripcion-inscInfo" 	class="ftitle" style="tex-align:center">Datos de la inscripci&oacute;n:</span>
           	<label id="edit_inscripcion-Nombrelbl"	for="Nombre">Nombre:</label>
           	<input id="edit_inscripcion-Nombre" 	name="Nombre" type="text" disabled="disabled" />
        	<label id="edit_inscripcion-Licencialbl" for="Licencia">Licencia: </label>
        	<input id="edit_inscripcion-Licencia"	name="Licencia" type="text" disabled="disabled"/>
        	<label id="edit_inscripcion-Categorialbl" for="Categoria">Categoria: </label>
            <input id="edit_inscripcion-Categoria" 	name="Categoria" type="text" style="text-align:center" disabled="disabled"/>
        	<label id="edit_inscripcion-Gradolbl"	for="Grado">Grado: </label>
            <input id="edit_inscripcion-Grado" 		name="Grado" type="text" style="text-align:center" disabled="disabled" />
            <label id="edit_inscripcion-NombreGuialbl"	for="NombreGuia">Gu&iacute;a: </label>
            <input id="edit_inscripcion-NombreGuia" name="NombreGuia" type="text" disabled="disabled"/>
            <label id="edit_inscripcion-NombreClublbl" for="NombreClub">Club: </label>
           	<input id="edit_inscripcion-NombreClub" name="NombreClub" type="text" disabled="disabled" />
           	<input id="edit_inscripcion-J1" 		name="J1" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
           	<label id="edit_inscripcion-J1lbl" 		for="J1">J1</label>
            <input id="edit_inscripcion-J2" 		name="J2" class="easyui-checkbox" type="checkbox" value="2" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J2lbl" 		for="J2">J2</label>
            <input id="edit_inscripcion-J3"			name="J3" class="easyui-checkbox" type="checkbox" value="4" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J3lbl"		for="J3">J3</label>
            <input id="edit_inscripcion-J4"			name="J4" class="easyui-checkbox" type="checkbox" value="8" onclick="doMoney(this);"/>
 	        <label id="edit_inscripcion-J4lbl"		for="J4">J4</label>
            <input id="edit_inscripcion-J5"			name="J5" class="easyui-checkbox" type="checkbox" value="16" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J5lbl" 		for="J5">J5</label>
            <input id="edit_inscripcion-J6" 		name="J6" class="easyui-checkbox" type="checkbox" value="32" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J6lbl" 		for="J6">J6</label>
            <input id="edit_inscripcion-J7" 		name="J7" class="easyui-checkbox" type="checkbox" value="64" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J7lbl" 		for="J7">J7</label>
            <input id="edit_inscripcion-J8" 		name="J8" class="easyui-checkbox" type="checkbox" value="128" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J8lbl" 		for="J8">J8</label>
        	<label id="edit_inscripcion_Equipolbl"	for="Equipo">Equipo: </label>         	    
            <span id="edit_inscripcion-Equiposp">
            	<select id="edit_inscripcion-Equipo" 	name="Equipo" class="easyui-combogrid" style="width:130px"></select>
            </span>
            <label id="edit_inscripcion-Celolbl"	for="Celo2">Celo:</label>
           	<input id="edit_inscripcion-Celo" 		name="Celo" type="hidden" value="0"/>
            <input id="edit_inscripcion-Celo2"		name="Celo2" class="easyui-checkbox" type="checkbox" value="1" />
	        <label id="edit_inscripcion-Observacioneslbl" for="Observaciones">Observaciones:</label>
	        <input id="edit_inscripcion-Observaciones" type="text" name="Observaciones"/>
	        <label id="edit_inscripcion-Pagadolbl"	for="Pagado">Pagado: </label>
 	  		<input id="edit_inscripcion-Pagado" 	name="Pagado" 
 	  			class="easyui-validatebox" type="text" style="text-align:right;padding-right:5px"/>
 	  		<span id="edit_inscripcion-PagadoEuro">&euro;</span>
        </form> 
    </div>
    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
	<div id="edit_inscripcion-dlg-buttons" style="padding:5px">
		<span style="float:left">
	   		<a id="edit_inscripcion-editDogBtn" href="#" class="easyui-linkbutton"
	   			data-options="iconCls:'icon-dog'">Modificar</a>
	   	</span>
	   	<span style="float:right">  	
			<a id="edit_inscripcion-cancelBtn" href="#" class="easyui-linkbutton"
				data-options="iconCls:'icon-cancel'">Cancelar</a>
       		<a id="edit_inscripcion-okBtn" href="#" class="easyui-linkbutton" onclick="saveInscripcion(true)" 
       			data-options="iconCls:'icon-save'">Aceptar</a>
       	</span>
    </div>
    
</div>

    
    <script type="text/javascript">
    
    // declaracion del dialogo
    $('#edit_inscripcion-dialog').dialog({
        iconCls: 'icon-notes',
        closed: true,
        modal: true,
        buttons: '#edit_inscripcion-dlg-buttons',
        title: 'Editar inscripcion'
    });

    $('#edit_inscripcion-form').form({
        onLoadSuccess: function(data) {
            // retrieve info on jornadas
        	var jornadas=$('#inscripciones-jornadas').datagrid('getData')['rows'];
            // evaluate celo checkbox status
        	$('#edit_inscripcion-Celo2').prop('checked',(data.Celo!=0)?true:false);
            // evaluate checked status of jornadas
        	$('#edit_inscripcion-J1').prop('checked',(data.Jornadas&0x0001)?true:false);
        	$('#edit_inscripcion-J2').prop('checked',(data.Jornadas&0x0002)?true:false);
        	$('#edit_inscripcion-J3').prop('checked',(data.Jornadas&0x0004)?true:false);
        	$('#edit_inscripcion-J4').prop('checked',(data.Jornadas&0x0008)?true:false);
        	$('#edit_inscripcion-J5').prop('checked',(data.Jornadas&0x0010)?true:false);
        	$('#edit_inscripcion-J6').prop('checked',(data.Jornadas&0x0020)?true:false);
        	$('#edit_inscripcion-J7').prop('checked',(data.Jornadas&0x0040)?true:false);
        	$('#edit_inscripcion-J8').prop('checked',(data.Jornadas&0x0080)?true:false);
        	// fix disabled status on closed jornadas
        	$('#edit_inscripcion-J1').prop('disabled',(jornadas[0].Cerrada==="1")?true:false);
        	$('#edit_inscripcion-J2').prop('disabled',(jornadas[1].Cerrada==="1")?true:false);
        	$('#edit_inscripcion-J3').prop('disabled',(jornadas[2].Cerrada==="1")?true:false);
        	$('#edit_inscripcion-J4').prop('disabled',(jornadas[3].Cerrada==="1")?true:false);
        	$('#edit_inscripcion-J5').prop('disabled',(jornadas[4].Cerrada==="1")?true:false);
        	$('#edit_inscripcion-J6').prop('disabled',(jornadas[5].Cerrada==="1")?true:false);
        	$('#edit_inscripcion-J7').prop('disabled',(jornadas[6].Cerrada==="1")?true:false);
        	$('#edit_inscripcion-J8').prop('disabled',(jornadas[7].Cerrada==="1")?true:false);
        	// fix text for jornadas
        	$('#edit_inscripcion-J1lbl').text('J1 - '+jornadas[0].Nombre);
        	$('#edit_inscripcion-J2lbl').text('J2 - '+jornadas[1].Nombre);
        	$('#edit_inscripcion-J3lbl').text('J3 - '+jornadas[2].Nombre);
        	$('#edit_inscripcion-J4lbl').text('J4 - '+jornadas[3].Nombre);
        	$('#edit_inscripcion-J5lbl').text('J5 - '+jornadas[4].Nombre);
        	$('#edit_inscripcion-J6lbl').text('J6 - '+jornadas[5].Nombre);
        	$('#edit_inscripcion-J7lbl').text('J7 - '+jornadas[6].Nombre);
        	$('#edit_inscripcion-J8lbl').text('J8 - '+jornadas[7].Nombre);
        }
    });
    
    // tooltips
    addTooltip($('#edit_inscripcion-editDogBtn').linkbutton(),"Modificar la información del perro<br/>en la Base de Datos");
    addTooltip($('#edit_inscripcion-okBtn').linkbutton(),"Aceptar cambios<br/> Actualizar datos de la inscripci&oacute;n");
    addTooltip($('#edit_inscripcion-cancelBtn').linkbutton(),"Cancelar cambios. Cerrar la ventana");
    
 	// acciones de los botones
    $('#edit_inscripcion-cancelBtn').bind('click',function() {
        $('#edit_inscripcion-form').form('reset'); 
        $('#edit_inscripcion-Equipo').combogrid({ 'setValue' : '-- Sin asignar --' });
        $('#edit_inscripcion-dialog').dialog('close');
    });

    $('#edit_inscripcion-editDogBtn').bind('click',function() {
        editInscribedDog();
    });
    
    // campos del formulario
	
    $('#edit_inscripcion-Equipo').combogrid({
		panelWidth: 350,
		panelHeight: 100,
		idField: 'ID',
		textField: 'Nombre',
		url: '/agility/server/database/equiposFunctions.php?Operation=enumerate&Prueba='+workingData.prueba,
		method: 'get',
		mode: 'remote',
		required: true,
		columns: [[
			{field:'ID',hidden:'true'},
			{field:'Nombre',title:'Nombre',width:25,align:'right'},
			{field:'Observaciones',title:'Observaciones',width:75,align:'right'}
		]],
		multiple: false,
		fitColumns: true,
		singleSelect: true,
		selectOnNavigation: false
    });
    
    function doMoney(obj) {
        // current values
    	var jornadas=$('#edit_inscripcion-Jornadas').val();
        var money=Number($('#edit_inscripcion-Pagado').val());
        // evaluate jornadas
    	if (obj.checked) $('#edit_inscripcion-Jornadas').val(jornadas | obj.value);
    	else $('#edit_inscripcion-Jornadas').val(jornadas & ~obj.value);
    	// evaluate money
        if (obj.checked) $('#edit_inscripcion-Pagado').val(money+12);
        else $('#edit_inscripcion-Pagado').val(money-12);
    }

    // layout
    var dg= {'cols':8, 'rows':15}; // declare base datagrid
	doLayout(dg,'#edit_inscripcion-dogInfo',		0,	0,	3,	1	);
	doLayout(dg,"#edit_inscripcion-inscInfo",		3,	0,	4,	1	);
	doLayout(dg,"#edit_inscripcion-Nombrelbl",		0,	1,	2,	1	);
	doLayout(dg,"#edit_inscripcion-Nombre",			0,	2,	3,	1	);
	doLayout(dg,"#edit_inscripcion-Licencialbl",	0,	3,	2,	1	);
	doLayout(dg,"#edit_inscripcion-Licencia",		0,	4,	3,	1	);
	doLayout(dg,"#edit_inscripcion-Categorialbl",	0,	5,	1,	1	);
	doLayout(dg,"#edit_inscripcion-Categoria",		0,	6,	1,	1	);
	doLayout(dg,"#edit_inscripcion-Gradolbl",		2,	5,	1,	1	);
	doLayout(dg,"#edit_inscripcion-Grado",			2,	6,	1,	1	);
	doLayout(dg,"#edit_inscripcion-NombreGuialbl",	0,	7,	2,	1	);
	doLayout(dg,"#edit_inscripcion-NombreGuia",		0,	8,	3,	1	);
	doLayout(dg,"#edit_inscripcion-NombreClublbl",	0,	9,	2,	1	);
	doLayout(dg,"#edit_inscripcion-NombreClub",		0,	10,	3,	1	);
	doLayout(dg,"#edit_inscripcion-J1",				3,	1,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J1lbl",			4,	1,	3,	1	);
	doLayout(dg,"#edit_inscripcion-J2",				3,	2,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J2lbl",			4,	2,	3,	1	);
	doLayout(dg,"#edit_inscripcion-J3",				3,	3,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J3lbl",			4,	3,	3,	1	);
	doLayout(dg,"#edit_inscripcion-J4",				3,	4,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J4lbl",			4,	4,	3,	1	);
	doLayout(dg,"#edit_inscripcion-J5",				3,	5,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J5lbl" ,			4,	5,	3,	1	);
	doLayout(dg,"#edit_inscripcion-J6",				3,	6,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J6lbl",			4,	6,	3,	1	);
	doLayout(dg,"#edit_inscripcion-J7" ,			3,	7,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J7lbl",			4,	7,	3,	1	);
	doLayout(dg,"#edit_inscripcion-J8",				3,	8,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J8lbl",			4,	8,	3,	1	);
	doLayout(dg,"#edit_inscripcion_Equipolbl",		3,	9,	1,	1	);   	    
	doLayout(dg,"#edit_inscripcion-Equiposp",		4,	9,	2,	1	); 
	doLayout(dg,"#edit_inscripcion-Celolbl",		3,	10,	1,	1	);
	doLayout(dg,"#edit_inscripcion-Celo2",			4,	10,	1,	1	);
	doLayout(dg,"#edit_inscripcion-Pagadolbl",		3,	11,	1,	1	);
	doLayout(dg,"#edit_inscripcion-Pagado",			4,	11,	1,	1	);
	doLayout(dg,"#edit_inscripcion-PagadoEuro",		5,	11,	1,	1	);
	doLayout(dg,"#edit_inscripcion-Observacioneslbl",0,	11,	3,	1	);
	doLayout(dg,"#edit_inscripcion-Observaciones",	0,	12,	6,	1	);
    </script>