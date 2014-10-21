	
<!-- Gestion desde el tablet de el orden de salida y entrada de datos -->
<div id="vw_combinada-Panel" class="easyui-panel" style="width:1920px;height:1080px;">

	
	<!-- paneles de lista de mangas y datos de cada manga -->
	<div id="vw_combinada-Layout1" class="easyui-layout" data-options="fit:true">
	
	
		<!-- marco izquierdo. top:livestream bottom:llamada-->
		<div data-options="region:'west',split:true,border:false,collapsible:false,collapsed:false" 
				style="width:768px"> <!-- dos quintos del ancho de pantalla -->
		
			<div id="vw_combinada-Layout2" class="easyui-layout" data-options="fit:true">
			
				<div data-options="region:'north',split:true,border:false,title:'LiveStream',collapsible:false,collapsed:false" 
					style="height:270px">
					<div id="vw_combinada-LiveStream" class="easyui-panel"></div>
					hola
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
	collapsed:false
});
$('#vw_combinada-LiveStream').panel({
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	href:"/agility/server/database/videowall.php",
	queryParams: {
		Operation: 'LiveStream',
		Session: workingData.sesion
	},
	loadingMessage:"Actualizando datos LiveStream..."
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
</script>