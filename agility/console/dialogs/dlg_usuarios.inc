<!-- 
dlg_usuarios.inc

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>
	<div id="password-dialog" style="width:550px;height:300px;padding:10px 20px">
		<div class="ftitle"><?php _e('Password change'); ?></div>
		<form id="password-form" method="get">
			<div id="password-SameUser" class="fitem">
				<input id="password-UserID" type="hidden" name="UserID" value="" />
                <label for="password-CurrentPassword"><?php _e('Current password'); ?></label>
                <input id="password-CurrentPassword" name="CurrentPassword" type="password" style="height:25px;width:375px"/>
            </div>
            <div class="fitem">
                <label for="password-NewPassword"><?php _e('New password'); ?></label>
                <input id="password-NewPassword" name="NewPassword" type="password" style="height:25px;width:375px"/>
            </div>
            <div class="fitem">
                <label for="password-NewPassword2"><?php _e('New password'); ?> (<?php _e('again'); ?>)</label>
                <input id="password-NewPassword2" name="NewPassword2" type="password" style="height:25px;width:375px"/>
            </div>
		</form>
	</div>
	
   	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE CAMBIO DE CONTRASENYA -->
   	<div id="password-dlg-buttons">
   	    <a id="password-okBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-ok'" onclick="savePassword()"><?php _e('Save'); ?></a>
   	    <a id="password-cancelBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-cancel'" onclick="$('#password-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
   	</div>
   	
    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE Usuarios -->
    <div id="usuarios-dialog" style="width:450px;height:auto;padding:10px 20px">
        <div class="ftitle"><?php _e('User information'); ?></div>
        <form id="usuarios-form" method="get">
            <div class="fitem">
                <label for="usuarios-Login"><?php _e('Name'); ?></label>
                <input id="usuarios-Login" type="text" name="Login" style="width:250px" />
                <a id="usuarios-passwdBtn" href="#" class="easyui-linkbutton" 
                	data-options="iconCls:'icon-key'" onclick="setPassword('#usuarios-datagrid');"><?php _e('Password'); ?></a>
                <input id="usuarios-ID" name="ID" type="hidden" /> 
                <input id="usuarios-Operation" name="Operation" type="hidden" /> <!-- insert/update/delete operation -->
            </div>
            <div class="fitem">
                <label for="usuarios-Gecos"><?php _e('Information'); ?></label>
                <input id="usuarios-Gecos" name="Gecos" type="text" style="height:25px;width:375px"/>
            </div>
            <div class="fitem">
                <label for="usuarios-Phone"><?php _e('Telephone'); ?></label>
                <input id="usuarios-Phone" name="Phone" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="usuarios-Email"><?php _e('Electronic mail'); ?></label>
                <input id="usuarios-Email" type="text" name="Email" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="usuarios-Club"><?php _e('Club'); ?>:</label>
                <select id="usuarios-Club" name="Club" style="width:200px"></select>
            </div>
            <div class="fitem">
                <label for="usuarios-Perms"><?php _e('Category'); ?></label>
                <select id="usuarios-Perms" name="Perms">
                <option value="1"><?php _e('Admin'); ?></option>
                <option value="2"><?php _e('Operator'); ?></option>
                <option value="3"><?php _e('Assistant'); ?></option>
                <option value="4"><?php _e('Guest'); ?></option>
                </select>
            </div>
        </form>
    </div>
    
   	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
   	<div id="usuarios-dlg-buttons">
   	    <a id="usuarios-okBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-ok'" onclick="saveUser()"><?php _e('Save'); ?></a>
   	    <a id="usuarios-cancelBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-cancel'" onclick="$('#usuarios-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
   	</div>
   	
	<script type="text/javascript">
		// declaraciones
		$('#usuarios-dialog').dialog({ closed:true, buttons:'#usuarios-dlg-buttons', iconCls:'icon-users', modal:true });
		$('#usuarios-form').form({novalidate:true});
		$('#password-dialog').dialog({ closed:true, buttons:'#password-dlg-buttons', iconCls:'icon-users', modal:true });
		$('#password-form').form({novalidate:true});
		
        // - botones
		addTooltip($('#usuarios-passwdBtn').linkbutton(),'<?php _e("Change user password"); ?>');
		addTooltip($('#usuarios-okBtn').linkbutton(),'<?php _e("Store user information into database"); ?>');
		addTooltip($('#usuarios-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');

		addTooltip($('#password-okBtn').linkbutton(),'<?php _e("Change password"); ?>');
		addTooltip($('#password-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
		
        // on ESC key close session
        $('#usuarios-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
        	if (e.keyCode == 27){ $('#usuarios-dialog').dialog('close');
        	}
        });
        $('#usuarios-Login').textbox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#usuarios-Email').textbox({
            required: false,
            validType: 'email'
        });
        $('#usuarios-Gecos').textbox();
        $('#usuarios-Phone').textbox();
        $('#usuarios-Perms').combobox({panelHeight:'auto'});

        $('#usuarios-Club').combogrid({
            panelWidth: 350,
            panelHeight: 200,
            idField: 'ID',
            textField: 'Nombre',
            url: '/agility/server/database/clubFunctions.php',
            queryParams: {Operation:'enumerate',Federation:workingData.federation},
            method: 'get',
            mode: 'remote',
            required: true,
            columns: [[
                {field:'ID', hidden:true},
                {field:'Nombre',title:'<?php _e('Club name'); ?>',width:80,align:'right'},
                {field:'Provincia',title:'<?php _e('Province'); ?>',width:40,align:'right'}
            ]],
            multiple: false,
            fitColumns: true,
            selectOnNavigation: false
        });
     	// bind enter key on password dialog
        $('#password-CurrentPassword').bind('keypress', function (evt) {
            if (evt.keyCode != 13) return true;
            $('#password-NewPassword').focus();
            return false;
        });
        $('#password-NewPassword').bind('keypress', function (evt) {
            if (evt.keyCode != 13) return true;
            $('#password-NewPassword2').focus();
            return false;
        });
        //on Enter key on passwd field click on accept
        $('#password-NewPassword2').bind('keypress', function (evt) {
            if (evt.keyCode != 13) return true;
            $('#password-okBtn').click();
            return false;
        });
	</script>