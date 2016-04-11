<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Chrono");
if ( ! $am->allowed(ENABLE_CHRONO)) { include_once("unregistered.html"); return 0;}
$linfo=$am->getRegistrationInfo();
?>
<!--
chrono.inc

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<!-- Pantalla simulador de cronometro -->
	<div id="chrono_Screen-dialog">
		<div id="chrono_common" style="font-size:2.0em;display:inline-block;width:100%">
			<!-- logotipo del club organizador -->
			<span id="chrono_Club" style="display:none" ></span>
			<img id="chrono_LogoClub" alt="Logo" src="/agility/images/logos/agilitycontest.png" width="80" height="80" class="chrono_logo"/>
			<!-- Recuadros de decoracion -->
			<span class="chrono_fondo chrono_flogo" id="chrono_LogoClubBg">&nbsp;</span>
			<span class="chrono_fondo chrono_fheader" id="chrono_PruebaBg">&nbsp;</span>
			<span class="chrono_fondo chrono_ftiempo" id="chrono_TiempoBg">&nbsp;</span>
			<span class="chrono_fondo chrono_fdata" id="chrono_ResultadosBg">&nbsp;</span>
			<span class="chrono_fondo chrono_finfo" id="chrono_DatosBg">&nbsp;</span>
			<!-- Informacion de la prueba -->
			<span class="chrono_info" id="chrono_PruebaLbl">
				<em><?php _e('Contest data');?> - <?php _e('Journey data');?> - <?php _e('Series data');?></em>
			</span>
			<!-- Datos de resultados -->
			<span class="chrono_data chrono_dataLbl" id="chrono_FaltasLbl"><?php _e('F');?>:</span>
			<span class="chrono_data"  id="chrono_Faltas">0</span>
			<span class="chrono_data chrono_dataLbl" id="chrono_TocadosLbl"><?php _e('T');?>:</span>
			<span class="chrono_data"  id="chrono_Tocados">0</span>
			<span class="chrono_data chrono_dataLbl" id="chrono_RehusesLbl"><?php _e('R');?>:</span>
			<span class="chrono_data" id="chrono_Rehuses">0</span>
			<span class="chrono_data" id="chrono_EliminadoLbl"></span>
			<span id="chrono_Eliminado" style="display:none">0</span>
			<span class="chrono_data" id="chrono_NoPresentadoLbl"></span>
			<span id="chrono_NoPresentado" style="display:none">0</span>
			<span class="chrono_data" id="chrono_PuestoLbl"></span>
			<span style="display:none" id="chrono_Puesto"></span>
			<!-- Cronometro -->
			<span class="chrono_flags" id="chrono_Manual"></span>
			<span class="chrono_flags" id="chrono_Intermedio"></span>
			<span class="chrono_flags" id="chrono_Error"></span>
			<span class="chrono_flags" id="chrono_Reconocimiento"></span>
			<span class="chrono_tiempo" id="chrono_Tiempo">00.00</span>
			<!-- Informacion del participante -->
			<img id="chrono_Logo" alt="Logo" src="/agility/images/logos/agilitycontest.png" width="80" height="80" class="chrono_logo"/>
			<span class="chrono_info" id="chrono_Dorsal"><?php _e('Dorsal');?></span>
			<span class="chrono_info" id="chrono_Nombre"><?php _e('Dog');?></span>
			<span class="chrono_info" id="chrono_NombreGuia"><?php _e('Handler');?></span>
			<span class="chrono_info" id="chrono_NombreClub"><?php _e('Club');?></span>
			<span class="chrono_info" id="chrono_Categoria"><?php _e('Category');?></span>
			<span class="chrono_info" id="chrono_Grado"><?php _e('Grade');?></span>
			<span class="chrono_info" id="chrono_Celo"><?php _e('Heat');?></span>
    		<span id="chrono_timestamp" style="display:none"></span>
			<span id="chrono_Perro" style="display:none" ></span>
			<span id="chrono_Cat" style="display:none" ></span>
		</div>
	</div>

<div id="chrono-simButtons">
	<div id="chrono-copyright" style="width:100%;display:none">
		<span style="float:left;padding:5px;">
			<em>AgilityContest-<?php echo $config->getEnv('version_name'); ?>. &copy; 2015 by JAMC</em>
		</span>
		<span style="float:right;padding:5px;">
			<em><?php _e('Copy licensed to club');?>: <?php echo $linfo['Club']; ?></em>
		</span>
	</div>
	<div id="chrono-buttons" style="width:100%;display:inline-block">
		<span style="float:left;padding:5px;">
   			<a id="chrono-recBtn" href="#" class="easyui-linkbutton"
   			   	data-options="iconCls: 'icon-huella'" onclick="chrono_rec();"><?php _e('Course walk'); ?></a>
   			<a id="chrono-fltBtn" href="#" class="easyui-linkbutton"
   			   	data-options="iconCls: 'icon-hand'" onclick="chrono_button('Faltas')"><?php _e('Fault'); ?></a>
   			<a id="chrono-rehBtn" href="#" class="easyui-linkbutton"
			   data-options="iconCls: 'icon-fist'" onclick="chrono_button('Rehuses')"><?php _e('Refusal'); ?></a>
   			<a id="chrono-elimBtn" href="#" class="easyui-linkbutton"
   			   	data-options="iconCls: 'icon-undo'" onclick="chrono_button('Eliminado')"><?php _e('Eliminated'); ?></a>
   			<a id="chrono-errorBtn" href="#" class="easyui-linkbutton"
			   data-options="iconCls: 'icon-alert'" onclick="chrono_markError()"><?php _e('Error'); ?></a>
   			<a id="chrono-resetBtn" href="#" class="easyui-linkbutton"
			   data-options="iconCls: 'icon-undo'" onclick="chrono_sensor('crono_reset',{},1000)"><?php _e('Reset'); ?></a>
		</span>
		<span style="float:right;padding:5px;">
   			<a id="chrono-countDownBtn" href="#" class="easyui-linkbutton"
			   data-options="iconCls: 'icon-whistle'" onclick="chrono_sensor('salida',{},1000);"><?php _e('CountDown'); ?></a>
   			<a id="chrono-startBtn" href="#" class="easyui-linkbutton"
			   data-options="iconCls: 'icon-on'" onclick="chrono_sensor('crono_start',{},2000)"><?php _e('Begin'); ?></a>
   			<a id="chrono-intBtn" href="#" class="easyui-linkbutton"
   			   	data-options="iconCls: 'icon-help'" onclick="chrono_sensor('crono_int',{},3000)"><?php _e('Intermediate'); ?></a>
   			<a id="chrono-stopBtn" href="#" class="easyui-linkbutton"
   			   	data-options="iconCls: 'icon-off'" onclick="chrono_sensor('crono_stop',{},4000)"><?php _e('End'); ?></a>
		</span>
	</div>
</div>	<!-- botones -->

<!-- declare a tag to attach a chrono object to -->
<div id="cronoauto"><span id="chrono_StartStopFlag" style="display:none">Start</span></div>
		
<script type="text/javascript">

// create a Chronometer instance
$('#cronoauto').Chrono( {
	seconds_sel: '#chrono_timestamp',
	auto: false,
	interval: 50,
	showMode: 2,
	onBeforePause: function() { $('#chrono_Intermedio').text('<?php _e("Intermediate");?>').addClass('blink'); return true; },
	onBeforeResume: function() { $('#chrono_Intermedio').text('').removeClass('blink'); return true; },
	onUpdate: function(elapsed,running,paused) {
		$('#chrono_Tiempo').html(toFixedT(parseFloat(elapsed/1000.0),(running)?1:ac_config.numdecs));
		return true;
	}
});

$('#chrono_Screen-dialog').dialog({
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:false,
	maximizable:false,
	maximized:true,
	onOpen: function() {
		startEventMgr(workingData.sesion,chrono_processEvents);
		bindKeysToChrono();
	},
	buttons:'#chrono-simButtons'
});

// buttons
addTooltip($('#chrono-fltBtn').linkbutton(),"<?php _e('Send fault/touch from chrono remote control');?>");
addTooltip($('#chrono-rehBtn').linkbutton(),"<?php _e('Send refusal from chrono remote control');?>");
addTooltip($('#chrono-elimBtn').linkbutton(),"<?php _e('Send eliminated from chrono remote control');?>");
addTooltip($('#chrono-recBtn').linkbutton(),"<?php _e('Start/stop 7 minutes course walk');?>");
addTooltip($('#chrono-startBtn').linkbutton(),"<?php _e('Start chronometer');?>");
addTooltip($('#chrono-intBtn').linkbutton(),"<?php _e('Mark intermediate time');?>");
addTooltip($('#chrono-stopBtn').linkbutton(),"<?php _e('Stop chronometer');?>");
addTooltip($('#chrono-errorBtn').linkbutton(),"<?php _e('Simulate chrono sensors alignment failure');?>");
addTooltip($('#chrono-resetBtn').linkbutton(),"<?php _e('Reset chronometer. Set count to zero');?>");
// addTooltip($('#chrono-countDownBtn').linkbutton(),"<?php _e('Start 15 seconds countdown');?>");
$('#chrono-countDownBtn').linkbutton(); // mouseover stops timer on tooltip hiding. REVISE IT

// layout
var layout= {'cols':800, 'rows':300}; // declare base datagrid as A5 sheet

doLayout(layout,"#chrono_LogoClubBg",	685,	2,		110,	75	);
doLayout(layout,"#chrono_PruebaBg",		5,		2,		675,	17	);
doLayout(layout,"#chrono_TiempoBg",		5,		25,		675,	180	);
doLayout(layout,"#chrono_ResultadosBg",	685,	80,		110,	125	);
doLayout(layout,"#chrono_DatosBg",		5,		210,	790,	85	);

doLayout(layout,"#chrono_PruebaLbl",	10,		5,		665,	17	);
doLayout(layout,"#chrono_LogoClub",		695,	10,		95,		60	);

doLayout(layout,"#chrono_FaltasLbl",	700,	100,	50,		25	);
doLayout(layout,"#chrono_TocadosLbl",	700,	125,	50,		25	);
doLayout(layout,"#chrono_RehusesLbl",	700,	150,	50,		25	);
doLayout(layout,"#chrono_Faltas",		750,	100,	35,		25	);
doLayout(layout,"#chrono_Tocados",		750,	125,	35,		25	);
doLayout(layout,"#chrono_Rehuses",		750,	150,	35,		25	);
// same location for elim, np and puesto
doLayout(layout,"#chrono_EliminadoLbl",	700,	175,	85,		25	);
doLayout(layout,"#chrono_NoPresentadoLbl",	700,	175,	85,		25	);
doLayout(layout,"#chrono_PuestoLbl",	700,	175,	85,		25	);

doLayout(layout,"#chrono_Manual",		600,	30,	    55, 	10	);
doLayout(layout,"#chrono_Intermedio",	580,	40,	    75, 	10	);
doLayout(layout,"#chrono_Reconocimiento",455,	160,	215, 	10	);
doLayout(layout,"#chrono_Error",		560,	170,    115, 	10	);
doLayout(layout,"#chrono_Tiempo",		5,		100,	665, 	90	);

doLayout(layout,"#chrono_Logo",			10,		215,	80,		55	);
doLayout(layout,"#chrono_Dorsal",		100,	225,	100,	25	);
doLayout(layout,"#chrono_Nombre",		220,	225,	230,	25	);
doLayout(layout,"#chrono_Categoria",	450,	225,	200,	25	);
doLayout(layout,"#chrono_Grado",		650,	225,	125,	25	);
doLayout(layout,"#chrono_NombreGuia",	100,	250,	350,	20	);
doLayout(layout,"#chrono_NombreClub",	450,	250,	270,	20	);
doLayout(layout,"#chrono_Celo",			720,	250,	55,		20	);

</script>
