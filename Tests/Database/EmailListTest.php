<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\Database\{
    EmailList
};

/**
 * This class tests the EmailList class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/EmailListTest
 */
class EmailListTest
extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Bacon\Tests\Traits\User;

    /**
     * Creates test user
     */
    public function setUp()
    {
        // setup test user
        $this->User = $this->createUser();
    }

    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // no params
        $result = new EmailList();
        $this->assertEquals('Sizzle\Bacon\Database\EmailList', get_class($result));

        // test with bad id
        $result2 = new EmailList(-1);
        $this->assertFalse(isset($result2->id));

        // test with good id in testCreate() below
    }

    /**
     * Tests the create function.
     */
    public function testCreate()
    {
        // Info for email credentials
        $user_id = $this->User->id;
        $name = 'List #'.rand();

        // Create credentials
        $EmailList = new EmailList();
        $id = $EmailList->create($user_id, $name);

        // Check class variables set
        $this->assertEquals($EmailList->id, $id);
        $this->assertEquals($EmailList->user_id, $user_id);
        $this->assertEquals($EmailList->name, $name);

        // See if credentials were saved in DB
        $EmailList2 = new EmailList($id);
        $this->assertEquals($EmailList2->id, $id);
        $this->assertEquals($EmailList2->user_id, $user_id);
        $this->assertEquals($EmailList2->name, $name);
    }

    /**
     * Tests the update function.
     */
    public function testUpdate()
    {
        // Info for email credentials to be updated
        $user_id = $this->User->id;
        $name = 'My '.rand().'th List';
        $EmailList = new EmailList();
        $id = $EmailList->create($user_id, $name);

        // Update credentials
        $name2 = 'user'.rand();
        $success = $EmailList->update($name2);
        $this->assertTrue($success);
        $this->assertEquals($EmailList->user_id, $user_id);
        $this->assertEquals($EmailList->name, $name2);

        // test it's in the database
        $EmailList2 = new EmailList($id);
        $this->assertTrue(isset($EmailList2->id));
        $this->assertEquals($EmailList2->id, $id);
        $this->assertEquals($EmailList2->user_id, $user_id);
        $this->assertEquals($EmailList2->name, $name2);
    }

    /**
     * Tests the delete function.
     */
    public function testDelete()
    {
        // Info for email credentials to be deleted
        $user_id = $this->User->id;
        $name = 'Nom '.rand();
        $EmailList = new EmailList();
        $id = $EmailList->create($user_id, $name);

        // Delete it!
        $EmailList->delete();
        $this->assertFalse(isset($EmailList->id));
        $this->assertFalse(isset($EmailList->user_id));
        $this->assertFalse(isset($EmailList->name));

        // test with old (now "deleted") id
        $EmailList2 = new EmailList($id);
        $this->assertFalse(isset($EmailList2->id));
    }

    /**
     * Delete users created for testing
     */
    protected function tearDown()
    {
        //$this->deleteUsers();
    }
}
