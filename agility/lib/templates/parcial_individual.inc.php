<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<table id="parciales_individual-datagrid">
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
        <th width="6%" data-options="field:'Licencia',		align:'center'" > <?php _e('Lic'); ?>.</th>
        <th width="5%" data-options="field:'Categoria',	align:'center',formatter:formatCatGrad" > <?php _e('Cat'); ?>.</th>
        <!--
        <th data-options="field:'Grado',		width:3, align:'center', formatter:formatGrado" > <?php _e('Grd'); ?>.</th>
        -->
        <th width="18%" data-options="field:'NombreGuia',	align:'right'" > <?php _e('Handler'); ?></th>
        <th width="15%" data-options="field:'NombreClub',	align:'right'" > <?php _e('Club'); ?></th>
        <th width="4%" data-options="field:'Faltas',		align:'center',formatter:formatFaltasTocados,styler:formatBorder"> <?php _e('F/T'); ?></th>
        <!--
        <th data-options="field:'Tocados',	hidden:true ">/th>
        -->
        <th width="4%" data-options="field:'Rehuses',		align:'center'"> <?php _e('R'); ?>.</th>
        <!--
        <th data-options="field:'PRecorrido',	hidden:true ">/th>
        -->
        <th width="5%" data-options="field:'Tiempo',		align:'right',formatter:formatTP"><?php _e('Time'); ?></th>
        <!--
        <th data-options="field:'PTiempo',	hidden:true ">/th>
        -->
        <th width="4%" data-options="field:'Velocidad',	align:'right',formatter:formatV1"> <?php _e('Vel'); ?>.</th>
        <th width="6%" data-options="field:'Penalizacion',	align:'right',formatter:formatPenalizacionFinal" > <?php _e('Penaliz'); ?>.</th>
        <th width="8%" data-options="field:'Calificacion',	align:'center',styler:formatBorder" > <?php _e('Calif'); ?>.</th>
        <th width="5%" data-options="field:'Puesto',		align:'center',formatter:formatPuestoFinalBig" ><?php _e('Position'); ?></th>
        <!--
        <th data-options="field:'CShort',	hidden:true ">/th>
        -->
    </tr>
    </thead>
</table>

<script type="text/javascript">
    $('#parciales_individual-datagrid').datagrid({
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
        fitColumns: true,
        singleSelect: true,
        autoRowHeight: false,
        idField: 'Perro',
        rowStyler:myRowStyler ,
        onLoadSuccess: function() {
            // set club/country
            $(this).datagrid('options').columns[0][6].title=clubOrCountry();
            // on international contests hide license, and enlarge name to allow pedigree name
            if (isInternational(workingData.federation)) {
                $(this).datagrid('hideColumn','Licencia');
                $(this).datagrid('autoSizeColumn','Nombre');
            }
            $(this).datagrid('fitColumns'); // expand to max width
        }
    });
</script>