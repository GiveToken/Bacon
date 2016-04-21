<?php
namespace Sizzle\Bacon\Tests;

use \Sizzle\Bacon\Connection;

/**
 * This class tests the Connection class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/ConnectionTest
 */
class ConnectionTest
extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the getService function.
     */
    public function testConstructor()
    {
        new Connection();
        $this->assertTrue(isset(Connection::$mysqli));
        $this->assertEquals(Connection::$mysqli->character_set_name(), 'utf8mb4');
    }
}
