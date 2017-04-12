<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
require_once(__DIR__ . "/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Videowall::parciales");
if ( ! $am->allowed(ENABLE_VIDEOWALL)) { include_once("unregistered.php"); return 0;}
$combined=http_request("combined","i",0);
?>
<!--
vw_anuncios.php

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

<!-- Presentacion de anuncios promocionales en pantalla -->

<div id="vw_anuncios-window">
        <!-- http://rolandocaldas.com/html5/video-de-fondo-en-html5 -->
        <video id="vw_video" autoplay="autoplay" preload="auto" muted="muted"
               loop="loop" poster="/agility/server/getRandomImage.php" style="width=100%;height:auto">
            <!-- http://guest:@192.168.122.168/videostream.cgi -->
            <source id="vw_videomp4" src="" type='video/mp4'/>
            <source id="vw_videoogv" src="" type='video/ogg'/>
            <source id="vw_videowebm" src="" type='video/webm'/>
        </video>

</div> <!-- vw_anuncios-window -->

<script type="text/javascript">

$('#vw_anuncios-window').window({
    fit:true,
    noheader:true,
    border:false,
    closable:false,
    collapsible:false,
    collapsed:false,
    resizable:true,
    callback: null,
    onOpen: function() {
        // start event manager to receive events
        startEventMgr();
        // start video playback
        var bg=workingData.datosSesion.Background;
        var ls1=workingData.datosSesion.LiveStream;
        var ls2=workingData.datosSesion.LiveStream2;
        var ls3=workingData.datosSesion.LiveStream3;
        if ( bg !== '' ) $('#vw_video').attr('poster', bg);
        if ( ls1!== '' ) $('#vw_videomp4').attr('src', ls1); else $('#vw_videomp4').remove();
        if ( ls2!== '' ) $('#vw_videoogv').attr('src', ls2); else $('#vw_videoogv').remove();
        if ( ls3!== '' ) $('#vw_videowebm').attr('src', ls3); else $('#vw_videowebm').remove();
        // if LiveStream is present load and play assigned session's livestream url
        var video=$('#vwls_video')[0];
        if (!video) return; // no video tag found
        video.load();
        video.play();
    }
});

var eventHandler= {
    'null': null,// null event: no action taken
    'init': function(event) { return false; /* to be filled later */  },
    'open': function(event){ return false; /* to be filled later */  },
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
    'reconfig':	function(event) { loadConfiguration(); return false; }, // reload configuration from server
    'info':	null // click on user defined tandas
};

</script>