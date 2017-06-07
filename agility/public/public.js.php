/*
public.js

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

/*********************************************** funciones de formateo de pantalla */
<?php
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
$config =Config::getInstance();
?>


/**
 * Obtiene la informacion de la prueba para cabecera y pie de pagina
 * @
 */
function pb_getHeaderInfo(showJourney) {
    if (typeof(showJourney)==="undefined") showJourney=true;
    $.ajax( {
        type: "GET",
        dataType: 'json',
        url: "/agility/server/web/publicFunctions.php",
        data: {
            Operation: 'infodata',
            Prueba: workingData.prueba,
            Jornada: workingData.jornada,
            Manga: workingData.manga,
            Tanda: workingData.tanda,
            Mode: workingData.mode
        },
        success: function(data,status,jqxhr) {
            var str= data.Prueba.Nombre;
            if (data.Jornada) { // training session has no journey :-)
                if (showJourney) str = str + '<br />' +  data.Jornada.Nombre;
            }
            $('#pb_header-infocabecera').html(str);
            // on international competitions, use federation Organizer logo
            var logo='/agility/images/logos/'+data.Club.Logo;
            if ( (data.Club.logo==="") || isInternational(data.Prueba.RSCE)) {
                logo=ac_fedInfo[data.Prueba.RSCE].OrganizerLogo
            }
            $('#pb_header-logo').attr('src',logo);
        }
    });
}

function pb_setFooterInfo() {
    var logo=ac_fedInfo[workingData.federation].Logo;
    var logo2=ac_fedInfo[workingData.federation].ParentLogo;
    var url=ac_fedInfo[workingData.federation].WebURL;
    var url2=ac_fedInfo[workingData.federation].ParentWebURL;
    $('#pb_footer-footerData').load("/agility/public/pb_footer.php",{},function(response,status,xhr){
        $('#pb_footer-logoFederation').attr('src',logo);
        $('#pb_footer-urlFederation').attr('href',url);
        $('#pb_footer-logoFederation2').attr('src',logo2);
        $('#pb_footer-urlFederation2').attr('href',url2);
    });
}

function pb_updateEntrenamientos() {
    $('#entrenamientos-datagrid').datagrid('reload');
}

function pb_updateOrdenSalida2(id) {
    $('#ordensalida-datagrid').datagrid('reload',{
        Operation: 'getDataByTanda',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 1, // defaults to "-- sin asignar --"
        ID:  id // Tanda ID
    });
}

/**
 * Imprime el orden de salida de la prueba y jornada seleccionada por el usuario
 * ejecutada desde la ventana con combogrid
 */
function pb_updateOrdenSalida() {
    var row=$('#pb_enumerateMangas').combogrid('grid').datagrid('getSelected');
    if (row) pb_updateOrdenSalida2(row.ID);
}

/**
 * Imprime los inscritos en la prueba y jornada seleccionada por el usuario
 */
function pb_updateInscripciones() {
    $('#pb_inscripciones-datagrid').datagrid('reload', {
        Operation:'inscritosbyjornada',
        Prueba:workingData.prueba,
        Jornada:workingData.jornada
    });
}

/**
 * Imprime los inscritos en la prueba y jornada por equipos seleccionada por el usuario
 */
function pb_updateInscripciones_eq3() {
    $('#pb_inscripciones_eq3-datagrid').datagrid('reload', {
        Operation:'select',
        Prueba:workingData.prueba,
        Jornada:workingData.jornada,
        where:'',
        HideDefault:1, // do not show default team
        AddLogo:1 // generate LogoTeam
    });
}

/**
 * imprime el programa de la jornada
 */
function pb_updatePrograma() {
    $('#pb_programa-datagrid').datagrid('reload',{
        Operation: 'getTandas',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 0 // Set Session ID to 0 to include everything
    });
}

/**
 * En funcion de public, videowall, tablet o livestream, ajustamos el datagrid y los contenidos
 * En funcion de federacion ajustamos, club, pais, categorias
 *
 * @param {object} dg jquery easyui datagrid object
 */
function pb_setTrainingLayout(dg) {
    $('#vw_header-infomanga').html("(<?php _e('No round selected');?>)");
    // fix country/club and reload datagrid
    dg.datagrid('setFieldTitle',{'field':'NombreClub','title':clubOrCountry()});
    // en funcion de la federacion se ajusta el numero de categorias
    var cats=howManyHeights(workingData.federation);
    dg.datagrid((cats==3)?'hideColumn':'showColumn','Value4');
    dg.datagrid('fitColumns');
}

/**
 * Call server for events
 */
function pb_lookForMessages() {
    // TO BE WRITTEN
}