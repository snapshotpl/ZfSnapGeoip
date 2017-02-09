<?php
return [
    'maxmind' => [
        'database' => [
            'source' => 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz',
            'destination' => __DIR__ . '/../data/',
            'filename' => 'GeoLiteCity.dat',
            'flag' => GEOIP_STANDARD,
            'regionvars' => __DIR__ . '/../../../geoip/geoip/src/geoipregionvars.php',
        ],
        'http_client' => [
            'options' => [
                'timeout' => 300,
            ],
        ],
        'timezone_function_path' => __DIR__ . '/../../../geoip/geoip/src/timezone.php',
    ],
    'service_manager' => [
        'invokables' => [
            'geoip_record' => ZfSnapGeoip\Entity\Record::class,
            'geoip_hydrator' => Zend\Stdlib\Hydrator\ClassMethods::class,
            'ZfSnapGeoip\HttpClient\Adapter' => Zend\Http\Client\Adapter\Curl::class,
        ],
        'factories' => [
            'geoip' => ZfSnapGeoip\Service\GeoipFactory::class,
            'ZfSnapGeoip\DatabaseConfig' => ZfSnapGeoip\DatabaseConfigFactory::class,
            'ZfSnapGeoip\HttpClient' => ZfSnapGeoip\HttpClientFactory::class,
        ],
        'shared' => [
            'geoip_record' => false,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            ZfSnapGeoip\View\Helper\Geoip::class => ZfSnapGeoip\View\Helper\GeoipFactory::class,
        ],
        'aliases' => [
            'geoip' => ZfSnapGeoip\View\Helper\Geoip::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            ZfSnapGeoip\Controller\ConsoleController::class => ZfSnapGeoip\Controller\ConsoleControllerFactory::class,
        ],
        'aliases' => [
            'ZfSnapGeoip\Controller\Console',
        ],
    ],
    'console' => [
        'router' => [
            'routes' => [
                'geoip-download' => [
                    'options' => [
                        'route' => ZfSnapGeoip\Module::CONSOLE_GEOIP_DOWNLOAD,
                        'defaults' => ['controller' => 'ZfSnapGeoip\Controller\Console',
                            'action' => 'download',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
