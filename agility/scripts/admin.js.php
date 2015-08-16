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
    return false;
}

function performClearDatabase(oper,pass,callback) {
    // comprobamos si el password es correcto
    checkPassword(authInfo.Login,pass,function(data) {
        if (data.errorMsg) { // error
            $.messager.alert("Error",data.errorMsg,"error");
        } else { // success:
            // si password correcto invocamos la operacion
            $.ajax({
                type:'GET',
                url:"/agility/server/adminFunctions.php",
                dataType:'json',
                data: {
                    Operation: oper
                },
                success: function(res) { callback(res); },
                error: function(XMLHttpRequest,textStatus,errorThrown) {
                    $.messager.alert("Error: "+oper,"Error: "+textStatus + " "+ errorThrown,'error' );
                }
            });
        }
    });
}

function read_restoreFile(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#tools-restoreData').val(e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function restoreDatabase(){
    var l1="<strong>AVISO:</strong><br/>";
    var l2="Esta operaci&oacute;n <strong>BORRARA <em>TODOS</em> LOS DATOS ACTUALES</strong>. antes de intentar recuperar los nuevos<br/>";
    var l3="Aseg&uacute;rese de realizar una copia de seguridad antes de seguir<br/><br/>";
    var l4="Para continuar, introduzca la contrase&ntilde;a del usuario administrador, y pulse <em>Aceptar</em>";
    if (!checkForAdmin()) return;
    if ($('#tools-restoreFile').val()=="") {
        $.messager.alert("Restore","Debe especificar un fichero '.sql' con un backup anterior","error");
        return false;
    }
    $.messager.password('Recuperar base de datos',l1+l2+l3+l4 , function(pass){
        if (pass){
            // comprobamos si el password es correcto
            checkPassword(authInfo.Login,pass,function(data) {
                if (data.errorMsg) { // error
                    $.messager.alert("Error",data.errorMsg,"error");
                } else { // success:
                    $.messager.progress({
                        title: 'Restore',
                        msg: 'Restaurando base de datos',
                        interval: 0
                    });
                    // si password correcto invocamos la operacion
                    $.ajax({
                        type:'POST', // use post to send file
                        url:"/agility/server/adminFunctions.php",
                        dataType:'json',
                        data: {
                            Operation: 'restore',
                            Data: $('#tools-restoreData').val()
                        },
                        contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
                        success: function(data) {
                            if (data.errorMsg){
                                $.messager.show({ width:300, height:150, title: 'Database Restore Error', msg: data.errorMsg });
                            } else {
                                $.messager.alert(
                                    "Restore Database",
                                    "Base de datos recuperada<br />Pulse Aceptar para reiniciar la aplicación",
                                    "info",
                                    function(){window.location.reload();} // reload application main page
                                );
                            }
                            $.messager.progress('close');
                        },
                        error: function(XMLHttpRequest,textStatus,errorThrown) {
                            $.messager.alert("DBRestore Error","Error: "+textStatus + " "+ errorThrown,'error' );
                            $.messager.progress('close');
                        }
                    });
                    // en paralelo arrancamos una tarea para leer el progreso de la operacion
                    function getProgress(){
                        $.ajax({
                            url:"/agility/server/adminFunctions.php",
                            dataType:'json',
                            data: {
                                Operation: 'progress'
                            },
                            success: function(data) {
                                if(data.progress!=="Done"){
                                    var bar = $.messager.progress('bar');  // get the progressbar object
                                    bar.progressbar('setValue', data.progress);  // set new progress value
                                    setTimeout(getProgress,200);
                                } else {
                                    $.messager.progress('close');
                                }
                            }
                        });
                    }
                    setTimeout(getProgress,200);
                }
            });
        }
    }).window('resize',{width:640});
}

function clearDatabase(){
    var l1="<strong>AVISO:</strong><br/>";
    var l2="Esta operaci&oacute;n <strong>BORRA <em>TODOS</em> LOS DATOS </strong><br/>" +
        "Incluyendo pruebas,inscripciones, resultados, jueces, perros, guias y clubes<br/>" +
        "Solo deberia usarse como paso previo a una importación de datos desde fichero excel<br/> ";
    var l3="Aseg&uacute;rese de realizar una copia de seguridad antes de seguir<br/><br/>";
    var l4="Para continuar, introduzca la contrase&ntilde;a del usuario administrador, y pulse <em>Aceptar</em>";
    if (!checkForAdmin()) return;
    $.messager.password('Factory Reset',l1+l2+l3+l4 , function(pass){
        if (pass){
            performClearDatabase('reset',pass,function(data){
                if (data.errorMsg){
                    $.messager.show({ width:300, height:150, title: 'Database Reset Error', msg: data.errorMsg });
                } else {
                    $.messager.alert("Reset Database","Base de datos vacía<br />Por favor reinicie la aplicación","info");
                }
            });
        }
    }).window('resize',{width:640});
}

function removePruebas(){
    var l1="<strong>AVISO:</strong><br/>";
    var l2="Esta operaci&oacute;n eliminar&aacute; todas las pruebas,";
    var l3="competiciones y resultados almacenados<br/><br/>";
    var l4="Para continuar, introduzca la contrase&ntilde;a del usuario administrador, y pulse <em>Aceptar</em>";
    if (!checkForAdmin()) return;
    $.messager.password('Borrar pruebas',l1+l2+l3+l4, function(pass){
        if (pass){
            performClearDatabase('clear',pass,function(data){
                if (data.errorMsg){
                    $.messager.show({ width:300, height:150, title: 'Contests clear Error', msg: data.errorMsg });
                } else {
                    $.messager.alert("Clear Database","Todas las competiciones han sido borradas<br />Por favor reinicie la aplicación","info");
                }
            });
        }
    }).window('resize',{width:640});
}

function askForUpgrade(msg){
    var l1="<strong>AVISO:</strong><br/>";
    var l2="Aseg&uacute;rese de realizar una copia de seguridad antes de seguir<br/><br/>";
    var l3="Para actualizar AgilityContest, introduzca la contrase&ntilde;a del usuario administrador, y pulse <em>Aceptar</em>";
    if (!checkForAdmin()) return;
    $.messager.password('Actualizar AgilityContest',msg+l1+l2+l3 , function(pass) {
        if (pass) {
            // comprobamos si el password es correcto
            checkPassword(authInfo.Login,pass,function(data) {
                if (data.errorMsg) { // error
                    $.messager.alert("Error", data.errorMsg, "error");
                } else {
                    window.location='/agility/upgrade.php';
                }
            });
        }
    }).window('resize',{width:480});;
}

function checkForUpgrades() {
    var msg="<p>Current Version: "+ac_config.version_name+"<br />Current Release: "+ac_config.version_date+"</p>";
    $.ajax({
        url:"/agility/server/adminFunctions.php",
        dataType:'json',
        data: {
            Operation: 'upgrade'
        },
        success: function(data) {
            if (typeof(data.errorMsg)!=="undefined") {
                $.messager.alert("Check for Upgrades",data.errorMsg,"error");
                return;
            }
            if (data.version_date==ac_config.version_date) {
                msg = msg +"<p>AgilityContest est&aacute; actualizado</p>";
                $.messager.alert("Version Info",msg,"info");
            }
            msg = msg +"<p>Last Version: "+data.version_name+"<br />Last Release: "+data.version_date+"</p>";
            if (data.version_date>ac_config.version_date) askForUpgrade(msg);
        }
    });
}