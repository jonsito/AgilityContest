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
	<div id="pb_ordensalida-layout" class="easyui-layout" data-options="fit:true" style="width:100%">
		<div id="pb_ordensalida-Cabecera" data-options="region:'north',split:false" style="height:80px" class="pb_floatingheader">
            <img id="pb_ordensalida-logo" src="/agility/images/logos/rsce.png" width="75" style="float:left"/>
		    <span style="float:left;padding:10px;" id="pb_ordensalida-infocabecera">Cabecera</span>
			<span style="float:right;" id="pb_ordensalida-texto">
                Orden de Salida<br />
                <label for="pb_enumerateMangas">&nbsp;</label>
                <select id="pb_enumerateMangas" style="width:200px"></select>
            </span>
		</div>
		<div id="pb_tabla" data-options="region:'center'">
			<span id="pb_ordensalidaData"></span>
		</div>
	</div>
</div> <!-- pb_ordensalida-window -->

<script type="text/javascript">

$('#pb_enumerateMangas').combogrid({
    panelWidth: 350,
    panelHeight: 150,
    idField: 'ID',
    textField: 'Nombre',
    url: '/agility/server/database/resultadosFunctions.php',
    method: 'get',
    required: true,
    multiple: false,
    fitColumns: true,
    singleSelect: true,
    editable: false,  // to disable tablet keyboard popup
    selectOnNavigation: true, // let use cursor keys to interactive select
    columns: [[
        {field:'ID',hidden:true},
        {field:'Nombre',title:'Mangas disponibles',width:50,align:'right'},
        {field:'Prueba',hidden:true},
        {field:'Jornada',hidden:true},
        {field:'Manga',hidden:true},
        {field:'TipoManga',hidden:true},
        {field:'Mode',hidden:true}
    ]],
    onBeforeLoad: function(param) {
        param.Operation='enumerateResultados';
        param.Prueba= workingData.prueba;
        param.Jornada= workingData.jornada;
        param.Manga= 1; // fake data to get Resultados constructor working
        return true;
    },
    onChange:function(value){
        var row=$('#pb_enumerateMangas').combogrid('grid').datagrid('getSelected');
        if (!row) return;
        workingData.manga=row.Manga;
        workingData.tanda=0; // fake tanda. use manga+mode to evaluate results
        workingData.mode=row.Mode;
        pb_updateOrdenSalida();
    }
});

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
        // call once and then fire as timed task
		pb_updateOrdenSalida();
		$(this).window.defaults.callback = setInterval(pb_updateOrdenSalida,10000);
	},
	onClose: function() { 
		clearInterval($(this).window.defaults.callback);
	}
});

</script>