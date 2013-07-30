<?php

/**
 * Console Controller
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip\Controller;

use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController
{
    /**
     * Download GeoIP data via console
     *
     * @return type
     */
    public function downloadAction()
    {
        if (!($this->getRequest() instanceof ConsoleRequest)){
            throw new \RuntimeException('You can use this action only from a console!');
        }

        $sm = $this->getServiceLocator();
        $console = $sm->get('Console');
        $config = $sm->get('Config');
        $databaseConfig = $config['maxmind']['database'];
        $source = $databaseConfig['source'];
        $destination = $databaseConfig['destination'];
        $gzFilename = basename($source);
        $datFilename = $databaseConfig['filename'];

        /* @var $console Zend\Console\Adapter\AdapterInterface */
        $console->writeLine(sprintf('Downloading %s...', $source));

        if (!copy($source, $destination . $gzFilename)) {
            $console->writeLine('Error during file download occured');
            return;
        }

        $console->writeLine('Download completed');
        $console->writeLine('Unzip the downloading data...');
        system(sprintf('gunzip -f %s', $destination . $gzFilename));
        $console->writeLine(sprintf('Unzip completed (%s)', $destination . $datFilename));
    }
}
