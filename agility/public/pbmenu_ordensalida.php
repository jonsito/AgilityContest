<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Public::ordensalida");
if ( ! $am->allowed(ENABLE_PUBLIC)) { include_once("unregistered.php"); return 0;}
?>
<!--
pb_ordensalida.inc

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<!-- Presentacion del orden de salida de la jornada -->
<div id="pb_ordensalida-panel">
	<div id="pb_ordensalida-layout" style="width:100%">
		<div id="pb_ordensalida-Cabecera" style="height:10%;" class="pb_floatingheader"
             data-options="region:'north',split:false,collapsed:false">
            <a id="pb_header-link" class="easyui-linkbutton" onClick="pb_updateOrdenSalida2(workingData.tanda);" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/agilitycontest.png" width="50" />
            </a>
		    <span style="float:left;padding:10px;" id="pb_header-infocabecera"><?php _e('Header'); ?></span>
			<span style="float:right;" id="pb_header-texto">
                <?php _e('Starting order'); ?><br />
                <span id="pb_enumerateMangas" style="width:200px" >Nombre Manga</span>
            </span>
		</div>
		<div id="team_table" data-options="region:'center'">
			<table id="pb_ordensalida-datagrid"></table>
		</div>
        <div id="pb_ordensalida-footer" data-options="region:'south',split:false" style="height:10%;" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
	</div>
</div> <!-- pb_ordensalida-window -->

<script type="text/javascript">

// in a mobile device, increase north window height
if (isMobileDevice()) {
    $('#pb_ordensalida-Cabecera').css('height','90%');
}

addTooltip($('#pb_header-link').linkbutton(),'<?php _e("Update starting order"); ?>');
$('#pb_ordensalida-layout').layout({fit:true});

$('#pb_ordensalida-panel').panel({
    fit:true,
    noheader:true,
    border:false,
    closable:false,
    collapsible:false,
    collapsed:false,
    resizable:true,
    callback: null,
    // 1 minute poll is enouth for this, as no expected changes during a session
    onOpen: function() {
        // update header
        pb_getHeaderInfo();
        // update footer
        pb_setFooterInfo();
    }
});

$('#pb_ordensalida-datagrid').datagrid({
    method: 'get',
    url: '/agility/server/database/tandasFunctions.php',
    queryParams: {
        Operation: 'getDataByTanda',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 1, // defaults to "-- sin asignar --"
        ID: workingData.tanda // Tanda 0 defaults to every tandas
    },
    loadMsg: "<?php _e('Updating starting order');?> ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: true,
    nowrap: false,
    width: '100%',
    height: 'auto',
    columns:[[
        { field:'Prueba',		width:0, hidden:true }, // extra field to be used on form load/save
        { field:'Jornada',		width:0, hidden:true }, // extra field to be used on form load/save
        { field:'Manga',		width:0, hidden:true },
        { field:'Tanda',		width:0, hidden:true }, // string with tanda's name
        { field:'ID',			width:0, hidden:true }, // tanda ID
        { field:'Perro',     	width:'5%', align:'center',	title: '#',formatter: formatOrdenSalida },
        { field:'Pendiente',	width:0, hidden:true },
        { field:'Tanda',		width:0, hidden:true },
        { field:'Equipo',		width:0, hidden:true },
        { field:'LogoClub',     width:'5%', align:'center',	title: '',formatter: formatLogoPublic },
        { field:'NombreEquipo',	width:'12%', align:'center',title: '<?php _e('Team'); ?>',hidden:true},
        { field:'Dorsal',		width:'5%', align:'center',	title: '<?php _e('Dorsal'); ?>', styler:checkPending },
        { field:'Nombre',		width:'15%', align:'center',title: '<?php _e('Name'); ?>',formatter: formatBoldBig},
        { field:'Raza',         width:'10%', align:'center',title: '<?php _e('Breed'); ?>' },
        { field:'Licencia',		width:'5%', align:'center',	title: '<?php _e('License'); ?>'},
        { field:'NombreGuia',	width:'23%', align:'right',	title: '<?php _e('Handler'); ?>' },
        { field:'NombreClub',	width:'19%', align:'right',	title: '<?php _e('Club'); ?>' },
        { field:'Categoria',	width:'4%', align:'center',	title: '<?php _e('Cat'); ?>.',formatter:formatCategoria },
        { field:'Grado',		width:'4%', align:'center',	title: '<?php _e('Grade'); ?>', formatter:formatGrado },
        { field:'Celo',			width:'4%', align:'center',	title: '<?php _e('Heat'); ?>', formatter:formatCelo },
        { field:'Observaciones',width:0, hidden:true }
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:myRowStyler,
    onBeforeLoad:function(param) {
        if (workingData.tanda==0) return false; // do not try to load if not variable initialized
        return true;
    },
    onLoadSuccess:function(){
        var mySelf=$('#pb_ordensalida-datagrid');
        // show/hide team name
        if (isTeamByJornada(workingData.datosJornada) ) {
            mySelf.datagrid('showColumn','NombreEquipo');
            mySelf.datagrid('hideColumn','Observaciones');
        } else  {
            mySelf.datagrid('hideColumn','NombreEquipo');
            mySelf.datagrid('showColumn','Observaciones');
        }
        mySelf.datagrid('fitColumns'); // expand to max width
    }
});

// fire autorefresh if configured
setTimeout(function(){ $('#pb_enumerateMangas').text(workingData.nombreTanda)},0);
var rtime=parseInt(ac_config.web_refreshtime);
if (rtime!=0) {
    
    function update() {
        pb_updateOrdenSalida2(workingData.tanda);
        workingData.timeout=setTimeout(update,1000*rtime);
    }
    
    if (workingData.timeout!=null) clearTimeout(workingData.timeout);
    update();
}

</script>