/**
 * Load html contents from 'page' URL and set as contents on 'id' tag
 * @param id tag to replace DOM contents
 * @param page URL where to retrieve HTML data
 */
function loadContents(id,page) {
	$('#mymenu').panel('collapse');
	$(id).load(page);
}

/**
 * Poor's man implementation of php's replaceAll
 */
function replaceAll(find,replace,from) {
	return from.replace(new RegExp(find, 'g'), replace);
}

/**
* Object to store working data primary keys
*/
var workingData = new Object({
	perro: 0, // IDPerro del perro en edicion
	guia: '', // nombre del guia en edicion
	club: '', // nombre del club activo
	juez: '', // nombre del juez activo
	prueba: 0, // ID de la prueba en curso
	jornada: 0, // ID de la jornada en curso
	manga: 0, // ID de la manga en curso
	manga2: 0, // ID de la segunda manga para el calculo de resultados
	datosPrueba: new Object(), // last selected prueba data
	datosJornada: new Object(), // last selected jornada data
	datosRonda: new Object() // last selected ronda (grade, manga1, manga2)
});
