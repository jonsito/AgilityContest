<!-- 
dlg_usuarios.inc

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
	<div id="password-dialog" style="width:550px;height:auto;padding:10px 20px">
		<div class="ftitle"><?php _e('Password change'); ?></div>
		<form id="password-form" method="get">
			<div id="password-SameUser" class="fitem">
				<input id="password-UserID" type="hidden" name="UserID" value="" />
                <label for="password-CurrentPassword"><?php _e('Current password'); ?></label>
                <input id="password-CurrentPassword" name="CurrentPassword" type="password" style="height:25px;width:375px"/>
            </div>
            <div class="fitem">
                <label for="password-NewPassword"><?php _e('New password'); ?></label>
                <input id="password-NewPassword" name="NewPassword" type="password" style="height:25px;width:375px" />
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

<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE USUARIOS -->
<div  style="width:100%;height:500px">
    <!-- DECLARACION DE LA TABLA DE USUARIOS -->
    <table id="usuarios-datagrid">  </table>
</div>

<!-- BARRA DE TAREAS DE LA TABLA DE USUARIOS -->
<div id="usuarios-toolbar" style="width:100%;display:inline-block">
 			<span style="float:left;padding:5px">
   				<a id="usuarios-newBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-users'"
                   onclick="newUser('#usuarios-datagrid',$('#usuarios-datagrid-search').val())"><?php _e('New user'); ?></a>
   				<a id="usuarios-editBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-edit'"
                   onclick="editUser('#usuarios-datagrid')"><?php _e('Edit user'); ?></a>
   				<a id="usuarios-delBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-trash'"
                   onclick="deleteUser('#usuarios-datagrid')"><?php _e('Delete user'); ?></a>
   				<input id="usuarios-datagrid-search" type="text" value="<?php _e('-- Search --'); ?>" class="search_textfield"
                       onfocus="handleSearchBox(this,true);" onblur="handleSearchBox(this,false);"/>
   			</span>
    <span style="float:right;padding:5px">
   				<a id="usuarios-keyBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-key'"
                   onclick="setPassword('#usuarios-datagrid')"><?php _e('Password'); ?></a>
   				<a id="usuarios-reloadBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-brush'"
                   onclick="
                           // clear selection and reload table
                           $('#usuarios-datagrid-search').val('<?php _e('-- Search --'); ?>');
                           $('#usuarios-datagrid').datagrid('load',{ where: '' });"
                ><?php _e('Clear'); ?></a>
   			</span>
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
        $('#usuarios-Perms').combobox({valueField:'value',panelHeight:'auto'});

        $('#usuarios-Club').combogrid({
            panelWidth: 350,
            panelHeight: 200,
            idField: 'ID',
            textField: 'Nombre',
            url: '../ajax/database/clubFunctions.php',
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
        $('#password-CurrentPassword').textbox({
            required:true,
            validType:'length[1,255]',
            iconCls:'icon-lock'
        }).textbox('textbox').bind('keypress', function (evt) {
            if (evt.keyCode != 13) return true;
            $('#password-NewPassword').focus();
            return false;
        });

        $('#password-NewPassword').textbox({
            required:true,
            validType:'length[1,255]',
            iconCls:'icon-lock'
        }).textbox('textbox').bind('keypress', function (evt) {
            if (evt.keyCode != 13) return true;
            $('#password-NewPassword2').focus();
            return false;
        });

        //on Enter key on passwd field click on accept
        $('#password-NewPassword2').textbox({
            required:true,
            validType:'length[1,255]',
            iconCls:'icon-lock'
        }).textbox('textbox').bind('keypress', function (evt) {
            if (evt.keyCode != 13) return true;
            $('#password-okBtn').click();
            return false;
        });


        // datos de la tabla de usuarios
        $('#usuarios-datagrid').datagrid({
            // datos del panel padre asociado
            fit: true,
            border: false,
            closable: false,
            collapsible: false,
            expansible: false,
            collapsed: false,
            title: '<?php _e('User data management'); ?>',
            // datos de la conexion ajax
            url: '../ajax/database/userFunctions.php',
            queryParams: { Operation: 'select' },
            loadMsg: '<?php _e('Updating users list'); ?>',
            method: 'get',
            toolbar: '#usuarios-toolbar',
            pagination: false,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            view: scrollview,
            pageSize: 50,
            multiSort: true,
            remoteSort: true,
            columns: [[
                { field:'ID',		hidden:true },
                { field:'Login',	width:15, sortable:true,	title:'Login' },
                { field:'Gecos',	width:30, sortable:true,	title:'<?php _e('Information'); ?>' },
                { field:'Phone',	width:10, 					title:'<?php _e('Telephone'); ?>' },
                { field:'Email',	width:20, sortable:true,   	title:'E-mail' },
                { field:'Club',		hidden:true },
                { field:'NombreClub', width:20, sortable:true,   	title:'<?php _e('Club')?>' },
                { field:'Perms',	width:15,					title:'<?php _e('Category'); ?>', formatter:formatPermissions }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
            // on double click fireup editor dialog
            onDblClickRow:function() {
                editUser('#usuarios-datagrid');
            }
        });

        // key handler ( notify "null" dialog cause we dont want to close tabs on escape :-)
        addKeyHandler('#usuarios-datagrid',null,newUser,editUser,deleteUser);

        // tooltips para los botones de gestion de usuarios
        addTooltip($('#usuarios-newBtn').linkbutton(),'<?php _e("Create and insert new user<br/> into database"); ?>');
        addTooltip($('#usuarios-editBtn').linkbutton(),'<?php _e("Modify data on selected user"); ?>');
        addTooltip($('#usuarios-delBtn').linkbutton(),'<?php _e("Remove selected user from database"); ?>');
        addTooltip($('#usuarios-reloadBtn').linkbutton(),'<?php _e("Clear search box. Update data"); ?>');
        addTooltip($('#usuarios-keyBtn').linkbutton(),'<?php _e("Change user password"); ?>');
        addTooltip($('#usuarios-datagrid-search'),"Look for users matching search criteria");
    </script>