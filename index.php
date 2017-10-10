<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'src/Stalk.php';


//remove this if not testing on local machine
$stalk = Stalk::get('50.226.114.86');

var_dump($stalk);

