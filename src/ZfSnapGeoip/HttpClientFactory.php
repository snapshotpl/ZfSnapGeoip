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

        $client = new Client();
        $client->setAdapter($adapter);

        return $client;
    }

}
