/*
 pruebas.js

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

//***** gestion de pruebas		*********************************************************


/**
 * Recalcula el formulario de pruebas anyadiendo parametros de busqueda
 */
function doSearchPrueba() {
	var includeClosed= $('#pruebas-openBox').is(':checked')?'1':'0';
	// reload data adding search criteria
    $('#pruebas-datagrid').datagrid('load',{
        where: $('#pruebas-search').val(),
        closed: includeClosed
    });
}

/**
 *Open dialogo de creacion de pruebas
 *@param {string} dg datagrid ID de donde se obtiene la prueba
 *@param {string} def default value to insert into Nombre 
 *@param {function} onAccept what to do when a new prueba is created
 */
function newPrueba(dg,def,onAccept){
	$('#pruebas-dialog').dialog('open').dialog('setTitle','<?php _e('Nueva Prueba'); ?>');
	$('#pruebas-form').form('clear');
    $('#pruebas-Federation').combogrid('readonly',false);
	if (!strpos(def,"Buscar")) $('#pruebas-Nombre').val(def.capitalize());// fill prueba Name
	$('#pruebas-Operation').val('insert');
	if (onAccept!==undefined)$('#pruebas-okBtn').one('click',onAccept);
}

/**
 * If there are any inscriptions in a contest, disable change of federation
 * @param id
 * @callback what to do with ajax response
 * @returns
 */
function hasInscripciones(id,callback) {
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/inscripcionFunctions.php',
        data: { Operation: 'howmany', Prueba: id },
        dataType: 'json',
        // beforeSend: function(jqXHR,settings){ return frm.form('validate'); },
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
            } else {
            	var flag= (parseInt(result.Inscritos)!=0)?true:false;
            	callback(flag);
            }
        }
    });
}

/**
 * Open dialogo de modificacion de pruebas
 * @param {string} dg datagrid ID de donde se obtiene la prueba
 */
function editPrueba(dg){
	if ($('#pruebas-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Edit Error"); ?>','<?php _e("There is no contest selected"); ?>',"warning");
    	return; // no way to know which prueba is selected
    }
    // add extra required data to form dialog
    row.Operation='update';
    $('#pruebas-dialog').dialog('open').dialog('setTitle','<?php _e('Modify contest data'); ?>');
    $('#pruebas-form').form('load',row);
}

/**
 * Ask server routines for add/edit a prueba into BBDD
 */
function savePrueba() {
	// take care on bool-to-int translation from checkboxes to database
    $('#pruebas-Cerrada').val( $('#pruebas-Cerrada').is(':checked')?'1':'0');
    var frm = $('#pruebas-form');
    if (!frm.form('validate')) return; // don't call inside ajax to avoid override beforeSend()
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/pruebaFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        // beforeSend: function(jqXHR,settings){ return frm.form('validate'); },
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
            } else {
                $('#pruebas-dialog').dialog('close');        // close the dialog
                $('#pruebas-datagrid').datagrid('reload');    // reload the prueba data
            }
        }
    });
}

/**
 * Delete data related with a prueba in BBDD
 * @param {string} dg datagrid ID de donde se obtiene la prueba
 */
function deletePrueba(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Delete error"); ?>','<?php _e("There is no contest selected"); ?>',"warning");
    	return; // no way to know which prueba is selected
    }
    $.messager.confirm('<?php _e('Confirm'); ?>',
    		"<p><strong><?php _e('Notice'); ?>:</strong></p>" +
    		'<?php _e("<p>By deleting this contest <b>youll loose</b> every associated data and scores"); ?>' +
    		"</p><p><?php _e('Do you really want to delete this contest'); ?>?</p>",function(r){
        if (r){
            $.get('/agility/server/database/pruebaFunctions.php',{Operation:'delete',ID:row.ID},function(result){
                if (result.success){
                    $(dg).datagrid('unselectAll').datagrid('reload');    // reload the prueba data
                } else {
                    $.messager.show({ width:300, height:200, title:'<?php _e('Error'); ?>', msg:result.errorMsg });
                }
            },'json');
        }
    });
}

function exportPrueba(dg) {
    var row = $(dg).datagrid('getSelected');
    var url='/agility/server/excel/scores_writer.php';
    if (!row) {
        $.messager.alert('<?php _e("Export error"); ?>','<?php _e("There is no contest selected"); ?>',"warning");
        return; // no way to know which prueba is selected
    }
    $.fileDownload(
        url,
        {
            httpMethod: 'GET',
            data: {
                Prueba:row.ID
            },
            preparingMessageHtml: '<?php _e("We are preparing your report, please wait"); ?> ...',
            failMessageHtml: '<?php _e("There was a problem generating your report, please try again."); ?>'
        }
    );
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

function pruebas_emailEditClub(index,row) {
    var m = $.messager.prompt({
        title: "<?php _e('Change Email');?>",
        msg: "<?php _e('Enter new email for club');?>: <br/>"+row.Nombre,
        fn: function(r) {
            if (!r) return false;
            $.ajax({
                type: 'GET',
                url: '/agility/server/mailFunctions.php',
                data: {
                    Prueba: workingData.prueba,
                    Federation: workingData.federation,
                    Club: row['ID'],
                    Email: r,
                    Operation: "updateclub"
                },
                dataType: 'json',
                // beforeSend: function(jqXHR,settings){ return frm.form('validate'); },
                success: function (result) {
                    if (result.errorMsg){
                        $.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
                        return false;
                    }
                    $('#pruebas_email-Clubs').datagrid('updateRow',{
                            index: index,
                            row: {Email:r,Sent:0} // set new mail and mark sent flag as false
                    });
                }
            });
        },
        width: 350
    });
    m.find('.messager-input').val(row.Email); // set default value for prompt
    return false;
}

function emailPrueba(dg) {
    var row = $(dg).datagrid('getSelected');
    var url='/agility/server/excel/scores_writer.php';
    if (!row) {
        $.messager.alert('<?php _e("Mailer error"); ?>','<?php _e("There is no contest selected"); ?>',"warning");
        return false; // no way to know which prueba is selected
    }
    if (ac_regInfo.Serial==="00000000") {
        $.messager.alert('<?php _e("Mail services"); ?>',
            '<p><?php _e("Electronic mail operations<br/>are not allowed for unregistered licenses"); ?></p>',
            "info").window('resize',{width:480});
        return false;
    }
    // if no poster / tryptich warn user
    if ((row.Triptico=="")||(row.Cartel=="")) {
        $.messager.confirm({
            title:'<?php _e("Missing data"); ?>',
            msg:'<p><?php _e("Contest has poster and/or tryptich undefined"); ?></p><p><?php _e("Continue anyway?");?></p>',
            width:400,
            fn:function(r){
                if (!r) return false;
                $('#pruebas_email-dialog').dialog('open').dialog('setTitle', '<?php _e('Email contest info to clubs'); ?>');
            }
        });
        return false; // no way to know which prueba is selected
    }
    // arriving here means that cartel and tryptich are present. so no need to confirm
    $('#pruebas_email-dialog').dialog('open').dialog('setTitle', '<?php _e('Email contest info to clubs'); ?>');
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Borra la marca de correo enviado de todos los mensajes
 */
function prueba_clearSentMark() {
    $.messager.confirm(
        '<?php _e('Confirm'); ?>',
        '<?php _e("Mark every club as pending to receive mail"); ?> <br/> <?php _e('Sure');?>',
            function(r){
            if (!r) return false;
            $.get(
                '/agility/server/mailFunctions.php',
                {Operation:'clearsent',Prueba:workingData.prueba,Federation:workingData.federation},
                function(result){
                    if (result.success){
                        $('#pruebas_email-Clubs').datagrid('reload',{Operation:'enumerate',Prueba:workingData.prueba,Federation:workingData.federation});    // reload the prueba data
                    } else {
                        $.messager.show({ width:300, height:200, title:'<?php _e('Error'); ?>', msg:result.errorMsg });
                    }
                },
                'json'
            );
        }
    );
}

/**
 * Ask send mail with contest info and inscription templates to each selected club
 */
function perform_emailPrueba() {
    var dg=$('#pruebas_email-Clubs');
    var resend=$('#pruebas_email-ReSend').prop('checked');
    var sendtome= $('#pruebas_email-SendToMe').prop('checked')?1:0;
    var emptytemplate=$('#pruebas_email-EmptyTemplate').val();
    var contents= $('#pruebas_email-Contents').val();

    function handleMail(rows,index,size) {
        if (index>=size){
            // recursive call finished, clean, close and refresh
            pwindow.window('close');
            dg.datagrid('clearSelections');
            dg.datagrid('reload',{Prueba:workingData.prueba,Federation:workingData.federation,Operation:'enumerate'});
            return;
        }
        // take care on sent mails and "ReSend" flag
        if ( (!resend) && (rows[index]['Sent']!=0) ) {
            $('#pruebas_email-progresslabel').html('<?php _e("Skipping"); ?>'+": "+rows[index].Nombre+"<br/> <?php _e('Already sent');?>");
            setTimeout(function(){handleMail(rows,index+1,size);},2000); // fire again
            return;
        }
        // skip row ID:1 and fields with no mail
        if ( (rows[index]['ID']<1) || (rows[index]['Email']=="")) {
            $('#pruebas_email-progresslabel').html('<?php _e("Skipping"); ?>'+": "+rows[index].Nombre+"<br/> <?php _e('No mail declared');?>");
            setTimeout(function(){handleMail(rows,index+1,size);},2000); // fire again
            return;
        }
        $('#pruebas_email-progresslabel').html('<?php _e("Processing"); ?>'+": "+rows[index].Nombre+"<br/> &lt;"+rows[index].Email+"&gt;");
        $('#pruebas_email-progressbar').progressbar('setValue', (100.0*(index+1)/size).toFixed(2));
        $.ajax({
            cache: false,
            timeout: 30000, // 20 segundos
            type:'POST',
            url:"/agility/server/mailFunctions.php",
            dataType:'json',
            data: {
                Prueba: workingData.prueba,
                Federation: workingData.federation,
                Operation: 'sendInscriptions',
                Club: rows[index].ID,
                Email: rows[index].Email,
                EmptyTemplate: emptytemplate,
                SendToMe: sendtome,
                Contents: contents
            },
            success: function(result) {
                handleMail(rows,index+1,size);
            }
        });
    }

    var pwindow=$('#pruebas_email-progresswindow');
    var selectedRows= dg.datagrid('getSelections');
    var size=selectedRows.length;
    if(size==0) {
        $.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no selected clubs to send mail to"); ?>',"warning");
        return; // no hay ninguna inscripcion seleccionada. retornar
    }
    if (ac_authInfo.Perms>2) {
        $.messager.alert('<?php _e("No permission"); ?>','<?php _e("Current user has not enought permissions to send mail"); ?>',"error");
        return; // no tiene permiso para realizar inscripciones. retornar
    }
    pwindow.window('open');
    // clear cache before send
    $.ajax({
        cache: false,
        timeout: 30000, // 20 segundos
        type:'POST',
        url:"/agility/server/mailFunctions.php",
        dataType:'json',
        data: {
            Prueba: workingData.prueba,
            Federation: workingData.federation,
            Operation: 'clearcache'
        },
        success: function(result) {
            handleMail(selectedRows,0,size);
        }
    });
}

// ***** gestion de jornadas	*********************************************************

/**
 * Edita la jornada seleccionada
 *@param pruebaID objeto que contiene los datos de la prueba
 *@param row datos de la jornada
 */
function editJornadaFromPrueba(pruebaID,row) {
    if (!row) {
        $.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no journey selected"); ?>',"warning");
    	return; // no hay ninguna jornada seleccionada. retornar
    }
    if (row.Cerrada==true) { // no permitir la edicion de pruebas cerradas
    	$.messager.alert('<?php _e("Invalid selection"); ?>','<?php _e("Cannot edit a journey stated as closed"); ?>',"error");
        return;
    }
    // todo ok: abrimos ventana de dialogo
    $('#jornadas-dialog').dialog('open').dialog('setTitle','<?php _e('Modify journey data'); ?>');
    $('#jornadas-form').form('load',row); // will trigger onLoadSuccess in dlg_pruebas
}

/**
 * Cierra la jornada seleccionada
 *@param pruebaID objeto que contiene los datos de la prueba
 *@param datagridID identificador del datagrid del que se toman los datos
 */
function closeJornadaFromPrueba(pruebaID,datagridID) {
	// obtenemos datos de la JORNADA seleccionada
	var row= $(datagridID).datagrid('getSelected');
    // var row = $('#jornadas-datagrid-'+prueba.ID).datagrid('getSelected');
    if (!row) {
        $.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no journey selected"); ?>',"warning");
    	return; // no hay ninguna jornada seleccionada. retornar
    }
    if (row.Cerrada==true) { // no permitir la edicion de pruebas cerradas
    	$.messager.alert('<?php _e("Invalid selection"); ?>','<?php _e("Cannot close an already closed journey"); ?>',"error");
        return;
    }
    // $.messager.defaults={ ok:"Cerrar", cancel:"Cancelar" };
    var w=$.messager.confirm(
    		'<?php _e("Notice"); ?>',
    		'<?php _e("Setting a journey as closed"); ?><br/>' +
    		'<?php _e("Youll be no longer able to edit,delete or modify,"); ?><br/>' +
    		'<?php _e("inscriptions or scores"); ?><br/>' +
    		'<?php _e("Continue?"); ?>',
    		function(r) { 
    	    	if(r) {
    	            $.get('/agility/server/database/jornadaFunctions.php',{Operation:'close',ID:row.ID},function(result){
    	                if (result.success){
    	                    $(datagridID).datagrid('reload');    // reload the pruebas data
    	                } else {
    	                    $.messager.show({ width:300, height:200, title:'<?php _e('Error'); ?>', msg:result.errorMsg });
    	                }
    	            },'json');
    	    	}
    		});
    w.window('resize',{width:400,height:150}).window('center');
}

/**
 * Ask for commit new/edit jornada to server
 */
function saveJornada(){
	// take care on bool-to-int translation from checkboxes to database
    if ($('#jornadas-PreAgilityChk').is(':checked')) {
        $('#jornadas-PreAgility').val( ($('#jornadas-MangasPreAgility').combobox('getValue')==1)?'1':'0');
        $('#jornadas-PreAgility2').val( ($('#jornadas-MangasPreAgility').combobox('getValue')==2)?'1':'0');
    } else {
    	$('#jornadas-PreAgility').val(0);
    	$('#jornadas-PreAgility2').val(0);
    }
    $('#jornadas-Grado1').val( $('#jornadas-Grado1Chk').is(':checked')?$('#jornadas-MangasGrado1').combobox('getValue'):'0');
    $('#jornadas-Grado2').val( $('#jornadas-Grado2').is(':checked')?'1':'0');
    $('#jornadas-Grado3').val( $('#jornadas-Grado3').is(':checked')?'1':'0');
    $('#jornadas-Open').val( $('#jornadas-Open').is(':checked')?'1':'0');
    var eq3=$('#jornadas-Equipos3');
    var eq4=$('#jornadas-Equipos4');
    if ($('#jornadas-EquiposChk').is(':checked')) {
        var val=$('#jornadas-MangasEquipos').combobox('getValue');
        switch (parseInt(val)) {
            case 1: /* old 3best */     eq3.val(3); eq4.val(0); break;
            case 2: /* old 4combined */ eq3.val(0); eq4.val(4); break;
            case 3: /* 2 best of 3 */   eq3.val(2); eq4.val(0); break;
            case 4: /* 3 best of 4 */   eq3.val(3); eq4.val(0); break;
            case 5: /* 2 combined */    eq3.val(0); eq4.val(2); break;
            case 6: /* 3 combined */    eq3.val(0); eq4.val(3); break;
            case 7: /* 4 combined */    eq3.val(0); eq4.val(4); break;
            default: eq3.val(0); eq4.val(0); break;
        }
    } else {
        eq3.val(0);
        eq4.val(0);
    }
    $('#jornadas-KO').val( $('#jornadas-KO').is(':checked')?'1':'0');
    $('#jornadas-Games').val( $('#jornadas-Games').is(':checked')?'1':'0');
    $('#jornadas-Especial').val( $('#jornadas-Especial').is(':checked')?'1':'0');
    $('#jornadas-Cerrada').val( $('#jornadas-Cerrada').is(':checked')?'1':'0');
    // handle fecha
    // do normal submit
    $('#jornadas-Operation').val('update');
    
    var frm = $('#jornadas-form');
    if (!frm.form('validate')) return; // don't call inside ajax to avoid override beforeSend()
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/jornadaFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
            } else {
            	var id=$('#jornadas-Prueba').val();
                $('#jornadas-dialog').dialog('close');        // close the dialog
                // notice that some of these items may fail if dialog is not deployed. just ignore
                $('#jornadas-datagrid-'+id).datagrid('reload',{ Prueba:id , Operation:'select' }); // reload the prueba data
                $('#inscripciones-jornadas').datagrid('reload');    // reload the prueba data
            }
        }
    });
}

/**
 * Comprueba si se puede seleccionar la prueba elegida en base a las mangas pre-existentes
 * @param {checkbox} id checkbox que se acaba de (de) seleccionar
 * @param {bitmap} mask mascara de la prueba marcada (seleccionada o de-seleccionada)
 * 0x0001, 'PreAgility 1 Manga'
 * 0x0002, 'PreAgility 2 Mangas'
 * 0x0004, 'Grado1',
 * 0x0008, 'Grado2',
 * 0x0010, 'Grado3',
 * 0x0020, 'Individual', (pen)
 * 0x0040, 'Equipos3',
 * 0x0080, 'Equipos4',
 * 0x0100, 'KO',
 * 0x0200, 'Especial'
 * 0x0400, 'eq 2of3'
 * 0x0800, 'eq 2combined'
 * 0x1000, 'eq 3combined'
 * 0x2000, 'wao/games'
 */
function checkPrueba(id,mask) {
	var pruebas=0;
	// mascara de pruebas seleccionadas
	if ( $('#jornadas-PreAgilityChk').is(':checked') ) {
		$('#jornadas-MangasPreAgility').combobox('enable');
		pruebas |= 0x0003; // 1:manga simple 2:dos mangas
	} else {
		$('#jornadas-MangasPreAgility').combobox('disable');
	}
	// pruebas |= $('#jornadas-PreAgility').is(':checked')?0x0001:0;
	// pruebas |= $('#jornadas-PreAgility2').is(':checked')?0x0002:0;

    // grado 1 puede tener 1 2 o 3 mangas
    if ($('#jornadas-Grado1Chk').is(':checked')) {
	    pruebas |= 0x0004;
        $('#jornadas-MangasGrado1').combobox('enable');
        $('#jornadas-Grado1').val($('#jornadas-MangasGrado1').combobox('getValue'));
    } else {
        $('#jornadas-MangasGrado1').combobox('disable');
        $('#jornadas-Grado1').val(0);
    }

	pruebas |= $('#jornadas-Grado2').is(':checked')?0x0008:0;
	pruebas |= $('#jornadas-Grado3').is(':checked')?0x0010:0;
	pruebas |= $('#jornadas-Open').is(':checked')?0x0020:0;

	if ( $('#jornadas-EquiposChk').is(':checked') ) {
		$('#jornadas-MangasEquipos').combobox('enable');
        pruebas |= 0x1CC0; // eq3o4:64 eq4c:128 eq2o3:1024 eq2c:2048 eq3c:4096
	} else {
		$('#jornadas-MangasEquipos').combobox('disable');
	}
	// pruebas |= $('#jornadas-Equipos3').is(':checked')?0x0040:0;
	// pruebas |= $('#jornadas-Equipos4').is(':checked')?0x0080:0;
    pruebas |= $('#jornadas-KO').is(':checked')?0x0100:0;
    pruebas |= $('#jornadas-Games').is(':checked')?0x2000:0;

	if ( $('#jornadas-Especial').is(':checked') ) {
		$('#jornadas-Observaciones').textbox('enable');
		pruebas |= 0x0200;
	} else {
		$('#jornadas-Observaciones').textbox('disable');
	}

	// si no hay prueba seleccionada no hacer nada
	if (pruebas==0) {
	    console.log("WARN: Journey with no item(s) declared. Skip")
	    return;
    }

	// si estamos seleccionando una prueba ko/open/games/equipos, no permitir ninguna otra
	if ( (mask & 0x3DE0) != 0 ) {
		if (mask!=pruebas) {
			$.messager.alert('<?php _e('Error'); ?>','<?php _e('KO, Games (WAO), Individual (Open), or team rounds must be declared in a separate journey'); ?>','error');
			$(id).prop('checked',false);
			if (id==='#jornadas-EquiposChk') $('#jornadas-MangasEquipos').combobox('disable');
		}
	} else {
		if ( (pruebas & 0x3DE0) != 0 ) {
			$.messager.alert('<?php _e('Error'); ?>','<?php _e('You cannot add additional rounds when KO, Games (WAO), Individual (Open) or team rounds are already declared in a journey'); ?>','error');
			$(id).prop('checked',false);
			if (id==='#jornadas-PreAgilityChk') $('#jornadas-MangasPreAgility').combobox('disable');
			if (id==='#jornadas-Especial') $('#jornadas-Observaciones').textbox('disable');
		}
	}
}
