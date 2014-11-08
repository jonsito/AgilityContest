<!-- 
llamada.inc

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