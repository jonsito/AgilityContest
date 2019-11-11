<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<table id="finales_last_equipos-datagrid">
    <thead>
    <!--
    <tr>
        <th colspan="8"> <span class="main_theader"><?php _e('Competitor data'); ?></span></th>
        <th colspan="7"> <span class="main_theader" id="finales_last_individual_roundname_m1"><?php _e('Round'); ?> 1</span></th>
        <th colspan="7"> <span class="main_theader" id="finales_last_individual_roundname_m2"><?php _e('Round'); ?> 2</span></th>
        <th colspan="4"> <span class="main_theader"><?php _e('Final scores'); ?></span></th>
    </tr>
    -->
    <tr>
        <!--
        <th data-options="field:'Perro',		hidden:true " ></th>
         -->
        <th width="3%" data-options="field:'Orden',		   align:'left',formatter:formatOrdenLlamadaPista" >#</th>
        <!--
        <th data-options="field:'Equipo',		hidden:true "></th>
        <th data-options="field:'NombreClub',	hidden:true "></th>
         -->
        <th width="3%" data-options="field:'LogoClub',		align:'left',formatter:formatLogo" > &nbsp;</th>
        <th width="3%" data-options="field:'Dorsal',		align:'left'" > <?php _e('Dors'); ?>.</th>
        <th width="7%" data-options="field:'Nombre',		align:'center',formatter:formatBold"> <?php _e('Name'); ?></th>
        <!--
        <th width="4%" data-options="field:'Licencia',		align:'center'" > <?php _e('Lic'); ?>.</th>
        -->
        <th width="5%" data-options="field:'Categoria',	    align:'center',formatter:formatCatGrad" > <?php _e('Cat'); ?>.</th>
        <!--
        <th data-options="field:'Grado',		width:3, align:'center', formatter:formatGrado" > <?php _e('Grd'); ?>.</th>
        -->
        <th width="9%" data-options="field:'NombreGuia',	align:'right'" > <?php _e('Handler'); ?></th>
        <th width="7%" data-options="field:'NombreEquipo',	align:'right'" > <?php _e('Team'); ?></th>
        <th width="2%" data-options="field:'F1',			align:'center',styler:formatBorder"> <?php _e('F/T'); ?></th>
        <th width="2%" data-options="field:'R1',			align:'center'"> <?php _e('R'); ?>.</th>
        <th width="4%" data-options="field:'T1',			align:'right',formatter:formatT1"> <?php _e('Time'); ?>.</th>
        <th width="3%" data-options="field:'V1',			align:'right',formatter:formatV1"> <?php _e('Vel'); ?>.</th>
        <th width="4%" data-options="field:'P1',			align:'right',formatter:formatP1"> <?php _e('Penal'); ?>.</th>
        <th width="5%" data-options="field:'C1',			align:'center'"> <?php _e('Cal'); ?>.</th>
        <th width="3%" data-options="field:'Puesto1',		align:'center'"> <?php _e('Pos'); ?>.</th>
        <th width="2%" data-options="field:'F2',			align:'center',styler:formatBorder"> <?php _e('F/T'); ?></th>
        <th width="2%" data-options="field:'R2',			align:'center'"> <?php _e('R'); ?>.</th>
        <th width="4%" data-options="field:'T2',			align:'right',formatter:formatT2"> <?php _e('Time'); ?>.</th>
        <th width="3%" data-options="field:'V2',			align:'right',formatter:formatV2"> <?php _e('Vel'); ?>.</th>
        <th width="4%" data-options="field:'P2',			align:'right',formatter:formatP2"> <?php _e('Penal'); ?>.</th>
        <th width="5%" data-options="field:'C2',			align:'center'"> <?php _e('Cal'); ?>.</th>
        <th width="3%" data-options="field:'Puesto2',		align:'center'"> <?php _e('Pos'); ?>.</th>
        <th width="4%" data-options="field:'Tiempo',		align:'right',formatter:formatTF,styler:formatBorder"><?php _e('Time'); ?></th>
        <th width="4%" data-options="field:'Penalizacion',	align:'right',formatter:formatPenalizacionFinal" > <?php _e('Penaliz'); ?>.</th>
        <th width="4%" data-options="field:'Calificacion',	align:'center'" > <?php _e('Calif'); ?>.</th>
        <th width="4%" data-options="field:'Puesto',		align:'center',formatter:formatPuestoFinalBig" ><?php _e('Position'); ?></th>
    </tr>
    </thead>
</table>

<script type="text/javascript">

    $('#finales_last_equipos-datagrid').datagrid({
        expandCount: 0,
        // propiedades del panel asociado
        fit: false, // set to false as we used thead to declare columns, and they have their own width
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
        autoRowHeight:false,
        idField: 'ID',
        pageSize: 500 // enought bit to make it senseless
        // columns declared at html section to show additional headers
    });
</script>