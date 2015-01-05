<!-- 
vw_ordensalida.inc

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


<!-- Presentacion del orden de salida de la jornada -->
<div id="vw_ordensalida-window">
	<div id="vw_ordensalida-layout" style="width:100%">
		<div id="vwi_cabecera" data-options="region:'north',split:false" style="height:10%" class="vw_floatingheader">
		<span id="vw_ordensalida-infocabecera">Cabecera</span><br/>
			<span id="vw_ordensalida-texto" >Orden de salida</span>
			<select id="vw_ordensalida-combogrid" style="width:200px;"></select>
		</div>
		<div id="vwi_tabla" data-options="region:'center'">
			<span id="vw_ordensalidaManga"></span>
		</div>
	</div>
</div> <!-- vw_llamada-window -->

<script type="text/javascript">
$('#vw_ordensalida-layout').layout({fit:true});

$('#vw_ordensalida-combogrid').combogrid({
	url: '/agility/server/database/mangaFunctions.php',
	queryParams: {
		'Operation': 'enumerate',
		'Jornada' : workingData.jornada
	},
	method: 'get',
    fitColumns: true,
    singleSelect: true,
    showHeader: false,
    idField: 'ID',
    textField: 'Descripcion',
    editable: false,
	columns:[[
		{ field:'ID',			hidden:true }, // ID de la jornada
		{ field:'Tipo',			hidden:true }, // Tipo de manga
		{ field:'Grado',		hidden:true }, // Grado de los perros de la manga
		{ field:'Recorrido',	hidden:true }, // 0:L/M/S 1:L/M+S 2:L+M+S
		{ field:'Descripcion',	width:200, sortable:false, align:'right',
		      	formatter: function(val,row){ // si manga especial, obtener texto del campo observaciones
		       	    if (row.Tipo!=16) return val;
		       	    if (workingData.datosJornada.Observaciones==="") return val;
	           	    return workingData.datosJornada.Observaciones;
	       	    }
		}, // texto del tipo de manga
	]],
	onBeforeLoad: function(param) { 
		if (workingData.jornada==0) return false; // do not load if no jornada declared
		return true;
	},
	onChange: function(value) {
		workingData.manga=value;
		vwos_paintOrdenSalida(workingData.prueba,workingData.jornada,value);
	}
});

//add 'callback' property to store interval references
$.extend($.fn.window.defaults,{callback:null});
$('#vw_ordensalida-window').window({
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
		vwos_procesaOrdenSalida();
		var a=setInterval(vwos_procesaOrdenSalida,30000);
		$(this).window.defaults.callback=a; 
	},
	onClose: function() { 
		clearInterval($(this).window.defaults.callback);
	}
});

</script>