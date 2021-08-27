/*
 inscripciones.js

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
header('Content-Type: text/javascript');
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

//***** gestion de inscripciones de una prueba	*****************************************************

/**
 *Abre dialogo de registro de inscripciones
 *@param {string} dg datagrid ID de donde se obtiene el id de la prueba
 *@param {string} def default value to insert into search field
 *@param {function} onAccept what to do when a new inscription is created
 */
function newInscripcion(dg,def,onAccept) {
	$('#new_inscripcion-dialog').dialog('open').dialog('setTitle','<?php _e('New inscriptions'); ?>');
	// let openEvent on dialog fire up form setup
	if (onAccept!==undefined)$('#new_inscripcion-okBtn').one('click',onAccept);
}

/**
 * On-the-fly change inscription on a journey
 *
 * Call server to perform a simple (un)inscription on a given journey for a dog
 * On success refresh affected datagrid row
 * @param idx inscription datagrid index
 * @param prueba Prueba ID
 * @param perro Dog ID
 * @param jindex Journey index (0..7)
 * @param obj changed checkbox
 */
function changeInscription(idx,prueba,perro,jindex,obj) {

    $.messager.progress({height:75, text:'<?php _e("Updating inscription");?>'});
    var ji=1+parseInt(jindex);
    $.ajax({
        type: 'GET',
        url: '../ajax/database/inscripcionFunctions.php',
        data: {
            Operation: (obj.checked)?"insertIntoJourney":"deleteFromJourney",
            Prueba: prueba,
            Perro: perro,
            Jornada: ji // notice index, no real Jornada ID
        },
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
                obj.checked=!obj.checked; // revert change ( beware on recursive onChange events )
            } else {
                var j="J"+ji;
                // on save done refresh related datagrid index data
                var row=$('#inscripciones-datagrid').datagrid('getSelected');
                if (row) row[j]=obj.checked;
            }
        }
    }).then(function(){ // jquery ajax are promises, so can use .then(resolve(),reject())
        $.messager.progress('close');
    });
}

function editInscripcion() {
	if ($('#inscripciones-datagrid-search').is(":focus")) return; // on enter key in search input ignore
	// obtenemos datos de la inscripcion seleccionada
	var row= $('#inscripciones-datagrid').datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no inscription(s) selected"); ?>',"warning");
    	return; // no hay ninguna inscripcion seleccionada. retornar
    }
    row.Operation='update';
    $('#edit_inscripcion-form').form('load',row);
    $('#edit_inscripcion-dialog').dialog('open');
}

/**
 * Save Inscripcion being edited, as result of doneBtn.onClick()
 * On success refresh every related datagrids
 * if (done) close dialog, else reload
 */
function saveInscripcion(close) {
	// make sure that "Celo" field has correct value
	$('#edit_inscripcion-Celo').val( $('#edit_inscripcion-Celo2').is(':checked')?'1':'0');
    var frm = $('#edit_inscripcion-form');
    if (!frm.form('validate')) return;

    // disable button in ajax call to avoid recall twice
    $('#edit_inscripcion-okBtn').linkbutton('disable');
    $.ajax({
        type: 'GET',
        url: '../ajax/database/inscripcionFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
            } else {
            	// on save done refresh related data/combo grids and close dialog
                $('#inscripciones-datagrid').datagrid('reload');
            	if (close)  $('#edit_inscripcion-dialog').dialog('close');
            }
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            $.messager.alert("Save Inscripcion","Error:"+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus+" - "+errorThrown,'error' );
        }
    }).then(function(){
        $('#edit_inscripcion-okBtn').linkbutton('enable');
    });
}

/**
 * Delete data related with an inscription in BBDD
 */
function deleteInscripcion() {
	var row = $('#inscripciones-datagrid').datagrid('getSelected');    
	if (!row) {
		$.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no inscription(s) selected"); ?>',"warning");
    	return; // no hay ninguna inscripcion seleccionada. retornar
    }
	$.messager.confirm(
	        'Confirm',
			"<p><b><?php _e('Notice'); ?>:</b></p>" +
			"<p><?php _e('If you delete this inscription'); ?> ("+row.Nombre+")<br/>" +
			"<?php _e('<b>youll loose</b> every related results and data on this contest'); ?><br />" +
			"<?php _e('afecting journeys not marked as <em>closed</em>'); ?><br/>" +
			"<?php _e('Really want to delete selected inscription'); ?>?</p>",
			function(r){
				if (r){
					$.get(
						// URL
						'../ajax/database/inscripcionFunctions.php',
						// arguments
						{ 
							Operation: 'delete',
							ID: row.ID, // id de la inscripcion
							Perro: row.Perro, // id del perro
							Prueba: row.Prueba // id de la prueba
						},
						// on Success function
						function(result){
							if (result.success) {
								$('#inscripciones-datagrid').datagrid('clearSelections');
								reloadWithSearch('#inscripciones-datagrid','inscritos',false);
							} else {
								$.messager.show({ width: 300, height: 200, title: 'Error', msg: result.errorMsg });
							}
						},
						// expected datatype format for response
						'json'
					);
				} // if (r)
		}).window('resize',{width:475});
}

/**
 * Ask for commit new inscripcion to server
 * @param {string} dg datagrid to retrieve selections from
 */
function insertInscripcion(dg) {

    var mask=parseInt($('#new_inscripcion-Jornadas').val());
    var jornadas= $('#inscripciones-jornadas').datagrid('getData')['rows'];

    /**
     * This code is recursivelly called
     * @param rows selected rows
     * @param index current row
     * @param size of selected rows
     */
	function handleInscription(rows,index) {
        console.log("handleInscription index:"+index);
	    function doInscribeAjax(rows,index) {
            // preparamos progress bar
            $('#new_inscripcion-progresslabel').text('<?php _e("Enrolling"); ?>: '+rows[index].Nombre);
            $('#new_inscripcion-progressbar').progressbar('setValue', (100.0*(index+1)/rows.length).toFixed(2));
            $.ajax({
                cache: false,
                timeout: 20000, // 20 segundos
                type:'GET',
                url:"../ajax/database/inscripcionFunctions.php",
                dataType:'json',
                data: {
                    Prueba: workingData.prueba,
                    Operation: 'insert',
                    Perro: rows[index].ID,
                    Jornadas: mask,
                    Celo: $('#new_inscripcion-Celo').val(),
                    Pagado: $('#new_inscripcion-Pagado').val()
                },
                success: function(result) {
                    // clear dog name as already done
                    $('#new_inscripcion-progresslabel').text('<?php _e("Enrolling"); ?>...');
                },
                error: function(XMLHttpRequest,textStatus,errorThrown) {
                    $.messager.alert("Insert Inscription","Error:"+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus+" - "+errorThrown,'error' );
                    $('#new_inscripcion-okBtn').linkbutton('enable'); // enable button and do not continue inscription chain
                }
            }).done(function(result){
                setTimeout(function(){ handleInscription(rows,index+1);},0);
            });
        }

        // iterate current dog into journey n checking if can inscribe
        // if cannot, remove journey from inscription mask
        // when all journeys are checked, do real inscription
        function checkAndInscribe(rows,index) {
	        var mask=parseInt($('#new_inscripcion-Jornadas').val());
            var msg="<?php _e('Cannot inscribe dog');?> " + rows[index].Nombre +
                    "<br/> <?php _e('into journey');?> :<br/>&nbsp;";
            var showmsg=false;
	        for (var n=0;n<8;n++) {
	            // si la jornada no esta asignada, skip
	            if (jornadas[n].Nombre=="-- Sin asignar --") continue;
	            // si el perro no esta inscrito en la jornada, skip
                if ( (mask & (1<<n)) ==0 ) continue;
                // si se quiere inscribir comprueba si es posible
	            if (!canInscribe(jornadas[n],rows[index].Grado,rows[index].CatGuia)) {
	                mask &= ~(1<<n); // borramos mascara de inscripcion en la jornada en que no se puede
                    msg += "<br/>"+(n+1)+" - "+jornadas[n].Nombre;
                    showmsg=true;
                }
            }
	        // si en alguna jornada no se puede inscribir avisa
	        if (showmsg) {
	            $.messager.alert({
                    title:'<?php _e("Notice");?>',
                    msg:msg,
                    icon:'warning',
                    modal:true,
                    fn: function() {
                        if (mask!=0) doInscribeAjax(rows,index);
                        else setTimeout(function(){ handleInscription(rows,index+1);},0);
                    }
	            });
	        } else {
                // si no queda ninguna jornada en la que inscribirse busca siguiente perro
                if (mask!=0) doInscribeAjax(rows,index);
                else setTimeout(function(){ handleInscription(rows,index+1);},0);
            }
        }

        // tenemos como parametros el array de perrosy el indice del perro actual
        // al final de la lista a inscribir, borramos progressbar y recargamos datagrids
		if (index>=rows.length){
            // recursive call finished, clean, close and refresh
            $('#new_inscripcion-okBtn').linkbutton('enable');
            $('#new_inscripcion-progresswindow').window('close');
            $(dg).datagrid('clearSelections');
            reloadWithSearch('#new_inscripcion-datagrid','noinscritos');
            reloadWithSearch('#inscripciones-datagrid','inscritos');
			return;
		}

		// comprobamos si perro[index] a se puede inscribir en una jornada determinada
        // y en su caso procedemos
        checkAndInscribe(rows,index);
	}

	var selectedRows= $(dg).datagrid('getSelections');
	if(selectedRows.length==0) {
		$.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no marked dog to be inscribed"); ?>',"warning");
    	return; // no hay ninguna inscripcion seleccionada. retornar
	}
	if (ac_authInfo.Perms>2) {
    	$.messager.alert('<?php _e("No permission"); ?>','<?php _e("Current user has not enought permissions to handle inscriptions"); ?>',"error");
    	return; // no tiene permiso para realizar inscripciones. retornar
	}

    $('#new_inscripcion-progresswindow').window('open');
    // disable button in ajax call to avoid recall twice
    $('#new_inscripcion-okBtn').linkbutton('disable');
	handleInscription(selectedRows,0);
}

/**
 * Reajusta los dorsales de los perros inscritos ordenandolos por club,categoria,grado,nombre
 * @param idprueba ID de la prueba
 */
function reorderInscripciones(idprueba) {
    $.messager.confirm(
        '<?php _e("Reorder dorsals"); ?>',
        '<?php _e("Current dorsal numbers will be lost<br />Continue"); ?>?',
        function (r) {
            if (!r) return false;
            $.messager.progress({title:'<?php _e("Sort"); ?>',text:'<?php _e("Re-ordering Dorsals");?>'});
            // retriever current sorting order
            var dg=$('#inscripciones-datagrid');
            var order= dg.datagrid('options').sortOrder;
            var sort= dg.datagrid('options').sortName;
            if ( (sort==null) || (sort=="" )) { order=""; sort=""; }
            $.ajax({
                cache: false,
                timeout: 60000, // 60 segundos
                type:'GET',
                url:"../ajax/database/inscripcionFunctions.php",
                dataType:'json',
                data: {
                    Prueba: idprueba,
                    Operation: 'reorder',
                    order: order,
                    sort: sort
                },
                success: function(data) {
                    if(data.errorMsg) {
                        $.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: data.errorMsg });
                    } else {
                        $('#inscripciones-datagrid').datagrid('reload');
                    }
                },
                error: function(XMLHttpRequest,textStatus,errorThrown) {
                    $.messager.alert("Reorder insciptions","Error:"+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus+" - "+errorThrown,'error' );
                }
            }).then(function(){
                $.messager.progress('close');
            });
        }
    );
}


function clearJourneyInscriptions(current){
    var row=$('#inscripciones-jornadas').datagrid('getData')['rows'][current];
    if (row.Nombre==='-- Sin asignar --') {
        $.messager.alert('<?php _e("Undeclared"); ?>','<?php _e("Selected journey to clear is empty"); ?>',"warning");
        return false; // no hay ninguna jornada seleccionada para clonar
    }
    $.messager.progress({title:'<?php _e("Clear inscriptions"); ?>',text:'<?php _e("Clearing inscriptions in journey");?>'+"'"+row.Nombre+"'" });
    $.ajax({
        cache: false,
        timeout: 60000, // 60 segundos
        type:'GET',
        url:"../ajax/database/inscripcionFunctions.php",
        dataType:'json',
        data: {
            Prueba: row.Prueba,
            Operation: 'clearinscripciones',
            Jornada: row.ID
        },
        success: function(data) {
            if(data.errorMsg) {
                $.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: data.errorMsg });
            } else {
                $('#inscripciones-datagrid').datagrid('reload');
            }
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            $.messager.alert("Clear Journey inscriptions","Error:"+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus+" - "+errorThrown,'error' );
        }
    }).then(function(){
        $.messager.progress('close');
    });
    return false;
}

function inscribeAllIntoJourney(current){
    var row=$('#inscripciones-jornadas').datagrid('getData')['rows'][current];
    if (row.Nombre==='-- Sin asignar --') {
        $.messager.alert('<?php _e("Undeclared"); ?>','<?php _e("Must declare this journey first"); ?>',"warning");
        return false; // no hay ninguna jornada seleccionada para clonar
    }
    $.messager.progress({title:'<?php _e("Inscribe all"); ?>',text:'<?php _e("Inscribe all dogs into journey");?>'+"'"+row.Nombre+"'" });
    $.ajax({
        cache: false,
        timeout: 60000, // 60 segundos
        type:'GET',
        url:"../ajax/database/inscripcionFunctions.php",
        dataType:'json',
        data: {
            Prueba: row.Prueba,
            Operation: 'populateinscripciones',
            Jornada: row.ID
        },
        success: function(data) {
            if(data.errorMsg) {
                $.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: data.errorMsg });
            } else {
                $('#inscripciones-datagrid').datagrid('reload');
            }
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            $.messager.alert("Inscribe all in journey","Error:"+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus+" - "+errorThrown,'error' );
        }
    }).then(function(){
        $.messager.progress('close');
    });
    return false;
}

function inscribeSelectedIntoJourney(current){
    function doInscribeSelectedIntoJourney(tojourney) {
        $.messager.progress({title:'<?php _e("Inscribe selection"); ?>',text:'<?php _e("Cloning inscriptions from selected journey into ");?>'+"'"+tojourney.Nombre+"'" });
        $.ajax({
            cache: false,
            timeout: 60000, // 60 segundos
            type:'GET',
            url:"../ajax/database/inscripcionFunctions.php",
            dataType:'json',
            data: {
                Prueba: row.Prueba,
                Operation: 'cloneinscripciones',
                From: row.ID,
                Jornada: tojourney.ID
            },
            success: function(data) {
                if(data.errorMsg) {
                    $.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: data.errorMsg });
                } else {
                    $('#inscripciones-datagrid').datagrid('reload');
                }
            },
            error: function(XMLHttpRequest,textStatus,errorThrown) {
                $.messager.alert("Save Team","Error:"+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus+" - "+errorThrown,'error' );
            }
        }).then(function(){
            $.messager.progress('close');
        });
    }

    var row=$('#inscripciones-jornadas').datagrid('getSelected');
    if (!row) {
        $.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no journey selected"); ?>',"warning");
        return false; // no hay ninguna jornada seleccionada para clonar
    }
    if(row.Nombre==='-- Sin asignar --') {
        $.messager.alert('<?php _e("Undeclared"); ?>','<?php _e("Selected journey to clone has no data"); ?>',"warning");
        return false; // no hay ninguna jornada seleccionada para clonar
    }
    var tojourney=$('#inscripciones-jornadas').datagrid('getData')['rows'][current];
    if (tojourney.Nombre==='-- Sin asignar --') {
        $.messager.confirm('<?php _e("Undeclared"); ?>','<?php _e("Selected journey to clone into is not defined. Create?"); ?>',function(r){
            if (!r) return false;
            // create new journey data from original
            var id=tojourney.ID;
            var journey=cloneObj(row);
            journey.ID=id;
            journey.Operation='update';
            journey.Nombre="Clone of "+row.Nombre;
            // update journey info
            $.ajax({
                type: 'GET',
                url: '../ajax/database/jornadaFunctions.php',
                data: journey,
                dataType: 'json',
                success: function (result) {
                    if (result.errorMsg){
                        $.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
                        return false;
                    } else {
                        doInscribeSelectedIntoJourney(journey);
                        $('#inscripciones-jornadas').datagrid('reload');    // reload the prueba data
                    }
                }
            });
        });
        return false; // no hay ninguna jornada seleccionada para clonar, y el usuario aborta operacion
    }
    // arriving here means that destination journey exists and is defined. try to process
    doInscribeSelectedIntoJourney(tojourney);
    return false;
}

/**
 * cambia el dorsal
 * @param idprueba ID de la prueba
 */
function setDorsal() {
	var row = $('#inscripciones-datagrid').datagrid('getSelected');
	if (!row) {
		$.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no inscription(s) selected"); ?>',"warning");
		return; // no hay ninguna inscripcion seleccionada. retornar
	}
	var m=$.messager.prompt(
		'<?php _e("Set dorsal"); ?>',
		'<?php _e("Please type new dorsal<br />If already assigned, <br/>dorsals will be swapped"); ?>',
		function(r) {
			if (!r || isNaN(parseInt(r))) return;
			$.messager.progress({title:'<?php _e("Set dorsal"); ?>',text:'<?php _e("Setting new dorsal...");?>'});
			$.ajax({
				cache: false,
				timeout: 60000, // 60 segundos
				type:'GET',
				url:"../ajax/database/inscripcionFunctions.php",
				dataType:'json',
				data: {
					Prueba: row.Prueba,
					Perro: row.Perro,
					Dorsal: row.Dorsal,
					NewDorsal: parseInt(r),
					Operation: 'setdorsal'
				},
				success: function(data) {
					if(data.errorMsg) {
						$.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: data.errorMsg });
					} else {
						$('#inscripciones-datagrid').datagrid('reload');
					}
				},
                error: function(XMLHttpRequest,textStatus,errorThrown) {
                    $.messager.alert("Set Dorsal","Error:"+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus+" - "+errorThrown,'error' );
                }
			}).then(function(){
                $.messager.progress('close');
            });
		}
	);
    m.find('.messager-input').bind('keypress', function(e) { // accept "Enter" as "OK" button
            if(e.keyCode==13) $('body div.messager-body>div.messager-button').children('a.l-btn:first-child').click();
        }
    );
}
/**
 * Comprueba si una jornada admite inscripciones
 * @param {object} jornada, datos de la jornada
 * @param {string} grado, (opcional) grado del perro
 * @param {string} catguia, (opcional) categoria del guia I(nfantil) J(uvenil) A(dulto) S(senior) P(araagility)
 */
function canInscribe(jornada,grado,catguia) {
	// jornada no definida
    if (jornada.Nombre === '-- Sin asignar --') return false;
    // jornada cerrada
	if (jornada.Cerrada==1) return false;
	if ( (grado===null) || typeof(grado)==="undefined") return true;
    if (grado==='Baja') return false;
    if (grado==='Ret.') return false;
    // preagility solo compite en pre-agility
    if ( (grado==="P.A.") && (jornada.PreAgility==0) ) return false;
    // En jornadas genericas no se comprueba el grado (salvo pre-agility)
    if (jornada.Open!=0) return true;
    if (jornada.Games!=0) return true;
    if (jornada.KO!=0) return true;
    if (jornada.Especial!=0) return true;
    // PENDING: can Grade 1 participate in teams ?
    if (isJornadaEquipos(jornada)) return true;
    // En demas pruebas se comprueba la el grado del perro y -si esta definido- el del guia
    if ( (catguia===null) || typeof(catguia)==="undefined") {
        // en versiones anteriores a 4.5.2 no había categorias para el guia
        if ( (grado==="ch") && (jornada.Children==0) ) return false;
        if ( (grado==="Jr") && (jornada.Junior==0) ) return false;
        if ( (grado==="Sr") && (jornada.Senior==0) ) return false;
        if ( (grado==="GI") && (jornada.Grado1==0) ) return false;
        if ( (grado==="GII") && (jornada.Grado2==0) ) return false;
        if ( (grado==="GIII") && (jornada.Grado3==0) ) return false;
        return true;
    }
    // a partir de la version 4.5.2 tenemos que tener en cuenta la categoria del guia
    if (jornada.Children!=0) { // hay manga infantil
        if (catguia==='I') return true; // si el guia es infantil se puede inscribir
    }
    if (jornada.Junior!=0) { // hay manga juvenil
        if (catguia==='J') return true; // si el guia es juvenil se puede inscribir
        if (catguia==='I') return true; // si el guia es infantil y NO hay manga infantil se puede inscribir
    }
    if (jornada.Senior!=0) { // hay manga senior
        if (catguia==='S') return true;
    }
    if (jornada.ParaAgility!=0) { // manga para-agility
        if (catguia==='P') return true;
    }
    // si guia adulto y perro y grado coinciden, se puede inscribir
    if ( (catguia==='A') && (grado==="GI") && (jornada.Grado1!=0) ) return true;
    if ( (catguia==='A') && (grado==="GII") && (jornada.Grado2!=0) ) return true;
    if ( (catguia==='A') && (grado==="GIII") && (jornada.Grado3!=0) ) return true;
    // si guia infantil o juvenil o senior cuando no hay dichas mangas pero si hay grado 2 (o 3 en senior /pa)
    if ( (catguia==='I') && (grado==="GII") && (jornada.Grado2!=0) ) return true;
    if ( (catguia==='J') && (grado==="GII") && (jornada.Grado2!=0) ) return true;
    if ( (catguia==='S') && (grado==="GII") && (jornada.Grado2!=0) ) return true;
    if ( (catguia==='S') && (grado==="GIII") && (jornada.Grado3!=0) ) return true;
    if ( (catguia==='P') && (grado==="GII") && (jornada.Grado2!=0) ) return true;
    if ( (catguia==='P') && (grado==="GIII") && (jornada.Grado3!=0) ) return true;
    // arriving here means cannot inscribe
    return false;
}

function importExportInscripciones() {
	var cb='<input type="text" id="excel-selClub" class="easyui-combobox" name="excel-selClub" />';
	var options= {
		0: '<?php _e("Generate empty excel inscription template");?>',
		1: '<?php _e("Generate inscription template for club");?>: '+cb,
		2: '*<?php _e("Export current inscriptions to Excel file");?>',
		3: '<?php _e("Import inscriptions from Excel file");?>'
	};
	$.messager.radio(
		'<?php _e('Excel import/export'); ?>',
		'<?php _e('Choose from available operations'); ?>:<br/>&nbsp;<br/>',
		options,
		function(r) {
            var opt=parseInt(r);
		    var club= /* opt==1 */ $('#excel-selClub').combobox('getValue'); // on "--sin asignar--" means print inscriptions
            if (opt==0) club=-1;
            if (opt==2) club=0;
            if (opt!=3) { // export
                $.fileDownload(
                    '../ajax/excel/excelWriterFunctions.php',
                    {
                        httpMethod: 'GET',
                        data: {	Operation: 'Inscripciones', Prueba: workingData.prueba, Club: club },
                        preparingMessageHtml: '<?php _e("Creating Excel file. Please wait"); ?> ...',
                        failMessageHtml: '<?php _e("There was a problem generating your report, please try again"); ?>.'
                    }
                );
            } else { // import
                check_permissions(access_perms.ENABLE_IMPORT, function (res) {
                    if (res.errorMsg) {
                        $.messager.alert('License error','<?php _e("Current license has no Excel import function enabled"); ?>', "error");
                    } else {
                        $('#importdialog').dialog('open');
                    }
                    return false; // prevent default fireup of event trigger
                });
            }
		}
	).window('resize',{width:530});
	$('#excel-selClub').combobox({
		width:165,
		valueField:'ID',
		textField:'Nombre',
		mode:'remote',
		url:'../ajax/database/clubFunctions.php',
		queryParams: {
			Operation:	'enumerate',
			Combo:	1,
			Federation: workingData.federation
		}
	});
	return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Imprime las inscripciones
 * @returns {Boolean} true on success, otherwise false
 */
function printInscripciones() {

    function do_print(r,filas) {
        var where= $('#inscripciones-datagrid-search').val();
        if (where==="<?php _e('-- Search --');?>") where="";
        var dg=$('#inscripciones-datagrid');
        var order= dg.datagrid('options').sortOrder;
        var sort= dg.datagrid('options').sortName;
        if ( (sort==null) || (sort=="" )) { order=""; sort=""; }
        $.fileDownload(
            '../ajax/pdf/print_inscritosByPrueba.php',
            {
                httpMethod: 'GET',
                data: {
                    Prueba: workingData.prueba,
                    Jornada: jornada,
                    Mode: parseInt(r),
                    Filas: filas,
                    page: 0,
                    rows: 0,
                    where: where,
                    sort: sort,
                    order: order
                },
                preparingMessageHtml: '<?php _e("Printing inscriptions. Please wait"); ?> ...',
                failMessageHtml: '<?php _e("There was a problem generating your report, please try again"); ?>.'
            }
        );
        return false;
    }

	// en el caso de que haya alguna jornada seleccionada.
	// anyadir al menu la posibilidad de imprimir solo los inscritos en dicha jornada
    var str='<select id="idg_rows" name="idg_rows" class="easyui-combobox"/><br/>';
	var options= {
        1:'*<?php _e('Catalog'); ?>',
        2:'<?php _e('Statistics'); ?>',
        0:'<?php _e('Simple (raw) listing'); ?>',
        4:'<?php _e('Current (screen) selection/order'); ?>',
        6:'<?php _e('Handlers with more than one dog'); ?>',
        5:'<?php _e('Competition ID Cards'); ?>',
        7:'<?php _e('Post-It Dorsal labels'); ?> '+str
	};
	// buscamos la jornada seleccionada
	var row=$('#inscripciones-jornadas').datagrid('getSelected');
    var jornada=0;
	// si hay jornada seleccionada la anyadimos a la lista
	if (row!==null && row.Nombre!=="-- Sin asignar --") {
		options[3]='<?php _e('Inscriptions for journey'); ?>: "'+row.Nombre+'"';
        jornada=row.ID;
	}
	$.messager.radio(
		'<?php _e('Select form'); ?>',
		'<?php _e('Select type of document to be generated'); ?>:',
		options,
		function(r){
			if (r) {
                var filas=$('#idg_rows').combobox('getValue');
			    setTimeout(do_print(r,filas),0);
			}
			return false ;
		}
	).window('resize',{width:(jornada==0)?325:375});
	$('#idg_rows').combobox({
        width:60,
        panelHeight:100,
        textField:'text',
        valueField:'value',
        data: [
            {text:'3x7',value:'7'},
            {text:'3x8',value:'8',selected:true},
            {text:'3x9',value:'9'},
            {text:'3x10',value:'10'}
        ]
	    });
	return false; //this is critical to stop the click event which will trigger a normal file download!
}
