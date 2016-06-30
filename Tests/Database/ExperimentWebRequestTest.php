<?php
namespace Sizzle\Tests;

use \Sizzle\Bacon\Database\{
    ExperimentWebRequest
};

/**
 * This class tests the ExperimentWebRequest class
 *
 * vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/ExperimentWebRequestTest
 */
class ExperimentWebRequestTest
extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // no params
        $result = new ExperimentWebRequest();
        $this->assertEquals('Sizzle\Bacon\Database\ExperimentWebRequest', get_class($result));
    }
}
