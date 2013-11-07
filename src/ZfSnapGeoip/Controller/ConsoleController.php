<?php

/**
 * Console Controller
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip\Controller;

use Zend\Console\ColorInterface as Color;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController
{
    /**
     * Download GeoIP data via console
     */
    public function downloadAction()
    {
        if (!($this->getRequest() instanceof ConsoleRequest)) {
            throw new \RuntimeException('You can use this action only from a console!');
        }

        $serviceManager = $this->getServiceLocator();
        $console = $serviceManager->get('Console');
        $config = $serviceManager->get('Config');
        $databaseConfig = $config['maxmind']['database'];
        $source = $databaseConfig['source'];
        $destination = $databaseConfig['destination'];
        $gzFilename = basename($source);
        $datFilename = $databaseConfig['filename'];

        if (!$this->getRequest()->getParam('override') && is_file($destination . $datFilename)) {
            $console->writeline('Database already exist. Skipping...', Color::RED);
            return;
        }

        /* @var $console Zend\Console\Adapter\AdapterInterface */
        $console->writeLine(sprintf('Downloading %s...', $source), Color::YELLOW);

        if (!copy($source, $destination . $gzFilename)) {
            $console->writeLine('Error during file download occured', Color::RED);
            return;
        }

        $console->writeLine('Download completed', Color::GREEN);
        $console->writeLine('Unzip the downloading data...', Color::YELLOW);
        system(sprintf('gunzip -f %s', $destination . $gzFilename));
        $console->writeLine(sprintf('Unzip completed (%s)', $destination . $datFilename), Color::GREEN);
    }
}
