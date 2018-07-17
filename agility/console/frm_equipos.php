<!-- 
frm_equipos.php

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
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>
 
<!-- ventana de presentacion de los datos de los equipos de una prueba -->
<div id="team_datagrid-dialog" style="width:800px;height:480px">
	<!-- DECLARACION DE LA TABLA DE EQUIPOS -->
	<table id="team_datagrid"></table>
</div>	

<!-- BARRA DE TAREAS DE LA TABLA DE Equipos -->
<div id="team_datagrid-toolbar" style="width:100%;display:inline-block">
   	<span style="float:left;padding:5px">
   		<a id="team_datagrid-newBtn" href="#" class="easyui-linkbutton"	data-options="iconCls:'icon-add'"
   			onclick="newTeam('#team_datagrid',$('#team_datagrid-search').val())"><?php _e('New'); ?></a>
   		<a id="team_datagrid-editBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-edit'"
   			onclick="editTeam('#team_datagrid')"><?php _e('Edit'); ?></a>
   		<a id="team_datagrid-delBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-trash'"
   			onclick="deleteTeam('#team_datagrid')"><?php _e('Delete'); ?></a>
        <a id="team_datagrid-delMembersBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-users'"
            onclick="deleteTeamMembers('#team_datagrid')"><?php _e('Un-inscribe'); ?></a>
   		<input id="team_datagrid-search" type="text" value="<?php _e('-- Search --'); ?>" class="search_textfield"
			   onfocus="handleSearchBox(this,true);" onblur="handleSearchBox(this,false);"/>
   		<a id="team_datagrid-reloadBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-brush'"
           onclick="reloadWithSearch('#team_datagrid','select',true);"><?php _e('Clear'); ?></a>
   	</span>
   	<span style="float:right;padding:5px">
		<a id="team_datagrid-checkBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-help'"
			onclick="checkTeams('#team_datagrid')"><?php _e('Verify');?></a>
		<a id="team_datagrid-printBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-print'"
			onclick="printTeams('#team_datagrid')"><?php _e('Print');?></a>
   	</span>
</div>

<!--  Botones de la tabla de equipos (solo el boton de cerrar) -->
<div id="team_datagrid-buttons" style="width:100%;display:inline-block">
	<span style="float:right;padding:5px">
   		<a id="team_datagrid-doneBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-cancel'"
   			onclick="$('#team_datagrid-dialog').dialog('close')"><?php _e('Done'); ?></a>
   	</span>
</div>
 
<!-- FORMULARIO DE ASIGNACION DE EQUIPO A UNA INSCRIPCION -->
<div id="selteam-window" style="position:relative;width:600px;height:auto;padding:5px 5px">
	<div id="selteam-Layout" class="easyui-layout" data-options="fit:true'">
		<div id="selteam-Content" data-options="region:'north',border:'true'">
			<form id="selteam-Form">
				<div class="fitem">
					<input type="hidden" id="selteam-Parent" name="Parent"/> <!-- datagrid padre -->
					<input type="hidden" id="selteam-Operation" name="Operation" value="update_team"/>
					<input type="hidden" id="selteam-Prueba" name="Prueba"/>
					<input type="hidden" id="selteam-Jornada" name="Jornada"/>
                    <input type="hidden" id="selteam-Nombre" name="Nombre"/>
					<input type="hidden" id="selteam-Perro" name="Perro"/>
                    <input type="hidden" id="selteam-LongCategoria" name="LongCategoria"/>
                    <input type="hidden" id="selteam-Categoria" name="Categoria"/>
                    <input type="hidden" id="selteam-NombreGuia" name="NombreGuia"/>
                    <input type="hidden" id="selteam-Guia" name="Guia"/>
                    <table id="selteam-datagrid"></table>
				</div>
				<div class="fitem">
					<label for="selteam-Equipo"><?php _e('Team');?>:</label>
					<select id="selteam-Equipo" name="Equipo" style="width:200px"></select>
					<input type="hidden" id="selteam-ID" name="ID"/><!-- will be rewritten with team-ID on submit -->
				</div>
			</form>
		</div> <!-- contenido -->
		<div data-options="region:'center'"></div>
		<div id="selteam-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
			<a id="selteam-okBtn" href="#" class="easyui-linkbutton" 
				data-options="iconCls:'icon-ok'" onclick="changeTeam()"><?php _e('Accept');?></a>
			<a id="selteam-cancelBtn" href="#" class="easyui-linkbutton" 
				data-options="iconCls:'icon-cancel'" onclick="$('#selteam-window').window('close');"><?php _e('Cancel');?></a>
		</div>	<!-- botones -->
	</div> <!-- Layout -->
</div> <!-- Window -->

<!-- barra de progreso de actualizacion de cambio de equipo -->
<div id="selteam-progresswindow" class="easyui-window"
     data-options="title:'<?php _e('Processing data'); ?>...',width:300,modal:true,collapsable:false,minimizable:false,maximizable:false,closable:false,closed:true">
    <p id="selteam-progresslabel" style="text-align:center"><?php _e('Setting team for'); ?>:</p>
    <div id="selteam-progressbar" class="easyui-progressbar" style="width:300px;text-align:center;" data-options="value:0"></div>
</div>

<script type="text/javascript">

$('#selteam-datagrid').datagrid({
    width: '100%',
    height: 'auto',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    url: null,
    columns: [[
        { field:'ID',		hidden:true }, // inscripcion ID
        { field:'Prueba',	hidden:true }, // prueba ID
        { field:'Jornadas',	hidden:true }, // bitmask de jornadas inscritas
        { field:'Perro',	hidden:true }, // dog ID
        { field:'Equipo',	hidden:true }, // only used on Team contests
        { field:'Pagado', 	hidden:true }, // to store if handler paid :-)
        { field:'Guia', 	hidden:true }, // Guia ID
        { field:'Club',		hidden:true }, // Club ID
        { field:'LOE_RRC',	hidden:true }, // LOE/RRC
        { field:'Club',		hidden:true }, // Club ID
        { field:'Dorsal',	width:6,        sortable:true, align: 'right',	title: '<?php _e('Dorsal'); ?>' },
        { field:'Nombre',	width:15,       sortable:true, align: 'right',	title: '<?php _e('Name'); ?>' },
        { field:'Licencia',	hidden:true },
        { field:'Categoria',width:8,        sortable:false, align: 'center',title: '<?php _e('Cat');    ?>' ,formatter:formatCategoria},
        { field:'Grado',	hidden:true },
        { field:'NombreGuia',	width:25,   sortable:true, align: 'right',	title: '<?php _e('Handler'); ?>' },
        { field:'NombreClub',	width:15,   sortable:true, align: 'right',	title: '<?php _e('Club');   ?>' },
        { field:'NombreEquipo',	hidden:true },
        { field:'Observaciones',hidden:true },
        { field:'Celo',	 hidden:true}
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler: myRowStyler
});

//TODO: estudiar por qué el "closed:true" en el data-options no funciona
$('#team_datagrid-dialog').dialog({
	iconCls:'icon-huella',
	modal:true,
	closable:true,
	collapsible:false,
	collapsed:false,
	minimizable:false,
	maximizable:false,
	resizable:true,
	title:'<?php _e("Team management on contest");?>'+": '"+workingData.datosPrueba.Nombre+"'",
	closed:true,
	onClose: function() {
	    $('#inscripciones-datagrid').datagrid('reload');
	    autoBackupDatabase(1,"");
	}
});

// datos de la tabla de equipos
$('#team_datagrid').datagrid({
	fit: true,
	url: '../ajax/database/equiposFunctions.php',
	queryParams: { Operation:'select', Prueba:workingData.prueba, Jornada:workingData.jornada, where:''	},
	loadMsg: '<?php _e('Updating team list');?> ...',
    footer: '#team_datagrid-buttons',
    toolbar: '#team_datagrid-toolbar',
	method: 'get',
	mode: 'remote',
    multiSort: true,
    remoteSort: true,
    idField: 'ID',
    columns: [[
        { field:'ID',			hidden:true },
        { field:'Prueba',		hidden:true },
		{ field:'Jornada',		hidden:true },
		// { field:'Orden',		hidden:true },
 		{ field:'Nombre',		width:50, sortable:true,	title: '<?php _e('Name');?>',formatter:formatBold },
		{ field:'Categorias',	width:10, sortable:true,	title: '<?php _e('Cat');?>.' },
		{ field:'Observaciones',width:35, sortable:true,	title: '<?php _e('Comments');?>'},
		{ field:'Miembros',		hidden:true },
		{ field:'DefaultTeam',	width:5, sortable:false,	align: 'center', title: 'Def', formatter:formatOk }
    ]],
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    view: detailview, // to allow multiple select in subgrid cannot use scrollview
    // pageSize: 25,
    rowStyler: myRowStyler, // function that personalize colors on alternate rows
	onDblClickRow:function() { editTeam('#team_datagrid'); }, // on double click fireup editor dialog        
	// especificamos un formateador especial para desplegar la tabla de inscritos por equipo
	detailFormatter:function(idx,row){
        var dg="team-inscripcion-datagrid-"+ replaceAll(' ','_',row.ID);
		return '<div style="padding:2px"><table id="' + dg + '"></table></div>';
	},
	onExpandRow: function(idx,row) {
        showInscripcionesByTeam(idx,row);
    },
    onLoadSuccess: function(data){
	    // if only one row, expand it
	    if (parseInt(data.total)===1) setTimeout( function(){
	        $('#team_datagrid').datagrid('expandRow',0)
        },0);
    }
});

// key handler
addKeyHandler('#team_datagrid','#team_datagrid-dialog',newTeam,editTeam,deleteTeam);
// - tooltips
addTooltip($('#team_datagrid-search'),'<?php _e("Show teams matching search criteria");?>');
addTooltip($('#team_datagrid-newBtn').linkbutton(),'<?php _e("Declare a new team on this contest");?>');
addTooltip($('#team_datagrid-editBtn').linkbutton(),'<?php _e("Edit data on selected team");?>');
addTooltip($('#team_datagrid-delBtn').linkbutton(),'<?php _e("Remove team on this contest");?>');
addTooltip($('#team_datagrid-delMembersBtn').linkbutton(),'<?php _e("Un-inscribe members of selected team from current journey");?>');
addTooltip($('#team_datagrid-reloadBtn').linkbutton(),'<?php _e("Clear search box. Update team list");?>');
addTooltip($('#team_datagrid-checkBtn').linkbutton(),"<?php _e('Verify teams. Enumerate found problems');?>");
addTooltip($('#team_datagrid-printBtn').linkbutton(),"<?php _e('Print team list on this journey');?>"+' <br />'+"<?php _e('and their team members');?>");
addTooltip($('#team_datagrid-doneBtn').linkbutton(),'<?php _e("Close windows. Return to previous menu");?>');

//mostrar las inscripciones agrupadas por equipos
function showInscripcionesByTeam(index,team){
	// - sub tabla de participantes asignados a un equipo
	var mySelf='#team-inscripcion-datagrid-'+replaceAll(' ','_',team.ID);
	$(mySelf).datagrid({
		width: '100%',
		height: 'auto',
		title: '<?php _e('Registered inscriptions on team');?>: '+team.Nombre,
		pagination: false,
		rownumbers: false,
		fitColumns: true,
		singleSelect: false,
		loadMsg: '<?php _e('Reading inscription list');?>...',
		url: '../ajax/database/inscripcionFunctions.php',
		queryParams: {
		    Operation: 'inscritosbyteam',
            Prueba:workingData.prueba,
            Jornada:workingData.jornada,
            Equipo: team.ID
		},
		method: 'get',
        mode: 'remote',
        multiSort: 'true', // can sort only "-- Sin asignar --" team
        remoteSort: 'true',
		columns: [[
			{ field:'ID',		hidden:true }, // inscripcion ID
			{ field:'Prueba',	hidden:true }, // prueba ID
			{ field:'Jornadas',	hidden:true }, // bitmask de jornadas inscritas
			{ field:'Perro',	hidden:true }, // dog ID
			{ field:'Equipo',	hidden:true }, // only used on Team contests
			{ field:'Pagado', 	hidden:true }, // to store if handler paid :-)
			{ field:'Guia', 	hidden:true }, // Guia ID
			{ field:'Club',		hidden:true }, // Club ID
			{ field:'LOE_RRC',	hidden:true }, // LOE/RRC
			{ field:'Club',		hidden:true }, // Club ID
			{ field:'Dorsal',	width:6,        sortable:true, align: 'right',	title: '<?php _e('Dorsal'); ?>' },
			{ field:'Nombre',	width:15,       sortable:true, align: 'right',	title: '<?php _e('Name'); ?>' },
			{ field:'Licencia',	width:6,        sortable:true, align: 'center', title: '<?php _e('Lic');    ?>' },
			{ field:'Categoria',width:7,        sortable:false, align: 'center',title: '<?php _e('Cat');    ?>' ,formatter:formatCategoria},
			{ field:'Grado',	width:6,        sortable:false, align: 'center',title: '<?php _e('Grade');  ?>', formatter:formatGrado },
			{ field:'NombreGuia',	width:25,   sortable:true, align: 'right',	title: '<?php _e('Handler'); ?>' },
			{ field:'NombreClub',	width:15,   sortable:true, align: 'right',	title: '<?php _e('Club');   ?>' },
			{ field:'NombreEquipo',	hidden:true },
			{ field:'Observaciones',width:12,                                   title: '<?php _e('Comments');?>' },
			{ field:'Celo',		width:4, align:'center', formatter: formatCelo,	title: '<?php _e('Heat');   ?>' }
 		]],
		// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
		rowStyler: myRowStyler,
		onResize:function(){
			$('#team_datagrid').datagrid('fixDetailRowHeight',index);
		},
		onLoadSuccess:function(){
			setTimeout(function(){
				$('#team_datagrid').datagrid('fixDetailRowHeight',index);
			},0);
		},
        // only allow sorting when default team
        onBeforeSortColumn: function(sort,order) {
            return (team.Nombre==='-- Sin asignar --')?true:false;
        },
        // on double click fireup editor dialog
		onDblClickRow:function(index,row) {
		    $(mySelf).datagrid('selectRow',index); // mark row as selected
		    changeTeamDialog(mySelf,row);
		}
	}); // end of inscritos-by-team_team_id
	
	// addSimpleKeyHandler(mySelf,changeTeamDialog);
	$('#team_datagrid').datagrid('fixDetailRowHeight',index);
} // end of showPerrosByGuia

$('#selteam-window').window({
	title: '<?php _e('Select new team');?>',
	collapsible: false,
	minimizable: false,
	maximizable: false,
	closable: true,
	closed: true,
	shadow: false,
	modal: true
});

addTooltip($('#selteam-okBtn').linkbutton(),"<?php _e('Assign competitor to selected team');?>");
addTooltip($('#selteam-cancelBtn').linkbutton(),"<?php _e('Cancel selection. Close window');?>");

//datos del formulario de asignacion de equipo
//- declaracion del formulario
$('#selteam-Form').form({
	onLoadSuccess: function(data) {
	    // stupid combogrid that doesn't display right data after form load
	    $('#selteam-Equipo').combogrid('clear').combogrid('setValue',data.Equipo);
	}
});

$('#selteam-Equipo').combogrid({
	panelWidth: 450,
	panelHeight: 200,
	idField: 'ID',
	textField: 'Nombre',
	url: '../ajax/database/equiposFunctions.php',
	queryParams: { Operation:'enumerate', Prueba:workingData.prueba, Jornada:workingData.jornada },
	loadMsg: '<?php _e('Updating team list');?>...',
	method: 'get',
	mode: 'remote',
	required: true,
	editable: isMobileDevice()?false:true, //disable keyboard deploy on mobile devices
	columns: [[
		{ field:'ID',			hidden:true },
		{ field:'Prueba',		hidden:true },
		{ field:'Jornada',		hidden:true },
		{ field:'Nombre',		width:30, sortable:true,	title: '<?php _e('Name');?>' },
		{ field:'Categorias',	width:15, sortable:true,	title: '<?php _e('Cat');?>' },
		{ field:'Observaciones',width:50, sortable:true,	title: '<?php _e('Comments');?>'}
	]],
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	selectOnNavigation: false,
    onBeforeLoad:function (params) {
        params.Operation = 'enumerate';
        params.Prueba = workingData.prueba;
        params.Jornada = workingData.jornada;
        return true;
    }
});

</script>
