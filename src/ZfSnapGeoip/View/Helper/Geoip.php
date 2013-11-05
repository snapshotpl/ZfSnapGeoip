<?php

/**
 * Geoip view helper
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Geoip extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var ZfSnapGeoip\Service\Geoip
     */
    private $geoip;

    /**
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * @param string $ipAddress
     * @return \ZfSnapGeoip\Service\Geoip
     */
    public function __invoke($ipAddress = null)
    {
        return $this->getGeoip()->getRecord($ipAddress);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getGeoip()->getRecord();
    }

    private function getGeoip()
    {
        if (!$this->geoip) {
            $this->geoip = $this->serviceLocator->getServiceLocator()->get('Geoip');
        }
        return $this->geoip;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}
