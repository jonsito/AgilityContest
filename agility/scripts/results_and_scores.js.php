/*
results_and_scores.js.php

 Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

 This program is free software; you can redistribute it and/or modify it under the terms
 of the GNU General Public License as published by the Free Software Foundation;
 either version 2 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 See the GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along with this program;
 if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

<?php
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

/**
 * Funciones relacionadas presentacion de resultados y clasificaciones
 */

function showClasificacionesByTeam(parent,idx,row) {
    var mySelf=parent+"-"+parseInt(row.ID);
    // retrieve datagrid contents
    var datos=[];
    for(var n=0; n<workingData.individual.length;n++) {
        var competitor=workingData.individual[n];
        if (competitor['Equipo']!=row.ID) continue;
        datos.push(competitor);
    }
    // deploy datagrid
    $(mySelf).datagrid({
        fit:false,
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        singleSelect: true,
        width: '100%',
        height: 'auto',
        remote:false,
        idField: 'Perro',
        data: datos,
        columns: [[
            {field:'Perro',		hidden:true },
            {field:'Equipo',	hidden:true },
            {field:'Dorsal',	width:'3%', align:'left',     title:"<?php _e('Dors'); ?>" },
            {field:'LogoClub',	hidden:true },
            {field:'Nombre',	width:'9%', align:'center',   title:"<?php _e('Name'); ?>",   formatter:formatBold},
            {field:'Licencia',	width:'4%', align:'center',   title:"<?php _e('Lic'); ?>." },
            {field:'Categoria',	width:'4%', align:'center',   title:"<?php _e('Cat'); ?>.",   formatter:formatCategoria },
            {field:'Grado',	hidden:true },
            {field:'NombreEquipo',hidden:true },
            {field:'NombreGuia',width:'13%', align:'right',    title:"<?php _e('Handler'); ?>" },
            {field:'NombreClub',width:'12%', align:'right',    title:"<?php _e('Club'); ?>" },
            {field:'F1',		width:'2%', align:'center',   title:"<?php _e('F/T'); ?>",    styler:formatBorder },
            {field:'R1',		width:'2%', align:'center',   title:"R." },
            {field:'T1',		width:'5%', align:'right',    title:"<?php _e('Time'); ?>.",  formatter:formatT1 },
            {field:'V1',		width:'3%', align:'right',    title:"<?php _e('Vel'); ?>.",   formatter:formatV1 },
            {field:'P1',		width:'5%', align:'right',    title:"<?php _e('Penal'); ?>.", formatter:formatP1},
            {field:'C1',		width:'3%', align:'center',   title:"<?php _e('Cal'); ?>."},
            {field:'F2',		width:'2%', align:'center',   title:"<?php _e('F/T'); ?>",    styler:formatBorder },
            {field:'R2',		width:'2%', align:'center',   title:" <?php _e('R'); ?>." },
            {field:'T2',		width:'5%', align:'right',    title:"<?php _e('Time'); ?>.",  formatter:formatT2 },
            {field:'V2',		width:'3%', align:'right',    title:"<?php _e('Vel'); ?>.",   formatter:formatV2 },
            {field:'P2',		width:'5%', align:'right',    title:"<?php _e('Penal'); ?>.", formatter:formatP2 },
            {field:'C2',		width:'3%', align:'center',   title:"<?php _e('Cal'); ?>." },
            {field:'Tiempo',	width:'4%', align:'right',    title:"<?php _e('Time'); ?>",   formatter:formatTF,styler:formatBorder },
            {field:'Penalizacion',width:'5%',align:'right',   title:"<?php _e('Penaliz'); ?>.",formatter:formatPenalizacionFinal },
            {field:'Calificacion',width:'3%',align:'center',  title:"<?php _e('Calif'); ?>." },
            {field:'Puesto',	width:'3%', align:'center',   title:"<?php _e('Position'); ?>",formatter:formatBold }
        ]],
        // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
        rowStyler:myRowStyler,
        onResize:function(){
            $(parent).datagrid('fixDetailRowHeight',idx);
        },
        onLoadSuccess:function(data){
            setTimeout(function(){ $(parent).datagrid('fixDetailRowHeight',idx); },0);
        }
    });
    $(parent).datagrid('fixDetailRowHeight',idx);
}


/**
 * Actualiza los datos de TRS y TRM de la fila especificada
 * Rellena tambien el datagrid de resultados parciales
 */
function updateParciales(row) {
    if (typeof (row) === "undefined") {
        row=$('#enumerateParciales').combogrid('grid').datagrid('getSelected');
        if (!row) return;
        setManga(row);
    }
    // en lugar de invocar al datagrid, lo que vamos a hacer es
    // una peticion ajax, para obtener a la vez los datos tecnicos de la manga
    // y de los jueces
    $.ajax({
        type:'GET',
        url:"/agility/server/database/resultadosFunctions.php",
        dataType:'json',
        data: {
            Operation:	'getResultados',
            Prueba:		workingData.prueba,
            Jornada:	workingData.jornada,
            Manga:		workingData.manga,
            Mode:       parseInt(row.Mode)
        },
        success: function(dat) {
            // informacion de la manga
            // use html() instead of text() to handle html special chars
            $('#parciales-NombreManga').html(workingData.nombreManga);
            $('#parciales-Juez1').html((dat['manga'].Juez1<=1)?"":'<?php _e('Judge');?> 1: ' + dat['manga'].NombreJuez1);
            $('#parciales-Juez2').html((dat['manga'].Juez2<=1)?"":'<?php _e('Judge');?> 2: ' + dat['manga'].NombreJuez2);
            // datos de TRS
            $('#parciales-Distancia').html('<?php _e('Distance');?>: ' + dat['trs'].dist + 'm.');
            $('#parciales-Obstaculos').html('<?php _e('Obstacles');?>: ' + dat['trs'].obst);
            $('#parciales-TRS').html('<?php _e('Standard C. Time');?>: ' + dat['trs'].trs + 's.');
            $('#parciales-TRM').html('<?php _e('Maximum C. Time');?>: ' + dat['trs'].trm + 's.');
            $('#parciales-Velocidad').html('<?php _e('Speed');?>: ' + dat['trs'].vel + 'm/s');
            // actualizar datagrid
            workingData.teamCounter=1; // reset team's puesto counter
            $('#parciales-datagrid').datagrid('loadData',dat);
        }
    });
}

/**
 * Actualiza datos de la clasificacion general
 */
function updateFinales(ronda) {
    if (typeof(ronda)==="undefined") {
        ronda=$('#enumerateFinales').combogrid('grid').datagrid('getSelected');
        if (ronda==null) {
            // $.messager.alert("Error:","!No ha seleccionado ninguna ronda de esta jornada!","warning");
            return; // no way to know which ronda is selected
        }
    }
    // do not call doResults cause expected json data
    $.ajax({
        type:'GET',
        url:"/agility/server/database/clasificacionesFunctions.php",
        dataType:'json',
        data: {
            Operation: (isJornadaEquipos())?'clasificacionEquipos':'clasificacionIndividual',
            Prueba:	ronda.Prueba,
            Jornada:ronda.Jornada,
            Manga1:	ronda.Manga1,
            Manga2:	ronda.Manga2,
            Rondas: ronda.Rondas,
            Mode: 	ronda.Mode
        },
        success: function(dat) {
            // nombres de las mangas
            $('#finales-NombreRonda').html(ronda.Nombre);
            $('#finales_roundname_m1').html(ronda.NombreManga1);
            $('#finales_roundname_m2').html(ronda.NombreManga2);
            // datos de los jueces
            $('#finales-Juez1').html((dat['jueces'][0]=="-- Sin asignar --")?"":'<?php _e('Judge');?> 1: ' + dat['jueces'][0]);
            $('#finales-Juez2').html((dat['jueces'][1]=="-- Sin asignar --")?"":'<?php _e('Judge');?> 2: ' + dat['jueces'][1]);
            // datos de trs manga 1
            $('#finales-Ronda1').html(ronda.NombreManga1);
            $('#finales-Distancia1').html('<?php _e('Distance');?>: ' + dat['trs1'].dist + 'm.');
            $('#finales-Obstaculos1').html('<?php _e('Obstacles');?>: ' + dat['trs1'].obst);
            $('#finales-TRS1').html('<?php _e('Standard C. Time');?>: ' + dat['trs1'].trs + 's.');
            $('#finales-TRM1').html('<?php _e('Maximum C. Time');?>: ' + dat['trs1'].trm + 's.');
            $('#finales-Velocidad1').html('<?php _e('Speed');?>: ' + dat['trs1'].vel + 'm/s');
            // datos de trs manga 2
            if (ronda.Manga2==0) { // single round
                $('#finales-Ronda2').html("");
                $('#finales-Distancia2').html("");
                $('#finales-Obstaculos2').html("");
                $('#finales-TRS2').html("");
                $('#finales-TRM2').html("");
                $('#finales-Velocidad2').html("");
            } else {
                $('#finales-Ronda2').html(ronda.NombreManga2);
                $('#finales-Distancia2').html('<?php _e('Distance');?>: ' + dat['trs2'].dist + 'm.');
                $('#finales-Obstaculos2').html('<?php _e('Obstacles');?>: ' + dat['trs2'].obst);
                $('#finales-TRS2').html('<?php _e('Standard C. Time');?>: ' + dat['trs2'].trs + 's.');
                $('#finales-TRM2').html('<?php _e('Maximum C. Time');?>: ' + dat['trs2'].trm + 's.');
                $('#finales-Velocidad2').html('<?php _e('Speed');?>: ' + dat['trs2'].vel + 'm/s');
            }
            // clasificaciones

            if ( isJornadaEquipos() ) {
                var dg=$('#finales_equipos-datagrid');
                workingData.individual=dat.individual;
                dg.datagrid('options').expandCount = 0;
                dg.datagrid('loadData',dat.equipos);
            } else {var dg=$('#finales_individual-datagrid');
                workingData.individual=dat.rows;
                dg.datagrid('loadData',dat.rows);
            }
        }
    });
}
