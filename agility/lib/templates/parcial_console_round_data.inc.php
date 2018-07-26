<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<form id="resultadosmanga-trs-form" method="get">
    <table width="100%">
        <tr><th colspan="6"><?php _e('Technical data on selected round'); ?>:</th></tr>
        <tr id="resultadosmanga-LargeRow">
            <td><input type="radio" name="rRecorrido" id="resultadosmanga-LargeBtn" value="0" onclick="consoleReloadParcial(0,true)"></td>
            <th id="resultadosmanga-LargeLbl" style="width:150px">Large</th>
            <td><?php _e('Distance'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_DIST_L"></td>
            <td><?php _e('Obstacles'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_OBST_L"></td>
            <td><?php _e('SCT'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_TRS_L"></td>
            <td><?php _e('MCT'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_TRM_L"></td>
            <td><?php _e('Vel'); ?>. <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_VEL_L"></td>
        </tr>
        <tr id="resultadosmanga-MediumRow">
            <td><input type="radio" name="rRecorrido" id="resultadosmanga-MediumBtn" value="1" onclick="consoleReloadParcial(1,true)"></td>
            <th id="resultadosmanga-MediumLbl">Medium</th>
            <td><?php _e('Distance'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_DIST_M"></td>
            <td><?php _e('Obstacles'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_OBST_M"></td>
            <td><?php _e('SCT'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_TRS_M"></td>
            <td><?php _e('MCT'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_TRM_M"></td>
            <td><?php _e('Vel'); ?>. <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_VEL_M"></td>
        </tr>
        <tr id="resultadosmanga-SmallRow">
            <td><input type="radio" name="rRecorrido" id="resultadosmanga-SmallBtn" value="2" onclick="consoleReloadParcial(2,true)"></td>
            <th id="resultadosmanga-SmallLbl">Small</th>
            <td><?php _e('Distance'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_DIST_S"></td>
            <td><?php _e('Obstacles'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_OBST_S"></td>
            <td><?php _e('SCT'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_TRS_S"></td>
            <td><?php _e('MCT'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_TRM_S"></td>
            <td><?php _e('Vel'); ?>. <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_VEL_S"></td>
        </tr>
        <tr id="resultadosmanga-TinyRow">
            <td><input type="radio" name="rRecorrido" id="resultadosmanga-TinyBtn" value="3" onclick="consoleReloadParcial(3,true)"></td>
            <th id="resultadosmanga-TinyLbl">Tiny</th>
            <td><?php _e('Distance'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_DIST_T"></td>
            <td><?php _e('Obstacles'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_OBST_T"></td>
            <td><?php _e('SCT'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_TRS_T"></td>
            <td><?php _e('MCT'); ?> <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_TRM_T"></td>
            <td><?php _e('Vel'); ?>. <input type="text" size="4" class="easyui-textbox trstrm" readonly="readonly" id="rm_VEL_T"></td>
        </tr>
    </table>
</form>
<script type="text/javascript" charset="UTF-8">
    $.each(['L','M','S','T'],function(index,cat){
       $.each(['DIST_','OBST_','TRS_','TRM_','VEL_'],function(index2,grad){
         $('#rm_'+grad+cat).textbox();
       });
    });
</script>