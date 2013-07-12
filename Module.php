<?php

/**
 * Geoip module
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip;

use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface;

class Module implements ConsoleUsageProviderInterface
{
    const CONSOLE_GEOIP_DOWNLOAD = 'geoip download';

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConsoleUsage(AdapterInterface $console)
    {
        return array(
            self::CONSOLE_GEOIP_DOWNLOAD => 'Downloads current libs',
        );
    }
}
