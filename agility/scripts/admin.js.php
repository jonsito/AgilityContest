/*
admin.js

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

<?php
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

/*
* Admin related functions from dlg_tools.inc
*/

function checkForAdmin() {
    if (parseInt(authInfo.Perms)>1) {
        $.messager.alert("Invalid user","Current user '"+authInfo.Login+"' has not enought privileges","error");
        return false;
    }
    return true;
}

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
    var l1="<strong>AVISO:</strong><br/>";
    var l2="Esta operaci&oacute;n <strong>BORRARA <em>TODOS</em> LOS DATOS ACTUALES</strong>. antes de intentar recuperar los nuevos<br/>";
    var l3="Aseg&uacute;rese de realizar una copia de seguridad antes de seguir<br/><br/>";
    var l4="Para continuar, introduzca la contrase&ntilde;a del usuario administrador, y pulse <em>Aceptar</em>";
    if (!checkForAdmin()) return;
    $.messager.password('Recuperar base de datos',l1+l2+l3+l4 , function(r){
        if (r){
            $.messager.alert("TODO","Restore Database from Application is not yet supported. Sorry","error");
        }
    }).window('resize',{width:640});
}

function factoryReset(){
    var l1="<strong>AVISO:</strong><br/>";
    var l2="Esta operaci&oacute;n restaura los valores de fabrica y <strong>BORRA <em>TODOS</em> LOS DATOS </strong><br/>" +
        "Incluyendo pruebas,inscripciones, resultados, jueces, perros, guias y clubes<br/>" +
        "Solo deberia usarse como paso previo a una importación de datos desde fichero excel<br/> ";
    var l3="Aseg&uacute;rese de realizar una copia de seguridad antes de seguir<br/><br/>";
    var l4="Para continuar, introduzca la contrase&ntilde;a del usuario administrador, y pulse <em>Aceptar</em>";
    if (!checkForAdmin()) return;
    $.messager.password('Factory Reset',l1+l2+l3+l4 , function(r){
        if (r){
            $.messager.alert("TODO","Factory Reset not yet supported. Sorry","error");
        }
    }).window('resize',{width:640});
}

function removePruebas(){
    var l1="<strong>AVISO:</strong><br/>";
    var l2="Esta operaci&oacute;n eliminar&aacute; todas las pruebas,";
    var l3="competiciones y resultados almacenados<br/><br/>";
    var l4="Para continuar, introduzca la contrase&ntilde;a del usuario administrador, y pulse <em>Aceptar</em>";
    if (!checkForAdmin()) return;
    $.messager.password('Borrar pruebas',l1+l2+l3+l4, function(r){
        if (r){
            $.messager.alert("TODO","Contest Clear not yet supported. Sorry","error");
        }
    }).window('resize',{width:640});
}
