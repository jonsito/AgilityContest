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
	perro: 0, // Dorsal del perro en edicion
	guia: '', // nombre del guia en edicion
	club: '', // nombre del club activo
	juez: '', // nombre del juez activo
	prueba: 0, // ID de la prueba en curso
	jornada: 0, // ID de la jornada en curso
	manga: 0 // ID de la manga en curso
});

/**
 * Function to reorganize form elements on Manga data panel
 */
function dmanga_setRecorridos() {
	var val=$("input:radio[name=Recorrido]:checked").val();
	switch (val) {
	case '0':
		var distl=$('#dmanga_DistL').val();
		var obstl=$('#dmanga_ObstL').val();
		$('#dmanga_DistM').attr('disabled',true);
		$('#dmanga_DistM').val(distl);
		$('#dmanga_ObstM').attr('disabled',true);
		$('#dmanga_ObstM').val(obstl);
		$('#dmanga_DistS').attr('disabled',true);
		$('#dmanga_DistS').val(distl);
		$('#dmanga_ObstS').attr('disabled',true);
		$('#dmanga_ObstS').val(obstl);
		break;
	case '1':
		var distm=$('#dmanga_DistM').val();
		var obstm=$('#dmanga_ObstM').val();
		$('#dmanga_DistM').removeAttr('disabled');
		$('#dmanga_ObstM').removeAttr('disabled');
		$('#dmanga_DistS').attr('disabled',true);
		$('#dmanga_DistS').val(distm);
		$('#dmanga_ObstS').attr('disabled',true);
		$('#dmanga_ObstS').val(obstm);
		break;
	case '2':
		$('#dmanga_DistM').removeAttr('disabled');
		$('#dmanga_ObstM').removeAttr('disabled');
		$('#dmanga_DistS').removeAttr('disabled');
		$('#dmanga_ObstS').removeAttr('disabled');
		break;
	}
}
