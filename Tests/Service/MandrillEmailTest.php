<?php
namespace Sizzle\Bacon\Tests\Service;

use \Sizzle\Bacon\Service\MandrillEmail;

/**
 * This class tests the MandrillEmail class
 *
   Make sure you have set TEST_EMAIL in src/Tests/local.php
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Service/MandrillEmailTest
 */
class MandrillEmailTest
extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        $mandrill = new MandrillEmail();
        $this->assertEquals('Sizzle\Bacon\Service\MandrillEmail', get_class($mandrill));
    }

    /**
     * Tests the send function.
     */
    public function testSend()
    {
        $mandrill = new MandrillEmail();
        $success = $mandrill->send(
            array(
                'to'=>array(array('email'=>'noEmailShouldGoHere@gosizzle.io')),
                'from_email'=>'test@gosizzle.io',
                'from_name'=>'S!zzle Tests',
                'subject'=>'New S!zzle Mandrill Test',
                'html'=>'This message should have gone to TEST_EMAIL',
            )
        );
        $this->assertTrue($success);
        if ('' != TEST_EMAIL) {
            echo "\n\nCheck your inbox for the test email.\n\n";
        } else {
            echo "\n\nPlease set TEST_EMAIL in src/Tests/local.php\n\n";
        }

        // TODO: hit Mandrill for info on where it was sent
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
        $mock = \Mockery::mock('overload:\\Sizzle\\Bacon\\Service\\MandrillEmail');
        $this->assertEquals('Sizzle\Bacon\Service\MandrillEmail', get_class($mock));
        $mock->shouldReceive('sendMail')
            ->with(array())
            ->andReturn(true);
        $mandrill = new MandrillEmail();
        $this->assertTrue($mandrill->sendMail(array()));
    }

    /**
     * Cleans up after the tests
     */
    public function tearDown()
    {
        \Mockery::close();
    }
}
