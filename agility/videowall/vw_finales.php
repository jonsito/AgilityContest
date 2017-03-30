<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
require_once(__DIR__ . "/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Videowall::finales");
if ( ! $am->allowed(ENABLE_VIDEOWALL)) { include_once("unregistered.php"); return 0;}
$combined=http_request("combined","i",0);
?>
<!--
vw_finales.inc

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

<!-- Presentacion de resultados finales -->

<div id="vw_finales-window">
    <!-- ventana interior -->
    <div id="vw_common" style="display:inline-block;width:100%;height:auto">
        <div id="vw_finales-Cabecera" data-options="region:'north',split:false" class="vw_floatingheader" style="height:155px;font-size:1.0em;" >

            <table width="100%">
                <tr>
                    <td rowspan="2">
                        <span style="float:left;background:rgba(255,255,255,0.5);"> <img id="vw_header-logo" src="/agility/images/logos/rsce.png" width="100"/> </span>
                    </td>
                    <td align="left">
                        <span style="float:left;padding:10px" id="vw_header-infoprueba"><?php _e('Contest'); ?></span>
                    </td>
                    <td align="right">
                        <span id="vw_header-texto"><?php _e('Final scores'); ?></span>&nbsp;-&nbsp;
                        <span id="vw_header-ring"><?php _e('Ring'); ?></span>
                            <br />
                            <span id="finales-NombreRonda" style="width:200px">(<?php _e('No round selected'); ?>)</span>
                    </td>
                </tr>
                <tr>
                     <td colspan="2" align="right" id="vw_finales_trs-data">
                         <?php include_once(__DIR__."/../lib/templates/final_rounds_data.inc.php"); ?>
                     </td>
                </tr>
            </table>
        </div>

        <!-- tabla de datos: se cargan la de individual y de equipos, y en runtime se selecciona una u otra -->
        <div id="vw_finales-data" data-options="region:'center'" style="background-color:transparent;">
            <!-- datagrid para resultados individuales -->
            <div id="finales_individual-table" class="scores_table" style="display:none;width:100%">
                <?php include_once(__DIR__ . "/../lib/templates/final_individual.inc.php"); ?>
            </div>
            <!-- datagrid para resultados por equipos -->
            <div id="finales_equipos-table" class="scores_table" style="display:none;width:103%">
                <?php include_once(__DIR__."/../lib/templates/final_teams.inc.php"); ?>
            </div>
        </div>

        <!-- Pie de pagina -->
        <div id="vw_finales-footer" data-options="region:'south',split:false" class="vw_floatingfooter" style="height:70px;font-size:1.2em;">
             <span id="vw_footer-footerData"></span>
        </div>
    </div>

</div> <!-- vw_finales-window -->

<script type="text/javascript">

$('#vw_common').layout({fit:true});

$('#vw_finales-window').window({
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
        $(this).datagrid('scrollTo',0); // point to first result
    }
});

$('#finales_equipos-datagrid').datagrid({
    onBeforeLoad: function (param) {
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='<?php _e('Contest'); ?>') return false;
        return true;
    },
    onLoadSuccess: function(data) {
        if (data.total==0) return; // no data yet
        var dg=$('#finales_equipos-datagrid');
        dg.datagrid('expandRow',0); // expand 2 first rows
        dg.datagrid('expandRow',1);
        dg.datagrid('scrollTo',0); // point to first result
        dg.datagrid('fixDetailRowHeight');
    }
});

var eventHandler= {
    'null': null,// null event: no action taken
    'init': function(event) { // operator starts tablet application
        vw_updateWorkingData(event,function(e,d){
            vw_updateHeaderAndFooter(e,d);
            clearFinalRoundInformation();
            vwcf_configureScreenLayout(d); // fix individual or team view for final results
            $('#vw_header-infoprueba').html('<?php _e("Header"); ?>');
            $('#vw_header-NombreRonda').html("(<?php _e('No round selected');?>)");
        });
    },
    'open': function(event){ // operator select tanda
        vw_updateWorkingData(event,function(e,d){
            vw_updateHeaderAndFooter(e,d);
            vwcf_configureScreenLayout(d); // fix individual or team view for final results
            updateFinales(0,d.Ronda);
        });
    },
    'close': null,      // no more dogs in tanda
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
    'crono_dat':  null, // datos provenientes de crono
    'crono_ready':    null, // chrono ready and listening
    'aceptar':	function(event){ // operador pulsa aceptar
        vw_updateWorkingData(event,function(e,d){
            updateFinales(0,d.Ronda);
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
                /* EVTCMD_NEXTFONT:     */ null,
                /* EVTCMD_PREVFONT:     */ null,
                /* EVTCMD_INCFONTSIZE:  */ null,
                /* EVTCMD_DECFONTSIZE:  */ null,
                /* EVTCMD_SETDELAY:     */ null,
                /* EVTCMD_NOTUSED:     */ null,
                /* EVTCMD_MESSAGE:      */ function(e) {videowall_showMessage(e); },
                /* EVTCMD_ENABLEOSD:    */ null
            ]
        )
    },
    'reconfig':	function(event) { loadConfiguration(); }, // reload configuration from server
    'info':	null // click on user defined tandas
};

</script>