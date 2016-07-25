<?php
namespace Sizzle\Bacon\Tests\Database;

use Sizzle\Bacon\{
    Connection,
    Database\RecruitingCompany,
    Database\User
};

/**
 * This class tests the RecruitingCompany class
 *
 * vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/RecruitingCompanyTest
 */
class RecruitingCompanyTest extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Bacon\Tests\Traits\Organization;
    use \Sizzle\Bacon\Tests\Traits\RecruitingToken;

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
        // $id = null case
        $RecruitingCompany = new RecruitingCompany();
        $this->recruitingCompanies[] = $RecruitingCompany->id;
        $this->assertEquals('Sizzle\Bacon\Database\RecruitingCompany', get_class($RecruitingCompany));
        $this->assertFalse(isset($RecruitingCompany->name));

        // $id specified case
        $user_id = $this->User->id;
        $name = rand().' Inc.';
        $logo = rand().'.jpg';
        $website = 'www.'.rand().'com';
        $values = 'obaoyibrebearbvreb ivy beriyvbreyb eroyb eroayvb '.rand();
        $facebook = 'f'.rand();
        $linkedin = 'in/'.rand();
        $youtube = 'y'.rand();
        $twitter = 't'.rand();
        $google_plus = 'g'.rand();
        $pinterest = 'p'.rand();
        $query = "INSERT INTO recruiting_company (
                      `user_id`,
                      `name`,
                      `logo`,
                      `website`,
                      `values`,
                      `facebook`,
                      `linkedin`,
                      `youtube`,
                      `twitter`,
                      `google_plus`,
                      `pinterest`
                  ) VALUES (
                      '$user_id',
                      '$name',
                      '$logo',
                      '$website',
                      '$values',
                      '$facebook',
                      '$linkedin',
                      '$youtube',
                      '$twitter',
                      '$google_plus',
                      '$pinterest'
                  )";
        $this->User->execute_query($query);
        $id = Connection::$mysqli->insert_id;
        $result = new RecruitingCompany($id);
        $this->recruitingCompanies[] = $result->id;
        $this->assertTrue(isset($result->name));
        $this->assertEquals($result->id, $id);
        $this->assertEquals($result->user_id, $user_id);
        $this->assertEquals($result->name, $name);
        $this->assertEquals($result->logo, $logo);
        $this->assertEquals($result->website, $website);
        $this->assertEquals($result->values, $values);
        $this->assertEquals($result->facebook, $facebook);
        $this->assertEquals($result->linkedin, $linkedin);
        $this->assertEquals($result->youtube, $youtube);
        $this->assertEquals($result->twitter, $twitter);
        $this->assertEquals($result->google_plus, $google_plus);
        $this->assertEquals($result->pinterest, $pinterest);
    }

    /**
     * Tests the save function when an insert is required.
     */
    public function testSaveInsert()
    {
        // test saving a new recruiting company
        $RecruitingCompany = new RecruitingCompany();
        $this->recruitingCompanies[] = $RecruitingCompany->id;
        $RecruitingCompany->user_id = $this->User->id;
        $RecruitingCompany->name = rand().' Inc.';
        $RecruitingCompany->logo = rand().'.png';
        $RecruitingCompany->website = 'www.'.rand().'com';
        $RecruitingCompany->values = 'For great justice, '.rand();
        $RecruitingCompany->facebook = 'f'.rand();
        $RecruitingCompany->linkedin = 'in/'.rand();
        $RecruitingCompany->youtube = 'y'.rand();
        $RecruitingCompany->twitter = 't'.rand();
        $RecruitingCompany->google_plus = 'g'.rand();
        $RecruitingCompany->pinterest = 'p'.rand();

        // id & created should be null before save
        $this->assertNull($RecruitingCompany->id);
        $this->assertNull($RecruitingCompany->created);
        $RecruitingCompany->save();

        // id & created should be populated after save
        $this->assertGreaterThan(0, $RecruitingCompany->id);
        //$this->assertNotNull($RecruitingCompany->created);

        // make sure all properties were inserted correctly
        $RecruitingCompany2 = new RecruitingCompany($RecruitingCompany->id);
        $this->recruitingCompanies[] = $RecruitingCompany2->id;
        foreach (get_object_vars($RecruitingCompany2) as $key => $value) {
            $this->assertEquals($RecruitingCompany->$key, $value);
        }

        // pass this onto the next test
        return $RecruitingCompany;
    }

    /**
     * Tests the save function when an update is required.
     *
     * @param RecruitingCompany $RecruitingCompany - an existing company
     *
     * @depends testSaveInsert
     */
    public function testSaveUpdate(RecruitingCompany $RecruitingCompany)
    {
        // set new values
        $RecruitingCompany->name = rand().' Co.';
        $RecruitingCompany->logo = rand().'.gif';
        $RecruitingCompany->website = 'test.'.rand().'com';
        $RecruitingCompany->values = 'To build a '.rand();
        $RecruitingCompany->facebook = 'fa'.rand();
        $RecruitingCompany->linkedin = 'ink/'.rand();
        $RecruitingCompany->youtube = 'yo'.rand();
        $RecruitingCompany->twitter = 'tw'.rand();
        $RecruitingCompany->google_plus = 'gp'.rand();
        $RecruitingCompany->pinterest = 'pi'.rand();
        $RecruitingCompany->save();

        // make sure all properties were updated correctly
        $RecruitingCompany2 = new RecruitingCompany($RecruitingCompany->id);
        $this->recruitingCompanies[] = $RecruitingCompany2->id;
        foreach (get_object_vars($RecruitingCompany2) as $key => $value) {
            $this->assertEquals($RecruitingCompany->$key, $value);
        }
    }

    /**
     * Tests the getAll function
     */
    public function testGetAll()
    {
        // test company with organization
        $org = $this->createOrganization();
        $co = $this->createRecruitingCompany();
        $user = new User($co->user_id);
        $user->organization_id = $org->id;
        $user->save();
        $name = "{$co->name} ({$org->name})";

        // test company without organization
        $co2 = $this->createRecruitingCompany();
        $name2 = "{$co->name} (No organization)";

        // call & compare
        $sql = 'SELECT COUNT(*) AS companies FROM recruiting_company';
        $result = (new RecruitingCompany())->execute_query($sql);
        $row = $result->fetch_assoc();
        $companyCount = $row['companies'];
        $all = (new RecruitingCompany())->getAll();
        $this->assertEquals($companyCount, count($all));
        $found = false;
        foreach ($all as $company) {
            if ($company['id'] == $co->id && $company['name'] == $name) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * Tests the getTokens function
     */
    public function testGetTokens()
    {
        $us = $this->createUser();
        $co = $this->createRecruitingCompany($us->id);
        $tok1 = $this->createRecruitingToken($us->id, $co->id);
        $tok2 = $this->createRecruitingToken($us->id, $co->id);
        $tok3 = $this->createRecruitingToken($us->id, $co->id);
        $tokens = $co->getTokens();
        $this->assertTrue(is_array($tokens));
        $this->assertEquals(3, count($tokens));
    }

    /**
     * Delete users created for testing
     */
    protected function tearDown()
    {
        $this->deleteRecruitingTokens();
        $this->deleteRecruitingCompanies();
        $this->deleteUsers();
        $this->deleteOrganizations();
    }
}
