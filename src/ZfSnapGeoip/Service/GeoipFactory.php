<?php

/**
 * Geoip Service Factory
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GeoipFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $request = $serviceLocator->get('Request');

        $serviceConfig = $config['maxmind']['database'];
        $currentIp = $request->getServer('REMOTE_ADDR');

        $service = new Geoip($serviceConfig);
        $service->setIp($currentIp);

        return $service;
    }
}
