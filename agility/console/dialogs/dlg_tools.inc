<h3>Utilidades</h3>   	

<div id="tools-dlg-buttons">
	<p> 
		<span style="display:inline-block;width:350px">Hacer copia de seguridad: </span>
    	<a id="tools-backupBtn" href="#" class="easyui-linkbutton" style="width:85px"
	   		data-options="iconCls:'icon-db_backup'" onclick="backupDatabase()">Backup</a>
	</p>
	<p> 
		<span style="display:inline-block;width:350px">Restaurar copia de seguridad</span>
    	<a id="tools-restoreBtn" href="#" class="easyui-linkbutton" style="width:85px"
		   	data-options="iconCls:'icon-db_restore'" onclick="restoreDatabase()">Restore</a>
	</p>
	<p> 
		<span style="display:inline-block;width:350px"> Restaurar aplicaci&oacute;n a valores de f&aacute;brica</span>
    	<a id="tools-resetBtn" href="#" class="easyui-linkbutton" style="width:85px"
	   		data-options="iconCls:'icon-on'" onclick="factoryReset()">&nbsp;Reset&nbsp;</a>
	</p>
	<p> 
		<span style="display:inline-block;width:350px"> Borrar pruebas registradas.</span>
    	<a id="tools-eraseBtn" href="#" class="easyui-linkbutton" style="width:85px"
	   		data-options="iconCls:'icon-brush'" onclick="removePruebas()">Clear</a>
	</p>
</div>

<script type="text/javascript">

addTooltip($('#tools-backupBtn').linkbutton(),"Descargar copia de seguridad de la base de datos");
addTooltip($('#tools-restoreBtn').linkbutton(),"Restaurar la base de datos desde la copia de seguridad");
addTooltip($('#tools-resetBtn').linkbutton(),"Hacer un Factory Reset a la aplicación. Reiniciar BBDD");
addTooltip($('#tools-eraseBtn').linkbutton(),"Borrar todas las pruebas de la BBDD<br />Dejar solo información de perros/guias/clubes/jueces");

function backupDatabase(){
	$.fileDownload(
		'/agility/server/adminFunctions.php',
		{	
			httpMethod: 'GET',
			data: {
				Operation: 'backup' 
			},
		    preparingMessageHtml: "We are preparing your backup, please wait...",
		    failMessageHtml: "There was a problem generating your backup, please try again."
		}
	);
	$.messager.alert("TODO","Erase pruebas not yet supported. Sorry","error");
	return false;
}

function restoreDatabase(){
	var l1="<strong>AVISO:</strong><br />";
	var l2="Esta operaci&oacute;n <strong>BORRARA <em>TODOS</em> LOS DATOS ACTUALES</strong>. antes de intentar recuperar los nuevos<br />";
	var l3="Aseg&uacute;rese de realizar una copia de seguridad antes de seguir<br /><br />";
	var l4="Para continuar, introduzca la contrase&ntilde;a del usuario administrador, y pulse <em>Aceptar</em>";
	$.messager.password('Recuperar base de datos',l1+l2+l3+l4 , function(r){
		if (r){
			$.messager.alert("TODO","Restore Database from Application is not yet supported. Sorry","error");
		}
	}).window('resize',{width:640});
}

function factoryReset(){
	var l1="<strong>AVISO:</strong><br />";
	var l2="Esta operaci&oacute;n restaura los valores de fabrica y <strong>BORRA <em>TODOS</em> LOS DATOS </strong><br />" +
		"Incluyendo pruebas,inscripciones, resultados, jueces, perros, guias y clubes<br />" +
		"Solo deberia usarse como paso previo a una importación de datos desde fichero excel<br /> ";
	var l3="Aseg&uacute;rese de realizar una copia de seguridad antes de seguir<br/><br />";
	var l4="Para continuar, introduzca la contrase&ntilde;a del usuario administrador, y pulse <em>Aceptar</em>";
	$.messager.password('Factory Reset',l1+l2+l3+l4 , function(r){
		if (r){
			$.messager.alert("TODO","Factory Reset not yet supported. Sorry","error");
		}
	}).window('resize',{width:640});
}
function removePruebas(){
	var l1="<strong>AVISO:</strong><br />";
	var l2="Esta operaci&oacute;n eliminar&aacute; todas las pruebas,";
	var l3="competiciones y resultados almacenados<br/><br />";
	var l4="Para continuar, introduzca la contrase&ntilde;a del usuario administrador, y pulse <em>Aceptar</em>";
	$.messager.password('Borrar pruebas',l1+l2+l3+l4, function(r){
		if (r){
			$.messager.alert("TODO","Contest Clear not yet supported. Sorry","error");
		}
	}).window('resize',{width:640});
}

</script>