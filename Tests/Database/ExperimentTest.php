<?php
namespace Sizzle\Tests;

use \Sizzle\Bacon\Database\{
    Experiment
};

/**
 * This class tests the Experiment class
 *
 * vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/ExperimentTest
 */
class ExperimentTest
extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // no params
        $result = new Experiment();
        $this->assertEquals('Sizzle\Bacon\Database\Experiment', get_class($result));
    }
}
