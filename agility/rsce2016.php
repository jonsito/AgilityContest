<?php
header("Access-Control-Allow-Origin: https//{$_SERVER['SERVER_ADDR']}/agility",false);
header("Access-Control-Allow-Origin: https://{$_SERVER['SERVER_NAME']}/agility",false);
/*
 index.php

 Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

 This program is free software; you can redistribute it and/or modify it under the terms
 of the GNU General Public License as published by the Free Software Foundation;
 either version 2 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 See the GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along with this program;
 if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require_once(__DIR__ . "/server/tools.php");
require_once(__DIR__ . "/server/auth/Config.php");
require_once(__DIR__ . "/server/auth/AuthManager.php");
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
require_once(__DIR__. "/server/upgradeVersion.php");
require_once(__DIR__. "/server/web/public.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>XXVI Campeonato en Espa&ntilde;a de Agility R.S.C.E.</title>
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
    
    <script src="/agility/lib/jquery-1.12.3.min.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" ></script>
    <script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-dnd/datagrid-dnd.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-detailview.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-groupview.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-scrollview.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/lib/sprintf.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/scripts/easyui-patches.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/scripts/common.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/scripts/auth.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/scripts/competicion.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/public/public.js.php" type="text/javascript" charset="utf-8" > </script>

    <script type="text/javascript" charset="utf-8">

        /* make sure configuration is loaded from server before onLoad() event */
        loadConfiguration();
        getLicenseInfo();
        getFederationInfo();

        /* not really needed for public access, but stay here for compatibility */
        function initialize() {
            // make sure that every ajax call provides sessionKey
            $.ajaxSetup({
                beforeSend: function(jqXHR,settings) {
                    if ( typeof(ac_authInfo.SessionKey)!=='undefined' && ac_authInfo.SessionKey!=null) {
                        jqXHR.setRequestHeader('X-AC-SessionKey',ac_authInfo.SessionKey);
                    }
                    return true;
                }
            });
        }

        /**
         * Common rowStyler function for AgilityContest datagrids
         * @param {int} idx Row index
         * @param {Object} row Row data
         * @return {string} proper row style for given idx
         */
        function myRowStyler(idx,row) {
            var res="background-color:";
            var c1='<?php echo $config->getEnv('vw_rowcolor1'); ?>';
            var c2='<?php echo $config->getEnv('vw_rowcolor2'); ?>';
            if ( (idx&0x01)==0) { return res+c1+";"; } else { return res+c2+";"; }
        }

        function loadInscriptions(prueba,jornada) {

        }
        function loadTimeTable(prueba,jornada) {

        }
        function loadOrdenSalida(prueba,jornada,tanda) {

        }
        function loadPartialScores(prueba,jornada,manga) {

        }
        function loadFinalScores(prueba,jornada,serie) {

        }

    </script>

    <style type="text/css">
        html, body {
            margin:0;
            padding:0;
            height: 100%;
        }
        #menu_panel {
            /* background should be extracted from contest poster information */
            background: #000000 url("background.jpg") no-repeat bottom left;
            background-size: 100% 100%;
            width: 100%;
            height: auto;
            min-height:100%;
        }
        .menu_enum dt {
            font-size: 1.4vw;
            font-weight: bold;
        }
        .menu_enum dd {
            font-size: 1.2vw;
        }
    </style>
</head>
<body class="easyui-layout" data-options="fit:true">

    <div id="menu_panel" data-options="title:'Menu',region:'west',split:true" style="width:80%">
        <div style="float:right;padding:2%">
        <h2>Seguimiento de datos en en l&iacute;nea</h2>
            <?php
            $pruebaID=18;
            $pb=new PublicWeb($pruebaID);
            $ptree=$pb->publicweb_deploy();
            echon("<h2>{$ptree['Prueba']['Nombre']}</h2>");
            echon('<dl class="menu_enum">');
            foreach ($ptree['Jornadas'] as $jornada) {
                if ($jornada['Nombre']==='-- Sin asignar --') continue;
                echon( "<dt>{$jornada['Nombre']}</dt>");
                echon("<dd>");
                    echon("<ol>");
                        echon('<li><a href="javascript:loadTimeTable('.$pruebaID.','.$jornada['ID'].')">o</a> '._("Timetable")."</li>");
                        echon('<li><a href="javascript:loadInscriptions('.$pruebaID.','.$jornada['ID'].')">o</a> '._("Inscriptions")."</li>");
                        echon('<li>'._("Starting order"));
                            echon('<ul style="list-style:none">');
                            foreach ($jornada['Tandas'] as $tanda ){
                                if ($tanda['TipoManga']==0) continue; // skip user defined tandas
                                echon ('<li><a href="javascript:loadTimeTable('.$pruebaID.','.$jornada['ID'].','.$tanda['ID'].')">o</a> '.$tanda['Nombre']."</li>");
                            }
                            echon("</ul>");
                        echon("</li>");
                        echon("<li>"._("Partial scores"));
                            echon('<ul style="list-style:none">');
                            foreach ($jornada['Mangas'] as $manga ){
                                echon ('<li><a href="javascript:loadPartialScores('.$pruebaID .','.$jornada['ID'].','.$manga['ID'].')">o</a> '.$manga['Nombre']."</li>");
                            }
                            echon("</ul>");
                        echon("</li>");
                        echon("<li>"._("Final scores"));
                            echon('<ul style="list-style:none">');
                            foreach ($jornada['Series'] as $serie ){
                                echon ('<li><a href="javascript:loadFinalScores('.$pruebaID .','.$jornada['ID'].','.$serie['ID'].')">o</a> '.$serie['Nombre']."</li>");
                            }
                            echon("</ul>");
                        echon("</li>");
                    echon("</ol>");
                echon("</dd>");
            }
            echon('</dl>');
            ?>
        </div>
    </div>

    <div id="data_panel" data-options="title:'Data',region:'center'">
    </div>

</body>
</html>