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
		<input type="file" id="tools-restoreFile" name="restoreFile" accept="application/sql" required="required" onchange="read_restoreFile(this)">
		<input id="tools-restoreData" type="hidden" name="restoreData" value="">
	</p>
	<p> 
		<span style="display:inline-block;width:350px">Borrar el contenido de la base de datos</span>
    	<a id="tools-resetBtn" href="#" class="easyui-linkbutton" style="width:85px"
	   		data-options="iconCls:'icon-on'" onclick="clearDatabase()">&nbsp;Reset&nbsp;</a>
	</p>
	<p>
		<span style="display:inline-block;width:350px">Borrar pruebas registradas.</span>
		<a id="tools-eraseBtn" href="#" class="easyui-linkbutton" style="width:85px"
		   data-options="iconCls:'icon-brush'" onclick="removePruebas()">Clear</a>
	</p>
	<p>
		<span style="display:inline-block;width:350px">Buscar actualizaciones</span>
		<a id="tools-upgradeBtn" href="#" class="easyui-linkbutton" style="width:85px"
		   data-options="iconCls:'icon-reload'" onclick="upgradeApp()">Upgrade</a>
	</p>
</div>

<script type="text/javascript">

addTooltip($('#tools-backupBtn').linkbutton(),"Descargar copia de seguridad de la base de datos");
addTooltip($('#tools-restoreBtn').linkbutton(),"Restaurar la base de datos desde la copia de seguridad");
addTooltip($('#tools-resetBtn').linkbutton(),"Hacer un Factory Reset a la aplicación. Reiniciar BBDD");
addTooltip($('#tools-eraseBtn').linkbutton(),"Borrar todas las pruebas de la BBDD<br />Dejar solo información de perros/guias/clubes/jueces");
addTooltip($('#tools-upgradeBtn').linkbutton(),"Buscar e instalar actualizaciones de AgilityContest");
</script>