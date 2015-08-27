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
			<img id="chrono_LogoClub" alt="Logo" src="/agility/images/logos/rsce.png" width="80" height="80" class="chrono_logo"/>
			<!-- Recuadros de decoracion -->
			<span class="chrono_fondo chrono_fheader" id="chrono_PruebaBg">&nbsp;</span>
			<span class="chrono_fondo chrono_ftiempo" id="chrono_TiempoBg">&nbsp;</span>
			<span class="chrono_fondo chrono_fdata" id="chrono_ResultadosBg">&nbsp;</span>
			<span class="chrono_fondo chrono_finfo" id="chrono_DatosBg">&nbsp;</span>
			<!-- Informacion de la prueba -->
			<span class="chrono_info" id="chrono_PruebaLbl"><em>Datos de la prueba - Datos de la jornada - Datos de la manga</em></span>
			<!-- Datos de resultados -->
			<span class="chrono_data chrono_dataLbl" id="chrono_FaltasLbl">F:</span>
			<span class="chrono_data"  id="chrono_Faltas">0</span>
			<span class="chrono_data chrono_dataLbl" id="chrono_TocadosLbl">T:</span>
			<span class="chrono_data"  id="chrono_Tocados">0</span>
			<span class="chrono_data chrono_dataLbl" id="chrono_RehusesLbl">R:</span>
			<span class="chrono_data"  id="chrono_Rehuses">0</span>
			<!-- Cronometro -->
			<span class="chrono_flags" id="chrono_Manual"></span>
			<span class="chrono_flags" id="chrono_Intermedio"></span>
			<span class="chrono_tiempo" id="chrono_Tiempo">00.00</span>
			<!-- Informacion del participante -->
			<img id="chrono_Logo" alt="Logo" src="/agility/images/logos/rsce.png" width="80" height="80" class="chrono_logo"/>
			<span class="chrono_info" id="chrono_Dorsal">Dorsal</span>
			<span class="chrono_info" id="chrono_Nombre">Nombre</span>
			<span class="chrono_info" id="chrono_NombreGuia">Gu&iacute;a</span>
			<span class="chrono_info" id="chrono_NombreClub">Club</span>
			<span class="chrono_info" id="chrono_Categoria">Categor&iacute;a</span>
			<span class="chrono_info" id="chrono_Grado">Grado</span>
			<span class="chrono_info" id="chrono_Celo">Celo</span>
    		<span id="chrono_timestamp" style="display:none"></span>
			<span id="chrono_Perro" style="display:none" ></span>
		</div>
	</div>

<div id="chrono-simButtons">
	<div id="chrono-copyright" style="width:100%;display:none">
		<span style="float:left;padding:5px;">
			<em>AgilityContest-<?php echo $config->getEnv('version_name'); ?>. &copy; 2015 by JAMC</em>
		</span>
		<span style="float:right;padding:5px;">
			<em>Copia licenciada para el club: <?php echo $linfo['Club']; ?></em>
		</span>
	</div>
	<div id="chrono-buttons" style="width:100%;display:inline-block">
		<span style="float:left;padding:5px;">
   			<a id="chrono-recBtn" href="#" class="easyui-linkbutton"
   			   	data-options="iconCls: 'icon-huella'" onclick="chrono_button('crono_rec',{})">Reconocimiento</a>
   			<a id="chrono-fltBtn" href="#" class="easyui-linkbutton"
   			   	data-options="iconCls: 'icon-hand'" onclick="chrono_button('crono_dat',{'Falta':1})">Falta</a>
   			<a id="chrono-rehBtn" href="#" class="easyui-linkbutton"
   			   	data-options="iconCls: 'icon-fist'" onclick="chrono_button('crono_dat',{'Rehuse':1})">Reh&uacute;se</a>
   			<a id="chrono-elimBtn" href="#" class="easyui-linkbutton"
   			   	data-options="iconCls: 'icon-undo'" onclick="chrono_button('crono_dat',{'Eliminado':1})">Eliminado</a>
		</span>
		<span style="float:right;padding:5px;">
   			<a id="chrono-startBtn" href="#" class="easyui-linkbutton"
   			   	data-options="iconCls: 'icon-on'" onclick="chrono_sensor('crono_start',{},4000)">Inicio</a>
   			<a id="chrono-intBtn" href="#" class="easyui-linkbutton"
   			   	data-options="iconCls: 'icon-help'" onclick="chrono_sensor('crono_int',{},4000)">Intermedio</a>
   			<a id="chrono-stopBtn" href="#" class="easyui-linkbutton"
   			   	data-options="iconCls: 'icon-off'" onclick="chrono_sensor('crono_stop',{},4000)">Final</a>
		</span>
	</div>
</div>	<!-- botones -->

<!-- declare a tag to attach a chrono object to -->
<div id="cronoauto"></div>
		
<script type="text/javascript">
	
// create a Chronometer instance
$('#cronoauto').Chrono( {
	seconds_sel: '#chrono_timestamp',
	auto: false,
	interval: 100,
	showMode: 2,
	onBeforePause: function() { $('#chrono_Intermedio').text('Intermedio').addClass('blink'); return true; },
	onBeforeResume: function() { $('#chrono_Intermedio').text('').removeClass('blink'); return true; },
	onUpdate: function(elapsed,running,pause) { 
		$('#chrono_Tiempo').html(parseFloat(elapsed/1000).toFixed((running)?1:2));
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
addTooltip($('#chrono-fltBtn').linkbutton(),"Enviar falta/tocado desde botonera del crono");
addTooltip($('#chrono-rehBtn').linkbutton(),"Enviar rehuse desde botonera del crono");
addTooltip($('#chrono-elimBtn').linkbutton(),"Marcar eliminado desde la botonera del crono");
addTooltip($('#chrono-recBtn').linkbutton(),"7 Minutos de reconocimiento de pista");
addTooltip($('#chrono-startBtn').linkbutton(),"Arrancar el cron&oacute;metro");
addTooltip($('#chrono-intBtn').linkbutton(),"Mostrar tiempo intermedio");
addTooltip($('#chrono-stopBtn').linkbutton(),"Parar el cron&oacute;metro");

// layout
var layout= {'cols':800, 'rows':300}; // declare base datagrid as A5 sheet

doLayout(layout,"#chrono_PruebaBg",		5,		2,		675,	17	);
doLayout(layout,"#chrono_TiempoBg",		5,		25,		675,	180	);
doLayout(layout,"#chrono_ResultadosBg",	685,	80,		110,	125	);
doLayout(layout,"#chrono_DatosBg",		5,		210,	790,	85	);

doLayout(layout,"#chrono_PruebaLbl",	10,		5,		665,	17	);
doLayout(layout,"#chrono_LogoClub",		695,	10,		95,		60	);

doLayout(layout,"#chrono_FaltasLbl",	700,	100,	50,		30	);
doLayout(layout,"#chrono_TocadosLbl",	700,	135,	50,		30	);
doLayout(layout,"#chrono_RehusesLbl",	700,	170,	50,		30	);
doLayout(layout,"#chrono_Faltas",		750,	100,	35,		30	);
doLayout(layout,"#chrono_Tocados",		750,	135,	35,		30	);
doLayout(layout,"#chrono_Rehuses",		750,	170,	35,		30	);

doLayout(layout,"#chrono_Manual",		600,	30,	    55, 	10	);
doLayout(layout,"#chrono_Intermedio",	600,	40,	    55, 	10	);
doLayout(layout,"#chrono_Tiempo",		10,		100,	665, 	90	);

doLayout(layout,"#chrono_Logo",			10,		215,	80,		55	);
doLayout(layout,"#chrono_Dorsal",		100,	225,	100,	25	);
doLayout(layout,"#chrono_Nombre",		200,	225,	250,	25	);
doLayout(layout,"#chrono_Categoria",	450,	225,	200,	25	);
doLayout(layout,"#chrono_Grado",		650,	225,	125,	25	);
doLayout(layout,"#chrono_NombreGuia",	100,	250,	350,	20	);
doLayout(layout,"#chrono_NombreClub",	450,	250,	250,	20	);
doLayout(layout,"#chrono_Celo",			700,	250,	75,		20	);

</script>
