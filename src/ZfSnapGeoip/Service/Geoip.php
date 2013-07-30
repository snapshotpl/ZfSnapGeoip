<?php

/**
 * Geoip Service
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip\Service;

use \ZfSnapGeoip\Exception\DomainException;
use \ZfSnapGeoip\IpAwareInterface;

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
        if ($ip instanceof IpAwareInterface) {
            $ip = $ip->getIpAddress();
        }

        if ($ip !== $this->ip) {
            $this->record = null;
            $this->ip = $ip;
        }
        return $this;
    }

    /**
     * @return \GeoIP
     */
    public function getGeoip()
    {
        if (!$this->geoip) {
            $config = $this->config;
            $database = $config['destination'] . $config['filename'];
            $this->geoip = geoip_open($database, $config['flag']);
        }
        return $this->geoip;
    }

    /**
     * @param string $ip
     * @return \geoiprecord
     */
    public function getRecord($ip = null)
    {
        if (!$this->record || $ip !== $this->ip) {
            $this->record = GeoIP_record_by_addr($this->getGeoip(), $ip);
        }
        return $this->record;
    }

    /**
     * @param string $property
     * @param string $ip
     * @return string
     */
    private function getRecordProperty($property, $ip = null)
    {
        if ($ip === null) {
            $ip = $this->ip;
        } else {
            $this->setIp($ip);
        }

        $record = $this->getRecord($ip);

        if ($record !== null && property_exists($record, $property)) {
            return $record->{$property};
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
        return $this->getRecordProperty('country_code', $ip);
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getCountryCode3($ip = null)
    {
        return $this->getRecordProperty('country_code3', $ip);
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getCountryName($ip = null)
    {
        return $this->getRecordProperty('country_name', $ip);
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getRegionCode($ip = null)
    {
        return $this->getRecordProperty('region', $ip);
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getRegionName($ip = null)
    {
        $regions = $this->getRegions();
        $countryCode = $this->getCountryCode($ip);

        if (isset($regions[$countryCode])) {
            $regionCodes = $regions[$countryCode];
            $regionCode = $this->getRegionCode($ip);

            if (isset($regionCodes[$regionCode])) {
                return $regionCodes[$regionCode];
            }
        }
        return null;
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getPostalCode($ip = null)
    {
        return $this->getRecordProperty('postal_code', $ip);
    }

    /**
     * @param string $ip
     * @return float
     */
    public function getLatitude($ip = null)
    {
        return $this->getRecordProperty('latitude', $ip);
    }

    /**
     * @param string $ip
     * @return float
     */
    public function getLongitude($ip = null)
    {
        return $this->getRecordProperty('longitude', $ip);
    }

    /**
     * @param string $ip
     * @return float
     */
    public function getMetroCode($ip = null)
    {
        return $this->getRecordProperty('metro_code', $ip);
    }

    /**
     * @param type $ip
     * @return int
     */
    public function getAreaCode($ip = null)
    {
        return $this->getRecordProperty('area_code', $ip);
    }

    /**
     * @param type $ip
     * @return int
     */
    public function getDmaCode($ip = null)
    {
        return $this->getRecordProperty('dma_code', $ip);
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getContinentCode($ip = null)
    {
        return $this->getRecordProperty('continent_code', $ip);
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getCity($ip = null)
    {
        return $this->getRecordProperty('city', $ip);
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
            'continentalCode'   => $this->getContinentCode($ip),
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
