<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\Database\{
    RecruitingTokenImage
};

/**
 * This class tests the RecruitingTokenImage class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/RecruitingTokenImageTest
 */
class RecruitingTokenImageTest
extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Tests\Traits\RecruitingToken;

    /**
     * Requires the util.php file of functions
     */
    public static function setUpBeforeClass()
    {
        include_once __DIR__.'/../../../../util.php';
    }

    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // no params
        $result = new RecruitingTokenImage();
        $this->assertEquals('Sizzle\Bacon\Database\RecruitingTokenImage', get_class($result));

        // test with bad id
        $result2 = new RecruitingTokenImage(-1);
        $this->assertFalse(isset($result2->id));

        // test with good id in testCreate() below
    }

    /**
     * Tests the create function.
     */
    public function testCreate()
    {
        $token = $this->createRecruitingToken();

        // Create image
        $image = new RecruitingTokenImage();
        $fileName = rand().'.jpg';
        $id = $image->create($fileName, $token->id);

        // Check class variables set
        $this->assertEquals($image->id, $id);
        $this->assertEquals($image->file_name, $fileName);
        $this->assertEquals($image->recruiting_token_id, $token->id);

        // See if open was saved in DB
        $image2 = new RecruitingTokenImage($id);
        $this->assertEquals($image2->id, $id);
        $this->assertEquals($image2->file_name, $fileName);
        $this->assertEquals($image2->recruiting_token_id, $token->id);

        // cleanup
        $sql = "DELETE FROM recruiting_token_image WHERE id = '$id'";
        execute($sql);
    }

    /**
     * Tests the getByRecruitingTokenId function.
     */
    public function testGetByRecruitingTokenId()
    {
        $token = $this->createRecruitingToken();

        // Create images
        $image = new RecruitingTokenImage();
        $fileName = rand().'.jpg';
        $fileName2 = rand().'.png';
        $fileName3 = rand().'.gif';
        $ids[] = $image->create($fileName, $token->id);
        $ids[] = $image->create($fileName2, $token->id);
        $ids[] = $image->create($fileName3, $token->id);

        // Test function
        $images = (new RecruitingTokenImage())->getByRecruitingTokenId($token->id);
        $this->assertEquals(3, count($images));
        $this->assertEquals($images[0]['file_name'], $fileName);
        $this->assertEquals($images[0]['recruiting_token_id'], $token->id);
        $this->assertEquals($images[1]['file_name'], $fileName2);
        $this->assertEquals($images[1]['recruiting_token_id'], $token->id);
        $this->assertEquals($images[2]['file_name'], $fileName3);
        $this->assertEquals($images[2]['recruiting_token_id'], $token->id);

        // cleanup
        foreach ($ids as $id) {
            $sql = "DELETE FROM recruiting_token_image WHERE id = '$id'";
            execute($sql);
        }
    }

    /**
     * Delete users created for testing
     */
    protected function tearDown()
    {
        $this->deleteRecruitingTokens();
    }
}
