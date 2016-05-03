<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\Database\{
    Organization
};

/**
 * This class tests the Organization class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/OrganizationTest
 */
class OrganizationTest
extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Bacon\Tests\Traits\RecruitingToken;

    /**
     * Creates test user & credentials
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
        $result = new Organization();
        $this->assertEquals('Sizzle\Bacon\Database\Organization', get_class($result));

        // test with bad id
        $result2 = new Organization(-1);
        $this->assertFalse(isset($result2->id));

        // test with good id (Sizzle)
        $result3 = new Organization(1);
        $this->assertEquals($result3->id, 1);
        $this->assertEquals($result3->name, 'Sizzle');
        $this->assertEquals($result3->website, 'https://www.gosizzle.io');
    }

    /**
     * Tests the create function with a name only.
     */
    public function testCreateNameOnly()
    {
        $name = 'The '.rand().' Corporation';

        // Create open
        $organization = new Organization();
        $id = $organization->create($name);

        // Check class variables set
        $this->assertEquals($organization->id, $id);
        $this->assertEquals($organization->name, $name);
        $this->assertFalse(isset($organization->website));
        $this->assertFalse(isset($organization->paying_user));

        // See if open was saved in DB
        $organization2 = new Organization($id);
        $this->assertEquals($organization2->id, $id);
        $this->assertEquals($organization2->name, $name);
        $this->assertFalse(isset($organization2->website));
        $this->assertFalse(isset($organization2->paying_user));
        $this->assertTrue(isset($organization2->created));
    }

    /**
     * Tests the create function with a name & website.
     */
    public function testCreateNameWebsite()
    {
        // setup vars
        $name = 'The '.rand().' Corporation';
        $website = 'http://www.'.rand().'.org';

        // Create open
        $organization = new Organization();
        $id = $organization->create($name, $website);

        // Check class variables set
        $this->assertEquals($organization->id, $id);
        $this->assertEquals($organization->name, $name);
        $this->assertEquals($organization->website, $website);
        $this->assertFalse(isset($organization->paying_user));

        // See if open was saved in DB
        $organization2 = new Organization($id);
        $this->assertEquals($organization2->id, $id);
        $this->assertEquals($organization2->name, $name);
        $this->assertEquals($organization2->website, $website);
        $this->assertFalse(isset($organization2->paying_user));
        $this->assertTrue(isset($organization2->created));
    }

    /**
     * Tests the create function with a name and paying user id.
     */
    public function testCreateNamePayingUser()
    {
        // test vars
        $name = 'The '.rand().' Corporation';
        $user_id = $this->User->id;

        // Create open
        $organization = new Organization();
        $id = $organization->create($name, null, $user_id);

        // Check class variables set
        $this->assertEquals($organization->id, $id);
        $this->assertEquals($organization->name, $name);
        $this->assertFalse(isset($organization->website));
        $this->assertEquals($organization->paying_user, $user_id);

        // See if open was saved in DB
        $organization2 = new Organization($id);
        $this->assertEquals($organization2->id, $id);
        $this->assertEquals($organization2->name, $name);
        $this->assertFalse(isset($organization2->website));
        $this->assertEquals($organization2->paying_user, $user_id);
        $this->assertTrue(isset($organization2->created));
    }

    /**
     * Tests the create function with name, website & paying user id.
     */
    public function testCreateAll()
    {
        // test ALL the things!
        $name = 'The '.rand().' Corporation';
        $website = 'http://www.'.rand().'.org';
        $user_id = $this->User->id;

        // Create open
        $organization = new Organization();
        $id = $organization->create($name, $website, $user_id);

        // Check class variables set
        $this->assertEquals($organization->id, $id);
        $this->assertEquals($organization->name, $name);
        $this->assertEquals($organization->website, $website);
        $this->assertEquals($organization->paying_user, $user_id);

        // See if open was saved in DB
        $organization2 = new Organization($id);
        $this->assertEquals($organization2->id, $id);
        $this->assertEquals($organization2->name, $name);
        $this->assertEquals($organization2->website, $website);
        $this->assertEquals($organization2->paying_user, $user_id);
        $this->assertTrue(isset($organization2->created));
    }

    /**
     * Tests the getJobs() function
     */
    public function testGetJobs()
    {
        $organization = new Organization();
        $organization->create('Random '.rand().' Co');

        // no jobs exist yet
        $jobs = $organization->getJobs();
        $this->assertTrue(is_array($jobs));
        $this->assertTrue(empty($jobs));

        // create a couple jobs & test
        $this->User->organization_id = $organization->id;
        $this->User->save();
        $recruitingToken1 = $this->createRecruitingToken($this->User->id);
        $recruitingToken2 = $this->createRecruitingToken($this->User->id);
        $jobs2 = $organization->getJobs();
        $this->assertTrue(is_array($jobs2));
        $this->assertFalse(empty($jobs2));
        $this->assertEquals(2, count($jobs2));
        $this->assertEquals($recruitingToken1->job_title, $jobs2[0]['title']);
        $this->assertEquals($recruitingToken1->job_description, $jobs2[0]['description']);
        $this->assertEquals($recruitingToken1->long_id, $jobs2[0]['long_id']);
        $this->assertEquals($recruitingToken2->job_title, $jobs2[1]['title']);
        $this->assertEquals($recruitingToken2->job_description, $jobs2[1]['description']);
        $this->assertEquals($recruitingToken2->long_id, $jobs2[1]['long_id']);

        // delete one and make sure it still works
        $recruitingToken1->delete();
        $jobs3 = $organization->getJobs();
        $this->assertTrue(is_array($jobs3));
        $this->assertFalse(empty($jobs3));
        $this->assertEquals(1, count($jobs3));
        $this->assertEquals($recruitingToken2->job_title, $jobs3[0]['title']);
        $this->assertEquals($recruitingToken2->job_description, $jobs3[0]['description']);
        $this->assertEquals($recruitingToken2->long_id, $jobs3[0]['long_id']);
    }

    /**
     * Delete users created for testing
     */
    protected function tearDown()
    {
        //$this->deleteUsers();
        //$this->deleteRecruitingTokens();
    }
}
