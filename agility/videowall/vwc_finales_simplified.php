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
vwc_finales.php

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

<!--
Pantalla de de visualizacion combinada llamada/resultados
Modelo simplificado de resultados finales
 prefijos:
  vw_ commun para todos los marcadores
  vwc_ comun para todos los paneles combinados
  vwcp_ id asociados al panel combinado de parciales
  vwcf_ id asociados al panel combinado de finales
  vwcs_ id's exclusivos del panel combinado simplificado
-->
<div id="vwcf-window">
    <div id="vwcf-layout">
        <div data-options="region:'north'" class="vwc_top" style="height:10%;padding:5px"> <!-- CABECERA -->
            <div style="display:inline-block;width=100%;padding:0px" class="vwc_header">
                <img  id="vwc_header-logo" src="/agility/images/logos/rsce.png"/>
                <span id="vwcs_title">
                    <span id="vwc_header-infoprueba"><?php _e('Contest'); ?></span> -
                    <span id="vwc_header-infojornada"><?php _e('Journey'); ?></span>
                    <span id="vwc_header-ring" style="display:none; text-align:right"><?php _e('Ring'); ?></span>
                </span>
                <span id="header-combinadaFlag" style="display:none">true</span> <!--indicador de combinada-->
                <span id="vwcf_header-NombreRonda" style="display:none"><?php _e('Series');?></span>
                <span id="vwc_header-calltoring" style="text-align:left">
                    <span id="vwcf_header-NombreTanda"><?php _e('Round');?></span>
                </span>
                <span id="vwcs_header-trs">
                    <!-- Datos de TRS y TRM -->
                    <table class="vw_trs" style="border-collapse:collapse">
                        <tbody>
                        <tr style="text-align:left;border-bottom:1pt solid black;">
                            <td id="vwcf_finales-Manga1" colspan="2"><?php _e('Round'); ?> 1:</td>
                            <td id="vwcf_finales-Distancia1"><?php _e('Dist'); ?>:</td>
                            <td id="vwcf_finales-Obstaculos1"><?php _e('Obst'); ?>:</td>
                            <td ><?php _e('SCT'); ?>: <span id="vwcf_finales-TRS1"></span>s.</td>
                            <td ><?php _e('MCT'); ?>: <span id="vwcf_finales-TRM1"></span>s.</td>
                            <td id="vwcf_finales-Velocidad1"><?php _e('Vel'); ?>:</td>
                        </tr>
                        <tr style="text-align:left">
                            <td id="vwcf_finales-Manga2" colspan="2"><?php _e('Round'); ?> 2:</td>
                            <td id="vwcf_finales-Distancia2"><?php _e('Dist'); ?>:</td>
                            <td id="vwcf_finales-Obstaculos2"><?php _e('Obst'); ?>:</td>
                            <td ><?php _e('SCT'); ?>: <span id="vwcf_finales-TRS2"></span>s.</td>
                            <td ><?php _e('MCT'); ?>: <span id="vwcf_finales-TRM2"></span>s.</td>
                            <td id="vwcf_finales-Velocidad2"><?php _e('Vel'); ?>:</td>
                        </tr>
                        </tbody>
                    </table>
                </span>
            </div>
        </div>
        <div data-options="region:'west'" style="width:34%;"> <!-- LLAMADA A PISTA -->
            <table id="vwc_llamada-datagrid"></table>
        </div>
        <div data-options="region:'center',border:false" class="vwc_top"><!-- Espacio vacio -->&nbsp;</div>
        <div data-options="region:'east'" style="width:65%;"> <!-- RESULTADOS PARCIALES -->
            <!-- tabla de clasificaciones -->
            <table id="vwcf_clasificacion-datagrid"></table>
        </div>
        <div data-options="region:'south',border:false" style="height:22%;">
            <div id="vwcf-layout2">
                <div data-options="region:'north'" style="height:30%" class="vwc_live"> <!-- DATOS DEL PERRO EN PISTA -->
                    <div id="vwcf_common" style="display:inline-block;width:100%" >
                        <!-- Informacion del participante -->
                        <span class="vwc_dlabel" id="vwls_Numero"><?php _e('Num'); ?></span>
                        <span style="display:none" id="vwls_Perro">0</span>
                        <img class="vwc_logo" id="vwls_Logo" alt="Logo" src="/agility/images/logos/rsce.png"/>
                        <span class="vwc_dlabel" id="vwls_Dorsal"><?php _e('Dorsal'); ?></span>
                        <span class="vwc_data" id="vwls_Nombre"><?php _e('Name'); ?></span>
                        <span class="vwc_dlabel" id="vwls_NombreGuia"><?php _e('Handler'); ?></span>
                        <span class="vwc_dlabel" id="vwls_NombreClub" style="display:none"><?php _e('Club'); ?></span>
                        <span class="vwc_dlabel" id="vwls_Categoria" style="display:none"><?php _e('Category'); ?></span>
                        <span class="vwc_dlabel" id="vwls_Grado" style="display:none"><?php _e('Grade'); ?></span>
                        <span class="vwc_dlabel" id="vwls_Celo"><?php _e('Heat'); ?></span>
                        <!-- datos de resultados -->
                        <span class="vwc_dlabel" id="vwls_FaltasLbl"><?php _e('F'); ?>:</span>
                        <span class="vwc_data"  id="vwls_Faltas">0</span>
                        <span class="vwc_dlabel" id="vwls_RehusesLbl"><?php _e('R'); ?>:</span>
                        <span class="vwc_data"  id="vwls_Rehuses">0</span>
                        <span class="vwc_dlabel" id="vwls_TocadosLbl"><?php _e('T'); ?>:</span>
                        <span class="vwc_data"  id="vwls_Tocados">0</span>
                        <!-- Informacion de cronometro -->
                        <span class="vwc_dtime"  id="vwls_Tiempo">00.000</span>
                        <span style="display:none" id="vwls_TIntermedio">00.000</span>
                        <span style="display:none" id="vwls_Eliminado">0</span>
                        <span style="display:none" id="vwls_NoPresentado">0</span>
                        <span class="vwc_dtime"  id="vwls_Puesto">Puesto</span>
                        <span id="vwls_timestamp" style="display:none"></span>
                    </div>
                </div>
                <div data-options="region:'center',border:false" class="vwc_bottom"> <!-- PATROCINADORES -->
                    <div style="display:inline-block;width=100%;padding:20px" class="vwc_footer">
                        <span id="vw_footer-footerData"></span>
                    </div>
                </div>
                <div data-options="region:'east'" style="width:68%"> <!-- ULTIMOS TRES RESULTADOS -->
                    <!-- tabla de ultimos 4 resultados -->
                    <table id="vwcf_ultimos-datagrid"> </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- declare a tag to attach a chrono object to -->
<div id="cronometro"><span id="vwls_StartStopFlag" style="display:none">Start</span></div>

<script type="text/javascript">

    // create a Chronometer instance
    $('#cronometro').Chrono( {
        seconds_sel: '#vwls_timestamp',
        auto: false,
        interval: 50,
        showMode: 2,
        onUpdate: function(elapsed,running,pause) {
            var time=parseFloat(elapsed/1000);
            $('#vwls_Tiempo').html(toFixedT(time,(running)?1:ac_config.numdecs));
            vwcf_evalPuestoIntermedio();
            return true;
        },
        onBeforePause:function() { $('#vwls_Tiempo').addClass('blink'); return true; },
        onBeforeResume: function() { $('#vwls_Tiempo').removeClass('blink'); return true; },
        onBeforeReset: function() { $('#vwls_Tiempo').removeClass('blink'); return true; }
    });

    $('#vwcf-layout').layout({fit:true});
    $('#vwcf-layout2').layout({fit:true});

    $('#vwcf-window').window({
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

    $('#vwcf_clasificacion-datagrid').datagrid({
        // propiedades del panel asociado
        fit: true,
        border: false,
        closable: false,
        collapsible: false,
        collapsed: false,
        // propiedades del datagrid
        // no tenemos metodo get ni parametros: directamente cargamos desde el datagrid
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        singleSelect: true,
        autoRowHeight:true, // let the formatters decide the size
        loadMsg: "<?php _e('Updating round scores');?>...",
        rowStyler:myRowStyler,
        onBeforeLoad: function (param) {
            // do not update until 'open' received
            if( $('#vwcf_header-infoprueba').html()==='<?php _e('Contest'); ?>') return false;
            return true;
        },
        onLoadSuccess: function(data) {
            $('#vwcf_clasificacion-datagrid').datagrid('scrollTo',0); // point to first result
        },
        columns:[[
            { field:'Licencia',     width:'40%', align:'center', title: '<span class="vws_theader"><?php _e('Competitor data'); ?></span>', colspan:4},
            { field:'C1',           width:'20%', align:'center', title: '<span class="vws_theader" id="vwcf_finales_thead_m1"><?php _e('Round'); ?> 1</span>', colspan:3},
            { field:'C2',           width:'20%', align:'center', title: '<span class="vws_theader" id="vwcf_finales_thead_m2"><?php _e('Round'); ?> 2</span>', colspan:3},
            { field:'Clasificacion',width:'20%', align:'center', title: '<span class="vws_theader"><?php _e('Final scores'); ?></span>', colspan:3}
        ],[
            {field:'LogoClub',		width:'5%', align:'left',     formatter:formatLogo, title:''},
            {field:'Dorsal',		width:'5%', align:'center',     title:"<?php _e('Dors'); ?>."},
            {field:'Nombre',		width:'10%',align:'center',   formatter:formatBold,   title:"<?php _e('Name'); ?>"},
            {field:'NombreGuia',	width:'20%',align:'right',    title:"<?php _e('Handler'); ?>"},
            {field:'T1',			width:'8%', align:'right',    formatter:formatT1, styler:formatBorder, title:"<?php _e('Time'); ?>."},
            {field:'P1',			width:'8%', align:'right',    formatter:formatP1, title:"<?php _e('Penal'); ?>."},
            {field:'Puesto1',		width:'4%', align:'center',   title:"<?php _e('Pos'); ?>."},
            {field:'T2',			width:'8%', align:'right',    formatter:formatT2, styler:formatBorder, title:"<?php _e('Time'); ?>."},
            {field:'P2',			width:'8%', align:'right',    formatter:formatP2, title:"<?php _e('Penal'); ?>."},
            {field:'Puesto2',		width:'4%', align:'center',   title:"<?php _e('Pos'); ?>"},
            {field:'Tiempo',		width:'8%', align:'right',    formatter:formatTF, styler:formatBorder,    title:"<?php _e('Time'); ?>"},
            {field:'Penalizacion',	width:'8%', align:'right',    formatter:formatPenalizacionFinal,  title:"<?php _e('Penaliz'); ?>."},
            {field:'Puesto',		width:'4%', align:'center',   formatter:formatPuestoFinalBig, title:"<?php _e('Position'); ?>"}
        ]]
    });

    $('#vwcf_ultimos-datagrid').datagrid({
        // propiedades del panel asociado
        fit: true,
        border: false,
        closable: false,
        collapsible: false,
        collapsed: false,
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        singleSelect: true,
        autoRowHeight: true,
        rowStyler:myRowStyler,
        columns: [[
            {field:'Orden',		    width:'5%', align:'center',   formatter:formatOrdenLlamadaPista,  title:"#"},
            {field:'LogoClub',		width:'5%', align:'left',     formatter:formatLogo,               title:""},
            {field:'Dorsal',		width:'5%', align:'center',                                         title:"<?php _e('Dors'); ?>."},
            {field:'Nombre',		width:'9%', align:'center',   formatter:formatBold,               title:"<?php _e('Name'); ?>"},
            {field:'NombreGuia',	width:'19%',align:'right',                                        title:"<?php _e('Handler'); ?>"},
            {field:'NombreClub',	hidden:true},
            {field:'T1',			width:'7%', align:'right',    formatter:formatT1, styler:formatBorder, title:"<?php _e('Time'); ?>."},
            {field:'P1',			width:'8%', align:'right',    formatter:formatP1,                 title:"<?php _e('Penal'); ?>."},
            {field:'Puesto1',		width:'4%', align:'center',                                       title:"<?php _e('Pos'); ?>."},
            {field:'T2',			width:'7%', align:'right',    formatter:formatT2, styler:formatBorder, title:"<?php _e('Time'); ?>."},
            {field:'P2',			width:'8%', align:'right',    formatter:formatP2,                 title:"<?php _e('Penal'); ?>."},
            {field:'Puesto2',		width:'4%', align:'center',                                       title:"<?php _e('Pos'); ?>"},
            {field:'Tiempo',		width:'8%', align:'right',    formatter:formatTF, styler:formatBorder,    title:"<?php _e('Time'); ?>"},
            {field:'Penalizacion',	width:'7%', align:'right',    formatter:formatPenalizacionFinal,  title:"<?php _e('Penaliz'); ?>."},
            {field:'Puesto',		width:'4%', align:'center',   formatter:formatPuestoFinalBig,     title:"<?php _e('Position'); ?>"}
        ]]
    });

    $('#vwc_llamada-datagrid').datagrid({
        // propiedades del panel asociado
        fit: true,
        border: false,
        closable: false,
        collapsible: false,
        collapsed: false,
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        singleSelect: true,
        autoRowHeight: true,
        columns:[[
            { field:'Licencia',     width:'100%',align:'center', title: '<span class="vws_theader"><?php _e("Call to ring"); ?></span>', colspan:11}
            ],[
            { field:'Orden',		width:'10%', align:'center', title: '#', formatter:formatOrdenLlamadaPista},
            { field:'Logo', 		width:'10%', align:'center', title: '', formatter:formatLogo},
            { field:'Manga',		hidden:true },
            { field:'Perro',		hidden:true },
            { field:'Equipo',		hidden:true },
            { field:'Dorsal',		width:'10%', align:'center', title: '<?php _e('Dorsal'); ?>'},
            { field:'Nombre',		width:'25%', align:'center',  title: '<?php _e('Name'); ?>',formatter:formatBold},
            { field:'NombreGuia',	width:'40%', align:'right', title: '<?php _e('Handler'); ?>',formatter:formatLlamadaGuia },
            { field:'NombreClub',	hidden:true },
            { field:'NombreEquipo',	hidden:true },
            { field:'Celo',	        width:'5%', align:'center', title: '<?php _e('Heat'); ?>',formatter:formatCelo }
            ]
        ],
        rowStyler:myRowStyler,
        onLoadSuccess: function(data) {
            var mySelf=$('#vwc_llamada-datagrid');
            mySelf.datagrid('fitColumns'); // expand to max width
        },
        onBeforeLoad: function(param) {
            // do not update until 'open' received
            if( $('#vwc_header-infoprueba').html()==='<?php _e('Contest'); ?>') return false;
            return true;
        }
    });

    // header elements layout
    var layout= {'rows':110,'cols':1900};
    doLayout(layout,"#vwc_header-logo",	        0,	    0,	100,    100	);
    doLayout(layout,"#vwcs_title",	            120,	0,	1760,	50	);
    doLayout(layout,"#vwc_header-calltoring",	120,	65,	800,	25	);
    doLayout(layout,"#vwcs_header-trs",	        1000,	50,	880,	60	);
    // livedata elements layout
    var liveLayout = {'rows':200,'cols':1900};
    doLayout(liveLayout,"#vwls_Numero",	        0,	    25,	    70,	    150	);
    doLayout(liveLayout,"#vwls_Logo",	        100,	10,	    100,	180	);
    doLayout(liveLayout,"#vwls_Dorsal",	        220,	25,	    80, 	150	);
    doLayout(liveLayout,"#vwls_Nombre",	        320,    25,	    350,	150	);
    doLayout(liveLayout,"#vwls_NombreGuia",	    670,	25,     450,	150	);
    doLayout(liveLayout,"#vwls_Celo",	        1120,   25,	    80,	150	);
    // doLayout(liveLayout,"#vwls_NombreClub",	    600,	100,    300,	100	);
    // doLayout(liveLayout,"#vwls_Categoria",	    0,	    0,	100,	100	);
    // doLayout(liveLayout,"#vwls_Grado",	        0,	    0,	100,	100	);
    doLayout(liveLayout,"#vwls_FaltasLbl",	    1200,	25,     50,	    150	);
    doLayout(liveLayout,"#vwls_Faltas",	        1250,	25,     50,	    150	);
    doLayout(liveLayout,"#vwls_RehusesLbl",	    1300,	25,	    50,	    150	);
    doLayout(liveLayout,"#vwls_Rehuses",	    1350,	25,	    50,	    150	);
    doLayout(liveLayout,"#vwls_TocadosLbl",	    1400,	25,	    50,	    150	);
    doLayout(liveLayout,"#vwls_Tocados",	    1450,	25,	    50,	    150	);
    doLayout(liveLayout,"#vwls_Tiempo",	        1500,	25, 	200,	150	);
    doLayout(liveLayout,"#vwls_Puesto",	        1700,	25, 	200,	150	);

    var eventHandler= {
        'null': null,// null event: no action taken
        'init': function (event, time) { // operator starts tablet application
            $('#vwcf_header-infoprueba').html('<?php _e("Contest"); ?>');
            $('#vwcf_header-infojornada').html('<?php _e("Journey"); ?>');
            $('#vwcf_header-infomanga').html("(<?php _e('No round selected');?>)");
            vw_updateWorkingData(event,function(e,d){
                vwc_updateDataInfo(e,d); // fix header
                vw_formatResultadosDatagrid(e,d); // fix team/logos/cat/grade presentation
                vwcf_updateLlamada(e,d);
            });
        },
        'open': function (event, time) { // operator select tanda
            vw_updateWorkingData(event,function(e,d){
                vwc_updateDataInfo(e,d);
                /* vw_updateFinales(e,d); */ // required to be done at updateLlamada
                vwcf_updateLlamada(e,d);
            });
        },
        'close': function (event, time) { // no more dogs in tanda
            vw_updateWorkingData(event,function(e,d){
                vwcf_updateLlamada({'Dog':-1},d); // seek at end of list
            });
        },
        'datos': function (event, time) {      // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
            vwls_updateData(event);
            vwcf_evalPuestoIntermedio();
        },
        'llamada': function (event, time) {    // llamada a pista
            var crm=$('#cronometro');
            myCounter.stop();
            crm.Chrono('stop',time);
            crm.Chrono('reset',time);
            vw_updateWorkingData(event,function(e,d){
                vwcf_updateLlamada(e,d);
            });
        },
        'salida': function (event, time) {     // orden de salida
            myCounter.start();
            vwcf_evalPuestoIntermedio();
        },
        'start': function (event, time) {      // start crono manual
            // si crono automatico, ignora
            var ssf = $('#vwls_StartStopFlag');
            if (ssf.text() === "Auto") return;
            ssf.text("Stop");
            myCounter.stop(); // stop 15 seconds countdown if needed
            var crm = $('#cronometro');
            crm.Chrono('stop', time);
            crm.Chrono('reset');
            crm.Chrono('start', time);
            vwcf_evalPuestoIntermedio();
        },
        'stop': function (event, time) {      // stop crono manual
            $('#vwls_StartStopFlag').text("Start");
            myCounter.stop();
            $('#cronometro').Chrono('stop', time);
        },
        // nada que hacer aqui: el crono automatico se procesa en el tablet
        'crono_start': function (event, time) { // arranque crono automatico
            var crm = $('#cronometro');
            myCounter.stop();
            $('#vwls_StartStopFlag').text('Auto');
            // si esta parado, arranca en modo automatico
            if (!crm.Chrono('started')) {
                crm.Chrono('stop', time);
                crm.Chrono('reset');
                crm.Chrono('start', time);
                vwcf_evalPuestoIntermedio();
                return
            }
            if (ac_config.crono_resync === "0") {
                crm.Chrono('reset'); // si no resync, resetea el crono y vuelve a contar
                crm.Chrono('start', time);
            } // else wait for chrono restart event
        },
        'crono_restart': function (event, time) {	// paso de tiempo manual a automatico
            $('#cronometro').Chrono('resync', event['stop'], event['start']);
        },
        'crono_int': function (event, time) {	// tiempo intermedio crono electronico
            var crm = $('#cronometro');
            if (!crm.Chrono('started')) return;	// si crono no esta activo, ignorar
            crm.Chrono('pause', time);
            vwcf_evalPuestoIntermedio();
            setTimeout(function () {
                crm.Chrono('resume');
            }, 5000);
        },
        'crono_stop': function (event, time) {	// parada crono electronico
            $('#vwls_StartStopFlag').text("Start");
            $('#cronometro').Chrono('stop', time);
        },
        'crono_reset': function (event, time) {	// puesta a cero del crono electronico
            var crm = $('#cronometro');
            myCounter.stop();
            $('#vwls_StartStopFlag').text("Start");
            crm.Chrono('stop', time);
            crm.Chrono('reset', time);
        },
        'crono_dat': function(event,time) {      // actualizar datos -1:decrease 0:ignore 1:increase
            vwls_updateChronoData(event);
            vwcf_evalPuestoIntermedio();
        },
        'crono_error': null, // fallo en los sensores de paso
        'aceptar': function (event,time) { // operador pulsa aceptar
            myCounter.stop();
            $('#cronometro').Chrono('stop', time);  // nos aseguramos de que los cronos esten parados
            vw_updateWorkingData(event,function(e,d){
                /* vw_updateFinales(e,d); */ // required to be done at
            });
        },
        'cancelar': function (event,time) {  // operador pulsa cancelar
            var crm = $('#cronometro');
            myCounter.stop();
            crm.Chrono('stop', time);
            crm.Chrono('reset', time);
        },
        'camera':	null, // change video source
        'info': null // click on user defined tandas
    };
</script>