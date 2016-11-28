/*
 modules.js

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

function newPlugin() {
    alert("Not yet implemented");
}

function updatePlugin(dg) {
    var row = $(dg).datagrid('getSelected');
    if (!row) {
        $.messager.alert('<?php _e("Edit Error"); ?>','<?php _e("There is no module selected"); ?>',"warning");
        return; // no way to know which module is selected
    }
    alert("Not yet implemented");
}

function deletePlugin(dg) {
    var row = $(dg).datagrid('getSelected');
    if (!row) {
        $.messager.alert('<?php _e("Delete Error"); ?>','<?php _e("There is no module selected"); ?>',"warning");
        return; // no way to know which module is selected
    }
    if (row.ModuleID==0) {
        $.messager.alert('<?php _e("Delete Error"); ?>','<?php _e("This entry cannot be deleted"); ?>',"error");
        return; // cannot delete default module
    }
    alert("Not yet implemented");
}