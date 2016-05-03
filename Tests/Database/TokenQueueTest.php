<?php
namespace Sizzle\Bacon\Tests\Database;

use Sizzle\Bacon\{
    Connection,
    Database\TokenQueue
};

/**
 * This class tests the TokenQueue class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/TokenQueueTest
 */
class TokenQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // $id = null case
        $tokenQueue = new TokenQueue();
        $this->assertEquals('Sizzle\Bacon\Database\TokenQueue', get_class($tokenQueue));
        $this->assertFalse(isset($tokenQueue->id));
    }
}
