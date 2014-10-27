	
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
						<form id="vwc_form" class="videowall_form">
							<!-- Recuadros de decoracion -->
							<span id="vwc_Resultados" class="videowall_rectangulo">&nbsp;</span>
							<span id="vwc_Datos" class="videowall_rectangulo" >&nbsp;</span>
							<!-- datos de resultados -->
							<label id="vwc_FaltasLbl" for="Faltas">Faltas:</label>
							<input type="text" id="vwc_Faltas" name="Faltas" value="0" readonly="readonly" disabled="disabled"/>
							<label id="vwc_TocadosLbl" for="Tocados">Tocados:</label>
							<input type="text" id="vwc_Tocados" name="Tocados" value="0" readonly="readonly" disabled="disabled"/>
							<label id="vwc_RehusesLbl" for="Rehuses">Rehuses:</label>
							<input type="text" id="vwc_Rehuses" name="Rehuses" value="0" readonly="readonly" disabled="disabled"/>
							<label id="vwc_TiempoLbl" for="Tiempo">Tiempo:</label>
							<input type="text" id="vwc_Tiempo" name="Tiempo" value="0" readonly="readonly" disabled="disabled"/>
							<!-- Informacion del participante -->
							<label id="vwc_Logo">Logo</label>
							<label id="vwc_Dorsal">Dorsal</label>
							<label id="vwc_Nombre">Nombre</label>
							<label id="vwc_NombreGuia">Guia</label>
							<label id="vwc_NombreClub">Club</label>
							<label id="vwc_Categoria">Categoria</label>
							<label id="vwc_Grado">Grado</label>
							<label id="vwc_Celo">Celo</label>
						</form>
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

doLayout(layout,"#vwc_Logo",		50,		345,	100,	80	);
doLayout(layout,"#vwc_Dorsal",		150,	345,	60,		40	);
doLayout(layout,"#vwc_Nombre",		210,	345,	290,	40	);
doLayout(layout,"#vwc_NombreGuia",	150,	385,	300,	40	);
doLayout(layout,"#vwc_NombreClub",	450,	385,	250,	40	);
doLayout(layout,"#vwc_Categoria",	550,	345,	100,	40	);
doLayout(layout,"#vwc_Grado",		650,	345,	100,	40	);
doLayout(layout,"#vwc_Celo",		700,	385,	50,		40	);

</script>