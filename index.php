<?php


require_once 'src/Stalk.php';


//remove this if not testing on local machine
Stalk::ip('169.159.66.44');


var_dump(Stalk::city() . ' ' . Stalk::country_name());

/*
* same ass
*
* $stalk = new Stalk;
* $stalk = $stalk->get();
* var_dump($stalk->city . ' ' . $stalk->country_name());
*
*/
