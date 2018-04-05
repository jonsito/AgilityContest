<?php
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
$config =Config::getInstance();
?>
<div id="access_denied-window" style="position:relative;width:550px;height:225px;padding:10px">
    <span style="float:left"><img src="../images/sad_dog.png" alt="triste"/></span><h1>Acceso denegado</h1>
    <p>
        <?php _e('Current license does not allow view/handle training sessions');?>
    </p><p>
        <?php _e('Please contact with contest organization');?>
    </p>
</div>

<script type="text/javascript">
    var w=$('#access_denied-window').window({
        title:'License restriction',
        border:true,
        closable:true,
        collapsible:false,
        collapsed:false,
        resizable:false,
        maximizable:false,
        minimizable:false
    });
    w.window('center');
</script>