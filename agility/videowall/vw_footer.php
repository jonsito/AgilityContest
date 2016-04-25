<?php
    require_once(__DIR__."/../server/auth/Config.php");
    $config = Config::getInstance();
/*
vw_footer.php

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
/* File used to insert logo, supporters,  head lines and so */
?>
<div id="vw_footer">
    <span style="float:left">
        <a id="vw_footer-urlFederation" target="fed" href="" style="border:0 none;">
            <img id="vw_footer-logoFederation" src="/agility/images/logos/agilitycontest.png" alt="" width="50"/>
        </a>
        <a id="vw_footer-urlFederation2" target="fed2" href="">
            <img id="vw_footer-logoFederation2" src="/agility/images/logos/agilitycontest.png" alt="" width="50"/>
        </a>
        <span style="display:inline-block;padding:12px;font-size:10px;font-style:oblique">
            Powered by AgilityContest-<?php echo $config->getEnv('version_name'); ?> <br/>Copyright &copy; 2013-2015 JAMC
        </span>
    </span>
    <span style="float:right">

<?php
/* el fichero "supporters,csv" tiene el formato CSV: "patrocinador":"logo":"url"[:"categoria"] */
$file=fopen(__DIR__."/../images/supporters/supporters.csv","r");
if ($file) {
    while (($datos = fgetcsv($file, 0, ':','"')) !== FALSE) {
        $nitems=count($datos);
        if ($nitems<3) continue; // invalid format
        $cat=($nitems==3)?"bronze":strtolower($datos[3]); // "gold","silver","bronze"
        $height=10;
        if ($cat=="gold") $height=50;
        if ($cat=="silver") $height=25;
        echo '<a  target="'.$datos[0].'" href="'.$datos[2].'">';
        echo '<img id="vw_footer-'.$datos[0].'" src="/agility/images/supporters/'.$datos[1].'" alt="'.$cat." ".$datos[0].'" height="'.$height.'"/>';
        echo '</a>&nbsp;';
    }
    fclose($file); // this also removes temporary file
}
?>
        <!-- El logo de y URL de la aplicación siempre esta presente :-) -->
        <a target="acontest" href="https://www.github.com/jonsito/AgilityContest">
            <img id="vw_footer-logoAgilityContest" src="/agility/images/supporters/agilitycontest.png" alt="agilitycontest" height="50"/>
        </a>
    </span>
</div>