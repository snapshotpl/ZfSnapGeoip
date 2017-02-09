<?php

return [
    'maxmind' => [
        'database' => [
            'destination' => __DIR__ . '/../../data/',
            'regionvars' => __DIR__ . '/../../vendor/geoip/geoip/src/geoipregionvars.php',
        ],
    ],
];
