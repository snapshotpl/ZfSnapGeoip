<?php

namespace ZfSnapGeoip;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client;

class HttpClientFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $adapter Zend\Http\Client\Adapter\AdapterInterface */
        $adapter = $serviceLocator->get('ZfSnapGeoip\HttpClient\Adapter');

        $config = $serviceLocator->get('config');
        $options = $config['maxmind']['http_client']['options'];

        $client = new Client();
        $client->setAdapter($adapter);
        $client->setOptions($options);

        return $client;
    }

}
