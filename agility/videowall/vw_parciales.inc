	<!-- Presentacion combinada de live stream, llamada a pista y resultados parciales -->
<div id="vw_parciales-window">
	<div id="vw_parciales-Resultados" class="easyui-panel"
		data-options="noheader:true,border:false,closable:false,collapsible:false,collapsed:false">
			<span id="vwc_resultadosParciales"></span>
	</div>
</div> <!-- vw_llamada-window -->

<script type="text/javascript">

$('#vw_parciales-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true,
	onOpen: function() {
		startEventMgr(workingData.sesion,vw_processParciales);
	}
});

</script>