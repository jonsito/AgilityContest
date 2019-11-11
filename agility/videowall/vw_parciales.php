<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = AuthManager::getInstance("Videowall::parciales");
if ( ! $am->allowed(ENABLE_VIDEOWALL)) { include_once("unregistered.php"); return 0;}
?>
<!--
vw_parciales.inc

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

<div id="vw_parciales-window">
    <div id="vw_parciales-layout" style="width:100%">
        <div id="vw_parciales-Cabecera" data-options="region:'north',split:false" style="height:125px" class="vw_floatingheader">
            <table width="100%">
                <tr>
                    <td rowspan="2">
                        <span style="float:left;background:rgba(255,255,255,0.5);"> <img id="vw_header-logo" src="../images/logos/agilitycontest.png" width="100"/> </span>
                    </td>
                    <td align="left">
                        <span style="float:left;padding:10px" id="vw_header-infoprueba"><?php _e('Contest'); ?></span>
                    </td>
                    <td align="right">
                        <span id="vw_header-texto"><?php _e('Partial scores'); ?></span>&nbsp;-&nbsp;
                        <span id="vw_header-ring"><?php _e('Ring'); ?></span>
                        <br />
                        <span id="vw_header-infomanga" style="width:200px">(<?php _e('No round selected'); ?>)</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="right" id="vw_parciales_trs-data">
                        <?php include_once(__DIR__ . "/../console/templates/parcial_round_data.inc.php"); ?>
                    </td>
                </tr>
            </table>
        </div>
        <div id="vw_parciales-data" data-options="region:'center'" >
            <!-- datagrid para resultados individuales -->
            <div id="parciales_individual-table" class="scores_table" style="display:none;width:100%">
                <?php include_once(__DIR__ . "/../console/templates/parcial_individual.inc.php"); ?>
            </div>
            <!-- datagrid para resultados por equipos -->
            <div id="parciales_equipos-table" class="scores_table" style="display:none;width:100%">
                <?php include_once(__DIR__ . "/../console/templates/parcial_teams.inc.php"); ?>
            </div>
        </div>
        <div id="vw_parciales-footer" data-options="region:'south',split:false" class="vw_floatingfooter">
            <span id="vw_footer-footerData"></span>
        </div>
    </div>
</div> <!-- vw_parciales-window -->

<script type="text/javascript">

$('#vw_parciales-layout').layout({fit:true});

$('#vw_parciales-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true,
	onOpen: function() {
        startEventMgr();
	}
});

$('#parciales_individual-datagrid').datagrid({
    onBeforeLoad: function (param) {
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='<?php _e('Contest'); ?>') return false;
        return true;
    },
    onLoadSuccess: function(data) {
        if (data.total==0) return; // no data yet
        $(this).datagrid('autoSizeColumn','Nombre');
        $(this).datagrid('fitColumns'); // expand to max width
        $(this).datagrid('scrollTo',0); // point to first result
    }
});

$('#parciales_equipos-datagrid').datagrid({
    onBeforeLoad: function (param) {
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='<?php _e('Contest'); ?>') return false;
        return true;
    },
    onLoadSuccess: function(data) {
        if (data.total==0) return; // no data yet
        $(this).datagrid('expandRow',0); // expand 2 first rows
        $(this).datagrid('expandRow',1);
        $(this).datagrid('scrollTo',0); // point to first result
        $(this).datagrid('fixDetailRowHeight');
    }
});

var eventHandler= {
    'null': null,// null event: no action taken
    'init': function(event) { // operator starts tablet application
        vw_updateWorkingData(event,function(e,d){
            vw_updateHeaderAndFooter(e,d);
            vwcp_configureScreenLayout(d); // fix individual or team view for final results
            clearParcialRoundInformation();
            $('#vw_header-infoprueba').html('<?php _e("Header"); ?>');
            $('#vw_header-infomanga').html("(<?php _e('No round selected');?>)");
        });
    },
    'open': function(event){ // operator select tandac
        vw_updateWorkingData(event,function(e,d){
            vw_updateHeaderAndFooter(e,d);
            vwcp_configureScreenLayout(d); // fix individual or team view for final results
            updateParciales(d.Ronda.Mode,d);
        });
    },
    'close': null,    // no more dogs in tanda
    'datos': null,      // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
    'llamada': null,    // llamada a pista
    'salida': null,     // orden de salida
    'start': null,      // start crono manual
    'stop': null,       // stop crono manual
    // nada que hacer aqui: el crono automatico se procesa en el tablet
    'crono_start':  null, // arranque crono automatico
    'crono_restart': null,// paso de tiempo intermedio a manual
    'crono_int':  	null, // tiempo intermedio crono electronico
    'crono_stop':  null, // parada crono electronico
    'crono_reset':  null, // puesta a cero del crono electronico
    'crono_error':  null, // fallo en los sensores de paso
    'crono_dat':    null, // datos desde crono electronico
    'crono_ready':    null, // chrono ready and listening
    'user':   null, // user defined event
    'aceptar':	function(event){ // operador pulsa aceptar
        vw_updateWorkingData(event,function(e,d){
            updateParciales(d.Ronda.Mode,d);
        });
    },
    'cancelar': null, // operador pulsa cancelar
    'camera':	null, // change video source
    'command': function(event){ // videowall remote control
        handleCommandEvent(
            event,
            [
                /* EVTCMD_NULL:         */ function(e) {console.log("Received null command"); },
                /* EVTCMD_SWITCH_SCREEN:*/ function(e) {videowall_switchConsole(e); },
                /* EVTCMD_SETFONTFAMILY:*/ null,
                /* EVTCMD_NOTUSED3:     */ null,
                /* EVTCMD_SETFONTSIZE:  */ null,
                /* EVTCMD_OSDSETALPHA:     */ null,
                /* EVTCMD_OSDSETDELAY:  */ null,
                /* EVTCMD_NOTUSED7:     */ null,
                /* EVTCMD_MESSAGE:      */ function(e) {videowall_showMessage(e); },
                /* EVTCMD_ENABLEOSD:    */ null
            ]
        )
    },
    'reconfig':	function(event) { loadConfiguration(); }, // reload configuration from server
    'info':	null // click on user defined tandas
};

</script>