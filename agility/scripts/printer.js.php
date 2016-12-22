/*
printer.js

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

/**
 * Funciones relacionadas con gestion de impresos del desarrollo de la competicion
 */


<?php
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

/************************** listado de perros */

/**
 * Imprime el listado de los perros registrados en el orden especificado por #perros-datagrid
 */
function print_listaPerros(mode) {
    var url='/agility/server/pdf/print_listaPerros.php';
    if (mode==="excel") url='/agility/server/excel/dog_writer.php';
    var options=$('#perros-datagrid').datagrid('options');
    $.fileDownload(
        url,
        {
            httpMethod: 'GET',
            data: {
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
function print_ordenTandas() {
    $.fileDownload(
        '/agility/server/pdf/print_ordenTandas.php',
        {
            httpMethod: 'GET',
            data: {
                Prueba: workingData.prueba,
                Jornada: workingData.jornada
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
	var url='/agility/server/pdf/print_entrenamientos.php';
	if (mode==="excel") url='/agility/server/excel/trainingtable_writer.php';
	$.fileDownload(
		url,
		{
			httpMethod: 'GET',
			data: {
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
 * manda a la impresora el orden de salida
 * @param cats lista de categorias a imprimir
 * @param excel lista de categorias a imprimir
 * @returns {Boolean}
 */
function print_ordenSalida(cats,excel) {
    var url='/agility/server/pdf/print_ordenDeSalida.php';
    if (isJornadaEqConjunta()) url='/agility/server/pdf/print_ordenSalidaEquipos4.php';
	if (excel) url='/agility/server/excel/print_ordenSalidaExcel.php';
    $.fileDownload(
        url,
        {
            httpMethod: 'GET',
            data: {
                Prueba: workingData.prueba,
                Jornada: workingData.jornada,
                Manga: workingData.manga,
				Categorias: cats,
				Excel: excel,
				EqConjunta: isJornadaEqConjunta()?1:0
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
        '/agility/server/pdf/print_trsTemplates.php',
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
function print_asistente(pages,cats) {
    $.fileDownload(
        '/agility/server/pdf/print_entradaDeDatos.php',
        {
            httpMethod: 'GET',
            data: {
                Prueba: workingData.prueba,
                Jornada: workingData.jornada,
                Manga: workingData.manga,
				Categorias: cats,
                Mode: pages
            },
            preparingMessageHtml:'(assistant sheets) <?php _e("We are preparing your report, please wait"); ?> ...',
            failMessageHtml:'(assistant sheets) <?php _e("There was a problem generating your report, please try again."); ?>'
        }
    );
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * En pruebas de equipos 4 conjunta se ofrece la opción de usar una única entrada para el equipo
 */
function print_asistenteEquipos(cats) {
    $.fileDownload(
        '/agility/server/pdf/print_entradaDeDatosEquipos4.php',
        {
            httpMethod: 'GET',
            data: {
                Prueba: workingData.prueba,
                Jornada: workingData.jornada,
                Manga: workingData.manga,
				Categorias: cats
            },
            preparingMessageHtml: '(assistant team sheets) <?php _e("We are preparing your report, please wait"); ?> ...',
            failMessageHtml:'(assistant team sheets) <?php _e("There was a problem generating your report, please try again."); ?>'
        }
    );
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/********************** impresion de datos parciales ***************/

function print_parcial(mode) {
    var url='/agility/server/pdf/print_resultadosByManga.php';
    if ( parseInt(workingData.datosJornada.Equipos3)!=0)
        url='/agility/server/pdf/print_resultadosByEquipos.php';
    if ( parseInt(workingData.datosJornada.Equipos4)!=0)
        url='/agility/server/pdf/print_resultadosByEquipos4.php';
	$.fileDownload(
		url,
		{
			httpMethod: 'GET',
			data: { 
				Prueba: workingData.prueba,
				Jornada: workingData.jornada,
				Manga: workingData.manga,
				Mode: mode,
				Operation: 'print'
			},
	        preparingMessageHtml: '(partial scores) <?php _e("We are preparing your report, please wait"); ?> ...',
	        failMessageHtml:'(partial scores) <?php _e("There was a problem generating your report, please try again."); ?>'
		}
	);
}

/**
 * imprime los resultados de la manga/categoria solicitadas
 * @param val 0:L 1:M 2:S 3:T
 */
function checkAndPrintParcial(val) {
	var value=parseInt(val); // stupid javascript!!
	// obtenemos informacion sobre los datos a imprimir
	var mode=getMangaMode(workingData.datosPrueba.RSCE,workingData.datosManga.Recorrido,value);
	$.ajax({
		type:'GET',
		url:"/agility/server/database/resultadosFunctions.php",
		dataType:'json',
		data: {	Operation:'getPendientes', Prueba:workingData.prueba, Jornada:workingData.jornada, Manga:workingData.manga, Mode: mode },
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
				var w=$.messager.confirm('<?php _e('Invalid data'); ?>',str,function(r){if (r) print_parcial(mode);});
				w.window('resize',{width:550}).window('center');
			}
		}
	});
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/******************** Entrada comun para las operaciones de impresion del desarrollo de la jornada *******/

/**
 * Prints TRS templates
 * @param {int} def default option
 * @param {string} cb (optional: nombre del combobox de donde obtener la categoria
 * @returns {boolean} False to avoid key binding event chaining
 */
function print_commonDesarrollo(def,cb) {
    var options= {
        0:((def==0)?'*':'')+'<?php _e('Activities and timetable on this journey'); ?>',
		1:((def==1)?'*':'')+'<?php _e('Starting order on this round'); ?> - PDF',
		2:((def==2)?'*':'')+'<?php _e('Starting order on this round'); ?> - Excel<br/>',
        3:((def==3)?'*':'')+'<?php _e('SheetCalc to evaluate SCT and MCT'); ?>',
        4:((def==4)?'*':'')+'<?php _e('Sheet to anotate data on rounds'); ?>',
        5:((def==5)?'*':'')+'<?php _e('Judge assistant empty sheet'); ?><br/>',
        6:((def==6)?'*':'')+'<?php _e('Judge assistant sheets (1 dog/page)'); ?>',
        7:((def==7)?'*':'')+'<?php _e('Judge assistant sheets (5 dogs/page)'); ?>',
        8:((def==8)?'*':'')+'<?php _e('Judge assistant sheets (10 dogs/page)'); ?><br/>'
        // As we need to select categoria, cannot directly access to print parciales
        //8:((def==8)?'*':'')+'Imprimir resultados parciales de la manga'
    };
    var options4= {
        0:((def==0)?'*':'')+'<?php _e('Activities and timetable on this journey'); ?>',
        1:((def==1)?'*':'')+'<?php _e('Starting order on this round'); ?>',
		2:((def==2)?'*':'')+'<?php _e('Starting order on this round'); ?> (<?php _e("Excel format");?>)<br/>',
        3:((def==3)?'*':'')+'<?php _e('SheetCalc to evaluate SCT and MCT'); ?>',
        4:((def==4)?'*':'')+'<?php _e('Sheet to anotate data on rounds'); ?>',
        5:((def==5)?'*':'')+'<?php _e('Judge assistant empty sheet'); ?><br/>',
        6:((def==6)?'*':'')+'<?php _e('Judge assistant sheets (1 dog/page)'); ?>',
        7:((def==7)?'*':'')+'<?php _e('Judge assistant sheets (5 dogs/page)'); ?>',
        8:((def==8)?'*':'')+'<?php _e('Judge assistant sheets (15 dogs/page)'); ?>',
        9:((def==9)?'*':'')+'<?php _e('Judge assistant sheets (combined for team 4)'); ?><br/>'
    };

    function checkCanPrint(oper) {
        switch(parseInt(oper)){
            case 0: case 3:case 4:case 5: return true;
            case 1: case 2: case 6: case 7: case 8:case 9:
                var row= $('#competicion-listamangas').datagrid('getSelected');
                if (row )  return true;
                $.messager.alert('<?php _e('Error'); ?>','<?php _e('There is no selected round'); ?>','error');
                return false; // no hay ninguna manga seleccionada. retornar
        }
        return false;
    }

	var cats='-';
	var catstr='';
	if (typeof(cb)!=="undefined") {
		cats=$(cb).combobox('getValue');
		if (cats!=="-") catstr="<?php _e("Selected category");?>: "+$(cb).combobox('getText')+" <br />";
	}
    $.messager.radio(
        '<?php _e("Print form"); ?>',
        catstr+'<?php _e('Select document type to be generated'); ?>:',
        isJornadaEqConjunta()?options4:options,
        function(r){
            if (!r) return false;
            if (!checkCanPrint(r)) return false;
            switch(parseInt(r)){
                case 0: print_ordenTandas(); break;
                case 1: print_ordenSalida(cats,false); break;
				case 2: print_ordenSalida(cats,true); break;
                case 3: print_trsTemplates(0); break;
                case 4: print_trsTemplates(1); break;
                case 5: print_trsTemplates(2); break;
                case 6: print_asistente(1,cats); break;
                case 7: print_asistente(5,cats); break;
                case 8: print_asistente(15,cats); break;
                case 9: print_asistenteEquipos(cats); break;
            }
        }).window('resize',{width:450});
    return false; //this is critical to stop the click event which will trigger a normal file download!
}


/******************************** Datos de clasificacion general **********************/

/**
 * Imprime una hoja con los podio de esta ronda
 */
function clasificaciones_printPodium() {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='/agility/server/pdf/print_podium.php';
    if (isJornadaEqMejores()) url='/agility/server/pdf/print_podium_eq3.php';
    if (isJornadaEqConjunta()) url='/agility/server/pdf/print_podium_eq4.php';
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
				Rondas: ronda.Rondas
			},
	        preparingMessageHtml:'(podium) <?php _e("We are preparing your report, please wait"); ?> ...',
	        failMessageHtml:'(podium) <?php _e("There was a problem generating your report, please try again."); ?>'
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Imprime los resultados finales separados por categoria y grado, tal y como pide la RSCE
 */
function clasificaciones_printCanina() {
	
	// Server-side excel generation
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='/agility/server/pdf/print_clasificacion_excel.php';
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
				Rondas: ronda.Rondas,
				Mode: mode
			},
	        preparingMessageHtml:'(excel) <?php _e("We are preparing your report, please wait"); ?> ...',
	        failMessageHtml: '(excel) <?php _e("There was a problem generating your report, please try again."); ?>'
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Imprime los resultados finales de la ronda seleccionada en formato CSV para su conversion en etiquetas
 * @param {int} flag 0:CSV 1:PDF
 * @param {int} start if mode==PDF first line in output
 * @param {string} list CSV dorsal list
 * @returns {Boolean} false 
 */
function clasificaciones_printEtiquetas(flag,start,list) {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='/agility/server/pdf/print_etiquetas_csv.php';
	if (flag!=0) url='/agility/server/pdf/print_etiquetas_pdf.php';
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
				Manga1:ronda.Manga1,
				Manga2:ronda.Manga2,
				Rondas: ronda.Rondas,
				Mode: mode,
				Start: strt,
				List: list
			},
	        preparingMessageHtml: '(labels) <?php _e("We are preparing your report, please wait"); ?> ...',
	        failMessageHtml: '(labels) <?php _e("There was a problem generating your report, please try again."); ?>'
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
}

/**
 * Imprime los resultados finales de la ronda seleccionada en formato pdf
 * @return false
 */
function clasificaciones_printClasificacion() {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='/agility/server/pdf/print_clasificacion.php';
    if (isJornadaEqMejores()) url='/agility/server/pdf/print_clasificacion_equipos.php';
    if (isJornadaEqConjunta()) url='/agility/server/pdf/print_clasificacion_equipos.php';
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
				Rondas: ronda.Rondas,
				Mode: mode
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
	case 0:
	case 1:
	case 3:
	case 4: prfirst.numberspinner('disable'); prlist.numberspinner('disable'); break;
	case 2: prfirst.numberspinner('enable'); prlist.numberspinner('disable'); break;
	case 5: prfirst.numberspinner('enable'); prlist.numberspinner('enable'); break;
	}
}

/**
 * Presenta un menu al usuario indicando que es lo que se quiere imprimir
 */
function clasificaciones_doPrint() {
	var r=$('input:radio[name="r_prformat"]:checked').val();
	var line=$('#r_prfirst').numberspinner('getValue');
	var list=$('#r_prlist').textbox('getValue');
	$('#resultados-printDialog').dialog('close');
	switch(parseInt(r)) {
		case 0: clasificaciones_printPodium(); break;
		case 1: clasificaciones_printEtiquetas(0,line,''); break; // csv
		case 3: clasificaciones_printCanina(); break;
		case 4: clasificaciones_printClasificacion(); break;
		case 5: clasificaciones_printEtiquetas(1,line,list); break;
		case 2: clasificaciones_printEtiquetas(1,line,''); break;
	}
	return false; //this is critical to stop the click event which will trigger a normal file download!
}
