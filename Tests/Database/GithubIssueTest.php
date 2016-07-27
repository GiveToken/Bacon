<?php
namespace Sizzle\Tests;

use \Sizzle\Bacon\Database\{
    GithubIssue
};

/**
 * This class tests the GithubIssue class
 *
 * vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/GithubIssueTest
 */
class GithubIssueTest
extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // no params
        $result = new GithubIssue();
        $this->assertEquals('Sizzle\Bacon\Database\GithubIssue', get_class($result));

        // test with bad id
        $result2 = new GithubIssue(-1);
        $this->assertFalse(isset($result2->id));

        // test with good id in testCreate() below
    }

    /**
     * Tests the create function.
     */
    public function testCreate()
    {
        $repository = 'repo'.rand();
        $issue = rand();

        // Create issue
        $GithubIssue = new GithubIssue();
        $id = $GithubIssue->create($repository, $issue);

        // Check class variables set
        $this->assertEquals($GithubIssue->id, $id);
        $this->assertEquals($GithubIssue->repository, $repository);
        $this->assertEquals($GithubIssue->issue, $issue);

        // See if issue deets were saved in DB
        $GithubIssue2 = new GithubIssue($id);
        $this->assertEquals($GithubIssue2->id, $id);
        $this->assertEquals($GithubIssue2->repository, $repository);
        $this->assertEquals($GithubIssue2->issue, $issue);
        $this->assertEquals($GithubIssue2->closed, 'No');
    }



    /**
     * Tests the close function.
     */
    public function testClose()
    {
        $repository = 'repo'.rand();
        $issue = rand();

        // Create issue
        $GithubIssue = new GithubIssue();
        $id = $GithubIssue->create($repository, $issue);
        $GithubIssue->close();

        // Check closed
        $this->assertEquals($GithubIssue->closed, 'Yes');
        $GithubIssue2 = new GithubIssue($id);
        $this->assertEquals($GithubIssue2->closed, 'Yes');

        $repository = 'repo'.rand();
        $issue = rand();

        // Create issue
        $GithubIssue3 = new GithubIssue();
        $id = $GithubIssue3->create($repository, $issue);

        // Check closed
        $GithubIssue4 = new GithubIssue($id);
        $this->assertEquals($GithubIssue4->closed, 'No');
        $GithubIssue4->close();
        $this->assertEquals($GithubIssue4->closed, 'Yes');
        $GithubIssue5 = new GithubIssue($id);
        $this->assertEquals($GithubIssue5->closed, 'Yes');
    }
}
