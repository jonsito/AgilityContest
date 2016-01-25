<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Videowall::combinada");
if ( ! $am->allowed(ENABLE_VIDEOWALL)) { include_once("unregistered.php"); return 0;}
?>
<!--
vw_llamada.inc

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

<!-- Pantalla de de visualizacion combinada llamada/parciales -->

<div id="vw_combinada-window">
    <div id="vw_combinada-layout" style="width:100%">
        <div id="vw_combinada-Cabecera" data-options="region:'north',split:false" style="height:110px" class="vw_floatingheader">
            <table style="width:100%">
                <tr>
                    <td style="text-align:left">
                        <img id="vw_header-logo" src="/agility/images/logos/rsce.png" width="50" style="float:left;"/>
                        <span style="float:left;padding:5px" id="vw_header-infoprueba"><?php _e('Header'); ?></span>
                    </td>
                    <td style="text-align:right">
                        <span id="vw_header-combinadaFlag" style="display:none">true</span> <!--indicador de combinada-->
                        <span id="vw_header-ring"><?php _e('Ring'); ?></span>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:left"><?php _e('Call to ring'); ?></td>
                    <td style="text-align:right">
                        <?php _e('Partial scores'); ?> -
                        <span id="vw_header-infomanga">&nbsp;</span>
                    </td>
                </tr>
            </table>
        </div>
        <div id="vw_combinada-data" data-options="region:'west',split:true" style="width:50%" >
            <table id="vw_llamada-datagrid"></table>
        </div>
        <div id="vw_combinada-data" data-options="region:'center'" >
            <!-- Datos de TRS y TRM -->
            <table class="vw_trs">
                <tbody>
                <tr style="text-align:right">
                    <td><?php _e('Data'); ?>:</td>
                    <td><?php _e('Dist'); ?>:</td><td id="vw_parciales-Distancia" style="text-align:left;">&nbsp;</td>
                    <td><?php _e('Obst'); ?>:</td><td id="vw_parciales-Obstaculos" style="text-align:left;">&nbsp;</td>
                    <td><?php _e('SCT'); ?>:</td><td id="vw_parciales-TRS" style="text-align:left;">&nbsp;</td>
                    <td><?php _e('MCT'); ?>:</td><td id="vw_parciales-TRM" style="text-align:left;">&nbsp;</td>
                    <td><?php _e('Vel'); ?>:</td><td id="vw_parciales-Velocidad" style="text-align:left;">&nbsp;</td>
                </tr>
                </tbody>
            </table>
            <table id="vw_parciales-datagrid"></table>
        </div>
        <div id="vw_combinada-footer" data-options="region:'south',split:false" class="vw_floatingfooter">
            <span id="vw_footer-footerData"></span>
        </div>
    </div>
</div> <!-- vw_combinada-window -->

<script type="text/javascript">

$('#vw_combinada-layout').layout({fit:true});

$('#vw_combinada-window').window({
    fit:true,
    noheader:true,
    border:false,
    closable:false,
    collapsible:false,
    collapsed:false,
    resizable:true,
    onOpen: function() {
        startEventMgr(workingData.sesion,videowall_eventManager);
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
    loadMsg: "<?php _e('Updating round results');?> ...",
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
        // { field:'Dorsal',		width:'5%', align:'center', title: 'Dorsal'},
        { field:'LogoClub',		width:'10%', align:'center', title: '', formatter:formatLogo},
        // { field:'Licencia',		width:'5%%', align:'center',  title: 'Licencia'},
        { field:'Nombre',		width:'12%', align:'center',  title: '<?php _e('Name'); ?>',formatter:formatBoldBig},
        { field:'NombreGuia',	width:'17%', align:'right', title: '<?php _e('Handler'); ?>' },
        { field:'NombreClub',	width:'14%', align:'right', title: '<?php _e('Club'); ?>' },
        { field:'Categoria',	width:'4%', align:'center', title: '<?php _e('Cat'); ?>.' },
        { field:'Grado',	    width:'4%', align:'center', title: '<?php _e('Grade'); ?>' },
        { field:'Faltas',		width:'4%', align:'center', title: '<?php _e('Faults'); ?>'},
        { field:'Rehuses',		width:'4%', align:'center', title: '<?php _e('Refusals'); ?>'},
        { field:'Tocados',		width:'4%', align:'center', title: '<?php _e('Touchs'); ?>'},
        { field:'PRecorrido',	hidden:true },
        { field:'Tiempo',		width:'6%', align:'right', title: '<?php _e('Time'); ?>', formatter:formatTiempo},
        { field:'PTiempo',		hidden:true },
        { field:'Velocidad',	width:'4%', align:'right', title: '<?php _e('Vel'); ?>.', formatter:formatVelocidad},
        { field:'Penalizacion',	width:'6%', align:'right', title: '<?php _e('Penal'); ?>.', formatter:formatPenalizacion},
        { field:'Calificacion',	width:'7%', align:'center',title: '<?php _e('Calification'); ?>'},
        { field:'Puesto',		width:'6%', align:'center',  title: '<?php _e('Position'); ?>', formatter:formatPuesto},
        { field:'CShort',       hidden:true}
    ]],
    rowStyler:myRowStyler,
    onBeforeLoad: function(param) {
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='<?php _e('Header'); ?>') return false;
        return true;
    }
});

$('#vw_llamada-datagrid').datagrid({
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
    loadMsg: "<?php _e('Updating list of teams to be called to ring');?> ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: true,
    columns:[[
        { field:'Orden',		width:'5%', align:'center', title: '#', formatter:formatOrdenLlamadaPista},
        { field:'Logo', 		width:'10%', align:'center', title: '', formatter:formatLogo},
        { field:'Manga',		hidden:true },
        { field:'Perro',		hidden:true },
        { field:'Equipo',		hidden:true },
        { field:'NombreEquipo',	hidden:true },
        { field:'Dorsal',		width:'5%', align:'center', title: '<?php _e('Dorsal'); ?>'},
        { field:'Licencia',		width:'10%', align:'center',  title: '<?php _e('License'); ?>'},
        { field:'Nombre',		width:'15%', align:'center',  title: '<?php _e('Name'); ?>',formatter:formatBold},
        { field:'NombreGuia',	width:'30%', align:'right', title: '<?php _e('Handler'); ?>',formatter:formatLlamadaGuia },
        { field:'NombreClub',	width:'20%', align:'right', title: '<?php _e('Club'); ?>' },
        { field:'Celo',	        width:'5%', align:'center', title: '<?php _e('Heat'); ?>',formatter:formatCelo }
    ]],
    rowStyler:myLlamadaRowStyler,
    onBeforeLoad: function(param) {
        var mySelf=$('#vw_llamada-datagrid');
        // show/hide team name
        if (isTeamByJornada(workingData.datosJornada) ) {
            mySelf.datagrid('hideColumn','Grado');
        } else  {
            mySelf.datagrid('showColumn','Grado');
        }
        mySelf.datagrid('fitColumns'); // expand to max width
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='<?php _e('Header'); ?>') return false;
        return true;
    }
});

var eventHandler= {
    'null': null,// null event: no action taken
    'init': function(event) { // operator starts tablet application
        $('#vw_header-infoprueba').html('<?php _e("Header"); ?>');
        $('#vw_header-infomanga').html("(<?php _e('No round selected');?>)");
        vw_updateWorkingData(event,function(e,d){
            vw_updateDataInfo(e,d);
            vw_initParcialesDatagrid(e,d);
            vw_updateLlamada(e,d);
        });
    },
    'open': function(event){ // operator select tanda
        vw_updateWorkingData(event,function(e,d){
            vw_updateDataInfo(e,d);
            vw_updateParciales(e,d);
            vw_updateLlamada(e,d);
        });
    },
    'datos': null,      // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
    'llamada': null,    // llamada a pista
    'salida': null,     // orden de salida
    'start': null,      // start crono manual
    'stop': null,       // stop crono manual
    // nada que hacer aqui: el crono automatico se procesa en el tablet
    'crono_start':  null, // arranque crono automatico
    'crono_restart': null,// paso de tiempo intermedio a manual
    'crono_int':  	null, // tiempo intermedio crono electronico
    'crono_stop':  null, // parada crono electronico
    'crono_reset':  null, // puesta a cero del crono electronico
    'crono_error':  null, // fallo en los sensores de paso
    'aceptar':	function(event){ // operador pulsa aceptar
        vw_updateWorkingData(event,function(e,d){
            vw_updateLlamada(e,d);
            vw_updateParciales(e,d);
        });
    },
    'cancelar': function(event){    // operador pulsa cancelar
        vw_updateWorkingData(event,function(e,d){
            vw_updateLlamada(e,d);
            vw_updateParciales(e,d);
        });
    },
    'info':	null // click on user defined tandas
};

</script>