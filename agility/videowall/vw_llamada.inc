	<!-- Presentacion combinada de live stream, llamada a pista y resultados parciales -->
<div id="vw_llamada-window">
	<div id="vw_llamada-Cabecera" class="vw_floatingheader">
		<span id="vw_llamada-Texto">Llamada a pista</span>
	</div>
	<div id="vw_llamada-Pendientes" class="easyui-panel"
		data-options="noheader:true,border:false,closable:false,collapsible:false,collapsed:false">
		<span id="vwc_listaPendientes"></span>
	</div>	
</div> <!-- vw_llamada-window -->

<script type="text/javascript">

$('#vw_llamada-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true,
	onOpen: function() {
		startEventMgr(workingData.sesion,vw_processLlamada);
	}
});

</script>