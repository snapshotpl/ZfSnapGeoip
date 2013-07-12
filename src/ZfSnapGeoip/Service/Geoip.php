<?php

/**
 * Geoip Service
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip\Service;

use \ZfSnapGeoip\Exception\DomainException;

class Geoip
{
    /**
     * @var \GeoIP
     */
    private $geoip;

    /**
     * @var geoiprecord
     */
    private $record;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $regions;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->closeGeoip();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getCity();
    }

    /**
     * @param string $ip|ZfSnapGeoip\IpAwareInterface
     * @return \ZfSnapGeoip\Service\Geoip
     */
    public function setIp($ip)
    {
        if ($ip !== null) {
            if ($ip instanceof ZfSnapGeoip\IpAwareInterface) {
                $ip = $ip->getIpAddress();
            }
            $this->ip = $ip;
        }

        return $this;
    }

    /**
     * @return \GeoIP
     */
    private function getGeoip()
    {
        if (!$this->geoip) {
            $this->geoip = geoip_open($this->config['destination'] . $this->config['filename'], $this->config['flag']);
        }
        return $this->geoip;
    }

    /**
     * @param string $property
     * @param string $ip
     * @return \geoiprecord
     */
    private function getRecord($property, $ip = null)
    {
        if ($ip === null) {
            $ip = $this->ip;
        } else {
            $this->setIp($ip);
        }

        if (!$this->record && $ip !== null) {
            $this->record = geoip_record_by_addr($this->getGeoip(), $ip);
        }

        if ($this->record !== null && property_exists($this->record, $property)) {
            return $this->record->{$property};
        }

        return null;
    }

    /**
     * @return \ZfSnapGeoip\Service\Geoip
     */
    private function closeGeoip()
    {
        if ($this->geoip) {
            geoip_close($this->geoip);
        }

        return $this;
    }

    /**
     * @return array
     */
    private function getRegions()
    {
        if (!$this->regions) {
            $regionVarPath = $this->config['regionvars'];
            include($regionVarPath);

            if (!isset($GEOIP_REGION_NAME)) {
                throw new DomainException(sprintf('Missing region names data in path %s', $regionVarPath));
            }

            $this->regions = $GEOIP_REGION_NAME;
        }
        return $this->regions;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getCountryCode($ip = null)
    {
        return $this->getRecord('country_code', $ip);
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getCountryCode3($ip = null)
    {
        return $this->getRecord('country_code3', $ip);
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getCountryName($ip = null)
    {
        return $this->getRecord('country_name', $ip);
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getRegionCode($ip = null)
    {
        return $this->getRecord('region', $ip);
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getRegionName($ip = null)
    {
        $regions = $this->getRegions();
        return $regions[$this->getCountryCode($ip)][$this->getRegionCode($ip)];
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getPostalCode($ip = null)
    {
        return $this->getRecord('postal_code', $ip);
    }

    /**
     * @param string $ip
     * @return float
     */
    public function getLatitude($ip = null)
    {
        return $this->getRecord('latitude', $ip);
    }

    /**
     * @param string $ip
     * @return float
     */
    public function getLongitude($ip = null)
    {
        return $this->getRecord('longitude', $ip);
    }

    /**
     * @param string $ip
     * @return float
     */
    public function getMetroCode($ip = null)
    {
        return $this->getRecord('metro_code', $ip);
    }

    /**
     * @param type $ip
     * @return int
     */
    public function getAreaCode($ip = null)
    {
        return $this->getRecord('area_code', $ip);
    }

    /**
     * @param type $ip
     * @return int
     */
    public function getDmaCode($ip = null)
    {
        return $this->getRecord('dma_code', $ip);
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getContinentalCode($ip = null)
    {
        return $this->getRecord('continental_code', $ip);
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getCity($ip = null)
    {
        return $this->getRecord('city', $ip);
    }

    /**
     * @param string $ip
     * @return array
     */
    public function toArray($ip = null)
    {
        return array(
            'areaCode'          => $this->getAreaCode($ip),
            'city'              => $this->getCity($ip),
            'continentalCode'   => $this->getContinentalCode($ip),
            'countryCode'       => $this->getCountryCode($ip),
            'countryCode3'      => $this->getCountryCode3($ip),
            'countryName'       => $this->getCountryName($ip),
            'dmaCode'           => $this->getDmaCode($ip),
            'latitude'          => $this->getLatitude($ip),
            'longitude'         => $this->getLongitude($ip),
            'metroCode'         => $this->getMetroCode($ip),
            'postalCode'        => $this->getPostalCode($ip),
            'regionCode'        => $this->getRegionCode($ip),
            'regionName'        => $this->getRegionName($ip),
        );
    }
}
