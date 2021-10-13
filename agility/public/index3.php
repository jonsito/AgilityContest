<?php
header("Access-Control-Allow-Origin: https://{$_SERVER['SERVER_NAME']}",false);
/*
 index.php

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

require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
require_once(__DIR__ . "/../server/auth/AuthManager.php");
define("SYSTEM_INI",__DIR__ . "/../../config/system.ini");
if (!file_exists(SYSTEM_INI)) {
    die("Missing system configuration file: ".SYSTEM_INI." . Please properly configure and install application");
}
if(!isset($config)) $config =Config::getInstance();

/* check for properly installed xampp */
if( ! function_exists('openssl_get_publickey')) {
    die("Invalid configuration: please uncomment line 'module=php_openssl.dll' in file '\\xampp\\php\\php.ini'");
}
$am=AuthManager::getInstance("Public");
if (!$am->allowed(ENABLE_PUBLIC)) {
    die("Current license has no permissions to handle public (web) access related functions");
}
require_once(__DIR__ . "/../server/web/PublicWeb.php");

$pruebaID=http_request("Prueba","i",18);
$pb=new PublicWeb($pruebaID);
$ptree=$pb->publicweb_deploy();
$poster=$ptree['Prueba']['Cartel'];
if (($poster==null) || ($poster=="")) $poster="../default_poster.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $ptree['Prueba']['Nombre'] . " - " . _("Web access"); ?> </title>
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

    <link rel="stylesheet" type="text/css" href="../lib/jquery-easyui-1.4.2/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
    <link rel="stylesheet" type="text/css" href="../lib/jquery-easyui-1.4.2/themes/icon.css" />
    <link rel="stylesheet" type="text/css" href="../css/style.css" />
    <link rel="stylesheet" type="text/css" href="../css/switchbutton.css" />
    <link rel="stylesheet" type="text/css" href="../css/datagrid.css" />
    <link rel="stylesheet" type="text/css" href="../css/videowall_css.php" />
    <link rel="stylesheet" type="text/css" href="../css/public_css.php" />
    
    <script src="../lib/jquery-2.2.4.min.js" type="text/javascript" charset="utf-8" > </script>
    <script src="../lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" ></script>
    <script src="../lib/jquery-easyui-1.4.2/extensions/datagrid-dnd/datagrid-dnd.js" type="text/javascript" charset="utf-8" > </script>
    <script src="../lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-detailview.js" type="text/javascript" charset="utf-8" > </script>
    <script src="../lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-scrollview.js" type="text/javascript" charset="utf-8" > </script>
    <script src="../lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
    <script src="../lib/sprintf.js" type="text/javascript" charset="utf-8" > </script>
    <script src="../scripts/easyui-patches.js" type="text/javascript" charset="utf-8" > </script>
    <script src="../scripts/datagrid_formatters.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="../scripts/common.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="../scripts/auth.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="../scripts/competicion.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="../scripts/results_and_scores.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="../scripts/ligas.js.php" type="text/javascript" charset="utf-8" > </script>
    <script src="../public/public.js.php" type="text/javascript" charset="utf-8" > </script>

    <script type="text/javascript" charset="utf-8">

        /* make sure configuration is loaded from server before onLoad() event */
        var pb_config = {
            'Timeout':null,
            'LastEvent':0,
            'ConsoleMessages':'',
            'SelectedDorsal': 0,
            // null-true-false to use (when available) Notification API
            'Notifications': null,
            // required for notifications as working data is not yet set
            'PruebaID': <?php echo $pruebaID ;?>
        };
        loadConfiguration();
        getLicenseInfo();
        getFederationInfo();

        /* not really needed for public access, but stay here for compatibility */
        function initialize() {
            // make sure that every ajax call provides sessionKey
            $.ajaxSetup({
                beforeSend: function(jqXHR,settings) {
                    if ( typeof(ac_authInfo.SessionKey)!=="undefined" && ac_authInfo.SessionKey!==null) {
                        jqXHR.setRequestHeader('X-Ac-Sessionkey',ac_authInfo.SessionKey);
                    }
                    return true;
                }
            });
        }

        function myRowStyler(idx,row) { return pbRowStyler(idx,row); }
        function myRowStyler2(idx,row) { return pbRowStyler2(idx,row); }

        /**
         * Abre el panel derecho, cierra el menu, si flag, cierra tambien baner inicial
         * @param {boolean} flag ask for closing also left banner
         */
        function pbmenu_collapseMenu(flag) {
            pb_config.Timeout="readyToRun";
            var p=$('#pb_layout');
            if (flag) {
                p.layout('panel','west').panel('options').width='1%';
                p.layout('collapse','west');
            }
            p.layout('panel','east').panel('options').width='98%';
            p.layout('expand','east');
            $('#pb_back-link').css('display','inherit');
        }

        /**
         * Abre el menu, cierra la vista, apaga temporizadores
         * Si flag, cierra tambien panel de banner
         * @param {boolean} flag
         */
        function pbmenu_expandMenu(flag) {
            var p=$('#pb_layout');
            if (flag) {
                p.layout('panel','west').panel('options').width='1%';
                p.layout('collapse','west');
            }
            p.layout('panel','east').panel('options').width='60%';
            p.layout('expand','east');
            $('#pb_back-link').css('display','none');
            if (pb_config.Timeout !== null ) {
                clearTimeout(pb_config.Timeout);
                pb_config.Timeout=null;
            }
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
            pbmenu_collapseMenu(true);
            var page="../public/pbmenu_inscripciones.php";
            if (isJornadaEqMejores() ) page="../public/pbmenu_inscripciones_equipos.php";
            if (isJornadaEqConjunta() ) page="../public/pbmenu_inscripciones_equipos.php";
            $('#pb_layout').layout('panel','east').panel('refresh',page);
        }

        function pbmenu_loadTrainingSession(prueba) {
            var p=<?php echo json_encode($ptree['Prueba']); ?>;
            setPrueba(p);
            pbmenu_collapseMenu(true);
            $('#pb_layout').layout('panel','east').panel('refresh',"../public/pbmenu_entrenamientos.php");
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
            pbmenu_collapseMenu(true);
            $('#pb_layout').layout('panel','east').panel('refresh',"../public/pbmenu_programa.php");
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
            pbmenu_collapseMenu(true);
            $('#pb_layout').layout('panel','east').panel('refresh',"../public/pbmenu_ordensalida.php");
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
            pbmenu_collapseMenu(true);
            var page="../public/pbmenu_parciales.php";
            if (isJornadaEquipos(null) ) page="../public/pbmenu_parciales_equipos.php";
            $('#pb_layout').layout('panel','east').panel('refresh',page);
        }

        function pbmenu_loadFinalScores(prueba,jornada,serie) {
            pbmenu_getAndSet(prueba,jornada);
            workingData.datosRonda=workingData.datosJornada.Series[serie];
            pbmenu_collapseMenu(true);
            var page="../public/pbmenu_finales.php";
            if (isJornadaEquipos(null) ) page="../public/pbmenu_finales_equipos.php";
            $('#pb_layout').layout('panel','east').panel('refresh',page);
        }

    </script>

    <style>
        html, body {
            margin:0;
            padding:0;
            height: 100%;
        }
        #poster_panel {
            background: <?php echo $config->getEnv('pb_hdrbg1');?> url("<?php echo $poster;?>") no-repeat bottom left;
            background-size: 100% 100%;
            width: 100%;
            height: auto;
            min-height:100%;
        }
        #menu_panel {
            /* background should be extracted from contest poster information */
            background: <?php echo $config->getEnv('pb_hdrbg1');?> url("../background.jpg") no-repeat bottom left;
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
</head>
<body id="body">

<div id="pb_layout">

<div id="poster_panel" data-options="region:'west',split:false" style="width:40%">
    <!-- render here contest poster image as declared in database -->
</div>

<div id="menu_panel" data-options="region:'center'">
        <h1><?php _e("Online information"); ?></h1>
            <?php
            echon("<h2>{$ptree['Prueba']['Nombre']}</h2>");
            echon('<dl class="menu_enum">');

            // evaluamos datos de la sesion actual si el tablet esta activo
            if (array_key_exists('Current',$ptree)) {
                $p=$ptree['Current']->Pru;
                $j=$ptree['Current']->Jor;
                $mng=$ptree['Current']->Mng;
                $t=$ptree['Current']->Tnd;
                foreach($ptree['Jornadas'] as $jornada) {
                    foreach ($jornada['Tandas'] as $tanda) {
                        if ( ($tanda['Manga']==$mng) && ($tanda['ID']==$t) ) {
                            // ok. ahora hay que adivinar el mode.
                            // PENDING: not sure about multicat modes. need to revise
                            $mode=-1;
                            switch($tanda['Categoria']){
                                case 'L':   $mode=0; break;
                                case 'M':   $mode=1; break;
                                case 'S':   $mode=2; break;
                                case 'MS':  $mode=3; break;
                                case 'LMS': $mode=4; break;
                                case 'T':   $mode=5; break;
                                case 'LM':  $mode=6; break;
                                case 'ST':  $mode=7; break;
                                case 'LMST':$mode=8; break;
                                case 'X':   $mode=9; break;
                                case 'XL':   $mode=10; break;
                                case 'MST':  $mode=11; break;
                                case 'XLMST':$mode=12; break;
                            }
                            echon('<dt>Live session now: <a class="easyui-linkbutton" href="javascript:pbmenu_loadPartialScores('.$p.','.$j.','.$mng.','.$mode.');">'.$tanda['Nombre'].'</a></dt><dd>&nbsp;</dd>');
                        }
                    }
                }
            }

            // si la licencia permite sesiones de entrenamiento las mostramos
            if ( $am->allowed(ENABLE_TRAINING)) {
                echon( '<dt><a class="easyui-linkbutton" href="javascript:pbmenu_loadTrainingSession('.$pruebaID.');">'._("Training session").'</a></dt><br/>');
            }

            // enumeramos jornadas
            foreach ($ptree['Jornadas'] as $jornada) {
                if ($jornada['Nombre']==='-- Sin asignar --') continue;
                if (count($jornada['Mangas'])==0) continue; // no rounds, no print
                $final_equipos=($jornada['Nombre']==="Final Equipos")?true:false;
                echon( "<dt>{$jornada['Nombre']}<br/>&nbsp;<br/></dt>");
                echon("<dd>");
                    echon("<ol>");
                        echon('<li><a class="easyui-linkbutton" href="javascript:pbmenu_loadTimeTable('.$pruebaID.','.$jornada['ID'].')">'._("Timetable")."</a><br/>&nbsp;<br/></li>");
                        echon('<li><a class="easyui-linkbutton" href="javascript:pbmenu_loadInscriptions('.$pruebaID.','.$jornada['ID'].')">'._("Inscriptions")."</a><br/>&nbsp;<br/></li>");

                        // orden de salida
                        echon('<li>'._("Starting order").'<br/>');
                            echon('<table style="table-layout:fixed">');
                            $tipo=0;
                            foreach ($jornada['Tandas'] as $tanda ){
                                if ($tanda['TipoManga']==0) continue; // skip user defined tandas
                                // on rsce / rfec final team rounds there is only one round (agility) so remove jumping
                                if ( in_array($tanda['TipoManga'],array(13,14)) && strpos($jornada['Nombre'],"Final")!==FALSE) continue;
                                if ($tanda['TipoManga']!=$tipo) echon( ($tipo==0)? '<tr>' : '</tr><tr>');
                                $tipo=$tanda['TipoManga'];
                                echon ('<td><a class="easyui-linkbutton" href="javascript:pbmenu_loadStartingOrder('.$pruebaID.','.$jornada['ID'].','.$tanda['ID'].')">'.$tanda['Nombre']."</a> </td>");
                            }
                            echon("</tr></table>");
                        echon("<br/>&nbsp;<br/></li>");
                        // skipping single round series may lead in empty partial scores section.
                        // so detect and avoid

                        // firstly enumerate rounds
                        $roundstr="";
                        $mng=0;
                        foreach ($jornada['Mangas'] as $manga ){
                            if ($manga['TipoManga']==16) continue; // special single round: skip show partial scores
                            // first round in single round pre-agility: skip showing partial scores
                            if ( ($manga['TipoManga']==1) && ($jornada['PreAgility']==1) ) continue;
                            // on rsce / rfec final team rounds there is only one round (agility) so remove jumping
                            if ( in_array($manga['TipoManga'],array(13,14)) && strpos($jornada['Nombre'],"Final")!==FALSE) continue;
                            if ($manga['Manga']!=$mng) $roundstr .= ($mng==0)? '<tr>' : '</tr><tr>';
                            $mng=$manga['Manga'];
                            $roundstr .= '<td><a class="easyui-linkbutton" href="javascript:pbmenu_loadPartialScores('.$pruebaID .','.$jornada['ID'].','.$manga['Manga'].','.$manga['Mode'].')">'.$manga['Nombre']."</a> </td>\n";
                        }
                        // on empty rounds count skip partial scores; else display them
                        if ($roundstr!=="") {
                            $str=($final_equipos)?_("Final scores"):_("Partial scores");
                            echon("<li>{$str}<br>"); echon('<table>'); echo $roundstr; echon('</table>'); echon("</li>");
                        }

                        if (!$final_equipos) {
                            echon("<br/>&nbsp;<br/><li>"._("Final scores").'<br/>');
                            echon('<table style="table-layout:fixed"><tr>');
                            $lastmanga1=0;
                            for ($n=0;$n<count($jornada['Series']);$n++) {
                                $serie=$jornada['Series'][$n];
                                if ($serie['Manga1']!=$lastmanga1) {
                                    $lastmanga1=$serie['Manga1'];
                                    echon('</tr><tr>');
                                }
                                echon ('<td><a class="easyui-linkbutton" href="javascript:pbmenu_loadFinalScores('.$pruebaID .','.$jornada['ID'].','.$n.')">'.$serie['Nombre']."</a> </td>");
                            }
                            echon('</tr></table>');
                        }
                        echon("<br/>&nbsp;<br/></li>");
                    echon("</ol>");
                echon("</dd>");
            }
            echon('</dl>');
            ?>
        <h2> <?php _e("Options and Messages");?></h2>
        <dl class="menu_enum">
            <dd>
                <ul style="list-style-type:none">
                    <li>
                        <?php _e('Activate notifications');?>
                        <label class="switch">
                            <input id="pbmenu-Notifications" type="checkbox" class="switch-input"
                                onchange="pbmenu_enableSystemNotifications();" checked=""/>
                            <span class="switch-label" data-on="On" data-off="Off"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </li>
                    <li>
                        <!-- button to display message dialog -->
                        <?php _e('Display received messages since session start');?>
                        <a class="easyui-linkbutton" href="javascript:pbmenu_displayNofifications();"><?php _e("Display");?></a>
                    </li>
                    <li>
                        <!-- textbox to enter dorsal to be highligthed on listings -->
                        <label for="pbmenu-Dorsal"><?php _e("Dorsal to track info "); ?>:</label>
                        <input id="pbmenu-Dorsal" name="Dorsal" type="text"  style="width:50px;" value="0"/>
                    </li>
                    <li>
                        <!-- button to display message dialog -->
                        <?php _e('Personalize notifications');?>
                        <a id="pbmenu-Options" class="easyui-linkbutton" href="javascript:pbmenu_notificationOptions();"><?php _e("Setup");?></a>
                    </li>
                </ul>
            </dd>
        </dl>
    </div>

    <div id="data_panel" data-options="region:'east',split:true,collapsed:true" style="width:20%">
        <!-- to be replaced on mouse click to load proper page -->
        <div id="public-contenido">&nbsp;</div>
    </div>
</div>
<script type="text/javascript">
    $('#pbmenu-Notifications').removeAttr('checked');
    // define the layout structure
    $('#pb_layout').layout({fit:true});
    $('#pb_layout').layout('panel','west').panel({
        // once closed do not allow expand poster window. instead expand menu
        onBeforeExpand: function() { 
            ac_config.allow_scroll=true;
            setTimeout(pbmenu_expandMenu(false),0);
            return false;
        },
        // on collapse disable scrolling (if any)
        onBeforeCollapse: function() { 
            ac_config.allow_scroll=true; 
            return true; 
        }
    });

    $('#pbmenu-Dorsal').numberbox({ required: true, min: 0, max: 9999, precision:0 }).numberbox('setValue',0);
    addTooltip($('#pbmenu-Dorsal').numberbox('textbox'),'<?php _e("Dorsal to track info. Set to zero if no dorsal selected"); ?>');
    addTooltip($('#pbmenu-Options').linkbutton(),'<?php _e("Not available yet"); ?>');

</script>
</body>
</html>