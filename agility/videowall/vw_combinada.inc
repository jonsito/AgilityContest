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
                        <span style="float:left;padding:5px" id="vw_header-infoprueba">Cabecera</span>
                    </td>
                    <td style="text-align:right">
                        <span id="vw_header-combinadaFlag" style="display:none">true</span> <!--indicador de combinada-->
                        <span id="vw_header-ring">Ring</span>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:left">Llamada a pista</td>
                    <td style="text-align:right">
                        Resultados Provisionales -
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
                    <td>Datos:</td>
                    <td>Dist:</td><td id="vw_parciales-Distancia" style="text-align:left;">&nbsp;</td>
                    <td>Obst:</td><td id="vw_parciales-Obstaculos" style="text-align:left;">&nbsp;</td>
                    <td>TRS:</td><td id="vw_parciales-TRS" style="text-align:left;">&nbsp;</td>
                    <td>TRM:</td><td id="vw_parciales-TRM" style="text-align:left;">&nbsp;</td>
                    <td>Vel:</td><td id="vw_parciales-Velocidad" style="text-align:left;">&nbsp;</td>
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
        startEventMgr(workingData.sesion,vw_procesaCombinada);
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
        // { field:'Dorsal',		width:'5%', align:'center', title: 'Dorsal'},
        { field:'LogoClub',		width:'10%', align:'center', title: '', formatter:formatLogo},
        // { field:'Licencia',		width:'5%%', align:'center',  title: 'Licencia'},
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
        { field:'Penalizacion',	width:'6%', align:'right', title: 'Penal.', formatter:formatPenalizacion},
        { field:'Calificacion',	width:'7%', align:'center',title: 'Calificacion'},
        { field:'Puesto',		width:'4%', align:'center',  title: 'Puesto', formatter:formatPuesto},
        { field:'CShort',       hidden:true}
    ]],
    rowStyler:myRowStyler,
    onBeforeLoad: function(param) {
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='Cabecera') return false;
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
    loadMsg: "Actualizando lista de equipos pendientes de salir...",
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
        { field:'Dorsal',		width:'5%', align:'center', title: 'Dorsal'},
        { field:'Licencia',		width:'10%', align:'center',  title: 'Licencia'},
        { field:'Nombre',		width:'15%', align:'center',  title: 'Nombre',formatter:formatBold},
        { field:'NombreGuia',	width:'30%', align:'right', title: 'Guia',formatter:formatLlamadaGuia },
        { field:'NombreClub',	width:'20%', align:'right', title: 'Club' },
        { field:'Celo',	        width:'5%', align:'center', title: 'Celo.',formatter:formatCelo },
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
        if( $('#vw_header-infoprueba').html()==='Cabecera') return false;
        return true;
    }
});

</script>