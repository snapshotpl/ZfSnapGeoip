<?php

namespace ZfSnapGeoip;

use Zend\Console\Adapter\AdapterInterface;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface as ConsoleUsage;
use Zend\ModuleManager\Feature\ConfigProviderInterface as Config;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface as Autoloader;
use Zend\ModuleManager\Feature\BootstrapListenerInterface as BootstrapListener;

/**
 * Geoip module
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class Module implements ConsoleUsage, Config, Autoloader, BootstrapListener
{
    const CONSOLE_GEOIP_DOWNLOAD = 'geoip download [--no-clobber] [-q]';

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * @return array
     */
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

    /**
     * @param AdapterInterface $console
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return array(
            'Manage GeoIP database',
            self::CONSOLE_GEOIP_DOWNLOAD => 'Downloads the newest GeoIP db',
            array('--no-clobber', 'Don\'t overwrite an existing db file'),
            array('-q', 'Turn off output'),
        );
    }

    /**
     * @param EventInterface $e
     */
    public function onBootstrap(EventInterface $e)
    {
        $serviceManager = $e->getParam('application')->getServiceManager();
        $config = $serviceManager->get('config');

        require_once $config['maxmind']['timezone_function_path'];
    }

}
