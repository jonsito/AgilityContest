<!-- 
dlg_ordensalida.inc

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>
<!-- Ventana de seleccion de fichero para importacion de datos excel -->
<div id="ordensalida-excel-dialog" style="width:640px;height:auto;padding:10px; display=none;">
    <?php require_once(__DIR__."/../../lib/templates/import_ordensalida_dialog.inc.php"); ?>
</div>
<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO DE IMPORTACION -->
<div id="ordensalida-excel-buttons">
    <a id="ordensalida-excel-okBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls: 'icon-ok'" onclick="ordensalida_excelImport()"><?php _e('Import'); ?></a>
    <a id="ordensalida-excel-cancelBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls: 'icon-cancel'" onclick="$('#ordensalida-excel-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
</div>

<!--  dialogo de ajuste manual del orden de equipos en competiciones modalidad eq3 -->
<div id="ordeneq3-dialog" style="width:700px;height:300px;padding:5px">
    <!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
    <table id="ordeneq3-datagrid" style="padding:5px"></table>
</div>

<!-- Dialogo del ajuste de orden de salida de los perros seleccionados -->
<div id="ordensalida-reorder-dialog" style="width:640px;height:200px;padding:5px">
        <!-- tabla de reajuste de orden de salida -->
        <table id="ordensalida-reorder-datagrid" style="padding:5px"></table>
</div>
<!-- botones de aceptar/cancelar del ajuste del orden de salida de perros seleccionados -->
<div id="ordensalida-reorder-buttons">
    <a id="ordensalida-reorder-okBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls: 'icon-ok'" onclick="ordensalida_reorder()"><?php _e('Reorder'); ?></a>
    <a id="ordensalida-reorder-cancelBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls: 'icon-cancel'" onclick="$('#ordensalida-reorder-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
</div>

<!--  dialogo de orden de salida e introduccion de datos de una competicion -->
<div id="ordensalida-dialog" style="width:925px;height:500px;padding:5px">
    <!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
    <table id="ordensalida-datagrid" style="padding:5px"></table>
</div>
<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
<div id="ordensalida-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
        <label for="ordensalida-selection"><?php _e("Method");?></label>
        <input id="ordensalida-selection"/>
	   	<a id="ordensalida-sortBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-db_restore'"
           onclick="evalOrdenSalida($('#ordensalida-selection').combobox('getValue'))"><?php _e('Sort'); ?></a>
        <a id="ordensalida-eqBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-huella'" onclick="$('#ordeneq3-dialog').dialog('open');"><?php _e('Teams'); ?></a>
        <label for="ordensalida-categoria"><?php _e('Cat');?></label>
        <select id="ordensalida-categoria" style="width:80px"></select>
        <span style="">
            <!-- need revision. in the meanwhile, hide -->
            <label for="ordensalida-rango"><?php _e('Range');?></label>
            <input id="ordensalida-rango" style="width:80px" value="0-99999"/>
        </span>
   	</span>
   	<span style="float:right;padding:5px">
	   	<a id="ordensalida-checkBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-help'" onclick="reloadAndCheck()"><?php _e('Check'); ?></a>
   		<!-- estos elementos deben estar alineados a la derecha -->
   		<a id="ordensalida-reloadBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-reload'" onclick="reloadOrdenSalida();"><?php _e('Refresh'); ?></a>
   		<a id="ordensalida-printBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-print'" onclick="print_commonDesarrollo(1,'#ordensalida-categoria');"><?php _e('Print'); ?></a>
	</span>
</div>

<script type="text/javascript">
$('#ordensalida-reorder-dialog').dialog({
    title:' <?php _e('Reorder selected dog(s)'); ?>',
    closed:true,
    modal:true,
    buttons:'#ordensalida-reorder-buttons',
    iconCls:'icon-order'
});

$('#ordensalida-reorder-datagrid').datagrid({
    loopcount: 0, // added by me
    // propiedades del panel asociado
    fit: true,
    border: false,
    closable: false,
    collapsible: false,
    collapsed: false,
    // propiedades del datagrid
    method: '', // no method: local load
    url: '', // mo url: local load
    queryParams: {}, // no params: local load
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    // toolbar: '#ordensalida-toolbar',
    columns:[[
        { field:'Perro',		hidden:true },
        { field:'Equipo',		hidden:true },
        { field:'Current',		hidden:true },
        { field:'Orden',	    width:15, sortable:false,	align:'right',  title: '<?php _e('Order'); ?>',formatter:formatReorder },
        { field:'Dorsal',		width:15, sortable:false,	align:'center',  title: '<?php _e('Dorsal'); ?>'},
        { field:'Nombre',		width:25, sortable:false,	align:'left',  title: '<?php _e('Name'); ?>',formatter:formatBold},
        { field:'Licencia',		hidden: true },
        { field:'Categoria',	width:10, sortable:false,   align:'center', title: '<?php _e('Cat'); ?>.' ,formatter:formatCategoria},
        { field:'NombreGuia',	width:50, sortable:false,   align:'right', title: '<?php _e('Handler'); ?>' },
        { field:'NombreClub',	width:30, sortable:false,   align:'right', title: '<?php _e('Club'); ?>' },
        { field:'Celo',		    hidden:true }
    ]]

});

$('#ordensalida-excel-dialog').dialog( {
    title:' <?php _e('Import starting order for this round from Excel file'); ?>',
    closed:true,
    modal:true,
    buttons:'#ordensalida-excel-buttons',
    iconCls:'icon-table',
    onOpen: function() {
        ac_import.type='resultados';
        $('#import-excel-progressbar').progressbar('setValue',"");
        $('#import-excelRoundInfo').html(workingData.nombrePrueba + ' -- ' + workingData.nombreJornada + ' -- ' + workingData.nombreManga);
    },
    onClose: function() { ac_import.progress_status='paused'; }
} );

$('#ordeneq3-dialog').dialog({
    title:         '<?php _e( "Manual setting of teams order for this round"); ?>',
    collapsible:	false,
    minimizable:	false,
    maximizable:	false,
    resizable:		true,
    iconCls:		'icon-huella',
    closed:			true,
    modal:			true,
    onBeforeOpen:   reloadSimpleOrdenEquipos
});

$('#ordensalida-selection').combobox({
    width:125,
    panelWidth:300,
    panelHeight: 'auto',
    textField:'text',
    valueField:'value',
    showItemIcon: true,
    editable: false,
    label: 'Perform Action:',
    labelPosition: 'top',
    data: [
        {value:'random',text:"<?php _e('Random order'); ?>",icon:"dice.png"},
        {value:'results',text:"<?php _e('Reverse results'); ?>",icon:"endflag.png",selected:true},
        {value:'clone',text:"<?php _e('Same order'); ?>",icon:"downdown.png"},
        {value:'reverse',text:"<?php _e('Reverse order'); ?>",icon:"updown.png"},
        {value:'alpha',text:"<?php _e('Alphabetical (handlers)'); ?>",icon:"abc.png"},
        {value:'dorsal',text:"<?php _e('Order by dorsal'); ?>",icon:"order.png"},
        {value:'excel',text:"<?php _e('Import from Excel'); ?>",icon:"table.png"}
    ],
    formatter: function(row) { // add an icon to each combobox option
        var imgfile="../lib/jquery-easyui-1.4.2/themes/icons/"+row.icon;
        return '<img class="item-img" src="'+imgfile+'"/>&nbsp;<span class="item-text">'+row.text+'</span>';
    }
});

$('#ordensalida-categoria').combobox({
    width:80,
    value: '-',
    panelWidth: '110',
    panelHeight: 'auto',
    valueField: 'Categoria',
    textField: 'Nombre',
    data: getOrdenSalidaCategorias(),
    onSelect: function(rec) { reloadOrdenSalida(); }
});

$('#ordensalida-rango').textbox({
    width:80,
    required:false,
    validType:'intrange' // created by me at easyui-patches
});

// datos de la tabla de equipos
$('#ordeneq3-datagrid').datagrid({
    // propiedades del panel asociado
    fit: true,
    url: '../ajax/database/ordenSalidaFunctions.php',
    queryParams: { Operation:'getTeams', Prueba:workingData.prueba, Jornada:workingData.jornada, Manga:workingData.manga, where:''	},
    loadMsg: '<?php _e('Updating teams starting order');?> ...',
    method: 'get',
    mode: 'remote',
    multiSort: false,
    remoteSort: false,
    idField: 'ID',
    columns: [[
        { field:'ID',			hidden:true },
        { field:'Prueba',		hidden:true },
        { field:'Jornada',		hidden:true },
        // { field:'Orden',		hidden:true },
        { field:'Nombre',		width:20, title: '<?php _e('Name');?>',formatter:formatBold },
        { field:'Categorias',	width:10, title: '<?php _e('Cat');?>.' },
        { field:'Observaciones',width:65, title: '<?php _e('Comments');?>'},
        { field:'Miembros',		hidden:true },
        { field:'DefaultTeam',	width:5, align: 'center', title: '<?php _e('Def'); ?>', formatter:formatOk }
    ]],
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    view: scrollview,
    pageSize: 25,
    rowStyler: myRowStyler, // function that personalize colors on alternate rows
    onBeforeLoad: function (params) {return (workingData.manga>0) }, // do not load if no manga selected
    onLoadSuccess: function(data) { $(this).datagrid('enableDnd'); },
    onDragEnter: function(dst,src) {
        return (parseInt(dst.DefaultTeam)!==1 && parseInt(src.DefaultTeam)!==1); // allow dnd if not from/to default team
    },
    onDrop: function(dst,src,updown) {
        dragAndDropOrdenEquipos(src.ID,dst.ID,(updown==='top')?0:1,reloadSimpleOrdenEquipos);
    }
});

$('#ordensalida-dialog').dialog({
	collapsible:	false,
	minimizable:	false,
	maximizable:	false,
	resizable:		true,
	iconCls:		'icon-order',
	closed:			true,
	modal:			true
});

$('#ordensalida-datagrid').datagrid({
    clipboard: null, // added by me to handle cut&paste
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: '../ajax/database/ordenSalidaFunctions.php',
    queryParams: {
        Operation: 'getData',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Manga: workingData.manga,
        Categorias: $('#ordensalida-categoria').combobox('getValue')
    },
    loadMsg: '<?php _e("Updating starting order"); ?> ...',
    toolbar: '#ordensalida-toolbar',
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    // toolbar: '#ordensalida-toolbar',
    columns:[[
		{ field:'Perro',		hidden:true },
		{ field:'Equipo',		hidden:true },
		{ field:'NombreEquipo',	width:35, sortable:false,	align:'left',  title: '<?php _e('Team'); ?>'},
		{ field:'Dorsal',		width:15, sortable:false,	align:'center',  title: '<?php _e('Dorsal'); ?>'},
		{ field:'Nombre',		width:25, sortable:false,	align:'left',  title: '<?php _e('Name'); ?>',formatter:formatBold},
		{ field:'Licencia',		width:15, sortable:false,   align:'right', title: '<?php _e('License'); ?>' },
		{ field:'Categoria',	width:10, sortable:false,   align:'center', title: '<?php _e('Cat'); ?>.' ,formatter:formatCategoria},
		{ field:'NombreGuia',	width:50, sortable:false,   align:'right', title: '<?php _e('Handler'); ?>' },
		{ field:'NombreClub',	width:30, sortable:false,   align:'right', title: '<?php _e('Club'); ?>' },
		{ field:'Celo',		    width:10, sortable:false, formatter: formatCelo,	align:'center', title:'<?php _e('Heat'); ?>'}
    ]],
    rowStyler:function(index,row) { // colorize rows. Mark handlers with multiple dogs
        if (row.PerrosPorGuia>1) {
            return ((index&0x01)===0)?'background-color:#cbc;':'background-color:#ede;';
        } else {
            return ((index&0x01)===0)?'background-color:#ccc;':'background-color:#eee;';
        }
    },
    onBeforeLoad: function(param) { return (workingData.manga > 0); }, // do not load if no manga selected
    onLoadSuccess: function(data) {
		// show/hide 'NombreEquipo' based of manga type
        if (isJornadaKO() &&  $('#ordensalida-dialog').dialog('options').closed ) {
            if (parseInt(data.total)===0) {
                $.messager.alert(
                    "<?php _e('No data yet');?>",
                    "<?php _e('Need to choose starting order to show valid data on this round');?>","warning");
            }
            if ( parseInt(workingData.datosManga.Tipo)===15) { // on first round
                $.messager.alert(
                    "<?php _e('On first round');?>",
                    "<?php _e('Make sure to select competitors and starting order to organize rounds');?>","warning");
            }
        }
		if (isTeam(workingData.datosManga.Tipo) ) $(this).datagrid('showColumn','NombreEquipo');
		else  $(this).datagrid('hideColumn','NombreEquipo');
        // get focus on datagrid (to bind keystrokes) and enable drag and drop
    	$(this).datagrid('enableDnd');
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
        $(this).datagrid('options').clipboard=null; // reset clipboard
        $('#ordensalida-pasteBtn').linkbutton('disable');
    },
    onDblClickRow: function(index,row) {
        // copyPasteOrdenSalida2({'Row':row,'Index':index});
        selectDogsToReorder(index,row);
    },
    onDragEnter: function(dst,src) {
        var from=":"+src.Equipo+":"+src.Categoria+":"+src.Celo+":";
        var to=":"+dst.Equipo+":"+dst.Categoria+":"+dst.Celo+":";
        if (isJornadaEqConjunta()) {
            // en jornadas por equipos conjunta, no hay que tener en cuenta ni categoria ni celo
            from=":"+src.Equipo+":";
            to=":"+dst.Equipo+":";
        }
		return (from === to);
    },
    onDrop: function(dst,src,updown) {
        dragAndDropOrdenSalida(src.Perro,dst.Perro,(updown==='top')?0:1,reloadOrdenSalida);
    }
});

//activa teclas up/down para navegar por el panel , esc para cerrar y ctrl+shift+enter para ver fila
addSimpleKeyHandler('#ordensalida-datagrid','#ordensalida-dialog');

// botones
addTooltip($('#ordensalida-sortBtn').linkbutton(),'<?php _e("Re-evaluate starting order according sort method"); ?>');
addTooltip($('#ordensalida-checkBtn').linkbutton(),'<?php _e("Look for <em>proximity alerts</em>"); ?>'+" <br/>"+'<?php _e("with dogs belonging same handler"); ?>');
addTooltip($('#ordensalida-eqBtn').linkbutton(),'<?php _e("Manual setting of teams starting order"); ?>');
addTooltip($('#ordensalida-printBtn').linkbutton(),'<?php _e("Print starting order"); ?>');
addTooltip($('#ordensalida-reloadBtn').linkbutton(),'<?php _e("Reload starting order. Update window"); ?>');
addTooltip($('#ordensalida-categoria').combobox('textbox'),'<?php _e("Select categories to work with"); ?>');
addTooltip($('#ordensalida-excel-okBtn').linkbutton(),'<?php _e("Import round result data from selected Excel file"); ?>');
addTooltip($('#ordensalida-excel-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
addTooltip($('#ordensalida-reorder-okBtn').linkbutton(),'<?php _e("Move dogs to new provided starting order"); ?>');
addTooltip($('#ordensalida-reorder-cancelBtn').linkbutton(),'<?php _e("Cancel re-ordering. Close window"); ?>');

</script>