<?php
    require_once(__DIR__."/../server/auth/Config.php");
    $config = Config::getInstance();
/*
pb_footer.php

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
/* File used to insert logo, supporters,  head lines and so */
?>
<div id="pb_footer">
    <span style="float:left">
        <a id="pb_footer-urlFederation" target="fed" href="" style="border:0 none;">
            <img id="pb_footer-logoFederation" src="/agility/images/logos/rsce.png" alt="" height="50"/>
        </a>
        <a id="pb_footer-urlFederation2" target="fed2" href="">
            <img id="pb_footer-logoFederation2" src="/agility/images/logos/fci.png" alt="" height="50"/>
        </a>
        <span style="display:inline-block;padding:10px;font-size:10px;font-style:oblique">
            Powered by AgilityContest-2.0.1<br/> Copyright &copy; 2013-2015 JAMC
        </span>
    </span>
    <span style="float:right">

<?php
        /* el fichero "supporters,csv" tiene el formato CSV: "patrocinador":"logo":"url" */
        $file=fopen(__DIR__."/../images/supporters/supporters.csv","r");
        if ($file) {
            while (($datos = fgetcsv($file, 0, ':','"')) !== FALSE) {
                $nitems=count($datos);
                if ($nitems!=3) continue;
                echo '<a  target="'.$datos[0].'" href="'.$datos[2].'">';
                echo '<img id="vw_footer-'.$datos[0].'" src="/agility/images/supporters/'.$datos[1].'" alt="'.$datos[0].'" height="50"/>';
                echo '</a>&nbsp;';
            }
            fclose($file); // this also removes temporary file
        }
?>
        <!-- El logo de y URL de la aplicación siempre esta presente :-) -->
        <a target="acontest" href="https://www.github.com/jonsito/AgilityContest">
            <img id="pb_footer-logoAgilityContest" src="/agility/images/supporters/agilitycontest.png" alt="agilitycontest" height="50"/>
        </a>
        <span style="padding:5px">
            <audio controls autoplay>
                <source src="/agility/videos/living_for_agility.ogg" type="audio/ogg">
            </audio>
        </span>
    </span>
</div>