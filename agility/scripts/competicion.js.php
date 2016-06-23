/*
competicion.js

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
 * Funciones relacionadas con la ventana de competicion
 */

/************************** Gestion de datos de la ventana de manga activa */

/**
 * indica si la manga es agility o jumping
 */
function isAgility(tanda) {
    switch(Number(tanda)) {
        case	0	: /* default */ return true;
			// en pre-agility no hay categorias
        case    1	:/* Pre-Agility 1'*/ return true;
        case    2	:/* Pre-Agility 2'*/ return false; // second round
        case    3	:/* Agility-1 GI Large'*/ return true;
        case    4	:/* Agility-1 GI Medium */ return true;
        case    5	:/* Agility-1 GI Small'*/ return true;
        case    6	:/* Agility-2 GI Large'*/ return true;
        case    7	:/* Agility-2 GI Medium'*/ return true;
        case    8	:/* Agility-2 GI Small'*/ return true;
        case    9	:/* Agility GII Large*/ return true;
        case    10	:/* Agility GII Medium*/ return true;
        case    11	:/* Agility GII Small*/ return true;
        case    12	:/* Agility GIII Large*/ return true;
        case    13	:/* Agility GIII Medium*/ return true;
        case  	14	:/* Agility GIII Small*/ return true;
        case  	15	:/* Agility Large*/ return true;
        case  	16	:/* Agility Medium'*/ return true;
        case  	17	:/* Agility Small*/ return true;
        case  	18	:/* Agility Eq. 3 Large*/ return true;
        case  	19	:/* Agility Eq. 3 Medium*/ return true;
        case  	20	:/* Agility Eq. 3 Small*/ return true;
            // en jornadas por equipos conjunta tres alturas se mezclan categorias M y S
        case  	21	:/* Ag. Equipos 4 Large'*/ return true;
        case  	22	:/* Ag. Equipos 4 Med/Small*/ return true;
        case  	23	:/* Jumping GII Large'*/ return false;
        case  	24	:/* Jumping GII Medium'*/ return false;
        case  	25	:/* Jumping GII Small'*/ return false;
        case  	26	:/* Jumping GIII Large'*/ return false;
        case  	27	:/* Jumping GIII Medium'*/ return false;
        case  	28	:/* Jumping GIII Small'*/ return false;
        case  	29	:/* Jumping Large'*/ return false;
        case  	30	:/* Jumping Medium*/ return false;
        case  	31	:/* Jumping Small*/ return false;
        case  	32  :/* Jumping Eq. 3 Large*/ return false;
        case  	33	:/* Jumping Eq. 3 Medium*/ return false;
        case  	34	:/* Jumping Eq. 3 Small*/ return false;
			// en jornadas por equipos conjunta 3 alturas se mezclan categorias M y S
        case  	35	:/* Jp. Equipos 4 Large*/ return false;
        case  	36	:/* Jp. Equipos 4 Med/Small*/ return false;
			// en las rondas KO, y especiales los perros compiten todos contra todos a una sola manga
        case  	37	:/* Manga K.O.l*/ return true;
        case  	38	:/* Manga Especial Largel*/ return true;
        case  	39  :/* Manga Especial Medium'l*/ return true;
        case  	40	:/* Manga Especial Smalll*/ return true;

			// "Tiny" support for Pruebas de cuatro alturas
        case  	41	:/* Agility-1 GI Tiny*/ return true;
        case  	42	:/* Agility-2 GI Tiny*/ return false; // second round
        case  	43	:/* Agility GII Tiny*/ return true;
        case  	44	:/* Agility GIII Tiny*/ return true;
        case  	45	:/* Agility Tiny*/ return true;
        case  	46	:/* Agility Eq. 3 Tiny*/ return true;
			// en equipos4  cuatro alturas  agrupamos por LM y ST
        case  	47	:/* Ag. Equipos 4 Large/Medium*/ return true;
        case  	48	:/* Ag. Equipos 4 Small/Tiny*/ return true;
        case  	49	:/* Jumping GII Tinyy*/ return false;
        case  	50	:/* Jumping GIII Tiny*/ return false;
        case  	51	:/* Jumping Tiny*/ return false;
        case  	52	:/* Jumping Eq. 3 Tiny*/ return false;
        case  	53	:/* Jp. Equipos 4 Large/Medium*/ return false;
        case  	54	:/* Jp. Equipos 4 Small/Tiny*/ return false;
        case  	55	:/* Manga Especial Tiny*/ return true;
    }
}

function isJumping(tanda) {
    return !isAgility(tanda);
}

/**
 * Obtiene el modo de visualizacion de una manga determinada en funcion de la prueba, tipo de recorrido y categorias
 * @param {int} federation ID
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
 * @param {int} federation ID
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
    var trs_tipo=$('#dmanga_TRS_L_Tipo').val();     //0:fixed 1:best+ 2:mean+ 3:L+ 4:M+ 5:S+ 6:speed
    var trs_factor=$('#dmanga_TRS_L_Factor').val(); //0:seconds 1:percentage
    var trm_tipo=$('#dmanga_TRM_L_Tipo').val();     //0:fixed 1:trs+
    var trm_factor=$('#dmanga_TRM_L_Factor').val(); //0:seconds 1:percentage
    var dist=$('#dmanga_DistL').val();
    var obst=$('#dmanga_ObstL').val();
    $('#dmanga_LargeRow').css('display','table-row');
    $('#dmanga_LargeLbl').html(data.L);

    // second row ( medium )
    if (data.M!=="") {
        // use own data and make it visible
        tipo=4;
        last=data.L;
        dist=$('#dmanga_DistM').val();
        obst=$('#dmanga_ObstM').val();
        trs_tipo=$('#dmanga_TRS_M_Tipo').val();
        trs_factor=$('#dmanga_TRS_M_Factor').val();
        trm_tipo=$('#dmanga_TRM_M_Tipo').val();
        trm_factor=$('#dmanga_TRM_M_Factor').val();
        $('#dmanga_MediumRow').css('display','table-row');
        $('#dmanga_MediumLbl').html(data.M);
    } else {
        // use parent data and hide
        $('#dmanga_DistM').val(dist);
        $('#dmanga_ObstM').val(obst);
        $('#dmanga_MediumRow').css('display','none');
        $('#dmanga_MediumLbl').html(last);
        $('#dmanga_TRS_M_Tipo').val(trs_tipo);
        $('#dmanga_TRS_M_Factor').val(trs_factor);
        $('#dmanga_TRM_M_Tipo').val(trm_tipo);
        $('#dmanga_TRM_M_Factor').val(trm_factor);
    }

    // third row (small )
    if (data.S!=="") {
        // use own data and make it visible
        tipo=5;
        last=data.S;
        dist=$('#dmanga_DistS').val();
        obst=$('#dmanga_ObstS').val();
        $('#dmanga_SmallRow').css('display','table-row');
        $('#dmanga_SmallLbl').html(data.S);
        trs_tipo=$('#dmanga_TRS_S_Tipo').val();
        trs_factor=$('#dmanga_TRS_S_Factor').val();
        trm_tipo=$('#dmanga_TRM_S_Tipo').val();
        trm_factor=$('#dmanga_TRM_S_Factor').val();
    } else {
        // use parent data and hide
        $('#dmanga_DistS').val(dist);
        $('#dmanga_ObstS').val(obst);
        $('#dmanga_SmallRow').css('display','none');
        $('#dmanga_SmallLbl').html(last);
        $('#dmanga_TRS_S_Tipo').val(trs_tipo);
        $('#dmanga_TRS_S_Factor').val(trs_factor);
        $('#dmanga_TRM_S_Tipo').val(trm_tipo);
        $('#dmanga_TRM_S_Factor').val(trm_factor);
    }

    // fourth row (tiny )
    if (data.T!=="") {
        // use own data and make it visible
        last=data.T;
        dist=$('#dmanga_DistT').val();
        obst=$('#dmanga_ObstT').val();
        $('#dmanga_TinyRow').css('display','table-row');
        $('#dmanga_TinyLbl').html(data.T);
        trs_tipo=$('#dmanga_TRS_T_Tipo').val();
        trs_factor=$('#dmanga_TRS_T_Factor').val();
        trm_tipo=$('#dmanga_TRM_T_Tipo').val();
        trm_factor=$('#dmanga_TRM_T_Factor').val();
    } else {
        // use parent data and hide
        $('#dmanga_DistT').val(dist);
        $('#dmanga_ObstT').val(obst);
        $('#dmanga_TinyRow').css('display','none');
        $('#dmanga_TinyLbl').html(last);
        $('#dmanga_TRS_T_Tipo').val(trs_tipo);
        $('#dmanga_TRS_T_Factor').val(trs_factor);
        $('#dmanga_TRM_T_Tipo').val(trm_tipo);
        $('#dmanga_TRM_T_Factor').val(trm_factor);
    }
}

function dmanga_shareJuez() {
    $('#dmanga_Operation').val('sharejuez');
    $('#dmanga_Jornada').val(workingData.jornada);
    $('#dmanga_Manga').val(0); // not really needed, but...
    var frm = $('#competicion-formdatosmanga');
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/mangaFunctions.php',
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
    $('#dmanga_Operation').val('update');
    $('#dmanga_Jornada').val(workingData.jornada);
    $('#dmanga_Manga').val(id);
    var frm = $('#competicion-formdatosmanga');
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/mangaFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.hasOwnProperty('errorMsg')){
            	$.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
            } else {// on submit success, reload results
    			var recorrido=$("input:radio[name=Recorrido]:checked").val();
    			$.messager.alert('<?php _e('Data saved'); ?>','<?php _e('Data on current round stored'); ?>','info');
    			workingData.datosManga.Recorrido=recorrido;
    			setupResultadosWindow(recorrido);
            }
        }
    });
}

/**
 * Recarga los datos asociados a una manga
 * Restaura ventana de informacion, orden de salida y competicion
 * @param id identificador de la manga
 */
function reload_manga(id) {
	// ventana de datos
	var url='/agility/server/database/mangaFunctions.php?Operation=getbyid&Jornada='+workingData.jornada+"&Manga="+id;
    // update judge list to prevent federation change
    $('#dmanga-Juez1').combogrid('load',{'Operation':'Enumerate','Federation':workingData.federation});
    $('#dmanga-Juez2').combogrid('load',{'Operation':'Enumerate','Federation':workingData.federation});
    $('#competicion-formdatosmanga').form('load',url); // notice that "onBeforeLoad is declared"
}


function proximityAlert() {
	var data=$('#ordensalida-datagrid').datagrid('getRows');
	var guias= [];
	var lista="<br />";
	for (var idx=0;idx<data.length;idx++) {
		var NombreGuia=data[idx].NombreGuia;
		// not yet declared: store perro and orden
		if ( !(NombreGuia in guias) ) {
			guias[NombreGuia] = { 'index': idx, 'perro': data[idx].Nombre }; 
			continue; 
		} 
		// already declared: eval distance
		var dist=idx-guias[NombreGuia].index;
		if (dist>parseInt(ac_config.proximity_alert)) {
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
    var msg='<?php _e('Youll lost <strong>EVERY</strong> inserted results'); ?>'+'<br />'+
        '<?php _e('in every categories on this round'); ?>'+'<br /><br />'+
        '<?php _e('Do you really want to continue?'); ?>';
    var w=$.messager.confirm('<?php _e("Erase results");?>', msg, function(r){
        if (!r) return;
        $.ajax({
            type:'GET',
            url:"/agility/server/database/resultadosFunctions.php",
            dataType:'json',
            data: {
                Prueba: workingData.prueba,
                Jornada: workingData.jornada,
                Manga: workingData.manga,
                Operation: 'reset'
            }
        }).done( function(msg) {
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
        '<?php _e('in all categories with the Agility/Jumping counterpart of this round'); ?>'+'<br /><br />'+
        '<?php _e('Do you really want to continue?'); ?>';
    var w=$.messager.confirm('<?php _e("Swaps results");?>', msg, function(r){
        if (!r) return;
        $.ajax({
            type:'GET',
            url:"/agility/server/database/mangaFunctions.php",
            dataType:'json',
            data: {
                Prueba: workingData.prueba,
                Jornada: workingData.jornada,
                Manga: workingData.manga,
                Operation: 'swap'
            }
        }).done( function(msg) {
            $('#competicion-dialog').dialog('close');
            $('#competicion-listamangas').datagrid('reload');
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
    var rows = dg.datagrid('getRows');
    var dorsal = parseInt(drs.val());
    drs.val("--- Dorsal ---");
    drs.blur();// remove focus to hide tooltip
    if (dorsal >= 0) {
        var idx = dg.datagrid('getRowIndex', dorsal);
        if (idx < 0) {
            $.messager.alert('<?php _e("Not found"); ?>', '<?php _e("Cannot find dog with dorsal"); ?>'+" "+dorsal, "error");
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
    }
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
        	if (evt.ctrlKey) { displayRowData(dg); return false; }
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
 * Evaluate puesto for given perro in final scores
 * Notice that the dog data for current round is not yet stored
 * @param {object} datos (idPerro, penalization on current round)
 * @param {function} callback(resultado,puesto)
 * @returns {boolean}
 */
function getPuestoFinal(datos,callback) {
    var mode=getMangaMode(workingData.datosPrueba.RSCE,workingData.datosManga.Recorrido,datos.Categoria);
    if (mode==-1) {
        $.messager.alert('<?php _e('Error'); ?>','<?php _e('Internal error: invalid Federation/Course/Category combination'); ?>','error');
        return false;
    }
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
        url:"/agility/server/database/clasificacionesFunctions.php",
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
function getPuestoParcial(datos,callback) {
    var mode=getMangaMode(workingData.datosPrueba.RSCE,workingData.datosManga.Recorrido,datos.Categoria);
    if (mode==-1) {
        $.messager.alert('<?php _e('Error'); ?>','<?php _e('Internal error: invalid Federation/Course/Category combination'); ?>','error');
        return false;
    }
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
        url:"/agility/server/database/resultadosFunctions.php",
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
    if (isJornadaEquipos()) $('#parciales_equipos-datagrid').datagrid('loadData',{total:0, rows:{}});
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
	$.ajax({
		type:'GET',
		url:"/agility/server/database/resultadosFunctions.php",
		dataType:'json',
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
			Pendiente: data['Pendiente']
		},
		success: function(dat) {
			if (dat.Manga!=workingData.manga) return; // window changed
			$('#competicion-datagrid').datagrid('updateRow',{index: idx,row: dat});
			$('#lnkb1_'+idx).linkbutton();
			$('#lnkb2_'+idx).linkbutton();
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
				type:'GET',
				url:"/agility/server/database/ordenSalidaFunctions.php",
				dataType:'json',
				data: {
					Prueba: workingData.prueba,
					Jornada: workingData.jornada,
					Manga: workingData.manga,
                    Categorias: $('#ordensalida-categoria').combobox('getValue'),
					Operation: oper
				}
			}).done( function(msg) {
				reloadOrdenSalida();
			});
		});
	} else { // 'reverse' o 'clone'
		$.ajax({
			type:'GET',
			url:"/agility/server/database/ordenSalidaFunctions.php",
			dataType:'json',
			data: {
				Prueba: workingData.prueba,
				Jornada: workingData.jornada,
				Manga: workingData.manga,
                Categorias: $('#ordensalida-categoria').combobox('getValue'),
				Operation: oper
			}
		}).done( function(msg) {
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
		type:'GET',
		url:"/agility/server/database/ordenSalidaFunctions.php",
		dataType:'json',
		data: {	
			Operation: 'dnd', Prueba: workingData.prueba, Jornada: workingData.jornada,	Manga: workingData.manga, From: from,To: to,Where: where
		}
	}).done( function(msg) {
		whenDone();
	});
}

//reajusta el programa de la jornada
//poniendo la tanda "from" delante (where==0) o detras (where==1) de la tanda "to"
function dragAndDropOrdenTandas(from,to,where) {
	if (workingData.prueba==0) return;
	if (workingData.jornada==0) return;
	$.ajax({
		type:'GET',
		url:"/agility/server/database/tandasFunctions.php",
		dataType:'json',
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
    if (!row && name!== 'ordentandas') {
    	$.messager.alert('<?php _e('Error'); ?>','<?php _e('There is no round selected'); ?>','info');
    	return; // no hay ninguna manga seleccionada. retornar
    }
    var title = workingData.nombrePrueba + ' -- ' + workingData.nombreJornada;
    $('#ordentandas-dialog').dialog('close');
    $('#ordensalida-dialog').dialog('close');
    $('#competicion-dialog').dialog('close');
    $('#resultadosmanga-dialog').dialog('close');
    if (name==='ordentandas') {
        // abrimos ventana de dialogo
        $('#ordentandas-dialog').dialog('open').dialog('setTitle',' <?php _e("Journey"); ?>'+": "+title);
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
		type:'GET',
		url:"/agility/server/database/resultadosFunctions.php",
		dataType:'json',
		data: {	Operation:'getResultados', Prueba:workingData.prueba, Jornada:workingData.jornada, Manga:idmanga, Mode: mode },
		success: function(dat) {
			var suffix='L';
			switch (mode) {
			case 0: case 4: case 6: case 8: suffix='L'; break;
			case 1: case 3: suffix='M'; break;
			case 2: case 7: suffix='S'; break;
			case 5: suffix='T'; break;
			}
			$('#dm'+idxmanga+'_Nombre').val(dat['manga'].TipoManga);
			$('#dm'+idxmanga+'_DIST_'+suffix).val(dat['trs'].dist);
			$('#dm'+idxmanga+'_OBST_'+suffix).val(dat['trs'].obst);
			$('#dm'+idxmanga+'_TRS_'+suffix).val(dat['trs'].trs);
			$('#dm'+idxmanga+'_TRM_'+suffix).val(dat['trs'].trm);
            var vel=(''+dat['trs'].vel).replace('&asymp;','\u2248');
			$('#dm'+idxmanga+'_VEL_'+suffix).val(vel);
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
	var resultados=[];

	// FASE 0 ajustamos los jueces de la ronda
	$('#dm1_Juez1').val(row.Juez11);
	$('#dm1_Juez2').val(row.Juez12);
	$('#dm2_Juez1').val(row.Juez21);
	$('#dm2_Juez2').val(row.Juez22);

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

    // visibilidad de primera manga
    $('#datos_manga1-InfoRow').css('display','table-row');
    $('#datos_manga1-LargeRow').css('display',(infomanga['L']==="")?'none':'table-row');
    $('#datos_manga1-MediumRow').css('display',(infomanga['M']==="")?'none':'table-row');
    $('#datos_manga1-SmallRow').css('display',(infomanga['S']==="")?'none':'table-row');
    $('#datos_manga1-TinyRow').css('display',(infomanga['T']==="")?'none':'table-row');
    // textos de la primera manga
    $('#datos_manga1-LargeLbl').html(infomanga['L']);
    $('#datos_manga1-MediumLbl').html(infomanga['M']);
    $('#datos_manga1-SmallLbl').html(infomanga['S']);
    $('#datos_manga1-TinyLbl').html(infomanga['T']);
    // datos evaluados de TRS y TRM de primera manga
    // si fedinfo.Modes[rec][i] resultados_fillForm no hace nada
    resultados_fillForm(resultados,row.Manga1,'1',fedinfo.Modes[rec][0]);
    resultados_fillForm(resultados,row.Manga1,'1',fedinfo.Modes[rec][1]);
    resultados_fillForm(resultados,row.Manga1,'1',fedinfo.Modes[rec][2]);
    resultados_fillForm(resultados,row.Manga1,'1',fedinfo.Modes[rec][3]);
    // Manga 2
    if (row.Manga2<=0) {
        // esta ronda solo tiene una manga. desactiva la segunda
        $('#datos_manga2-InfoRow').css('display','none');
        $('#datos_manga2-LargeRow').css('display','none');
        $('#datos_manga2-MediumRow').css('display','none');
        $('#datos_manga2-SmallRow').css('display','none');
        $('#datos_manga2-TinyRow').css('display','none');
    } else {
        // visibilidad de segunda manga
        $('#datos_manga2-InfoRow').css('display','table-row');
        $('#datos_manga2-LargeRow').css('display',(infomanga['L']==="")?'none':'table-row');
        $('#datos_manga2-MediumRow').css('display',(infomanga['M']==="")?'none':'table-row');
        $('#datos_manga2-SmallRow').css('display',(infomanga['S']==="")?'none':'table-row');
        $('#datos_manga2-TinyRow').css('display',(infomanga['T']==="")?'none':'table-row');
        // textos de la segunda manga
        $('#datos_manga2-LargeLbl').html(infomanga['L']);
        $('#datos_manga2-MediumLbl').html(infomanga['M']);
        $('#datos_manga2-SmallLbl').html(infomanga['S']);
        $('#datos_manga2-TinyLbl').html(infomanga['T']);
        // datos evaluados de TRS y TRM de segunda manga
        // si fedinfo.Modes[rec][i] resultados_fillForm no hace nada
        resultados_fillForm(resultados,row.Manga2,'2',fedinfo.Modes[rec][0]);
        resultados_fillForm(resultados,row.Manga2,'2',fedinfo.Modes[rec][1]);
        resultados_fillForm(resultados,row.Manga2,'2',fedinfo.Modes[rec][2]);
        resultados_fillForm(resultados,row.Manga2,'2',fedinfo.Modes[rec][3]);
    }

    // FASE 2: cargamos informacion sobre resultados globales y la volcamos en el datagrid
    mode=$('#resultados-selectCategoria').combobox('getValue');
	$.ajax({
		type:'GET',
		url:"/agility/server/database/clasificacionesFunctions.php",
		dataType:'json',
		data: {
            Operation: (isJornadaEquipos())?'clasificacionEquipos':'clasificacionIndividual',
			Prueba:workingData.prueba,
            Jornada:workingData.jornada,
            Federation:fed,
			Manga1:row.Manga1,
			Manga2:row.Manga2,
			Rondas: row.Rondas,
			Mode: mode
		},
		success: function(dat) {
            if ( isJornadaEquipos() ) {
                $('#finales_equipos_roundname_m1').text(row.Manga1.Nombre);
                $('#finales_equipos_roundname_m2').text(row.Manga2.Nombre);
                workingData.individual=dat.individual;
                $('#finales_equipos-datagrid').datagrid('loadData',dat.equipos);
            } else {
                $('#finales_individual_roundname_m1').text(row.Manga1.Nombre);
                $('#finales_individual_roundname_m2').text(row.Manga2.Nombre);
                workingData.individual=dat.rows;
                $('#finales_individual-datagrid').datagrid('loadData',dat.rows);
            }
		}
	});
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
	var url='/agility/server/pdf/print_clasificacion.php';
	var mode=$('#resultados-selectCategoria').combobox('getValue');
	var str1="";
	var str2="";
	if (ronda==null) {
    	$.messager.alert('<?php _e("Error"); ?>','<?php _e("There is no selected round for this journey"); ?>',"warning");
    	return false; // no way to know which ronda is selected
	}
	// verificamos manga 1
	$.ajax({
		type:'GET',
		url:"/agility/server/database/resultadosFunctions.php",
		dataType:'json',
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
				type:'GET',
				url:"/agility/server/database/resultadosFunctions.php",
				dataType:'json',
				data: {	
					Operation:	'getPendientes', 
					Prueba:	workingData.prueba, 
					Jornada:workingData.jornada, 
					Manga:ronda.Manga2, 
					Mode: mode 
				},
				success: function(data) {
					if (parseInt(data['total'])!=0) str2=verifyCompose(data,ronda.Manga2,ronda.NombreManga2);
					if (str1==="" && str2==="") {
						$.messager.alert('<?php _e("Verify OK"); ?>','<?php _e("No dogs found without their course results"); ?>',"info");
					} else {
						var w=$.messager.alert('<?php _e("Verify Error"); ?>',str1+str2,"error");
						w.window('resize',{width:600}).window('center');
					}
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
		type:'GET',
		url:"/agility/server/database/clasificacionesFunctions.php",
		dataType:'json',
		data: {
            Operation: (isJornadaEquipos())?'clasificacionEquipos':'clasificacionIndividual',
			Prueba:workingData.prueba,
			Jornada:workingData.jornada,
			Manga1:ronda.Manga1,
			Manga2:ronda.Manga2,
			Rondas: ronda.Rondas,
			Mode: mode
		},
		success: function(dat) {
            if ( isJornadaEquipos() ) {
                $('#finales_equipos_roundname_m1').text(ronda.NombreManga1);
                $('#finales_equipos_roundname_m2').text(ronda.NombreManga2);
                workingData.individual=dat.individual;
                $('#finales_equipos-datagrid').datagrid('loadData',dat.equipos);
            } else {
                $('#finales_individual_roundname_m1').text(ronda.NombreManga1);
                $('#finales_individual_roundname_m2').text(ronda.NombreManga2);
                workingData.individual=dat.rows;
                $('#finales_individual-datagrid').datagrid('loadData',dat.rows);
            }
		}
	});
}
