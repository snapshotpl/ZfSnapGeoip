<?php

/**
 * IPable iterface
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip;

interface IpAwareInterface
{
    /**
     * @return string IP address
     */
    public function getIpAddress();
}