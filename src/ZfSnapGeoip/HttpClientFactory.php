<?php
namespace ZfSnapGeoip;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;

class HttpClientFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $client = new Client();
        $client->setAdapter(new Curl());

        return $client;
    }
}