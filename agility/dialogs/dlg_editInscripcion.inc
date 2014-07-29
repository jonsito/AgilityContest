<!-- FORMULARIO DE EDICION DE INSCRIPCIONES -->
<div id="edit_inscripcion-dialog" 
   	style="width:475px;height:525px;padding:10px">
        <form id="edit_inscripcion-data" method="get" novalidate="novalidate">
        	<input id="edit_inscripcion-ID" type="hidden" name="ID"/>
        	<input id="edit_inscripcion-Operation" type="hidden" name="Operation" value="update"/>
            <input id="edit_inscripcion-LOE_RRC" name="LOE_RRC" type="hidden" />
            <input id="edit_inscripcion-Raza" name="Raza" type="hidden" />
           	<input id="edit_inscripcion-Perro" name="Perro" type="hidden" />
           	<span  id="edit_inscripcion-dogInfo" 	class="ftitle">Informaci&oacute;n del perro:</span>
           	<span  id="edit_inscripcion-inscInfo" 	class="ftitle">Datos de la inscripci&oacute;n:</span>
           	<label id="edit_inscripcion-Nombrelbl"	for="Nombre">Nombre:</label>
           	<input id="edit_inscripcion-Nombre" 	name="Nombre" type="text" disabled="disabled" />
        	<label id="edit_inscripcion-Licencialbl" for="Licencia">Licencia: </label>
        	<input id="edit_inscripcion-Licencia"	name="Licencia" type="text" disabled="disabled"/>
        	<label id="edit_inscripcion-Categorialbl" for="Categoria">Categoria: </label>
            <input id="edit_inscripcion-Categoria" 	name="Categoria" type="text" disabled="disabled"/>
        	<label id="edit_inscripcion-Gradolbl"	for="Grado">Grado: </label>
            <input id="edit_inscripcion-Grado" 		name="Grado" type="text" disabled="disabled" />
            <label id="edit_inscripcion-NombreGuialbl"	for="NombreGuia">Gu&iacute;a: </label>
            <input id="edit_inscripcion-NombreGuia" name="NombreGuia" type="text" disabled="disabled"/>
            <label id="edit_inscripcion-NombreClublbl" for="NombreClub">Club: </label>
           	<input id="edit_inscripcion-NombreClub" name="NombreClub" type="text" disabled="disabled" />
           	<input id="edit_inscripcion-J1" 		name="J1" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
           	<label id="edit_inscripcion-J1lbl" 		for="J1">J1</label>
            <input id="edit_inscripcion-J2" 		name="J2" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J2lbl" 		for="J2">J2</label>
            <input id="edit_inscripcion-J3"			name="J3" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J3lbl"		for="J3">J3</label>
            <input id="edit_inscripcion-J4"			name="J4" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
 	        <label id="edit_inscripcion-J4lbl"		for="J4">J4</label>
            <input id="edit_inscripcion-J5"			name="J5" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J5lbl" 		for="J5">J5</label>
            <input id="edit_inscripcion-J6" 		name="J6" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J6lbl" 		for="J6">J6</label>
            <input id="edit_inscripcion-J7" 		name="J7" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J7lbl" 		for="J7">J7</label>
            <input id="edit_inscripcion-J8" 		name="J8" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J8lbl" 		for="J8">J8</label>
        	<label id="edit_inscripcion_Equipolbl"	for="Equipo">Equipo: </label>         	    
            <span id="edit_inscripcion-Equiposp">
            	<select id="edit_inscripcion-Equipo" 	name="Equipo" class="easyui-combogrid" style="width:130px"></select>
            </span>
            <label id="edit_inscripcion-Celolbl"	for="Celo">Celo:</label>
            <input id="edit_inscripcion-Celo"		name="Celo" class="easyui-checkbox" type="checkbox" value="1" />
	        <label id="edit_inscripcion-Observacioneslbl" for="Observaciones">Observaciones:</label>
	        <input id="edit_inscripcion-Observaciones" type="text" name="Observaciones"/>
	        <label id="edit_inscripcion-Pagadolbl"	for="Pagado">Pagado: </label>
 	  		<input id="edit_inscripcion-Pagado" 	name="Pagado" 
 	  			class="easyui-validatebox" type="text" style="text-align:right;padding-right:5px"/>
 	  		<span id="edit_inscripcion-PagadoEuro">&euro;</span>
        </form> 
           		
    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
	<div id="edit_inscripcion-dlg-buttons" style="padding:2px 5px">
		<span style="float:left">
	   		<a id="edit_inscripcion-editDogBtn" href="#" class="easyui-linkbutton"
	   			data-options="iconCls:'icon-edit'">Editar</a>
	   	</span>
	   	<span style="float:right">  	
			<a id="edit_inscripcion-cancelBtn" href="#" class="easyui-linkbutton"
				data-options="iconCls:'icon-cancel'">Cancelar</a>
       		<a id="edit_inscripcion-okBtn" href="#" class="easyui-linkbutton" onclick="updateInscripcion()" 
       			data-options="iconCls:'icon-save'">Aceptar</a>
       		<a id="edit_inscripcion-clearBtn" class="easyui-linkbutton" href="#"
       			data-options="iconCls:'icon-undo'">Deshacer</a>
       	</span>
    </div>
    
</div>

    
    <script type="text/javascript">
    
    // declaracion del dialogo
    $('#edit_inscripcion-dialog').dialog({
        closed:true,
        modal:true,
        buttons:'#edit_inscripcion-dlg-buttons',
        title: 'Editar inscripcion'
    });

    $('#edit_inscripcion-form').form({
        onLoadSuccess: function(data) {
        }
    });
    
    // tooltips
    addTooltip($('#edit_inscripcion-editDogBtn').linkbutton(),"Modificar la información del perro<br/>en la Base de Datos");
    addTooltip($('#edit_inscripcion-okBtn').linkbutton(),"Aceptar cambios<br/> Actualizar datos de la inscripci&oacute;n");
    addTooltip($('#edit_inscripcion-cancelBtn').linkbutton(),"Cancelar cambios. Cerrar la ventana");
    addTooltip($('#edit_inscripcion-clearBtn').linkbutton(),"Deshacer cambios. restaurar formulario"); 
 	// acciones de los botones
    $('#edit_inscripcion-cancelBtn').bind('click',function() {
        $('#edit_inscripcion-form').form('reset'); 
        $('#edit_inscripcion-Equipo').combogrid({ 'setValue' : '-- Sin asignar --' });
        $('#edit_inscripcion-dialog').dialog('close');
    });
    
    $('#edit_inscripcion-clearBtn').bind('click',function() {
        $('#edit_inscripcion-form').form('reset'); 
        $('#edit_inscripcion-Equipo').combogrid({ 'setValue' : '-- Sin asignar --' });
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
		url: 'database/pruebaFunctions.php?Operation=equipos&ID='+workingData.prueba,
		method: 'get',
		mode: 'remote',
		required: false,
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
    
    function doMoney(checkbox) {
        var money=Number($('#edit_inscripcion-Pagado').val());
        if (checkbox.checked) $('#edit_inscripcion-Pagado').val(money+12);
        else $('#edit_inscripcion-Pagado').val(money-12);
    }

    // layout
    var dg= {'cols':7, 'rows':15}; // declare base datagrid
	doLayout(dg,'#edit_inscripcion-dogInfo',		0,	0,	3,	1	);
	doLayout(dg,"#edit_inscripcion-inscInfo",		3,	0,	3,	1	);
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
	doLayout(dg,"#edit_inscripcion-J1lbl",			4,	1,	2,	1	);
	doLayout(dg,"#edit_inscripcion-J2",				3,	2,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J2lbl",			4,	2,	2,	1	);
	doLayout(dg,"#edit_inscripcion-J3",				3,	3,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J3lbl",			4,	3,	2,	1	);
	doLayout(dg,"#edit_inscripcion-J4",				3,	4,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J4lbl",			4,	4,	2,	1	);
	doLayout(dg,"#edit_inscripcion-J5",				3,	5,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J5lbl" ,			4,	5,	2,	1	);
	doLayout(dg,"#edit_inscripcion-J6",				3,	6,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J6lbl",			4,	6,	2,	1	);
	doLayout(dg,"#edit_inscripcion-J7" ,			3,	7,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J7lbl",			4,	7,	2,	1	);
	doLayout(dg,"#edit_inscripcion-J8",				3,	8,	1,	1	);
	doLayout(dg,"#edit_inscripcion-J8lbl",			4,	8,	2,	1	);
	doLayout(dg,"#edit_inscripcion_Equipolbl",		3,	9,	1,	1	);   	    
	doLayout(dg,"#edit_inscripcion-Equiposp",		4,	9,	2,	1	); 
	doLayout(dg,"#edit_inscripcion-Celolbl",		3,	10,	1,	1	);
	doLayout(dg,"#edit_inscripcion-Celo",			4,	10,	1,	1	);
	doLayout(dg,"#edit_inscripcion-Pagadolbl",		3,	11,	1,	1	);
	doLayout(dg,"#edit_inscripcion-Pagado",			4,	11,	1,	1	);
	doLayout(dg,"#edit_inscripcion-PagadoEuro",		5,	11,	1,	1	);
	doLayout(dg,"#edit_inscripcion-Observacioneslbl",0,	11,	3,	1	);
	doLayout(dg,"#edit_inscripcion-Observaciones",	0,	12,	6,	1	);
    </script>