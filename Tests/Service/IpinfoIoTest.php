<?php
namespace Sizzle\Bacon\Tests\Service;

use \Sizzle\Bacon\Service\IpinfoIo;

/**
 * This class tests the IpinfoIoTest class
 *
 * vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Service/IpinfoIoTest
 */
class IpinfoIoTest
extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        $IpinfoIo = new IpinfoIo();
        $this->assertEquals('Sizzle\Bacon\Service\IpinfoIo', get_class($IpinfoIo));
    }

    /**
     * Tests the getInfo function.
     */
    public function testGetInfo()
    {
        // test success
        $ipAddress = '50.207.137.70';
        /* TechnologyAdvice office, expecting
        {
          "ip": "50.207.137.70",
          "hostname": "50-207-137-70-static.hfc.comcastbusiness.net",
          "city": "Stanford",
          "region": "California",
          "country": "US",
          "loc": "37.4178,-122.1720",
          "org": "AS7922 Comcast Cable Communications, Inc.",
          "postal": "94305"
        }
        */
        $IpinfoIo = new IpinfoIo();
        $locale = $IpinfoIo->getInfo($ipAddress);
        $this->assertTrue((bool)$locale);
        $this->assertEquals($ipAddress, $locale->ip);
        $this->assertEquals('50-207-137-70-static.hfc.comcastbusiness.net', $locale->hostname);
        $this->assertEquals('Palo Alto', $locale->city);
        $this->assertEquals('California', $locale->region);
        $this->assertEquals('US', $locale->country);
        $this->assertEquals('37.4576,-122.1041', $locale->loc);
        $this->assertEquals('AS7922 Comcast Cable Communications, Inc.', $locale->org);
        $this->assertEquals('94303', $locale->postal);
    }

    /**
     * Tests mocking the IpinfoIo class.
     * This is my first attempt at mocking...
     *
     * @runInSeparateProcess
     * @preserveGlobalState  disabled
     */
    public function testMock()
    {
        $mock = \Mockery::mock('overload:\\Sizzle\\Bacon\\Service\\IpinfoIo');
        $this->assertEquals('Sizzle\Bacon\Service\IpinfoIo', get_class($mock));
        $ipAddress = '1.1.1.1';
        $return = (object) array(
            "ip"=> $ipAddress,
            "hostname"=> "static.tl.andalasia.net",
            "city"=> "Castle",
            "region"=> "Andalasia",
            "country"=> "Cartoon",
            "loc"=> "1337,7331",
            "org"=> "True Love",
            "postal"=> "1337"
        );
        $mock->shouldReceive('getInfo')
            ->with($ipAddress)
            ->andReturn($return);
        $IpinfoIo = new IpinfoIo();
        $locale = $IpinfoIo->getInfo($ipAddress);
        $this->assertTrue((bool)$locale);
        $this->assertEquals($ipAddress, $locale->ip);
        $this->assertEquals('static.tl.andalasia.net', $locale->hostname);
        $this->assertEquals('Castle', $locale->city);
        $this->assertEquals('Andalasia', $locale->region);
        $this->assertEquals('Cartoon', $locale->country);
        $this->assertEquals('1337,7331', $locale->loc);
        $this->assertEquals('True Love', $locale->org);
        $this->assertEquals('1337', $locale->postal);
    }

    /**
     * Cleans up after the tests
     */
    public function tearDown()
    {
        \Mockery::close();
    }
}
