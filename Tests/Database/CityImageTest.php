<?php
namespace Sizzle\Bacon\Tests\Database;

use Sizzle\Bacon\Database\City;
use Sizzle\Bacon\Database\CityImage;

/**
 * This class tests the City class
 *
 * vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/CityImageTest
 */
class CityImageTest extends \PHPUnit_Framework_TestCase
{
    protected $city_with_images;
    protected $city_with_no_images;

    private function generateCity()
    {
        // create a city for testing
        $city = new City();
        $city->name = "Test City ".rand();;
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
        return $city;
    }

    protected function setUp()
    {
        // city with images
        $this->city_with_images = $this->generateCity();
        $this->city_with_images->save();
        $city_id = $this->city_with_images->id;
        $sql = "INSERT INTO `giftbox`.`city_image` (`city_id`, `image_file`) VALUES ('$city_id', 'AL/Ralph/3.svg');";
        $this->city_with_images->execute_query($sql); // image #1
        $this->city_with_images->execute_query($sql); // image #2
        $this->city_with_images->execute_query($sql); // image #3
        $this->city_with_images->execute_query($sql); // image #4

        // city with no images
        $this->city_with_no_images = $this->generateCity();
        $this->city_with_no_images->save();
    }

    public function testGetAllImagesForCity()
    {
        $imagesForCityWithImages = (new CityImage())->getAllImageUrlsForCity($this->city_with_images->id);
        $this->assertEquals(4, count($imagesForCityWithImages));

        $imagesForCityWithNoImages = (new CityImage())->getAllImageUrlsForCity($this->city_with_no_images->id);
        $this->assertEquals(0, count($imagesForCityWithNoImages));
    }
}
