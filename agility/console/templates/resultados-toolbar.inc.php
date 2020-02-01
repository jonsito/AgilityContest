
<div id="resultados-toolbar" style="width:100%;display:none"> <!-- hide until datagrid gets loaded -->
    <table style="width:100%;padding:2px;">
        <tr>
            <td><label for="resultados-selectCategoria"><?php _e('Category');?></label></td>
            <td>
                <input id="resultados-selectCategoria" name="Categoria">
            </td>
            <td style="width:10%">&nbsp;</td>
            <td>
                <a id="resultados-competicionBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-endflag'" onclick="loadCompetitionWindow();"><?php _e('Competition'); ?></a>
            </td>
            <td style="width:10%">&nbsp;</td>
            <td>
                <a id="resultados-refreshBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-reload'" onclick="reloadClasificaciones();"><?php _e('Refresh'); ?></a>
            </td>
            <td>
                <a id="resultados-verifyBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-search'" onclick="verifyClasificaciones();"><?php _e('Verify'); ?></a>
            </td>
            <td>
                <a id="resultados-emailBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-mail'" onclick="emailClasificaciones(false);"><?php _e('Mail'); ?></a>
            </td>
            <td>
                <a id="resultados-printBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-print'" onclick="$('#resultados-printDialog').dialog('open');"><?php _e('Reports'); ?></a>
            </td>
        </tr>
    </table>
</div>


<script type="text/javascript">

    $('#resultados-selectCategoria').combobox({
        width:125,
        valueField:'mode',
        textField:'text',
        panelHeight:75,
        onLoadSuccess:function() {
            // set default value to do not macht any valid round. Just to force user to select category
            $('#resultados-selectCategoria')
                .combobox('setValue',-1)
                .combobox('setText','<?php _e("Select");?>');
        },
        onSelect:function (row) { if (row.mode>=0) reloadClasificaciones(); }
    });

    addTooltip($('#resultados-competicionBtn').linkbutton(),'<?php _e("Jump to Journey development window"); ?>');
    addTooltip($('#resultados-refreshBtn').linkbutton(),'<?php _e("Update score tables"); ?>');
    addTooltip($('#resultados-verifyBtn').linkbutton(),'<?php _e("Check for dogs without registered data"); ?>');
    addTooltip($('#resultados-printBtn').linkbutton(),'<?php _e("Print scores on current round"); ?>');
    addTooltip($('#resultados-emailBtn').linkbutton(),'<?php _e("Share results and scores by electronic mail"); ?>');

</script>
