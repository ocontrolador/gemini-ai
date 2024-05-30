<?php

// Usar apenas no desenvolvimento

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

function dd(array $array):void 
{
  var_dump($array); 
  die('FIM!');
}

?>
