<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 21/10/15
 * Time: 14:11
 */
require_once ("../modules/Federations.php");
$feds=Federations::getFederationList();
foreach($feds as $fed) {
    echo "ID:{$fed['ID']} : {$fed['LongName']}\n";
    // print_r($fed);
}
?>