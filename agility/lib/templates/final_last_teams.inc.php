<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<table id="finales_ultimos_equipos-datagrid">
    <thead>
    <tr> <!--
         <th data-options="field:'ID',			hidden:true"></th>
         <th data-options="field:'Prueba',		hidden:true"></th>
         <th data-options="field:'Jornada',		hidden:true"></th>
         -->
        <th data-options="field:'Orden',		width:20, align:'center',formatter:formatOrdenLlamadaPista" >#</th>
        <th data-options="field:'Logo',		    width:'19%', sortable:false, formatter:formatTeamLogos">&nbsp</th>
        <th data-options="field:'Nombre',		width:'20.5%', sortable:false, formatter:formatBold"><?php _e('Team'); ?></th>
        <th data-options="field:'Categorias',	width:'4%', sortable:false, formatter:formatCategoria"><?php _e('Cat'); ?></th>
        <th data-options="field:'T1',		    align:'center', width:'9.5%', sortable:false"><?php _e('Time'); ?> 1</th>
        <th data-options="field:'P1',		    align:'center',width:'10%', sortable:false"><?php _e('Penal'); ?> 1</th>
        <th data-options="field:'T2',		    align:'center',width:'9.5%', sortable:false"><?php _e('Time'); ?> 2</th>
        <th data-options="field:'P2',		    align:'center',width:'10%', sortable:false"><?php _e('Penal'); ?> 2</th>
        <th data-options="field:'Tiempo',		align:'center',width:'8.5%', sortable:false,formatter:formatBold"><?php _e('Time'); ?></th>
        <th data-options="field:'Penalizacion',	align:'center',width:'9%', sortable:false,formatter:formatBold"><?php _e('Penalization'); ?></th>
    </tr>
    </thead>
</table>

<script type="text/javascript">
    $('#finales_ultimos_equipos-datagrid').datagrid({
        expandCount: 0,
        // propiedades del panel asociado
        fit: false, // do not set to true to take care on extra elements in panel
        border: false,
        closable: false,
        collapsible: false,
        collapsed: false,
        // no tenemos metodo get ni parametros: directamente cargamos desde el datagrid
        loadMsg:  "<?php _e('Updating final scores');?>...",
        // propiedades del datagrid
        width:'99%',
        pagination: false,
        rownumbers: true,
        fitColumns: true,
        singleSelect: true,
        rowStyler:myRowStyler,
        autoRowHeight: false,
        idField: 'ID',
        pageSize: 500, // enought bit to make it senseless
        // columns declared at html section to show additional headers
        // especificamos un formateador especial para desplegar la tabla de perros por equipos
        view:detailview,
        detailFormatter:function(idx,row){
            var dgname="finales_ultimos_equipos-datagrid-"+parseInt(row.ID);
            return '<div style="padding:2px"><table id="'+dgname+'"></table></div>';
        },
        onExpandRow: function(idx,row) {
            $(this).datagrid('options').expandCount++;
            showClasificacionesByTeam("#finales_ultimos_equipos-datagrid",idx,row);
        },
        onCollapseRow: function(idx,row) {
            $(this).datagrid('options').expandCount--;
            var dg="#finales_ultimos_equipos-datagrid-" + parseInt(row.ID);
            $(dg).remove();
        }
    });

</script>