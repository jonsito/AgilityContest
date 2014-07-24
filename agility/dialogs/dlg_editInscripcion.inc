
   <!-- FORMULARIO DE EDICION DE INSCRIPCIONES -->
    <div id="edit_inscripcion-dialog" class="easyui-dialog" style="width:750px;height:400px;padding:10px 20px">
        <div class="ftitle">Datos de la Inscripci&oacute;n</div>

        
        	<form id="edit_inscripcion-data" method="get" novalidate="novalidate">
        		<input id="edit_inscripcion-ID" type="hidden" name="ID"/>
        		<input id="edit_inscripcion-Operation" type="hidden" name="Operation"/>
        
        		<div> Datos del Perro</div>		
        		<div class="fitem">
        			<table>
        			<tr>
        				<td colspan="4">
                		<input id="edit_inscripcion-LOE_RRC" name="LOE_RRC" type="hidden" />
                		<input id="edit_inscripcion-Raza" name="Raza" type="hidden" />
            			<input id="edit_inscripcion-Perro" name="Perro" type="hidden" />
        				<label for="Nombre">Nombre:</label>
            			<input id="edit_inscripcion-Nombre" name="Nombre" type="text" size="20" disabled="disabled" />
        				</td>
        			</tr>
        			<tr>
        				<td colspan="2">
            			<label for="Licencia">Licencia: </label>
            			<input id="edit_inscripcion-Licencia" name="Licencia" type="text" size="5" disabled="disabled" />
        				</td>
        				<td colspan="2">
            			<label for="Categoria">Categoria: </label>
            			<input id="edit_inscripcion-Categoria" name="Categoria" type="text" size="3" disabled="disabled"/>
        				</td>
        				<td colspan="2">
            			<label for="Grado">Grado: </label>
            			<input id="edit_inscripcion-Grado" name="Grado" type="text" size="3" disabled="disabled" />
        				</td>
        				<td>&nbsp;</td>
        				<td>
        				<a id="edit_inscripcion-editDogBtn" href="#" class="easyui-linkbutton" style="float:right">Editar</a>
        				</td>
        			</tr>
        			<tr>
        				<td colspan="4">
        				<input id="edit_inscripcion-Guia" type="hidden" name="Guia"/>
            			<label for="NombreGuia">Gu&iacute;a: </label>
           				<input id="edit_inscripcion-NombreGuia" name="NombreGuia" type="text" disabled="disabled" />
        				</td>
        				<td colspan="4">
        				<input id="edit_inscripcion-Club" type="hidden" name="Club"/>
           				<label for="NombreClub"> Club: </label>
           				<input id="edit_inscripcion-NombreClub" name="NombreClub" type="text" disabled="disabled" /><br />
        				</td>
        			</tr>
        			</table>
        		</div>
        	
        		<div>Datos de la inscripci&oacute;n</div>
            	<div>Jornadas:</div>
            	<div class="fitem">
            	    <label for="J1">J1</label>
            	    <input id="edit_inscripcion-J1" name="J1" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label for="J2">J2</label>
            	    <input id="edit_inscripcion-J2" name="J2" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label for="J3">J3</label>
            	    <input id="edit_inscripcion-J3" name="J3" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label for="J4">J4</label>
            	    <input id="edit_inscripcion-J4" name="J4" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label for="J5">J5</label>
            	    <input id="edit_inscripcion-J5" name="J5" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label for="J6">J6</label>
            	    <input id="edit_inscripcion-J6" name="J6" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label for="J7">J7</label>
            	    <input id="edit_inscripcion-J7" name="J7" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label for="J8">J8</label>
            	    <input id="edit_inscripcion-J8" name="J8" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label for="Equipo">Equipo: </label>
            	    <select id="edit_inscripcion-Equipo" name="Equipo" style="width:200px"></select>
				</div>
            
				<div>Datos adicionales</div>
        		<div class="fitem">
            		<label for="Celo">Celo:</label>
            		<input id="edit_inscripcion-Celo" name="Celo" class="easyui-checkbox" type="checkbox" value="1" />
	                <label for="Pagado">Pagado: </label>
	                <input id="edit_inscripcion-Pagado" name="Pagado" class="easyui-validatebox" type="text" size="3" style="width:50px;text-align:right;padding-right:5px"/>&euro;
	                <span style="float:right">
	                <label for="Observaciones">Observaciones: </label>
	                <input id="edit_inscripcion-Observaciones" name="Observaciones" type="text" style="width:250px"/>
	                </span>
				</div>
	        </form>
    
			<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
			<div id="edit_inscripcion-dlg-buttons">        	
				<a id="edit_inscripcion-cancelBtn" href="#" class="easyui-linkbutton" >Cancelar</a>
        		<a id="edit_inscripcion-okBtn" href="#" class="easyui-linkbutton" onclick="updateInscripcion()">Aceptar</a>
        		<a id="edit_inscripcion-clearBtn" href="#" class="easyui-linkbutton">Limpiar</a>
    		</div>
    	</div>

    <script type="text/javascript">
    // datos del formulario de nuevo/edit prueba
    $('#edit_inscripcion-dialog').dialog({
        closed:true,
        buttons:'#edit_inscripcion-dlg-buttons'
    });
    // - declaracion del formulario
    $('#edit_inscripcion-buscar').form();
    $('#edit_inscripcion-data').form();
    $('#edit_inscripcion-form').form();
    
    // - botones
    $('#edit_inscripcion-okBtn').linkbutton({iconCls:'icon-save'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Aceptar datos. Inscribir perro en la prueba</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#edit_inscripcion-cancelBtn').linkbutton({iconCls:'icon-cancel'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Cerrar formulario de inscripciones</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#edit_inscripcion-cancelBtn').bind('click',function() {
        $('#edit_inscripcion-buscar').form('clear'); 
        $('#edit_inscripcion-data').form('clear'); 
        $('#edit_inscripcion-form').form('clear'); 
        $('#edit_inscripcion-Participante').combogrid({ 'setValue' : '' });
        $('#edit_inscripcion-Equipo').combogrid({ 'setValue' : '-- Sin asignar --' });
        $('#edit_inscripcion-dialog').dialog('close');
    });
    $('#edit_inscripcion-clearBtn').linkbutton({iconCls:'icon-undo'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Limpiar formulario</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#edit_inscripcion-clearBtn').bind('click',function() {
        $('#edit_inscripcion-buscar').form('clear'); 
        $('#edit_inscripcion-data').form('clear'); 
        $('#edit_inscripcion-form').form('clear'); 
        $('#edit_inscripcion-Participante').combogrid({ 'setValue' : '' });
        $('#edit_inscripcion-Equipo').combogrid({ 'setValue' : '-- Sin asignar --' });
    });
    
    $('#edit_inscripcion-newDogBtn').linkbutton({iconCls:'icon-dog'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Dar de alta un nuevo perro</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#edit_inscripcion-newDogBtn').bind('click',function() {
        $('#edit_inscripcion-buscar').form('clear'); 
        $('#edit_inscripcion-data').form('clear'); 
        $('#edit_inscripcion-form').form('clear'); 
        $('#edit_inscripcion-Participante').combogrid({ 'setValue' : '' });
        newDog();
    });

    $('#edit_inscripcion-editDogBtn').linkbutton({iconCls:'icon-edit'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Modificar los datos del perro</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#edit_inscripcion-editDogBtn').bind('click',function() {
        editInscribedDog(0);
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
    
    $('#edit_inscripcion-Participante').combogrid({
        fit:true,
        delay: 500, // dont search on every keystroke
		panelWidth: 640,
		panelHeight: 200,
		idField: 'IDPerro',
		textField: 'Nombre',
		url: 'database/dogFunctions.php?Operation=enumerate',
		method: 'get',
		mode: 'remote',
		required: false,
		columns: [[
			{field:'IDPerro',hidden:'true'},
			{field:'Nombre',title:'Perro',width:20,align:'right'},
			{field:'Categoria',title:'Cat.',width:10,align:'center'},
			{field:'Grado',title:'Grado',width:10,align:'center'},
			{field:'Guia',title:'Guia',width:40,align:'right'},
			{field:'Club',title:'Club',width:20,align:'right'}
		]],
		multiple: true,
		fitColumns: true,
		singleSelect: false,
		selectOnNavigation: false ,
		onSelect: function(index,row) {
			var idperro=row.IDPerro;
			if (!idperro) return;
			// load form with json retrieved data
	        $('#edit_inscripcion-data').form('load','database/dogFunctions.php?Operation=getbyidperro&IDPerro='+idperro);
	        // and set default Team and money
	        $('#edit_inscripcion-Equipo').combogrid('setValue','-- Sin asignar --'); 
	        $('#edit_inscripcion-Pagado').val(0); 
		}
	});

    function doMoney(checkbox) {
        var money=Number($('#edit_inscripcion-Pagado').val());
        if (checkbox.checked) $('#edit_inscripcion-Pagado').val(money+12);
        else $('#edit_inscripcion-Pagado').val(money-12);
    }
    </script>