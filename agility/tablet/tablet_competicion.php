<!-- 
tablet_competicion.php

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

<?php include_once(__DIR__."/tablet_entradadatos.inc");?>
		
<div id="tablet-window" style="margin:0px;padding:0px">
	<!-- toolbar para orden de tandas -->
	<div id="tablet-toolbar" style="padding:5px">
		<a id="tablet-reloadBtn" href="#" class="easyui-linkbutton" 
			data-options="iconCls:'icon-reload'" onclick="$('#tablet-datagrid').datagrid('reload');">Actualizar</a>
	</div>
	<!-- Tabla desplegable para la entrada de datos desde el tablet -->
	<table id="tablet-datagrid" style="margin:0px;padding:0px;"></table>
</div> <!-- tandas / orden de salida -->
		
<script type="text/javascript">

$('#tablet-reloadBtn').linkbutton();

$('#tablet-window').window({
	title: 'Orden de salida',
	fit: true,
	collapsible:	false,
	minimizable:	false,
	maximizable:	false,
	resizable:		false,
	closable:		false,
	iconCls:		'icon-order',
	maximized:		true,
	closed:			false,
	modal:			false
});

$('#tablet-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: '/agility/server/database/tandasFunctions.php',
    queryParams: {
        Operation: 'getTandas',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: workingData.sesion
    },
	toolbar:'#tablet-toolbar',
    loadMsg: "Actualizando programa ...",
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: false,
    view: detailview,
    pageSize: 100, // enought bit to make it senseless
    columns:[[ 
          	{ field:'ID',		hidden:true },
        	{ field:'Sesion',	hidden:true },
        	{ field:'Prueba',	hidden:true },
          	{ field:'Jornada',	hidden:true },
          	{ field:'Manga',	hidden:true },
      		{ field:'From',		hidden:true },
      		{ field:'To',		hidden:true },
      		{ field:'Nombre',	width:200, sortable:false, align:'ledt',title:'Secuencia de salida a pista',styler:tandasStyler},
      		{ field:'Categoria',hidden:true },
      		{ field:'Grado',	hidden:true }
    ]],
    rowStyler: myRowStyler,            
    // especificamos un formateador especial para desplegar la tabla de perros por tanda
    detailFormatter:function(idx,row){
        return '<div style="padding:2px"><table id="tablet-datagrid-' + parseInt(row.ID) + '"></table></div>';
    },
    onClickRow: function(idx,row) { tablet_updateSession(row);},
    onExpandRow: function(idx,row) { if (row.Tipo!=0) tablet_showPerrosByTanda(idx,row); }
});

// mostrar los perros de una tanda
function tablet_showPerrosByTanda(index,row){ 
	// - sub tabla orden de salida de una tanda
    var mySelf='#tablet-datagrid-'+row.ID;
	$(mySelf).datagrid({
		method: 'get',
		url: '/agility/server/database/tandasFunctions.php',
	    queryParams: {
	        Operation: 'getDataByTanda',
	        Prueba: row.Prueba,
	        Jornada: row.Jornada,
	        Sesion: row.Sesion,
	        ID:row.ID
	    },
	    loadMsg: "Actualizando orden de salida ...",
	    pagination: false,
	    rownumbers: true,
	    fitColumns: true,
	    singleSelect: true,
	    autoRowHeight: false,
	    width: '100%',
	    height: 'auto',
		columns:[[
		        { field:'Parent',		width:0, hidden:true }, // self reference to row index
	            { field:'Prueba',		width:0, hidden:true }, // extra field to be used on form load/save
	            { field:'Jornada',		width:0, hidden:true }, // extra field to be used on form load/save
	            { field:'Manga',		width:0, hidden:true },
	            { field:'Tanda',		width:0, hidden:true }, // string with tanda's name
	            { field:'ID',			width:0, hidden:true }, // tanda ID
	            { field:'Perro',		width:0, hidden:true },
	            { field:'Licencia',		width:0, hidden:true },
	            { field:'Pendiente',	width:0, hidden:true },
	            { field:'Tanda',		width:0, hidden:true },
	            { field:'Celo',			width:10, align:'center',	title: 'Celo', formatter:formatCelo},
	            { field:'Nombre',		width:20, align:'left',		title: 'Nombre'},
	            { field:'NombreGuia',	width:45, align:'right',	title: 'Guia' },
	            { field:'NombreClub',	width:30, align:'right',	title: 'Club' },
	            { field:'Dorsal',		width:12, align:'center',	title: 'Dorsal', styler:checkPending },
	            { field:'Categoria',	width:10, align:'center',	title: 'Categ.' },
	            { field:'Grado',		width:10, align:'center',	title: 'Grado' },
	            { field:'Faltas',		width:5, align:'center',	title: 'F'},
	            { field:'Rehuses',		width:5, align:'center',	title: 'R'},
	            { field:'Tocados',		width:5, align:'center',	title: 'T'},
	            { field:'Tiempo',		width:15, align:'right',	title: 'Tiempo'	}, 
	            { field:'Eliminado',	width:5, align:'center',	formatter:formatEliminado,	title: 'EL.'},
	            { field:'NoPresentado',	width:5, align:'center',	formatter:formatNoPresentado,	title: 'NP'},		
	            { field:'Observaciones',width:0, hidden:true }
	          ]],
          	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
        rowStyler:myRowStyler,
        onClickRow: function(idx,data) {
	    	data.Session=workingData.sesion;
            data.Parent=mySelf; // store datagrid reference
            $('#tdialog-form').form('load',data);
            $('#tablet-window').window('close');
            $('#tdialog-window').window('open');
        },
        onResize:function(){
            $('#tablet-datagrid').datagrid('fixDetailRowHeight',index);
        },
        onLoadSuccess:function(){
            setTimeout(function(){ $('#tablet-datagrid').datagrid('fixDetailRowHeight',index); },0);
            if (! isMobileDevice() ) {
        		$(mySelf).datagrid('enableDnd');
    			$(mySelf).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
            }
    	},
        onDragEnter: function(dst,src) {
            if (dst.Manga!=src.Manga) return false;
            if (dst.Categoria!=src.Categoria) return false;
            if (dst.Grado!=src.Grado) return false;
            if (dst.Celo!=src.Celo) return false;
            return true;
        }, 
        onDrop: function(dst,src,updown) {
            // reload el orden de salida en la manga asociada
            workingData.prueba=src.Prueba;
            workingData.jornada=src.Jornada;
            workingData.manga=src.Manga;
            dragAndDropOrdenSalida(
                    src.Perro,
                    dst.Perro,
                    (updown==='top')?0:1,
                    function()  { $(mySelf).datagrid('reload'); }
             	);
        }
	});
	$('#tablet-datagrid').datagrid('fixDetailRowHeight',index);
}
</script>
