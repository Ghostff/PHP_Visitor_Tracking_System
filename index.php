<?php



include 'src/Stalk.php';

$stalk = Stalk::ip('99.75.13.122');

var_dump($stalk->city . ' ' . $stalk->country_name);
