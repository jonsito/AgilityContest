<?php
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Public::inscripciones_equipos");
if ( ! $am->allowed(ENABLE_PUBLIC)) { include_once("unregistered.php"); return 0;}
?>

<!--
pb_inscripciones_eq3.inc

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<!-- Presentacion de las inscripciones de la jornada -->
<div id="pb_inscripciones-panel">
	<div id="pb_inscripciones-layout" style="width:100%">
		<div id="pb_inscripciones-Cabecera" data-options="region:'north',split:false" style="height:10%;" class="pb_floatingheader">
            <a id="pb_back-link" class="easyui-linkbutton" onClick="pbmenu_expandMenu(true);" href="#" style="float:left">
                <img id="pb_back-logo" src="/agility/images/backtomenu.png" width="50" />
            </a>&nbsp;
            <a id="pb_header-link" class="easyui-linkbutton" onClick="pb_updateInscripciones_eq3();" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/agilitycontest.png" width="50" />
            </a>
		    <span style="float:left;padding:10px" id="pb_header-infocabecera"><?php _e('Header'); ?></span>
			<span style="float:right;" id="pb_header-texto"><?php _e('Inscription list'); ?></span>
		</div>
		<div id="pb_inscripciones-data" data-options="region:'center'"  class="scores_table">
			<table id="pb_inscripciones_eq3-datagrid"></table>
		</div>
        <div id="pb_inscripciones-footer" data-options="region:'south',split:false" style="height:10%;" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
	</div>
</div> <!-- pb_inscripciones-window -->

<script type="text/javascript">

addTooltip($('#pb_header-link').linkbutton(),'<?php _e("Update inscription list"); ?>');
addTooltip($('#pb_back-link').linkbutton(),'<?php _e("Back to contest menu"); ?>');
$('#pb_inscripciones-layout').layout({fit:true});
$('#pb_inscripciones-panel').panel({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:false,
	callback: null,
	onOpen: function() {
        // generate header
        pb_getHeaderInfo();
        // generate footer
        pb_setFooterInfo();
	}
});

// datos de la tabla de equipos
$('#pb_inscripciones_eq3-datagrid').datagrid({
    fit: true,
    url: '/agility/server/database/equiposFunctions.php',
    queryParams: {
        Operation:'select',
        Prueba:workingData.prueba,
        Jornada:workingData.jornada,
        where:'',
        HideDefault:1, // do not show default team
        AddLogo:1 // generate LogoTeam
    },
    loadMsg: "<?php _e('Updating inscriptions');?> ...",
    method: 'get',
    mode: 'remote',
    multiSort: true,
    remoteSort: true,
    idField: 'ID',
    columns: [[
        { field:'ID',			hidden:true },
        { field:'Prueba',		hidden:true },
        { field:'Jornada',		hidden:true },
        { field:'Orden',		hidden:true /*, width:20, sortable:false,	title: '', formatter:formatTeamLogos */},
        { field:'LogoTeam',     width:20, title:'', formatter:formatLogo},
        { field:'Nombre',		width:20, sortable:true,	title: '<?php _e('Team');?>' },
        { field:'Categorias',	width:10, sortable:true,	title: '<?php _e('Cat');?>.' },
        { field:'Observaciones',width:65, sortable:true,	title: '<?php _e('Comments');?>'},
        { field:'Miembros',		hidden:true },
        { field:'DefaultTeam',	width:5, sortable:false,	align: 'center', title: 'Def', formatter:formatOk }
    ]],
    pagination: false,
    fitColumns: true,
    singleSelect: true,
    view: scrollview,
    pageSize: 50,
    rowStyler:pbRowStyler,
    autoRowHeight:true,
    // especificamos un formateador especial para desplegar la tabla de inscritos por equipo
    detailFormatter:function(idx,row){
        return '<div style="padding:2px"><table id="pb_inscripciones_eq3-datagrid-' + replaceAll(' ','_',row.ID) + '"></table></div>';
    },
    onExpandRow: function(idx,row) {
        showInscripcionesByTeam(idx,row);
    },
    onLoadSuccess: function(data) {
        /*
        function fireUp(index) {
            setTimeout(function() {dg.datagrid('expandRow',index);},1000*index);
        }
        var dg = $('#pb_inscripciones_eq3-datagrid');
        var count = dg.datagrid('getRows').length;
        for(var i=0; i<count; i++) fireUp(i);
        */
    }
});

//mostrar las inscripciones agrupadas por equipos
function showInscripcionesByTeam(index,team){
    // - sub tabla de participantes asignados a un equipo
    var mySelf='#pb_inscripciones_eq3-datagrid-'+replaceAll(' ','_',team.ID);
    $(mySelf).datagrid({
        width: '100%',
        height: 'auto',
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        singleSelect: true,
        loadMsg: '<?php _e('Updating inscriptions');?> ...',
        url: '/agility/server/database/inscripcionFunctions.php',
        queryParams: { Operation: 'inscritosbyteam', Prueba:workingData.prueba, Jornada:workingData.jornada, Equipo: team.ID },
        method: 'get',
        autorowheight:true,
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
            { field:'Dorsal',	    width:'5%',        sortable:false, align: 'center',	title: '<?php _e('Dorsal'); ?>',formatter:formatDorsal },
            { field:'LogoClub',     width:'7%',        sortable:false, align: 'center',	title: '',formatter:formatLogo },
            { field:'Nombre',	    width:'15%',       sortable:false, align: 'left',	title: '<?php _e('Name'); ?>',formatter:formatDogName },
            { field:'Licencia',	    width:'7%',        sortable:false, align: 'center',title: '<?php _e('Lic');    ?>' },
            { field:'Raza',	        width:'15%',        sortable:false, align: 'center',title: '<?php _e('Breed');    ?>' },
            { field:'Categoria',    width:'5%',        sortable:false, align: 'center',title: '<?php _e('Cat');    ?>',formatter:formatCatGrad },
            // { field:'Grado',	width:6,        sortable:false, align: 'center',title: '<?php _e('Grade');  ?>',formatter:formatGrado },
            { field:'NombreGuia',	width:'18%',   sortable:false, align: 'right',	title: '<?php _e('Handler'); ?>' },
            { field:'NombreClub',	width:'16%',   sortable:false, align: 'right',	title: clubOrCountry() },
            { field:'NombreEquipo',	hidden:true },
            { field:'Celo',		    width:'5%', align:'center', formatter: formatCelo,	title: '<?php _e('Heat');   ?>' },
            { field:'Observaciones',width:'7%',                                   title: '<?php _e('Comments');?>' }
        ]],
        // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
        rowStyler:pbRowStyler2,
        // on double click fireup editor dialog
        onResize:function(){
            $('#pb_inscripciones_eq3-datagrid').datagrid('fixDetailRowHeight',index);
        },
        onLoadSuccess:function(){
            // on required contests hide license, and enlarge name to allow pedigree name
            if (useLongNames()) {
                $(this).datagrid('hideColumn','Licencia');
                $(this).datagrid('autoSizeColumn','Nombre');
            }
            $(this).datagrid('fitColumns'); // expand to max width
            setTimeout(function(){
                $('#pb_inscripciones_eq3-datagrid').datagrid('fixDetailRowHeight',index);
            },0);
        }
    }); // end of inscritos-by-team_team_id
    $('#pb_inscripciones_eq3-datagrid').datagrid('fixDetailRowHeight',index);
} // end of showPerrosByTeam

</script>