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
            <th id="parciales-Juez1" colspan="2" style="text-align:left"><?php _e('Judge'); ?> 1:</th>
            <th id="parciales-Juez2" colspan="2" style="text-align:right"><?php _e('Judge'); ?> 2:</th>
        </tr>
        <tr style="text-align:center">
            <td id="parciales-Distancia"><?php _e('Dist'); ?>:</td>
            <td id="parciales-Obstaculos"><?php _e('Obst'); ?>:</td>
            <td id="parciales-TRS"><?php _e('S.C.T.'); ?>:</td>
            <td id="parciales-TRM"><?php _e('M.C.T.'); ?>:</td>
            <td id="parciales-Velocidad"><?php _e('Vel'); ?>:</td>
        </tr>
        </tbody>
    </table>