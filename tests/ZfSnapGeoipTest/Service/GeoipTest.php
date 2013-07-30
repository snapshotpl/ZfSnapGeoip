<?php

namespace ZfSnapGeoipTest\Service;

use \ZfSnapGeoip\Service\Geoip;
use \ZfSnapGeoipTest\Bootstrap;

/**
 * ServiceGeoipTest
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class GeoipTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $sm;

    /**
     * @var ZfSnapGeoip\Service\Geoip
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
        $this->sm = Bootstrap::getServiceManager();
        $this->assertInstanceOf('\Zend\ServiceManager\ServiceManager', $this->sm);

        $config = $this->sm->get('Config');

        $config['maxmind']['database']['regionvars'] = __DIR__ .'/../../../vendor/geoip/geoip/geoipregionvars.php';

        $geoip = new Geoip($config['maxmind']['database']);
        $this->geoip = $geoip;
        $this->assertInstanceOf('\ZfSnapGeoip\Service\Geoip', $geoip);
    }

    protected function tearDown()
    {
        $this->geoip->setIp(null);
    }

    public function testIp()
    {
        $this->ipTest();
    }

    public function testIpViaInterface()
    {
        $this->ipTest(function ($ip) {
            return $this->getIpAwareInterfaceImplementation($ip);
        });
    }

    public function testCountryCode()
    {
        $serviceTrue = $this->getServiceWithTrueIp();
        $this->assertEquals('US', $serviceTrue->getCountryCode());

        $serviceLocal = $this->getServiceWithLocalIp();
        $this->assertNull($serviceLocal->getCountryCode());

        $serviceTrueSecond = $this->getServiceWithTrueIp();
        $this->assertEquals('US', $serviceTrueSecond->getCountryCode());
    }

    public function testCountryCodeArgument()
    {
        $this->assertEquals('US', $this->geoip->getCountryCode($this->ip['google']));
        $this->assertNull($this->geoip->getCountryCode($this->ip['local']));
        $this->assertEquals('US', $this->geoip->getCountryCode($this->ip['google']));
    }

    public function testCityTrue()
    {
        $service = $this->getServiceWithTrueIp();

        $this->assertEquals('Mountain View', $service->getCity());
        $this->assertEquals('Mountain View', (string)$service);
    }

    public function testCity()
    {
        $service = $this->getServiceWithLocalIp();

        $this->assertEquals(null, $service->getCity());
        $this->assertEquals('', (string) $service);
    }

    public function testGeoPosition()
    {
        $service = $this->getServiceWithTrueIp();

        $this->assertTrue(is_double($service->getLatitude()));
        $this->assertTrue(is_double($service->getLongitude()));
    }

    public function testRegionNameTrue()
    {
        $service = $this->getServiceWithTrueIp();

        $this->assertEquals('California', $service->getRegionName());
    }

    public function testRegionNameFalse()
    {
        $service = $this->getServiceWithLocalIp();

        $this->assertNull($service->getRegionName());
    }

    /**
     * @return ZfSnapGeoip\Service\Geoip
     */
    private function getServiceWithTrueIp()
    {
        return $this->geoip->setIp($this->ip['google']);
    }

    /**
     * @return ZfSnapGeoip\Service\Geoip
     */
    private function getServiceWithLocalIp()
    {
        return $this->geoip->setIp($this->ip['local']);
    }

    private function ipTest($builder = null)
    {
        $ips = $this->ip;

        $ipLocal = $ips['local'];
        $ipGoogle = $ips['google'];

        if (is_callable($builder)) {
            foreach ($ips as &$ip) {
                $ip = $builder($ip);
            }
        }

        $ipLocalInterface = $ips['local'];
        $ipGoogleInterface = $ips['google'];

        $service = $this->geoip;

        $this->assertNull($service->getIp());

        $service->setIp($ipLocalInterface);
        $this->assertEquals($service->getIp(), $ipLocal);

        $service->setIp(null);
        $this->assertNull($service->getIp());

        $service->setIp($ipLocalInterface);
        $this->assertEquals($service->getIp(), $ipLocal);

        $this->assertEquals($service->getIp(), $ipLocal);
        $this->assertNotEquals($service->getIp(), $ipGoogle);

        $service->setIp($ipGoogleInterface);
        $this->assertEquals($service->getIp(), $ipGoogle);
        $this->assertNotEquals($service->getIp(), $ipLocal);
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
