<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Videowall::parciales");
if ( ! $am->allowed(ENABLE_VIDEOWALL)) { include_once("unregistered.php"); return 0;}
?>
<!--
vwls_ordensalida.inc

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

<!-- Presentacion del orden de salida a traves de videostream -->

<div id="vw_ordensalida-window">

    <div id="vw_parent-layout" class="easyui-layout" style="width:100%;height:auto;">

        <!-- imagen de fondo -->
        <video id="vwls_video" autoplay="autoplay" preload="auto" muted="muted" loop="loop"
               poster="/agility/server/getRandomImage.php">
            <!-- http://guest:@192.168.122.168/videostream.cgi -->
            <source id="vwls_videomp4" src="" type='video/mp4'/>
            <source id="vwls_videoogv" src="" type='video/ogg'/>
            <source id="vwls_videowebm" src="" type='video/webm'/>
        </video>
        <div data-options="region:'east',split:false,border:false" style="width:5%;background-color:transparent;"></div>
        <div data-options="region:'west',split:false,border:false" style="width:30%;background-color:transparent;"></div>
        <div data-options="region:'center',border:false" style="background-color:transparent;">
        <!-- ventana interior -->
            <div id="vw_ordensalida-layout">
                <div id="vw_ordensalida-Cabecera" data-options="region:'north',split:false" class="vw_floatingheader"
                      style="height:75px;font-size:1.0em;opacity:0.5;" >
                    <span style="float:left;background:rgba(255,255,255,0.5);">
                        <img id="vw_header-logo" src="/agility/images/logos/rsce.png" width="50"/>
                    </span>
                    <span style="float:left;padding:10px" id="vw_header-infoprueba"><?php _e('Header'); ?></span>

                    <div style="float:right;padding:10px;text-align:right;">
                        <span id="vw_header-texto"><?php _e('Starting order'); ?></span>&nbsp;-&nbsp;
                        <span id="vw_header-ring"><?php _e('Ring'); ?></span>
                        <br />
                        <span id="vw_header-infomanga" style="width:200px">(<?php _e('No round selected'); ?>)</span>
                    </div>

                </div>

                <div id="vw_tabla" data-options="region:'center'">
                    <a href="#vwls_bottom" id="vwls_top"></a>
                    <table id="vw_ordensalida-datagrid"></table>
                    <a href="#vwls_top" id="vwls_bottom"></a>
                </div>

                <div id="vw_ordensalida-footer" data-options="region:'south',split:false" class="vw_floatingfooter"
                    style="font-size:1.2em;opacity:0.5;">
                    <span id="vw_footer-footerData"></span>
                </div>
            </div>
        </div>
    </div>

</div> <!-- vw_ordensalida-window -->

<script type="text/javascript">

$('#vw_parent-layout').layout({fit:true});
$('#vw_ordensalida-layout').layout({fit:true});

$('#vw_ordensalida-window').window({
    fit:true,
    noheader:true,
    border:false,
    closable:false,
    collapsible:false,
    collapsed:false,
    resizable:true,
    callback: null,
    onOpen: function() {
        startEventMgr(workingData.sesion,videowall_eventManager);
    }
});

$('#vw_ordensalida-datagrid').datagrid({
    method: 'get',
    url: '/agility/server/database/tandasFunctions.php',
    queryParams: {
        Operation: 'getDataByTanda',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: workingData.sesion // used only at startup. then use TandaID
    },
    loadMsg: "<?php _e('Updating starting order');?> ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: true,
    nowrap: false,
    fit: true,
    height: 'auto',
    columns:[[
        { field:'Prueba',		width:0, hidden:true }, // extra field to be used on form load/save
        { field:'Jornada',		width:0, hidden:true }, // extra field to be used on form load/save
        { field:'Manga',		width:0, hidden:true },
        { field:'Tanda',		width:0, hidden:true }, // string with tanda's name
        { field:'ID',			width:0, hidden:true }, // tanda ID
        { field:'Perro',     	width:'5%', align:'center',	title: '#',formatter: formatOrdenSalida },
        { field:'Pendiente',	width:0, hidden:true },
        { field:'Tanda',		width:0, hidden:true },
        { field:'Equipo',		width:0, hidden:true },
        { field:'Logo',     	width:'5%', align:'center',	title: '',formatter: formatLogoVideoWall },
        { field:'NombreEquipo',	width:'12%', align:'center',title: '<?php _e('Team'); ?>',hidden:true},
        { field:'Dorsal',		width:'5%', align:'center',	title: '<?php _e('Dorsal'); ?>', styler:checkPending },
        { field:'Nombre',		width:'15%', align:'left',	title: '<?php _e('Name'); ?>', formatter: formatBoldBig},
        { field:'Raza',         width:'12%', align:'center',title: '<?php _e('Breed'); ?>' },
        { field:'Licencia',		width:'5%', align:'center',	title: '<?php _e('License'); ?>'},
        { field:'NombreGuia',	width:'23%', align:'right',	title: '<?php _e('Handler'); ?>' },
        { field:'NombreClub',	width:'19%', align:'right',	title: '<?php _e('Club'); ?>' },
        { field:'Categoria',	width:'4%', align:'center',	title: '<?php _e('Categ'); ?>.' },
        { field:'Grado',		width:'4%', align:'center',	title: '<?php _e('Grade'); ?>' },
        { field:'Celo',			width:'4%', align:'center',	title: '<?php _e('Heat'); ?>', formatter:formatCelo }
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:myRowStyler,
    onBeforeLoad:function(params) {
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='<?php _e('Header'); ?>') return false;
        return true;
    },
    onLoadSuccess:function(){
        var mySelf=$('#vw_ordensalida-datagrid');
        // show/hide team name
        if (isTeamByJornada(workingData.datosJornada) ) {
            mySelf.datagrid('showColumn','NombreEquipo');
            mySelf.datagrid('hideColumn','Observaciones');
        } else  {
            mySelf.datagrid('hideColumn','NombreEquipo');
            mySelf.datagrid('showColumn','Observaciones');
        }
        mySelf.datagrid('fitColumns'); // expand to max width
        // and start autoscroll
        vw_autoscroll('#vw_tabla','#vwls_bottom');
    }
});

var eventHandler= {
    'null': null,// null event: no action taken
    'init': function(event) { // operator starts tablet application
        vw_updateWorkingData(event,function(evt,data){
            vw_updateDataInfo(evt,data);
            $('#vw_header-infomanga').html("(<?php _e('No round selected');?>)");
            // clear datagrid
            $('#vw_ordensalida-datagrid').datagrid('loadData', {"total":0,"rows":[]});
        });
    },
    'open': function(event){ // operator select tanda
        vw_updateWorkingData(event,function(evt,data){
            vw_updateDataInfo(evt,data);
            vw_updateOrdenSalida(evt,data);
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

    'aceptar':	null, // operador pulsa aceptar
    'cancelar': null, // operador pulsa cancelar
    'info':	null // click on user defined tandas
};

</script>