<!-- 
vw_combinada.inc

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<!-- Presentacion combinada de live stream, llamada a pista y resultados parciales -->
<div id="vw_combinada-window">
	<!-- paneles de lista de mangas y datos de cada manga -->
	<div id="vw_combinada-Layout1" class="easyui-layout" data-options="fit:true">
		
		<!-- marco izquierdo. top:livestream bottom:llamada-->
		<div data-options="region:'west',split:true,border:false,collapsible:false,collapsed:false" 
				style="width:40%"> <!-- dos quintos del ancho de pantalla -->
		
			<div id="vw_combinada-Layout2" class="easyui-layout" data-options="fit:true">
			
				<div data-options="region:'north',split:true,border:false,collapsible:false,collapsed:false" 
					style="height:35%">
					<div id="vwls_LiveStream" class="easyui-panel"
						data-options="noheader:true,border:false,closable:false,collapsible:false,collapsed:false,resizable:true">
						<!-- http://rolandocaldas.com/html5/video-de-fondo-en-html5 -->
					    <video id="vwls_video" autoplay="autoplay" preload="auto" muted="muted" loop="loop"
					    	poster="/agility/server/getRandomImage.php">
    						<source src="/agility/videos/sample_video.mp4" type='video/mp4; codecs="avc1,mp4a"' />
    						<!-- ogv se ve muy mal en firefox
    						<source src="/agility/videos/sample_video.ogv" type='video/ogg; codecs="theora,vorbis"' />
    						-->
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
				<div data-options="region:'center',split:true,border:false">

				<div id="vwc_llamada-layout" style="width:100%">
					<div id="vwc_llamada-Cabecera" data-options="region:'north',split:false" style="height:10%" class="vw_floatingheader">
						<span id="vwc_llamada-Texto">Llamada a pista</span>
					</div>
					<div id="vw_combinada-Pendientes" data-options="region:'center'">
						<span id="vwc_listaPendientes"></span>
					</div>
				</div>
					
				</div>
				
			</div>
		
		</div> <!-- marco izquierdo  -->
		
		<!-- marco derecho: resultados parciales -->
		<div data-options="region:'center'">
			<div id="vw_combinada-Resultados" class="easyui-panel"
				data-options="noheader:true,border:false,closable:false,collapsible:false,collapsed:false">
				<span id="vwc_resultadosParciales"></span>
			</div>
		</div> <!-- resultados parciales -->
		
	</div> <!-- combinada-Layout -->
	
</div> <!-- combinada-Panel -->

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

$('#vw_combinada-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	onOpen: function() {
		startEventMgr(workingData.sesion,vwc_processCombinada);
	}
});

$('#vw_combinada-Layout1').layout();
$('#vw_combinada-Layout2').layout();
$('#vwc_llamada-layout').layout({fit:true});

// layout
var layout= {'cols':800, 'rows':450}; // declare base datagrid as A5 sheet

doLayout(layout,"#vwls_Resultados",	625,	35,		150,	130	);
doLayout(layout,"#vwls_Datos",		25,		345,	750,	80	);

doLayout(layout,"#vwls_FaltasLbl",	635,	50,		75,		25	);
doLayout(layout,"#vwls_Faltas",		735,	50,		25,		25	);
doLayout(layout,"#vwls_TocadosLbl",	635,	75,		75,		25	);
doLayout(layout,"#vwls_Tocados",	735,	75,		25,		25	);
doLayout(layout,"#vwls_RehusesLbl",	635,	100,	75,		25	);
doLayout(layout,"#vwls_Rehuses",	735,	100,	25,		25	);
doLayout(layout,"#vwls_TiempoLbl",	635,	125,	50,		25	);
doLayout(layout,"#vwls_Tiempo",		705,	125,	55,		25	);

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
