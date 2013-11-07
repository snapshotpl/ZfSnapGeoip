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
        $console        = $serviceLocator->get('console');
        $config         = $serviceLocator->get('config');
        $databaseConfig = $config['maxmind']['database'];

        return new ConsoleController($console, $databaseConfig);
    }
}