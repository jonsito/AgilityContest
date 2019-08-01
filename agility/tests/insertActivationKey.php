<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 30/11/15
 * Time: 13:42
 */

// read license file ( 1st argument )
$licenses = file($argv[1],FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!$licenses) die("Cannot open licenses file");

// iterate on each line adding activationkey when required
foreach ( $licenses as $lic) {
    $data=json_decode($lic,true);
    if ($data['activationkey']==="") {
        $akey=substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(20))), 0, 20);
        $akey=chunk_split($akey,4,"-");
        $data['activationkey']=substr($akey,0,strlen($akey)-1);
    }
    echo json_encode($data).PHP_EOL;
}
?>
