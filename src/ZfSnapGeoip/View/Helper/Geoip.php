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
    private $sl;

    /**
     * @param string $ip
     * @return \ZfSnapGeoip\Service\Geoip
     */
    public function __invoke($ip = null)
    {
        return $this->getGeoip()->getRecord($ip);
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
            $this->geoip = $this->sl->getServiceLocator()->get('Geoip');
        }
        return $this->geoip;
    }

    public function getServiceLocator()
    {
        return $this->sl;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->sl = $serviceLocator;
    }
}
