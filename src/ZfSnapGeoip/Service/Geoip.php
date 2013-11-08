<?php

/**
 * Geoip Service
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip\Service;

use ZfSnapGeoip\Entity\RecordInterface;
use ZfSnapGeoip\Exception\DomainException;
use ZfSnapGeoip\IpAwareInterface;
use Zend\Http\Request as HttpRequest;
use Zend\ServiceManager\ServiceManager;
use geoiprecord as GeoipCoreRecord;

class Geoip implements \Zend\ServiceManager\ServiceManagerAwareInterface
{
    /**
     * @var \GeoIP
     */
    private $geoip;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var GeoipCoreRecord[]
     */
    private $records;

    /**
     * @var string
     */
    private $defaultIp;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $regions;

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->closeGeoip();
    }

    /**
     * @return \GeoIP
     */
    public function getGeoip()
    {
        if (!$this->geoip) {
            $config   = $this->getConfig();
            $database = $config['destination'] . $config['filename'];
            if (file_exists($database)) {
                $this->geoip = geoip_open($database, $config['flag']);
            } else {
                throw new DomainException('You need to download Maxmind database. You can use ZFTool or composer.json for that :)');
            }
        }
        return $this->geoip;
    }

    /**
     * @param string $ipAddress
     * @return GeoipCoreRecord
     */
    public function getGeoipRecord($ipAddress)
    {
        $ipAddress = $this->getIp($ipAddress);

        if (!isset($this->records[$ipAddress])) {
            $this->records[$ipAddress] = GeoIP_record_by_addr($this->getGeoip(), $ipAddress);
        }

        return $this->records[$ipAddress];
    }

    /**
     * @param string $ipAddress
     * @return string
     */
    private function getIp($ipAddress)
    {
        if ($ipAddress === null) {
            $ipAddress = $this->getDefaultIp();
        }

        if ($ipAddress instanceof IpAwareInterface) {
            $ipAddress = $ipAddress->getIpAddress();
        }

        return $ipAddress;
    }

    /**
     * @param string $ipAdress
     * @return RecordInterface
     */
    public function getRecord($ipAdress = null)
    {
        $record = $this->serviceManager->get('geoip_record');
        /* @var $record RecordInterface */

        if (!$record instanceof RecordInterface) {
            throw new DomainException('Incorrect record implementation');
        }

        $geoipRecord = $this->getGeoipRecord($ipAdress);

        if (!$geoipRecord instanceof GeoipCoreRecord) {
            return $record;
        }

        $data                = get_object_vars($geoipRecord);
        $data['region_name'] = $this->getRegionName($data);

        $hydrator = $this->serviceManager->get('geoip_hydrator');
        /* @var $hydrator \Zend\Stdlib\Hydrator\HydratorInterface */

        $hydrator->hydrate($data, $record);

        return $record;
    }

    /**
     * @param string $ipAddress
     * @return RecordInterface
     */
    public function lookup($ipAddress = null)
    {
        return $this->getRecord($ipAddress);
    }

    /**
     * @return self
     */
    private function closeGeoip()
    {
        if ($this->geoip) {
            geoip_close($this->geoip);
            $this->geoip = null;
        }
        return $this;
    }

    /**
     * @return array
     */
    private function getRegions()
    {
        if ($this->regions === null) {
            $config        = $this->getConfig();
            $regionVarPath = $config['regionvars'];
            include($regionVarPath);

            if (!isset($GEOIP_REGION_NAME)) {
                throw new DomainException(sprintf('Missing region names data in path %s', $regionVarPath));
            }

            $this->regions = $GEOIP_REGION_NAME;
        }
        return $this->regions;
    }

    /**
     * @return array
     */
    private function getConfig()
    {
        if ($this->config === null) {
            $config       = $this->serviceManager->get('Config');
            $this->config = $config['maxmind']['database'];
        }
        return $this->config;
    }

    /**
     *
     * @return $string|null
     */
    private function getDefaultIp()
    {
        if ($this->defaultIp === null) {
            $request = $this->serviceManager->get('Request');

            if ($request instanceof HttpRequest) {
                $ipAddress       = $request->getServer('REMOTE_ADDR');
                $this->defaultIp = $ipAddress;
            } else {
                $this->defaultIp = false;
                return null;
            }
        }

        return $this->defaultIp;
    }

    /**
     * @param string $ip
     * @return string
     */
    private function getRegionName(array $data = array())
    {
        $regions     = $this->getRegions();
        $countryCode = isset($data['country_code']) ? $data['country_code'] : null;

        if (isset($regions[$countryCode])) {
            $regionCodes = $regions[$countryCode];
            $regionCode  = isset($data['region']) ? $data['region'] : null;

            if (isset($regionCodes[$regionCode])) {
                return $regionCodes[$regionCode];
            }
        }
        return null;
    }

    /**
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
}
