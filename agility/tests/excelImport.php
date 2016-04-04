<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 30/11/15
 * Time: 13:42
 */
require_once __DIR__.'/../server/excel/dog_reader.php';

echo "before class";
$dr=new DogReader(0);
$dr->dropTable();
echo "before validate";
$dr->validateFile("inscriptionlist.xlsx");
echo "after validate";
?>