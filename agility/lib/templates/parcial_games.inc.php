<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<table id="parciales_games-datagrid">
    <thead>
    <tr>
        <!--
        <th data-options="field:'Perro',		hidden:true "></th>
        <th data-options="field:'Manga',		hidden:true "></th>
        <th data-options="field:'Perro',		hidden:true "></th>
        <th data-options="field:'Raza',		    hidden:true "></th>
        <th data-options="field:'Equipo',		hidden:true "></th>
        <th data-options="field:'NombreEquipo',	hidden:true "></th>
         -->
        <th width="5%" data-options="field:'LogoClub',		align:'left',formatter:formatLogo" > &nbsp;</th>
        <th width="5%" data-options="field:'Dorsal',		align:'left'" > <?php _e('Dors'); ?>.</th>
        <th width="10%" data-options="field:'Nombre',		align:'left',formatter:formatDogName"> <?php _e('Name'); ?></th>
        <!--
        <th width="6%" data-options="field:'Licencia',		hidden:true"></th>
        -->
        <th width="5%" data-options="field:'Categoria',	align:'center',formatter:formatCatGrad" > <?php _e('Cat'); ?>.</th>
        <!--
        <th data-options="field:'Grado',		width:3, align:'center', formatter:formatGrado" > <?php _e('Grd'); ?>.</th>
        -->
        <th width="15%" data-options="field:'NombreGuia',	align:'right'" > <?php _e('Handler'); ?></th>
        <th width="10%" data-options="field:'NombreClub',	align:'right'" id="parciales_individual-Club"> <?php _e('Club'); ?></th>
        <th width="10%" data-options="field:'Faltas',		align:'center',styler:formatBorder"> <?php _e('Opening'); ?></th>
        <th width="10%" data-options="field:'Tocados',		align:'center'"> <?php _e('Closing'); ?></th>
        <!--
        <th data-options="field:'Tocados',	hidden:true ">/th>
        <th data-options="field:'Rehuses',	hidden:true ">/th>
        <th data-options="field:'PRecorrido',	hidden:true ">/th>
        -->
        <th width="8%" data-options="field:'Tiempo',		align:'right',formatter:formatTP"><?php _e('Time'); ?></th>
        <!--
        <th data-options="field:'PTiempo',	hidden:true ">/th>
        <th data-options="field:'Velocidad',	hidden:true ">/th>
        -->
        <th width="10%" data-options="field:'Penalizacion',	align:'right',formatter:formatPenalizacionFinal,styler:formatBorder" > <?php _e('Points'); ?>.</th>
        <th width="6%" data-options="field:'Calificacion',	align:'center'" > <?php _e('Calif'); ?>.</th>
        <th width="6%" data-options="field:'Puesto',		align:'center',formatter:formatPuestoFinalBig" ><?php _e('Position'); ?></th>
        <!--
        <th data-options="field:'CShort',	hidden:true ">/th>
        -->
    </tr>
    </thead>
</table>

<script type="text/javascript">
    $('#parciales_games-datagrid').datagrid({
        // declared by me. not used in individual scores
        expandCount: 0,
        // propiedades del panel asociado
        fit: false, // parent is a fake div, so donn't ask to fit parent width: let fitcolumns do the job
        border: false,
        closable: false,
        collapsible: false,
        collapsed: false,
        // propiedades del datagrid
        // no tenemos metodo get ni parametros: directamente cargamos desde el datagrid
        loadMsg:  "<?php _e('Updating partial scores');?> ...",
        width:'100%',
        pagination: false,
        rownumbers: false,
        fitColumns: false,
        singleSelect: true,
        autoRowHeight: false,
        idField: 'Perro',
        rowStyler:myRowStyler,
        onBeforeLoad: function(param) {
            // do not load if no manga selected
            if (parseInt(workingData.manga) <= 0) return false;
            var name=( parseInt(workingData.datosManga.Tipo)===29)?"<?php _e('Closing'); ?>":'Gambler';
            // adjust field to "Closing" or "Gambler" acording mode
            $('#parciales_games-datagrid').datagrid('setFieldTitle',{field:'Tocados',title:name});
            return true;
        }
    });
</script>