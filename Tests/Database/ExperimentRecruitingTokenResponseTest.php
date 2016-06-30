<?php
namespace Sizzle\Tests;

use \Sizzle\Bacon\Database\{
    ExperimentRecruitingTokenResponse
};

/**
 * This class tests the ExperimentRecruitingTokenResponse class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/ExperimentRecruitingTokenResponseTest
 */
class ExperimentRecruitingTokenResponseTest
extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // no params
        $result = new ExperimentRecruitingTokenResponse();
        $this->assertEquals('Sizzle\Bacon\Database\ExperimentRecruitingTokenResponse', get_class($result));
    }

    /**
     * Tests the create function.
     *
     * @expectedException Exception
     */
    public function testCreateFail()
    {
        // fail
        (new ExperimentRecruitingTokenResponse())->create(-1, 'fail', -1);
    }
}
