/*
auth.js

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

/*
* Client-side uthentication related functions
*/

function acceptLogin() {
	$.ajax({
		type: 'POST',
  		url: '/agility/server/database/userFunctions.php',
   		dataType: 'json',
   		data: {
   			Operation: 'login',
   			Username: $('#login-Username').val(),
   			Password: $('#login-Password').val(),
   		},
   		contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
   		success: function(data) {
       		if (data.errorMsg) { // error
       			$.messager.alert("Error",data.errorMsg,"error");
       		} else {// success: 
       			$.messager.alert("Usuario"+data.Login,"Sesi&oacute;n iniciada correctamente","info");
           		$('#login_menu-text').html("Cerrar sesi&oacute;n: "+data.Login);
           		initAuthInfo(data);
       		} 
       	},
   		error: function() { alert("error");	},
	});
	$('#login-dialog').dialog('close');	
}

function acceptLogout() {
	var user=authInfo.Login;
	$.ajax({
		type: 'POST',
   		url: '/agility/server/database/userFunctions.php',
   		dataType: 'json',
   		data: {
   			Operation: 'logout',
   			Username: user,
   			Password: "",
   		},
   		contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
   		success: function(data) {
       		if (data.errorMsg) { // error
       			$.messager.alert("Error",data.errorMsg,"error");
       		} else {// success: 
       			$.messager.alert("Usuario"+user,"Sesi&oacute;n finalizada correctamente","info");
           		$('#login_menu-text').html("Iniciar sesi&oacute;n");
           		initAuthInfo(null);
       		} 
       	},
   		error: function() { alert("error");	},
	});
	$('#login-Usuario').val('');
	$('#login-Password').val('');
	$('#login-dialog').dialog('close');	
}
	
function cancelLogin() {
	$('#login-Usuario').val('');
	$('#login-Password').val('');
	// close window
	$('#login-dialog').dialog('close');
}