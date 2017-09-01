<?php

$rustart = getrusage();

ini_set('display_errors', 'on');
require_once 'vendor/autoload.php';
include 'database.php';


// The Manager helps to order the mails and to find stuff within it
$manager = new \MailManagement\Manager();

$results = $manager->runStatusCheck();

if(!$results){
  die($manager->error);
}

// // Print log
// foreach ($results as $result) {
//   echo "Inserted mail ({$result->ID}) into schedule ({$result->schedule["ID"]}) with error({$result->error})\n";
// }


// Script end
function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
     -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}

$ru = getrusage();
echo "This process used " . rutime($ru, $rustart, "utime") .
    " ms for its computations\n";
echo "It spent " . rutime($ru, $rustart, "stime") .
    " ms in system calls\n";
