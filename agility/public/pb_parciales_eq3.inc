<!-- 
pb_parciales.inc

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

<div id="pb_parciales-window">
    <div id="pb_parciales-layout" style="width:100%">
        <div id="pb_parciales-Cabecera" data-options="region:'north',split:false" style="height:100px" class="pb_floatingheader">
            <a id="pb_header-link" class="easyui-linkbutton" onClick="pb_updateResults();" href="#" style="float:left">
                <img id="pb_header-logo" src="/agility/images/logos/rsce.png" width="75" />
            </a>
            <span style="float:left;padding:10px" id="pb_header-infocabecera">Cabecera</span>
            <span style="float:right;padding:10px" id="pb_header-texto">
                Resultados provisionales<br/>
                <label for="pb_enumerateParciales" style="font-size:0.7em;">Manga:</label>
                <select id="pb_enumerateParciales" style="width:200px"></select>
            </span>
        </div>
        <div id="pb_parciales-data" data-options="region:'center'" >
            <div id="pb_parciales-Resultados" class="easyui-panel"
                 data-options="noheader:true,border:false,closable:false,collapsible:false,collapsed:false">
                <span id="pb_resultadosParciales"></span>
            </div>
        </div>
        <div id="pb_parciales-footer" data-options="region:'south',split:false" style="height:100px" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
    </div>
</div> <!-- pb_parciales-window -->

<script type="text/javascript">

addTooltip($('#pb_header-link').linkbutton(),"Actualizar datos de resultados parciales");
$('#pb_parciales-layout').layout({fit:true});

$('#pb_enumerateParciales').combogrid({
	panelWidth: 300,
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
			{field:'Nombre',title:'Resultados disponibles',width:50,align:'right'},
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
    onOpen: function() {
        pb_updateResults(); // notice no results will still be reported. just to update header info
    },
	onChange:function(value){
		var row=$('#pb_enumerateParciales').combogrid('grid').datagrid('getSelected');
		if (!row) return;
		workingData.manga=row.Manga;
		workingData.tanda=0; // fake tanda. use manga+mode to evaluate results
		workingData.mode=row.Mode;
		pb_updateResults();
	}
});

$('#pb_parciales-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true,
	// 1 minute poll is enouth for this, as no expected changes during a session
	onOpen: function() {
        // update header
        pb_getHeaderInfo();
        // update footer
        pb_setFooterInfo();
	}
});

</script>