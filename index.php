<?php


require_once 'src/Stalk.php';


if ( ! file_exists('src/lib/GeoLiteCity.dat')) {
    die('
        You need to get a 
        <a href="https://www.maxmind.com">Maxmind</a> GeoLiteCity.dat file <br />
        <a href="http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz">Here</a>
        is a link to a free one.<br />
        <b>After download, extract the GeoLiteCity.dat to src/lib directory</b>
        <p />
        <i>NOTE</i> When runing on local server use (<code> Stalk::$ip </code>)
         to define an ip before every other stalk method is called 
        eg<code> Stalk::$ip = \'73.93.23.234\'; </code>
    '
    );
}
Stalk::$ip = '169.159.66.44';
$stalk = new Stalk;
$stalk = $stalk->get();

echo $stalk->ip; //outputs clients IP address

$stalk->browser->name; //outputs clients Browser name
$stalk->browser->version; //outputs clients Browser version
$stalk->browser->OS; //outputs clients Operating system

$stalk->country_code; //outputs clients country code
$stalk->country_code3; //outputs clients full country code
$stalk->country_name; //outputs clients country name
$stalk->region; //outputs clients region
$stalk->state; //outputs clients full region
$stalk->city; //outputs clients city
$stalk->postal_code; //outputs clients postal code
$stalk->latitude; //outputs clients latitude
$stalk->longitude; //outputs clients longitude
$stalk->area_code; //outputs clients area code
$stalk->dma_code; //outputs clients dma code
$stalk->metro_code; //outputs clients metro code
$stalk->continent_code; //outputs continent code

?>