<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = AuthManager::getInstance("Public::finales");
if ( ! $am->allowed(ENABLE_PUBLIC)) { include_once("unregistered.php"); return 0;}
?>
<!--
pbmenu_finales.php

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

<!-- Presentacion de clasificacion final -->

<div id="pb_finales-panel">
    <div id="pb_finales-layout" style="width:100%">
        <div id="pb_finales-Cabecera" style="height:20%;" class="pb_floatingheader" data-options="region:'north',split:false,collapsed:false">
            <a id="pb_back-link" class="easyui-linkbutton" onClick="pbmenu_expandMenu(true);" href="#" style="float:left">
                <img id="pb_back-logo" src="../images/backtomenu.png" width="40" />
            </a>&nbsp;
            <a id="pb_header-link" class="easyui-linkbutton" onClick="updateFinales(0,workingData.datosRonda);" href="#" style="float:left">
                <img id="pb_header-logo" src="../images/logos/agilitycontest.png" width="40" />
            </a>
            <span style="float:left;padding:5px" id="pb_header-infocabecera"><?php _e('Header'); ?></span>
            <span style="float:right" id="pb_header-texto">
                <?php _e('Final scores'); ?><br/>
		        <span id="enumerateFinales" style="width:200px"></span>
            </span>
            <!-- Datos de TRS y TRM -->
            <?php include(__DIR__ . "/../console/templates/final_rounds_data.inc.php"); ?>
        </div>
        <div id="pb_table" data-options="region:'center'">
            <div class="scores_table">
                <?php include(__DIR__ . "/../console/templates/final_individual.inc.php"); ?>
            </div>
        </div>
        <div id="pb_finales-footer" data-options="region:'south',split:false" style="height:10%;" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
    </div>
</div> <!-- finales-window -->

<script type="text/javascript">

addTooltip($('#pb_header-link').linkbutton(),'<?php _e("Update scores"); ?>');
addTooltip($('#pb_back-link').linkbutton(),'<?php _e("Back to contest menu"); ?>');

$('#pb_finales-layout').layout({fit:true});
$('#pb_finales-panel').panel({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true
});

$('#finales_individual-datagrid').datagrid({
    onBeforeLoad: function (param) {
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='<?php _e('Contest'); ?>') return false;
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
function pbmenu_updateFinalesIndividual() {
    var rtime=parseInt(ac_config.web_refreshtime);
    if ( (rtime===0) || (pb_config.Timeout===null) ) return;
    updateFinales(0,workingData.datosRonda);
    pb_config.Timeout=setTimeout(pbmenu_updateFinalesIndividual,1000*rtime);
}
// dirty, but works: remove license, hanndle club/country and so
vwcf_configureScreenLayout(null);
// fix header text
setTimeout(function(){
    // update header
    pb_getHeaderInfo();
    // update footer info
    pb_setFooterInfo();
    $('#enumerateFinales').text(workingData.datosRonda.Nombre);
},0);
// and fire timeout if enabled
if (pb_config.Timeout==="readyToRun")  pbmenu_updateFinalesIndividual();

</script>
