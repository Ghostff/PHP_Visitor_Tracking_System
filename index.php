<?php
require_once('funcs.php');
//TRACK_UNIQ_ONLY track user once each time the visit your site (note) setting to false will continuesly save users info even on reload 
//TRACK_USERS saves all pages a users visits repitedly eg(home, home, demo, demo, demo, account, home)
//INDIV_PAGES (works with TRACK_USERS)  this save as group. eg(home, demo, account) and takes fewer process (* TRACK_USERS must be true for this to work)
new stalk(true, true, true);
//HTML page starts bellow
?>