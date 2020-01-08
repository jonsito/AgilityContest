<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
require_once(__DIR__ . "/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = AuthManager::getInstance("Videowall::finales");
if ( ! $am->allowed(ENABLE_LIVESTREAM)) { include_once("unregistered.php"); return 0;}
$combined=http_request("combined","i",0);
?>
<!--
vwls_finales.inc

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

<!-- Presentacion de resultados finales -->

<div id="vw_finales-window">

    <div id="vw_parent-layout" class="easyui-layout" style="width:100%;height:auto;">

        <?php if ($combined==1) { ?>
            <!-- http://rolandocaldas.com/html5/video-de-fondo-en-html5 -->
            <video id="vwls_video" autoplay="autoplay" preload="auto" muted="muted"
                   loop="loop" poster="../ajax/images/getRandomImage.php" style="width=100%;height:auto">
                <!-- http://guest:@192.168.122.168/videostream.cgi -->
                <source id="vwls_videomp4" src="" type='video/mp4'/>
                <source id="vwls_videoogv" src="" type='video/ogg'/>
                <source id="vwls_videowebm" src="" type='video/webm'/>
            </video>
        <?php } else { ?>
            <img alt="green" src="../ajax/images/getChromaKeyImage.php" style="z-index:-1;" />
        <?php } ?>

        <div data-options="region:'east',split:false,border:false" style="width:5%;background-color:transparent;"></div>
        <div data-options="region:'west',split:false,border:false" style="width:30%;background-color:transparent;"></div>
        <div data-options="region:'center',border:false" style="background-color:transparent;">
        <!-- ventana interior -->
            <div id="vwls_common" style="display:inline-block;width:100%;height:auto">
                <div id="vw_finales-Cabecera" data-options="region:'north',split:false" class="vw_floatingheader"
                      style="height:120px;font-size:1.0em;" >

                    <table width="100%">
                        <tr>
                            <td rowspan="2">
                                <span style="float:left;background:rgba(255,255,255,0.5);"> <img alt="header-logo" id="vw_header-logo" src="../images/logos/agilitycontest.png" width="100"/> </span>
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
                                <?php include_once(__DIR__ . "/../console/templates/final_rounds_data.inc.php"); ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- tabla de datos: se cargan la de individual y de equipos, y en runtime se selecciona una u otra -->
                <div id="vw_table" data-options="region:'center'" class="scores_table" style="background-color:transparent;">
                    <!-- datagrid para resultados individuales -->
                        <table id="finales_individual-datagrid">
                            <thead>
                            <tr>
                                <th colspan="5"> <span class="main_theader" ><?php _e('Competitor data'); ?></span></th>
                                <th colspan="3"> <span class="main_theader" id="finales_individual_roundname_m1"><?php _e('Round'); ?> 1</span></th>
                                <th colspan="3"> <span class="main_theader" id="finales_individual_roundname_m2"><?php _e('Round'); ?> 2</span></th>
                                <th colspan="3"> <span class="main_theader"><?php _e('Final scores'); ?></span></th>
                            </tr>
                            <tr>
                                <th width="5" data-options="field:'LogoClub',		align:'left',formatter:formatLogo" > &nbsp;</th>
                                <th width="5" data-options="field:'Dorsal',		align:'left'" > <?php _e('Dors'); ?>.</th>
                                <th width="0" data-options="field:'Licencia',	hidden:true" ></th>
                                <th width="10" data-options="field:'Nombre',		align:'center',formatter:formatBold"> <?php _e('Name'); ?></th>
                                <th width="18" data-options="field:'NombreGuia',	align:'right'" > <?php _e('Handler'); ?></th>
                                <th width="7" data-options="field:'T1',			align:'right',formatter:formatT1,styler:formatBorder"> <?php _e('Time'); ?>.</th>
                                <th width="7" data-options="field:'P1',			align:'right',formatter:formatP1"> <?php _e('Penal'); ?>.</th>
                                <th width="4" data-options="field:'Puesto1',		align:'center'"> <?php _e('Pos'); ?>.</th>
                                <th width="7" data-options="field:'T2',			align:'right',formatter:formatT2,styler:formatBorder"> <?php _e('Time'); ?>.</th>
                                <th width="7" data-options="field:'P2',			align:'right',formatter:formatP2"> <?php _e('Penal'); ?>.</th>
                                <th width="4" data-options="field:'Puesto2',		align:'center'"> <?php _e('Pos'); ?>.</th>
                                <th width="7" data-options="field:'Tiempo',		align:'right',formatter:formatTF,styler:formatBorder"><?php _e('Time'); ?></th>
                                <th width="7" data-options="field:'Penalizacion',	align:'right',formatter:formatPenalizacionFinal" > <?php _e('Penaliz'); ?>.</th>
                                <th width="6" data-options="field:'Puesto',		align:'center',formatter:formatPuestoFinalBig" ><?php _e('Position'); ?></th>
                            </tr>
                            </thead>
                        </table>
                    <!-- datagrid para resultados por equipos -->
                    <?php include_once(__DIR__ . "/../console/templates/final_teams.inc.php"); ?>
                </div>

                <!-- Pie de pagina -->
                <div id="vw_finales-footer" data-options="region:'south',split:false" class="vw_floatingfooter"
                    style="font-size:1.2em;">
                    <span id="vw_footer-footerData"></span>
                </div>
            </div>
        </div>
    </div>

</div> <!-- vw_finales-window -->

<script type="text/javascript">

$('#vw_parent-layout').layout({fit:true});
$('#vwls_common').layout({fit:true});

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
    expandCount: 0,
    // propiedades del panel asociado
    fit: false, // set to false as we used thead to declare columns, and they have their own width
    border: false,
    closable: false,
    collapsible: false,
    collapsed: false,
    // propiedades del datagrid
    // no tenemos metodo get ni parametros: directamente cargamos desde el datagrid
    loadMsg: "<?php _e('Updating final scores');?>...",
    width:'100%',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight:false,
    idField: 'ID',
    pageSize: 500, // enought bit to make it senseless
    // columns declared at html section to show additional headers
    scrollbarSize:0,
    rowStyler:lsRowStyler,
    onBeforeLoad: function (param) {
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='<?php _e('Contest'); ?>') return false;
        return true;
    },
    onLoadSuccess: function(data) {
        if (parseInt(data.total)===0) return; // no data yet
        $('#finales_individual-datagrid').datagrid('scrollTo',0); // point to first result
    }
});

var fed=$('#finales_equipos-datagrid');
fed.datagrid({
    rowStyler:lsRowStyler,
    onBeforeLoad: function (param) {
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='<?php _e('Contest'); ?>') return false;
        return true;
    },
    onLoadSuccess: function(data) {
        if (parseInt(data.total)===0) return; // no data yet
        $(this).datagrid('scrollTo',0); // point to first result
        // at livestream no space to expand first row. so let remain hidden
    }
});

// hide Categorias field in team view
fed.datagrid('hideColumn','Categorias');
fed.datagrid('fitColumns');

var eventHandler= {
    'null': null,// null event: no action taken
    'init': function(event) { // operator starts tablet application
        vwls_keyBindings(); // capture space keyboard to enable/disable OSD
        vwls_enableOSD(1);
        vw_updateWorkingData(event,function(e,d){
            vw_updateHeaderAndFooter(e,d);
            clearFinalRoundInformation();
            vwcf_configureScreenLayout(d); // fix individual/team national/international view for final results
            $('#vw_header-infoprueba').html('<?php _e("Header"); ?>');
            $('#vw_header-infomanga').html("(<?php _e('No round selected');?>)");
        });
    },
    'open': function(event){ // operator select tanda
        vw_updateWorkingData(event,function(e,d){
            vw_updateHeaderAndFooter(e,d);
            vwcf_configureScreenLayout(d); // fix individual/team national/international view for final results
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
    'crono_ready': null, // crono is active
    'user': null, // user defined event
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
                /* EVTCMD_NULL:         */ function(e) { console.log("Received null command"); },
                /* EVTCMD_SWITCH_SCREEN:*/ function(e) { livestream_switchConsole(e); },
                /* EVTCMD_SETFONTFAMILY:*/ null,
                /* EVTCMD_NOTUSED3:     */ null,
                /* EVTCMD_SETFONTSIZE:  */ null,
                /* EVTCMD_OSDSETALPHA:  */ function(e) { vwls_setAlphaOSD(e['Value'],"#vw_table"); },
                /* EVTCMD_OSDSETDELAY:  */ function(e) { vwls_setDelayOSD(e['Value']); },
                /* EVTCMD_NOTUSED7:     */ null,
                /* EVTCMD_MESSAGE:      */ function(e) { livestream_showMessage(e); },
                /* EVTCMD_ENABLEOSD:    */ function(e) { vwls_enableOSD(e['Value']); }
            ]
        )
    },
    'reconfig':	function(event) { loadConfiguration(); }, // reload configuration from server
    'info':	null // click on user defined tandas
};

</script>