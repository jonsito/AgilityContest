<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
$nmangas=http_request("NumMangas","i","2");
?>

<table id="finales_individual-datagrid">
    <thead data-options="frozen:true">
    <tr>
        <th colspan="7"><span class="main_theader" id="finales_individual_teaminfo"><?php _e('Competitor data'); ?></span> </th>
        <th colspan="4"><span class="main_theader" id="finales_individual_finalscores"><?php _e('Final scores'); ?></span></th>
    </tr>
    <tr>
        <!-- datos identificativos -->
        <th width="3%" data-options="field:'LogoClub',		align:'left',formatter:formatLogo" > &nbsp;</th>
        <th width="3%" data-options="field:'Dorsal',		align:'left'" > <?php _e('Dors'); ?>.</th>
        <th width="7%" data-options="field:'Nombre',		align:'left',formatter:formatBold"> <?php _e('Name'); ?></th>
        <th width="4%" data-options="field:'Licencia',		align:'center'" > <?php _e('Lic'); ?>.</th>
        <th width="5%" data-options="field:'Categoria',	    align:'center',formatter:formatCatGrad" > <?php _e('Cat'); ?>.</th>
        <th width="9%" data-options="field:'NombreGuia',	align:'right'" > <?php _e('Handler'); ?></th>
        <th width="7%" data-options="field:'NombreClub',	align:'right'" id="finales_individual-ClubOrCountry"> <?php _e('Club'); ?></th>
        <!-- datos globales -->
        <th width="4%" data-options="field:'Tiempo',		align:'right',formatter:formatTF,styler:formatBorder"><?php _e('Time'); ?></th>
        <th width="4%" data-options="field:'Penalizacion',	align:'right',formatter:formatPenalizacionFinal" > <?php _e('Penaliz'); ?>.</th>
        <th width="4%" data-options="field:'Calificacion',	align:'center'" > <?php _e('Calif'); ?>.</th>
        <th width="4%" data-options="field:'Puesto',		align:'center',formatter:formatPuestoFinalBig" ><?php _e('Position'); ?></th>
    </tr>
    </thead>
    <thead data-options="frozen:false">
        <tr>
        <?php for ($nmanga=1;$nmanga<=$nmangas; $nmanga++ ) { ?>
            <th colspan="7"><span class="main_theader" id="finales_individual_roundname_m<?php echo $nmanga;?>"><?php _e('Round'); ?> <?php echo $nmanga;?></span></th>
        <?php } ?>
        </tr>
        <tr>
<?php for ($nmanga=1;$nmanga<=$nmangas; $nmanga++ ) { ?>
        <th width="2%" data-options="field:'F<?php echo $nmanga;?>',    align:'center',styler:formatBorder"> <?php _e('F/T'); ?></th>
        <th width="2%" data-options="field:'R<?php echo $nmanga;?>',	align:'center'"> <?php _e('R'); ?>.</th>
        <th width="4%" data-options="field:'T<?php echo $nmanga;?>',	align:'right',formatter:formatT<?php echo $nmanga;?>"> <?php _e('Time'); ?>.</th>
        <th width="3%" data-options="field:'V<?php echo $nmanga;?>',	align:'right',formatter:formatV<?php echo $nmanga;?>"> <?php _e('Vel'); ?>.</th>
        <th width="4%" data-options="field:'P<?php echo $nmanga;?>',	align:'right',formatter:formatP<?php echo $nmanga;?>"> <?php _e('Penal'); ?>.</th>
        <th width="5%" data-options="field:'C<?php echo $nmanga;?>',	align:'center'"> <?php _e('Cal'); ?>.</th>
        <th width="3%" data-options="field:'Puesto<?php echo $nmanga;?>',align:'center'"> <?php _e('Pos'); ?>.</th>
<?php } ?>
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
        fitColumns: true,
        singleSelect: true,
        rowStyler:myRowStyler,
        autoRowHeight:false,
        idField: 'ID',
        toolbar: '#resultados-tooolbar',
        pageSize: 500 // enought bit to make it senseless
        // columns declared at html section to show additional headers
    });

</script>