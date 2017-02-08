<?php

namespace ZfSnapGeoip\Service;

use Interop\Container\ContainerInterface;

final class GeoipFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new Geoip($container);
    }
}
