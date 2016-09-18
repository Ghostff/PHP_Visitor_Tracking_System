PHP Visitors Tracker
----------
99% accurate visitors tracking system. includes tracking of visitors IP, browser, OS, country, region, town etc

#USAGE
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
#Using Scope Resolution Operator 
```php
Stalk::ip(); //outputs clients IP address
```
```php
Stalk::browser()->name(); //outputs clients Browser name
Stalk::browser()->version(); //outputs clients Browser version
Stalk::browser()->OS(); //outputs clients Operating system
//--OR
Stalk::browser('name'); //outputs clients Browser name
Stalk::browser('version'); //outputs clients Browser version
Stalk::browser('OS'); //outputs clients Operating system
```
```php

Stalk::country_code(); //outputs clients country code
Stalk::country_code3(); //outputs clients full country code
Stalk::country_name(); //outputs clients country name
Stalk::region(); //outputs clients region
Stalk::state(); //outputs clients full region
Stalk::city(); //outputs clients city
Stalk::postal_code(); //outputs clients postal code
Stalk::latitude(); //outputs clients latitude
Stalk::longitude(); //outputs clients longitude
Stalk::area_code(); //outputs clients area code
Stalk::dma_code(); //outputs clients dma code
Stalk::metro_code(); //outputs clients metro code
Stalk::continent_code(); //outputs continent code
```
#Get all Stalk Property
```php
$stalk->get(); //return object of stalk properties 
```
-- OR
```php
Stalk::all(); //return object of stalk properties 
```
