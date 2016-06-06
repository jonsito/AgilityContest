<?php
require_once(__DIR__."/../../server/tools.php");
require_once(__DIR__."/../../server/auth/Config.php");
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
        <th data-options="field:'Dorsal',		width:'3%', align:'left'" > <?php _e('Dors'); ?>.</th>
        <th data-options="field:'LogoClub',		width:'3%', align:'left',formatter:formatLogo" > &nbsp;</th>
        <th data-options="field:'Nombre',		width:'7%', align:'center',formatter:formatBold"> <?php _e('Name'); ?></th>
        <th data-options="field:'Licencia',		width:'4%', align:'center'" > <?php _e('Lic'); ?>.</th>
        <th data-options="field:'Categoria',	width:'5%', align:'center',formatter:formatCatGrad" > <?php _e('Cat'); ?>.</th>
        <!--
        <th data-options="field:'Grado',		width:'3%', align:'center', formatter:formatGrado" > <?php _e('Grd'); ?>.</th>
        -->
        <th data-options="field:'NombreGuia',	width:'9%', align:'right'" > <?php _e('Handler'); ?></th>
        <th data-options="field:'NombreClub',	width:'7%', align:'right'" > <?php _e('Club'); ?></th>
        <th data-options="field:'Faltas',			width:'2%', align:'center',styler:formatBorder"> <?php _e('F/T'); ?></th>
        <!--
        <th data-options="field:'Tocados',	hidden:true ">/th>
        -->
        <th data-options="field:'Rehuses',			width:'2%', align:'center'"> <?php _e('R'); ?>.</th>
        <!--
        <th data-options="field:'PRecorrido',	hidden:true ">/th>
        -->
        <th data-options="field:'Tiempo',		width:'4%', align:'right',formatter:formatTF,styler:formatBorder"><?php _e('Time'); ?></th>
        <!--
        <th data-options="field:'PTiempo',	hidden:true ">/th>
        -->
        <th data-options="field:'Velocidad',	width:'3%', align:'right',formatter:formatV1"> <?php _e('Vel'); ?>.</th>
        <th data-options="field:'Penalizacion',	width:'4%', align:'right',formatter:formatPenalizacionFinal" > <?php _e('Penaliz'); ?>.</th>
        <th data-options="field:'Calificacion',	width:'4%', align:'center'" > <?php _e('Calif'); ?>.</th>
        <th data-options="field:'Puesto',		width:'4%', align:'center',formatter:formatPuestoFinalBig" ><?php _e('Position'); ?></th>
        <!--
        <th data-options="field:'CShort',	hidden:true ">/th>
        -->
    </tr>
    </thead>
</table>

<script type="text/javascript">
    $('#parciales_individual-datagrid').datagrid({
        // propiedades del panel asociado
        fit: true,
        border: false,
        closable: false,
        collapsible: false,
        collapsed: false,
        // propiedades del datagrid
        method: 'get',
        url: '/agility/server/database/resultadosFunctions.php',
        queryParams: {
            Prueba: workingData.prueba,
            Jornada: workingData.jornada,
            Manga: workingData.manga,
            Mode: (workingData.datosManga.Recorrido!=2)?0:4, // def to 'Large' or 'LMS' depending of datosmanga
            Operation: 'getResultados'
        },
        loadMsg:  "<?php _e('Updating partial scores');?> ...",
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        singleSelect: true,
        autoRowHeight: true,
        view: defaultView,
        idField: 'NombreEquipo',
        rowStyler:myRowStyler,
        onBeforeLoad: function(param) { // do not load if no manga selected
            var row=$('#pb_enumerateParciales').combogrid('grid').datagrid('getSelected');
            if (!row) return false;
            return true;
        }
    });
</script>