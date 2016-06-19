<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<table id="parciales_equipos-datagrid" width="100%">
    <thead>
    <tr> <!--
         <th data-options="field:'ID',			hidden:true"></th>
         <th data-options="field:'Prueba',		hidden:true"></th>
         <th data-options="field:'Jornada',		hidden:true"></th>
         -->
        <th width="20%" data-options="field:'Logo',		    sortable:false, formatter:formatTeamLogos">&nbsp</th>
        <th width="43%" data-options="field:'Nombre',		sortable:false, formatter:formatBold"><?php _e('Team'); ?></th>
        <th width="5%"  data-options="field:'Categorias',	sortable:false, formatter:formatCategoria"><?php _e('Cat'); ?></th>
        <th width="15%" data-options="field:'Tiempo',		sortable:false,align:'center',formatter:formatBold"><?php _e('Time'); ?></th>
        <th width="15%" data-options="field:'Penalizacion', sortable:false,align:'center',formatter:formatBold"><?php _e('Penalization'); ?></th>
    </tr>
    </thead>
</table>

<script type="text/javascript">
    $('#parciales_equipos-datagrid').datagrid({
        expandCount: 0,
        // propiedades del panel asociado
        fit: false, // do not set to true to take care on extra elements in panel
        border: false,
        closable: false,
        collapsible: false,
        collapsed: false,
        // propiedades del datagrid
        // no tenemos metodo get ni parametros: directamente cargamos desde el datagrid
        loadMsg:  "<?php _e('Updating partial scores');?>...",
        width:'100%', // let space to expand/collapse and row number bars to avoid horizontal scroll
        height:2048, // enought big to assure overflow
        pagination: false,
        rownumbers: true,
        fitColumns: true,
        singleSelect: true,
        rowStyler:myRowStyler,
        autoRowHeight: false,
        idField: 'ID',
        pageSize: 1000, // enought bit to make it senseless
        // columns declared at html section to show additional headers
        // especificamos un formateador especial para desplegar la tabla de perros por equipos
        view:detailview,
        detailFormatter:function(idx,row){
            var dgname="parciales_equipos-datagrid-"+parseInt(row.ID);
            return '<div style="padding:2px"><table id="'+dgname+'"></table></div>';
        },
        onExpandRow: function(idx,row) {
            $(this).datagrid('options').expandCount++;
            showPartialScoresByTeam("#parciales_equipos-datagrid",idx,row);
        },
        onCollapseRow: function(idx,row) {
            $(this).datagrid('options').expandCount--;
            var dg="#parciales_equipos-datagrid-" + parseInt(row.ID);
            $(dg).remove();
        }
    });

</script>