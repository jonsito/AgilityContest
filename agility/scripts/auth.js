/*
auth.js

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

/**
 * Try to authenticate with server
 * On success store sessionkey and call callback
 * On fail, show message
 */
function login(user,pass,callback){
}

/**
 * Tell server to close current session and remove sessionkey token
 * On success call callback
 */
function logout(callback) {
	
}

/**
 * Tell server join to session with provided ID
 * @param id Session ID to join to
 * @param callback what to do on success
 */
function joinSession(id,callback) {
	
}

/**
 * Ask server if current session has enought permissions
 * @param perm 0:root 1:admin 2:operator 3:assistant 4:guest 5:none 
 * @return true on access granted, else false
 */
function access(perms) {

}