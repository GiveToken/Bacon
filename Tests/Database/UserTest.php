<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\{
    Connection,
    Database\User
};

/**
 * This class tests the User class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/UserTest
 */
class UserTest
extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Bacon\Tests\Traits\User;
    use \Sizzle\Bacon\Tests\Traits\Organization;

    /**
     * Requires the util.php file of functions
     */
    public static function setUpBeforeClass()
    {
        include_once __DIR__.'/../../../../util.php';
    }

    /**
     * Tests the __construct method.
     */
    public function testConstructor()
    {
        // $id = null case
        $user = new User();
        $this->assertEquals('Sizzle\Bacon\Database\User', get_class($user));
        $this->assertFalse(isset($user->email_address));
    }

    /**
     * Tests the exists method.
     */
    public function testExists()
    {
        $this->markTestIncomplete();
    }

    /**
     * Tests the fetch method.
     */
    public function testFetch()
    {
        // $key default
        $email = rand() . '@gossizle.io';
        $sql = "INSERT INTO user (email_address) VALUES ('$email')";
        Connection::$mysqli->query($sql);
        $userId = Connection::$mysqli->insert_id;
        $user = (new User())->fetch($email);
        $this->assertEquals('Sizzle\Bacon\Database\User', get_class($user));
        $this->assertEquals($email, $user->email_address);
        $this->assertEquals($userId, $user->id);
        $user = (new User())->fetch(rand() . $email); //this one shouldn't be there
        $this->assertFalse(isset($user));

        // $key = api_key
        $email = rand() . '@gossizle.io';
        $apiKey = rand() . '_key';
        $sql = "INSERT INTO user (email_address, api_key)
                VALUES ('$email', '$apiKey')";
        Connection::$mysqli->query($sql);
        $userId = Connection::$mysqli->insert_id;
        $user = (new User())->fetch($apiKey, 'api_key');
        $this->assertEquals('Sizzle\Bacon\Database\User', get_class($user));
        $this->assertEquals($email, $user->email_address);
        $this->assertEquals($apiKey, $user->api_key);
        $this->assertEquals($userId, $user->id);
        $user = (new User())->fetch(rand() . $apiKey, 'api_key');
        //this one shouldn't be there
        $this->assertFalse(isset($user));

        // $key = email_address
        $email = rand() . '@gossizle.io';
        $sql = "INSERT INTO user (email_address) VALUES ('$email')";
        Connection::$mysqli->query($sql);
        $userId = Connection::$mysqli->insert_id;
        $user = (new User())->fetch($email, 'email_address');
        $this->assertEquals('Sizzle\Bacon\Database\User', get_class($user));
        $this->assertEquals($email, $user->email_address);
        $this->assertEquals($userId, $user->id);
        $user = (new User())->fetch(rand() . $email, 'email_address');
        //this one shouldn't be there
        $this->assertFalse(isset($user));

        // $key = reset_code
        $email = rand() . '@gossizle.io';
        $reset = rand() . '_code';
        $sql = "INSERT INTO user (email_address, reset_code)
                VALUES ('$email', '$reset')";
        Connection::$mysqli->query($sql);
        $userId = Connection::$mysqli->insert_id;
        $user = (new User())->fetch($reset, 'reset_code');
        $this->assertEquals('Sizzle\Bacon\Database\User', get_class($user));
        $this->assertEquals($email, $user->email_address);
        $this->assertEquals($reset, $user->reset_code);
        $this->assertEquals($userId, $user->id);
        $user = (new User())->fetch(rand() . $reset, 'reset_code');
        //this one shouldn't be there
        $this->assertFalse(isset($user));

        // $key garbage
        $user = (new User())->fetch($email, 'garbage');
        $this->assertFalse(isset($user));
    }

    /**
     * Tests the update_token method.
     */
    public function testUpdateToken()
    {
        $this->markTestIncomplete();
    }

    /**
     * Tests the save method.
     */
    public function testSave()
    {
        $this->markTestIncomplete();
    }

    /**
     * Tests the activate method.
     */
    public function testActivate()
    {
        //create user that needs activation
        $user = $this->createUser();
        $user->activation_key = 'v5y3q'.rand().'nachoes';
        $user->save();
        $this->assertTrue($user->activate($user->activation_key));
        // second time should fail
        $this->assertFalse($user->activate($user->activation_key));

        // create user that doens't need activation
        $user2 = $this->createUser();
        $key = 'jrhgksv'.rand().'gorilla';
        $this->assertFalse($user2->activate($key));

        // create user that needs activation, but use bad key
        $user3 = $this->createUser();
        $user3->activation_key = 'bsu4v65'.rand().'pecan';
        $user3->save();
        $key = '4ic7tq346b'.rand().'cashew';
        $this->assertFalse($user3->activate($key));
        // but true key should still work
        $this->assertTrue($user3->activate($user3->activation_key));
    }

    /**
     * Tests the getRecruiterProfile method.
     */
    public function testGetRecruiterProfile()
    {
        //fail on nonexistant user
        $user = new User(-1);
        $this->assertTrue(is_array($user->getRecruiterProfile()));
        $this->assertTrue(empty($user->getRecruiterProfile()));

        // should work for any user
        $user2 = $this->createUser();
        $profile2 = $user2->getRecruiterProfile();
        $this->assertFalse(empty($profile2));
        $this->assertEquals($user2->first_name, $profile2['first_name']);
        $this->assertEquals($user2->last_name, $profile2['last_name']);
        $this->assertEquals(null, $profile2['position']);
        $this->assertEquals(null, $profile2['linkedin']);
        $this->assertEquals(null, $profile2['website']);
        $this->assertEquals(null, $profile2['about']);
        $this->assertEquals(null, $profile2['face_image']);
        $this->assertEquals(null, $profile2['organization']);

        // user with a complete profile
        $org = $this->createOrganization();
        $org->website = 'http://awesome.'.rand().'.io';
        $user3 = $this->createUser();
        $user3->organization_id = $org->id;
        $user3->save();
        $profile3 = $user3->getRecruiterProfile();
        $this->assertFalse(empty($profile3));
        $this->assertEquals($user3->first_name, $profile3['first_name']);
        $this->assertEquals($user3->last_name, $profile3['last_name']);
        $this->assertEquals($user3->position, $profile3['position']);
        $this->assertEquals($user3->linkedin, $profile3['linkedin']);
        $this->assertEquals($org->website, $profile3['website']);
        $this->assertEquals($user3->about, $profile3['about']);
        $this->assertEquals($user3->face_image, $profile3['face_image']);
        $this->assertEquals($org->name, $profile3['organization']);
    }

    /**
     * Delete users created for testing
     */
    protected function tearDown()
    {
        foreach ($this->users as $id) {
            $sql = "DELETE FROM user_milestone WHERE user_id = '$id'";
            (new User())->execute_query($sql);
        }
        $this->deleteUsers();
        $this->deleteOrganizations();
    }
}
