<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
require_once(__DIR__ . "/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Videowall::livestream");
if ( ! $am->allowed(ENABLE_LIVESTREAM)) { include_once("unregistered.php"); return 0;}
$combined=http_request("combined","i",0);
?>
<!--
livestream.inc

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


<!-- Pantalla liveStream -->
<div id="vwls_LiveStream-window" style="padding:0px;height:auto;">
	<div id="vwls_LiveStream" class="easyui-panel" style="overflow-x:hidden;overflow-y:hidden"
		data-options="noheader:true,border:false,closable:false,collapsible:false,collapsed:false,resizable:true">
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
		<div id="osd_common" style="display:inline-block;width:100%;">

			<div id="vwls_mangasInfo">
				<!-- Recuadro de informacion de la manga -->
				<span class="vwls_fondo<?php echo ($combined==1)?'_combined':'_chromakey';?>" id="vwls_InfoManga">&nbsp;</span>
				<!-- datos de la manga -->
				<span class="vwls_label" id="vwls_Manga" style="text-align:center;"><?php _e('Round'); ?></span>
			</div>

			<!-- datos de resultados -->
			<div id="vwls_resultadosInfo">
				<!-- Recuadro de decoracion resultados -->
				<span class="vwls_fondo<?php echo ($combined==1)?'_combined':'_chromakey';?>" id="vwls_Resultados">&nbsp;</span>
				<!-- datos de resultados -->
				<span style="display:none"  id="vwls_Faltas">0</span>
				<span style="display:none"  id="vwls_Tocados">0</span>
				<span class="vwls_dlabel" id="vwls_FaltasTocadosLbl"><?php _e('F'); ?>:</span> <!-- should be F/T -->
				<span class="vwls_data"  id="vwls_FaltasTocados">0</span>
				<span class="vwls_dlabel" id="vwls_RehusesLbl"><?php _e('R'); ?>:</span>
				<span class="vwls_data"  id="vwls_Rehuses">0</span>
				<!-- <span class="vwls_dlabel" id="vwls_TiempoLbl">Time</span> -->
				<span class="vwls_dtime"  id="vwls_Tiempo">00.00</span>
				<span style="display:none" id="vwls_TIntermedio">00.000</span>
				<span class="vwls_dtime" id="vwls_EliminadoLbl"></span>
				<span style="display:none" id="vwls_Eliminado">0</span>
				<span class="vwls_dtime" id="vwls_NoPresentadoLbl"></span>
				<span style="display:none" id="vwls_NoPresentado">0</span>
				<span class="vwls_dtime" id="vwls_PuestoLbl"></span>
				<span style="display:none" id="vwls_Puesto"></span>
			</div>

			<!-- Informacion del participante -->
			<div id="vwls_competitorInfo">
				<!-- recuadro de decoracion de datos del competidor en pist -->
				<span class="vwls_fondo<?php echo ($combined==1)?'_combined':'_chromakey';?>" id="vwls_Datos">&nbsp;</span>
				<!-- call to ring order number (first:1) -->
				<span class="vwls_label vwls_dorsal" id="vwls_Numero"><?php _e('Number'); ?></span>
				<!-- logogipo -->
				<img id="vwls_Logo" alt="Logo" src="/agility/images/logos/agilitycontest.png" width="70" height="70" class="vwls_logo"/>
				<span class="vwls_label" id="vwls_Dorsal"><?php _e('Dorsal'); ?></span>
				<span class="vwls_label" id="vwls_Nombre"><?php _e('Name'); ?></span>
				<span class="vwls_label" id="vwls_NombreGuia"><?php _e('Handler'); ?></span>
				<span class="vwls_label" id="vwls_NombreClub"><?php _e('Club'); ?></span>
				<span class="vwls_label" id="vwls_Categoria" style="display:none"><?php _e('Category'); ?></span>
				<span class="vwls_label" id="vwls_Grado" style="display:none"><?php _e('Grade'); ?></span>
				<span class="vwls_label" id="vwls_Celo"><?php _e('Heat'); ?></span>
				<span style="display:none" id="vwls_Perro">0</span>
				<span style="display:none" id="vwls_Cat">-</span>
			</div>
		</div>
	</div>
</div>

<!-- declare a tag to attach a chrono object to -->
<div id="cronometro">
	<span id="vwls_StartStopFlag" style="display:none">Start</span>
	<span style="display:none" id="vwls_timestamp"></span>
</div>
		
<script type="text/javascript">

// create a Chronometer instance
$('#cronometro').Chrono( {
	seconds_sel: '#vwls_timestamp',
	auto: false,
	interval: 50,
	showMode: 2,
	onUpdate: function(elapsed,running,pause) { 
		$('#vwls_Tiempo').html(toFixedT(parseFloat(elapsed/1000),(running)?1:ac_config.numdecs));
		return true;
	},
	onBeforePause:function() { $('#vwls_Tiempo').addClass('blink'); return true; },
	onBeforeResume: function() { $('#vwls_Tiempo').removeClass('blink'); return true; },
	onBeforeReset: function() { $('#vwls_Tiempo').removeClass('blink'); return true; }
});

$('#vwls_LiveStream-window').window({
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

// layout
var layout= {'cols':800, 'rows':450}; // declare base datagrid as A5 sheet

// layout for results box
switch (parseInt(ac_config.ls_dataposition)) {
	case 1: // upper left corner
		doLayout(layout,"#vwls_Resultados",		740,	10,		40,		85 ); // background box
		doLayout(layout,"#vwls_FaltasTocadosLbl",750,	15,		12,		15	);
		doLayout(layout,"#vwls_FaltasTocados",	762,	15,		10,		15	);
		doLayout(layout,"#vwls_RehusesLbl",		750,	30,		12,		15	);
		doLayout(layout,"#vwls_Rehuses",		762,	30,		10,		15	);
		// doLayout(layout,"#vwls_TocadosLbl",	750,	45,	    12,		15	);
		// doLayout(layout,"#vwls_Tocados",		762,	45,	    10,		15	);
		doLayout(layout,"#vwls_Tiempo",			742,	60,     35,		15	);
		doLayout(layout,"#vwls_EliminadoLbl",	745,	75,     30,		15	);
		doLayout(layout,"#vwls_NoPresentadoLbl",745,	75,     30,		15	);
		doLayout(layout,"#vwls_PuestoLbl",		745,	75,     30,		15	);
		// fix font size to allow miliseconds
		$("#vwls_Tiempo").css('font-size','1.1vw');
		$("#vwls_EliminadoLbl").css('font-size','1.1vw');
		$("#vwls_NoPresentadoLbl").css('font-size','1.1vw');
		$("#vwls_PuestoLbl").css('font-size','1.1vw');
		break;
	case 2: // lower left corner
		doLayout(layout,"#vwls_Resultados",		690,	410,	90,	30 ); // background box
		doLayout(layout,"#vwls_FaltasTocadosLbl",700,	412,	10,		15	);
		doLayout(layout,"#vwls_FaltasTocados",	710,	412,	10,		15	);
		doLayout(layout,"#vwls_RehusesLbl",		725,	412,	10,		15	);
		doLayout(layout,"#vwls_Rehuses",		735,	412,	10,		15	);
		// doLayout(layout,"#vwls_TocadosLbl",		750,	412,    10,		15	);
		// doLayout(layout,"#vwls_Tocados",		760,	412,    10,		15	);
		doLayout(layout,"#vwls_Tiempo",			735,	425,    35,		15	);
		doLayout(layout,"#vwls_EliminadoLbl",	700,	425,    30,		15	);
		doLayout(layout,"#vwls_NoPresentadoLbl",700,	425,    30,		15	);
		doLayout(layout,"#vwls_PuestoLbl",		700,	425,    30,		15	);
		break;
	case 3: // lower centered next to competitors data
		doLayout(layout,"#vwls_Resultados",		405,	410,	90,	30 ); // background box
		doLayout(layout,"#vwls_FaltasTocadosLbl",415,	412,	10,		15	);
		doLayout(layout,"#vwls_FaltasTocados",			425,	412,	10,		15	);
		doLayout(layout,"#vwls_RehusesLbl",		440,	412,	10,		15	);
		doLayout(layout,"#vwls_Rehuses",		450,	412,	10,		15	);
		// doLayout(layout,"#vwls_TocadosLbl",	465,	412,    10,		15	);
		// doLayout(layout,"#vwls_Tocados",		475,	412,    10,		15	);
		doLayout(layout,"#vwls_Tiempo",			450,	425,    35,		15	);
		doLayout(layout,"#vwls_EliminadoLbl",	410,	425,    35,		15	);
		doLayout(layout,"#vwls_NoPresentadoLbl",410,	425,    35,		15	);
		doLayout(layout,"#vwls_PuestoLbl",		410,	425,    35,		15	);
		break;
	default: vwls_showResultsInfo(0); // desactiva visualizacion de resultados
		break;
}

// data for competitor box
doLayout(layout,"#vwls_Datos",			10,		410,	390,	30 ); // background box
doLayout(layout,"#vwls_Numero",			20,		416,	30,		50	);
doLayout(layout,"#vwls_Logo",			50,		365,	70,		70	); // may be redefined later for intl contests
doLayout(layout,"#vwls_Dorsal",			125,	412,	25,		20	);
doLayout(layout,"#vwls_Nombre",			150,	412,	220,	20	);
doLayout(layout,"#vwls_NombreGuia",		125,	425,	140,	20	);
doLayout(layout,"#vwls_NombreClub",		265,	425,	145,	20	);
doLayout(layout,"#vwls_Celo",			370,	412,	30,		20	);
// doLayout(layout,"#vwls_Grado",			400,	415,	100,	20	); // already shown in infomanga
// doLayout(layout,"#vwls_Categoria",		510,	412,	140,	20	); // already shown in infomanga

// data for infomanga box
switch (parseInt(ac_config.ls_infoposition)) {
	case 0: // hidden
		$("#vwls_InfoManga").css('display','none');
		$("#vwls_Manga").css('display','none');
		break;
	case 1: // screen top
		doLayout(layout, "#vwls_InfoManga", 15, 10, 165, 20 ); // transparent boxes for infomanga
		doLayout(layout, "#vwls_Manga", 20, 14, 155, 15);
		break;
	case 2: // on top of competitor box
		// $("#vwls_InfoManga").css('z-index',1);
		// $("#vwls_Manga").css('z-index',1);
		doLayout(layout, "#vwls_InfoManga", 125, 391, 170, 20 ); // transparent boxes for infomanga
		doLayout(layout, "#vwls_Manga", 130, 396, 160, 15);
		break;
}

var eventHandler= {
	'null': null,// null event: no action taken
	'init': function(event,time) { // operator starts tablet application
		setupWorkingData(event['Pru'],event['Jor'],(event['Mng']>0)?event['Mng']:1); // use shortname to ensure data exists
		vwls_enableOSD(0); 	// activa visualizacion de OSD
		vwls_keyBindings(); // capture <space> to show/hide osd
	},
	'open': function(event,time){ // operator select tanda
		vwls_enableOSD(1); 	// activa visualizacion de OSD
		vwls_showRoundInfo(1); // activa visualizacion de datos de la manga
		vwls_showCompetitorInfo(0); // desactiva visualizacion de datos del competidor
		vwls_showResultsInfo(0); // desactiva visualizacion de resultados
		vw_updateWorkingData(event,function(e,d){
			if (isInternational(null)) // decrease logo height to match "country flag" aspect sze
				 doLayout(layout,"#vwls_Logo",			50,		390,	70,		45	);
			else doLayout(layout,"#vwls_Logo",			50,		365,	70,		70	);
			vw_updateHeaderAndFooter(e,d);
		});
	},
	'close': function(event,time){ // no more dogs in tabla
		ac_config.dogInRing=false;
		vwls_enableOSD(0); // apaga el OSD
	},
	'datos': function(event,time) {      // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		vwls_updateData(event);
	},
	'llamada': function(event,time) {    // llamada a pista
		ac_config.dogInRing=true;
		// do not stop nor reset chrono in 'aceptar' or 'llamada'. Just stop countdown
		var crm=$('#cronometro');
		myCounter.stop();
		// crm.Chrono('stop',time);
		// crm.Chrono('reset',time);
		vwls_enableOSD(1); 	// activa visualizacion de OSD
		vwls_showRoundInfo(1); // activa visualizacion de datos de la manga
		vwls_showCompetitorInfo(1); // activa visualizacion de datos del competidor
		vwls_showResultsInfo(crm.Chrono('started')); // desactiva visualizacion de resultados si crono parado
		vwls_showData(event);
	},
	'salida': function(event,time){     // orden de salida
		vwls_displayPuesto(false,0); // clear puesto
		vwls_showResultsInfo(1); // activa visualizacion de datos del competidor
		if (ac_config.ls_tobefirst!=="0") vwls_displayToBeFirst(event['Perro']);
		myCounter.start();
	},
	'start': function(event,time) {      // start crono manual
		vwls_displayPuesto(false,0); // clear puesto
		vwls_showCompetitorInfo(1); // muestra datos del competidor
		vwls_showResultsInfo(1); // activa visualizacion de resultados
		// si crono automatico, ignora
		var ssf=$('#vwls_StartStopFlag');
		if (ssf.text()==="Auto") return;
		ssf.text("Stop");
		myCounter.stop(); // stop 15 seconds countdown if needed
		var crm=$('#cronometro');
		crm.Chrono('stop',time);
		crm.Chrono('reset');
		crm.Chrono('start',time);
	},
	'stop': function(event,time){      // stop crono manual
		var crm=$('#cronometro');
		$('#vwls_StartStopFlag').text("Start");
		myCounter.stop();
		crm.Chrono('stop',time);
		vwls_displayPuesto(true,crm.Chrono('getValue')/1000);
	},
	// nada que hacer aqui: el crono automatico se procesa en el tablet
	'crono_start':  function(event,time){ // arranque crono automatico
		vwls_displayPuesto(false,0);
		vwls_showCompetitorInfo(1); // muestra datos del competidor
		vwls_showResultsInfo(1); // activa visualizacion de resultados
		var crm=$('#cronometro');
		myCounter.stop();
		$('#vwls_StartStopFlag').text('Auto');
		// si esta parado, arranca en modo automatico
		if (!crm.Chrono('started')) {
			crm.Chrono('stop',time);
			crm.Chrono('reset');
			crm.Chrono('start',time);
			return
		}
		if (ac_config.crono_resync==="0") {
			crm.Chrono('reset'); // si no resync, resetea el crono y vuelve a contar
			crm.Chrono('start',time);
		} // else wait for chrono restart event
	},
	'crono_restart': function(event,time){	// paso de tiempo manual a automatico
		$('#cronometro').Chrono('resync',event['stop'],event['start']);
	},
	'crono_int':  	function(event,time){	// tiempo intermedio crono electronico
		var crm=$('#cronometro');
		if (!crm.Chrono('started')) return;	// si crono no esta activo, ignorar
		crm.Chrono('pause',time);
        setTimeout(function(){crm.Chrono('resume');},5000);
	},
	'crono_stop':  function(event,time){	// parada crono electronico
		var crm=$('#cronometro');
		$('#vwls_StartStopFlag').text("Start");
		crm.Chrono('stop',time);
		vwls_displayPuesto(true,crm.Chrono('getValue')/1000);
	},
	'crono_reset':  function(event,time){	// puesta a cero del crono electronico
		var crm=$('#cronometro');
		myCounter.stop();
		$('#vwls_StartStopFlag').text("Start");
		crm.Chrono('stop',time);
		crm.Chrono('reset',time);
		vwls_displayPuesto(false,0);
	},
	'crono_dat': function(event,time) {      // actualizar datos -1:decrease 0:ignore 1:increase
		vwls_updateChronoData(event);
	},
	'crono_error':  null, // fallo en los sensores de paso
	'crono_ready':  null, // estado del crono
	'aceptar':	function(event,time){ // operador pulsa aceptar
		myCounter.stop(); // not really needed, but...
	},
	'cancelar': function(event,time){  // operador pulsa cancelar
		ac_config.dogInRing=false;
		var crm=$('#cronometro');
		myCounter.stop();
		crm.Chrono('stop',time);
		crm.Chrono('reset',time);
		vwls_showRoundInfo(1); // activa visualizacion de datos de la manga
		vwls_showCompetitorInfo(0); //oculta datos del competidor
		vwls_showResultsInfo(0); // oculta visualizacion de resultados
	},
	'camera':	null, // change video source
    'command': function(event){ // livestream remote control
        handleCommandEvent(
            event,
            [
                /* EVTCMD_NULL:         */ function(e) { console.log("Received null command"); },
                /* EVTCMD_SWITCH_SCREEN:*/ function(e) { livestream_switchConsole(e); },
                /* EVTCMD_SETFONTFAMILY:*/ null,
                /* EVTCMD_NOTUSED3:     */ null,
                /* EVTCMD_SETFONTSIZE:  */ null,
                /* EVTCMD_NOTUSED5:     */ null,
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
