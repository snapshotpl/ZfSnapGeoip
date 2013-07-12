<?php

return array(
    'maxmind' => array(
        'database' => array(
            'source' => 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz',
            'destination' => __DIR__ . '/../data/',
            'filename' => 'GeoLiteCity.dat',
            'flag' => GEOIP_STANDARD,
            'regionvars' => __DIR__ .'/../../../vendor/geoip/geoip/geoipregionvars.php'
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'geoip' => function ($sm) {
                $config = $sm->get('config');
                $request = $sm->get('request');
                $service = new ZfSnapGeoip\Service\Geoip($config['maxmind']['database']);
                $service->setIp($request->getServer('REMOTE_ADDR'));

                return $service;
            },
        ),
    ),

    'view_helpers' => array(
        'factories' => array(
            'geoip' => function ($pm) {
                $sm = $pm->getServiceLocator();
                $geoip = $sm->get('geoip');
                $helper = new ZfSnapGeoip\View\Helper\Geoip($geoip);

                return $helper;
            },
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
                            'action' => 'download'
                        )
                    )
                )
            )
        )
    ),
);