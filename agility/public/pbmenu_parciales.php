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

<!-- Presentacion de resultados parciales -->

<div id="pb_parciales-panel">
    <div id="pb_parciales-layout" style="width:100%">
        <div id="pb_parciales-Cabecera"  style="height:15%;" class="pb_floatingheader" data-options="region:'north',split:false,collapsed:false">
            <a id="pb_back-link" class="easyui-linkbutton" onClick="pbmenu_expandMenu(true);" href="#" style="float:left">
                <img id="pb_back-logo" src="/agility/images/backtomenu.png" width="40" />
            </a>&nbsp;
            <a id="pb_header-link" class="easyui-linkbutton" onClick="updateParciales(workingData.datosManga.Mode,workingData.datosManga);" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/agilitycontest.png" width="40" />
            </a>
            <span id="header-combinadaFlag" style="display:none">false</span> <!--indicador de combinada-->
            <span style="float:left;padding:5px" id="pb_header-infocabecera"><?php _e('Header'); ?></span>
            <span style="float:right;padding:5px" id="pb_header-texto">
                <?php _e('Partial scores'); ?><br/>
                <span id="enumerateParciales" style="width:200px"></span>
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

addTooltip($('#pb_header-link').linkbutton(),'<?php _e("Update partial scores table"); ?>');
addTooltip($('#pb_back-link').linkbutton(),'<?php _e("Back to contest menu"); ?>');
$('#pb_parciales-layout').layout({fit:true});

$('#pb_parciales-panel').panel({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true
});

$('#parciales_individual-datagrid').datagrid({
    onBeforeLoad: function(param) { // do not load if no manga selected
        if (workingData.manga==0) return false; // do not try to load if not variable initialized
        return true;
    },
    onLoadSuccess: function(data) {
        if (data.total==0) return; // no data yet
        $(this).datagrid('autoSizeColumn','Nombre');
        $(this).datagrid('fitColumns'); // expand to max width
        // $(this).datagrid('scrollTo',0); // do not autoscroll: let the user decide
    }
});

// fire autorefresh if configured
function pbmenu_updateParcialesIndividual() {
    // if asked to stop grant request
    var rtime=parseInt(ac_config.web_refreshtime);
    if ((rtime===0) || (workingData.timeout===null)) return;
    // refresh data
    updateParciales(workingData.datosManga.Mode,workingData.datosManga);
    pb_lookForMessages();
    // re-trigger timeout
    workingData.timeout=setTimeout(pbmenu_updateParcialesIndividual,1000*rtime);
}
vwcp_configureScreenLayout(null); // dirty, but works: remove license, hanndle club/country and so
setTimeout( function(){
    // update header
    pb_getHeaderInfo();
    // update footer
    pb_setFooterInfo();
    // set text for manga name
    $('#enumerateParciales').text(workingData.datosManga.Nombre);
}, 0);
if (workingData.timeout==="readyToRun")  pbmenu_updateParcialesIndividual();

</script>