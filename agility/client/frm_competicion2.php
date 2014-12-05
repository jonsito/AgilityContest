<!-- 
frm_competicion2.php

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
 
<?php require_once("dialogs/dlg_ordentandas.inc");?>
<?php require_once("dialogs/dlg_ordensalida.inc");?>
<?php require_once("dialogs/dlg_competicion.inc");?>
<?php require_once("dialogs/dlg_resultadosManga.inc");?>
 	
<!-- PANEL INFORMATIVO SOBRE LA MANGAS DE ESTA JORNADA -->
<div id="competicion_info" class="easyui-panel" title="Informaci&oacute;n de la jornada de competici&oacute;n">

	<!-- paneles de lista de mangas y datos de cada manga -->
	<div id="competicion_infolayout" class="easyui-layout" style="height:400px">
		<div data-options="region:'west',title:'Mangas de la jornada',split:true,collapsed:false" style="width:250px">
			<!-- Tabla que contiene la lista de Mangas de la jornada -->
			<table id="competicion-listamangas" style="padding:50px"></table>
		</div>
		<div data-options="region:'center',title:'Datos de la manga'" style="width:600px;">
			<font size="11"> <!--  take care on some stupid browsers -->
			<span id="competicion-datosmanga" class="c_competicion-datosmanga"></span>
			</font>
		</div> <!-- datos de la manga -->
	</div> <!-- informacion de layout -->
</div> <!-- panel de informacion -->  

<!-- BARRA DE TAREAS DE LA LISTA DE MANGAS-->
<div id="competicion-listamanga-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:10px">
		<a id="competicion-ordentandasBtn" href="#" class="easyui-linkbutton"
			data-options="iconCls:'icon-updown'" style="width:185px"
			onclick="competicionDialog('ordentandas');">Programaci&oacute;n</a>
		<a id="competicion-ordensalidaBtn" href="#" class="easyui-linkbutton"
			data-options="iconCls:'icon-order'" style="width:185px"
			onclick="competicionDialog('ordensalida');">Orden de salida</a>
		<a id="competicion-competicionBtn" href="#" class="easyui-linkbutton"
			data-options="iconCls:'icon-table'" style="width:185px"
			onclick="competicionDialog('competicion');">Entrada de datos</a>
		<a id="competicion-resultmangaBtn" href="#" class="easyui-linkbutton"
			data-options="iconCls:'icon-endflag'" style="width:185px"
			onclick="competicionDialog('resultadosmanga');">Resultados de la manga</a>
	</span>
</div>

<script type="text/javascript">

// declaracion de cada elemento grafico
$('#competicion_info').panel({
	title:workingData.nombrePrueba+' -- '+workingData.nombreJornada,
	border:true,
	closable:true,
	collapsible:false,
	collapsed:false
});

$('#competicion_infolayout').layout();

$('#competicion-listamangas').datagrid({
	url: '/agility/server/database/mangaFunctions.php?Operation=enumerate&Jornada='+workingData.jornada,
	fit: true,
	method: 'get',
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    showHeader: false,
    toolbar: '#competicion-listamanga-toolbar',
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
    rowStyler:myRowStyler,
    onSelect: function (index,row) {
        if (index<0) { // no manga selected
            $('#competicion-datosmanga').html("");
        	workingData.manga=0;
        	workingData.nombreManga="";
        	workingData.datosManga=null;
            return; 
        }
        // guardamos el id y el nombre de la manga
        workingData.datosManga=row;
        workingData.manga=row.ID;
        workingData.nombreManga=row.Descripcion;
        // cannot use loadcontents, because need to execute commands, _after_ html document load success
        $('#competicion-datosmanga').load("/agility/client/dialogs/infomanga.inc", function() {
            // titulo del panel lateral con la informacion de la manga
        	$('#competicion_infolayout').layout('panel','center').panel('setTitle','Datos de la manga -- '+workingData.nombreManga);
        	// datos del panel lateral con informacion de la manga
        	reload_manga(workingData.manga);
        });
        // refresh orden de salida/competicion/resultados
        reloadOrdenSalida();
        reloadCompeticion();
        reloadResultadosManga(row.Recorrido);
    }
});

//tooltips
addTooltip($('#competicion-ordentandasBtn').linkbutton(),"Ver/Ordenar la secuencia de <br/>Mangas/Categorias/Grados de la prueba");
addTooltip($('#competicion-ordensalidaBtn').linkbutton(),"Ver/Editar el Orden de salida de la manga");
addTooltip($('#competicion-competicionBtn').linkbutton(),"Insertar resultados de los participantes en la manga");
addTooltip($('#competicion-resultmangaBtn').linkbutton(),"Ver los resultados parciales de la manga"); 

</script>
    