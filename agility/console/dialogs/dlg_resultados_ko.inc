<!--
dlg_resultados_ko.inc

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

<!--  dialogo de presentacion de resultados parciales de una manga KO -->
<div id="resultadosko-dialog" style="width:800px;height:450px;padding:0 10px 100px 10px">
</div>
<script type="text/javascript">

    $('#resultadosko-dialog').dialog({
        collapsible:	false,
        minimizable:	false,
        maximizable:	false,
        resizable:		true,
        iconCls:		'icon-endflag',
        closed:			true,
        modal:			true
    });

</script>