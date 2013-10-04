/**
 * Load html contents from 'page' URL and set as contents on 'id' tag
 * @param id tag to replace DOM contents
 * @param page URL where to retrieve HTML data
 */
function loadContents(id,page) { 
	$(id).load(page);
}

/**
 * Poor's man implementation of php's replaceAll
 */
function replaceAll(find,replace,from) {
	return from.replace(new RegExp(find, 'g'), replace);
}