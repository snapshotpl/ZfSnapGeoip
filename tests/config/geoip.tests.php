<?php
return array(
    'maxmind' => array(
        'database' => array(
            'destination' => __DIR__ . '/../../data/',
            'regionvars' => __DIR__ .'/../../vendor/geoip/geoip/src/geoipregionvars.php',
        ),
    ),
);