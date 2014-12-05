<!-- 
vw_inscripciones.inc

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


<!-- Presentacion de las inscripciones de la jornada -->
<div id="vw_inscripciones-window">
	<div id="vw_inscripciones-layout" style="width:100%">
		<div id="vwi_cabecera" data-options="region:'north',split:false" style="height:10%" class="vw_floatingheader">
			<span id="vw_inscripciones-texto" >Listado de inscritos en la jornada</span>
		</div>
		<div id="vwi_tabla" data-options="region:'center'">
			<span id="vw_inscripcionesJornada"></span>
		</div>
	</div>
</div> <!-- vw_llamada-window -->

<script type="text/javascript">
$('#vw_inscripciones-layout').layout({fit:true});

//add 'callback' property to store interval references
$.extend($.fn.window.defaults,{callback:null});
$('#vw_inscripciones-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true,
	callback: null, 
	// 1 minute poll is enouth for this, as no expected changes during a session
	onOpen: function() {
		// call once and then fire as timed task
		vwi_procesaInscripciones();
		var a=setInterval(vwi_procesaInscripciones,10000);
		$(this).window.defaults.callback=a; 
	},
	onClose: function() { 
		clearInterval($(this).window.defaults.callback);
	}
});

</script>