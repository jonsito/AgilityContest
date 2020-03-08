/*
 datagrid_formatters.js.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

// convert javascript.Date object to YYYY-MM-DD format
function dateToMysql(d) {
    var m=1+d.getMonth(); // getMonth() returns 0..11
    var mes=((m<10)?'0':'')+m.toString();
    var dia=((d.getDate()<10)?'0':'')+d.getDate().toString();
    return ""+d.getFullYear() + "-" + mes + "-" + dia;
}

// convert string to Date object
// accepted strings
// yyyy-mm-dd ( add 00:00:00 )
// yyyy-mm-dd hh:mm:ss
// hh:mm (add yyyy-mm-dd from parent, and assume 00 seconds
// hh:mm:ss ( add yyyy-mm-dd
function mysqlToDate(defdate,datetime) {
    if (typeof(datetime)==="undefined") return mysqlToDate("1970-01-01 00:00:00",defdate); // recursive call with default value
    var dt=datetime.trim();
    if (dt==="") return mysqlToDate("1970-01-01 00:00:00",defdate); // recursive call with default value
    if ( /^\d{4}-(0\d|1[0-2])-(0\d|[1-2]\d|3[0-1]) (?:2[0-3]|[01]\d):[0-5]\d:[0-5]\d$/.test(dt) ) { // yyyy-mm dd hh:mm:ss

    } else if ( /^\d{4}-(0\d|1[0-2])-(0\d|[1-2]\d|3[0-1])$/.test(dt) ) { // yyyy-mm-dd
        dt=dt+" 00:00:00";
    } else if ( /^(?:2[0-3]|[01]\d):[0-5]\d:[0-5]\d$/.test(dt) ) { // hh:mm:ss
        var a=defdate.split(" ");
        dt=a[0]+" "+dt;
    } else if ( /^(?:2[0-3]|[01]\d):[0-5]\d$/.test(dt) ){ // hh:mm
        var a=defdate.split(" ");
        dt=a[0]+" "+dt+":00";
    } else {
        console.log("mysqlToDate: invalid entry provided:"+datetime);
        return mysqlToDate("1970-01-01 00:00:00",defdate);
    }
    var b=dt.split(" ");
    var d=b[0].split("-");
    var t=b[1].split(":");
    return new Date(d[0],(d[1]-1),d[2],t[0],t[1],t[2]);
}

function formatYMD(val,row,idx) {
    var d = mysqlToDate(row.Fecha,val);
    return dateToMysql(d);
}

function formatHM(val,row,idx){
    var d = mysqlToDate(row.Fecha,val);
    var hora=((d.getHours()<10)?'0':'')+d.getHours().toString();
    var min=((d.getMinutes()<10)?'0':'')+d.getMinutes().toString();
    return ""+hora+":"+min;
}
function formatHMS(val,row,idx){
    var d = mysqlToDate(row.Fecha,val);
    var hora=((d.getHours()<10)?'0':'')+d.getHours().toString();
    var min=((d.getMinutes()<10)?'0':'')+d.getMinutes().toString();
    var sec=((d.getSeconds()<10)?'0':'')+d.getSeconds().toString();
    return ""+hora+":"+min+":"+sec;
}

function formatMinSecs(val,row,idx) {
    var time=parseInt(val); // seconds
    var min=Math.floor(time/60);
    var secs= time%60;
    return  "" + min + "'" + ((secs<10)?"0"+secs:secs)+'"';
}

/**
 * rowStyler function for AgilityContest public datagrids
 * @param {int} idx Row index
 * @param {Object} row Row data
 * @return {string} proper row style for given idx
 */
function pbRowStyler(idx,row) {
    var res="background-color:";
    var c1='<?php echo $config->getEnv('pb_rowcolor1'); ?>';
    var c2='<?php echo $config->getEnv('pb_rowcolor2'); ?>';
    var c3='<?php echo $config->getEnv('pb_rowcolor5'); ?>';
    // pbmenu-Dorsal does not exist in public/index.php, only in index2.php
    var d=($('#pbmenu-Dorsal').length)?$('#pbmenu-Dorsal').numberbox('getValue'):0;
    if (row.Dorsal == d ) return res+c3+";";
    if ( (idx&0x01)==0) { return res+c1+";"; } else { return res+c2+";"; }
}

/**
 * secondary rowStyler function for AgilityContest public datagrids
 * @param {int} idx Row index
 * @param {Object} row Row data
 * @return {string} proper row style for given idx
 */
function pbRowStyler2(idx,row) {
    var res="background-color:";
    var c1='<?php echo $config->getEnv('pb_rowcolor3'); ?>';
    var c2='<?php echo $config->getEnv('pb_rowcolor4'); ?>';
    var c3='<?php echo $config->getEnv('pb_rowcolor5'); ?>';
    // pbmenu-Dorsal does not exist in public/index.php, only in index2.php
    var d=($('#pbmenu-Dorsal').length)?$('#pbmenu-Dorsal').numberbox('getValue'):0;
    if (row.Dorsal == d ) return res+c3+";";
    if ( (idx&0x01)==0) { return res+c1+";"; } else { return res+c2+";"; }
}

/* formatters generales */

function formatBoldDog(val,row,idx) {
    var retired=(row.Baja!=0)?';color:red':'';
    return '<span style="font-weight:bold'+retired+'">'+val+'</span>';
}
function formatBold(val,row,idx) { return '<span style="font-weight:bold">'+val+'</span>'; }
function formatBoldBig(val,row,idx) { return '<span style="font-weight:bold;font-size:1.5em;">'+val+'</span>'; }
function formatBorder(val,row,idx) { return 'border-left: 1px solid #000;'; }
function formatGrado(val,row,idx) {
    var fed=workingData.federation;
    if (typeof (ac_fedInfo[fed]) === "undefined") return val;
    if (typeof (ac_fedInfo[fed].ListaGradosShort[val]) === "undefined") return val;
    return ac_fedInfo[fed].ListaGradosShort[val];
}
function formatCategoria(val,row,idx) {
    var fed=workingData.federation;
    if (typeof (ac_fedInfo[fed]) === "undefined") return val;
    if (typeof (ac_fedInfo[fed].ListaCategoriasShort[val]) === "undefined") return val;
    return ac_fedInfo[fed].ListaCategoriasShort[val];
}
function formatCatGuia(val,row,idx) {
    var fed=workingData.federation;
    if (typeof (ac_fedInfo[fed]) === "undefined") return val;
    if (typeof (ac_fedInfo[fed].ListaCatGuias[val]) === "undefined") return val;
    return ac_fedInfo[fed].ListaCatGuias[val];
}
function formatCatGrad(val,row,idx) {
    var hasGrade=true;
    if (isJornadaEquipos(null)) hasGrade=false;
    if (isJornadaOpen()) hasGrade=false;
    if (isJornadaKO()) hasGrade=false;
    if (!hasGrade) return formatCategoria(val,row,idx);
    // return formatCategoria(row.Categoria,row.idx)+"/"+formatGrado(row.Grado,row,idx);
    return row.Categoria+"-"+formatGrado(row.Grado,row,idx); // not enoght space in column :-(
}

function formatGrado1(val,row,idx) {
    switch (parseInt(val)) {
        case 0: return "   ";
        case 1: return "&#x2714; (2)" // default for historic reasons
        case 2: return "&#x2714; (1)"
        case 3: return "&#x2714; (3)"
    }
    // default. should not happen
    return (val==0)?"   ":"&#x2714; ("+val+")";
}

function formatTeamDogs(val,row,idx) {
    var dogs=getTeamDogs(row);
    return (dogs[0]<=1)?"   ":""+dogs[0]+"/"+dogs[1]
}

/** convierte Equipos3/Equipos4 en mindogs */
function formatMinDogs(val,row,idx) {
    var d=getTeamDogs(row)[0];
    return (d<=1)?' ':d;
}
/** convierte Equipos3/Equipos4 en maxdogs */
function formatMaxDogs(val,row,idx) {
    var d=getTeamDogs(row)[1];
    return (d<=1)?' ':d;
}

/** categoria:numdogs */
function formatTrainingCell1(val,row,idx) { return row.Key1+" - "+val; }
function formatTrainingCell2(val,row,idx) { return row.Key2+" - "+val; }
function formatTrainingCell3(val,row,idx) { return row.Key3+" - "+val; }
function formatTrainingCell4(val,row,idx) { return row.Key4+" - "+val; }

function formatDogName(val,row,idx) { // long name limited to 20 characters
    if (typeof(val)==="undefined") return ""; // to prevent initial empty rows
    if (!useLongNames()) return formatBoldBig(val.substr(0,16),row,idx); // cut on 16 chars to avoid loosing datagrid rendering
    return formatBold(row.Nombre+" - <br/>"+row.NombreLargo.substr(0,20),row,idx);
}

function clubOrCountry() {
    return isInternational(workingData.federation)? "<?php _e('Country');?>":"<?php _e('Club');?>";
}

/* formatter para el orden de salida de la ventana de reordenacion */
function formatReorder(val,row,idx) {
    return '<input type="text" style="width:35px;" id="reorder-item'+idx+'" value="'+val+'" maxlength="4"'+
        ' onchange="reorder_check('+row.Current+',this)"/>';
}

/* formatters para datagrid de inscripciones */
function formatJourneyInscription(journey,val,row,idx) {
    if ( typeof (val) === "undefined" ) return ""; // not yet loaded datagrid
    var jornada=$('#inscripciones-jornadas').datagrid('getRows')[journey];
    if ( jornada.Nombre === "-- Sin asignar --") return ""; // undefined journey
    if (!canInscribe(jornada,row['Grado'])) return ""; // journey doesn't match grade
    var checked=(val==0)?'':'checked="checked"';
    var fn="changeInscription("+idx+","+row.Prueba+","+row.Perro+","+journey+",this);"
    return '<input type="checkbox" value="1" '+checked+' onchange="'+fn+'">';
}

function formatJ1(val,row,idx) { return formatJourneyInscription(0,val,row,idx); }
function formatJ2(val,row,idx) { return formatJourneyInscription(1,val,row,idx); }
function formatJ3(val,row,idx) { return formatJourneyInscription(2,val,row,idx); }
function formatJ4(val,row,idx) { return formatJourneyInscription(3,val,row,idx); }
function formatJ5(val,row,idx) { return formatJourneyInscription(4,val,row,idx); }
function formatJ6(val,row,idx) { return formatJourneyInscription(5,val,row,idx); }
function formatJ7(val,row,idx) { return formatJourneyInscription(6,val,row,idx); }
function formatJ8(val,row,idx) { return formatJourneyInscription(7,val,row,idx); }

/* formatters para datagrid de resultados */
function formatFaltasTocados(val,row,idx) { return parseInt(row.Faltas)+parseInt(row.Tocados); }
function formatPuesto(val,row,idx) { return '<span style="font-weight:bold">'+((row.Penalizacion>=100)?"-":val)+'</span>'; }
function formatPuestoBig(val,row,idx) { return '<span style="font-size:1.5em;font-weight:bold">'+((row.Penalizacion>=100)?"-":val)+'</span>'; }
function formatVelocidad(val,row,idx) { return (row.Penalizacion>=200)?"-":toFixedT(parseFloat(val),1); }
function formatTiempo(val,row,idx) { return (row.Penalizacion>=200)?"-":toFixedT(parseFloat(val),ac_config.numdecs); }
function formatTiempoEquipos1(val,row,idx) { return (row.Outs1>0)?"-":toFixedT(parseFloat(val),ac_config.numdecs); }
function formatTiempoEquipos2(val,row,idx) { return (row.Outs2>0)?"-":toFixedT(parseFloat(val),ac_config.numdecs); }
function formatPenalizacion(val,row,idx) { return toFixedT(parseFloat(val),ac_config.numdecs); }
function formatTiempoBold(val,row,idx) {
    var t=toFixedT(parseFloat(val),ac_config.numdecs);
    return '<span style="font-weight:bold">'+t+'</span>';
}
function formatPenalizacionBold(val,row,idx) {
    var t=toFixedT(parseFloat(val),ac_config.numdecs);
    return '<span style="font-weight:bold">'+t+'</span>';
}
function formatEliminado(val,row,idx) { return (row.Eliminado==0)?"":'<?php _e("Elim"); ?>'; }
function formatNoPresentado(val,row,idx) { return (row.NoPresentado==0)?"":'<?php _e("N.P."); ?>'; }

/* formaters para el frm_clasificaciones */
function formatPuestoFinal(val,row,idx) { return '<span style="font-weight:bold">'+((row.Penalizacion>=200)?"-":val)+'</span>'; }
function formatPuestoFinalBig(val,row,idx) {
    return '<span style="font-size:1.5em;font-weight:bold">'+((row.Penalizacion>=400)?"-":val)+'</span>';
}

function formatPenalizacionFinal(val,row,idx) {
    var p=row.Penalizacion;
    if (p>=800) return "-";
    if (p>=400) return toFixedT(parseFloat(p-400),ac_config.numdecs);
    return toFixedT(parseFloat(val),ac_config.numdecs);
}

function formatTPen(val,p,idx) {
    if (p>=400) return '-';
    if (p>=200) return '0';
    return toFixedT(parseFloat(val),ac_config.numdecs);
}

function formatT1(val,row,idx) { return formatTPen(val,row.P1,idx); }
function formatT2(val,row,idx) { return formatTPen(val,row.P2,idx); }
function formatT3(val,row,idx) { return formatTPen(val,row.P3,idx); }
function formatT4(val,row,idx) { return formatTPen(val,row.P4,idx); }
function formatT5(val,row,idx) { return formatTPen(val,row.P5,idx); }
function formatT6(val,row,idx) { return formatTPen(val,row.P6,idx); }
function formatT7(val,row,idx) { return formatTPen(val,row.P7,idx); }
function formatT8(val,row,idx) { return formatTPen(val,row.P8,idx); }
function formatP1(val,row,idx) { return formatTPen(val,row.P1,idx); }
function formatP2(val,row,idx) { return formatTPen(val,row.P2,idx); }
function formatP3(val,row,idx) { return formatTPen(val,row.P3,idx); }
function formatP4(val,row,idx) { return formatTPen(val,row.P4,idx); }
function formatP5(val,row,idx) { return formatTPen(val,row.P5,idx); }
function formatP6(val,row,idx) { return formatTPen(val,row.P6,idx); }
function formatP7(val,row,idx) { return formatTPen(val,row.P7,idx); }
function formatP8(val,row,idx) { return formatTPen(val,row.P8,idx); }
function formatV1(val,row,idx) { return (row.P1>=200)?"-":toFixedT(parseFloat(val),1); }
function formatV2(val,row,idx) { return (row.P2>=200)?"-":toFixedT(parseFloat(val),1); }
function formatV3(val,row,idx) { return (row.P3>=200)?"-":toFixedT(parseFloat(val),1); }
function formatV4(val,row,idx) { return (row.P4>=200)?"-":toFixedT(parseFloat(val),1); }
function formatV5(val,row,idx) { return (row.P5>=200)?"-":toFixedT(parseFloat(val),1); }
function formatV6(val,row,idx) { return (row.P6>=200)?"-":toFixedT(parseFloat(val),1); }
function formatV7(val,row,idx) { return (row.P7>=200)?"-":toFixedT(parseFloat(val),1); }
function formatV8(val,row,idx) { return (row.P8>=200)?"-":toFixedT(parseFloat(val),1); }

function formatTF(val,row,idx) {
    var t=parseFloat(row.Tiempo);
    return (row.Penalizacion>=200)?"-":toFixedT(t,ac_config.numdecs);
}
function formatTP(val,row,idx) {
    var t=parseFloat(row.Tiempo);
    return (row.Penalizacion>=200)?"-":toFixedT(t,ac_config.numdecs);
}

/**
 * Return short name for requested federation. Use to format datagrid cell
 * @param {int} val Federation ID
 * @param {int} row unused
 * @param {int} idx unused
 * @returns {string} requested value or index if not found
 */
function formatFederation(val,row,idx) {
    if (typeof(val)==="undefined") return "";
    var v=parseInt(val);
    if (typeof(ac_fedInfo[v])==="undefined") return val;
    return ac_fedInfo[v].Name;
}
function formatModuleID(val,row,idx) {
    if (typeof(val)==="undefined") return "";
    return ""+row.FederationID+" / "+val;
}
/* stylers para formateo de celdas especificas */
function formatPreAgility(val,row,idx) {
    // notice that in 3.4+ PreAgility2 is no longer used
    if (typeof(workingData.prueba)==="undefined") return "";
    $('#jornadas-PreAgilityName').html(ac_fedInfo[workingData.federation].ListaGrados['P.A.']);
    var pa=parseInt(row.PreAgility)+parseInt(row.PreAgility2);
    return (pa==0)?"":"&#x2714; ("+pa+")";
}
/* indicar numero de miembros de un equipo */
function formatTeamCount(val,row,idx) { // val is in format BEGIN,id,id,END
    if (typeof(val)==="undefined") return " ";
    if (row.Nombre==="-- Sin asignar --") return " ";
    return val.toString().split(",").length-2;
}

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

function formatTrainingState(val,row,idx) {
    if (row.Estado<0) return '<span style="font-weight:bold;color:#0000FF">'+(idx+1).toString()+'</span>';
    if (row.Estado==0) return '<span style="font-weight:bold;color:#FF0000">'+(idx+1).toString()+'</span>';
    if (row.Estado>0) return '<span style="font-weight:bold;color:#00FF00">'+(idx+1).toString()+'</span>';
}
function formatTrainingTime(val,row,idx) { return toHMS(val * ac_config.training_time); }

/**
 * Return logo matching requested cell value
 * @param val logo name
 * @param row row data
 * @param idx row index
 * @returns {string} html string to be inserted 
 */
function formatLogo(val,row,idx) {
    // TODO: no idea why idx:0 has no logo declared
    if (typeof(val)==="undefined") val="empty.png";
    var fed=workingData.federation;
    return '<img src="../ajax/images/getLogo.php?Fed='+fed+'&Logo='+val+'" width="30" height="30" alt="'+val+'"/>\n';
}

/**
 * Return list of logos matching requested team cell value
 * Iterate competitor list searching for team members. add their logo team if not yet done
 * @param val not used. just for compatibility with datagrid formatters
 * @param row row data
 * @param idx row index
 * @returns {string} html string to be inserted 
 */
function formatTeamLogos(val,row,idx) {
    var logos=[];
    if (typeof (workingData.individual) === "undefined" ) return "logo logo logo logo";
    for(var n=0; n<workingData.individual.length;n++) {
        var competitor=workingData.individual[n];
        if (competitor['Equipo']!=row.ID) continue;
        if ($.inArray(competitor['LogoClub'],logos)<0) logos.push(competitor['LogoClub']);
        if (logos.length>=4) break; // TODO: replace with maxdogs
    }
    var str="";
    var fed=workingData.federation;
    for (n=0;n<logos.length;n++) {
        str +='<img src="../ajax/images/getLogo.php?Fed='+fed+'&Logo='+logos[n]+'" width="30" height="30" alt="'+logos[n]+'"/>\n';
    }
    return str;
}

/* comodity function to set up round SCT unit based on SCT type */
function round_setUnit(unit,dest) {
    if (unit==0) $(dest).combobox('setValue','s'); // fixed SCT: set unit to seconds
    else if (unit==6) $(dest).combobox('setValue','m'); // Velocity instead of time/percent: set unit to mts/sec
    else $(dest).combobox('setValue','%'); // else assume percentages
}

/* comodity function to set up round SCT mode based on SCT unit */
function round_setMode(tipo,dest) {
    var current=parseInt($(dest).combobox('getValue'));
    if ((tipo==='s')&&(current==6) ){
        // on change to seconds assume fixed sct when current is speed
        $(dest).combobox('setValue',0);
    }
    if (tipo==='m') {
        // on change to m/s force speed defined sct
        $(dest).combobox('setValue',6);
    }
    if (tipo==='%') {
        // on percentage: assume result plus something when in fixed/speed mode; else dont change
        if ( (current==0) || (current==6) ) $(dest).combobox('setValue',1);
    }
}

function formatTeamResults( name,value , rows ) {
    // todo: check eq3 or eq4 contest and eval time and penalization
    var time=0.0;
    var penal=0.0;
    var logos="";
    var width=toPercent($(name).datagrid('getPanel').panel('options').width,90);
    var mindogs=getMinDogsByTeam();
    function addLogo(logo) {
        if (logos.indexOf(logo)>=0) return;
        logos = logos + '&nbsp;<img height="40px" src="../images/logos/'+ logo + '"/>';
    }
    for (var n=0;n<mindogs;n++) {
        if ( typeof(rows[n])==="undefined") {
            penal+=400.0;
            addLogo('null.png');
        } else {
            penal+=parseFloat(rows[n].Penalizacion);
            time+=parseFloat(rows[n].Tiempo);
            addLogo(rows[n].LogoClub);
        }
    }
    // return "Equipo: "+value+" Tiempo: "+time+" Penalizaci&oacute;n: "+penal;
    return '<div class="vw_equipos3" style="width:'+width+'px">'+
        '<span style="width:'+toPercent(width,20)+'px;text-align:left;">'+logos+'</span>'+
        '<span style="width:'+toPercent(width,45)+'px;text-align:right;">'+value+'</span>' +
        '<span style="width:'+toPercent(width,10)+'px;text-align:right;">T: '+toFixedT(time,ac_config.numdecs)+'</span>' +
        '<span style="width:'+toPercent(width,10)+'px;text-align:right;">P:'+toFixedT(penal,ac_config.numdecs)+'</span>'+
        '<span style="width:'+toPercent(width,10)+'px;text-align:right;">'+(workingData.teamCounter++)+'</span>'+
        '</div>';
}

function formatVwTeamResults(value,rows) { return formatTeamResults('#vw_parciales-datagrid',value,rows); }

function formatTeamClasificaciones(dgname,value,rows) {
    var logos="";
    var mindogs=getMinDogsByTeam();
    var maxdogs=getMaxDogsByTeam();
    function sortResults(a,b) {
        return (a.penal== b.penal)? (a.time - b.time) : (a.penal - b.penal);
    }
    function addLogo(logo) {
        if (logos.indexOf(logo)>=0) return;
        logos = logos + '&nbsp;<img height="40px" src="../images/logos/'+ logo + '"/>';
    }
    // cogemos y ordenamos los datos de cada manga
    var manga1={ time:0.0, penal:0.0, perros:[] };
    var manga2={ time:0.0, penal:0.0, perros:[] };
    for (var n=0;n<maxdogs;n++) {
        if (typeof(rows[n]) === "undefined") {
            manga1.perros[n] = {time: parseFloat(0.0), penal: parseFloat(400.0)};
            manga2.perros[n] = {time: parseFloat(0.0), penal: parseFloat(400.0)};
            addLogo('null.png');
        } else {
            manga1.perros[n] = {time: parseFloat(rows[n].T1), penal: parseFloat(rows[n].P1)};
            manga2.perros[n] = {time: parseFloat(rows[n].T2), penal: parseFloat(rows[n].P2)};
            addLogo(rows[n].LogoClub);
        }
    }
    // ordenamos ahora las matrices de resultados
    (manga1.perros).sort(sortResults);
    (manga2.perros).sort(sortResults);
    // y sumamos los dos/tres/cuatro/cinco primeros ( en funcion del tipo de competicion de equipos ) resultados
    for (n=0;n<mindogs;n++) {
        manga1.time +=parseFloat(manga1.perros[n].time);
        manga1.penal +=parseFloat(manga1.perros[n].penal);
        manga2.time +=parseFloat(manga2.perros[n].time);
        manga2.penal +=parseFloat(manga2.perros[n].penal);
    }
    // el resultado final es la suma de las mangas
    var time=manga1.time+manga2.time;
    var penal=manga1.penal+manga2.penal;
    var m1="T1: "+toFixedT((manga1.time),ac_config.numdecs)+" -- P1: "+toFixedT((manga1.penal),ac_config.numdecs);
    var m2="T2: "+toFixedT((manga2.time),ac_config.numdecs)+" -- P2: "+toFixedT((manga2.penal),ac_config.numdecs);
    var mf="<?php _e('Time');?>: "+toFixedT(time,ac_config.numdecs)+" -- <?php _e('Penal');?>: "+toFixedT(penal,ac_config.numdecs);

    var width=toPercent($(dgname).datagrid('getPanel').panel('options').width,90); // let expand button to exist
    // return "Equipo: "+value+" Tiempo: "+time+" Penalizaci&oacute;n: "+penal;
    return '<div class="vw_equipos3" style="width:'+width+'px">'+
        '<span style="width:'+toPercent(width,15)+'px;text-align:left;">'+logos+'</span>'+
        '<span style="width:'+toPercent(width,20)+'px;text-align:left;">'+value+'</span>' +
        '<span style="width:'+toPercent(width,15)+'px;text-align:left;">'+m1+'</span>' +
        '<span style="width:'+toPercent(width,15)+'px;text-align:left;">'+m2+'</span>'+
        '<span style="width:'+toPercent(width,25)+'px;text-align:right;">'+mf+'</span>'+
        '<span style="width:'+toPercent(width,5)+'px;text-align:right;font-size:1.25vw;">'+(workingData.teamCounter++)+'</span>'+
        '</div>';
}

function formatVwTeamClasificaciones(value,rows) { return formatTeamClasificaciones('#vwcf_clasificacion-datagrid',value,rows); }

// repaint datagrid saving options, and load with empty data
// WARNING !! DO NOT USE AS RESULT OF "OnLoadSuccess" (infinite loop)
function resetDatagrid(dg,data) {
    if ( dg.length ==0) return; // to avoid try to reset undefined jquery objects
    if (typeof(data)==="undefined") data={"total":0,"rows":[]};
    var opts=dg.datagrid('options');
    setTimeout(function(){
        dg.datagrid(opts);
        dg.datagrid('loadData', data);
        dg.datagrid('fitColumns');
    },0);
}

// called on init or openS
// select individual or team view
// show/hide columns according (inter)national federation type
function vwcf_configureScreenLayout() {
    var resdg=$('#finales_individual-datagrid');
    var lastdg=$('#finales_last_individual-datagrid');
    var restdg=$('#finales_equipos-datagrid');
    var lasttdg=$('#finales_last_equipos-datagrid');
    var calldg=$('#vwc_llamada-datagrid');
    var team=isJornadaEquipos(null);
    var intl=isInternational(null);

    // on intl move country to right of country flag
    if (intl && !team) {
        resdg.datagrid('moveField',{idxHead:1,idxFrom:'NombreClub', idxTo:'Dorsal'});
        lastdg.datagrid('moveField',{idxHead:0,idxFrom:'NombreClub', idxTo:'Dorsal'});
    }
    // individual or team view
    resdg.datagrid('getPanel').panel((team)?'close':'open');
    lastdg.datagrid('getPanel').panel((team)?'close':'open');
    restdg.datagrid('getPanel').panel((team)?'open':'close');
    lasttdg.datagrid('getPanel').panel((team)?'open':'close');
    calldg.datagrid((team)?'hideColumn':'showColumn','NombreClub');
    calldg.datagrid((team)?'showColumn':'hideColumn','NombreEquipo');

    // show hide license and califications according national or international
    resdg.datagrid((intl)?'hideColumn':'showColumn','Licencia');
    lastdg.datagrid((intl)?'hideColumn':'showColumn','Licencia');
    $('#finales_individual_teaminfo').parents('td').attr('colspan',(intl)?6:7);
    $('#finales_individual-ClubOrCountry').html(clubOrCountry());
    $('#finales_last_individual-ClubOrCountry').html(clubOrCountry());
    // $('#finales_equipos-ClubOrCountry').html(clubOrCountry()); // doesn't exist :-)
    $('#finales_last_equipos-ClubOrCountry').html(clubOrCountry());

    // reload datagrid with new options and fill with empty data and expand to max width
    resetDatagrid(resdg);
    resetDatagrid(lastdg);
    resetDatagrid(restdg);
    resetDatagrid(lasttdg);
    calldg.datagrid('fitColumns'); // do not load empty data as 'open' will do
}

// called on init or open
// select individual or team view
// show/hide columns according (inter)national federation type
function vwcp_configureScreenLayout() {
    var resdg=$('#parciales_individual-datagrid');
    var lastdg=$('#parciales_last_individual-datagrid');
    var restdg=$('#parciales_equipos-datagrid');
    var lasttdg=$('#parciales_last_equipos-datagrid');
    var calldg=$('#vwc_llamada-datagrid');
    var team=isJornadaEquipos(null);
    var intl=isInternational(null);
    var games=isJornadaGames(null);

    // on intl move country to right of country flag
    if (intl && !team) {
        resdg.datagrid('moveField',{idxHead:0,idxFrom:'NombreClub', idxTo:'Dorsal'});
        lastdg.datagrid('moveField',{idxHead:0,idxFrom:'NombreClub', idxTo:'Dorsal'});
    }
    // individual or team view

    resdg.datagrid('getPanel').panel((team)?'close':'open');
    lastdg.datagrid('getPanel').panel((team)?'close':'open');
    restdg.datagrid('getPanel').panel((team)?'open':'close');
    lasttdg.datagrid('getPanel').panel((team)?'open':'close');
    calldg.datagrid((team)?'hideColumn':'showColumn','NombreClub');
    calldg.datagrid((team)?'showColumn':'hideColumn','NombreEquipo');
    // show hide license according national or international
    resdg.datagrid((intl||games)?'hideColumn':'showColumn','Licencia');
    lastdg.datagrid((intl||games)?'hideColumn':'showColumn','Licencia');
    // restdg.datagrid((intl)?'hideColumn':'showColumn','Licencia');
    // lasttdg.datagrid((intl)?'hideColumn':'showColumn','Licencia'); // no existe campo 'Licencia' en resultados  equipos

    $('#parciales_individual-ClubOrCountry').html(clubOrCountry());
    $('#parciales_last_individual-ClubOrCountry').html(clubOrCountry());
    // $('#finales_equipos-ClubOrCountry').html(clubOrCountry()); // doesn't exist :-)
    $('#parciales_last_equipos-ClubOrCountry').html(clubOrCountry());

    // reload datagrid with new options and fill with empty data and expand to max width
    resetDatagrid(resdg);
    resetDatagrid(lastdg);
    resetDatagrid(restdg);
    resetDatagrid(lasttdg);
    calldg.datagrid('fitColumns'); // do not load empty data as 'open' will do
}

function ordenSalida_configureScreenLayout(dg) {
    // On Team journeys, show team name instead of club, and viceversa
    if (isJornadaEquipos(null) ) {
        dg.datagrid('showColumn','NombreEquipo');
        dg.datagrid('hideColumn','NombreClub');
    } else  {
        dg.datagrid('hideColumn','NombreEquipo');
        dg.datagrid('showColumn','NombreClub');
    }
    // on international contests hide license, and enlarge name to allow pedigree name
    if (isInternational(workingData.federation)) {
        dg.datagrid('setFieldTitle',{'field':'NombreClub','title':'<?php _e("Country");?>'});
        dg.datagrid('hideColumn','Licencia');
        dg.datagrid('moveField',{idxHead:0,idxFrom:'NombreClub', idxTo:'Dorsal'});
    }
    resetDatagrid(dg);
}

function inscripciones_configureScreenLayout(dg) {
    // on international contests hide license, and enlarge name to allow pedigree name
    if (isInternational(workingData.federation)) {
        dg.datagrid('setFieldTitle',{'field':'NombreClub','title':'<?php _e("Country");?>'});
        dg.datagrid('hideColumn','Licencia');
        dg.datagrid('moveField',{idxHead:0,idxFrom:'NombreClub', idxTo:'Dorsal'});
    }
    // do not call reset, to avoid load infinite loop
    var opts=dg.datagrid('options');
    setTimeout(function(){dg.datagrid(opts)},0);
}

