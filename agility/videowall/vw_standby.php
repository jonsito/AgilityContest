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
vw_standby.php

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

<!-- pantalla en negro, sin actividad hasta recibir notificación por control remoto -->

<div id="vw_standby-window" style="background:#000;color:#fff">
    <div id="img" style="width:100px;height:100px;position:fixed">
        <img src="../images/AgilityContest.png" alt="logo" style="width:96px;height:96px;padding:2px;">
    </div>
</div> <!-- vw_standby-window -->

<script type="text/javascript">

    // animated image source code taken from
    // http://stackoverflow.com/questions/13784686/moving-an-image-randomly-around-a-page

    function makeNewPosition($container) {
        // Get viewport dimensions (remove the dimension of the div)
        var h = $container.height() - 100;
        var w = $container.width() - 100;
        var nh = Math.floor(Math.random() * h);
        var nw = Math.floor(Math.random() * w);
        return [nh, nw];
    }

    function calcSpeed(prev, next) {
        var x = Math.abs(prev[1] - next[1]);
        var y = Math.abs(prev[0] - next[0]);
        var greatest = x > y ? x : y;
        var speedModifier = 0.1;
        var speed = Math.ceil(greatest / speedModifier);
        return speed;
    }

    function animateDiv($target) {
        var newq = makeNewPosition($target.parent());
        var oldq = $target.offset();
        var speed = calcSpeed([oldq.top, oldq.left], newq);
        $target.animate({
            top: newq[0],
            left: newq[1]
        }, speed, function() {
            animateDiv($target);
        });
    }

$('#vw_standby-window').window({
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
        setTimeout(function(){ animateDiv($('#img')); },0);
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
    'user':  null, // user defined event
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
                /* EVTCMD_OSDSETDELAY   */ null,
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