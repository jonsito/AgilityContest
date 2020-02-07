/*
printer.js

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

/**
 * Funciones relacionadas con gestion de impresos del desarrollo de la competicion
 */


<?php
header('Content-Type: text/javascript');
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

/************************** listado de perros */

/**
 * Imprime el listado de los clubes indicados en #clubes-datagrid
 *@param {string} mode "" (ask), "pdf", "excel"
 */
function print_listaClubes(mode) {

    function do_print(mode) {
        var url='../ajax/pdf/print_listados_db.php';
        if (mode==="excel") url='../ajax/excel/excelWriterFunctions.php';
        var options=$('#clubes-datagrid').datagrid('options');
        $.fileDownload(
            url,
            {
                httpMethod: 'GET',
                data: {
                    Operation: 'Clubes',
                    Federation: workingData.federation, // not used in clubes, but required for excelWriter
                    where:$('#clubes-datagrid-search').val(),
                    sort: options.sortName,
                    order: options.sortOrder,
                    page: 0,
                    rows: 0
                },
                preparingMessageHtml: '(club list) <?php _e("We are preparing your report, please wait"); ?> ...',
                failMessageHtml: '(club list) <?php _e("There was a problem generating your report, please try again."); ?>'
            }
        );
    }

    if (mode==="pdf") { do_print("pdf"); return false; }
    if (mode==="excel") { do_print("excel"); return false; }
    $.messager.radio(
        '<?php _e("Print"); ?>',
        '<?php _e("Create club listing from current selection"); ?>:<br/>',
        {
            0:'*<?php _e("Generate PDF document"); ?>',
            1:'<?php _e("Export to Excel file"); ?>'
        },
        function(r) {
            if (!r) return false;
            switch (parseInt(r)) {
                case 0: do_print("pdf"); break;
                case 1: do_print("excel"); break;
            }
        }
    );
    return false;
}

/**
 * Imprime el listado de los perros registrados en el orden especificado por #perros-datagrid
 */
function print_listaPerros(mode) {
    var url='../ajax/pdf/print_listados_db.php';
    if (mode==="excel") url='../ajax/excel/excelWriterFunctions.php';
    var options=$('#perros-datagrid').datagrid('options');
    $.fileDownload(
        url,
        {
            httpMethod: 'GET',
            data: {
                Operation: 'Dogs',
                Federation: workingData.federation,
                where:$('#perros-datagrid-search').val(),
                sort: options.sortName,
                order: options.sortOrder,
                page: 0,
                rows: 0
            },
            preparingMessageHtml: '(dog list) <?php _e("We are preparing your report, please wait"); ?> ...',
            failMessageHtml: '(dog list) <?php _e("There was a problem generating your report, please try again."); ?>'
        }
    );
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/************************** programa de actividades **********/

/**
 * Imprime la secuencia de tandas de la jornada
 */
function print_ordenTandas(comments) {
    $.fileDownload(
        '../ajax/pdf/print_ordenTandas.php',
        {
            httpMethod: 'GET',
            data: {
                Prueba: workingData.prueba,
                Jornada: workingData.jornada,
                Comentarios: comments
            },
            preparingMessageHtml:'(rounds order) <?php _e("We are preparing your report, please wait"); ?> ...',
            failMessageHtml: '(rounds order) <?php _e("There was a problem generating your report, please try again."); ?>'
        }
    );
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Imprime el programa de entrenamientos
 * @param {string} mode "pdf" "excel" "csv"
 */
function print_entrenamientos(mode) {
	var url='../ajax/pdf/print_entrenamientos.php';
	if (mode==="excel") url='../ajax/excel/excelWriterFunctions.php';
	$.fileDownload(
		url,
		{
			httpMethod: 'GET',
			data: {
			    Operation: 'TrainingTable',
				Federation: workingData.federation,
				Prueba: workingData.prueba
			},
			preparingMessageHtml:'(training table) <?php _e("We are preparing your report, please wait"); ?> ...',
			failMessageHtml: '(training table) <?php _e("There was a problem generating your report, please try again."); ?>'
		}
	);
	return false; //this is critical to stop the click event which will trigger a normal file download!
}

/************************** impresion del orden de salida **********/

/**
 * Generate printouts in TeamBest mode grouping team members
 */
function print_team3as4() {
    $.messager.radio(
        '<?php _e("Print grouped"); ?>',
        '<?php _e("Print data in group (all team members together) mode");?><br/>&nbsp;<br/>'+
        '<?php _e("Select option "); ?>:',
        {
            0:'*<?php _e("Starting order"); ?>',
            1:'<?php _e("Assistant sheets"); ?>'
        },
        function(r){
            if (!r) return false;
            switch(parseInt(r)){
                case 0: return print_ordenSalida('+',0,'0-9999','');
                case 1: return print_asistenteEquipos('-',0,'9999',''/*,cabecera*/);
            }
        }
    ).window('resize',{width:450});
    return false;
}

/**
 * manda a la impresora el orden de salida
 * @param {string} cats categorias a imprimir (-,L,M,S,T,+) Note: '+' means force team 4 mode
 * @param {boolean} excel false:PDF true:Excel
 * @param {string} rango lista de perros a imprimir ( turnos de reconocimiento )
 * @param {string} comentarios texto extra a anyadir a la cabecera
 * @returns {Boolean}
 *
 */
function print_ordenSalida(cats,excel,rango,comentarios) {
    var url='../ajax/pdf/print_ordenDeSalida.php';
	if (excel) url='../ajax/excel/excelWriterFunctions.php';
    $.fileDownload(
        url,
        {
            httpMethod: 'GET',
            data: {
                Operation: 'OrdenSalida',
                Prueba: workingData.prueba,
                Jornada: workingData.jornada,
                Manga: workingData.manga,
				Categorias: (cats==='+')?'-':cats,
				Excel: excel,
				EqConjunta: isJornadaEqConjunta()?1:((cats==='+')?1:0),
                JornadaKO: isJornadaKO()?1:0,
                JornadaGames: isJornadaGames()?1:0,
                Rango: rango,
                Comentarios: comentarios
            },
            preparingMessageHtml: '(Starting order) <?php _e("We are preparing your report, please wait"); ?> ...',
            failMessageHtml: '(Starting order) <?php _e("There was a problem generating your report, please try again."); ?>'
        }
    );
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/*************************** impresion de la hoja de calculo de TRS ********/

/**
 * Prints TRS templates
 * @param mode // 0: Distance/Velocity=TRS dataSheet 1: TRS List template
 * @returns {boolean} False to avoid key binding event chaining
 */
function print_trsTemplates(mode) {
    $.fileDownload(
        '../ajax/pdf/print_trsTemplates.php',
        {
            httpMethod: 'GET',
            data: {
                Prueba: workingData.prueba,
                Jornada: workingData.jornada,
                Manga: workingData.manga,
                Mode: mode
            },
            preparingMessageHtml: '(sct template) <?php _e("We are preparing your report, please wait"); ?> ...',
            failMessageHtml: '(sct template) <?php _e("There was a problem generating your report, please try again."); ?>'
        }
    );
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/************************** Hojas del asistente del juez ****************/
/**
 * Imprime formularios del asistente en formato standard ( 1,5,10,15 perros/pagina )
 * @param pagemode 1,5,10,15 perros por pagina
 * @param cats categorias a mostrar
 * @param fill indica si rellenar datos con resultados
 * @param rango rango de perros a imprimir
 * @param comentarios texto a pintar bajo la cabecera de la pagina
 * @param empty en juegos, imprimir hoja vacia
 * @returns {boolean}
 */

function print_asistente(pagemode,cats,fill,rango,comentarios,empty) {
    $.fileDownload(
        '../ajax/pdf/print_entradaDeDatos.php',
        {
            httpMethod: 'GET',
            data: {
                Prueba: workingData.prueba,
                Jornada: workingData.jornada,
                Manga: workingData.manga,
				Categorias: cats,
                Mode: pagemode,
                FillData:(fill===true)?1:0,
                Rango:rango,
                EqConjunta: isJornadaEqConjunta()?1:0,
                JornadaKO: isJornadaKO()?1:0,
                JornadaGames: isJornadaGames()?1:0,
                Comentarios:comentarios,
                EmptyPage: (empty===true)?1:0
            },
            preparingMessageHtml:'(assistant sheets) <?php _e("We are preparing your report, please wait"); ?> ...',
            failMessageHtml:'(assistant sheets) <?php _e("There was a problem generating your report, please try again."); ?>'
        }
    );
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * En las pruebas KO se imprimen 16 perros por pagina, agrupados de dos en dos
 */
function print_asistenteKO(cats,fill,rango,comentarios,cabecera) {
    if (typeof(cabecera)==="undefined") cabecera='<?php _("Data entry");?>';
    return print_asistente(16,cats,fill,rango,comentarios,cabecera,false);
}

/**
 * En snooker / gambler la hoja no da para imprimir más que 8 entradas
 */
function print_asistenteGames(cats,fill,rango,comentarios,cabecera,empty) {
    if (typeof(cabecera)==="undefined") cabecera='<?php _("Data entry");?>';
    return print_asistente(8,cats,fill,rango,comentarios,cabecera,empty);
}

/**
 * En pruebas de equipos 4 conjunta se ofrece la opción de usar una única entrada para el equipo
 */
function print_asistenteEquipos(cats,fill,rango,comentarios,cabecera) {
    if (typeof(cabecera)==="undefined") cabecera='<?php _("Data entry");?>';
    $.fileDownload(
        '../ajax/pdf/print_entradaDeDatosEquipos4.php',
        {
            httpMethod: 'GET',
            data: {
                Prueba: workingData.prueba,
                Jornada: workingData.jornada,
                Manga: workingData.manga,
				Categorias: cats,
                FillData:(fill)?1:0,
                Rango:rango,
                Comentarios:comentarios,
                Title: cabecera
            },
            preparingMessageHtml: '(assistant team sheets) <?php _e("We are preparing your report, please wait"); ?> ...',
            failMessageHtml:'(assistant team sheets) <?php _e("There was a problem generating your report, please try again."); ?>'
        }
    );
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/********************** impresion de datos parciales ***************/
function importExportParcial(recorrido) {
    var value=parseInt(recorrido);
    var mode=getMangaMode(workingData.datosPrueba.RSCE,workingData.datosManga.Recorrido,value);
    $.messager.radio(
        '<?php _e("Import/Export"); ?>',
        '<?php _e("Import/Export partial scores from/to Excel file"); ?>:<br/>&nbsp;<br/>',
        {
            0:'*<?php _e("Create Excel file with current round results"); ?>',
            1:'<?php _e("Import partial scores on this round from Excel file"); ?>'
        },
        function(r){
            if (!r) return false;
            switch(parseInt(r)){
                case 0:
                    $.fileDownload(
                        '../ajax/excel/excelWriterFunctions.php',
                        {
                            httpMethod: 'GET',
                            data: {
                                Prueba: workingData.prueba,
                                Jornada: workingData.jornada,
                                Manga: workingData.manga,
                                Mode: mode,
                                Operation: 'PartialScores'
                            },
                            preparingMessageHtml: '(Excel partial scores) <?php _e("We are preparing your report, please wait"); ?> ...',
                            failMessageHtml: '(Excel partial scores) <?php _e("There was a problem generating your report, please contact author."); ?>'
                        }
                    );
                    break;
                case 1:
                    // import
                    check_permissions(access_perms.ENABLE_IMPORT, function (res) {
                        if (res.errorMsg) {
                            $.messager.alert('License error','<?php _e("Current license has no Excel import function enabled"); ?>', "error");
                        } else {
                            if (parseInt(workingData.datosJornada.Cerrada)!==0) {
                                $.messager.alert(
                                    "<?php _e('Not allowed');?>",
                                    "<?php _e('Cannot import round results in a closed journey');?>",
                                    "error"
                                );
                                return;
                            }
                            $('#resultadosmanga-excel-dialog').dialog('open');
                        }
                        return false; // prevent default fireup of event trigger
                    });
                    break;
            }
        }
        ).window('resize',{width:500});
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

function print_parcial(mode) {
    var mergestyle="none";
    // temporary fix
    if ( (howManyHeights()==5) && ([0,1,2,5,9].includes(mode) ) ) { mergestyle="inline-block"; }
    var merge='<br/><span style="display:inline-block;width:100%">'+
            '<span id="pp_merge_span" style="display:'+mergestyle+';float:right">'+
            '<label id="pp_mergecatsLbl" for="pp_mergecats"><?php _e('Combine sub-categories'); ?> </label>'+
            '<select id="pp_mergecats" style="width:125px" name="pp_mergecats" class="easyui-combobox"'+
            ' data-options="panelHeight:\'auto\'" >'+
            '<option value="0" selected="selected"><?php _e('Separate listings');?></option>'+
            '<option value="3"><?php _e('XL+L / M / S+XS');?></option>'+
            '<option value="1"><?php _e('XL+L / M+S+XS');?></option>'+
            '<option value="2"><?php _e('Single listing');?></option>'+
            '</select>'+
            '</span></span>';
    var title='<span id="pp_header">'+
        '<br/>&nbsp;<br/><?php _e("Header title");?>: '+
        '<input id="pp_headertitle" class="easyui-textbox" type="text" value="<?php _e("Partial scores"); ?>"/>'+
        '</span>';
    var msgs=  {
        0: '*<?php _e("Create PDF Report for selected series");?>',
        2: '<?php _e("Print filled assistant sheets 10 dogs/pages"); ?>',
        3: '<?php _e("Print filled assistant sheets 15 dogs/pages"); ?>',
        6: '<?php _e("Create PDF Report for all series");?>'+merge+title
    };
    if (isJornadaKO()) msgs={
        0: '*<?php _e("Create PDF Report");?>',
        4: '<?php _e("Print filled assistant sheets 16 dogs/pages"); ?>'
    };
    if (isJornadaEqMejores()) msgs= {
        0: '*<?php _e("Create PDF (teams) Report");?>',
        1: '<?php _e("Create PDF (individual) Report");?>',
        2: '<?php _e("Print filled assistant sheets 10 dogs/pages"); ?>',
        3: '<?php _e("Print filled assistant sheets 15 dogs/pages"); ?>'+title
    }
    $.messager.radio(
        '<?php _e("Partial scores"); ?>',
        '<?php _e("Select output format"); ?>:',
        msgs,
        function (r) {
            if (!r) return false;
            var t= $('#pp_headertitle').textbox('getText');
            var url="";
            var global=0;
            switch (parseInt(r)) {
                case 6:
                    global=1;
                    // no break;
                case 0: // create pdf
                    // generic, ko, games
                    url = '../ajax/pdf/print_resultadosByManga.php'; // default
                    // since 4.2.x Equipos3/Equipos4 becomes mindogs/maxdogs for team journeys
                    minmax=getTeamDogs();
                    if (minmax[0]>1) {
                        if (minmax[0]!=minmax[1]) url = '../ajax/pdf/print_resultadosByEquipos.php'; // team best
                        else url = '../ajax/pdf/print_resultadosByEquipos4.php'; // team All
                    }
                    // no break
                case 1: // on x-of-y best team contests extra option to print individual results
                    if (url==="") url = '../ajax/pdf/print_resultadosByManga.php';
                    $.fileDownload(
                        url,
                        {
                            httpMethod: 'GET',
                            data: {
                                Prueba: workingData.prueba,
                                Jornada: workingData.jornada,
                                Manga: workingData.manga,
                                Mode: mode,
                                Merge: $('#pp_mergecats').combobox('getValue'),
                                Global: global,
                                Operation: 'print',
                                Title: t
                            },
                            preparingMessageHtml: '(partial scores) <?php _e("We are preparing your report, please wait"); ?> ...',
                            failMessageHtml: '(partial scores) <?php _e("There was a problem generating your report, please contact author."); ?>'
                        }
                    );
                    break;
                case 2: // filled normal rounds 10 dogs/page
                    print_asistente(10, modeToCats(mode), true,"1-99999","",false);
                    break;
                case 3: // filled normal rounds 15 dogs/page
                    print_asistente(15, modeToCats(mode), true,"1-99999","",false);
                    break;
                case 4: // filled ko assistant sheets
                    print_asistente(16, modeToCats(mode), true,"1-99999","",false);
                    break;
                case 5: // create excel file
                    $.fileDownload(
                        '../ajax/excel/excelWriterFunctions.php',
                        {
                            httpMethod: 'GET',
                            data: {
                                Prueba: workingData.prueba,
                                Jornada: workingData.jornada,
                                Manga: workingData.manga,
                                Mode: mode,
                                Merge: $('#pp_mergecats').combobox('getValue'),
                                Operation: 'PartialScores',
                                Title: t
                            },
                            preparingMessageHtml: '(Excel partial scores) <?php _e("We are preparing your report, please wait"); ?> ...',
                            failMessageHtml: '(Excel partial scores) <?php _e("There was a problem generating your report, please contact author."); ?>'
                        }
                    );
                    break;
                // PENDING: add filled sheet for snooker/gambler
            }
            return false; // return false to prevetn event keyboard chaining
        }).window('resize', {width: 450});
    $('#pp_mergecats').combobox({});
    $('#pp_headertitle').textbox({required: true, validType: 'length[1,255]'});
    if (isJornadaKO()) $('#pp_header').css('display','none'); // do not show edit header option in KO journeys
    return false;
}

/**
 * imprime los resultados de la manga/categoria solicitadas
 * @param {integer} recorrido 0:L 1:M 2:S 3:T 4:X
 */
function checkAndPrintParcial(recorrido) {
	var value=parseInt(recorrido); // stupid javascript!!
	// obtenemos informacion sobre los datos a imprimir
	var mode=getMangaMode(workingData.datosPrueba.RSCE,workingData.datosManga.Recorrido,value);
	$.ajax({
		type:'GET',
		url:"../ajax/database/resultadosFunctions.php",
		dataType:'json',
		data: {	Operation: 'getPendientes', Prueba: workingData.prueba, Jornada: workingData.jornada, Manga: workingData.manga, Mode: mode },
		success: function(data) {
			if (parseInt(data['total'])==0) {
				// No hay perros pendientes de salir: imprimimos los datos de la manga y categoria solicitada
				print_parcial(mode);
			} else {
				var str='<h3><?php _e("Dogs with pending data to be entered"); ?>:</h3>';
				str +="<table><tr><th><?php _e('Dorsal'); ?></th><th><?php _e('Dog');?></th><th><?php _e('Handler');?></th><th>Club</th></tr>";
				// componemos mensaje de error
				$.each(
					data['rows'],
					function(index,val) {
						str+="<tr><td>"+val['Dorsal']+"</td><td>"+val['Nombre']+"</td><td>"+val['NombreGuia']+"</td><td>"+val['NombreClub']+"</td></tr>";
					}
				);
				str+="</table><br /><?php _e('Print anyway'); ?>?";
				var w=$.messager.confirm({
                    padding:'5px',
                    width:640,
                    height:400,
                    maxHeight:400, // PENDING: this doesn't work
				    title: '<?php _e('Incomplete data'); ?>',
                    msg: str,
                    fn: function(r){if (r) print_parcial(mode);}
                });
				w.window('center').css('overflow-y','auto')
			}
		}
	});
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/******************** Entrada comun para las operaciones de impresion del desarrollo de la jornada *******/

function print_performCommonDesarollo() {
    var oper= parseInt ($('input[name=printer_dialog-option]:checked').val() );
    var comments=$('#printer_dialog-comments').textbox('getValue');
    var range=$('#printer_dialog-range').textbox('getValue');
    var row= $('#competicion-listamangas').datagrid('getSelected'); // look for round selected
    var cats=$('#printer_dialog-cats').val();
    switch(oper){
        case 0: print_ordenTandas(comments); return false; // programa de la jornada
        case 1: if (!row) break; print_ordenSalida(cats,false,range,comments); return false; //orden de salida pdf
        case 2: if (!row) break; print_ordenSalida(cats,true,range,comments); return false; // orden de salida excel
        case 3: print_trsTemplates(0); return false; // tabla trs/trm
        case 4: print_trsTemplates(1); return false; // hoja de anotacion de trs/trm
        case 5: // hoja de asistente vacia
            if ( !isMangaGames() )  { print_trsTemplates(2); return false; } // normal empty template
            if (row) print_asistenteGames(cats,false,range,comments,true) // snooker, gambler empty template
            return false;
        case 6: if (!row) break; print_asistente(1,cats,false,range,comments,false); return false; // hojas asistente 1 perro/pag
        case 7: if (!row) break; print_asistente(5,cats,false,range,comments,false); return false; // hojas asistente 5 perros/pag
        case 8: if (!row) break; print_asistente(15,cats,false,range,comments,false); return false; // hojas asistente 15 perros/pag
        case 10: if (!row) break; print_asistente(10,cats,false,range,comments,false); return false; // hojas asistente 10 perros/pag
        case 9: if (!row) break; print_asistenteEquipos(cats,false,range,comments); return false; // hojas asistente team4 conjunta
        case 11: if (!row) break; print_asistenteKO(cats,false,range,comments); return false; // hojas asistente manga KO
        case 12: if (!row) break; print_asistenteGames(cats,false,range,comments,false); return false; // hojas asistente juegos
    }
    // arriving here means round required but not selected. notify and abort
    $.messager.alert('<?php _e('Error'); ?>','<?php _e('There is no selected round'); ?>','error');
    return false; // no hay ninguna manga seleccionada. retornar
}

function print_changeSelection(val) {
    var cats=$('#printer_dialog-cats').val();
    var rangeobj=$('#printer_dialog-range');
    switch (val) {
        case 0: case 2: case 3: case 4: case 5: rangeobj.textbox('disable');  break;
        // if cat selected enable range selection on starting order or assistant pages
        case 1: case 6: case 7: case 8: case 9: rangeobj.textbox( (cats==='-')?'disable':'enable'); break;
    }
}

/**
 * Open competition printer dialog and select options
 * @param {int} def default option
 * @param {string} cb (optional: nombre del combobox de donde obtener la categoria
 * @returns {boolean} False to avoid key binding event chaining
 */
function print_commonDesarrollo(def,cb) {
	var cats='-';
	var catstr='';
	if (typeof(cb)!=="undefined") {
		cats=$(cb).combobox('getValue');
		if (cats!=="-") catstr="<?php _e("Selected category");?>: "+$(cb).combobox('getText')+" <br />";
	}
	var rangeobj=$('#printer_dialog-range');
	// prepare dialog
    switch(parseInt(def)) {
        case 0: case 2: case 3: case 4: case 5: rangeobj.textbox('disable');  break;
        // if cat selected enable range selection on starting order or assistant pages
        case 1: case 6: case 7: case 8: case 9: case 10: rangeobj.textbox( (cats==='-')?'disable':'enable'); break;
    }
    $('#printer_dialog-cats').val(cats);
	$('#printer_dialog-currentcat').html(catstr);
    $('#printer_dialog-team4').css('display',isJornadaEqConjunta()?'inherit':'none');
    $('#printer_dialog-ko').css('display',isJornadaKO()?'inherit':'none');
    $('#printer_dialog-games').css('display',isMangaGames()?'inherit':'none');
	$('#printer_dialog-option'+def.toString()).prop('checked',true);
	$('#printer_dialog-comments').textbox('setValue','');
	rangeobj.textbox('setValue','1-99999'); // on window open default is print all
	// and finally open
    $('#printer_dialog-dialog').dialog('open');
    return false; //this is critical to stop the click event which will trigger a normal file download!
}


/******************************** Datos de clasificacion general **********************/

/**
 * Imprime Podium (1) o clasificacion General (0)
 *
 */
function clasificaciones_printGlobal(podium,merge) {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='../ajax/pdf/print_podium.php';
	var msgtag=(podium==0)?"( Global )":"( Podium )";
    if (isJornadaEquipos()) url='../ajax/pdf/print_podium_equipos.php';
	if (ronda==null) {
    	$.messager.alert('<?php _e("Error"); ?>','<?php _e("There is no selected round on this journey"); ?>',"warning");
    	return false; // no way to know which ronda is selected
	}
	$.fileDownload(
		url,
		{
			httpMethod: 'GET',
			data: { 
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
                Podium: podium,
                Merge: merge
			},
	        preparingMessageHtml:msgtag+' <?php _e("We are preparing your report, please wait"); ?> ...',
	        failMessageHtml:msgtag+' <?php _e("There was a problem generating your report, please try again."); ?>'
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Imprime los resultados finales en formato excel separados por categoria y grado, tal y como pide la RSCE
 */
function clasificaciones_printCanina() {
	
	// Server-side excel generation
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='../ajax/pdf/print_clasificacion_excel.php';
	var mode=$('#resultados-selectCategoria').combobox('getValue');
	if (ronda==null) {
    	$.messager.alert('<?php _e("Error"); ?>','<?php _e("There is no selected round on this journey"); ?>',"warning");
    	return false; // no way to know which ronda is selected
	}
	$.fileDownload(
		url,
		{
			httpMethod: 'GET',
			data: { 
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
	        preparingMessageHtml:'(excel) <?php _e("We are preparing your report, please wait"); ?> ...',
	        failMessageHtml: '(excel) <?php _e("There was a problem generating your report, please contact the author."); ?>'
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Imprime los resultados finales de la ronda seleccionada en formato CSV para su conversion en etiquetas
 * @param {int} flag 0:CSV 1:PDF
 * @param {int} start if mode==PDF first line in output
 * @param {string} list CSV dorsal list
 * @param {boolean} discriminate on set discriminate by country
 * @param {boolean} global true:all series false:just selected
 * @returns {Boolean} false 
 */
function clasificaciones_printEtiquetas(flag,start,list,discriminate,global) {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='../ajax/pdf/print_etiquetas_csv.php';
	if (flag!=0) url='../ajax/pdf/print_etiquetas_pdf.php';
	var mode=$('#resultados-selectCategoria').combobox('getValue');
	var strt=parseInt(start)-1;
	if (ronda==null) {
        $.messager.alert('<?php _e("Error"); ?>','<?php _e("There is no selected round on this journey"); ?>',"warning");
    	return false; // no way to know which ronda is selected
	}
	$.fileDownload(
		url,
		{
			httpMethod: 'GET',
			data: { 
				Prueba:workingData.prueba,
				Jornada:workingData.jornada,
                // en version 4.2.x+ en grado1 rsce puede haber tres mangas :-(
				Manga1:ronda.Manga1,
                Manga2:ronda.Manga2,
                Manga3:ronda.Manga3,
				Rondas: ronda.Rondas,
				Mode: mode,
				Start: strt,
				List: list,
                Global: (global)?1:0,
                PrintMode: flag, /* 0:csv 1:rsce label 2:cneac forms */
                Discriminate: (discriminate)?1:0 /* false: don't, true: exclude by country membership */
			},
	        preparingMessageHtml: '(labels) <?php _e("We are preparing your report, please wait"); ?> ...',
	        failMessageHtml: '(labels) <?php _e("There was a problem generating your report, please try again."); ?>'
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
}


/**
 * Imprime una tabla con los mejores perros del global de
 * las jornadas de la prueba por categorias, grados, y conjuntas
 * Se ignoran infantil, junior, senior y pre-agility
 * @return false
 */
function clasificaciones_printHallOfFame() {
    var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
    var url='../ajax/pdf/print_hall_of_fame.php';
    $.fileDownload(
        url,
        {
            httpMethod: 'GET',
            data: {
                Prueba:workingData.prueba,
            },
            preparingMessageHtml: '(scores) <?php _e("We are preparing your report, please wait"); ?> ...',
            failMessageHtml:'(scores) <?php _e("There was a problem generating your report, please try again."); ?>'
        }
    );
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Imprime los resultados finales de la ronda seleccionada en formato pdf
 * @param {int} stats 0..1 Also print statistics
 * @param {int} children 0..1  On RFEC Junior rounds, print separate sheets for Children/Junior
 * @param {int} force 0..1  On team-3 force print of individual scores
 * @return false
 */
function clasificaciones_printClasificacion(stats,children,force) {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='../ajax/pdf/print_clasificacion.php';
    if (isJornadaEqMejores() && (force==0)) url='../ajax/pdf/print_clasificacion_equipos.php';
    if (isJornadaEqConjunta()) {
        stats=0; // no sense in equipos conjunta
        url='../ajax/pdf/print_clasificacion_equipos.php';
    }
	var mode=$('#resultados-selectCategoria').combobox('getValue');
	if (ronda==null) {
        $.messager.alert('<?php _e("Error"); ?>','<?php _e("There is no selected round on this journey"); ?>',"warning");
    	return false; // no way to know which ronda is selected
	}
	$.fileDownload(
		url,
		{
			httpMethod: 'GET',
			data: { 
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
				Mode: mode,
                Stats: stats,
                Children: children
			},
	        preparingMessageHtml: '(scores) <?php _e("We are preparing your report, please wait"); ?> ...',
	        failMessageHtml:'(scores) <?php _e("There was a problem generating your report, please try again."); ?>'
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Ajusta el menu de seleccion de metodo de impresion en funcion de la opcion seleccionada
 */
function r_selectOption(val) {
    var prfirst= $('#r_prfirst');
    var prlist=$('#r_prlist');
	switch (parseInt(val)) {
	case 0: // podium
	case 1: // csv
	case 3: // excel
	case 4: // pdf
    case 8: // global scores for every heihgts
    case 6: // individual pdf on teams-3
        prfirst.numberspinner('disable'); prlist.textbox('disable'); break;
	case 2: // etiquetas rsce
	    prfirst.numberspinner('enable'); prlist.textbox('enable'); break;
	case 5: // etiquetas cneac
	    prfirst.numberspinner('disable'); prlist.textbox('enable'); break;
	}
}

/**
 * Presenta un menu al usuario indicando que es lo que se quiere imprimir
 */
function clasificaciones_doPrint() {
	var r=$('input:radio[name="r_prformat"]:checked').val();
	var line=$('#r_prfirst').numberspinner('getValue');
    var list=$('#r_prlist').textbox('getValue');
    var prstats=$('#r_prstats').prop('checked');
    var discriminate=$('#r_discriminate').prop('checked');
    var children=$('#r_children').prop('checked');
    var global=$('#r_global').prop('checked');
    var merge=$('#r_mergecats').combobox('getValue');
	$('#resultados-printDialog').dialog('close');
	switch(parseInt(r)) {
		case 0: /* podium */ clasificaciones_printGlobal(1); break;
		case 1: /* csv */ clasificaciones_printEtiquetas(0,line,'',false,global); break; // csv
        case 3: /* excel */ clasificaciones_printCanina(); break;
        case 6: /* mejores prueba */ clasificaciones_printHallOfFame(); break;
        case 7: /* individual on team3 */  clasificaciones_printClasificacion((prstats)?1:0,(children)?1:0,1); break;
        case 8: /* Global del grado */ clasificaciones_printGlobal(0,merge); break;
		case 4: /* pdf */ clasificaciones_printClasificacion((prstats)?1:0,(children)?1:0,0); break;
		case 5: /* forms cneac */ clasificaciones_printEtiquetas(2,line,list,discriminate,global); break;
		case 2: /* labels rsce */ clasificaciones_printEtiquetas(1,line,list,discriminate,global); break;
	}
	return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * presenta el menu de impresión de datos de ligas de competicion Excel/PDF
 */
function printLeague() {

    // PENDING: add pdf/excel selector

    var tt=$('#ligas-tab');
    var tab=tt.tabs('getSelected');
    var index=tt.tabs('getTabIndex',tab);
    $.fileDownload(
        '../ajax/pdf/print_ligas.php',
        {
            httpMethod: 'GET',
            data: {
                Operation: 'shortData',
                Grado:  (index==0)?"GI":(index==1)?"GII":"GIII",
                Perro:  0, // in long mode show results by perro
                Federation: workingData.datosFederation.ID
            },
            preparingMessageHtml:'(pdf) <?php _e("We are preparing your report, please wait"); ?> ...',
            failMessageHtml: '(pdf) <?php _e("There was a problem generating your report, please contact the author."); ?>'
        }
    );
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

function printLeagueByDog() {

    // PENDING: add pdf/excel selector

    $.fileDownload(
        '../ajax/pdf/print_ligas.php',
        {
            httpMethod: 'GET',
            data: {
                Operation: 'longData',
                Perro:  $('#ligas-perro-ID').val(), // hidden input field,
                Grado:  $('#ligas-perro-Grado').textbox('getValue'), // textbox
                Federation: workingData.datosFederation.ID
            },
            preparingMessageHtml:'(pdf) <?php _e("We are preparing your report, please wait"); ?> ...',
            failMessageHtml: '(pdf) <?php _e("There was a problem generating your report, please contact the author."); ?>'
        }
    );
    return false; //this is critical to stop the click event which will trigger a normal file download!
}