<?php

return array(
    'maxmind' => array(
        'database' => array(
            'source' => 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz',
            'destination' => __DIR__ . '/../data/',
            'filename' => 'GeoLiteCity.dat',
            'flag' => GEOIP_STANDARD,
            'regionvars' => __DIR__ . '/../../../geoip/geoip/src/geoipregionvars.php',
        ),
        'http_client' => array(
            'options' => array(
                'timeout' => 300,
            ),
        ),
        'timezone_function_path' => __DIR__ . '/../../../geoip/geoip/src/timezone.php',
    ),
    'service_manager' => array(
        'invokables' => array(
            'geoip_record' => 'ZfSnapGeoip\Entity\Record',
            'geoip_hydrator' => Zend\Stdlib\Hydrator\ClassMethods::class,
            'ZfSnapGeoip\HttpClient\Adapter' => Zend\Http\Client\Adapter\Curl::class,
        ),
        'factories' => array(
            'geoip' => ZfSnapGeoip\Service\GeoipFactory::class,
            'ZfSnapGeoip\DatabaseConfig' => ZfSnapGeoip\DatabaseConfigFactory::class,
            'ZfSnapGeoip\HttpClient' => ZfSnapGeoip\HttpClientFactory::class,
        ),
        'shared' => array(
            'geoip_record' => false,
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            ZfSnapGeoip\View\Helper\Geoip::class => ZfSnapGeoip\View\Helper\GeoipFactory::class,
        ),
        'aliases' => [
            'geoip' => ZfSnapGeoip\View\Helper\Geoip::class,
        ],
    ),
    'controllers' => array(
        'factories' => array(
            'ZfSnapGeoip\Controller\Console' => ZfSnapGeoip\Controller\ConsoleControllerFactory::class,
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'geoip-download' => array(
                    'options' => array(
                        'route' => ZfSnapGeoip\Module::CONSOLE_GEOIP_DOWNLOAD,
                        'defaults' => array(
                            'controller' => 'ZfSnapGeoip\Controller\Console',
                            'action' => 'download',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
