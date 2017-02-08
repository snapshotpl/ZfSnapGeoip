<?php

namespace ZfSnapGeoipTest\Service;

use PHPUnit_Framework_TestCase;
use ZfSnapGeoip\Entity\RecordInterface;
use ZfSnapGeoip\Service\Geoip;
use ZfSnapGeoipTest\Bootstrap;

/**
 * ServiceGeoipTest
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class GeoipTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Geoip
     */
    private $geoip;

    /**
     * @var array
     */
    private $ip = array(
        'local' => '192.168.0.1',
        'google' => '216.239.51.99',
    );

    protected function setUp()
    {
        $sm = Bootstrap::getServiceManager();
        $this->geoip = $sm->get('geoip');
    }

    public function testValidTestDependencies()
    {
        $this->assertInstanceOf('\ZfSnapGeoip\Service\Geoip', $this->geoip);
    }

    public function testCountryCode()
    {
        $recordLocal = $this->getRecordWithLocalIp();
        $this->assertNull($recordLocal->getCountryCode());

        $recordTrue = $this->getRecordWithTrueIp();
        $this->assertEquals('US', $recordTrue->getCountryCode());



        $recordTrueSecond = $this->getRecordWithTrueIp();
        $this->assertEquals('US', $recordTrueSecond->getCountryCode());
    }

    public function testCountryCodeArgument()
    {
        $this->assertEquals('US', $this->geoip->getRecord($this->ip['google'])->getCountryCode());
        $this->assertNull($this->geoip->getRecord($this->ip['local'])->getCountryCode());
        $this->assertEquals('US', $this->geoip->getRecord($this->ip['google'])->getCountryCode());
    }

    public function testCityTrue()
    {
        $record = $this->getRecordWithTrueIp();

        $this->assertEquals('Mountain View', $record->getCity());
        $this->assertEquals('Mountain View', (string) $record);
    }

    public function testCity()
    {
        $record = $this->getRecordWithLocalIp();

        $this->assertEquals(null, $record->getCity());
        $this->assertEquals('', (string) $record);
    }

    public function testGeoPosition()
    {
        $record = $this->getRecordWithTrueIp();

        $this->assertTrue(is_double($record->getLatitude()));
        $this->assertTrue(is_double($record->getLongitude()));
    }

    public function testRegionNameTrue()
    {
        $record = $this->getRecordWithTrueIp();

        $this->assertEquals('California', $record->getRegionName());
    }

    public function testRegionNameFalse()
    {
        $record = $this->getRecordWithLocalIp();

        $this->assertNull($record->getRegionName());
    }

    public function testIpAwareInterface()
    {
        $localImplement = $this->getIpAwareInterfaceImplementation($this->ip['local']);
        $this->assertNull($this->geoip->getRecord($localImplement)->getCity());

        $publicImplement = $this->getIpAwareInterfaceImplementation($this->ip['google']);
        $this->assertEquals('Mountain View', $this->geoip->getRecord($publicImplement)->getCity());
    }

    /**
     * @return RecordInterface
     */
    private function getRecordWithTrueIp()
    {
        return $this->geoip->getRecord($this->ip['google']);
    }

    /**
     * @return RecordInterface
     */
    private function getRecordWithLocalIp()
    {
        return $this->geoip->getRecord($this->ip['local']);
    }

    private function getIpAwareInterfaceImplementation($ip)
    {
        $ipIterface = $this->getMockBuilder('\ZfSnapGeoip\IpAwareInterface')->getMock();

        $ipIterface->expects($this->any())
                ->method('getIpAddress')
                ->will($this->returnValue($ip));

        return $ipIterface;
    }

}
