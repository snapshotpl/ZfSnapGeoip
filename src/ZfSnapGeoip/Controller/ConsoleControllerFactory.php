<?php

namespace ZfSnapGeoip\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory of ConsoleController
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class ConsoleControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\Mvc\Controller\ControllerManager */
        $sm = $serviceLocator->getServiceLocator();

        $console = $sm->get('Console');
        $config = $sm->get('ZfSnapGeoip\DatabaseConfig');
        $httpClient = $sm->get('ZfSnapGeoip\HttpClient');

        return new ConsoleController($console, $config, $httpClient);
    }

}
