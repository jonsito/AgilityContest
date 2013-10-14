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
* Object to store working data
*/
var workingData = new Object({
	perro: 0,
	guia: '',
	club: '',
	juez: '',
	prueba: '',
	jornada: 0,
	manga: 0
});