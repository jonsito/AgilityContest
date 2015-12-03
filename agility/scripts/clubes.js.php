/*
 clubes.js

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

// ***** gestion de clubes		*********************************************************
function clubes_FederationMark(row,mask) { return ( (parseInt(row.Federations)&mask)==0)?" ":"&#x2714;";}
function clubes_Fed1(val,row,idx) { return clubes_FederationMark(row,1); } // RSCE Federation module
function clubes_Fed2(val,row,idx) { return clubes_FederationMark(row,2); } // RFEC Federation module
function clubes_Fed3(val,row,idx)  { return clubes_FederationMark(row,4); } // UCA Federation module
function clubes_Fed4(val,row,idx)  { return clubes_FederationMark(row,8); } // (not defined yet) Federation module
function clubes_Fed5(val,row,idx)  { return clubes_FederationMark(row,16); } // (not defined yet) Federation module
function clubesBaja(val,row,idx) { return ( parseInt(val)==0)?" ":"&#x26D4;"; }

function country_styler(index,row) {
    var res="margin:0px;padding:0px;height:40px;background-color:";
    // var res="background-color:";
    var c1='<?php echo $config->getEnv('easyui_rowcolor1'); ?>'; // even rows
    var c2='<?php echo $config->getEnv('easyui_rowcolor2'); ?>'; // odd rows
    if ((index & 0x01) == 0) {
        return res + c1 + ";";
    } else {
        return res + c2 + ";";
    }
}
function format_countryFlag(val,row,idx) { return '<img src="/agility/images/logos/'+val+'" height="30" style="padding-top:2px"/>'; }

/**
 * Vista preliminar del logo
 */
function setLogoPreview(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function(e) {
			$('#clubes-logo-preview').attr('src', e.target.result);
		};
		reader.readAsDataURL(input.files[0]);
	}
}

function acceptLogoPreview() {
	// insert logo into back image
	var input=document.getElementById('clubes-logo-filePreview');
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function(e) {
			$('#clubes-Logo').attr('src', e.target.result);
		};
		reader.readAsDataURL(input.files[0]);
	}
	// mark logo to be saved
	workingData.logoChanged=true;
	// and close logo dialog
	$('#clubes-logo-dialog').dialog('close');
}

/*
 * Export logo image to server and store into database
 */
function saveLogo() {
    // Get the image
	var img=$('#clubes-Logo');
	var w=img.naturalWidth(); // equivalent to img.prop('naturalWidth');
	var h=img.naturalHeight(); // equivalent to img.prop('naturalHeight');
	// copy into a canvas to send it
    var canvas = document.createElement("canvas");
    canvas.width=w;
    canvas.height=h;
    canvas.getContext("2d").drawImage(img[0], 0,0,w,h);
    $.ajax({
  		type: 'POST',
    	url: '/agility/server/database/clubFunctions.php',
    	dataType: 'text',
    	data: {
    		Operation: 'setlogo',
    		ID: $('#clubes-ID').val(),
    		imagedata: canvas.toDataURL("image/png")
    	},
    	contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
    	success: function(data) { workingData.logoChanged=false; },
    	error: function() { alert("error");	}
   	});
}

/**
 * Recalcula la tabla de clubes anyadiendo parametros de busqueda
 */
function doSearchClub() {
	// reload data adding search criteria
    $('#clubes-datagrid').datagrid('load',{
        where: $('#clubes-datagrid-search').val()
    });
}

/**
 * Abre el dialogo para crear un nuevo club
 *@param {string} dg datagrid id
 *@param {string} def nombre por defecto del club
 *@param {function} onAccept what to do when a new club is created
 */
function newClub(dg,def,onAccept){
	$('#clubes-dialog').dialog('open').dialog('setTitle','Nuevo club');
	$('#clubes-form').form('clear');
	// si el nombre del club contiene "Buscar" ignoramos
	if (!strpos(def,"Buscar")) $('#clubes-Nombre').val(def.capitalize());
	$('#clubes-Operation').val('insert');
	// select ID=1 to get default logo
	var nombre="/agility/server/database/clubFunctions.php?Operation=getlogo&ID=1&Federation="+workingData.Federation;
    $('#clubes-Logo').attr("src",nombre);
    // add onAccept related function if any
	if (onAccept!==undefined)
		$('#clubes-okBtn').one('click',onAccept);
}

/**
 * Abre el dialogo para editar un club existente
 * @var {string} dg current active datagrid ID
 */
function editClub(dg){
	if ($('#clubes-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("<?php _e('Edit Error');?>:",'<?php _e("There is no club selected"); ?>',"warning");
    	return; // no way to know which club is selected
    }
    if (isInternational(workingData.federation)) {
        $.messager.alert("<?php _e('Edit Error');?>:",'<?php _e("Country information is not editable"); ?>',"error");
        return; // do not allow editing country information
    }
    row.Operation='update';
    // use date.getTime to bypass cache
    var time=new Date().getTime();
	var nombre="/agility/server/database/clubFunctions.php?Operation=getlogo&ID="+row.ID+"&Federation="+workingData.federation+"&time="+time;
    $('#clubes-Logo').attr("src",nombre);
    $('#clubes-dialog').dialog('open').dialog('setTitle','<?php _e('Modify club data'); ?>');
    $('#clubes-form').form('load',row);
    // set up federation checkboxes
    $('#clubes-RSCE').prop('checked',( (row.Federations & 1)!=0)?true:false);
    $('#clubes-RFEC').prop('checked',( (row.Federations & 2)!=0)?true:false);
    $('#clubes-UCA').prop('checked',( (row.Federations & 4)!=0)?true:false);
}

/**
 * Funcion invocada cuando se pulsa "OK" en el dialogo de clubes
 * Ask for commit new/edit club to server
 */
function saveClub(){
    var frm = $('#clubes-form');
    if (!frm.form('validate')) return; // don't call inside ajax to avoid override beforeSend()
    if (isInternational(workingData.federation)) {
        $.messager.alert("<?php _e('Save Error');?>:",'<?php _e("Country information is not editable"); ?>',"error");
        return; // do not allow editing country information
    }
    // evaluate federation checkboxes
    // TODO: convert to federation modules
    var fed=0;
    if ( $('#clubes-RSCE').is(':checked') ) fed |=1;
    if ( $('#clubes-RFEC').is(':checked') ) fed |=2;
    if ( $('#clubes-UCA').is(':checked') ) fed |=4;
    $('#clubes-Federations').val(fed);
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/clubFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({ width:300, height:200, title: 'Error', msg: result.errorMsg });
            } else {
            	if (workingData.logoChanged==true) saveLogo();
            	var oper=$('#clubes-Operation').val();
            	if(result.insert_id && (oper==="insert") ) $('#clubes-ID').val(result.insert_id);
                $('#clubes-dialog').dialog('close');        // close the dialog
                $('#clubes-datagrid').datagrid('reload');    // reload the clubes data
            }
        }
    });
}

/**
 * Pide confirmacion para borrar un club de la base de datos
 * En caso afirmativo lo borra
 * @var {string} dg current active datagrid ID
 */
function deleteClub(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("<?php _e('Delete error');?>:",'<?php _e("There is no club selected"); ?>',"warning");
    	return; // no way to know which dog is selected
    }
    if (row.ID==1) {
    	$.messager.alert("<?php _e('Delete error');?>:",'<?php _e("This entry cannot be erased"); ?>',"error");
    	return; // cannot delete default club
    }
    // take care on International mode
    if (isInternational(workingData.federation)) {
        $.messager.alert("<?php _e('Delete error');?>:",'<?php _e("Countries cannot be deleted"); ?>',"error");
        return; // cannot delete countries
    }
    $.messager.confirm('<?php _e('Confirm'); ?>',"<?php _e('Clear club');?>"+' "'+row.Nombre+'" <?php _e('from database. Sure?');?>',function(r){
        if (!r) return;
        $.get('/agility/server/database/clubFunctions.php',{Operation:'delete',ID:row.ID},function(result){
            if (result.success){
                $(dg).datagrid('reload');    // reload the provided datagrid
            } else {
                $.messager.show({ width:300,height:200,title: 'Error',msg: result.errorMsg });
            }
        },'json');
    });
}
