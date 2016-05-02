<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\Database\{
    UserMilestone
};

/**
 * This class tests the UserMilestone & Milestone classes
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/UserMilestoneTest
 */
class UserMilestoneTest extends \PHPUnit_Framework_TestCase
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
     * Creates test user
     */
    public function setUp()
    {
        // setup test user
        $this->User = $this->createUser();

        // setup test milestone
        $this->milestone_name = 'Super '.rand().' Thing';
        $query = "INSERT INTO milestone (`name`) VALUES ('{$this->milestone_name}')";
        $this->milestone_id = insert($query);
    }

    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // id case
        $UserMilestone = new UserMilestone($this->User->id, $this->milestone_id);
        $this->assertEquals('Sizzle\Bacon\Database\UserMilestone', get_class($UserMilestone));
        $this->assertTrue(isset($UserMilestone->id));
        $this->assertEquals($this->User->id, $UserMilestone->user_id);
        $this->assertEquals($this->milestone_id, $UserMilestone->milestone_id);
        $this->assertTrue(isset($UserMilestone->created));

        // name case
        $UserMilestone = new UserMilestone($this->User->id, $this->milestone_name);
        $this->assertEquals('Sizzle\Bacon\Database\UserMilestone', get_class($UserMilestone));
        $this->assertTrue(isset($UserMilestone->id));
        $this->assertEquals($this->User->id, $UserMilestone->user_id);
        $this->assertEquals($this->milestone_id, $UserMilestone->milestone_id);
        $this->assertTrue(isset($UserMilestone->created));
    }

    /**
     * Destroys the test data.
     */
    protected function tearDown()
    {
        $query = "DELETE FROM user_milestone WHERE milestone_id = '{$this->milestone_id}'";
        (new UserMilestone())->execute_query($query);
        $query = "DELETE FROM milestone WHERE id = '{$this->milestone_id}'";
        (new UserMilestone())->execute_query($query);
        $this->deleteUsers();
    }
}
