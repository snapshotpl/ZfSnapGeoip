<?php

namespace ZfSnapGeoipTest;

use RuntimeException;
use UnexpectedValueException;
use Zend\Console\Console;
use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

class Bootstrap
{
    protected static $serviceManager;

    public static function init()
    {
        // Load the user-defined test configuration file, if it exists; otherwise, load
        if (is_readable(__DIR__ . '/../ApplicationConfig.php')) {
            $testConfig = include __DIR__ . '/../ApplicationConfig.php';
        } else {
            $testConfig = include __DIR__ . '/../ApplicationConfig.php.dist';
        }

        $zf2ModulePaths = array(dirname(dirname(__DIR__)));
        if (($path = static::findParentPath('vendor'))) {
            $zf2ModulePaths[] = $path;
        }
        if (($path = static::findParentPath('module')) !== $zf2ModulePaths[0]) {
            $zf2ModulePaths[] = $path;
        }

        $zf2ModulePaths = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;
        $zf2ModulePaths .= getenv('ZF2_MODULES_TEST_PATHS') ?: (defined('ZF2_MODULES_TEST_PATHS') ? ZF2_MODULES_TEST_PATHS : '');

        static::initAutoloader();

        Console::overrideIsConsole(false);

        // use ModuleManager to load this module and it's dependencies
        $baseConfig = array(
            'module_listener_options' => array(
                'module_paths' => explode(PATH_SEPARATOR, $zf2ModulePaths),
            ),
        );

        $applicationConfig = ArrayUtils::merge($baseConfig, $testConfig);

        $serviceManagerConfig = new ServiceManagerConfig();

        $serviceManager = new ServiceManager();
        $serviceManagerConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', $applicationConfig);
        $serviceManager->get('ModuleManager')->loadModules();

//        $serviceManager = new ServiceManager([
//            'services' => [
//                'ApplicationConfig' => $applicationConfig,
//            ]
//        ]);

        static::$serviceManager = $serviceManager;
    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';
        }

        $zf2Path = getenv('ZF2_PATH') ?: (defined('ZF2_PATH') ? ZF2_PATH : (is_dir($vendorPath . '/ZF2/library') ? $vendorPath . '/ZF2/library' : false));

        if (!$zf2Path) {
            throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
        }

        if (isset($loader)) {
            $loader->add('Zend', $zf2Path . '/Zend');
        } else {
            include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
            AutoloaderFactory::factory(array(
                'Zend\Loader\StandardAutoloader' => array(
                    'autoregister_zf' => true,
                    'namespaces' => array(
                        __NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__,
                    ),
                ),
            ));
        }
    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);

            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }
}

function unzipFile($gzipPath, $destPath)
{
    $gz = gzopen($gzipPath, 'rb');
    if (!$gz) {
        throw new UnexpectedValueException(
        'Could not open gzip file'
        );
    }

    $dest = fopen($destPath, 'wb');
    if (!$dest) {
        gzclose($gz);
        throw new UnexpectedValueException(
        'Could not open destination file'
        );
    }

    stream_copy_to_stream($gz, $dest);

    gzclose($gz);
    fclose($dest);
}

function downloadFile($url, $path)
{
    $newfname = $path;
    $file = fopen($url, 'rb');
    if ($file) {
        $newf = fopen($newfname, 'wb');
        if ($newf) {
            while (!feof($file)) {
                fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
            }
        }
    }
    if ($file) {
        fclose($file);
    }
    if ($newf) {
        fclose($newf);
    }
}
if (!file_exists(__DIR__ . '/../../data/GeoLiteCity.dat')) {
    $gzip = __DIR__ . '/../../data/GeoLiteCity.dat.gz';
    $dat = __DIR__ . '/../../data/GeoLiteCity.dat';
    downloadFile('http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz', $gzip);
    unzipFile($gzip, $dat);
    unlink($gzip);
}

Bootstrap::init();
