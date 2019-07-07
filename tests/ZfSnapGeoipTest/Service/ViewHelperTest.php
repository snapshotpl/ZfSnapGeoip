<?php

namespace ZfSnapGeoipTest\Service;

use PHPUnit_Framework_TestCase;
use ZfSnapGeoip\Entity\RecordInterface;
use ZfSnapGeoipTest\Bootstrap;

class ViewHelperTest extends PHPUnit_Framework_TestCase
{
    private $geoip;

    protected function setUp()
    {
        $sm = Bootstrap::getServiceManager();
        $this->geoip = $sm->get('ViewHelperManager')->get('geoip');
    }

    public function testShowRecordFromViewHelper()
    {
        $result = $this->geoip->__invoke('216.239.51.99');

        $this->assertInstanceOf(RecordInterface::class, $result);
    }
}
