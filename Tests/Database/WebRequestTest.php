<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\Database\{
    WebRequest
};

/**
 * This class tests the WebRequest class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/WebRequestTest
 */
class WebRequestTest
extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the newVisitor function.
     */
    public function testNewVisitor()
    {
        // test true
        $cookie = rand();
        $this->assertTrue((new WebRequest())->newVisitor($cookie));

        // test false
        $webRequest = new WebRequest(5);
        $this->assertFalse((new WebRequest())->newVisitor($webRequest->visitor_cookie));
    }
}
