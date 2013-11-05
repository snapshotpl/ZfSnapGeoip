ZfSnapGeoip [![Build Status](https://travis-ci.org/snapshotpl/ZfSnapGeoip.png?branch=master)](https://travis-ci.org/snapshotpl/ZfSnapGeoip)
===========

Maxmind Geoip module for Zend Framework 2

Version 2.0.2 Created by Witold Wasiczko

Usage
-----
Default ZfSnapGeoip returns Record object created by current user's IP address.

**In controller:**

```php
$record = $this->getServiceLocator()->get('geoip')->getRecord();
echo $record->getCity();
```

```php
$record = $this->getServiceLocator()->get('geoip')->getRecord('216.239.51.99');
echo $record->getLongitude();
echo $record->getLatitude();
```

**By view helper:**

Returns city name for current IP:
```php
<?php echo $this->geoip() ?>
```
Returns country name for given IP:
```php
<?php echo $this->geoip('184.106.35.179')->getCountryName() ?>
```

You can also implements \ZfSnapGeoip\IpAwareInterface interface and then use instance in service/helper:
```php
<?php echo $this->geoip($user)->getRegionName() ?>
```

Avaliable methods via \ZfSnapGeoip\Entity\Interface:
```
getAreaCode()
getCity()
getContinentCode()
getCountryCode()
getCountryCode3()
getCountryName()
getDmaCode()
getLatitude()
getLongitude()
getMetroCode()
getPostalCode()
getRegion()
getRegionName()
setAreaCode($data)
setCity($data)
setContinentCode($data)
setCountryCode($data)
setCountryCode3($data)
setCountryName($data)
setDmaCode($data)
setLatitude($data)
setLongitude($data)
setMetroCode($data)
setPostalCode($data)
setRegion($data)
setRegionName($data)
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
