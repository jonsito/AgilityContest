<!-- 
llamada.inc

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

<!-- Tabla de llamada a pista -->
<div id="vw_llamada-window">
	<div id="vw_llamada-layout" style="width:100%">
		<div id="vw_llamada-Cabecera" data-options="region:'north',split:false" style="height:10%" class="vw_floatingheader">
			<span style="float:right;" id="vw_llamada-Texto">Llamada a pista</span>
			<span style="float:left;" id="vw_llamada-infocabecera">Cabecera</span>
		</div>
		<div id="vw_llamada-Pendientes" data-options="region:'center'">
			<span id="vwc_listaPendientes"></span>
		</div>	
	</div>
</div> <!-- vw_llamada-window -->

<script type="text/javascript">

$('#vw_llamada-layout').layout({fit:true});

$('#vw_llamada-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true,
	onOpen: function() {
		alert($('#vw_llamada-infocabecera').html());
		startEventMgr(workingData.sesion,vw_processLlamada);
	}
});

</script>