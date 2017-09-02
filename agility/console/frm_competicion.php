<!-- 
frm_competicion.php

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
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
$allowClosed=1; // enable open closed journeys, but in readonly mode
require_once("dialogs/dlg_selectJornada.inc")
?>

<script type="text/javascript">

// display prueba selection dialog
$('#seljornada-window').window({
	onClose: loadCompetitionWindow
}).window('open');

</script>

<img class="mainpage" src="/agility/server/getRandomImage.php" alt="wallpaper" width="640" height="480" align="middle"/>
