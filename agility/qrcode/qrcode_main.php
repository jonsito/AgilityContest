<?php
header('Content-Type: text/javascript');
require_once(__DIR__ . "/../server/tools.php");
?>
<div id="reader-panel" class="easyui-panel">
    <br/>
    <div id="reader" style="width:480px;margin:0 auto"></div>
    <br/>
</div>

<div id="form-panel" class="easyui-panel" style="padding:10px">
    <form id="scanned">
        <input type="hidden" id="prueba"/>
        <label for="dorsal" style="width:200px"><?php _e('Dorsal');?>:</label><input id="dorsal" type="text"/><br/>
        <label for="perro" style="width:200px"><?php _e('Dog');?>:</label><input id="perro" type="text"/><br/>
        <label for="cat" style="width:200px"><?php _e('Cat');?>:</label><input id="cat" type="text"/><br/>
        <label for="guia" style="width:200px"><?php _e('Handler');?>:</label><input id="guia" type="text"/><br/>
        <label for="club" style="width:200px"><?php _e('Club');?>:</label><input id="club" type="text"/><br/>
    </form>
</div>
<div id="footer">
    <span style="float:right;">
        <input type="button" id="cancel" value="<?php _e('Clear');?>" onClick="qrcode_clear();"/>
        <input type="button" id="send" value="<?php _e('Send');?>" onClick="qrcode_send()"/>
    </span>
</div>

    <script type="text/javascript">
        $('#form-panel').panel({width:540,footer:'#footer'});
        $('#reader-panel').panel({width:540});

        $('#dorsal').textbox();
        $('#perro').textbox();
        $('#cat').textbox();
        $('#guia').textbox();
        $('#club').textbox();

        function onScanSuccess(qrMessage) {
            // handle the scanned code as you like
            console.log(`QR matched = ${qrMessage}`);
            handleReceivedData(qrMessage);
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning
            // console.warn(`QR error = ${error}`);
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10 , qrbox: 320 }, /* verbose= */ true);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>