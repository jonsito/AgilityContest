<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<table id="entrenamientos-datagrid" width="100%"></table>

<script type="text/javascript">

    $('#entrenamientos-datagrid').datagrid({
        columns: [[
            {field:'ID',     hidden:true},
            {field:'Prueba', hidden:true},
            {field:'Orden',       width:6,      align:'center', title:'#',     formatter: formatTrainingState},
            {field:'LogoClub',	  width:10,      align:'center', title:'',      formatter: formatLogo},
            {field:'NombreClub',  width:25,      align:'left',   title: '<?php _e('Club');?>' },
            {field:'Fecha',	      width:20,      align:'center', title: '<?php _e('Date');?>',formatter: formatYMD},
            {field:'Firma',       width:12,      align:'center', title: '<?php _e('Check-in');?>',formatter: formatHM},
            {field:'Veterinario', width:12,	    align:'center',  title: '<?php _e('Veterinary');?>',formatter: formatHM},
            {field:'Entrada',     width:12,      align:'right',  title: '<?php _e('Start');?>',formatter: formatHM},
            {field:'Duracion',    width:12,      align:'right',  title: '<?php _e('Duration');?>',formatter: formatMinSecs},
            {field:'Key1',        hidden:true},
            {field:'Value1',      width:18,      align:'center', title: '<?php _e('Ring');?> 1',formatter: formatTrainingCell1 },
            {field:'Key2',        hidden:true},
            {field:'Value2',      width:18,      align:'center', title: '<?php _e('Ring');?> 2',formatter: formatTrainingCell2 },
            {field:'Key3',        hidden:true},
            {field:'Value3',      width:18,      align:'center', title: '<?php _e('Ring');?> 3',formatter: formatTrainingCell3 },
            {field:'Key4',        hidden:true},
            {field:'Value4',      width:18,      align:'center', title: '<?php _e('Ring');?> 4',formatter: formatTrainingCell4 },
            {field:'-',           hidden:true},
            {field:'Observaciones',hidden:true },
            {field:'Estado', hidden:true}
        ]],
        nowrap: false,
        fit: false, // on fake container, do not try to fit
        height: 'auto',
        method: 'get',
        url: '/agility/server/database/trainingFunctions.php',
        queryParams: {
            Operation: 'select',
            Prueba: workingData.prueba, // when used from direct access
            Sesion: workingData.sesion // when used from event handler
        },
        loadMsg: "<?php _e('Updating training session order');?> ...",
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        singleSelect: true,
        autoRowHeight: true,
        // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
        rowStyler:myRowStyler,
        onLoadSuccess: function(data) {
            if (data['total']!=0) return;
            $.messager.alert("No data",'<?php _e("This contest has no training session defined");?>','info');
            workingData.timeout=null; // disable auto-refresh as no sense
        }
        // other parameters will be initializated later
    });

</script>