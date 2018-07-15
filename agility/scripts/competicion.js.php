/*
competicion.js

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
 * Funciones relacionadas con la ventana de competicion
 */

/**
 * Apertura de la ventana de competicion. Se invoca desde el menu de desarrollo de jornadas
 * o desde la ventana de clasificaciones
 */
function loadCompetitionWindow() {
    // default values for titles, page-to-load and need-to-close dialogs
    var extra="";
    var page="../console/frm_competicion2.php?tipo=std";
    var dialogs={
        'e':'#entrenamientos-dialog',
        't':'#ordentandas-dialog',
        's':'#ordensalida-dialog',
        'c':'#competicion-dialog',
        'r':'#resultadosmanga-dialog',
        'e1':'#entrenamientos-excel-dialog',
        'e2':'#resultadosmanga-excel-dialog',
        'os':'#ordensalida-excel-dialog'
    };
    if (parseInt(workingData.datosJornada.Equipos3)!==0) {
        page="../console/frm_competicion2.php?tipo=eq3";
        extra=" ( <?php _e('Teams 3');?> )";
        // use default dialogs
    }
    if (parseInt(workingData.datosJornada.Equipos4)!==0) {
        page="../console/frm_competicion2.php?tipo=eq4";
        extra=" ( <?php _e('Teams 4');?> )";
        // use default dialogs
    }
    if (parseInt(workingData.datosJornada.Open)!==0) {
        // an Individual - Open Contest is like a normal with no Grades but only categories
        page="../console/frm_competicion2.php?tipo=open";
        extra=" ( <?php _e('Individual');?> )";
    }
    if (parseInt(workingData.datosJornada.KO)!==0) {
        page="../console/frm_competicion2.php?tipo=ko";
        extra=" ( <?php _e('K.O. Rounds');?> )";
    }
    if (parseInt(workingData.datosJornada.Games)!==0) { // number shows how many rounds for series
        var t=parseInt(workingData.datosJornada.Tipo_Competicion);
        page="../console/frm_competicion2.php?tipo=games&mode="+t;
        if (t===1) extra=" ( <?php _e('WAO / Penthatlon');?> )";
        if (t===2) extra=" ( <?php _e('WAO / Biathlon');?> )";
        if (t===3) extra=" ( <?php _e('WAO / Games');?> )";
    }
    if (parseInt(workingData.datosJornada.Cerrada)!==0) {
        // allow deploy closed journeys, but do not allow modify
        $.messager.alert(
            "<?php _e('Notice');?>",
            "<?php _e('Selected journey is marked as closed<br/>You cannot add nor modify data');?>",
            "warning",
            function() {
                loadContents( page, '<?php _e('Journey deployment');?>'+extra, dialogs );
            }
        ).window('resize',{width:350});
    } else {
        check_softLevel( access_level.PERMS_OPERATOR,	function() {
            loadContents( page, '<?php _e('Journey deployment');?>'+extra, dialogs );
        } );
    }
}

/************************** Gestion de datos de la ventana de manga activa */

/**
 * Obtiene el modo de visualizacion de una manga determinada en funcion de la prueba, tipo de recorrido y categorias
 * @param {int} fed federation ID
 * @param {int} recorrido 0:separado 1:mixto 2:conjunto
 * @param {int} categoria 0:L 1:M 2:S 3:T
 * @returns {int} requested mode. -1 if invalid request
 */
function getMangaMode(fed,recorrido,categoria) {
    var f=parseInt(fed);
    var rec=parseInt(recorrido);
    if (typeof (ac_fedInfo[f]) === "undefined") {
        $.messager.show({width: 300, height: 200, msg: '<?php _e('Invalid or undefined Federation'); ?>', title: 'Error'});
        return -1;
    }
    if (typeof (ac_fedInfo[f]['InfoManga'][rec]) === "undefined") {
        $.messager.show({width: 300, height: 200, msg: '<?php _e('Invalid course mode'); ?>', title: 'Error'});
        return -1;
    }
    switch(categoria) {
        case '-':
        case 'LMST':
        case '-LMST':return ac_fedInfo[f].Modes[2][0]; // common for all categories; just use first mode (standard )
        case 'L':return ac_fedInfo[f].Modes[rec][0];
        case 'M':return ac_fedInfo[f].Modes[rec][1];
        case 'S':return ac_fedInfo[f].Modes[rec][2];
        case 'T':return ac_fedInfo[f].Modes[rec][3];
        // numerical index may also be requested
        default: return ac_fedInfo[f].Modes[rec][parseInt(categoria)];
    }
    return -1;
}

function getModeString(fed,mode) {
    var f=parseInt(fed);
    var m=parseInt(mode);
    if (typeof (ac_fedInfo[f]) === "undefined") {
        $.messager.show({width: 300, height: 200, msg: '<?php _e('Invalid or undefined Federation'); ?>', title: 'Error'});
        return 'Undefined';
    }
    if (typeof (ac_fedInfo[f].IndexedModes[m]) === "undefined") {
        $.messager.show({width: 300, height: 200, msg: '<?php _e('Invalid course mode'); ?>', title: 'Error'});
        return 'Undefined';
    }
    return ac_fedInfo[f].IndexedModes[m];
}

/**
 * Obtiene el texto asociado al modo de visualizacion de una manga determinada en funcion de la prueba, tipo de recorrido y categorias
 * @param {int} fed federation ID
 * @param {int} recorrido 0:separado 1:mixto 2:conjunto
 * @param {int} categoria 0:L 1:M 2:S 3:T
 * @returns {int} requested string. "Invalid" if invalid request
 */
function getMangaModeString(fed,recorrido,categoria) {
    var f=parseInt(fed);
    var rec=parseInt(recorrido);
    if (typeof (ac_fedInfo[f]) === "undefined") {
        $.messager.show({width: 300, height: 200, msg: '<?php _e('Invalid or undefined Federation'); ?>', title: 'Error'});
        return 'Undefined';
    }
    if (typeof (ac_fedInfo[f].ModeStrings[rec]) === "undefined") {
        $.messager.show({width: 300, height: 200, msg: '<?php _e('Invalid course mode'); ?>', title: 'Error'});
        return 'Undefined';
    }
    switch(categoria) {
        case '-':
        case '-LMST':return ac_fedInfo[f].ModeStrings[rec][0]; // common for all categories; just use first mode (standard )
        case 'L':return ac_fedInfo[f].ModeStrings[rec][0];
        case 'M':return ac_fedInfo[f].ModeStrings[rec][1];
        case 'S':return ac_fedInfo[f].ModeStrings[rec][2];
        case 'T':return ac_fedInfo[f].ModeStrings[rec][3];
        // numerical index may also be requested
        default: return ac_fedInfo[f].ModeStrings[rec][parseInt(categoria)];
    }
    return 'Undefined';
}

/**
 * Evaluate if possible and repaint timespeed readonly input box for each category
 */
function dmanga_evalTimeSpeed() {
    // fase 1: evaluamos datos de categoria Large
    var d=parseInt($("#dmanga_DistL").textbox('getValue'));
    var f=parseFloat($("#dmanga_TRS_L_Factor").textbox('getValue'));
    var time_l=-1;
    var speed_l=-1;
    var tspeed_l="-";
    switch ($("#dmanga_TRS_L_Tipo").textbox('getValue')) {
        case "0": // tipo fijo X segundos
            time_l= f; speed_l= (f==0)? 0: d/time_l; tspeed_l = toFixedT(speed_l,2)+" m/s";
            break;
        case "1": // mejor tiempo + xxx
        case "2": // media 3 mejores + xxx
            // use defaults
            break;
        case "6": // velocidad X metros por segundo
            speed_l= f; time_l= (f==0)?0: d/speed_l; tspeed_l = toFixedT(time_l,1)+" seg";
            break;
    }
    $("#dmanga_TRS_L_TimeSpeed").textbox('setValue',tspeed_l);

    // fase 2: evaluamos datos de categoria Medium
    d=parseInt($("#dmanga_DistM").textbox('getValue'));
    f=parseFloat($("#dmanga_TRS_M_Factor").textbox('getValue'));
    var u=$("#dmanga_TRS_M_Unit").combobox('getValue');
    var time_m=-1;
    var speed_m=-1;
    var tspeed_m="-";
    switch ($("#dmanga_TRS_M_Tipo").combobox('getValue')) {
        case "0": // tipo fijo X segundos
            time_m= f; speed_m= (f==0)? 0: d/time_m; tspeed_m = toFixedT(speed_m,2)+" m/s";
            break;
        case "1": // mejor tiempo + xxx
        case "2": // media 3 mejores + xxx
            // use defaults
            break;
        case "3": // tiempo estandard + xxx
            if ( tspeed_l != "-" ) {
                time_m = ( 's' == u )? time_l+f :time_l * ((100.0+f)/100.0); speed_m= (d==0)? 0: d/time_m; tspeed_m= toFixedT(speed_m,2)+" m/s";
            }
            break;
        case "6": // velocidad X metros por segundo
            speed_m= f; time_m= (f==0)?0: d/speed_m; tspeed_m = toFixedT(time_m,1)+" seg";
            break;
    }
    $("#dmanga_TRS_M_TimeSpeed").textbox('setValue',tspeed_m);

    // fase 3: evaluamos datos de categoria Small
    d=parseInt($("#dmanga_DistS").textbox('getValue'));
    f=parseFloat($("#dmanga_TRS_S_Factor").textbox('getValue'));
    u=$("#dmanga_TRS_S_Unit").combobox('getValue');
    var time_s=-1;
    var speed_s=-1;
    var tspeed_s="-";
    switch ($("#dmanga_TRS_S_Tipo").combobox('getValue')) {
        case "0": // tipo fijo X segundos
            time_s= f; speed_s= (f==0)? 0: d/time_s; tspeed_s = toFixedT(speed_s,2)+" m/s";
            break;
        case "1": // mejor tiempo + xxx
        case "2": // media 3 mejores + xxx
            // use defaults
            break;
        case "3": // tiempo estandard + xxx
            if ( tspeed_l != "-" ) {
                time_s = ( 's' == u )? time_l+f :time_l * ((100.0+f)/100.0); speed_s= (d==0)? 0: d/time_m; tspeed_s= toFixedT(speed_m,2)+" m/s";
            }
            break;
        case "4": // tiempo medium + xxx
            if (tspeed_m!="-") {
                time_s = ( 's' == u )? time_m+f :time_m * ((100.0+f)/100.0); speed_s= (d==0)? 0: d/time_s; tspeed_s= toFixedT(speed_s,2)+" m/s";
            }
            break;
        case "6": // velocidad X metros por segundo
            speed_s= f; time_s= (f==0)?0: d/speed_s; tspeed_s = toFixedT(time_s,1)+" seg";
            break;
    }
    $("#dmanga_TRS_S_TimeSpeed").textbox('setValue',tspeed_s);

    if ( ac_fedInfo[workingData.federation].Heights<4 ) return;

    // fase 4: evaluamos datos de categoria Toy
    d=parseInt($("#dmanga_DistT").textbox('getValue'));
    f=parseFloat($("#dmanga_TRS_T_Factor").textbox('getValue'));
    u=$("#dmanga_TRS_T_Unit").combobox('getValue');
    var time_t=-1;
    var speed_t=-1;
    var tspeed_t="-";
    switch ($("#dmanga_TRS_T_Tipo").combobox('getValue')) {
        case "0": // tipo fijo X segundos
            time_t= f; speed_t= (f==0)? 0: d/time_t; tspeed_t = toFixedT(speed_t,2)+" m/s";
            break;
        case "1": // mejor tiempo + xxx
        case "2": // media 3 mejores + xxx
            // use defaults
            break;
        case "3": // tiempo estandard + xxx
            if ( tspeed_l != "-" ) {
                time_t = ( 's' == u )? time_l+f :time_l * ((100.0+f)/100.0); speed_t= (d==0)? 0: d/time_t; tspeed_t= toFixedT(speed_t,2)+" m/s";
            }
            break;
        case "4": // tiempo medium + xxx
            if (tspeed_m!="-") {
                time_t = ( 's' == u )? time_m+f :time_m * ((100.0+f)/100.0); speed_t= (d==0)? 0: d/time_t; tspeed_t= toFixedT(speed_t,2)+" m/s";
            }
            break;
        case "5": // tiempo small + xxx
            if (tspeed_s!="-") {
                time_t = ( 's' == u )? time_s+f :time_s * ((100.0+f)/100.0); speed_t= (d==0)? 0: d/time_t; tspeed_t= toFixedT(speed_t,2)+" m/s";
            }
            break;
        case "6": // velocidad X metros por segundo
            speed_t= f; time_t= (f==0)?0: d/speed_t; tspeed_t = toFixedT(time_t,1)+" seg";
            break;
    }
    $("#dmanga_TRS_T_TimeSpeed").textbox('setValue',tspeed_t);
}

/**
 * repaint manga information acording federation and course mode
 */
function dmanga_setRecorridos() {
    var fed=workingData.federation;
    var rec=$("input[name='Recorrido']:checked").val();
    if (typeof (ac_fedInfo[fed]) === "undefined") {
        $.messager.show({width: 300, height: 200, msg: '<?php _e('Invalid or undefined Federation'); ?>', title: 'Error'});
        return false;
    }
    if (typeof (ac_fedInfo[fed]['InfoManga'][rec]) === "undefined") {
        $.messager.show({width: 300, height: 200, msg: '<?php _e('Invalid course mode'); ?>', title: 'Error'});
        return false;
    }
    var data=ac_fedInfo[fed]['InfoManga'][rec];// {object{L,M,S,T} } data
    var last=data.L;

    // first row (large) allways to be shown
    var trs_tipo=$('#dmanga_TRS_L_Tipo').combobox('getValue');     //0:fixed 1:best+ 2:mean+ 3:L+ 4:M+ 5:S+ 6:speed
    var trs_factor=$('#dmanga_TRS_L_Factor').textbox('getValue'); //0:seconds 1:percentage
    var trm_tipo=$('#dmanga_TRM_L_Tipo').combobox('getValue');     //0:fixed 1:trs+
    var trm_factor=$('#dmanga_TRM_L_Factor').textbox('getValue'); //0:seconds 1:percentage
    var dist=$('#dmanga_DistL').textbox('getValue');
    var obst=$('#dmanga_ObstL').textbox('getValue');
    $('#dmanga_LargeRow').css('display','table-row');
    $('#dmanga_LargeLbl').html(data.L);

    // second row ( medium )
    if (data.M!=="") {
        // use own data and make it visible
        tipo=4;
        last=data.L;
        dist=$('#dmanga_DistM').textbox('getValue');
        obst=$('#dmanga_ObstM').textbox('getValue');
        trs_tipo=$('#dmanga_TRS_M_Tipo').combobox('getValue');
        trs_factor=$('#dmanga_TRS_M_Factor').textbox('getValue');
        trm_tipo=$('#dmanga_TRM_M_Tipo').combobox('getValue');
        trm_factor=$('#dmanga_TRM_M_Factor').textbox('getValue');
        $('#dmanga_MediumRow').css('display','table-row');
        $('#dmanga_MediumLbl').html(data.M);
    } else {
        // use parent data and hide
        $('#dmanga_DistM').textbox('setValue',dist);
        $('#dmanga_ObstM').textbox('setValue',obst);
        $('#dmanga_MediumRow').css('display','none');
        $('#dmanga_MediumLbl').html(last);
        $('#dmanga_TRS_M_Tipo').combobox('setValue',trs_tipo);
        $('#dmanga_TRS_M_Factor').textbox('setValue',trs_factor);
        $('#dmanga_TRM_M_Tipo').combobox('setValue',trm_tipo);
        $('#dmanga_TRM_M_Factor').textbox('setValue',trm_factor);
    }

    // third row (small )
    if (data.S!=="") {
        // use own data and make it visible
        tipo=5;
        last=data.S;
        dist=$('#dmanga_DistS').textbox('getValue');
        obst=$('#dmanga_ObstS').textbox('getValue');
        $('#dmanga_SmallRow').css('display','table-row');
        $('#dmanga_SmallLbl').html(data.S);
        trs_tipo=$('#dmanga_TRS_S_Tipo').combobox('getValue');
        trs_factor=$('#dmanga_TRS_S_Factor').textbox('getValue');
        trm_tipo=$('#dmanga_TRM_S_Tipo').combobox('getValue');
        trm_factor=$('#dmanga_TRM_S_Factor').textbox('getValue');
    } else {
        // use parent data and hide
        $('#dmanga_DistS').textbox('setValue',dist);
        $('#dmanga_ObstS').textbox('setValue',obst);
        $('#dmanga_SmallRow').css('display','none');
        $('#dmanga_SmallLbl').html(last);
        $('#dmanga_TRS_S_Tipo').combobox('setValue',trs_tipo);
        $('#dmanga_TRS_S_Factor').textbox('setValue',trs_factor);
        $('#dmanga_TRM_S_Tipo').combobox('setValue',trm_tipo);
        $('#dmanga_TRM_S_Factor').textbox('setValue',trm_factor);
    }

    // fourth row (tiny )
    if (data.T!=="") {
        // use own data and make it visible
        last=data.T;
        dist=$('#dmanga_DistT').textbox('getValue');
        obst=$('#dmanga_ObstT').textbox('getValue');
        $('#dmanga_TinyRow').css('display','table-row');
        $('#dmanga_TinyLbl').html(data.T);
        trs_tipo=$('#dmanga_TRS_T_Tipo').combobox('getValue');
        trs_factor=$('#dmanga_TRS_T_Factor').textbox('getValue');
        trm_tipo=$('#dmanga_TRM_T_Tipo').combobox('getValue');
        trm_factor=$('#dmanga_TRM_T_Factor').textbox('getValue');
    } else {
        // use parent data and hide
        $('#dmanga_DistT').textbox('setValue',dist);
        $('#dmanga_ObstT').textbox('setValue',obst);
        $('#dmanga_TinyRow').css('display','none');
        $('#dmanga_TinyLbl').html(last);
        $('#dmanga_TRS_T_Tipo').combobox('setValue',trs_tipo);
        $('#dmanga_TRS_T_Factor').textbox('setValue',trs_factor);
        $('#dmanga_TRM_T_Tipo').combobox('setValue',trm_tipo);
        $('#dmanga_TRM_T_Factor').textbox('setValue',trm_factor);
    }
    dmanga_evalTimeSpeed(); // reevaluate time/speed readonly input box
}

function dmanga_shareJuez() {
    $('#dmanga_Operation').val('sharejuez');
    $('#dmanga_Jornada').val(workingData.jornada);
    $('#dmanga_Manga').val(0); // not really needed, but...
    var frm = $('#competicion-formdatosmanga');
    $.ajax({
        type: 'GET',
        url: '../ajax/database/mangaFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.hasOwnProperty('errorMsg')){
            	$.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
            } else {// on submit success, reload results
    			var recorrido=$("input:radio[name=Recorrido]:checked").val();
    			$.messager.alert('<?php _e('Data exported'); ?>','<?php _e('Judge data exported to all rounds'); ?>','info');
    			workingData.datosManga.Recorrido=recorrido;
    			setupResultadosWindow(recorrido);
            }
        }
    });
}

/**
 * Guarda las modificaciones a los datos de una manga
 * Notese que esto no debería modificar ni los datos del
 * orden de salida ni resultados de la competicion
 * @param {int} id Identificador de la manga
 */
function save_manga(id) {

    function real_saveManga() {
        var frm = $('#competicion-formdatosmanga');
        $.ajax({
            type: 'GET',
            url: '../ajax/database/mangaFunctions.php',
            data: frm.serialize(),
            dataType: 'json',
            success: function (result) {
                if (result.hasOwnProperty('errorMsg')){
                    $.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
                } else {// on submit success, reload results
                    var recorrido=$("input:radio[name=Recorrido]:checked").val();
                    $.messager.alert('<?php _e('Data saved'); ?>','<?php _e('Data on current round stored'); ?>','info');
                    workingData.datosManga.Recorrido=recorrido;
                    // update tspeed value
                    dmanga_evalTimeSpeed();
                    // refresh result window if required
                    setupResultadosWindow(recorrido);
                }
            }
        });
    }

    // PENDING:
    // more elaborate checks are needed for parsing sct
    function check_dmanga(cat) {
        var dist = parseInt($('#dmanga_Dist' + cat).textbox('getValue'));
        var trst = parseInt($('#dmanga_TRS_' + cat + '_Tipo').combobox('getValue'));
        var trsf = parseInt($('#dmanga_TRS_' + cat + '_Factor').textbox('getValue'));
        var trm = parseInt($('#dmanga_TRM_' + cat + '_Factor').textbox('getValue'));
        if (dist === 0) missing = true;
        if (trm === 0) missing = true;
        if (trst === 0 && trsf <10) missing = true; // fixed sct and empty or invalid data
        if (trst === 6 && trsf >10) missing = true; // speed based sct and empty or invalid data
    }

    var missing=false;
    var rec=$("input:radio[name=Recorrido]:checked").val();
    var data=ac_fedInfo[workingData.federation]['InfoManga'][rec];// {object{L,M,S,T} } data
    $('#dmanga_Operation').val('update');
    $('#dmanga_Jornada').val(workingData.jornada);
    $('#dmanga_Manga').val(id);

    if (data['L']!=='') check_dmanga('L');
    if (data['M']!=='') check_dmanga('M');
    if (data['S']!=='') check_dmanga('S');
    if (data['T']!=='') check_dmanga('T');
    if (missing===false ) { real_saveManga(); return false; }

    // missing data found: warn user before continue
    var ok=$.messager.defaults.ok;
    var cancel=$.messager.defaults.cancel;
    $.messager.defaults.ok="<?php _e('Continue');?>";
    $.messager.defaults.cancel="<?php _e('Back');?>";
    $.messager.confirm({
        title:"<?php _e('Missing data');?>",
        msg:"<?php _e('Some category has missing or invalid values for');?><br/>"+
            "<?php _e('either distance, SCT or MCT');?><br/>"+
            "<?php _e('Continue');?>?",
        width:400,
        height:'auto',
        icon:'warning',
        fn: function(r) {
            // restore text
            $.messager.defaults.ok=ok;
            $.messager.defaults.cancel=cancel;
            if (r) real_saveManga();
        }
    });
    return false;
}

/**
 * Recarga los datos asociados a una manga
 * Restaura ventana de informacion, orden de salida y competicion
 * @param id identificador de la manga
 */
function reload_manga(id) {
	// ventana de datos
	var url='../ajax/database/mangaFunctions.php?Operation=getbyid&Jornada='+workingData.jornada+"&Manga="+id;
    // update judge list to prevent federation change
    $('#dmanga-Juez1').combogrid('load',{'Operation':'Enumerate','Federation':workingData.federation});
    $('#dmanga-Juez2').combogrid('load',{'Operation':'Enumerate','Federation':workingData.federation});
    $('#competicion-formdatosmanga').form('load',url); // notice that "onBeforeLoad is declared"
}

function open_clasificaciones() {
    var page="../console/frm_clasificaciones2.php";
    if (isJornadaEquipos(null)) page="../console/frm_clasificaciones_equipos.php";
    if (parseInt(workingData.datosJornada.Open)!==0) page="../console/frm_clasificaciones2.php";
    if (parseInt(workingData.datosJornada.KO)!==0) page="../console/resultados_ko.php";
    $('#competicion_info').panel('close');
    loadContents(page,'<?php _e('Results & Scores');?>');
    return false;
}

function proximityAlert() {
	var data=$('#ordensalida-datagrid').datagrid('getRows');
	var guias= [];
	var lista="<br />";
	for (var idx=0; idx < data.length; idx++) {
		var NombreGuia=data[idx].NombreGuia;
		// not yet declared: store perro and orden
		if ( !(NombreGuia in guias) ) {
			guias[NombreGuia] = { 'index': idx, 'perro': data[idx].Nombre }; 
			continue; 
		} 
		// already declared: eval distance
		var dist=idx-guias[NombreGuia].index;
		if (dist > parseInt(ac_config.proximity_alert)) {
			// declared but more than 5 dogs ahead. reset index and continue
			guias[NombreGuia] = { 'index': idx, 'perro': data[idx].Nombre }; 
			continue;
		}
		// arriving here means that a dog is closer than 5 steps from previous one from same guia.
		// store to notify later
		lista = lista + NombreGuia+": " +
				(1+guias[NombreGuia].index)+":" + guias[NombreGuia].perro +
				" ---  " + 
				(1+idx) +":" + data[idx].Nombre + 
				"<br />";
	}
	// arriving here means work done
	if (lista==="<br />") {
		$.messager.alert('<?php _e('OK'); ?>','<?php _e('There are no dogs from same handler close together'); ?>','info');
	} else {
		var w=$.messager.alert('<?php _e('Proximity alert'); ?>','<p>'+'<?php _e('List of handlers with dogs so close together'); ?>'+':</p><p>'+lista+'</p>','warning');
		w.window('resize',{width:350}).window('center');
	}
}

function reloadAndCheck() {
	proximityAlert();
}

// data {Index:idx Row:row } or null
function copyPasteOrdenSalida(data) {
    var dg=$('#ordensalida-datagrid');
    var row=dg.datagrid('getSelected');
    if (!row) {
        $.messager.alert('<?php _e("Error"); ?>','<?php _e("There is no item selected"); ?>',"warning");
        return;
    }
    var btn=$('#ordensalida-pasteBtn');
    if ( (typeof(data)==="undefined") || (data==null) ){ // paste requested
        data=dg.datagrid('options').clipboard;
        if (data==null) {
            $.messager.alert('<?php _e("Error"); ?>','<?php _e("There is no item selected"); ?>',"warning");
            return;
        }
        // check if move order is possible
        var from=":"+data.Row.Equipo+":"+data.Row.Categoria+":"+data.Row.Celo+":";
        var to=":"+row.Equipo+":"+row.Categoria+":"+row.Celo+":";
        if (isJornadaEqConjunta()) {
            // en jornadas por equipos conjunta, no hay que tener en cuenta ni categoria ni celo
            from=":"+data.Row.Equipo+":";
            to=":"+row.Equipo+":";
        }
        if (from!==to) {
            $.messager.alert('<?php _e("Error"); ?>','<?php _e("Cannot move to selected destination"); ?>',"warning");
            return;
        }
        // do ordensalida change
        dragAndDropOrdenSalida(data.Row.Perro,row.Perro,0,function(){
            // update ordensalida
            reloadOrdenSalida();
            // disable paste button and clear clipboard
            btn.linkbutton('disable');
            dg.datagrid('options').clipboard=null;
        });
    } else {
        // save data into clipboard and enable paste button
        dg.datagrid('options').clipboard=data;
        btn.linkbutton('enable');
        $.messager.show({
            title:'Move',
            msg:"<?php _e('Dog stored into clipboard');?> <br/> <?php _e('To move, select new position and press Move');?>",
            showType:'fade',
            timeout:3000,
            style:{ right:'', bottom:'' }
        });
    }
}

function reloadOrdenSalida() {
    if (workingData.jornada==0) return;
    if (workingData.manga==0) return;
    $('#ordensalida-datagrid').datagrid(
        'load',
        {
            Prueba: workingData.prueba,
            Jornada: workingData.jornada ,
            Manga: workingData.manga ,
            Categorias: $('#ordensalida-categoria').combobox('getValue'),
            Operation: 'getData'
        }
    );
}

function reloadOrdenEquipos() {
    if (workingData.jornada==0) return;
    if (workingData.manga==0) return;
    $('#ordenequipos-datagrid').datagrid(
        'load',
        {
            Prueba: workingData.prueba,
            Jornada: workingData.jornada ,
            Manga: workingData.manga ,
            Categorias: $('#ordensalida-categoria').combobox('getValue'),
            Operation: 'getTeams'
        }
    );
}

function reloadCompeticion() {
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
    var dg=$('#competicion-datagrid');
	// si hay alguna celda en edicion, ignorar
	if (dg.datagrid('options').editIndex!=-1) {
        $.messager.alert('<?php _e("Busy"); ?>','<?php _e("Cannot update: a cell is being edited"); ?>',"error");
        return;
    }
    dg.datagrid(
            'load',
            { 
            	Prueba: workingData.prueba ,
            	Jornada: workingData.jornada , 
            	Manga: workingData.manga , 
            	Operation: 'getData',
                Categorias: $('#competicion-categoria').combobox('getValue'),
            	TeamView: isTeam(workingData.datosManga.Tipo)?'true':'false'
            }
    );
}

function resetCompeticion() {
    if (workingData.jornada==0) return;
    if (workingData.manga==0) return;
    // si hay alguna celda en edicion, ignorar
    if ($('#competicion-datagrid').datagrid('options').editIndex!=-1) {
        $.messager.alert('<?php _e("Busy"); ?>','<?php _e("Cannot reset: a cell is being edited"); ?>',"error");
        return;
    }
    var msg='<?php _e('You will lost <strong>EVERY</strong> inserted results'); ?>'+'<br />'+
        '<?php _e('in selected categories on this round'); ?>'+'<br /><br />'+
        '<?php _e('Do you really want to continue?'); ?>';
    var w=$.messager.confirm('<?php _e("Erase results");?>', msg, function(r){
        if (!r) return;
        $.ajax({
            type: 'GET',
            url: '../ajax/database/resultadosFunctions.php',
            dataType: 'json',
            data: {
                Prueba: workingData.prueba,
                Jornada: workingData.jornada,
                Manga: workingData.manga,
                Categorias: $('#competicion-categoria').combobox('getValue'),
                Operation: 'reset'
            }
        }).done( function(result) {
            if (result.errorMsg) {
                $.messager.alert('<?php _e('Error'); ?>',result.errorMsg,'error');
            }
            reloadCompeticion();
        });
    });
    w.window('resize',{width:400}).window('center');
}

function swapMangas() {
    if (workingData.jornada==0) return;
    if (workingData.manga==0) return;
    // si hay alguna celda en edicion, ignorar
    if ($('#competicion-datagrid').datagrid('options').editIndex!=-1) {
        $.messager.alert('<?php _e("Busy"); ?>','<?php _e("Cannot swap: a cell is being edited"); ?>',"error");
        return;
    }
    var msg='<?php _e('This action will exchange <strong>EVERY</strong> inserted results'); ?> '+
        '<?php _e('in selected categories with the Agility/Jumping counterpart of this round'); ?>'+'<br /><br />'+
        '<?php _e('Do you really want to continue?'); ?>';
    var w=$.messager.confirm('<?php _e("Swaps results");?>', msg, function(r){
        if (!r) return;
        $.messager.progress({text:"<?php _e('Working');?>..."});
        $.ajax({
            type:'GET',
            url: '../ajax/database/resultadosFunctions.php',
            dataType:'json',
            data: {
                Prueba: workingData.prueba,
                Jornada: workingData.jornada,
                Manga: workingData.manga,
                Categorias: $('#competicion-categoria').combobox('getValue'),
                Operation: 'swap'
            }
        }).done( function(msg) {
            $.messager.progress('close');
            reloadCompeticion();
        });
    });
    w.window('resize',{width:450}).window('center');
}

var autoUpdateID=null;

function autoUpdateCompeticion() {
	var enabled=$('#competicion-autoUpdateBtn').prop('checked');
	if (enabled) {
		if (autoUpdateID!==null) return; // already activated
		autoUpdateID=setInterval(function(){reloadCompeticion();}, 10000);
	} else {
		if (autoUpdateID==null) return; // already deactivated
		clearInterval(autoUpdateID);
		autoUpdateID=null;
	}
}

// search and edit the row matching specified dorsal
function competicionSelectByDorsal() {
    var dg = $('#competicion-datagrid');
    var drs = $('#competicion-search');
    // store and reset seach field
    var toLookFor=drs.val();
    drs.val("--- Search ---");
    drs.blur();// remove focus to hide tooltip
    if (isNaN(toLookFor)) { // value is not a number: search for dog name
        if (toLookFor==="--- Search ---") return false; // nothing to search
        dg.datagrid('options').idField="Nombre";
    } else {
        toLookFor=parseInt(toLookFor);
        if (toLookFor<=0) return false; // invalid dorsal to search for
        dg.datagrid('options').idField="Dorsal";
    }
    var idx = dg.datagrid('getRowIndex', toLookFor);
    if (idx < 0) {
        $.messager.alert('<?php _e("Not found"); ?>', '<?php _e("Cannot find dog with dorsal/name"); ?>'+" "+toLookFor, "error");
        return false;
    }
    dg.datagrid('scrollTo', {
        index: idx,
        callback: function (index) {
            dg.datagrid('selectRow', index);
            // enter focus into datagrid to allow key binding
            dg.datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
        }
    });
    return false;
}

/**
 * Key bindings para uso de teclas en el dialogo de entrada de datos
 * @param evt Key Event
 */

function competicionKeyEventHandler(evt) {

	// up & down keys
    function selectRow(t,up){
    	var count = t.datagrid('getRows').length;    // row count
    	var selected = t.datagrid('getSelected');
    	var res=true;
    	if (selected){
        	var index = t.datagrid('getRowIndex', selected);
        	index = index + (up ? -1 : 1);
        	if (index < 0) { res=false; index = 0;}
        	if (index >= count) { res=false; index = count - 1; }
        	t.datagrid('clearSelections');
        	t.datagrid('selectRow', index);
    	} else {
        	t.datagrid('selectRow', (up ? count-1 : 0));
    	}
    	return res; // tell caller if index overflows
	}

	function editRow(t) {
		var selected = t.datagrid('getSelected');
		if(!selected) return;
		if(selected.Dorsal==="*") return; // not editable row
		var index = t.datagrid('getRowIndex', selected);
        t.datagrid('beginEdit',index);
		var ed = $(t).datagrid('getEditor', {index:index,field:'Faltas'});
		// mark as selected contents on first field
		var input=$(ed.target).next().find('input');
		input.focus();
		input.select();
	}
	
	var dg=$('#competicion-datagrid');
    var searchbox=$('#competicion-search');
	var editIndex=dg.datagrid('options').editIndex; // added by me
	if (editIndex==-1) { // not editing
		switch (evt.keyCode) {
        case 38:	/* Up */	 
            selectRow(dg,true); 
            return false;
        case 40:    /* Down */	 
            selectRow(dg,false); 
            return false;
        case 13:	/* Enter */  
        	if (evt.ctrlKey || evt.metaKey) { displayRowData(dg); return false; }
            if (! searchbox.is(':focus') ) { editRow(dg); return false; }
            return true; // to allow parsing searchByDorsal textbox
        case 27:	/* Esc */
            // disable autorefresh if any
            $('#competicion-autoUpdateBtn').prop('checked',false);
            autoUpdateCompeticion(); // fire event 
            // and close window  	 
            $('#competicion-dialog').window('close');
            return false;
		}
	} else { //on edit
		switch (evt.keyCode) {
        case 13:	/* Enter */
        	// save data
        	dg.datagrid('endEdit', editIndex );
        	var data=dg.datagrid('getRows')[editIndex];
        	data.Pendiente=0;
            if (isJornadaKO()) data.Games=1; // trick to mark dog is competing in this ko round
        	saveCompeticionData(editIndex,data);
        	// and open edition on next row
        	dg.datagrid('selectRow', editIndex); // previous focus is lost
        	var res=selectRow(dg,false); // move down one row
        	if (res) editRow(dg); // and edit except if we already was at bottom
        	return false;
        case 27:	/* Esc */ 
            dg.datagrid('cancelEdit', editIndex);	
            return false;
		}
	}
	return true; // to allow follow key binding chain
}
/**
 * Evaluate puesto for given perro
 * Notice that the dog data for current round is not yet stored
 * @param {object} datos (idPerro, penalization on current round)
 * @param {function} callback(resultado,puesto)
 * @param {boolean} final evaluate partial or final position
 * @returns {boolean}
 */
function __getPuesto(datos,callback,final) {
    var mode=getMangaMode(workingData.datosPrueba.RSCE,workingData.datosManga.Recorrido,datos.Categoria);
    if (mode==-1) {
        $.messager.alert('<?php _e('Error'); ?>','<?php _e('Internal error: invalid Federation/Course/Category combination'); ?>','error');
        return false;
    }
    var url=(final)? '../ajax/database/clasificacionesFunctions.php' : '../ajax/database/resultadosFunctions.php';
    var param= {
        Operation:	'getPuesto',
        Prueba:		workingData.prueba,
        Jornada:	workingData.jornada,
        Manga:		workingData.manga,
        Mode:       mode
    };
    // console.log("perro:"+idperro+" categoria:"+datos.Categoria+" mode:"+mode);
    $.ajax({
        type:'GET',
        url: url,
        dataType:'json',
        data: $.extend({},param,datos),
        success: function(result) {
            if (result.errorMsg) {
                $.messager.alert('<?php _e('Error'); ?>',result.errorMsg,'error');
                return false;
            }
            if (result.success==true) {
                callback(datos,result);
                return false;
            }
        }
    });
    return false;
}

/**
 * Evaluate puesto for given perro in current round
 * @param {object} datos Perro,Faltas,Tocados,Rehuses,Eliminado,NoPresentado,Tiempo
 * @param {function} callback(resultado,puesto)
 * @returns {boolean}
 */
function getPuestoFinal(datos,callback) { return __getPuesto(datos,callback,true); }
function getPuestoParcial(datos,callback) { return __getPuesto(datos,callback,false); }

/**
 * Inicializa ventana de resultados ajustando textos segun federacion/recorrido
 * Borra datagrid previa
 * @param recorrido 0:L/M/S/T 1:L/M+S(3Heights) or LM/ST(4Heights)  2:/L+M+S+T
 */
function setupResultadosWindow(recorrido) {
	var fed= parseInt(workingData.datosPrueba.RSCE);
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
    if (typeof (ac_fedInfo[fed])==="undefined") return;
    // marcamos los botones de seleccion de categoria como desconectados
    $('#resultadosmanga-LargeBtn').prop('checked',false);
    $('#resultadosmanga-MediumBtn').prop('checked',false);
    $('#resultadosmanga-SmallBtn').prop('checked',false);
    $('#resultadosmanga-TinyBtn').prop('checked',false);
    // cargamos ventana inicial de resultados con una pantalla vacia
    if (isJornadaEquipos(null)) $('#parciales_equipos-datagrid').datagrid('loadData',{total:0, rows:{}});
    else $('#parciales_individual-datagrid').datagrid('loadData',{total:0, rows:{}});
    // actualizamos la informacion del panel de informacion de trs/trm
    var infomanga=ac_fedInfo[fed].InfoManga[parseInt(recorrido)];
    // visibilidad
    $('#resultadosmanga-LargeRow').css('display',(infomanga['L']==="")?'none':'table-row');
    $('#resultadosmanga-MediumRow').css('display',(infomanga['M']==="")?'none':'table-row');
    $('#resultadosmanga-SmallRow').css('display',(infomanga['S']==="")?'none':'table-row');
    $('#resultadosmanga-TinyRow').css('display',(infomanga['T']==="")?'none':'table-row');
    // textos
    $('#resultadosmanga-LargeLbl').html(infomanga['L']);
    $('#resultadosmanga-MediumLbl').html(infomanga['M']);
    $('#resultadosmanga-SmallLbl').html(infomanga['S']);
    $('#resultadosmanga-TinyLbl').html(infomanga['T']);
}

function saveCompeticionData(idx,data) {

    function sendEvent(evtdata) {
        var obj= {
            'Operation':'putEvent',
            'Type': 	'aceptar',
            'TimeStamp': Math.floor(Date.now() / 1000),
            'Source':	'Console',
            'Session':	1, // Session id for console events
            'Prueba':	workingData.prueba,
            'Jornada':	workingData.jornada,
            'Manga':	workingData.manga,
            'Tanda':	workingData.tanda,
            'Perro':	evtdata['Perro'],
            'Dorsal':	evtdata['Dorsal'],
            'Equipo':	evtdata['Equipo'],
            'Celo':		evtdata['Celo'],
            'Value':	evtdata['Value'],
            'Licencia':	evtdata['Licencia'],
            'Nombre':		evtdata['Nombre'],
            'NombreGuia':	evtdata['NombreGuia'],
            'NombreClub':	evtdata['NombreClub'],
            'Categoria':	evtdata['Categoria'],
            'Tocados':	    evtdata['Tocados'],
            'Faltas':		evtdata['Faltas'],
            'Rehuses':	    evtdata['Rehuses'],
            'Tiempo':		evtdata['Tiempo'],
            'Eliminado':	evtdata['Eliminado'],
            'NoPresentado':	evtdata['NoPresentado'],
            'Observaciones':data['Observaciones'],
            'Pendiente':    evtdata['Pendiente'],
            'Games':        evtdata['Games']
        };
        // send "update" event to every session listeners
        $.ajax({
            type: 'GET',
            url: '../ajax/database/eventFunctions.php',
            dataType: 'json',
            data: $.extend({},obj,data)
        });
    }

	$.ajax({
		type: 'GET',
		url: '../ajax/database/resultadosFunctions.php',
		dataType: 'json',
		data: {
			Operation:	'update',
			Prueba:		workingData.prueba,
			Jornada:	workingData.jornada,
			Manga:		workingData.manga,
			Dorsal: 	data['Dorsal'],
			Perro: 		data['Perro'],
			Licencia:	data['Licencia'],
			Nombre:		data['Nombre'],
			NombreGuia:	data['NombreGuia'],
			NombreClub:	data['NombreClub'],
			Categoria:	data['Categoria'],
			Tocados:	data['Tocados'],
			Faltas:		data['Faltas'],
			Rehuses:	data['Rehuses'],
			Tiempo:		data['Tiempo'],
			Eliminado:	data['Eliminado'],
			NoPresentado:	data['NoPresentado'],
			Observaciones:	data['Observaciones'],
			Pendiente: data['Pendiente'],
            Games: data['Games']
		},
		success: function(dat) {

		    // generate an event to track console modifications
            sendEvent(data);
			if (dat.Manga!=workingData.manga) return; // window changed
			$('#competicion-datagrid').datagrid('updateRow',{index: idx,row: dat});
			$('#lnkb1_'+idx).linkbutton();
			$('#lnkb2_'+idx).linkbutton();

			// increase nextToBackup counter and fire autobackup on limit
            var bd=parseInt(ac_config.backup_dogs)
            if (bd==0) return;
            ac_config.dogs_before_backup++;
            if (ac_config.dogs_before_backup>=ac_config.backup_dogs) autoBackupDatabase(1,"");
		}
	});

}

// genera un nuevo orden aleatorio
function evalOrdenSalida(oper) {
	if (workingData.prueba==0) return;
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
	if (oper==='random') {
		$.messager.confirm('<?php _e('Confirm'); ?>', '<?php _e('Every changes done till now will be lost<br />Continue?'); ?>', function(r){
			if (!r) return;
			$.ajax({
				type: 'GET',
				url: '../ajax/database/ordenSalidaFunctions.php',
				dataType: 'json',
				data: {
					Prueba: workingData.prueba,
					Jornada: workingData.jornada,
					Manga: workingData.manga,
                    Categorias: $('#ordensalida-categoria').combobox('getValue'),
					Operation: 'setOrder',
                    SortMethod: oper,
                    Range: $('#ordensalida-rango').textbox('getValue')
				}
			}).done( function(result) {
			    if (result.errorMsg){
			        $.messager.show({title:"Error",msg:result.errorMsg,timeout:5000,showType:'slide'})
                }
				reloadOrdenSalida();
			});
		});
	} else if (oper==="excel") {
        check_permissions(access_perms.ENABLE_IMPORT, function (res) {
            if (res.errorMsg) {
                $.messager.alert('License error','<?php _e("Current license has no Excel import function enabled"); ?>', "error");
            } else {
                $('#ordensalida-excel-dialog').dialog('open');
            }
            return false; // prevent default fireup of event trigger
        });
    } else { // 'reverse', 'clone', 'results', 'alpha','dorsal'
		$.ajax({
			type: 'GET',
			url: '../ajax/database/ordenSalidaFunctions.php',
			dataType: 'json',
			data: {
				Prueba: workingData.prueba,
				Jornada: workingData.jornada,
				Manga: workingData.manga,
                Categorias: $('#ordensalida-categoria').combobox('getValue'),
				Operation: 'setOrder',
                SortMethod: oper,
                Range: $('#ordensalida-rango').textbox('getValue')
			}
		}).done( function(result) {
            if (result.errorMsg){
                $.messager.show({title:"Error",msg:result.errorMsg,timeout:5000,showType:'slide'})
            }
			reloadOrdenSalida();
		});
	}
}

// reajusta el orden de salida 
// poniendo el idperro "from" delante (where==0) o detras (where==1) del idperro "to"
// al retornar la funcion se invoca whenDone, que normalmente recargara el formulario padre
function dragAndDropOrdenSalida(from,to,where,whenDone) {
	if (workingData.prueba==0) return;
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
	$.ajax({
		type: 'GET',
		url: '../ajax/database/ordenSalidaFunctions.php',
		dataType: 'json',
		data: {	
			Operation: 'dnd',
            Prueba: workingData.prueba,
            Jornada: workingData.jornada,
            Manga: workingData.manga,
            From: from,
            To: to,Where: where
		}
	}).done( function(result) {
        if (result.errorMsg){
            $.messager.show({title:"Error",msg:result.errorMsg,timeout:5000,showType:'slide'})
        }
		whenDone();
	});
}

//reajusta el programa de la jornada
//poniendo la tanda "from" delante (where==0) o detras (where==1) de la tanda "to"
function dragAndDropOrdenTandas(from,to,where) {
	if (workingData.prueba==0) return;
	if (workingData.jornada==0) return;
	$.ajax({
		type: 'GET',
		url: '../ajax/database/tandasFunctions.php',
		dataType: 'json',
		data: {	
			Operation: 'dnd', 
			Prueba: workingData.prueba, 
			Jornada: workingData.jornada, 
			From: from,
			To: to,
			Where: where
		},
		success: function (result) {
				if (result.errorMsg){ 
					$.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
				}
				reloadOrdenTandas();
		}
	});
}

/**
 * Abre la ventana de competicion requerida 'ordensalida','competicion','resultadosmanga'
 * @param name
 */
function competicionDialog(name) {
	// obtenemos datos de la manga seleccionada
	var row= $('#competicion-listamangas').datagrid('getSelected');
    var required=true;
    if (name=='ordentandas') required=false;
    if (name=='entrenamientos') required=false;
    if (!row && required) {
    	$.messager.alert('<?php _e('Error'); ?>','<?php _e('There is no round selected'); ?>','info');
    	return; // no hay ninguna manga seleccionada. retornar
    }
    var title = workingData.nombrePrueba + ' -- ' + workingData.nombreJornada;
    $('#entrenamientos-dialog').dialog('close');
    $('#ordentandas-dialog').dialog('close');
    $('#ordensalida-dialog').dialog('close');
    $('#competicion-dialog').dialog('close');
    $('#resultadosmanga-dialog').dialog('close');
    if (name==='entrenamientos') {
        check_permissions(access_perms.ENABLE_TRAINING,function(res) {
            if (res.errorMsg) {
                $.messager.alert('License error','<?php _e("This license has no permission to handle training sessions"); ?>',"error");
            } else {
                // abrimos ventana de dialogo
                $('#entrenamientos-dialog').dialog('open').dialog('setTitle',' <?php _e("Training sesion"); ?>'+": "+workingData.nombrePrueba);
                // cargamos ventana de orden de salida
                reloadEntrenamientos();
            }
            return false; // prevent default fireup of event trigger
        });
    }
    if (name==='ordentandas') {
        // abrimos ventana de dialogo
        $('#ordentandas-dialog').dialog('open').dialog('setTitle',' <?php _e("Timetable"); ?>'+": "+title);
        // cargamos ventana de orden de salida
        reloadOrdenTandas();
    }
    title = workingData.nombrePrueba + ' -- ' + workingData.nombreJornada + ' -- ' + workingData.nombreManga;
    if (name==='ordensalida') {
        var display= (isJornadaEqMejores() || isJornadaEqConjunta())?"inline-block":"none";
        $('#ordensalida-dialog').dialog('open').dialog('setTitle',' <?php _e("Starting order"); ?>'+": "+title);
        $('#ordensalida-eqBtn').css('display',display);
        // cargamos ventana de orden de salida
        reloadOrdenSalida();
    }
    if (name==='competicion') {
        // disable "data entry button" on closed journeys
        if (parseInt(workingData.datosJornada.Cerrada)!==0){
            $.messager.show({
                width:350,
                height:100,
                timeout:2000,
                title: '<?php _e('Error'); ?>',
                msg: '<?php _e("Cannot edit/modify results on closed journey");?>'
            });
            return false;
        }
        // abrimos ventana de dialogo
        $('#competicion-dialog').dialog('open').dialog('setTitle',' <?php _e("Data entry"); ?>'+": "+title);
        // cargamos ventana de entrada de datos
        reloadCompeticion();
    }
    if (name==='resultadosmanga') {
        // abrimos ventana de dialogo
        $('#resultadosmanga-dialog').dialog('open').dialog('setTitle',' <?php _e("Round results"); ?>'+": "+title);
        // iniciamls ventana de presentacion de resultados parciales acorde a la federacion y el recorrido
        setupResultadosWindow(row.Recorrido);
        // marcamos la primera opcion como seleccionada
        $('#resultadosmanga-LargeBtn').prop('checked','checked');
        // refrescamos datos de TRS y TRM
        if (howManyHeights(workingData.datosPrueba.RSCE)==4) consoleReloadParcial(3,false);
        consoleReloadParcial(2,false);
        consoleReloadParcial(1,false);
        consoleReloadParcial(0,true); // pintamos el datagrid con los datos de categoria "large"
    }
}

/************************************* funciones para la ventana de clasificaciones **************************/

/**
 * rellena los diversos formularios de informacion de resultados
 * resultados: almacen de resultados (array[mode][manga]
 * idmanga: Manga ID
 * idxmanga: 1..2 manga index
 * mode: 0..8
 */
function resultados_fillForm(resultados,idmanga,idxmanga,mode) {
    if (mode<0) return; // invalid mode. do not parse
	$.ajax({
		type: 'GET',
		url: '../ajax/database/resultadosFunctions.php',
		dataType: 'json',
		data: {	Operation:'getResultadosIndividual', Prueba:workingData.prueba, Jornada:workingData.jornada, Manga:idmanga, Mode: mode },
		success: function(dat) {
			var suffix='L';
			switch (mode) {
			case 0: case 4: case 6: case 8: suffix='L'; break;
			case 1: case 3: suffix='M'; break;
			case 2: case 7: suffix='S'; break;
			case 5: suffix='T'; break;
			}
			$('#dm'+idxmanga+'_Nombre').textbox('setValue',dat['manga'].TipoManga);
			$('#dm'+idxmanga+'_Lbl_'+suffix).html(ac_fedInfo[workingData.federation].IndexedModes[mode]);
			$('#dm'+idxmanga+'_DIST_'+suffix).textbox('setValue',dat['trs'].dist);
			$('#dm'+idxmanga+'_OBST_'+suffix).textbox('setValue',dat['trs'].obst);
			$('#dm'+idxmanga+'_TRS_'+suffix).textbox('setValue',dat['trs'].trs);
			$('#dm'+idxmanga+'_TRM_'+suffix).textbox('setValue',dat['trs'].trm);
            var vel=(''+dat['trs'].vel).replace('&asymp;','\u2248');
			$('#dm'+idxmanga+'_VEL_'+suffix).textbox('setValue',vel);
			// store manga results
			if (typeof resultados[mode] === "undefined") resultados[mode]=[];
			resultados[mode][idxmanga]=dat['rows'];
			// alert(JSON.stringify(resultados[mode]));
		}
	});
}

/**
 * rellena la ventana de informacion con los datos definitivos de cada manga de la ronda seleccionada
 */
function resultados_doSelectRonda(row) {

    function populate_clasificacion() {
        // fase 3 rellenar datos de la clasificacion.
        // como puede haber una carga diferida del datagrid en el caso de mangas multiples
        // se pone esto como una inner funcion que puede se ejecutada "en directo" o desde un
        // ajax response
        mode=$('#resultados-selectCategoria').combobox('getValue');
        $.ajax({
            type: 'GET',
            url: '../ajax/database/clasificacionesFunctions.php',
            dataType: 'json',
            data: {
                Operation: (isJornadaEquipos(null))? 'clasificacionEquipos' : 'clasificacionIndividual',
                Prueba:workingData.prueba,
                Jornada:workingData.jornada,
                Federation:fed,
                Manga1:row.Manga1,
                Manga2:row.Manga2,
                Manga3:row.Manga3,
                Manga4:row.Manga4,
                Manga5:row.Manga5,
                Manga6:row.Manga6,
                Manga7:row.Manga7,
                Manga8:row.Manga8,
                Rondas: row.Rondas,
                Mode: mode
            },
            success: function(dat) {
                if ( isJornadaEquipos(null) ) {
                    // las rondas por equipos siempre tienen dos mangas
                    $('#finales_equipos_roundname_m1').text(row.Manga1.Nombre);
                    $('#finales_equipos_roundname_m2').text(row.Manga2.Nombre);
                    workingData.individual=dat.individual;
                    $('#finales_equipos-datagrid').datagrid('loadData',dat.equipos);
                } else {
                    // en las mangas que esten definidas, ajusta el nombre
                    for (nmanga=1;nmanga<9;nmanga++) {
                        if (row['Manga'+nmanga]<=0) continue;
                        $('#finales_individual_roundname_m'+nmanga).text(row['Manga'+nmanga].Nombre);
                    }
                    workingData.individual=dat.rows;
                    $('#finales_individual-datagrid').datagrid('loadData',dat.rows);
                }
            }
        });
    }


    var resultados=[];

    // FASE 1 Ajustamos en funcion del tipo de recorrido lo que debemos ver en las mangas
    var fed= parseInt(workingData.datosPrueba.RSCE);
    if (workingData.jornada==0) {
        $.messager.alert('<?php _e("Error"); ?>','<?php _e("No defined journey"); ?>: '+fed,"error");
        return;
    }
    if (typeof (ac_fedInfo[fed])==="undefined") {
        $.messager.alert('<?php _e("Error"); ?>','<?php _e("Requested federation module is not installed"); ?>: '+fed,"error");
        return;
    }
    // Recordatorio: ambas mangas tienen siempre el mismo tipo de recorrido
    var fedinfo=ac_fedInfo[fed];
    var rec=parseInt(row.Recorrido1);
    var infomanga=fedinfo.InfoManga[rec];

    // contenido del combobox de seleccion de ronda
    // un poco tricky: hay que buscar todos los modos del recorrido,
    // anyadir los textos, y evitar duplicados. pero bueno....
    var rondas=[];
    var last=-1;
    var count=0;
    for (var n=0; n<4; n++) {
        var mode=fedinfo.Modes[rec][n];
        if (mode < 0) continue;
        if (mode==last) continue;
        last=mode;
        rondas[count]={'mode':mode,'text':fedinfo.ModeStrings[rec][n]};
        count++;
    }
    $('#resultados-selectCategoria').combobox('loadData',rondas);

    for (nmanga=1;nmanga<9;nmanga++) {
        if (row['Manga'+nmanga]<=0) {
            // la manga no existe: oculta informacion de dicha manga
            $('#datos_manga'+nmanga+'-InfoRow').css('display','none');
            $('#datos_manga'+nmanga+'-LargeRow').css('display','none');
            $('#datos_manga'+nmanga+'-MediumRow').css('display','none');
            $('#datos_manga'+nmanga+'-SmallRow').css('display','none');
            $('#datos_manga'+nmanga+'-TinyRow').css('display','none');
            $('#datos_manga'+nmanga+'-Separator').css('display','none');
        } else {
            // la manga existe: ajusta visibilidad de dicha manga
            $('#datos_manga'+nmanga+'-InfoRow').css('display','table-row');
            $('#datos_manga'+nmanga+'-LargeRow').css('display',(infomanga['L']==="")?'none':'table-row');
            $('#datos_manga'+nmanga+'-MediumRow').css('display',(infomanga['M']==="")?'none':'table-row');
            $('#datos_manga'+nmanga+'-SmallRow').css('display',(infomanga['S']==="")?'none':'table-row');
            $('#datos_manga'+nmanga+'-TinyRow').css('display',(infomanga['T']==="")?'none':'table-row');
            $('#datos_manga'+nmanga+'-Separator').css('display','table-row');
            // indica los datos de los jueces de dicha manga
            $('#dm'+nmanga+'_Juez1').textbox('setValue',row['Juez'+nmanga+'1']);
            $('#dm'+nmanga+'_Juez2').textbox('setValue',row['Juez'+nmanga+'2']);
            // datos evaluados de TRS y TRM de segunda manga
            // si fedinfo.Modes[rec][i] resultados_fillForm no hace nada
            resultados_fillForm(resultados,row['Manga'+nmanga],''+nmanga,fedinfo.Modes[rec][0]);
            resultados_fillForm(resultados,row['Manga'+nmanga],''+nmanga,fedinfo.Modes[rec][1]);
            resultados_fillForm(resultados,row['Manga'+nmanga],''+nmanga,fedinfo.Modes[rec][2]);
            resultados_fillForm(resultados,row['Manga'+nmanga],''+nmanga,fedinfo.Modes[rec][3]);
        }
    }

    // FASE 2: si estamos en individual, descargamos el datagrid con el numero de mangas apropiado
    if (isJornadaEquipos(null)) {
        $('#resultados-toolbar').css('display','inline-block');
        populate_clasificacion();
    } else {
        var page="";
        if ( (parseInt(workingData.datosJornada.Games)!==0) && parseInt(workingData.datosCompeticion.ModuleID)===3) {
            page="../lib/templates/final_games.inc.php";
        } else {
            var nmangas=0;
            for(n=8;n>0;n--) if (row['Manga'+n]!=0) {nmangas=n; break } // numero de mangas
            page="../lib/templates/final_individual.inc.php?NumMangas="+nmangas;
        }
        $('#resultados-data').load(page,
            function() {
                // anyadimos toolbar y keyhandler al datagrid de clasificaciones
                // $('#finales_individual-datagrid').datagrid({toolbar: '#resultados-toolbar'});
                $('#resultados-toolbar').css('display','inline-block');
                addSimpleKeyHandler('#finales_individual-datagrid',"");
                populate_clasificacion();
            });
    }
}

function verifyCompose(data,manga,nombre) {
	var str='<?php _e("<strong>These dogs are pending to provide course data in round"); ?> '+manga+" ( "+nombre+" )</strong>";
	str +="<table><tr><th><?php _e('Dorsal'); ?></th><th><?php _e('Dog');?></th><th><?php _e('Handler');?></th><th><?php _e('Club');?></th></tr>";
	// componemos mensaje de error
	$.each(
		data['rows'],
		function(index,val) {
			str+="<tr><td>"+val['Dorsal']+"</td><td>"+val['Nombre']+"</td><td>"+val['NombreGuia']+"</td><td>"+val['NombreClub']+"</td></tr>";
		}
	);
	str+="</table><br />";
	return str;
}

function verifyClasificaciones() {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var mode=$('#resultados-selectCategoria').combobox('getValue');
	var str1="";
    var str2="";
    var str3="";
	if (ronda==null) {
    	$.messager.alert('<?php _e("Error"); ?>','<?php _e("There is no selected round for this journey"); ?>',"warning");
    	return false; // no way to know which ronda is selected
	}
	// verificamos manga 1
	$.ajax({
		type: 'GET',
		url: '../ajax/database/resultadosFunctions.php',
		dataType: 'json',
		data: {	
			Operation:	'getPendientes', 
			Prueba:	workingData.prueba, 
			Jornada:workingData.jornada, 
			Manga:ronda.Manga1, 
			Mode: mode 
		},
		success: function(data) {
			if (parseInt(data['total'])!=0) str1=verifyCompose(data,ronda.Manga1,ronda.NombreManga1);
			if (ronda.Manga2==0) {
				// no hay segunda manga
				if (str1==="") {
					$.messager.alert('<?php _e("Verify OK"); ?>','<?php _e("No dogs found without their course results"); ?>',"info");
				} else {
					var w=$.messager.alert("Verify Error",str1,"error");
					w.window('resize',{width:600}).window('center');
				}
				return false; // prevent default fireup of event trigger
			}
			// verificamos manga 2
			$.ajax({
				type: 'GET',
				url: '../ajax/database/resultadosFunctions.php',
				dataType: 'json',
				data: {	
					Operation:	'getPendientes', 
					Prueba:	workingData.prueba, 
					Jornada:workingData.jornada, 
					Manga:ronda.Manga2, 
					Mode: mode 
				},
				success: function(data) {
					if (parseInt(data['total'])!=0) str2=verifyCompose(data,ronda.Manga2,ronda.NombreManga2);
					if (ronda.Manga3==0) {
					    // no hay tercera manga
                        if (str1==="" && str2==="") {
                            $.messager.alert('<?php _e("Verify OK"); ?>','<?php _e("No dogs found without their course results"); ?>',"info");
                        } else {
                            var w=$.messager.alert('<?php _e("Verify Error"); ?>',str1+str2,"error");
                            w.window('resize',{width:600}).window('center');
                        }
                        return false; // prevent default fireup of event trigger
                    }
                    // verificamos manga 3
                    $.ajax({
                        type: 'GET',
                        url: '../ajax/database/resultadosFunctions.php',
                        dataType: 'json',
                        data: {
                            Operation: 'getPendientes',
                            Prueba: workingData.prueba,
                            Jornada: workingData.jornada,
                            Manga: ronda.Manga3,
                            Mode: mode
                        },
                        success: function (data) {
                            if (parseInt(data['total']) != 0) str3 = verifyCompose(data, ronda.Manga3, ronda.NombreManga3);
                            if (str1==="" && str2==="" && str3==="") {
                                $.messager.alert('<?php _e("Verify OK"); ?>', '<?php _e("No dogs found without their course results"); ?>', "info");
                            } else {
                                var w = $.messager.alert('<?php _e("Verify Error"); ?>', str1 + str2 + str3, "error");
                                w.window('resize', {width: 600}).window('center');
                            }
                            return false; // prevent default fireup of event trigger
                        }
                    });
				}
			});
		}
	});
    return false; //this is critical to stop the click event which will trigger a normal file download!	
}

function reloadClasificaciones() {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	if (ronda==null) {
    	$.messager.alert('<?php _e("Error"); ?>','<?php _e("There is no selected round for this journey"); ?>',"warning");
    	return; // no way to know which ronda is selected
	}
	// obtenemos el modo activo
	var mode=$('#resultados-selectCategoria').combobox('getValue');
	// calculamos y recargamos tabla de clasificaciones
	$.ajax({
		type: 'GET',
		url: '../ajax/database/clasificacionesFunctions.php',
		dataType: 'json',
		data: {
            Operation: (isJornadaEquipos(null))? 'clasificacionEquipos' : 'clasificacionIndividual',
			Prueba:workingData.prueba,
			Jornada:workingData.jornada,
			Manga1:ronda.Manga1,
            Manga2:ronda.Manga2,
            Manga3:ronda.Manga3,
            Manga4:ronda.Manga4,
            Manga5:ronda.Manga5,
            Manga6:ronda.Manga6,
            Manga7:ronda.Manga7,
            Manga8:ronda.Manga8,
			Rondas: ronda.Rondas,
			Mode: mode
		},
		success: function(dat) {
		    var mangas= [1,2,3,4,5,6,7,8];
            if ( isJornadaEquipos(null) ) {
                // las rondas por equipos siempre tienen solo dos mangas
                $('#finales_equipos_roundname_m1').text(ronda.NombreManga1);
                $('#finales_equipos_roundname_m2').text(ronda.NombreManga2);
                workingData.individual=dat.individual;
                $('#finales_equipos-datagrid').datagrid('loadData',dat.equipos);
            } else {
                if (isJornadaGames()){
                    // en games las mangas dependen del tipo de competicion
                    switch (workingData.datosCompeticion.ModuleID) {
                        case 1: mangas=[1,2,3,4,5]; break; // penthatlon
                        case 2: mangas=[1,2,3,4]; break;// biathlon
                        case 3: mangas=[1,2]; break;// games
                        default: // should not happen. default to "standard" round
                            console.log("invalid module ID: "+workingData.datosCompeticion.ModuleID+" on Games journey");
                            break;
                    }
                }
                // now iterate on valid rounds to compose final scores
                mangas.forEach(function(value,index,source){
                    if (ronda['Manga'+value]<=0) return;
                    $('#finales_individual_roundname_m'+value).text(ronda['NombreManga'+value]);
                });
                // and populate table
                workingData.individual=dat.rows;
                $('#finales_individual-datagrid').datagrid('loadData',dat.rows)
            }
		}
	});
}
