<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Videowall::parciales");
if ( ! $am->allowed(ENABLE_VIDEOWALL)) die("<h1>VideoWall feature is not allowed with current license</h1>");
// tool to perform automatic upgrades in database when needed
require_once(__DIR__."/../server/upgradeVersion.php");
?>
<!--
vw_parciales.inc

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

<!-- Presentacion de resultados parciales -->

<div id="vw_parciales-window">
    <div id="vw_parciales-layout" style="width:100%">
        <div id="vw_parciales-Cabecera" data-options="region:'north',split:false" style="height:175px" class="vw_floatingheader">
            <img id="vw_header-logo" src="/agility/images/logos/rsce.png" width="75" style="float:left;"/>
            <span style="float:left;padding:10px" id="vw_header-infoprueba">Cabecera</span>

            <div style="float:right;padding:10px;text-align:right;">
                <span id="vw_header-texto">Resultados provisionales</span>&nbsp;-&nbsp;
                <span id="vw_header-ring">Ring</span>
                <br />
                <span id="vw_header-infomanga" style="width:200px">(Manga no definida)</span>
            </div>
            <!-- Datos de TRS y TRM -->
            <table class="vw_trs">
                <thead>
                <tr>
                    <th id="vw_parciales-NombreManga" colspan="3">&nbsp;</th>
                    <th id="vw_parciales-Juez1" colspan="4" style="text-align:center">Juez 1:</th>
                    <th id="vw_parciales-Juez2" colspan="4" style="text-align:center">Juez 2:</th>
                </tr>
                </thead>
                <tbody>
                <tr style="text-align:right">
                    <td>Datos de la manga:</td>
                    <td>Dist:</td><td id="vw_parciales-Distancia" style="text-align:left;">&nbsp;</td>
                    <td>Obst:</td><td id="vw_parciales-Obstaculos" style="text-align:left;">&nbsp;</td>
                    <td>TRS:</td><td id="vw_parciales-TRS" style="text-align:left;">&nbsp;</td>
                    <td>TRM:</td><td id="vw_parciales-TRM" style="text-align:left;">&nbsp;</td>
                    <td>Vel:</td><td id="vw_parciales-Velocidad" style="text-align:left;">&nbsp;</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="vw_parciales-data" data-options="region:'center'" >
            <table id="vw_parciales-datagrid"></table>
        </div>
        <div id="vw_parciales-footer" data-options="region:'south',split:false" class="vw_floatingfooter">
            <span id="vw_footer-footerData"></span>
        </div>
    </div>
</div> <!-- vw_parciales-window -->

<script type="text/javascript">

$('#vw_parciales-layout').layout({fit:true});

$('#vw_parciales-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true,
	onOpen: function() {
        startEventMgr(workingData.sesion,vw_procesaParciales);
	}
});

$('#vw_parciales-datagrid').datagrid({
    // propiedades del panel asociado
    fit: true,
    border: false,
    closable: false,
    collapsible: false,
    collapsed: false,
    // propiedades del datagrid
    method: 'get',
    url: '/agility/server/database/resultadosFunctions.php',
    queryParams: {
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Manga: workingData.manga,
        Mode: (workingData.datosManga.Recorrido!=2)?0:4, // def to 'Large' or 'LMS' depending of datosmanga
        Operation: 'getResultados'
    },
    loadMsg: "Actualizando resultados de la manga ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: true,
    // view: gview,
    // groupField: 'NombreEquipo',
    // groupFormatter: formatTeamResults,
    // toolbar: '#resultadosmanga-toolbar',
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'Perro',		hidden:true },
        { field:'Raza',		    hidden:true },
        { field:'Equipo',		hidden:true },
        { field:'NombreEquipo',	hidden:true },
        { field:'Dorsal',		width:'5%', align:'center', title: 'Dorsal'},
        { field:'LogoClub',		width:'5%', align:'center', title: '', formatter:formatLogo},
        { field:'Licencia',		width:'5%%', align:'center',  title: 'Licencia'},
        { field:'Nombre',		width:'10%', align:'center',  title: 'Nombre'},
        { field:'NombreGuia',	width:'15%', align:'right', title: 'Guia' },
        { field:'NombreClub',	width:'12%', align:'right', title: 'Club' },
        { field:'Categoria',	width:'4%', align:'center', title: 'Cat.' },
        { field:'Grado',	    width:'4%', align:'center', title: 'Grad.' },
        { field:'Faltas',		width:'4%', align:'center', title: 'Faltas'},
        { field:'Rehuses',		width:'4%', align:'center', title: 'Rehuses'},
        { field:'Tocados',		width:'4%', align:'center', title: 'Tocados'},
        { field:'PRecorrido',	hidden:true },
        { field:'Tiempo',		width:'6%', align:'right', title: 'Tiempo', formatter:formatTiempo},
        { field:'PTiempo',		hidden:true },
        { field:'Velocidad',	width:'4%', align:'right', title: 'Vel.', formatter:formatVelocidad},
        { field:'Penalizacion',	width:'6%%', align:'right', title: 'Penal.', formatter:formatPenalizacion},
        { field:'Calificacion',	width:'7%', align:'center',title: 'Calificacion'},
        { field:'Puesto',		width:'4%', align:'center',  title: 'Puesto', formatter:formatPuestoBig},
        { field:'CShort',       hidden:true}
    ]],
    rowStyler:myRowStyler,
    onBeforeLoad: function(param) {
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='Cabecera') return false;
        return true;
    }
});
</script>