<?php

namespace ZfSnapGeoip;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory of DatabaseConfig
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class DatabaseConfigFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, DatabaseConfig::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $data = $config['maxmind']['database'];

        return new DatabaseConfig($data);
    }
}
