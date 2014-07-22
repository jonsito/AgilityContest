<!-- PANEL INFORMATIVO SOBRE LA PRUEBA Y JORNADAS ASOCIADAS -->

<?php include_once("dialogs/dlg_perros.inc");?>
<?php include_once("dialogs/dlg_guias.inc");?>
<?php include_once("dialogs/dlg_clubes.inc");?>
<?php include_once("dialogs/dlg_jornadas.inc");?>
<?php include_once("dialogs/dlg_inscripciones.inc");?>

<div id="inscripciones-info" class="easyui-panel" title="Informaci&oacute;n de la prueba">
<div id="inscripciones-infolayout" class="easyui-layout" style="height:150px">
	<div data-options="region:'west',title:'Datos de la Prueba',split:true,collapsed:false" style="width:300px;padding:10px" class="c_inscripciones-datosprueba">
		<form id="inscripciones-pruebas" method="get" >
		<input type="hidden" name="ID"/>
		<input type="hidden" name="Club"/>
		<input type="hidden" name="Ubicacion"/>
		<input type="hidden" name="Triptico"/>
		<input type="hidden" name="Cartel"/>
		<input type="hidden" name="Cerrada"/>
		<p>
		<label for="Nombre" style="font-weight:bold">Denominaci&oacute;n:</label>
		<input id="inscripciones-pnombre" type="text" name="Nombre" disabled="disabled" size="19"/>
		</p>
		<p>
		<label for="Club" style="font-weight:bold">Club Organizador:</label>
		<input id="inscripciones-pclub" type="text" name="NombreClub" disabled="disabled" size="15"/>
		</p>
		<p>
		<label for="Observaciones" style="font-weight:bold">Observaciones:</label>
		<input id="inscripciones-pcomments" type="text" name="Observaciones" disabled="disabled" size="33"/>
		</p>
		</form>
	</div>
	<div data-options="region:'center',title:'Lista de jornadas de la prueba'" style="width:500px">
		<table id="inscripciones-jornadas" class="easyui-datagrid"></table>
	</div>
</div> 
</div> 
	
	<!-- BARRA DE TAREAS -->
    <div id="inscripciones-toolbar" style="padding:10px 10px 40px 10px;">
    	<span style="float:left">
    	<select id="inscripciones-newGrid" class="easyui-combogrid" style="width:150px"></select>
    	<a id="inscripciones-newBtn" href="#" class="easyui-linkbutton"
    		plain="true" iconCls="icon-add"
    		onclick="insertInscripcion()">Inscribir</a>
    	<a id="inscripciones-editBtn" href="#" class="easyui-linkbutton"
    		plain="true" iconCls="icon-edit"
    		onclick="editInscripcion()">Editar Registro</a>
    	<a id="inscripciones-delBtn" href="#" class="easyui-linkbutton"
    		plain="true" iconCls="icon-remove"
    		onclick="deleteInscripcion()">Borrar inscripci&oacute;n</a>
     	</span>
    	<span style="float:right">
    	<!-- estos elementos deben estar alineados a la derecha -->
    	<a id="inscripciones-printBtn" href="#" class="easyui-linkbutton"
    		plain="true" iconCls="icon-print"
    		>Imprimir</a>
	   	<a id="inscripciones-reloadBtn" href="#" class="easyui-linkbutton" 
	   		plain="true" iconCls="icon-reload"
	   		onclick="$('#inscripciones-datagrid').datagrid('reload');">Refrescar</a>
	   	</span>
    </div>
  
<div id="inscripciones-list" class="easyui-panel" style="width:auto;height:400px;">  
	<!-- DECLARACION DE LA TABLA DE INSCRIPCIONES -->
	<table id="inscripciones-datagrid" class="easyui-datagrid" ></table>
</div>

<div id="inscripciones-progresswindow" class="easyui-window">
	<p id="inscripciones-progresslabel" style="align:center">Inscribiendo a:</p>
	<div id="inscripciones-progressbar" class="easyui-progressbar" style="align:center;"></div>
</div> 

<script type="text/javascript">
// cabecera de la pagina
$('#Header_Operation').html('<p>Inscripciones - Formulario de registro</p>');
// inicializamos formularios
$('#inscripciones-info').panel({
	border:true,
	closable:false,
	collapsible:true,
	collapsed:true
});

$('#inscripciones-list').panel({
	noHeader:	true,
	border:		true,
	closable:	false,
	collapsible:false,
	collapsed:	false,
});
$('#inscripciones-infolayout').layout();
$('#inscripciones-pruebas').form('load','database/pruebaFunctions.php?Operation=getbyid&ID='+workingData.prueba);
$('#inscripciones-jornadas').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: true,
	collapsed: false,
	// propiedades especificas del datagrid
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
	url: 'database/jornadaFunctions.php?Operation=select&Prueba='+workingData.prueba,
	method: 'get',
	loadMsg: 'Actualizando datos de las jornadas...',
    columns:[[
            { field:'ID',			hidden:true }, // ID de la jornada
      	    { field:'Prueba',		hidden:true }, // ID de la prueba
      	    { field:'Numero',		width:10, sortable:false,	align:'center', title: '#'},
      		{ field:'Nombre',		width:70, sortable:false,   align:'right',  title: 'Nombre/Comentario' },
      		{ field:'Fecha',		width:40, sortable:false,	align:'right',  title: 'Fecha: ' },
      		{ field:'Hora',			width:30, sortable:false,	align:'right',  title: 'Hora.  ' },
      		{ field:'Grado1',		width:15, sortable:false,	align:'center', title: 'G-I    ' },
      		{ field:'Grado2',		width:15, sortable:false,	align:'center', title: 'G-II   ' },
      		{ field:'Grado3',		width:15, sortable:false,	align:'center', title: 'G-III  ' },
      		{ field:'Equipos3',		width:15, sortable:false,	align:'center', title: 'Eq. 3/4' },
      		{ field:'Equipos4',		width:15, sortable:false,	align:'center', title: 'Eq. Conj.' },
      		{ field:'PreAgility',	width:15, sortable:false,	align:'center', title: 'Pre.   ' },
      		{ field:'KO',			width:15, sortable:false,	align:'center', title: 'K.O.   ' },
      		{ field:'Exhibicion',	width:15, sortable:false,	align:'center', title: 'Show   ' },
      		{ field:'Otras',		width:15, sortable:false,	align:'center', title: 'Otras  ' },
      		{ field:'Cerrada',		width:20, sortable:false,	align:'center', title: 'Cerrada', formatter:identificaJornada }
    ]],
    rowStyler:myRowStyler,
	// on double click fireup editor dialog
	onDblClickRow:function(idx,row) { //idx: selected row index; row selected row data
    	editJornadaFromPrueba(workingData.prueba,'#inscripciones-jornadas');
	}
});

//activa teclas up/down para navegar por el panel de gestion de jornadas
$('#inscripciones-jornadas').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
    function selectRow(t,up){
    	var count = t.datagrid('getRows').length;    // row count
    	var selected = t.datagrid('getSelected');
    	if (selected){
        	var index = t.datagrid('getRowIndex', selected);
        	index = index + (up ? -1 : 1);
        	if (index < 0) index = 0;
        	if (index >= count) index = count - 1;
        	t.datagrid('clearSelections');
        	t.datagrid('selectRow', index);
    	} else {
        	t.datagrid('selectRow', (up ? count-1 : 0));
    	}
	}
	var t = $('#inscripciones-jornadas');
    switch(e.keyCode){
    case 38:	/* Up */	selectRow(t,true); return false;
    case 40:    /* Down */	selectRow(t,false); return false;
    case 13:	/* Enter */	editJornadaFromPrueba(workingData.prueba,'#inscripciones-jornadas');; return false;
    }
});

// esta funcion anyade un id al campo de jornada de manera que sea identificable
function identificaJornada(val,row,index) {
	return '<span id="jornada_cerrada-'+parseInt(index+1)+'" >'+val+'</span>';
}

// datos de la tabla de inscripciones
// - tabla
$('#inscripciones-datagrid').datagrid({
	title: 'Listado de inscritos en la prueba',
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades especificas del datagrid
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    multiSort: true,
    remoteSort: false,
	url: 'database/inscripcionFunctions.php?Operation=inscritos&IDPrueba='+workingData.prueba,
	method: 'get',
	loadMsg: 'Actualizando datos de inscripciones....',
    toolbar: '#inscripciones-toolbar',
    columns: [[
        { field:'ID',     hidden:true }, // inscripcion ID
        { field:'Perro',  hidden:true }, // dog ID
        { field:'Equipo', hidden:true }, // only used on Team contests
        { field:'Pagado', hidden:true }, // to store if handler paid :-)
        { field:'Guia', hidden:true }, // Guia ID
        { field:'Club', hidden:true }, // Club ID
    	{ field:'Dorsal',	width:6,  sortable:true, align: 'right',	title: 'Dorsal' },
    	{ field:'Nombre',	width:15, sortable:true, align: 'right',	title: 'Nombre' },
    	{ field:'Categoria',width:5,  sortable:true, align: 'center',  	title: 'Cat.' },
    	{ field:'Grado',	width:5,  sortable:true, align: 'center',  	title: 'Grado' },
    	{ field:'NombreGuia',	width:25, sortable:true, align: 'right',	title: 'Guia' },
    	{ field:'NombreClub',	width:15, sortable:true, align: 'right',	title: 'Club' },
    	{ field:'Observaciones',width:15,            title: 'Observaciones' },
    	{ field:'Celo',		width:4, align:'center', title: 'Celo' },
        { field:'J1',		width:3, align:'center', title: 'J1'},
        { field:'J2',		width:3, align:'center', title: 'J2'},
        { field:'J3',		width:3, align:'center', title: 'J3'},
        { field:'J4',		width:3, align:'center', title: 'J4'},
        { field:'J5',		width:3, align:'center', title: 'J5'},
        { field:'J6',		width:3, align:'center', title: 'J6'},
        { field:'J7',		width:3, align:'center', title: 'J7'},
        { field:'J8',		width:3, align:'center', title: 'J8'},
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:myRowStyler,
	// on double click fireup editor dialog
    onDblClickRow:function() { 
        editInscripcion();
    }
});

// activa teclas up/down para navegar por el panel
$('#inscripciones-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
    function selectRow(t,up){
    	var count = t.datagrid('getRows').length;    // row count
    	var selected = t.datagrid('getSelected');
    	if (selected){
        	var index = t.datagrid('getRowIndex', selected);
        	index = index + (up ? -1 : 1);
        	if (index < 0) index = 0;
        	if (index >= count) index = count - 1;
        	t.datagrid('clearSelections');
        	t.datagrid('selectRow', index);
    	} else {
        	t.datagrid('selectRow', (up ? count-1 : 0));
    	}
	}
	function selectPage(t,offset) {
		var p=t.datagrid('getPager').pagination('options');
		var curPage=p.pageNumber;
		var lastPage=1+parseInt(p.total/p.pageSize);
		if (offset==-2) curPage=1;
		if (offset==2) curPage=lastPage;
		if ((offset==-1) && (curPage>1)) curPage=curPage-1;
		if ((offset==1) && (curPage<lastPage)) curPage=curPage+1;
    	t.datagrid('clearSelections');
    	p.pageNumber=curPage;
    	t.datagrid('options').pageNumber=curPage;
    	t.datagrid('reload',{
    		where: $('#inscripciones-search').val(),
            onLoadSuccess: function(data){
            	t.datagrid('getPager').pagination('refresh',{pageNumber:curPage});
            }
    	});
	}
	var t = $('#inscripciones-datagrid');
    switch(e.keyCode){
    case 38:	/* Up */	selectRow(t,true); return false;
    case 40:    /* Down */	selectRow(t,false); return false;
    case 13:	/* Enter */	editInscripcion(); return false;
    case 45:	/* Insert */ newInscripcion(); return false;
    case 46:	/* Supr */	deleteInscripcion(); return false;
    case 33:	/* Re Pag */ selectPage(t,-1); return false;
    case 34:	/* Av Pag */ selectPage(t,1); return false;
    case 35:	/* Fin */    selectPage(t,2); return false;
    case 36:	/* Inicio */ selectPage(t,-2); return false;
    case 9: 	/* Tab */
        // if (e.shiftkey) return false; // shift+Tab
        return false;
    case 16:	/* Shift */
    case 17:	/* Ctrl */
    case 18:	/* Alt */
    case 27:	/* Esc */
        return false;
    }
}); 


// combogrid de inscripcion de participantes
$('#inscripciones-newGrid').combogrid({
    fit:true,
    delay: 250, // dont search on every keystroke
	panelWidth: 400,
	panelHeight: 150,
	idField: 'Perro',
	textField: 'Nombre',
	url: 'database/inscripcionFunctions.php?Operation=noinscritos&IDPrueba='+workingData.prueba,
	method: 'get',
	mode: 'remote',
	required: false,
	value: '- Nuevas inscripciones -',
	columns: [[
	   	{field:'ID',hidden:'true'},
		{field:'Guia',hidden:'true'},
		{field:'Club',hidden:'true'},
		{field:'Nombre',title:'Perro',width:20,align:'right'},
		{field:'Categoria',title:'Cat.',width:10,align:'center'},
		{field:'Grado',title:'Grado',width:10,align:'center'},
		{field:'NombreGuia',title:'Guia',width:40,align:'right'},
		{field:'NombreClub',title:'Club',width:20,align:'right'}
	]],
	multiple: true,
	fitColumns: true,
	singleSelect: false,
	selectOnNavigation: false
});


// tooltips
addTooltip($('#inscripciones-newBtn').linkbutton(),"Inscribir el/los perro(s) seleccionados"); 
addTooltip($('#inscripciones-editBtn').linkbutton(),"Modificar la inscripción seleccionada");
addTooltip($('#inscripciones-delBtn').linkbutton(),"Eliminar la inscripción seleccionada de la BBDD");
addTooltip($('#inscripciones-printBtn').linkbutton(),"Imprimir la lista de inscritos en la prueba");
addTooltip($('#inscripciones-reloadBtn').linkbutton(),"Refrescar la lista de inscripciones para la prueba");

// special handling for printing inscritos
$('#inscripciones-printBtn').on("click", function () {
	$.fileDownload(
		'pdf/inscritosByPrueba.php',
		{
			httpMethod: 'GET',
			data: { Prueba: workingData.prueba},
	        preparingMessageHtml: "We are preparing your report, please wait...",
	        failMessageHtml: "There was a problem generating your report, please try again."
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
});

// ventana de progreso de las inscripciones
$('#inscripciones-progresswindow').window({
	 width:450,
	 height:200,
	 modal:true,
	 collapsable:false,
	 minimizable:false,
	 maximizable:false,
	 closable:false,
	 closed:true
});

$('#inscripciones-progressbar').progressbar({
	width: 300,
    value: 0
});

</script>