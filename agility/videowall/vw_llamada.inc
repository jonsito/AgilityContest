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

<!-- Pantalla de llamada a pista -->

<div id="vw_llamada-window">
    <div id="vw_llamada-layout" style="width:100%">
        <div id="vw_llamada-Cabecera" data-options="region:'north',split:false" style="height:100px" class="vw_floatingheader">
            <img id="vw_header-logo" src="/agility/images/logos/rsce.png" width="75" style="float:left;"/>
            <span style="float:left;padding:10px" id="vw_header-infoprueba">Cabecera</span>
            <div style="float:right;padding:10px;text-align:right;">
                <span id="vw_header-texto">Llamada a pista</span>&nbsp;-&nbsp;
                <span id="vw_header-ring">Ring</span>
            </div>
        </div>
        <div id="vw_llamada-data" data-options="region:'center'" >
            <table id="vw_llamada-datagrid"></table>
        </div>
        <div id="vw_llamada-footer" data-options="region:'south',split:false" class="vw_floatingfooter">
            <span id="vw_footer-footerData"></span>
        </div>
    </div>
</div> <!-- vw_llamada-window -->

<script type="text/javascript">

$('#vw_llamada-layout').layout({fit:true});

$('#vw_llamada-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true,
	onOpen: function() {
        startEventMgr(workingData.sesion,vw_procesaLlamada);
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
        { field:'Orden',		width:'5%', align:'center', title: 'Orden', formatter:formatOrdenLlamadaPista},
        { field:'Logo', 		width:'5%', align:'center', title: '', formatter:formatLogo},
        { field:'Manga',		hidden:true },
        { field:'Perro',		hidden:true },
        { field:'Equipo',		hidden:true },
        { field:'NombreEquipo',	hidden:true },
        { field:'Dorsal',		width:'5%', align:'center', title: 'Dorsal'},
        { field:'Licencia',		width:'7%%', align:'center',  title: 'Licencia'},
        { field:'Nombre',		width:'15%', align:'center',  title: 'Nombre',formatter:formatBold},
        { field:'Raza',		    width:'10%', align:'center', title: 'Raza'},
        { field:'NombreGuia',	width:'22%', align:'right', title: 'Guia',formatter:formatLlamadaGuia },
        { field:'NombreClub',	width:'10%', align:'right', title: 'Club' },
        { field:'Categoria',	width:'4%', align:'center', title: 'Cat.' },
        { field:'Grado',	    width:'4%', align:'center', title: 'Grado' },
        { field:'Celo',	        width:'4%', align:'center', title: 'Celo.',formatter:formatCelo },
        { field:'Observaciones',width:'8%', align:'center', title: 'Observaciones' }
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