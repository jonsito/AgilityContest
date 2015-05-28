<?php
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Public::inscripciones");
if ( ! $am->allowed(ENABLE_PUBLIC)) die("<h1>Public access is not allowed for current license</h1>");
// tool to perform automatic upgrades in database when needed
require_once(__DIR__."/../server/upgradeVersion.php");
?>
?>

<!--
pb_inscripciones.inc

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

<!-- Presentacion de las inscripciones de la jornada -->
<div id="pb_inscripciones-window">
	<div id="pb_inscripciones-layout" style="width:100%">
		<div id="pb_inscripciones-Cabecera" data-options="region:'north',split:false" style="height:80px" class="pb_floatingheader">
            <a id="pb_header-link" class="easyui-linkbutton" onClick="pb_updateInscripciones();" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/rsce.png" width="50" />
            </a>
		    <span style="float:left;padding:10px" id="pb_header-infocabecera">Cabecera</span>
			<span style="float:right;" id="pb_header-texto">Listado de inscritos</span>
		</div>
		<div id="pb_inscripciones-data" data-options="region:'center'" >
			<span id="pb_inscripciones-datagrid"></span>
		</div>
        <div id="pb_inscripciones-footer" data-options="region:'south',split:false" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
	</div>
</div> <!-- pb_inscripciones-window -->

<script type="text/javascript">
addTooltip($('#pb_header-link').linkbutton(),"Actualizar listado de inscritos");
$('#pb_inscripciones-layout').layout({fit:true});
$('#pb_inscripciones-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:false,
	callback: null, 
	// 1 minute poll is enouth for this, as no expected changes during a session
	onOpen: function() {
        // generate header
        pb_getHeaderInfo();
        // generate footer
        pb_setFooterInfo();
	}
});

$('#pb_inscripciones-datagrid').datagrid({
    width: '100%',
    height: 'auto',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    loadMsg: '<?php _e('Leyendo inscripciones....');?>',
    url: '/agility/server/database/inscripcionFunctions.php',
    queryParams: { Operation: 'inscritosbyjornada', Prueba:workingData.prueba, Jornada:workingData.jornada },
    method: 'get',
    autorowheight:true,
    columns: [[
        { field:'ID',		hidden:true }, // inscripcion ID
        { field:'Prueba',	hidden:true }, // prueba ID
        { field:'Jornadas',	hidden:true }, // bitmask de jornadas inscritas
        { field:'Perro',	hidden:true }, // dog ID
        { field:'Equipo',	hidden:true }, // only used on Team contests
        { field:'Guia', 	hidden:true }, // Guia ID
        { field:'Club',		hidden:true }, // Club ID
        { field:'LOE_RRC',	hidden:true }, // LOE/RRC
        { field:'Club',		hidden:true }, // Club ID
        { field:'Dorsal',	    width:'5%',   sortable:false, align: 'center',	title: '<?php _e('Dorsal'); ?>',formatter:formatDorsal },
        { field:'LogoClub',	    width:'5%',    sortable:false, align: 'center',	title: '',formatter:formatLogo },
        { field:'Nombre',	    width:'15%',   sortable:false, align: 'right',	title: '<?php _e('Nombre'); ?>' },
        { field:'Licencia',	    width:'10%',   sortable:false, align: 'center',title: '<?php _e('Lic');    ?>' },
        { field:'Categoria',    width:'5%',    sortable:false, align: 'center',title: '<?php _e('Cat');    ?>' },
        { field:'Grado',	    width:'5%',    sortable:false, align: 'center',title: '<?php _e('Grado');  ?>' },
        { field:'NombreGuia',	width:'20%',   sortable:false, align: 'right',	title: '<?php _e('Gu&iacute;a'); ?>' },
        { field:'NombreClub',	width:'18%',   sortable:false, align: 'right',	title: '<?php _e('Club');   ?>' },
        { field:'NombreEquipo',	hidden:true },
        { field:'Observaciones',width:'10%',                                   title: '<?php _e('Observaciones');?>' },
        { field:'Celo',		    width:'5%', align:'center', formatter: formatCelo,	title: '<?php _e('Celo');   ?>' }
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:myRowStyler
});

</script>