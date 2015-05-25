<!-- 
vw_ordensalida.inc

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

<!-- Presentacion del orden de salida de la jornada -->
<div id="vw_ordensalida-window">
	<div id="vw_ordensalida-layout" style="width:100%">
		<div id="vw_ordensalida-Cabecera" data-options="region:'north',split:false" style="height:100px" class="vw_floatingheader">
            <img id="vw_header-logo" src="/agility/images/logos/rsce.png" style="float:left;width:75px" />
		    <span style="float:left;padding:10px;" id="vw_header-infoprueba">Cabecera</span>
			<div style="float:right;padding:10px;text-align:right;">
                <span id="vw_header-texto"></span> Orden de Salida</span>&nbsp;-&nbsp;
                <span id="vw_header-ring">Ring</span>
                <br />
                <span id="vw_header-infomanga" style="width:200px">(Manga no definida)</span>
            </div>
		</div>
		<div id="vw_tabla" data-options="region:'center'">
			<table id="vw_ordensalida-datagrid"></table>
		</div>
        <div id="vw_ordensalida-footer" data-options="region:'south',split:false" class="vw_floatingfooter">
            <span id="vw_footer-footerData"></span>
        </div>
	</div>
</div> <!-- vw_ordensalida-window -->

<script type="text/javascript">

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
        startEventMgr(workingData.sesion,vw_procesaOrdenSalida);
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
    loadMsg: "Actualizando orden de salida ...",
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
        { field:'Logo',     	width:'5%', align:'center',	title: '',formatter: formatLogo },
        { field:'NombreEquipo',	width:'12%', align:'center',title: 'Equipo',hidden:true},
        { field:'Dorsal',		width:'5%', align:'center',	title: 'Dorsal', styler:checkPending },
        { field:'Nombre',		width:'15%', align:'left',	title: 'Nombre', formatter: formatBoldBig},
        { field:'Licencia',		width:'5%', align:'center',	title: 'Licencia'},
        { field:'NombreGuia',	width:'23%', align:'right',	title: 'Guia' },
        { field:'NombreClub',	width:'19%', align:'right',	title: 'Club' },
        { field:'Categoria',	width:'4%', align:'center',	title: 'Categ.' },
        { field:'Grado',		width:'4%', align:'center',	title: 'Grado' },
        { field:'Celo',			width:'4%', align:'center',	title: 'Celo', formatter:formatCelo },
        { field:'Observaciones',width:'12%', align:'center',title: 'Observaciones' }
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:myRowStyler,
    onBeforeLoad:function(params) {
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='Cabecera') return false;
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

    }
});

</script>