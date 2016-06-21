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


/**
 * Limpia datos de jueces, trs y trm.
 * Se usa en los eventos "init" para borrar informacion previa
 */
function clearParcialRoundInformation() {
    $('#parciales-NombreManga').html("<?php _e('No round selected'); ?>");
    $('#parciales-Juez1').html("");
    $('#parciales-Juez2').html("");
    $('#parciales-Distancia').html("");
    $('#parciales-Obstaculos').html("");
    $('#parciales-TRS').html("");
    $('#parciales-TRM').html("");
    $('#parciales-Velocidad').html("");
}

function clearFinalRoundInformation() {
    $('#finales-NombreRonda').html("<?php _e('No round selected'); ?>");
    $('#finales-Juez1').html("");
    $('#finales-Juez2').html("");
    $('#finales-Ronda1').html("<?php _e('Data info for round'); ?> 1:");
    $('#finales-Distancia1').html("");
    $('#finales-Obstaculos1').html("");
    $('#inales-TRS1').html("");
    $('#finales-TRM1').html("");
    $('#finales-Velocidad1').html("");
    $('#finales-Ronda2').html("<?php _e('Data info for round'); ?> 2:");
    $('#finales-Distancia2').html("");
    $('#finales-Obstaculos2').html("");
    $('#finales-TRS2').html("");
    $('#finales-TRM2').html("");
    $('#finales-Velocidad2').html("");
}

/**
 * Muestra clasificacion final de los miembros de un equipo
 * @param parent datagrid de datos de equipos
 * @param idx indice de la fila del equipo asociado
 * @param row contendido de la fila del equipo asociado
 */
function showFinalScoresByTeam(parent,idx,row) {
    var mySelf=parent+"-"+parseInt(row.ID);
    // retrieve datagrid contents
    var maxdogs=getMaxDogsByTeam();
    var datos=[];
    for(var n=0; n<workingData.individual.length;n++) {
        var competitor=workingData.individual[n];
        if (competitor['Equipo']!=row.ID) continue;
        datos.push(competitor);
        if (datos.length>=maxdogs) break; // to speedup parsing data
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
            {field:'Dorsal',	width:'4%', align:'left',     title:"<?php _e('Dors'); ?>" },
            {field:'LogoClub',	hidden:true },
            {field:'Nombre',	width:'8%', align:'center',   title:"<?php _e('Name'); ?>",   formatter:formatBold},
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
        rowStyler:myRowStyler2,
        onResize:function(){
            $(mySelf).datagrid('fitColumns');
            $(parent).datagrid('fixDetailRowHeight',idx);
        },
        onLoadSuccess:function(data){
            $(mySelf).datagrid('fitColumns');
            setTimeout(function(){ $(parent).datagrid('fixDetailRowHeight',idx); },0);
        }
    });
    $(parent).datagrid('fixDetailRowHeight',idx);
}

/**
 * Muestra clasificacion parcial de los miembros de un equipo
 * @param parent datagrid de datos de equipos
 * @param idx indice de la fila del equipo asociado
 * @param row contendido de la fila del equipo asociado
 */
function showPartialScoresByTeam(parent,idx,row) {
    var mySelf=parent+"-"+parseInt(row.ID);
    // retrieve datagrid contents
    var datos=[];
    var maxdogs=getMaxDogsByTeam();
    for(var n=0; n<workingData.individual.length;n++) {
        var competitor=workingData.individual[n];
        if (competitor['Equipo']!=row.ID) continue;
        datos.push(competitor);
        if (datos.lenght>=maxdogs) break; // to speedup parse data
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
            {field:'Perro',		    hidden:true },
            {field:'Equipo',	    hidden:true },
            {field:'Dorsal',	    width:'5%', align:'right',   title:"<?php _e('Dorsal'); ?>" },
            {field:'LogoClub',	    hidden:true },
            {field:'Nombre',	    width:'10%', align:'center', title:"<?php _e('Name'); ?>",   formatter:formatBold},
            {field:'Licencia',	    width:'6%', align:'center', title:"<?php _e('Lic'); ?>." },
            {field:'Categoria',	    width:'6%', align:'center', title:"<?php _e('Cat'); ?>.",   formatter:formatCatGrad },
            {field:'Grado',	        hidden:true },
            {field:'NombreEquipo',  hidden:true },
            {field:'NombreGuia',    width:'18%',align:'right',  title:"<?php _e('Handler'); ?>" },
            {field:'NombreClub',    width:'14%',align:'right',  title:"<?php _e('Club'); ?>" },
            {field:'Faltas',		width:'4%', align:'center', title:"<?php _e('F/T'); ?>",    formatter:formatFaltasTocados,styler:formatBorder },
            {field:'Tocados',       hidden:true },
            {field:'Rehuses',		width:'4%', align:'center', title:"R." },
            {field:'Tiempo',		width:'6%', align:'right',  title:"<?php _e('Time'); ?>.",  formatter:formatTiempo },
            {field:'Velocidad',		width:'6%', align:'right',  title:"<?php _e('Vel'); ?>.",   formatter:formatVelocidad },
            {field:'Penalizacion',	width:'6%', align:'right',  title:"<?php _e('Penal'); ?>.", formatter:formatPenalizacion,styler:formatBorder},
            {field:'Calificacion',  width:'10%', align:'center', title:"<?php _e('Calif'); ?>." },
            {field:'Puesto',	    width:'5%', align:'center', title:"<?php _e('Position'); ?>",formatter:formatBold }
        ]],
        // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
        rowStyler:myRowStyler2,
        onResize:function(){
            $(mySelf).datagrid('fitColumns');
            $(parent).datagrid('fixDetailRowHeight',idx);
        },
        onLoadSuccess:function(data){
            $(mySelf).datagrid('fitColumns');
            setTimeout(function(){ $(parent).datagrid('fixDetailRowHeight',idx); },0);
        }
    });
    $(parent).datagrid('fixDetailRowHeight',idx);
}

/**
 * Actualizacion de resultados parciales en consola
 * Actualiza los datos de TRS y TRM de la fila especificada
 * Si se le indica, rellena tambien el datagrid re resultados parciales
 * @param {int} val 0:L 1:M 2:S 3:T
 * @param {boolean} fill true to fill resultados datagrid; else false
 */
function consoleReloadParcial(val,fill) {
    var mode=getMangaMode(workingData.datosPrueba.RSCE,workingData.datosManga.Recorrido,parseInt(val));
    if (mode==-1) {
        $.messager.alert('<?php _e('Error'); ?>','<?php _e('Internal error: invalid Federation/Course/Category combination'); ?>','error');
        return;
    }
    workingData.teamCounter=1; // reset team's puesto counter
    // reload resultados
    // en lugar de invocar al datagrid, lo que vamos a hacer es
    // una peticion ajax, para obtener a la vez los datos tecnicos de la manga
    $.ajax({
        type:'GET',
        url:"/agility/server/database/resultadosFunctions.php",
        dataType:'json',
        data: {
            Operation:	(isJornadaEquipos())?'getResultadosEquipos':'getResultados',
            Prueba:		workingData.prueba,
            Jornada:	workingData.jornada,
            Manga:		workingData.manga,
            Mode: mode
        },
        success: function(dat) {
            // update TRS data
            var suffix='L';
            switch (mode) {
                case 0: case 4: case 6: case 8: suffix='L'; break;
                case 1: case 3: suffix='M'; break;
                case 2: case 7: suffix='S'; break;
                case 5: suffix='T'; break;
            }
            $('#rm_DIST_'+suffix).val(dat['trs'].dist);
            $('#rm_OBST_'+suffix).val(dat['trs'].obst);
            $('#rm_TRS_'+suffix).val(dat['trs'].trs);
            $('#rm_TRM_'+suffix).val(dat['trs'].trm);
            var vel=(''+dat['trs'].vel).replace('&asymp;','\u2248');
            $('#rm_VEL_'+suffix).val(vel);

            // actualizar datagrid
            if (!fill) return;
            if ( isJornadaEquipos() ) {
                var dg=$('#parciales_equipos-datagrid');
                workingData.individual=dat.individual;
                dg.datagrid('options').expandCount = 0;
                dg.datagrid('loadData',dat.equipos);
            } else {
                var dg=$('#parciales_individual-datagrid');
                workingData.individual=dat.rows;
                dg.datagrid('loadData',dat.rows);
            }
        }
    });
}

/**
 * Actualiza los datos de TRS y TRM de la fila especificada
 * Rellena tambien el datagrid de resultados parciales
 */
function updateParciales(mode,row) {
    // si no nos pasan parametro, leemos el valor del combogrid
    if (typeof (mode) === "undefined") {
        var row=$('#enumerateParciales').combogrid('grid').datagrid('getSelected');
        if (!row) return;
        setManga(row);
        mode=row.Mode;
    } else {
        // informacion de la manga ya definida: ajusta texto y modo
        var modestr=getModeString(workingData.datosPrueba.RSCE,mode);
        $('#vw_header-infomanga').html(row.Manga.Nombre + " - " + modestr);
    }
    // en lugar de invocar al datagrid, lo que vamos a hacer es
    // una peticion ajax, para obtener a la vez los datos tecnicos de la manga
    // y de los jueces
    $.ajax({
        type:'GET',
        url:"/agility/server/database/resultadosFunctions.php",
        dataType:'json',
        data: {
            Operation:	(isJornadaEquipos())?'getResultadosEquipos':'getResultados',
            Prueba:		workingData.prueba,
            Jornada:	workingData.jornada,
            Manga:		workingData.manga,
            Mode:       parseInt(mode)
        },
        success: function(dat) {
            // use html() instead of text() to handle html special chars
            $('#parciales-NombreManga').html(workingData.nombreManga);
            $('#parciales-Juez1').html((dat['manga'].Juez1<=1)?"":dat['manga'].NombreJuez1);
            $('#parciales-Juez2').html((dat['manga'].Juez2<=1)?"":dat['manga'].NombreJuez2);
            // datos de TRS
            $('#parciales-Distancia').text(dat['trs'].dist);
            $('#parciales-Obstaculos').text(dat['trs'].obst);
            $('#parciales-TRS').text(dat['trs'].trs);
            $('#parciales-TRM').text(dat['trs'].trm);
            $('#parciales-Velocidad').text(dat['trs'].vel);
            // actualizar datagrid
            if ( isJornadaEquipos() ) {
                var dg=$('#parciales_equipos-datagrid');
                workingData.individual=dat.individual;
                dg.datagrid('options').expandCount = 0;
                dg.datagrid('loadData',dat.equipos);
            } else {
                var dg=$('#parciales_individual-datagrid');
                workingData.individual=dat.rows;
                dg.datagrid('loadData',dat.rows);
            }
        }
    });
}

/**
 * Actualiza datos de la clasificacion general
 */
function updateFinales(ronda,callback) {
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
            // datos de los jueces
            $('#finales-Juez1').html((dat['jueces'][0]=="-- Sin asignar --")?"":dat['jueces'][0]);
            $('#finales-Juez2').html((dat['jueces'][1]=="-- Sin asignar --")?"":dat['jueces'][1]);
            // datos de trs manga 1
            $('#finales-Ronda1').html(ronda.NombreManga1);
            $('#finales-Distancia1').text(dat['trs1'].dist);
            $('#finales-Obstaculos1').text(dat['trs1'].obst);
            $('#finales-TRS1').text(dat['trs1'].trs);
            $('#finales-TRM1').text(dat['trs1'].trm);
            $('#finales-Velocidad1').text( dat['trs1'].vel);
            // datos de trs manga 2
            if (ronda.Manga2==0) { // single round
                $('#finales-Ronda2').html("");
                $('#finales-Distancia2').text("");
                $('#finales-Obstaculos2').text("");
                $('#finales-TRS2').text("");
                $('#finales-TRM2').text("");
                $('#finales-Velocidad2').text("");
            } else {
                $('#finales-Ronda2').html(ronda.NombreManga2);
                $('#finales-Distancia2').text(dat['trs2'].dist);
                $('#finales-Obstaculos2').text(dat['trs2'].obst);
                $('#finales-TRS2').text(dat['trs2'].trs );
                $('#finales-TRM2').text(dat['trs2'].trm);
                $('#finales-Velocidad2').text(dat['trs2'].vel);
            }
            // clasificaciones

            if ( isJornadaEquipos() ) {
                $('#finales_equipos_roundname_m1').html(ronda.NombreManga1);
                $('#finales_equipos_roundname_m2').html(ronda.NombreManga2);
                var dg=$('#finales_equipos-datagrid');
                workingData.individual=dat.individual;
                dg.datagrid('options').expandCount = 0;
                dg.datagrid('loadData',dat.equipos);
            } else {
                $('#finales_individual_roundname_m1').html(ronda.NombreManga1);
                $('#finales_individual_roundname_m2').html(ronda.NombreManga2);
                var dg=$('#finales_individual-datagrid');
                workingData.individual=dat.rows;
                dg.datagrid('loadData',dat.rows);
            }
            // if defined, invoke callback
            if (typeof(callback)==='function') callback(dat);
        }
    });
}


function setFinalIndividualOrTeamView(data) {
    var team=false;
    if (parseInt(data.Jornada.Equipos3)!=0) { team=true; }
    if (parseInt(data.Jornada.Equipos4)!=0) { team=true;  }
    // limpiamos tablas
    // activamos la visualizacion de la tabla correcta
    if (team) {
        $("#finales_individual-table").css("display","none");
        $("#finales_last_individual-table").css("display","none");
        $("#finales_equipos-table").css("display","inherit");
        $("#finales_last_equipos-table").css("display","inherit");
        $("#finales_equipos-datagrid").datagrid('loadData', {"total":0,"rows":[]}).datagrid('fitColumns');
        $("#finales_last_equipos-datagrid").datagrid('loadData', {"total":0,"rows":[]}).datagrid('fitColumns');
    } else {
        $("#finales_equipos-table").css("display","none");
        $("#finales_last_equipos-table").css("display","none");
        $("#finales_individual-table").css("display","inherit");
        $("#finales_individual-table").css("display","inherit");
        $("#finales_individual-datagrid").datagrid('loadData', {"total":0,"rows":[]}).datagrid('fitColumns');
        $("#finales_last_individual-datagrid").datagrid('loadData', {"total":0,"rows":[]}).datagrid('fitColumns');
    }
}

function setParcialIndividualOrTeamView(data) {
    var team=false;
    if (parseInt(data.Jornada.Equipos3)!=0) { team=true; }
    if (parseInt(data.Jornada.Equipos4)!=0) { team=true;  }
    // limpiamos tablas
    // activamos la visualizacion de la tabla correcta
    if (team) {
        $("#parciales_individual-table").css("display","none");
        $("#parciales_last_individual-table").css("display","none");
        $("#parciales_equipos-table").css("display","inherit");
        $("#parciales_last_equipos-table").css("display","inherit");
        $("#parciales_equipos-datagrid").datagrid('loadData', {"total":0,"rows":[]}).datagrid('fitColumns');
        $("#parciales_last_equipos-datagrid").datagrid('loadData', {"total":0,"rows":[]}).datagrid('fitColumns');
    } else {
        $("#parciales_equipos-table").css("display","none");
        $("#parciales_last_equipos-table").css("display","none");
        $("#parciales_individual-table").css("display","inherit");
        $("#parciales_last_individual-table").css("display","inherit");
        $("#parciales_individual-datagrid").datagrid('loadData', {"total":0,"rows":[]}).datagrid('fitColumns');
        $("#parciales_last_individual-datagrid").datagrid('loadData', {"total":0,"rows":[]}).datagrid('fitColumns');
    }
}