PHP Visitors Tracker [Documentation](http://ghostff.com/library/php/Visitors_Tracking_System)
----------
99% accurate visitors tracking system.

# USAGE
```php
$stalk = new Stalk;
$stalk = $stalk->get();

$stalk->ip; //outputs clients IP address

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
```
