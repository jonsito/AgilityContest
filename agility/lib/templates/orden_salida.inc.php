<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<table id="ordensalida-datagrid" width="100%">
    <thead>
    <tr><!--
        <th width="0" data-options="field:'Prueba',		hidden:true"></th>
        <th width="0" data-options="field:'Jornada',	hidden:true"></th>
        <th width="0" data-options="field:'Manga',		hidden:true"></th>
        <th width="0" data-options="field:'Tanda',		hidden:true"></th>
        <th width="0" data-options="field:'ID',			hidden:true"></th>
        -->
        <th width="5%" data-options="field:'Perro',     align:'center',formatter: formatOrdenSalida">#</th>
        <!--
        <th width="0" data-options="field:'Pendiente',	hidden:true"></th>
        <th width="0" data-options="field:'Tanda',		hidden:true"></th>
        <th width="0" data-options="field:'Equipo',		hidden:true"></th>
        -->
        <th width="5%" data-options="field:'LogoClub',  align:'center',	formatter: formatLogo">&nbsp;</th>
        <th width="12%" data-options="field:'NombreEquipo',align:'center',hidden:true"><?php _e('Team'); ?></th>
        <th width="5%" data-options="field:'Dorsal',	align:'center',styler:checkPending"><?php _e('Dorsal'); ?> </th>
        <th width="18%" data-options="field:'Nombre',	align:'left',formatter: formatDogName"><?php _e('Name'); ?></th>
        <th width="5%" data-options="field:'Licencia',	align:'center'"><?php _e('License'); ?></th>
        <th width="15%" data-options="field:'Raza',     align:'center'"><?php _e('Breed'); ?></th>
        <th width="4%" data-options="field:'Categoria',	align:'center',formatter:formatCatGrad"><?php _e('Cat'); ?>.</th>
        <!--
        <th width="0" data-options="field:'Grado',		hidden:true"></th>
        -->
        <th width="17%" data-options="field:'NombreGuia',align:'right'"><?php _e('Handler'); ?></th>
        <th width="12%" data-options="field:'NombreClub',align:'right'"><?php _e('Club'); ?></th>
        <th width="4%" data-options="field:'Celo',		align:'center', formatter:formatCelo"><?php _e('Heat'); ?></th>
        <th width="10%" data-options="field:'Observaciones',align:'left'"><?php _e('Comments'); ?></th>
    </tr>
    </thead>
</table>

<script type="text/javascript">

    $('#ordensalida-datagrid').datagrid({
        nowrap: false,
        fit: false,
        height: 'auto',
        method: 'get',
        url: '/agility/server/database/tandasFunctions.php',
        /*
        queryParams: {
            Operation: 'getDataByTanda',
            Prueba: workingData.prueba,
            Jornada: workingData.jornada,
            Sesion: workingData.sesion // used only at startup. then use TandaID
        },
        */
        loadMsg: "<?php _e('Updating starting order');?> ...",
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        singleSelect: true,
        autoRowHeight: true,
        // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
        rowStyler:myRowStyler,
        onLoadSuccess:function(){
            var mySelf=$('#ordensalida-datagrid');
            // On Team journeys, show team name instead of club, and viceversa
            if (isJornadaEquipos(null) ) {
                mySelf.datagrid('showColumn','NombreEquipo');
                mySelf.datagrid('hideColumn','NombreClub');
            } else  {
                mySelf.datagrid('hideColumn','NombreEquipo');
                mySelf.datagrid('showColumn','NombreClub');
            }
            // on international contests hide license, and enlarge name to allow pedigree name
            if (isInternational(workingData.federation)) {
                mySelf.datagrid('hideColumn','Licencia');
                mySelf.datagrid('autoSizeColumn','Nombre');
            }
            mySelf.datagrid('fitColumns'); // expand to max width
            // start autoscrolling
            autoscroll(mySelf,0,ac_config.vw_polltime);
        }
    });

</script>