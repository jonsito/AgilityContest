<?php
    require_once(__DIR__."/../server/auth/Config.php");
    $config = Config::getInstance();
/*
pb_footer.php

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
/* File used to insert logo, supporters,  head lines and so */
?>
<div id="pb_footer">
    <span style="float:left">
        <a id="pb_footer-urlFederation" target="fed" href="" style="border:0 none;text-decoration:none;">
            <img id="pb_footer-logoFederation" src="../images/logos/agilitycontest.png" alt="" class="pb_icon_gold"/>
        </a>
        <a id="pb_footer-urlFederation2" target="fed2" href="" style="text-decoration:none">
            <img id="pb_footer-logoFederation2" src="../images/logos/agilitycontest.png" alt="" class="pb_icon_gold"/>
        </a>
        <span style="display:inline-block;padding:2px;font-size:1.4vw;font-style:oblique">
            AgilityContest-<?php echo $config->getEnv('version_name'); ?><br/> &copy; 2013-2021 JAMC
        </span>
    </span>
    <span style="float:right">

<?php
        /* el fichero "supporters,csv" tiene el formato CSV: "patrocinador":"logo":"url"[:"categoria"] */
        $file=fopen(__DIR__ . "/../images/supporters/supporters.csv","r");
        if ($file) {
            while (($datos = fgetcsv($file, 0, ':','"')) !== FALSE) {
                $nitems=count($datos);
                if ($nitems<3) continue; // invalid format
                if ($datos[1]==="null.png") continue;
                $cat=($nitems==3)?"bronze":strtolower($datos[3]); // "gold","silver","bronze"
                $height=10;
                $class='class="pb_icon_bronze"';
                if ($cat=="gold") $class='class="pb_icon_gold"';
                if ($cat=="silver") $class='class="pb_icon_silver"';
                echo '<a  target="'.$datos[0].'" href="'.$datos[2].'" style="text-decoration:none;">';
                echo '<img id="vw_footer-'.$datos[0].'" src="../images/supporters/'.$datos[1].'" alt="'.$cat." ".$datos[0].'" '.$class.'"/>';
                echo '</a>&nbsp;';
            }
            fclose($file); // this also removes temporary file
        }
?>
        <!-- El logo de y URL de la aplicación siempre esta presente :-) -->
        <a target="acontest" href="https://www.github.com/jonsito/AgilityContest " style="text-decoration:none;">
            <img id="pb_footer-logoAgilityContest" src="../images/supporters/agilitycontest.png" alt="agilitycontest" class="pb_icon_gold"/>
        </a>
        <a target="cubenode" href="http://www.cubenode.com" style="text-decoration:none;">
            <img id="pb_footer-logoAgilityContest" src="../awcfci2016/cubenode2.png" alt="cubenode" class="pb_icon_gold"/>
        </a>
        <span style="padding:5px">
            <!-- remove to avoid copyright issues -->
            <!--
            <audio controls autoplay>
                <source src="../videos/living_for_agility.ogg" type="audio/ogg">
            </audio>
            -->
        </span>
    </span>
</div>