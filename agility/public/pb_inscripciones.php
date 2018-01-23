<?php
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Public::inscripciones");
if ( ! $am->allowed(ENABLE_PUBLIC)) { include_once("unregistered.php"); return 0; }
?>

<!--
pb_inscripciones.inc

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

<!-- Presentacion de las inscripciones de la jornada -->
<div id="pb_inscripciones-window">
	<div id="pb_inscripciones-layout" style="width:100%">
		<div id="pb_inscripciones-Cabecera" data-options="region:'north',split:false" style="height:10%;" class="pb_floatingheader">
            <a id="pb_header-link" class="easyui-linkbutton" onClick="pb_updateInscripciones();" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/agilitycontest.png" width="50" />
            </a>
		    <span style="float:left;padding:10px" id="pb_header-infocabecera"><?php _e('Header'); ?></span>
			<span style="float:right;" id="pb_header-texto"><?php _e('Inscription list'); ?></span>
		</div>
		<div id="pb_inscripciones-data" data-options="region:'center'" >
			<span id="pb_inscripciones-datagrid"></span>
		</div>
        <div id="pb_inscripciones-footer" data-options="region:'south',split:false" style="height:10%;" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
	</div>
</div> <!-- pb_inscripciones-window -->

<script type="text/javascript">

// fire autorefresh if configured
// var rtime=parseInt(ac_config.web_refreshtime);
// if (rtime!=0) setInterval(pb_updateInscripciones,1000*rtime);

addTooltip($('#pb_header-link').linkbutton(),'<?php _e("Update inscription list"); ?>');
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
	},
    onClose: function() {
        // do not auto-refresh in inscriptions
        // clearInterval($(this).window.defaults.callback);
    }
});

$('#pb_inscripciones-datagrid').datagrid({
    configured: false, // added by me
    width: '100%',
    height: '100%',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    loadMsg: '<?php _e('Updating inscriptions');?> ...',
    url: '/agility/server/database/inscripcionFunctions.php',
    queryParams: { Operation: 'inscritosbyjornada', Prueba:workingData.prueba, Jornada:workingData.jornada },
    method: 'get',
    autorowheight:true,
    view: scrollview,
    pageSize: 25,
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
        { field:'LogoClub',	    width:'5%',    sortable:false, align: 'center',	title: '',formatter:formatLogo },
        { field:'Dorsal',	    width:'5%',   sortable:false, align: 'center',	title: '<?php _e('Dorsal'); ?>',formatter:formatDorsal },
        { field:'Nombre',	    width:'15%',   sortable:false, align: 'left',	title: '<?php _e('Name'); ?>',formatter:formatDogName },
        { field:'Licencia',	    width:'9%',   sortable:false, align: 'center', title: '<?php _e('Lic');    ?>' },
        { field:'Raza',         width:'11%',   sortable:false, align: 'right',  title: '<?php _e('Breed');   ?>' },
        { field:'Categoria',    width:'6%',    sortable:false, align: 'center', title: '<?php _e('Cat');    ?>',formatter:formatCatGrad },
        // { field:'Grado',	    width:'5',    sortable:false, align: 'center', title: '<?php _e('Grade');  ?>', formatter:formatGrado },
        { field:'NombreGuia',	width:'19%',   sortable:false, align: 'right',	title: '<?php _e('Handler'); ?>' },
        { field:'NombreClub',	width:'15%',   sortable:false, align: 'right',	title: clubOrCountry() },
        { field:'NombreEquipo',	hidden:true },
        { field:'Celo',		    width:'5%', align:'center', formatter: formatCelo,	title: '<?php _e('Heat');   ?>' },
        { field:'Observaciones',width:'10%',   sortable:false, align: 'right',  title: '<?php _e('Comments');   ?>' }
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:pbRowStyler,
    onBeforeLoad: function(data){
        var done=$(this).datagrid('options').configured;
        if (!done) {
            inscripciones_configureScreenLayout( $(this) );
            $(this).datagrid('options').configured=true;
        }
        return true;
    },
    onLoadSuccess: function(data) {
        $(this).datagrid('autoSizeColumn','Nombre');
        $(this).datagrid('fitColumns');
    }
});

</script>
