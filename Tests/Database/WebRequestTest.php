<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\Database\{
    Experiment,
    ExperimentWebRequest,
    WebRequest
};

/**
 * This class tests the WebRequest class
 *
 * vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/WebRequestTest
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

    /**
     * Tests the inExperiment function.
     */
    public function testInExperiment()
    {
        // test false no web request
        $this->assertFalse((new WebRequest())->inExperiment(-1, 'blah'));
        $this->assertFalse((new WebRequest(-1))->inExperiment(-1, 'blah'));

        // test false no experiment
        $webRequest = new WebRequest();
        $webRequest->visitor_cookie = rand();
        $webRequest->host = 'HTTP_HOST'.rand();
        $webRequest->user_agent = 'HTTP_USER_AGENT'.rand();
        $webRequest->uri = 'REQUEST_URI'.rand();
        $webRequest->remote_ip = 'REMOTE_ADDR'.rand();
        $webRequest->script = 'SCRIPT_NAME'.rand();
        $webRequest->save();
        $this->assertTrue(is_int($webRequest->id));
        $this->assertTrue(0 < $webRequest->id);
        $this->assertFalse((new WebRequest($webRequest->id))->inExperiment(-1, 'blah'));

        // test true
        $experiment = new Experiment();
        $experiment->user_id = 131;
        $experiment->title = 'rand() = '.rand();
        $experiment->save();
        $version = rand();
        $this->assertTrue((new WebRequest($webRequest->id))->inExperiment($experiment->id, $version));
    }
}
