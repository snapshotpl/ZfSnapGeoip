<?php

namespace ZfSnapGeoip\Entity;

/**
 * RecordInterface
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
interface RecordInterface
{
    public function getAreaCode();

    public function getCity();

    public function getContinentCode();

    public function getCountryCode();

    public function getCountryCode3();

    public function getCountryName();

    public function getDmaCode();

    public function getLatitude();

    public function getLongitude();

    public function getMetroCode();

    public function getPostalCode();

    public function getRegion();

    public function getRegionName();

    public function setAreaCode($data);

    public function setCity($data);

    public function setContinentCode($data);

    public function setCountryCode($data);

    public function setCountryCode3($data);

    public function setCountryName($data);

    public function setDmaCode($data);

    public function setLatitude($data);

    public function setLongitude($data);

    public function setMetroCode($data);

    public function setPostalCode($data);

    public function setRegion($data);

    public function setRegionName($data);

    public function getTimezone();
}
