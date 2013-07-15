<?php

namespace ZfSnapGeoip\View\Helper;

use ZfSnapGeoip\View\Helper\Geoip;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of GeoipFactory
 *
 * @author witold
 */
class GeoipFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        $geoipService = $sm->get('geoip');
        $helper = new Geoip($geoipService);

        return $helper;
    }
}
