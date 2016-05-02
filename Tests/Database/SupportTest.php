<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\Database\Support;

/**
 * This class tests the Support class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/SupportTest
 */
class SupportTest extends \PHPUnit_Framework_TestCase
{
    protected $id;

    /**
     * Requires the util.php file of functions
     */
    public static function setUpBeforeClass()
    {
        include_once __DIR__.'/../../../../util.php';
    }

    /**
     * Tests the __construct function.
     */
    public function testCreate()
    {
        $Support = (new Support())->create("fakeEmail@gosizzle.io", "This is also a fake message");
        $this->assertEquals('Sizzle\Bacon\Database\Support', get_class($Support));
        $this->assertTrue(isset($Support->id));
        $this->assertEquals("fakeEmail@gosizzle.io", $Support->email_address);
        $this->assertEquals("This is also a fake message", $Support->message);
        $this->id = $Support->id;
    }

    /**
     * Destroys the test data.
     */
    protected function tearDown()
    {
        $query = "DELETE FROM support WHERE id = '{$this->id}'";
        (new Support())->execute_query($query);
    }
}
