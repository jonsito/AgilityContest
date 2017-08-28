<?php
header("Access-Control-Allow-Origin: https//{$_SERVER['SERVER_ADDR']}/agility",false);
header("Access-Control-Allow-Origin: https://{$_SERVER['SERVER_NAME']}/agility",false);
/*
 index.php

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

/**
 * PERSONALIZED ENTRY PAGE FOR AWC FCI 2016
 */
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
require_once(__DIR__ . "/../server/auth/AuthManager.php");
if(!isset($config)) $config =Config::getInstance();

/* check for properly installed xampp */
if( ! function_exists('openssl_get_publickey')) {
    die("Invalid configuration: please uncomment line 'module=php_openssl.dll' in file '\\xampp\\php\\php.ini'");
}
$am=new AuthManager("Public");
if (!$am->allowed(ENABLE_PUBLIC)) {
    die("Current license has no permissions to handle public (web) access related functions");
}
// tool to perform automatic upgrades in database when needed
require_once(__DIR__ . "/../server/web/PublicWeb.php");

$pruebaID=http_request("Prueba","i",22);
$requestedJornada=http_request('J',"s",""); // Individual - Teams
if ($requestedJornada=="") $requestedJornada=http_request('Jornada',"s","Individual");
$requestedManga=http_request('M',"s",""); // Agility - Jumping - Final
if ($requestedManga=="") $requestedManga=http_request('Manga','s','Agility');
$requestedCategoria=http_request('C',"s",""); // Large - Medium - Small
if ($requestedCategoria=="") $requestedCategoria=http_request('Categoria',"s","Large"); // Large - Medium - Small
$requestedOrden=http_request('S',"s",""); // (emtpy):competion -  "Start":starting order
$pb=new PublicWeb($pruebaID);
$ptree=$pb->publicweb_deploy();
$poster="/agility/images/agilityawc2016.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $ptree['Prueba']['Nombre'] . " - " . _("On line data"); ?> </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="application-name" content="Agility Contest" />
    <meta name="copyright" content="© 2013-2015 Juan Antonio Martinez" />
    <meta name="author" lang="en" content="Juan Antonio Martinez" />
    <meta name="description"
          content="A web client-server (xampp) app to organize, register and show results for FCI Dog Agility Contests" />
    <meta name="distribution"
          content="This program is free software; you can redistribute it and/or modify it under the terms of the 
		GNU General Public License as published by the Free Software Foundation; either version 2 of the License, 
		or (at your option) any later version." />
    <!-- try to disable zoom in tablet on double click -->
    <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=2.0, minimum-scale=0.5, user-scalable=yes"/>

    <link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
    <link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/icon.css" />
    <link rel="stylesheet" type="text/css" href="/agility/css/style.css" />
    <link rel="stylesheet" type="text/css" href="/agility/css/datagrid.css" />
    <link rel="stylesheet" type="text/css" href="/agility/css/videowall_css.php" />
    <link rel="stylesheet" type="text/css" href="/agility/css/public_css.php" />

    <style type="text/css">
        html, body {
            margin:0;
            padding:0;
            height: 100%;
        }
        #poster_panel {
            /* background: #000000 url("/agility/awcfci2016/spainbg.png") no-repeat bottom left;*/
            background: <?php echo $config->getEnv('pb_hdrbg1');?>;
            background-size: 100% 100%;
            width: 100%;
            height: auto;
            min-height:100%;
        }
        #menu_panel {
            /* background: #000000 url("/agility/awcfci2016/spainbg.png") no-repeat bottom left;*/
            background: <?php echo $config->getEnv('pb_hdrbg1');?>;
            background-size: 100% 100%;
            width: 100%;
            height: auto;
        }
        .menu_enum dt {
            font-size: 1.4vw;
            font-weight: bold;
        }
        .menu_enum dd {
            /* to avoid double <dd><ol> indentation */
            margin: 0;
            list-style: inside;
            padding-left: 0;
            font-size: 1.2vw;
        }
        .menu_enum dd a {
            text-decoration: none; /* remove underline in <a></a> elements */
        }

        /* tip for fix data size in smartphones ----------- */
        @media only screen and (max-width: 760px) {
            .datagrid-cell {
                font-size:0.75em;
            }

        }
    </style>

    <script src="/agility/lib/jquery-2.2.4.min.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" ></script>
    <script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-dnd/datagrid-dnd.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-detailview.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-scrollview.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/lib/sprintf.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/scripts/easyui-patches.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/scripts/datagrid_formatters.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/scripts/common.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/scripts/auth.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/scripts/competicion.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/scripts/results_and_scores.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/public/public.js.php" type="text/javascript" charset="utf-8" > </script>

    <script type="text/javascript" charset="utf-8">

        /* make sure configuration is loaded from server before onLoad() event */
        var pb_config = {
            'Timeout':null,
            'LastEvent':0
        };
        loadConfiguration();
        getLicenseInfo();
        getFederationInfo(); // calls in turn initWorkingData()

        workingData.requestedJornada='<?php echo $requestedJornada;?>';
        workingData.requestedCategoria='<?php echo $requestedCategoria;?>';
        workingData.requestedManga='<?php echo $requestedManga;?>';
        workingData.requestedOrden='<?php echo $requestedOrden;?>';

        /* not really needed for public access, but stay here for compatibility */
        function initialize() {
            // make sure that every ajax call provides sessionKey
            $.ajaxSetup({
                beforeSend: function(jqXHR,settings) {
                    if ( typeof(ac_authInfo.SessionKey)!=='undefined' && ac_authInfo.SessionKey!==null) {
                        jqXHR.setRequestHeader('X-AC-SessionKey',ac_authInfo.SessionKey);
                    }
                    return true;
                }
            });
        }

        function myRowStyler(idx,row) { return pbRowStyler(idx,row); }
        function myRowStyler2(idx,row) { return pbRowStyler2(idx,row); }

        /**
         * return to caller page
         * @param {boolean} flag unused, just for compatibility
         */
        function pbmenu_expandMenu(flag) {
            if (pb_config.Timeout !== null ) {
                clearTimeout(pb_config.Timeout);
                pb_config.Timeout=null;
            }
            // jump to back page if available, else to generic one
            document.location.href=
                '<?php echo (empty($_SERVER['HTTP_REFERER']))?"http://agilitywc2016.com/competicion-2":$_SERVER['HTTP_REFERER'];?>';
        }

        function pbmenu_getAndSet(prueba,jornada) {
            var p=<?php echo json_encode($ptree['Prueba'],JSON_PRETTY_PRINT); ?>;
            var j=<?php echo json_encode($ptree['Jornadas'],JSON_PRETTY_PRINT); ?>;
            setPrueba(p);
            for(var n=0;n<j.length;n++) {
                if ( parseInt(j[n]['ID'])!==jornada) continue;
                setJornada(j[n]);
                break;
            }
        }
        
        function pbmenu_loadInscriptions(prueba,jornada) {
            pbmenu_getAndSet(prueba,jornada);
            var page="/agility/public/pbmenu_inscripciones.php";
            if (isJornadaEqMejores() ) page="/agility/public/pbmenu_inscripciones_equipos.php";
            if (isJornadaEqConjunta() ) page="/agility/public/pbmenu_inscripciones_equipos.php";
            pb_config.Timeout="readyToRun";
            $('#pb_layout').layout('panel','east').panel('refresh',page);
        }

        function pbmenu_loadTrainingSession(prueba) {
            var p=<?php echo json_encode($ptree['Prueba']); ?>;
            setPrueba(p);
            pb_config.Timeout="readyToRun";
            $('#public-contenido').panel('refresh',"/agility/public/pbmenu_entrenamientos.php");
        }

        function pbmenu_loadTimeTable(prueba,jornada) {
            var p=<?php echo json_encode($ptree['Prueba']); ?>;
            var j=<?php echo json_encode($ptree['Jornadas']); ?>;
            setPrueba(p);
            for(var n=0;n<j.length;n++) {
                if ( parseInt(j[n]['ID'])!==jornada) continue;
                setJornada(j[n]);
                break;
            }
            pb_config.Timeout="readyToRun";
            $('#public-contenido').panel('refresh',"/agility/public/pbmenu_programa.php");
        }

        function pbmenu_loadStartingOrder(prueba,jornada,tanda) {
            pbmenu_getAndSet(prueba,jornada);
            // evaluate tanda by looking at tandaID
            var tandas=workingData.datosJornada.Tandas;
            for (var n=0; n<tandas.length;n++) {
                if ( parseInt(tandas[n]['ID'])!==tanda ) continue;
                setTanda(tandas[n]);
                break;
            }
            pb_config.Timeout="readyToRun";
            $('#public-contenido').panel('refresh',"/agility/public/pbmenu_ordensalida.php");
        }

        function pbmenu_loadPartialScores(prueba,jornada,manga,mode) {
            pbmenu_getAndSet(prueba,jornada);
            // evaluate tanda by looking at tandaID
            var mangas=workingData.datosJornada.Mangas;
            for (var n=0; n<mangas.length;n++) {
                if ( parseInt(mangas[n]['Manga'])!==manga ) continue; // do not use ID
                if ( parseInt(mangas[n]['Mode'])!==mode ) continue; // check mode
                setManga(mangas[n]);
                break;
            }
            pb_config.Timeout="readyToRun";
            var page="/agility/public/pbmenu_parciales.php";
            if (isJornadaEquipos(null) ) page="/agility/public/pbmenu_parciales_equipos.php";
            $('#public-contenido').panel('refresh',page);
        }

        function pbmenu_loadFinalScores(prueba,jornada,serie) {
            pbmenu_getAndSet(prueba,jornada);
            workingData.datosRonda=workingData.datosJornada.Series[serie];
            var page="/agility/public/pbmenu_finales.php";
            if (isJornadaEquipos(null) ) page="/agility/public/pbmenu_finales_equipos.php";
            pb_config.Timeout="readyToRun";
            $('#public-contenido').panel('refresh',page);
        }

        function jumpToRequestedPage(){
            if (workingData.requestedJornada=='Individual') {
                if (workingData.requestedManga=="Agility") {
                    if (workingData.requestedOrden=="") {
                        if (workingData.requestedCategoria=="Large") { // agility large individual
                            pbmenu_loadPartialScores(22,169,215,0);
                        }
                        if (workingData.requestedCategoria=="Medium") { // agility medium individual
                            pbmenu_loadPartialScores(22,169,215,1);
                        }
                        if (workingData.requestedCategoria=="Small") { // agility small individual
                            pbmenu_loadPartialScores(22,169,215,2);
                        }
                    }
                    if (workingData.requestedOrden=="Start") {
                        if (workingData.requestedCategoria=="Large") { // agility large individual starting order
                            pbmenu_loadStartingOrder(22,169,641);
                        }
                        if (workingData.requestedCategoria=="Medium") { // agility medium individual starting order
                            pbmenu_loadStartingOrder(22,169,642);
                        }
                        if (workingData.requestedCategoria=="Small") { // agility small individual starting order
                            pbmenu_loadStartingOrder(22,169,643);
                        }
                    }
                }
                if (workingData.requestedManga=="Jumping") {
                    if (workingData.requestedOrden=="") {
                        if (workingData.requestedCategoria=="Large") { // jumping large individual
                            pbmenu_loadPartialScores(22,169,216,0);
                        }
                        if (workingData.requestedCategoria=="Medium") { // jumping medium individual
                            pbmenu_loadPartialScores(22,169,216,1);
                        }
                        if (workingData.requestedCategoria=="Small") { // jumping small individual
                            pbmenu_loadPartialScores(22,169,216,2);
                        }
                    }
                    if (workingData.requestedOrden=="Start") {
                        if (workingData.requestedCategoria=="Large") { // jumping large individual starting order
                            pbmenu_loadStartingOrder(22,169,644);
                        }
                        if (workingData.requestedCategoria=="Medium") { // jumping medium individual starting order
                            pbmenu_loadStartingOrder(22,169,645);
                        }
                        if (workingData.requestedCategoria=="Small") { // jumping small individual starting order
                            pbmenu_loadStartingOrder(22,169,646);
                        }
                    }
                }
                if (workingData.requestedManga=="Final") {
                    if (workingData.requestedCategoria=="Large") { // final large individual
                        pbmenu_loadFinalScores(22,169,0);
                    }
                    if (workingData.requestedCategoria=="Medium") { // final medium individual
                        pbmenu_loadFinalScores(22,169,1);
                    }
                    if (workingData.requestedCategoria=="Small") { // final small individual
                        pbmenu_loadFinalScores(22,169,2);
                    }
                }
            }
            if (workingData.requestedJornada=='Teams') {
                if (workingData.requestedManga=="Agility") {
                    if (workingData.requestedOrden=="") {
                        if (workingData.requestedCategoria=="Large") { // agility large teams
                            pbmenu_loadPartialScores(22,170,217,0);
                        }
                        if (workingData.requestedCategoria=="Medium") { // agility medium teams
                            pbmenu_loadPartialScores(22,170,217,1);
                        }
                        if (workingData.requestedCategoria=="Small") { // agility small teams
                            pbmenu_loadPartialScores(22,170,217,2);
                        }
                    }
                    if (workingData.requestedOrden=="Start") {
                        if (workingData.requestedCategoria=="Large") { // agility large teams starting order
                            pbmenu_loadStartingOrder(22,170,647);
                        }
                        if (workingData.requestedCategoria=="Medium") { // agility medium teams starting order
                            pbmenu_loadStartingOrder(22,170,648);
                        }
                        if (workingData.requestedCategoria=="Small") { // agility small teams starting order
                            pbmenu_loadStartingOrder(22,170,649);
                        }

                    }
                }
                if (workingData.requestedManga=="Jumping") {
                    if (workingData.requestedOrden=="") {
                        if (workingData.requestedCategoria=="Large") { // jumping large teams
                            pbmenu_loadPartialScores(22,170,218,0);
                        }
                        if (workingData.requestedCategoria=="Medium") { // jumping medium teams
                            pbmenu_loadPartialScores(22,170,218,1);
                        }
                        if (workingData.requestedCategoria=="Small") { // jumping small teams
                            pbmenu_loadPartialScores(22,170,218,2);
                        }
                    }
                    if (workingData.requestedOrden=="Start") {
                        if (workingData.requestedCategoria=="Large") { // jumping large teams starting order
                            pbmenu_loadStartingOrder(22,170,650);
                        }
                        if (workingData.requestedCategoria=="Medium") { // jumping medium teams starting order
                            pbmenu_loadStartingOrder(22,170,651);
                        }
                        if (workingData.requestedCategoria=="Small") { // jumping small teams starting order
                            pbmenu_loadStartingOrder(22,170,652);
                        }
                    }
                }
                if (workingData.requestedManga=="Final") {
                    if (workingData.requestedCategoria=="Large") { // final large teams
                        pbmenu_loadFinalScores(22,170,0);
                    }
                    if (workingData.requestedCategoria=="Medium") { // final medium teams
                        pbmenu_loadFinalScores(22,170,1);
                    }
                    if (workingData.requestedCategoria=="Small") { // final small teams
                        pbmenu_loadFinalScores(22,170,2);
                    }
                }
            }
        }

        function fireUpWhenReady() {
            if (typeof(ac_fedInfo[9])==="object") {
                jumpToRequestedPage();
                return;
            }
            setTimeout(function(){fireUpWhenReady()},200);
        }

    </script>
</head>

<body id="body" onLoad="fireUpWhenReady();">
    <!-- to be replaced on mouse click to load proper page -->
    <div id="public-contenido" class="easyui-panel"
         data-options="fit:true,border:false,noheader:true">
    </div>
</body>
</html>
