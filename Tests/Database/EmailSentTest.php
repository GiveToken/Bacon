<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\Database\{
    EmailCredential,
    EmailSent
};

/**
 * This class tests the EmailSent class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/EmailSentTest
 */
class EmailSentTest
extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Bacon\Tests\Traits\User;

    /**
     * Requires the util.php file of functions
     */
    public static function setUpBeforeClass()
    {
        include_once __DIR__.'/../../../../util.php';
    }

    /**
     * Creates test user & credentials
     */
    public function setUp()
    {
        // setup test user
        $this->User = $this->createUser();

        // Info for email credentials
        $user_id = $this->User->id;
        $username = 'user'.rand();
        $password = 'my'.rand();
        $smtp_host = rand(0, 255).'.'.rand(0, 255).'.'.rand(0, 255).'.'.rand(0, 255);
        $smtp_port = rand(1, 1000);

        // Create credentials
        $this->EmailCredential = new EmailCredential();
        $this->EmailCredential->create($user_id, $username, $password, $smtp_host, $smtp_port);
    }

    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // no params
        $result = new EmailSent();
        $this->assertEquals('Sizzle\Bacon\Database\EmailSent', get_class($result));

        // test with bad id
        $result2 = new EmailSent(-1);
        $this->assertFalse(isset($result2->id));

        // test with good id in testCreate() below
    }

    /**
     * Tests the create function.
     *
     * @expectedException TypeError
     */
    public function testCreateBadType()
    {
        // test creation with bad input
        $details = '';
        $EmailSent = new EmailSent();
        $id = $EmailSent->create($details);
    }

    /**
     * Tests the create function.
     */
    public function testCreate()
    {
        // test creation with empty input
        $details = array();
        $EmailSent = new EmailSent();
        $id = $EmailSent->create($details);
        $this->assertEquals(0, $id);

        // setup complete array
        $allDetails = array(
            'to'=>rand() . '@gosizzle.io',
            'from'=>rand() . '@gosizzle.io',
            'subject'=>rand() . ' Subject',
            'body'=>rand() . ' Body<br>',
            'email_credential_id'=>$this->EmailCredential->id,
            'success'=>'Yes'
        );

        //test creation with missing details
        $details = $allDetails;
        unset($details['to']);
        $EmailSent = new EmailSent();
        $id = $EmailSent->create($details);
        $this->assertEquals(0, $id);
        $details = $allDetails;
        unset($details['from']);
        $EmailSent = new EmailSent();
        $id = $EmailSent->create($details);
        $this->assertEquals(0, $id);
        $details = $allDetails;
        unset($details['subject']);
        $EmailSent = new EmailSent();
        $id = $EmailSent->create($details);
        $this->assertEquals(0, $id);
        $details = $allDetails;
        unset($details['body']);
        $EmailSent = new EmailSent();
        $id = $EmailSent->create($details);
        $this->assertEquals(0, $id);
        $details = $allDetails;
        unset($details['email_credential_id']);
        $EmailSent = new EmailSent();
        $id = $EmailSent->create($details);
        $this->assertEquals(0, $id);
        $details = $allDetails;
        unset($details['success']);
        $EmailSent = new EmailSent();
        $id = $EmailSent->create($details);
        $this->assertEquals(0, $id);

        // test creation with needed details
        $EmailSent = new EmailSent();
        $id = $EmailSent->create($allDetails);
        $this->assertTrue((int) $id > 0);
        $this->assertEquals($EmailSent->to, $allDetails['to']);
        $this->assertEquals($EmailSent->from, $allDetails['from']);
        $this->assertEquals($EmailSent->subject, $allDetails['subject']);
        $this->assertEquals($EmailSent->body, $allDetails['body']);
        $this->assertEquals($EmailSent->email_credential_id, $allDetails['email_credential_id']);
        $this->assertEquals($EmailSent->success, $allDetails['success']);

        // test creation with all details
        $details = $allDetails;
        $details['reply_to'] = rand() . '@gosizzle.io';
        $details['cc'] = rand() . '@gosizzle.io';
        $details['bcc'] = rand() . '@gosizzle.io';
        $details['error_message'] = 'Error ' . rand();
        $EmailSent = new EmailSent();
        $id = $EmailSent->create($details);
        $this->assertEquals($EmailSent->to, $details['to']);
        $this->assertEquals($EmailSent->from, $details['from']);
        $this->assertEquals($EmailSent->subject, $details['subject']);
        $this->assertEquals($EmailSent->body, $details['body']);
        $this->assertEquals($EmailSent->email_credential_id, $details['email_credential_id']);
        $this->assertEquals($EmailSent->success, $details['success']);
        $this->assertEquals($EmailSent->reply_to, $details['reply_to']);
        $this->assertEquals($EmailSent->cc, $details['cc']);
        $this->assertEquals($EmailSent->bcc, $details['bcc']);
        $this->assertEquals($EmailSent->error_message, $details['error_message']);
    }

    /**
     * Delete users created for testing
     */
    protected function tearDown()
    {
        $this->deleteUsers();
    }
}
