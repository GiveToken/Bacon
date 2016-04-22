<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\Database\{
    RecruitingToken,
    RecruitingTokenImage
};

/**
 * This class tests the RecruitingToken class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/RecruitingTokenTest
 */
class RecruitingTokenTest
extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Bacon\Tests\Traits\City;
    use \Sizzle\Bacon\Tests\Traits\RecruitingToken;

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
    }

    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // no params
        $result = new RecruitingToken();
        $this->assertEquals('Sizzle\Bacon\Database\RecruitingToken', get_class($result));

        // save for 1 param
        $result->user_id = $this->User->id;
        $result->long_id = substr(md5(microtime()), rand(0, 26), 20);
        $result->save();

        // test with id
        $result2 = new RecruitingToken($result->id);
        $this->assertEquals($result->id, $result2->id);
        $this->assertEquals($result->long_id, $result2->long_id);

        //test with 2 params
        $result3 = new RecruitingToken($result->id, 'id');
        $this->assertEquals($result->id, $result3->id);
        $this->assertEquals($result->long_id, $result3->long_id);
        $result4 = new RecruitingToken($result->long_id, 'long_id');
        $this->assertEquals($result->id, $result3->id);
        $this->assertEquals($result->long_id, $result3->long_id);
    }

    /**
     * Tests the uniqueLongId function.
     */
    public function testUniqueLongId()
    {
        // Create token to dup against
        $RecruitingToken = $this->createRecruitingToken();

        // Test function with param
        $RecruitingToken2 = new RecruitingToken();
        $this->assertTrue($RecruitingToken2->uniqueLongId(rand()));
        $this->assertFalse($RecruitingToken2->uniqueLongId($RecruitingToken->long_id));

        // Test function without param
        $RecruitingToken2->long_id = $RecruitingToken->long_id;
        $this->assertFalse($RecruitingToken2->uniqueLongId());
    }

    /**
     * Tests the getUserCompanies function.
     */
    public function testGetUserCompanies()
    {
        // create some companies for the user
        $co1 = $this->createRecruitingCompany($this->User->id);
        $co2 = $this->createRecruitingCompany($this->User->id);
        $co3 = $this->createRecruitingCompany($this->User->id);

        $companies = RecruitingToken::getUserCompanies($this->User->id);
        $this->assertEquals(3, count($companies));
        $this->assertEquals($companies[0]['id'], $co1->id);
        $this->assertEquals($companies[0]['name'], $co1->name);
        $this->assertEquals($companies[1]['id'], $co2->id);
        $this->assertEquals($companies[1]['name'], $co2->name);
        $this->assertEquals($companies[2]['id'], $co3->id);
        $this->assertEquals($companies[2]['name'], $co3->name);
    }

    /**
     * Tests the getUser function.
     */
    public function testGetUser()
    {
        $result = $this->createRecruitingToken($this->User->id);

        $user = $result->getUser();
        $this->assertNotNull($user);
        $this->assertNotEquals('', $user);
        $this->assertEquals($this->User->id, $user->id);
        $this->assertEquals('Y', $user->allow_token_responses);
        $this->assertEquals('Y', $user->receive_token_notifications);
    }

    /**
     * Tests the getCompany function.
     */
    public function testGetCompany()
    {
        $co = $this->createRecruitingCompany($this->User->id);

        $result = $this->createRecruitingToken($this->User->id, $co->id);

        $company = $result->getCompany();
        $this->assertNotNull($company);
        $this->assertNotEquals('', $company);
        $this->assertEquals($co->id, $company->id);
        $this->assertEquals($co->name, $company->name);
        $this->assertEquals($this->User->id, $company->user_id);
    }

    /**
     * Tests the recruiter_profile enum is save properly
     */
    public function testSaveRecruiterProfile()
    {
        // create company
        $co = $this->createRecruitingCompany($this->User->id);

        // test save with default (N)
        $result = $this->createRecruitingToken($this->User->id, $co->id);
        $test = new RecruitingToken($result->id);
        $this->assertEquals('N', $test->recruiter_profile);

        // test save with Y
        $result = $this->createRecruitingToken($this->User->id, $co->id);
        $result->recruiter_profile = 'Y';
        $result->save();
        $test = new RecruitingToken($result->id);
        $this->assertEquals($result->recruiter_profile, $test->recruiter_profile);

        // test save with N
        $result = $this->createRecruitingToken($this->User->id, $co->id);
        $result->recruiter_profile = 'N';
        $result->save();
        $test = new RecruitingToken($result->id);
        $this->assertEquals($result->recruiter_profile, $test->recruiter_profile);
    }

    /**
     * Tests the screenshot function.
     */
    public function testScreenshot()
    {
        $token = $this->createRecruitingToken();

        // Create images
        $image = new RecruitingTokenImage();
        $fileName = rand().'.jpg';
        $id = $image->create($fileName, $token->id);

        // Test function
        $image = $token->screenshot();
        $this->assertEquals($image, $fileName);

        // cleanup
        $sql = "DELETE FROM recruiting_token_image WHERE id = '$id'";
        execute($sql);
    }

    /**
     * Tests the delete function.
     */
    public function testDelete()
    {
        $token = $this->createRecruitingToken();
        $id = $token->id;
        $token->delete();
        $sql = "SELECT id FROM recruiting_token WHERE id = '$id' AND deleted IS NOT NULL";
        $result = execute_query($sql);
        $array = $result->fetch_all(MYSQLI_ASSOC);
        $this->assertTrue(is_array($array));
        $this->assertFalse(empty($array));
        $this->assertEquals(1, count($array));
    }

    /**
     * Tests the addCity function.
     */
    public function testAddCity()
    {
        $token = $this->createRecruitingToken();
        $city = $this->createCity();
        $this->assertTrue($token->addCity($city->id));
        $sql = "SELECT city_id
                FROM recruiting_token_city
                WHERE recruiting_token_id = '$token->id'
                AND deleted IS NULL";
        $result = execute_query($sql);
        $array = $result->fetch_all(MYSQLI_ASSOC);
        $this->assertTrue(is_array($array));
        $this->assertFalse(empty($array));
        $this->assertEquals(1, count($array));
        $this->assertEquals($city->id, $array[0]['city_id']);

        //cleanup
        $this->assertTrue($token->removeCity($city->id));
    }

    /**
     * Tests the removeCity function.
     */
    public function testRemoveCity()
    {
        $token = $this->createRecruitingToken();
        $city = $this->createCity();
        $this->assertTrue($token->addCity($city->id));
        $this->assertTrue($token->removeCity($city->id));
        $sql = "SELECT city_id
                FROM recruiting_token_city
                WHERE recruiting_token_id = '$token->id'
                AND deleted IS NULL";
        $result = execute_query($sql);
        $array = $result->fetch_all(MYSQLI_ASSOC);
        $this->assertTrue(is_array($array));
        $this->assertTrue(empty($array));
    }

    /**
     * Tests the getCities function.
     */
    public function testGetCities()
    {
        $token = $this->createRecruitingToken();
        $city = $this->createCity();
        $city2 = $this->createCity();
        $this->assertTrue($token->addCity($city->id));
        $this->assertTrue($token->addCity($city2->id));
        $cities = $token->getCities();
        $this->assertTrue(is_array($cities));
        $this->assertFalse(empty($cities));
        $this->assertEquals(2, count($cities));
        $this->assertEquals('Sizzle\Bacon\Database\City', get_class($cities[0]));
        $this->assertEquals('Sizzle\Bacon\Database\City', get_class($cities[1]));
        $this->assertEquals($city->id, $cities[0]->id);
        $this->assertEquals($city2->id, $cities[1]->id);

        //cleanup
        $this->assertTrue($token->removeCity($city->id));
        $this->assertTrue($token->removeCity($city2->id));
    }

    /**
     * Delete users created for testing
     */
    protected function tearDown()
    {
        //$this->deleteCities();
        //$this->deleteRecruitingTokens();
    }
}
