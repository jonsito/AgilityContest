/*
common.js

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

/**
 * checkf if a string starts with requested one
 */
if (typeof String.prototype.startsWith != 'function') {
    String.prototype.startsWith = function (str){
        return this.slice(0, str.length) == str;
    };
}

/**
 * Capitalize first letter on every word of provided string
 * Lowercase others
 */
if (typeof String.prototype.capitalize != 'function') {
	String.prototype.capitalize = function (){
		return this.toLowerCase().replace( /\b\w/g, function (m) {
			return m.toUpperCase();
		});
	};
}
/**
 * Evaluate start Time (used as base time in tablet/chrono/videowall
 */

var startDate=Date.now();

/**
 * Set text of 'header' field on main window
 * @param {string} msg text to be shown
 */
function setHeader(msg) { $('#Header_Operation').html('<p>'+msg+'</p>'); }

// permisos de ejecucion
var access_perms = {
    ENABLE_TEAM3    :1,  // permite gestionar pruebas de equipos 3
    ENABLE_TEAM4    :2,  // permite gestionar pruebas de equipos 4
    ENABLE_KO       :4,  // permite gestionar pruebas K.O
    ENABLE_SPECIAL  :8,  // permite gestionar pruebas de mangas multiples
    ENABLE_VIDEOWALL:16, // permite acceso desde videomarcador
    ENABLE_PUBLIC   :32, // permite acceso publico web
    ENABLE_CHRONO   :64 // permite gestion desde cronometro
};

/**
 * returns Categoria's long string according provided categoria and fereration
 * @param {string} cat Categoria
 * @param {int} fed Federation, as indexed in nombreCategorias
 * @returns {string} requested result, or original one if not found
 */
function toLongCategoria(cat,fed) {
    if (typeof(ac_fedInfo[parseInt(fed)])==='undefined') return cat;
    if (typeof(ac_fedInfo[parseInt(fed)].ListaCategorias[cat])==='undefined') return cat;
    return ac_fedInfo[parseInt(fed)].ListaCategorias[cat];
}

function isTeam(tipomanga) {
    switch(parseInt(tipomanga)) {
		case 8: case 9: case 13: case 14: return true;
		default: return false;
    }
}

/**
 * check if provided jornada has grades in their rounds
 * @param {object} jornada Journey data
 * @returns {boolean}
 */
function hasGradosByJornada(jornada) {
    if (parseInt(jornada.Equipos3)!=0) return false;
    if (parseInt(jornada.Equipos4)!=0) return false;
    if (parseInt(jornada.Open)!=0) return false;
    if (parseInt(jornada.KO)!=0) return false;
    if (parseInt(jornada.Especial)!=0) return false;
    return true;
}

/**
 * Check if provided jornada has Team rounds
 * @param {object} jornada Journey data
 * @returns {boolean}
 */
function isTeamByJornada(jornada) {
    if (parseInt(jornada.Equipos3)!=0) return true;
    if (parseInt(jornada.Equipos4)!=0) return true;
    return false;
}

function isJornadaOpen() { return (workingData.datosJornada.Open==1); }
function isJornadaEq3() { return (workingData.datosJornada.Equipos3==1); }
function isJornadaEq4() { return (workingData.datosJornada.Equipos4==1); }

function fedName(fed) {
	return ac_fedInfo[fed].Name;
}

function isInternational(fed){
	return (parseInt(ac_fedInfo[fed].International)!=0)?true:false;
}

// lista de dialogos a limpiar cada vez que se recarga la pantalla
var slaveDialogs = {};

//musiquita para el tablet
// usamos closures para garantizar que se crea y libera correctamente
var Sound = (function () {
	var sndData = "data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=";  
	//var sndData = "data:audio/mpeg;base64,//uQZAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAASW5mbwAAAA8AAAAHAAANDgAkJCQkJCQkJCQkJCQkJElJSUlJSUlJSUlJSUlJbW1tbW1tbW1tbW1tbW2SkpKSkpKSkpKSkpKSkpK2tra2tra2tra2tra2ttvb29vb29vb29vb29vb//////////////////8AAAA5TEFNRTMuOTlyAaoAAAAALFEAABSAJAYxTgAAgAAADQ5RrpH7AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//uQZAAAA0lgRw0YoAI0rNewoBQAEumDZ7mIgBDPQiLDACAAAi/GHgMBhekwGAwGAwGAyaZCCAcFCMofAAOCjHEw+dznPkIQgud/yEIQhCEIc7oQhGkI05znnOc5/+d6HOJh8Pi5zv9CNI1TnP6EIQjHDgcIT7VO8hKN/znOd0EA+Hxd//1OHBQnh4eAf////////////+c5znOf/Qhzn/////53/n/yE1Oc5yCAcDgcFCNITOc4fD4cAQAAAAgo2IGkkrbJKm5HHK7E0Wg0Y5DpRNW1f2MXh8GAXsVJJ5vs4gdJRWXbOE4OUOfTPDyQMiAxEggguHTOGhJE0OcGE8zHYTibpqB5J0vHSkJLqOmhcZNjMDLD3BwlwhpVFCiotz5XLjE4WSfIMLEdMVkyI4H2VPJs37V5W8uPq9JLezLrUyGktGrL7Nq+r3/p6m0TdXWr/NG0f/+U///////////T9P6+1+n3vrt6f3b/7+v+1FZmZAf9CpFNX+9HZVIxnBf///uxyf///kUIFQNAAVhxCggAAAMFJ6Vtde1kTSi///uSZAoB9CVn0X9ugAI1LWhg4AgAD2mfRc2+aQC5NqGAAJvYwpJFZGgOvvM7PJNEslqJkisyIsKSAc0DE6LJJOgyCvUmgAgsLWQU8pVv6zEckBAQipOtV/1mInkMAGpOs3/ooh/ROCBqv/62DUxomSH/6nDkyCpJf/qSDV5OpJK/+ovB65ikbP/9IvCD3v/9UsCjpM/////////9v6f+taldf36s6fdnPZ96WRLLfX1fv/+/X5nQjStXh3UgAFVBbO4kO5EcQOyDFB2IGioIIgHiHC00hrbII2sAF4wMswzisMoyD2KslUGBhjbLMj80AW0BaxqlSQWtNvrLIGWYvCWZa0/2qkYBAp0+3/6Y5AIon1q//img4BYPe366wmE6tv9upMP1HszqZt/1ICACU7/9ajETY08y/661EqLEmtm3/1xM68v6WXusVKg6ZkeRFK2glWVH/T//8tef//fn8/ftZb9ph5lGpazNXiTwwi9PCz9VG8AAfIAKCCgAAEQEJ5mtoGpjyb7KxwoANupoLAoBAJsceAgHAQCQ1ASGIjwYxP/7kmQXANSzZ9R7jD1QJc14cAAFjlCFpTMNPQMA0DXiBACKeSKAMv4X3Q6Xa1LLgoAWryC3gPZcQzIxPf76XYE8iBS87/XzczZ7+ucADLL5Obbvvm5uMj0W1VL03Nytbdel0gmgGmuztqiKmzg9HEVF/qiooRC52/9544JS//9xFMT/9UHSLu3/2dBsv2Xdm/vfZ0/732pdFf0////2yvU+8rGc+iqpLGQTEB4eMGA4oweABKgAEbo2Iou6vI3TuM0ohFSZkoQacOdZDsJixDILNGHOYUKoWNNNKJGJMDSFYaIQkPIso67qIN8okoQZoVtrWCbIfFehY5Yn6Z5Z0smhmyHjhtfxV9LHkvQ1uVqX32Z5Xq4+5huO6nEJZuTZVGqVpVN9aalf/r5i4jg7Vd6qefGD71JZln/8X9+//76l/zf7/8KD9A+hOrq+nZDPv1Zfdf7f+v9SOstCM1FVlaORmFuRgrBHMiC5FQCy6iQdvGxhsJl01fv49ExAkqhmWxuMtYZxE4Zisg1DibFaZJgaHqrbzK00QdBXA5KZG5TH01j/+5JkG4AEF2nJJXEAAjFNeJGgCABWHZtLuZiAEK06o4MAIACtFzcPERd9cQVBSM3EtdFXWzbHEXBVTGOurj0lJUJ0ESZWEa+KbSENjO7Xub0i00q1ZofilunSWuGu7d2i5qEpp+2e9KqNYRrlCJ3UIH/9P//6+jf///2/vX1b6U6plTv61W/qd/Wje3//7V667X6bozPZZDnOhhQiAVJERZJqUksWSyUSjcSaLRCJRt5Fc1gbinbuC9AcQFaQHTvIcBEgzuPGUi4snRYxCUcwfQsgrsOwgYWhgXI8OSxTLpXIGWzQQmJkZ4MTqWk51ZvJ8yEWLBPHByjpk06oghcPIMTANpiAEol42SstNCaE4emlhHZECKmpiai511tQRNFGi3ZNNaKZ4uqMWJ9mVTUvRV1p09PoIyfQ8nzn//0GoITSvsmjWX1IssxN6NZj////6f/////////////9X/X/9////v//vdORm9drUyX+pkap5QtP///3BAP/8uMVADAASEAAAAA0MqAw8zahXau1wS3Iikw6ZLeyGDnazhqd9I2K//uSZBIB1JtnTPduYAAtDNiR4AgAUbGdM81JtQDAtiIIEBdJBPOiTInkDB8C4SCl1SSd3qUqYkyMqCLIgiXTVJVWvpsTJBQGMFk1ZG39NEmRSINiDxijt7uuieDcjKHjW3/okyGejaLp5A1T/1okyHojfOpt9aS9NZdElNr//ZE1D3EkaLf1ayGilKkP+9FEXZgxDgf//+1/03ulNP///t1v//6+l/9////+z9f//p7t1tcxXY5GRUZpUD1EnqrB4qYhDOpgEC3m9ehVWxAMYWmANovkdaZlb714FobdPhDMr5MS1pRgGSmsPY5N0NroJlADfA6ZkKKkzZ3egzrJoALy4ki/6+gsWgCUnzferXrTWoFCBwOGrdBvXWsEqGczRUer6GnRUBgEKtJTa3dfWoE6ZkF/vapZwFfSmyuyDVqasfAcqCF6f/UIUWjcQDB//+fz//36gy//f++nTt9ZJ/59O3/b////Tb3u/oRaXYrCVUUaZSxYx1W4iFh9sxlwAFlxBiptIAtAXgPxQA00yJ80pueZAhYLJXfh9tlhGIK0lv/7kmQSAASpZ1D9aaAALa1IkaAIAFJBoR4ZuIAAyzQhgwIgAKy04cAcsFrHsYFxY7wHMJ0XDRi+bqMDRSCFacuApo8Qu4y0iQJRAzJdFbqQQWJOEQxcQprUggpBBZmmYBMAWayXTZBlu9OyCBmFeGpTMt3Uptq3OFAdTNal7/ZBliZFiCmv/6A7d//6ZO//84b//+dPEB/////v2//////276femv9+yvX2t37////t/9mruqtudVOSHkElFmQ4tnOMIN0mzTgFNJq/+ZOeGFkkO//hx8YIB0uH+BQgjKKsUEJ1D5kkk+HEjXDlQHplt8RkGMhCEfI8q1/kWQGWG6fGZ//E3FgZITabjMmnRXS/lUZogKBRFqLAlIUKqr//lYWSRxwc4fyLENHwaDnLV///kPLZBSYJwXETRAhaRlCgVjMgwyqK2SV9Tor//8ihVLZO//hT8//3/b/Z//un/vp//pQzP//0MfKUy///qhWUzqRP///mRTKwUwlzARpf////zHEiP/6lTEFNRTMuOTkuNVVVVVVVVVVVVVVVVVVVVVX/+5JkDQ/wAABpBwAACAAADSDgAAEAAAGkAAAAIAAANIAAAARVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV";
	var df = document.createDocumentFragment();
	return function Sound() {
		var snd = new Audio(sndData);
		df.appendChild(snd); // keep in fragment until finished playing
		snd.addEventListener('ended', function () {df.removeChild(snd);});
		snd.play();
		return snd;
	}
}());
// then do it
// var snd = Sound("data:audio/wav;base64," + base64string);

function beep() { Sound(); }

var ac_config={};
function loadConfiguration(callback) {
	$.ajax({
		type: "GET",
		url: "/agility/server/adminFunctions.php",
		data: {
			'Operation' : 'loadConfig'
		},
		async: true,
		cache: false,
		dataType: 'json',
		success: function(config){
			if ( typeof (config.program_name) !== "undefined") {
				ac_config=config;
				// extra configuration data to speedup
				ac_config.numdecs=(ac_config.crono_miliseconds=="0")?2:3;
				if (typeof(callback)!=="undefined") callback(ac_config);
			} else {
				$.messager.alert('<?php _e("Error"); ?>','<?php _e("LoadConfig(): cannot retrieve configuration from server"); ?>',"error")
			}
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			alert("error: "+textStatus + " "+ errorThrown );
		}
	});
}

var ac_regInfo={};
function getLicenseInfo() {
	$.ajax({
		type: "GET",
		url: "/agility/server/adminFunctions.php",
		data: {
			'Operation' : 'reginfo'
		},
		async: true,
		cache: false,
		dataType: 'json',
		success: function(reginfo){
			if ( typeof (reginfo.Serial) !== "undefined") {
				ac_regInfo=reginfo;
			} else {
				$.messager.alert('<?php _e("Error"); ?>','<?php _e("getLicenseInfo(): cannot retrieve License info from server"); ?>',"error")
			}
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			alert("error: "+textStatus + " "+ errorThrown );
		}
	});
}

var ac_fedInfo={};
function getFederationInfo() {
	$.ajax({
		type: "GET",
		url: '/agility/modules/moduleFunctions.php',
		data: {	'Operation' : 'list' },
		async: true,
		cache: false,
		dataType: 'json',
		success: function(list){
			if ( typeof (list) !== "array") {
				ac_fedInfo=list;
				initWorkingData(); // must be called _after_ data is loaded
			} else {
				$.messager.alert('<?php _e("Error"); ?>','<?php _e("getFederationsInfo(): cannot retrieve federations info from server"); ?>',"error")
			}
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			alert("error: "+textStatus + " "+ errorThrown );
		}
	});
}

function checkPermissions(perms, callback) {
	$.ajax({
		type: "GET",
		url: '/agility/server/adminFunctions.php',
		data: {	'Operation' : 'access','Perms':perms },
		async: true,
		cache: false,
		dataType: 'json',
		success: function(data){
			callback( (data.errorMsg)?false:true);
		},
		error: function(XMLHttpRequest,textStatus,errorThrown) {
			alert("error: "+textStatus + " "+ errorThrown );
		}
	});
}

/**
 * Load html contents from 'page' URL and set as contents on '#contenido' tag
 * @param page URL where to retrieve HTML data
 * @param title new page title
 * @param slaves list of dialogs to .destroy() on next loadContents
 */
function loadContents(page,title,slaves) {
    var cont=$('#contenido');
	$('#mymenu').panel('collapse');
	$.each(slaveDialogs,function(key,val) {
		$(val).dialog('panel').panel('clear'); 
	} ); 
	slaveDialogs=(typeof(slaves)==='undefined')?{}:slaves;
	cont.panel('clear');
	cont.panel('refresh',page);
	setHeader(title);
}

/**
 * Poor's man javascript implementation of php's replaceAll()
 */
function replaceAll(find,replace,from) {
	return from.replace(new RegExp(find, 'g'), replace);
}

/**
 * Poor's man javascript implementation of php's strpos()
 * @param {string} pajar
 * @param {string} aguja
 * @param {int} offset
 * @returns position or -1 if not found
 */
function strpos (pajar, aguja, offset) {
	var i = (pajar + '').indexOf(aguja, (offset || 0));
	return i === -1 ? false : i;
}

/*
 * A downcounter in seconds
 * from: http://stackoverflow.com/questions/1191865/code-for-a-simple-javascript-countdown-timer
 * usage:
 * var myCounter = new Countdown({  
 *   seconds:5,  // number of seconds to count down
 *   onUpdateStatus: function(sec){console.log(tenths of seconds);}, // callback for each second
 *   onCounterEnd: function(){ alert('counter ended!');} // final action
 * });
 * myCounter.reset(secs);
 * myCounter.start();
 */
function Countdown(options) {
	var timer=null;
	var instance = this;
	var seconds = options.seconds || 15;
	var count = 0;
	var updateStatus = options.onUpdateStatus || function () {};
	var counterEnd = options.onCounterEnd || function () {};
	var onstart = options.onStart || function () {};
	var onstop = options.onStop || function () {};

	function decrementCounter() {
		if (count <= 0) {
			counterEnd();
			instance.stop();
		} else {
			updateStatus(count);
			count=count - 0.5;
		}
	}

	this.start = function () {
		onstart();
		if (timer!=null) clearInterval(timer);
		count = seconds*10; // count tenths of seconds
		timer = setInterval(decrementCounter, 50);
	};

	this.stop = function () {
		onstop();
		if (timer!=null) clearInterval(timer);
		count=0;
		updateStatus(count);
	};

	// get/set start count
	this.reset = function (secs) {
		if (typeof(secs) === 'undefined') return seconds;
		var s=parseInt(secs);
		if (s>0) seconds=s;
		return seconds;
	};

	// get/set current count
	this.val = function(secs) {
		if (typeof(secs) !== 'undefined') count=secs*10;
		return count;
	}
}

/**
 * indica si una variable, funcion u objeto está definido
 * @param {string} variable objeto buscar
 * @returns {Boolean} true si existe el objeto 'variable'
 */
function isDefined(variable) { return (typeof(window[variable]) !== "undefined");}

/**
 * Convierte los campos de un formulario en un array
 * @param {string} formId ID del formulario
 * @returns {object} objeto que contiene los datos
 */
function formToObject(formId) {
    var formObj = {};
    var inputs = $(formId).serializeArray();
    $.each(inputs, function (i, input) {
        formObj[input.name] = input.value;
    });
    return formObj;
}

/**
 * Clone (deep-clone) an object
 * Posibly not the most efficient way to do, but enought for me
 * @param obj Object to be cloned
 */
function cloneObj(obj) {
	return JSON.parse(JSON.stringify(obj));
	// return jQuery.extend(true, {}, obj);
}

/*
 * Check for execution in mobile devices
 */
function isMobileDevice() { 
	 if( navigator.userAgent.match(/Android/i)
	 || navigator.userAgent.match(/webOS/i)
	 || navigator.userAgent.match(/iPhone/i)
	 || navigator.userAgent.match(/iPad/i)
	 || navigator.userAgent.match(/iPod/i)
	 || navigator.userAgent.match(/BlackBerry/i)
	 || navigator.userAgent.match(/Windows Phone/i)
	 ){
	    return true;
	  }
	 else {
	    return false;
	 }
}

/**
 * A partir de prueba, jornada y manga obtiene y despliega los datos necesarios en workingData
 * @param {int} prueba Prueba ID
 * @param {int} jornada Jornada ID
 * @param {int} manga Manga ID
 * @param {function} callback what to do when finished
 */
function setupWorkingData(prueba,jornada,manga,callback) {

	// set default values that allways should exist (default, un-erasable, and closed contest)
	if (typeof(prueba) === 'undefined') prueba = 1;
	if (typeof(jornada) === 'undefined') jornada = 1;
	if (typeof(manga) === 'undefined') manga = 1;

	// obtenemos datos de la manga
	$.ajax({
		type: 'GET',
		url: "/agility/server/database/mangaFunctions.php",
		dataType: 'json',
		data: {Operation: 'getbyid', Jornada: jornada, Manga: manga},
		success: function (data) {
			if (data.errorMsg) {$.messager.alert('<?php _e("Error"); ?>', data.errorMsg, "error");	return false; }
			setManga(data);
			return false;
		}
	}).always(function(){
		// obtenemos datos de la jornada
		$.ajax({
			type: 'GET',
			url: "/agility/server/database/jornadaFunctions.php",
			dataType: 'json',
			data: {Operation: 'getbyid', Prueba: prueba, ID: jornada},
			success: function (data) {
				if (data.errorMsg) { $.messager.alert('<?php _e("Error"); ?>', data.errorMsg, "error"); return false; }
				setJornada(data);
				return false;
			}
		}).always(function(){
			// obtenemos datos de los equipos de la jornada
			$.ajax({
				type: 'GET',
				url: "/agility/server/database/equiposFunctions.php",
				dataType: 'json',
				data: {Operation: 'enumerate', Prueba: prueba, Jornada: jornada},
				success: function (teams) {
					if (teams.errorMsg) { $.messager.alert('<?php _e("Error"); ?>', teams.errorMsg, "error"); return false; }
					workingData.teamsByJornada = {};
					$.each(teams.rows, function (idx, row) {
						workingData.teamsByJornada[row.ID] = row;
					});
					return false; // prevent default fireup of event trigger
				}
			}).always(function() {
				// obtenemos datos de la prueba
				$.ajax({
					type: 'GET',
					url: "/agility/server/database/pruebaFunctions.php",
					dataType: 'json',
					data: {Operation: 'getbyid', ID: prueba},
					success: function (prueba) {
						if (prueba.errorMsg) { $.messager.alert('<?php _e("Error"); ?>', prueba.errorMsg, "error"); return false; }
						// store prueba data
						setPrueba(prueba);
						return false; // prevent default fireup of event trigger
					}
				}).always(function() {
					if (typeof(callback) !=='undefined') callback();
				});
			});
		});
	});
}

/**
 * Set federation parameters of requested ID
 * By requesting data from module info
 * @param {int} f Federation ID
 */
function setFederation(f) {
	var fed=null;
	// iterate ac_fedInfo until ID matches
	for ( var i=0;i<10;i++) {
        if (typeof(ac_fedInfo[i])==="undefined") continue; // no federation with provided ID
		if (ac_fedInfo[i].ID==0) fed=ac_fedInfo[i]; // mark default and continue search
		if (ac_fedInfo[i].ID==f) { fed=ac_fedInfo[i]; break; } // found
	}
	workingData.federation= fed.ID;
	workingData.datosFederation=fed;
	// set background logo
	$('#logo_Federation').prop('src',fed.Logo);
}

/**
 * Set prueba from selection dialogs
 * On change also reset jornada info
 * @param {object} data prueba data
 */
function setPrueba(data) {
	workingData.prueba=Number(data.ID);
	workingData.nombrePrueba=data.Nombre;
	workingData.datosPrueba=data;
	setFederation(data.RSCE);
}

/**
 * Set jornada from selection dialogs
 * On change also reset manga info
 * @param {object} data jornada info
 */

function setJornada(data) {
	workingData.jornada=Number(data.ID);
	workingData.nombreJornada=data.Nombre;
	workingData.datosJornada=data;
}

function setManga(data) {
    if (typeof(data) !== 'undefined') {
        workingData.manga = data.ID;
        workingData.nombreManga = data.Nombre;
        workingData.datosManga = data
    } else {
        workingData.manga = 0;
        workingData.nombreManga = "";
        workingData.datosManga = {};
    }
}

var workingData = {};
/**
 * @param {int} id SessionID
 * Initialize working data information object
 */
function initWorkingData(id) {
	workingData.logoChanged=false;
	workingData.perro= 0; // IDPerro del perro en edicion
	workingData.guia= 0; // ID del guia en edicion
	workingData.club= 0; // ID del club activo
	workingData.juez= 0; // ID del juez activo
	workingData.prueba= 0; // ID de la prueba en curso
	workingData.nombrePrueba= ""; // nombre de la prueba en curso
	workingData.jornada= 0; // ID de la jornada en curso
	workingData.nombreJornada= ""; // nombre de la jornada
	workingData.manga= 0; // ID de la manga en curso
	workingData.nombreManga = ""; // denominacion de la manga
	workingData.manga2= 0; // ID de la segunda manga para el calculo de resultados
	workingData.tanda=0; // tanda (pareja manga/categoria) activa
	workingData.nombreTanda = ""; 
	workingData.sesion=0; // ID de la sesion para videomarcadores
	workingData.nombreSesion=""; // nombre de la sesion
	setFederation(0); // defaults to RSCE;
	if (typeof(workingData.federation)==="undefined") setFederation(0); // select RSCE as default federation
	if (typeof(workingData.datosPrueba)==="undefined") workingData.datosPrueba= {}; // last selected prueba data
	if (typeof(workingData.datosJornada)==="undefined") workingData.datosJornada= {}; // last selected jornada data
	if (typeof(workingData.datosManga)==="undefined") workingData.datosManga= {}; // last selected jornada data
    if (typeof(workingData.datosRonda)==="undefined") workingData.datosRonda= {}; // last selected ronda (grade, manga1, manga2)
    if (typeof(workingData.teamsByJornada)==="undefined") workingData.teamsByJornada= {}; // last selected ronda (grade, manga1, manga2)
	if (typeof(id)!=="undefined") {
		$.ajax({
			url: '/agility/server/database/sessionFunctions.php',
			data: { Operation: 'getByID', ID: id },
			dataType: 'json',
	        async: false, // this may generate a warning in js interpreter; ignore it
	        cache: false,
	        timeout: 30000,
			success: function(data) {
				workingData.perro	= data.Perro;
				workingData.prueba	= data.Prueba;
				workingData.nombrePrueba= ""; // nombre de la prueba
				workingData.jornada	= data.Jornada;
				workingData.nombreJornada= ""; // nombre de la jornada
				workingData.manga	= data.Manga;
				workingData.nombreManga = ""; // denominacion de la manga
				workingData.manga2= 0; // ID de la segunda manga para el calculo de resultados
				workingData.tanda	= data.Tanda;
				workingData.nombreTanda = ""; 
				workingData.sesion	= data.ID;
				workingData.nombreSesion	= data.Nombre;
				workingData.datosSesion = data;
			},
			error: function(msg){ alert("error setting workingData: "+msg);}
		});
	}
}

var authInfo ={};
function initAuthInfo(id) {
	authInfo.ID=0;
	authInfo.Login="";
	authInfo.Gecos="";
	authInfo.SessionKey=null;
	authInfo.Perms=5;
	authInfo.SessionID=0;
	if (typeof(id)!=="undefined") {
		authInfo.ID=id.UserID;
		authInfo.Login=id.Login;
		authInfo.Gecos=id.Gecos;
		authInfo.SessionKey=id.SessionKey;
		authInfo.Perms=id.Perms;
		authInfo.SessionID=id.SessionID;
	}
}


/**
 * Used to evaluate position, width and heigh on an element to be 
 * layed out in a grid
 * @param dg datagrid { cols, rows }
 * @param id id of element to be layed out
 * @param x start col
 * @param y start row
 * @param w nuber of cols
 * @param h number of rows
 */
function doLayout(dg,id,x,y,w,h) {
	var elem=$(id);
	elem.css('display','inline-block');
	elem.css('position','absolute');
	elem.css('float','left');
	// elem.css('padding','5px');
	elem.css('-webkit-box-sizing','border-box');
	elem.css('-moz-box-sizing','border-box');
	elem.css('box-sizing','border-box');
	elem.css('left',  ((25+x*100)/dg.cols)+'%');
	elem.css('top',   ((100+y*100)/dg.rows)+'%');
	elem.css('width', ((w*100)/dg.cols)+'%');
    elem.css('height',((h*100)/dg.rows)+'%');
    // elem.css('line-height',((h*100)/dg.rows)+'%');
    // elem.css('vertical-align','bottom');
}

/**
 * Add a tooltip provided element, with given text
 * @param {object} obj Element suitable to add a tooltip
 * @param {string} text Data text to be shown
 */
function addTooltip(obj,text) {
	obj.tooltip({
    	position: 'top',
		deltaX: 30, // shift tooltip 30px right from top/center
    	content: '<span style="color:#000">'+text+'</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
    	}
	});
}

/**
 * Add support for img.naturalWidth and img.naturalHeight in browsers that
 * lack of this (IE<9)
 */

(function($) {
	function img(url) {	var i=new Image; i.src=url;	return i; }
 
	if ('naturalWidth' in (new Image)) {
		$.fn.naturalWidth = function() { return this[0].naturalWidth; };
		$.fn.naturalHeight = function() { return this[0].naturalHeight; };
	} else {
		$.fn.naturalWidth = function() { return img(this.src).width; };
		$.fn.naturalHeight = function() { return img(this.src).height; };
	}
})(jQuery); 

/**
 * Function : print_r()
 * Arguments: The data - array,hash(associative array),object
 *    The level - OPTIONAL
 * Returns  : The textual representation of the array.
 * This function was inspired by the print_r function of PHP.
 * This will accept some data as the argument and return a
 * text that will be a more readable version of the
 * array/hash/object that is given.
 * Docs: http://www.openjs.com/scripts/others/dump_function_php_print_r.php
 */
function print_r(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) === 'object') { //Array/Hashes/Objects
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...<br />";
				dumped_text += print_r(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"<br />";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

/**
 * display selected cell including hidden fields
 * @param dg jquery datagrid object
 */ 
function displayRowData(dg) {
	var selected = dg.datagrid('getSelected');
	if (!selected) return;
	var index = dg.datagrid('getRowIndex', selected);
	$.messager.alert("Row Info",
			"<p>Contenido de la fila<br /></p><p>"+print_r(selected)+"</p>",
			"info",
			function() {
				dg.datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
				dg.datagrid('selectRow', index);
			}
	);
}

/**
 * reload main datagrid for perros/guias/clubes/jueces/pruebas/inscripciones
 * adding search criteria
 * @param {string} dg Datagrid name
 * @param {string} op Operation
 * @param {boolean} clear on true clear search field before query
 */
function reloadWithSearch(dg,op,clear) {
	var w=$(dg+'-search').val();
    var fed=workingData.federation;
    if (dg=='#clubes-datagrid') fed=-1;
    if (dg=='#jueces-datagrid') fed=-1;
	if (strpos(w,"-- Buscar --",0)) w='';
	if (clear==true) w='';
    $(dg).datagrid(
    	'load',
    	{ 
    	Operation: op, 
    	where: w, 
    	Federation: fed,
        Prueba: workingData.prueba,
        Jornada: workingData.jornada
    	} 
    );
    if (clear==true) $(dg+'-search').val('---- Buscar ----');
}

/**
 * activa teclas up/down para navegar por el panel , esc para cerrar y ctrl+shift+enter para ver fila
 * @param {string} datagrid '#datagrid-name'
 * @param {string} dialog '#dialog-name' or null if no close on escape
 * @param {function} onEnter function to be called on enter press
 */
function addSimpleKeyHandler(datagrid,dialog,onEnter){
	$(datagrid).datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){

		// move cursor
	    function selectRow(t,up){
	    	var count = t.datagrid('getRows').length;    // row count
	    	var selected = t.datagrid('getSelected');
	    	if (selected){
	        	var index = t.datagrid('getRowIndex', selected);
	        	index = index + (up ? -1 : 1);
	        	if (index < 0) index = 0;
	        	if (index >= count) index = count - 1;
	        	t.datagrid('clearSelections');
	        	t.datagrid('selectRow', index);
	    	} else {
	        	t.datagrid('selectRow', (up ? count-1 : 0));
	    	}
		}
		//main code
		var t = $(datagrid);
	    switch(e.keyCode){
	    case 27:    /* Esc */   if (dialog!==null) $(dialog).window('close'); return false;
	    case 38:	/* Up */	selectRow(t,true); return false;
	    case 40:    /* Down */	selectRow(t,false); return false;
	    case 13:	/* Enter */	if (e.ctrlKey) { displayRowData(t); return false; }
	    			if (typeof(onEnter)!=='undefined') onEnter(datagrid,$(datagrid).datagrid('getSelected'));
                    return false;
	    default:    // no break
	    			return false;
	    }
	});
    return false;
}

/**
 * function to select next row in datagrid
 * @param {string} datagrid name
 * @return selected data or null
 */
function selectNextRow(datagrid) {
	var t = $(datagrid);
	var count = t.datagrid('getRows').length;    // row count
	var selected = t.datagrid('getSelected');
	if (selected){
    	var index = t.datagrid('getRowIndex', selected);
    	index = index+1;
    	t.datagrid('clearSelections');
    	if (index >= count) return null; // at end of rows
    	t.datagrid('selectRow', index);
    	return t.datagrid('getSelected');
	} else {
		// no row selected: choose first one
    	t.datagrid('selectRow',0);
    	return t.datagrid('getSelected');
	}
}

/**
 * Generic function for adding key handling to datagrids
 * 
 * Create key bindings for edit,new,delete, and search actions on datagrid
 * assume that search textbox has 'dgid'-search as id
 * Called functions have a pointer to base datagrid
 * @param {string} dgid id(ie xxxx-datagrid) 
 * @param {function} insertfn new/insert function(dgid,searchval)
 * @param {function} updatefn edit function(dgid)
 * @param {function} deletefn delete function(dgid)
 * @returns true on success, else false
 */
function addKeyHandler(dgid,insertfn,updatefn,deletefn) {
    // activa teclas up/down para navegar por el panel
    $(dgid).datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
    	
    	// up & down
        function selectRow(t,up){
        	var count = t.datagrid('getRows').length;    // row count
        	var selected = t.datagrid('getSelected');
        	if (selected){
            	var index = t.datagrid('getRowIndex', selected);
            	index = index + (up ? -1 : 1);
            	if (index < 0) index = 0;
            	if (index >= count) index = count - 1;
            	t.datagrid('clearSelections');
            	t.datagrid('selectRow', index);
        	} else {
            	t.datagrid('selectRow', (up ? count-1 : 0));
        	}
    	}
    	
        // pgup & pg down
		function selectPage(t,offset) {
        	var count = t.datagrid('getRows').length;    // row count
        	var selected = t.datagrid('getSelected');
        	if (selected){
            	var index = t.datagrid('getRowIndex', selected);
            	switch(offset) {
            	case 1: index+=10; break;
            	case -1: index-=10; break;
            	case 2: index=count -1; break;
            	case -2: index=0; break;
            	}
            	if (index<0) index=0;
            	if (index>=count) index=count-1;
            	t.datagrid('clearSelections');
            	t.datagrid('selectRow', index);
        	} else {
            	t.datagrid('selectRow', 0);
        	}
		}
		
    	var t = $(dgid);
        switch(e.keyCode){
        case 38:	/* Up */	 selectRow(t,true); return false;
        case 40:    /* Down */	 selectRow(t,false); return false;
        case 13:	/* Enter */	 if (e.ctrlKey) displayRowData(t); else updatefn(dgid); return false;
        case 45:	/* Insert */ insertfn(dgid,$(dgid+'-search').val()); return false;
        case 46:	/* Supr */	 deletefn(dgid); return false;
        case 33:	/* Re Pag */ selectPage(t,-1); return false;
        case 34:	/* Av Pag */ selectPage(t,1); return false;
        case 35:	/* Fin */    selectPage(t,2); return false;
        case 36:	/* Inicio */ selectPage(t,-2); return false;
        case 9: 	/* Tab */
            // if (e.shiftkey) return false; // shift+Tab
            return false;
        case 16:	/* Shift */
        case 17:	/* Ctrl */
        case 18:	/* Alt */
        case 27:	/* Esc */
            return false;
        }
	}); 

    // - activar la tecla "Enter" en la casilla de busqueda
    $(dgid+'-search').keydown(function(event){
      	// reload data adding search criteriar
        if(event.keyCode == 13) reloadWithSearch(dgid,'select',false);
    });
}
