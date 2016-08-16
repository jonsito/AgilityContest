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
            {field:'Orden',       width:10,      align:'center', title:'#',     formatter: formatBoldBig},
            {field:'LogoClub',	  width:10,      align:'center', title:'',      formatter: formatLogo},
            {field:'NombreClub',  width:25,      align:'left',   title: '<?php _e('Club');?>' },
            {field:'Fecha',	      width:20,      align:'center', title: '<?php _e('Date');?>',formatter: formatYMD},
            {field:'Firma',       width:15,      align:'center', title: '<?php _e('Check-in');?>',formatter: formatHM},
            {field:'Veterinario', width:15,	    align:'center',  title: '<?php _e('Veterinary');?>',formatter: formatHM},
            {field:'Entrada',     width:20,      align:'right',  title: '<?php _e('Start');?>',formatter: formatHMS},
            {field:'Salida',      width:20,      align:'right',  title: '<?php _e('End');?>',formatter: formatHMS},
            {field:'L',           width:10,      align:'center', title: '<?php _e('Large');?>' },
            {field:'M',           width:10,      align:'center', title: '<?php _e('Medium');?>' },
            {field:'S',           width:10,      align:'center', title: '<?php _e('Small');?>' },
            {field:'T',           width:10,      align:'center', title: '<?php _e('Toy');?>' },
            {field:'-',           hidden:true},
            {field:'Observaciones',width:15,     align:'center', title: '<?php _e('Comments');?>' },
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
        rowStyler:myRowStyler
        // other parameters will be initializated later
    });

</script>