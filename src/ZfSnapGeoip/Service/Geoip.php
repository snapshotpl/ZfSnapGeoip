<?php

/**
 * Geoip Service
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip\Service;

use \ZfSnapGeoip\Exception\DomainException;
use \ZfSnapGeoip\IpAwareInterface;
use \Zend\ServiceManager\ServiceManager;

class Geoip implements \Zend\ServiceManager\ServiceManagerAwareInterface
{
    /**
     * @var \GeoIP
     */
    private $geoip;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $sm;

    /**
     * @var geoiprecord[]
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
            $config = $this->getConfig();
            $database = $config['destination'] . $config['filename'];
            if (file_exists($database)) {
                $this->geoip = geoip_open($database, $config['flag']);
            } else {
                throw new DomainException('You need to download Maxmind database. You can use ZFTool for that :)');
            }
        }
        return $this->geoip;
    }

    /**
     * @param string $ip
     * @return \geoiprecord
     */
    public function getGeoipRecord($ip)
    {
        $ip = $this->getIp($ip);

        if (!isset($this->records[$ip])) {
            $record = GeoIP_record_by_addr($this->getGeoip(), $ip);
            $this->records[$ip] = $record;
        }

        return $this->records[$ip];
    }

    /**
     * @param string $ip
     * @return string
     */
    private function getIp($ip)
    {
        if ($ip === null) {
            $ip = $this->getDefaultIp();
        }

        if ($ip instanceof IpAwareInterface) {
            $ip = $ip->getIpAddress();
        }

        return $ip;
    }

    /**
     * @param string $ip
     * @return \ZfSnapGeoip\Entity\RecordInterface
     */
    public function getRecord($ip = null)
    {
        $record = $this->sm->get('geoip_record');
        /* @var $record \ZfSnapGeoip\Entity\RecordInterface */

        if (!($record instanceof \ZfSnapGeoip\Entity\RecordInterface)) {
            throw new DomainException('Incorrect record implementation');
        }

        $geoipRecord = $this->getGeoipRecord($ip);

        if (!($geoipRecord instanceof \geoiprecord)) {
            return $record;
        }

        $data = get_object_vars($geoipRecord);
        $data['region_name'] = $this->getRegionName($data);

        $hydrator = $this->sm->get('geoip_hydrator');
        /* @var $hydrator \Zend\Stdlib\Hydrator\HydratorInterface */

        $hydrator->hydrate($data, $record);

        return $record;
    }

    /**
     * @param string $ip
     * @return \ZfSnapGeoip\Entity\RecordInterface
     */
    public function lookup($ip = null)
    {
        return $this->getRecord($ip);
    }

    /**
     * @return \ZfSnapGeoip\Service\Geoip
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
        if (!$this->regions) {
            $config = $this->getConfig();
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
        if (!$this->config) {
            $config = $this->sm->get('Config');
            $this->config = $config['maxmind']['database'];;
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
            $request = $this->sm->get('Request');

            if ($request instanceof \Zend\Http\Request) {
                $ip = $request->getServer('REMOTE_ADDR');
                $this->defaultIp = $ip;
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
        $regions = $this->getRegions();
        $countryCode = isset($data['country_code']) ? $data['country_code'] : null;

        if (isset($regions[$countryCode])) {
            $regionCodes = $regions[$countryCode];
            $regionCode = isset($data['region']) ? $data['region'] : null;

            if (isset($regionCodes[$regionCode])) {
                return $regionCodes[$regionCode];
            }
        }
        return null;
    }

    /**
     * @param \Zend\ServiceManager\ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
    }
}
