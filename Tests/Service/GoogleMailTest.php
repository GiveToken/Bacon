<?php
namespace Sizzle\Bacon\Tests\Service;

use \Sizzle\Bacon\Service\GoogleMail;

/**
 * This class tests the GoogleMail class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Service/GoogleMailTest
 */
class GoogleMailTest
extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        $GoogleMail = new GoogleMail();
        $this->assertEquals('Sizzle\Bacon\Service\GoogleMail', get_class($GoogleMail));
    }

    /**
     * Tests the sendMail function.
     */
    public function testSendMail()
    {
        // No way to test this locally?
        $this->markTestIncomplete();
    }

    /**
     * Tests mocking the GoogleMail class.
     * This is my first attempt at mocking...
     *
     * @runInSeparateProcess
     * @preserveGlobalState  disabled
     */
    public function testMock()
    {
        $mock = \Mockery::mock('overload:\\Sizzle\\Bacon\\Service\\GoogleMail');
        $this->assertEquals('Sizzle\Bacon\Service\GoogleMail', get_class($mock));
        $mock->shouldReceive('sendMail')
            ->with('founder@gosizzle.io', 'hi', 'hi', 'founder@gosizzle.io')
            ->andReturn(true);
        $GoogleMail = new GoogleMail();
        $this->assertTrue($GoogleMail->sendMail('founder@gosizzle.io', 'hi', 'hi', 'founder@gosizzle.io'));
    }

    /**
     * Cleans up after the tests
     */
    public function tearDown()
    {
        \Mockery::close();
    }
}
