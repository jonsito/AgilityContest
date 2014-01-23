
   <!-- FORMULARIO DE NUEVA INSCRIPCION -->
    <div id="inscripciones-dialog" class="easyui-dialog" style="width:750px;height:360px;padding:10px 20px">
        <div class="ftitle">Datos de la Inscripci&oacute;n</div>
        <div class="fitem">
            <form name="inscripciones-buscar" method="get">
            <label for="Participante">Participante:</label>
            <select id="inscripciones-Participante" name="Participante" class="easyui-combogrid" style="width:250px" value=""></select>
            <a id="inscripciones-newDogBtn" href="#" class="easyui-linkbutton" style="float:right">Nuevo Perro</a>
            </form>
        </div>
        <div class="fitem">
        	<form id="inscripciones-data" method="get" novalidate="novalidate">
                <input id="inscripciones-LOE_RRC" name="LOE_RRC" type="hidden" />
                <input id="inscripciones-Raza" name="Raza" type="hidden" />
        		<table>
        		<tr>
        			<td colspan="4">
            		<input id="inscripciones-Dorsal" type="hidden" name="Dorsal"/>
        			<label for="Nombre">Nombre:</label>
            		<input id="inscripciones-Nombre" name="Nombre" type="text" size="20" disabled="disabled" />
        			</td>
        		</tr>
        		<tr>
        			<td colspan="2">
            		<label for="Licencia">Licencia: </label>
            		<input id="inscripciones-Licencia" name="Licencia" type="text" size="4" disabled="disabled" />
        			</td>
        			<td colspan="2">
            		<label for="Categoria">Categoria: </label>
            		<input id="inscripciones-Categoria" name="Categoria" type="text" size="1" disabled="disabled"/>
        			</td>
        			<td colspan="2">
            		<label for="Grado">Grado: </label>
            		<input id="inscripciones-Grado" name="Grado" type="text" size="1" disabled="disabled" />
        			</td>
        			<td>&nbsp;</td>
        			<td>
        			<a id="inscripciones-editDogBtn" href="#" class="easyui-linkbutton" style="float:right">Editar</a>
        			</td>
        		</tr>
        		<tr>
        			<td colspan="4">
            		<label for="Guia">Gu&iacute;a: </label>
           			<input id="inscripciones-Guia" name="Guia" type="text" disabled="disabled" />
        			</td>
        			<td colspan="4">
           			<label for="Club"> Club: </label>
           			<input id="inscripciones-Club" name="Club" type="text" disabled="disabled" /><br />
        			</td>
        		</tr>
        		</table>
        	</form>
        </div>
        
        <form id="inscripciones-form" method="get" novalidate>
        	<!-- these two fields need to be filled before commit -->
        	<input id="inscripciones-fDorsal" type="hidden" name="Dorsal"/>
        	<input id="inscripciones-fPruebaID" type="hidden" name="ID"/>
        	<input id="inscripciones-fOperation" type="hidden" name="Operation"/>
        	<!-- "standard" data fields -->
        	<div class="fitem">
            	<label for="Celo">Celo:</label>
            	<input id="inscripciones-Celo" name="Celo" class="easyui-checkbox" type="checkbox" value="1" />
            </div>
            <div class="fitem">
                <label for="Equipo">Equipo: </label>
                <select id="inscripciones-Equipo" name="Equipo" class="easyui-combogrid" style="width:200px"></select>
            </div>
            <div>Jornadas:</div>
            <div class="fitem">
                <label for="J1"><input id="inscripciones-J1" name="J1" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>J1</label>
                <label for="J2"><input id="inscripciones-J2" name="J2" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>J2</label>
                <label for="J3"><input id="inscripciones-J3" name="J3" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>J3</label>
                <label for="J4"><input id="inscripciones-J4" name="J4" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>J4</label>
                <label for="J5"><input id="inscripciones-J5" name="J5" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>J5</label>
                <label for="J6"><input id="inscripciones-J6" name="J6" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>J6</label>
                <label for="J7"><input id="inscripciones-J7" name="J7" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>J7</label>
                <label for="J8"><input id="inscripciones-J8" name="J8" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>J8</label>
            </div>
           <div><hr /></div>
            <div class="fitem">
                <label for="Pagado">Pagado: </label>
                <input id="inscripciones-Pagado" name="Pagado" class="easyui-validatebox" type="text" size="3" style="width:50px;text-align:right;padding-right:5px"/>&euro;
                <span style="float:right">
                <label for="Observaciones">Observaciones: </label>
                <input id="inscripciones-Observaciones" name="Observaciones" type="text" style="width:250px"/>
                </span>
            </div>
        </form>
    
		<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
		<div id="inscripciones-dlg-buttons">        	
			<a id="inscripciones-cancelBtn" href="#" class="easyui-linkbutton" >Cerrar</a>
        	<a id="inscripciones-okBtn" href="#" class="easyui-linkbutton" onclick="insertInscripcion()">Inscribir</a>
        	<a id="inscripciones-clearBtn" href="#" class="easyui-linkbutton">Limpiar</a>
    	</div>
    </div>

    <script type="text/javascript">
    // datos del formulario de nuevo/edit prueba
    $('#inscripciones-dialog').dialog({
        closed:true,
        buttons:'#inscripciones-dlg-buttons'
    });
    // - declaracion del formulario
    $('#inscripciones-buscar').form();
    $('#inscripciones-data').form();
    $('#inscripciones-form').form();
    
    // - botones
    $('#inscripciones-okBtn').linkbutton({iconCls:'icon-save'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Aceptar datos. Inscribir perro en la prueba</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#inscripciones-cancelBtn').linkbutton({iconCls:'icon-cancel'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Cerrar formulario de inscripciones</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#inscripciones-cancelBtn').bind('click',function() {
        $('#inscripciones-buscar').form('clear'); 
        $('#inscripciones-data').form('clear'); 
        $('#inscripciones-form').form('clear'); 
        $('#inscripciones-Participante').combogrid({ 'setValue' : '' });
        $('#inscripciones-Equipo').combogrid({ 'setValue' : '-- Sin asignar --' });
        $('#inscripciones-dialog').dialog('close');
    });
    $('#inscripciones-clearBtn').linkbutton({iconCls:'icon-undo'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Limpiar formulario</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#inscripciones-clearBtn').bind('click',function() {
        $('#inscripciones-buscar').form('clear'); 
        $('#inscripciones-data').form('clear'); 
        $('#inscripciones-form').form('clear'); 
        $('#inscripciones-Participante').combogrid({ 'setValue' : '' });
        $('#inscripciones-Equipo').combogrid({ 'setValue' : '-- Sin asignar --' });
    });
    
    $('#inscripciones-newDogBtn').linkbutton({iconCls:'icon-dog'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Dar de alta un nuevo perro</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#inscripciones-newDogBtn').bind('click',function() {
        $('#inscripciones-buscar').form('clear'); 
        $('#inscripciones-data').form('clear'); 
        $('#inscripciones-form').form('clear'); 
        $('#inscripciones-Participante').combogrid({ 'setValue' : '' });
        newDog();
    });

    $('#inscripciones-editDogBtn').linkbutton({iconCls:'icon-edit'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Modificar los datos del perro</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#inscripciones-editDogBtn').bind('click',function() {
        editInscribedDog(0);
    });
    
    // campos del formulario
	
    $('#inscripciones-Equipo').combogrid({
		panelWidth: 350,
		panelHeight: 100,
		idField: 'ID',
		textField: 'Nombre',
		url: 'database/enumerate_EquiposByPrueba.php?ID='+workingData.prueba,
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
    
    $('#inscripciones-Participante').combogrid({
		panelWidth: 350,
		panelHeight: 200,
		idField: 'Dorsal',
		textField: 'Nombre',
		url: 'database/dogFunctions.php?Operation=enumerate',
		method: 'get',
		mode: 'remote',
		required: true,
		columns: [[
			{field:'Dorsal',hidden:'true'},
			{field:'Nombre',title:'Perro',width:20,align:'right'},
			{field:'Categoria',title:'Cat.',width:10,align:'center'},
			{field:'Grado',title:'Grado',width:10,align:'center'},
			{field:'Guia',title:'Guia',width:40,align:'right'},
			{field:'Club',title:'Club',width:20,align:'right'},
		]],
		multiple: false,
		fitColumns: true,
		singleSelect: true,
		selectOnNavigation: false ,
		onSelect: function(index,row) {
			var dorsal=row.Dorsal;
			if (!dorsal) return;
			// load form with json retrieved data
	        $('#inscripciones-data').form('load','database/dogFunctions.php?Operation=getbydorsal&Dorsal='+dorsal);
	        // and set default Team and money
	        $('#inscripciones-Equipo').combogrid('setValue','-- Sin asignar --'); 
	        $('#inscripciones-Pagado').val(0); 
		}
	});

    function doMoney(checkbox) {
        var money=Number($('#inscripciones-Pagado').val());
        if (checkbox.checked) $('#inscripciones-Pagado').val(money+12);
        else $('#inscripciones-Pagado').val(money-12);
    }
    </script>