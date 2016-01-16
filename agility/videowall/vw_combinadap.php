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

<!--
Pantalla de de visualizacion combinada llamada/parciales
 prefijos:
  vw_ commun para todos los marcadores
  vwc_ comun para todos los paneles combinados
  vwcp_ id asociados al panel combinado de parciales
  vwcf_ id asociados al panel combinado de finales
-->
<div id="vwcp-window">
    <div id="vwcp-layout">
        <div data-options="region:'north'" class="vwc_top" style="height:10%;padding:5px"> <!-- CABECERA -->
            <div style="display:inline-block;width=100%;padding:0px" class="vwc_header">
                <img  id="vwc_header-logo" src="/agility/images/logos/rsce.png"/>
                <span id="vwc_header-infoprueba"><?php _e('Contest'); ?></span>
                <span id="vwc_header-infojornada"><?php _e('Journey'); ?></span>
                <span id="vwc_header-combinadaFlag" style="display:none">true</span> <!--indicador de combinada-->
                <span id="vwc_header-ring" style="text-align:right"><?php _e('Ring'); ?></span>
                <span id="vwc_header-calltoring" style="text-align:left"><?php _e('Call to ring'); ?></span>
                <span id="vwcp_header-partialscores" style="text-align:right">
                    <?php _e('Partial scores'); ?> -
                    <span id="vwcp_header-infomanga">&nbsp;</span>
                </span>
            </div>
        </div>
        <div data-options="region:'west'" style="width:40%;"> <!-- LLAMADA A PISTA -->
            <table id="vwc_llamada-datagrid" class="vwc_top"></table>
        </div>
        <div data-options="region:'center',border:false" class="vwc_top"><!-- Espacio vacio -->&nbsp;</div>
        <div data-options="region:'east'" style="width:55%;"> <!-- RESULTADOS PARCIALES -->
            <!-- Datos de TRS y TRM -->
            <table class="vw_trs">
                <tbody>
                <tr style="text-align:right">
                    <td><?php _e('Data'); ?>:</td>
                    <td><?php _e('Dist'); ?>:</td><td id="vwcp_parciales-Distancia" style="text-align:left;">&nbsp;</td>
                    <td><?php _e('Obst'); ?>:</td><td id="vwcp_parciales-Obstaculos" style="text-align:left;">&nbsp;</td>
                    <td><?php _e('SCT'); ?>:</td><td id="vwcp_parciales-TRS" style="text-align:left;">&nbsp;</td>
                    <td><?php _e('MCT'); ?>:</td><td id="vwcp_parciales-TRM" style="text-align:left;">&nbsp;</td>
                    <td><?php _e('Vel'); ?>:</td><td id="vwcp_parciales-Velocidad" style="text-align:left;">&nbsp;</td>
                </tr>
                </tbody>
            </table>
            <!-- tabla de resultados -->
            <table id="vwcp_parciales-datagrid"></table>
        </div>
        <div data-options="region:'south',border:false" style="height:25%;">
            <div id="vwcp-layout2">
                <div data-options="region:'north'" style="height:30%" class="vwc_live"> <!-- DATOS DEL PERRO EN PISTA -->
                    <div id="vwcp_common" style="display:inline-block;width:100%" >
                        <!-- Informacion del participante -->
                        <span class="vwc_dlabel" id="vwls_Numero"><?php _e('Num'); ?></span>
                        <span style="display:none" id="vwls_Perro">0</span>
                        <img class="vwc_logo" id="vwls_Logo" alt="Logo" src="/agility/images/logos/rsce.png"/>
                        <span class="vwc_label" id="vwls_Dorsal"><?php _e('Dorsal'); ?></span>
                        <span class="vwc_label" id="vwls_Nombre"><?php _e('Name'); ?></span>
                        <span class="vwc_label" id="vwls_NombreGuia"><?php _e('Handler'); ?></span>
                        <span class="vwc_label" id="vwls_NombreClub"><?php _e('Club'); ?></span>
                        <span class="vwc_label" id="vwls_Categoria" style="display:none"><?php _e('Category'); ?></span>
                        <span class="vwc_label" id="vwls_Grado" style="display:none"><?php _e('Grade'); ?></span>
                        <span class="vwc_label" id="vwls_Celo"><?php _e('Heat'); ?></span>
                        <!-- datos de resultados -->
                        <span class="vwc_dlabel" id="vwls_FaltasLbl"><?php _e('F'); ?>:</span>
                        <span class="vwc_data"  id="vwls_Faltas">0</span>
                        <span class="vwc_dlabel" id="vwls_TocadosLbl"><?php _e('T'); ?>:</span>
                        <span class="vwc_data"  id="vwls_Tocados">0</span>
                        <span class="vwc_dlabel" id="vwls_RehusesLbl"><?php _e('R'); ?>:</span>
                        <span class="vwc_data"  id="vwls_Rehuses">0</span>
                        <!-- Informacion de cronometro -->
                        <span class="vwc_dtime"  id="vwls_Tiempo">00.00</span>
                        <span id="vwls_timestamp" style="display:none"></span>
                    </div>
                </div>
                <div data-options="region:'center',border:false" class="vwc_bottom"> <!-- PATROCINADORES -->
                    <div style="display:inline-block;width=100%;padding:20px" class="vwc_footer">
                        <span id="vw_footer-footerData"></span>
                    </div>
                </div>
                <div data-options="region:'east'" style="width:60%"> <!-- ULTIMOS TRES RESULTADOS -->
                    <!-- tabla de ultimos 4 resultados -->
                    <table id="vwcp_lastparciales-datagrid"></table>
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
            $('#vwls_Tiempo').html(parseFloat(elapsed/1000).toFixed((running)?1:ac_config.numdecs));
            return true;
        },
        onBeforePause:function() { $('#vwls_Tiempo').addClass('blink'); return true; },
        onBeforeResume: function() { $('#vwls_Tiempo').removeClass('blink'); return true; },
        onBeforeReset: function() { $('#vwls_Tiempo').removeClass('blink'); return true; }
    });

    $('#vwcp-layout').layout({fit:true});
    $('#vwcp-layout2').layout({fit:true});

    $('#vwcp-window').window({
        fit:true,
        noheader:true,
        border:false,
        closable:false,
        collapsible:false,
        collapsed:false,
        resizable:true,
        onOpen: function() {
            startEventMgr(workingData.sesion,vwcp_procesaCombinada);
        }
    });

    $('#vwcp_parciales-datagrid').datagrid({
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
            if( $('#vwcp_header-infoprueba').html()==='<?php _e('Contest'); ?>') return false;
            return true;
        }
    });

    $('#vwc_llamada-datagrid').datagrid({
        // propiedades del panel asociado
        fit: true,
        border: false,
        closable: false,
        collapsible: false,
        collapsed: false,
        // propiedades del datagrid
        // method: 'get',
        // url: '/agility/server/database/resultadosFunctions.php',
        // queryParams: {
        //    Prueba: workingData.prueba,
        //    Jornada: workingData.jornada,
        //    Manga: workingData.manga,
        //     Mode: (workingData.datosManga.Recorrido!=2)?0:4, // def to 'Large' or 'LMS' depending of datosmanga
        //    Operation: 'getResultados'
        // },
        // loadMsg: "<?php _e('Updating list of teams to be called to ring');?> ...",
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        singleSelect: true,
        autoRowHeight: true,
        columns:[[
            { field:'Orden',		width:'10%', align:'center', title: '#', formatter:formatOrdenLlamadaPista},
            { field:'Logo', 		width:'10%', align:'center', title: '', formatter:formatLogo},
            { field:'Manga',		hidden:true },
            { field:'Perro',		hidden:true },
            { field:'Equipo',		hidden:true },
            { field:'NombreEquipo',	hidden:true },
            { field:'Dorsal',		width:'10%', align:'center', title: '<?php _e('Dorsal'); ?>'},
            // { field:'Licencia',		width:'10%', align:'center',  title: '<?php _e('License'); ?>'},
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
            if( $('#vwc_header-infoprueba').html()==='<?php _e('Contest'); ?>') return false;
            return true;
        }
    });

    // header elements layout
    var layout= {'rows':110,'cols':1900};
    doLayout(layout,"#vwc_header-logo",	        0,	    0,	100,    100	);
    doLayout(layout,"#vwc_header-infoprueba",	120,	0,	1760,	25	);
    doLayout(layout,"#vwc_header-infojornada",	120,	35,	1760,	25	);
    doLayout(layout,"#vwc_header-ring",     	120,	35,	1760,	25	);
    doLayout(layout,"#vwc_header-calltoring",	120,	65,	5800,	25	);
    doLayout(layout,"#vwcp_header-partialscores",700,	65,	1180,	25	);
    // livedata elements layout
    var liveLayout = {'rows':200,'cols':1900};
    doLayout(liveLayout,"#vwls_Numero",	        10,	    25,	    90,	    150	);
    doLayout(liveLayout,"#vwls_Logo",	        120,	10,	    120,	180	);
    doLayout(liveLayout,"#vwls_Dorsal",	        250,	10,	    100,	100	);
    doLayout(liveLayout,"#vwls_Nombre",	        375,    10,	    375,	100	);
    doLayout(liveLayout,"#vwls_Celo",	        800,    10,	    100,	100	);
    doLayout(liveLayout,"#vwls_NombreGuia",	    250,	100,    550,	100	);
    doLayout(liveLayout,"#vwls_NombreClub",	    700,	100,    200,	100	);
    // doLayout(liveLayout,"#vwls_Categoria",	    0,	    0,	100,	100	);
    // doLayout(liveLayout,"#vwls_Grado",	        0,	    0,	100,	100	);
    doLayout(liveLayout,"#vwls_FaltasLbl",	    900,	25,     100,	150	);
    doLayout(liveLayout,"#vwls_Faltas",	        1000,	25,     100,	150	);
    doLayout(liveLayout,"#vwls_TocadosLbl",	    1100,	25,	    100,	150	);
    doLayout(liveLayout,"#vwls_Tocados",	    1200,	25,	    100,	150	);
    doLayout(liveLayout,"#vwls_RehusesLbl",	    1300,	25,	    100,	150	);
    doLayout(liveLayout,"#vwls_Rehuses",	    1400,	25,	    100,	150	);
    doLayout(liveLayout,"#vwls_Tiempo",	        1500,	25, 	300,	150	);

</script>