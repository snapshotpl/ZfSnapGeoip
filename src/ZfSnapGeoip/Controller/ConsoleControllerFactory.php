<?php

/**
 * Factory of ConsoleController
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsoleControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        $console        = $serviceLocator->get('Console');
        $config         = $serviceLocator->get('ZfSnapGeoip\DatabaseConfig');

        return new ConsoleController($console, $config);
    }
}