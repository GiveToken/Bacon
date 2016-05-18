<?php
namespace Sizzle\Tests;

use \Sizzle\Bacon\Database\{
    ExperimentNote
};

/**
 * This class tests the ExperimentNote class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/ExperimentNoteTest
 */
class ExperimentNoteTest
extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // no params
        $result = new ExperimentNote();
        $this->assertEquals('Sizzle\Bacon\Database\ExperimentNote', get_class($result));
    }
}
