<?php

namespace ZfSnapGeoip\Service;

use Zend\ServiceManager\ServiceManager;

class GeoipFactory
{
    public function __invoke(ServiceManager $serviceManager)
    {
        return new Geoip($serviceManager);
    }
}
