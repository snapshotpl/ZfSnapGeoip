<?php

return array(
    'maxmind'         => array(
        'database' => array(
            'source'      => 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz',
            'destination' => __DIR__ . '/../data/',
            'filename'    => 'GeoLiteCity.dat',
            'flag'        => GEOIP_STANDARD,
            'regionvars'  => __DIR__ . '/../../../geoip/geoip/src/geoipregionvars.php',
        ),
        'timezone_function_path' => __DIR__ . '/../../../geoip/geoip/src/timezone.php',
    ),
    'service_manager' => array(
        'invokables' => array(
            'geoip'          => 'ZfSnapGeoip\Service\Geoip',
            'geoip_record'   => 'ZfSnapGeoip\Entity\Record',
            'geoip_hydrator' => 'Zend\Stdlib\Hydrator\ClassMethods',
            'ZfSnapGeoip\HttpClient\Adapter' => 'Zend\Http\Client\Adapter\Curl',
        ),
        'factories'  => array(
            'ZfSnapGeoip\DatabaseConfig' => 'ZfSnapGeoip\DatabaseConfigFactory',
            'ZfSnapGeoip\HttpClient' => 'ZfSnapGeoip\HttpClientFactory',
        ),
        'shared'     => array(
            'geoip_record' => false,
        ),
    ),
    'view_helpers'    => array(
        'invokables' => array(
            'geoip' => 'ZfSnapGeoip\View\Helper\Geoip',
        ),
    ),
    'controllers'     => array(
        'factories' => array(
            'ZfSnapGeoip\Controller\Console' => 'ZfSnapGeoip\Controller\ConsoleControllerFactory',
        ),
    ),
    'console'         => array(
        'router' => array(
            'routes' => array(
                'geoip-download' => array(
                    'options' => array(
                        'route'    => ZfSnapGeoip\Module::CONSOLE_GEOIP_DOWNLOAD,
                        'defaults' => array(
                            'controller' => 'ZfSnapGeoip\Controller\Console',
                            'action'     => 'download',
                        )
                    )
                )
            )
        )
    ),
);
