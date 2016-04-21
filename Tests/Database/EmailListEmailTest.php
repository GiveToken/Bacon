<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\Database\{
    EmailList,
    EmailListEmail
};

/**
 * This class tests the EmailListEmail class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/EmailListEmailTest
 */
class EmailListEmailTest
extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Tests\Traits\User;

    /**
     * Requires the util.php file of functions
     */
    public static function setUpBeforeClass()
    {
        include_once __DIR__.'/../../../../util.php';
    }

    /**
     * Creates test user
     */
    public function setUp()
    {
        // setup test user
        $this->User = $this->createUser();

        //set up the test list
        $user_id = $this->User->id;
        $name = 'My '.rand().'th List';
        $this->EmailList = new EmailList();
        $this->EmailList->create($user_id, $name);
    }

    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // no params
        $result = new EmailListEmail();
        $this->assertEquals('Sizzle\Bacon\Database\EmailListEmail', get_class($result));

        // test with bad id
        $result2 = new EmailListEmail(-1);
        $this->assertFalse(isset($result2->id));

        // test with good id in testCreate() below
    }

    /**
     * Tests the create function.
     */
    public function testCreate()
    {
        // Info for email
        $email_list_id = $this->EmailList->id;
        $email = rand().'@GoSizzle.io';

        // Create credentials
        $EmailListEmail = new EmailListEmail();
        $id = $EmailListEmail->create($email_list_id, $email);

        // Check class variables set
        $this->assertEquals($EmailListEmail->id, $id);
        $this->assertEquals($EmailListEmail->email_list_id, $email_list_id);
        $this->assertEquals($EmailListEmail->email, $email);

        // See if credentials were saved in DB
        $EmailListEmail2 = new EmailListEmail($id);
        $this->assertEquals($EmailListEmail2->id, $id);
        $this->assertEquals($EmailListEmail2->email_list_id, $email_list_id);
        $this->assertEquals($EmailListEmail2->email, $email);
    }

    /**
     * Tests the delete function.
     */
    public function testDelete()
    {
        // Info for email credentials to be deleted
        $email_list_id = $this->EmailList->id;
        $email = rand().'@GoSizzle.io';
        $EmailListEmail = new EmailListEmail();
        $id = $EmailListEmail->create($email_list_id, $email);

        // Delete it!
        $EmailListEmail->delete();
        $this->assertFalse(isset($EmailListEmail->id));
        $this->assertFalse(isset($EmailListEmail->email_list_id));
        $this->assertFalse(isset($EmailListEmail->email));

        // test with old (now "deleted") id
        $EmailListEmail2 = new EmailListEmail($id);
        $this->assertFalse(isset($EmailListEmail2->id));
    }

    /**
     * Delete users created for testing
     */
    protected function tearDown()
    {
        //$this->deleteUsers();
    }
}
