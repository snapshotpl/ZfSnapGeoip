<?php

/**
 * Factory of DatabaseConfig
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DatabaseConfigFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $data   = $config['maxmind']['database'];

        return new DatabaseConfig($data);
    }
}