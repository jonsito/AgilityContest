	
<!-- Gestion desde el tablet de el orden de salida y entrada de datos -->
<div id="vw_combinada-Panel" class="easyui-panel" style="width:1920px;height:1080px;">

	
	<!-- paneles de lista de mangas y datos de cada manga -->
	<div id="vw_combinada-Layout1" class="easyui-layout" data-options="fit:true">
	
	
		<!-- marco izquierdo. top:livestream bottom:llamada-->
		<div data-options="region:'west',split:true,border:false,collapsible:false,collapsed:false" 
				style="width:800px"> <!-- dos quintos del ancho de pantalla -->
		
			<div id="vw_combinada-Layout2" class="easyui-layout" data-options="fit:true">
			
				<div data-options="region:'north',split:true,border:false,title:'LiveStream',collapsible:false,collapsed:false" 
					style="height:450px">
					<div id="vw_combinada-LiveStream" class="easyui-panel">
						<!-- http://rolandocaldas.com/html5/video-de-fondo-en-html5 -->
					    <video id="video-background" autoplay="autoplay" preload="auto" muted="muted"
					    	poster="/agility/images/wallpapers/foto_dama.jpg" height="450">
    						<source src="/agility/videos/sample_video.mp4" type='video/mp4; codecs="avc1,mp4a"' />
    						<!-- ogv se ve muy mal en firefox
    						<source src="/agility/videos/sample_video.ogv" type='video/ogg; codecs="theora,vorbis"' />
    						-->
    						<source src="/agility/videos/sample_video.webm" type='video/webm; codecs="vp8,vorbis"' />
    					</video> 
						<div id="videowall_data">
							<!-- Recuadros de decoracion -->
							<span class="vw_fondo" id="vwc_Resultados">&nbsp;</span>
							<span class="vw_fondo" id="vwc_Datos">&nbsp;</span>
							<!-- datos de resultados -->
							<span class="vw_label" id="vwc_FaltasLbl">Faltas:</span>
							<span class="vw_data"  id="vwc_Faltas">0</span>
							<span class="vw_label" id="vwc_TocadosLbl">Tocados:</span>
							<span class="vw_data"  id="vwc_Tocados">0</span>
							<span class="vw_label" id="vwc_RehusesLbl">Rehuses:</span>
							<span class="vw_data"  id="vwc_Rehuses">0</span>
							<span class="vw_label" id="vwc_TiempoLbl">Tiempo:</span>
							<span class="vw_data"  id="vwc_Tiempo">00.00</span>
							<!-- Informacion del participante -->
							<span class="vw_label" id="vwc_Logo">Logo</span>
							<span class="vw_label" id="vwc_Dorsal">Dorsal</span>
							<span class="vw_label" id="vwc_Nombre">Nombre</span>
							<span class="vw_label" id="vwc_NombreGuia">Guia</span>
							<span class="vw_label" id="vwc_NombreClub">Club</span>
							<span class="vw_label" id="vwc_Categoria">Categoria</span>
							<span class="vw_label" id="vwc_Grado">Grado</span>
							<span class="vw_label" id="vwc_Celo">Celo</span>
						</div>
					</div>
				</div>
				<div data-options="region:'center',split:true,border:false,title:'Llamada a pista'">
					<div id="vw_combinada-Pendientes" class="easyui-panel"></div>
				</div>
				
			</div>
		
		</div> <!-- marco izquierdo  -->
		
		<!-- marco derecho: resultados parciales -->
		<div data-options="region:'center',title:'Resultados parciales'">
			<div id="vw_combinada-Resultados" class="easyui-panel"></div>
		</div> <!-- resultados parciales -->
		
	</div> <!-- combinada-Layout -->
	
</div> <!-- combinada-Panel -->  

		
<script type="text/javascript">
$('#vw_combinada-Panel').panel({
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	onOpen: function() {
		startEventMgr(workingData.sesion,vw_processLiveStream);
	}
});

$('#vw_combinada-LiveStream').panel({
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false
});

$('#vw_combinada-Pendientes').panel({
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	href:"/agility/server/database/videowall.php",
	queryParams: {
		Operation: 'Llamada',
		Session: workingData.sesion
	},
	loadingMessage:"Obteniendo lista de pre-ring..."
});
$('#vw_combinada-Resultados').panel({
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	href:"/agility/server/database/videowall.php",
	queryParams: {
		Operation: 'Resultados',
		Session: workingData.sesion
	},
	loadingMessage:"Obteniendo resultados parciales..."
});
$('#vw_combinada-Layout1').layout();
$('#vw_combinada-Layout2').layout();


// layout
var layout= {'cols':800, 'rows':450}; // declare base datagrid as A5 sheet

doLayout(layout,"#vwc_Resultados",	625,	35,		150,	130	);
doLayout(layout,"#vwc_Datos",		25,		345,	750,	80	);

doLayout(layout,"#vwc_FaltasLbl",	640,	50,		75,		25	);
doLayout(layout,"#vwc_Faltas",		735,	50,		25,		25	);
doLayout(layout,"#vwc_TocadosLbl",	640,	75,		75,		25	);
doLayout(layout,"#vwc_Tocados",		735,	75,		25,		25	);
doLayout(layout,"#vwc_RehusesLbl",	640,	100,	75,		25	);
doLayout(layout,"#vwc_Rehuses",		735,	100,	25,		25	);
doLayout(layout,"#vwc_TiempoLbl",	640,	125,	50,		25	);
doLayout(layout,"#vwc_Tiempo",		710,	125,	50,		25	);

doLayout(layout,"#vwc_Logo",		50,		350,	100,	80	);
doLayout(layout,"#vwc_Dorsal",		150,	350,	60,		40	);
doLayout(layout,"#vwc_Nombre",		210,	350,	290,	40	);
doLayout(layout,"#vwc_NombreGuia",	150,	390,	300,	40	);
doLayout(layout,"#vwc_NombreClub",	450,	390,	250,	40	);
doLayout(layout,"#vwc_Categoria",	550,	350,	100,	40	);
doLayout(layout,"#vwc_Grado",		650,	350,	100,	40	);
doLayout(layout,"#vwc_Celo",		700,	390,	50,		40	);

</script>