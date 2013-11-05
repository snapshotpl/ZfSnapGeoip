<?php

return array(
    'maxmind' => array(
        'database' => array(
            'source' => 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz',
            'destination' => __DIR__ . '/../data/',
            'filename' => 'GeoLiteCity.dat',
            'flag' => GEOIP_STANDARD,
            'regionvars' => __DIR__ .'/../../../geoip/geoip/geoipregionvars.php',
        ),
    ),

    'service_manager' => array(
        'invokables' => array(
            'geoip' => 'ZfSnapGeoip\Service\Geoip',
            'geoip_record' => 'ZfSnapGeoip\Entity\Record',
            'geoip_hydrator' => 'Zend\Stdlib\Hydrator\ClassMethods',
        ),
        'shared' => array(
            'geoip_record' => false,
        ),
    ),

    'view_helpers' => array(
        'invokables' => array(
            'geoip' => 'ZfSnapGeoip\View\Helper\Geoip',
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'ZfSnapGeoip\Controller\Console' => 'ZfSnapGeoip\Controller\ConsoleController',
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
                        )
                    )
                )
            )
        )
    ),
);
