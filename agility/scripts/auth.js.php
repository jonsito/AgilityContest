/*
auth.js

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
* Client-side uthentication related functions
*/

/**
 * Abre el frame de login o logout dependiendo de si se ha iniciado o no la sesion
 */
function showLoginWindow() {
	if (typeof(ac_authInfo.SessionKey)==="undefined" || (ac_authInfo.SessionKey==null) ) {
		$('#login-window').remove();
		loadContents('/agility/console/frm_login.php','<?php _e('Init session');?>');
	} else {
		$('#logout-window').remove();
		loadContents('/agility/console/frm_logout.php','<?php _e('End session');?>');
	}
}

function showMyAdminWindow() {
	$('#myAdmin-window').remove();
	loadContents('/agility/console/frm_myAdmin.php','<?php _e('Direct database access');?>');
}

function acceptLogin() {
	var user= $('#login-Username').val();
	var pass=$('#login-Password').val();
	if (!user || !user.length) {
		$.messager.alert("Invalid data",'<?php _e("There is no user chosen");?>',"error");
		return;
	}
	// set federation
	setFederation($('#login-Federation').combogrid('getValue'));
	$.ajax({
		type: 'POST',
  		url: 'https://'+window.location.hostname+'/agility/server/database/userFunctions.php',
   		dataType: 'jsonp',
   		data: {
   			Operation: 'login',
   			Username: user,
   			Password: pass
   		},
   		contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
   		success: function(data) {
       		if (data.errorMsg) { // error
       			$.messager.alert("Error",data.errorMsg,"error");
       			initAuthInfo();
       		} else {// success:
       			var str="AgilityContest version: "+ac_config.version_name+"-"+ac_config.version_date+"<br />";
       			str =str+'<?php _e("License registered by");?>'+": "+data.User+"<br />";
       			str =str+'<?php _e("For use at club");?>'+": "+data.Club+"<br />";
				if (data.Expired==="1")  {
					str = str+'<br/><strong><span class="blink">'+'<?php _e("License expired");?>'+'</span></strong><br/>';
				}
       			str =str+'<br />'+'<?php _e("User");?>'+" "+data.Login+": "+'<?php _e("session login success");?>';
       			var w=$.messager.alert("Login",str,"info",function(){
					$('#login_menu-text').html('<?php _e("End session");?>'+": <br />"+data.Login);
					initAuthInfo(data);
				});
                w.window('resize',{width:400,height:200}).window('center');
       		} 
       	},
   		error: function() { alert("error");	}
	});
	$('#login-window').window('close');
}

function acceptLogout() {
	var user=ac_authInfo.Login;
	$.ajax({
		type: 'POST',
   		url: '/agility/server/database/userFunctions.php',
   		dataType: 'json',
   		data: {
   			Operation: 'logout',
   			Username: user,
   			Password: ""
   		},
   		contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
   		success: function(data) {
       		if (data.errorMsg) { // error
       			$.messager.alert("Error",data.errorMsg,"error");
       		} else {// success: 
       			$.messager.alert('<?php _e("User");?>'+" "+user,'<?php _e("Session has been closed by user");?>',"info");
           		$('#login_menu-text').html('<?php _e("Init session");?>');
           		initAuthInfo();
           		setFederation(0); // on logout defaults to RSCE
       		} 
       	},
   		error: function() { alert("error");	}
	});
	$('#logout-window').window('close');	
}

function checkPassword(user,pass,callback) {
	$.ajax({
		type: 'POST',
		url: 'https://'+window.location.hostname+'/agility/server/database/userFunctions.php',
		dataType: 'jsonp',
		data: {
			Operation: 'pwcheck',
			Username: user,
			Password: pass
		},
		contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
		success: function(data) { callback(data); },
		error: function() { alert("error");	}
	});
}

function acceptMyAdmin() {
	var user= $('#myAdmin-Username').val();
	var pass=$('#myAdmin-Password').val();
	if (!user || !user.length) {
		$.messager.alert("Invalid data",'<?php _e("No user specified");?>',"error");
		return;
	}
	checkPassword(user,pass,function(data) {
		if (data.errorMsg) { // error
			$.messager.alert("Error",data.errorMsg,"error");
		} else { // success:
			if (parseInt(data.Perms)<=1) window.open("/phpmyadmin","phpMyAdmin");
			else $.messager.alert("Error",'<?php _e("Current user has no <em>admin</em> privileges");?>',"error");
		}
	});
	$('#myAdmin-window').window('close');
}

function cancelLogin() {
	$('#login-Usuario').val('');
	$('#login-Password').val('');
	setFederation(0); // defaults to first federation (rsce)
	var w=$.messager.alert("Login","<?php _e('No user provided');?>"+"<br />"+"<?php _e('Starting session read-only (guest)');?>","warning",function(){
		// close window
		$('#login-window').window('close');
	});
}

function cancelLogout() {
	// close window
	$('#logout-window').window('close');
}

function cancelMyAdmin() {
	// close window
	$('#myAdmin-window').window('close');
}

function read_regFile(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function(e) {
			$('#registrationData').val(e.target.result);
		};
		reader.readAsDataURL(input.files[0]);
	}
}

function send_regFile() {
    $.ajax({
  		type: 'POST',
    	url: '/agility/server/adminFunctions.php',
    	dataType: 'json',
    	data: {
    		Operation: 'register',
    		Data: $('#registrationData').val()
    	},
    	contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
    	success: function(data) {
            if (data.errorMsg){
                $.messager.show({ width:300, height:150, title: 'Error', msg: data.errorMsg });
            } else {
                $('#registration_data').form('load',data);
            	$.messager.alert('<?php _e("Licensing");?>','<?php _e("Licensing data successfully loaded");?>',"info");
            }
    	},
    	error: function() { alert("error");	}
   	});
}

/*
Same as above, but use ajax call to retrieve real permissions from server
 */
function check_permissions(perms, callback) {
	$.ajax({
		type: "GET",
		url: '/agility/server/adminFunctions.php',
		data: {
			'Operation' : 'permissions',
			'Perms':perms
		},
		async: true,
		cache: false,
		dataType: 'json',
		success: function(data){ callback( data); },
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			alert("error: "+textStatus + " "+ errorThrown );
		}
	});
}

/*
 Comprueba si el usuario tiene privilegios suficientes para realizar la operacion indicada en callback
 (admin,operator,assistant,guest)
 En caso de no tener privilegios avisa, pero deja continuar
 Si callback es null, simplemente retorna true o false
 */
function check_softLevel(perm,callback) {
	if (typeof(callback) !== 'function') {
		return (ac_authInfo.Perms<=perm);
	}
	if (ac_authInfo.Perms>perm) {
		$.messager.alert(
			'<?php _e("User level");?>',
			'<?php _e("Current user has not enought level to make changes <br/>Read-only access enabled");?>',
			'warning',
			null
		);
	}
	callback();
}

/*
Comprueba si la licencia tiene habilitado el permiso para acceder a la funcionalidad deseada
( pruebas por equipos, ko, videomarcador, etc )
Por seguridad, los permisos no se comprueban nunca en el cliente, por lo que es necesaria una llamada
al servidor
 */
function check_access(perms,callback) {
    $.ajax({
        type:'GET',
        url:"/agility/server/adminFunctions.php",
        dataType:'json',
        data: {
            Operation:	'userlevel',
            Prueba:	workingData.prueba,
            ID:workingData.jornada,
            Perms : perms
        },
        success: function(res) { callback(res); },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            $.messager.alert("Restricted","Error: "+textStatus + " "+ errorThrown,'error' );
        }
    });
}