<!-- 
dlg_ordensalida.inc

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

<!--  dialogo de orden de salida e introduccion de datos de una competicion -->
<div id="ordenequipos-dialog" style="width:800px;height:450px;padding:5px">
    <!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
    <table id="ordenequipos-datagrid" style="padding:5px"></table>
</div>

<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
<div id="ordenequipos-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
	   	<a id="ordenequipos-randomBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-dice'" onclick="evalOrdenSalida('random')">Aleatorio</a>
	   	<a id="ordenequipos-reverseBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-updown'" onclick="evalOrdenSalida('reverse')">Invertir</a>
	   	<a id="ordenequipos-cloneBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-downdown'" onclick="evalOrdenSalida('sameorder')">Clonar</a>
	   	<a id="ordenequipos-checkBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-help'" onclick="reloadAndCheck()">Revisar</a>
   		</span>
   		<span style="float:right;padding:5px">
   		<!-- estos elementos deben estar alineados a la derecha -->
   		<a id="ordenequipos-reloadBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-reload'" onclick="reloadOrdenEquipos();">Actualizar</a>
   		<a id="ordenequipos-printBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-print'" onclick="print_commonDesarrollo(1);">Imprimir</a>
	</span>
</div>

<script type="text/javascript">

    $('#ordenequipos-dialog').dialog({
        collapsible:	false,
        minimizable:	false,
        maximizable:	false,
        resizable:		true,
        iconCls:		'icon-order',
        closed:			true,
        modal:			true
    });

    // datos de la tabla de equipos
    $('#ordenequipos-datagrid').datagrid({
        // propiedades del panel asociado
        expandedRow: -1, // added by jamc
        fit: true,
        toolbar: '#ordenequipos-toolbar',
        url: '/agility/server/database/ordenSalidaFunctions.php',
        queryParams: { Operation:'getTeams', Prueba:workingData.prueba, Jornada:workingData.jornada, Manga:workingData.manga, where:''	},
        loadMsg: '<?php _e('Actualizando lista de equipos');?> ...',
        method: 'get',
        mode: 'remote',
        multiSort: false,
        remoteSort: false,
        idField: 'ID',
        columns: [[
            { field:'ID',			hidden:true },
            { field:'Prueba',		hidden:true },
            { field:'Jornada',		hidden:true },
            // { field:'Orden',		hidden:true },
            { field:'Nombre',		width:20, title: '<?php _e('Nombre');?>' },
            { field:'Categorias',	width:10, title: '<?php _e('Cat.');?>' },
            { field:'Observaciones',width:65, title: '<?php _e('Observaciones');?>'},
            { field:'Miembros',		hidden:true },
            { field:'DefaultTeam',	width:5, align: 'center', title: 'Def', formatter:formatOk }
        ]],
        pagination: false,
        rownumbers: true,
        fitColumns: true,
        singleSelect: true,
        view: scrollview,
        pageSize: 25,
        rowStyler: myRowStyler, // function that personalize colors on alternate rows
        // especificamos un formateador especial para desplegar la tabla de inscritos por equipo
        detailFormatter:function(idx,row){
            var dg="ordenequipos_team-datagrid-"+ replaceAll(' ','_',row.ID);
            return '<div style="padding:2px"><table id="' + dg + '"></table></div>';
        },
        onExpandRow: function(idx,row) {
            var dg=$('#ordenequipos-datagrid');
            // collapse previous expanded row
            var oldRow=dg.datagrid('options').expandedRow;
            if ( (oldRow!=-1) && (oldRow!=idx) ) { dg.datagrid('collapseRow',oldRow); }
            dg.datagrid('options').expandedRow=idx;
            showOrdenByTeam(idx,row);
        },
        onCollapseRow: function(idx,row) {
            var dg="#ordenequipos_team-datagrid-"+ replaceAll(' ','_',row.ID);
            $(dg).remove();
        },
        onBeforeLoad: function (params) {return (workingData.manga>0) }, // do not load if no manga selected
        onLoadSuccess: function(data) { $(this).datagrid('enableDnd'); },
        onDragEnter: function(dst,src) {
            return (dst.DefaultTeam!=1 && src.DefaultTeam!=1) // allow dnd if not from/to default team
        },
        onDrop: function(dst,src,updown) {
            dragAndDropOrdenEquipos(src.ID,dst.ID,(updown==='top')?0:1,reloadOrdenEquipos);
        }

    });

    //mostrar las inscripciones agrupadas por equipos
    function showOrdenByTeam(index,team){
        // - sub tabla de participantes asignados a un equipo
        var mySelf='#ordenequipos_team-datagrid-'+replaceAll(' ','_',team.ID);
        $(mySelf).datagrid({
            width: '100%',
            height: 'auto',
            title: '<?php _e('Participantes del equipo');?>: '+team.Nombre,
            pagination: false,
            rownumbers: false,
            fitColumns: true,
            singleSelect: true,
            loadMsg: '<?php _e('Leyendo orden de salida del equipo....');?>'+team.Nombre,
            url: '/agility/server/database/ordenSalidaFunctions.php',
            queryParams: {
                Operation: 'getDataByTeam',
                Prueba:workingData.prueba,
                Jornada:workingData.jornada,
                Manga: workingData.manga,
                Equipo: team.ID
            },
            method: 'get',
            mode: 'remote',
            multiSort: 'true', // can sort only "-- Sin asignar --" team
            remoteSort: 'true',
            columns: [[
                { field:'ID',		hidden:true }, // inscripcion ID
                { field:'Prueba',	hidden:true }, // prueba ID
                { field:'Jornadas',	hidden:true }, // bitmask de jornadas inscritas
                { field:'Perro',	hidden:true }, // dog ID
                { field:'Equipo',	hidden:true }, // only used on Team contests
                { field:'Pagado', 	hidden:true }, // to store if handler paid :-)
                { field:'Guia', 	hidden:true }, // Guia ID
                { field:'Club',		hidden:true }, // Club ID
                { field:'LOE_RRC',	hidden:true }, // LOE/RRC
                { field:'Club',		hidden:true }, // Club ID
                { field:'Dorsal',	width:6,    align: 'right',	title: '<?php _e('Dorsal'); ?>' },
                { field:'Nombre',	width:15,   align: 'right',	title: '<?php _e('Nombre'); ?>' },
                { field:'Licencia',	width:6,    align: 'center',title: '<?php _e('Lic');    ?>' },
                { field:'Categoria',width:4,    align: 'center',title: '<?php _e('Cat');    ?>' },
                { field:'Grado',	width:6,    align: 'center',title: '<?php _e('Grado');  ?>' },
                { field:'NombreGuia',	width:25,align: 'right',	title: '<?php _e('Gu&iacute;a'); ?>' },
                { field:'NombreClub',	width:15,align: 'right',	title: '<?php _e('Club');   ?>' },
                { field:'NombreEquipo',	hidden:true },
                { field:'Observaciones',width:15,                                   title: '<?php _e('Observaciones');?>' },
                { field:'Celo',		width:4, align:'center', formatter: formatCelo,	title: '<?php _e('Celo');   ?>' }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
            onResize:function(){
                $('#ordenequipos-datagrid').datagrid('fixDetailRowHeight',index);
            },
            onLoadSuccess:function(){
                setTimeout(function(){
                    $('#ordenequipos-datagrid').datagrid('fixDetailRowHeight',index);
                },0);
            }
        }); // end of inscritos-by-team_team_id

        addSimpleKeyHandler(mySelf,null); // to allow debugging rows
        $('#ordenequipos-datagrid').datagrid('fixDetailRowHeight',index);
    } // end of showPerrosByGuia

    //activa teclas up/down para navegar por el panel , esc para cerrar y ctrl+shift+enter para ver fila
    addSimpleKeyHandler('#ordenequipos-datagrid',null);

    // botones
    addTooltip($('#ordenequipos-checkBtn').linkbutton(),"Verificar 'alertas de proximidad' <br/>entre perros del mismo gu&iacute;a");
    addTooltip($('#ordenequipos-randomBtn').linkbutton(),"Generar un nuevo orden de equipos aleatorio");
    addTooltip($('#ordenequipos-reverseBtn').linkbutton(),"Orden de equipos inverso a resultados de primera manga");
    addTooltip($('#ordenequipos-cloneBtn').linkbutton(),"Clonar el orden de los equipos de la primera manga");
    addTooltip($('#ordenequipos-printBtn').linkbutton(),"Imprimir el orden de equipos");
    addTooltip($('#ordenequipos-reloadBtn').linkbutton(),"Recargar orden de equipos. Actualizar ventana");

</script>