<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Public::parciales");
if ( ! $am->allowed(ENABLE_PUBLIC)) { include_once("unregistered.php"); return 0;}
?>

<!--
pb_parciales.inc

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

<!-- Presentacion de resultados parciales -->

<div id="pb_parciales-window">
    <div id="pb_parciales-layout" style="width:100%">
        <div id="pb_parciales-Cabecera"  style="height:20%;" class="pb_floatingheader"
             data-options="
                region:'north',
                split:true,
                title:'<?php _e('Partial scores');?>',
                collapsed:false,
                onCollapse:function(){
                	setTimeout(function(){
				    	var top = $('#pb_parciales-layout').layout('panel','expandNorth');
				    	var round = $('#enumerateParciales').combogrid('getText');
					    top.panel('setTitle','<?php _e('Partial scores');?>: '+round);
				    },0);
                }
                ">
            <a id="pb_header-link" class="easyui-linkbutton" onClick="updateParciales(/*empty*/);" href="#" style="float:left">
                <img id="pb_header-logo" src="../images/logos/agilitycontest.png" width="50" />
            </a>
            <span id="header-combinadaFlag" style="display:none">false</span> <!--indicador de combinada-->
            <span style="float:left;padding:10px" id="pb_header-infocabecera"><?php _e('Header'); ?></span>
            <span style="float:right;padding:10px" id="pb_header-texto">
                <?php _e('Partial scores'); ?><br/>
                <label for="enumerateParciales" style="font-size:0.7em;"><?php _e('Round'); ?>:</label>
                <select id="enumerateParciales" style="width:200px"></select>
            </span><br/>
            <!-- Datos de TRS y TRM -->
            <?php include_once(__DIR__."/../lib/templates/parcial_round_data.inc.php");?>
        </div>
        <div id="pb_parciales-data" data-options="region:'center'" >
            <div class="scores_table">
                <?php include_once(__DIR__."/../lib/templates/parcial_individual.inc.php");?>
            </div>
        </div>
        <div id="pb_parciales-footer" data-options="region:'south',split:false" style="height:10%;" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
    </div>
</div> <!-- pb_parciales-window -->

<script type="text/javascript">

// in a mobile device, increase north window height
if (isMobileDevice()) {
    $('#pb_parciales-Cabecera').css('height','90%');
}

addTooltip($('#pb_header-link').linkbutton(),'<?php _e("Update partial scores table"); ?>');
$('#pb_parciales-layout').layout({fit:true});

$('#enumerateParciales').combogrid({
	panelWidth: 300,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '../server/database/jornadaFunctions.php',
	method: 'get',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	editable: false,  // to disable tablet keyboard popup
	selectOnNavigation: true, // let use cursor keys to interactive select
	columns: [[
	   	    {field:'ID',hidden:true},
			{field:'Nombre',title:'<?php _e('Available scores'); ?>',width:50,align:'right'},
			{field:'Prueba',hidden:true},
			{field:'Jornada',hidden:true},
			{field:'Manga',hidden:true},
			{field:'TipoManga',hidden:true},
			{field:'Mode',hidden:true}
	]],
	onBeforeLoad: function(param) { 
		param.Operation='enumerateMangasByJornada';
		param.Prueba= workingData.prueba;
		param.ID= workingData.jornada;
		return true;
	},
	onChange:function(value){
		updateParciales(/* empty to force combogrid load */);
        $('#pb_parciales-layout').layout('collapse','north');
	}
});

$('#pb_parciales-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true,
	// 1 minute poll is enouth for this, as no expected changes during a session
	onOpen: function() {
        // update header
        pb_getHeaderInfo();
        // update footer
        pb_setFooterInfo();
	}
});

$('#parciales_individual-datagrid').datagrid({
    onBeforeLoad: function(param) { // do not load if no manga selected
        var row=$('#enumerateParciales').combogrid('grid').datagrid('getSelected');
        if (!row) return false;
        return true;
    },
	onLoadSuccess: function(data) {
		if (parseInt(data.total)===0) return; // no data yet
		$(this).datagrid('autoSizeColumn','Nombre');
		$(this).datagrid('fitColumns'); // expand to max width
		// $(this).datagrid('scrollTo',0); // do not autoscroll: let the user decide
	}
});


// fire autorefresh if configured
function pb_updateParcialesIndividual() {
	var rtime=parseInt(ac_config.web_refreshtime);
	updateParciales(/* empty to retrieve data from combogrid */);
	if (rtime!==0) pb_config.Timeout=setTimeout(pb_updateParcialesIndividual,1000*rtime);
}
if (pb_config.Timeout!==null) clearTimeout(pb_config.Timeout);
vwcp_configureScreenLayout(null); // dirty, but works: remove license, hanndle club/country and so
pb_updateParcialesIndividual();


</script>