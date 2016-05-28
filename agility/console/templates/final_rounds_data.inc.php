<?php
require_once(__DIR__."/../../server/tools.php");
require_once(__DIR__."/../../server/auth/Config.php");
$config =Config::getInstance();
?>
<!-- Datos de TRS y TRM -->
<table class="pb_trs">
    <tbody>
    <tr>
        <th id="finales-NombreRonda" colspan="2" style="display:none">(<?php _e('No round selected'); ?>)</th>
        <th id="finales-Juez1" colspan="2" style="text-align:center"><?php _e('Judge'); ?> 1:</th>
        <th id="finales-Juez2" colspan="2" style="text-align:center"><?php _e('Judge'); ?> 2:</th>
    </tr>
    <tr style="text-align:right">
        <td id="finales-Ronda1"><?php _e('Data info for round'); ?> 1:</td>
        <td id="finales-Distancia1"><?php _e('Distance'); ?>:</td>
        <td id="finales-Obstaculos1"><?php _e('Obstacles'); ?>:</td>
        <td id="finales-TRS1"><?php _e('Standard C. Time'); ?>:</td>
        <td id="finales-TRM1"><?php _e('Maximum C. Time'); ?>:</td>
        <td id="finales-Velocidad1"><?php _e('Speed'); ?>:</td>
    </tr>
    <tr style="text-align:right">
        <td id="finales-Ronda2"><?php _e('Data info for round'); ?> 2:</td>
        <td id="finales-Distancia2"><?php _e('Distance'); ?>:</td>
        <td id="finales-Obstaculos2"><?php _e('Obstacles'); ?>:</td>
        <td id="finales-TRS2"><?php _e('Standard C. Time'); ?>:</td>
        <td id="finales-TRM2"><?php _e('Maximum C. Time'); ?>:</td>
        <td id="finales-Velocidad2"><?php _e('Speed'); ?>:</td>
    </tr>
    </tbody>
</table>