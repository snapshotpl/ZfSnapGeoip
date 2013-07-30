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

        $service = new Geoip($serviceConfig);

        if ($request instanceof \Zend\Http\Request) {
            $currentIp = $request->getServer('REMOTE_ADDR');
            $service->setIp($currentIp);
        }

        return $service;
    }
}
