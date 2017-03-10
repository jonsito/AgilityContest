/*
results_and_scores.js.php

 Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

function scores_emailEditJuez(index,row) {
    var m = $.messager.prompt({
        title: "<?php _e('Change Email');?>",
        msg: "<?php _e('Enter new email for judge');?>: <br/>"+row.Nombre,
        fn: function(r) {
            if (!r) return false;
            $.ajax({
                type: 'GET',
                url: '/agility/server/mailFunctions.php',
                data: {
                    Prueba: workingData.prueba,
                    Federation: workingData.federation,
                    Juez: row['ID'],
                    Email: r,
                    Operation: "updateJuez"
                },
                dataType: 'json',
                // beforeSend: function(jqXHR,settings){ return frm.form('validate'); },
                success: function (result) {
                    if (result.errorMsg){
                        $.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
                        return false;
                    }
                    $('#scores_email-Jueces').datagrid('updateRow',{
                        index: index,
                        row: {Email:r}
                    });
                }
            });
        },
        width: 350
    });
    m.find('.messager-input').val(row.Email); // set default value for prompt
    return false;
}

/**
 * Open dialog for sending email to judge(s) and federation
 * @param {boolean} teams false:individual true:teams
 */
function emailClasificaciones(teams) {
    if (ac_regInfo.Serial==="00000000") {
        $.messager.alert('<?php _e("Mail services"); ?>',
            '<p><?php _e("Electronic mail operations<br/>are not allowed for unregistered licenses"); ?></p>',
            "info").window('resize',{width:480});
        return false;
    }
    $('#scores_email-dialog').dialog('open').dialog('setTitle', '<?php _e('Email results'); ?>');
    return false;
}

/**
 * Ask send mail with contest info and inscription templates to each selected club
 */
function perform_emailScores() {
    var dg = $('#scores_email-Jueces');
    var selectedRows = dg.datagrid('getSelections');
    var size = selectedRows.length;
    if (ac_authInfo.Perms > 2) {
        $.messager.alert('<?php _e("No permission"); ?>', '<?php _e("Current user has not enought permissions to send mail"); ?>', "error");
        return; // no tiene permiso para realizar inscripciones. retornar
    }
    if (size == 0) {
        $.messager.alert('<?php _e("No selection"); ?>', '<?php _e("There is no selected judge to send mail to"); ?>', "warning");
        return; // no hay ninguna inscripcion seleccionada. retornar
    }
    // compose list of receivers. ( need to send one and only one mail to all, to assure data integrity )
    var list = '';
    for (var n = 0; n < size; n++) { list = list + selectedRows[n]['Email'];  if (n < size - 1) list = list + ','  }
    // prepare a progress bar to mark running
    $.messager.progress({title:"<?php _e('Sending');?>...",msg:"<?php _e('Sending results and scores to'); ?> : <br/>" + list,text:""});
    $.ajax({
        cache: false,
        timeout: 30000, // 20 segundos
        type: 'POST',
        url: "/agility/server/mailFunctions.php",
        dataType: 'json',

        data: {
            Prueba: workingData.prueba,
            Jornada: workingData.jornada,
            Federation: workingData.federation,
            Operation: 'sendResults',
            Juez: 0, // not needed as we send just a comma separated list
            Email: list,
            SendToFederation: $('#scores_email-SendToFederation').val(),
            FedAddress: $('#scores_email-FedAddress').textbox('getValue'),
            PartialScores: $('#scores_email-PartialScores').val(),
            Contents: $('#scores_email-Contents').val()
        },
        success: function (result) {
            $.messager.progress('close');
            if (result.errorMsg) {
                $.messager.alert({width:350, height:150, title:'<?php _e('Error'); ?>',msg: result.errorMsg,icon:'error' });
            } else {
                $.messager.alert({width:300, height:150, title:'<?php _e('Done'); ?>',msg:'<?php _e('Mail successfully sent'); ?>' ,icon:'info' });
            }
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            $.messager.progress('close');
            alert("Send scores by mail error: "+textStatus + " "+ errorThrown );
        }
    });
}

/**
 * Ejecuta un scroll del datagrid cada cierto tiempo
 * @param dg datagrid a "scrollear"
 * @param pos posicion donde se realiza el scroll
 * @param time periodo de scroll. 0 detiene movimiento
 */
function autoscroll(dg,pos,time) {
    var pTime=parseInt(time); // seconds
    if ( (pTime==0) || (ac_config.allow_scroll==false)) { // autoscroll disabled. stay on top
        dg.datagrid('scrollTo',0);
        return;
    }
    var size=dg.datagrid('getRows').length;
    setTimeout(function(){
        dg.datagrid('scrollTo',pos);
        if ( pos==(size-1)) pos=0; // at end: go to beging
        else pos+=10;
        if (pos>=size) pos=size-1; // next to end: pos at end
        autoscroll(dg,pos);
    },1000*pTime);
}

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
            // {field:'Licencia',	width:'4%', align:'center',   title:"<?php _e('Lic'); ?>." },
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
            $(this).datagrid('autoSizeColumn','Nombre').datagrid('fitColumns'); // fix size according name
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
            {field:'Nombre',	    width:'17%', align:'left', title:"<?php _e('Name'); ?>",   formatter:formatDogName},
            // {field:'Licencia',	    width:'6%', align:'center', title:"<?php _e('Lic'); ?>." },
            {field:'Categoria',	    width:'6%', align:'center', title:"<?php _e('Cat'); ?>.",   formatter:formatCatGrad },
            {field:'Grado',	        hidden:true },
            {field:'NombreEquipo',  hidden:true },
            {field:'NombreGuia',    width:'12%',align:'right',  title:"<?php _e('Handler'); ?>" },
            {field:'NombreClub',    width:'12%',align:'right',  title:clubOrCountry },
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
            if (data.total==0) return;
            $(this).datagrid('autoSizeColumn','Nombre').datagrid('fitColumns'); // fix size according name
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
            Operation:	(isJornadaEquipos(null))?'getResultadosEquipos':'getResultados',
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
            if ( isJornadaEquipos(null) ) {
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
            Operation:	(isJornadaEquipos(null))?'getResultadosEquipos':'getResultados',
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
            if ( isJornadaEquipos(null) ) {
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
 * @param {integer} perro. Si != 0 añadir entrada extra indicando el tiempo que tiene que hacer para quedar primero
 * @param {object} ronda Datos de la ronda que estamos. if undefined retrieve from combo
 * @param {function} callback que hacer una vez recibidos los datos
 */
function updateFinales(perro,ronda,callback) {
    if (typeof(ronda)==="undefined") ronda=$('#enumerateFinales').combogrid('grid').datagrid('getSelected');
    if (ronda==null) return; // no way to know which ronda is selected
    // do not call doResults cause expected json data
    $.ajax({
        type:'GET',
        url:"/agility/server/database/clasificacionesFunctions.php",
        dataType:'json',
        data: {
            Operation: (isJornadaEquipos(null))?'clasificacionEquipos':'clasificacionIndividual',
            Prueba:	ronda.Prueba,
            Jornada:ronda.Jornada,
            Manga1:	ronda.Manga1,
            Manga2:	ronda.Manga2,
            Rondas: ronda.Rondas,
            Mode: 	ronda.Mode,
            Perro:  perro
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

            if ( isJornadaEquipos(null) ) {
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
