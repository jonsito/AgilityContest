/*
competicion.js

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

/**
 * Funciones relacionadas con la ventana de competicion
 */

/************************** Gestion de datos de la ventana de manga activa */

/* formatters generales */

function formatBold(val,row,idx) { return '<span style="font-weight:bold">'+val+'</span>'; }
function formatBoldBig(val,row,idx) { return '<span style="font-weight:bold;font-size:1.5em;">'+val+'</span>'; }
function formatBorder(val,row,idx) { return 'border-left: 1px solid #000;'; }

/* formatters para el datagrid dlg_resultadosManga */
function formatPuesto(val,row,idx) { return '<span style="font-weight:bold">'+((row.Penalizacion>=100)?"-":val)+'</span>'; }
function formatPuestoBig(val,row,idx) { return '<span style="font-size:1.5em;font-weight:bold">'+((row.Penalizacion>=100)?"-":val)+'</span>'; }
function formatVelocidad(val,row,idx) { return (row.Penalizacion>=200)?"-":parseFloat(val).toFixed(1); }
function formatTiempo(val,row,idx) { return (row.Penalizacion>=200)?"-":parseFloat(val).toFixed(2); }
function formatPenalizacion(val,row,idx) { return parseFloat(val).toFixed(2); }
function formatEliminado(val,row,idx) { return (row.Eliminado==0)?"":"Elim"; }
function formatNoPresentado(val,row,idx) { return (row.NoPresentado==0)?"":"N.P."; }

/* formaters para el frm_clasificaciones */
function formatPuestoFinal(val,row,idx) { return '<span style="font-weight:bold">'+((row.Penalizacion>=200)?"-":val)+'</span>'; }
function formatPuestoFinalBig(val,row,idx) { return '<span style="font-size:1.5em;font-weight:bold">'+((row.Penalizacion>=200)?"-":val)+'</span>'; }
function formatPenalizacionFinal(val,row,idx) { return parseFloat(val).toFixed(2); }

function formatV1(val,row,idx) { return (row.P1>=200)?"-":parseFloat(val).toFixed(1); }
function formatT1(val,row,idx) { return (row.P1>=200)?"-":parseFloat(val).toFixed(2); }
function formatP1(val,row,idx) { return parseFloat(val).toFixed(2); }
function formatV2(val,row,idx) { return (row.P2>=200)?"-":parseFloat(val).toFixed(1); }
function formatT2(val,row,idx) { return (row.P2>=200)?"-":parseFloat(val).toFixed(2); }
function formatP2(val,row,idx) { return parseFloat(val).toFixed(2); }
function formatTF(val,row,idx) {
	var t=parseFloat(row.T1)+parseFloat(row.T2);
	return (row.Penalizacion>=200)?"-":t.toFixed(2); 
}
function formatRSCE(val,row,idx) {
	switch(parseInt(val)) {
	case 0: return "RSCE";
	case 1: return "RFEC";
	case 2: return "UCA";
	default: return val;
	}
}
/* stylers para formateo de celdas especificas */
function formatOk(val,row,idx) { return (parseInt(val)==0)?"":"&#x2714;"; }
function formatNotOk(val,row,idx) { return (parseInt(val)!=0)?"":"&#x2714;"; }
function formatCerrada(val,row,idx) { return (parseInt(val)==0)?"":"&#x26D4;"; }
function formatRing(val,row,idx) { return (val==='-- Sin asignar --')?"":val; }
function formatCelo(val,row,idx) { return (parseInt(val)==0)?" ":"&#x2665;"; }
function checkPending(val,row,idx) { return ( parseInt(row.Pendiente)!=0 )? 'color: #f00;': ''; }
function competicionRowStyler(idx,row) { return (row.Dorsal=='*')? myRowStyler(-1,row) : myRowStyler(idx,row); }
function formatOrdenSalida(val,row,idx) { return '<span style="font-size:1.5em;font-weight:bold;height:40px;line-height:40px">'+(1+idx)+'</span>'; }
function formatDorsal(val,row,idx) { return '<span style="font-size:1.5em;font-weight:bold;height:40px;line-height:40px">'+val+'</span>'; }

function formatOrdenLlamadaPista(val,row,idx) { if (val<=0) return ""; return '<span style="font-weight:bold;font-size:1.5em;">'+val+'</span>'; }
function formatLlamadaGuia(val,row,idx) { if (row.Orden>0) return val; return '<span style="font-weight:bold;font-size:1.4em;">'+val+'</span>'; }
function formatLogo(val,row,idx) {
    // TODO: no idea why idx:0 has no logo declared
    if (typeof(val)==='undefined') return '<img width="40" height="40" alt="empty.png" src="/agility/images/logos/empty.png"/>';
    return '<img width="40" height="40" alt="'+val+'" src="/agility/images/logos/'+val+'"/>';
}

function formatTeamResults( value , rows ) {
    // todo: check eq3 or eq4 contest and eval time and penalization
    var time=0.0;
    var penal=0.0;
    var logos="";
    var width=($('#vw_header-combinadaFlag').text()==='true')?500:1000;
    function addLogo(logo) {
        if (logos.indexOf(logo)>=0) return;
        logos = logos + '&nbsp;<img height="40px" src="/agility/images/logos/'+ logo + '"/>';
    }
    for (var n=0;n<3;n++) {
        if ( typeof(rows[n])==='undefined') {
            penal+=200.0;
            logos = logos + '&nbsp';
        } else {
            penal+=parseFloat(rows[n].Penalizacion);
            time+=parseFloat(rows[n].Tiempo);
            addLogo(rows[n].LogoClub);
        }
    }
    // return "Equipo: "+value+" Tiempo: "+time+" Penalizaci&oacute;n: "+penal;
    var res= '<div class="vw_equipos3" style="width:'+width+'px;">'+
        '<span style="width:10%;text-align:left;">'+logos+'</span>'+
        '<span style="width:25%;text-align:left;">Equipo: '+value+'</span>' +
        '<span style="width:30%;text-align:right;">Tiempo: '+(time).toFixed(2)+'</span>' +
        '<span style="width:30%;text-align:right;">Penaliz.:'+(penal).toFixed(2)+'</span>'+
        '<span style="width:5%;text-align:right;font-size:1.5em;">'+(workingData.teamCounter++)+'</span>'+
        '</div>';
    return res;
}

function formatTeamResultsConsole( value , rows ) {
    // todo: check eq3 or eq4 contest and eval time and penalization
    var time=0.0;
    var penal=0.0;
    for (var n=0;n<3;n++) {
        if ( typeof(rows[n])==='undefined') {
            penal+=200.0;
        } else {
            penal+=parseFloat(rows[n].Penalizacion);
            time+=parseFloat(rows[n].Tiempo);
        }
    }
    // return "Equipo: "+value+" Tiempo: "+time+" Penalizaci&oacute;n: "+penal;
    var res= '<div class="vw_equipos3" style="width:640px">'+
        '<span style="width:35%;text-align:left;">Equipo: '+value+'</span>' +
        '<span style="width:25%;text-align:right;">Tiempo: '+(time).toFixed(2)+'</span>' +
        '<span style="width:25%;text-align:right;">Penaliz.:'+(penal).toFixed(2)+'</span>'+
        '<span style="width:15%;text-align:right;font-size:1.5em">'+(workingData.teamCounter++)+'</span>'+
        '</div>';
    return res;
}

function formatTeamClasificaciones(value,rows) {
    var time=0.0;
    var penal=0.0;
    var logos="";
    function sortResults(a,b) {
        if (a.penal== b.penal) return (a.time - b.time);
        return (a.penal - b.penal);
    }
    function addLogo(logo) {
        if (logos.indexOf(logo)>=0) return;
        logos = logos + '&nbsp;<img height="40px" src="/agility/images/logos/'+ logo + '"/>';
    }
    // cogemos y ordenamos los datos de cada manga
    var manga1={ time:0.0, penal:0.0, perros:[] };
    var manga2={ time:0.0, penal:0.0, perros:[] };
    for (var n=0;n<4;n++) {
        if (typeof(rows[n]) === 'undefined') {
            manga1.perros[n] = {time: parseFloat(0.0), penal: parseFloat(200.0)};
            manga2.perros[n] = {time: parseFloat(0.0), penal: parseFloat(200.0)};
            logos = logos + '&nbsp';
        } else {
            manga1.perros[n] = {time: parseFloat(rows[n].T1), penal: parseFloat(rows[n].P1)};
            manga2.perros[n] = {time: parseFloat(rows[n].T2), penal: parseFloat(rows[n].P2)};
            addLogo(rows[n].LogoClub);
        }
    }
    // ordenamos ahora las matrices de resultados
    (manga1.perros).sort(sortResults);
    (manga2.perros).sort(sortResults);
    // y sumamos los tres primeros ( 3 mejores ) resultados
    for (var n=0;n<3;n++) {
        manga1.time +=parseFloat(manga1.perros[n].time);
        manga1.penal +=parseFloat(manga1.perros[n].penal);
        manga2.time +=parseFloat(manga2.perros[n].time);
        manga2.penal +=parseFloat(manga2.perros[n].penal);
    }
    // el resultado final es la suma de las mangas
    time=manga1.time+manga2.time;
    penal=manga1.penal+manga2.penal;
    // !Por fin! componemos una tabla html como respuesta
    var res= '<div class="pb_equipos3">'+
        '<span style="width:10%;text-align:left;">'+logos+'</span>'+
        '<span style="width:25%;text-align:left;"> Equipo: '+value+'</span>' +
        '<span > T1: '+(manga1.time).toFixed(2)+' - P1: '+(manga1.penal).toFixed(2)+'</span>'+
        '<span > T2: '+(manga2.time).toFixed(2)+' - P2: '+(manga2.penal).toFixed(2)+'</span>'+
        '<span style="width:25%;"> Time: '+(time).toFixed(2)+' - Penal: '+(penal).toFixed(2)+'</span>'+
        '</div>';
    return res;
}

function formatTeamClasificacionesConsole(value,rows) {
    var time=0.0;
    var penal=0.0;
    function sortResults(a,b) {
        if (a.penal== b.penal) return (a.time - b.time);
        return (a.penal - b.penal);
    }
    // cogemos y ordenamos los datos de cada manga
    var manga1={ time:0.0, penal:0.0, perros:[] };
    var manga2={ time:0.0, penal:0.0, perros:[] };
    for (var n=0;n<4;n++) {
        if (typeof(rows[n]) === 'undefined') {
            manga1.perros[n] = {time: parseFloat(0.0), penal: parseFloat(200.0)};
            manga2.perros[n] = {time: parseFloat(0.0), penal: parseFloat(200.0)};
        } else {
            manga1.perros[n] = {time: parseFloat(rows[n].T1), penal: parseFloat(rows[n].P1)};
            manga2.perros[n] = {time: parseFloat(rows[n].T2), penal: parseFloat(rows[n].P2)};
        }
    }
    // ordenamos ahora las matrices de resultados
    (manga1.perros).sort(sortResults);
    (manga2.perros).sort(sortResults);
    // y sumamos los tres primeros ( 3 mejores ) resultados
    for (var n=0;n<3;n++) {
        manga1.time +=parseFloat(manga1.perros[n].time);
        manga1.penal +=parseFloat(manga1.perros[n].penal);
        manga2.time +=parseFloat(manga2.perros[n].time);
        manga2.penal +=parseFloat(manga2.perros[n].penal);
    }
    // el resultado final es la suma de las mangas
    time=manga1.time+manga2.time;
    penal=manga1.penal+manga2.penal;

    // !Por fin! componemos una tabla html como respuesta
    var res= '<div class="pb_equipos3" style="width:800px">'+
        '<span style="width:35%;text-align:left;"> Equipo: '+value+'</span>' +
        '<span > T1: '+(manga1.time).toFixed(2)+' - P1: '+(manga1.penal).toFixed(2)+'</span>'+
        '<span > T2: '+(manga2.time).toFixed(2)+' - P2: '+(manga2.penal).toFixed(2)+'</span>'+
        '<span style="width:25%;"> Time: '+(time).toFixed(2)+' - Penal: '+(penal).toFixed(2)+'</span>'+
        '</div>';
    return res;
}

/**
 * Obtiene el modo de visualizacion de una manga determinada en funcion de la prueba, tipo de recorrido y categorias
 * @param {int} fed 0:RSCE 1:RFEC 2:UCA
 * @param {int} recorrido 0:separado 1:mixto 2:conjunto
 * @param {int} categoria 0:L 1:M 2:S 3:T
 * @returns {int} requested mode. -1 if invalid request
 */
function getMangaMode(fed,recorrido,categoria) {
    var modes= [ // federation/recorrido/categoria
        [/* RSCE */ [/* separado */ 0, 1, 2, -1], [/* mixto */ 0, 3, 3. -1], [/* conjunto */ 4, 4, 4, -1 ] ],
        [/* RFEC */ [/* separado */ 0, 1, 2, 5 ], [/* mixto */ 6, 6, 7, 7 ], [/* conjunto */ 8, 8, 8, 8 ] ],
        [/* UCA  */ [/* separado */ 0, 1, 2, 5 ], [/* mixto */ 6, 6, 7, 7 ], [/* conjunto */ 8, 8, 8, 8 ] ]
    ];
    if ( typeof (modes[fed]) === 'undefined' ) return -1;
    if ( typeof (modes[fed][recorrido]) === 'undefined' ) return -1;
    if ( typeof (modes[fed][recorrido][categoria]) === 'undefined' ) {
        switch(categoria) {
            case '-':
            case '-LMST':return modes[fed][2][0]; // same for all categories; just use first
            case 'L':return modes[fed][recorrido][0];
            case 'M':return modes[fed][recorrido][1];
            case 'S':return modes[fed][recorrido][2];
            case 'T':return modes[fed][recorrido][3];
        }
        return -1;
    }
    return modes[fed][recorrido][categoria];
}

// Same as above but return mode string
function getMangaModeString(fed,recorrido,categoria) {
    var modes= [ // federation/recorrido/categoria
        [/* RSCE */ [/* separado */ "Standard", "Midi", "Mini", "Invalid"], [/* mixto */ "Standard", "Midi+Mini", "Midi+Mini", "Invalid"], [/* conjunto */ "Conjunto", "Conjunto", "Conjunto", "Invalid" ] ],
        [/* RFEC */ [/* separado */ "Large", "Medium", "Small", "Toy" ], [/* mixto */ "Large+Medium", "Large+Medium", "Small+Toy", "Small+Toy" ], [/* conjunto */ "Conjunto", "Conjunto", "Conjunto", "Conjunto" ] ],
        [/* UCA  */ [/* separado */ "Cat 60", "Cat 50", "Cat 40", "Cat 30" ], [/* mixto */ "60+50", "60+50", "40+30", "40+30" ], [/* conjunto */ "60+50+40+30", "60+50+40+30", "60+50+40+30","60+50+40+30" ] ]
    ];
    if ( typeof (modes[fed]) === 'undefined' ) return -1;
    if ( typeof (modes[fed][recorrido]) === 'undefined' ) return -1;
    if ( typeof (modes[fed][recorrido][categoria]) === 'undefined' ) {
        switch(categoria) {
            case '-':
            case '-LMST':return modes[fed][2][0]; // same for all categories; just use first
            case 'L':return modes[fed][recorrido][0];
            case 'M':return modes[fed][recorrido][1];
            case 'S':return modes[fed][recorrido][2];
            case 'T':return modes[fed][recorrido][3];
        }
        return -1;
    }
    return modes[fed][recorrido][categoria];
}
/**
 * Actualiza el modo de visualizacion del panel infomangas
 * en funcion del tipo de recorrido seleccionado
 * para las pruebas de Canina
 */
function dmanga_setRecorridos_rsce() {
    var distl=0;
    var obstl=0;
    var distm=0;
    var obstm=0;
	var val=$("input[name='Recorrido']:checked").val();
	workingData.datosManga.Recorrido=val;
	switch (val) {
	case '2': // recorrido comun para std, mini y midi
		distl=$('#dmanga_DistL').val();
		obstl=$('#dmanga_ObstL').val();
		$('#dmanga_DistM').attr('readonly',true).val(distl);
		$('#dmanga_ObstM').attr('readonly',true).val(obstl);
		$('#dmanga_DistS').attr('readonly',true).val(distl);
		$('#dmanga_ObstS').attr('readonly',true).val(obstl);
		
		// set TRS and TRM for midi relative to Standard TRS
		$('#dmanga_TRS_M_Tipo').val(3); 
		$('#dmanga_TRS_M_Factor').val(0);
		$('#dmanga_TRM_M_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_M_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for small relative to Standard TRS
		$('#dmanga_TRS_S_Tipo').val(3); 
		$('#dmanga_TRS_S_Factor').val(0);
		$('#dmanga_TRM_S_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_S_Factor').val($('#dmanga_TRM_L_Factor').val());
		
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','none');
		$('#dmanga_SmallRow').css('display','none');
		$('#dmanga_LargeLbl').html("Com&uacute;n");
		$('#dmanga_MediumLbl').html("&nbsp;");
		$('#dmanga_SmallLbl').html("&nbsp;");
		break;
	case '1': // un recorrido para std y otro para mini-midi
		distm=$('#dmanga_DistM').val();
		obstm=$('#dmanga_ObstM').val();
		$('#dmanga_DistM').removeAttr('readonly');
		$('#dmanga_ObstM').removeAttr('readonly');
		$('#dmanga_DistS').attr('readonly',true).val(distm);
		$('#dmanga_ObstS').attr('readonly',true).val(obstm);
		
		// set Small TRS and TRM relative to Midi TRS
		$('#dmanga_TRS_S_Tipo').val(4); 
		$('#dmanga_TRS_S_Factor').val(0);
		$('#dmanga_TRM_S_Tipo').val($('#dmanga_TRM_M_Tipo').val()); 
		$('#dmanga_TRM_S_Factor').val($('#dmanga_TRM_M_Factor').val());
		
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','table-row');
		$('#dmanga_SmallRow').css('display','none');
		$('#dmanga_LargeLbl').html("Standard");
		$('#dmanga_MediumLbl').html("Midi+Mini");
		$('#dmanga_SmallLbl').html("&nbsp;");
		break;
	case '0': // recorridos separados para cada categoria
		$('#dmanga_DistM').removeAttr('readonly');
		$('#dmanga_ObstM').removeAttr('readonly');
		$('#dmanga_DistS').removeAttr('readonly');
		$('#dmanga_ObstS').removeAttr('readonly');
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','table-row');
		$('#dmanga_SmallRow').css('display','table-row');
		$('#dmanga_LargeLbl').html("Standard");
		$('#dmanga_MediumLbl').html("Midi");
		$('#dmanga_SmallLbl').html("Mini");
		break;
	}
}

/**
 * Actualiza el modo de visualizacion del panel infomangas
 * en funcion del tipo de recorrido seleccionado
 * para las pruebas de Caza
 */
function dmanga_setRecorridos_rfec() {
    var distl=0;
    var obstl=0;
    var dists=0;
    var obsts=0;
	var val=$("input[name='Recorrido']:checked").val();
	workingData.datosManga.Recorrido=val;
	switch (val) {
	case '2': // recorrido comun para std, med, min, y tiny
		distl=$('#dmanga_DistL').val();
		obstl=$('#dmanga_ObstL').val();
		$('#dmanga_DistM').attr('readonly',true).val(distl);
		$('#dmanga_ObstM').attr('readonly',true).val(obstl);
		$('#dmanga_DistS').attr('readonly',true).val(distl);
		$('#dmanga_ObstS').attr('readonly',true).val(obstl);
		$('#dmanga_DistT').attr('readonly',true).val(distl);
		$('#dmanga_ObstT').attr('readonly',true).val(obstl);
		
		// set TRS and TRM for midi relative to Standard TRS
		$('#dmanga_TRS_M_Tipo').val(3); 
		$('#dmanga_TRS_M_Factor').val(0);
		$('#dmanga_TRM_M_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_M_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for small relative to Standard TRS
		$('#dmanga_TRS_S_Tipo').val(3); 
		$('#dmanga_TRS_S_Factor').val(0);
		$('#dmanga_TRM_S_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_S_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for tiny relative to Standard TRS
		$('#dmanga_TRS_T_Tipo').val(3); 
		$('#dmanga_TRS_T_Factor').val(0);
		$('#dmanga_TRM_T_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_T_Factor').val($('#dmanga_TRM_L_Factor').val());
		
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','none');
		$('#dmanga_SmallRow').css('display','none');
		$('#dmanga_TinyRow').css('display','none');
		$('#dmanga_LargeLbl').html("Com&uacute;n");
		$('#dmanga_MediumLbl').html("&nbsp;");
		$('#dmanga_SmallLbl').html("&nbsp;");
		$('#dmanga_TinyLbl').html("&nbsp;");
		break;
	case '1': // un recorrido para std/midi y otro para mini/tiny
		distl=$('#dmanga_DistL').val();
		obstl=$('#dmanga_ObstL').val();
		dists=$('#dmanga_DistS').val();
		obsts=$('#dmanga_ObstS').val();
		$('#dmanga_DistM').attr('readonly',true).val(distl);
		$('#dmanga_ObstM').attr('readonly',true).val(obstl);
		$('#dmanga_DistS').removeAttr('readonly');
		$('#dmanga_DistT').attr('readonly',true).val(dists);
		$('#dmanga_ObstT').attr('readonly',true).val(obsts);
		
		// set TRS and TRM for midi relative to Standard TRS
		$('#dmanga_TRS_M_Tipo').val(3); 
		$('#dmanga_TRS_M_Factor').val(0);
		$('#dmanga_TRM_M_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_M_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for tiny relative to small TRS
		$('#dmanga_TRS_T_Tipo').val(5); 
		$('#dmanga_TRS_T_Factor').val(0);
		$('#dmanga_TRM_T_Tipo').val($('#dmanga_TRM_S_Tipo').val()); 
		$('#dmanga_TRM_T_Factor').val($('#dmanga_TRM_S_Factor').val());
		
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','none');
		$('#dmanga_SmallRow').css('display','table-row');
		$('#dmanga_TinyRow').css('display','none');
		$('#dmanga_LargeLbl').html("Large+Medium");
		$('#dmanga_MediumLbl').html("&nbsp;");
		$('#dmanga_SmallLbl').html("Small+Tiny");
		$('#dmanga_TinyLbl').html("&nbsp;");
		break;
	case '0': // recorridos separados para cada categoria
		$('#dmanga_DistM').removeAttr('readonly');
		$('#dmanga_ObstM').removeAttr('readonly');
		$('#dmanga_DistS').removeAttr('readonly');
		$('#dmanga_ObstS').removeAttr('readonly');
		$('#dmanga_DistT').removeAttr('readonly');
		$('#dmanga_ObstT').removeAttr('readonly');
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','table-row');
		$('#dmanga_SmallRow').css('display','table-row');
		$('#dmanga_TinyRow').css('display','table-row');
		$('#dmanga_LargeLbl').html("Large");
		$('#dmanga_MediumLbl').html("Medium");
		$('#dmanga_SmallLbl').html("Small");
		$('#dmanga_TinyLbl').html("Tiny");
		break;
	}
}
/**
 * Actualiza el modo de visualizacion del panel infomangas
 * en funcion del tipo de recorrido seleccionado
 * para las pruebas de UCA
 */
function dmanga_setRecorridos_uca() {
	var val=$("input[name='Recorrido']:checked").val();
    var distl=0;
    var obstl=0;
    var dists=0;
    var obsts=0;
	workingData.datosManga.Recorrido=val;
	switch (val) {
	case '2': // recorrido comun para std, med, min, y tiny
		distl=$('#dmanga_DistL').val();
		obstl=$('#dmanga_ObstL').val();
		$('#dmanga_DistM').attr('readonly',true).val(distl);
		$('#dmanga_ObstM').attr('readonly',true).val(obstl);
		$('#dmanga_DistS').attr('readonly',true).val(distl);
		$('#dmanga_ObstS').attr('readonly',true).val(obstl);
		$('#dmanga_DistT').attr('readonly',true).val(distl);
		$('#dmanga_ObstT').attr('readonly',true).val(obstl);
		
		// set TRS and TRM for midi relative to Standard TRS
		$('#dmanga_TRS_M_Tipo').val(3); 
		$('#dmanga_TRS_M_Factor').val(0);
		$('#dmanga_TRM_M_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_M_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for small relative to Standard TRS
		$('#dmanga_TRS_S_Tipo').val(3); 
		$('#dmanga_TRS_S_Factor').val(0);
		$('#dmanga_TRM_S_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_S_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for tiny relative to Standard TRS
		$('#dmanga_TRS_T_Tipo').val(3); 
		$('#dmanga_TRS_T_Factor').val(0);
		$('#dmanga_TRM_T_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_T_Factor').val($('#dmanga_TRM_L_Factor').val());
		
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','none');
		$('#dmanga_SmallRow').css('display','none');
		$('#dmanga_TinyRow').css('display','none');
		$('#dmanga_LargeLbl').html("Com&uacute;n");
		$('#dmanga_MediumLbl').html("&nbsp;");
		$('#dmanga_SmallLbl').html("&nbsp;");
		$('#dmanga_TinyLbl').html("&nbsp;");
		break;
	case '1': // un recorrido para std/midi y otro para mini/tiny
		distl=$('#dmanga_DistL').val();
		obstl=$('#dmanga_ObstL').val();
		dists=$('#dmanga_DistS').val();
		obsts=$('#dmanga_ObstS').val();
		$('#dmanga_DistM').attr('readonly',true).val(distl);
		$('#dmanga_ObstM').attr('readonly',true).val(obstl);
		$('#dmanga_DistS').removeAttr('readonly');
		$('#dmanga_DistT').attr('readonly',true).val(dists);
		$('#dmanga_ObstT').attr('readonly',true).val(obsts);
		
		// set TRS and TRM for midi relative to Standard TRS
		$('#dmanga_TRS_M_Tipo').val(3); 
		$('#dmanga_TRS_M_Factor').val(0);
		$('#dmanga_TRM_M_Tipo').val($('#dmanga_TRM_L_Tipo').val()); 
		$('#dmanga_TRM_M_Factor').val($('#dmanga_TRM_L_Factor').val());

		// set TRS and TRM for tiny relative to small TRS
		$('#dmanga_TRS_T_Tipo').val(5); 
		$('#dmanga_TRS_T_Factor').val(0);
		$('#dmanga_TRM_T_Tipo').val($('#dmanga_TRM_S_Tipo').val()); 
		$('#dmanga_TRM_T_Factor').val($('#dmanga_TRM_S_Factor').val());
		
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','none');
		$('#dmanga_SmallRow').css('display','table-row');
		$('#dmanga_TinyRow').css('display','none');
		$('#dmanga_LargeLbl').html("60+50");
		$('#dmanga_MediumLbl').html("&nbsp;");
		$('#dmanga_SmallLbl').html("40+30");
		$('#dmanga_TinyLbl').html("&nbsp;");
		break;
	case '0': // recorridos separados para cada categoria
		$('#dmanga_DistM').removeAttr('readonly');
		$('#dmanga_ObstM').removeAttr('readonly');
		$('#dmanga_DistS').removeAttr('readonly');
		$('#dmanga_ObstS').removeAttr('readonly');
		$('#dmanga_DistT').removeAttr('readonly');
		$('#dmanga_ObstT').removeAttr('readonly');
		// visibilidad de cada fila
		$('#dmanga_MediumRow').css('display','table-row');
		$('#dmanga_SmallRow').css('display','table-row');
		$('#dmanga_TinyRow').css('display','table-row');
		$('#dmanga_LargeLbl').html("Cat. 60");
		$('#dmanga_MediumLbl').html("Cat. 50");
		$('#dmanga_SmallLbl').html("Cat. 40");
		$('#dmanga_TinyLbl').html("Cat. 30");
		break;
	}
}

function dmanga_shareJuez() {
    $('#dmanga_Operation').val('sharejuez');
    $('#dmanga_Jornada').val(workingData.jornada);
    $('#dmanga_Manga').val(0); // not really needed, but...
    var frm = $('#competicion-formdatosmanga');
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/mangaFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.hasOwnProperty('errorMsg')){
            	$.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
            } else {// on submit success, reload results
    			var recorrido=$("input:radio[name=Recorrido]:checked").val();
    			$.messager.alert('Data saved','Exportados los datos de jueces','info');
    			workingData.datosManga.Recorrido=recorrido;
    			setupResultadosWindow(recorrido);
            }
        }
    });
}

/**
 * Guarda las modificaciones a los datos de una manga
 * Notese que esto no debería modificar ni los datos del
 * orden de salida ni resultados de la competicion
 * @param {int} id Identificador de la manga
 */
function save_manga(id) {
    $('#dmanga_Operation').val('update');
    $('#dmanga_Jornada').val(workingData.jornada);
    $('#dmanga_Manga').val(id);
    var frm = $('#competicion-formdatosmanga');
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/mangaFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.hasOwnProperty('errorMsg')){
            	$.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
            } else {// on submit success, reload results
    			var recorrido=$("input:radio[name=Recorrido]:checked").val();
    			$.messager.alert('Data saved','Datos de la manga almacenados','info');
    			workingData.datosManga.Recorrido=recorrido;
    			setupResultadosWindow(recorrido);
            }
        }
    });
}

/**
 * Recarga los datos asociados a una manga
 * Restaura ventana de informacion, orden de salida y competicion
 * @param id identificador de la manga
 */
function reload_manga(id) {
	// ventana de datos
	var url='/agility/server/database/mangaFunctions.php?Operation=getbyid&Jornada='+workingData.jornada+"&Manga="+id;
    $('#competicion-formdatosmanga').form('load',url);
}


function proximityAlert() {
	var data=$('#ordensalida-datagrid').datagrid('getRows');
	var guias= [];
	var lista="<br />";
	for (var idx=0;idx<data.length;idx++) {
		var NombreGuia=data[idx].NombreGuia;
		// not yet declared: store perro and orden
		if ( !(NombreGuia in guias) ) {
			guias[NombreGuia] = { 'index': idx, 'perro': data[idx].Nombre }; 
			continue; 
		} 
		// already declared: eval distance
		var dist=idx-guias[NombreGuia].index;
		if (dist>ac_config.proximity_alert) {
			// declared but more than 5 dogs ahead. reset index and continue
			guias[NombreGuia] = { 'index': idx, 'perro': data[idx].Nombre }; 
			continue;
		}
		// arriving here means that a dog is closer than 5 steps from previous one from same guia.
		// store to notify later
		lista = lista + NombreGuia+": " +
				(1+guias[NombreGuia].index)+":" + guias[NombreGuia].perro +
				" ---  " + 
				(1+idx) +":" + data[idx].Nombre + 
				"<br />";
	}
	// arriving here means work done
	if (lista==="<br />") {
		$.messager.alert('Correcto','No aparecen perros del mismo gu&uiacute;a pro&oacute;ximos','info');
	} else {
		var w=$.messager.alert('Alerta de proximidad','<p>Lista de gu&iacute;as con perros demasiado juntos:</p><p>'+lista+'</p>','warning');
		w.window('resize',{width:350}).window('center');
	}
}

function reloadAndCheck() {
	proximityAlert();
}

function reloadOrdenSalida() {
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
    $('#ordensalida-datagrid').datagrid(
            'load',
            { 
            	Prueba: workingData.prueba,
            	Jornada: workingData.jornada , 
            	Manga: workingData.manga , 
            	Operation: 'getData' 
            }
    );
}

function reloadCompeticion() {
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
	// si hay alguna celda en edicion, ignorar
	if ($('#competicion-datagrid').datagrid('options').editIndex!=-1) return;
    $('#competicion-datagrid').datagrid(
            'load',
            { 
            	Prueba: workingData.prueba ,
            	Jornada: workingData.jornada , 
            	Manga: workingData.manga , 
            	Operation: 'getData',
            	TeamView: isTeam(workingData.datosManga.Tipo)?'true':'false'
            }
    );
}

var autoUpdateID=null;

function autoUpdateCompeticion() {
	var enabled=$('#competicion-autoUpdateBtn').prop('checked');
	if (enabled) {
		if (autoUpdateID!==null) return; // already activated
		autoUpdateID=setInterval(function(){reloadCompeticion();}, 10000);
	} else {
		if (autoUpdateID==null) return; // already deactivated
		clearInterval(autoUpdateID);
		autoUpdateID=null;
	}
}

/**
 * Key bindings para uso de teclas en el dialogo de entrada de datos
 * @param evt Key Event
 */

function competicionKeyEventHandler(evt) {

	// up & down keys
    function selectRow(t,up){
    	var count = t.datagrid('getRows').length;    // row count
    	var selected = t.datagrid('getSelected');
    	var res=true;
    	if (selected){
        	var index = t.datagrid('getRowIndex', selected);
        	index = index + (up ? -1 : 1);
        	if (index < 0) { res=false; index = 0;}
        	if (index >= count) { res=false; index = count - 1; }
        	t.datagrid('clearSelections');
        	t.datagrid('selectRow', index);
    	} else {
        	t.datagrid('selectRow', (up ? count-1 : 0));
    	}
    	return res; // tell caller if index overflows
	}

	function editRow(t) {
		var selected = t.datagrid('getSelected');
		if(!selected) return;
		if(selected.Dorsal==="*") return; // not editable row
		var index = t.datagrid('getRowIndex', selected);
        t.datagrid('beginEdit',index);
		var ed = $(t).datagrid('getEditor', {index:index,field:'Faltas'});
		// mark as selected contents on first field
		var input=$(ed.target).next().find('input');
		input.focus();
		input.select();
	}
	
	var dg=$('#competicion-datagrid');
	var editIndex=dg.datagrid('options').editIndex; // added by me
	if (editIndex==-1) { // not editing
		switch (evt.keyCode) {
        case 38:	/* Up */	 
            selectRow(dg,true); 
            return false;
        case 40:    /* Down */	 
            selectRow(dg,false); 
            return false;
        case 13:	/* Enter */  
        	if (evt.ctrlKey) displayRowData(dg); else editRow(dg); 
            return false;
        case 27:	/* Esc */
            // disable autorefresh if any
            $('#competicion-autoUpdateBtn').prop('checked',false);
            autoUpdateCompeticion(); // fire event 
            // and close window  	 
            $('#competicion-window').window('close'); 
            return false;
        default: 
        	return false;
		}
	} else { //on edit
		switch (evt.keyCode) {
        case 13:	/* Enter */
        	// save data
        	dg.datagrid('endEdit', editIndex );
        	var data=dg.datagrid('getRows')[editIndex];
        	data.Pendiente=0;
        	saveCompeticionData(editIndex,data);
        	// and open edition on next row
        	dg.datagrid('selectRow', editIndex); // previous focus is lost
        	var res=selectRow(dg,false); // move down one row
        	if (res) editRow(dg); // and edit except if we already was at bottom
        	return false;
        case 27:	/* Esc */ 
            dg.datagrid('cancelEdit', editIndex);	
            return false;
		}
	}
	return true; // to allow follow key binding chain
}

/** 
 * actualiza los datos de TRS y TRM de la fila especificada
 * Si se le indica, rellena tambien el datagrid re resultados parciales
 * @param {int} val 0:L 1:M 2:S 3:T
 * @param {boolean} fill true to fill resultados datagrid; else false
 */
function reloadParcial(val,fill) {
	var value=parseInt(val); // stupid javascript!!
	var mode=getMangaMode(workingData.datosPrueba.RSCE,workingData.datosManga.Recorrido,value);
	if (mode==-1) {
		$.messager.alert('Error','Internal error: invalid RSCE/Recorrido/Categoria combination','error');
		return;
	}
	// reload resultados
	// en lugar de invocar al datagrid, lo que vamos a hacer es
	// una peticion ajax, para obtener a la vez los datos tecnicos de la manga
	$.ajax({
		type:'GET',
		url:"/agility/server/database/resultadosFunctions.php",
		dataType:'json',
		data: {
			Operation:	'getResultados',
			Prueba:		workingData.prueba,
			Jornada:	workingData.jornada,
			Manga:		workingData.manga,
			Mode: mode
		},
		success: function(dat) {
			var suffix='L';
			switch (mode) {
			case 0: case 4: case 6: case 8: suffix='L'; break;
			case 1: case 3: suffix='M'; break;
			case 2: case 7: suffix='S'; break;
			case 5: suffix='T'; break;
			}
			$('#rm_DIST_'+suffix).val(dat['trs'].dist);
			$('#rm_OBST_'+suffix).val(dat['trs'].obst);
			$('#rm_TRS_'+suffix).val(dat['trs'].trs);
			$('#rm_TRM_'+suffix).val(dat['trs'].trm);
			$('#rm_VEL_'+suffix).val(dat['trs'].vel);
			if (fill) $('#resultadosmanga-datagrid').datagrid('loadData',dat);
		}
	});
}

/**
 * Inicializa ventana de resultados ajustando textos
 * borra datagrid previa
 * @param recorrido 0:L/M/S/T 1:L/M+S(RSCE) LM/ST(RFEC)  2:/L+M+S+T
 */
function setupResultadosWindow(recorrido) {
	var fed= parseInt(workingData.datosPrueba.RSCE);
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
    $('#resultadosmanga-LargeBtn').prop('checked',false);
    $('#resultadosmanga-MediumBtn').prop('checked',false);
    $('#resultadosmanga-SmallBtn').prop('checked',false);
    $('#resultadosmanga-TinyBtn').prop('checked',false);
    $('#resultadosmanga-datagrid').datagrid('loadData',{total:0, rows:{}});
    // actualizamos la informacion del panel de informacion de trs/trm
    switch(parseInt(recorrido)){
    case 0: // Large / Medium / Small / Tiny separados
    	// ajustar visibilidad
    	$('#resultadosmanga-LargeRow').css('display','table-row');
    	$('#resultadosmanga-MediumRow').css('display','table-row');
    	$('#resultadosmanga-SmallRow').css('display','table-row');
    	$('#resultadosmanga-TinyRow').css('display',(fed==0)?'none':'table-row');
    	// ajustar textos
    	switch(fed) {
    		case 0:
    	    	$('#resultadosmanga-LargeLbl').html("Standard");
    	    	$('#resultadosmanga-MediumLbl').html("Midi");
    	    	$('#resultadosmanga-SmallLbl').html("Mini");
    	    	$('#resultadosmanga-TinyLbl').html("Enano");
    	    	break;
    		case 1:
    	    	$('#resultadosmanga-LargeLbl').html("Large");
    	    	$('#resultadosmanga-MediumLbl').html("Medium");
    	    	$('#resultadosmanga-SmallLbl').html("Small");
    	    	$('#resultadosmanga-TinyLbl').html("Tiny");
    	    	break;
    		case 2:
    	    	$('#resultadosmanga-LargeLbl').html("Cat. 60");
    	    	$('#resultadosmanga-MediumLbl').html("Cat. 50");
    	    	$('#resultadosmanga-SmallLbl').html("Cat. 40");
    	    	$('#resultadosmanga-TinyLbl').html("Cat. 30");
    	    	break;
    	}
    	break;
    case 1: // RSCE: Large / Medium+Small --------- RFEC: Large+Medium / Tiny+Small
    	// ajustar visibilidad
    	$('#resultadosmanga-LargeRow').css('display','table-row');
    	$('#resultadosmanga-MediumRow').css('display',(fed==0)?'table-row':'none');
    	$('#resultadosmanga-SmallRow').css('display',(fed==0)?'none':'table-row');
    	$('#resultadosmanga-TinyRow').css('display','none');
    	// ajustar textos
    	switch(fed) {
    		case 0:
    	    	$('#resultadosmanga-LargeLbl').html("Standard");
    	    	$('#resultadosmanga-MediumLbl').html("Mini+Midi");
    	    	$('#resultadosmanga-SmallLbl').html("&nbsp;");
    	    	$('#resultadosmanga-TinyLbl').html("&nbsp;");
    			break;
    		case 1:
    	    	$('#resultadosmanga-LargeLbl').html("Large+Medium");
    	    	$('#resultadosmanga-MediumLbl').html("&nbsp;");
    	    	$('#resultadosmanga-SmallLbl').html("Small+Tiny");
    	    	$('#resultadosmanga-TinyLbl').html("&nbsp;");
    			break;
    		case 2:
    	    	$('#resultadosmanga-LargeLbl').html("60+50");
    	    	$('#resultadosmanga-MediumLbl').html("&nbsp;");
    	    	$('#resultadosmanga-SmallLbl').html("40+30");
    	    	$('#resultadosmanga-TinyLbl').html("&nbsp;");
    			break;
    	}
    	break;
    case 2: // Large+Medium+Small (+Tiny) conjunta
    	// ajustar visibilidad
    	$('#resultadosmanga-LargeRow').css('display','table-row');
    	$('#resultadosmanga-MediumRow').css('display','none');
    	$('#resultadosmanga-SmallRow').css('display','none');
    	$('#resultadosmanga-TinyRow').css('display','none');
    	// ajustar textos
    	switch(fed) {
    		case 0:
    	    	$('#resultadosmanga-LargeLbl').html((fed==0)?"Conjunta L+M+S":"Conjunta L+M+S+T");
    			break;
    		case 1:
    	    	$('#resultadosmanga-LargeLbl').html((fed==0)?"Conjunta L+M+S":"Conjunta L+M+S+T");
    			break;
    		case 2:
    	    	$('#resultadosmanga-LargeLbl').html((fed==0)?"Conjunta L+M+S":"Conjunta 6+5+4+3");
    			break;
    	}
    	$('#resultadosmanga-MediumLbl').html("&nbsp;");
    	$('#resultadosmanga-SmallLbl').html("&nbsp;");
    	$('#resultadosmanga-TinyLbl').html("&nbsp;");
    	break;
    }
}

function saveCompeticionData(idx,data) {
	$.ajax({
		type:'GET',
		url:"/agility/server/database/resultadosFunctions.php",
		dataType:'json',
		data: {
			Operation:	'update',
			Prueba:		workingData.prueba,
			Jornada:	workingData.jornada,
			Manga:		workingData.manga,
			Dorsal: 	data['Dorsal'],
			Perro: 		data['Perro'],
			Licencia:	data['Licencia'],
			Nombre:		data['Nombre'],
			NombreGuia:	data['NombreGuia'],
			NombreClub:	data['NombreClub'],
			Categoria:	data['Categoria'],
			Tocados:	data['Tocados'],
			Faltas:		data['Faltas'],
			Rehuses:	data['Rehuses'],
			Tiempo:		data['Tiempo'],
			Eliminado:	data['Eliminado'],
			NoPresentado:	data['NoPresentado'],
			Observaciones:	data['Observaciones'],
			Pendiente: data['Pendiente']
		},
		success: function(dat) {
			if (dat.Manga!=workingData.manga) return; // window changed
			$('#competicion-datagrid').datagrid('updateRow',{index: idx,row: dat});
			$('#lnkb1_'+idx).linkbutton();
			$('#lnkb2_'+idx).linkbutton();
		}
	});
}

// genera un nuevo orden aleatorio
function evalOrdenSalida(mode) {
	if (workingData.prueba==0) return;
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
	if (mode==='random') {
		$.messager.confirm('Confirmar', 'Se perderan todos los ajustes hechos a mano<br />Desea continuar?', function(r){
			if (!r) return;
			$.ajax({
				type:'GET',
				url:"/agility/server/database/ordenSalidaFunctions.php",
				dataType:'json',
				data: {
					Prueba: workingData.prueba,
					Jornada: workingData.jornada,
					Manga: workingData.manga,
					Operation: mode
				}
			}).done( function(msg) {
				reloadOrdenSalida();
			});
		});
	} else {
		$.ajax({
			type:'GET',
			url:"/agility/server/database/ordenSalidaFunctions.php",
			dataType:'json',
			data: {
				Prueba: workingData.prueba,
				Jornada: workingData.jornada,
				Manga: workingData.manga,
				Operation: mode
			}
		}).done( function(msg) {
			reloadOrdenSalida();
		});
	}
}

// reajusta el orden de salida 
// poniendo el idperro "from" delante (where==0) o detras (where==1) del idperro "to"
// al retornar la funcion se invoca whenDone, que normalmente recargara el formulario padre
function dragAndDropOrdenSalida(from,to,where,whenDone) {
	if (workingData.prueba==0) return;
	if (workingData.jornada==0) return;
	if (workingData.manga==0) return;
	$.ajax({
		type:'GET',
		url:"/agility/server/database/ordenSalidaFunctions.php",
		dataType:'json',
		data: {	
			Operation: 'dnd', Prueba: workingData.prueba, Jornada: workingData.jornada,	Manga: workingData.manga, From: from,To: to,Where: where
		}
	}).done( function(msg) {
		whenDone();
	});
}

//reajusta el programa de la jornada
//poniendo la tanda "from" delante (where==0) o detras (where==1) de la tanda "to"
function dragAndDropOrdenTandas(from,to,where) {
	if (workingData.prueba==0) return;
	if (workingData.jornada==0) return;
	$.ajax({
		type:'GET',
		url:"/agility/server/database/tandasFunctions.php",
		dataType:'json',
		data: {	
			Operation: 'dnd', 
			Prueba: workingData.prueba, 
			Jornada: workingData.jornada, 
			From: from,
			To: to,
			Where: where
		},
		success: function (result) {
				if (result.errorMsg){ 
					$.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
				}
				reloadOrdenTandas();
		}
	});
}
/**
 * Abre la ventana de competicion requerida 'ordensalida','competicion','resultadosmanga'
 * @param name
 */
function competicionDialog(name) {
	// obtenemos datos de la manga seleccionada
	var row= $('#competicion-listamangas').datagrid('getSelected');
    if (!row && name!== 'ordentandas') {
    	$.messager.alert('Error','No hay ninguna manga seleccionada','info');
    	return; // no hay ninguna manga seleccionada. retornar
    }
    var title = workingData.nombrePrueba + ' -- ' + workingData.nombreJornada;
    $('#ordentandas-dialog').dialog('close');
    $('#ordensalida-dialog').dialog('close');
    $('#competicion-dialog').dialog('close');
    $('#resultadosmanga-dialog').dialog('close');
    if (name==='ordentandas') {
        // abrimos ventana de dialogo
        $('#ordentandas-dialog').dialog('open').dialog('setTitle',"Jornada: "+title);
        // cargamos ventana de orden de salida
        reloadOrdenTandas();
    }
    title = workingData.nombrePrueba + ' -- ' + workingData.nombreJornada + ' -- ' + workingData.nombreManga;
    if (name==='ordensalida') {
        // abrimos ventana de dialogo
        $('#ordensalida-dialog').dialog('open').dialog('setTitle'," Orden de Salida: "+title);
        // cargamos ventana de orden de salida
        reloadOrdenSalida();
    }
    if (name==='competicion') {
        // abrimos ventana de dialogo
        $('#competicion-dialog').dialog('open').dialog('setTitle'," Entrada de datos: "+title);
        // cargamos ventana de entrada de datos
        reloadCompeticion();
    }
    if (name==='resultadosmanga') {
        // abrimos ventana de dialogo
        $('#resultadosmanga-dialog').dialog('open').dialog('setTitle'," Resultados de la manga: "+title);
        // iniciamls ventana de presentacion de resultados parciales acorde al tipo de prueba (RSCE/RFEC) y recorrido
        setupResultadosWindow(row.Recorrido);
        // marcamos la primera opcion como seleccionada
        $('#resultadosmanga-LargeBtn').prop('checked','checked');
        // refrescamos datos de TRS y TRM
        if (workingData.datosPrueba.RSCE!=0) reloadParcial(3,false);
        reloadParcial(2,false);
        reloadParcial(1,false);
        reloadParcial(0,true); // pintamos el datagrid con los datos de categoria "large"
    }
}

/************************************* funciones para la ventana de clasificaciones **************************/

/**
 * rellena los diversos formularios de informacion de resultados
 * resultados: almacen de resultados (array[mode][manga]
 * idmanga: Manga ID
 * idxmanga: 1..2 manga index
 * mode: 0..4
 */
function resultados_fillForm(resultados,idmanga,idxmanga,mode) {
	$.ajax({
		type:'GET',
		url:"/agility/server/database/resultadosFunctions.php",
		dataType:'json',
		data: {	Operation:'getResultados', Prueba:workingData.prueba, Jornada:workingData.jornada, Manga:idmanga, Mode: mode },
		success: function(dat) {
			var suffix='L';
			switch (mode) {
			case 0: case 4: case 6: case 8: suffix='L'; break;
			case 1: case 3: suffix='M'; break;
			case 2: case 7: suffix='S'; break;
			case 5: suffix='T'; break;
			}
			$('#dm'+idxmanga+'_Nombre').val(dat['manga'].TipoManga);
			$('#dm'+idxmanga+'_DIST_'+suffix).val(dat['trs'].dist);
			$('#dm'+idxmanga+'_OBST_'+suffix).val(dat['trs'].obst);
			$('#dm'+idxmanga+'_TRS_'+suffix).val(dat['trs'].trs);
			$('#dm'+idxmanga+'_TRM_'+suffix).val(dat['trs'].trm);
			$('#dm'+idxmanga+'_VEL_'+suffix).val(dat['trs'].vel);
			// store manga results
			if (typeof resultados[mode] === "undefined") resultados[mode]=[];
			resultados[mode][idxmanga]=dat['rows'];
			// alert(JSON.stringify(resultados[mode]));
		}
	});
}

/**
 * rellena la ventana de informacion con los datos definitivos de cada manga de la ronda seleccionada
 */
function resultados_doSelectRonda(row) {
	var resultados=[];
	var fed=parseInt(workingData.datosPrueba.RSCE);
	// FASE 0 ajustamos los jueces de la ronda
	$('#dm1_Juez1').val(row.Juez11);
	$('#dm1_Juez2').val(row.Juez12);
	$('#dm2_Juez1').val(row.Juez21);
	$('#dm2_Juez2').val(row.Juez22);
    // FASE 1 Ajustamos en funcion del tipo de recorrido lo que debemos ver en las mangas
    // Recordatorio: ambas mangas tienen siempre el mismo tipo de recorrido
    switch(parseInt(row.Recorrido1)){
    case 0: // Large / Medium / Small / Tiny
    	// Manga 1
    	$('#datos_manga1-LargeRow').css('display','table-row');
    	$('#datos_manga1-MediumRow').css('display','table-row');
    	$('#datos_manga1-SmallRow').css('display','table-row');
		$('#datos_manga1-TinyRow').css('display',(fed==0)?'none':'table-row');

		resultados_fillForm(resultados,row.Manga1,'1',0);
		resultados_fillForm(resultados,row.Manga1,'1',1);
		resultados_fillForm(resultados,row.Manga1,'1',2);
		if (fed!=0) resultados_fillForm(resultados,row.Manga1,'1',5);
		// set up categoria names and comboboxes
		switch(fed){
		case 0:
	    	$('#datos_manga1-LargeLbl').html("Standard");
	    	$('#datos_manga1-MediumLbl').html("Midi");
	    	$('#datos_manga1-SmallLbl').html("Mini");
	    	$('#datos_manga1-TinyLbl').html("Enano");
			$('#resultados-selectCategoria').combobox('loadData',
					[{mode:0,text:'Standard',selected:true},{mode:1,text:'Midi'},{mode:2,text:'Mini'}]);
			break;
		case 1:
	    	$('#datos_manga1-LargeLbl').html("Large");
	    	$('#datos_manga1-MediumLbl').html("Medium");
	    	$('#datos_manga1-SmallLbl').html("Small");
	    	$('#datos_manga1-TinyLbl').html("tiny");
			$('#resultados-selectCategoria').combobox('loadData',
					[{mode:0,text:'Large',selected:true},{mode:1,text:'Medium'},{mode:2,text:'Small'},{mode:5,text:'Tiny'}]);
			break;
		case 2:
	    	$('#datos_manga1-LargeLbl').html("Cat. 60");
	    	$('#datos_manga1-MediumLbl').html("Cat. 50");
	    	$('#datos_manga1-SmallLbl').html("Cat. 40");
	    	$('#datos_manga1-TinyLbl').html("Cat. 30");
			$('#resultados-selectCategoria').combobox('loadData',
					[{mode:0,text:'Cat 60',selected:true},{mode:1,text:'Cat 50'},{mode:2,text:'Cat 40'},{mode:5,text:'Cat 30'}]);
			break;
		}
    	// Manga 2
		if (row.Manga2<=0) {
			// esta ronda solo tiene una manga. desactiva la segunda
			$('#datos_manga2-InfoRow').css('display','none');
			$('#datos_manga2-LargeRow').css('display','none');
			$('#datos_manga2-MediumRow').css('display','none');
			$('#datos_manga2-SmallRow').css('display','none');
			$('#datos_manga2-TinyRow').css('display','none');
		} else {
			$('#datos_manga2-InfoRow').css('display','table-row');
			$('#datos_manga2-LargeRow').css('display','table-row');
    		$('#datos_manga2-MediumRow').css('display','table-row');
    		$('#datos_manga2-SmallRow').css('display','table-row');
    		$('#datos_manga2-TinyRow').css('display',(fed==0)?'none':'table-row');
    		switch (fed) {
    		case 0:
        		$('#datos_manga2-LargeLbl').html("Standard");
        		$('#datos_manga2-MediumLbl').html("Midi");
        		$('#datos_manga2-SmallLbl').html("Mini");
        		$('#datos_manga2-TinylLbl').html("Enano");
    			break;
    		case 1:
        		$('#datos_manga2-LargeLbl').html("Large");
        		$('#datos_manga2-MediumLbl').html("Medium");
        		$('#datos_manga2-SmallLbl').html("Small");
        		$('#datos_manga2-TinylLbl').html("Tiny");
    			break;
    		case 2:
        		$('#datos_manga2-LargeLbl').html("Cat. 60");
        		$('#datos_manga2-MediumLbl').html("Cat. 50");
        		$('#datos_manga2-SmallLbl').html("Cat. 40");
        		$('#datos_manga2-TinylLbl').html("Cat. 30");
    			break;
    		}
    		resultados_fillForm(resultados,row.Manga2,'2',0);
    		resultados_fillForm(resultados,row.Manga2,'2',1);
    		resultados_fillForm(resultados,row.Manga2,'2',2);
    		if (fed!=0) resultados_fillForm(resultados,row.Manga2,'2',5);
		}
    	break;
    case 1: // Large / Medium+Small (RSCE ) ---- Large+Medium / Small+Tiny (RFEC)
    	// Manga 1
    	$('#datos_manga1-LargeRow').css('display','table-row');
    	$('#datos_manga1-MediumRow').css('display',(fed==0)?'table-row':'none');
    	$('#datos_manga1-SmallRow').css('display',(fed==0)?'none':'table-row');
    	$('#datos_manga1-TinyRow').css('display','none');
    	
    	switch(fed) {
    	case 0:
        	$('#datos_manga1-LargeLbl').html("Standard");
        	$('#datos_manga1-MediumLbl').html("Midi+Mini");
        	$('#datos_manga1-SmallLbl').html("&nbsp;");
        	$('#datos_manga1-TinyLbl').html("&nbsp;");
    		resultados_fillForm(resultados,row.Manga1,'1',0); // l
    		resultados_fillForm(resultados,row.Manga1,'1',3); // m+s
    		$('#resultados-selectCategoria').combobox('loadData',
    				[{mode:0,text:'Standard',selected:true},{mode:3,text:'Midi + Mini'}]);
    		break;
    	case 1:
        	$('#datos_manga1-LargeLbl').html("Large+Medium");
        	$('#datos_manga1-MediumLbl').html("&nbsp;");
        	$('#datos_manga1-SmallLbl').html("Small+Tiny");
        	$('#datos_manga1-TinyLbl').html("&nbsp;");
    		resultados_fillForm(resultados,row.Manga1,'1',6); // l+m
    		resultados_fillForm(resultados,row.Manga1,'1',7); // s+t
    		$('#resultados-selectCategoria').combobox('loadData',
    				[{mode:6,text:'Large+Medium',selected:true},{mode:7,text:'Small+Tiny'}]);
    		break;
    	case 2:
        	$('#datos_manga1-LargeLbl').html("60+50");
        	$('#datos_manga1-MediumLbl').html("&nbsp;");
        	$('#datos_manga1-SmallLbl').html("40+30");
        	$('#datos_manga1-TinyLbl').html("&nbsp;");
    		resultados_fillForm(resultados,row.Manga1,'1',6); // l+m
    		resultados_fillForm(resultados,row.Manga1,'1',7); // s+t
    		$('#resultados-selectCategoria').combobox('loadData',
    				[{mode:6,text:'Cat. 60+50',selected:true},{mode:7,text:'Cat. 40+30'}]);
    		break;
    	}
    	// Manga 2
		if (row.Manga2<=0) { // no hay segunda manga: oculta formulario
			$('#datos_manga2-InfoRow').css('display','none');
			$('#datos_manga2-LargeRow').css('display','none');
			$('#datos_manga2-MediumRow').css('display','none');
			$('#datos_manga2-SmallRow').css('display','none');
			$('#datos_manga2-TinyRow').css('display','none');
		} else {
	    	$('#datos_manga2-LargeRow').css('display','table-row');
	    	$('#datos_manga2-MediumRow').css('display',(fed==0)?'table-row':'none');
	    	$('#datos_manga2-SmallRow').css('display',(fed==0)?'none':'table-row');
	    	$('#datos_manga2-TinyRow').css('display','none');
	    	switch(fed){
	    	case 0:
		    	$('#datos_manga2-LargeLbl').html("Standard");
		    	$('#datos_manga2-MediumLbl').html("Midi+Mini");
		    	$('#datos_manga2-SmallLbl').html("&nbsp;");
		    	$('#datos_manga2-TinyLbl').html("&nbsp;");
	    		break;
	    	case 1:
		    	$('#datos_manga2-LargeLbl').html("Large+Medium");
		    	$('#datos_manga2-MediumLbl').html("&nbsp;");
		    	$('#datos_manga2-SmallLbl').html("Small+Tiny");
		    	$('#datos_manga2-TinyLbl').html("&nbsp;");
	    		break;
	    	case 2:
		    	$('#datos_manga2-LargeLbl').html("60+50");
		    	$('#datos_manga2-MediumLbl').html("&nbsp;");
		    	$('#datos_manga2-SmallLbl').html("40+30");
		    	$('#datos_manga2-TinyLbl').html("&nbsp;");
	    		break;
	    	}
			if (fed==0) {
				resultados_fillForm(resultados,row.Manga2,'2',0);
				resultados_fillForm(resultados,row.Manga2,'2',3);
			} else {
				resultados_fillForm(resultados,row.Manga2,'2',6);
				resultados_fillForm(resultados,row.Manga2,'2',7);
			}
		}
    	break;
    case 2: // Large+Medium+Small+tiny conjunta
    	// Manga 1
    	$('#datos_manga1-LargeRow').css('display','table-row');
    	$('#datos_manga1-MediumRow').css('display','none');
    	$('#datos_manga1-SmallRow').css('display','none');
    	$('#datos_manga1-TinyRow').css('display','none');
    	
    	switch(fed){
    	case 0:	$('#datos_manga1-LargeLbl').html('Conjunta L+M+S');	break;
    	case 1:	$('#datos_manga1-LargeLbl').html('Conjunta L+M+S+T'); break;
    	case 2:	$('#datos_manga1-LargeLbl').html('Conjunta 6+5+4+3'); break;
    	}
    	$('#datos_manga1-MediumLbl').html("&nbsp;");
    	$('#datos_manga1-SmallLbl').html("&nbsp;");
    	$('#datos_manga1-TinyLbl').html("&nbsp;");
    	if (fed==0) {
    		resultados_fillForm(resultados,row.Manga1,'1',4);
    		$('#resultados-selectCategoria').combobox('loadData',
    				[{mode:4,text:'Conjunta L+M+S',selected:true}]);
    	} else {
    		resultados_fillForm(resultados,row.Manga1,'1',8);
    		$('#resultados-selectCategoria').combobox('loadData',
    				[{mode:8,text:'Conjunta L+M+S+T',selected:true}]);
    	}
    	// Manga 2
		if (row.Manga2<=0) {
			$('#datos_manga2-InfoRow').css('display','none');
			$('#datos_manga2-LargeRow').css('display','none');
			$('#datos_manga2-MediumRow').css('display','none');
			$('#datos_manga2-SmallRow').css('display','none');
			$('#datos_manga2-TinyRow').css('display','none');
		} else {
	    	$('#datos_manga2-LargeRow').css('display','table-row');
	    	$('#datos_manga2-MediumRow').css('display','none');
	    	$('#datos_manga2-SmallRow').css('display','none');
	    	$('#datos_manga2-TinyRow').css('display','none');
	    	
	    	switch(fed){
	    	case 0:	$('#datos_manga2-LargeLbl').html('Conjunta L+M+S');	break;
	    	case 1:	$('#datos_manga2-LargeLbl').html('Conjunta L+M+S+T'); break;
	    	case 2:	$('#datos_manga2-LargeLbl').html('Conjunta 6+5+4+3'); break;
	    	}
	    	$('#datos_manga2-MediumLbl').html("&nbsp;");
	    	$('#datos_manga2-SmallLbl').html("&nbsp;");
	    	$('#datos_manga2-TinyLbl').html("&nbsp;");
			if (fed==0) resultados_fillForm(resultados,row.Manga2,'2',4);
			else resultados_fillForm(resultados,row.Manga2,'2',8);
		}
    	break;
    } 
    // FASE 2: cargamos informacion sobre resultados globales y la volcamos en el datagrid
    var mode=$('#resultados-selectCategoria').combobox('getValue');
	$.ajax({
		type:'GET',
		url:"/agility/server/database/clasificacionesFunctions.php",
		dataType:'json',
		data: {	
			Prueba:workingData.prueba,
            Jornada:workingData.jornada,
            Federation:fed,
			Manga1:row.Manga1,
			Manga2:row.Manga2,
			Rondas: row.Rondas,
			Mode: mode
		},
		success: function(dat) {
			$('#resultados_thead_m1').text(row.NombreManga1);
			$('#resultados_thead_m2').text(row.NombreManga2);
			$('#resultados-datagrid').datagrid('loadData',dat);
		}
	});
}

function verifyCompose(data,manga,nombre) {
	var str="<strong>Perros pendientes de introducci&oacute;n de datos en manga "+manga+" ( "+nombre+" )</strong>";
	str +="<table><tr><th>Dorsal</th><th>Perro</th><th>Gu&iacute;a</th><th>Club</th></tr>";
	// componemos mensaje de error
	$.each(
		data['rows'],
		function(index,val) {
			str+="<tr><td>"+val['Dorsal']+"</td><td>"+val['Nombre']+"</td><td>"+val['NombreGuia']+"</td><td>"+val['NombreClub']+"</td></tr>";
		}
	);
	str+="</table><br />";
	return str;
}

function verifyClasificaciones() {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	var url='/agility/server/pdf/print_clasificacion.php';
	var mode=$('#resultados-selectCategoria').combobox('getValue');
	var str1="";
	var str2="";
	if (ronda==null) {
    	$.messager.alert("Error:","!No ha seleccionado ninguna ronda de esta jornada!","warning");
    	return false; // no way to know which ronda is selected
	}
	// verificamos manga 1
	$.ajax({
		type:'GET',
		url:"/agility/server/database/resultadosFunctions.php",
		dataType:'json',
		data: {	
			Operation:	'getPendientes', 
			Prueba:	workingData.prueba, 
			Jornada:workingData.jornada, 
			Manga:ronda.Manga1, 
			Mode: mode 
		},
		success: function(data) {
			if (parseInt(data['total'])!=0) str1=verifyCompose(data,ronda.Manga1,ronda.NombreManga1);
			if (ronda.Manga2==0) {
				// no hay segunda manga
				if (str1==="") {
					$.messager.alert("Verify OK","No se han encontrado perros sin datos registrados","info");
				} else {
					var w=$.messager.alert("Verify Error",str1,"error");
					w.window('resize',{width:600}).window('center');
				}
				return false; // prevent default fireup of event trigger
			}
			// verificamos manga 2
			$.ajax({
				type:'GET',
				url:"/agility/server/database/resultadosFunctions.php",
				dataType:'json',
				data: {	
					Operation:	'getPendientes', 
					Prueba:	workingData.prueba, 
					Jornada:workingData.jornada, 
					Manga:ronda.Manga2, 
					Mode: mode 
				},
				success: function(data) {
					if (parseInt(data['total'])!=0) str2=verifyCompose(data,ronda.Manga2,ronda.NombreManga2);
					if (str1==="" && str2==="") {
						$.messager.alert("Verify OK","No se han encontrado perros sin datos registrados","info");
					} else {
						var w=$.messager.alert("Verify Error",str1+str2,"error");
						w.window('resize',{width:600}).window('center');
					}
				}
			});
		}
	});
    return false; //this is critical to stop the click event which will trigger a normal file download!	
}

function reloadClasificaciones() {
	var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
	if (ronda==null) {
    	$.messager.alert("Error:","!No ha seleccionado ninguna ronda de esta jornada!","warning");
    	return; // no way to know which ronda is selected
	}
	// obtenemos el modo activo
	var mode=$('#resultados-selectCategoria').combobox('getValue');
	// calculamos y recargamos tabla de clasificaciones
	$.ajax({
		type:'GET',
		url:"/agility/server/database/clasificacionesFunctions.php",
		dataType:'json',
		data: {	
			Prueba:workingData.prueba,
			Jornada:workingData.jornada,
			Manga1:ronda.Manga1,
			Manga2:ronda.Manga2,
			Rondas: ronda.Rondas,
			Mode: mode
		},
		success: function(dat) {
			$('#resultados_thead_m1').text(ronda.NombreManga1);
			$('#resultados_thead_m2').text(ronda.NombreManga2);
			$('#resultados-datagrid').datagrid('loadData',dat);
		}
	});
}
