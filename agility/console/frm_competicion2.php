<!-- 
frm_competicion2.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->
<?php
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Competicion");

require_once("dialogs/dlg_entrenamientos.inc");
require_once("dialogs/dlg_ordentandas.inc");
require_once("dialogs/dlg_ordensalida.inc");
require_once("dialogs/dlg_competicion.inc");
switch(http_request("tipo","s","std")) {
    case "eq3":
    case "eq4":
        if ( ! $am->allowed(ENABLE_TEAMS)) {
            require_once("unregistered.html");
            return 0;
        }
        require_once("dialogs/dlg_resultados_equipos.inc");
        break;
    case "ko":
        if ( ! $am->allowed(ENABLE_KO)) {
            require_once("unregistered.html");
            return 0;
        }
        require_once("dialogs/dlg_resultados_ko.inc");
        break;
    case "std":
    case "open":
    default:
        require_once("dialogs/dlg_resultados_individual.inc");
        break;
}
?>
 	
<!-- PANEL INFORMATIVO SOBRE LA MANGAS DE ESTA JORNADA -->
<div id="competicion_info" style="width:100%">

	<!-- paneles de lista de mangas y datos de cada manga -->
	<div id="competicion_infolayout" class="easyui-layout" style="height:400px">
		<div data-options="region:'west',title:'<?php _e('Journey rounds');?>',split:true,collapsed:false" style="width:25%">
			<!-- Tabla que contiene la lista de Mangas de la jornada -->
			<table id="competicion-listamangas" style="padding:50px"></table>
		</div>
		<div data-options="region:'center',title:'<?php _e('Round data');?>'" style="width:600px;">
			<span id="competicion-datosmanga" class="c_competicion-datosmanga" style="font-size:11px"></span>
		</div> <!-- datos de la manga -->
	</div> <!-- informacion de layout -->
</div> <!-- panel de informacion -->  

<!-- BARRA DE TAREAS DE LA LISTA DE MANGAS-->
<div id="competicion-listamanga-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:10px">
		<a id="competicion-entrenamientosBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-tools'" style="width:185px"
           onclick="competicionDialog('entrenamientos');"><?php _e('Training');?></a>
		<a id="competicion-ordentandasBtn" href="#" class="easyui-linkbutton"
			data-options="iconCls:'icon-updown'" style="width:185px"
			onclick="competicionDialog('ordentandas');"><?php _e('Planning');?></a>
		<a id="competicion-ordensalidaBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-order'" style="width:185px"
           onclick="competicionDialog('ordensalida');"><?php _e('Starting order');?></a>
		<a id="competicion-competicionBtn" href="#" class="easyui-linkbutton"
			data-options="iconCls:'icon-table'" style="width:185px"
			onclick="competicionDialog('competicion');"><?php _e('Data entry');?></a>
		<a id="competicion-resultmangaBtn" href="#" class="easyui-linkbutton"
			data-options="iconCls:'icon-endflag'" style="width:185px"
			onclick="competicionDialog('resultadosmanga');"><?php _e('Round results');?></a>
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
	url: '/agility/server/database/mangaFunctions.php',
    queryParams: { Operation: 'enumerate', Jornada: workingData.jornada },
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
          	    formatter: function(val,row){ 
              	    // si manga especial, obtener texto del campo observaciones
              	    if (parseInt(row.Tipo)!=16) return val;
              	    if (workingData.datosJornada.Observaciones==="") return val;
              	    return workingData.datosJornada.Observaciones;
          	    }
      	    } // texto del tipo de manga
    ]],
    rowStyler:myRowStyler,
    onClickRow: function (index,row) {
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
        var infomanga="/agility/console/dialogs/infomanga.php?Federation="+workingData.federation;
        $('#competicion-datosmanga').load(infomanga, function() {
            // titulo del panel lateral con la informacion de la manga
        	$('#competicion_infolayout').layout('panel','center').panel('setTitle','<?php _e('Datos de la manga');?> -- '+workingData.nombreManga);
        	// datos del panel lateral con informacion de la manga
        	reload_manga(workingData.manga);
            // refresh orden de salida/competicion/resultados
            reloadOrdenSalida();
            reloadCompeticion();
            setupResultadosWindow(row.Recorrido);
        });
    }
});

//tooltips
addTooltip($('#competicion-entrenamientosBtn').linkbutton(),"<?php _e('View/Edit Training session timetable for the contest')?>");
addTooltip($('#competicion-ordentandasBtn').linkbutton(),"<?php _e('View/Edit Rounds and series order<br />on this journey')?>");
addTooltip($('#competicion-ordensalidaBtn').linkbutton(),"<?php _e('View/Edit Starting order on selected round');?>");
addTooltip($('#competicion-competicionBtn').linkbutton(),"<?php _e('Insert/Edit competitors results');?>");
addTooltip($('#competicion-resultmangaBtn').linkbutton(),"<?php _e('Review Partial results on selected round');?>");

</script>
    