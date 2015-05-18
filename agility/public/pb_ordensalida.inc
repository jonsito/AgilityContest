<!-- 
pb_ordensalida.inc

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
<div id="pb_ordensalida-window">
	<div id="pb_ordensalida-layout" style="width:100%">
		<div id="pb_ordensalida-Cabecera" data-options="region:'north',split:false" style="height:100px" class="pb_floatingheader">
            <img id="pb_header-logo" src="/agility/images/logos/rsce.png" width="75" style="float:left"/>
		    <span style="float:left;padding:10px;" id="pb_header-infocabecera">Cabecera</span>
			<span style="float:right;" id="pb_header-texto">
                Orden de Salida<br />
                <label for="pb_enumerateMangas">&nbsp;</label>
                <select id="pb_enumerateMangas" style="width:200px"></select>
            </span>
		</div>
		<div id="pb_tabla" data-options="region:'center'">
			<table id="pb_ordensalida-datagrid"></table>
		</div>
        <div id="pb_ordensalida-footer" data-options="region:'south',split:false" style="height:100px" class="pb_floatingfooter">
            <span id="pb_ordensalida-footerData"></span>
        </div>
	</div>
</div> <!-- pb_ordensalida-window -->

<script type="text/javascript">
$('#pb_ordensalida-layout').layout({fit:true});

$('#pb_ordensalida-window').window({
    fit:true,
    noheader:true,
    border:false,
    closable:false,
    collapsible:false,
    collapsed:false,
    resizable:true,
    callback: null,
    // 1 minute poll is enouth for this, as no expected changes during a session
    onOpen: function() {
        // update header
        pb_getHeaderInfo();
        // update footer
        var logo=nombreCategorias[workingData.federation]['logo'];
        $('#pb_ordensalida-footerData').load("/agility/public/pb_footer.php",{},function(response,status,xhr){
            $('#pb_footer-logoFederation').attr('src','/agility/images/logos/'+logo);
        });
        // call once and then fire as timed task
        // pb_updateOrdenSalida();
        $(this).window.defaults.callback = setInterval(pb_updateOrdenSalida,10000);
    },
    onClose: function() {
        clearInterval($(this).window.defaults.callback);
    }
});

$('#pb_enumerateMangas').combogrid({
    panelWidth: 350,
    panelHeight: 150,
    idField: 'ID',
    textField: 'Nombre',
    method: 'get',
    url: '/agility/server/database/tandasFunctions.php',
    queryParams: {
        Operation: 'getTandas',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 0
    },
    required: true,
    multiple: false,
    fitColumns: true,
    singleSelect: true,
    editable: false,  // to disable tablet keyboard popup
    selectOnNavigation: true, // let use cursor keys to interactive select
    columns:[[
        { field:'ID',		hidden:true },
        { field:'Sesion',	hidden:true },
        { field:'Prueba',	hidden:true },
        { field:'Jornada',	hidden:true },
        { field:'Manga',	hidden:true },
        { field:'Categoria',hidden:true },
        { field:'Grado',	hidden:true },
        { field:'Sesion',	hidden:true },
        { field:'Tipo',	    hidden:true },
        { field:'Horario',	hidden:true },
        { field:'Nombre',	width:150, sortable:false, align:'left',title:'Lista de Tandas de la jornada'},
        { field:'Comentario', hidden:true}
    ]],
    rowStyler: myRowStyler,
    onSelect: function(index,row) {
        $('#pb_ordensalida-datagrid').datagrid('reload',{
            Operation: 'getDataByTanda',
            Prueba: workingData.prueba,
            Jornada: workingData.jornada,
            Sesion: 1, // defaults to "-- sin asignar --"
            ID:  row.ID // Tanda ID
        });
    }
});

$('#pb_ordensalida-datagrid').datagrid({
    method: 'get',
    url: '/agility/server/database/tandasFunctions.php',
    queryParams: {
        Operation: 'getDataByTanda',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 1, // defaults to "-- sin asignar --"
        ID: 0 // Tanda 0 defaults to every tandas
    },
    loadMsg: "Actualizando orden de salida ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: true,
    nowrap: false,
    width: '100%',
    height: 'auto',
    columns:[[
        { field:'Prueba',		width:0, hidden:true }, // extra field to be used on form load/save
        { field:'Jornada',		width:0, hidden:true }, // extra field to be used on form load/save
        { field:'Manga',		width:0, hidden:true },
        { field:'Tanda',		width:0, hidden:true }, // string with tanda's name
        { field:'ID',			width:0, hidden:true }, // tanda ID
        { field:'Perro',     	width:8, align:'center',	title: '#',formatter: formatOrdenSalida },
        { field:'Pendiente',	width:0, hidden:true },
        { field:'Tanda',		width:0, hidden:true },
        { field:'Equipo',		width:0, hidden:true },
        { field:'Logo',     	width:5, align:'center',	title: '',formatter: formatLogo },
        { field:'NombreEquipo',	width:20, align:'center',	title: 'Equipo' },
        { field:'Dorsal',		width:10, align:'center',	title: 'Dorsal', styler:checkPending },
        { field:'Nombre',		width:20, align:'left',		title: 'Nombre'},
        { field:'Licencia',		width:10, align:'center',	title: 'Licencia'},
        { field:'NombreGuia',	width:30, align:'right',	title: 'Guia' },
        { field:'NombreClub',	width:25, align:'right',	title: 'Club' },
        { field:'Categoria',	width:10, align:'center',	title: 'Categ.' },
        { field:'Grado',		width:10, align:'center',	title: 'Grado' },
        { field:'Celo',			width:8, align:'center',	title: 'Celo', formatter:formatCelo }
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:myRowStyler,
    onLoadSuccess:function(){
        mySelf=$('#pb_ordensalida-datagrid');
        // show/hide team name
        if (isTeamByJornada(workingData.datosJornada) ) mySelf.datagrid('showColumn','NombreEquipo');
        else  mySelf.datagrid('hideColumn','NombreEquipo');
    }
});

</script>