<?php
require_once(__DIR__."/../../server/tools.php");
require_once(__DIR__."/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<table id="finales_individual-datagrid">
    <thead>
    <tr>
        <th colspan="8"> <span class="main_theader"><?php _e('Competitor data'); ?></span></th>
        <th colspan="7"> <span class="main_theader" id="finales_roundname_m1"><?php _e('Round'); ?> 1</span></th>
        <th colspan="7"> <span class="main_theader" id="finales_roundname_m2"><?php _e('Round'); ?> 2</span></th>
        <th colspan="4"> <span class="main_theader"><?php _e('Final scores'); ?></span></th>
    </tr>
    <tr>
        <!--
        <th data-options="field:'Perro',		hidden:true " ></th>
         -->
        <th data-options="field:'Dorsal',		width:20, align:'left'" > <?php _e('Dors'); ?>.</th>
        <th data-options="field:'LogoClub',		width:20, align:'left',formatter:formatLogoPublic" > &nbsp;</th>
        <th data-options="field:'Nombre',		width:35, align:'center',formatter:formatBold"> <?php _e('Name'); ?></th>
        <th data-options="field:'Licencia',		width:15, align:'center'" > <?php _e('Lic'); ?>.</th>
        <th data-options="field:'Categoria',	width:15, align:'center',formatter:formatCategoria" > <?php _e('Cat'); ?>.</th>
        <th data-options="field:'Grado',		width:15, align:'center', formatter:formatGrado" > <?php _e('Grd'); ?>.</th>
        <th data-options="field:'NombreGuia',	width:50, align:'right'" > <?php _e('Handler'); ?></th>
        <th data-options="field:'NombreClub',	width:45, align:'right'" > <?php _e('Club'); ?></th>
        <th data-options="field:'F1',			width:15, align:'center',styler:formatBorder"> <?php _e('F/T'); ?></th>
        <th data-options="field:'R1',			width:15, align:'center'"> <?php _e('R'); ?>.</th>
        <th data-options="field:'T1',			width:25, align:'right',formatter:formatT1"> <?php _e('Time'); ?>.</th>
        <th data-options="field:'V1',			width:16, align:'right',formatter:formatV1"> <?php _e('Vel'); ?>.</th>
        <th data-options="field:'P1',			width:21, align:'right',formatter:formatP1"> <?php _e('Penal'); ?>.</th>
        <th data-options="field:'C1',			width:23, align:'center'"> <?php _e('Cal'); ?>.</th>
        <th data-options="field:'Puesto1',		width:15, align:'center'"> <?php _e('Pos'); ?>.</th>
        <th data-options="field:'F2',			width:15, align:'center',styler:formatBorder"> <?php _e('F/T'); ?></th>
        <th data-options="field:'R2',			width:15, align:'center'"> <?php _e('R'); ?>.</th>
        <th data-options="field:'T2',			width:25, align:'right',formatter:formatT2"> <?php _e('Time'); ?>.</th>
        <th data-options="field:'V2',			width:16, align:'right',formatter:formatV2"> <?php _e('Vel'); ?>.</th>
        <th data-options="field:'P2',			width:21, align:'right',formatter:formatP2"> <?php _e('Penal'); ?>.</th>
        <th data-options="field:'C2',			width:23, align:'center'"> <?php _e('Cal'); ?>.</th>
        <th data-options="field:'Puesto2',		width:15, align:'center'"> <?php _e('Pos'); ?>.</th>
        <th data-options="field:'Tiempo',		width:25, align:'right',formatter:formatTF,styler:formatBorder"><?php _e('Time'); ?></th>
        <th data-options="field:'Penalizacion',	width:25, align:'right',formatter:formatPenalizacionFinal" > <?php _e('Penaliz'); ?>.</th>
        <th data-options="field:'Calificacion',	width:20, align:'center'" > <?php _e('Calif'); ?>.</th>
        <th data-options="field:'Puesto',		width:15, align:'center',formatter:formatPuestoFinalBig" ><?php _e('Position'); ?></th>
    </tr>
    </thead>
</table>

<script type="text/javascript">
    
    $('#finales_individual-datagrid').datagrid({
        expandCount: 0,
        // propiedades del panel asociado
        fit: true,
        border: false,
        closable: false,
        collapsible: false,
        collapsed: false,
        // propiedades del datagrid
        // no tenemos metodo get ni parametros: directamente cargamos desde el datagrid
        loadMsg: "<?php _e('Updating final scores');?>...",
        width:'100%',
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        singleSelect: true,
        rowStyler:myRowStyler,
        autoRowHeight:true
    });

</script>