<?php
namespace Sizzle\Bacon\Tests\Database;

use Sizzle\Bacon\Database\City;

/**
 * This class tests the City class
 *
 * vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/CityTest
 */
class CityTest extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Bacon\Tests\Traits\City;

    protected $existing_city;

    protected function setUp()
    {
        // create a city for testing
        $this->existing_city = $this->createCity();
    }

    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        // $id = null case
        $result = new City();
        $this->assertEquals('Sizzle\Bacon\Database\City', get_class($result));
        $this->assertFalse(isset($result->name));

        // $id specified case
        $result = new City($this->existing_city->id);
        foreach (get_object_vars($result) as $key => $value) {
            $this->assertEquals($this->existing_city->$key, $value);
        }
    }

    public function testSaveInsert()
    {
        // test saving a new city
        $city = new City();
        $city->name = "City #" . rand();
        $city->population = rand(10000, 10000000);
        $city->longitude = rand(0, 100);
        $city->latitude = rand(0, 100);
        $city->county = "County " . rand(0, 100);
        $city->country = "CT";
        $city->timezone = "Awesome Standard Time";
        $city->temp_hi_spring = rand(0, 100);
        $city->temp_lo_spring = rand(0, 100);
        $city->temp_avg_spring = rand(0, 100);
        $city->temp_hi_summer = rand(0, 100);
        $city->temp_lo_summer = rand(0, 100);
        $city->temp_avg_summer = rand(0, 100);
        $city->temp_hi_fall = rand(0, 100);
        $city->temp_lo_fall = rand(0, 100);
        $city->temp_avg_fall = rand(0, 100);
        $city->temp_hi_winter = rand(0, 100);
        $city->temp_lo_winter = rand(0, 100);
        $city->temp_avg_winter = rand(0, 100);

        // id should be null before save
        $this->assertNull($city->id);
        $city->save();

        // id should be populated after save
        $this->assertGreaterThan(0, $city->id);

        // make sure all properties were inserted correctly
        $saved_city = new City($city->id);
        foreach (get_object_vars($saved_city) as $key => $value) {
            $this->assertEquals($city->$key, $value);
        }

        // delete the inserted city
        $city->delete();
    }

    public function testSaveUpdate()
    {
        // test updating an existing city
        $city = $this->existing_city;

        // set string properties to "updated <property_name>"
        // set numeric properties to strlen(<property_name>)
        foreach (get_object_vars($city) as $key => $value) {
            if ($key !== 'id' && $key != 'created') {
                if (is_string($value)) {
                    $city->$key = "updated ".$key;
                } elseif (is_numeric($value)) {
                    $city->$key = strlen($key);
                }
            }
        }

        // update the city
        $city->save();

        // go get the updated record
        $updated_city = new City($city->id);
        // check the properties of the updatedCity
        foreach (get_object_vars($updated_city) as $key => $value) {
            if ($key !== 'id' && $key != 'created') {
                if (is_string($city->$key)) {
                    $this->assertEquals("updated ".$key, $value);
                } elseif (is_numeric($city->$key)) {
                    $this->assertEquals(strlen($key), $value);
                }
            }
        }
    }

    /**
     * Tests the getIdFromName function
     */
    public function testGetIdFromName()
    {
        // test success
        $id = (new City())->getIdFromName($this->existing_city->name);
        $this->assertEquals($this->existing_city->id, $id);

        // test fail
        $this->assertEquals(null, (new City())->getIdFromName('Not the name of a city'));
    }

    /**
     * Delete cities created for testing
     */
    protected function tearDown()
    {
        $this->deleteCities();
    }

    /**
     * Tests the save function.
     */
    public function testSave()
    {
        // $id = null case
        $City = new City();
        $this->assertFalse($City->save());
        $City->name = 'City of '.rand();
        $this->assertFalse($City->save());
        $this->assertFalse($City->save());
        $City->population = rand();
        $this->assertFalse($City->save());
        $City->longitude = rand(0, 180);
        $this->assertFalse($City->save());
        $City->latitude = rand(0, 180);
        $this->assertFalse($City->save());
        $City->county = 'County '.rand();
        $this->assertFalse($City->save());
        $City->country = 'USA';
        $this->assertFalse($City->save());
        $City->timezone = rand().' Time';
        $this->assertFalse($City->save());
        $City->temp_hi_spring = rand(0, 127);
        $this->assertFalse($City->save());
        $City->temp_lo_spring = rand(0, 127);
        $this->assertFalse($City->save());
        $City->temp_avg_spring = rand(0, 127);
        $this->assertFalse($City->save());
        $City->temp_hi_summer = rand(0, 127);
        $this->assertFalse($City->save());
        $City->temp_lo_summer = rand(0, 127);
        $this->assertFalse($City->save());
        $City->temp_avg_summer = rand(0, 127);
        $this->assertFalse($City->save());
        $City->temp_hi_fall = rand(0, 127);
        $this->assertFalse($City->save());
        $City->temp_lo_fall = rand(0, 127);
        $this->assertFalse($City->save());
        $City->temp_avg_fall = rand(0, 127);
        $this->assertFalse($City->save());
        $City->temp_hi_winter = rand(0, 127);
        $this->assertFalse($City->save());
        $City->temp_lo_winter = rand(0, 127);
        $this->assertFalse($City->save());
        $City->temp_avg_winter = rand(0, 127);
        $this->assertTrue($City->save());
        $query = "SELECT * FROM city WHERE `name` = '{$City->name}'";
        $result = $City->execute_query($query);
        $this->assertEquals(1, $result->num_rows);
        $row = $result->fetch_assoc();
        $this->assertEquals($City->name, $row['name']);
        $this->assertEquals($City->population, $row['population']);
        $this->assertEquals($City->longitude, $row['longitude']);
        $this->assertEquals($City->latitude, $row['latitude']);
        $this->assertEquals($City->county, $row['county']);
        $this->assertEquals($City->country, $row['country']);
        $this->assertEquals($City->timezone, $row['timezone']);
        $this->assertEquals($City->temp_hi_spring, $row['temp_hi_spring']);
        $this->assertEquals($City->temp_lo_spring, $row['temp_lo_spring']);
        $this->assertEquals($City->temp_avg_spring, $row['temp_avg_spring']);
        $this->assertEquals($City->temp_hi_summer, $row['temp_hi_summer']);
        $this->assertEquals($City->temp_lo_summer, $row['temp_lo_summer']);
        $this->assertEquals($City->temp_avg_summer, $row['temp_avg_summer']);
        $this->assertEquals($City->temp_hi_fall, $row['temp_hi_fall']);
        $this->assertEquals($City->temp_lo_fall, $row['temp_lo_fall']);
        $this->assertEquals($City->temp_avg_fall, $row['temp_avg_fall']);
        $this->assertEquals($City->temp_hi_winter, $row['temp_hi_winter']);
        $this->assertEquals($City->temp_lo_winter, $row['temp_lo_winter']);
        $this->assertEquals($City->temp_avg_winter, $row['temp_avg_winter']);

        // $id set case
        $City = new City();
        $City->id = 12;
        $this->assertFalse($City->save());
    }

    /**
     * Tests the match10 function.
     */
    public function testMatch10()
    {
        // too many test
        $cities = (new City())->match10('');
        $this->assertTrue(empty($cities));

        // test 10
        $firstPart = rand();
        for ($i=0; $i<10; $i++) {
            $this->createCity($firstPart.rand());
        }

        // 10 matches test
        $cities = (new City())->match10($firstPart);
        $this->assertFalse(empty($cities));
        $this->assertEquals(10, count($cities));
    }
}
