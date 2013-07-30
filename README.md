ZfSnapGeoip
===========

Maxmind Geoip module for Zend Framework 2

Version 0.1.0 Created by Witold Wasiczko

Usage
-----
Default ZfSnapGeoip uses current IP address.

**In controller:**

```php
$geoip = $this->getServiceLocator()->get('geoip');
$geoip->getCity();
```

**In view:**

Returns city name for current IP:
```php
<?php echo $this->geoip() ?>
```
Returns country name for given IP:
```php
<?php echo $this->geoip('184.106.35.179')->getCountryName() ?>
```

You can also implements ZfSnapGeoip\IpAwareInterface interface and then use instance in service/helper:
```php
<?php echo $this->geoip($user)->getRegionName() ?>
```
Or
```php
<?php echo $geoip->getRegionName($user) ?>
```

All avaliable methods:
```
getAreaCode()
getCity()
getContinentalCode()
getCountryCode()
getCountryCode3()
getCountryName()
getDmaCode()
getLatitude()
getLongitude()
getMetroCode()
getPostalCode()
getRegionCode()
getRegionName()
```

Get all data to array:
```php
toArray()
```

How to install?
---------------
Via [`composer`](https://getcomposer.org/)
```json
{
    "require": {
        "snapshotpl/zf-snap-geoip": "dev-master"
    }
}
```

To download data file from http://dev.maxmind.com/geoip/legacy/geolite/ use Zend\Console (you can add this to crontab):
```
php index/public.php geoip download
```
Or use autoupdate database during install/update in composer (just add this lines to composer.json and run composer):
```json
{
    "scripts": {
       "post-install-cmd": [
            "ZfSnapGeoip\\Composer\\ScriptHandler::downloadData"
        ],
        "post-update-cmd": [
            "ZfSnapGeoip\\Composer\\ScriptHandler::downloadData"
        ]
    }
}
```

TODO
----

- unit tests
