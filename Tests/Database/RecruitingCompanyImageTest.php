<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\{
    Connection,
    Database\RecruitingCompanyImage
};

/**
 * This class tests the RecruitingCompanyImage class
 *
 * vendor/bin/phpunit --bootstrap src/Tests/autoload.php src/Bacon/Tests/Database/RecruitingCompanyImageTest
 */
class RecruitingCompanyImageTest
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

        // test images
        $this->images = array();
    }

    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // $id = null case
        $result = new RecruitingCompanyImage();
        $this->assertEquals('Sizzle\Bacon\Database\RecruitingCompanyImage', get_class($result));
        $this->assertFalse(isset($result->id));

        // invalid id case
        $result = new RecruitingCompanyImage(-1);
        $this->assertEquals('Sizzle\Bacon\Database\RecruitingCompanyImage', get_class($result));
        $this->assertFalse(isset($result->id));

        // valid id case
        $file_name = rand().'.jpg';
        $query = "INSERT INTO recruiting_company_image (recruiting_company_id, file_name)
                  VALUES ('{$this->RecruitingCompany->id}', '$file_name')";
        $result->execute_query($query);
        $id = Connection::$mysqli->insert_id;
        $this->images[] = $id;
        $result = new RecruitingCompanyImage($id);
        $this->assertEquals('Sizzle\Bacon\Database\RecruitingCompanyImage', get_class($result));
        $this->assertTrue(isset($result->id));
        $this->assertEquals($result->id, $id);
        $this->assertEquals($result->file_name, $file_name);
        $this->assertEquals($result->recruiting_company_id, $this->RecruitingCompany->id);
        $this->assertTrue(isset($result->created));
    }

    /**
     * Tests the create function.
     */
    public function testCreate()
    {
        // create token image
        $file_name = rand().'.jpg';
        $RecruitingCompanyImage = new RecruitingCompanyImage();
        $id = $RecruitingCompanyImage->create($this->RecruitingCompany->id, $file_name);
        $this->images[] = $id;
        $this->assertEquals($RecruitingCompanyImage->id, $id);
        $this->assertEquals($RecruitingCompanyImage->file_name, $file_name);
        $this->assertEquals($RecruitingCompanyImage->recruiting_company_id, $this->RecruitingCompany->id);

        // check it's in the DB
        $RecruitingCompanyImage2 = new RecruitingCompanyImage($id);
        $this->assertEquals($RecruitingCompanyImage2->id, $id);
        $this->assertEquals($RecruitingCompanyImage2->file_name, $file_name);
        $this->assertEquals($RecruitingCompanyImage2->recruiting_company_id, $this->RecruitingCompany->id);
    }

    /**
     * Tests the delete function.
     */
    public function testDelete()
    {
        // creaete image
        $file_name = rand().'.jpg';
        $query = "INSERT INTO recruiting_company_image (recruiting_company_id, file_name)
                  VALUES ('{$this->RecruitingCompany->id}', '$file_name')";
        Connection::$mysqli->query($query);
        $id = Connection::$mysqli->insert_id;
        $this->images[] = $id;
        $result = new RecruitingCompanyImage($id);
        $this->assertTrue(isset($result->id));

        // delete image
        $result->delete();

        // check class variables get unset
        $this->assertFalse(isset($result->id));
        $this->assertFalse(isset($result->recruiting_company_id));
        $this->assertFalse(isset($result->file_name));

        // check it's gone from DB
        $result2 = new RecruitingCompanyImage($id);
        $this->assertFalse(isset($result2->id));

        //check it's deleted from the filesystem
        $this->markTestIncomplete();
    }

    /**
     * Tests the getByRecruitingTokenId function.
     */
    public function testGetByRecruitingTokenId()
    {
        $RecruitingCompanyImage = new RecruitingCompanyImage();

        // token with no images should return empty array
        $images = $RecruitingCompanyImage->getByRecruitingTokenId($this->RecruitingToken->id);
        $this->assertTrue(is_array($images));
        $this->assertTrue(empty($images));

        // create token images
        $file_name[1] = rand().'.jpg';
        $file_name[2] = rand().'.jpg';
        $file_name[3] = rand().'.jpg';
        $this->images[] = $RecruitingCompanyImage->create($this->RecruitingCompany->id, $file_name[1]);
        $this->images[] = $RecruitingCompanyImage->create($this->RecruitingCompany->id, $file_name[2]);
        $this->images[] = $RecruitingCompanyImage->create($this->RecruitingCompany->id, $file_name[3]);
        $images = $RecruitingCompanyImage->getByRecruitingTokenId($this->RecruitingToken->id);
        $this->assertTrue(is_array($images));
        $this->assertEquals(count($images), 3);
        foreach ($images as $image) {
            $this->assertTrue($image['id'] > 0);
            $this->assertTrue(in_array($image['file_name'], $file_name));
        }
    }

    /**
     * Tests the getByRecruitingTokenLongId function.
     */
    public function testGetByRecruitingTokenLongId()
    {
        $RecruitingCompanyImage = new RecruitingCompanyImage();

        // token with no images should return empty array
        $images = $RecruitingCompanyImage->getByRecruitingTokenLongId($this->RecruitingToken->long_id);
        $this->assertTrue(is_array($images));
        $this->assertTrue(empty($images));

        // create token images
        $file_name[1] = rand().'.jpg';
        $file_name[2] = rand().'.jpg';
        $file_name[3] = rand().'.jpg';
        $this->images[] = $RecruitingCompanyImage->create($this->RecruitingCompany->id, $file_name[1]);
        $this->images[] = $RecruitingCompanyImage->create($this->RecruitingCompany->id, $file_name[2]);
        $this->images[] = $RecruitingCompanyImage->create($this->RecruitingCompany->id, $file_name[3]);
        $images = $RecruitingCompanyImage->getByRecruitingTokenLongId($this->RecruitingToken->long_id);
        $this->assertTrue(is_array($images));
        $this->assertEquals(count($images), 3);
        foreach ($images as $image) {
            $this->assertTrue($image['id'] > 0);
            $this->assertTrue(in_array($image['file_name'], $file_name));
        }
    }

    /**
     * Tests the getByCompanyId function.
     */
    public function testGetByCompanyId()
    {
        $RecruitingCompanyImage = new RecruitingCompanyImage();

        // token with no images should return empty array
        $images = $RecruitingCompanyImage->getByCompanyId($this->RecruitingCompany->id);
        $this->assertTrue(is_array($images));
        $this->assertTrue(empty($images));

        // create token images
        $file_name[1] = rand().'.jpg';
        $file_name[2] = rand().'.jpg';
        $file_name[3] = rand().'.jpg';
        $this->images[] = $RecruitingCompanyImage->create($this->RecruitingCompany->id, $file_name[1]);
        $this->images[] = $RecruitingCompanyImage->create($this->RecruitingCompany->id, $file_name[2]);
        $this->images[] = $RecruitingCompanyImage->create($this->RecruitingCompany->id, $file_name[3]);
        $images = $RecruitingCompanyImage->getByCompanyId($this->RecruitingCompany->id);
        $this->assertTrue(is_array($images));
        $this->assertEquals(count($images), 3);
        foreach ($images as $image) {
            $this->assertTrue($image['id'] > 0);
            $this->assertTrue(in_array($image['file_name'], $file_name));
        }
    }

    /**
     * Delete users created for testing
     */
    protected function tearDown()
    {
        foreach ($this->images as $id) {
            $sql = "DELETE FROM recruiting_company_image WHERE id = '$id'";
            (new RecruitingCompanyImage())->execute_query($sql);
        }
        $this->deleteRecruitingTokens();
    }
}
