<?php
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
$config =Config::getInstance();
?>
<div id="reader-panel">
    <br/>
    <div id="reader" style="width:480px;height:450px;margin:0 auto"></div>
    <br/>
</div>

<div id="form-panel">
    <form id="scanned">
    <span style="float:left;position:relative;left:25%;padding:10px">
        <input type="hidden" id="qr_ID"/>
        <label for="qr_dorsal"><?php _e('Dorsal');?>:</label><input id="qr_dorsal" type="text"/><br/>
        <label for="qr_perro"><?php _e('Dog');?>:</label><input id="qr_perro" type="text"/><br/>
        <label for="qr_cat"><?php _e('Cat');?>:</label><input id="qr_cat" type="text"/><br/>
        <label for="qr_guia"><?php _e('Handler');?>:</label><input id="qr_guia" type="text"/><br/>
        <label for="qr_club"><?php _e('Club');?>:</label><input id="qr_club" type="text"/><br/>
    </span>
    </form>
</div>
<div id="footer">
    <span style="float:left;position:relative;left:50%;padding:5px">
        <a id="qr_clear" href="javascript:void(0)" class="easyui-linkbutton" onclick="qrcode_clear();"><?php _e('Clear');?></a>
        <span style="display:inline-block; width:25px">&nbsp;</span>
        <a id="qr_send" href="javascript:void(0)" class="easyui-linkbutton" onclick="qrcode_send();"><?php _e('Send');?></a>
    </span>
</div>

    <script type="text/javascript">
        $('#form-panel').panel({width:'auto',footer:'#footer'});
        $('#reader-panel').panel({width:'auto'});

        $('#qr_dorsal').textbox({disabled:true,width:40});
        $('#qr_perro').textbox({disabled:true});
        $('#qr_cat').textbox({disabled:true,width:40});
        $('#qr_guia').textbox({disabled:true});
        $('#qr_club').textbox({disabled:true});

        $('#qr_send').linkbutton({ iconCls:'icon-ok' });
        $('#qr_clear').linkbutton({ iconCls:'icon-trash' });
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
            "reader", { fps: 10 , qrbox: 320 , aspectRatio: '1.33' }, /* verbose= */ true);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>