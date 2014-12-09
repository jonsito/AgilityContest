    <div id="admin-tab" class="easyui-tabs" style="width:100%;height:auto;">
    	<div title="Usuarios" data-options="iconCls:'icon-users'" style="padding:20px;display:none;">
    	<?php require_once("dialogs/dlg_usuarios.inc")?>
    	</div>
    	<div title="Sesiones" data-options="iconCls:'icon-order'" style="padding:20px;display:none;">
    	<?php require_once("dialogs/dlg_sesiones.inc")?>
    	</div>
    	<div title="Configuraci&oacute;n" data-options="iconCls:'icon-setup'" style="padding:20px;display:none;">
    	<?php require_once("dialogs/dlg_configuracion.inc")?>
    	</div>
    	<div title="Utilidades" data-options="iconCls:'icon-tools'" style="padding:20px;display:none;">
    	<?php require_once("dialogs/dlg_tools.inc")?>
    	</div>
    </div>