<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\{
    Connection,
    Database\RecruitingCompanyVideo
};

/**
 * This class tests the RecruitingCompanyVideo class
 *
 * ./vendor/bin/phpunit --bootstrap src/Tests/autoload.php src/Bacon/Tests/Database/RecruitingCompanyVideoTest
 */
class RecruitingCompanyVideoTest
extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Bacon\Tests\Traits\RecruitingToken;

    /**
     * Creates testing items in the database
     */
    public function setUp()
    {
        // setup test user
        $this->User = $this->createUser();

        // setup test company
        $this->RecruitingCompany = $this->createRecruitingCompany($this->User->id);

        // setup test token
        $this->RecruitingToken = $this->createRecruitingToken($this->User->id, $this->RecruitingCompany->id);

        // test videos
        $this->vids = array();
    }

    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // $id = null case
        $result = new RecruitingCompanyVideo();
        $this->assertEquals('Sizzle\Bacon\Database\RecruitingCompanyVideo', get_class($result));
        $this->assertFalse(isset($result->id));

        // invalid id case
        $result = new RecruitingCompanyVideo(-1);
        $this->assertEquals('Sizzle\Bacon\Database\RecruitingCompanyVideo', get_class($result));
        $this->assertFalse(isset($result->id));

        // valid id case
        $source_id = rand();
        $query = "INSERT INTO recruiting_company_video (recruiting_company_id, source_id)
                  VALUES ('{$this->RecruitingCompany->id}', '$source_id')";
        $result->execute_query($query);
        $id = Connection::$mysqli->insert_id;
        $this->vids[] = $id;
        $result = new RecruitingCompanyVideo($id);
        $this->assertEquals('Sizzle\Bacon\Database\RecruitingCompanyVideo', get_class($result));
        $this->assertTrue(isset($result->id));
        $this->assertEquals($result->id, $id);
        $this->assertEquals($result->source_id, $source_id);
        $this->assertEquals($result->recruiting_company_id, $this->RecruitingCompany->id);
    }

    /**
     * Tests the create function.
     */
    public function testCreate()
    {
        // create youtube token video
        $source = 'youtube';
        $source_id = rand();
        $RecruitingCompanyVideo = new RecruitingCompanyVideo();
        $id = $RecruitingCompanyVideo->create($this->RecruitingCompany->id, $source, $source_id);
        $this->vids[] = $id;
        $this->assertEquals($RecruitingCompanyVideo->id, $id);
        $this->assertEquals($RecruitingCompanyVideo->source, $source);
        $this->assertEquals($RecruitingCompanyVideo->source_id, $source_id);
        $this->assertEquals($RecruitingCompanyVideo->recruiting_company_id, $this->RecruitingCompany->id);

        // create vimeo token video
        $source = 'vimeo';
        $source_id = rand();
        $RecruitingCompanyVideo = new RecruitingCompanyVideo();
        $id = $RecruitingCompanyVideo->create($this->RecruitingCompany->id, $source, $source_id);
        $this->vids[] = $id;
        $this->assertEquals($RecruitingCompanyVideo->id, $id);
        $this->assertEquals($RecruitingCompanyVideo->source, $source);
        $this->assertEquals($RecruitingCompanyVideo->source_id, $source_id);
        $this->assertEquals($RecruitingCompanyVideo->recruiting_company_id, $this->RecruitingCompany->id);

        // check it's in the DB
        $RecruitingCompanyVideo2 = new RecruitingCompanyVideo($id);
        $this->assertEquals($RecruitingCompanyVideo2->id, $id);
        $this->assertEquals($RecruitingCompanyVideo->source, $source);
        $this->assertEquals($RecruitingCompanyVideo->source_id, $source_id);
        $this->assertEquals($RecruitingCompanyVideo2->recruiting_company_id, $this->RecruitingCompany->id);
    }

    /**
     * Tests the getByRecruitingTokenLongId function.
     */
    public function testGetByRecruitingTokenLongId()
    {
        $RecruitingCompanyVideo = new RecruitingCompanyVideo();

        // token with no images should return empty array
        $images = $RecruitingCompanyVideo->getByRecruitingTokenLongId($this->RecruitingToken->long_id);
        $this->assertTrue(is_array($images));
        $this->assertTrue(empty($images));

        // create token images
        $source = 'vimeo';
        $source_id[1] = rand();
        $source_id[2] = rand();
        $source_id[3] = rand();
        $this->vids[] = $RecruitingCompanyVideo->create($this->RecruitingCompany->id, $source, $source_id[1]);
        $this->vids[] = $RecruitingCompanyVideo->create($this->RecruitingCompany->id, $source, $source_id[2]);
        $this->vids[] = $RecruitingCompanyVideo->create($this->RecruitingCompany->id, $source, $source_id[3]);
        $images = $RecruitingCompanyVideo->getByRecruitingTokenLongId($this->RecruitingToken->long_id);
        $this->assertTrue(is_array($images));
        $this->assertEquals(count($images), 3);
        foreach ($images as $image) {
            $this->assertTrue($image['id'] > 0);
            $this->assertTrue(in_array($image['source_id'], $source_id));
        }
    }

    /**
     * Tests the getByCompanyId function.
     */
    public function testGetByCompanyId()
    {
        $RecruitingCompanyVideo = new RecruitingCompanyVideo();

        // token with no images should return empty array
        $images = $RecruitingCompanyVideo->getByCompanyId($this->RecruitingCompany->id);
        $this->assertTrue(is_array($images));
        $this->assertTrue(empty($images));

        // create token images
        $source = 'vimeo';
        $source_id[1] = rand();
        $source_id[2] = rand();
        $source_id[3] = rand();
        $this->vids[] = $RecruitingCompanyVideo->create($this->RecruitingCompany->id, $source, $source_id[1]);
        $this->vids[] = $RecruitingCompanyVideo->create($this->RecruitingCompany->id, $source, $source_id[2]);
        $this->vids[] = $RecruitingCompanyVideo->create($this->RecruitingCompany->id, $source, $source_id[3]);
        $images = $RecruitingCompanyVideo->getByCompanyId($this->RecruitingCompany->id);
        $this->assertTrue(is_array($images));
        $this->assertEquals(count($images), 3);
        foreach ($images as $image) {
            $this->assertTrue($image['id'] > 0);
            $this->assertTrue(in_array($image['source_id'], $source_id));
        }
    }

    /**
     * Tests the delete function.
     */
    public function testDelete()
    {
        // create token video
        $query = "INSERT INTO recruiting_company_video (recruiting_company_id)
                  VALUES ('{$this->RecruitingCompany->id}')";
        Connection::$mysqli->query($query);
        $id = Connection::$mysqli->insert_id;
        $this->vids[] = $id;
        $result = new RecruitingCompanyVideo($id);


        // delete token video
        $result->delete();

        // check class variables get unset
        $this->assertFalse(isset($result->id));
        $this->assertFalse(isset($result->recruiting_company_id));
        $this->assertFalse(isset($result->source));
        $this->assertFalse(isset($result->source_id));

        // check it's gone from DB
        $result2 = new RecruitingCompanyVideo($id);
        $this->assertFalse(isset($result2->id));
    }

    /**
     * Delete users created for testing
     */
    protected function tearDown()
    {
        foreach ($this->vids as $id) {
            $sql = "DELETE FROM recruiting_company_video WHERE id = '$id'";
            (new RecruitingCompanyVideo())->execute_query($sql);
        }
        $this->deleteRecruitingTokens();
    }
}
