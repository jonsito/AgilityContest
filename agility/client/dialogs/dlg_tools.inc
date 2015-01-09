<h3>Utilidades</h3>   	

<div id="tools-dlg-buttons">
	<p> 
		<span style="display:inline-block;width:350px">Hacer copia de seguridad: </span>
    	<a id="tools-backupBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-db_backup'" onclick="backupDatabase()">Backup</a>
	</p>
	<p> 
		<span style="display:inline-block;width:350px">Restaurar copia de seguridad</span>
    	<a id="tools-restoreBtn" href="#" class="easyui-linkbutton" 
		   	data-options="iconCls:'icon-db_restore'" onclick="restoreDatabase()">Restore</a>
	</p>
	<p> 
		<span style="display:inline-block;width:350px"> Restaurar aplicaci&oacute;n a valores de f&aacute;brica</span>
    	<a id="tools-resetBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-on'" onclick="factoryReset()">Reset</a>
	</p>
</div>

<script type="text/javascript">

addTooltip($('#tools-backupBtn').linkbutton(),"Descargar copia de seguridad de la base de datos");
addTooltip($('#tools-restoreBtn').linkbutton(),"Restaurar la base de datos desde la copia de seguridad");
addTooltip($('#tools-resetBtn').linkbutton(),"Hacer un Factory Reset a la aplicación. Reiniciar BBDD");

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
	return false;
}

function restoreDatabase(){
	$.messager.alert("TODO","Restore Database from Application is not yet supported. Sorry","error");
}

function factoryReset(){
	$.messager.alert("TODO","Factory Reset not yet supported. Sorry","error");
}

</script>