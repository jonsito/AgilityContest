<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
require_once(__DIR__ . "/../templates/resultados-toolbar.inc.php");
?>

<table id="finales_individual-datagrid">
    <thead>
    <tr>
        <th colspan="7"><span class="main_theader" id="finales_individual_teaminfo"><?php _e('Competitor data'); ?></span> </th>
        <th colspan="5"><span class="main_theader" id="finales_individual_roundname_m1"><?php _e('Round'); ?> 1</span></th>
        <th colspan="5"><span class="main_theader" id="finales_individual_roundname_m2"><?php _e('Round'); ?> 2</span></th>
        <th colspan="3"><span class="main_theader" id="finales_individual_finalscores"><?php _e('Final scores'); ?></span></th>
    </tr>
    <tr>
        <!--
        <th data-options="field:'Perro',		hidden:true " ></th>
         -->
        <th width="3%" data-options="field:'LogoClub',		align:'left',formatter:formatLogo" > &nbsp;</th>
        <th width="3%" data-options="field:'Dorsal',		align:'left'" > <?php _e('Dors'); ?>.</th>
        <th width="7%" data-options="field:'Nombre',		align:'left',formatter:formatDogName"> <?php _e('Name'); ?></th>
        <th width="4%" data-options="field:'Licencia',		align:'center'" > <?php _e('Lic'); ?>.</th>
        <th width="5%" data-options="field:'Categoria',	    align:'center',formatter:formatCatGrad" > <?php _e('Cat'); ?>.</th>
        <!--
        <th data-options="field:'Grado',		width:3, align:'center', formatter:formatGrado" > <?php _e('Grd'); ?>.</th>
        -->
        <th width="9%" data-options="field:'NombreGuia',	align:'right'" > <?php _e('Handler'); ?></th>
        <th width="7%" data-options="field:'NombreClub',	align:'right'" id="finales_individual-ClubOrCountry"> <?php _e('Club'); ?></th>


        <!--
        usaremos
        - las faltas para guardar secuencia de apertura
        - los rehuses para la secuencia de cierre
        - Penalizacion para el total
        - Escondemos velocidad y calificacion
        -->
        <!-- Snooker -->
        <th width="6%" data-options="field:'F1',    align:'center',styler:formatBorder"> <?php _e('Opening Seq'); ?></th>
        <th width="6%" data-options="field:'R1',	align:'center'"> <?php _e('Closing Seq'); ?>.</th>
        <th width="4%" data-options="field:'T1',	align:'right',formatter:formatT1"> <?php _e('Time'); ?>.</th>
        <th width="4%" data-options="field:'P1',	align:'right',formatter:formatP1"> <?php _e('Points'); ?>.</th>
        <th width="3%" data-options="field:'Puesto1',align:'center'"> <?php _e('Pos'); ?>.</th>

        <!-- Gambler -->
        <th width="6%" data-options="field:'F2',    align:'center',styler:formatBorder"> <?php _e('Opening Seq'); ?></th>
        <th width="6%" data-options="field:'R2',	align:'center'"> <?php _e('Gambler'); ?>.</th>
        <th width="4%" data-options="field:'T2',	align:'right',formatter:formatT2"> <?php _e('Time'); ?>.</th>
        <th width="4%" data-options="field:'P2',	align:'right',formatter:formatP2"> <?php _e('Points'); ?>.</th>
        <th width="3%" data-options="field:'Puesto2',align:'center'"> <?php _e('Pos'); ?>.</th>

        <!-- clasificacion final -->
        <th width="7%" data-options="field:'Tiempo',		align:'right',formatter:formatTF,styler:formatBorder"><?php _e('Time'); ?></th>
        <th width="7%" data-options="field:'Penalizacion',	align:'right',formatter:formatPenalizacionFinal" > <?php _e('Points'); ?>.</th>
        <th width="4%" data-options="field:'Puesto',		align:'center',formatter:formatPuestoFinalBig" ><?php _e('Position'); ?></th>
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
        width:'99%',
        pagination: false,
        rownumbers: false,
        fitColumns: false,
        singleSelect: true,
        rowStyler:myRowStyler,
        autoRowHeight:false,
        idField: 'ID',
        toolbar: '#resultados-toolbar',
        pageSize: 500 // enought bit to make it senseless
        // columns declared at html section to show additional headers
    });

</script>