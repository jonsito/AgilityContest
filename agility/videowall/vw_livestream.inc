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
		   	poster="http://guest:@192.168.122.168/videostream.cgi">
		   	<!-- "/agility/server/getRandomImage.php" -->
    		<source src="/agility/videos/null.mp4" type='video/mp4; codecs="avc1,mp4a"' />
    		<source src="/agility/videos/sample_video.ogv" type='video/ogg; codecs="theora,vorbis"' />
    		<source src="/agility/videos/sample_video.webm" type='video/webm; codecs="vp8,vorbis"' />
    	</video> 
		<div id="vwls_common">
			<!-- Recuadros de decoracion -->
			<span class="vwls_fondo" id="vwls_Resultados">&nbsp;</span>
			<span class="vwls_fondo" id="vwls_Datos">&nbsp;</span>
			<!-- datos de resultados -->
			<span class="vwls_label" id="vwls_FaltasLbl">Faltas:</span>
			<span class="vwls_data"  id="vwls_Faltas">0</span>
			<span class="vwls_label" id="vwls_TocadosLbl">Tocados:</span>
			<span class="vwls_data"  id="vwls_Tocados">0</span>
			<span class="vwls_label" id="vwls_RehusesLbl">Rehuses:</span>
			<span class="vwls_data"  id="vwls_Rehuses">0</span>
			<span class="vwls_label" id="vwls_TiempoLbl">Tiempo:</span>
			<span class="vwls_data"  id="vwls_Tiempo">00.00</span>
			<!-- Informacion del participante -->
			<span style="display:none" id="vwls_Perro">0</span>
			<img id="vwls_Logo" alt="Logo" src="/agility/images/logos/rsce.png" width="80" height="80" class="vwls_logo"/>
			<span class="vwls_label" id="vwls_Dorsal">Dorsal</span>
			<span class="vwls_label" id="vwls_Nombre">Nombre</span>
			<span class="vwls_label" id="vwls_NombreGuia">Guia</span>
			<span class="vwls_label" id="vwls_NombreClub">Club</span>
			<span class="vwls_label" id="vwls_Categoria">Categoria</span>
			<span class="vwls_label" id="vwls_Grado">Grado</span>
			<span class="vwls_label" id="vwls_Celo">Celo</span>
		</div>
	</div>
</div>

<!-- declare a tag to attach a chrono object to -->
<div id="cronomanual"></div>
		
<script type="text/javascript">
	
// create a Chronometer instance
$('#cronomanual').Chrono( {
	seconds_sel: '#vwls_Tiempo',
	auto: false,
	interval: 100,
	showMode: 2
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

doLayout(layout,"#vwls_Resultados",	620,	35,		155,	130	);
doLayout(layout,"#vwls_Datos",		25,		345,	750,	80	);

doLayout(layout,"#vwls_FaltasLbl",	630,	50,		75,		25	);
doLayout(layout,"#vwls_Faltas",		740,	50,		25,		25	);
doLayout(layout,"#vwls_TocadosLbl",	630,	75,		75,		25	);
doLayout(layout,"#vwls_Tocados",	740,	75,		25,		25	);
doLayout(layout,"#vwls_RehusesLbl",	630,	100,	75,		25	);
doLayout(layout,"#vwls_Rehuses",	740,	100,	25,		25	);
doLayout(layout,"#vwls_TiempoLbl",	630,	125,	50,		25	);
doLayout(layout,"#vwls_Tiempo",		710,	125,	55,		25	);

doLayout(layout,"#vwls_Logo",		30,		350,	80,		80	);
doLayout(layout,"#vwls_Dorsal",		150,	350,	100,	40	);
doLayout(layout,"#vwls_Nombre",		250,	350,	250,	40	);
doLayout(layout,"#vwls_NombreGuia",	150,	390,	300,	40	);
doLayout(layout,"#vwls_NombreClub",	450,	390,	250,	40	);
doLayout(layout,"#vwls_Categoria",	550,	350,	100,	40	);
doLayout(layout,"#vwls_Grado",		650,	350,	100,	40	);
doLayout(layout,"#vwls_Celo",		700,	390,	50,		40	);

jQuery('#vwls_LiveStream').fitText(0.035);
</script>
