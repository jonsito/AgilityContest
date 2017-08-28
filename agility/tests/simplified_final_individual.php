<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 21/10/15
 * Time: 14:11
 */
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
require_once(__DIR__ . "/../server/auth/AuthManager.php");
if(!isset($config)) $config =Config::getInstance();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php _e("Final individual");?>(<?php _e("simplified");?>)</title>
    <link rel="stylesheet" type="text/css" href="/agility/css/videowall_css.php" />
    <script src="/agility/lib/jquery-2.2.4.min.js" type="text/javascript" charset="utf-8" > </script>
</head>
<body style="padding:0;margin:0;height:100%;min-height:100%">
<table class="simple_table">
    <tr>
        <th class="simple_header" colspan="5" rowspan="3">
            <img src="/agility/images/agilityawc2016.png" alt="agility awc 2016" width="100%" height="300%"/>
        </th>
        <th class="simple_header">&nbsp;</th>
        <th class="simple_header" colspan="7" >AGILTY SMALL INDIVIDUAL</th>
        <td class="simple_header" colspan="3" style="text-align:right;font-size:1.3vw;font-style:italic"> 189m/40s &nbsp;</td>
    </tr>
    <tr> <!-- cabeceras de la tabla-->
        <th class="simple_header">&nbsp;</th>

        <th class="simple_tableheader lborder tborder rborder" colspan="4">Competitor Info</th>
        <th class="simple_tableheader tborder rborder" colspan="3">Round data</th>
        <th class="simple_tableheader tborder rborder" colspan="3">Final data</th>
    </tr>
    <tr>
        <th class="simple_header">&nbsp;</th>

        <td class="simple_results_odd lborder">Spain</td>
        <td class="simple_results_odd" style="text-align:center;">126</td>
        <td class="simple_results_odd">Narnia</td>
        <td class="simple_results_odd rborder">Carmen Briceño</td>
        <td class="simple_results_odd">35.24</td>
        <td class="simple_results_odd">0.00</td>
        <td class="simple_results_odd rborder">1</td>
        <td class="simple_results_odd">65.31</td>
        <td class="simple_results_odd">0.00</td>
        <td class="simple_results_odd rborder">1</td>
    </tr>
    <tr> <!-- segunda posicion -->
        <th colspan="5" class="simple_tableheader lborder tborder rborder">NEXT</th>

        <th class="simple_header">&nbsp;</th>

        <td class="simple_results_even lborder">Germany</td>
        <td class="simple_results_even" style="text-align:center;">32</td>
        <td class="simple_results_even">Peanut</td>
        <td class="simple_results_even rborder">Tobias Wust</td>
        <td class="simple_results_even">36.23</td>
        <td class="simple_results_even">0.00</td>
        <td class="simple_results_even rborder">3</td>
        <td class="simple_results_even">65.35</td>
        <td class="simple_results_even">0.00</td>
        <td class="simple_results_even rborder">2</td>
    </tr>
    <tr>
        <!-- octavo perro en entrar -->
        <td class="simple_call_odd lborder">77</td>
        <td class="simple_call_odd">Austria</td>
        <td class="simple_call_odd" style="text-align:center;">38</td>
        <td class="simple_call_odd">Heisse</td>
        <td class="simple_call_odd rborder">Vlad Criprian Stefanut</td>

        <th class="simple_header">&nbsp;</th>

        <!-- tercera posicion -->
        <td class="simple_results_odd lborder">&nbsp;</td>
        <td class="simple_results_odd" style="text-align:center;">381</td>
        <td class="simple_results_odd">Ammy</td>
        <td class="simple_results_odd rborder">Roland Kolenko</td>
        <td class="simple_results_odd">36.40</td>
        <td class="simple_results_odd">0.00</td>
        <td class="simple_results_odd rborder">4</td>
        <td class="simple_results_odd">65.97</td>
        <td class="simple_results_odd">0.00</td>
        <td class="simple_results_odd rborder">3</td>
    </tr>
    <tr>
        <!-- septimo perro en entrar -->
        <td class="simple_call_even lborder">76</td>
        <td class="simple_call_even">Spain</td>
        <td class="simple_call_even" style="text-align:center;">120</td>
        <td class="simple_call_even">Melendi</td>
        <td class="simple_call_even rborder">Luis Luque</td>

        <th class="simple_header">&nbsp;</th>

        <!-- cuarta posicion -->
        <td class="simple_results_even lborder">Turkey</td>
        <td class="simple_results_even" style="text-align:center;">56</td>
        <td class="simple_results_even">Lali</td>
        <td class="simple_results_even rborder">Samir Abu Laila</td>
        <td class="simple_results_even">38.62</td>
        <td class="simple_results_even">0.00</td>
        <td class="simple_results_even rborder">10</td>
        <td class="simple_results_even">70.52</td>
        <td class="simple_results_even">0.00</td>
        <td class="simple_results_even rborder">4</td>
    </tr>
    <tr>
        <!-- sexto perro en entrar -->
        <td class="simple_call_odd lborder">75</td>
        <td class="simple_call_odd">Austria</td>
        <td class="simple_call_odd" style="text-align:center;">38</td>
        <td class="simple_call_odd">Heisse</td>
        <td class="simple_call_odd rborder">Vlad Criprian Stefanut</td>

        <th class="simple_header">&nbsp;</th>

        <!-- quinta posicion -->
        <td class="simple_results_odd lborder">&nbsp;</td>
        <td class="simple_results_odd" style="text-align:center;">381</td>
        <td class="simple_results_odd">Ammy</td>
        <td class="simple_results_odd rborder">Roland Kolenko</td>
        <td class="simple_results_odd">36.40</td>
        <td class="simple_results_odd">0.00</td>
        <td class="simple_results_odd rborder">4</td>
        <td class="simple_results_odd">65.97</td>
        <td class="simple_results_odd">0.00</td>
        <td class="simple_results_odd rborder">5</td>
    </tr>
    <tr>
        <!-- quinto perro en entrar -->
        <td class="simple_call_even lborder">74</td>
        <td class="simple_call_even">Spain</td>
        <td class="simple_call_even" style="text-align:center;">120</td>
        <td class="simple_call_even">Melendi</td>
        <td class="simple_call_even rborder">Luis Luque</td>

        <th class="simple_header">&nbsp;</th>

        <!-- sexta posicion -->
        <td class="simple_results_even lborder">Turkey</td>
        <td class="simple_results_even" style="text-align:center;">56</td>
        <td class="simple_results_even">Lali</td>
        <td class="simple_results_even rborder">Samir Abu Laila</td>
        <td class="simple_results_even">38.62</td>
        <td class="simple_results_even">0.00</td>
        <td class="simple_results_even rborder">10</td>
        <td class="simple_results_even">70.52</td>
        <td class="simple_results_even">0.00</td>
        <td class="simple_results_even rborder">6</td>
    </tr>
    <tr>
        <!-- cuarto perro en entrar -->
        <td class="simple_call_odd lborder">73</td>
        <td class="simple_call_odd">Austria</td>
        <td class="simple_call_odd" style="text-align:center;">38</td>
        <td class="simple_call_odd">Heisse</td>
        <td class="simple_call_odd rborder">Vlad Criprian Stefanut</td>

        <th class="simple_header">&nbsp;</th>

        <!-- septima posicion -->
        <td class="simple_results_odd lborder">&nbsp;</td>
        <td class="simple_results_odd" style="text-align:center;">381</td>
        <td class="simple_results_odd">Ammy</td>
        <td class="simple_results_odd rborder">Roland Kolenko</td>
        <td class="simple_results_odd">36.40</td>
        <td class="simple_results_odd">0.00</td>
        <td class="simple_results_odd rborder">4</td>
        <td class="simple_results_odd">65.97</td>
        <td class="simple_results_odd">0.00</td>
        <td class="simple_results_odd rborder">7</td>
    </tr>
    <tr>
        <!-- tercer perro en entrar -->
        <td class="simple_call_even lborder">72</td>
        <td class="simple_call_even">Spain</td>
        <td class="simple_call_even" style="text-align:center;">120</td>
        <td class="simple_call_even">Melendi</td>
        <td class="simple_call_even rborder">Luis Luque</td>

        <th class="simple_header">&nbsp;</th>

        <!-- octava posicion -->
        <td class="simple_results_even lborder">Turkey</td>
        <td class="simple_results_even" style="text-align:center;">56</td>
        <td class="simple_results_even">Lali</td>
        <td class="simple_results_even rborder">Samir Abu Laila</td>
        <td class="simple_results_even">38.62</td>
        <td class="simple_results_even">0.00</td>
        <td class="simple_results_even rborder">10</td>
        <td class="simple_results_even">70.52</td>
        <td class="simple_results_even">0.00</td>
        <td class="simple_results_even rborder">8</td>
    </tr>
    <tr>
        <!-- segundo perro en entrar -->
        <td class="simple_call_odd lborder">71</td>
        <td class="simple_call_odd">Austria</td>
        <td class="simple_call_odd" style="text-align:center;">38</td>
        <td class="simple_call_odd">Heisse</td>
        <td class="simple_call_odd rborder">Vlad Criprian Stefanut</td>

        <th class="simple_header">&nbsp;</th>

        <!-- novena posicion -->
        <td class="simple_results_odd lborder">&nbsp;</td>
        <td class="simple_results_odd" style="text-align:center;">381</td>
        <td class="simple_results_odd">Ammy</td>
        <td class="simple_results_odd rborder">Roland Kolenko</td>
        <td class="simple_results_odd">36.40</td>
        <td class="simple_results_odd">0.00</td>
        <td class="simple_results_odd rborder">4</td>
        <td class="simple_results_odd">65.97</td>
        <td class="simple_results_odd">0.00</td>
        <td class="simple_results_odd rborder">9</td>
    </tr>
    <tr>
        <!-- proximo perro en entrar -->
        <td class="simple_call_even lborder">70</td>
        <td class="simple_call_even">France</td>
        <td class="simple_call_even" style="text-align:center;">41</td>
        <td class="simple_call_even">Hidwine</td>
        <td class="simple_call_even rborder">Aline Tricot</td>

        <th class="simple_header">&nbsp;</th>

        <!-- decima posicion -->
        <td class="simple_results_even lborder">&nbsp;</td>
        <td class="simple_results_even" style="text-align:center;">8</td>
        <td class="simple_results_even">Brahma</td>
        <td class="simple_results_even rborder">Manes Nicole</td>
        <td class="simple_results_even">40.22</td>
        <td class="simple_results_even">5.22</td>
        <td class="simple_results_even rborder">23</td>
        <td class="simple_results_even">73.97</td>
        <td class="simple_results_even">5.22</td>
        <td class="simple_results_even rborder">10</td>
    </tr>
    <tr style="outline:2px solid <?php echo $config->getEnv('vws_linecolor')?>;">
        <td colspan="16">
            <form id="vws_current">
            <table class="simple_table">
                <tr class="simple_current">
                    <td>
                        <input type="text" name="Orden" readonly="readonly" size="3" value="69"/>
                    </td>
                    <td>
                        <input type="hidden" name="Logo" value="Spain"/>
                        <img src="/agility/images/logos/getLogo.php?Federation=1&Logo=ES.png" alt="Spain" style="width:6vw;"/>
                    </td>
                    <td style="text-align:center;">
                        <input type="text" name="Dorsal" readonly="readonly" size="3" value="127"/>
                    </td>
                    <td colspan="2" style="text-align:center;">
                        <input type="hidden" name="Perro" value="Spain"/>
                        <input type="text" name="Nombre" readonly="readonly" size="12" value="Magia"/>
                    </td>
                    <td colspan="5" style="text-align:center;">
                        <input type="hidden" name="Guia" value="1234"/>
                        <input type="text" name="NombreGuia" readonly="readonly" size="30" value="Carmen Brice&ntilde;o"/>
                    </td>
                    <td style="text-align:right">F:&nbsp;</td>
                    <td  style="text-align:left">
                        <input type="hidden" name="Faltas" value="0"/>
                        <input type="hidden" name="Tocados" value="0"/>
                        <input type="text" name="FaltasTocados" readonly="readonly" size="1" value="0"/>
                    </td>
                    <td style="text-align:right">R:&nbsp;</td>
                    <td style="text-align:left">
                        <input type="text" name="Rehuses" readonly="readonly" size="1" value="0"/>
                        <input type="hidden" name="Eliminado" value="0"/>
                        <input type="hidden" name="NoPresentado" value="0"/>
                        <input type="hidden" name="Pendiente" value="0"/>
                    </td>
                    <td style="text-align:right">Time:&nbsp;</td>
                    <td style="text-align:left">
                        <input type="text" name="Tiempo" readonly="readonly" size="6" value="38.45"/>
                        <input type="hidden" name="TIntermedio" value="0"/>
                    </td>
                    <td style="text-align:right">P:&nbsp;</td>
                    <td style="text-align:left">
                        <input type="text" name="Penalizacion" readonly="readonly" size="6" value="0"/>
                    </td>
                </tr>
            </table>
            </form>
        </td>
    </tr>
    <tr>
        <th colspan="5" rowspan="2" class="simple_header">
            <?php include_once(__DIR__ . "/../videowall/vws_footer.php");?>
        </th>

        <td class="simple_results_odd lborder">68</td>
        <!-- perro que acaba de salir -->
        <td class="simple_results_odd">Austria</td>
        <td class="simple_results_odd" style="text-align:center;">43</td>
        <td class="simple_results_odd">Louwie</td>
        <td class="simple_results_odd rborder">Ronald Vlemincx</td>
        <td class="simple_results_odd">41.07</td>
        <td class="simple_results_odd">1.07</td>
        <td class="simple_results_odd rborder">12</td>
        <td class="simple_results_odd">75.59</td>
        <td class="simple_results_odd">1.59</td>
        <td class="simple_results_odd rborder">6</td>
    </tr>
    <tr>
        <td class="simple_results_even lborder">67</td>
        <!-- penultimo perro que acaba de salir -->
        <td class="simple_results_even">Spain</td>
        <td class="simple_results_even" style="text-align:center;">43</td>
        <td class="simple_results_even">Xira</td>
        <td class="simple_results_even rborder">Sergio Ruiz Jim&eacute;nez</td>
        <td class="simple_results_even">43.52</td>
        <td class="simple_results_even">14.52</td>
        <td class="simple_results_even rborder">57</td>
        <td class="simple_results_even">43.52</td>
        <td class="simple_results_even">114.52</td>
        <td class="simple_results_even rborder">67</td>
    </tr>
</table>
</body>
</html>