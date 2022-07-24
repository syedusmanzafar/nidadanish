<?php
    error_reporting(E_ALL);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://www.nidadanish.com/upgrades/core_4.11.5-4.12.1/restore_2021-06-24_09-45-59.php");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   
    $html = curl_exec($ch);
    echo $html;
