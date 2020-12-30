/*
qrcode.js

Copyright  2013-2020 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

<?php
header('Content-Type: text/javascript');
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
$config =Config::getInstance();
?>
var lastQRCodeReceived;

function handleReceivedData(msg) {
    if (msg===lastQRCodeReceived) return;
    lastQRCodeReceived=msg;
    beep();
    // received data is in format [Dorsal,DogID]
    let data=JSON.parse(msg);
    $('#qr_dorsal').textbox('setValue',data[0]);
    $('#qr_ID').val(data[1]);
    // call to server to retrieve remaining data
    $.ajax({
        type: "GET",
        url: '../ajax/database/dogFunctions.php',
        data: {
            Operation : 'getbyidperro',
            Federation: workingData.federation,
            ID	: data[1]
        },
        async: true,
        cache: false,
        dataType: 'json',
        success: function(res){
            $('#qr_perro').textbox('setValue',res["Nombre"]);
            $('#qr_cat').textbox('setValue',res["Categoria"]+" - "+res["Grado"] );
            $('#qr_guia').textbox('setValue',res["NombreGuia"]);
            $('#qr_club').textbox('setValue',res["NombreClub"]);
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            alert("c_showData() error: "+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus + " "+ errorThrown );
        }
    });
}

function qrcode_clear() {
    $('#scanned').form('clear');
    lastQRCodeReceived="";
}

function qrcode_send(){

}