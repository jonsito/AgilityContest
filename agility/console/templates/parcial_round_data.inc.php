<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>
    <!-- Datos de TRS y TRM -->
    <table class="pb_trs">
        <tbody>
        <tr>
            <th colspan="1">
                <span id="parciales-NombreManga" colspan="1" style="display:none">(<?php _e('No round selected'); ?>)</span>
                &nbsp;
            </th>
            <th colspan="2" style="text-align:left"><?php _e('Judge'); ?> 1:<span id="parciales-Juez1"></span></th>
            <th colspan="2" style="text-align:right"><?php _e('Judge'); ?> 2:<span id="parciales-Juez2"></span></th>
        </tr>
        <tr style="text-align:center">
            <td><?php _e('Dist'); ?>:<span id="parciales-Distancia"></span>m</td>
            <td><?php _e('Obst'); ?>:<span id="parciales-Obstaculos"></span></td>
            <td><?php _e('S.C.T.'); ?>:<span id="parciales-TRS"></span>s</td>
            <td><?php _e('M.C.T.'); ?>:<span id="parciales-TRM"></span>s</td>
            <td><?php _e('Vel'); ?>:<span id="parciales-Velocidad"></span>m/s</td>
        </tr>
        </tbody>
    </table>