<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Videowall::livestream");
if ( ! $am->allowed(ENABLE_VIDEOWALL)) { include_once("unregistered.html"); return 0;}
// tool to perform automatic upgrades in database when needed
require_once(__DIR__."/../server/upgradeVersion.php");
?>
<!--
livestream.inc

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


<!-- Pantalla liveStream -->
<div id="vwls_LiveStream-window" style="padding:10px">
	<div id="vwls_LiveStream" class="easyui-panel"
		data-options="noheader:true,border:false,closable:false,collapsible:false,collapsed:false,resizable:true">
		<!-- http://rolandocaldas.com/html5/video-de-fondo-en-html5 -->
		<video id="vwls_video" autoplay="autoplay" preload="auto" muted="muted" loop="loop"
			poster="/agility/server/getRandomImage.php">
			<!-- http://guest:@192.168.122.168/videostream.cgi -->
    		<source id="vwls_videomp4" src="" type='video/mp4'/>
    		<source id="vwls_videoogv" src="" type='video/ogg'/>
    		<source id="vwls_videowebm" src="" type='video/webm'/>
    	</video> 
		<div id="vwls_common" style="font-size:1.75em;display:inline-block;width:100%">
			<!-- Recuadros de decoracion -->
			<span class="vwls_fondo" id="vwls_Resultados">&nbsp;</span>
            <span class="vwls_fondo" id="vwls_Datos">&nbsp;</span>
            <span class="vwls_fondo" id="vwls_InfoManga">&nbsp;</span>
			<!-- datos de resultados -->
			<span class="vwls_label" id="vwls_FaltasLbl">Faltas:</span>
			<span class="vwls_data"  id="vwls_Faltas">0</span>
			<span class="vwls_label" id="vwls_TocadosLbl">Tocados:</span>
			<span class="vwls_data"  id="vwls_Tocados">0</span>
			<span class="vwls_label" id="vwls_RehusesLbl">Reh&uacute;ses:</span>
			<span class="vwls_data"  id="vwls_Rehuses">0</span>
			<span class="vwls_label" id="vwls_TiempoLbl">Tiempo:</span>
			<span class="vwls_data"  id="vwls_Tiempo">00.00</span>
       		<span id="vwls_timestamp" style="display:none"></span>
			<!-- Informacion del participante -->
			<span style="display:none" id="vwls_Perro">0</span>
			<img id="vwls_Logo" alt="Logo" src="/agility/images/logos/rsce.png" width="80" height="80" class="vwls_logo"/>
			<span class="vwls_label" id="vwls_Dorsal">Dorsal</span>
			<span class="vwls_label" id="vwls_Nombre">Nombre</span>
			<span class="vwls_label" id="vwls_NombreGuia">Gu&iacute;a</span>
			<span class="vwls_label" id="vwls_NombreClub">Club</span>
			<span class="vwls_label" id="vwls_Categoria">Categor&iacute;a</span>
			<span class="vwls_label" id="vwls_Grado">Grado</span>
            <span class="vwls_label" id="vwls_Celo">Celo</span>
            <!-- Informacion de la manga -->
            <span class="vwls_label" id="vwls_Manga" style="text-align:center;">Manga</span>
		</div>
	</div>
</div>

<!-- declare a tag to attach a chrono object to -->
<div id="cronomanual"></div>
		
<script type="text/javascript">
	
// create a Chronometer instance
$('#cronomanual').Chrono( {
	seconds_sel: '#vwls_timestamp',
	auto: false,
	interval: 100,
	showMode: 2,
	onUpdate: function(elapsed,running,pause) { 
		$('#vwls_Tiempo').html(parseFloat(elapsed/1000).toFixed((running)?1:2));
		return true;
	}
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
		startEventMgr(workingData.sesion,vwls_processLiveStream);
	}
});

// layout
var layout= {'cols':800, 'rows':450}; // declare base datagrid as A5 sheet

doLayout(layout,"#vwls_Resultados",	620,	20,		155,	100	);
doLayout(layout,"#vwls_Datos",		25,		390,	750,	45	);
doLayout(layout,"#vwls_InfoManga",	25,	    20,	    250,	20	);

doLayout(layout,"#vwls_FaltasLbl",	630,	30,		75,		20	);
doLayout(layout,"#vwls_Faltas",		740,	30,		25,		20	);
doLayout(layout,"#vwls_TocadosLbl",	630,	50,		75,		20	);
doLayout(layout,"#vwls_Tocados",	740,	50,		25,		20	);
doLayout(layout,"#vwls_RehusesLbl",	630,	70,	    75,		20	);
doLayout(layout,"#vwls_Rehuses",	740,	70,	    25,		20	);
doLayout(layout,"#vwls_TiempoLbl",	630,	90,     50,		20	);
doLayout(layout,"#vwls_Tiempo",		710,	90,     55,		20	);

doLayout(layout,"#vwls_Logo",		30,		360,	80,		80	);
doLayout(layout,"#vwls_Dorsal",		120,	395,	110,	25	);
doLayout(layout,"#vwls_Nombre",		230,	395,	270,	25	);
doLayout(layout,"#vwls_NombreGuia",	120,	415,	380,	25	);
doLayout(layout,"#vwls_NombreClub",	500,	415,	200,	25	);
doLayout(layout,"#vwls_Categoria",	500,	395,	150,	25	);
doLayout(layout,"#vwls_Grado",		650,	395,	125,	25	);
doLayout(layout,"#vwls_Celo",		700,	415,	75,		25	);

doLayout(layout,"#vwls_Manga",		25, 	20,	    250,	15	);

jQuery('#vwls_common').fitText(0.02);
</script>
