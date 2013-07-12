<?php

/**
 * Geoip view helper
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZfSnapGeoip\Service\Geoip as Service;

class Geoip extends AbstractHelper
{
    /**
     * @var ZfSnapGeoip\Service\Geoip
     */
    private $geoip;

    /**
     * @param \ZfSnapGeoip\Service\Geoip $geoip
     */
    public function __construct(Service $geoip)
    {
        $this->geoip = $geoip;
    }

    /**
     * @param string $ip
     * @return \ZfSnapGeoip\Service\Geoip
     */
    public function __invoke($ip = null)
    {
        return $this->geoip->setIp($ip);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->geoip;
    }
}
