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
<div id="chrono_Screen-dialog" style="position:relative;width:820px;height:350px;padding:5px 5px">
	<div id="chrono_common" style="font-size:2.0em;display:inline-block;width:100%">
		<!-- Recuadros de decoracion -->
		<span class="chrono_fondo" id="chrono_TiempoBg">&nbsp;</span>
		<span class="chrono_fondo" id="chrono_ResultadosBg">&nbsp;</span>
		<span class="chrono_fondo" id="chrono_DatosBg">&nbsp;</span>
		<!-- datos de resultados -->
		<span class="chrono_label" id="chrono_FaltasLbl">Faltas:</span>
		<span class="chrono_data"  id="chrono_Faltas">0</span>
		<span class="chrono_label" id="chrono_TocadosLbl">Tocados:</span>
		<span class="chrono_data"  id="chrono_Tocados">0</span>
		<span class="chrono_label" id="chrono_RehusesLbl">Reh&uacute;ses:</span>
		<span class="chrono_data"  id="chrono_Rehuses">0</span>
		<span class="chrono_tiempo" id="chrono_Tiempo">00.00</span>
		<!-- Informacion del participante -->
		<img id="chrono_Logo" alt="Logo" src="/agility/images/logos/rsce.png" width="80" height="80" class="chrono_logo"/>
		<span class="chrono_label" id="chrono_Dorsal">Dorsal</span>
		<span class="chrono_label" id="chrono_Nombre">Nombre</span>
		<span class="chrono_label" id="chrono_NombreGuia">Gu&iacute;a</span>
		<span class="chrono_label" id="chrono_NombreClub">Club</span>
		<span class="chrono_label" id="chrono_Categoria">Categor&iacute;a</span>
		<span class="chrono_label" id="chrono_Grado">Grado</span>
		<span class="chrono_label" id="chrono_Celo">Celo</span>
    	<span id="chrono_timestamp" style="display:none"></span>
		<span id="chrono_Perro" style="display:none" ></span>
	</div>
</div>

<div id="chrono-simButtons" style="text-align:right;width:100%;display:inline-block">
	<span style="float:left;padding:5px;">
   		<a id="chrono-recBtn" href="#" class="easyui-linkbutton" 
   		   	data-options="iconCls: 'icon-huella'" onclick="chrono_reconocimiento()">Reconocimiento</a>
	</span>
	<span style="float:right;padding:5px;">
   		<a id="chrono-startBtn" href="#" class="easyui-linkbutton" 
   		   	data-options="iconCls: 'icon-on'" onclick="chrono_start()">Inicio</a>
   		<a id="chrono-intBtn" href="#" class="easyui-linkbutton" 
   		   	data-options="iconCls: 'icon-help'" onclick="chrono_intermediate()">Intermedio</a>
   		<a id="chrono-stopBtn" href="#" class="easyui-linkbutton" 
   		   	data-options="iconCls: 'icon-off'" onclick="chrono_stop()">Final</a>
	</span>
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
	resizable:true,
	onOpen: function() {
		startEventMgr(workingData.sesion,chrono_processEvents);
	},
	buttons:'#chrono-simButtons'
});

addTooltip($('#chrono-recBtn').linkbutton(),"7 Minutos de reconocimiento de pista");
addTooltip($('#chrono-startBtn').linkbutton(),"Arrancar el cron&oacute;metro");
addTooltip($('#chrono-intBtn').linkbutton(),"Mostrar tiempo intermedio");
addTooltip($('#chrono-stopBtn').linkbutton(),"Parar el cron&oacute;metro");

// layout
var layout= {'cols':800, 'rows':300}; // declare base datagrid as A5 sheet

doLayout(layout,"#chrono_TiempoBg",		5,		5,		550,	190	);
doLayout(layout,"#chrono_ResultadosBg",	560,	5,		235,	190	);
doLayout(layout,"#chrono_DatosBg",		5,		200,	790,	95	);

doLayout(layout,"#chrono_FaltasLbl",	580,	25,		130,	55	);
doLayout(layout,"#chrono_TocadosLbl",	580,	80,		130,	55	);
doLayout(layout,"#chrono_RehusesLbl",	580,	135,	130,	55	);
doLayout(layout,"#chrono_Faltas",		740,	25,		25,		55	);
doLayout(layout,"#chrono_Tocados",		740,	80,		25,		55	);
doLayout(layout,"#chrono_Rehuses",		740,	135,	25,		55	);

doLayout(layout,"#chrono_Tiempo",		10,		80,		540, 	105	);

doLayout(layout,"#chrono_Logo",			10,		210,	85,		85	);
doLayout(layout,"#chrono_Dorsal",		100,	210,	100,	40	);
doLayout(layout,"#chrono_Nombre",		200,	210,	250,	40	);
doLayout(layout,"#chrono_Categoria",	450,	210,	200,	40	);
doLayout(layout,"#chrono_Grado",		650,	210,	125,	40	);
doLayout(layout,"#chrono_NombreGuia",	100,	255,	350,	40	);
doLayout(layout,"#chrono_NombreClub",	450,	255,	250,	40	);
doLayout(layout,"#chrono_Celo",			700,	255,	75,		40	);

</script>
