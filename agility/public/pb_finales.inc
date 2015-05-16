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

<div id="pb_finales-window">
    <div id="pb_finales-layout" style="width:100%">
        <div id="pb_finales-Cabecera" data-options="region:'north',split:false" style="height:100px" class="pb_floatingheader">
            <img id="pb_finales-logo" src="/agility/images/logos/rsce.png" width="75" style="float:left"/>
            <span style="float:left;padding:10px" id="pb_finales-infocabecera">Cabecera</span>
            <span style="float:right" id="pb_finales-texto">
                Clasificaci&oacute;n final<br/>
                <label for="pb_enumerateFinales" style="font-size:0.7em">Manga:</label>
		        <select id="pb_enumerateFinales" style="width:200px"></select>
            </span>
        </div>
        <div id="pb_tabla" data-options="region:'center'"><!-- Datos de TRS y TRM -->
            <div id="vwc_tablaTRS">
                <table class="vwc_trs">
                    <tbody>
                    <!--  TODO: write TRS data -->
                    </tbody>
                </table>
            </div>
            <div id="pb_resultados-data" style="width:100%;height:800px">
                <table id="pb_resultados-datagrid">
                    <thead>
                    <tr>
                        <th colspan="7"> <span class="resultados_theader">Datos del participante</span></th>
                        <th colspan="6"> <span class="resultados_theader" id="pb_resultados_thead_m1">Manga 1</span></th>
                        <th colspan="6"> <span class="resultados_theader" id="pb_resultados_thead_m2">Manga 2</span></th>
                        <th colspan="4"> <span class="resultados_theader">Clasificaci&oacute;n</span></th>
                    </tr>
                    <tr>
                        <!--
                        <th data-options="field:'Perro',		hidden:true " ></th>
                         -->
                        <th data-options="field:'Dorsal',		width:20, align:'left'" > Dors.</th>
                        <th data-options="field:'Nombre',		width:35, align:'left'" > Nombre</th>
                        <th data-options="field:'Licencia',		width:15, align:'center'" > Lic.</th>
                        <th data-options="field:'Categoria',	width:15, align:'center'" > Cat.</th>
                        <th data-options="field:'Grado',		width:15, align:'center'" > Grd.</th>
                        <th data-options="field:'NombreGuia',	width:50, align:'right'" > Guia</th>
                        <th data-options="field:'NombreClub',	width:45, align:'right'" > Club</th>
                        <th data-options="field:'F1',			width:15, align:'center',styler:formatBorder"> F/T</th>
                        <th data-options="field:'R1',			width:15, align:'center'"> R.</th>
                        <th data-options="field:'T1',			width:25, align:'right',formatter:formatT1"> Tmp.</th>
                        <th data-options="field:'V1',			width:15, align:'right',formatter:formatV1"> Vel</th>
                        <th data-options="field:'P1',			width:20, align:'right',formatter:formatP1"> Penal.</th>
                        <th data-options="field:'C1',			width:25, align:'center'"> Cal.</th>
                        <th data-options="field:'F2',			width:15, align:'center',styler:formatBorder"> F/T</th>
                        <th data-options="field:'R2',			width:15, align:'center'"> R.</th>
                        <th data-options="field:'T2',			width:25, align:'right',formatter:formatT2"> Tmp.</th>
                        <th data-options="field:'V2',			width:15, align:'right',formatter:formatV2"> Vel.</th>
                        <th data-options="field:'P2',			width:20, align:'right',formatter:formatP2"> Penal.</th>
                        <th data-options="field:'C2',			width:25, align:'center'"> Cal.</th>
                        <th data-options="field:'Tiempo',		width:25, align:'right',formatter:formatTF,styler:formatBorder">Tiempo</th>
                        <th data-options="field:'Penalizacion',	width:25, align:'right',formatter:formatPenalizacionFinal" > Penaliz.</th>
                        <th data-options="field:'Calificacion',	width:20, align:'center'" > Calif.</th>
                        <th data-options="field:'Puesto',		width:15, align:'center',formatter:formatPuestoFinal" > Puesto </th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div id="pb_finales-footer" data-options="region:'south',split:false" style="height:100px" class="pb_floatingfooter">
            <span id="pb_finales-footerData"></span>
        </div>
    </div>
</div> <!-- finales-window -->

<script type="text/javascript">

$('#pb_enumerateFinales').combogrid({
	panelWidth: 300,
	panelHeight: 150,
	idField: 'Nombre',
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
			{field:'Nombre',title:'Clasificaciones disponibles',width:50,align:'right'},
			{field:'Prueba',hidden:true},
			{field:'Jornada',hidden:true},
			{field:'Manga1',hidden:true},
			{field:'Manga2',hidden:true},
			{field:'NombreManga1',hidden:true},
			{field:'NombreManga2',hidden:true},
			{field:'Recorrido',hidden:true},
			{field:'Mode',hidden:true}
	]],
	onBeforeLoad: function(param) { 
		param.Operation='enumerateClasificaciones';
		param.Prueba= workingData.prueba;
		param.Jornada= workingData.jornada;
		param.Manga= 1; // fake data to get Resultados constructor working
		return true;
	},
	onChange:function(value){
		pb_updateFinales();
	}
});

$('#pb_finales-layout').layout({fit:true});
$('#pb_finales-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true,
	// 1 minute poll is enouth for this, as no expected changes during a session
	onOpen: function() {
        // update footer info
        var logo=nombreCategorias[workingData.federation]['logo'];
        $('#pb_finales-footerData').load("/agility/public/pb_footer.php",{},function(response,status,xhr){
            $('#pb_footer-logoFederation').attr('src','/agility/images/logos/'+logo);
        });
		// call once and then fire as timed task
		pb_updateFinales();
		$(this).window.defaults.callback = setInterval(pb_updateFinales,60000);
	},
	onClose: function() { 
		clearInterval($(this).window.defaults.callback);
	}
});

$('#pb_resultados-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	toolbar:'#resultados-toolbar',
	// no tenemos metodo get ni parametros: directamente cargamos desde el datagrid
	loadMsg: "Actualizando resultados de la ronda...",
	pagination: false,
	rownumbers: false,
	fitColumns: true,
	singleSelect: true,
	rowStyler:myRowStyler
});
</script>