<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = AuthManager::getInstance("Videowall::combinada");
if ( ! $am->allowed(ENABLE_VIDEOWALL)) { include_once("unregistered.php"); return 0;}
?>
<!--
vwc_finales.php

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

<!--
Pantalla de de visualizacion combinada llamada/parciales
 prefijos:
  vw_ commun para todos los marcadores
  vwc_ comun para todos los paneles combinados
  vwcf_ id asociados al panel combinado de parciales
  vwcf_ id asociados al panel combinado de finales
-->
<div id="vwcf-window">
    <div id="vwcf-layout">
        <div data-options="region:'north'" class="vwc_top" style="height:10%;padding:5px"> <!-- CABECERA -->
            <div style="display:inline-block;width=100%;padding:0px" class="vwc_header">
                <img  id="vwc_header-logo" src="../images/logos/agilitycontest.png"/>
                <span id="vwc_header-infoprueba"><?php _e('Contest'); ?></span>
                <span id="vwc_header-infojornada"><?php _e('Journey'); ?></span>
                <span id="header-combinadaFlag" style="display:none">true</span> <!--indicador de combinada-->
                <span id="vwc_header-ring" style="text-align:right"><?php _e('Ring'); ?></span>
                <span id="vwc_header-calltoring" style="text-align:left">
                    <?php _e('Call to ring'); ?> -
                    <span id="vwc_header-NombreTanda">&nbsp;</span>
                </span>
                <span id="vwc_header-finalscores" style="text-align:right">
                    <?php _e('Final scores'); ?> -
                    <span id="vwc_header-NombreRonda">&nbsp;</span>
                    <!-- <span id="vwc_header-ModeString">&nbsp;</span> -->
                </span>
            </div>
        </div>
        <div data-options="region:'west'" style="width:25%;"> <!-- LLAMADA A PISTA -->
            <table id="vwc_llamada-datagrid" class="vwc_top"></table>
        </div>
        <div data-options="region:'center',border:false" class="vwc_top"><!-- Espacio vacio -->&nbsp;</div>
        <div data-options="region:'east'" class="scores_table" style="width:74%"> <!-- CLASIFICACION FINAL -->
            <!-- Datos de TRS y TRM -->
            <?php include_once(__DIR__ . "/../console/templates/final_rounds_data.inc.php"); ?>
            <!-- datagrid para clasificacion individual -->
            <?php include_once(__DIR__ . "/../console/templates/final_individual.inc.php"); ?>
            <!-- datagrid para clasificacion por equipos -->
            <?php include_once(__DIR__ . "/../console/templates/final_teams.inc.php"); ?>
        </div>
        <div data-options="region:'south',border:false" style="height:25%;">
            <div id="vwcf-layout2">
                <div data-options="region:'north'" style="height:30%" class="vwc_live"> <!-- DATOS DEL PERRO EN PISTA -->
                    <div id="vwcf_common" style="display:inline-block;width:100%" >
                        <!-- Informacion del participante -->
                        <span class="vwc_dlabel" id="vwls_Numero"><?php _e('Num'); ?></span>
                        <span style="display:none" id="vwls_Perro">0</span>
                        <img class="vwc_logo" id="vwls_Logo" alt="Logo" src="../images/logos/agilitycontest.png"/>
                        <span class="vwc_label" id="vwls_Dorsal"><?php _e('Dorsal'); ?></span>
                        <span class="vwc_label" id="vwls_Nombre"><?php _e('Name'); ?></span>
                        <span class="vwc_label" id="vwls_NombreGuia"><?php _e('Handler'); ?></span>
                        <span class="vwc_label" id="vwls_NombreClub"><?php _e('Club'); ?></span>
                        <span class="vwc_label" id="vwls_Categoria" style="display:none"><?php _e('Category'); ?></span>
                        <span class="vwc_label" id="vwls_Grado" style="display:none"><?php _e('Grade'); ?></span>
                        <span class="vwc_label" id="vwls_Celo"><?php _e('Heat'); ?></span>
                        <!-- datos de resultados -->
                        <span style="display:none"  id="vwls_Faltas">0</span>
                        <span style="display:none"  id="vwls_Tocados">0</span>
                        <span class="vwc_dlabel" id="vwls_FaltasTocadosLbl"><?php _e('F'); ?>:</span>
                        <span class="vwc_data"  id="vwls_FaltasTocados">0</span>
                        <span class="vwc_dlabel" id="vwls_RehusesLbl"><?php _e('R'); ?>:</span>
                        <span class="vwc_data"  id="vwls_Rehuses">0</span>
                        <!-- Informacion de cronometro -->
                        <span class="vwc_dtime"  id="vwls_Tiempo">00.000</span>
                        <span style="display:none" id="vwls_TIntermedio">00.000</span>
                        <span style="display:none" id="vwls_EliminadoLbl"></span>
                        <span style="display:none" id="vwls_Eliminado">0</span>
                        <span style="display:none" id="vwls_NoPresentadoLbl"></span>
                        <span style="display:none" id="vwls_NoPresentado">0</span>
                        <span class="vwc_dtime"  id="vwls_Puesto">Puesto</span>
                    </div>
                </div>
                <div data-options="region:'center',border:false" class="vwc_bottom"> <!-- PATROCINADORES -->
                    <div style="display:inline-block;width=100%;padding:20px" class="vwc_footer">
                        <span id="vw_footer-footerData"></span>
                    </div>
                </div>
                <div id="finales_last-div" data-options="region:'east'" style="width:76%"> <!-- ULTIMOS TRES RESULTADOS -->
                    <?php include_once(__DIR__ . "/../console/templates/final_last_individual.inc.php"); ?>
                    <?php include_once(__DIR__ . "/../console/templates/final_last_teams.inc.php"); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- declare a tag to attach a chrono object to and several crono-related vars-->
<div id="cronometro">
    <span id="vwls_StartStopFlag" style="display:none">Start</span>
    <span id="vwls_timestamp" style="display:none"></span>
</div>

<script type="text/javascript">

    // create a Chronometer instance
    $('#cronometro').Chrono( {
        seconds_sel: '#vwls_timestamp',
        auto: false,
        interval: 50,
        showMode: 2,
        onUpdate: function(elapsed,running,paused) {
            var time=parseFloat(elapsed/1000.0);
            $('#vwls_Tiempo').html(toFixedT(time,(running)?1:ac_config.numdecs));
            if (!running && !paused) return true; // do not update penalization on stop
            vwcf_evalPenalizacion();
            return true;
        },
        onBeforePause:function() { $('#vwls_Tiempo').addClass('blink'); return true; },
        onBeforeResume: function() { $('#vwls_Tiempo').removeClass('blink'); return true; },
        onBeforeReset: function() { $('#vwls_Tiempo').removeClass('blink'); return true; }
    });

    $('#vwcf-layout').layout({fit:true});
    $('#vwcf-layout2').layout({fit:true});

    $('#vwcf-window').window({
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
            if( $('#vwc_header-infoprueba').html()==='<?php _e('Contest'); ?>') return false;
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
            if( $('#vwc_header-infoprueba').html()==='<?php _e('Contest'); ?>') return false;
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


    $('#vwc_llamada-datagrid').datagrid({
        // propiedades del panel asociado
        fit: true,
        border: false,
        closable: false,
        collapsible: false,
        collapsed: false,
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        singleSelect: true,
        autoRowHeight: true,
        columns:[[
            { field:'Orden',		width:'10%', align:'center', title: '#', formatter:formatOrdenLlamadaPista},
            { field:'LogoClub',		width:'10%', align:'center', title: '', formatter:formatLogo},
            { field:'Manga',		hidden:true },
            { field:'Perro',		hidden:true },
            { field:'Equipo',		hidden:true },
            { field:'Dorsal',		width:'10%', align:'center', title: '<?php _e('Dorsal'); ?>'},
            // { field:'Licencia',		width:'10%', align:'center',  title: '<?php _e('License'); ?>'},
            { field:'Nombre',		width:'15%', align:'center',  title: '<?php _e('Name'); ?>',formatter:formatBold},
            { field:'NombreGuia',	width:'30%', align:'right', title: '<?php _e('Handler'); ?>',formatter:formatLlamadaGuia },
            { field:'NombreClub',	width:'20%', align:'right', title: '<?php _e('Club'); ?>' },
            { field:'NombreEquipo',	width:'20%', align:'right', title: '<?php _e('Team'); ?>',hidden:true },
            { field:'Celo',	        width:'5%', align:'center', title: '<?php _e('Heat'); ?>',formatter:formatCelo }
        ]],
        rowStyler:myLlamadaRowStyler,
        onBeforeLoad: function(param) {
            // do not update until 'open' received
            if( $('#vwc_header-infoprueba').html()==='<?php _e('Contest'); ?>') return false;
            return true;
        }
    });
    
    // header elements layout
    var layout= {'rows':110,'cols':1900};
    doLayout(layout,"#vwc_header-logo",	        0,	    0,	100,    100	);
    doLayout(layout,"#vwc_header-infoprueba",	120,	0,	1760,	25	);
    doLayout(layout,"#vwc_header-infojornada",	120,	35,	1760,	25	);
    doLayout(layout,"#vwc_header-ring",     	120,	35,	1760,	25	);
    doLayout(layout,"#vwc_header-calltoring",	120,	65,	5800,	25	);
    doLayout(layout,"#vwc_header-finalscores",700,	65,	1180,	25	);
    // livedata elements layout
    var liveLayout = {'rows':200,'cols':1900};
    doLayout(liveLayout,"#vwls_Numero",	        0,	    25,	    70,	    150	);
    doLayout(liveLayout,"#vwls_Logo",	        100,	10,	    120,	180	);
    doLayout(liveLayout,"#vwls_Dorsal",	        230,	10,	    80, 	100	);
    doLayout(liveLayout,"#vwls_Nombre",	        335,    10,	    660,	100	);
    doLayout(liveLayout,"#vwls_Celo",	        995,   10,	    100,	100	);
    doLayout(liveLayout,"#vwls_NombreGuia",	    230,	100,    510,	100	);
    doLayout(liveLayout,"#vwls_NombreClub",	    750,	100,    350,	100	);
    // doLayout(liveLayout,"#vwls_Categoria",	    0,	    0,	100,	100	);
    // doLayout(liveLayout,"#vwls_Grado",	        0,	    0,	100,	100	);
    // doLayout(liveLayout,"#vwls_TocadosLbl",	    900,	25,     100,	150	);
    // doLayout(liveLayout,"#vwls_Tocados",	        1000,	25,     100,	150	);
    doLayout(liveLayout,"#vwls_FaltasTocadosLbl", 1100,	25,	    100,	150	);
    doLayout(liveLayout,"#vwls_FaltasTocados",	1200,	25,	    100,	150	);
    doLayout(liveLayout,"#vwls_RehusesLbl",	    1300,	25,	    100,	150	);
    doLayout(liveLayout,"#vwls_Rehuses",	    1400,	25,	    100,	150	);
    doLayout(liveLayout,"#vwls_Tiempo",	        1500,	25, 	200,	150	);
    doLayout(liveLayout,"#vwls_Puesto",	        1700,	25, 	200,	150	);

    var eventHandler= {
        'null': null,// null event: no action taken
        'init': function (event, time) { // operator starts tablet application
            vw_updateWorkingData(event,function(e,d){
                vwc_updateHeaderAndFooter(e,d); // fix header
                vwcf_configureScreenLayout(d); // fix individual/team national/international view for final results
                vwcf_updateLlamada(e,d);
            });
        },
        'open': function (event, time) { // operator select tandaxx
            vw_updateWorkingData(event,function(e,d){
                vwc_updateHeaderAndFooter(e,d);
                vwcf_configureScreenLayout(d); // fix individual or team view for final results
                vwcf_updateLlamada(e,d);
            });
        },
        'close': function (event, time) { // no more dogs in tanda
            vw_updateWorkingData(event,function(e,d){
                vwcf_updateLlamada({'Dog':-1},d); // seek at end of list. update clasifications in inner call
            });
        },
        'datos': function (event, time) {      // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
            vwls_updateData(event);
            vwcf_evalPenalizacion();
        },
        'llamada': function (event, time) {    // llamada a pista
            if (event['Source']!=='tablet') return;
            myCounter.stop(); // do not stop chrono, just countdown if any
            vw_updateWorkingData(event,function(e,d){
                vwcf_updateLlamada(e,d);
            });
        },
        'salida': function (event, time) {     // orden de salida
            myCounter.start();
            vwcf_displayPuesto(false,0);
        },
        'start': function (event, time) {      // start crono manual
            // si crono automatico, ignora
            var ssf = $('#vwls_StartStopFlag');
            if (ssf.text() === "Auto") return;
            ssf.text("Stop");
            myCounter.stop(); // stop 15 seconds countdown if needed
            var crm = $('#cronometro');
            crm.Chrono('stop', time);
            crm.Chrono('reset');
            crm.Chrono('start', time);
            vwcf_displayPuesto(false,0);
        },
        'stop': function (event, time) {      // stop crono manual
            var crm= $('#cronometro');
            $('#vwls_StartStopFlag').text("Start");
            myCounter.stop();
            crm.Chrono('stop', time);
            vwcf_displayPuesto(true,crm.Chrono('getValue')/1000);
        },
        // nada que hacer aqui: el crono automatico se procesa en el tablet
        'crono_start': function (event, time) { // arranque crono automatico
            vwcf_displayPuesto(false,0);
            var crm = $('#cronometro');
            myCounter.stop();
            $('#vwls_StartStopFlag').text('Auto');
            // si esta parado, arranca en modo automatico
            if (!crm.Chrono('started')) {
                crm.Chrono('stop', time);
                crm.Chrono('reset');
                crm.Chrono('start', time);
                vwcf_evalPenalizacion();
                return
            }
            if (ac_config.crono_resync === "0") {
                crm.Chrono('reset'); // si no resync, resetea el crono y vuelve a contar
                crm.Chrono('start', time);
            } // else wait for chrono restart event
        },
        'crono_restart': function (event, time) {	// paso de tiempo manual a automatico
            $('#cronometro').Chrono('resync', event['stop'], event['start']);
        },
        'crono_int': function (event, time) {	// tiempo intermedio crono electronico
            var crm = $('#cronometro');
            if (!crm.Chrono('started')) return;	// si crono no esta activo, ignorar
            crm.Chrono('pause', time);
            setTimeout(function () {
                crm.Chrono('resume');
            }, 5000);
        },
        'crono_stop': function (event, time) {	// parada crono electronico
            var crm= $('#cronometro');
            $('#vwls_StartStopFlag').text("Start");
            crm.Chrono('stop', time);
            vwcf_displayPuesto(true,crm.Chrono('getValue')/1000);
        },
        'crono_reset': function (event, time) {	// puesta a cero del crono electronico
            var crm = $('#cronometro');
            myCounter.stop();
            $('#vwls_StartStopFlag').text("Start");
            crm.Chrono('stop', time);
            crm.Chrono('reset', time);
            vwcf_displayPuesto(false,0);
        },
        'crono_dat': function(event,time) {      // actualizar datos -1:decrease 0:ignore 1:increase
            vwls_updateChronoData(event);
            vwcf_evalPenalizacion();
        },
        'crono_error': null, // fallo en los sensores de paso
        'crono_ready':    null, // chrono ready and listening
        'user':    null, // user defined event
        'aceptar': function (event,time) { // operador pulsa aceptar
            myCounter.stop();
            vw_updateWorkingData(event,function(e,d){
                // do not call updateFinales() here; do it at 'llamada' or 'close'
            });
        },
        'cancelar': function (event,time) {  // operador pulsa cancelar
            var crm = $('#cronometro');
            myCounter.stop();
            crm.Chrono('stop', time);
            crm.Chrono('reset', time);
        },
        'camera':	null, // change video source
        'command': function(event,time){ // videowall remote control
            handleCommandEvent(
                event,
                [
                    /* EVTCMD_NULL:         */ function(e) {console.log("Received null command"); },
                    /* EVTCMD_SWITCH_SCREEN:*/ function(e) {videowall_switchConsole(e); },
                    /* EVTCMD_SETFONTFAMILY:*/ null,
                    /* EVTCMD_NOTUSED3:     */ null,
                    /* EVTCMD_SETFONTSIZE:  */ null,
                    /* EVTCMD_OSDSETALPHA:  */ null,
                    /* EVTCMD_OSDSETDELAY:  */ null,
                    /* EVTCMD_NOTUSED7:     */ null,
                    /* EVTCMD_MESSAGE:      */ function(e) {videowall_showMessage(e); },
                    /* EVTCMD_ENABLEOSD:    */ null
                ]
            )
        },
        'reconfig':	function(event,time) { loadConfiguration(); }, // reload configuration from server
        'info': null // click on user defined tandas
    };
</script>