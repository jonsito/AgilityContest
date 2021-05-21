<!-- 
frm_competicion2.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
$am = AuthManager::getInstance("Competicion");
require_once("dialogs/dlg_printer.inc");
require_once("dialogs/dlg_entrenamientos.inc");
require_once("dialogs/dlg_ordentandas.inc");
require_once("dialogs/dlg_ordensalida.inc");
require_once("dialogs/dlg_jueces.inc");
switch(http_request("tipo","s","std")) {
    case "eq3":
    case "eq4":
        if ( ! $am->allowed(ENABLE_TEAMS)) {
            require_once("unregistered.html");
            return 0;
        }
        require_once("dialogs/dlg_competicion.inc");
        require_once("dialogs/dlg_resultados_equipos.inc");
        break;
    case "ko":
        if ( ! $am->allowed(ENABLE_KO)) {
            require_once("unregistered.html");
            return 0;
        }
        require_once("dialogs/dlg_competicion.inc");
        require_once("dialogs/dlg_resultados_ko.inc");
        break;
    case "games":
        if ( ! $am->allowed(ENABLE_SPECIAL)) {
            require_once("unregistered.html");
            return 0;
        }
        $t=http_request('mode',"i",0);
        if ($t!==3)  { // agility,jumping,speedstakes
            require_once("dialogs/dlg_competicion.inc");
            require_once("dialogs/dlg_resultados_ko.inc");
        } else { // snooker,gambler
            require_once("dialogs/dlg_competicion_games.inc");
            require_once("dialogs/dlg_resultados_games.inc");
        }
        break;
    case "std":
    case "open":
    case "special": // single round
    default:
        require_once("dialogs/dlg_competicion.inc");
        require_once("dialogs/dlg_resultados_individual.inc");
        break;
}
?>
 	
<!-- PANEL INFORMATIVO SOBRE LA MANGAS DE ESTA JORNADA -->
<div id="competicion_info" style="width:100%">

	<!-- paneles de lista de mangas y datos de cada manga -->
	<div id="competicion_infolayout" class="easyui-layout" style="height:450px">
		<div data-options="region:'west',title:'<?php _e('Journey rounds');?>',split:true,collapsed:false" style="width:20%">
			<!-- Tabla que contiene la lista de Mangas de la jornada -->
			<table id="competicion-listamangas" style="padding:20px"></table>
		</div>
		<div data-options="region:'center',title:'<?php _e('Round data');?>'" style="width:650px;">
			<span id="competicion-datosmanga" class="c_competicion-datosmanga" style="font-size:11px"></span>
		</div> <!-- datos de la manga -->
	</div> <!-- informacion de layout -->
</div> <!-- panel de informacion -->  

<!-- BARRA DE TAREAS DE LA LISTA DE MANGAS-->
<div id="competicion-listamanga-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
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
	closable:false,
	collapsible:false,
	collapsed:false,
    onClose:function() {autoBackupDatabase(1,"");}
});

$('#competicion_infolayout').layout();

$('#competicion-listamangas').datagrid({
	url: '../ajax/database/mangaFunctions.php',
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
              	    if (parseInt(row.Tipo)!==16) return val;
              	    if (workingData.datosJornada.Observaciones==="") return val;
              	    return workingData.datosJornada.Observaciones;
          	    }
      	    } // texto del tipo de manga
    ]],
    rowStyler:myRowStyler,
    onBeforeSelect: function(index,row) {
	    if (index<0) return false; // no row selected
        // check for current round data changed
        if(workingData.datosManga.modified===1) {
            $.messager.alert({
                icon: 'warning',
                title: "<?php _e('Not saved'); ?>",
                msg: "<?php _e('Round data have not been saved.<br/>Please save or restore data before continuing')?>",
                fn: function(r){}
            });
            return false;
        }
        if (parseInt(workingData.datosJornada.Games)===0) return true; // no games
        // en funcion de la modalidad de juegos y del tipo de jornada se debe permitir o no
        // penthathlon
        var a=["Desconocida",-1];
        var t=parseInt(row.Tipo);
        if (parseInt(workingData.datosJornada.Tipo_Competicion)===1) a=['Penthathlon', $.inArray(t,[25,26,27,28,31])];
        if (parseInt(workingData.datosJornada.Tipo_Competicion)===2) a=['Biathlon', $.inArray(t,[25,26,27,28])];
        if (parseInt(workingData.datosJornada.Tipo_Competicion)===3) a=['Games',$.inArray(t,[29,30])];
        if (a[1]<0) {
            $.messager.alert("<?php _e('Notice');?>",
                "'"+row.Descripcion+"' <?php _e('round is not available on journey');?> "+a[0],
                "error");
        }
        return (a[1]>=0);
    },
    onSelect: function (index,row) {
        if (index<0) { // no manga selected
            $('#competicion-datosmanga').html("");
            setManga(null);
            return; 
        }
        // guardamos el id y el nombre de la manga
        row.Manga=row.ID;
        row.Nombre=row.Descripcion;
        setManga(row);
        // cannot use loadcontents, because need to execute commands, _after_ html document load success
        var infomanga="../console/dialogs/infomanga.php?Federation="+workingData.federation+"&Manga="+workingData.manga;
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
        autoBackupDatabase(1,"");
    }
});
// to allow inspect data
addSimpleKeyHandler('#competicion-listamangas',"");

//tooltips
addTooltip($('#competicion-entrenamientosBtn').linkbutton(),"<?php _e('View/Edit Training session timetable for the contest')?>");
addTooltip($('#competicion-ordentandasBtn').linkbutton(),"<?php _e('View/Edit Rounds and series order<br />on this journey')?>");
addTooltip($('#competicion-ordensalidaBtn').linkbutton(),"<?php _e('View/Edit Starting order on selected round');?>");
addTooltip($('#competicion-competicionBtn').linkbutton(),"<?php _e('Insert/Edit competitors results');?>");
addTooltip($('#competicion-resultmangaBtn').linkbutton(),"<?php _e('Review Partial results on selected round');?>");
</script>
    