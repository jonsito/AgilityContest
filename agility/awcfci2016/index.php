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
require_once(__DIR__. "/../server/web/public.php");

$pruebaID=http_request("Prueba","i",22);
$pb=new PublicWeb($pruebaID);
$ptree=$pb->publicweb_deploy();
$poster="/agility/images/agilityawc2016.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $ptree['Prueba']['Nombre'] . " - " . _("OnLine data"); ?> </title>
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
        loadConfiguration();
        getLicenseInfo();
        getFederationInfo();
        workingData.timeout=null;

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

        function myRowStyler(idx,row) { return pbRowStyler(idx,row); }
        function myRowStyler2(idx,row) { return pbRowStyler2(idx,row); }

        function pb_collapseMenu(flag) {
            var p=$('#pb_layout');
            if (flag) {
                p.layout('panel','west').panel('options').width='1%';
                p.layout('collapse','west');
            }
            p.layout('panel','east').panel('options').width='98%';
            p.layout('expand','east');
            $('#pb_back-link').css('display','inherit');
        }
        function pb_expandMenu(flag) {
            var p=$('#pb_layout');
            if (flag) {
                p.layout('panel','west').panel('options').width='1%';
                p.layout('collapse','west');
            }
            p.layout('panel','east').panel('options').width='60%';
            p.layout('expand','east');
            $('#pb_back-link').css('display','none');
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
            pb_collapseMenu(true);
            var page="/agility/public/pbmenu_inscripciones.php";
            if (isJornadaEqMejores() ) page="/agility/public/pbmenu_inscripciones_equipos.php";
            if (isJornadaEqConjunta() ) page="/agility/public/pbmenu_inscripciones_equipos.php";
            $('#pb_layout').layout('panel','east').panel('refresh',page);
        }

        function pbmenu_loadTrainingSession(prueba) {
            var p=<?php echo json_encode($ptree['Prueba']); ?>;
            setPrueba(p);
            pb_collapseMenu(true);
            $('#pb_layout').layout('panel','east').panel('refresh',"/agility/public/pbmenu_entrenamientos.php");
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
            pb_collapseMenu(true);
            $('#pb_layout').layout('panel','east').panel('refresh',"/agility/public/pbmenu_programa.php");
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
            pb_collapseMenu(true);
            $('#pb_layout').layout('panel','east').panel('refresh',"/agility/public/pbmenu_ordensalida.php");
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
            pb_collapseMenu(true);
            var page="/agility/public/pbmenu_parciales.php";
            if (isJornadaEquipos(null) ) page="/agility/public/pbmenu_parciales_equipos.php";
            $('#pb_layout').layout('panel','east').panel('refresh',page);
        }

        function pbmenu_loadFinalScores(prueba,jornada,serie) {
            pbmenu_getAndSet(prueba,jornada);
            workingData.datosRonda=workingData.datosJornada.Series[serie];
            pb_collapseMenu(true);
            var page="/agility/public/pbmenu_finales.php";
            if (isJornadaEquipos(null) ) page="/agility/public/pbmenu_finales_equipos.php";
            $('#pb_layout').layout('panel','east').panel('refresh',page);
        }

    </script>

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
</head>
<body id="body">

<div id="pb_layout">

<div id="poster_panel" data-options="region:'west',split:false" style="width:40%">
    <a href="www.agilitywc2016.com">
        <img src="/agility/images/agilityawc2016.png" alt="logo_agilityawc2016" style="max-width:90%;padding:20px"/>
    </a>
    <div style="padding:20px;font-weight: bold; font-size:1.2vw;">
        <h2><?php _e('Important notice'); ?>:</h2>
        <p>
            <?php _e("Data shown in these pages is a <em>real time copy</em> of the contest server, and may be modified by Judges and Organization after revision");?>
        </p><p>
            <?php _e("For official scores and results, please look at");?> <a href="http://agilitywc2016.com/competition">AWC-FCI 2016 web</a>
        </p>

    </div>
    <div style="font-size:0.9vw;">
        <table style="padding:20px;width:auto; position:absolute; bottom:0">
            <tr>
                <th>Powered by<br/> AgilityContest 2.3.1</th>
                <th>Hosting courtesy of<br/> CubeNode Systems SL</th>
            </tr>
            <tr>
                <td style="width:50%">
                    <a target="agilitycontest" href="http://www.agilitycontest.es">
                        <img src="/agility/images/AgilityContest.png" style="max-width:50%">
                    </a>
                </td>
                <td style="width:50%">
                    <a target="cubenode" href="http://www.cubenode.com">
                        <img src="/agility/awcfci2016/cubenode.png" style="max-width:100%">
                    </a>
                </td>
            </tr>
        </table>
    </div>
</div>

<div id="menu_panel" data-options="region:'center'">
        <h1><?php echo /*"{$ptree['Prueba']['Nombre']} - " . */_("Online information"); ?></h1>
            <?php
            echon('<dl class="menu_enum">');

            // evaluamos datos de la sesion actual
            $p=$ptree['Current']->Pru;
            $j=$ptree['Current']->Jor;
            $mng=$ptree['Current']->Mng;
            $t=$ptree['Current']->Tnd;
            foreach($ptree['Jornadas'] as $jornada) {
                foreach ($jornada['Tandas'] as $tanda) {
                    if ( ($tanda['Manga']==$mng) && ($tanda['ID']==$t) ) {
                        // ok. ahora hay que adivinar el mode.
                        // como solucion de emergencia, y dado que estamos en el awfci el modo solo puede ser 0,1 o 2
                        $mode=-1;
                        if ($tanda['Categoria']==="L") $mode=0;
                        if ($tanda['Categoria']==="M") $mode=1;
                        if ($tanda['Categoria']==="S") $mode=2;
                        echon('<dt>Live session now: <a class="easyui-linkbutton" href="javascript:pbmenu_loadPartialScores('.$p.','.$j.','.$mng.','.$mode.');">'.$tanda['Nombre'].'</a></dt><dd>&nbsp;</dd>');
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
                echon( "<dt>{$jornada['Nombre']}</dt>");
                echon("<dd>");
                    echon("<ol>");
                        echon('<li><a class="easyui-linkbutton" href="javascript:pbmenu_loadTimeTable('.$pruebaID.','.$jornada['ID'].')">'._("Timetable")."</a> </li>");
                        echon('<li><a class="easyui-linkbutton" href="javascript:pbmenu_loadInscriptions('.$pruebaID.','.$jornada['ID'].')">'._("Inscriptions")."</a> </li>");
                        echon('<li>'._("Starting order").'<br/>');
                            echon('<table>');
                            $tipo=0;
                            foreach ($jornada['Tandas'] as $tanda ){
                                if ($tanda['TipoManga']==0) continue; // skip user defined tandas
                                if ($tanda['TipoManga']!=$tipo) echon( ($tipo==0)? '<tr>' : '</tr><tr>');
                                $tipo=$tanda['TipoManga'];
                                echon ('<td><a class="easyui-linkbutton" href="javascript:pbmenu_loadStartingOrder('.$pruebaID.','.$jornada['ID'].','.$tanda['ID'].')">'.$tanda['Nombre']."</a> </td>");
                            }
                            echon("</tr></table");
                        echon("</li>");
                        // skipping single round series may lead in empty partial scores section.
                        // so detect and avoid

                        // firstly enumerate rounds
                        $roundstr="";
                        $mng=0;
                        foreach ($jornada['Mangas'] as $manga ){
                            // on single round series (special or preagility1) skip partial scores
                            if ($manga['TipoManga']==16) continue; // special single round
                            if ( ($manga['TipoManga']==1) && ($jornada['PreAgility2']==0) ) continue;
                            if ($manga['Manga']!=$mng) $roundstr .= ($mng==0)? '<tr>' : '</tr><tr>';
                            $mng=$manga['Manga'];
                            $roundstr .= '<td><a class="easyui-linkbutton" href="javascript:pbmenu_loadPartialScores('.$pruebaID .','.$jornada['ID'].','.$manga['Manga'].','.$manga['Mode'].')">'.$manga['Nombre']."</a> </td>\n";
                        }
                        // on empty rounds count skip partial scores; else display them
                        if ($roundstr!=="") {
                            echon("<li>"._("Round").'s<br>'); echon('<table>'); echo $roundstr; echon('</table>'); echon("</li>");
                        }

                        echon("<li>"._("Scores").'<br/>');
                            echon('<table><tr>');
                            for ($n=0;$n<count($jornada['Series']);$n++) {
                                $serie=$jornada['Series'][$n];
                                echon ('<td><a class="easyui-linkbutton" href="javascript:pbmenu_loadFinalScores('.$pruebaID .','.$jornada['ID'].','.$n.')">'.$serie['Nombre']."</a> </td>");
                            }
                            echon('</tr></table>');
                        echon("</li>");
                    echon("</ol>");
                echon("</dd>");
            }
            echon('</dl>');
            ?>
    </div>

    <div id="data_panel" data-options="region:'east',split:true,collapsed:true" style="width:20%">
        <!-- to be replaced on mouse click to load proper page -->
        <div id="public-contenido">&nbsp;</div>
    </div>
</div>
<script type="text/javascript">
    // define the layout structure
    $('#pb_layout').layout({fit:true});
    $('#pb_layout').layout('panel','west').panel({
        // once closed do not allow expand poster window. instead expand menu
        onBeforeExpand: function() { 
            ac_config.allow_scroll=true;
            setTimeout(pb_expandMenu(false),0); 
            return false;
        },
        // on collapse disable scrolling (if any)
        onBeforeCollapse: function() { 
            ac_config.allow_scroll=true; 
            return true; 
        }
    });

</script>
</body>
</html>