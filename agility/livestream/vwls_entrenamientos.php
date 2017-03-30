<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
require_once(__DIR__ . "/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Videowall::entrenamientos");
if ( ! $am->allowed(ENABLE_LIVESTREAM)) { include_once("unregistered.php"); return 0;}
if ( ! $am->allowed(ENABLE_TRAINING)) { include_once("trainingnotallowed.php"); return 0;}
$combined=http_request("combined","i",0);
?>
<!--
vwls_entrenamientos.inc

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

<!-- Presentacion de la sesion de entrenamientos a traves de  videostream -->

<div id="vw_entrenamientos-window">

    <div id="vw_parent-layout" class="easyui-layout" style="width:100%;height:auto;">

        <?php if ($combined==1) { ?>
            <!-- http://rolandocaldas.com/html5/video-de-fondo-en-html5 -->
            <video id="vwls_video" autoplay="autoplay" preload="auto" muted="muted"
                   loop="loop" poster="/agility/server/getRandomImage.php" style="width=100%;height:auto">
                <!-- http://guest:@192.168.122.168/videostream.cgi -->
                <source id="vwls_videomp4" src="" type='video/mp4'/>
                <source id="vwls_videoogv" src="" type='video/ogg'/>
                <source id="vwls_videowebm" src="" type='video/webm'/>
            </video>
        <?php } else { ?>
            <img src="/agility/server/getChromaKeyImage.php" style="z-index:-1;" />
        <?php } ?>

        <div data-options="region:'east',split:false,border:false" style="width:5%;background-color:transparent;"></div>
        <div data-options="region:'west',split:false,border:false" style="width:30%;background-color:transparent;"></div>
        <div data-options="region:'center',border:false" style="background-color:transparent;">
        <!-- ventana interior -->
            <div id="vwls_common" style="display:inline-block;width:100%;height:auto">
                <div id="vw_entrenamientos-Cabecera" data-options="region:'north',split:false" class="vw_floatingheader"
                      style="height:75px;font-size:1.0em;" >
                    <span style="float:left;background:rgba(255,255,255,0.5);">
                        <img id="vw_header-logo" src="/agility/images/logos/rsce.png" width="50"/>
                    </span>
                    <span style="float:left;padding:10px" id="vw_header-infoprueba"><?php _e('Header'); ?></span>

                    <div style="float:right;padding:10px;text-align:right;">
                        <span id="vw_header-texto"><?php _e('Training session'); ?></span>
                        <span id="vw_header-ring" style="display:none"><?php _e('Ring'); ?></span>
                        <br />
                        <span id="vw_header-infomanga" style="display:none">(<?php _e('No round selected'); ?>)</span>
                    </div>

                </div>

                <div id="vw_tabla" data-options="region:'center'">
                    <?php include_once(__DIR__."/../lib/templates/entrenamientos.inc.php");?>
                </div>

                <div id="vw_entrenamientos-footer" data-options="region:'south',split:false" class="vw_floatingfooter"
                    style="font-size:1.2em;">
                    <span id="vw_footer-footerData"></span>
                </div>
            </div>
        </div>
    </div>

</div> <!-- vw_entrenamientos-window -->

<script type="text/javascript">

$('#vw_parent-layout').layout({fit:true});
$('#vwls_common').layout({fit:true});

$('#vw_entrenamientos-window').window({
    fit:true,
    noheader:true,
    border:false,
    closable:false,
    collapsible:false,
    collapsed:false,
    resizable:true,
    callback: null,
    onOpen: function() {
        startEventMgr();
    }
});

$('#entrenamientos-datagrid').datagrid({
    onBeforeLoad:function(params) {
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='<?php _e('Header'); ?>') return false;
        return true;
    }
});

var eventHandler= {
    'null': null,// null event: no action taken
    'init': function(event) { // operator starts tablet application
        vwls_keyBindings(); // capture keyboard
        vwls_enableOSD(1); // by default screen is visible
        vw_updateWorkingData(event,function(evt,data){
            var dg=$('#entrenamientos-datagrid');
            vw_updateHeaderAndFooter(evt,data,false);
            vw_setTrainingLayout(dg,'livestream');
        });
    },
    'open': function(event){ // operator select tanda
        vw_updateWorkingData(event,function(evt,data){
            // not really needed, but to avoid to reload tablet when livestream restarts
            var dg=$('#entrenamientos-datagrid');
            vw_updateHeaderAndFooter(evt,data,false);
            vw_setTrainingLayout(dg,'livestream');
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
    'crono_dat':  null, // datos desde crono
    'crono_ready':  null, // datos desde crono
    'aceptar':	null, // operador pulsa aceptar
    'cancelar': null, // operador pulsa cancelar
    'camera':	null, // change video source
    'command': function(event){ // videowall remote control
        handleCommandEvent(
            event,
            [
                /* EVTCMD_NULL:         */ function(e) { console.log("Received null command"); },
                /* EVTCMD_SWITCH_SCREEN:*/ function(e) { livestream_switchConsole(e); },
                /* EVTCMD_NEXTFONT:     */ null,
                /* EVTCMD_PREVFONT:     */ null,
                /* EVTCMD_INCFONTSIZE:  */ null,
                /* EVTCMD_DECFONTSIZE:  */ null,
                /* EVTCMD_SETDELAY:     */ function(e) { vwls_setDelayOSD(e['Value']); },
                /* EVTCMD_NOTUSED:     */ null,
                /* EVTCMD_MESSAGE:      */ function(e) { livestream_showMessage(e); },
                /* EVTCMD_ENABLEOSD:    */ function(e) { vwls_enableOSD(e['Value']); }
            ]
        )
    },
    'reconfig':	function(event) { loadConfiguration(); }, // reload configuration from server
    'info':	null // click on user defined tandas
};

</script>