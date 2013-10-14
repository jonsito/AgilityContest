<!-- FORMULARIO DE SELECCION DE PRUEBAS ABIERTAS-->
<div id="selprueba-window" class="easyui-window" style="position:relative,width:500px;height:150px;padding:20px 20px">
	<div id="selprueba-Layout" class="easyui-layout" fit="true">
		<div id="selprueba-Content" region="north" border="true">
			<form id="selprueba-Prueba">
        		<div class="fitem">
            		<label for="Search">Buscar:</label>
            		<select id="selprueba-Search" name="Search" class="easyui-combogrid" style="width:200px">&nbsp;</select>
        		</div>
			</form>
		</div> <!-- contenido -->
		<div region="center" border="false">&nbsp;</div>
		<div id="selprueba-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
    	    <a id="selprueba-okBtn" href="#" class="easyui-linkbutton" onclick="acceptSelectPrueba()">Aceptar</a>
    	    <a id="selprueba-cancelBtn" href="#" class="easyui-linkbutton" onclick="cancelSelectPrueba()">Cancelar</a>
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
	modal: true
});

$('#selprueba-form').form();

$('#selprueba-okBtn').linkbutton({ iconCls: 'icon-ok' }).tooltip({
	position: 'top',
	content: '<span style="color:#000">Trabajar con la prueba seleccionada</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

$('#selprueba-cancelBtn').linkbutton({ iconCls: 'icon-cancel' }).tooltip({
	position: 'top',
	content: '<span style="color:#000">Cancelar selecci&oacute;n. Cerrar ventana</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

$('#selprueba-Search').combogrid({
	panelWidth: 350,
	panelHeight: 100,
	idField: 'Nombre',
	textField: 'Nombre',
	url: 'database/enumerate_PruebasAbiertas.php',
	method: 'get',
	mode: 'remote',
	columns: [[
		{field:'Nombre',title:'Nombre',width:30,align:'right'},
		{field:'Club',title:'Club',width:20,align:'right'},
		{field:'Observaciones',title:'Observaciones.',width:50,align:'right'}
	]],
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	selectOnNavigation: false 
});

function acceptSelectPrueba() {
	workingData.prueba=$('#selprueba-Search').combogrid('getValue');	
	$('#selprueba-window').window('close');	
}

function cancelSelectPrueba() {
	workingData.prueba='';
	$('#selprueba-window').window('close');
}
</script>