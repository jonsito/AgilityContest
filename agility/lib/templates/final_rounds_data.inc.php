<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>
    <!-- Datos de TRS y TRM -->
    <table class="pb_trs">
        <tbody>
        <tr>
            <th id="finales-NombreRonda" colspan="1" style="display:none">(<?php _e('No round selected'); ?>)</th>
            <th colspan="3" style="text-align:center"><?php _e('Judge'); ?> 1:<span id="finales-Juez1"></span></th>
            <th colspan="3" style="text-align:center"><?php _e('Judge'); ?> 2:<span id="finales-Juez2"></span></th>
        </tr>
        <tr style="text-align:right">
            <td id="finales-Ronda1" colspan="2"><?php _e('Data info for round'); ?> 1:</td>
            <td><?php _e('Dist'); ?>:<span id="finales-Distancia1"></span>m</td>
            <td><?php _e('Obst'); ?>:<span id="finales-Obstaculos1"></span></td>
            <td><?php _e('S.C.T.'); ?>:<span id="finales-TRS1"></span>s</td>
            <td><?php _e('M.C.T.'); ?>:<span id="finales-TRM1"></span>s</td>
            <td><?php _e('Vel'); ?>:<span id="finales-Velocidad1"></span>m/s</td>
        </tr>
        <tr style="text-align:right">
            <td id="finales-Ronda2" colspan="2"><?php _e('Data info for round'); ?> 2:</td>
            <td><?php _e('Dist'); ?>:<span id="finales-Distancia2"></span>m</td>
            <td><?php _e('Obst'); ?>:<span id="finales-Obstaculos2"></span></td>
            <td><?php _e('S.C.T.'); ?>:<span id="finales-TRS2"></span>s</td>
            <td><?php _e('M.C.T.'); ?>:<span id="finales-TRM2"></span>s</td>
            <td><?php _e('Vel'); ?>:<span id="finales-Velocidad2"></span>m/s</td>
        </tr>
        </tbody>
    </table>